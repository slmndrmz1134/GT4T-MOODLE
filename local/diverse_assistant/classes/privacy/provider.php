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

namespace local_diverse_assistant\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use local_diverse_assistant\local\retention;
use local_diverse_assistant\local\store;

/**
 * Privacy API: what the plugin stores and sends, with export and deletion.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\user_preference_provider {

    #[\Override]
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(store::TABLE_CONVERSATIONS, [
            'userid' => 'privacy:metadata:conv:userid',
            'courseid' => 'privacy:metadata:conv:courseid',
            'title' => 'privacy:metadata:conv:title',
            'timecreated' => 'privacy:metadata:conv:timecreated',
            'timemodified' => 'privacy:metadata:conv:timemodified',
        ], 'privacy:metadata:conv');
        $collection->add_database_table(store::TABLE_MESSAGES, [
            'role' => 'privacy:metadata:msg:role',
            'content' => 'privacy:metadata:msg:content',
            'timecreated' => 'privacy:metadata:msg:timecreated',
        ], 'privacy:metadata:msg');
        $collection->add_database_table(store::TABLE_USAGE, [
            'userid' => 'privacy:metadata:use:userid',
            'courseid' => 'privacy:metadata:use:courseid',
            'prompttokens' => 'privacy:metadata:use:prompttokens',
            'completiontokens' => 'privacy:metadata:use:completiontokens',
            'timecreated' => 'privacy:metadata:use:timecreated',
        ], 'privacy:metadata:use');
        $collection->add_external_location_link('aiservice', [
            'message' => 'privacy:metadata:aiservice:message',
            'history' => 'privacy:metadata:aiservice:history',
            'coursematerials' => 'privacy:metadata:aiservice:coursematerials',
        ], 'privacy:metadata:aiservice');
        $collection->add_user_preference(retention::PREFERENCE, 'privacy:metadata:preference:retention');
        $collection->add_user_preference(retention::NOTICE_PREFERENCE, 'privacy:metadata:preference:notice');
        return $collection;
    }

    #[\Override]
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $params = ['contextlevel' => CONTEXT_COURSE, 'userid1' => $userid, 'userid2' => $userid];
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel
                   AND (ctx.instanceid IN (SELECT courseid FROM {" . store::TABLE_CONVERSATIONS . "} WHERE userid = :userid1)
                        OR ctx.instanceid IN (SELECT courseid FROM {" . store::TABLE_USAGE . "} WHERE userid = :userid2))";
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    #[\Override]
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_course) {
            return;
        }
        $params = ['courseid' => $context->instanceid];
        $userlist->add_from_sql('userid', 'SELECT userid FROM {' . store::TABLE_CONVERSATIONS . '} WHERE courseid = :courseid',
            $params);
        $userlist->add_from_sql('userid', 'SELECT userid FROM {' . store::TABLE_USAGE . '} WHERE courseid = :courseid', $params);
    }

    #[\Override]
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $userid = (int)$contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_course) {
                continue;
            }
            $conversations = $DB->get_records(store::TABLE_CONVERSATIONS,
                ['userid' => $userid, 'courseid' => $context->instanceid], 'timecreated ASC');
            $data = [];
            foreach ($conversations as $conversation) {
                $messages = [];
                foreach (store::get_messages((int)$conversation->id) as $message) {
                    $messages[] = [
                        'role' => $message->role,
                        'content' => $message->content,
                        'timecreated' => transform::datetime($message->timecreated),
                    ];
                }
                $data[] = [
                    'title' => $conversation->title,
                    'timecreated' => transform::datetime($conversation->timecreated),
                    'timemodified' => transform::datetime($conversation->timemodified),
                    'messages' => $messages,
                ];
            }
            $usage = $DB->get_record_sql('SELECT COUNT(1) AS questions, COALESCE(SUM(prompttokens + completiontokens), 0) AS tokens
                                            FROM {' . store::TABLE_USAGE . '}
                                           WHERE userid = ? AND courseid = ?', [$userid, $context->instanceid]);
            if (!$data && !$usage->questions) {
                continue;
            }
            writer::with_context($context)->export_data([get_string('pluginname', 'local_diverse_assistant')], (object)[
                'conversations' => $data,
                'questionsinlast30days' => (int)$usage->questions,
                'tokensinlast30days' => (int)$usage->tokens,
            ]);
        }
    }

    #[\Override]
    public static function delete_data_for_all_users_in_context(\context $context): void {
        if ($context instanceof \context_course) {
            store::delete_for_course((int)$context->instanceid);
        }
    }

    #[\Override]
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;
        $userid = (int)$contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof \context_course) {
                store::delete_for_user($userid, 0, (int)$context->instanceid);
                $DB->delete_records(store::TABLE_USAGE, ['userid' => $userid, 'courseid' => $context->instanceid]);
            }
        }
    }

    #[\Override]
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;
        $context = $userlist->get_context();
        if (!$context instanceof \context_course) {
            return;
        }
        foreach ($userlist->get_userids() as $userid) {
            store::delete_for_user((int)$userid, 0, (int)$context->instanceid);
            $DB->delete_records(store::TABLE_USAGE, ['userid' => $userid, 'courseid' => $context->instanceid]);
        }
    }

    #[\Override]
    public static function export_user_preferences(int $userid): void {
        $retention = get_user_preferences(retention::PREFERENCE, null, $userid);
        if ($retention !== null) {
            writer::export_user_preference('local_diverse_assistant', retention::PREFERENCE, $retention,
                retention::get_label(retention::get($userid)));
        }
        $notice = get_user_preferences(retention::NOTICE_PREFERENCE, null, $userid);
        if ($notice !== null) {
            writer::export_user_preference('local_diverse_assistant', retention::NOTICE_PREFERENCE,
                transform::datetime((int)$notice), get_string('privacy:metadata:preference:notice', 'local_diverse_assistant'));
        }
    }
}
