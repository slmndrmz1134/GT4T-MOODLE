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
 * Theme DIVERSE - Dutch language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE is het thema van het leerplatform van de DIVERSE European University: een child-thema van Boost Union met het kleurenpalet en de typografie van DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE thema-instellingen (Boost Union child-thema).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Overerving';
$string['inheritanceinherit'] = 'Overerven';
$string['inheritanceduplicate'] = 'Dupliceren';
$string['inheritanceoptionsexplanation'] = 'In de meeste gevallen werkt overerving probleemloos. Mocht u toch problemen ondervinden met Boost Union-functies, schakel deze instelling dan over naar \'Dupliceren\' en meld het probleem via GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre-SCSS-overerving';
$string['prescssinheritancesetting_desc'] = 'Hiermee bepaalt u of de Pre-SCSS-code van Boost Union wordt overgeërfd of gedupliceerd.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra-SCSS-overerving';
$string['extrascssinheritancesetting_desc'] = 'Hiermee bepaalt u of de Extra-SCSS-code van Boost Union wordt overgeërfd of gedupliceerd.';
// ... Partner login pages.
$string['partnerlogin'] = 'Inlogpagina\'s van partners';
$string['partnerlogin_desc'] = 'Het paneel naast het inlogformulier toont een foto, een naam en één zin: die van DIVERSE zolang er geen partner is gekozen, en die van een partnerinstelling als een bezoeker die kiest in het menu "Select Partner". Zonder foto wordt het DIVERSE-patroon getoond. Het logo van de partner vervangt het DIVERSE-logo op het inlogformulier; zonder eigen logo wordt het logo gebruikt dat de instelling voor haar gebruikers heeft geüpload (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Er staan nog geen partnerinstellingen op de inlogpagina.';
$string['loginphoto'] = 'Inlogfoto: {$a}';
$string['loginphoto_desc'] = 'Een liggende foto van minstens 1600 pixels breed. Hij vult het paneel naast het inlogformulier; het onderste deel wordt onder de naam van de instelling donkerder gemaakt.';
$string['loginlogo'] = 'Inloglogo: {$a}';
$string['loginlogo_desc'] = 'Wordt boven het inlogformulier getoond als deze instelling gekozen is. Een breed logo op een transparante of witte achtergrond (PNG of SVG) werkt het best.';

// Landing page (site home for visitors).
$string['nav_about'] = 'Over';
$string['nav_partners'] = 'Partners';
$string['nav_courses'] = 'Cursussen';
$string['landing_eyebrow'] = 'Europese Universiteitsalliantie';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovatie, onderwijs en duurzaamheid voor een veerkrachtige toekomst.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) brengt hogescholen, innovatoren en bedrijven samen om de groene en digitale transformatie van Europa te versnellen.';
$string['landing_getstarted'] = 'Aan de slag';
$string['landing_meetpartners'] = 'Ontdek de partners';
$string['landing_keyfigures'] = 'Kerncijfers';
$string['stat_projectpartners'] = 'Projectpartners';
$string['stat_countries'] = 'Landen';
$string['stat_courses'] = 'Beschikbare cursussen';
$string['about_eyebrow'] = 'Over GT4T';
$string['about_title'] = 'Van kennis naar praktische oplossingen';
$string['about_intro'] = 'GT4T versterkt de rol van hogescholen en universiteiten in de groene en digitale transitie door hoger onderwijs te verbinden met de industrie en innovatie-ecosystemen. De missie is ideeën en kennis om te zetten in praktische oplossingen voor een duurzamer, inclusiever en toekomstbestendig Europa.';
$string['about_support'] = 'Ondersteund door het EIT HEI Initiative, begeleid en medegefinancierd door EIT Climate-KIC en gecoördineerd door Satakunta University of Applied Sciences (SAMK, Finland).';
$string['focus_item1_title'] = 'Circulaire economie';
$string['focus_item1_desc'] = 'Schonere productie, duurzame materialen en verantwoord gebruik van grondstoffen.';
$string['focus_item2_title'] = 'Digitale transformatie';
$string['focus_item2_desc'] = 'Vaardigheden in kunstmatige intelligentie, data-analyse, automatisering en slimme productie.';
$string['focus_item3_title'] = 'Energietransitie';
$string['focus_item3_desc'] = 'Innovatie en ondernemerschap die de overstap van Europa naar schone energie ondersteunen.';
$string['partners_eyebrow'] = 'Partners';
$string['partners_title'] = 'Wie doet mee aan GT4T';
$string['partners_intro'] = 'Hogescholen, innovatiecentra en bedrijven uit heel Europa werken samen in het project.';
$string['partners_lead'] = 'Hoofdpartner';
$string['courses_title'] = 'Verken cursussen';
$string['courses_all'] = 'Alle {$a} cursussen';
$string['footer_about'] = 'DIVERSE Europese Universiteitsalliantie: innovatie, onderwijs en duurzaamheid voor een veerkrachtige toekomst.';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Privacysamenvatting';

// Login page brand panel.
$string['login_intro'] = 'Gezamenlijke opleidingen en praktische innovatieprojecten van onze partneruniversiteiten, op één plek.';
$string['login_partner_intro'] = 'Log in met je account van {$a}.';
$string['login_partner_member'] = 'Lid van de DIVERSE European University Alliance';
$string['signup_intro'] = 'Maak je account aan en neem deel aan de gezamenlijke cursussen van de DIVERSE-partneruniversiteiten.';
$string['signup_partner_intro'] = 'Maak je account van {$a} aan.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Hoofdnavigatie';
$string['shell_mypartner'] = 'Mijn partner';
$string['shell_siteadmin'] = 'Sitebeheer';
$string['dash_hello'] = 'Hallo, {$a}';
$string['dash_summary_overdue'] = 'Te laat: {$a}. Begin hier.';
$string['dash_summary_grading'] = 'Inzendingen die op je beoordeling wachten: {$a}.';
$string['dash_summary_todo'] = 'Deadline in de komende 30 dagen: {$a}.';
$string['dash_summary_clear'] = 'Er staat op dit moment niets open.';
$string['dash_continue'] = 'Ga verder waar je gebleven was';
$string['dash_start'] = 'Begin hier';
$string['dash_lastvisit'] = 'laatste bezoek {$a} geleden';
$string['dash_progress'] = '{$a}% voltooid';
$string['dash_todo'] = 'Te doen';
$string['dash_todo_empty'] = 'Er is op dit moment niets te doen.';
$string['dash_overdue'] = 'Te laat';
$string['dash_thisweek'] = 'Deze week';
$string['dash_later'] = 'Later';
$string['dash_allwork'] = 'Bekijk alle {$a} items';
$string['dash_recentgrades'] = 'Recente cijfers';
$string['dash_allgrades'] = 'Alle cijfers';
$string['dash_nogrades'] = 'Je cijfers en feedback verschijnen hier.';
$string['dash_allcourses'] = 'Alle cursussen';
$string['dash_nocourses'] = 'Je bent nog niet ingeschreven voor een cursus.';
$string['dash_teaching'] = 'Mijn onderwijs';
$string['dash_needsgrading'] = 'Wacht op beoordeling';
$string['dash_course_students'] = '{$a} studenten';
$string['dash_stat_teaching'] = 'Cursussen die je geeft';
$string['dash_stat_students'] = 'Studenten';
$string['dash_stat_waiting'] = 'Te beoordelen inzendingen';
$string['dash_site'] = 'Site-overzicht';
$string['dash_stat_users'] = 'Actieve gebruikers';
$string['dash_stat_recent'] = 'Actief in de afgelopen 7 dagen';
$string['dash_stat_courses'] = 'Zichtbare cursussen';
$string['dash_stat_partners'] = 'Partners';
$string['dash_stat_pending'] = 'Accounts wachten op goedkeuring';
$string['dash_partners'] = 'Partners';
$string['dash_partner'] = 'Partner';
$string['dash_actions'] = 'Veelgebruikte taken';
$string['dash_browseusers'] = 'Gebruikers bekijken';
$string['dash_managecourses'] = 'Cursussen beheren';
$string['dash_logs'] = 'Sitelogboeken';
$string['dash_system'] = 'Systeem';
$string['dash_cron_ran'] = 'Geplande taken zijn {$a} geleden uitgevoerd';
$string['dash_cron_never'] = 'Geplande taken zijn nog niet uitgevoerd';
$string['dash_pending_review'] = 'Accounts die op goedkeuring wachten bekijken';
$string['hero_stats'] = '{$a->sections} secties, {$a->activities} activiteiten';
$string['hero_continue'] = 'Verdergaan';
$string['hero_start'] = 'Starten';

// Privacy API.
$string['privacy:metadata'] = 'Het DIVERSE-thema slaat geen persoonlijke gegevens op over gebruikers.';
