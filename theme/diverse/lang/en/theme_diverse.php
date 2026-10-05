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
// ... Partner login pages.
$string['partnerlogin'] = 'Partner login pages';
$string['partnerlogin_desc'] = 'The panel next to the login form shows a photo, a name and one sentence: DIVERSE\'s while no partner is chosen, and a partner university\'s when a visitor chooses it in the "Select Partner" menu. Without a photo the DIVERSE pattern is shown. A partner\'s logo replaces the DIVERSE logo on the login form; without one, the logo the university uploaded for its users (tenant > Appearance > Logos) is used.';
$string['partnerlogin_none'] = 'No partner universities are listed on the login page yet.';
$string['loginphoto'] = 'Login photo: {$a}';
$string['loginphoto_desc'] = 'A landscape photo, at least 1600 pixels wide. It fills the panel next to the login form; the bottom part is darkened under the university\'s name.';
$string['loginlogo'] = 'Login logo: {$a}';
$string['loginlogo_desc'] = 'Shown at the top of the login form when this university is chosen. A wide logo on a transparent or white background (PNG or SVG) works best.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Landing page (site home for visitors).
$string['nav_about'] = 'About';
$string['nav_partners'] = 'Partners';
$string['nav_courses'] = 'Courses';
$string['landing_eyebrow'] = 'European University Alliance';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, education and sustainability for a resilient future.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) brings universities, innovators and businesses together to drive Europe\'s green and digital transformation.';
$string['landing_getstarted'] = 'Get started';
$string['landing_meetpartners'] = 'Meet the partners';
$string['landing_keyfigures'] = 'Key figures';
$string['stat_projectpartners'] = 'Project partners';
$string['stat_countries'] = 'Countries';
$string['stat_courses'] = 'Courses available';
$string['about_eyebrow'] = 'About GT4T';
$string['about_title'] = 'From knowledge to practical solutions';
$string['about_intro'] = 'GT4T strengthens the role of universities in the green and digital transition by connecting higher education with industry and innovation ecosystems. Its mission is to turn ideas and knowledge into practical solutions for a more sustainable, inclusive and future-ready Europe.';
$string['about_support'] = 'Supported by the EIT HEI Initiative, guided and co-funded by EIT Climate-KIC, and coordinated by Satakunta University of Applied Sciences (SAMK, Finland).';
$string['focus_item1_title'] = 'Circular economy';
$string['focus_item1_desc'] = 'Cleaner production, sustainable materials and responsible use of resources.';
$string['focus_item2_title'] = 'Digital transformation';
$string['focus_item2_desc'] = 'Skills in artificial intelligence, data analytics, automation and smart manufacturing.';
$string['focus_item3_title'] = 'Energy transition';
$string['focus_item3_desc'] = 'Innovation and entrepreneurship that support Europe\'s move to clean energy.';
$string['partners_eyebrow'] = 'Partners';
$string['partners_title'] = 'Who takes part in GT4T';
$string['partners_intro'] = 'Universities, innovation hubs and companies from across Europe work together in the project.';
$string['partners_lead'] = 'Lead partner';
$string['courses_title'] = 'Explore courses';
$string['courses_all'] = 'All {$a} courses';
$string['footer_about'] = 'The DIVERSE European University Alliance: innovation, education and sustainability for a resilient future.';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Privacy summary';

// Login page brand panel.
$string['login_intro'] = 'Joint training and hands-on innovation projects from our partner universities, in one place.';
$string['login_partner_intro'] = 'Log in with your {$a} account.';
$string['login_partner_member'] = 'Member of the DIVERSE European University Alliance';

// Privacy API.
$string['privacy:metadata'] = 'The DIVERSE theme does not store any personal data about any user.';
