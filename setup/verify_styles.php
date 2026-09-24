<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$rev = theme_get_revision();
$url = "http://localhost/theme/styles.php/moove/{$rev}/all";
$css = file_get_contents($url);
echo "styles.php length: " . strlen($css) . PHP_EOL;
echo "precompiled length: " . strlen(file_get_contents($CFG->dirroot . '/theme/moove/style/moodle.css')) . PHP_EOL;

require_once($CFG->dirroot . '/theme/moove/lib.php');
$theme = theme_config::load('moove');
$compiler = new core_scss();
$compiler->prepend_raw_scss($theme->get_pre_scss_code());
$compiler->append_raw_scss(theme_moove_get_main_scss_content($theme));
$compiler->setImportPaths([
    $CFG->dirroot . '/theme/moove/scss/',
    $CFG->dirroot . '/theme/boost/scss/',
]);
$compiler->append_raw_scss($theme->get_extra_scss_code());
try {
    $compiled = $compiler->to_css();
    echo "full pipeline: " . (strpos($compiled, 'gt4t-hero') !== false ? 'FOUND' : 'NOT FOUND') . " len=" . strlen($compiled) . PHP_EOL;
} catch (Throwable $e) {
    echo "full pipeline FAILED: " . $e->getMessage() . PHP_EOL;
}
