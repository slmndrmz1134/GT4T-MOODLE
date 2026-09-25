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

namespace local_diverse_assistant\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_diverse_assistant\local\chat_service;
use local_diverse_assistant\local\provider\factory;
use local_diverse_assistant\local\provider\provider_exception;
use local_diverse_assistant\local\retention;
use local_diverse_assistant\local\store;

/**
 * Whether the assistant can be used in a course, the user's choices and saved chats.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_state extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course id'),
        ]);
    }

    /**
     * Get the state.
     *
     * @param int $courseid Course id.
     * @return array
     */
    public static function execute(int $courseid): array {
        global $USER;
        ['courseid' => $courseid] = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);
        $course = get_course($courseid);
        $context = \context_course::instance($course->id);
        self::validate_context($context);
        require_capability('local/diverse_assistant:use', $context);

        $reason = chat_service::get_unavailable_reason($course);
        try {
            $providername = factory::create()->get_name();
        } catch (provider_exception $e) {
            $providername = '';
        }

        $retention = retention::get();
        $conversations = [];
        if ($retention !== retention::NOT_SAVED) {
            foreach (store::list_conversations((int)$USER->id, (int)$course->id) as $conversation) {
                $conversations[] = [
                    'id' => (int)$conversation->id,
                    'title' => $conversation->title,
                    'time' => userdate($conversation->timemodified, get_string('strftimedatetimeshort', 'langconfig')),
                ];
            }
        }

        return [
            'available' => $reason === '',
            'reason' => $reason === '' ? '' : get_string('reason_' . $reason, 'local_diverse_assistant'),
            'noticeaccepted' => retention::notice_accepted(),
            'notice' => get_string('notice_body', 'local_diverse_assistant', $providername),
            'retention' => $retention,
            'retentionlabel' => retention::get_label($retention),
            'conversations' => $conversations,
        ];
    }

    /**
     * Return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'available' => new external_value(PARAM_BOOL, 'Whether questions can be asked now'),
            'reason' => new external_value(PARAM_TEXT, 'Why not, if not'),
            'noticeaccepted' => new external_value(PARAM_BOOL, 'Whether the user has read the notice'),
            'notice' => new external_value(PARAM_TEXT, 'The notice shown before the first chat'),
            'retention' => new external_value(PARAM_INT, 'Days chats are kept; 0 not saved, -1 until deleted'),
            'retentionlabel' => new external_value(PARAM_TEXT, 'Name of the retention choice'),
            'conversations' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Conversation id'),
                'title' => new external_value(PARAM_TEXT, 'Start of the first question'),
                'time' => new external_value(PARAM_TEXT, 'Time of the last message'),
            ])),
        ]);
    }
}
