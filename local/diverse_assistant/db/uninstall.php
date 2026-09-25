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
 * DIVERSE AI assistant - Uninstall
 *
 * Moodle drops the plugin's tables and settings itself; the students' preferences are removed here.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Remove the plugin's user preferences.
 *
 * @return bool
 */
function xmldb_local_diverse_assistant_uninstall() {
    global $DB;

    $DB->delete_records_select('user_preferences', $DB->sql_like('name', ':name'),
        ['name' => $DB->sql_like_escape('local_diverse_assistant_') . '%']);

    return true;
}
