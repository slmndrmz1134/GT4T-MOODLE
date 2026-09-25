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

/**
 * DIVERSE AI assistant - AJAX functions used by the chat panel
 *
 * Sending a message is not here: answers stream from stream.php.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_diverse_assistant_get_state' => [
        'classname' => \local_diverse_assistant\external\get_state::class,
        'description' => 'Whether the assistant can be used in a course, the user\'s choices and saved chats.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_diverse_assistant_get_messages' => [
        'classname' => \local_diverse_assistant\external\get_messages::class,
        'description' => 'Messages of one of the user\'s saved chats.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_diverse_assistant_delete_conversation' => [
        'classname' => \local_diverse_assistant\external\delete_conversation::class,
        'description' => 'Delete one of the user\'s saved chats.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_diverse_assistant_delete_history' => [
        'classname' => \local_diverse_assistant\external\delete_history::class,
        'description' => 'Delete all of the user\'s saved chats in every course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_diverse_assistant_set_retention' => [
        'classname' => \local_diverse_assistant\external\set_retention::class,
        'description' => 'Set how long the user\'s chats are kept.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_diverse_assistant_accept_notice' => [
        'classname' => \local_diverse_assistant\external\accept_notice::class,
        'description' => 'Record that the user has read the notice shown before the first chat.',
        'type' => 'write',
        'ajax' => true,
    ],
];
