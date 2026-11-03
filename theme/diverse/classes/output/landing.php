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
use theme_diverse\local\partners;

/**
 * Theme DIVERSE - Data for the landing page.
 *
 * The landing lists the GT4T project partners (PROJECT_PARTNERS). Course figures are read live and
 * filtered by what the current (usually anonymous) visitor may see.
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


    /**
     * @var array[] Full partners of the GT4T project, lead partner first: name, ISO country code, website.
     *
     * Source: https://gt4t.intelligent-industry.fi/about-us/ (Wild Campus and Windesheim link to the wrong
     * site there; their own addresses are used here).
     */
    const PROJECT_PARTNERS = [
        ['name' => 'Satakunta University of Applied Sciences (SAMK)', 'country' => 'FI', 'url' => 'https://www.samk.fi/',
            'lead' => true, 'tenant' => 'samk'],
        ['name' => 'Beykent University', 'country' => 'TR', 'url' => 'https://www.beykent.edu.tr/en', 'tenant' => 'beykent'],
        ['name' => 'Dublin Business Innovation Centre (Furthr)', 'country' => 'IE', 'url' => 'https://www.furthr.ie/',
            'tenant' => 'furthr'],
        ['name' => 'Griffith College', 'country' => 'IE', 'url' => 'https://www.griffith.ie/', 'tenant' => 'griffith'],
        ['name' => 'Ikigaia Oy', 'country' => 'FI', 'url' => 'https://www.ikigaia.fi/', 'tenant' => 'ikigaia'],
        ['name' => 'Technische Hochschule Rosenheim', 'country' => 'DE', 'url' => 'https://www.th-rosenheim.de/',
            'tenant' => 'rosenheim'],
        ['name' => 'Wild Campus GmbH', 'country' => 'DE', 'url' => 'https://wildcampus.de/', 'tenant' => 'wildcampus'],
        ['name' => 'Windesheim University of Applied Sciences', 'country' => 'NL', 'url' => 'https://www.windesheim.nl/',
            'tenant' => 'windesheim'],
    ];

    /**
     * Export the landing page data.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $DB;

        $partners = $this->get_project_partners();
        $courses = $this->get_featured_courses();
        $coursecount = $DB->count_records_select('course', 'id <> :siteid AND visible = 1', ['siteid' => SITEID]);
        $countrycount = count(array_unique(array_column(self::PROJECT_PARTNERS, 'country')));

        return [
            // Students in a lecture hall (Vitaly Gariev, Unsplash licence), chosen by the project owner.
            'heroimage' => $output->image_url('hero', 'theme_diverse')->out(false),
            'hasheroimage' => true,
        ] + $this->get_partner_media($partners) + [
            'partners' => $partners,
            'partnercount' => count($partners),
            'countrycount' => $countrycount,
            'ctatext' => get_string('cta_text', 'theme_diverse', (object)['partners' => count($partners), 'countries' => $countrycount]),
            'coursecount' => $coursecount,
            'courses' => $courses,
            'hascourses' => !empty($courses),
            'loggedin' => isloggedin() && !isguestuser(),
            'loginurl' => (new \core\url('/login/index.php'))->out(false),
            'dashboardurl' => (new \core\url('/my/'))->out(false),
            'allcoursesurl' => (new \core\url('/course/index.php'))->out(false),
            'privacyurl' => (new \core\url('/admin/tool/dataprivacy/summary.php'))->out(false),
            'homeurl' => (new \core\url('/'))->out(false),
            'logourl' => $output->image_url('logo', 'theme_diverse')->out(false),
            // The GT4T project, next to the DIVERSE logo in the closing band (owner's request).
            'gt4tlogourl' => $output->image_url('gt4t-logo', 'theme_diverse')->out(false),
            'gt4turl' => 'https://gt4t.intelligent-industry.fi/',
        ];
    }

    /**
     * The logo strip under the hero: the logos the partners uploaded for their login pages.
     *
     * @param array $partners See get_project_partners().
     * @return array logos, haslogos
     */
    protected function get_partner_media(array $partners): array {
        $logos = array_values(array_filter($partners, fn($partner) => !empty($partner['logourl'])));
        return [
            'logos' => $logos,
            'haslogos' => !empty($logos),
        ];
    }

    /**
     * Get the GT4T project partners with their country name in the current language.
     *
     * @return array
     */
    protected function get_project_partners(): array {
        $countries = get_string_manager()->get_list_of_countries();
        $tenants = array_column(partners::all(), 'id', 'idnumber');
        $partners = [];
        foreach (self::PROJECT_PARTNERS as $partner) {
            $tenantid = $tenants[$partner['tenant']] ?? null;
            $logo = $tenantid ? partners::logo_url($tenantid) : null;
            $photo = $tenantid ? partners::file_url(partners::PHOTO_AREA, $tenantid) : null;
            $words = array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', $partner['name'])));
            $partners[] = [
                'initials' => \core_text::strtoupper(implode('', array_map(fn($word) => \core_text::substr($word, 0, 1),
                    array_slice($words, 0, 2)))),
                'logourl' => $logo ? $logo->out(false) : null,
                'photourl' => $photo ? $photo->out(false) : null,
                'name' => $partner['name'],
                'country' => $countries[$partner['country']] ?? $partner['country'],
                'url' => $partner['url'],
                'domain' => preg_replace('/^www\./', '', parse_url($partner['url'], PHP_URL_HOST)),
                'lead' => !empty($partner['lead']),
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
            $items[] = [
                'fullname' => $course->get_formatted_fullname(),
                'shortname' => $course->get_formatted_shortname(),
                'category' => $category->get_formatted_name(),
                'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
                // Neighbours get different colours, whatever their ids.
                'coverstyle' => self::COVER_STYLES[$index % count(self::COVER_STYLES)],
            ] + self::course_cover($course);
        }
        return $items;
    }

    /**
     * The cover of a course for theme_diverse/course_cover: its image, or its initials on a brand colour.
     *
     * @param core_course_list_element $course
     * @return array
     */
    public static function course_cover(core_course_list_element $course): array {
        $imageurl = self::get_course_image_url($course);
        $name = format_string($course->fullname, true, ['context' => \core\context\system::instance(), 'escape' => false]);
        $words = array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', $name)));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= \core_text::strtoupper(\core_text::substr($word, 0, 1));
        }
        return [
            'imageurl' => $imageurl,
            'hasimage' => $imageurl !== null,
            'coverstyle' => self::COVER_STYLES[$course->id % count(self::COVER_STYLES)],
            'initials' => $initials,
        ];
    }

    /**
     * Get the URL of the course overview image, but only if its file can actually be read,
     * so that a missing file in moodledata falls back to the initials instead of a broken image.
     *
     * @param core_course_list_element $course
     * @return string|null
     */
    protected static function get_course_image_url(core_course_list_element $course): ?string {
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
