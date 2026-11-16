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
 * DIVERSE video bookmarks - Web services of the panel under the video
 *
 * @package    local_diverse_videonotes
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_diverse_videonotes_get_notes' => [
        'classname' => \local_diverse_videonotes\external\get_notes::class,
        'description' => 'The bookmarks of the current user on the video of an activity.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/diverse_videonotes:use',
    ],
    'local_diverse_videonotes_add_note' => [
        'classname' => \local_diverse_videonotes\external\add_note::class,
        'description' => 'Add a bookmark of the current user on the video of an activity.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/diverse_videonotes:use',
    ],
    'local_diverse_videonotes_delete_note' => [
        'classname' => \local_diverse_videonotes\external\delete_note::class,
        'description' => 'Delete one of the current user\'s bookmarks.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/diverse_videonotes:use',
    ],
];
