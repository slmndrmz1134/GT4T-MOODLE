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
 * Strings for auth_gt4t_validator plugin.
 *
 * @package    auth_gt4t_validator
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'DIVERSE Student Validator';
$string['auth_gt4t_validatordescription'] = 'This plugin validates student information (student number, email, name) against an external API before allowing registration. If the API confirms the data, registration proceeds; otherwise it is rejected.';
$string['invalidstudent'] = 'Student information could not be verified: {$a}';
$string['studentno'] = 'Student Number';
$string['apierror'] = 'API validation failed. Please try again later.';
$string['noapiurl'] = 'API URL has not been configured. Please contact the administrator.';
$string['apiurl'] = 'API URL';
$string['apiurl_desc'] = 'The full URL of the external API endpoint for student validation (e.g. https://example.com/api/validate-student).';
$string['apikey'] = 'API Key';
$string['apikey_desc'] = 'The API key to send in the X-API-Key header for authentication.';
$string['registrationpending'] = 'Registration Pending';
$string['pendingapproval'] = 'Your registration has been received and is pending approval by your university administrator. You will be able to log in once your account is approved.';
