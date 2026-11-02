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
 * Theme DIVERSE - Romanian language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE este tema platformei de învățare a DIVERSE European University: o temă copil Boost Union cu paleta de culori și tipografia DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'Setări temă DIVERSE (temă copil Boost Union).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Moștenire';
$string['inheritanceinherit'] = 'Moștenire';
$string['inheritanceduplicate'] = 'Duplicare';
$string['inheritanceoptionsexplanation'] = 'În cele mai multe cazuri, moștenirea funcționează perfect. Dacă întâmpinați probleme cu funcțiile Boost Union, schimbați această setare în \'Duplicare\' și raportați problema pe GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Moștenire Pre-SCSS';
$string['prescssinheritancesetting_desc'] = 'Această setare controlează dacă codul Pre-SCSS de la Boost Union trebuie moștenit sau duplicat.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Moștenire Extra-SCSS';
$string['extrascssinheritancesetting_desc'] = 'Această setare controlează dacă codul Extra-SCSS de la Boost Union trebuie moștenit sau duplicat.';
// ... Partner login pages.
$string['partnerlogin'] = 'Paginile de autentificare ale partenerilor';
$string['partnerlogin_desc'] = 'Panoul de lângă formularul de autentificare afișează o fotografie, un nume și o propoziție: ale DIVERSE cât timp nu este ales niciun partener și ale unei universități partenere când un vizitator o alege din meniul "Select Partner". Fără fotografie se afișează modelul DIVERSE. Sigla partenerului înlocuiește sigla DIVERSE din formular; dacă lipsește, se folosește sigla încărcată de universitate pentru utilizatorii săi (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Pe pagina de autentificare nu există încă universități partenere.';
$string['loginphoto'] = 'Fotografie de autentificare: {$a}';
$string['loginphoto_desc'] = 'O fotografie orizontală, lată de cel puțin 1600 de pixeli. Umple panoul de lângă formularul de autentificare; partea de jos este întunecată sub numele universității.';
$string['loginlogo'] = 'Siglă de autentificare: {$a}';
$string['loginlogo_desc'] = 'Afișată în partea de sus a formularului de autentificare când este aleasă această universitate. Cel mai bine funcționează o siglă orizontală pe fundal transparent sau alb (PNG sau SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Despre';
$string['nav_partners'] = 'Parteneri';
$string['nav_courses'] = 'Cursuri';
$string['landing_eyebrow'] = 'Alianța Europeană a Universităților';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Inovație, educație și sustenabilitate pentru un viitor rezilient.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) reunește universități, inovatori și companii pentru a susține transformarea verde și digitală a Europei.';
$string['landing_getstarted'] = 'Începe';
$string['landing_meetpartners'] = 'Cunoaște partenerii';
$string['landing_keyfigures'] = 'Cifre-cheie';
$string['stat_projectpartners'] = 'Parteneri de proiect';
$string['stat_countries'] = 'Țări';
$string['stat_courses'] = 'Cursuri disponibile';
$string['about_eyebrow'] = 'Despre GT4T';
$string['about_title'] = 'De la cunoaștere la soluții practice';
$string['about_intro'] = 'GT4T consolidează rolul universităților în tranziția verde și digitală, conectând învățământul superior cu industria și ecosistemele de inovare. Misiunea sa este de a transforma ideile și cunoștințele în soluții practice pentru o Europă mai sustenabilă, mai incluzivă și pregătită pentru viitor.';
$string['about_support'] = 'Susținut de EIT HEI Initiative, îndrumat și cofinanțat de EIT Climate-KIC și coordonat de Satakunta University of Applied Sciences (SAMK, Finlanda).';
$string['focus_item1_title'] = 'Economie circulară';
$string['focus_item1_desc'] = 'Producție mai curată, materiale sustenabile și utilizarea responsabilă a resurselor.';
$string['focus_item2_title'] = 'Transformare digitală';
$string['focus_item2_desc'] = 'Competențe în inteligență artificială, analiza datelor, automatizare și producție inteligentă.';
$string['focus_item3_title'] = 'Tranziție energetică';
$string['focus_item3_desc'] = 'Inovare și antreprenoriat care sprijină trecerea Europei la energie curată.';
$string['partners_eyebrow'] = 'Parteneri';
$string['partners_title'] = 'Cine participă la GT4T';
$string['partners_intro'] = 'Universități, centre de inovare și companii din întreaga Europă lucrează împreună în proiect.';
$string['partners_lead'] = 'Partener coordonator';
$string['courses_title'] = 'Explorează cursuri';
$string['courses_all'] = 'Toate cele {$a} cursuri';
$string['footer_about'] = 'Alianța Europeană a Universităților DIVERSE: inovație, educație și sustenabilitate pentru un viitor rezilient.';
$string['footer_platform'] = 'Platformă';
$string['footer_privacy'] = 'Rezumat confidențialitate';

// Login page brand panel.
$string['login_intro'] = 'Formări comune și proiecte de inovare practice de la universitățile noastre partenere, într-un singur loc.';
$string['login_partner_intro'] = 'Autentifică-te cu contul tău {$a}.';
$string['login_partner_member'] = 'Membru al Alianței Universitare Europene DIVERSE';
$string['signup_intro'] = 'Creează-ți contul și alătură-te cursurilor comune ale universităților partenere DIVERSE.';
$string['signup_partner_intro'] = 'Creează-ți contul {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Navigare principală';
$string['messages_select'] = 'Selectați o conversație pentru a o citi aici.';
$string['nav_discover'] = 'Descoperă';
$string['nav_search'] = 'Ce vrei să înveți?';
$string['dash_resume'] = 'Continuă să înveți';
$string['dash_recommended'] = 'Mai multe cursuri pentru tine';
$string['dash_allcategories'] = 'Toate cursurile';
$string['card_activities'] = 'Activități: {$a}';
$string['card_people'] = 'Persoane: {$a}';
$string['card_enrolled'] = 'Înscris';
$string['card_open'] = 'Înscriere deschisă';
$string['lesson_previous'] = 'Anterior';
$string['lesson_next'] = 'Următor';
$string['shell_mypartner'] = 'Partenerul meu';
$string['shell_siteadmin'] = 'Administrarea site-ului';
$string['dash_hello'] = 'Bine ai revenit, {$a}';
$string['dash_summary_overdue'] = 'Întârziate: {$a}. Începe de aici.';
$string['dash_summary_grading'] = 'Predări care așteaptă nota ta: {$a}.';
$string['dash_summary_todo'] = 'Termen în următoarele 30 de zile: {$a}.';
$string['dash_summary_clear'] = 'Nu este nimic de predat momentan.';
$string['dash_lastvisit'] = 'ultima vizită acum {$a}';
$string['dash_progress'] = '{$a}% finalizat';
$string['dash_todo'] = 'De făcut';
$string['dash_todo_empty'] = 'Nu este nimic de făcut momentan.';
$string['dash_overdue'] = 'Întârziate';
$string['dash_thisweek'] = 'Săptămâna aceasta';
$string['dash_later'] = 'Mai târziu';
$string['dash_allwork'] = 'Vezi toate cele {$a} elemente';
$string['dash_recentgrades'] = 'Note recente';
$string['dash_allgrades'] = 'Toate notele';
$string['dash_nogrades'] = 'Notele și feedbackul tău vor apărea aici.';
$string['dash_allcourses'] = 'Toate cursurile';
$string['dash_nocourses'] = 'Nu ești încă înscris la niciun curs.';
$string['dash_teaching'] = 'Cursurile mele';
$string['dash_needsgrading'] = 'Așteaptă notare';
$string['dash_course_students'] = '{$a} studenți';
$string['dash_stat_teaching'] = 'Cursuri pe care le predai';
$string['dash_stat_students'] = 'Studenți';
$string['dash_stat_waiting'] = 'Predări de notat';
$string['dash_site'] = 'Prezentare generală a site-ului';
$string['dash_stat_users'] = 'Utilizatori activi';
$string['dash_stat_recent'] = 'Activi în ultimele 7 zile';
$string['dash_stat_courses'] = 'Cursuri vizibile';
$string['dash_stat_partners'] = 'Parteneri';
$string['dash_stat_pending'] = 'Conturi care așteaptă aprobarea';
$string['dash_partners'] = 'Parteneri';
$string['dash_partner'] = 'Partener';
$string['dash_actions'] = 'Sarcini frecvente';
$string['dash_browseusers'] = 'Răsfoiește utilizatorii';
$string['dash_managecourses'] = 'Gestionează cursurile';
$string['dash_logs'] = 'Jurnalele site-ului';
$string['dash_system'] = 'Sistem';
$string['dash_cron_ran'] = 'Sarcinile programate au rulat acum {$a}';
$string['dash_cron_never'] = 'Sarcinile programate nu au rulat încă';
$string['dash_pending_review'] = 'Verifică conturile care așteaptă aprobarea';
$string['hero_stats'] = '{$a->sections} secțiuni, {$a->activities} activități';
$string['hero_continue'] = 'Continuă';
$string['hero_start'] = 'Începe';

// Privacy API.
$string['privacy:metadata'] = 'Tema DIVERSE nu stochează niciun fel de date personale despre utilizatori.';
