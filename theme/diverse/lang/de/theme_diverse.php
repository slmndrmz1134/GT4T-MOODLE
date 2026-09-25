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
 * Theme DIVERSE - German language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE ist das Design der Lernplattform der DIVERSE European University: ein Child-Theme von Boost Union mit der Farbpalette und Typografie von DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE Theme-Einstellungen (Boost Union Child-Theme).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Vererbung';
$string['inheritanceinherit'] = 'Vererben';
$string['inheritanceduplicate'] = 'Duplizieren';
$string['inheritanceoptionsexplanation'] = 'Meistens funktioniert die Vererbung einwandfrei. Sollte es dennoch zu Problemen mit Boost Union-Funktionen kommen, die einfache SCSS-Vererbung verhindern, wechseln Sie diese Einstellung zu \'Duplizieren\' und melden Sie das Problem auf GitHub.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre-SCSS-Vererbung';
$string['prescssinheritancesetting_desc'] = 'Steuert, ob der Pre-SCSS-Code von Boost Union vererbt oder dupliziert werden soll.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra-SCSS-Vererbung';
$string['extrascssinheritancesetting_desc'] = 'Steuert, ob der Extra-SCSS-Code von Boost Union vererbt oder dupliziert werden soll.';

// Landing page (site home for visitors).
$string['nav_collab'] = 'Zusammenarbeit';
$string['nav_impact'] = 'Wirkung';
$string['nav_partners'] = 'Partner';
$string['nav_courses'] = 'Kurse';
$string['landing_eyebrow'] = 'Europäische Hochschulallianz';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, Bildung und Nachhaltigkeit für eine zukunftsfähige Gesellschaft.';
$string['landing_intro'] = 'Die DIVERSE European University Alliance vernetzt Partnerhochschulen, Wirtschaft und Innovationsökosysteme in ganz Europa.';
$string['landing_getstarted'] = 'Jetzt starten';
$string['landing_meetpartners'] = 'Partnerhochschulen kennenlernen';
$string['landing_keyfigures'] = 'Zahlen und Fakten';
$string['stat_partners'] = 'Partnerhochschulen';
$string['stat_courses'] = 'Verfügbare Kurse';
$string['stat_users'] = 'Registrierte Nutzer/innen';
$string['collab_eyebrow'] = 'Unsere Zusammenarbeit';
$string['collab_title'] = 'Gemeinsame Weiterbildung und praxisnahe Innovationsprojekte';
$string['collab_intro'] = 'Partnerhochschulen bieten gemeinsame Weiterbildungen und praxisorientierte Innovationsprojekte an. Gemischte, grenzüberschreitende Teams bearbeiten reale Herausforderungen von Unternehmen und regionalen Ökosystemen und verbinden:';
$string['collab_item1'] = 'Innovationsmethoden';
$string['collab_item2'] = 'Ideenvalidierung';
$string['collab_item3'] = 'Entwicklung von Wertangeboten';
$string['collab_item4'] = 'Pitch-Vorbereitung';
$string['collab_item5'] = 'Bewusstsein für frühzeitige Kommerzialisierung';
$string['collab_outro'] = 'DIVERSE schafft reproduzierbare Kooperationsmodelle mit gemeinsamen Schulungsformaten, einheitlichen Toolkits und Methoden sowie übertragbaren Lehrkonzepten.';
$string['impact_eyebrow'] = 'Langfristige Wirkung';
$string['impact_title'] = 'Nachhaltige Zusammenarbeit';
$string['impact_intro'] = 'DIVERSE fördert die strukturierte Zusammenarbeit zwischen Hochschulen und Ökosystemen durch Pilotprojekte, grenzüberschreitende Research-to-Business-Verbindungen und systematische Entrepreneurship-Ausbildung.';
$string['impact_item1_title'] = 'Pilotprojekte & Proof-of-Concepts';
$string['impact_item1_desc'] = 'Erprobung und Validierung nachhaltiger Lösungen mit Industrie- und Regionalpartnern.';
$string['impact_item2_title'] = 'Research-to-Business';
$string['impact_item2_desc'] = 'Grenzüberschreitende Brücken, die akademische Forschung in wirksame Praxis umsetzen.';
$string['impact_item3_title'] = 'Venture Science Center';
$string['impact_item3_desc'] = 'Dauerhafte Knotenpunkte für Ausbildung, Prototyping und Ökosystem-Einbindung für langfristige Wirkung und Skalierbarkeit.';
$string['partners_eyebrow'] = 'Partner';
$string['partners_title'] = 'Partnerhochschulen';
$string['partners_intro'] = 'Jede Hochschule bietet eigene Kurse an. Gemeinsame Kurse stehen Lernenden aller Partnerhochschulen offen.';
$string['partners_courses'] = '{$a} Kurse';
$string['partners_login'] = 'Anmelden';
$string['courses_title'] = 'Kurse entdecken';
$string['courses_all'] = 'Alle {$a} Kurse';
$string['footer_about'] = 'DIVERSE European University Alliance: Innovation, Bildung und Nachhaltigkeit für eine zukunftsfähige Gesellschaft.';
$string['footer_platform'] = 'Plattform';
$string['footer_privacy'] = 'Datenschutzübersicht';

// Login page brand panel.
$string['login_tagline'] = 'Grenzenlos mit Partnern lernen';
$string['login_intro'] = 'Gemeinsame Weiterbildungen und praxisnahe Innovationsprojekte unserer Partnerhochschulen an einem Ort.';

// Privacy API.
$string['privacy:metadata'] = 'Das Design DIVERSE speichert keine personenbezogenen Daten über Nutzer/innen.';
