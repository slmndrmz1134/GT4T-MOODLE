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
