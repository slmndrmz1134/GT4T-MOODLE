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

namespace local_diverse_assistant\local\provider;

/**
 * A finished answer from an AI service.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_result {
    /**
     * Constructor.
     *
     * @param string $text The answer (Markdown).
     * @param int $prompttokens Input tokens the service counted, 0 if it did not say.
     * @param int $completiontokens Output tokens the service counted, 0 if it did not say.
     * @param string $finishreason Why the answer ended, e.g. "stop" or "length".
     */
    public function __construct(
        /** @var string The answer (Markdown). */
        public readonly string $text,
        /** @var int Input tokens. */
        public readonly int $prompttokens = 0,
        /** @var int Output tokens. */
        public readonly int $completiontokens = 0,
        /** @var string Why the answer ended. */
        public readonly string $finishreason = '',
    ) {
    }
}
