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
// ... Partner login pages.
$string['partnerlogin'] = 'Anmeldeseiten der Partner';
$string['partnerlogin_desc'] = 'Das Feld neben dem Anmeldeformular zeigt ein Foto, einen Namen und einen Satz: das von DIVERSE, solange kein Partner gewählt ist, und das einer Partnerhochschule, wenn ein Besucher sie im Menü "Select Partner" wählt. Ohne Foto wird das DIVERSE-Muster gezeigt. Das Logo eines Partners ersetzt das DIVERSE-Logo im Anmeldeformular; ohne eigenes Logo wird das Logo verwendet, das die Hochschule für ihre Nutzer hochgeladen hat (Mandant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Auf der Anmeldeseite sind noch keine Partnerhochschulen aufgeführt.';
$string['loginphoto'] = 'Anmeldefoto: {$a}';
$string['loginphoto_desc'] = 'Ein Foto im Querformat, mindestens 1600 Pixel breit. Es füllt das Feld neben dem Anmeldeformular; der untere Teil wird unter dem Namen der Hochschule abgedunkelt.';
$string['loginlogo'] = 'Anmeldelogo: {$a}';
$string['loginlogo_desc'] = 'Wird oben im Anmeldeformular gezeigt, wenn diese Hochschule gewählt ist. Am besten eignet sich ein breites Logo auf transparentem oder weißem Hintergrund (PNG oder SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Über uns';
$string['nav_partners'] = 'Partner';
$string['nav_courses'] = 'Kurse';
$string['landing_eyebrow'] = 'Europäische Hochschulallianz';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovation, Bildung und Nachhaltigkeit für eine zukunftsfähige Gesellschaft.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) bringt Hochschulen, Innovatoren und Unternehmen zusammen, um Europas grüne und digitale Transformation voranzutreiben.';
$string['landing_getstarted'] = 'Jetzt starten';
$string['landing_meetpartners'] = 'Partnerhochschulen kennenlernen';
$string['landing_keyfigures'] = 'Zahlen und Fakten';
$string['stat_projectpartners'] = 'Projektpartner';
$string['stat_countries'] = 'Länder';
$string['stat_courses'] = 'Verfügbare Kurse';
$string['about_eyebrow'] = 'Über GT4T';
$string['about_title'] = 'Vom Wissen zur praktischen Lösung';
$string['about_intro'] = 'GT4T stärkt die Rolle der Hochschulen im grünen und digitalen Wandel, indem es Hochschulbildung mit Industrie und Innovationsökosystemen verbindet. Ziel ist es, Ideen und Wissen in praktische Lösungen für ein nachhaltigeres, inklusiveres und zukunftsfähiges Europa umzusetzen.';
$string['about_support'] = 'Gefördert durch die EIT HEI Initiative, begleitet und kofinanziert von EIT Climate-KIC und koordiniert von der Satakunta University of Applied Sciences (SAMK, Finnland).';
$string['focus_item1_title'] = 'Kreislaufwirtschaft';
$string['focus_item1_desc'] = 'Sauberere Produktion, nachhaltige Materialien und verantwortungsvoller Umgang mit Ressourcen.';
$string['focus_item2_title'] = 'Digitale Transformation';
$string['focus_item2_desc'] = 'Kompetenzen in künstlicher Intelligenz, Datenanalyse, Automatisierung und intelligenter Fertigung.';
$string['focus_item3_title'] = 'Energiewende';
$string['focus_item3_desc'] = 'Innovation und Unternehmertum für Europas Weg zu sauberer Energie.';
$string['partners_eyebrow'] = 'Partner';
$string['partners_title'] = 'Wer bei GT4T mitwirkt';
$string['partners_intro'] = 'Hochschulen, Innovationszentren und Unternehmen aus ganz Europa arbeiten im Projekt zusammen.';
$string['partners_lead'] = 'Konsortialführer';
$string['courses_title'] = 'Kurse entdecken';
$string['courses_all'] = 'Alle {$a} Kurse';
$string['footer_about'] = 'DIVERSE European University Alliance: Innovation, Bildung und Nachhaltigkeit für eine zukunftsfähige Gesellschaft.';
$string['footer_explore'] = 'Entdecken';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
$string['footer_platform'] = 'Plattform';
$string['footer_privacy'] = 'Datenschutzübersicht';

// Login page brand panel.
$string['login_intro'] = 'Gemeinsame Weiterbildungen und praxisnahe Innovationsprojekte unserer Partnerhochschulen an einem Ort.';
$string['login_partner_intro'] = 'Melden Sie sich mit Ihrem Konto der {$a} an.';
$string['login_partner_member'] = 'Mitglied der DIVERSE European University Alliance';
$string['signup_intro'] = 'Erstellen Sie Ihr Konto und nehmen Sie an den gemeinsamen Kursen der DIVERSE-Partnerhochschulen teil.';
$string['signup_partner_intro'] = 'Erstellen Sie Ihr Konto der {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Hauptnavigation';
$string['messages_select'] = 'Wählen Sie eine Unterhaltung aus, um sie hier zu lesen.';
$string['mycourses_all'] = 'Alle';
$string['mycourses_notstarted'] = 'Nicht begonnen';
$string['mycourses_inprogress'] = 'In Bearbeitung';
$string['mycourses_completed'] = 'Abgeschlossen';
$string['mycourses_favourites'] = 'Favoriten';
$string['mycourses_none'] = 'Hier ist noch nichts.';
$string['card_favourite'] = '{$a} zu Favoriten hinzufügen';
$string['card_unfavourite'] = '{$a} aus Favoriten entfernen';
$string['dash_kpi_courses'] = 'Belegte Kurse';
$string['dash_kpi_completed'] = 'Abgeschlossen';
$string['dash_kpi_due'] = 'Fällig diese Woche';
$string['dash_kpi_progress'] = 'Durchschnittlicher Fortschritt';
$string['dash_activity'] = 'Deine Aktivität diese Woche';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Diese Woche noch keine Aktivität.';
$string['dash_hero_next'] = 'Als Nächstes: {$a}';
$string['steps_eyebrow'] = 'So funktioniert es';
$string['steps_title'] = 'In drei Schritten loslernen';
$string['step1_title'] = 'Hochschule wählen';
$string['step1_desc'] = 'Wähle auf der Anmeldeseite deine Partnerhochschule oder GT4T.';
$string['step2_title'] = 'Mit deinem Konto anmelden';
$string['step2_desc'] = 'Nutze das Konto deiner Hochschule oder lege ein neues an.';
$string['step3_title'] = 'Dort weitermachen, wo du aufgehört hast';
$string['step3_desc'] = 'Dein Dashboard zeigt die nächste Aktivität, Fristen und Bewertungen.';
$string['cta_title'] = 'Bereit zum Lernen?';
$string['cta_text'] = 'Kurse von {$a->partners} Partnern aus {$a->countries} Ländern an einem Ort.';
$string['nav_discover'] = 'Entdecken';
$string['nav_guide'] = 'Plattform-Leitfaden';
$string['nav_search'] = 'Was möchten Sie lernen?';
$string['dash_resume'] = 'Weiterlernen';
$string['dash_recommended'] = 'Weitere Kurse für Sie';
$string['dash_allcategories'] = 'Alle Kurse';
$string['card_activities'] = 'Aktivitäten: {$a}';
$string['card_people'] = 'Personen: {$a}';
$string['card_enrolled'] = 'Eingeschrieben';
$string['card_open'] = 'Offene Einschreibung';
$string['lesson_previous'] = 'Zurück';
$string['lesson_next'] = 'Weiter';
$string['shell_mypartner'] = 'Mein Partner';
$string['shell_siteadmin'] = 'Website-Verwaltung';
$string['dash_hello'] = 'Willkommen zurück, {$a}';
$string['dash_summary_overdue'] = 'Überfällig: {$a}. Beginnen Sie hier.';
$string['dash_summary_grading'] = 'Abgaben, die auf Ihre Bewertung warten: {$a}.';
$string['dash_summary_todo'] = 'In den nächsten 30 Tagen fällig: {$a}.';
$string['dash_summary_clear'] = 'Derzeit ist nichts fällig.';
$string['dash_lastvisit'] = 'letzter Besuch vor {$a}';
$string['dash_progress'] = '{$a} % abgeschlossen';
$string['dash_todo'] = 'Zu erledigen';
$string['dash_todo_empty'] = 'Derzeit gibt es nichts zu erledigen.';
$string['dash_overdue'] = 'Überfällig';
$string['dash_thisweek'] = 'Diese Woche';
$string['dash_later'] = 'Später';
$string['dash_allwork'] = 'Alle {$a} Einträge ansehen';
$string['dash_recentgrades'] = 'Neueste Bewertungen';
$string['dash_allgrades'] = 'Alle Bewertungen';
$string['dash_nogrades'] = 'Ihre Bewertungen und Rückmeldungen erscheinen hier.';
$string['dash_allcourses'] = 'Alle Kurse';
$string['dash_nocourses'] = 'Sie sind noch in keinem Kurs eingeschrieben.';
$string['dash_teaching'] = 'Meine Lehre';
$string['dash_needsgrading'] = 'Warten auf Bewertung';
$string['dash_course_students'] = '{$a} Studierende';
$string['dash_stat_teaching'] = 'Ihre Kurse';
$string['dash_stat_students'] = 'Studierende';
$string['dash_stat_waiting'] = 'Zu bewertende Abgaben';
$string['dash_site'] = 'Überblick über die Website';
$string['dash_stat_users'] = 'Aktive Nutzer';
$string['dash_stat_recent'] = 'In den letzten 7 Tagen aktiv';
$string['dash_stat_courses'] = 'Sichtbare Kurse';
$string['dash_stat_partners'] = 'Partner';
$string['dash_stat_pending'] = 'Konten warten auf Freigabe';
$string['dash_partners'] = 'Partner';
$string['dash_partner'] = 'Partner';
$string['dash_actions'] = 'Häufige Aufgaben';
$string['dash_browseusers'] = 'Nutzer durchsuchen';
$string['dash_managecourses'] = 'Kurse verwalten';
$string['dash_logs'] = 'Website-Protokolle';
$string['dash_system'] = 'System';
$string['dash_cron_ran'] = 'Geplante Aufgaben liefen vor {$a}';
$string['dash_cron_never'] = 'Geplante Aufgaben sind noch nicht gelaufen';
$string['dash_pending_review'] = 'Konten mit ausstehender Freigabe prüfen';
$string['hero_stats'] = '{$a->sections} Abschnitte, {$a->activities} Aktivitäten';
$string['hero_continue'] = 'Weiter';
$string['hero_start'] = 'Starten';

// Dashboard cards.
$string['dash_news_label'] = 'Neu';
$string['dash_news_title'] = 'Plattform-Leitfaden: So funktioniert DIVERSE';
$string['dash_news_text'] = 'Kurze Videos: Kurse finden, an Live-Sitzungen teilnehmen und den Kursassistenten fragen.';
$string['dash_news_action'] = 'Jetzt ansehen';
$string['dash_progress_title'] = 'Lernfortschritt';
$string['dash_progress_help'] = 'Aktivitäten mit Abschlussverfolgung in deinen Kursen';
$string['dash_progress_total'] = 'Aktivitäten gesamt';
$string['dash_progress_done'] = 'abgeschlossen';
$string['dash_state_completed'] = 'Abgeschlossen';
$string['dash_state_started'] = 'Begonnen';
$string['dash_state_overdue'] = 'Überfällig';
$string['dash_state_notstarted'] = 'Nicht begonnen';
$string['dash_week_help'] = 'An jedem Tag abgeschlossene Aktivitäten';
$string['dash_week_activities'] = 'Aktivitäten';
$string['dash_week_more'] = '{$a} Aktivitäten mehr als letzte Woche';
$string['dash_week_fewer'] = '{$a} Aktivitäten weniger als letzte Woche';
$string['dash_week_same'] = 'So viele Aktivitäten wie letzte Woche';
$string['dash_deadlines'] = 'Fristen';
$string['dash_due_in'] = 'Fällig in {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'Nr.';
$string['dash_col_progress'] = 'Fortschritt';
$string['dash_col_next'] = 'Als Nächstes';
$string['dash_col_teacher'] = 'Lehrende';
$string['dash_notvisited'] = 'noch nicht besucht';
$string['dash_goal_title'] = 'Wochenziel';
$string['dash_goal_help'] = 'Wie viele Aktivitäten du jede Woche abschließen willst';
$string['dash_goal_change'] = 'Ziel ändern';
$string['dash_goal_choice'] = '{$a} pro Woche';
$string['dash_goal_this'] = 'Aktivitäten diese Woche';
$string['dash_goal_reached'] = 'Ziel erreicht!';
$string['dash_goal_keep'] = 'Weiter so!';
$string['dash_goal_left'] = 'noch {$a}';
$string['dash_goal_weeks'] = '{$a} Wochen';
$string['dash_goal_total'] = 'Insgesamt abgeschlossen';
$string['dash_goal_average'] = 'Wochenschnitt';
$string['dash_goal_streak'] = 'Serie';
$string['dash_nothing_to_grade'] = 'Nichts wartet auf eine Bewertung.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Öffnen';

// My courses: search, order and view.
$string['mycourses_search'] = 'Meine Kurse durchsuchen';
$string['mycourses_nomatch'] = 'Kein Kurs passt zu deiner Suche.';
$string['mycourses_lastvisited'] = 'Zuletzt besucht';
$string['mycourses_view'] = 'Ansicht';
$string['mycourses_cards'] = 'Karten';
$string['mycourses_list'] = 'Liste';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'Als Nächstes';
$string['course_aside'] = 'Dieser Kurs';
$string['course_live_next'] = 'Nächste Live-Sitzung';
$string['course_live_room'] = 'Live-Raum';
$string['course_live_none'] = 'Noch keine Live-Sitzung geplant.';
$string['course_live_join'] = 'Live-Sitzung beitreten';
$string['course_nodeadlines'] = 'In diesem Kurs ist nichts fällig.';
$string['course_assistant'] = 'Kursassistent';
$string['course_assistant_text'] = 'Frag nach Texten, Fristen oder einem Wort, das du nicht kennst.';
$string['course_assistant_open'] = 'Assistent fragen';
$string['course_teachers'] = 'Lehrende';
$string['course_message'] = 'Nachricht an {$a} senden';

// Colour palette: the switch in the top bar.
$string['palette'] = 'Farben';
$string['palette_orange'] = 'Orange';
$string['palette_green'] = 'Grün';

// My courses: the line under the title.
$string['mycourses_summary'] = '{$a->courses} Kurse · {$a->inprogress} begonnen';

// Dashboard cards: time, row menu, overdue button.
$string['dash_goal_timeweek'] = 'Zeit diese Woche';
$string['dash_goal_time'] = '{$a->h} Std. {$a->m} Min.';
$string['dash_opencourse'] = 'Kurs öffnen';
$string['dash_submit'] = 'Abgeben';

// Course page: progress of a section.
$string['course_section_done'] = '{$a->done} von {$a->total} erledigt';

// Login page: the way back to the public site.
$string['login_back'] = 'Zurück zur Startseite';

// Site administration: the tiles above the menu.
$string['admin_shortcuts'] = 'Schnellzugriff';

// The rail: its logo shows and hides the names of the places.
$string['rail_expand'] = 'Menünamen anzeigen';
$string['privacy:metadata:preference:railopen'] = 'Ob der Benutzer das Menü links geöffnet hält, mit den Namen der Bereiche neben ihren Symbolen.';

// Colour palette (user menu).
$string['palette_togreen'] = 'Grüne Farben (GT4T)';
$string['palette_toorange'] = 'Orange Farben (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'Das Design DIVERSE speichert keine personenbezogenen Daten über Nutzer/innen.';
$string['privacy:metadata:preference:palette'] = 'Die Farbpalette, die die Person für die Plattform gewählt hat.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'Das Wochenziel, das die Person im Dashboard gewählt hat: wie viele Aktivitäten pro Woche.';
