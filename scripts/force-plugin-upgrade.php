<?php
// Force-install pending plugins (e.g. theme_moove) when `upgrade.php` exits early.
define('CLI_SCRIPT', true);
define('IGNORE_COMPONENT_CACHE', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/upgradelib.php');

$manager = core_plugin_manager::instance();

echo "=== Plugin status ===\n";
foreach (['theme_moove', 'tool_mutenancy', 'tool_mulib'] as $component) {
    $info = $manager->get_plugin_info($component);
    if (!$info) {
        echo "$component: NOT FOUND on disk\n";
        continue;
    }
    echo "$component: " . $info->get_status() . " (disk v{$info->versiondisk}, db v" . ($info->versiondb ?? 'null') . ")\n";
}

echo "\n=== Running plugin upgrade ===\n";
upgrade_noncore(true);
upgrade_themes();
cache_helper::purge_all(true);

echo "\n=== After upgrade ===\n";
$manager = core_plugin_manager::instance();
foreach (['theme_moove', 'tool_mutenancy', 'tool_mulib'] as $component) {
    $info = $manager->get_plugin_info($component);
    if (!$info) {
        echo "$component: NOT FOUND\n";
        continue;
    }
    echo "$component: " . $info->get_status() . " (db v" . ($info->versiondb ?? 'null') . ")\n";
}

echo "\nDone. Select Moove at: Site administration > Appearance > Themes > Theme selector\n";
