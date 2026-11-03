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

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use theme_diverse\local\partners;

/**
 * Theme DIVERSE - Application navigation for logged-in users.
 *
 * A permanent rail of icons on the left (a tab bar at the bottom on phones) replaces Moodle's top menu, so
 * that the main places are one click away on every page. The links are built from the user's roles on each page,
 * so they must stay cheap: no course lists here.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shell implements renderable, templatable {
    /** @var string Short name of the course with the platform's how-to videos. */
    const GUIDE_SHORTNAME = 'DIVERSE-GUIDE';

    /** @var string[] Page layouts without the navigation: pages that are not part of the application. */
    const EXCLUDED_LAYOUTS = ['embedded', 'frametop', 'frontpage', 'login', 'maintenance', 'popup', 'print', 'redirect', 'secure'];

    /** @var string[] Icon class of each navigation link (see scss/icons.scss). */
    const ICONS = [
        'dashboard' => 'fa-table-cells-large',
        'discover' => 'fa-magnifying-glass',
        'courses' => 'fa-book',
        'calendar' => 'fa-calendar',
        'grades' => 'fa-award',
        'messages' => 'fa-message',
        'partner' => 'fa-building',
        'users' => 'fa-users',
        'admin' => 'fa-sliders',
        'guide' => 'fa-circle-question',
        'manage' => 'fa-gear',
    ];

    /** @var \moodle_page */
    protected $page;

    /** @var int|null Id of the guide course, if the site has one the user can open. */
    protected $guideid;

    /**
     * Constructor.
     *
     * @param \moodle_page $page
     */
    public function __construct(\moodle_page $page) {
        $this->page = $page;
        $this->guideid = self::guide_id();
    }

    /**
     * Id of the guide course, if the site has one the user can open.
     *
     * @return int|null
     */
    public static function guide_id(): ?int {
        global $DB;

        $guide = $DB->get_record('course', ['shortname' => self::GUIDE_SHORTNAME], 'id, visible');
        if ($guide && ($guide->visible || has_capability('moodle/course:viewhiddencourses', \context_course::instance($guide->id)))) {
            return (int)$guide->id;
        }
        return null;
    }

    /**
     * Whether a page shows the application navigation.
     *
     * @param \moodle_page $page
     * @return bool
     */
    public static function applies(\moodle_page $page): bool {
        return isloggedin() && !isguestuser() && !in_array($page->pagelayout, self::EXCLUDED_LAYOUTS, true);
    }

    /**
     * Address of the course catalogue, if local_diverse_catalog is installed.
     *
     * @return \core\url|null
     */
    public static function catalog_url(): ?\core\url {
        return class_exists(\local_diverse_catalog\catalog::class) ? new \core\url('/local/diverse_catalog/index.php') : null;
    }

    /**
     * Export the links.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG;

        $main = [
            $this->link('dashboard', get_string('myhome'), '/my/', ['/my/index.php']),
        ];
        $catalog = self::catalog_url();
        if ($catalog) {
            $main[] = $this->link('discover', get_string('nav_discover', 'theme_diverse'), $catalog->out_as_local_url(false), [
                '/local/diverse_catalog/',
                '/course/info.php',
            ]);
        }
        $main = array_merge($main, [
            $this->link('courses', get_string('mycourses'), '/my/courses.php', ['/my/courses.php', '/course/view.php']),
            $this->link('calendar', get_string('calendar', 'core_calendar'), '/calendar/view.php?view=month', ['/calendar/']),
            $this->link('grades', get_string('grades'), '/grade/report/mygrades.php', ['/grade/report/']) + ['nophone' => true],
        ]);
        if (!empty($CFG->messaging)) {
            $main[] = $this->link('messages', get_string('messages', 'message'), '/message/index.php', ['/message/']);
        }
        // The how-to videos. On phones the tab bar has no room left for it.
        if ($this->guideid) {
            $main[] = $this->link('guide', get_string('nav_guide', 'theme_diverse'), '/course/view.php?id=' . $this->guideid, [])
                + ['nophone' => true];
        }

        $manage = [];
        $tenant = partners::current();
        if ($tenant && has_capability('tool/mutenancy:view', \context_tenant::instance($tenant->id))) {
            $manage[] = $this->link(
                'partner',
                get_string('shell_mypartner', 'theme_diverse'),
                '/admin/tool/mutenancy/tenant.php?id=' . $tenant->id,
                ['/admin/tool/mutenancy/']
            );
        }
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $manage[] = $this->link('users', get_string('users'), '/admin/user.php', ['/admin/user.php', '/user/editadvanced.php']);
            $manage[] = $this->link(
                'admin',
                get_string('shell_siteadmin', 'theme_diverse'),
                '/admin/search.php',
                ['/admin/search.php', '/admin/settings.php', '/admin/category.php']
            );
        }

        return [
            'homeurl' => (new \core\url('/', ['redirect' => 0]))->out(false),
            'logourl' => $output->image_url('favicon', 'theme_diverse')->out(false),
            'sitename' => format_string(get_site()->shortname),
            'manageicon' => self::ICONS['manage'],
            'main' => $main,
            'manage' => $manage,
            'hasmanage' => !empty($manage),
            'managecurrent' => in_array(true, array_column($manage, 'current'), true),
        ];
    }

    /**
     * One navigation link.
     *
     * @param string $icon Key of ICONS.
     * @param string $label
     * @param string $url Site-relative URL.
     * @param string[] $paths Starts of the local page addresses on which this link is the current one.
     * @return array
     */
    protected function link(string $icon, string $label, string $url, array $paths): array {
        $current = $this->page->url->out_as_local_url(false);
        $iscurrent = false;
        foreach ($paths as $path) {
            $iscurrent = $iscurrent || str_starts_with($current, $path);
        }
        // Every tab of a course belongs to My courses (the guide to itself); the information page of another course
        // is still browsing.
        if ($this->page->course->id != SITEID && !str_starts_with($current, '/course/info.php')) {
            $iscurrent = $icon === ($this->page->course->id == $this->guideid ? 'guide' : 'courses');
        }
        return [
            'key' => $icon,
            'label' => $label,
            'url' => (new \core\url($url))->out(false),
            'icon' => self::ICONS[$icon],
            'current' => $iscurrent,
        ];
    }
}
