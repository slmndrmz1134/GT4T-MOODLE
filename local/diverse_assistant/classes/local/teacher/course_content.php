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
 * The course content as a teacher edits it: the stored HTML of names, descriptions and pages.
 *
 * Unlike the students' course materials, nothing is filtered: {mlang} blocks of every language and @@PLUGINFILE@@
 * links stay as they are, so an edited text keeps them. Hidden activities are included (the teacher sees them).
 * Only text written by teachers is read; nothing written by students.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_content {
    /** Activities whose texts the assistant can change with one click; others are changed in their edit form. */
    public const DIRECT_EDIT_MODULES = ['page', 'label'];

    /**
     * The course content for the assistant, and which items it contains in full.
     *
     * @param \stdClass $course The course.
     * @param int $currentcmid Activity the teacher is looking at (it comes first), 0 on the course page.
     * @param int $maxchars Longest text; items that do not fit are listed without their texts.
     * @return array ['text' => string, 'cmids' => int[], 'sectionids' => int[]]: the text and the activities and
     *     sections whose texts are in it in full, which are the only ones the assistant may propose to change.
     */
    public static function build(\stdClass $course, int $currentcmid, int $maxchars): array {
        $modinfo = get_fast_modinfo($course);
        $coursecontext = \context_course::instance($course->id);
        $canupdatesections = has_capability('moodle/course:update', $coursecontext);
        $budget = $maxchars;
        $cmids = [];
        $sectionids = [];

        $parts = ['# COURSE: ' . $course->fullname];
        $summary = trim(html_to_text((string)$course->summary, 0, false));
        if ($summary !== '') {
            $parts[] = "Course summary (for information, cannot be changed here):\n" . $summary;
        }

        $current = null;
        if ($currentcmid) {
            try {
                $current = $modinfo->get_cm($currentcmid);
            } catch (\moodle_exception $e) {
                $current = null;
            }
        }
        if ($current && $current->uservisible && !$current->deletioninprogress) {
            $block = self::activity_block($current, true);
            if ($block['fields'] !== null && \core_text::strlen($block['text']) <= $budget) {
                $budget -= \core_text::strlen($block['text']);
                $cmids[] = (int)$current->id;
                $parts[] = "## CURRENT ACTIVITY (the teacher is looking at it now)\n\n" . $block['text'];
            } else {
                $parts[] = "## CURRENT ACTIVITY (the teacher is looking at it now)\n\n" . $block['header']
                    . "\n(Its texts are too long to include.)";
            }
        }

        $parts[] = '## COURSE OUTLINE AND CONTENT';
        foreach ($modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible) {
                continue;
            }
            $sectionblock = self::section_block($course, $section, $canupdatesections);
            if ($canupdatesections && \core_text::strlen($sectionblock['text']) <= $budget) {
                $budget -= \core_text::strlen($sectionblock['text']);
                $sectionids[] = (int)$section->id;
                $parts[] = $sectionblock['text'];
            } else {
                $parts[] = $sectionblock['header'] . ($canupdatesections ? "\n(Texts not included: the content is too long.)" : '');
            }

            foreach ($modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if (!$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                if ($current && (int)$cm->id === (int)$current->id) {
                    $parts[] = self::activity_header($cm) . "\n(See CURRENT ACTIVITY above.)";
                    continue;
                }
                $block = self::activity_block($cm, false);
                if ($block['fields'] === null) {
                    $parts[] = $block['header'] . "\n(You cannot change this activity.)";
                } else if (\core_text::strlen($block['text']) <= $budget) {
                    $budget -= \core_text::strlen($block['text']);
                    $cmids[] = (int)$cm->id;
                    $parts[] = $block['text'];
                } else {
                    $parts[] = $block['header'] . "\n(Texts not included: the content is too long. To change it, the "
                        . "teacher can open this activity and ask there.)";
                }
            }
        }

        return [
            'text' => implode("\n\n", $parts),
            'cmids' => $cmids,
            'sectionids' => $sectionids,
        ];
    }

    /**
     * The stored texts of an activity, if the current user may change it.
     *
     * @param \cm_info $cm The activity.
     * @return array|null name, and description and descriptionformat (activities with a description), and content
     *     and contentformat (pages); null if the user cannot change the activity.
     */
    public static function read_activity(\cm_info $cm): ?array {
        global $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');
        try {
            // Moodle's own check before editing an activity; it also returns the stored record.
            [, , , $record] = can_update_moduleinfo($cm);
        } catch (\moodle_exception $e) {
            return null;
        }
        $fields = ['name' => (string)$record->name];
        if (plugin_supports('mod', $cm->modname, FEATURE_MOD_INTRO, true)) {
            $fields['description'] = (string)($record->intro ?? '');
            $fields['descriptionformat'] = (int)($record->introformat ?? FORMAT_HTML);
        }
        if ($cm->modname === 'page') {
            $fields['content'] = (string)($record->content ?? '');
            $fields['contentformat'] = (int)($record->contentformat ?? FORMAT_HTML);
        }
        return $fields;
    }

    /**
     * The stored texts of a section.
     *
     * @param \section_info $section The section.
     * @return array name (null when the section shows its default name), summary and summaryformat.
     */
    public static function read_section(\section_info $section): array {
        return [
            'name' => $section->name === null || $section->name === '' ? null : (string)$section->name,
            'summary' => (string)$section->summary,
            'summaryformat' => (int)$section->summaryformat,
        ];
    }

    /**
     * A fingerprint of stored texts, to notice changes made by someone else in the meantime.
     *
     * @param array|null $fields From read_activity() or read_section().
     * @return string
     */
    public static function hash(?array $fields): string {
        if ($fields === null) {
            return '';
        }
        ksort($fields);
        return sha1(json_encode($fields));
    }

    /**
     * The block of an activity.
     *
     * @param \cm_info $cm The activity.
     * @param bool $current Whether the teacher is looking at it.
     * @return array ['header' => string, 'text' => string, 'fields' => array|null]
     */
    private static function activity_block(\cm_info $cm, bool $current): array {
        $header = self::activity_header($cm);
        $fields = self::read_activity($cm);
        if ($fields === null) {
            return ['header' => $header, 'text' => $header, 'fields' => null];
        }
        $lines = [$header];
        if ($cm->modname === 'label') {
            $lines[] = "Text (HTML, field \"description\"):\n" . self::field_text($fields['description'] ?? '');
        } else {
            $lines[] = 'Name: ' . $fields['name'];
            if (array_key_exists('description', $fields)) {
                $lines[] = "Description (HTML):\n" . self::field_text($fields['description']);
            }
            if (array_key_exists('content', $fields)) {
                $lines[] = "Content (HTML):\n" . self::field_text($fields['content']);
            }
        }
        if (!in_array($cm->modname, self::DIRECT_EDIT_MODULES, true)) {
            $lines[] = '(Changes to this activity open its edit form for the teacher to save.)';
        }
        return ['header' => $header, 'text' => implode("\n", $lines), 'fields' => $fields];
    }

    /**
     * The heading line of an activity, with its id and visibility.
     *
     * @param \cm_info $cm The activity.
     * @return string
     */
    private static function activity_header(\cm_info $cm): string {
        $notes = [];
        if (!$cm->visible) {
            $notes[] = 'hidden from students';
        } else if (!$cm->visibleoncoursepage) {
            $notes[] = 'not shown on the course page';
        }
        if (!empty($cm->availability)) {
            $notes[] = 'has access restrictions';
        }
        $type = get_string('modulename', $cm->modname);
        return "### [cmid={$cm->id}] {$type} ({$cm->modname}): \"{$cm->name}\""
            . ($notes ? ' (' . implode(', ', $notes) . ')' : '');
    }

    /**
     * The block of a section.
     *
     * @param \stdClass $course The course.
     * @param \section_info $section The section.
     * @param bool $editable Whether the user may change sections.
     * @return array ['header' => string, 'text' => string]
     */
    private static function section_block(\stdClass $course, \section_info $section, bool $editable): array {
        $fields = self::read_section($section);
        $notes = [];
        if (!$section->visible) {
            $notes[] = 'hidden from students';
        }
        if ($section->is_delegated()) {
            $notes[] = 'subsection';
        }
        if (!$editable) {
            $notes[] = 'you cannot change sections';
        }
        $displayname = trim(strip_tags(get_section_name($course, $section)));
        $header = "## [section={$section->section}] {$displayname}" . ($notes ? ' (' . implode(', ', $notes) . ')' : '');
        $lines = [$header];
        $lines[] = 'Section name: ' . ($fields['name'] ?? '(none, the default name is shown)');
        $lines[] = "Section summary (HTML):\n" . self::field_text($fields['summary']);
        return ['header' => $header, 'text' => implode("\n", $lines)];
    }

    /**
     * A stored text as the model sees it.
     *
     * @param string $text The text.
     * @return string
     */
    private static function field_text(string $text): string {
        $text = trim($text);
        return $text === '' ? '(empty)' : $text;
    }
}
