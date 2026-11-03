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
 * DIVERSE course catalogue: search and filter the courses the user can see.
 *
 * @package    local_diverse_catalog
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_diverse_catalog\catalog;

require_login(null, false);

$query = optional_param('q', '', PARAM_TEXT);
$categories = array_map('intval', optional_param_array('cat', [], PARAM_INT));
$status = optional_param('status', 'all', PARAM_ALPHA);
$sort = optional_param('sort', 'name', PARAM_ALPHA);
$status = in_array($status, ['all', 'enrolled', 'available']) ? $status : 'all';
$sort = in_array($sort, ['name', 'newest', 'popular']) ? $sort : 'name';

$url = new moodle_url('/local/diverse_catalog/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('title', 'local_diverse_catalog'));
$PAGE->set_heading(get_string('title', 'local_diverse_catalog'));

$cards = catalog::search($query, $categories, $status, $sort);
$results = $query !== ''
    ? get_string('results_for', 'local_diverse_catalog', (object)['count' => count($cards), 'query' => $query])
    : get_string('results', 'local_diverse_catalog', count($cards));

$option = fn(string $value, string $current, string $string) => [
    'value' => $value,
    'label' => get_string($string, 'local_diverse_catalog'),
    'selected' => $value === $current,
];

// The filters in use, each with the address of the same results without it.
$params = array_filter(['q' => $query, 'status' => $status === 'all' ? '' : $status, 'sort' => $sort === 'name' ? '' : $sort]);
$without = function(string $name, $value = null) use ($url, $params, $categories): string {
    $remaining = $params;
    unset($remaining[$name]);
    $cats = $name === 'cat' ? array_values(array_diff($categories, [$value])) : $categories;
    foreach ($cats as $index => $cat) {
        $remaining["cat[$index]"] = $cat;
    }
    return (new moodle_url($url, $remaining))->out(false);
};
$allcategories = catalog::categories($categories);
$active = [];
if ($query !== '') {
    $active[] = ['label' => '“' . $query . '”', 'url' => $without('q')];
}
foreach ($allcategories as $category) {
    if ($category['selected']) {
        $active[] = ['label' => $category['name'], 'url' => $without('cat', $category['id'])];
    }
}
if ($status !== 'all') {
    $active[] = ['label' => get_string('status_' . $status, 'local_diverse_catalog'), 'url' => $without('status')];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_diverse_catalog/page', [
    'actionurl' => $url->out(false),
    'query' => $query,
    'stats' => [
        ['value' => count(catalog::courses()), 'label' => get_string('stat_courses', 'local_diverse_catalog')],
        ['value' => count($allcategories), 'label' => get_string('stat_categories', 'local_diverse_catalog')],
        ['value' => count(enrol_get_my_courses('id')), 'label' => get_string('stat_mine', 'local_diverse_catalog')],
    ],
    'active' => $active,
    'hasactive' => !empty($active),
    'categories' => $allcategories,
    'statuses' => [
        $option('all', $status, 'status_all'),
        $option('enrolled', $status, 'status_enrolled'),
        $option('available', $status, 'status_available'),
    ],
    'sorts' => [
        $option('name', $sort, 'sort_name'),
        $option('newest', $sort, 'sort_newest'),
        $option('popular', $sort, 'sort_popular'),
    ],
    'results' => $results,
    'courses' => $cards,
    'hascourses' => !empty($cards),
    'filtered' => $query !== '' || $categories || $status !== 'all',
]);
echo $OUTPUT->footer();
