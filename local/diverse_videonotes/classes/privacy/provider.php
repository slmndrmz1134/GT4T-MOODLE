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

namespace local_diverse_videonotes\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * DIVERSE video bookmarks - Privacy: each user's bookmarks, in the context of the activity they mark
 *
 * @package    local_diverse_videonotes
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * The data the plugin stores.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_diverse_videonotes', [
            'userid' => 'privacy:metadata:userid',
            'cmid' => 'privacy:metadata:cmid',
            'position' => 'privacy:metadata:position',
            'note' => 'privacy:metadata:note',
            'timecreated' => 'privacy:metadata:timecreated',
        ], 'privacy:metadata');
        return $collection;
    }

    /**
     * The activities a user has bookmarks in.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql(
            "SELECT ctx.id
               FROM {local_diverse_videonotes} n
               JOIN {context} ctx ON ctx.instanceid = n.cmid AND ctx.contextlevel = :level
              WHERE n.userid = :userid",
            ['level' => CONTEXT_MODULE, 'userid' => $userid]
        );
        return $contextlist;
    }

    /**
     * The users with bookmarks in an activity.
     *
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }
        $userlist->add_from_sql('userid', "SELECT userid FROM {local_diverse_videonotes} WHERE cmid = :cmid",
            ['cmid' => $context->instanceid]);
    }

    /**
     * Export a user's bookmarks.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            $records = $DB->get_records('local_diverse_videonotes', ['userid' => $userid, 'cmid' => $context->instanceid],
                'position ASC');
            if (!$records) {
                continue;
            }
            $data = array_values(array_map(fn($record) => (object)[
                'position' => gmdate('H:i:s', $record->position),
                'note' => $record->note,
                'timecreated' => transform::datetime($record->timecreated),
            ], $records));
            writer::with_context($context)->export_data(
                [get_string('pluginname', 'local_diverse_videonotes')],
                (object)['bookmarks' => $data]
            );
        }
    }

    /**
     * Delete everyone's bookmarks in an activity.
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel == CONTEXT_MODULE) {
            $DB->delete_records('local_diverse_videonotes', ['cmid' => $context->instanceid]);
        }
    }

    /**
     * Delete a user's bookmarks in the given activities.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $DB->delete_records('local_diverse_videonotes', ['userid' => $userid, 'cmid' => $context->instanceid]);
            }
        }
    }

    /**
     * Delete the bookmarks of some users in an activity.
     *
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE || !$userlist->get_userids()) {
            return;
        }
        [$insql, $params] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params['cmid'] = $context->instanceid;
        $DB->delete_records_select('local_diverse_videonotes', "cmid = :cmid AND userid $insql", $params);
    }
}
