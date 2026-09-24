<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// ── Slider ─────────────────────────────────────────────────────────────────
set_config('slidertitle1',
    'GreenTech4Transformation (GT4T)',
    'theme_moove');

set_config('slidercontent1',
    'GT4T is a strategic initiative of DIVERSE University that brings together'
    . ' expertise in Circular Economy, Digital Transformation, Energy Transition,'
    . ' and Venture Science to accelerate the journey towards a sustainable future.',
    'theme_moove');

set_config('sliderbutton1', 'Get Started', 'theme_moove');
set_config('sliderurl1',    '/login/index.php', 'theme_moove');

// ── Marketing boxes ────────────────────────────────────────────────────────
set_config('marketingheading1', 'Circular Economy', 'theme_moove');
set_config('marketingcontent1',
    'Learn how to design out waste, keep products and materials in use,'
    . ' and regenerate natural systems through circular business models.',
    'theme_moove');

set_config('marketingheading2', 'Digital Transformation', 'theme_moove');
set_config('marketingcontent2',
    'Explore how digital technologies drive organisational change, create new'
    . ' value and enable smarter, more connected societies.',
    'theme_moove');

set_config('marketingheading3', 'Energy Transition', 'theme_moove');
set_config('marketingcontent3',
    'Understand the global shift from fossil fuels to renewable energy sources'
    . ' and discover the policies, technologies and skills needed for a net-zero world.',
    'theme_moove');

set_config('marketingheading4', 'Venture Science Center', 'theme_moove');
set_config('marketingcontent4',
    'Bridge the gap between academic research and real-world impact by turning'
    . ' scientific breakthroughs into sustainable ventures and societal solutions.',
    'theme_moove');

// ── Numbers section ────────────────────────────────────────────────────────
set_config('numberscustomtitle',
    'GT4T in Numbers',
    'theme_moove');

set_config('numberscustomdesc',
    'A growing community of learners, educators and partners united by a shared'
    . ' commitment to green and digital transformation.',
    'theme_moove');

// ── FAQ ────────────────────────────────────────────────────────────────────
set_config('faqquestion1', 'What is GT4T?', 'theme_moove');
set_config('faqanswer1',
    'GreenTech4Transformation (GT4T) is a strategic research and education'
    . ' initiative of DIVERSE University focused on circular economy, digital'
    . ' transformation, energy transition, and venture science.',
    'theme_moove');

set_config('faqquestion2', 'Who can join the GT4T programmes?', 'theme_moove');
set_config('faqanswer2',
    'GT4T programmes are open to students, professionals, entrepreneurs and'
    . ' researchers who want to develop competencies at the intersection of'
    . ' sustainability and technology.',
    'theme_moove');

set_config('faqquestion3', 'Are the courses available online?', 'theme_moove');
set_config('faqanswer3',
    'Yes. All GT4T courses are delivered through this Moodle platform, allowing'
    . ' flexible, self-paced learning accessible from anywhere in the world.',
    'theme_moove');

set_config('faqquestion4', 'How do I get a certificate?', 'theme_moove');
set_config('faqanswer4',
    'Complete all required activities and assessments within a course. Upon'
    . ' successful completion you will automatically receive a digital certificate'
    . ' issued by DIVERSE University.',
    'theme_moove');

// ── Purge caches ───────────────────────────────────────────────────────────
theme_reset_all_caches();
purge_all_caches();

echo "All frontpage content updated to English. Caches cleared.\n";
