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

namespace theme_diverse\output\core;

use coursecat_helper;
use theme_diverse\output\shell;

/**
 * Theme DIVERSE - Course renderer.
 *
 * The course lists of the category pages (course/index.php) and of the course search show the same cards as Discover
 * and My courses, with Moodle's own paging around them. Without local_diverse_catalog, which makes the cards, they
 * stay Boost Union's.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \theme_boost_union\output\core\course_renderer {
    /**
     * The courses of a category or of a search, as cards.
     *
     * @param coursecat_helper $chelper
     * @param \core_course_list_element[] $courses
     * @param int|null $totalcount
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;

        if (!class_exists(\local_diverse_catalog\catalog::class) || !shell::applies($this->page)) {
            return parent::coursecat_courses($chelper, $courses, $totalcount);
        }
        $totalcount = $totalcount ?? count($courses);
        if (!$totalcount) {
            return '';
        }

        // Moodle's paging: numbered pages, or a link to the rest of the category.
        $paging = '';
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        if ($totalcount > count($courses)) {
            if ($paginationurl) {
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $paging = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, ['perpage' => $perpage]));
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                $paging = \html_writer::div(\html_writer::link($viewmoreurl,
                    $chelper->get_courses_display_option('viewmoretext', new \lang_string('viewmore')),
                    ['class' => 'btn btn-secondary']), 'paging paging-morelink');
            }
        }

        $cards = '';
        foreach (\local_diverse_catalog\catalog::cards($courses) as $card) {
            $cards .= $this->render_from_template('theme_diverse/course_card', $card);
        }
        $attributes = $chelper->get_and_erase_attributes('courses');
        $attributes['class'] = trim(($attributes['class'] ?? '') . ' diverse-category-courses');
        return \html_writer::div(\html_writer::tag('ul', $cards, ['class' => 'diverse-card-grid']) . $paging, '', $attributes);
    }
}
