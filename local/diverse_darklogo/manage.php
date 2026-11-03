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
 * DIVERSE dark logos - The partners and what they have uploaded, for the site administrator
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_darklogo\local\logos;

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_diverse_darklogo');

$tenants = class_exists(\context_tenant::class) && $DB->get_manager()->table_exists('tool_mutenancy_tenant')
    ? $DB->get_records('tool_mutenancy_tenant', null, 'name ASC', 'id, name')
    : [];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manage', 'local_diverse_darklogo'));
echo html_writer::div(get_string('intro', 'local_diverse_darklogo'), 'mb-3');

if (!$tenants) {
    echo $OUTPUT->notification(get_string('manage_empty', 'local_diverse_darklogo'), 'info');
} else {
    $table = new html_table();
    $table->head = [get_string('manage_tenant', 'local_diverse_darklogo'), get_string('manage_status', 'local_diverse_darklogo'), ''];
    foreach ($tenants as $tenant) {
        $count = (int)(bool)logos::file($tenant->id, logos::LOGO) + (int)(bool)logos::file($tenant->id, logos::COMPACT);
        $status = [0 => 'manage_none', 1 => 'manage_one', 2 => 'manage_both'][$count];
        $edit = new \core\url('/local/diverse_darklogo/edit.php', ['id' => $tenant->id]);
        $table->data[] = [
            format_string($tenant->name),
            get_string($status, 'local_diverse_darklogo'),
            html_writer::link($edit, get_string('manage_edit', 'local_diverse_darklogo')),
        ];
    }
    echo html_writer::table($table);
}
echo $OUTPUT->footer();
