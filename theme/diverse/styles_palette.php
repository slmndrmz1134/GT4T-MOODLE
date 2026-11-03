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
 * Theme DIVERSE - Serve the stylesheet of the green palette.
 *
 * A slimmed-down /theme/styles.php: the same theme compiled with $diverse-palette set to green, cached next to the
 * normal stylesheet in the local cache. Pages of users with the green palette load it instead of the normal one
 * (\theme_diverse\local\palette::alter_css_urls()). The address has the same parts as the normal one:
 * /theme/diverse/styles_palette.php[/_s]/diverse/<rev>_<subrev>/all[-rtl].
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
define('ABORT_AFTER_CONFIG', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/csslib.php');

$slashargument = ltrim((string)min_get_slash_argument(), '/');
$usesvg = strpos($slashargument, '_s/') !== 0;
if (!$usesvg) {
    $slashargument = substr($slashargument, 3);
}
$parts = explode('/', $slashargument);
if (count($parts) !== 3 || $parts[0] !== 'diverse' || !in_array($parts[2], ['all', 'all-rtl'], true)) {
    css_send_css_not_found();
}
$type = $parts[2];
$revs = explode('_', $parts[1]);
$rev = min_clean_param($revs[0], 'INT');
$subrev = min_clean_param($revs[1] ?? 0, 'INT');

$candidatesheet = "{$CFG->localcachedir}/theme/{$rev}/diverse/css/{$type}_{$subrev}_green" . ($usesvg ? '' : '-nosvg') . '.css';
$etag = sha1(implode('/', [$rev, 'diverse', $type, $subrev, 'green', $usesvg]));

// Most requests: the stylesheet of the current revision is already compiled.
if (min_is_revision_valid_and_current($rev) && file_exists($candidatesheet)) {
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        css_send_unmodified(filemtime($candidatesheet), $etag);
    }
    css_send_cached_css($candidatesheet, $etag);
}

define('ABORT_AFTER_CONFIG_CANCEL', true);
define('NO_MOODLE_COOKIES', true);
define('NO_UPGRADE_CHECK', true);
require("$CFG->dirroot/lib/setup.php");

$theme = \core\output\theme_config::load('diverse');
$theme->force_svg_use($usesvg);
$theme->set_rtl_mode($type === 'all-rtl');
$current = $rev > 0 && $rev == theme_get_revision() && $subrev == theme_get_sub_revision_for_theme('diverse');

// Compiling takes a while: one request compiles, the others wait for its result.
$lock = \core\lock\lock_config::get_lock_factory('theme_diverse_palette')->get_lock("{$type}_{$usesvg}", 120);
if (!$current || !file_exists($candidatesheet)) {
    \theme_diverse\local\palette::$compilinggreen = true;
    $csscontent = $theme->get_css_content();
    if ($current) {
        css_store_css($theme, $candidatesheet, $csscontent);
    }
}
if ($lock) {
    $lock->release();
}

if ($current) {
    css_send_cached_css($candidatesheet, $etag);
}
// An old revision: do not let browsers keep it.
css_send_uncached_css($csscontent);
