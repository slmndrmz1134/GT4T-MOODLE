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

namespace local_diverse_catalog;

use core_course_category;
use core_course_list_element;

/**
 * The courses a user can see, for browsing and searching them.
 *
 * Everything comes from Moodle's own course categories, so partner organisations stay separate: a user only finds
 * the courses of their own partner and the shared ones. Cards show only real figures: nothing here is estimated.
 *
 * @package    local_diverse_catalog
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class catalog {
    /** @var int Length of the summary on a card. */
    const SUMMARY_LENGTH = 110;

    /**
     * The courses the current user can see.
     *
     * @return core_course_list_element[]
     */
    public static function courses(): array {
        return core_course_category::top()->get_courses(['recursive' => true, 'sort' => ['fullname' => 1]]);
    }

    /**
     * Top-level categories with the number of courses the user can see in each.
     *
     * @param array $selected Ids of the selected categories.
     * @return array[] id, name, count, selected, url
     */
    public static function categories(array $selected = []): array {
        $counts = [];
        foreach (self::courses() as $course) {
            $id = self::root_category($course);
            if ($id) {
                $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }
        $rows = [];
        foreach ($counts as $id => $count) {
            $category = core_course_category::get($id, IGNORE_MISSING);
            if ($category) {
                $rows[] = [
                    'id' => $id,
                    'name' => self::text($category->name),
                    'count' => $count,
                    'selected' => in_array($id, $selected),
                    'url' => (new \core\url('/local/diverse_catalog/index.php', ['cat[]' => $id]))->out(false),
                ];
            }
        }
        usort($rows, fn($a, $b) => strcoll($a['name'], $b['name']));
        return $rows;
    }

    /**
     * Search and filter the visible courses.
     *
     * @param string $query Words that must all appear in the name, summary, category or teachers.
     * @param int[] $categories Top-level categories; empty means all.
     * @param string $status all, enrolled or available.
     * @param string $sort name, newest or popular.
     * @return array Cards.
     */
    public static function search(string $query, array $categories, string $status, string $sort): array {
        $enrolled = array_keys(enrol_get_my_courses('id'));
        $words = array_filter(preg_split('/\s+/u', \core_text::strtolower(trim($query))));

        $matches = [];
        foreach (self::courses() as $course) {
            if ($categories && !in_array(self::root_category($course), $categories)) {
                continue;
            }
            $isenrolled = in_array($course->id, $enrolled);
            if (($status === 'enrolled' && !$isenrolled) || ($status === 'available' && $isenrolled)) {
                continue;
            }
            if ($words) {
                $text = \core_text::strtolower(implode(' ', [
                    $course->fullname,
                    $course->shortname,
                    strip_tags((string)$course->summary),
                    self::category_name($course),
                    self::teachers($course),
                ]));
                foreach ($words as $word) {
                    if (!str_contains($text, $word)) {
                        continue 2;
                    }
                }
            }
            $matches[$course->id] = $course;
        }

        $cards = self::cards($matches);
        if ($sort === 'newest') {
            usort($cards, fn($a, $b) => $b['timecreated'] <=> $a['timecreated']);
        } else if ($sort === 'popular') {
            usort($cards, fn($a, $b) => $b['people'] <=> $a['people']);
        }
        return $cards;
    }

    /**
     * Courses the user can see but is not enrolled in.
     *
     * @param int $limit
     * @return array Cards.
     */
    public static function recommended(int $limit): array {
        $enrolled = array_keys(enrol_get_my_courses('id'));
        $courses = array_filter(self::courses(), fn($course) => !in_array($course->id, $enrolled));
        return self::cards(array_slice($courses, 0, $limit, true));
    }

    /**
     * The courses the current user is enrolled in, for the My courses page.
     *
     * Status comes from completion and, in courses without completion tracking, from whether the user has visited.
     * The courses someone teaches have no status: they are listed under All and Favourites only.
     *
     * @param string $filter all, notstarted, inprogress, completed or favourites.
     * @return array courses (each with lastaccess), hascourses, tabs, stats (enrolled, inprogress, completed, average)
     */
    public static function mine(string $filter): array {
        global $DB, $USER;

        $courses = enrol_get_my_courses(['summary', 'summaryformat'], 'fullname ASC');
        $cards = self::cards(array_map(fn($course) => new core_course_list_element($course), $courses));
        $visited = $DB->get_records_menu('user_lastaccess', ['userid' => $USER->id], '', 'courseid, timeaccess');

        $filters = ['all' => [], 'notstarted' => [], 'inprogress' => [], 'completed' => [], 'favourites' => []];
        foreach ($cards as $card) {
            $card['lastaccess'] = (int)($visited[$card['id']] ?? 0);
            $filters['all'][] = $card;
            if ($card['favourite']) {
                $filters['favourites'][] = $card;
            }
            if ($card['studies']) {
                if ($card['progress'] >= 100) {
                    $filters['completed'][] = $card;
                } else if ($card['progress'] > 0 || isset($visited[$card['id']])) {
                    $filters['inprogress'][] = $card;
                } else {
                    $filters['notstarted'][] = $card;
                }
            }
        }

        $filter = isset($filters[$filter]) ? $filter : 'all';
        $studies = (bool)array_filter($cards, fn($card) => $card['studies']);
        $tabs = [];
        foreach ($filters as $name => $list) {
            if ($studies || in_array($name, ['all', 'favourites'])) {
                $tabs[] = [
                    'label' => get_string('mycourses_' . $name, 'theme_diverse'),
                    'count' => count($list),
                    'url' => (new \core\url('/my/courses.php', ['filter' => $name]))->out(false),
                    'current' => $name === $filter,
                ];
            }
        }
        $tracked = array_filter($cards, fn($card) => $card['hasprogress']);
        $average = $tracked ? (int)round(array_sum(array_column($tracked, 'progress')) / count($tracked)) : null;
        return [
            'courses' => $filters[$filter],
            'hascourses' => !empty($filters[$filter]),
            'tabs' => $tabs,
            'stats' => [
                'enrolled' => count($cards),
                'inprogress' => count($filters['inprogress']),
                'completed' => count($filters['completed']),
                'average' => $average,
            ],
        ];
    }

    /**
     * Add a course to the favourites of the current user or remove it: the same stars as in Moodle's own course overview.
     *
     * @param int $courseid A course the user is enrolled in; other courses are ignored.
     * @param bool $favourite
     */
    public static function set_favourite(int $courseid, bool $favourite): void {
        global $USER;

        if (!array_key_exists($courseid, enrol_get_my_courses('id'))) {
            return;
        }
        $service = \core_favourites\service_factory::get_service_for_user_context(\core\context\user::instance($USER->id));
        $context = \core\context\course::instance($courseid);
        $exists = $service->favourite_exists('core_course', 'courses', $courseid, $context);
        if ($favourite && !$exists) {
            $service->create_favourite('core_course', 'courses', $courseid, $context);
        } else if (!$favourite && $exists) {
            $service->delete_favourite('core_course', 'courses', $courseid, $context);
        }
    }

    /**
     * What the course cards show.
     *
     * @param core_course_list_element[] $courses Keyed by course id.
     * @return array
     */
    public static function cards(array $courses): array {
        global $DB, $FULLME, $USER;

        if (!$courses) {
            return [];
        }
        [$insql, $params] = $DB->get_in_or_equal(array_keys($courses), SQL_PARAMS_NAMED);
        $activities = $DB->get_records_sql_menu(
            "SELECT course, COUNT(id) FROM {course_modules}
              WHERE course $insql AND visible = 1 AND deletioninprogress = 0
           GROUP BY course",
            $params
        );
        $people = $DB->get_records_sql_menu(
            "SELECT e.courseid, COUNT(DISTINCT ue.userid)
               FROM {enrol} e
               JOIN {user_enrolments} ue ON ue.enrolid = e.id
              WHERE e.courseid $insql
           GROUP BY e.courseid",
            $params
        );
        $open = $DB->get_records_select_menu('enrol', "courseid $insql AND enrol = 'self' AND status = 0", $params, '', 'courseid, id');
        $enrolled = array_keys(enrol_get_my_courses('id'));
        $service = \core_favourites\service_factory::get_service_for_user_context(\core\context\user::instance($USER->id));
        $favourites = array_map(fn($item) => (int)$item->itemid, $service->find_favourites_by_type('core_course', 'courses'));
        $favouriteurl = (new \core\url('/local/diverse_catalog/favourite.php'))->out(false);
        $returnurl = (new \core\url($FULLME))->out_as_local_url(false);

        $cards = [];
        foreach ($courses as $id => $course) {
            $isenrolled = in_array($id, $enrolled);
            $teaches = has_capability('moodle/grade:viewall', \core\context\course::instance($id));
            $studies = $isenrolled && !$teaches;
            $progress = $studies ? \core_completion\progress::get_course_progress_percentage(get_course($id)) : null;
            $unfinished = $studies && ($progress ?? 0) < 100;
            $summary = trim(html_to_text(format_text((string)$course->summary, $course->summaryformat), 0, false));
            $cards[] = \theme_diverse\output\landing::course_cover($course) + [
                'id' => $id,
                'name' => self::text($course->fullname),
                'category' => self::category_name($course),
                'teachers' => self::teachers($course),
                'summary' => shorten_text($summary, self::SUMMARY_LENGTH),
                'activities' => (int)($activities[$id] ?? 0),
                'people' => (int)($people[$id] ?? 0),
                'enrolled' => $isenrolled,
                'studies' => $studies,
                'favourite' => in_array((int)$id, $favourites, true),
                'favouriteurl' => $favouriteurl,
                'returnurl' => $returnurl,
                'sesskey' => sesskey(),
                'selfenrol' => !$isenrolled && isset($open[$id]),
                'hasprogress' => $progress !== null,
                'progress' => $progress !== null ? (int)floor($progress) : 0,
                'action' => $unfinished
                    ? get_string(($progress ?? 0) > 0 ? 'hero_continue' : 'hero_start', 'theme_diverse')
                    : get_string('view'),
                'actionprimary' => $unfinished,
                'timecreated' => (int)$course->timecreated,
                'url' => (new \core\url($isenrolled ? '/course/view.php' : '/course/info.php', ['id' => $id]))->out(false),
            ];
        }
        return $cards;
    }

    /**
     * The top-level category of a course.
     *
     * @param core_course_list_element $course
     * @return int|null
     */
    protected static function root_category(core_course_list_element $course): ?int {
        $category = core_course_category::get($course->category, IGNORE_MISSING);
        return $category ? (int)explode('/', trim($category->path, '/'))[0] : null;
    }

    /**
     * Name of the category of a course.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected static function category_name(core_course_list_element $course): string {
        $category = core_course_category::get($course->category, IGNORE_MISSING);
        return $category ? self::text($category->name) : '';
    }

    /**
     * A name for a template: filtered, and not escaped here because Mustache escapes it.
     *
     * @param string $text
     * @return string
     */
    protected static function text(string $text): string {
        return format_string($text, true, ['context' => \core\context\system::instance(), 'escape' => false]);
    }

    /**
     * The teachers of a course.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected static function teachers(core_course_list_element $course): string {
        $names = [];
        foreach ($course->get_course_contacts() as $contact) {
            $names[] = $contact['username'];
        }
        return implode(', ', array_unique($names));
    }
}
