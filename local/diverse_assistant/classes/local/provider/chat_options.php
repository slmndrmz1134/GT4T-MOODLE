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
 * Extra settings for one request: tools the model may call and a longer answer limit.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_options {
    /**
     * Constructor.
     *
     * @param array $tools Tools the model may call: list of ['name' => string, 'description' => string,
     *     'parameters' => array JSON schema of the arguments]. Tool calls are returned, never run by the service.
     * @param int $maxoutputtokens Longest answer in tokens; 0 for the service's default.
     * @param \Closure|null $ontoolstart Called with the tool name when the model starts writing a tool call.
     */
    public function __construct(
        /** @var array Tools the model may call. */
        public readonly array $tools = [],
        /** @var int Longest answer in tokens, 0 for the default. */
        public readonly int $maxoutputtokens = 0,
        /** @var \Closure|null Called with the tool name when a tool call starts. */
        public readonly ?\Closure $ontoolstart = null,
    ) {
    }
}
