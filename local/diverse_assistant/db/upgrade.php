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
    return true;
}
