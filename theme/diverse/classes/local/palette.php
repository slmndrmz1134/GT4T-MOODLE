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
 * Theme DIVERSE - The colours each user chooses: the DIVERSE orange (default) or the GT4T green, light or dark.
 *
 * Every choice but orange and light is the same theme compiled once more with other colours (scss/pre.scss): green,
 * dark, green-dark. Pages of users who chose one load that stylesheet instead of the normal one (styles_palette.php).
 * The choices are user preferences, set from the top bar, the rail or the user menu (palette.php). Without a choice
 * of mode the platform is light. Visitors always see orange and light; the dark mode is for the application pages
 * only.
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

    /** @var string User preference holding the chosen mode, light or dark; unset follows the device. */
    const MODE_PREFERENCE = 'theme_diverse_mode';

    /** @var string[] The modes a user can choose. */
    const MODES = ['light', 'dark'];

    /** @var string[] The compilations besides the normal one, as styles_palette.php names them. */
    const VARIANTS = ['green', 'dark', 'green-dark'];

    /** @var bool Whether the theme is being compiled with the green palette (set by styles_palette.php). */
    public static $compilinggreen = false;

    /** @var bool Whether the theme is being compiled in dark mode (set by styles_palette.php). */
    public static $compilingdark = false;

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
     * The mode the current user chose: light or dark.
     *
     * Without a choice the platform opens light, whatever the device's setting (project owner, November 2026); the
     * 'auto' mode of the switches and the head (follow the device) is then never used.
     *
     * @return string
     */
    public static function mode(): string {
        if (!isloggedin() || isguestuser()) {
            return 'light';
        }
        $mode = get_user_preferences(self::MODE_PREFERENCE);
        return in_array($mode, self::MODES, true) ? $mode : 'light';
    }

    /**
     * Save the mode of the current user.
     *
     * @param string $mode light or dark
     */
    public static function set_mode(string $mode): void {
        if (in_array($mode, self::MODES, true)) {
            set_user_preference(self::MODE_PREFERENCE, $mode);
        }
    }

    /**
     * Name of the compilation for a palette and a mode, '' for the normal one.
     *
     * @param bool $green
     * @param bool $dark
     * @return string
     */
    public static function variant(bool $green, bool $dark): string {
        return implode('-', array_filter([$green ? 'green' : '', $dark ? 'dark' : '']));
    }

    /**
     * The address of a theme stylesheet in another compilation.
     *
     * @param \core\url $url The normal or a compiled stylesheet of the theme.
     * @param string $variant '' for the normal one.
     * @return \core\url|null Null if the address is not one of the theme's stylesheets.
     */
    public static function variant_url(\core\url $url, string $variant): ?\core\url {
        global $CFG;

        $root = preg_quote($CFG->wwwroot . '/theme/', '#');
        if (!preg_match("#^{$root}(?:styles\.php|diverse/styles_palette\.php)((/_s)?/diverse/[^/?]+/all(-rtl)?)(\?.*)?$#",
                $url->out(false), $m)) {
            return null;
        }
        $new = new \core\url($variant === '' ? '/theme/styles.php' : '/theme/diverse/styles_palette.php',
            $variant === '' ? [] : ['variant' => $variant]);
        $new->set_slashargument($m[1]);
        return $new;
    }

    /**
     * Whether the page may use the compilations: not in theme designer mode, and only with slash arguments.
     *
     * @return bool
     */
    public static function available(): bool {
        global $CFG;
        return empty($CFG->themedesignermode) && !empty($CFG->slasharguments);
    }

    /**
     * Point the page stylesheets to the compilation the user chose.
     *
     * Only the normal stylesheet is replaced: in theme designer mode, without slash arguments or when a Boost Union
     * flavour already replaced it, the page keeps the orange palette. The dark mode only applies to application pages.
     *
     * @param array $urls The CSS URLs (passed as reference).
     */
    public static function alter_css_urls(array &$urls): void {
        global $PAGE;

        $dark = self::mode() === 'dark' && \theme_diverse\output\shell::applies($PAGE);
        $variant = self::variant(self::is_green(), $dark);
        if ($variant === '' || !self::available()) {
            return;
        }
        foreach ($urls as $i => $url) {
            if ($url instanceof \core\url && str_contains($url->out(false), '/theme/styles.php/')) {
                $urls[$i] = self::variant_url($url, $variant) ?? $url;
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
        // The mode, for phones: tablets and desktops have it in the rail (the theme hides this one there).
        $dark = self::mode() === 'dark';
        $hook->add_navitem((object)[
            'itemtype' => 'link',
            'url' => new \core\url('/theme/diverse/palette.php', [
                'mode' => $dark ? 'light' : 'dark',
                'returnurl' => $PAGE->url->out_as_local_url(false),
                'sesskey' => sesskey(),
            ]),
            'title' => get_string($dark ? 'mode_light' : 'mode_dark', 'theme_diverse'),
            'titleidentifier' => ($dark ? 'mode_light' : 'mode_dark') . ',theme_diverse',
        ]);
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
