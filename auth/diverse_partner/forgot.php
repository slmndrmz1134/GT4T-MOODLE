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
 * "Forgotten password" page: passwords are reset by the partner's manager, not by e-mail.
 *
 * The site sends no e-mail (no SMTP, owner's decision, October 2026), so Moodle's own reset by e-mail cannot work.
 * The forgottenpasswordurl setting points here (set by setup/diverse_setup.php); Moodle's login/forgot_password.php
 * redirects here too. The page names the partner chosen on the login page, if any.
 *
 * @package    auth_diverse_partner
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

$PAGE->set_url(new \core\url('/auth/diverse_partner/forgot.php'));
$PAGE->set_context(\core\context\system::instance());
$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('passwordforgotten'));
$PAGE->set_heading(get_string('passwordforgotten'));

$partner = null;
if (function_exists('mutenancy_is_active') && mutenancy_is_active()) {
    $tenantid = (int)\tool_mutenancy\local\tenancy::get_current_tenantid();
    $tenant = $tenantid ? \tool_mutenancy\local\tenant::fetch($tenantid) : null;
    if ($tenant && !$tenant->archived) {
        $partner = format_string($tenant->sitefullname ?: $tenant->name, true, ['escape' => false]);
    }
}

echo $OUTPUT->header();
// The DIVERSE theme shows its login brand panel (partner photo and name) next to the login layout's content.
if (method_exists($OUTPUT, 'login_brand_panel')) {
    echo $OUTPUT->login_brand_panel();
}
echo $OUTPUT->heading(get_string('passwordforgotten'));
$message = $partner === null ? get_string('forgot_nopartner', 'auth_diverse_partner')
    : get_string('forgot_partner', 'auth_diverse_partner', $partner);
echo $OUTPUT->notification($message, \core\output\notification::NOTIFY_INFO, false);
echo html_writer::div(
    html_writer::link(get_login_url(), get_string('forgot_back', 'auth_diverse_partner'), ['class' => 'btn btn-primary']),
    'mt-3'
);
echo $OUTPUT->footer();
