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
 * Authentication Plugin: DIVERSE Student Validator
 *
 * Kayıt sırasında öğrenci bilgilerini harici bir API ile doğrular.
 *
 * @package    auth_gt4t_validator
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

/**
 * DIVERSE Student Validator authentication plugin.
 *
 * Kayıt formu gönderildiğinde, öğrenci bilgilerini (öğrenci no, e-posta, isim, soyisim)
 * harici bir API'ye gönderip doğrulama yapar. API onay verirse kayıt tamamlanır.
 */
class auth_plugin_gt4t_validator extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'gt4t_validator';
        $this->config = get_config('auth_gt4t_validator');
    }

    /**
     * Kullanıcı girişi — Moodle'ın dahili parola doğrulaması kullanılır.
     *
     * @param string $username Kullanıcı adı
     * @param string $password Parola
     * @return bool
     */
    public function user_login($username, $password) {
        global $CFG, $DB;
        if ($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id))) {
            return validate_internal_user_password($user, $password);
        }
        return false;
    }

    /**
     * Parola güncellemesi.
     *
     * @param object $user
     * @param string $newpassword
     * @return bool
     */
    public function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        return update_internal_user_password($user, $newpassword);
    }

    /**
     * Bu plugin kayıt (signup) destekliyor mu?
     *
     * @return bool
     */
    public function can_signup() {
        return true;
    }

    /**
     * Kayıt formu submit edilince çalışır.
     * Önce API'ye istek atılır, doğrulama başarılıysa kullanıcı oluşturulur.
     *
     * @param object $user Yeni kullanıcı nesnesi
     * @param bool $notify Onay bildirimi gösterilsin mi
     * @return bool
     * @throws moodle_exception Doğrulama başarısızsa
     */
public function user_signup($user, $notify = true) {
    global $CFG, $DB, $SESSION;
    require_once($CFG->dirroot . '/user/profile/lib.php');
    require_once($CFG->dirroot . '/user/lib.php');
    require_once($CFG->dirroot . '/login/lib.php');

    $role = $user->user_role ?? 'student';

    // Harici API ile doğrulama yap (Sadece öğrenciler için)
    if ($role === 'student') {
        try {
            $validation = $this->validate_student($user);
            if (empty($validation) || empty($validation->valid)) {
                $apiurl = get_config('auth_gt4t_validator', 'apiurl');
                if (strpos($apiurl, 'example.com') !== false) {
                    // Mock a successful validation for placeholder/development environments.
                    $validation = (object)['valid' => true];
                } else {
                    $msg = !empty($validation->message) ? $validation->message : 'Unknown validation error';
                    throw new moodle_exception('invalidstudent', 'auth_gt4t_validator', '', $msg);
                }
            }
        } catch (moodle_exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            $apiurl = get_config('auth_gt4t_validator', 'apiurl');
            if (strpos($apiurl, 'example.com') !== false) {
                // Mock a successful validation for placeholder/development environments.
                $validation = (object)['valid' => true];
            } else {
                throw new moodle_exception('apierror', 'auth_gt4t_validator', '', $e->getMessage());
            }
        }
    }

    $user->confirmed = 1;

    $plainpassword = $user->password;
    $user->password = hash_internal_user_password($user->password);
    if (empty($user->calendartype)) {
        $user->calendartype = $CFG->calendartype;
    }

    $user->id = user_create_user($user, false, false);
    user_add_password_history($user->id, $plainpassword);
    profile_save_data($user);

    // Save user role in preferences
    set_user_preference('gt4t_user_role', $role, $user->id);

    if (!empty($SESSION->wantsurl)) {
        set_user_preference('auth_gt4t_validator_wantsurl', $SESSION->wantsurl, $user);
    }

    \core\event\user_created::create_from_userid($user->id)->trigger();

    $user = get_complete_user_data('id', $user->id);
    complete_user_login($user);
    $urltogo = core_login_get_return_url();
    redirect($urltogo);
}

    /**
     * Onay (confirmation) destekliyor mu?
     *
     * @return bool
     */
    public function can_confirm() {
        return true;
    }

    /**
     * Yeni kullanıcıyı onaylama.
     *
     * @param string $username
     * @param string $confirmsecret
     * @return int
     */
    public function user_confirm($username, $confirmsecret) {
        global $DB;
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;
            } else if ($user->secret === $confirmsecret && $user->confirmed) {
                unset_user_preference('auth_gt4t_validator_wantsurl', $user);
                return AUTH_CONFIRM_ALREADY;
            }
        }

        return AUTH_CONFIRM_ERROR;
    }

    /**
     * Dahili parola kullanılıyor mu?
     *
     * @return bool
     */
    public function prevent_local_passwords() {
        return false;
    }

    /**
     * Bu plugin dahili (internal) mi?
     *
     * @return bool
     */
    public function is_internal() {
        return true;
    }

    /**
     * Parola değiştirilebilir mi?
     *
     * @return bool
     */
    public function can_change_password() {
        return true;
    }

    /**
     * Parola değiştirme URL'si.
     *
     * @return moodle_url|null
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Parola sıfırlama destekliyor mu?
     *
     * @return bool
     */
    public function can_reset_password() {
        return true;
    }

    /**
     * Manuel olarak ayarlanabilir mi?
     *
     * @return bool
     */
    public function can_be_manually_set() {
        return true;
    }

    /**
     * Plugin açıklaması (Manage Authentication sayfasında görünür).
     *
     * @return string
     */
    public function get_description() {
        return get_string('auth_gt4t_validatordescription', 'auth_gt4t_validator');
    }

    /**
     * Harici API'ye öğrenci bilgilerini gönderir ve doğrulama yapar.
     *
     * @param object $user Kullanıcı nesnesi
     * @return object|null JSON yanıtı (valid, message alanları beklenir)
     */
    private function validate_student($user) {
        $apiurl = get_config('auth_gt4t_validator', 'apiurl');
        $apikey = get_config('auth_gt4t_validator', 'apikey');

        if (empty($apiurl)) {
            throw new moodle_exception('noapiurl', 'auth_gt4t_validator');
        }

        $curl = new \curl();
        $headers = ['Content-Type: application/json'];

        if (!empty($apikey)) {
            $headers[] = 'X-API-Key: ' . $apikey;
        }

        $curl->setHeader($headers);

        $data = json_encode([
            'student_no' => $user->idnumber ?? '',
            'email'      => $user->email,
            'firstname'  => $user->firstname,
            'lastname'   => $user->lastname,
        ]);

        $result = $curl->post($apiurl, $data);

        if ($curl->get_errno()) {
            throw new moodle_exception('apierror', 'auth_gt4t_validator', '', $curl->error);
        }

        return json_decode($result);
    }
}
