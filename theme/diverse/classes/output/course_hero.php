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

namespace theme_diverse\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;

/**
 * Theme DIVERSE - Header of the course page: cover, teachers, progress and the next step.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_hero implements renderable, templatable {
    /** @var \stdClass */
    protected $course;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     */
    public function __construct(\stdClass $course) {
        $this->course = $course;
    }

    /**
     * A name for a template: filtered, and not escaped here because Mustache escapes it.
     *
     * @param string|null $text
     * @return string
     */
    protected static function text(?string $text): string {
        return format_string((string)$text, true, ['context' => \context_system::instance(), 'escape' => false]);
    }

    /**
     * Export the header.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $course = $this->course;
        $context = \context_course::instance($course->id);
        $teaches = has_capability('moodle/grade:viewall', $context);
        $progress = $teaches ? null : \core_completion\progress::get_course_progress_percentage($course);

        $modinfo = get_fast_modinfo($course);
        $activities = self::activities($course);

        $sections = 0;
        foreach ($modinfo->get_section_info_all() as $section) {
            if ($section->section > 0 && $section->uservisible && !empty($modinfo->sections[$section->section])) {
                $sections++;
            }
        }

        $next = $teaches ? null : self::next_activity($course);
        $element = new \core_course_list_element($course);
        $teachers = [];
        foreach ($element->get_course_contacts() as $contact) {
            $teachers[] = $contact['username'];
        }
        $summary = trim(html_to_text(format_text((string)$course->summary, $course->summaryformat), 0, false));

        return landing::course_cover($element) + [
            'name' => self::text($course->fullname),
            'category' => self::category_name($course),
            'summary' => shorten_text($summary, 240),
            'teachers' => implode(', ', array_unique($teachers)),
            'hasprogress' => $progress !== null,
            'progress' => $progress !== null ? (int)floor($progress) : 0,
            'stats' => get_string('hero_stats', 'theme_diverse', (object)[
                'sections' => $sections,
                'activities' => count($activities),
            ]),
            'people' => count_enrolled_users($context),
            'hasnext' => $next !== null,
            'nexturl' => $next ? $next->url->out(false) : '',
            'nextname' => $next ? self::text($next->name) : '',
            'nextlabel' => get_string(($progress ?? 0) > 0 ? 'hero_continue' : 'hero_start', 'theme_diverse'),
        ];
    }

    /**
     * The bar above an activity: where the user is in the course and the way on.
     *
     * @param \stdClass $course
     * @param \cm_info $cm
     * @return array
     */
    public static function lesson_bar(\stdClass $course, \cm_info $cm): array {
        $activities = array_values(self::activities($course));
        $index = array_search($cm->id, array_map(fn($item) => $item->id, $activities));
        $teaches = has_capability('moodle/grade:viewall', \context_course::instance($course->id));
        $progress = $teaches ? null : \core_completion\progress::get_course_progress_percentage($course);
        $previous = $index > 0 ? $activities[$index - 1] : null;
        $next = $index !== false && isset($activities[$index + 1]) ? $activities[$index + 1] : null;

        return [
            'courseurl' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
            'coursename' => self::text($course->fullname),
            'position' => $index === false ? '' : ($index + 1) . ' / ' . count($activities),
            'hasprogress' => $progress !== null,
            'progress' => $progress !== null ? (int)floor($progress) : 0,
            'hasprevious' => $previous !== null,
            'previousurl' => $previous ? $previous->url->out(false) : '',
            'hasnext' => $next !== null,
            'nexturl' => $next ? $next->url->out(false) : '',
        ];
    }

    /**
     * The activities the user can open, in course order.
     *
     * @param \stdClass $course
     * @return \cm_info[]
     */
    public static function activities(\stdClass $course): array {
        $modinfo = get_fast_modinfo($course);
        $activities = [];
        foreach ($modinfo->sections as $cmids) {
            foreach ($cmids as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if ($cm->uservisible && $cm->has_view() && $cm->url) {
                    $activities[$cmid] = $cm;
                }
            }
        }
        return $activities;
    }

    /**
     * The first activity the user has not completed, or simply the first one in a course without completion tracking.
     *
     * @param \stdClass $course
     * @return \cm_info|null
     */
    public static function next_activity(\stdClass $course): ?\cm_info {
        $activities = self::activities($course);
        $completion = new \completion_info($course);
        $tracked = false;
        foreach ($activities as $cm) {
            if ($completion->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
                continue;
            }
            $tracked = true;
            if ($completion->get_data($cm, true)->completionstate == COMPLETION_INCOMPLETE) {
                return $cm;
            }
        }
        // All tracked work is done, or nothing is tracked: only a course without tracking offers a start.
        return $tracked ? null : (reset($activities) ?: null);
    }

    /**
     * Name of the course category, if the user may see it.
     *
     * @param \stdClass $course
     * @return string
     */
    public static function category_name(\stdClass $course): string {
        $category = \core_course_category::get($course->category, IGNORE_MISSING, true);
        return $category ? $category->get_formatted_name() : '';
    }
}
