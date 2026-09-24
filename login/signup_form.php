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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once('lib.php');

class login_signup_form extends moodleform implements renderable, templatable {
    function definition() {
        global $USER, $CFG, $PAGE;

        $mform = $this->_form;

        // Always post to the canonical signup URL (avoids relative-path 404s).
        $mform->updateAttributes(['action' => (new moodle_url('/login/signup.php'))->out(false)]);

        if (mutenancy_is_active()) {
            $tenantchoices = login_get_signup_tenant_choices();
            $tenantids = array_values(array_filter(array_keys($tenantchoices), static function($key): bool {
                return $key !== '';
            }));

            if (count($tenantids) > 1) {
                $mform->addElement(
                    'select',
                    'tenantid',
                    get_string('signuptenant', 'theme_moove'),
                    $tenantchoices
                );
                $mform->addRule('tenantid', get_string('required'), 'required', null, 'client');
                $mform->addRule('tenantid', get_string('required'), 'required', null, 'server');
                $mform->addElement(
                    'static',
                    'tenantid_help',
                    '',
                    get_string('signuptenant_help', 'theme_moove')
                );

                $currenttenantid = (int) \tool_mutenancy\local\tenancy::get_current_tenantid();
                if ($currenttenantid && isset($tenantchoices[$currenttenantid])) {
                    $mform->setDefault('tenantid', $currenttenantid);
                }
            } else if (count($tenantids) === 1) {
                $mform->addElement('hidden', 'tenantid', $tenantids[0]);
                $mform->setType('tenantid', PARAM_INT);
                $label = $tenantchoices[$tenantids[0]];
                $mform->addElement(
                    'static',
                    'tenantid_display',
                    get_string('signuptenant', 'theme_moove'),
                    \html_writer::tag('strong', $label)
                );
            }
        }

        $mform->addElement('hidden', 'user_role', 'student');
        $mform->setType('user_role', PARAM_ALPHA);

        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

        if (!empty($CFG->passwordpolicy)){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('password', 'password', get_string('password'), [
            'maxlength' => MAX_PASSWORD_CHARACTERS,
            'size' => 12,
            'autocomplete' => 'new-password',
        ]);
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        $mform->addRule('password', get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength', MAX_PASSWORD_CHARACTERS, 'client');

        $mform->addElement('password', 'password2', get_string('password') . ' (' . get_string('again') . ')', [
            'maxlength' => MAX_PASSWORD_CHARACTERS,
            'size' => 12,
            'autocomplete' => 'new-password',
        ]);
        $mform->setType('password2', core_user::get_property_type('password'));
        $mform->addRule('password2', get_string('missingpassword'), 'required', null, 'client');
        $mform->addRule('password2', get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength', MAX_PASSWORD_CHARACTERS, 'client');

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

        profile_signup_fields($mform);

        if (signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        // Add "Agree to sitepolicy" controls. By default it is a link to the policy text and a checkbox but
        // it can be implemented differently in custom sitepolicy handlers.
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        // buttons
        $this->set_display_vertical();
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (mutenancy_is_active()) {
            $tenantchoices = login_get_signup_tenant_choices();
            $tenantids = array_values(array_filter(array_keys($tenantchoices), static function($key): bool {
                return $key !== '';
            }));

            if (count($tenantids) > 0) {
                $tenantid = isset($data['tenantid']) ? (int) $data['tenantid'] : 0;
                if (!login_is_valid_signup_tenant($tenantid)) {
                    $errors['tenantid'] = get_string('signuptenantinvalid', 'theme_moove');
                }
            }
        }

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, core_login_validate_extend_signup_form($data));

        if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        // Core validator still expects email2; we only ask for email once on the form.
        $data['email2'] = $data['email'] ?? '';

        if (isset($data['password'], $data['password2']) && $data['password'] !== $data['password2']) {
            $errors['password2'] = get_string('passwordsdiffer');
        }

        $errors += signup_validate_data($data, $files);

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
