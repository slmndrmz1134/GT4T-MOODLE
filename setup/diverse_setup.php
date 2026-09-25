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
 * DIVERSE site setup: applies the site configuration the DIVERSE platform needs.
 *
 * Run once after installing or deploying the code (for example on the cPanel server over SSH),
 * after admin/cli/upgrade.php. It is idempotent: it only adds or enables what is missing, so it
 * is safe to run again.
 *
 * Language settings are never changed: the site language, the language list and menu, users'
 * languages and filter_multilang2's own settings stay as they are. Language packs are only
 * installed when missing, and a filter an admin has deliberately set to "Off, but available"
 * is left alone.
 *
 * Usage:
 *   php setup/diverse_setup.php [--dry-run] [--langs=tr,de,hr] [--reset-dashboards]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/filterlib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/my/lib.php');

[$options, $unrecognised] = cli_get_params(
    ['help' => false, 'dry-run' => false, 'langs' => 'tr,de,hr', 'reset-dashboards' => false],
    ['h' => 'help', 'n' => 'dry-run']
);

if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}

if ($options['help']) {
    cli_writeln("DIVERSE site setup. Applies only what is missing; safe to run again.

Options:
  -n, --dry-run          Show what would change without changing anything.
      --langs=LIST       Language packs to install when missing (default: tr,de,hr).
      --reset-dashboards Reset every user's dashboard to the default one, so that everybody
                         gets the default blocks. This removes users' own dashboard changes.
  -h, --help             Print this help.

Example:
  php setup/diverse_setup.php --dry-run
  php setup/diverse_setup.php --reset-dashboards");
    exit(0);
}

$dryrun = (bool) $options['dry-run'];
$changed = false;

/**
 * Print one step result.
 *
 * @param string $status OK (already fine), SET (changed), WOULD (dry run), SKIP (left alone), WARN
 * @param string $message
 */
function diverse_setup_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

cli_heading('DIVERSE site setup' . ($dryrun ? ' (dry run, nothing is changed)' : ''));

// 1. Theme.
if (!core_component::get_component_directory('theme_diverse')) {
    diverse_setup_report('WARN', 'theme_diverse is not installed: run admin/cli/upgrade.php first.');
} else if (get_config('core', 'theme') === 'diverse') {
    diverse_setup_report('OK', 'Site theme is diverse.');
} else if ($dryrun) {
    diverse_setup_report('WOULD', 'Set the site theme to diverse (currently ' . get_config('core', 'theme') . ').');
} else {
    set_config('theme', 'diverse');
    theme_reset_all_caches();
    diverse_setup_report('SET', 'Site theme set to diverse.');
    $changed = true;
}

// 2. Multi-language content filter: enable it and apply it to headings, never touching its own settings.
if (!core_component::get_component_directory('filter_multilang2')) {
    diverse_setup_report('WARN', 'filter_multilang2 is not installed: run admin/cli/upgrade.php first.');
} else {
    $states = filter_get_global_states();
    $state = isset($states['multilang2']) ? (int) $states['multilang2']->active : TEXTFILTER_DISABLED;
    if ($state === TEXTFILTER_ON) {
        diverse_setup_report('OK', 'Multi-language content filter is on.');
    } else if ($state === TEXTFILTER_OFF) {
        diverse_setup_report('SKIP', 'Multi-language content filter was set to "Off, but available" by an admin: left as is.');
    } else if ($dryrun) {
        diverse_setup_report('WOULD', 'Turn the multi-language content filter on.');
    } else {
        filter_set_global_state('multilang2', TEXTFILTER_ON);
        diverse_setup_report('SET', 'Multi-language content filter turned on.');
        $changed = true;
    }

    // Apply it to course names and headings too: add it to the string filters, keeping any others.
    // Moodle then turns on "Filter all strings" (filterall) by itself.
    if ($state !== TEXTFILTER_OFF) {
        $stringfilters = array_filter(explode(',', (string) get_config('core', 'stringfilters')));
        if (in_array('multilang2', $stringfilters)) {
            diverse_setup_report('OK', 'Multi-language content filter applies to headings.');
        } else if ($dryrun) {
            diverse_setup_report('WOULD', 'Apply the multi-language content filter to headings.');
        } else {
            filter_set_applies_to_strings('multilang2', true);
            diverse_setup_report('SET', 'Multi-language content filter now applies to headings.');
            $changed = true;
        }
    }
}

// 3. Language packs: install the missing ones only; installed packs and language settings are not touched.
$wanted = array_filter(array_map('trim', explode(',', core_text::strtolower($options['langs']))));
$installed = array_keys(get_string_manager()->get_list_of_translations(true));
$missing = array_values(array_diff($wanted, $installed));
if (empty($missing)) {
    diverse_setup_report('OK', 'Language packs installed: ' . implode(', ', $installed) . '.');
} else if ($dryrun) {
    diverse_setup_report('WOULD', 'Install missing language packs: ' . implode(', ', $missing) . '.');
} else {
    core_php_time_limit::raise();
    $controller = new \tool_langimport\controller();
    $controller->install_languagepacks($missing);
    foreach ($controller->info as $message) {
        diverse_setup_report('SET', strip_tags($message));
    }
    foreach ($controller->errors as $message) {
        diverse_setup_report('WARN', strip_tags($message));
    }
    get_string_manager()->reset_caches();
    $changed = true;
}

// 4. Default dashboard: timeline, recently accessed courses, calendar.
$syscontext = context_system::instance();
$systempage = $DB->get_record('my_pages', ['userid' => null, 'name' => MY_PAGE_DEFAULT, 'private' => MY_PAGE_PRIVATE]);
if (!$systempage) {
    diverse_setup_report('WARN', 'Default dashboard page not found.');
} else {
    $blockparams = ['pagetypepattern' => 'my-index', 'subpagepattern' => $systempage->id, 'parentcontextid' => $syscontext->id];
    if ($DB->record_exists('block_instances', ['blockname' => 'recentlyaccessedcourses'] + $blockparams)) {
        diverse_setup_report('OK', 'Default dashboard has the "Recently accessed courses" block.');
    } else if ($dryrun) {
        diverse_setup_report('WOULD', 'Add the "Recently accessed courses" block to the default dashboard, above the calendar.');
    } else {
        $page = new moodle_page();
        $page->set_context($syscontext);
        $page->set_pagetype('my-index');
        $page->set_subpage($systempage->id);
        $page->blocks->add_region('content');
        $page->blocks->add_block('recentlyaccessedcourses', 'content', 1, false, 'my-index', $systempage->id);
        $DB->set_field('block_instances', 'defaultweight', 2, ['blockname' => 'calendar_month'] + $blockparams);
        diverse_setup_report('SET', 'Added the "Recently accessed courses" block to the default dashboard.');
        $changed = true;
    }

    $userdashboards = $DB->count_records_select(
        'my_pages',
        'userid IS NOT NULL AND name = :name AND private = :private',
        ['name' => MY_PAGE_DEFAULT, 'private' => MY_PAGE_PRIVATE]
    );
    if (!$options['reset-dashboards']) {
        if ($userdashboards) {
            diverse_setup_report('SKIP', "$userdashboards users have their own dashboard copy and keep it; " .
                'use --reset-dashboards to give them the default one.');
        }
    } else if ($dryrun) {
        diverse_setup_report('WOULD', "Reset $userdashboards user dashboards to the default.");
    } else {
        my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');
        diverse_setup_report('SET', "Reset $userdashboards user dashboards to the default.");
        $changed = true;
    }
}

if ($changed) {
    purge_all_caches();
    diverse_setup_report('SET', 'Caches purged.');
}
cli_writeln($dryrun ? 'Dry run finished.' : 'DIVERSE setup finished.');
exit(0);
