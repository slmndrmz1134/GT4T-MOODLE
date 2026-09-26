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

/**
 * How long each student's chats are kept. Every student chooses this for themselves; administrators cannot change it.
 *
 * "Not saved" (the default) keeps nothing on the server: the chat only lives in the student's browser tab.
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class retention {
    /** User preference with the number of days. */
    public const PREFERENCE = 'local_diverse_assistant_retention';

    /** User preference with the time the student read the notice shown before the first chat. */
    public const NOTICE_PREFERENCE = 'local_diverse_assistant_notice';

    /** Nothing is saved on the server. */
    public const NOT_SAVED = 0;

    /** Kept until the student deletes it. */
    public const FOREVER = -1;

    /** The choices, in days. */
    public const OPTIONS = [self::NOT_SAVED, 1, 7, 30, 365, self::FOREVER];

    /** Choice for students who have not chosen: the most private one. */
    public const DEFAULT = self::NOT_SAVED;

    /** Days the text-free usage log is kept (hourly limit and totals for administrators). */
    public const USAGE_DAYS = 30;

    /**
     * A user's choice.
     *
     * @param int|null $userid User id, null for the current user.
     * @return int Days, or NOT_SAVED / FOREVER.
     */
    public static function get(?int $userid = null): int {
        $value = get_user_preferences(self::PREFERENCE, null, $userid);
        if ($value === null || !is_numeric($value) || !in_array((int)$value, self::OPTIONS, true)) {
            return self::DEFAULT;
        }
        return (int)$value;
    }

    /**
     * Save the current user's choice and apply it at once.
     *
     * @param int $days One of OPTIONS.
     * @return int How many saved conversations were deleted because of the new choice.
     */
    public static function set(int $days): int {
        global $USER;
        if (!in_array($days, self::OPTIONS, true)) {
            throw new \invalid_parameter_exception('Unknown retention period: ' . $days);
        }
        set_user_preference(self::PREFERENCE, $days);
        return self::apply((int)$USER->id);
    }

    /**
     * Whether chats of the user are saved on the server.
     *
     * @param int|null $userid User id, null for the current user.
     * @return bool
     */
    public static function is_saved(?int $userid = null): bool {
        return self::get($userid) !== self::NOT_SAVED;
    }

    /**
     * Delete the user's conversations that are older than their choice allows.
     *
     * @param int $userid User id.
     * @return int How many conversations were deleted.
     */
    public static function apply(int $userid): int {
        $days = self::get($userid);
        if ($days === self::FOREVER) {
            return 0;
        }
        if ($days === self::NOT_SAVED) {
            return store::delete_for_user($userid);
        }
        return store::delete_for_user($userid, time() - $days * DAYSECS);
    }

    /**
     * Apply every user's choice and trim the usage log. Run by the scheduled task.
     *
     * @return int How many conversations were deleted.
     */
    public static function cleanup(): int {
        global $DB;
        $deleted = 0;
        $userids = $DB->get_fieldset_sql('SELECT DISTINCT userid FROM {' . store::TABLE_CONVERSATIONS . '}');
        foreach ($userids as $userid) {
            $deleted += self::apply((int)$userid);
        }
        store::delete_usage_before(time() - self::USAGE_DAYS * DAYSECS);
        return $deleted;
    }

    /**
     * Choices for the chat panel.
     *
     * @param int $selected The current choice.
     * @return array[] Each with value, label and selected.
     */
    public static function get_options(int $selected): array {
        $options = [];
        foreach (self::OPTIONS as $days) {
            $options[] = ['value' => $days, 'label' => self::get_label($days), 'selected' => $days === $selected];
        }
        return $options;
    }

    /**
     * Name of a choice.
     *
     * @param int $days One of OPTIONS.
     * @return string
     */
    public static function get_label(int $days): string {
        return match ($days) {
            self::NOT_SAVED => get_string('retention_notsaved', 'local_diverse_assistant'),
            self::FOREVER => get_string('retention_forever', 'local_diverse_assistant'),
            1 => get_string('retention_oneday', 'local_diverse_assistant'),
            default => get_string('retention_days', 'local_diverse_assistant', $days),
        };
    }

    /**
     * Whether the user has read the notice shown before the first chat.
     *
     * @return bool
     */
    public static function notice_accepted(): bool {
        return (bool)get_user_preferences(self::NOTICE_PREFERENCE, 0);
    }

    /**
     * Record that the current user has read the notice.
     */
    public static function accept_notice(): void {
        set_user_preference(self::NOTICE_PREFERENCE, time());
    }
}
