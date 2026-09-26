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

namespace local_diverse_assistant;

use core\hook\output\before_footer_html_generation;
use local_diverse_assistant\output\panel;

/**
 * Hook callbacks.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Add the chat panel to course pages.
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE;
        if (!panel::should_display($PAGE)) {
            return;
        }
        $panel = new panel($PAGE->course, $PAGE->context);
        $hook->add_html($hook->renderer->render_from_template('local_diverse_assistant/panel',
            $panel->export_for_template($hook->renderer)));
        $PAGE->requires->js_call_amd('local_diverse_assistant/assistant', 'init', [$panel->get_js_config()]);
    }
}
