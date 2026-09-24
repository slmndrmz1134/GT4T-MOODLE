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
 * Theme GT4T - Version file
 *
 * GT4T is a Boost Union child theme based on the official Boost Union Child boilerplate
 * (https://github.com/moodle-an-hochschulen/moodle-theme_boost_union_child, commit 78855a5b).
 *
 * @package    theme_gt4t
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_gt4t';
$plugin->version = 2026092500;
$plugin->release = 'v5.0-r1';
$plugin->requires = 2025041400;
$plugin->supported = [500, 500];
$plugin->maturity = MATURITY_BETA;
$plugin->dependencies = ['theme_boost_union' => 2025041466];
