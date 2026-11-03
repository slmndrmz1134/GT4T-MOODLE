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
 * Theme DIVERSE - Privacy provider
 *
 * @package    theme_diverse
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_diverse\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use theme_diverse\local\goal;
use theme_diverse\local\palette;
use theme_diverse\output\shell;

/**
 * Privacy Subsystem: the theme stores only the colour palette, light or dark mode, the weekly goal and whether the menu
 * shows its names, as each user chooses them (user preferences).
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Describe the data the theme stores.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(palette::PREFERENCE, 'privacy:metadata:preference:palette');
        $collection->add_user_preference(goal::PREFERENCE, 'privacy:metadata:preference:weeklygoal');
        $collection->add_user_preference(shell::RAIL_PREFERENCE, 'privacy:metadata:preference:railopen');
        $collection->add_user_preference(palette::MODE_PREFERENCE, 'privacy:metadata:preference:mode');
        return $collection;
    }

    /**
     * Export the palette, the weekly goal and the menu choice of a user.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid) {
        $value = get_user_preferences(palette::PREFERENCE, null, $userid);
        if ($value !== null) {
            writer::export_user_preference(
                'theme_diverse',
                palette::PREFERENCE,
                $value,
                get_string('palette_togreen', 'theme_diverse')
            );
        }
        $value = get_user_preferences(goal::PREFERENCE, null, $userid);
        if ($value !== null) {
            writer::export_user_preference(
                'theme_diverse',
                goal::PREFERENCE,
                $value,
                get_string('dash_goal_title', 'theme_diverse')
            );
        }
        $value = get_user_preferences(palette::MODE_PREFERENCE, null, $userid);
        if ($value !== null) {
            writer::export_user_preference('theme_diverse', palette::MODE_PREFERENCE, $value,
                get_string('mode_dark', 'theme_diverse'));
        }
        $value = get_user_preferences(shell::RAIL_PREFERENCE, null, $userid);
        if ($value !== null) {
            writer::export_user_preference(
                'theme_diverse',
                shell::RAIL_PREFERENCE,
                $value,
                get_string('rail_expand', 'theme_diverse')
            );
        }
    }
}
