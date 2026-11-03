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

use theme_diverse\local\palette;
use theme_diverse\local\partners;

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
            if ($primarynav && !$primarynav->find('diverse_about', \navigation_node::TYPE_CUSTOM)) {
                $primarynav->add(
                    get_string('nav_about', 'theme_diverse'),
                    new \moodle_url('/#diverse-about'),
                    \navigation_node::TYPE_CUSTOM,
                    null,
                    'diverse_about'
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
        $colors = palette::is_green() ? palette::PATTERN_COLORS : self::PATTERN_COLORS;
        return $colors[$id % count($colors)];
    }

    /**
     * Wrap the main content with the landing page sections on the site home.
     *
     * The main content token from the parent stays in place, so the site home content
     * configured by the admin is still rendered between the two parts.
     *
     * @return string
     */
    public function main_content() {
        $maincontent = parent::main_content();
        if ($this->page->pagelayout === 'mydashboard' && $this->page->pagetype === 'my-index' && shell::applies($this->page)) {
            return $this->render_from_template('theme_diverse/dashboard', (new dashboard())->export_for_template($this)) .
                $maincontent;
        }
        if ($this->page->pagelayout === 'mycourses' && shell::applies($this->page) &&
                class_exists(\local_diverse_catalog\catalog::class)) {
            $courses = \local_diverse_catalog\catalog::mine(optional_param('filter', 'all', PARAM_ALPHA));
            $stats = $courses['stats'];
            $courses['summary'] = get_string('mycourses_summary', 'theme_diverse', (object)[
                'courses' => $stats['enrolled'],
                'inprogress' => $stats['inprogress'],
            ]);
            $courses['tiles'] = [
                ['value' => $stats['enrolled'], 'label' => get_string('dash_kpi_courses', 'theme_diverse'), 'icon' => 'fa-book'],
                ['value' => $stats['inprogress'], 'label' => get_string('mycourses_inprogress', 'theme_diverse'),
                    'icon' => 'fa-arrow-trend-up'],
                ['value' => $stats['completed'], 'label' => get_string('mycourses_completed', 'theme_diverse'),
                    'icon' => 'fa-circle-check'],
                ['value' => $stats['average'] === null ? '–' : $stats['average'] . '%',
                    'label' => get_string('dash_kpi_progress', 'theme_diverse'), 'icon' => 'fa-award'],
            ];
            return $this->render_from_template('theme_diverse/my_courses', $courses) . $maincontent;
        }
        // The course page: the sections, and next to them the live class, the deadlines, the assistant and the teachers.
        if ($this->is_course_home()) {
            $aside = $this->render_from_template('theme_diverse/course_aside',
                (new course_aside($this->page->course))->export_for_template($this));
            return \html_writer::div(\html_writer::div($maincontent, 'diverse-course-main') . $aside, 'diverse-course-layout');
        }
        // Site administration: the pages opened most as tiles above Moodle's full menu, not above search results.
        if ($this->page->pagetype === 'admin-search' && optional_param('query', '', PARAM_RAW) === '' &&
                has_capability('moodle/site:config', \context_system::instance())) {
            return $this->render_from_template('theme_diverse/admin_shortcuts', ['items' => $this->admin_shortcuts()]) .
                $maincontent;
        }
        // Messages and notifications: the page's title in the title row, the app in a card under it.
        if ($this->is_messaging_page()) {
            $message = $this->page->pagetype === 'message-index';
            $title = \html_writer::tag('header', \html_writer::tag('h1',
                get_string($message ? 'messages' : 'notifications', 'message'), ['class' => 'diverse-dash-title']),
                ['class' => 'diverse-title-bar diverse-app-title']);
            if (!$message) {
                return $title . $maincontent . $this->notification_actions();
            }
            return $title . $maincontent . \html_writer::tag(
                'p',
                get_string('messages_select', 'theme_diverse'),
                ['class' => 'diverse-message-empty']
            );
        }
        if (!$this->is_landing_page()) {
            return $maincontent;
        }

        $context = (new landing())->export_for_template($this);
        return $this->render_from_template('theme_diverse/landing_top', $context) .
            $maincontent .
            $this->render_from_template('theme_diverse/landing_bottom', $context);
    }

    /**
     * Add the class of the application layout to the pages that have the application navigation.
     *
     * @param string|array $additionalclasses
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        if (shell::applies($this->page)) {
            $additionalclasses[] = 'diverse-shell';
            if ($this->page->course->id != SITEID) {
                $additionalclasses[] = 'diverse-in-course';
            }
            if ($this->is_course_tab()) {
                $additionalclasses[] = 'diverse-course-tab';
            }
            if ($this->is_course_home()) {
                $additionalclasses[] = 'diverse-course-home';
            }
            if ($this->has_title_row()) {
                $additionalclasses[] = 'diverse-title-row';
            }
        }
        return parent::body_attributes($additionalclasses);
    }

    /**
     * Whether this is a page of the course itself (the course, its settings, participants, grades...), not of an activity.
     *
     * @return bool
     */
    protected function is_course_tab(): bool {
        return shell::applies($this->page) && $this->page->course->id != SITEID && !$this->page->cm &&
            $this->page->context->contextlevel === CONTEXT_COURSE;
    }

    /**
     * The icons of the top bar, with the palette switch and the language menu in front of them on application pages.
     *
     * @return string
     */
    public function navbar_plugin_output() {
        global $CFG;

        $output = parent::navbar_plugin_output();
        if (!shell::applies($this->page)) {
            return $output;
        }
        $green = palette::is_green();
        $return = $this->page->url->out_as_local_url(false);
        $palette = [];
        foreach ([false, true] as $choice) {
            $palette[] = [
                'label' => get_string($choice ? 'palette_green' : 'palette_orange', 'theme_diverse'),
                'url' => (new \core\url('/theme/diverse/palette.php', ['green' => (int)$choice, 'returnurl' => $return,
                    'sesskey' => sesskey()]))->out(false),
                'current' => $choice === $green,
                'green' => (int)$choice,
            ];
        }
        // The same choice as the language menu in Moodle's user menu, with the same ?lang= links.
        $languages = [];
        $translations = get_string_manager()->get_list_of_translations();
        if (count($translations) > 1 && !empty($CFG->langmenu)) {
            foreach ($translations as $code => $name) {
                $languages[] = [
                    'name' => $name,
                    'url' => (new \core\url($this->page->url, ['lang' => $code]))->out(false),
                    'current' => $code === current_language(),
                ];
            }
        }
        return $this->render_from_template('theme_diverse/navbar_tools', [
            'palette' => $palette,
            'languages' => $languages,
            'haslanguages' => !empty($languages),
            'language' => \core_text::strtoupper(explode('_', current_language())[0]),
        ]) . $output;
    }

    /**
     * Whether the page starts with its own title row, next to which the top bar's search and buttons stand.
     *
     * @return bool
     */
    protected function has_title_row(): bool {
        if (!shell::applies($this->page)) {
            return false;
        }
        return $this->page->pagetype === 'my-index' || $this->page->pagelayout === 'mycourses' ||
            $this->page->pagetype === 'local-diverse_catalog-index' || $this->is_course_tab() || $this->is_messaging_page();
    }

    /**
     * The tiles above the site administration: the pages of the plugins this site has.
     *
     * @return array label, url, icon
     */
    protected function admin_shortcuts(): array {
        $pages = [
            ['/admin/user.php', get_string('userlist', 'admin'), 'fa-users'],
            ['/user/editadvanced.php?id=-1', get_string('addnewuser'), 'fa-user-plus'],
            ['/course/management.php', get_string('coursemgmt', 'admin'), 'fa-book'],
            ['/cohort/index.php', get_string('cohorts', 'cohort'), 'fa-people-group'],
        ];
        if (\core_component::get_component_directory('tool_mutenancy')) {
            $pages[] = ['/admin/tool/mutenancy/index.php', get_string('tenants', 'tool_mutenancy'), 'fa-building'];
        }
        $pages[] = ['/admin/settings.php?section=theme_diverse', get_string('appearance', 'admin'), 'fa-palette'];
        if (\core_component::get_component_directory('mod_bigbluebuttonbn')) {
            $pages[] = ['/admin/settings.php?section=modsettingbigbluebuttonbn', get_string('modulename', 'bigbluebuttonbn'),
                'fa-video'];
        }
        if (\core_component::get_component_directory('local_diverse_assistant')) {
            $pages[] = ['/admin/settings.php?section=local_diverse_assistant',
                get_string('pluginname', 'local_diverse_assistant'), 'fa-wand-magic-sparkles'];
        }
        $pages[] = ['/admin/plugins.php', get_string('pluginsoverview', 'core_admin'), 'fa-puzzle-piece'];
        $pages[] = ['/admin/index.php', get_string('notifications', 'admin'), 'fa-bell'];

        return array_map(fn($page) => [
            'url' => (new \core\url($page[0]))->out(false),
            'label' => $page[1],
            'icon' => $page[2],
        ], $pages);
    }

    /**
     * At the top of the list of the notifications page: mark them all as read, and the notification preferences.
     *
     * The page itself only marks a notification as read when it is opened; the popover of the bell has these two.
     * Moodle's list has an empty header, which the script fills with them.
     *
     * @return string
     */
    protected function notification_actions(): string {
        global $USER;

        $markall = \html_writer::tag('button',
            \html_writer::tag('i', '', ['class' => 'fa fa-check-double', 'aria-hidden' => 'true']) . get_string('markallread'),
            ['type' => 'button', 'class' => 'btn btn-secondary', 'data-action' => 'diverse-mark-all-read']);
        $label = get_string('notificationpreferences', 'message');
        $preferences = \html_writer::link(new \core\url('/message/notificationpreferences.php'),
            \html_writer::tag('i', '', ['class' => 'fa fa-gear', 'aria-hidden' => 'true']) .
            \html_writer::span($label, 'visually-hidden'),
            ['class' => 'btn btn-secondary diverse-app-action-icon', 'title' => $label]);

        $userid = (int)$USER->id;
        $this->page->requires->js_amd_inline(<<<JS
require(['message_popup/notification_repository'], function(Repository) {
    var actions = document.querySelector('.diverse-app-actions');
    var header = document.querySelector('.notification-area .control-area > .header');
    if (!actions || !header) {
        return;
    }
    header.appendChild(actions);
    actions.hidden = false;
    var button = actions.querySelector('[data-action="diverse-mark-all-read"]');
    button.addEventListener('click', function() {
        button.disabled = true;
        Repository.markAllAsRead({useridto: {$userid}}).then(function() {
            document.querySelectorAll('.notification-area .unread').forEach(function(item) {
                item.classList.remove('unread');
            });
            document.querySelectorAll('.popover-region-notifications .count-container').forEach(function(count) {
                count.classList.add('hidden');
            });
            return null;
        }).always(function() {
            button.disabled = false;
        });
    });
});
JS);
        return \html_writer::div($markall . $preferences, 'diverse-app-actions', ['hidden' => 'hidden']);
    }

    /**
     * Whether this is the messages page or the notifications page of the application.
     *
     * @return bool
     */
    protected function is_messaging_page(): bool {
        return in_array($this->page->pagetype, ['message-index', 'message-output-popup-notifications'], true) &&
            shell::applies($this->page);
    }

    /**
     * Whether this is the main page of a course, outside edit mode: the sections with the course's column next to them.
     *
     * @return bool
     */
    protected function is_course_home(): bool {
        return $this->is_course_tab() && str_starts_with($this->page->pagetype, 'course-view-') &&
            !$this->page->user_is_editing();
    }

    /**
     * Add the application navigation at the top of the page.
     *
     * The logo leads to the public site home, which introduces the platform: Moodle points it to the dashboard,
     * which the navigation already has. Moodle takes the logo's address from the root of the breadcrumb, so the
     * link itself is changed here and the breadcrumb keeps the dashboard.
     *
     * @return string
     */
    public function standard_top_of_body_html() {
        $html = parent::standard_top_of_body_html();
        if (shell::applies($this->page)) {
            $home = json_encode((new \core\url('/', ['redirect' => 0]))->out(false));
            $this->page->requires->js_amd_inline("document.querySelectorAll('a.navbar-brand').forEach(a => { a.href = {$home}; });");
            $html .= $this->render_from_template(
                'theme_diverse/shell_nav',
                (new shell($this->page))->export_for_template($this)
            );
        }
        return $html;
    }

    /**
     * The page header, with the DIVERSE course header on every tab of a course.
     *
     * @return string
     */
    public function full_header() {
        $header = parent::full_header();
        if ($this->is_course_tab()) {
            $hero = (new course_hero($this->page->course))->export_for_template($this);
            return $this->render_from_template('theme_diverse/course_hero', $hero) . $header;
        }
        if (shell::applies($this->page) && $this->page->course->id != SITEID && $this->page->cm) {
            $bar = course_hero::lesson_bar($this->page->course, $this->page->cm);
            return $this->render_from_template('theme_diverse/lesson_bar', $bar) . $header;
        }
        return $header;
    }

    /**
     * The edit mode switch: the dashboard and My courses are built from the user's roles, not edited by hand.
     *
     * @return string
     */
    public function edit_switch() {
        if (shell::applies($this->page) && $this->page->pagetype === 'my-index') {
            return '';
        }
        return parent::edit_switch();
    }

    /** @var \core\url|null Partner logo shown on the login form instead of the usual logo (see render_login()). */
    protected $loginlogo = null;

    /**
     * Render the login form with the brand panel next to it.
     *
     * DIVERSE and every partner chosen in the "Select Partner" menu get the same panel: their photo
     * (or the DIVERSE pattern), their name and one sentence. A partner's logo replaces the logo on the form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        $partner = partners::current();
        if ($partner) {
            $this->loginlogo = partners::logo_url($partner->id);
        }
        $form = parent::render_login($form);
        $this->loginlogo = null;

        // The Privacy Policy and the Terms of Use next to the cookies notice in the footer of the form.
        $cookies = strpos($form, 'cookiesenabled_help_html');
        $after = $cookies === false ? false : strpos($form, '</button>', $cookies);
        if ($after !== false) {
            $form = substr_replace($form, $this->login_policy_links(), $after + strlen('</button>'), 0);
        }

        return $this->login_brand_panel() . $form;
    }

    /**
     * Render the signup form with the brand panel next to it, which follows the university chosen in the form.
     *
     * @param \login_signup_form $form
     * @return string
     */
    public function render_login_signup_form($form) {
        return $this->login_brand_panel('signup') . parent::render_login_signup_form($form);
    }

    /**
     * Buttons opening the Privacy Policy and the Terms of Use (local_diverse_policy), styled like the cookies notice.
     *
     * @return string Nothing if local_diverse_policy is not installed.
     */
    protected function login_policy_links(): string {
        if (!class_exists(\local_diverse_policy\policies::class)) {
            return '';
        }
        $links = '';
        foreach ([\local_diverse_policy\policies::PRIVACY, \local_diverse_policy\policies::TERMS] as $document) {
            $links .= \local_diverse_policy\policies::link($document, ['class' => 'btn btn-secondary diverse-login-policy-link']);
        }
        return $links;
    }

    /**
     * The brand panel next to the login form: the chosen partner's (or DIVERSE's) photo, name and one sentence.
     *
     * Public so that other pages with the login layout (e.g. auth_diverse_partner's forgotten password page) show the
     * same panel instead of an empty half.
     *
     * On the signup page the panel follows the university chosen in the form's "University" field.
     *
     * @param string $page 'login' or 'signup' (changes the sentence and follows the university field).
     * @return string
     */
    public function login_brand_panel(string $page = 'login'): string {
        $signup = $page === 'signup';
        $current = partners::current();
        $context = $this->login_brand_content($current, $signup);
        $context['homeurl'] = (new \core\url('/'))->out(false);

        if ($signup && partners::enabled()) {
            // Name, sentence and photo of DIVERSE (0) and of every partner, for the university field of the form.
            $brands = ['0' => $this->login_brand_content(null, true)];
            foreach (partners::all() as $partner) {
                $brands[(string)$partner->id] = $this->login_brand_content($partner, true);
            }
            $context['brandsjson'] = json_encode($brands);
            $this->page->requires->js_amd_inline(self::SIGNUP_BRAND_JS);
        }

        return $this->render_from_template('theme_diverse/login_brand', $context);
    }

    /**
     * Name, sentence and photo of the brand panel for a partner or, without one, for DIVERSE.
     *
     * @param \stdClass|null $partner
     * @param bool $signup Signup page sentence instead of the login one.
     * @return array name, intro, photourl, ispartner
     */
    protected function login_brand_content(?\stdClass $partner, bool $signup): array {
        // Without an uploaded photo the panel shows the landing page's photo, so it never falls back to a bare colour.
        $photo = partners::file_url(partners::PHOTO_AREA, $partner ? $partner->id : 0) ?? $this->image_url('hero', 'theme_diverse');
        if ($partner) {
            $intro = get_string($signup ? 'signup_partner_intro' : 'login_partner_intro', 'theme_diverse', $partner->name);
        } else {
            $intro = get_string($signup ? 'signup_intro' : 'login_intro', 'theme_diverse');
        }
        return [
            'name' => $partner ? $partner->name : get_string('landing_title', 'theme_diverse'),
            'intro' => $intro,
            'photourl' => $photo ? $photo->out(false) : null,
            'ispartner' => (bool)$partner,
        ];
    }

    /** @var string Signup page: show the brand of the university chosen in the form's "University" field. */
    const SIGNUP_BRAND_JS = <<<'JS'
(function() {
    var panel = document.querySelector('.diverse-login-brand[data-brands]');
    var field = document.querySelector('select[name="tenantid"]');
    if (!panel || !field) {
        return;
    }
    var brands = JSON.parse(panel.getAttribute('data-brands'));
    var photo = panel.querySelector('.diverse-login-photo');
    field.addEventListener('change', function() {
        var brand = brands[field.value] || brands['0'];
        panel.setAttribute('aria-label', brand.name);
        panel.querySelector('.diverse-login-brand-title').textContent = brand.name;
        panel.querySelector('.diverse-login-brand-intro').textContent = brand.intro;
        panel.classList.toggle('diverse-login-brand-photo', !!brand.photourl);
        photo.hidden = !brand.photourl;
        if (brand.photourl) {
            photo.src = brand.photourl;
        } else {
            photo.removeAttribute('src');
        }
        panel.querySelector('.diverse-login-brand-member').hidden = !brand.ispartner;
    });
})();
JS;

    /**
     * Render the "Select Partner" menu of the login page.
     *
     * Unlike the tool_mutenancy original, the button names the partner that is currently chosen and the
     * menu lists every partner, the chosen one marked, so visitors can see where they are logging in.
     *
     * @param \tool_mutenancy\output\logintenantselector $selector
     * @return string
     */
    public function render_logintenantselector(\tool_mutenancy\output\logintenantselector $selector) {
        global $DB;

        $current = partners::current();
        $partners = partners::login_list();
        if (!$current && !$partners) {
            return '';
        }

        $menu = new \core\output\action_menu();
        $menu->set_menu_trigger($current ? $current->name : get_string('login_tenant_select', 'tool_mutenancy'));
        $menu->set_boundary('window');
        $checked = new \core\output\pix_icon('i/checked', get_string('selected', 'form'));

        // The shared DIVERSE site, under the site's own name (not the partner's site name shown on this page).
        $sitename = $DB->get_field('course', 'fullname', ['category' => 0]);
        $menu->add(new \core\output\action_menu\link_secondary(
            new \core\url('/login/', ['tenant' => 0]),
            $current ? null : $checked,
            format_string($sitename)
        ));
        foreach ($partners as $partner) {
            $menu->add(new \core\output\action_menu\link_secondary(
                new \core\url('/login/', ['tenant' => $partner->idnumber]),
                ($current && $current->id == $partner->id) ? $checked : null,
                $partner->name
            ));
        }
        return \core\output\html_writer::div($this->render($menu), 'diverse-partner-select');
    }

    /**
     * Whether the current page is the site home.
     *
     * Visitors land here; logged-in users are sent to their dashboard and reach it with the
     * Home link of the primary navigation (/?redirect=0).
     *
     * @return bool
     */
    protected function is_landing_page(): bool {
        return $this->page->pagelayout === 'frontpage' &&
            $this->page->pagetype === 'site-index';
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
        // A partner university's own favicon comes first (see tenant_image_url()).
        $tenantfavicon = $this->tenant_image_url(\core\output\core_renderer::favicon());
        if ($tenantfavicon) {
            return $tenantfavicon;
        }

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
     * A partner university's own compact logo comes first (see tenant_image_url()), except on the site home, which
     * is DIVERSE's page for every visitor and always shows the DIVERSE logo. Otherwise falls back to the
     * DIVERSE logo (theme/diverse/pix/logo.png) if no flavour or admin compact logo is configured in Boost Union.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $tenantlogo = $this->is_landing_page() ? null
            : $this->tenant_image_url(\core\output\renderer_base::get_compact_logo_url($maxwidth, $maxheight), true);
        if ($tenantlogo) {
            return $tenantlogo;
        }

        $parentlogo = parent::get_compact_logo_url($maxwidth, $maxheight);
        if ($parentlogo) {
            return $parentlogo;
        }

        return $this->image_url('logo', 'theme_diverse');
    }

    /**
     * Return the site's main logo URL.
     *
     * On the login form of a chosen partner, the logo uploaded for it in this theme's settings comes first.
     * Then a partner university's own logo (see tenant_image_url()), except on the site home (always the DIVERSE logo,
     * see get_compact_logo_url()). Otherwise falls back to the DIVERSE logo
     * (theme/diverse/pix/logo.png) if no flavour or admin logo is configured in Boost Union.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        // On the login form of a chosen partner: the logo uploaded for it in the theme settings.
        if ($this->loginlogo) {
            return $this->loginlogo;
        }

        $tenantlogo = $this->is_landing_page() ? null
            : $this->tenant_image_url(\core\output\renderer_base::get_logo_url($maxwidth, $maxheight), true);
        if ($tenantlogo) {
            return $tenantlogo;
        }

        $parentlogo = parent::get_logo_url($maxwidth, $maxheight);
        if ($parentlogo) {
            return $parentlogo;
        }

        return $this->image_url('logo', 'theme_diverse');
    }

    /**
     * The given image if it is a partner university's own upload whose file is available on this site.
     *
     * tool_mutenancy lets each partner university (tenant) upload its own logo, compact logo and favicon, and serves
     * them through Moodle core's logo and favicon functions for the users of that tenant. Boost Union replaces those
     * functions with its own and never asks core, so this theme asks core directly and passes the result on only if
     * it is a tenant's image. Uploads whose file is missing on this site (e.g. lost when the site was moved) are
     * skipped, so users get the usual logo instead of a broken image.
     *
     * Logos are uploaded with empty margins as often as not, which makes them tiny in the top bar: with $trim, the
     * URL of a copy cut to the visible logo is returned instead (see partners::navbar_logo_url()).
     *
     * @param mixed $url What core returned: a pluginfile URL, another URL or false.
     * @param bool $trim Whether the image is a logo to show without its empty margins.
     * @return \core\url|null The URL if it points to an available file in a tenant context, null otherwise.
     */
    private function tenant_image_url($url, bool $trim = false): ?\core\url {
        static $checked = [];
        if (!$url instanceof \core\url || !function_exists('mutenancy_is_active') || !mutenancy_is_active()) {
            return null;
        }
        $key = $url->out(false) . ($trim ? '#trim' : '');
        if (array_key_exists($key, $checked)) {
            return $checked[$key];
        }
        $checked[$key] = null;

        // A pluginfile URL: /pluginfile.php/<contextid>/<component>/<filearea>/<size>/<revision>/<filename>.
        $path = $url->get_path();
        if (str_ends_with($path, '/pluginfile.php') && $url->get_param('file')) {
            $path .= $url->get_param('file');
        }
        $parts = explode('/', $path);
        $start = array_search('pluginfile.php', $parts, true);
        if ($start === false || count($parts) < $start + 5) {
            return null;
        }
        [$contextid, $component, $filearea] = array_slice($parts, $start + 1, 3);
        $context = \context::instance_by_id((int)$contextid, IGNORE_MISSING);
        if (!$context || $context->contextlevel == CONTEXT_SYSTEM) {
            // The site's own image, not a partner's: Boost Union decides about that one.
            return null;
        }

        $filename = rawurldecode(end($parts));
        $fs = get_file_storage();
        foreach ($fs->get_area_files($context->id, $component, $filearea, false, 'itemid, filepath, filename', false) as $file) {
            if ($file->get_filename() === $filename && $fs->get_file_system()->is_file_readable_locally_by_storedfile($file)) {
                $checked[$key] = ($trim ? partners::navbar_logo_url($file) : null) ?? $url;
                break;
            }
        }
        return $checked[$key];
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
        $catalog = shell::applies($this->page) ? shell::catalog_url() : null;
        $action = $catalog ?? new \moodle_url('/course/search.php');
        $data = [
            'action' => $action->out(false),
            'inputname' => $catalog ? 'q' : 'search',
            'searchstring' => $catalog ? get_string('nav_search', 'theme_diverse') : get_string('searchcourses', 'core'),
        ];
        return $this->render_from_template('theme_diverse/navbar_search', $data);
    }
}

