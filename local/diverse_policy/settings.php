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
 * DIVERSE policies: settings (Site administration > Plugins > Local plugins > DIVERSE policies).
 *
 * @package    local_diverse_policy
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_diverse_policy', get_string('pluginname', 'local_diverse_policy'));
    $settings->add(new admin_setting_configtext('local_diverse_policy/privacyurl',
        get_string('privacyurl', 'local_diverse_policy'), get_string('documenturl_desc', 'local_diverse_policy'),
        '', PARAM_URL));
    $settings->add(new admin_setting_configtext('local_diverse_policy/termsurl',
        get_string('termsurl', 'local_diverse_policy'), get_string('documenturl_desc', 'local_diverse_policy'),
        '', PARAM_URL));
    $ADMIN->add('localplugins', $settings);
}
