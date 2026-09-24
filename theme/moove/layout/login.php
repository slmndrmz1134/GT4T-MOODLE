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

defined('MOODLE_INTERNAL') || die();

/**
 * Login page layout for theme_moove (GT4T branding).
 *
 * @package    theme_moove
 * @copyright  2025 Willian Mano - willianmanoaraujo@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

$loginbackgroundurl = \theme_moove_get_login_background_url();
$bodyclasses = $loginbackgroundurl ? ['gt4t-login-has-bgimage'] : [];
$bodyattributes = $OUTPUT->body_attributes($bodyclasses);
$logourl = $OUTPUT->get_logo();

$templatecontext = [
    'sitename' => format_string($SITE->fullname, true, [
        'context' => \core\context\course::instance(SITEID),
        'escape' => false,
    ]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'wwwroot' => $CFG->wwwroot,
    'logourl' => $logourl ?: false,
    'loginbackgroundurl' => $loginbackgroundurl,
];

echo $OUTPUT->render_from_template('theme_moove/login', $templatecontext);
