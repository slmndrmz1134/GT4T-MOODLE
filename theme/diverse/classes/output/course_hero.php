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
        $activities = array_filter($modinfo->get_cms(), fn($cm) => $cm->uservisible && $cm->has_view());

        $sections = 0;
        foreach ($modinfo->get_section_info_all() as $section) {
            if ($section->section > 0 && $section->uservisible && !empty($modinfo->sections[$section->section])) {
                $sections++;
            }
        }

        $next = $teaches ? null : $this->next_activity($activities);
        $teachers = [];
        foreach ((new \core_course_list_element($course))->get_course_contacts() as $contact) {
            $teachers[] = $contact['username'];
        }

        return self::cover($course, $output) + [
            'name' => self::text($course->fullname),
            'category' => self::category_name($course),
            'teachers' => implode(', ', array_unique($teachers)),
            'hasprogress' => $progress !== null,
            'progress' => $progress !== null ? (int)floor($progress) : 0,
            'stats' => get_string('hero_stats', 'theme_diverse', (object)[
                'sections' => $sections,
                'activities' => count($activities),
            ]),
            'hasnext' => $next !== null,
            'nexturl' => $next ? $next->url->out(false) : '',
            'nextname' => $next ? self::text($next->name) : '',
            'nextlabel' => get_string(($progress ?? 0) > 0 ? 'hero_continue' : 'hero_start', 'theme_diverse'),
        ];
    }

    /**
     * The first activity the user has not completed, or simply the first one without completion tracking.
     *
     * @param \cm_info[] $activities In course order.
     * @return \cm_info|null
     */
    protected function next_activity(array $activities): ?\cm_info {
        $completion = new \completion_info($this->course);
        foreach ($activities as $cm) {
            if ($completion->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
                continue;
            }
            $state = $completion->get_data($cm, true)->completionstate;
            if ($state == COMPLETION_INCOMPLETE) {
                return $cm;
            }
        }
        // All tracked work is done, or nothing is tracked: only a course without tracking offers a start.
        $tracked = array_filter($activities, fn($cm) => $completion->is_enabled($cm) != COMPLETION_TRACKING_NONE);
        return $tracked ? null : (reset($activities) ?: null);
    }

    /**
     * The cover of a course: its overview image, or Moodle's pattern in DIVERSE colours. A file missing from
     * moodledata falls back to the pattern, so that no broken image is shown.
     *
     * @param \stdClass $course
     * @param renderer_base $output
     * @return array coverurl
     */
    public static function cover(\stdClass $course, renderer_base $output): array {
        $filesystem = get_file_storage()->get_file_system();
        foreach ((new \core_course_list_element($course))->get_course_overviewfiles() as $file) {
            if ($filesystem->is_file_readable_locally_by_storedfile($file) && $file->is_valid_image()) {
                $url = \core\url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                );
                return ['coverurl' => $url->out(false)];
            }
        }
        return ['coverurl' => $output->get_generated_image_for_id($course->id)];
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
