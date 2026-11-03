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
 * Theme DIVERSE - The colour palette each user chooses: the DIVERSE orange (default) or the GT4T green.
 *
 * The green palette is the same theme compiled a second time with other colours (scss/pre.scss). Pages of users who
 * chose it load that stylesheet instead of the normal one (styles_palette.php); the choice is a user preference, set
 * from the user menu (palette.php). Visitors always see the orange palette.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class palette {
    /** @var string User preference holding the chosen palette; unset means orange. */
    const PREFERENCE = 'theme_diverse_palette';

    /** @var string Value of the preference for the green palette. */
    const GREEN = 'green';

    /** @var bool Whether the theme is being compiled with the green palette (set by styles_palette.php). */
    public static $compilinggreen = false;

    /** @var string[] Base colours of the generated course card patterns in the green palette (see core_renderer). */
    const PATTERN_COLORS = ['#19B35C', '#053225', '#127A42', '#D4F7A8', '#006D77'];

    /**
     * Whether the current user sees the green palette.
     *
     * @return bool
     */
    public static function is_green(): bool {
        return isloggedin() && !isguestuser() && get_user_preferences(self::PREFERENCE) === self::GREEN;
    }

    /**
     * Save the palette of the current user.
     *
     * @param bool $green
     */
    public static function set(bool $green): void {
        if ($green) {
            set_user_preference(self::PREFERENCE, self::GREEN);
        } else {
            unset_user_preference(self::PREFERENCE);
        }
    }

    /**
     * Point the page stylesheets of users with the green palette to the green compilation.
     *
     * Only the normal stylesheet is replaced: in theme designer mode, without slash arguments or when a Boost Union
     * flavour already replaced it, the page keeps the orange palette.
     *
     * @param array $urls The CSS URLs (passed as reference).
     */
    public static function alter_css_urls(array &$urls): void {
        global $CFG;

        if (!self::is_green() || !empty($CFG->themedesignermode) || empty($CFG->slasharguments)) {
            return;
        }
        $styles = preg_quote($CFG->wwwroot . '/theme/styles.php', '|');
        foreach ($urls as $i => $url) {
            if ($url instanceof \core\url && preg_match("|^$styles((/_s)?/diverse/[^/]+/all(-rtl)?)$|", $url->out(false), $m)) {
                $urls[$i] = new \core\url('/theme/diverse/styles_palette.php');
                $urls[$i]->set_slashargument($m[1]);
            }
        }
    }

    /**
     * Add the switch between the palettes to the user menu.
     *
     * @param \core_user\hook\extend_user_menu $hook
     */
    public static function extend_user_menu(\core_user\hook\extend_user_menu $hook): void {
        global $PAGE;

        if (!isloggedin() || isguestuser() || !in_array('diverse', [$PAGE->theme->name, ...$PAGE->theme->parents], true)) {
            return;
        }
        $green = self::is_green();
        $hook->add_navitem((object)[
            'itemtype' => 'link',
            'url' => new \core\url('/theme/diverse/palette.php', [
                'green' => (int)!$green,
                'returnurl' => $PAGE->url->out_as_local_url(false),
                'sesskey' => sesskey(),
            ]),
            'title' => get_string($green ? 'palette_toorange' : 'palette_togreen', 'theme_diverse'),
            'titleidentifier' => ($green ? 'palette_toorange' : 'palette_togreen') . ',theme_diverse',
        ]);
    }
}
