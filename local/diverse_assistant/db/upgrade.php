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
 * DIVERSE AI assistant - Upgrade steps
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion Version before the upgrade.
 * @return bool
 */
function xmldb_local_diverse_assistant_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026092600) {
        // Every service now keeps its own API key: move the single key to the service it was saved for.
        $key = get_config('local_diverse_assistant', 'apikey');
        if ($key !== false) {
            $provider = get_config('local_diverse_assistant', 'provider') ?: 'openai';
            if ($key !== '' && !get_config('local_diverse_assistant', 'apikey_' . $provider)) {
                set_config('apikey_' . $provider, $key, 'local_diverse_assistant');
            }
            unset_config('apikey', 'local_diverse_assistant');
        }
        // Check the connection again when the settings page opens, to record the service with the result.
        set_config('statusdirty', 1, 'local_diverse_assistant');
        upgrade_plugin_savepoint(true, 2026092600, 'local', 'diverse_assistant');
    }

    if ($oldversion < 2026092700) {
        // Teacher mode: changes the assistant proposes to teachers.
        $table = new xmldb_table('local_diverse_assistant_prop');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('conversationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('messageid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('action', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('proposed', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('original', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('appliedhash', XMLDB_TYPE_CHAR, '40', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_index('conversationid', XMLDB_INDEX_NOTUNIQUE, ['conversationid']);
        $table->add_index('messageid', XMLDB_INDEX_NOTUNIQUE, ['messageid']);
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, ['timemodified']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2026092700, 'local', 'diverse_assistant');
    }
    return true;
}
