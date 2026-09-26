<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_diverse_assistant\local\teacher;

/**
 * Changes the assistant proposes to a teacher: checked, stored and shown as cards until the teacher handles them.
 *
 * A proposal keeps the texts before the change, so an applied proposal can be undone. Proposals are deleted
 * KEEP_DAYS after their last change, or earlier with the saved conversation they were made in.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class proposals {
    /** Table of proposals. */
    public const TABLE = 'local_diverse_assistant_prop';

    /** Proposal: a new page. */
    public const ACTION_NEW_PAGE = 'newpage';

    /** Proposal: a new text and media area. */
    public const ACTION_NEW_LABEL = 'newlabel';

    /** Proposal: new texts for an activity. */
    public const ACTION_UPDATE_ACTIVITY = 'updateactivity';

    /** Proposal: new texts for a section. */
    public const ACTION_UPDATE_SECTION = 'updatesection';

    /** Waiting for the teacher. */
    public const STATUS_PENDING = 'pending';

    /** Applied to the course. */
    public const STATUS_APPLIED = 'applied';

    /** Applied, then undone. */
    public const STATUS_UNDONE = 'undone';

    /** Declined by the teacher. */
    public const STATUS_DISCARDED = 'discarded';

    /** Days a proposal is kept after its last change (the time an applied change can be undone). */
    public const KEEP_DAYS = 30;

    /** Most proposals kept from one answer. */
    public const MAX_PER_ANSWER = 5;

    /** Longest name, in characters. */
    private const MAX_NAME_LENGTH = 255;

    /** Longest HTML text, in characters. */
    private const MAX_HTML_LENGTH = 300000;

    /** Longest note, in characters. */
    private const MAX_NOTE_LENGTH = 500;

    /**
     * Check the model's tool calls and store the valid ones as pending proposals.
     *
     * @param \stdClass $course The course.
     * @param int $userid The teacher.
     * @param array $toolcalls From chat_result::$toolcalls.
     * @param array $editable 'cmids' and 'sectionids' whose texts the model saw in full.
     * @return array ['records' => \stdClass[] stored proposals, 'problems' => string[] messages for the teacher]
     */
    public static function create_from_tool_calls(\stdClass $course, int $userid, array $toolcalls, array $editable): array {
        global $DB;
        $records = [];
        $problems = [];
        if (count($toolcalls) > self::MAX_PER_ANSWER) {
            $problems[] = get_string('proposal_error_toomany', 'local_diverse_assistant', self::MAX_PER_ANSWER);
            $toolcalls = array_slice($toolcalls, 0, self::MAX_PER_ANSWER);
        }
        foreach ($toolcalls as $call) {
            try {
                $record = self::validate($course, $call, $editable);
            } catch (\moodle_exception $e) {
                $problems[] = $e->getMessage();
                continue;
            }
            $now = time();
            $record->userid = $userid;
            $record->courseid = (int)$course->id;
            $record->conversationid = 0;
            $record->messageid = 0;
            $record->appliedhash = null;
            $record->status = self::STATUS_PENDING;
            $record->timecreated = $now;
            $record->timemodified = $now;
            $record->id = $DB->insert_record(self::TABLE, $record);
            $records[] = $record;
        }
        return ['records' => $records, 'problems' => $problems];
    }

    /**
     * Turn one tool call into a proposal record (not yet stored).
     *
     * @param \stdClass $course The course.
     * @param array $call ['name' => string, 'arguments' => array|null].
     * @param array $editable 'cmids' and 'sectionids' whose texts the model saw in full.
     * @return \stdClass With action, cmid, sectionid, proposed and original.
     * @throws \moodle_exception With a message for the teacher if the call is not valid.
     */
    private static function validate(\stdClass $course, array $call, array $editable): \stdClass {
        $args = $call['arguments'] ?? null;
        if (!is_array($args)) {
            throw new \moodle_exception('proposal_error_invalid', 'local_diverse_assistant');
        }
        $modinfo = get_fast_modinfo($course);
        $coursecontext = \context_course::instance($course->id);
        $note = self::clean_note($args['note'] ?? '');

        switch ($call['name'] ?? '') {
            case tools::NEW_PAGE:
            case tools::NEW_LABEL:
                $ispage = $call['name'] === tools::NEW_PAGE;
                $section = self::get_section($modinfo, $args['section'] ?? null);
                $modname = $ispage ? 'page' : 'label';
                if (!has_all_capabilities(['moodle/course:manageactivities', "mod/{$modname}:addinstance"], $coursecontext)) {
                    throw new \moodle_exception('proposal_error_nopermission', 'local_diverse_assistant');
                }
                $proposed = ['note' => $note];
                if ($ispage) {
                    $proposed['name'] = self::clean_name($args['name'] ?? '');
                    $proposed['content'] = self::clean_html($args['content'] ?? '');
                    $description = self::clean_html($args['description'] ?? '');
                    if ($description !== '') {
                        $proposed['description'] = $description;
                    }
                    if ($proposed['name'] === '' || $proposed['content'] === '') {
                        throw new \moodle_exception('proposal_error_invalid', 'local_diverse_assistant');
                    }
                } else {
                    $proposed['description'] = self::clean_html($args['content'] ?? '');
                    if ($proposed['description'] === '') {
                        throw new \moodle_exception('proposal_error_invalid', 'local_diverse_assistant');
                    }
                }
                return (object)[
                    'action' => $ispage ? self::ACTION_NEW_PAGE : self::ACTION_NEW_LABEL,
                    'cmid' => 0,
                    'sectionid' => (int)$section->id,
                    'proposed' => json_encode($proposed),
                    'original' => null,
                ];

            case tools::UPDATE_ACTIVITY:
                $cm = self::get_cm($modinfo, $args['cmid'] ?? null);
                if (!in_array((int)$cm->id, $editable['cmids'] ?? [], true)) {
                    throw new \moodle_exception('proposal_error_notincluded', 'local_diverse_assistant', '',
                        format_string($cm->name, true, ['context' => $cm->context]));
                }
                $original = course_content::read_activity($cm);
                if ($original === null) {
                    throw new \moodle_exception('proposal_error_nopermission', 'local_diverse_assistant');
                }
                $proposed = [];
                if (isset($args['name']) && $cm->modname !== 'label') {
                    $proposed['name'] = self::clean_name($args['name']);
                }
                if (isset($args['description']) && array_key_exists('description', $original)) {
                    $proposed['description'] = self::clean_html($args['description']);
                }
                if (isset($args['content']) && array_key_exists('content', $original)) {
                    $proposed['content'] = self::clean_html($args['content']);
                }
                $proposed = self::without_unchanged($proposed, $original, ['name', 'content']);
                if (!$proposed) {
                    throw new \moodle_exception('proposal_error_nochange', 'local_diverse_assistant', '',
                        format_string($cm->name, true, ['context' => $cm->context]));
                }
                $proposed['note'] = $note;
                return (object)[
                    'action' => self::ACTION_UPDATE_ACTIVITY,
                    'cmid' => (int)$cm->id,
                    'sectionid' => (int)$cm->section,
                    'proposed' => json_encode($proposed),
                    'original' => json_encode($original),
                ];

            case tools::UPDATE_SECTION:
                $section = self::get_section($modinfo, $args['section'] ?? null);
                if (!has_capability('moodle/course:update', $coursecontext)) {
                    throw new \moodle_exception('proposal_error_nopermission', 'local_diverse_assistant');
                }
                if (!in_array((int)$section->id, $editable['sectionids'] ?? [], true)) {
                    throw new \moodle_exception('proposal_error_notincluded', 'local_diverse_assistant', '',
                        get_section_name($course, $section));
                }
                $original = course_content::read_section($section);
                $proposed = [];
                if (isset($args['name'])) {
                    $proposed['name'] = self::clean_name($args['name']);
                }
                if (isset($args['summary'])) {
                    $proposed['summary'] = self::clean_html($args['summary']);
                }
                $proposed = self::without_unchanged($proposed, $original, ['name']);
                if (!$proposed) {
                    throw new \moodle_exception('proposal_error_nochange', 'local_diverse_assistant', '',
                        get_section_name($course, $section));
                }
                $proposed['note'] = $note;
                return (object)[
                    'action' => self::ACTION_UPDATE_SECTION,
                    'cmid' => 0,
                    'sectionid' => (int)$section->id,
                    'proposed' => json_encode($proposed),
                    'original' => json_encode($original),
                ];
        }
        throw new \moodle_exception('proposal_error_invalid', 'local_diverse_assistant');
    }

    /**
     * A proposal of the given user.
     *
     * @param int $id Proposal id.
     * @param int $userid The user who must own it.
     * @return \stdClass
     * @throws \moodle_exception If it does not exist or belongs to someone else.
     */
    public static function get(int $id, int $userid): \stdClass {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['id' => $id, 'userid' => $userid]);
        if (!$record) {
            throw new \moodle_exception('errornotfound', 'local_diverse_assistant');
        }
        return $record;
    }

    /**
     * Proposals made in the given saved answers.
     *
     * @param int[] $messageids Message ids.
     * @return array messageid => \stdClass[] proposals, oldest first.
     */
    public static function get_for_messages(array $messageids): array {
        global $DB;
        $messageids = array_filter(array_map('intval', $messageids));
        if (!$messageids) {
            return [];
        }
        [$insql, $params] = $DB->get_in_or_equal($messageids);
        $result = [];
        foreach ($DB->get_records_select(self::TABLE, "messageid $insql", $params, 'id ASC') as $record) {
            $result[(int)$record->messageid][] = $record;
        }
        return $result;
    }

    /**
     * Record in which saved answer proposals were made.
     *
     * @param int[] $ids Proposal ids.
     * @param int $conversationid Conversation id.
     * @param int $messageid Id of the answer.
     */
    public static function link_to_message(array $ids, int $conversationid, int $messageid): void {
        global $DB;
        if (!$ids) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($ids);
        $DB->execute("UPDATE {" . self::TABLE . "} SET conversationid = ?, messageid = ? WHERE id $insql",
            array_merge([$conversationid, $messageid], $params));
    }

    /**
     * Change the status of a proposal.
     *
     * @param \stdClass $record The proposal; updated.
     * @param string $status New status.
     * @param array $fields Other fields to change.
     */
    public static function update(\stdClass $record, string $status, array $fields = []): void {
        global $DB;
        $record->status = $status;
        $record->timemodified = time();
        foreach ($fields as $name => $value) {
            $record->$name = $value;
        }
        $DB->update_record(self::TABLE, $record);
    }

    /**
     * Delete a user's proposals.
     *
     * @param int $userid User id.
     * @param int $courseid Only in this course; 0 for all courses.
     */
    public static function delete_for_user(int $userid, int $courseid = 0): void {
        global $DB;
        $conditions = ['userid' => $userid];
        if ($courseid) {
            $conditions['courseid'] = $courseid;
        }
        $DB->delete_records(self::TABLE, $conditions);
    }

    /**
     * Delete the proposals made in saved conversations that are being deleted.
     *
     * @param int[] $conversationids Conversation ids.
     */
    public static function delete_for_conversations(array $conversationids): void {
        global $DB;
        foreach (array_chunk($conversationids, 500) as $chunk) {
            [$insql, $params] = $DB->get_in_or_equal($chunk);
            $DB->delete_records_select(self::TABLE, "conversationid $insql", $params);
        }
    }

    /**
     * Delete the proposals of a course.
     *
     * @param int $courseid Course id.
     */
    public static function delete_for_course(int $courseid): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['courseid' => $courseid]);
    }

    /**
     * Delete proposals not changed for KEEP_DAYS.
     *
     * @return int How many were deleted.
     */
    public static function cleanup(): int {
        global $DB;
        $before = time() - self::KEEP_DAYS * DAYSECS;
        $count = $DB->count_records_select(self::TABLE, 'timemodified < ?', [$before]);
        $DB->delete_records_select(self::TABLE, 'timemodified < ?', [$before]);
        return $count;
    }

    /**
     * What the model is told about its earlier proposals, appended to its answer in the conversation history.
     *
     * @param \stdClass[] $records Proposals of one answer.
     * @return string Empty when there are none.
     */
    public static function history_note(array $records): string {
        if (!$records) {
            return '';
        }
        $items = [];
        foreach ($records as $record) {
            $proposed = json_decode($record->proposed, true) ?: [];
            $what = match ($record->action) {
                self::ACTION_NEW_PAGE => 'new page "' . ($proposed['name'] ?? '') . '"',
                self::ACTION_NEW_LABEL => 'new text and media area',
                self::ACTION_UPDATE_ACTIVITY => "new texts for [cmid={$record->cmid}] ("
                    . implode(', ', array_diff(array_keys($proposed), ['note'])) . ')',
                default => 'new texts for a section (' . implode(', ', array_diff(array_keys($proposed), ['note'])) . ')',
            };
            $items[] = "{$what}: {$record->status}";
        }
        return "\n\n[Proposals made in this answer, shown to the teacher as cards: " . implode('; ', $items) . '.]';
    }

    /**
     * A proposal for the panel.
     *
     * @param \stdClass $record The proposal.
     * @return array See export_structure().
     */
    public static function export(\stdClass $record): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $course = get_course($record->courseid);
        $modinfo = get_fast_modinfo($course);
        $coursecontext = \context_course::instance($course->id);
        $proposed = json_decode($record->proposed, true) ?: [];
        $original = $record->original === null ? null : (json_decode($record->original, true) ?: []);
        $pending = $record->status === self::STATUS_PENDING;

        $section = null;
        try {
            $section = $modinfo->get_section_info_by_id((int)$record->sectionid);
        } catch (\moodle_exception $e) {
            $section = null;
        }
        $cm = null;
        if ($record->cmid) {
            $cm = $modinfo->get_cms()[$record->cmid] ?? null;
            if ($cm && $cm->deletioninprogress) {
                $cm = null;
            }
        }
        $sectionname = $section ? get_section_name($course, $section) : '';
        $missing = !$section || ($record->action === self::ACTION_UPDATE_ACTIVITY && !$cm)
            || ($record->status === self::STATUS_APPLIED && in_array($record->action,
                [self::ACTION_NEW_PAGE, self::ACTION_NEW_LABEL], true) && !$cm);

        $changes = [];
        $prefill = [];
        $formurl = null;
        $canapply = false;
        switch ($record->action) {
            case self::ACTION_NEW_PAGE:
            case self::ACTION_NEW_LABEL:
                $ispage = $record->action === self::ACTION_NEW_PAGE;
                $modname = $ispage ? 'page' : 'label';
                $title = $ispage ? get_string('proposal_newpage', 'local_diverse_assistant', self::plain_name($proposed['name']))
                    : get_string('proposal_newlabel', 'local_diverse_assistant');
                if ($ispage) {
                    $changes[] = self::change('name', '', $proposed['name'], $coursecontext);
                    if (isset($proposed['description'])) {
                        $changes[] = self::change('description', '', $proposed['description'], $coursecontext);
                    }
                    $changes[] = self::change('content', '', $proposed['content'], $coursecontext);
                } else {
                    $changes[] = self::change('text', '', $proposed['description'], $coursecontext);
                }
                $canapply = $section && has_all_capabilities(['moodle/course:manageactivities', "mod/{$modname}:addinstance"],
                    $coursecontext);
                if ($section) {
                    $formurl = new \moodle_url('/course/modedit.php', ['add' => $modname, 'course' => $course->id,
                        'section' => $section->section, 'return' => 0]);
                    $prefill = self::prefill('/course/modedit.php',
                        ['add' => $modname, 'course' => $course->id, 'section' => $section->section], $proposed);
                }
                break;

            case self::ACTION_UPDATE_ACTIVITY:
                $name = $cm ? $cm->name : ($original['name'] ?? '');
                $modname = $cm ? $cm->modname : '';
                $type = $cm ? get_string('modulename', $cm->modname) : get_string('activity');
                $title = get_string('proposal_updateactivity', 'local_diverse_assistant',
                    ['type' => $type, 'name' => self::plain_name($name)]);
                $context = $cm ? $cm->context : $coursecontext;
                foreach (['name', 'description', 'content'] as $field) {
                    if (array_key_exists($field, $proposed)) {
                        $label = $field === 'description' && $modname === 'label' ? 'text' : $field;
                        $area = $field === 'content' ? ['mod_page', 'content', 0]
                            : ($field === 'description' ? ['mod_' . $modname, 'intro', 0] : null);
                        $changes[] = self::change($label, $original[$field] ?? '', $proposed[$field], $context, $area);
                    }
                }
                if ($cm) {
                    $canapply = in_array($cm->modname, course_content::DIRECT_EDIT_MODULES, true)
                        && has_capability('moodle/course:manageactivities', $cm->context);
                    $formurl = new \moodle_url('/course/modedit.php', ['update' => $cm->id, 'return' => 1]);
                    $prefill = self::prefill('/course/modedit.php', ['update' => $cm->id], $proposed);
                }
                break;

            default:
                $title = get_string('proposal_updatesection', 'local_diverse_assistant', self::plain_name($sectionname));
                $area = $section ? ['course', 'section', (int)$section->id] : null;
                if (array_key_exists('name', $proposed)) {
                    $changes[] = self::change('sectionname', $original['name'] ?? '', $proposed['name'], $coursecontext);
                }
                if (array_key_exists('summary', $proposed)) {
                    $changes[] = self::change('summary', $original['summary'] ?? '', $proposed['summary'], $coursecontext,
                        $area);
                }
                $canapply = $section && has_capability('moodle/course:update', $coursecontext);
                if ($section) {
                    $formurl = new \moodle_url('/course/editsection.php', ['id' => $section->id]);
                    $prefill = self::prefill('/course/editsection.php', ['id' => $section->id], $proposed);
                }
        }

        $viewurl = null;
        if ($record->status === self::STATUS_APPLIED && !$missing) {
            if ($cm && $cm->url) {
                $viewurl = $cm->url;
            } else if ($section) {
                $viewurl = course_get_url($course, $section->section);
            }
        }

        return [
            'id' => (int)$record->id,
            'action' => $record->action,
            'status' => $record->status,
            'statuslabel' => $missing ? get_string('proposal_missing', 'local_diverse_assistant')
                : get_string('proposal_status_' . $record->status, 'local_diverse_assistant'),
            'title' => $title,
            'where' => $sectionname === '' ? '' : get_string('proposal_where', 'local_diverse_assistant',
                self::plain_name($sectionname)),
            'note' => (string)($proposed['note'] ?? ''),
            'changes' => $changes,
            'canapply' => $pending && !$missing && $canapply,
            'canform' => $pending && !$missing && $formurl !== null,
            'canundo' => $record->status === self::STATUS_APPLIED && !$missing,
            'candiscard' => $pending,
            'formurl' => $formurl ? $formurl->out(false) : '',
            'viewurl' => $viewurl ? $viewurl->out(false) : '',
            'prefill' => json_encode($prefill),
        ];
    }

    /**
     * Structure of export() for web service functions.
     *
     * @return \core_external\external_single_structure
     */
    public static function export_structure(): \core_external\external_single_structure {
        return new \core_external\external_single_structure([
            'id' => new \core_external\external_value(PARAM_INT, 'Proposal id'),
            'action' => new \core_external\external_value(PARAM_ALPHA, 'newpage, newlabel, updateactivity or updatesection'),
            'status' => new \core_external\external_value(PARAM_ALPHA, 'pending, applied, undone or discarded'),
            'statuslabel' => new \core_external\external_value(PARAM_TEXT, 'Status for the teacher'),
            'title' => new \core_external\external_value(PARAM_TEXT, 'What the proposal changes'),
            'where' => new \core_external\external_value(PARAM_TEXT, 'Section of the change'),
            'note' => new \core_external\external_value(PARAM_TEXT, 'Why, in the assistant\'s words'),
            'changes' => new \core_external\external_multiple_structure(new \core_external\external_single_structure([
                'field' => new \core_external\external_value(PARAM_ALPHA, 'Field'),
                'label' => new \core_external\external_value(PARAM_TEXT, 'Name of the field'),
                'before' => new \core_external\external_value(PARAM_RAW, 'Text before, as safe HTML; empty for new items'),
                'after' => new \core_external\external_value(PARAM_RAW, 'Proposed text, as safe HTML'),
            ])),
            'canapply' => new \core_external\external_value(PARAM_BOOL, 'Whether it can be applied with one click'),
            'canform' => new \core_external\external_value(PARAM_BOOL, 'Whether it can be opened in the edit form'),
            'canundo' => new \core_external\external_value(PARAM_BOOL, 'Whether it can be undone'),
            'candiscard' => new \core_external\external_value(PARAM_BOOL, 'Whether it can be declined'),
            'formurl' => new \core_external\external_value(PARAM_URL, 'Edit form with the proposal'),
            'viewurl' => new \core_external\external_value(PARAM_URL, 'Where to see the applied change'),
            'prefill' => new \core_external\external_value(PARAM_RAW, 'JSON: which form fields to fill with what'),
        ]);
    }

    /**
     * One changed field for the card.
     *
     * @param string $field name, description, content, text, sectionname or summary.
     * @param string|null $before Stored text before; empty for new items.
     * @param string $after Proposed text.
     * @param \context $context Context of the texts.
     * @param array|null $area [component, filearea, itemid] of files the stored text links to.
     * @return array
     */
    private static function change(string $field, ?string $before, string $after, \context $context,
            ?array $area = null): array {
        $isname = in_array($field, ['name', 'sectionname'], true);
        return [
            'field' => $field,
            'label' => get_string('field_' . $field, 'local_diverse_assistant'),
            'before' => $isname ? self::preview_name((string)$before) : self::preview_html((string)$before, $context, $area),
            'after' => $isname ? self::preview_name($after) : self::preview_html($after, $context, $area),
        ];
    }

    /**
     * Which form fields to fill with what, for opening a proposal in Moodle's edit form.
     *
     * @param string $path Path of the form page.
     * @param array $params Parameters that identify the form.
     * @param array $proposed Proposed texts.
     * @return array ['match' => ['path' => ..., 'params' => ...], 'fields' => list of id, name, value, editor]
     */
    private static function prefill(string $path, array $params, array $proposed): array {
        $issection = $path === '/course/editsection.php';
        $fields = [];
        if (array_key_exists('name', $proposed)) {
            $fields[] = ['id' => 'id_name', 'name' => 'name', 'value' => (string)$proposed['name'], 'editor' => false];
        }
        if (array_key_exists('description', $proposed)) {
            $fields[] = ['id' => 'id_introeditor', 'name' => 'introeditor', 'value' => $proposed['description'],
                'editor' => true];
        }
        if (array_key_exists('content', $proposed)) {
            $fields[] = ['id' => 'id_page', 'name' => 'page', 'value' => $proposed['content'], 'editor' => true];
        }
        if ($issection && array_key_exists('summary', $proposed)) {
            $fields[] = ['id' => 'id_summary_editor', 'name' => 'summary_editor', 'value' => $proposed['summary'],
                'editor' => true];
        }
        return ['match' => ['path' => $path, 'params' => array_map('strval', $params)], 'fields' => $fields];
    }

    /**
     * Stored or proposed HTML as safe HTML for a card, showing every language of {mlang} blocks.
     *
     * @param string $html The text.
     * @param \context $context Its context.
     * @param array|null $area [component, filearea, itemid] of files it links to.
     * @return string
     */
    public static function preview_html(string $html, \context $context, ?array $area = null): string {
        if (trim($html) === '') {
            return '';
        }
        if ($area) {
            $html = file_rewrite_pluginfile_urls($html, 'pluginfile.php', $context->id, $area[0], $area[1], $area[2]);
        }
        return format_text(self::show_languages($html), FORMAT_HTML,
            ['context' => $context, 'filter' => false, 'para' => false, 'overflowdiv' => false]);
    }

    /**
     * A name as safe HTML for a card, showing every language of {mlang} blocks.
     *
     * @param string $name The name.
     * @return string
     */
    public static function preview_name(string $name): string {
        return trim($name) === '' ? '' : self::show_languages(s($name));
    }

    /**
     * Replace {mlang xx} markers with a small language label, so the teacher sees all languages.
     *
     * @param string $html HTML (or escaped text).
     * @return string
     */
    private static function show_languages(string $html): string {
        $html = preg_replace_callback('/\{mlang\s+([^}]+)\}/i',
            fn($m) => '<span class="badge text-bg-secondary local-diverse-assistant-lang">'
                . s(\core_text::strtoupper(trim($m[1]))) . '</span> ', $html);
        return preg_replace('/\{mlang\}/i', '', $html);
    }

    /**
     * A name as plain text for titles, in the teacher's language.
     *
     * @param string $name Stored name, may contain {mlang} blocks.
     * @return string
     */
    private static function plain_name(string $name): string {
        return trim(strip_tags(format_string($name, true, ['context' => \context_system::instance(), 'escape' => false])));
    }

    /**
     * A section by number.
     *
     * @param \course_modinfo $modinfo The course.
     * @param mixed $number Section number from the model.
     * @return \section_info
     * @throws \moodle_exception If there is no such section.
     */
    private static function get_section(\course_modinfo $modinfo, mixed $number): \section_info {
        $section = is_numeric($number) ? $modinfo->get_section_info((int)$number) : null;
        if (!$section) {
            throw new \moodle_exception('proposal_error_section', 'local_diverse_assistant', '', s((string)$number));
        }
        return $section;
    }

    /**
     * An activity of the course by id.
     *
     * @param \course_modinfo $modinfo The course.
     * @param mixed $cmid Id from the model.
     * @return \cm_info
     * @throws \moodle_exception If there is no such activity.
     */
    private static function get_cm(\course_modinfo $modinfo, mixed $cmid): \cm_info {
        $cm = is_numeric($cmid) ? ($modinfo->get_cms()[(int)$cmid] ?? null) : null;
        if (!$cm || $cm->deletioninprogress) {
            throw new \moodle_exception('proposal_error_activity', 'local_diverse_assistant', '', s((string)$cmid));
        }
        return $cm;
    }

    /**
     * Remove proposed fields that equal the stored ones.
     *
     * @param array $proposed Proposed fields.
     * @param array $original Stored fields.
     * @param string[] $exact Fields compared as they are; the others are compared as HTML, ignoring spacing.
     * @return array
     */
    private static function without_unchanged(array $proposed, array $original, array $exact): array {
        foreach ($proposed as $field => $value) {
            $old = (string)($original[$field] ?? '');
            $same = in_array($field, $exact, true) ? trim($value) === trim($old)
                : preg_replace('/\s+/', ' ', trim($value)) === preg_replace('/\s+/', ' ', trim($old));
            if ($same) {
                unset($proposed[$field]);
            }
        }
        return $proposed;
    }

    /**
     * A proposed name: one line of plain text.
     *
     * @param mixed $name From the model.
     * @return string
     */
    private static function clean_name(mixed $name): string {
        $name = is_string($name) ? $name : '';
        $name = trim(preg_replace('/\s+/u', ' ', clean_param(fix_utf8($name), PARAM_TEXT)));
        return \core_text::substr($name, 0, self::MAX_NAME_LENGTH);
    }

    /**
     * A proposed HTML text, cleaned like any text a user submits.
     *
     * @param mixed $html From the model.
     * @return string
     */
    private static function clean_html(mixed $html): string {
        $html = is_string($html) ? trim(fix_utf8($html)) : '';
        if ($html === '') {
            return '';
        }
        return trim(clean_text(\core_text::substr($html, 0, self::MAX_HTML_LENGTH), FORMAT_HTML));
    }

    /**
     * The model's note for the teacher.
     *
     * @param mixed $note From the model.
     * @return string
     */
    private static function clean_note(mixed $note): string {
        $note = is_string($note) ? trim(clean_param(fix_utf8($note), PARAM_TEXT)) : '';
        return \core_text::substr($note, 0, self::MAX_NOTE_LENGTH);
    }
}
