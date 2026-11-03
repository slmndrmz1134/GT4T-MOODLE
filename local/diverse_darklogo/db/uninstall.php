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
 * DIVERSE dark logos - Uninstall: the logos of the partners go with the plugin
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Delete the dark logos of all partners.
 *
 * @return bool
 */
function xmldb_local_diverse_darklogo_uninstall(): bool {
    global $DB;

    $fs = get_file_storage();
    $contextids = $DB->get_fieldset_sql(
        'SELECT DISTINCT contextid FROM {files} WHERE component = :component',
        ['component' => 'local_diverse_darklogo']
    );
    foreach ($contextids as $contextid) {
        $fs->delete_area_files($contextid, 'local_diverse_darklogo');
    }
    return true;
}
