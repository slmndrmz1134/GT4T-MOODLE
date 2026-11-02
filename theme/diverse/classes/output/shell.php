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
 * A permanent rail on the left (a tab bar at the bottom on phones) replaces Moodle's top menu, so that the
 * main places are one click away on every page. The links are built from the user's roles on each page,
 * so they must stay cheap: no course lists here.
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shell implements renderable, templatable {
    /** @var string[] Page layouts without the navigation: pages that are not part of the application. */
    const EXCLUDED_LAYOUTS = ['embedded', 'frametop', 'frontpage', 'login', 'maintenance', 'popup', 'print', 'redirect', 'secure'];

    /** @var string[] SVG path of each navigation icon. */
    const ICONS = [
        'dashboard' => 'M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z',
        'courses' => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20V2H6.5A2.5 2.5 0 0 0 4 4.5zM4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5',
        'calendar' => 'M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM16 2v4M8 2v4M3 10h18',
        'grades' => 'M12 15a7 7 0 1 0 0-14 7 7 0 0 0 0 14zM8.2 13.9 7 23l5-3 5 3-1.2-9.1',
        'messages' => 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z',
        'partner' => 'M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M10 21v-4h4v4',
        'users' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8',
        'admin' => 'M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6',
        'home' => 'M3 11l9-8 9 8M5 10v10h14V10M9 20v-6h6v6',
    ];

    /** @var \moodle_page */
    protected $page;

    /**
     * Constructor.
     *
     * @param \moodle_page $page
     */
    public function __construct(\moodle_page $page) {
        $this->page = $page;
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
     * Export the links.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG;

        $main = [
            $this->link('dashboard', get_string('myhome'), '/my/', ['/my/index.php']),
            $this->link('courses', get_string('mycourses'), '/my/courses.php', ['/my/courses.php', '/course/view.php']),
            $this->link('calendar', get_string('calendar', 'core_calendar'), '/calendar/view.php?view=month', ['/calendar/']),
            $this->link('grades', get_string('grades'), '/grade/report/mygrades.php', ['/grade/report/']),
        ];
        if (!empty($CFG->messaging)) {
            $main[] = $this->link('messages', get_string('messages', 'message'), '/message/index.php', ['/message/']);
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
            'main' => $main,
            'manage' => $manage,
            'hasmanage' => !empty($manage),
            'home' => $this->link('home', get_string('sitehome'), '/?redirect=0', []),
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
        return [
            'label' => $label,
            'url' => (new \core\url($url))->out(false),
            'icon' => self::ICONS[$icon],
            'current' => $iscurrent,
        ];
    }
}
