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
 * Example messages and notifications for the Beykent and Griffith demo accounts (platform presentation, October 2026).
 *
 * Creates short conversations between the demo accounts (the last message of each unread for its recipient) and a few
 * course notifications (forum post, graded assignment, live session), dated over the last hours, so that the message
 * drawer and the notification bell are not empty during a demo. No e-mail is sent (the demo accounts do not receive
 * e-mail). Everything created is remembered, and --delete removes it again.
 *
 * Usage:
 *   php setup/diverse_demo_messages.php [--dry-run]
 *   php setup/diverse_demo_messages.php --delete [--dry-run]
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options, $unrecognised] = cli_get_params(['help' => false, 'dry-run' => false, 'delete' => false],
    ['h' => 'help', 'n' => 'dry-run']);
if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode(PHP_EOL . '  ', $unrecognised)));
}
if ($options['help']) {
    cli_writeln("Example messages and notifications for the Beykent and Griffith demo accounts.

Options:
      --delete     Remove the messages and notifications this script created.
  -n, --dry-run    Show what would change without changing anything.
  -h, --help       Print this help.");
    exit(0);
}
$dryrun = (bool)$options['dry-run'];
$memory = 'demomessages';

/**
 * Print one step result.
 *
 * @param string $status OK, SET, WOULD, SKIP, WARN
 * @param string $message
 */
function diverse_messages_report(string $status, string $message): void {
    cli_writeln(str_pad('[' . $status . ']', 8) . $message);
}

cli_heading('DIVERSE demo messages' . ($options['delete'] ? ' (delete)' : '') . ($dryrun ? ' (dry run, nothing is changed)' : ''));

$saved = get_config('diverse_setup', $memory);

if ($options['delete']) {
    if ($saved === false) {
        diverse_messages_report('OK', 'Nothing to delete.');
        exit(0);
    }
    $created = json_decode($saved, true);
    if ($dryrun) {
        diverse_messages_report('WOULD', 'Delete ' . count($created['messages']) . ' messages and ' .
            count($created['notifications']) . ' notifications.');
        exit(0);
    }
    foreach ($created['messages'] as $id) {
        $DB->delete_records('message_user_actions', ['messageid' => $id]);
        $DB->delete_records('messages', ['id' => $id]);
    }
    foreach ($created['conversations'] as $id) {
        // Only conversations that have no other messages left.
        if (!$DB->record_exists('messages', ['conversationid' => $id])) {
            \core_message\api::delete_all_conversation_data($id);
        }
    }
    $DB->delete_records_list('message_popup_notifications', 'notificationid', $created['notifications'] ?: [0]);
    $DB->delete_records_list('notifications', 'id', $created['notifications'] ?: [0]);
    // Notification settings of the demo accounts as they were before.
    foreach ($created['users'] ?? [] as $userid => $before) {
        foreach ($before['preferences'] as $preference => $value) {
            if ($value === null) {
                unset_user_preference($preference, $userid);
            } else {
                set_user_preference($preference, $value, $userid);
            }
        }
        $DB->set_field('user', 'emailstop', $before['emailstop'], ['id' => $userid]);
    }
    unset_config($memory, 'diverse_setup');
    \cache_helper::purge_by_definition('core', 'message_time_last_message_between_users');
    diverse_messages_report('SET', 'Deleted ' . count($created['messages']) . ' messages and ' .
        count($created['notifications']) . ' notifications.');
    exit(0);
}

if ($saved !== false) {
    diverse_messages_report('OK', 'The demo messages already exist (use --delete first to create them again).');
    exit(0);
}

// Conversations: [user A, user B, [[0 = A / 1 = B, text, minutes ago], ...]]. The last message is left unread.
$conversations = [
    ['beykent.student1', 'beykent.teacher', [
        [0, 'Hocam merhaba, GT4T-301 challenge raporunu cuma gününe kadar teslim edebilir miyiz?', 150],
        [1, 'Merhaba, evet cuma 23:59\'a kadar yükleyebilirsiniz. Takım arkadaşlarına da iletir misin?', 140],
        [0, 'Tabii, teşekkürler hocam! Bugünkü canlı derste görüşürüz.', 25],
    ]],
    ['beykent.student3', 'beykent.student2', [
        [0, 'Bugünkü canlı derse katılıyor musun?', 90],
        [1, 'Evet, bağlantıyı ders sayfasından açacağım. Sunum slaytlarını hazırladın mı?', 80],
        [0, 'Yarısını bitirdim, akşam paylaşırım 👍', 12],
    ]],
    ['beykent.admin', 'beykent.teacher', [
        [0, 'Bugün partnerlere platform tanıtımı yapacağız. Canlı ders oturumları hazır mı?', 55],
        [1, 'Evet hocam, IBM401 ve GT4T derslerindeki oturumlar hazır.', 6],
    ]],
    ['griffith.teacher', 'beykent.teacher', [
        [0, 'Hi! For today\'s IBM401 live session, shall we start with the Turkish market case and then the Irish one?', 120],
        [1, 'Sounds good. I will prepare 10 minutes on Istanbul\'s startup ecosystem.', 100],
        [0, 'Perfect, see you in the session. I\'ll share the breakout groups in the forum.', 18],
    ]],
    ['griffith.student2', 'beykent.student1', [
        [0, 'Hi! We\'re in the same team for the IBM401 case study. Shall we meet before the live session?', 45],
        [1, 'Hi! Sure, 15 minutes before works for me.', 35],
        [0, 'Great, I\'ll send you the draft slides.', 8],
    ]],
    ['griffith.student1', 'griffith.teacher', [
        [0, 'Hello, I couldn\'t open the reading for week 3 in GRI-DMI-101. Could you check the link?', 70],
        [1, 'Thanks for letting me know, it works now.', 60],
        [0, 'Great, thank you!', 10],
    ]],
    ['griffith.teacher', 'griffith.admin', [
        [0, 'Two new students registered on DIVERSE today. Could you approve their accounts?', 40],
        [1, 'Done, both accounts are approved.', 30],
    ]],
    ['griffith.student3', 'griffith.student1', [
        [0, 'Did you finish the quiz in GT4T-101?', 65],
        [1, 'Yes! The question on the European Green Deal was tricky.', 15],
    ]],
];

// Notifications: [recipient, sender, component, provider, course shortname, subject, minutes ago].
$notifications = [
    ['beykent.student1', 'griffith.teacher', 'mod_forum', 'posts', 'IBM401',
        'IBM401: Breakout groups for today\'s live session', 20],
    ['beykent.student1', 'beykent.teacher', 'mod_assign', 'assign_notification', 'GT4T-301',
        'GT4T-301: Your challenge report draft has been graded', 95],
    ['beykent.student2', 'beykent.teacher', 'mod_bigbluebuttonbn', 'instance_updated', 'GT4T-101',
        'GT4T-101: Live session time updated', 50],
    ['beykent.student3', 'griffith.teacher', 'mod_forum', 'posts', 'IBM401',
        'IBM401: Breakout groups for today\'s live session', 20],
    ['beykent.teacher', 'griffith.teacher', 'mod_forum', 'posts', 'IBM401',
        'IBM401: Breakout groups for today\'s live session', 20],
    ['beykent.teacher', 'beykent.student1', 'mod_assign', 'assign_notification', 'GT4T-301',
        'GT4T-301: New submission from beykent.student1', 130],
    ['beykent.admin', 'beykent.teacher', 'mod_forum', 'posts', 'CTP001',
        'CTP001: Welcome to the common training programme', 75],
    ['griffith.student1', 'griffith.teacher', 'mod_forum', 'posts', 'IBM401',
        'IBM401: Breakout groups for today\'s live session', 20],
    ['griffith.student1', 'griffith.teacher', 'mod_assign', 'assign_notification', 'GRI-DMI-101',
        'GRI-DMI-101: Your week 2 assignment has been graded', 110],
    ['griffith.student2', 'griffith.teacher', 'mod_bigbluebuttonbn', 'instance_updated', 'IBM401',
        'IBM401: Live session time updated', 45],
    ['griffith.student3', 'griffith.teacher', 'mod_forum', 'posts', 'GT4T-101',
        'GT4T-101: New discussion: Green Deal in your country', 85],
    ['griffith.teacher', 'beykent.teacher', 'mod_forum', 'posts', 'IBM401',
        'IBM401: Re: Breakout groups for today\'s live session', 14],
    ['griffith.admin', 'griffith.teacher', 'mod_forum', 'posts', 'CTP001',
        'CTP001: Welcome to the common training programme', 75],
];

// All accounts must exist.
$usernames = [];
foreach ($conversations as [$a, $b]) {
    $usernames[$a] = $usernames[$b] = true;
}
foreach ($notifications as [$to, $from]) {
    $usernames[$to] = $usernames[$from] = true;
}
$users = $DB->get_records_list('user', 'username', array_keys($usernames), '', 'id, username, deleted');
$byname = [];
foreach ($users as $user) {
    if (!$user->deleted) {
        $byname[$user->username] = $user;
    }
}
$missing = array_diff(array_keys($usernames), array_keys($byname));
if ($missing) {
    cli_error('Missing demo accounts: ' . implode(', ', $missing) . ' (run setup/diverse_demo_accounts.php first).');
}

if ($dryrun) {
    $count = array_sum(array_map(fn($c) => count($c[2]), $conversations));
    diverse_messages_report('WOULD', 'Create ' . count($conversations) . " conversations with $count messages.");
    diverse_messages_report('WOULD', 'Create ' . count($notifications) . ' notifications.');
    diverse_messages_report('WOULD', 'Show notifications on the site to these ' . count($byname) .
        ' accounts, with e-mail off for every kind of notification.');
    exit(0);
}

$created = ['messages' => [], 'conversations' => [], 'notifications' => [], 'users' => []];
$now = time();

// The demo accounts have "notifications disabled" (emailstop), which also hides notifications on the site. Turn it
// off, but first set every kind of notification to "on the site only", so that no e-mail is ever sent to the
// (made-up) addresses. --delete puts both back.
$providers = $DB->get_records('message_providers', null, '', 'id, component, name');
foreach ($byname as $user) {
    $full = \core_user::get_user($user->id);
    $before = ['emailstop' => (int)$full->emailstop, 'preferences' => []];
    foreach ($providers as $provider) {
        $preference = 'message_provider_' . $provider->component . '_' . $provider->name . '_enabled';
        $before['preferences'][$preference] = get_user_preferences($preference, null, $user->id);
        set_user_preference($preference, 'popup', $user->id);
    }
    if ($full->emailstop) {
        // Takes effect at the account's next login.
        $DB->set_field('user', 'emailstop', 0, ['id' => $user->id]);
    }
    $created['users'][$user->id] = $before;
}
diverse_messages_report('SET', 'Notifications shown on the site to ' . count($byname) . ' demo accounts (e-mail off).');

foreach ($conversations as [$a, $b, $lines]) {
    $pair = [$byname[$a]->id, $byname[$b]->id];
    $conversationid = \core_message\api::get_conversation_between_users($pair);
    if (!$conversationid) {
        $conversationid = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            $pair)->id;
        $created['conversations'][] = (int)$conversationid;
    }
    foreach ($lines as $i => [$who, $text, $minutes]) {
        $sender = $pair[$who];
        $recipient = $pair[1 - $who];
        // Sent like core_message\api::send_message_to_conversation(), without its privacy check: the demo accounts of
        // two partners need not share a course.
        $eventdata = new \core\message\message();
        $eventdata->courseid = SITEID;
        $eventdata->component = 'moodle';
        $eventdata->name = 'instantmessage';
        $eventdata->userfrom = \core_user::get_user($sender);
        $eventdata->convid = $conversationid;
        $eventdata->fullmessage = $text;
        $eventdata->fullmessagehtml = '';
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->smallmessage = $text;
        $eventdata->timecreated = time();
        $eventdata->notification = 0;
        $messageid = message_send($eventdata);
        $time = $now - $minutes * MINSECS;
        $DB->set_field('messages', 'timecreated', $time, ['id' => $messageid]);
        $created['messages'][] = (int)$messageid;
        if ($i < count($lines) - 1) {
            $record = $DB->get_record('messages', ['id' => $messageid]);
            \core_message\api::mark_message_as_read($recipient, $record, $time + MINSECS);
        }
    }
    diverse_messages_report('SET', "Conversation $a – $b (" . count($lines) . ' messages).');
}

$courses = [];
foreach ($notifications as [$to, $from, $component, $provider, $shortname, $subject, $minutes]) {
    if (!array_key_exists($shortname, $courses)) {
        $courses[$shortname] = $DB->get_record('course', ['shortname' => $shortname], 'id, fullname');
    }
    $course = $courses[$shortname];
    if (!$course) {
        diverse_messages_report('WARN', "Course $shortname not found: notification \"$subject\" skipped.");
        continue;
    }
    $url = new \core\url('/course/view.php', ['id' => $course->id]);
    $eventdata = new \core\message\message();
    $eventdata->courseid = $course->id;
    $eventdata->component = $component;
    $eventdata->name = $provider;
    $eventdata->userfrom = \core_user::get_user($byname[$from]->id);
    $eventdata->userto = \core_user::get_user($byname[$to]->id);
    $eventdata->subject = $subject;
    $eventdata->fullmessage = $subject;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml = '';
    $eventdata->smallmessage = $subject;
    $eventdata->notification = 1;
    $eventdata->contexturl = $url->out(false);
    $eventdata->contexturlname = format_string($course->fullname);
    $id = message_send($eventdata);
    if (!$id) {
        diverse_messages_report('WARN', "Notification \"$subject\" for $to was not stored (notifications turned off?).");
        continue;
    }
    // Unread: Moodle marks a notification read at once when no notification processor is set up for the user.
    $DB->update_record('notifications', (object)['id' => $id, 'timecreated' => $now - $minutes * MINSECS,
        'timeread' => null]);
    $DB->delete_records('message_popup_notifications', ['notificationid' => $id]);
    $DB->insert_record('message_popup_notifications', (object)['notificationid' => $id]);
    $created['notifications'][] = (int)$id;
    diverse_messages_report('SET', "Notification for $to: $subject");
}

set_config($memory, json_encode($created), 'diverse_setup');
\cache_helper::purge_by_definition('core', 'message_time_last_message_between_users');
cli_writeln('Done: ' . count($created['messages']) . ' messages, ' . count($created['notifications']) . ' notifications. ' .
    'Remove them with --delete.');
exit(0);
