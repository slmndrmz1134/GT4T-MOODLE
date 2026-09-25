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
 * Theme DIVERSE - Language pack
 *
 * @package    theme_diverse
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as it is perfectly well ordered, just not alphabetically.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// General.
$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE is the theme of the DIVERSE European University learning platform: a Boost Union child theme with the DIVERSE colour palette and typography.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE theme settings (Boost Union child theme).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in DIVERSE as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Landing page (site home for visitors).
$string['nav_collab'] = 'Collaboration';
$string['nav_impact'] = 'Impact';
$string['nav_partners'] = 'Partners';
$string['nav_courses'] = 'Courses';
$string['landing_eyebrow'] = 'European University Alliance';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, education and sustainability for a resilient future.';
$string['landing_intro'] = 'The DIVERSE European University Alliance connects partner universities, industry and innovation ecosystems across Europe.';
$string['landing_getstarted'] = 'Get started';
$string['landing_meetpartners'] = 'Meet the partners';
$string['landing_keyfigures'] = 'Key figures';
$string['stat_partners'] = 'Partner universities';
$string['stat_courses'] = 'Courses available';
$string['stat_users'] = 'Registered users';
$string['collab_eyebrow'] = 'How we collaborate';
$string['collab_title'] = 'Joint training and hands-on innovation projects';
$string['collab_intro'] = 'Partner universities deliver joint training and hands-on innovation projects. Mixed, cross-border teams work on real challenges from companies and regional ecosystems, combining:';
$string['collab_item1'] = 'Innovation methodologies';
$string['collab_item2'] = 'Idea validation';
$string['collab_item3'] = 'Value proposition development';
$string['collab_item4'] = 'Pitching preparation';
$string['collab_item5'] = 'Early-stage commercialisation awareness';
$string['collab_outro'] = 'DIVERSE creates repeatable cooperation models including shared training formats, common toolkits and methodologies, and transferable delivery approaches.';
$string['impact_eyebrow'] = 'Long-term impact';
$string['impact_title'] = 'Collaboration that lasts';
$string['impact_intro'] = 'DIVERSE promotes structured collaboration between universities and ecosystems through pilots, cross-border research-to-business connections, and systematic entrepreneurship education.';
$string['impact_item1_title'] = 'Pilots & Proof-of-Concepts';
$string['impact_item1_desc'] = 'Testing and validating sustainable solutions with industry and regional partners.';
$string['impact_item2_title'] = 'Research-to-Business';
$string['impact_item2_desc'] = 'Cross-border connections that turn academic research into real-world impact.';
$string['impact_item3_title'] = 'Venture Science Center';
$string['impact_item3_desc'] = 'Permanent hubs for training, prototyping, and ecosystem engagement, ensuring long-term impact and scalability.';
$string['partners_eyebrow'] = 'Partners';
$string['partners_title'] = 'Partner universities';
$string['partners_intro'] = 'Each university offers its own courses. Shared courses are open to learners from every partner.';
$string['partners_courses'] = '{$a} courses';
$string['partners_login'] = 'Log in';
$string['courses_title'] = 'Explore courses';
$string['courses_all'] = 'All {$a} courses';
$string['footer_about'] = 'The DIVERSE European University Alliance: innovation, education and sustainability for a resilient future.';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Privacy summary';

// Login page brand panel.
$string['login_tagline'] = 'Learn with partners across borders';
$string['login_intro'] = 'Joint training and hands-on innovation projects from our partner universities, in one place.';

// Privacy API.
$string['privacy:metadata'] = 'The DIVERSE theme does not store any personal data about any user.';
