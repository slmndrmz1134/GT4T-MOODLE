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
use theme_diverse\local\goal;
use theme_diverse\local\partners;

/**
 * Theme DIVERSE - Data for the dashboard.
 *
 * The dashboard answers "what do I do now?" and is built from the user's roles, section by section: a user
 * who both teaches and studies sees both, a site administrator also sees the state of the site.
 *
 * A student gets a grid of cards over the whole width: progress over all activities, the activities finished this
 * week, the deadlines, the courses to continue and the weekly goal.
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

    /** @var int Largest number of deadlines on the student's card. */
    const MAX_DEADLINES = 4;

    /** @var int Largest number of course cards shown in a section. */
    const MAX_COURSES = 8;

    /** @var int Number of courses in the "Keep learning" table. */
    const MAX_RESUME = 5;

    /** @var int Radius of the progress ring in its 160 x 160 drawing. */
    const RING_RADIUS = 60;

    /** @var int Space between two parts of the progress ring, along the ring. */
    const RING_GAP = 14;

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
            'greeting' => get_string('dash_hello', 'theme_diverse', self::text($USER->firstname)),
            'today' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'allcoursesurl' => (new \core\url('/my/courses.php'))->out(false),
            'calendarurl' => (new \core\url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
            'isadmin' => $isadmin,
            'deadlines' => $todo['deadlines'],
            'hasdeadlines' => !empty($todo['deadlines']),
        ];

        $guide = shell::guide_id();
        if ($guide && !$isadmin) {
            $data['news'] = ['url' => (new \core\url('/course/view.php', ['id' => $guide]))->out(false)];
        }

        $hascatalog = class_exists(catalog::class);
        if ($hascatalog && !$isadmin) {
            $data['catalogurl'] = (new \core\url('/local/diverse_catalog/index.php'))->out(false);
            $data['recommended'] = catalog::recommended(self::MAX_COURSES);
            $data['hasrecommended'] = !empty($data['recommended']);
        }
        if ($learning) {
            $data['learning'] = $this->export_learning($learning, $todo['overduecms']);
        }
        // A site administrator is usually enrolled for support, not to teach: the site comes first for them.
        if ($teaching && !$isadmin) {
            $data['teaching'] = $this->export_teaching($teaching, $output);
            // Without the student cards, the deadlines stand next to the work to grade.
            $data['teaching']['showdeadlines'] = !$learning && $todo['deadlines'];
        }
        if ($isadmin) {
            $data['admin'] = $this->export_admin();
        }
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
     * Midnight of the Monday of the current week, in the user's time zone.
     *
     * @param int $time
     * @return int
     */
    protected static function week_start(int $time): int {
        return usergetmidnight(usergetmidnight($time) - ((int)userdate($time, '%u') - 1) * DAYSECS + 12 * HOURSECS);
    }

    /**
     * The student part: progress, this week, the courses to continue and the weekly goal.
     *
     * @param \stdClass[] $courses
     * @param int[] $overduecms Course modules with overdue work (from the calendar).
     * @return array
     */
    protected function export_learning(array $courses, array $overduecms): array {
        global $DB, $USER;

        $lastaccess = $DB->get_records_menu('user_lastaccess', ['userid' => $USER->id], '', 'courseid, timeaccess');
        uasort($courses, fn($a, $b) => ($lastaccess[$b->id] ?? 0) <=> ($lastaccess[$a->id] ?? 0));

        // When the user completed each activity of these courses.
        [$insql, $params] = $DB->get_in_or_equal(array_keys($courses), SQL_PARAMS_NAMED);
        $params['userid'] = $USER->id;
        $completed = $DB->get_records_sql_menu(
            "SELECT cmc.coursemoduleid, cmc.timemodified
               FROM {course_modules_completion} cmc
               JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
              WHERE cmc.userid = :userid AND cm.course $insql AND cmc.completionstate IN (" .
                COMPLETION_COMPLETE . ', ' . COMPLETION_COMPLETE_PASS . ')',
            $params
        );
        $viewed = $DB->get_records_sql_menu(
            "SELECT cmv.coursemoduleid, 1
               FROM {course_modules_viewed} cmv
               JOIN {course_modules} cm ON cm.id = cmv.coursemoduleid
              WHERE cmv.userid = :userid AND cm.course $insql",
            $params
        );

        $states = ['completed' => 0, 'started' => 0, 'overdue' => 0, 'notstarted' => 0];
        foreach ($courses as $course) {
            if (empty($course->enablecompletion)) {
                continue;
            }
            foreach (course_hero::activities($course) as $cm) {
                if ($cm->completion == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                $state = isset($completed[$cm->id]) ? 'completed'
                    : (in_array($cm->id, $overduecms) ? 'overdue' : (isset($viewed[$cm->id]) ? 'started' : 'notstarted'));
                $states[$state]++;
            }
        }

        $rows = [];
        foreach (array_slice($courses, 0, self::MAX_RESUME, true) as $course) {
            $rows[] = $this->export_row($course, $lastaccess[$course->id] ?? 0, count($rows) + 1);
        }

        return [
            'progress' => $this->export_progress($states),
            'week' => $this->export_week($completed),
            'goal' => $this->export_goal($completed),
            'rows' => $rows,
            'more' => count($courses) > self::MAX_RESUME,
        ];
    }

    /**
     * The progress ring: one rounded part per state of the activities, with a small gap between the parts.
     *
     * @param int[] $states Number of activities per state: completed, started, overdue, notstarted.
     * @return array
     */
    protected function export_progress(array $states): array {
        $total = array_sum($states);
        $length = 2 * M_PI * self::RING_RADIUS;
        $parts = count(array_filter($states));
        $gap = $parts > 1 ? self::RING_GAP : 0;
        $offset = 0;
        $segments = [];
        $legend = [];
        foreach ($states as $state => $count) {
            $legend[] = ['state' => $state, 'label' => get_string('dash_state_' . $state, 'theme_diverse'), 'count' => $count];
            if (!$count) {
                continue;
            }
            $part = $count / $total * $length;
            $segments[] = [
                'state' => $state,
                'dash' => round(max($part - $gap, .1), 2) . ' ' . round($length, 2),
                'offset' => round(-$offset - $gap / 2, 2),
            ];
            $offset += $part;
        }
        return [
            'total' => $total,
            'percent' => $total ? (int)floor($states['completed'] / $total * 100) : 0,
            'segments' => $segments,
            'legend' => $legend,
            'hastotal' => $total > 0,
        ];
    }

    /**
     * Activities completed on each day of the current week, and the change since last week.
     *
     * @param int[] $completed Completion time per course module.
     * @return array
     */
    protected function export_week(array $completed): array {
        $now = time();
        $monday = self::week_start($now);
        $lastmonday = self::week_start($monday - DAYSECS);
        $days = array_fill(0, 7, 0);
        $lastweek = 0;
        foreach ($completed as $time) {
            if ($time >= $monday) {
                $days[min(6, (int)floor(($time - $monday) / DAYSECS))]++;
            } else if ($time >= $lastmonday) {
                $lastweek++;
            }
        }

        // The scale goes up in steps of four, so that the five lines of the axis are whole numbers.
        $scale = max(4, (int)ceil(max($days) / 4) * 4);
        $bars = [];
        foreach ($days as $index => $count) {
            $start = usergetmidnight($monday + $index * DAYSECS + 12 * HOURSECS);
            $label = userdate($start, '%a');
            $bars[] = [
                'label' => $label,
                'date' => userdate($start, get_string('strftimedaydate', 'langconfig')),
                'count' => $count,
                'height' => (int)round($count / $scale * 100),
                'today' => $start === usergetmidnight($now),
                'description' => get_string('dash_activity_day', 'theme_diverse', (object)['day' => $label, 'count' => $count]),
            ];
        }

        $thisweek = array_sum($days);
        $change = $thisweek - $lastweek;
        return [
            'bars' => $bars,
            'axis' => array_map(fn($step) => $scale - $step * $scale / 4, range(0, 4)),
            'trend' => $change > 0 ? get_string('dash_week_more', 'theme_diverse', $change)
                : ($change < 0 ? get_string('dash_week_fewer', 'theme_diverse', -$change)
                : get_string('dash_week_same', 'theme_diverse')),
            'up' => $change > 0,
        ];
    }

    /**
     * The weekly goal: this week against the goal, the weekly average of the last four weeks and the streak.
     *
     * @param int[] $completed Completion time per course module.
     * @return array
     */
    protected function export_goal(array $completed): array {
        $target = goal::get();
        $monday = self::week_start(time());

        // Activities per week, counted back from this week (0) for a year.
        $weeks = array_fill(0, 53, 0);
        foreach ($completed as $time) {
            $back = $time >= $monday ? 0 : (int)ceil(($monday - $time) / (7 * DAYSECS));
            if ($back < 53) {
                $weeks[$back]++;
            }
        }
        $streak = 0;
        for ($week = 1; $week < 53 && $weeks[$week] >= $target; $week++) {
            $streak++;
        }
        $streak += $weeks[0] >= $target ? 1 : 0;

        $done = $weeks[0];
        $percent = (int)min(100, round($done / $target * 100));
        $average = array_sum(array_slice($weeks, 1, 4)) / 4;
        return [
            'done' => $done,
            'target' => $target,
            'percent' => $percent,
            'reached' => $done >= $target,
            'left' => get_string('dash_goal_left', 'theme_diverse', max(0, $target - $done)),
            'total' => count($completed),
            'average' => format_float($average, $average == floor($average) ? 0 : 1),
            'streak' => get_string('dash_goal_weeks', 'theme_diverse', $streak),
            'choices' => array_map(fn($choice) => [
                'label' => get_string('dash_goal_choice', 'theme_diverse', $choice),
                'url' => (new \core\url('/theme/diverse/goal.php', ['goal' => $choice, 'sesskey' => sesskey()]))->out(false),
                'current' => $choice === $target,
            ], goal::CHOICES),
        ];
    }

    /**
     * One course in the "Keep learning" table.
     *
     * @param \stdClass $course
     * @param int $lastaccess
     * @param int $number Position in the table.
     * @return array
     */
    protected function export_row(\stdClass $course, int $lastaccess, int $number): array {
        $next = course_hero::next_activity($course);
        $progress = \core_completion\progress::get_course_progress_percentage($course);
        $teacher = null;
        foreach ((new \core_course_list_element($course))->get_course_contacts() as $contact) {
            $teacher = $contact;
            break;
        }
        $activities = count(course_hero::activities($course));
        $meta = [get_string('card_activities', 'theme_diverse', $activities)];
        $meta[] = $lastaccess ? get_string('dash_lastvisit', 'theme_diverse', self::ago($lastaccess))
            : get_string('dash_notvisited', 'theme_diverse');

        return [
            'number' => $number,
            'name' => self::text($course->fullname),
            'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
            'category' => course_hero::category_name($course),
            'meta' => implode(' · ', $meta),
            'hasprogress' => $progress !== null,
            'progress' => $progress !== null ? (int)floor($progress) : 0,
            'next' => $next ? self::text($next->name) : '',
            'nexturl' => $next ? $next->url->out(false) : '',
            'nexttype' => $next ? $next->get_module_type_name() : '',
            'teacher' => $teacher ? $teacher['username'] : '',
            'initials' => $teacher ? \core_text::strtoupper(
                \core_text::substr((string)$teacher['user']->firstname, 0, 1) . \core_text::substr((string)$teacher['user']->lastname, 0, 1)
            ) : '',
            'role' => $teacher ? $teacher['rolename'] : '',
        ];
    }

    /**
     * How long ago something happened, in one unit: minutes, hours or days.
     *
     * @param int $time
     * @return string
     */
    protected static function ago(int $time): string {
        $seconds = max(60, time() - $time);
        foreach ([DAYSECS, HOURSECS, MINSECS] as $unit) {
            if ($seconds >= $unit) {
                return format_time((int)floor($seconds / $unit) * $unit);
            }
        }
        return format_time($seconds);
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
                ['value' => count($courses), 'label' => get_string('dash_stat_teaching', 'theme_diverse'), 'icon' => 'fa-book'],
                ['value' => $students, 'label' => get_string('dash_stat_students', 'theme_diverse'), 'icon' => 'fa-users'],
                ['value' => $waiting, 'label' => get_string('dash_stat_waiting', 'theme_diverse'), 'icon' => 'fa-pen',
                    'attention' => $waiting > 0],
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
                ['value' => $activeusers, 'label' => get_string('dash_stat_users', 'theme_diverse'), 'icon' => 'fa-users'],
                ['value' => $recentusers, 'label' => get_string('dash_stat_recent', 'theme_diverse'), 'icon' => 'fa-clock'],
                ['value' => $courses, 'label' => get_string('dash_stat_courses', 'theme_diverse'), 'icon' => 'fa-book'],
                ['value' => count($partnerrows), 'label' => get_string('dash_stat_partners', 'theme_diverse'),
                    'icon' => 'fa-building'],
                ['value' => $pending, 'label' => get_string('dash_stat_pending', 'theme_diverse'), 'icon' => 'fa-user',
                    'attention' => $pending > 0],
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
     * The user's deadlines, the way students plan them: overdue first, then by date.
     *
     * Events come from the calendar's action events, the same source as Moodle's timeline: they disappear
     * when the work is done, and teachers get their own actions (for example grading).
     *
     * @return array deadlines (the first MAX_DEADLINES), overduecms, overduecount, todototal
     */
    protected function get_todo(): array {
        $now = time();
        $endoftoday = usergetmidnight($now) + DAYSECS;

        $events = \core_calendar\local\api::get_action_events_by_timesort(
            $now - self::OVERDUE_DAYS * DAYSECS,
            $now + self::AHEAD_DAYS * DAYSECS,
            null,
            50,
            true
        );

        $deadlines = [];
        $overduecms = [];
        $total = 0;
        $courses = [];
        foreach ($events as $event) {
            $action = $event->get_action();
            if (!$action || !$event->get_course()) {
                continue;
            }
            $time = $event->get_times()->get_sort_time()->getTimestamp();
            $overdue = $time < $now;
            $total++;
            if ($overdue && $event->get_course_module()) {
                $overduecms[] = (int)$event->get_course_module()->get('id');
            }
            if (count($deadlines) >= self::MAX_DEADLINES) {
                continue;
            }
            $courseid = $event->get_course()->get('id');
            $courses[$courseid] = $courses[$courseid] ?? get_course($courseid);
            // The activity's own name: the event's name adds words such as "is due" in the site language.
            $name = $event->get_name();
            if ($event->get_course_module()) {
                $name = get_fast_modinfo($courses[$courseid])->get_cm($event->get_course_module()->get('id'))->name;
            }
            $date = userdate($time, get_string('strftimedaydateshort', 'theme_diverse'));
            if ($overdue) {
                $when = get_string('dash_overdue', 'theme_diverse');
            } else if ($time < $endoftoday) {
                $when = get_string('today', 'core_calendar') . ', ' . userdate($time, get_string('strftimetime', 'langconfig'));
            } else {
                $when = get_string('dash_due_in', 'theme_diverse', format_time(usergetmidnight($time) - usergetmidnight($now)));
            }
            $deadlines[] = [
                'name' => self::text($name),
                'meta' => implode(' · ', [$when, self::text($courses[$courseid]->fullname), $date]),
                'overdue' => $overdue,
                'url' => $action->get_url()->out(false),
                'action' => $action->get_name(),
            ];
        }

        return [
            'deadlines' => $deadlines,
            'overduecms' => $overduecms,
            'overduecount' => count($overduecms),
            'todototal' => $total,
        ];
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
