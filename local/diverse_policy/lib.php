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
 * DIVERSE policies: two separate checkboxes on the signup form, one per document.
 *
 * New users confirm that they have read the Privacy Policy and accept the Terms of Use, each with a link opening the
 * PDF in a new tab. Moodle's own site policy (one embedded document after login and one checkbox at the end of the
 * form) is not used: the project partners asked for this instead (October 2026).
 *
 * @package    local_diverse_policy
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_diverse_policy\policies;

/**
 * Add the two checkboxes to the signup form (callback of core_login_extend_signup_form()).
 *
 * @param MoodleQuickForm $mform
 */
function local_diverse_policy_extend_signup_form($mform) {
    $mform->addElement('header', 'local_diverse_policy', get_string('signupheader', 'local_diverse_policy'));
    $mform->setExpanded('local_diverse_policy');
    foreach ([policies::PRIVACY, policies::TERMS] as $document) {
        $name = 'local_diverse_policy_' . $document;
        $mform->addElement('checkbox', $name, '',
            get_string('signup' . $document, 'local_diverse_policy', policies::link($document)));
        $mform->addRule($name, get_string('signuprequired', 'local_diverse_policy'), 'required', null, 'client');
    }
}

/**
 * Both checkboxes must be ticked (callback of core_login_validate_extend_signup_form()).
 *
 * @param array $data
 * @return array errors by element name
 */
function local_diverse_policy_validate_extend_signup_form($data) {
    $errors = [];
    foreach ([policies::PRIVACY, policies::TERMS] as $document) {
        if (empty($data['local_diverse_policy_' . $document])) {
            $errors['local_diverse_policy_' . $document] = get_string('signuprequired', 'local_diverse_policy');
        }
    }
    return $errors;
}

/**
 * Record on the new account that the documents were accepted (callback of core_login_post_signup_requests()).
 *
 * The new user is saved after this callback, so the flag is stored with the account (Moodle's user.policyagreed).
 *
 * @param stdClass $data The new user
 */
function local_diverse_policy_post_signup_requests($data) {
    if (!empty($data->local_diverse_policy_privacy) && !empty($data->local_diverse_policy_terms)) {
        $data->policyagreed = 1;
    }
}
