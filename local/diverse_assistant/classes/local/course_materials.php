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

namespace local_diverse_assistant\local;

use core_external\external_api;
use core_external\external_settings;

/**
 * The course materials sent to the AI service with each question, as plain text.
 *
 * Privacy rules:
 * - Only what the current user can see: hidden and restricted activities are left out (Moodle's own visibility checks).
 * - Only text written by teachers: the course and section descriptions, page contents and activity descriptions.
 *   Forum posts, submissions, quiz questions and anything else written by students are never included.
 * - The text is read through each activity's public web service functions, not from its database tables, and with
 *   filters on, so the multi-language filter leaves only the user's language.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_materials {
    /**
     * Activities whose teacher-written text is included, and the web service function that returns it.
     *
     * file: file to include for older external classes; key: list in the result ('' if the result is the list);
     * cmfield: course module id field; fields: text fields to include.
     */
    private const SOURCES = [
        'page' => ['class' => 'mod_page_external', 'method' => 'get_pages_by_courses', 'key' => 'pages',
            'fields' => ['intro', 'content']],
        'label' => ['class' => 'mod_label_external', 'method' => 'get_labels_by_courses', 'key' => 'labels',
            'fields' => ['intro']],
        'book' => ['class' => 'mod_book_external', 'method' => 'get_books_by_courses', 'key' => 'books',
            'fields' => ['intro']],
        'url' => ['class' => 'mod_url_external', 'method' => 'get_urls_by_courses', 'key' => 'urls',
            'fields' => ['intro', 'externalurl']],
        'resource' => ['class' => 'mod_resource_external', 'method' => 'get_resources_by_courses', 'key' => 'resources',
            'fields' => ['intro']],
        'folder' => ['class' => 'mod_folder_external', 'method' => 'get_folders_by_courses', 'key' => 'folders',
            'fields' => ['intro']],
        'quiz' => ['class' => 'mod_quiz_external', 'method' => 'get_quizzes_by_courses', 'key' => 'quizzes',
            'fields' => ['intro']],
        'forum' => ['file' => '/mod/forum/externallib.php', 'class' => 'mod_forum_external',
            'method' => 'get_forums_by_courses', 'key' => '', 'cmfield' => 'cmid', 'fields' => ['intro']],
    ];

    /**
     * The materials of a course as seen by the current user.
     *
     * @param \stdClass $course The course.
     * @param int $currentcmid Activity the user is looking at (its text comes first), 0 on the course page.
     * @param int $maxchars Longest result; the rest is cut off.
     * @return string
     */
    public static function build(\stdClass $course, int $currentcmid, int $maxchars): string {
        $modinfo = get_fast_modinfo($course);
        $context = \context_course::instance($course->id);
        $texts = self::collect_texts($course->id, $modinfo);

        $parts = ['# ' . self::plain(format_string($course->fullname, true, ['context' => $context]))];
        $summary = self::html_to_plain(format_text($course->summary ?? '', $course->summaryformat ?? FORMAT_HTML,
            ['context' => $context]));
        if ($summary !== '') {
            $parts[] = $summary;
        }

        $current = '';
        $sections = [];
        foreach ($modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible) {
                continue;
            }
            $lines = [];
            $sectionsummary = self::html_to_plain(format_text($section->summary ?? '', $section->summaryformat ?? FORMAT_HTML,
                ['context' => $context]));
            if ($sectionsummary !== '') {
                $lines[] = $sectionsummary;
            }
            foreach ($modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if (!$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                $block = self::activity_block($cm, $texts[$cm->id] ?? []);
                if ((int)$cm->id === $currentcmid) {
                    $current = $block;
                }
                $lines[] = $block;
            }
            if ($lines) {
                array_unshift($lines, '## ' . self::plain(get_section_name($course, $section)));
                $sections[] = implode("\n\n", $lines);
            }
        }

        if ($current !== '') {
            $parts[] = "## CURRENT PAGE (the student is looking at this now)\n\n" . $current;
        }
        $text = preg_replace("/\n{3,}/", "\n\n", implode("\n\n", array_merge($parts, $sections)));
        if (\core_text::strlen($text) > $maxchars) {
            $text = \core_text::substr($text, 0, $maxchars) . "\n\n[The rest of the course materials was cut off because of length.]";
        }
        return $text;
    }

    /**
     * Text of one activity.
     *
     * @param \cm_info $cm The activity.
     * @param array $fields Its text fields from the web service, as HTML.
     * @return string
     */
    private static function activity_block(\cm_info $cm, array $fields): string {
        $body = [];
        foreach ($fields as $name => $value) {
            $text = $name === 'externalurl' ? trim((string)$value) : self::html_to_plain((string)$value);
            if ($text !== '') {
                $body[] = $text;
            }
        }
        if (!$body && $cm->modname === 'label') {
            $body[] = self::html_to_plain($cm->get_formatted_content());
        }
        if ($cm->modname === 'label') {
            return implode("\n\n", $body);
        }
        $type = get_string('modulename', $cm->modname);
        $heading = '### ' . $type . ': ' . self::plain($cm->get_formatted_name());
        return implode("\n\n", array_merge([$heading], $body));
    }

    /**
     * Teacher-written text of the course's activities, keyed by course module id.
     *
     * @param int $courseid Course id.
     * @param \course_modinfo $modinfo The course's modules as seen by the current user.
     * @return array cmid => [field => HTML]
     */
    private static function collect_texts(int $courseid, \course_modinfo $modinfo): array {
        global $CFG;

        $texts = [];
        // Run the filters, so the multi-language filter keeps only the user's language.
        $settings = external_settings::get_instance();
        $previousfilter = $settings->get_filter();
        $settings->set_filter(true);
        try {
            foreach (self::SOURCES as $modname => $source) {
                if (!$modinfo->get_instances_of($modname)) {
                    continue;
                }
                if (!empty($source['file'])) {
                    require_once($CFG->dirroot . $source['file']);
                }
                try {
                    $result = call_user_func([$source['class'], $source['method']], [$courseid]);
                    $result = external_api::clean_returnvalue(
                        call_user_func([$source['class'], $source['method'] . '_returns']), $result);
                } catch (\Throwable $e) {
                    debugging("local_diverse_assistant: could not read {$modname} texts: " . $e->getMessage(),
                        DEBUG_DEVELOPER);
                    continue;
                }
                $items = $source['key'] === '' ? $result : ($result[$source['key']] ?? []);
                $cmfield = $source['cmfield'] ?? 'coursemodule';
                foreach ($items as $item) {
                    $cmid = (int)($item[$cmfield] ?? 0);
                    foreach ($source['fields'] as $field) {
                        if (isset($item[$field]) && $item[$field] !== '') {
                            $texts[$cmid][$field] = $item[$field];
                        }
                    }
                }
            }
            $texts += self::collect_assignment_texts($courseid, $modinfo);
        } finally {
            $settings->set_filter($previousfilter);
        }
        return $texts;
    }

    /**
     * Descriptions of assignments (the task the teacher set, never the submissions).
     *
     * @param int $courseid Course id.
     * @param \course_modinfo $modinfo The course's modules.
     * @return array cmid => ['intro' => HTML]
     */
    private static function collect_assignment_texts(int $courseid, \course_modinfo $modinfo): array {
        global $CFG;
        if (!$modinfo->get_instances_of('assign')) {
            return [];
        }
        require_once($CFG->dirroot . '/mod/assign/externallib.php');
        try {
            $result = external_api::clean_returnvalue(\mod_assign_external::get_assignments_returns(),
                \mod_assign_external::get_assignments([$courseid]));
        } catch (\Throwable $e) {
            debugging('local_diverse_assistant: could not read assignment texts: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return [];
        }
        $texts = [];
        foreach ($result['courses'] ?? [] as $course) {
            foreach ($course['assignments'] ?? [] as $assignment) {
                if (!empty($assignment['intro'])) {
                    $texts[(int)$assignment['cmid']] = ['intro' => $assignment['intro']];
                }
            }
        }
        return $texts;
    }

    /**
     * HTML to plain text.
     *
     * @param string $html HTML.
     * @return string
     */
    private static function html_to_plain(string $html): string {
        if (trim($html) === '') {
            return '';
        }
        // Markdown marks the model understands; the converter would otherwise upper-case bold text and headings.
        $html = preg_replace('~<(strong|b)\b[^>]*>(.*?)</\1>~is', '**$2**', $html);
        $html = preg_replace_callback('~<h([1-6])\b[^>]*>(.*?)</h\1>~is',
            fn($matches) => '<p>' . str_repeat('#', min(6, (int)$matches[1] + 3)) . ' ' . strip_tags($matches[2]) . '</p>',
            $html);
        // Paragraphs inside list items put the bullet on a line of its own; quote marks add noise.
        $html = preg_replace(['~<li\b[^>]*>\s*<p\b[^>]*>~i', '~</p>\s*</li>~i', '~</?blockquote\b[^>]*>~i'],
            ['<li>', '</li>', ''], $html);
        return trim(html_to_text($html, 0, false));
    }

    /**
     * A formatted one-line string (name, title) to plain text.
     *
     * @param string $string Formatted string.
     * @return string
     */
    private static function plain(string $string): string {
        return trim(html_entity_decode(strip_tags($string), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
