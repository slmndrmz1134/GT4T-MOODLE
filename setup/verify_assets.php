<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$t = theme_config::load('moove');
echo "marketing1icon config: " . get_config('theme_moove', 'marketing1icon') . PHP_EOL;
echo "marketing1icon URL: " . $t->setting_file_url('marketing1icon', 'marketing1icon') . PHP_EOL;
echo "logo config: " . get_config('theme_moove', 'logo') . PHP_EOL;
echo "logo URL: " . $t->setting_file_url('logo', 'logo') . PHP_EOL;
