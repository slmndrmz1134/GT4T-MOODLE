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
 * GT4T Student Validator - Custom signup form with Student Number field.
 *
 * @package    auth_gt4t_validator
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/login/signup_form.php');

/**
 * Extends the default signup form to add a Student Number (idnumber) field.
 */
class auth_gt4t_validator_signup_form extends login_signup_form
{

    /**
     * Define the form - adds Student Number field to standard signup form.
     */
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;

        // ---- Build our own form with Student Number included ----

        if (function_exists('mutenancy_is_active') && mutenancy_is_active()) {
            global $SITE;
            $mform->addElement('html', '<div><strong>' . format_string($SITE->fullname) . '</strong></div>');
        }

        // Username
        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

        // ★ Student Number (Öğrenci No) - our custom field
        $mform->addElement('text', 'idnumber', get_string('studentno', 'auth_gt4t_validator'), 'maxlength="50" size="12"');
        $mform->setType('idnumber', PARAM_RAW);
        $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');

        // Password
        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('password'), [
            'maxlength' => MAX_PASSWORD_CHARACTERS,
            'size' => 12,
            'autocomplete' => 'new-password',
        ]);
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        $mform->addRule(
            'password',
            get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength',
            MAX_PASSWORD_CHARACTERS,
            'client'
        );

        // Email
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        // Email (again)
        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');

        // Name fields
        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }

        // City
        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', core_user::get_property_type('city'));
        if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }

        // Country
        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        } else {
            $mform->setDefault('country', '');
        }

        // Profile fields
        profile_signup_fields($mform);

        // Captcha
        if (signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        // Site policy
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        // Buttons
        $this->set_display_vertical();
        $this->add_action_buttons(true, get_string('createaccount'));
    }

    /**
     * Definition after data - trim fields.
     */
    public function definition_after_data()
    {
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate form data including student number.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if (empty($data['idnumber'])) {
            $errors['idnumber'] = get_string('required');
        }

        return $errors;
    }
}
