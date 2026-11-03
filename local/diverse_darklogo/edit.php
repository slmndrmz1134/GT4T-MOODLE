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
 * DIVERSE dark logos - Upload the dark logos of a partner
 *
 * @package    local_diverse_darklogo
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_darklogo\local\logos;

require(__DIR__ . '/../../config.php');

$tenantid = required_param('id', PARAM_INT);

require_login();

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);
$context = \context_tenant::instance($tenant->id);
require_capability('tool/mutenancy:configappearance', $context);

$returnurl = new \core\url('/admin/tool/mutenancy/tenant.php', ['id' => $tenant->id]);
$title = get_string('pagetitle', 'local_diverse_darklogo', format_string($tenant->name));

$PAGE->set_context($context);
$PAGE->set_url('/local/diverse_darklogo/edit.php', ['id' => $tenant->id]);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$form = new \local_diverse_darklogo\form\edit(null, ['tenantid' => $tenant->id]);

$data = new \stdClass();
foreach (logos::AREAS as $area) {
    $draftid = file_get_submitted_draft_itemid($area);
    file_prepare_draft_area($draftid, $context->id, logos::COMPONENT, $area, 0, logos::options());
    $data->$area = $draftid;
}
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $form->get_data()) {
    foreach (logos::AREAS as $area) {
        file_save_draft_area_files($formdata->$area, $context->id, logos::COMPONENT, $area, 0, logos::options());
    }
    // The pages show the logos from the theme's caches.
    theme_reset_all_caches();
    redirect($returnurl, get_string('saved', 'local_diverse_darklogo'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::div(get_string('intro', 'local_diverse_darklogo'), 'mb-3');
$form->display();
echo $OUTPUT->footer();
