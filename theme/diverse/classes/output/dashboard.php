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
use local_diverse_catalog\catalog;
use theme_diverse\local\partners;

/**
 * Theme DIVERSE - Data for the dashboard.
 *
 * The dashboard answers "what do I do now?" and is built from the user's roles, section by section: a user
 * who both teaches and studies sees both, a site administrator also sees the state of the site.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dashboard implements renderable, templatable {
    /** @var int How far back overdue work is shown, as in Moodle's own timeline. */
    const OVERDUE_DAYS = 30;

    /** @var int How far ahead work is shown. */
    const AHEAD_DAYS = 30;

    /** @var int Largest number of items shown in the to-do list. */
    const MAX_TODO = 8;

    /** @var int Largest number of course cards shown in a section. */
    const MAX_COURSES = 8;

    /** @var int Number of courses offered to continue. */
    const MAX_RESUME = 3;

    /**
     * Export the dashboard.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $USER;

        [$teaching, $learning] = $this->split_courses();
        $todo = $this->get_todo();
        $isadmin = has_capability('moodle/site:config', \context_system::instance());

        $data = [
            'avatar' => $output->user_picture($USER, ['size' => 100, 'link' => false]),
            'greeting' => get_string('dash_hello', 'theme_diverse', self::text($USER->firstname)),
            'today' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'allcoursesurl' => (new \core\url('/my/courses.php'))->out(false),
            'calendarurl' => (new \core\url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
            'isadmin' => $isadmin,
        ] + $todo;

        $hascatalog = class_exists(catalog::class);
        if ($hascatalog && !$isadmin) {
            $data['categories'] = catalog::categories();
            $data['hascategories'] = !empty($data['categories']);
            $data['catalogurl'] = (new \core\url('/local/diverse_catalog/index.php'))->out(false);
            $data['recommended'] = catalog::recommended(self::MAX_COURSES);
            $data['hasrecommended'] = !empty($data['recommended']);
        }
        if ($learning) {
            $data['learning'] = $this->export_learning($learning, $output);
            $data['learning']['kpis'] = $this->export_kpis($data['learning']['allcards'], $todo['duethisweek']);
            unset($data['learning']['allcards']);
            $first = $data['learning']['resume'][0] ?? null;
            if ($first) {
                $data['hero'] = [
                    'url' => $first['url'],
                    'label' => $first['action'],
                    'next' => get_string('dash_hero_next', 'theme_diverse', $first['next'] ?: $first['name']),
                ];
            }
        }
        // A site administrator is usually enrolled for support, not to teach: the site comes first for them.
        if ($teaching && !$isadmin) {
            $data['teaching'] = $this->export_teaching($teaching, $output);
        }
        if ($isadmin) {
            $data['admin'] = $this->export_admin();
        }
        $data['showtodo'] = $todo['hastodo'] || $learning;
        $data['summary'] = $this->summary($todo, $data['teaching']['waiting'] ?? 0);
        $data['nocontent'] = !$learning && !$teaching && !$isadmin;
        return $data;
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
     * Sort the user's courses into those they teach and those they study.
     *
     * @return array [teaching courses, learning courses]
     */
    protected function split_courses(): array {
        $teaching = [];
        $learning = [];
        $courses = enrol_get_my_courses(['summary', 'summaryformat', 'enablecompletion', 'showgrades'], 'fullname ASC');
        foreach ($courses as $course) {
            // Non-editing teachers teach too: they see everyone's grades, students only their own.
            if (has_capability('moodle/grade:viewall', \context_course::instance($course->id))) {
                $teaching[$course->id] = $course;
            } else {
                $learning[$course->id] = $course;
            }
        }
        return [$teaching, $learning];
    }

    /**
     * Four figures at the top of a student's dashboard, all from the user's own courses and calendar.
     *
     * @param array $cards Course cards (see catalog::cards()).
     * @param int $due Number of activities due from now until the end of the week.
     * @return array[] value, label, icon, ring, progress
     */
    protected function export_kpis(array $cards, int $due): array {
        $tracked = array_filter($cards, fn($card) => !empty($card['hasprogress']));
        $completed = count(array_filter($tracked, fn($card) => $card['progress'] >= 100));
        $average = $tracked ? (int)round(array_sum(array_column($tracked, 'progress')) / count($tracked)) : null;
        $kpis = [
            ['value' => count($cards), 'label' => get_string('dash_kpi_courses', 'theme_diverse'), 'icon' => 'fa-book'],
            ['value' => $completed . ' / ' . count($cards), 'label' => get_string('dash_kpi_completed', 'theme_diverse'),
                'icon' => 'fa-circle-check'],
            ['value' => $due, 'label' => get_string('dash_kpi_due', 'theme_diverse'), 'icon' => 'fa-calendar'],
        ];
        // Without completion tracking there is no progress to average: the figure is left out rather than shown as 0.
        if ($average !== null) {
            $kpis[] = ['value' => $average . '%', 'label' => get_string('dash_kpi_progress', 'theme_diverse'),
                'ring' => true, 'progress' => $average];
        }
        return $kpis;
    }

    /**
     * How much the user did on each day of the current week, counted from the standard log.
     *
     * @return array|null bars (label, count, height, today, description), or null without a readable log.
     */
    protected function get_activity(): ?array {
        global $USER;

        $readers = get_log_manager()->get_readers('\core\log\sql_reader');
        $reader = reset($readers);
        if (!$reader) {
            return null;
        }
        $now = time();
        $monday = usergetmidnight($now) - ((int)userdate($now, '%u') - 1) * DAYSECS;
        $bars = [];
        for ($day = 0; $day < 7; $day++) {
            $start = usergetmidnight($monday + $day * DAYSECS + 12 * HOURSECS);
            $end = usergetmidnight($start + DAYSECS + 12 * HOURSECS);
            $count = $start > $now ? 0 : $reader->get_events_select_count(
                'userid = :userid AND timecreated >= :start AND timecreated < :end',
                ['userid' => $USER->id, 'start' => $start, 'end' => $end]
            );
            $label = userdate($start, '%a');
            $bars[] = [
                'label' => $label,
                'count' => $count,
                'today' => $start <= $now && $now < $end,
                'description' => get_string('dash_activity_day', 'theme_diverse', (object)['day' => $label, 'count' => $count]),
            ];
        }
        $max = max(array_column($bars, 'count'));
        foreach ($bars as &$bar) {
            $bar['height'] = $max ? max(4, (int)round($bar['count'] / $max * 100)) : 4;
        }
        return ['bars' => $bars, 'empty' => $max === 0];
    }

    /**
     * The student part: where to continue, course progress and recent grades.
     *
     * @param \stdClass[] $courses
     * @param renderer_base $output
     * @return array
     */
    protected function export_learning(array $courses, renderer_base $output): array {
        global $DB, $USER;

        $lastaccess = $DB->get_records_menu('user_lastaccess', ['userid' => $USER->id], '', 'courseid, timeaccess');
        uasort($courses, fn($a, $b) => ($lastaccess[$b->id] ?? 0) <=> ($lastaccess[$a->id] ?? 0));

        $resume = [];
        foreach (array_slice($courses, 0, self::MAX_RESUME, true) as $course) {
            $next = course_hero::next_activity($course);
            $resume[] = $this->export_course($course, $output) + [
                'activities' => count(course_hero::activities($course)),
                'action' => get_string($lastaccess[$course->id] ?? 0 ? 'hero_continue' : 'hero_start', 'theme_diverse'),
                'url' => ($next ? $next->url : new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
                'next' => $next ? self::text($next->name) : '',
                'type' => $next ? $next->get_module_type_name() : '',
                'lastaccess' => isset($lastaccess[$course->id])
                    ? get_string('dash_lastvisit', 'theme_diverse', format_time(time() - $lastaccess[$course->id])) : '',
            ];
        }

        if (class_exists(catalog::class)) {
            $elements = array_map(fn($course) => new \core_course_list_element($course), $courses);
            $cards = catalog::cards($elements);
        } else {
            $cards = array_map(fn($course) => $this->export_course($course, $output), array_values($courses));
        }

        $recentgrades = $this->get_recent_grades($courses);
        $activity = $this->get_activity();
        return [
            'resume' => $resume,
            'allcards' => $cards,
            'activity' => $activity,
            'hasactivity' => $activity !== null,
            'courses' => array_slice($cards, 0, self::MAX_COURSES),
            'hascourses' => !empty($cards),
            'more' => count($cards) > self::MAX_COURSES,
            'grades' => $recentgrades,
            'hasgrades' => !empty($recentgrades),
            'gradesurl' => (new \core\url('/grade/report/mygrades.php'))->out(false),
        ];
    }

    /**
     * The teacher part: courses with their students and the work waiting to be graded.
     *
     * @param \stdClass[] $courses
     * @param renderer_base $output
     * @return array
     */
    protected function export_teaching(array $courses, renderer_base $output): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $cards = [];
        $grading = [];
        $students = 0;
        $waiting = 0;
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            $coursestudents = count_enrolled_users($context, 'moodle/course:isincompletionreports');
            $coursewaiting = 0;
            foreach (get_fast_modinfo($course)->get_instances_of('assign') as $cm) {
                if (!$cm->uservisible || !has_capability('mod/assign:grade', $cm->context)) {
                    continue;
                }
                $count = (new \assign($cm->context, $cm, $course))->count_submissions_need_grading();
                if ($count) {
                    $coursewaiting += $count;
                    $grading[] = [
                        'name' => self::text($cm->name),
                        'course' => self::text($course->fullname),
                        'count' => $count,
                        'url' => (new \core\url('/mod/assign/view.php', ['id' => $cm->id, 'action' => 'grading']))->out(false),
                    ];
                }
            }
            $students += $coursestudents;
            $waiting += $coursewaiting;
            $cards[] = $this->export_course($course, $output) + [
                'students' => $coursestudents,
                'waiting' => $coursewaiting,
                'participantsurl' => (new \core\url('/user/index.php', ['id' => $course->id]))->out(false),
                'gradebookurl' => (new \core\url('/grade/report/grader/index.php', ['id' => $course->id]))->out(false),
            ];
        }
        usort($grading, fn($a, $b) => $b['count'] <=> $a['count']);

        return [
            'courses' => array_slice($cards, 0, self::MAX_COURSES),
            'more' => count($cards) > self::MAX_COURSES,
            'grading' => array_slice($grading, 0, 6),
            'hasgrading' => !empty($grading),
            'waiting' => $waiting,
            'stats' => [
                ['value' => count($courses), 'label' => get_string('dash_stat_teaching', 'theme_diverse')],
                ['value' => $students, 'label' => get_string('dash_stat_students', 'theme_diverse')],
                ['value' => $waiting, 'label' => get_string('dash_stat_waiting', 'theme_diverse'), 'attention' => $waiting > 0],
            ],
        ];
    }

    /**
     * The administrator part: site figures, partners, things that need attention and common tasks.
     *
     * @return array
     */
    protected function export_admin(): array {
        global $CFG, $DB;

        $activeusers = $DB->count_records_select(
            'user',
            'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id <> :guest',
            ['guest' => $CFG->siteguest]
        );
        $recentusers = $DB->count_records_select(
            'user',
            'deleted = 0 AND lastaccess > :since AND id <> :guest',
            ['since' => time() - 7 * DAYSECS, 'guest' => $CFG->siteguest]
        );
        $pending = $DB->count_records('user', ['deleted' => 0, 'confirmed' => 0]);
        $courses = $DB->count_records_select('course', 'id <> :site AND visible = 1', ['site' => SITEID]);
        $partnerrows = partners::overview();

        $links = [
            ['/user/editadvanced.php?id=-1', get_string('addnewuser')],
            ['/admin/user.php', get_string('dash_browseusers', 'theme_diverse')],
            ['/course/management.php', get_string('dash_managecourses', 'theme_diverse')],
            ['/admin/settings.php?section=theme_diverse', get_string('partnerlogin', 'theme_diverse')],
            ['/report/log/index.php?id=0', get_string('dash_logs', 'theme_diverse')],
            ['/admin/index.php', get_string('notifications', 'admin')],
        ];

        $lastcron = (int)$DB->get_field_sql('SELECT MAX(lastruntime) FROM {task_scheduled}');
        $cronok = $lastcron && (time() - $lastcron) < HOURSECS;

        return [
            'stats' => [
                ['value' => $activeusers, 'label' => get_string('dash_stat_users', 'theme_diverse')],
                ['value' => $recentusers, 'label' => get_string('dash_stat_recent', 'theme_diverse')],
                ['value' => $courses, 'label' => get_string('dash_stat_courses', 'theme_diverse')],
                ['value' => count($partnerrows), 'label' => get_string('dash_stat_partners', 'theme_diverse')],
                ['value' => $pending, 'label' => get_string('dash_stat_pending', 'theme_diverse'), 'attention' => $pending > 0],
            ],
            'pending' => $pending,
            'pendingurl' => (new \core\url('/admin/user.php'))->out(false),
            'partners' => $partnerrows,
            'haspartners' => !empty($partnerrows),
            'actions' => array_map(fn($link) => [
                'url' => (new \core\url($link[0]))->out(false),
                'label' => $link[1],
            ], $links),
            'cronok' => $cronok,
            'cron' => $lastcron
                ? get_string('dash_cron_ran', 'theme_diverse', format_time(time() - $lastcron))
                : get_string('dash_cron_never', 'theme_diverse'),
            'version' => $CFG->release,
        ];
    }

    /**
     * Group the user's work into overdue, today, this week and later, the way students plan it.
     *
     * Events come from the calendar's action events, the same source as Moodle's timeline: they disappear
     * when the work is done, and teachers get their own actions (for example grading).
     *
     * @return array
     */
    protected function get_todo(): array {
        $now = time();
        $endoftoday = usergetmidnight($now) + DAYSECS;
        $endofweek = usergetmidnight($now) + 7 * DAYSECS;

        $groups = [
            'overdue' => ['title' => get_string('dash_overdue', 'theme_diverse'), 'overdue' => true, 'items' => []],
            'today' => ['title' => get_string('today', 'core_calendar'), 'items' => []],
            'week' => ['title' => get_string('dash_thisweek', 'theme_diverse'), 'items' => []],
            'later' => ['title' => get_string('dash_later', 'theme_diverse'), 'items' => []],
        ];

        $events = \core_calendar\local\api::get_action_events_by_timesort(
            $now - self::OVERDUE_DAYS * DAYSECS,
            $now + self::AHEAD_DAYS * DAYSECS,
            null,
            50,
            true
        );

        $overdue = 0;
        $total = 0;
        $courses = [];
        $monday = usergetmidnight($now) - ((int)userdate($now, '%u') - 1) * DAYSECS;
        $sunday = usergetmidnight($monday + 6 * DAYSECS + 12 * HOURSECS) + DAYSECS;
        $busy = [];
        $duethisweek = 0;
        foreach ($events as $event) {
            $action = $event->get_action();
            if (!$action || !$event->get_course()) {
                continue;
            }
            $time = $event->get_times()->get_sort_time()->getTimestamp();
            $key = $time < $now ? 'overdue' : ($time < $endoftoday ? 'today' : ($time < $endofweek ? 'week' : 'later'));
            if ($time >= $monday && $time < $sunday) {
                $busy[usergetmidnight($time)] = true;
                $duethisweek += $time >= $now ? 1 : 0;
            }
            $total++;
            $overdue += $key === 'overdue' ? 1 : 0;
            if ($total > self::MAX_TODO) {
                continue;
            }
            $courseid = $event->get_course()->get('id');
            $courses[$courseid] = $courses[$courseid] ?? get_course($courseid);
            // The activity's own name: the event's name adds words such as "is due" in the site language.
            $name = $event->get_name();
            if ($event->get_course_module()) {
                $name = get_fast_modinfo($courses[$courseid])->get_cm($event->get_course_module()->get('id'))->name;
            }
            $groups[$key]['items'][] = [
                'name' => self::text($name),
                'course' => self::text($courses[$courseid]->fullname),
                'time' => userdate($time, get_string('strftimedateshort', 'langconfig')) . ', ' .
                    userdate($time, get_string('strftimetime', 'langconfig')),
                'url' => $action->get_url()->out(false),
                'action' => $action->get_name(),
            ];
        }

        // The current week, Monday first, with a mark on the days that have something due.
        $week = [];
        for ($day = 0; $day < 7; $day++) {
            $start = usergetmidnight($monday + $day * DAYSECS + 12 * HOURSECS);
            $week[] = [
                'weekday' => userdate($start, '%a'),
                'day' => userdate($start, '%d'),
                'date' => userdate($start, get_string('strftimedaydate', 'langconfig')),
                'url' => (new \core\url('/calendar/view.php', ['view' => 'day', 'time' => $start]))->out(false),
                'today' => $start === usergetmidnight($now),
                'busy' => isset($busy[$start]),
            ];
        }

        $visible = array_values(array_filter($groups, fn($group) => !empty($group['items'])));
        return [
            'week' => $week,
            'duethisweek' => $duethisweek,
            'todo' => $visible,
            'hastodo' => !empty($visible),
            'todototal' => $total,
            'todomore' => $total > self::MAX_TODO,
            'overduecount' => $overdue,
        ];
    }

    /**
     * The user's latest grades that are visible to them, with the teacher's feedback.
     *
     * @param \stdClass[] $courses
     * @return array
     */
    protected function get_recent_grades(array $courses): array {
        global $CFG, $DB, $USER;
        require_once($CFG->libdir . '/gradelib.php');

        $visible = array_filter($courses, fn($course) => !empty($course->showgrades) &&
            has_capability('moodle/grade:view', \context_course::instance($course->id)));
        if (!$visible) {
            return [];
        }

        [$insql, $params] = $DB->get_in_or_equal(array_keys($visible), SQL_PARAMS_NAMED);
        $params['userid'] = $USER->id;
        $sql = "SELECT g.id AS gradeid, g.finalgrade, g.feedback, g.feedbackformat, g.timemodified AS gradetime, gi.*
                  FROM {grade_grades} g
                  JOIN {grade_items} gi ON gi.id = g.itemid
                 WHERE g.userid = :userid AND gi.courseid $insql AND gi.itemtype = 'mod'
                       AND g.finalgrade IS NOT NULL AND g.hidden = 0 AND gi.hidden = 0
              ORDER BY g.timemodified DESC";

        $grades = [];
        foreach ($DB->get_records_sql($sql, $params, 0, 4) as $record) {
            $item = new \grade_item($record, false);
            $feedback = trim(strip_tags((string)$record->feedback));
            $grades[] = [
                'name' => self::text($record->itemname),
                'course' => self::text($visible[$record->courseid]->fullname),
                'grade' => grade_format_gradevalue($record->finalgrade, $item, true),
                'feedback' => shorten_text($feedback, 90),
                'hasfeedback' => $feedback !== '',
            ];
        }
        return $grades;
    }

    /**
     * What every course card shows.
     *
     * @param \stdClass $course
     * @param renderer_base $output
     * @return array
     */
    protected function export_course(\stdClass $course, renderer_base $output): array {
        $progress = \core_completion\progress::get_course_progress_percentage($course);
        return landing::course_cover(new \core_course_list_element($course)) + [
            'name' => self::text($course->fullname),
            'category' => course_hero::category_name($course),
            'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
            'hasprogress' => $progress !== null,
            'progress' => $progress !== null ? (int)floor($progress) : 0,
        ];
    }

    /**
     * One sentence under the greeting.
     *
     * @param array $todo
     * @param int $waiting Submissions waiting to be graded by this user.
     * @return string
     */
    protected function summary(array $todo, int $waiting): string {
        if ($todo['overduecount']) {
            return get_string('dash_summary_overdue', 'theme_diverse', $todo['overduecount']);
        }
        if ($waiting) {
            return get_string('dash_summary_grading', 'theme_diverse', $waiting);
        }
        if ($todo['todototal']) {
            return get_string('dash_summary_todo', 'theme_diverse', $todo['todototal']);
        }
        return get_string('dash_summary_clear', 'theme_diverse');
    }
}
