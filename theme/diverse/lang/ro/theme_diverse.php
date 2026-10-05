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

// Privacy API.
$string['privacy:metadata'] = 'Tema DIVERSE nu stochează niciun fel de date personale despre utilizatori.';
