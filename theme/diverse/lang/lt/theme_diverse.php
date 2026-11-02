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
 * Theme DIVERSE - Lithuanian language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE yra DIVERSE Europos universiteto mokymosi platformos tema: „Boost Union" vaiko tema su DIVERSE spalvų palete ir tipografija.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE temos nustatymai (Boost Union vaiko tema).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Paveldėjimas';
$string['inheritanceinherit'] = 'Paveldėti';
$string['inheritanceduplicate'] = 'Dubliuoti';
$string['inheritanceoptionsexplanation'] = 'Daugeliu atvejų paveldėjimas veikia puikiai. Jei susiduriate su Boost Union funkcijų problemomis, pakeiskite šį nustatymą į \'Dubliuoti\' ir praneškite apie problemą GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre-SCSS paveldėjimas';
$string['prescssinheritancesetting_desc'] = 'Šis nustatymas valdo, ar „Boost Union" Pre-SCSS kodas turi būti paveldimas, ar dubliuojamas.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra-SCSS paveldėjimas';
$string['extrascssinheritancesetting_desc'] = 'Šis nustatymas valdo, ar „Boost Union" Extra-SCSS kodas turi būti paveldimas, ar dubliuojamas.';
// ... Partner login pages.
$string['partnerlogin'] = 'Partnerių prisijungimo puslapiai';
$string['partnerlogin_desc'] = 'Skydelyje šalia prisijungimo formos rodoma nuotrauka, pavadinimas ir vienas sakinys: DIVERSE, kol partneris nepasirinktas, ir partnerio universiteto, kai lankytojas jį pasirenka meniu "Select Partner". Be nuotraukos rodomas DIVERSE raštas. Partnerio logotipas pakeičia DIVERSE logotipą prisijungimo formoje; jei jo nėra, naudojamas logotipas, kurį universitetas įkėlė savo naudotojams (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Prisijungimo puslapyje dar nėra partnerių universitetų.';
$string['loginphoto'] = 'Prisijungimo nuotrauka: {$a}';
$string['loginphoto_desc'] = 'Horizontali nuotrauka, ne mažiau kaip 1600 pikselių pločio. Ji užpildo skydelį šalia prisijungimo formos; apatinė dalis po universiteto pavadinimu patamsinama.';
$string['loginlogo'] = 'Prisijungimo logotipas: {$a}';
$string['loginlogo_desc'] = 'Rodomas prisijungimo formos viršuje, kai pasirinktas šis universitetas. Geriausiai tinka platus logotipas permatomame arba baltame fone (PNG arba SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Apie';
$string['nav_partners'] = 'Partneriai';
$string['nav_courses'] = 'Kursai';
$string['landing_eyebrow'] = 'Europos universitetų aljansas';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Inovacijos, švietimas ir tvarumas atsparios ateities labui.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) suburia universitetus, inovatorius ir įmones, kad paspartintų žaliąją ir skaitmeninę Europos transformaciją.';
$string['landing_getstarted'] = 'Pradėti';
$string['landing_meetpartners'] = 'Susipažinti su partneriais';
$string['landing_keyfigures'] = 'Pagrindiniai skaičiai';
$string['stat_projectpartners'] = 'Projekto partneriai';
$string['stat_countries'] = 'Šalys';
$string['stat_courses'] = 'Prieinami kursai';
$string['about_eyebrow'] = 'Apie GT4T';
$string['about_title'] = 'Nuo žinių iki praktinių sprendimų';
$string['about_intro'] = 'GT4T stiprina universitetų vaidmenį žaliojoje ir skaitmeninėje pertvarkoje, susiedamas aukštąjį mokslą su pramone ir inovacijų ekosistemomis. Jo misija – idėjas ir žinias paversti praktiniais sprendimais tvaresnei, įtraukesnei ir ateičiai pasirengusiai Europai.';
$string['about_support'] = 'Remia EIT HEI Initiative, kuruoja ir bendrai finansuoja EIT Climate-KIC, koordinuoja Satakunta University of Applied Sciences (SAMK, Suomija).';
$string['focus_item1_title'] = 'Žiedinė ekonomika';
$string['focus_item1_desc'] = 'Švaresnė gamyba, tvarios medžiagos ir atsakingas išteklių naudojimas.';
$string['focus_item2_title'] = 'Skaitmeninė transformacija';
$string['focus_item2_desc'] = 'Dirbtinio intelekto, duomenų analitikos, automatizavimo ir išmaniosios gamybos įgūdžiai.';
$string['focus_item3_title'] = 'Energetikos pertvarka';
$string['focus_item3_desc'] = 'Inovacijos ir verslumas, padedantys Europai pereiti prie švarios energijos.';
$string['partners_eyebrow'] = 'Partneriai';
$string['partners_title'] = 'Kas dalyvauja GT4T';
$string['partners_intro'] = 'Universitetai, inovacijų centrai ir įmonės iš visos Europos kartu dirba projekte.';
$string['partners_lead'] = 'Pagrindinis partneris';
$string['courses_title'] = 'Naršyti kursus';
$string['courses_all'] = 'Visi {$a} kursai';
$string['footer_about'] = 'DIVERSE Europos universitetų aljansas: inovacijos, švietimas ir tvarumas atsparios ateities labui.';
$string['footer_platform'] = 'Platforma';
$string['footer_privacy'] = 'Privatumo santrauka';

// Login page brand panel.
$string['login_intro'] = 'Bendri mokymai ir praktiniai inovacijų projektai iš mūsų partnerinių universitetų – vienoje vietoje.';
$string['login_partner_intro'] = 'Prisijunkite su savo {$a} paskyra.';
$string['login_partner_member'] = 'DIVERSE Europos universitetų aljanso narys';
$string['signup_intro'] = 'Susikurkite paskyrą ir prisijunkite prie bendrų DIVERSE partnerinių universitetų kursų.';
$string['signup_partner_intro'] = 'Susikurkite savo {$a} paskyrą.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Pagrindinė navigacija';
$string['nav_discover'] = 'Atrasti';
$string['nav_search'] = 'Ko norite išmokti?';
$string['dash_resume'] = 'Tęskite mokymąsi';
$string['dash_recommended'] = 'Daugiau kursų jums';
$string['dash_allcategories'] = 'Visi kursai';
$string['card_activities'] = 'Veiklų: {$a}';
$string['card_people'] = 'Žmonių: {$a}';
$string['card_enrolled'] = 'Užsiregistravote';
$string['card_open'] = 'Atvira registracija';
$string['lesson_previous'] = 'Ankstesnis';
$string['lesson_next'] = 'Kitas';
$string['shell_mypartner'] = 'Mano partneris';
$string['shell_siteadmin'] = 'Svetainės administravimas';
$string['dash_hello'] = 'Sveiki sugrįžę, {$a}';
$string['dash_summary_overdue'] = 'Vėluojančių darbų: {$a}. Pradėkite čia.';
$string['dash_summary_grading'] = 'Pateikčių, laukiančių jūsų įvertinimo: {$a}.';
$string['dash_summary_todo'] = 'Terminas per artimiausias 30 dienų: {$a}.';
$string['dash_summary_clear'] = 'Šiuo metu nieko nereikia atlikti.';
$string['dash_lastvisit'] = 'paskutinį kartą prieš {$a}';
$string['dash_progress'] = 'Atlikta {$a} %';
$string['dash_todo'] = 'Reikia atlikti';
$string['dash_todo_empty'] = 'Šiuo metu nieko nereikia atlikti.';
$string['dash_overdue'] = 'Vėluoja';
$string['dash_thisweek'] = 'Šią savaitę';
$string['dash_later'] = 'Vėliau';
$string['dash_allwork'] = 'Peržiūrėti visus ({$a})';
$string['dash_recentgrades'] = 'Naujausi įvertinimai';
$string['dash_allgrades'] = 'Visi įvertinimai';
$string['dash_nogrades'] = 'Čia bus rodomi jūsų įvertinimai ir atsiliepimai.';
$string['dash_allcourses'] = 'Visi kursai';
$string['dash_nocourses'] = 'Dar nesate užsiregistravę į jokį kursą.';
$string['dash_teaching'] = 'Mano dėstymas';
$string['dash_needsgrading'] = 'Laukia įvertinimo';
$string['dash_course_students'] = 'Studentų: {$a}';
$string['dash_stat_teaching'] = 'Jūsų dėstomi kursai';
$string['dash_stat_students'] = 'Studentai';
$string['dash_stat_waiting'] = 'Pateiktys vertinti';
$string['dash_site'] = 'Svetainės apžvalga';
$string['dash_stat_users'] = 'Aktyvūs naudotojai';
$string['dash_stat_recent'] = 'Aktyvūs per 7 dienas';
$string['dash_stat_courses'] = 'Matomi kursai';
$string['dash_stat_partners'] = 'Partneriai';
$string['dash_stat_pending'] = 'Paskyros laukia patvirtinimo';
$string['dash_partners'] = 'Partneriai';
$string['dash_partner'] = 'Partneris';
$string['dash_actions'] = 'Dažni veiksmai';
$string['dash_browseusers'] = 'Peržiūrėti naudotojus';
$string['dash_managecourses'] = 'Tvarkyti kursus';
$string['dash_logs'] = 'Svetainės žurnalai';
$string['dash_system'] = 'Sistema';
$string['dash_cron_ran'] = 'Suplanuotos užduotys vykdytos prieš {$a}';
$string['dash_cron_never'] = 'Suplanuotos užduotys dar nevykdytos';
$string['dash_pending_review'] = 'Peržiūrėti patvirtinimo laukiančias paskyras';
$string['hero_stats'] = 'Skyrių: {$a->sections}, veiklų: {$a->activities}';
$string['hero_continue'] = 'Tęsti';
$string['hero_start'] = 'Pradėti';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE tema nesaugo jokių asmens duomenų apie jokį naudotoją.';
