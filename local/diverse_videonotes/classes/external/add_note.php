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

namespace local_diverse_videonotes\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_diverse_videonotes\local\notes;

/**
 * DIVERSE video bookmarks - Add a bookmark of the current user
 *
 * @package    local_diverse_videonotes
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_note extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'position' => new external_value(PARAM_INT, 'Seconds from the start of the video'),
            'note' => new external_value(PARAM_TEXT, 'The note', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Add the bookmark.
     *
     * @param int $cmid
     * @param int $position
     * @param string $note
     * @return array The new bookmark.
     */
    public static function execute(int $cmid, int $position, string $note = ''): array {
        global $USER;
        $params = self::validate_parameters(self::execute_parameters(),
            ['cmid' => $cmid, 'position' => $position, 'note' => $note]);
        notes::require_use($params['cmid']);
        return notes::add($USER->id, $params['cmid'], $params['position'], $params['note']);
    }

    /**
     * Returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return get_notes::note_structure();
    }
}
