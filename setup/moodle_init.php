<?php
/**
 * GT4T Moodle First-Run Initialisation
 *
 * Runs automatically on first container boot (via docker-entrypoint-custom.sh)
 * when the Moodle database has just been installed but has no customisations.
 *
 * Sets:
 *   - Site name / short name
 *   - Active theme → Moove
 *   - Brand colours (GT4T green #1DC71D)
 *   - Navbar logo (from theme/moove/pix/logo.png)
 *   - Hero slider content (English)
 *   - Four marketing boxes with SVG icons (English)
 *   - Numbers section (English)
 *   - Four FAQ items (English)
 *   - Custom SCSS overrides
 */

define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// ── 1. Site identity ────────────────────────────────────────────────────────
$DB->set_field('course', 'fullname',  'DIVERSE European University',     ['id' => SITEID]);
$DB->set_field('course', 'shortname', 'DIVERSE',                         ['id' => SITEID]);

// ── 2. Theme ────────────────────────────────────────────────────────────────
set_config('theme', 'moove');
set_config('brandcolor',         '#1DC71D', 'theme_moove');
set_config('secondarymenucolor', '#1DC71D', 'theme_moove');
set_config('displaymarketingbox', '1', 'theme_moove');
set_config('numbersfrontpage',    '1', 'theme_moove');
set_config('faqcount',            '4', 'theme_moove');

// ── 3. Logo ─────────────────────────────────────────────────────────────────
$fs         = get_file_storage();
$syscontext = context_system::instance();
$logopath   = '/var/www/html/theme/moove/pix/logo.png';

if (file_exists($logopath)) {
    $fs->delete_area_files($syscontext->id, 'theme_moove', 'logo');
    $fs->create_file_from_pathname([
        'contextid' => $syscontext->id,
        'component' => 'theme_moove',
        'filearea'  => 'logo',
        'itemid'    => 0,
        'filepath'  => '/',
        'filename'  => 'logo.png',
    ], $logopath);
    set_config('logo', '/logo.png', 'theme_moove');
    echo "[init] Logo uploaded.\n";
} else {
    echo "[init] WARNING: logo.png not found at $logopath\n";
}

// ── 4. Hero slider image ────────────────────────────────────────────────────
$imgW = 1400; $imgH = 500;
$im   = imagecreatetruecolor($imgW, $imgH);

// Gradient: dark-green → black
for ($y = 0; $y < $imgH; $y++) {
    $ratio = $y / $imgH;
    $r = (int)(20  * (1 - $ratio));
    $g = (int)(80  * (1 - $ratio));
    $b = (int)(20  * (1 - $ratio));
    $col = imagecolorallocate($im, $r, $g, $b);
    imageline($im, 0, $y, $imgW, $y, $col);
}
// Cyan accent line
$cyan = imagecolorallocate($im, 0, 200, 230);
imagefilledrectangle($im, 0, $imgH - 8, $imgW, $imgH, $cyan);
// Subtle green glow
for ($i = 20; $i > 0; $i--) {
    $alpha  = (int)(120 - $i * 4);
    $glowC  = imagecolorallocatealpha($im, 29, 199, 29, $alpha);
    imagefilledellipse($im, (int)($imgW * 0.3), (int)($imgH * 0.5), $i * 25, $i * 15, $glowC);
}

$heroFile = tempnam(sys_get_temp_dir(), 'gt4t_hero') . '.png';
imagepng($im, $heroFile);
imagedestroy($im);

$fs->delete_area_files($syscontext->id, 'theme_moove', 'sliderimage1');
$fs->create_file_from_pathname([
    'contextid' => $syscontext->id,
    'component' => 'theme_moove',
    'filearea'  => 'sliderimage1',
    'itemid'    => 0,
    'filepath'  => '/',
    'filename'  => 'gt4t_hero.png',
], $heroFile);
unlink($heroFile);
set_config('sliderimage1', '/gt4t_hero.png', 'theme_moove');
echo "[init] Hero image uploaded.\n";

// ── 5. Slider text (legacy Moove slider; hero uses frontpage.mustache) ───────
set_config('slidertitle1',
    'GreenTech4Transformation (GT4T)',
    'theme_moove');
set_config('slidercontent1',
    'A project funded by the EIT Higher Education Initiative and coordinated by'
    . ' Satakunta University of Applied Sciences (SAMK, Finland).',
    'theme_moove');
set_config('sliderbutton1', 'Get Started',      'theme_moove');
set_config('sliderurl1',    '/login/index.php', 'theme_moove');

// ── 6. Marketing section header ─────────────────────────────────────────────
set_config('marketingheading', 'Focus Areas', 'theme_moove');
set_config('marketingcontent',
    '<p>The project focuses on sustainability-driven innovation and entrepreneurship in:</p>',
    'theme_moove');

// ── 7. Marketing SVG icons ──────────────────────────────────────────────────
$icons = [
    1 => ['color' => '#1DC71D', 'path' =>
        'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z'
        . 'M10 17l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'],
    2 => ['color' => '#00C8E8', 'path' =>
        'M9 3L5 6.99h3V14h2V6.99h3L9 3zm7 14.01V10h-2v7.01h-3L15 21l4-3.99h-3z'],
    3 => ['color' => '#FFB300', 'path' =>
        'M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5'
        . ' 0 1 1 0-5 2.5 2.5 0 0 1 0 5z'],
    4 => ['color' => '#1DC71D', 'path' =>
        'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z'
        . 'M9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z'],
];

foreach ($icons as $i => $icon) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="80" height="80">
  <circle cx="12" cy="12" r="12" fill="{$icon['color']}" opacity="0.15"/>
  <path d="{$icon['path']}" fill="{$icon['color']}"/>
</svg>
SVG;
    $tmp = tempnam(sys_get_temp_dir(), "gt4t_icon{$i}") . '.svg';
    file_put_contents($tmp, $svg);
    $fs->delete_area_files($syscontext->id, 'theme_moove', "marketing{$i}icon");
    $fs->create_file_from_pathname([
        'contextid' => $syscontext->id,
        'component' => 'theme_moove',
        'filearea'  => "marketing{$i}icon",
        'itemid'    => 0,
        'filepath'  => '/',
        'filename'  => "icon{$i}.svg",
    ], $tmp);
    unlink($tmp);
    set_config("marketing{$i}icon", "/icon{$i}.svg", 'theme_moove');
}
echo "[init] Marketing icons uploaded.\n";

// ── 8. Marketing box text ───────────────────────────────────────────────────
$marketing = [
    1 => [
        'heading' => 'Circular Economy',
        'content' => '<p>Sustainability-driven innovation that designs out waste, keeps materials in use, and regenerates natural systems.</p>',
    ],
    2 => [
        'heading' => 'Digital Transformation',
        'content' => '<p>Connecting digital technologies with higher education to drive the green and digital transition across industries.</p>',
    ],
    3 => [
        'heading' => 'Energy Transition',
        'content' => '<p>Advancing the shift towards renewable energy and sustainable energy systems through research, training, and innovation.</p>',
    ],
    4 => [
        'heading' => 'Venture Science Center',
        'content' => '<p>Permanent hubs for training, prototyping, and ecosystem engagement—ensuring long-term impact and scalability.</p>',
    ],
];

foreach ($marketing as $i => $box) {
    set_config("marketing{$i}heading", $box['heading'], 'theme_moove');
    set_config("marketing{$i}content", $box['content'], 'theme_moove');
}

// ── 9. Numbers section ──────────────────────────────────────────────────────
set_config('numbersfrontpagecontent',
    '<p>Part of the DIVERSE European University Alliance, GT4T connects partner universities, industry, and innovation ecosystems across Europe.</p>',
    'theme_moove');

// ── 10. FAQ ─────────────────────────────────────────────────────────────────
$faq = [
    1 => [
        'q' => 'What is GT4T?',
        'a' => '<p>GreenTech4Transformation (GT4T) is a project funded by the EIT Higher Education Initiative and coordinated by Satakunta University of Applied Sciences (SAMK, Finland). It strengthens universities\' role in the green and digital transition by connecting higher education with industry and innovation ecosystems.</p>',
    ],
    2 => [
        'q' => 'Who can participate in GT4T programmes?',
        'a' => '<p>GT4T programmes are open to students, staff, and partners from DIVERSE alliance universities. Mixed, cross-border teams work on real challenges from companies and regional ecosystems.</p>',
    ],
    3 => [
        'q' => 'How does GT4T collaborate with industry?',
        'a' => '<p>Partner universities deliver joint training and hands-on innovation projects. Teams apply innovation methodologies, idea validation, value proposition development, pitching preparation, and early-stage commercialisation awareness to real industry challenges.</p>',
    ],
    4 => [
        'q' => 'What is the Venture Science Center?',
        'a' => '<p>Venture Science Center–type hubs provide a permanent home for training, prototyping, and ecosystem engagement. They support pilots, proof-of-concepts, cross-border research-to-business connections, and systematic entrepreneurship education.</p>',
    ],
];

foreach ($faq as $i => $item) {
    set_config("faqquestion{$i}", $item['q'], 'theme_moove');
    set_config("faqanswer{$i}",   $item['a'], 'theme_moove');
}

// ── 10. Custom SCSS overrides ───────────────────────────────────────────────
$scss = <<<'SCSS'
/* GT4T brand overrides */
:root {
    --bs-primary: #1DC71D;
    --bs-link-color: #1DC71D;
}
SCSS;

set_config('customscss', $scss, 'theme_moove');

// ── 11. Frontpage display options ───────────────────────────────────────────
set_config('frontpage',         '0', null);
set_config('frontpageloggedin', '0', null);
$DB->set_field('course', 'newsitems', 0, ['id' => SITEID]);

// ── 12. Purge caches ────────────────────────────────────────────────────────
theme_reset_all_caches();
purge_all_caches();

echo "[init] GT4T Moodle initialisation complete.\n";
