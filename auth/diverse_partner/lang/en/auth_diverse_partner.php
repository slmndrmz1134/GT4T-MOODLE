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
 * Strings for auth_diverse_partner.
 *
 * @package    auth_diverse_partner
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_diverse_partnerdescription'] = 'Partner accounts can only log in on the login page of their own partner organisation (chosen in the "Select Partner" menu). Elsewhere they get the usual "Invalid login" message. Site administrators can log in on every page. This method does not authenticate anybody itself.';
$string['forgot_back'] = 'Back to the login page';
$string['forgot_nopartner'] = 'Passwords are reset by your institution, not by e-mail. Please contact the partner administrator of your university or organisation; if you do not belong to a partner, contact the DIVERSE site administrator.';
$string['forgot_partner'] = 'Passwords are reset by your institution, not by e-mail. Please contact the partner administrator of {$a}.';
$string['pluginname'] = 'DIVERSE partner login check';
$string['privacy:metadata'] = 'The DIVERSE partner login check stores no personal data.';
