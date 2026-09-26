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

namespace local_diverse_assistant\local;

use local_diverse_assistant\local\teacher\proposals;

/**
 * Database access for saved conversations and the usage log.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store {
    /** Table of conversations. */
    public const TABLE_CONVERSATIONS = 'local_diverse_assistant_conv';

    /** Table of messages. */
    public const TABLE_MESSAGES = 'local_diverse_assistant_msg';

    /** Table of the usage log (no text, only counts). */
    public const TABLE_USAGE = 'local_diverse_assistant_use';

    /** Longest conversation title, in characters. */
    private const TITLE_LENGTH = 80;

    /** Most conversations listed in the history panel. */
    private const HISTORY_LIMIT = 50;

    /**
     * A conversation of the given user.
     *
     * @param int $conversationid Conversation id.
     * @param int $userid The user who must own it.
     * @return \stdClass
     * @throws \moodle_exception If it does not exist or belongs to someone else.
     */
    public static function get_conversation(int $conversationid, int $userid): \stdClass {
        global $DB;
        $conversation = $DB->get_record(self::TABLE_CONVERSATIONS, ['id' => $conversationid, 'userid' => $userid]);
        if (!$conversation) {
            throw new \moodle_exception('errornotfound', 'local_diverse_assistant');
        }
        return $conversation;
    }

    /**
     * The user's saved conversations in a course, newest first.
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     * @return \stdClass[] Records with id, title and timemodified.
     */
    public static function list_conversations(int $userid, int $courseid): array {
        global $DB;
        return array_values($DB->get_records(self::TABLE_CONVERSATIONS, ['userid' => $userid, 'courseid' => $courseid],
            'timemodified DESC, id DESC', 'id, title, timemodified', 0, self::HISTORY_LIMIT));
    }

    /**
     * Messages of a conversation, oldest first.
     *
     * @param int $conversationid Conversation id.
     * @param int $last Only the last this many messages; 0 for all.
     * @return \stdClass[] Records with id, role, content and timecreated.
     */
    public static function get_messages(int $conversationid, int $last = 0): array {
        global $DB;
        if ($last > 0) {
            $messages = $DB->get_records(self::TABLE_MESSAGES, ['conversationid' => $conversationid], 'id DESC',
                'id, role, content, timecreated', 0, $last);
            return array_reverse(array_values($messages));
        }
        return array_values($DB->get_records(self::TABLE_MESSAGES, ['conversationid' => $conversationid], 'id ASC',
            'id, role, content, timecreated'));
    }

    /**
     * Save a question and its answer, starting a conversation if needed.
     *
     * @param int $conversationid Existing conversation, or 0 to start one.
     * @param int $userid Owner.
     * @param int $courseid Course.
     * @param string $question The student's message.
     * @param string $answer The assistant's answer.
     * @return int The conversation id.
     */
    public static function save_exchange(int $conversationid, int $userid, int $courseid, string $question,
            string $answer): int {
        global $DB;
        $now = time();
        $transaction = $DB->start_delegated_transaction();
        if ($conversationid) {
            $DB->set_field(self::TABLE_CONVERSATIONS, 'timemodified', $now, ['id' => $conversationid, 'userid' => $userid]);
        } else {
            $title = shorten_text(preg_replace('/\s+/u', ' ', trim($question)), self::TITLE_LENGTH);
            $conversationid = $DB->insert_record(self::TABLE_CONVERSATIONS, (object)[
                'userid' => $userid,
                'courseid' => $courseid,
                'title' => $title,
                'timecreated' => $now,
                'timemodified' => $now,
            ]);
        }
        $DB->insert_records(self::TABLE_MESSAGES, [
            ['conversationid' => $conversationid, 'role' => 'user', 'content' => $question, 'timecreated' => $now],
            ['conversationid' => $conversationid, 'role' => 'assistant', 'content' => $answer, 'timecreated' => $now],
        ]);
        $transaction->allow_commit();
        return $conversationid;
    }

    /**
     * Id of the last answer of a conversation.
     *
     * @param int $conversationid Conversation id.
     * @return int 0 if there is none.
     */
    public static function get_last_answer_id(int $conversationid): int {
        global $DB;
        return (int)$DB->get_field_sql('SELECT MAX(id) FROM {' . self::TABLE_MESSAGES . '} WHERE conversationid = ? AND role = ?',
            [$conversationid, 'assistant']);
    }

    /**
     * Delete one of the user's conversations.
     *
     * @param int $conversationid Conversation id.
     * @param int $userid The user who must own it.
     * @return bool Whether it existed.
     */
    public static function delete_conversation(int $conversationid, int $userid): bool {
        global $DB;
        if (!$DB->record_exists(self::TABLE_CONVERSATIONS, ['id' => $conversationid, 'userid' => $userid])) {
            return false;
        }
        self::delete_conversations([$conversationid]);
        return true;
    }

    /**
     * Delete a user's conversations.
     *
     * @param int $userid User id.
     * @param int $before Only those whose last message is older than this time; 0 for all.
     * @param int $courseid Only in this course; 0 for all courses.
     * @return int How many were deleted.
     */
    public static function delete_for_user(int $userid, int $before = 0, int $courseid = 0): int {
        global $DB;
        $select = 'userid = :userid';
        $params = ['userid' => $userid];
        if ($before) {
            $select .= ' AND timemodified < :before';
            $params['before'] = $before;
        }
        if ($courseid) {
            $select .= ' AND courseid = :courseid';
            $params['courseid'] = $courseid;
        }
        $ids = $DB->get_fieldset_select(self::TABLE_CONVERSATIONS, 'id', $select, $params);
        self::delete_conversations($ids);
        return count($ids);
    }

    /**
     * Delete everything the plugin stored about a course.
     *
     * @param int $courseid Course id.
     */
    public static function delete_for_course(int $courseid): void {
        global $DB;
        self::delete_conversations($DB->get_fieldset_select(self::TABLE_CONVERSATIONS, 'id', 'courseid = ?', [$courseid]));
        $DB->delete_records(self::TABLE_USAGE, ['courseid' => $courseid]);
        proposals::delete_for_course($courseid);
    }

    /**
     * Delete conversations, their messages and the proposals made in them.
     *
     * @param int[] $ids Conversation ids.
     */
    public static function delete_conversations(array $ids): void {
        global $DB;
        proposals::delete_for_conversations($ids);
        foreach (array_chunk($ids, 500) as $chunk) {
            [$insql, $params] = $DB->get_in_or_equal($chunk);
            $DB->delete_records_select(self::TABLE_MESSAGES, "conversationid $insql", $params);
            $DB->delete_records_select(self::TABLE_CONVERSATIONS, "id $insql", $params);
        }
    }

    /**
     * Record that a question was answered (no text is stored).
     *
     * @param int $userid User id.
     * @param int $courseid Course id.
     * @param int $prompttokens Input tokens.
     * @param int $completiontokens Output tokens.
     */
    public static function log_usage(int $userid, int $courseid, int $prompttokens, int $completiontokens): void {
        global $DB;
        $DB->insert_record(self::TABLE_USAGE, (object)[
            'userid' => $userid,
            'courseid' => $courseid,
            'prompttokens' => $prompttokens,
            'completiontokens' => $completiontokens,
            'timecreated' => time(),
        ]);
    }

    /**
     * How many questions the user asked since a time.
     *
     * @param int $userid User id.
     * @param int $since Unix time.
     * @return int
     */
    public static function count_usage(int $userid, int $since): int {
        global $DB;
        return $DB->count_records_select(self::TABLE_USAGE, 'userid = ? AND timecreated >= ?', [$userid, $since]);
    }

    /**
     * Site-wide totals since a time, for the settings page.
     *
     * @param int $since Unix time.
     * @return \stdClass With questions, users and tokens.
     */
    public static function usage_summary(int $since): \stdClass {
        global $DB;
        $record = $DB->get_record_sql(
            "SELECT COUNT(1) AS questions, COUNT(DISTINCT userid) AS users,
                    COALESCE(SUM(prompttokens + completiontokens), 0) AS tokens
               FROM {" . self::TABLE_USAGE . "}
              WHERE timecreated >= ?", [$since]);
        return (object)[
            'questions' => (int)$record->questions,
            'users' => (int)$record->users,
            'tokens' => (int)$record->tokens,
        ];
    }

    /**
     * Delete usage rows older than a time.
     *
     * @param int $before Unix time.
     */
    public static function delete_usage_before(int $before): void {
        global $DB;
        $DB->delete_records_select(self::TABLE_USAGE, 'timecreated < ?', [$before]);
    }
}
