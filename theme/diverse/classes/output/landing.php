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
use core_course_category;
use core_course_list_element;

/**
 * Theme DIVERSE - Data for the landing page and the login brand panel.
 *
 * All figures are read live from the site: partners are the active tenants of tool_mutenancy,
 * courses and categories are filtered by what the current (usually anonymous) visitor may see.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class landing implements renderable, templatable {
    /** @var int Number of courses shown in the "Explore courses" section. */
    const FEATURED_COURSES = 4;

    /** @var string[] Cover styles cycled through for courses without a readable overview image. */
    const COVER_STYLES = ['ink', 'brand', 'peach', 'soft'];

    /** @var string[] Pattern shape drawn on each cover style, in the same order as COVER_STYLES. */
    const COVER_SHAPES = ['ring', 'plus', 'half', 'hash'];

    /**
     * Export the landing page data.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG, $DB;

        $partners = $this->get_partners();
        $courses = $this->get_featured_courses();
        $coursecount = $DB->count_records_select('course', 'id <> :siteid AND visible = 1', ['siteid' => SITEID]);
        $usercount = $DB->count_records_select(
            'user',
            'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id <> :guestid',
            ['guestid' => $CFG->siteguest]
        );

        return [
            'partners' => $partners,
            'haspartners' => !empty($partners),
            'partnercount' => count($partners),
            'coursecount' => $coursecount,
            'usercount' => $usercount,
            'courses' => $courses,
            'hascourses' => !empty($courses),
            'loginurl' => (new \core\url('/login/index.php'))->out(false),
            'allcoursesurl' => (new \core\url('/course/index.php'))->out(false),
            'privacyurl' => (new \core\url('/admin/tool/dataprivacy/summary.php'))->out(false),
            'homeurl' => (new \core\url('/'))->out(false),
            'logourl' => $output->image_url('logo', 'theme_diverse')->out(false),
        ];
    }

    /**
     * Export the smaller data set for the brand panel next to the login form.
     *
     * @return array
     */
    public function export_for_login(): array {
        $partners = $this->get_partners();
        return [
            'partners' => $partners,
            'haspartners' => !empty($partners),
            'homeurl' => (new \core\url('/'))->out(false),
        ];
    }

    /**
     * Get the partner universities: the active tenants that are listed on the login page.
     *
     * Tenant categories are hidden from visitors by tool_mutenancy, so only the tenant name and the
     * number of visible courses are shown (the same names the login page's tenant selector shows),
     * and each card links to the tenant's own login page.
     *
     * @return array
     */
    protected function get_partners(): array {
        global $DB;

        if (
            !\core_component::get_component_directory('tool_mutenancy') ||
                !$DB->get_manager()->table_exists('tool_mutenancy_tenant')
        ) {
            return [];
        }

        $partners = [];
        $records = $DB->get_records(
            'tool_mutenancy_tenant',
            ['archived' => 0, 'loginshow' => 1],
            'name ASC',
            'id, name, categoryid'
        );
        foreach ($records as $record) {
            $path = $DB->get_field('course_categories', 'path', ['id' => $record->categoryid]);
            if ($path === false) {
                continue;
            }
            $coursecount = $DB->count_records_sql(
                "SELECT COUNT(c.id)
                   FROM {course} c
                   JOIN {course_categories} cc ON cc.id = c.category
                  WHERE c.visible = 1 AND (cc.path = :path OR " . $DB->sql_like('cc.path', ':pathlike') . ")",
                ['path' => $path, 'pathlike' => $path . '/%']
            );
            $loginurl = \tool_mutenancy\local\tenant::get_login_url($record->id) ?? new \core\url('/login/index.php');
            $partners[] = [
                'name' => format_string($record->name, true, ['context' => \core\context\system::instance()]),
                'coursecount' => $coursecount,
                'url' => $loginurl->out(false),
            ];
        }
        return $partners;
    }

    /**
     * Pick the featured courses, taking one course from each top-level category in turn
     * so that every partner (and the shared courses) is represented.
     *
     * @return array
     */
    protected function get_featured_courses(): array {
        $groups = [];
        $courses = core_course_category::top()->get_courses(['recursive' => true, 'sort' => ['sortorder' => 1]]);
        foreach ($courses as $course) {
            $category = core_course_category::get($course->category, IGNORE_MISSING);
            if (!$category) {
                continue;
            }
            $topcategoryid = explode('/', trim($category->path, '/'))[0];
            $groups[$topcategoryid][] = [$course, $category];
        }

        $picked = [];
        while (count($picked) < self::FEATURED_COURSES && !empty($groups)) {
            foreach ($groups as $key => $list) {
                $picked[] = array_shift($groups[$key]);
                if (empty($groups[$key])) {
                    unset($groups[$key]);
                }
                if (count($picked) >= self::FEATURED_COURSES) {
                    break;
                }
            }
        }

        $items = [];
        foreach ($picked as $index => [$course, $category]) {
            $imageurl = $this->get_course_image_url($course);
            $shape = self::COVER_SHAPES[$index % count(self::COVER_SHAPES)];
            $items[] = [
                'fullname' => $course->get_formatted_fullname(),
                'shortname' => $course->get_formatted_shortname(),
                'category' => $category->get_formatted_name(),
                'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
                'imageurl' => $imageurl,
                'hasimage' => $imageurl !== null,
                'coverstyle' => self::COVER_STYLES[$index % count(self::COVER_STYLES)],
                'is' . $shape => true,
            ];
        }
        return $items;
    }

    /**
     * Get the URL of the course overview image, but only if its file can actually be read,
     * so that a missing file in moodledata falls back to a patterned cover instead of a broken image.
     *
     * @param core_course_list_element $course
     * @return string|null
     */
    protected function get_course_image_url(core_course_list_element $course): ?string {
        $filesystem = get_file_storage()->get_file_system();
        foreach ($course->get_course_overviewfiles() as $file) {
            // Check readability first: is_valid_image() reads the file and warns if it is missing.
            if ($filesystem->is_file_readable_locally_by_storedfile($file) && $file->is_valid_image()) {
                return \core\url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                )->out(false);
            }
        }
        return null;
    }
}
