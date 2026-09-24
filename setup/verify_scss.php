<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/theme/moove/lib.php');

$theme = theme_config::load('moove');
$scsscontent = theme_moove_get_main_scss_content($theme);
$scsscontent .= theme_moove_get_pre_scss($theme);

$compiler = new core_scss();
$compiler->append_raw_scss($scsscontent);
$compiler->setImportPaths([
    $CFG->dirroot . '/theme/moove/scss/',
    $CFG->dirroot . '/theme/boost/scss/',
]);

try {
    $css = $compiler->to_css();
    echo (strpos($css, 'gt4t-hero') !== false ? 'Compile OK: FOUND gt4t-hero' : 'Compile OK: NOT FOUND gt4t-hero') . PHP_EOL;
    echo 'CSS length: ' . strlen($css) . PHP_EOL;
} catch (Throwable $e) {
    echo 'Compile FAILED: ' . $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
