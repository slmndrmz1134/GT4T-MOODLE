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
 * The user stopped the answer (closed the connection): the request to the AI service is ended as well.
 *
 * Thrown from the callback that receives the answer. It is a provider_exception so the providers pass it on unchanged,
 * but it is not an error of the service: no fallback model is tried and nothing is recorded for the settings page.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aborted_exception extends provider_exception {
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('erroraborted');
    }
}
