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

namespace local_diverse_videonotes;

use core\hook\output\before_footer_html_generation;

/**
 * DIVERSE video bookmarks - Hook callbacks
 *
 * @package    local_diverse_videonotes
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /** @var string[] Page layouts with an activity's own page around the video. */
    const LAYOUTS = ['incourse', 'standard', 'course'];

    /**
     * On an activity's page, the panel of the user's bookmarks; its script shows it under the video if the page has
     * one it can control (an uploaded video or a video.js player, YouTube included).
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE;

        // $PAGE->cm is a magic property: empty() would always find it empty.
        if (during_initial_install() || !isloggedin() || isguestuser() || !$PAGE->cm ||
                !in_array($PAGE->pagelayout, self::LAYOUTS, true)) {
            return;
        }
        $context = \context_module::instance($PAGE->cm->id);
        if (!has_capability('local/diverse_videonotes:use', $context)) {
            return;
        }
        $hook->add_html($hook->renderer->render_from_template('local_diverse_videonotes/panel', [
            'maxlength' => local\notes::MAX_NOTE,
        ]));
        $PAGE->requires->js_call_amd('local_diverse_videonotes/notes', 'init', [(int)$PAGE->cm->id]);
    }
}
