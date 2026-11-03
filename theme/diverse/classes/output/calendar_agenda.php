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
 * Theme DIVERSE - The agenda next to the month of the calendar: the coming events, day by day.
 *
 * The events are Moodle's own "upcoming events" of the current user (calendar_get_view(), the same as the Upcoming
 * events block: what the user may see, as far ahead as their calendar preference says), in the course the calendar
 * shows. Only read; nothing is stored.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_agenda implements renderable, templatable {
    /** @var int Most events listed. */
    const LIMIT = 8;

    /** @var string[] Colour of the bar of an event, by the kind of event (scss/calendar.scss). */
    const KINDS = ['site' => 'site', 'category' => 'category', 'course' => 'course', 'group' => 'group', 'user' => 'user'];

    /** @var int Course the calendar shows (SITEID for all courses). */
    protected $courseid;

    /**
     * Constructor.
     *
     * @param int $courseid Course the calendar shows (SITEID for all courses).
     */
    public function __construct(int $courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Export the agenda.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        $calendar = \calendar_information::create(time(), $this->courseid, null);
        [$data] = calendar_get_view($calendar, 'upcoming_mini');

        $days = [];
        foreach (array_slice($data->events ?? [], 0, self::LIMIT) as $event) {
            $event = (object)$event;
            $day = usergetmidnight($event->timestart);
            if (!isset($days[$day])) {
                $days[$day] = [
                    'label' => $day == usergetmidnight(time()) ? get_string('today', 'calendar') :
                        userdate($day, get_string('strftimedaydate', 'langconfig')),
                    'events' => [],
                ];
            }
            $course = isset($event->course) ? (object)$event->course : null;
            $start = userdate($event->timestart, get_string('strftimetime', 'langconfig'));
            $days[$day]['events'][] = [
                'name' => $event->name,
                'url' => $event->viewurl ?? $event->url ?? '',
                'course' => $course && !empty($course->fullname) && $course->id != SITEID ? $course->fullname : '',
                'time' => $event->timeduration ? $start . ' – ' .
                    userdate($event->timestart + $event->timeduration, get_string('strftimetime', 'langconfig')) : $start,
                'duration' => $event->timeduration ? format_time($event->timeduration) : '',
                'kind' => self::KINDS[$event->normalisedeventtype ?? ''] ?? 'course',
            ];
        }

        return [
            'days' => array_values($days),
            'hasevents' => !empty($days),
            'today' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'upcomingurl' => (new \core\url('/calendar/view.php', ['view' => 'upcoming', 'course' => $this->courseid]))->out(false),
        ];
    }
}
