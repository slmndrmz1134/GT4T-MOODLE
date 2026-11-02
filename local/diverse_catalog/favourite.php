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
 * Adds a course to the user's favourites or removes it, from the star on a course card.
 *
 * @package    local_diverse_catalog
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_diverse_catalog\catalog;

$id = required_param('id', PARAM_INT);
$favourite = required_param('favourite', PARAM_BOOL);
$returnurl = required_param('returnurl', PARAM_LOCALURL);

require_login(null, false);
require_sesskey();

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/diverse_catalog/favourite.php'));

catalog::set_favourite($id, (bool)$favourite);
redirect(new moodle_url($returnurl));
