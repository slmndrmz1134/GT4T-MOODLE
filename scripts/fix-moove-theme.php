<?php
// Diagnose and fix Moove theme on production (CSS build, config, wwwroot).
define('CLI_SCRIPT', true);
define('IGNORE_COMPONENT_CACHE', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/upgradelib.php');
require_once($CFG->libdir . '/csslib.php');
require_once($CFG->libdir . '/outputlib.php');

echo "=== Moodle URL / proxy ===\n";
echo "wwwroot: {$CFG->wwwroot}\n";
echo "reverseproxy: " . (!empty($CFG->reverseproxy) ? 'true' : 'false') . "\n";
echo "sslproxy: " . (!empty($CFG->sslproxy) ? 'true' : 'false') . "\n";
echo "MOODLE_URL env: " . (getenv('MOODLE_URL') ?: '(not set)') . "\n";

echo "\n=== Theme config ===\n";
echo "theme (site): " . get_config('core', 'theme') . "\n";

$manager = core_plugin_manager::instance();
$moove = $manager->get_plugin_info('theme_moove');
if (!$moove) {
    echo "ERROR: theme_moove not found on disk.\n";
    exit(1);
}
echo "theme_moove status: " . $moove->get_status() . " (db v" . ($moove->versiondb ?? 'null') . ")\n";

if ($moove->get_status() !== core_plugin_manager::PLUGIN_STATUS_UPTODATE) {
    echo "\nInstalling/upgrading plugins...\n";
    upgrade_noncore(true);
    upgrade_themes();
    $manager = core_plugin_manager::instance();
    $moove = $manager->get_plugin_info('theme_moove');
    echo "theme_moove after upgrade: " . $moove->get_status() . "\n";
}

if (get_config('core', 'theme') !== 'moove') {
    echo "\nSetting default theme to moove...\n";
    set_config('theme', 'moove');
}

echo "\n=== Building Moove CSS ===\n";
$theme = theme_config::load('moove');
theme_build_css_for_themes([$theme], ['ltr', 'rtl']);
echo "CSS build done.\n";

echo "\n=== Purging caches ===\n";
cache_helper::purge_all(true);
purge_all_caches();

echo "\n=== Done ===\n";
echo "Active theme: " . get_config('core', 'theme') . "\n";
echo "If site still looks like Boost, verify wwwroot is https://thinkhub.club (not http://localhost).\n";
echo "Hard-refresh browser (Ctrl+Shift+R) or try incognito.\n";
