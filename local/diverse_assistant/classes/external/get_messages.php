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
use local_diverse_assistant\local\store;
use local_diverse_assistant\local\teacher\proposals;

/**
 * Messages of one of the user's saved chats.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_messages extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'conversationid' => new external_value(PARAM_INT, 'Conversation id'),
        ]);
    }

    /**
     * Get the messages.
     *
     * @param int $conversationid Conversation id.
     * @return array
     */
    public static function execute(int $conversationid): array {
        global $USER;
        ['conversationid' => $conversationid] = self::validate_parameters(self::execute_parameters(),
            ['conversationid' => $conversationid]);
        $conversation = store::get_conversation($conversationid, (int)$USER->id);
        $context = \context_course::instance($conversation->courseid);
        self::validate_context($context);
        require_capability('local/diverse_assistant:use', $context);

        $records = store::get_messages($conversationid);
        $proposals = proposals::get_for_messages(array_column($records, 'id'));
        $messages = [];
        foreach ($records as $message) {
            $messages[] = [
                'role' => $message->role,
                'html' => $message->role === 'user' ? chat_service::render_question($message->content)
                    : (trim($message->content) === '' ? '' : chat_service::render_answer($message->content, $context)),
                'proposals' => array_map([proposals::class, 'export'], $proposals[(int)$message->id] ?? []),
            ];
        }
        return ['id' => (int)$conversation->id, 'messages' => $messages];
    }

    /**
     * Return structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Conversation id'),
            'messages' => new external_multiple_structure(new external_single_structure([
                'role' => new external_value(PARAM_ALPHA, 'user or assistant'),
                'html' => new external_value(PARAM_RAW, 'The message as safe HTML'),
                'proposals' => new external_multiple_structure(proposals::export_structure(),
                    'Changes proposed in this answer (teacher mode)'),
            ])),
        ]);
    }
}
