<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
require_once($CFG->dirroot . '/user/lib.php');

global $DB, $CFG;

$email = 'admin@demo.com';
$username = 'admin';
$password = 'password';
$firstname = 'Admin';
$lastname = 'User';

$existing = $DB->get_record('user', ['username' => $username]);
if ($existing) {
    echo "Admin user '{$username}' already exists (id={$existing->id}). Updating password...\n";
    $existing->password = hash_internal_user_password($password);
    $existing->timemodified = time();
    $DB->update_record('user', $existing);
    echo "Password updated.\n";
    exit(0);
}

$user = new stdClass();
$user->auth = 'manual';
$user->confirmed = 1;
$user->mnethostid = $CFG->mnet_localhost_id;
$user->username = $username;
$user->password = hash_internal_user_password($password);
$user->firstname = $firstname;
$user->lastname = $lastname;
$user->email = $email;
$user->lang = 'en';
$user->timecreated = time();
$user->timemodified = time();

$userid = user_create_user($user);
echo "Admin user '{$username}' created with id={$userid}\n";

if (!empty($CFG->siteadmins)) {
    $admins = explode(',', $CFG->siteadmins);
    if (!in_array($userid, $admins)) {
        $admins[] = $userid;
        set_config('siteadmins', implode(',', $admins));
        echo "User added to siteadmins.\n";
    }
}
