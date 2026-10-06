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

namespace auth_diverse_partner;

/**
 * Tests for the DIVERSE partner login check.
 *
 * @package    auth_diverse_partner
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \auth_plugin_diverse_partner
 */
final class auth_test extends \advanced_testcase {

    /** @var \stdClass First partner. */
    private $beykent;

    /** @var \stdClass Second partner. */
    private $rosenheim;

    protected function setUp(): void {
        global $CFG;
        parent::setUp();
        require_once($CFG->dirroot . '/auth/diverse_partner/auth.php');
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');
        $this->beykent = $generator->create_tenant(['idnumber' => 'beykent']);
        $this->rosenheim = $generator->create_tenant(['idnumber' => 'rosenheim']);
    }

    /**
     * A partner's account logs in only on its own partner's page; site administrators everywhere.
     */
    public function test_login_allowed(): void {
        $student = $this->getDataGenerator()->create_user(['tenantid' => $this->rosenheim->id]);
        $global = $this->getDataGenerator()->create_user();

        $this->assertTrue(\auth_plugin_diverse_partner::login_allowed($student, (int)$this->rosenheim->id));
        $this->assertFalse(\auth_plugin_diverse_partner::login_allowed($student, (int)$this->beykent->id));
        $this->assertFalse(\auth_plugin_diverse_partner::login_allowed($student, 0));

        $this->assertTrue(\auth_plugin_diverse_partner::login_allowed($global, 0));
        $this->assertFalse(\auth_plugin_diverse_partner::login_allowed($global, (int)$this->beykent->id));

        $this->assertTrue(\auth_plugin_diverse_partner::login_allowed(get_admin(), (int)$this->beykent->id));
        $this->assertTrue(\auth_plugin_diverse_partner::login_allowed(get_admin(), 0));
    }

    /**
     * Through the login form, a login on another partner's page fails like an unknown account; other ways of logging in
     * are not checked.
     */
    public function test_login_form(): void {
        set_config('auth', 'manual,diverse_partner');
        $this->getDataGenerator()->create_user(['username' => 'rosenheim.student1', 'password' => 'Secret-123',
            'tenantid' => $this->rosenheim->id]);

        $this->assertFalse($this->login('/login/index.php', (int)$this->beykent->id, $reason));
        $this->assertSame(AUTH_LOGIN_NOUSER, $reason);
        $this->assertFalse($this->login('/login/index.php', 0, $reason));

        $user = $this->login('/login/index.php', (int)$this->rosenheim->id, $reason);
        $this->assertSame('rosenheim.student1', $user->username);
        $this->assertSame(AUTH_LOGIN_OK, $reason);

        // Not the login form (e.g. the mobile app's token service).
        $this->assertNotFalse($this->login('/login/token.php', (int)$this->beykent->id, $reason));
    }

    /**
     * Log in as rosenheim.student1 from the given script with the given partner chosen.
     *
     * @param string $script
     * @param int $tenantid
     * @param int|null $reason
     * @return \stdClass|false
     */
    private function login(string $script, int $tenantid, &$reason) {
        global $SCRIPT;
        $oldscript = $SCRIPT;
        $SCRIPT = $script;
        \tool_mutenancy\local\tenancy::force_current_tenantid($tenantid);
        try {
            return authenticate_user_login('rosenheim.student1', 'Secret-123', true, $reason);
        } finally {
            \tool_mutenancy\local\tenancy::unforce_current_tenantid();
            $SCRIPT = $oldscript;
        }
    }
}
