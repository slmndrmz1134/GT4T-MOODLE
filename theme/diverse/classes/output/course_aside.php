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
 * Theme DIVERSE - The column next to the sections on the course page: the next live class, the deadlines of the
 * course, the course assistant and the teachers.
 *
 * Everything comes from Moodle's own APIs: the live class from the calendar events of the course's BigBlueButton
 * activities, the deadlines from the calendar's action events and the teachers from the course contacts.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_aside implements renderable, templatable {
    /** @var int Largest number of deadlines shown. */
    const MAX_DEADLINES = 3;

    /** @var int How far back overdue work is shown, as on the dashboard. */
    const OVERDUE_DAYS = 30;

    /** @var int How far ahead the next live class is looked for. */
    const LIVE_DAYS = 60;

    /** @var string Module of the live classes. */
    const LIVE_MODULE = 'bigbluebuttonbn';

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
     * Export the column.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $deadlines = $this->deadlines();
        $teachers = $this->teachers();
        $live = $this->live();
        $next = $this->teaches() ? null : course_hero::next_activity($this->course);
        return [
            // For the sections on the page: how far the student is in each, and the activity to do next.
            'sectionsjson' => json_encode(['sections' => $this->sections(), 'next' => $next ? (int)$next->id : 0]),
            'live' => $live,
            'haslive' => $live !== null,
            'deadlines' => $deadlines,
            'hasdeadlines' => !empty($deadlines),
            'calendarurl' => (new \core\url('/calendar/view.php', ['view' => 'upcoming', 'course' => $this->course->id]))->out(false),
            'assistant' => \core_component::get_component_directory('local_diverse_assistant') !== null,
            'teachers' => $teachers,
            'hasteachers' => !empty($teachers),
        ];
    }

    /**
     * Whether the user teaches the course: teachers have no progress of their own.
     *
     * @return bool
     */
    protected function teaches(): bool {
        return has_capability('moodle/grade:viewall', \context_course::instance($this->course->id));
    }

    /**
     * How many of the tracked activities of each section the student has completed.
     *
     * @return array[] number, text ("2 of 4 done"), percent; empty for teachers and without completion tracking.
     */
    protected function sections(): array {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        $completion = new \completion_info($this->course);
        if ($this->teaches() || !$completion->is_enabled()) {
            return [];
        }
        $modinfo = get_fast_modinfo($this->course);
        $sections = [];
        foreach ($modinfo->get_section_info_all() as $section) {
            $done = 0;
            $total = 0;
            foreach ($modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if (!$cm->uservisible || $completion->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                $total++;
                $state = $completion->get_data($cm, true)->completionstate;
                $done += in_array($state, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS]) ? 1 : 0;
            }
            if ($total) {
                $sections[] = [
                    'number' => (int)$section->section,
                    'text' => get_string('course_section_done', 'theme_diverse', (object)['done' => $done, 'total' => $total]),
                    'percent' => (int)floor($done / $total * 100),
                ];
            }
        }
        return $sections;
    }

    /**
     * The next live class of the course, or the live classroom when no session is planned.
     *
     * @return array|null name, when, url, scheduled; null if the course has no live classroom the user can open.
     */
    protected function live(): ?array {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $rooms = array_filter(
            get_fast_modinfo($this->course)->get_instances_of(self::LIVE_MODULE),
            fn($cm) => $cm->uservisible
        );
        if (!$rooms) {
            return null;
        }
        $byinstance = [];
        foreach ($rooms as $cm) {
            $byinstance[$cm->instance] = $cm;
        }

        // A session that started in the last hours may still be running.
        $now = time();
        $events = calendar_get_legacy_events($now - 3 * HOURSECS, $now + self::LIVE_DAYS * DAYSECS, false, false,
            $this->course->id, false);
        uasort($events, fn($a, $b) => $a->timestart <=> $b->timestart);
        foreach ($events as $event) {
            $ends = $event->timestart + max((int)$event->timeduration, HOURSECS);
            if ($event->modulename !== self::LIVE_MODULE || !isset($byinstance[$event->instance]) || $ends < $now) {
                continue;
            }
            $cm = $byinstance[$event->instance];
            return [
                'name' => self::text($cm->name),
                'when' => userdate($event->timestart, get_string('strftimedaydatetime', 'langconfig')),
                'url' => $cm->url->out(false),
                'scheduled' => true,
            ];
        }

        $cm = reset($rooms);
        return ['name' => self::text($cm->name), 'when' => '', 'url' => $cm->url->out(false), 'scheduled' => false];
    }

    /**
     * The next deadlines of the course: overdue ones first.
     *
     * @return array[] name, url, day, month, meta, overdue
     */
    protected function deadlines(): array {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $now = time();
        $events = \core_calendar\local\api::get_action_events_by_course(
            $this->course,
            $now - self::OVERDUE_DAYS * DAYSECS,
            null,
            null,
            self::MAX_DEADLINES
        );
        $modinfo = get_fast_modinfo($this->course);
        $deadlines = [];
        foreach ($events as $event) {
            $action = $event->get_action();
            $module = $event->get_course_module();
            if (!$action || !$module) {
                continue;
            }
            $cm = $modinfo->get_cm($module->get('id'));
            $time = $event->get_times()->get_sort_time()->getTimestamp();
            $overdue = $time < $now;
            $when = $overdue ? get_string('dash_overdue', 'theme_diverse')
                : get_string('dash_due_in', 'theme_diverse', format_time(max(DAYSECS, usergetmidnight($time) - usergetmidnight($now))));
            $deadlines[] = [
                'name' => self::text($cm->name),
                'url' => $action->get_url()->out(false),
                'day' => userdate($time, '%d'),
                'month' => userdate($time, '%b'),
                'meta' => $when . ' · ' . $cm->get_module_type_name(),
                'overdue' => $overdue,
            ];
        }
        return $deadlines;
    }

    /**
     * The teachers of the course, with a way to write to them.
     *
     * @return array[] name, role, initials, messageurl
     */
    protected function teachers(): array {
        global $CFG, $USER;

        $teachers = [];
        foreach ((new \core_course_list_element($this->course))->get_course_contacts() as $contact) {
            $user = $contact['user'];
            $teachers[] = [
                'name' => $contact['username'],
                'role' => $contact['rolename'],
                'initials' => \core_text::strtoupper(\core_text::substr((string)$user->firstname, 0, 1) .
                    \core_text::substr((string)$user->lastname, 0, 1)),
                'messageurl' => !empty($CFG->messaging) && $user->id != $USER->id
                    ? (new \core\url('/message/index.php', ['id' => $user->id]))->out(false) : '',
            ];
        }
        return $teachers;
    }
}
