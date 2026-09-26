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

namespace local_diverse_assistant\output;

use local_diverse_assistant\local\chat_service;
use local_diverse_assistant\local\provider\factory;
use local_diverse_assistant\local\retention;
use local_diverse_assistant\local\teacher\proposals;

/**
 * The chat panel on course pages.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panel implements \renderable, \templatable {
    /** Page layouts where the panel is never shown. */
    private const EXCLUDED_LAYOUTS = ['embedded', 'frametop', 'login', 'maintenance', 'popup', 'print', 'redirect', 'secure'];

    /**
     * Constructor.
     *
     * @param \stdClass $course The course.
     * @param \context $context Context of the page (course or activity).
     */
    public function __construct(
        /** @var \stdClass The course. */
        private readonly \stdClass $course,
        /** @var \context Context of the page. */
        private readonly \context $context,
    ) {
    }

    /**
     * Whether the panel belongs on this page.
     *
     * The panel is shown whenever the user may use the assistant in the course; if it cannot answer right now
     * (e.g. during a quiz), the panel explains why.
     *
     * @param \moodle_page $page The page.
     * @return bool
     */
    public static function should_display(\moodle_page $page): bool {
        if (during_initial_install() || !get_config('local_diverse_assistant', 'version')) {
            return false;
        }
        if (!get_config('local_diverse_assistant', 'enabled') || factory::get_api_key() === '') {
            return false;
        }
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        if (empty($page->course->id) || (int)$page->course->id === SITEID) {
            return false;
        }
        if (!in_array($page->context->contextlevel, [CONTEXT_COURSE, CONTEXT_MODULE], true)) {
            return false;
        }
        if (in_array($page->pagelayout, self::EXCLUDED_LAYOUTS, true)) {
            return false;
        }
        return has_capability('local/diverse_assistant:use', \context_course::instance($page->course->id));
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): array {
        return [
            'coursename' => format_string($this->course->shortname, true, ['context' => \context_course::instance($this->course->id)]),
            'maxlength' => chat_service::MAX_MESSAGE_LENGTH,
            'retentionoptions' => retention::get_options(retention::get()),
            'teacher' => chat_service::is_teacher_mode($this->course),
            'hascm' => $this->context->contextlevel === CONTEXT_MODULE,
            'keepdays' => proposals::KEEP_DAYS,
        ];
    }

    /**
     * Settings for the JavaScript module.
     *
     * @return array
     */
    public function get_js_config(): array {
        global $USER;
        return [
            'courseid' => (int)$this->course->id,
            'cmid' => $this->context->contextlevel === CONTEXT_MODULE ? (int)$this->context->instanceid : 0,
            'userid' => (int)$USER->id,
            'streamurl' => (new \moodle_url('/local/diverse_assistant/stream.php'))->out(false),
            'historylimit' => chat_service::MAX_HISTORY,
            'teacher' => chat_service::is_teacher_mode($this->course),
            // Links to files in proposals are shown from the edit form's draft area.
            'usercontextid' => (int)\context_user::instance($USER->id)->id,
        ];
    }
}
