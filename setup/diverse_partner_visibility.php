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
 * Hide a partner from the visitors' screens for a while, or show it again.
 *
 * Hiding a partner (owner's request, October 2026: GT4T for now):
 * - takes it out of the "Select Partner" menu of the login page (tenant setting "show on login page"),
 * - takes it out of the university list of the signup form (no self-registration for this partner),
 * - hides its course category, so its own courses are not listed (admins still see them).
 * The partner itself, its users and the shared courses stay as they are; its login page keeps working with its
 * direct address (/login/index.php?tenant=<idnumber>). The previous values are remembered and --show puts them back.
 *
 * Usage:
 *   php setup/diverse_partner_visibility.php --partner=gt4t --hide [--dry-run]
 *   php setup/diverse_partner_visibility.php --partner=gt4t --show [--dry-run]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options, $unrecognised] = cli_get_params(
    ['help' => false, 'dry-run' => false, 'partner' => '', 'hide' => false, 'show' => false],
    ['h' => 'help', 'n' => 'dry-run']
);
if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}
if ($options['help'] || $options['partner'] === '' || $options['hide'] === $options['show']) {
    cli_writeln("Hide a partner from the login menu, the signup list and the course list, or show it again.

Options:
      --partner=IDNUMBER  Partner (tenant) idnumber, e.g. gt4t (required).
      --hide              Hide the partner.
      --show              Show it again with the values it had before --hide.
  -n, --dry-run           Show what would change without changing anything.
  -h, --help              Print this help.");
    exit($options['help'] ? 0 : 1);
}
if (!function_exists('mutenancy_is_active') || !mutenancy_is_active()) {
    cli_error('Partner organisations (tool_mutenancy) are not active.');
}

$dryrun = (bool)$options['dry-run'];
\core\session\manager::set_user(get_admin());
$tenant = \tool_mutenancy\local\tenant::fetch_by_idnumber($options['partner']);
if (!$tenant) {
    cli_error("Partner {$options['partner']} does not exist.");
}
$category = $DB->get_record('course_categories', ['id' => $tenant->categoryid]);
$memory = 'partnerhidden_' . $tenant->idnumber;
$saved = get_config('diverse_setup', $memory);

/**
 * Print one step result.
 *
 * @param string $status OK (already fine), SET (changed), WOULD (dry run), WARN
 * @param string $message
 */
function diverse_visibility_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

cli_heading(($options['hide'] ? 'Hide' : 'Show') . " partner \"{$tenant->name}\"" . ($dryrun ? ' (dry run, nothing is changed)' : ''));

if ($options['hide']) {
    if ($saved === false) {
        $before = [
            'loginshow' => (int)$tenant->loginshow,
            'registerauth' => \tool_mutenancy\local\config::is_overridden($tenant->id, 'core', 'registerauth')
                ? \tool_mutenancy\local\config::get($tenant->id, 'core', 'registerauth') : null,
            'categoryvisible' => $category ? (int)$category->visible : null,
        ];
        if (!$dryrun) {
            set_config($memory, json_encode($before), 'diverse_setup');
        }
    }
    if (!$tenant->loginshow) {
        diverse_visibility_report('OK', 'Not in the "Select Partner" menu of the login page.');
    } else if ($dryrun) {
        diverse_visibility_report('WOULD', 'Remove it from the "Select Partner" menu of the login page.');
    } else {
        \tool_mutenancy\local\tenant::update((object)['id' => $tenant->id, 'loginshow' => 0]);
        diverse_visibility_report('SET', 'Removed from the "Select Partner" menu of the login page.');
    }
    if (\tool_mutenancy\local\config::is_overridden($tenant->id, 'core', 'registerauth')
            && (string)\tool_mutenancy\local\config::get($tenant->id, 'core', 'registerauth') === '') {
        diverse_visibility_report('OK', 'Not in the university list of the signup form.');
    } else if ($dryrun) {
        diverse_visibility_report('WOULD', 'Remove it from the university list of the signup form.');
    } else {
        \tool_mutenancy\local\config::override($tenant->id, 'registerauth', '', 'core');
        diverse_visibility_report('SET', 'Removed from the university list of the signup form.');
    }
    if (!$category || !$category->visible) {
        diverse_visibility_report('OK', 'Its course category is hidden.');
    } else if ($dryrun) {
        diverse_visibility_report('WOULD', "Hide its course category \"{$category->name}\".");
    } else {
        \core_course_category::get($category->id, MUST_EXIST, true)->hide();
        diverse_visibility_report('SET', "Course category \"{$category->name}\" hidden.");
    }
} else {
    if ($saved === false) {
        diverse_visibility_report('OK', 'The partner is not hidden by this script; nothing to show again.');
        exit(0);
    }
    $before = json_decode($saved, true);
    if ($dryrun) {
        diverse_visibility_report('WOULD', 'Put back its login menu entry, signup setting and course category visibility.');
        exit(0);
    }
    \tool_mutenancy\local\tenant::update((object)['id' => $tenant->id, 'loginshow' => $before['loginshow']]);
    diverse_visibility_report('SET', 'Login menu entry put back.');
    \tool_mutenancy\local\config::override($tenant->id, 'registerauth', $before['registerauth'], 'core');
    diverse_visibility_report('SET', 'Signup setting put back.');
    if ($category && $before['categoryvisible'] && !$category->visible) {
        \core_course_category::get($category->id, MUST_EXIST, true)->show();
        diverse_visibility_report('SET', "Course category \"{$category->name}\" visible again.");
    }
    unset_config($memory, 'diverse_setup');
}
cli_writeln('Done.');
exit(0);
