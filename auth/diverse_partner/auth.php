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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

/**
 * DIVERSE partner login check.
 *
 * On the login page a visitor chooses a partner organisation (tool_mutenancy tenant) in the "Select Partner" menu.
 * tool_mutenancy itself lets any account log in there and then moves it to its own partner. With this plugin enabled,
 * an account can only log in on the login page of its own partner: a partner's account on the shared DIVERSE login
 * page or on another partner's page gets the usual "Invalid login" message, as for a wrong password, so the page does
 * not tell that the account exists elsewhere.
 *
 * The plugin never authenticates anybody itself (accounts keep their own authentication method). It only checks
 * logins through the login form; the mobile app and web services are not affected. Site administrators can log in
 * on every page, so that nobody is locked out of the site. Disabling the plugin restores tool_mutenancy's behaviour.
 *
 * @package    auth_diverse_partner
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_diverse_partner extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'diverse_partner';
        $this->config = get_config('auth_diverse_partner');
    }

    /**
     * Never authenticates anybody: accounts use their own authentication method.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function user_login($username, $password) {
        return false;
    }

    /**
     * Refuse a successful login through the login form when the partner chosen on the page is not the account's own.
     *
     * Moodle calls this for every enabled authentication method after the password was accepted. Emptying the user's id
     * makes Moodle refuse the login as an unknown user ("Invalid login").
     *
     * @param stdClass $user
     * @param string $username
     * @param string $password
     */
    public function user_authenticated_hook(&$user, $username, $password) {
        global $SCRIPT;
        if ($SCRIPT !== '/login/index.php' || empty($user->id)) {
            return;
        }
        if (!function_exists('mutenancy_is_active') || !mutenancy_is_active()) {
            return;
        }
        $chosen = (int)\tool_mutenancy\local\tenancy::get_current_tenantid();
        if (!self::login_allowed($user, $chosen)) {
            $user->id = 0;
        }
    }

    /**
     * Whether an account may log in on the login page of the chosen partner.
     *
     * @param stdClass $user Account record (id and tenantid).
     * @param int $chosentenantid Partner chosen on the login page, 0 for the shared DIVERSE login page.
     * @return bool
     */
    public static function login_allowed(stdClass $user, int $chosentenantid): bool {
        if (is_siteadmin($user->id)) {
            return true;
        }
        return (int)($user->tenantid ?? 0) === $chosentenantid;
    }

    /**
     * Not an internal (password storing) method.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Passwords are not kept by this plugin.
     *
     * @return bool
     */
    public function prevent_local_passwords() {
        return false;
    }

    /**
     * No password changes.
     *
     * @return bool
     */
    public function can_change_password() {
        return false;
    }

    /**
     * No self-registration through this plugin.
     *
     * @return bool
     */
    public function can_signup() {
        return false;
    }
}
