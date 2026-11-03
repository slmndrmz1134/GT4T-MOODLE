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
 * Theme DIVERSE - Switch the colour palette of the current user (link in the user menu).
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();
if (isguestuser()) {
    throw new \core\exception\moodle_exception('noguest');
}

$green = optional_param('green', null, PARAM_BOOL);
$mode = optional_param('mode', '', PARAM_ALPHA);
$returnurl = optional_param('returnurl', '/my/', PARAM_LOCALURL);
$ajax = optional_param('ajax', false, PARAM_BOOL);

if ($green !== null) {
    \theme_diverse\local\palette::set($green);
}
if ($mode !== '') {
    \theme_diverse\local\palette::set_mode($mode);
}
// The switch in the top bar changes the stylesheet in the page itself and only saves the choice here.
if ($ajax) {
    echo json_encode(['green' => \theme_diverse\local\palette::is_green(), 'mode' => \theme_diverse\local\palette::mode()]);
    die;
}
redirect(new \core\url($returnurl ?: '/my/'));
