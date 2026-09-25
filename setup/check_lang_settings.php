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
 * DIVERSE language settings snapshot: prints every language-related setting as sorted "key = value" lines,
 * so that a snapshot taken before a change can be compared with one taken after it.
 *
 * Read-only: it changes nothing. Covers the site language settings, installed language packs,
 * filter_multilang2's state and settings, and how many users use each language.
 *
 * Usage:
 *   php setup/check_lang_settings.php > /tmp/lang-before.txt
 *   (make the change)
 *   php setup/check_lang_settings.php > /tmp/lang-after.txt
 *   diff /tmp/lang-before.txt /tmp/lang-after.txt    (no output = language settings unchanged)
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/filterlib.php');

$lines = [];

// Site language settings.
foreach (['lang', 'langlist', 'langmenu', 'autolang', 'autolangusercreation', 'langcache', 'langstringcache',
        'stringfilters', 'filterall'] as $name) {
    $lines[] = 'core.' . $name . ' = ' . var_export(get_config('core', $name), true);
}

// Installed language packs.
$packs = array_keys(get_string_manager()->get_list_of_translations(true));
sort($packs);
$lines[] = 'langpacks = ' . implode(',', $packs);

// filter_multilang2: site-wide state (1 on, -1 off but available, -9999 disabled) and its own settings.
$states = filter_get_global_states();
$lines[] = 'filter_multilang2.state = ' . (isset($states['multilang2']) ? $states['multilang2']->active : 'not installed');
foreach ((array) get_config('filter_multilang2') as $name => $value) {
    $lines[] = 'filter_multilang2.' . $name . ' = ' . var_export($value, true);
}

// Users' chosen languages.
foreach ($DB->get_records_sql('SELECT lang, COUNT(1) AS users FROM {user} WHERE deleted = 0 GROUP BY lang') as $row) {
    $lines[] = 'users.lang.' . $row->lang . ' = ' . $row->users;
}

sort($lines);
echo implode(PHP_EOL, $lines) . PHP_EOL;
