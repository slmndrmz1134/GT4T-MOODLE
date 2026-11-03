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

namespace local_diverse_darklogo;

use local_diverse_darklogo\local\logos;

/**
 * DIVERSE dark logos - Hook callbacks
 *
 * Adds the dark logos to the partner management menu of tool_mutenancy.
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * On the Appearance tab of a partner, a section with its dark logos and the button to change them, under the
     * section of the normal logos. tool_mutenancy has no hook for the tab: the section is added to the page and
     * moved into place by a script (without it, it stays at the end of the page).
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(\core\hook\output\before_footer_html_generation $hook): void {
        global $PAGE, $DB;

        if (during_initial_install() || $PAGE->url->get_path() !== '/admin/tool/mutenancy/tenant_appearance.php') {
            return;
        }
        $tenantid = (int)$PAGE->url->get_param('id');
        if (!$tenantid || !$DB->record_exists('tool_mutenancy_tenant', ['id' => $tenantid])) {
            return;
        }
        $context = \context_tenant::instance($tenantid);
        if (!has_capability('tool/mutenancy:view', $context)) {
            return;
        }

        $rows = '';
        foreach ([logos::LOGO => 'logo', logos::COMPACT => 'logocompact'] as $area => $string) {
            $url = logos::url($tenantid, $area);
            $value = $url ? \html_writer::img($url->out(false), '', ['style' => 'max-height: 64px; background-color: #1f2328;'])
                : get_string('none');
            $rows .= \html_writer::tag('dt', get_string($string, 'local_diverse_darklogo'), ['class' => 'col-sm-3']) .
                \html_writer::tag('dd', $value, ['class' => 'col-sm-9']);
        }
        $buttons = '';
        if (has_capability('tool/mutenancy:configappearance', $context)) {
            $buttons = \html_writer::div(
                \html_writer::link(
                    new \core\url('/local/diverse_darklogo/edit.php', ['id' => $tenantid]),
                    get_string('appearance_edit', 'local_diverse_darklogo'),
                    ['class' => 'btn btn-secondary']
                ),
                'buttons'
            );
        }
        $section = \html_writer::tag('h2', get_string('menuitem', 'local_diverse_darklogo')) .
            \html_writer::tag('dl', $rows, ['class' => 'row']) . $buttons;
        $hook->add_html(\html_writer::div($section, 'mb-4', ['id' => 'local-diverse-darklogo', 'hidden' => 'hidden']));

        $PAGE->requires->js_amd_inline("
            var section = document.getElementById('local-diverse-darklogo');
            var buttons = document.querySelector('#region-main .buttons');
            if (section && buttons) {
                buttons.insertAdjacentElement('afterend', section);
            }
            if (section) {
                section.hidden = false;
            }
        ");
    }

    /**
     * A link to the page where a partner's dark logos are uploaded.
     *
     * @param \tool_mutenancy\hook\tenant_management_menu $hook
     */
    public static function tenant_management_menu(\tool_mutenancy\hook\tenant_management_menu $hook): void {
        if (!has_capability('tool/mutenancy:configappearance', $hook->tenantcontext)) {
            return;
        }
        $hook->tenantnode->add(
            get_string('menuitem', 'local_diverse_darklogo'),
            new \core\url('/local/diverse_darklogo/edit.php', ['id' => $hook->tenant->id])
        );
    }
}
