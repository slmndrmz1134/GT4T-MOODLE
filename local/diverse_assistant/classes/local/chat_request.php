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

namespace local_diverse_assistant\local;

/**
 * A checked question, ready to be sent to the AI service.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_request {
    /**
     * Constructor.
     *
     * @param int $userid The user asking.
     * @param int $courseid The course.
     * @param int $conversationid Saved conversation to continue, 0 for a new one.
     * @param bool $saved Whether the user's chats are saved on the server.
     * @param string $message The question.
     * @param array $messages Everything sent to the service: instructions, earlier messages and the question.
     * @param bool $teacher Teacher mode: the assistant may propose changes to the course.
     * @param array $editable Teacher mode: 'cmids' and 'sectionids' whose texts the model sees in full.
     */
    public function __construct(
        /** @var int The user asking. */
        public readonly int $userid,
        /** @var int The course. */
        public readonly int $courseid,
        /** @var int Saved conversation to continue, 0 for a new one. */
        public readonly int $conversationid,
        /** @var bool Whether the chat is saved on the server. */
        public readonly bool $saved,
        /** @var string The question. */
        public readonly string $message,
        /** @var array Messages sent to the service. */
        public readonly array $messages,
        /** @var bool Teacher mode. */
        public readonly bool $teacher = false,
        /** @var array Activities and sections the model may propose to change. */
        public readonly array $editable = [],
    ) {
    }
}
