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

namespace theme_diverse\local;

/**
 * Theme DIVERSE - The weekly goal of a student: how many activities they want to finish each week.
 *
 * The dashboard compares the activities completed this week with it. The goal is a user preference, chosen on the
 * dashboard (goal.php); without a choice it is DEFAULT.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class goal {
    /** @var string User preference holding the goal. */
    const PREFERENCE = 'theme_diverse_weeklygoal';

    /** @var int Goal of a user who has not chosen one. */
    const DEFAULT = 5;

    /** @var int[] Goals a user can choose. */
    const CHOICES = [3, 5, 10, 15];

    /**
     * The current user's goal.
     *
     * @return int
     */
    public static function get(): int {
        $goal = (int)get_user_preferences(self::PREFERENCE, self::DEFAULT);
        return in_array($goal, self::CHOICES, true) ? $goal : self::DEFAULT;
    }

    /**
     * Save the current user's goal.
     *
     * @param int $goal One of CHOICES.
     */
    public static function set(int $goal): void {
        if ($goal === self::DEFAULT) {
            unset_user_preference(self::PREFERENCE);
        } else if (in_array($goal, self::CHOICES, true)) {
            set_user_preference(self::PREFERENCE, $goal);
        }
    }
}
