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
 * Applies and undoes proposals the teacher approved, through Moodle's own course functions.
 *
 * Moodle checks the teacher's permissions, logs the change, triggers its events and keeps deleted activities in the
 * recycle bin. Only pages, text and media areas and sections are changed here: other activities have settings that
 * only their edit form handles correctly, so their proposals are opened in that form instead.
 *
 * Before writing, the stored texts are compared with those the proposal was made for: if someone changed them in the
 * meantime, nothing is overwritten.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class applier {
    /**
     * Apply a pending proposal.
     *
     * @param \stdClass $record The proposal; updated.
     * @throws \moodle_exception With a message for the teacher.
     */
    public static function apply(\stdClass $record): void {
        self::require_status($record, proposals::STATUS_PENDING);
        $course = get_course($record->courseid);
        $proposed = json_decode($record->proposed, true) ?: [];
        unset($proposed['note']);

        switch ($record->action) {
            case proposals::ACTION_NEW_PAGE:
            case proposals::ACTION_NEW_LABEL:
                $section = self::get_section($course, (int)$record->sectionid);
                $cmid = self::create_activity($course, $section,
                    $record->action === proposals::ACTION_NEW_PAGE ? 'page' : 'label', $proposed);
                $cm = self::get_cm($course, $cmid);
                proposals::update($record, proposals::STATUS_APPLIED, [
                    'cmid' => $cmid,
                    'appliedhash' => course_content::hash(course_content::read_activity($cm)),
                ]);
                return;

            case proposals::ACTION_UPDATE_ACTIVITY:
                $cm = self::get_cm($course, (int)$record->cmid);
                if (!in_array($cm->modname, course_content::DIRECT_EDIT_MODULES, true)) {
                    throw new \moodle_exception('apply_error_formonly', 'local_diverse_assistant');
                }
                $original = json_decode($record->original, true);
                if (course_content::hash(course_content::read_activity($cm)) !== course_content::hash($original)) {
                    throw new \moodle_exception('apply_error_changed', 'local_diverse_assistant');
                }
                self::update_activity($course, $cm, $proposed);
                $cm = self::get_cm($course, (int)$record->cmid);
                proposals::update($record, proposals::STATUS_APPLIED, [
                    'appliedhash' => course_content::hash(course_content::read_activity($cm)),
                ]);
                return;

            case proposals::ACTION_UPDATE_SECTION:
                $section = self::get_section($course, (int)$record->sectionid);
                $original = json_decode($record->original, true);
                if (course_content::hash(course_content::read_section($section)) !== course_content::hash($original)) {
                    throw new \moodle_exception('apply_error_changed', 'local_diverse_assistant');
                }
                self::update_section($course, $section, $proposed);
                proposals::update($record, proposals::STATUS_APPLIED, [
                    'appliedhash' => course_content::hash(course_content::read_section(
                        self::get_section($course, (int)$record->sectionid))),
                ]);
                return;
        }
        throw new \moodle_exception('errornotfound', 'local_diverse_assistant');
    }

    /**
     * Undo an applied proposal: delete what it created or restore the texts it replaced.
     *
     * @param \stdClass $record The proposal; updated.
     * @throws \moodle_exception With a message for the teacher.
     */
    public static function undo(\stdClass $record): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        self::require_status($record, proposals::STATUS_APPLIED);
        $course = get_course($record->courseid);

        if ($record->action === proposals::ACTION_UPDATE_SECTION) {
            $section = self::get_section($course, (int)$record->sectionid);
            if (course_content::hash(course_content::read_section($section)) !== $record->appliedhash) {
                throw new \moodle_exception('undo_error_changed', 'local_diverse_assistant');
            }
            $original = json_decode($record->original, true);
            self::update_section($course, $section, $original);
            proposals::update($record, proposals::STATUS_UNDONE);
            return;
        }

        $cm = self::get_cm($course, (int)$record->cmid);
        $fields = course_content::read_activity($cm);
        if ($fields === null) {
            throw new \moodle_exception('proposal_error_nopermission', 'local_diverse_assistant');
        }
        if (course_content::hash($fields) !== $record->appliedhash) {
            throw new \moodle_exception('undo_error_changed', 'local_diverse_assistant');
        }
        if ($record->action === proposals::ACTION_UPDATE_ACTIVITY) {
            self::update_activity($course, $cm, json_decode($record->original, true));
        } else {
            // The same check as Moodle's own "Delete" link; the recycle bin keeps the activity if it is enabled.
            require_capability('moodle/course:manageactivities', $cm->context);
            course_delete_module($cm->id);
        }
        proposals::update($record, proposals::STATUS_UNDONE);
    }

    /**
     * Decline a pending proposal.
     *
     * @param \stdClass $record The proposal; updated.
     * @throws \moodle_exception If it was already handled.
     */
    public static function discard(\stdClass $record): void {
        self::require_status($record, proposals::STATUS_PENDING);
        proposals::update($record, proposals::STATUS_DISCARDED);
    }

    /**
     * Create a page or a text and media area at the end of a section.
     *
     * @param \stdClass $course The course.
     * @param \section_info $section The section.
     * @param string $modname page or label.
     * @param array $fields name, description, content.
     * @return int Id of the new activity.
     */
    private static function create_activity(\stdClass $course, \section_info $section, string $modname, array $fields): int {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/modlib.php');

        // The same defaults as Moodle's "Add an activity" form; also checks the teacher may add this activity.
        [, , , , $moduleinfo] = prepare_new_moduleinfo_data($course, $modname, $section->section);
        $moduleinfo->introeditor['text'] = $fields['description'] ?? '';
        $moduleinfo->introeditor['format'] = FORMAT_HTML;
        if ($modname === 'page') {
            // The page settings the form would propose.
            $config = get_config('page');
            $moduleinfo->name = $fields['name'];
            $moduleinfo->content = $fields['content'];
            $moduleinfo->contentformat = FORMAT_HTML;
            $moduleinfo->display = $config->display;
            $moduleinfo->popupwidth = $config->popupwidth;
            $moduleinfo->popupheight = $config->popupheight;
            $moduleinfo->printintro = $config->printintro;
            $moduleinfo->printlastmodified = $config->printlastmodified;
        }
        return (int)create_module($moduleinfo)->coursemodule;
    }

    /**
     * Change the texts of a page or a text and media area.
     *
     * The activity's data is loaded the way its edit form loads it (Moodle's get_moduleinfo_data() and the form's own
     * preprocessing), so every setting is saved back unchanged; only the given texts differ.
     *
     * @param \stdClass $course The course.
     * @param \cm_info $cm The activity.
     * @param array $fields name, description (+format), content (+format).
     */
    private static function update_activity(\stdClass $course, \cm_info $cm, array $fields): void {
        global $CFG, $COURSE;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->dirroot . '/mod/' . $cm->modname . '/mod_form.php');

        [$cmrecord, , $module, $data, $cw] = get_moduleinfo_data($cm->get_course_module_record(), $course);
        $formclass = 'mod_' . $module->name . '_mod_form';
        // The form reads the global course, which its own page (course/modedit.php) sets with require_login().
        $previouscourse = $COURSE;
        $COURSE = $course;
        try {
            $form = new $formclass($data, $cw->section, $cmrecord, $course);
        } finally {
            $COURSE = $previouscourse;
        }
        $values = (array)$data;
        $form->data_preprocessing($values);
        $moduleinfo = (object)$values;

        if (array_key_exists('name', $fields)) {
            $moduleinfo->name = $fields['name'];
        }
        if (array_key_exists('description', $fields)) {
            // Files of the old text are in the editor's draft area and are saved back; links to them stay valid.
            $moduleinfo->introeditor['text'] = $fields['description'];
            $moduleinfo->introeditor['format'] = $fields['descriptionformat'] ?? FORMAT_HTML;
        }
        if (array_key_exists('content', $fields)) {
            $moduleinfo->page['text'] = $fields['content'];
            $moduleinfo->page['format'] = $fields['contentformat'] ?? FORMAT_HTML;
        }
        // Moodle's function for saving an activity form: it checks the teacher may change this activity.
        update_module($moduleinfo);
    }

    /**
     * Change the name and/or summary of a section.
     *
     * @param \stdClass $course The course.
     * @param \section_info $section The section.
     * @param array $fields name (null for the default name), summary (+format).
     */
    private static function update_section(\stdClass $course, \section_info $section, array $fields): void {
        // The same check as the section edit form.
        require_capability('moodle/course:update', \context_course::instance($course->id));
        $data = [];
        if (array_key_exists('name', $fields)) {
            $data['name'] = $fields['name'];
        }
        if (array_key_exists('summary', $fields)) {
            $data['summary'] = $fields['summary'];
            $data['summaryformat'] = $fields['summaryformat'] ?? FORMAT_HTML;
        }
        \core_courseformat\formatactions::section($course)->update($section, $data);
    }

    /**
     * Stop unless the proposal has the given status.
     *
     * @param \stdClass $record The proposal.
     * @param string $status Required status.
     * @throws \moodle_exception
     */
    private static function require_status(\stdClass $record, string $status): void {
        if ($record->status !== $status) {
            throw new \moodle_exception('apply_error_status', 'local_diverse_assistant');
        }
    }

    /**
     * A section of the course by id, from fresh course data.
     *
     * @param \stdClass $course The course.
     * @param int $sectionid Section id.
     * @return \section_info
     * @throws \moodle_exception If it no longer exists.
     */
    private static function get_section(\stdClass $course, int $sectionid): \section_info {
        get_fast_modinfo($course->id, 0, true);
        $section = get_fast_modinfo($course->id)->get_section_info_by_id($sectionid);
        if (!$section) {
            throw new \moodle_exception('apply_error_missing', 'local_diverse_assistant');
        }
        return $section;
    }

    /**
     * An activity of the course, from fresh course data.
     *
     * @param \stdClass $course The course.
     * @param int $cmid Activity id.
     * @return \cm_info
     * @throws \moodle_exception If it no longer exists.
     */
    private static function get_cm(\stdClass $course, int $cmid): \cm_info {
        get_fast_modinfo($course->id, 0, true);
        $cm = get_fast_modinfo($course->id)->get_cms()[$cmid] ?? null;
        if (!$cm || $cm->deletioninprogress) {
            throw new \moodle_exception('apply_error_missing', 'local_diverse_assistant');
        }
        return $cm;
    }
}
