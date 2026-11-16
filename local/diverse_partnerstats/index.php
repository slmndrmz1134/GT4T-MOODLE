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
 * DIVERSE partner statistics - The students of a partner university at a glance
 *
 * @package    local_diverse_partnerstats
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_partnerstats\local\stats;

require(__DIR__ . '/../../config.php');

require_login(null, false);

$tenants = stats::viewable_tenants();
if (!$tenants) {
    throw new \core\exception\moodle_exception('nopermission', 'local_diverse_partnerstats');
}
$tenantid = optional_param('tenant', 0, PARAM_INT);
$tenant = $tenants[$tenantid] ?? reset($tenants);

$url = new \core\url('/local/diverse_partnerstats/index.php', count($tenants) > 1 ? ['tenant' => $tenant->id] : []);
$PAGE->set_context(\context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$title = get_string('pagetitle', 'local_diverse_partnerstats', format_string($tenant->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);

$data = stats::tenant($tenant);
$totals = $data['totals'];
$dash = fn($value, $suffix = '') => $value === null ? '–' : $value . $suffix;

$context = [
    'title' => $title,
    'days' => stats::ACTIVE_DAYS,
    'tiles' => [
        ['icon' => 'fa-user-graduate', 'value' => $totals['students'],
            'label' => get_string('tile_students', 'local_diverse_partnerstats')],
        ['icon' => 'fa-bolt', 'value' => $totals['active'],
            'label' => get_string('tile_active', 'local_diverse_partnerstats', stats::ACTIVE_DAYS)],
        ['icon' => 'fa-book', 'value' => $totals['courses'], 'label' => get_string('tile_courses', 'local_diverse_partnerstats')],
        ['icon' => 'fa-circle-check', 'value' => $dash($totals['completionpercent'], '%'),
            'label' => get_string('tile_completion', 'local_diverse_partnerstats')],
        ['icon' => 'fa-award', 'value' => $dash($totals['average'], '%'),
            'label' => get_string('tile_average', 'local_diverse_partnerstats')],
    ],
    'courses' => array_map(fn($course) => $course + ['averagetext' => $dash($course['average'], '%')], $data['courses']),
    'hascourses' => !empty($data['courses']),
    'students' => array_map(fn($student) => $student + ['averagetext' => $dash($student['average'], '%')], $data['students']),
    'hasstudents' => !empty($data['students']),
    'choose' => count($tenants) > 1,
    'tenants' => array_values(array_map(fn($other) => [
        'id' => $other->id,
        'name' => format_string($other->name),
        'selected' => $other->id == $tenant->id,
    ], $tenants)),
    'action' => (new \core\url('/local/diverse_partnerstats/index.php'))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_diverse_partnerstats/index', $context);
echo $OUTPUT->footer();
