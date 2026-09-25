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

namespace theme_diverse\output;

/**
 * Theme DIVERSE - Core renderer.
 *
 * Extends Boost Union's renderer instead of overriding its layouts or templates, so that
 * everything Boost Union renders around the page (navbar, language menu, footer, info banners,
 * tenant selection on the login page) keeps working unchanged and survives Boost Union upgrades.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost_union\output\core_renderer {
    /** @var string[] Base colours of the generated course card patterns: brand orange, ink, action orange, peach, grey. */
    const PATTERN_COLORS = ['#FF671F', '#1F2328', '#C2410C', '#FFD8C2', '#63666A'];

    /**
     * Base colour of the generated pattern on course cards without an image, taken from the DIVERSE
     * palette instead of the site-wide "course colour" settings so that the cards match the theme.
     *
     * @param int $id Id to use when generating the colour.
     * @return string hex colour code
     */
    public function get_generated_color_for_id($id) {
        return self::PATTERN_COLORS[$id % count(self::PATTERN_COLORS)];
    }

    /**
     * Wrap the main content with the landing page sections on the site home for visitors.
     *
     * The main content token from the parent stays in place, so the site home content
     * configured by the admin is still rendered between the two parts.
     *
     * @return string
     */
    public function main_content() {
        $maincontent = parent::main_content();
        if (!$this->is_landing_page()) {
            return $maincontent;
        }

        $context = (new landing())->export_for_template($this);
        return $this->render_from_template('theme_diverse/landing_top', $context) .
            $maincontent .
            $this->render_from_template('theme_diverse/landing_bottom', $context);
    }

    /**
     * Render the login form with the DIVERSE brand panel next to it.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        $brandpanel = $this->render_from_template('theme_diverse/login_brand', (new landing())->export_for_login());
        return $brandpanel . parent::render_login($form);
    }

    /**
     * Whether the current page is the site home seen by a visitor (not logged in, or guest).
     *
     * @return bool
     */
    protected function is_landing_page(): bool {
        return $this->page->pagelayout === 'frontpage' &&
            $this->page->pagetype === 'site-index' &&
            (!isloggedin() || isguestuser());
    }
}
