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
 * Theme functions.
 *
 * @package    theme_moove
 * @copyright 2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = \core\context\system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Moove scss.
    $moovevariables = file_get_contents($CFG->dirroot . '/theme/moove/scss/moove/_variables.scss');
    $moove = file_get_contents($CFG->dirroot . '/theme/moove/scss/default.scss');
    $security = file_get_contents($CFG->dirroot . '/theme/moove/scss/moove/_security.scss');

    $lastpreset = '';
    if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_moove', 'preset', 0, '/', $filename))) {
        $lastpreset = $presetfile->get_content();
    }

    // Combine them together.
    $allscss = $moovevariables . "\n" . $scss . "\n" . $moove . "\n" . $lastpreset .    "\n" . $security;

    return $allscss;
}

/**
 * Login page background URL for the active tenant only.
 *
 * Per-tenant images are set in MuTenancy (theme_boost | loginbackgroundimage override).
 * Site-wide theme_boost login images are intentionally ignored so one university does not
 * leak to all tenants. Do not inject this URL into compiled theme SCSS (see get_extra_scss).
 *
 * @return string|null Pluginfile URL or null for the default GT4T login gradient.
 */
function theme_moove_get_login_background_url(): ?string {
    if (function_exists('mutenancy_is_active') && mutenancy_is_active()) {
        $tenantid = \tool_mutenancy\local\tenancy::get_current_tenantid();
        if ($tenantid && \tool_mutenancy\local\config::is_overridden($tenantid, 'theme_boost', 'loginbackgroundimage')) {
            $url = \tool_mutenancy\local\appearance::get_boost_setting_image_url('loginbackgroundimage', $tenantid);
            if ($url) {
                return $url->out(false);
            }
        }

        return null;
    }

    $theme = theme_config::load('moove');
    $url = $theme->setting_file_url('loginbgimg', 'loginbgimg');

    return $url ?: null;
}

/**
 * GT4T brand logo URL (theme setting or bundled pix/logo.png).
 *
 * Used on the front page so the hero and navbar always show GT4T branding,
 * independent of per-tenant logos elsewhere on the site.
 *
 * @return string
 */
function theme_moove_get_gt4t_logo_url(): string {
    global $CFG;

    $theme = theme_config::load('moove');
    $url = $theme->setting_file_url('logo', 'logo');

    if ($url) {
        return $url;
    }

    return $CFG->wwwroot . '/theme/moove/pix/logo.png';
}

/**
 * GT4T favicon URL (theme .ico setting, or logo-derived favicon.ico).
 *
 * @return \moodle_url
 */
function theme_moove_get_gt4t_favicon_url(): moodle_url {
    global $CFG;

    $theme = theme_config::load('moove');
    $custom = $theme->setting_file_url('favicon', 'favicon');

    if (!empty($custom)) {
        $urlreplace = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
        $custom = str_replace($urlreplace, '', $custom);

        return new moodle_url($custom);
    }

    return new moodle_url('/theme/moove/pix/favicon.ico');
}

/**
 * Count visible courses (excludes the site home course).
 *
 * @return int
 */
function theme_moove_count_visible_courses(): int {
    global $DB;

    return (int) $DB->count_records_select(
        'course',
        'visible = 1 AND id <> :siteid',
        ['siteid' => SITEID]
    );
}

/**
 * Count active learners for the current or whole site context.
 *
 * @param int|null $tenantid null = all non-guest users on the site
 * @return int
 */
function theme_moove_count_active_learners(?int $tenantid = null): int {
    global $DB;

    $guestid = guest_user()->id;

    if ($tenantid) {
        return (int) $DB->count_records_select(
            'user',
            'tenantid = :tenantid AND deleted = 0 AND suspended = 0 AND confirmed = 1 AND id > :guestid',
            ['tenantid' => $tenantid, 'guestid' => $guestid]
        );
    }

    return (int) $DB->count_records_select(
        'user',
        'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id > :guestid',
        ['guestid' => $guestid]
    );
}

/**
 * Count visible courses for a tenant category tree.
 *
 * @param int $categoryid Tenant root category id.
 * @return int
 */
function theme_moove_count_tenant_visible_courses(int $categoryid): int {
    global $DB;

    try {
        $category = \core_course_category::get($categoryid, MUST_EXIST, true);
        $pathlike = $category->path . '/%';

        return (int) $DB->count_records_sql(
            "SELECT COUNT(1)
               FROM {course} c
               JOIN {course_categories} cc ON cc.id = c.category
              WHERE c.visible = 1
                AND c.id <> :siteid
                AND (cc.id = :categoryid OR cc.path LIKE :pathlike)",
            [
                'siteid' => SITEID,
                'categoryid' => $categoryid,
                'pathlike' => $pathlike,
            ]
        );
    } catch (\Throwable $e) {
        return theme_moove_count_visible_courses();
    }
}

/**
 * Dynamic hero stats for the GT4T front page.
 *
 * @return array Template context keys numbersusers, numberscourses, numberspartners.
 */
function theme_moove_get_hero_stats(): array {
    global $DB;

    $tenantid = null;
    $tenant = null;
    if (function_exists('mutenancy_is_active') && mutenancy_is_active()) {
        $tenantid = (int) \tool_mutenancy\local\tenancy::get_current_tenantid();
        if ($tenantid) {
            $tenant = \tool_mutenancy\local\tenant::fetch($tenantid);
            if (!$tenant || $tenant->archived) {
                $tenantid = null;
                $tenant = null;
            }
        }
    }

    if ($tenantid && $tenant) {
        $coursecount = theme_moove_count_tenant_visible_courses((int) $tenant->categoryid);
        $usercount = theme_moove_count_active_learners($tenantid);
    } else {
        $coursecount = theme_moove_count_visible_courses();
        $usercount = theme_moove_count_active_learners(null);
    }

    if (function_exists('mutenancy_is_active') && mutenancy_is_active()) {
        $partnercount = (int) $DB->count_records('tool_mutenancy_tenant', [
            'archived' => 0,
            'loginshow' => 1,
        ]);
    } else {
        $partnercount = 0;
    }

    return [
        'numbersusers' => number_format($usercount),
        'numberscourses' => number_format($coursecount),
        'numberspartners' => number_format($partnercount),
    ];
}

/**
 * In-page navigation items for the GT4T front page (navbar + footer).
 *
 * @param bool $hasfeatures Whether the marketing / features section is shown.
 * @param bool $hasfaq Whether the FAQ section is shown.
 * @return array List of ['id' => section id, 'label' => string].
 */
function theme_moove_get_frontpage_nav_items(bool $hasfeatures, bool $hasfaq): array {
    $items = [
        [
            'id' => 'gt4t-hero',
            'label' => get_string('gt4tnavhome', 'theme_moove'),
        ],
    ];

    if ($hasfeatures) {
        $items[] = [
            'id' => 'gt4t-features',
            'label' => get_string('gt4tnavfeatures', 'theme_moove'),
        ];
    }

    $items[] = [
        'id' => 'gt4t-collaborate',
        'label' => get_string('gt4tnavcollaborate', 'theme_moove'),
    ];

    $items[] = [
        'id' => 'gt4t-impact',
        'label' => get_string('gt4tnavimpact', 'theme_moove'),
    ];

    if ($hasfaq) {
        $items[] = [
            'id' => 'gt4t-faq',
            'label' => get_string('gt4tnavfaq', 'theme_moove'),
        ];
    }

    return $items;
}

/**
 * First scroll target below the hero (for "scroll down" and defaults).
 *
 * @param array $navitems From theme_moove_get_frontpage_nav_items().
 * @return string Element id without hash.
 */
function theme_moove_get_frontpage_scroll_target(array $navitems): string {
    foreach ($navitems as $item) {
        if ($item['id'] !== 'gt4t-hero') {
            return $item['id'];
        }
    }

    return 'gt4t-collaborate';
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_extra_scss($theme) {
    // Login backgrounds are per-tenant and set in layout/login.php (inline on .gt4t-login-bg).
    // Never bake a tenant URL into compiled CSS — that would show one university on every login.

    if (!empty($theme->settings->scss)) {
        return $theme->settings->scss;
    }

    return '';
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_pre_scss($theme) {
    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['brand-primary'],
        'secondarymenucolor' => 'secondary-menu-color',
        'fontsite' => 'font-family-sans-serif',
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }

        if ($configkey == 'fontsite' && $value == 'Moodle') {
            continue;
        }

        array_map(function($target) use (&$scss, $value) {
            if ($target == 'fontsite') {
                $scss .= '$' . $target . ': "' . $value . '", sans-serif !default' .";\n";
            } else {
                $scss .= '$' . $target . ': ' . $value . ";\n";
            }
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_moove_get_precompiled_css() {
    // Precompiled moodle.css is stale and missing GT4T frontpage styles.
    // Return empty so Moodle always uses live SCSS compilation.
    return '';
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return mixed
 */
function theme_moove_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $theme = theme_config::load('moove');

    if ($context->contextlevel == CONTEXT_SYSTEM &&
        ($filearea === 'logo' || $filearea === 'loginbgimg' || $filearea == 'favicon')) {
        $theme = theme_config::load('moove');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($filearea === 'hvp') {
        return theme_moove_serve_hvp_css($args[1], $theme);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && preg_match("/^sliderimage[1-9][0-9]?$/", $filearea) !== false) {
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing1icon') {
        return $theme->setting_file_serve('marketing1icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing2icon') {
        return $theme->setting_file_serve('marketing2icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing3icon') {
        return $theme->setting_file_serve('marketing3icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing4icon') {
        return $theme->setting_file_serve('marketing4icon', $args, $forcedownload, $options);
    }

    send_file_not_found();
}

/**
 * Serves the H5P Custom CSS.
 *
 * @param string $filename The filename.
 * @param theme_config $theme The theme config object.
 *
 * @throws dml_exception
 */
function theme_moove_serve_hvp_css($filename, $theme) {
    global $CFG, $PAGE;

    require_once($CFG->dirroot.'/lib/configonlylib.php'); // For minenable_zlib_compression function.

    $PAGE->set_context(\core\context\system::instance());
    $themename = $theme->name;

    $settings = new \theme_moove\util\settings();
    $content = $settings->hvpcss;

    $md5content = md5($content);
    $md5stored = get_config('theme_moove', 'hvpccssmd5');
    if ((empty($md5stored)) || ($md5stored != $md5content)) {
        // Content changed, so the last modified time needs to change.
        set_config('hvpccssmd5', $md5content, $themename);
        $lastmodified = time();
        set_config('hvpccsslm', $lastmodified, $themename);
    } else {
        $lastmodified = get_config($themename, 'hvpccsslm');
        if (empty($lastmodified)) {
            $lastmodified = time();
        }
    }

    // Sixty days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('HTTP/1.1 200 OK');

    header('Etag: "'.$md5content.'"');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastmodified).' GMT');
    header('Expires: '.gmdate('D, d M Y H:i:s', time() + $lifetime).' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.strlen($content));
    }

    echo $content;

    die;
}
