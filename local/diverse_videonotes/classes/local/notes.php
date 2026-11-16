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

namespace local_diverse_videonotes\local;

/**
 * DIVERSE video bookmarks - The bookmarks of a user on the video of an activity
 *
 * @package    local_diverse_videonotes
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class notes {
    /** @var string The table. */
    const TABLE = 'local_diverse_videonotes';

    /** @var int The most bookmarks a user keeps on one activity. */
    const MAX_PER_ACTIVITY = 200;

    /** @var int The longest note, in characters. */
    const MAX_NOTE = 255;

    /**
     * Check that the current user may use bookmarks in an activity, and return its context.
     *
     * @param int $cmid
     * @return \context_module
     */
    public static function require_use(int $cmid): \context_module {
        [$course, $cm] = get_course_and_cm_from_cmid($cmid);
        $context = \context_module::instance($cm->id);
        \core_external\external_api::validate_context($context);
        require_capability('local/diverse_videonotes:use', $context);
        if (!$cm->uservisible) {
            throw new \require_login_exception('activityiscurrentlyhidden');
        }
        return $context;
    }

    /**
     * The bookmarks of a user on an activity, in the order of the video.
     *
     * @param int $userid
     * @param int $cmid
     * @return array[] id, position, note
     */
    public static function list(int $userid, int $cmid): array {
        global $DB;
        $records = $DB->get_records(self::TABLE, ['userid' => $userid, 'cmid' => $cmid], 'position ASC, id ASC',
            'id, position, note');
        return array_values(array_map(fn($record) => self::export($record), $records));
    }

    /**
     * Add a bookmark.
     *
     * @param int $userid
     * @param int $cmid
     * @param int $position Seconds from the start of the video.
     * @param string $note
     * @return array id, position, note
     */
    public static function add(int $userid, int $cmid, int $position, string $note): array {
        global $DB;
        if ($DB->count_records(self::TABLE, ['userid' => $userid, 'cmid' => $cmid]) >= self::MAX_PER_ACTIVITY) {
            throw new \moodle_exception('toomany', 'local_diverse_videonotes', '', self::MAX_PER_ACTIVITY);
        }
        $record = (object)[
            'userid' => $userid,
            'cmid' => $cmid,
            'position' => max(0, $position),
            'note' => \core_text::substr(trim(clean_param($note, PARAM_TEXT)), 0, self::MAX_NOTE),
            'timecreated' => time(),
        ];
        $record->id = $DB->insert_record(self::TABLE, $record);
        return self::export($record);
    }

    /**
     * Delete one of a user's bookmarks.
     *
     * @param int $userid
     * @param int $id
     * @return int The activity the bookmark was on.
     */
    public static function delete(int $userid, int $id): int {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['id' => $id, 'userid' => $userid], '*', MUST_EXIST);
        $DB->delete_records(self::TABLE, ['id' => $record->id]);
        return (int)$record->cmid;
    }

    /**
     * The fields the panel shows.
     *
     * @param \stdClass $record
     * @return array
     */
    protected static function export(\stdClass $record): array {
        return [
            'id' => (int)$record->id,
            'position' => (int)$record->position,
            'note' => (string)$record->note,
        ];
    }
}
