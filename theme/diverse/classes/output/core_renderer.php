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
     * Constructor: extend Boost Union core renderer and register visitor landing navigation links.
     *
     * @param \moodle_page $page
     * @param string $target
     */
    public function __construct(\moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->add_landing_navigation_nodes();
    }

    /**
     * Add component scroll buttons to the primary navigation for visitors.
     */
    protected function add_landing_navigation_nodes() {
        if (!isloggedin() || isguestuser()) {
            $primarynav = $this->page->primarynav;
            if ($primarynav && !$primarynav->find('diverse_collab', \navigation_node::TYPE_CUSTOM)) {
                $primarynav->add(
                    get_string('nav_collab', 'theme_diverse'),
                    new \moodle_url('/#diverse-collaborate'),
                    \navigation_node::TYPE_CUSTOM,
                    null,
                    'diverse_collab'
                );
                $primarynav->add(
                    get_string('nav_impact', 'theme_diverse'),
                    new \moodle_url('/#diverse-impact'),
                    \navigation_node::TYPE_CUSTOM,
                    null,
                    'diverse_impact'
                );
                $primarynav->add(
                    get_string('nav_partners', 'theme_diverse'),
                    new \moodle_url('/#diverse-partners'),
                    \navigation_node::TYPE_CUSTOM,
                    null,
                    'diverse_partners'
                );
                $primarynav->add(
                    get_string('nav_courses', 'theme_diverse'),
                    new \moodle_url('/#diverse-courses'),
                    \navigation_node::TYPE_CUSTOM,
                    null,
                    'diverse_courses'
                );
            }
        }
    }

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

    /**
     * Returns the favicon URL for the DIVERSE platform.
     *
     * If the Boost Union admin has uploaded a custom favicon (via Appearance → Boost Union → Look → Favicon),
     * or a flavour overrides it, those take precedence via the parent implementation.
     * Otherwise the DIVERSE logo PNG (theme/diverse/pix/favicon.png) is used instead of the
     * plain Moodle favicon.ico stub that ships with the Boost Union Child boilerplate.
     *
     * @return \core\url
     */
    public function favicon() {
        // If Boost Union (or a flavour) has an explicit favicon configured, use it as-is.
        $parentfavicon = parent::favicon();

        // parent::favicon() falls back to image_url('favicon', 'theme') when nothing is configured.
        // That URL ends with "/favicon" (no file extension) and points to the old 766-byte stub .ico.
        // In that case we substitute the DIVERSE logo PNG that lives in pix/favicon.png.
        $url = $parentfavicon->out(false);
        if (preg_match('#/favicon$#', $url)) {
            return $this->image_url('favicon', 'theme_diverse');
        }

        return $parentfavicon;
    }

    /**
     * Inject the DIVERSE og:image meta tag for Google Search and social-media link previews.
     *
     * Appends <meta property="og:image"> pointing at the DIVERSE logo PNG after all the
     * standard head HTML that Boost Union and Moodle core produce.  Using image_url() ensures
     * the URL carries the correct theme-revision hash for cache-busting.
     *
     * @return string
     */
    public function standard_head_html() {
        $this->add_landing_navigation_nodes();
        $html = parent::standard_head_html();

        $logourl = $this->image_url('favicon', 'theme_diverse')->out(false);

        $html .= \html_writer::empty_tag('meta', [
            'property' => 'og:image',
            'content'  => $logourl,
        ]) . "\n";

        return $html;
    }

    /**
     * Return the site's compact logo URL for the navbar.
     *
     * Falls back to the DIVERSE logo (theme/diverse/pix/logo.png) if no flavour
     * or admin compact logo is configured in Boost Union.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $parentlogo = parent::get_compact_logo_url($maxwidth, $maxheight);
        if ($parentlogo) {
            return $parentlogo;
        }

        return $this->image_url('logo', 'theme_diverse');
    }

    /**
     * Return the site's main logo URL.
     *
     * Falls back to the DIVERSE logo (theme/diverse/pix/logo.png) if no flavour
     * or admin logo is configured in Boost Union.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $parentlogo = parent::get_logo_url($maxwidth, $maxheight);
        if ($parentlogo) {
            return $parentlogo;
        }

        return $this->image_url('logo', 'theme_diverse');
    }

    /**
     * Prepend the DIVERSE logo to the standard footer output.
     *
     * @return string
     */
    public function standard_footer_html() {
        $html = parent::standard_footer_html();

        $logourl = $this->image_url('logo', 'theme_diverse')->out(false);
        $brandlink = \html_writer::link(
            new \moodle_url('/'),
            \html_writer::empty_tag('img', [
                'src' => $logourl,
                'alt' => 'DIVERSE',
                'class' => 'diverse-platform-footer-logo',
            ]),
            ['class' => 'diverse-platform-footer-brand']
        );

        return \html_writer::div($brandlink, 'diverse-platform-footer-brand-wrap mb-3') . $html;
    }

    /**
     * Render the search box in the navbar.
     *
     * Provides a clean, modern course search bar in the navbar.
     *
     * @param bool $id Optional id.
     * @return string HTML
     */
    public function search_box($id = false) {
        $action = new \moodle_url('/course/search.php');
        $data = [
            'action' => $action->out(false),
            'inputname' => 'search',
            'searchstring' => get_string('searchcourses', 'core'),
        ];
        return $this->render_from_template('theme_diverse/navbar_search', $data);
    }
}

