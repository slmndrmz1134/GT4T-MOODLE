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
 * DIVERSE demo accounts and courses: one uniform set of accounts for every partner organisation and the
 * courses they teach together (approved by the project owner, October 2026).
 *
 * Every partner (tool_mutenancy tenant) gets five accounts named after their role, with no personal names:
 * <partner>.admin (partner manager), <partner>.teacher and <partner>.student1..3, e-mail addresses on the
 * partner's own domain. E-mail sending is switched off for these accounts, so no notification reaches the
 * real domains. Teachers and students are kept in one cohort per partner and role ("SAMK Teachers",
 * "SAMK Students"); courses enrol these cohorts, so a new account only has to be added to its cohort.
 *
 * - Each partner's own courses (in its course category) enrol its teachers and students.
 * - Shared courses (in "Shared Courses", outside the partners) enrol the teachers and students of several
 *   partners, each partner in its own group.
 * - Demo accounts take part in these courses only through their role cohorts: other enrolments and course roles
 *   they had from the former demo data are removed.
 * - The former demo accounts are renamed into this scheme, keeping their history; test
 *   accounts and accounts with personal names are deleted. The Algebra partner left the project: its
 *   accounts, courses, course category and cohorts are deleted.
 *
 * Passwords are never stored in this file. They are asked for when the script runs (or read from the
 * DIVERSE_USER_PASSWORD and DIVERSE_ADMIN_PASSWORD environment variables). New and renamed accounts get the
 * account password; --reset-passwords also sets it on the existing accounts and sets the admin password.
 *
 * Run after setup/diverse_setup.php, which creates the partner organisations. It is idempotent: it only adds
 * what is missing, so it is safe to run again. Users' languages are not changed (new accounts start in their
 * partner's language).
 *
 * Usage:
 *   php setup/diverse_demo_accounts.php [--dry-run] [--reset-passwords]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/resourcelib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/group/lib.php');
require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

[$options, $unrecognised] = cli_get_params(
    ['help' => false, 'dry-run' => false, 'reset-passwords' => false],
    ['h' => 'help', 'n' => 'dry-run']
);

if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}

if ($options['help']) {
    cli_writeln("DIVERSE demo accounts and courses. Applies only what is missing; safe to run again.

Options:
  -n, --dry-run          Show what would change without changing anything.
      --reset-passwords  Also set the account password on the existing demo accounts, and the admin password.
  -h, --help             Print this help.

Passwords are asked for while the script runs, or read from DIVERSE_USER_PASSWORD and
DIVERSE_ADMIN_PASSWORD.

Example:
  php setup/diverse_demo_accounts.php --dry-run
  php setup/diverse_demo_accounts.php --reset-passwords");
    exit(0);
}

$dryrun = (bool) $options['dry-run'];

// Partner idnumber => [name in account names, e-mail domain, language of new accounts].
$partners = [
    'satakunta' => ['SAMK', 'samk.fi', 'fi'],
    'beykent' => ['Beykent', 'beykent.edu.tr', 'tr'],
    'rosenheim' => ['Rosenheim', 'th-rosenheim.de', 'de'],
    'windesheim' => ['Windesheim', 'windesheim.nl', 'nl'],
    'griffith' => ['Griffith', 'griffith.ie', 'en'],
    'furthr' => ['Furthr', 'furthr.ie', 'en'],
    'ikigaia' => ['Ikigaia', 'ikigaia.fi', 'fi'],
    'wildcampus' => ['Wild Campus', 'wildcampus.de', 'de'],
    'gt4t' => ['GT4T', 'gt4t.eu', 'en'],
];

// Account => last name; the first name is the partner's name ("Rosenheim Teacher").
$slots = ['admin' => 'Admin', 'teacher' => 'Teacher', 'student1' => 'Student 1', 'student2' => 'Student 2',
    'student3' => 'Student 3'];

// Former demo accounts renamed into the scheme: old username => [its e-mail, new username].
$renames = [
    'ali_yilmaz' => ['ali@beykent.edu.tr', 'beykent.admin'],
    'teacher1' => ['teacher@beykent.edu.tr', 'beykent.teacher'],
    'student1' => ['student1@beykent.edu.tr', 'beykent.student1'],
    'thomas_muller' => ['thomas.muller@rosenheim.de', 'rosenheim.admin'],
    'teacher3' => ['teacher3@rosenheim.de', 'rosenheim.teacher'],
    'student3' => ['student3@rosenheim.de', 'rosenheim.student1'],
    'student4' => ['student@rosenheim.de', 'rosenheim.student2'],
];

// Test accounts and accounts with personal names to delete: username => its e-mail.
$deletes = [
    'fatih' => 'fatihkarakus563@gmail.com',
    'mehmet' => 'test@gmail.com',
    'mustafa' => 'edi@gm.com',
    'test2122' => 'test2122@gmail.com',
    'samet' => 'mehmetcansiz2626@gmail.com',
    'tool_generator_000001' => 'tool_generator_000001@example.com',
    'luka_modric' => 'modric@algebra.edu.hr',
    'teacher2' => 'teacher2@algebra.edu.hr',
    'student2' => 'student2@algebra.edu.hr',
];

// Partner that left the project: its accounts, courses, course category and cohorts are deleted.
$removedpartner = 'algebra';

// Older cohort idnumbers reused for the role cohorts: old idnumber => new idnumber.
$cohortrenames = ['beykent_teacher' => 'beykent_teachers', 'beykent_student' => 'beykent_students'];

// Own courses of partners that have none yet: partner => [shortname, full name, summary, section names].
$owncourses = [
    'satakunta' => ['SAMK-ITI-101', 'Intelligent Industry Basics',
        'Sensors, data and automation in today\'s factories, with examples from Satakunta\'s industry.',
        ['Smart factories', 'Industrial data', 'Automation in practice', 'Final project']],
    'windesheim' => ['WIN-CEB-101', 'Circular Economy in Business',
        'How companies design products, supply chains and business models that keep materials in use.',
        ['Linear and circular', 'Circular design', 'Circular business models', 'Case study']],
    'griffith' => ['GRI-DMI-101', 'Digital Media and Innovation',
        'Digital media, content strategy and innovation for organisations.',
        ['Digital media today', 'Content strategy', 'Innovation methods', 'Portfolio']],
];

// Shared courses: shortname => [full name, summary, section names, teaching partners, partners whose students
// take part]. Existing courses keep their name and content; only the enrolments are added.
$all = array_keys($partners);
$sharedcourses = [
    'CTP001' => ['Common Training Program', 'The common training programme of the GT4T partners.',
        ['Introduction', 'Working together', 'Tools', 'Reflection'], ['gt4t'], $all],
    'IBM401' => ['International Business Management', 'Managing business across countries and cultures.',
        ['Global markets', 'Cross-cultural management', 'Strategy', 'Case study'], ['griffith', 'beykent'],
        ['beykent', 'griffith', 'windesheim']],
    'GT4T-101' => ['Green Transition Fundamentals',
        'Climate goals, the European Green Deal and what the green transition means for companies and regions.',
        ['Why a green transition', 'Energy and resources', 'Green skills', 'Group assignment'],
        ['satakunta', 'windesheim'], $all],
    'GT4T-201' => ['Digital Transformation and Industry 4.0',
        'Digital technologies that change production and services, and how to introduce them.',
        ['Industry 4.0', 'Data and connectivity', 'Automation and AI', 'Transformation roadmap'],
        ['rosenheim', 'beykent'], ['rosenheim', 'beykent', 'satakunta', 'windesheim']],
    'GT4T-301' => ['Challenge-Based Learning Workshop',
        'Student teams from several countries solve a real challenge given by a company.',
        ['The challenge', 'Investigate', 'Act', 'Pitch and reflect'], ['beykent', 'griffith', 'furthr'],
        ['beykent', 'griffith', 'satakunta']],
    'GT4T-401' => ['Sustainable Entrepreneurship',
        'From a sustainable idea to a business: opportunities, business models and funding.',
        ['Opportunities', 'Business model', 'Funding and growth', 'Pitch'], ['ikigaia', 'wildcampus', 'furthr'],
        ['griffith', 'windesheim', 'rosenheim']],
];
$sharedcategoryname = 'Shared Courses';

/**
 * Print one step result.
 *
 * @param string $status OK (already fine), SET (changed), WOULD (dry run), SKIP (left alone), WARN, DEL (deleted)
 * @param string $message
 */
function diverse_demo_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

/**
 * A password from the environment or typed in the terminal (not shown), asked once.
 *
 * @param string $label What the password is for.
 * @param string $env Environment variable that can hold it.
 * @return string
 */
function diverse_demo_password(string $label, string $env): string {
    static $cache = [];
    if (isset($cache[$env])) {
        return $cache[$env];
    }
    $value = getenv($env);
    if ($value === false || $value === '') {
        if (!function_exists('stream_isatty') || !stream_isatty(STDIN)) {
            cli_error("Set $env or run the script in a terminal to type the $label.");
        }
        cli_write(ucfirst($label) . ': ');
        system('stty -echo');
        $value = trim((string)fgets(STDIN));
        system('stty echo');
        cli_writeln('');
    }
    if ($value === '') {
        cli_error("The $label must not be empty.");
    }
    return $cache[$env] = $value;
}

/**
 * Set a user's password.
 *
 * @param stdClass $user
 * @param string $password
 */
function diverse_demo_set_password(stdClass $user, string $password): void {
    $user = get_complete_user_data('id', $user->id);
    update_internal_user_password($user, $password);
}

cli_heading('DIVERSE demo accounts and courses' . ($dryrun ? ' (dry run, nothing is changed)' : ''));

if (!function_exists('mutenancy_is_active') || !mutenancy_is_active()) {
    cli_error('Partner organisations (tool_mutenancy) are not active.');
}
\core\session\manager::set_user(get_admin());

if (!$dryrun) {
    diverse_demo_password('account password', 'DIVERSE_USER_PASSWORD');
    if ($options['reset-passwords']) {
        diverse_demo_password('admin password', 'DIVERSE_ADMIN_PASSWORD');
    }
}

$syscontext = context_system::instance();
$roleids = [
    'teacher' => (int)$DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST),
    'student' => (int)$DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST),
];

// 1. Tenants.
$tenants = [];
foreach ($partners as $idnumber => [$label]) {
    $tenant = \tool_mutenancy\local\tenant::fetch_by_idnumber($idnumber);
    if (!$tenant || $tenant->archived) {
        diverse_demo_report('WARN', "Partner \"$idnumber\" is missing or archived: run setup/diverse_setup.php first. Skipped.");
        continue;
    }
    $tenants[$idnumber] = $tenant;
}

// 2. Test accounts and accounts with personal names.
foreach ($deletes as $username => $email) {
    $user = $DB->get_record('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);
    if (!$user) {
        diverse_demo_report('OK', "Account \"$username\" does not exist.");
    } else if (core_text::strtolower($user->email) !== $email || is_siteadmin($user)) {
        diverse_demo_report('SKIP', "Account \"$username\" is not the expected one ($user->email" .
            (is_siteadmin($user) ? ', site administrator' : '') . '): left as is.');
    } else if ($dryrun) {
        diverse_demo_report('WOULD', "Delete account \"$username\" ($email).");
    } else {
        delete_user($user);
        diverse_demo_report('DEL', "Deleted account \"$username\" ($email).");
    }
}

// 3. The partner that left: accounts, tenant, course category with its courses, cohorts.
$removed = $DB->get_record('tool_mutenancy_tenant', ['idnumber' => $removedpartner]);
if (!$removed) {
    diverse_demo_report('OK', "Partner \"$removedpartner\" does not exist.");
} else {
    $members = $DB->get_records('user', ['tenantid' => $removed->id, 'deleted' => 0], 'id', 'id, username, email');
    $category = core_course_category::get($removed->categoryid, IGNORE_MISSING, true);
    $courses = $category ? $category->get_courses(['recursive' => true]) : [];
    $cohortids = array_filter([$removed->cohortid, $removed->assoccohortid]);
    $cohortids = array_merge($cohortids, array_keys($DB->get_records_select('cohort',
        $DB->sql_like('idnumber', ':prefix'), ['prefix' => $removedpartner . '\_%'], '', 'id')));
    $summary = count($members) . ' accounts, ' . count($courses) . ' courses, course category "' .
        ($category ? $category->name : '-') . '", ' . count(array_unique($cohortids)) . ' cohorts';
    if ($dryrun) {
        diverse_demo_report('WOULD', "Delete partner \"$removed->name\" with $summary.");
    } else {
        foreach ($members as $member) {
            delete_user($DB->get_record('user', ['id' => $member->id]));
        }
        if (!$removed->archived) {
            \tool_mutenancy\local\tenant::archive($removed->id);
        }
        \tool_mutenancy\local\tenant::delete($removed->id, false);
        if ($category) {
            $category->delete_full(false);
        }
        foreach (array_unique($cohortids) as $cohortid) {
            if ($cohort = $DB->get_record('cohort', ['id' => $cohortid])) {
                cohort_delete_cohort($cohort);
            }
        }
        diverse_demo_report('DEL', "Deleted partner \"$removed->name\" with $summary.");
    }
}

// 4. Role cohorts: "<Partner> Teachers" and "<Partner> Students", in the system context so that shared courses
// outside the partners' categories can enrol them.
$cohorts = [];
foreach ($tenants as $idnumber => $tenant) {
    $label = $partners[$idnumber][0];
    foreach (['teacher' => 'Teachers', 'student' => 'Students'] as $role => $plural) {
        $cohortidnumber = "{$idnumber}_" . strtolower($plural);
        $name = "$label $plural";
        $cohort = $DB->get_record('cohort', ['idnumber' => $cohortidnumber]);
        $old = array_search($cohortidnumber, $cohortrenames, true);
        if (!$cohort && $old !== false) {
            $cohort = $DB->get_record('cohort', ['idnumber' => $old]);
        }
        if ($cohort && ($cohort->idnumber !== $cohortidnumber || $cohort->name !== $name)) {
            if ($dryrun) {
                diverse_demo_report('WOULD', "Rename cohort \"$cohort->name\" to \"$name\" ($cohortidnumber).");
            } else {
                $cohort->idnumber = $cohortidnumber;
                $cohort->name = $name;
                cohort_update_cohort($cohort);
                diverse_demo_report('SET', "Renamed cohort to \"$name\" ($cohortidnumber).");
            }
        } else if ($cohort) {
            diverse_demo_report('OK', "Cohort \"$name\" exists.");
        } else if ($dryrun) {
            diverse_demo_report('WOULD', "Create cohort \"$name\" ($cohortidnumber).");
            $cohort = (object)['name' => $name];
        } else {
            $cohort = (object)['contextid' => $syscontext->id, 'name' => $name, 'idnumber' => $cohortidnumber,
                'description' => '', 'visible' => 1];
            $cohort->id = cohort_add_cohort($cohort);
            diverse_demo_report('SET', "Created cohort \"$name\" ($cohortidnumber).");
        }
        $cohorts[$idnumber][$role] = $cohort ?: null;
    }
}

// 5. Accounts.
$accounts = [];
foreach ($tenants as $idnumber => $tenant) {
    [$label, $domain, $lang] = $partners[$idnumber];
    if (!get_string_manager()->translation_exists($lang)) {
        $lang = $CFG->lang;
    }
    foreach ($slots as $slot => $lastname) {
        $username = "$idnumber.$slot";
        $wanted = ['firstname' => $label, 'lastname' => $lastname, 'email' => "$username@$domain", 'auth' => 'manual',
            'emailstop' => 1, 'suspended' => 0];
        $user = $DB->get_record('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);
        $setpassword = (bool)$options['reset-passwords'];
        $action = null;
        if (!$user) {
            $old = array_search($username, array_map(fn($r) => $r[1], $renames), true);
            $olduser = $old === false ? null : $DB->get_record('user',
                ['username' => $old, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);
            if ($olduser && core_text::strtolower($olduser->email) === $renames[$old][0] && !is_siteadmin($olduser)) {
                $user = $olduser;
                $action = "renamed from \"$old\"";
                $setpassword = true;
            }
        }
        if (!$user) {
            if ($dryrun) {
                diverse_demo_report('WOULD', "Create account \"$username\" ($label $lastname, $wanted[email]).");
                $accounts[$idnumber][$slot] = null;
                continue;
            }
            // Created with a random password that meets the site's password policy; the chosen account password is
            // set right after (the owner chose simple demo passwords, which the policy would refuse).
            $new = (object)($wanted + ['username' => $username, 'password' => generate_password(20), 'confirmed' => 1,
                'mnethostid' => $CFG->mnet_localhost_id, 'lang' => $lang]);
            $user = $DB->get_record('user', ['id' => user_create_user($new, true)]);
            diverse_demo_report('SET', "Created account \"$username\" ($label $lastname).");
            $setpassword = true;
        } else {
            $changes = [];
            if ($user->username !== $username) {
                $changes['username'] = $username;
            }
            foreach ($wanted as $field => $value) {
                if ((string)$user->$field !== (string)$value) {
                    $changes[$field] = $value;
                }
            }
            if (!$changes) {
                diverse_demo_report('OK', "Account \"$username\" is up to date.");
            } else if ($dryrun) {
                diverse_demo_report('WOULD', "Update account \"$username\"" . ($action ? " ($action)" : '') . ': ' .
                    implode(', ', array_keys($changes)) . '.');
            } else {
                user_update_user((object)(['id' => $user->id] + $changes), false);
                $user = $DB->get_record('user', ['id' => $user->id]);
                diverse_demo_report('SET', "Updated account \"$username\"" . ($action ? " ($action)" : '') . ': ' .
                    implode(', ', array_keys($changes)) . '.');
            }
        }
        if (!$dryrun && $setpassword) {
            diverse_demo_set_password($user, diverse_demo_password('account password', 'DIVERSE_USER_PASSWORD'));
            diverse_demo_report('SET', "Password set for \"$username\".");
        } else if ($dryrun && $setpassword) {
            diverse_demo_report('WOULD', "Set the account password for \"$username\".");
        }

        // Partner membership, partner manager role and role cohort.
        if ($user->tenantid != $tenant->id) {
            if ($dryrun) {
                diverse_demo_report('WOULD', "Make \"$username\" a member of $tenant->name.");
            } else {
                $user = \tool_mutenancy\local\user::allocate($user->id, $tenant->id);
                diverse_demo_report('SET', "\"$username\" is now a member of $tenant->name.");
            }
        }
        if ($slot === 'admin') {
            if ($DB->record_exists('tool_mutenancy_manager', ['tenantid' => $tenant->id, 'userid' => $user->id])) {
                diverse_demo_report('OK', "\"$username\" manages $tenant->name.");
            } else if ($dryrun) {
                diverse_demo_report('WOULD', "Make \"$username\" a manager of $tenant->name.");
            } else {
                \tool_mutenancy\local\manager::add($tenant->id, $user->id);
                diverse_demo_report('SET', "\"$username\" now manages $tenant->name.");
            }
        } else {
            $cohort = $cohorts[$idnumber][$slot === 'teacher' ? 'teacher' : 'student'];
            if (!empty($cohort->id) && !$dryrun && !cohort_is_member($cohort->id, $user->id)) {
                cohort_add_member($cohort->id, $user->id);
                diverse_demo_report('SET', "\"$username\" added to cohort \"$cohort->name\".");
            }
        }
        $accounts[$idnumber][$slot] = $user;
    }
}

// 6. Admin password.
if ($options['reset-passwords']) {
    if ($dryrun) {
        diverse_demo_report('WOULD', 'Set the admin password.');
    } else {
        diverse_demo_set_password(get_admin(), diverse_demo_password('admin password', 'DIVERSE_ADMIN_PASSWORD'));
        diverse_demo_report('SET', 'Admin password set.');
    }
}

/**
 * The course with this shortname, created with sections, a welcome page and a forum if it is missing.
 *
 * @param string $shortname
 * @param array $data [full name, summary, section names]
 * @param int $categoryid
 * @param string $welcome Extra sentence for the welcome page.
 * @param bool $dryrun
 * @return stdClass|null The course, null in a dry run when it would be created.
 */
function diverse_demo_course(string $shortname, array $data, int $categoryid, string $welcome, bool $dryrun): ?stdClass {
    global $DB;
    [$fullname, $summary, $sections] = $data;
    $course = $DB->get_record('course', ['shortname' => $shortname]);
    if ($course) {
        diverse_demo_report('OK', "Course $shortname exists.");
        return $course;
    }
    if ($dryrun) {
        diverse_demo_report('WOULD', "Create course $shortname \"$fullname\".");
        return null;
    }
    $course = create_course((object)['fullname' => $fullname, 'shortname' => $shortname, 'category' => $categoryid,
        'summary' => "<p>$summary</p>", 'summaryformat' => FORMAT_HTML, 'format' => 'topics',
        'numsections' => count($sections), 'startdate' => usergetmidnight(time()), 'visible' => 1]);
    foreach (array_values($sections) as $i => $name) {
        course_update_section($course, get_fast_modinfo($course)->get_section_info($i + 1), ['name' => $name]);
    }
    $intro = ['text' => '', 'format' => FORMAT_HTML, 'itemid' => 0];
    create_module((object)['modulename' => 'page', 'course' => $course->id, 'section' => 0, 'visible' => 1,
        'name' => 'Welcome', 'introeditor' => $intro, 'display' => RESOURCELIB_DISPLAY_AUTO, 'printintro' => 0,
        'printlastmodified' => 0, 'contentformat' => FORMAT_HTML,
        'content' => "<h3>Welcome to $fullname</h3><p>$summary</p><p>$welcome</p>"]);
    create_module((object)['modulename' => 'forum', 'course' => $course->id, 'section' => 0, 'visible' => 1,
        'name' => 'Course discussion', 'type' => 'general', 'cmidnumber' => '', 'grade_forum' => 0,
        'introeditor' => ['text' => '<p>Questions and ideas about the course.</p>', 'format' => FORMAT_HTML, 'itemid' => 0]]);
    diverse_demo_report('SET', "Created course $shortname \"$fullname\".");
    return $course;
}

/**
 * Enrol a role cohort in a course (optionally into a group), unless it is enrolled already.
 *
 * @param stdClass $course
 * @param stdClass|null $cohort
 * @param int $roleid
 * @param int $groupid 0 for no group.
 * @param bool $dryrun
 */
function diverse_demo_enrol_cohort(stdClass $course, ?stdClass $cohort, int $roleid, int $groupid, bool $dryrun): void {
    global $DB;
    if (!$cohort) {
        return;
    }
    if (empty($cohort->id)) {
        diverse_demo_report('WOULD', "Enrol \"$cohort->name\" in $course->shortname.");
    } else if ($DB->record_exists('enrol', ['courseid' => $course->id, 'enrol' => 'cohort', 'customint1' => $cohort->id,
            'roleid' => $roleid])) {
        diverse_demo_report('OK', "$course->shortname enrols \"$cohort->name\".");
    } else if ($dryrun) {
        diverse_demo_report('WOULD', "Enrol \"$cohort->name\" in $course->shortname.");
    } else {
        enrol_get_plugin('cohort')->add_instance($course, ['customint1' => $cohort->id, 'roleid' => $roleid,
            'customint2' => $groupid]);
        diverse_demo_report('SET', "Enrolled \"$cohort->name\" in $course->shortname.");
    }
}

/**
 * Remove enrolments of whole partners (their tenant cohort, which also holds managers and teachers) from a course:
 * the role cohorts replace them.
 *
 * @param stdClass $course
 * @param bool $dryrun
 */
function diverse_demo_remove_tenant_cohorts(stdClass $course, bool $dryrun): void {
    global $DB;
    $instances = $DB->get_records_sql("SELECT e.*, c.name AS cohortname
                                         FROM {enrol} e
                                         JOIN {cohort} c ON c.id = e.customint1
                                        WHERE e.courseid = ? AND e.enrol = 'cohort' AND c.component = 'tool_mutenancy'",
        [$course->id]);
    foreach ($instances as $instance) {
        if ($dryrun) {
            diverse_demo_report('WOULD', "Remove the enrolment of all \"$instance->cohortname\" from $course->shortname.");
        } else {
            enrol_get_plugin('cohort')->delete_instance($instance);
            diverse_demo_report('DEL', "Removed the enrolment of all \"$instance->cohortname\" from $course->shortname.");
        }
    }
}

/**
 * Remove a course's other enrolments and roles of the demo accounts (left from the former demo data), so that they
 * take part only through their role cohorts.
 *
 * @param stdClass $course
 * @param int[] $userids Demo accounts.
 * @param bool $dryrun
 */
function diverse_demo_tidy_course(stdClass $course, array $userids, bool $dryrun): void {
    global $DB;
    if (!$userids) {
        return;
    }
    $context = context_course::instance($course->id);
    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
    $params['courseid'] = $course->id;
    $enrolments = $DB->get_records_sql("SELECT ue.id, ue.userid, e.id AS enrolid, e.enrol, u.username
                                          FROM {user_enrolments} ue
                                          JOIN {enrol} e ON e.id = ue.enrolid
                                          JOIN {user} u ON u.id = ue.userid
                                         WHERE e.courseid = :courseid AND e.enrol <> 'cohort' AND ue.userid $insql",
        $params);
    foreach ($enrolments as $ue) {
        if ($dryrun) {
            diverse_demo_report('WOULD', "Remove the $ue->enrol enrolment of \"$ue->username\" from $course->shortname.");
        } else {
            $instance = $DB->get_record('enrol', ['id' => $ue->enrolid]);
            enrol_get_plugin($ue->enrol)->unenrol_user($instance, $ue->userid);
            diverse_demo_report('DEL', "Removed the $ue->enrol enrolment of \"$ue->username\" from $course->shortname.");
        }
    }
    $params['contextid'] = $context->id;
    $roles = $DB->get_records_sql("SELECT ra.id, ra.userid, ra.roleid, r.shortname, u.username
                                     FROM {role_assignments} ra
                                     JOIN {role} r ON r.id = ra.roleid
                                     JOIN {user} u ON u.id = ra.userid
                                    WHERE ra.contextid = :contextid AND ra.component = '' AND ra.userid $insql",
        $params);
    foreach ($roles as $ra) {
        if ($dryrun) {
            diverse_demo_report('WOULD', "Remove the $ra->shortname role of \"$ra->username\" in $course->shortname.");
        } else {
            role_unassign($ra->roleid, $ra->userid, $context->id);
            diverse_demo_report('DEL', "Removed the $ra->shortname role of \"$ra->username\" in $course->shortname.");
        }
    }
}

$demouserids = [];
foreach ($accounts as $partneraccounts) {
    foreach ($partneraccounts as $account) {
        if ($account) {
            $demouserids[] = (int)$account->id;
        }
    }
}

// 7. Each partner's own courses.
foreach ($tenants as $idnumber => $tenant) {
    $label = $partners[$idnumber][0];
    if (isset($owncourses[$idnumber])) {
        [$shortname, $fullname, $summary, $sections] = $owncourses[$idnumber];
        diverse_demo_course($shortname, [$fullname, $summary, $sections], (int)$tenant->categoryid,
            "This course is taught by $label.", $dryrun);
    }
    $category = core_course_category::get($tenant->categoryid, IGNORE_MISSING, true);
    foreach ($category ? $category->get_courses(['recursive' => true]) : [] as $course) {
        $course = get_course($course->id);
        diverse_demo_remove_tenant_cohorts($course, $dryrun);
        diverse_demo_tidy_course($course, $demouserids, $dryrun);
        diverse_demo_enrol_cohort($course, $cohorts[$idnumber]['teacher'], $roleids['teacher'], 0, $dryrun);
        diverse_demo_enrol_cohort($course, $cohorts[$idnumber]['student'], $roleids['student'], 0, $dryrun);
        if (!$dryrun) {
            enrol_cohort_sync(new null_progress_trace(), $course->id);
        }
    }
}

// 8. Shared courses, each partner in its own group.
$sharedcategory = $DB->get_record('course_categories', ['name' => $sharedcategoryname, 'parent' => 0]);
if (!$sharedcategory && $dryrun) {
    diverse_demo_report('WOULD', "Create course category \"$sharedcategoryname\".");
} else if (!$sharedcategory) {
    $sharedcategory = core_course_category::create(['name' => $sharedcategoryname, 'idnumber' => 'shared'])->get_db_record();
    diverse_demo_report('SET', "Created course category \"$sharedcategoryname\".");
}
foreach ($sharedcourses as $shortname => [$fullname, $summary, $sections, $teaching, $learning]) {
    $teaching = array_values(array_intersect($teaching, array_keys($tenants)));
    $learning = array_values(array_intersect($learning, array_keys($tenants)));
    $names = array_map(fn($p) => $partners[$p][0], $teaching);
    $course = $sharedcategory ? diverse_demo_course($shortname, [$fullname, $summary, $sections], (int)$sharedcategory->id,
        'It is taught together by ' . implode(', ', $names) . '.', $dryrun) : null;
    if (!$course) {
        continue;
    }
    diverse_demo_remove_tenant_cohorts($course, $dryrun);
    diverse_demo_tidy_course($course, $demouserids, $dryrun);
    foreach (array_unique(array_merge($teaching, $learning)) as $idnumber) {
        $label = $partners[$idnumber][0];
        $group = groups_get_group_by_idnumber($course->id, "partner-$idnumber");
        $groupid = $group ? (int)$group->id : 0;
        if (!$groupid && $dryrun) {
            diverse_demo_report('WOULD', "Create group \"$label\" in $shortname.");
        } else if (!$groupid) {
            $groupid = groups_create_group((object)['courseid' => $course->id, 'name' => $label,
                'idnumber' => "partner-$idnumber"]);
            diverse_demo_report('SET', "Created group \"$label\" in $shortname.");
        }
        if (in_array($idnumber, $teaching, true)) {
            diverse_demo_enrol_cohort($course, $cohorts[$idnumber]['teacher'], $roleids['teacher'], $groupid, $dryrun);
        }
        if (in_array($idnumber, $learning, true)) {
            diverse_demo_enrol_cohort($course, $cohorts[$idnumber]['student'], $roleids['student'], $groupid, $dryrun);
        }
    }
    if (!$dryrun) {
        enrol_cohort_sync(new null_progress_trace(), $course->id);
    }
}

cli_writeln('');
cli_writeln($dryrun ? 'Dry run finished.' : 'DIVERSE demo accounts and courses finished. Accounts: ' .
    implode(', ', array_map(fn($p) => "$p.admin/.teacher/.student1-3", array_keys($tenants))) . '.');
