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
 * Callbacks for auth_gt4t_validator plugin.
 *
 * @package    auth_gt4t_validator
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the signup form with a Student Number (idnumber) field.
 *
 * This callback is called by core_login_extend_signup_form() in login/signup_form.php.
 *
 * @param MoodleQuickForm $mform The signup form
 */
function auth_gt4t_validator_extend_signup_form($mform) {
    // Add Student Number field before the submit buttons.
    $mform->addElement('text', 'idnumber', get_string('studentno', 'auth_gt4t_validator'), 'maxlength="50" size="12"');
    $mform->setType('idnumber', PARAM_RAW);
    $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');
}

/**
 * Validate the Student Number field on the signup form.
 *
 * @param array $data The form data
 * @return array Array of errors
 */
function auth_gt4t_validator_validate_extend_signup_form($data) {
    $errors = [];

    if (empty($data['idnumber'])) {
        $errors['idnumber'] = get_string('required');
    }

    return $errors;
}
