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
$string['footer_explore'] = 'Ontdekken';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
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
$string['messages_select'] = 'Selecteer een gesprek om het hier te lezen.';
$string['mycourses_all'] = 'Alle';
$string['mycourses_notstarted'] = 'Niet gestart';
$string['mycourses_inprogress'] = 'Bezig';
$string['mycourses_completed'] = 'Voltooid';
$string['mycourses_favourites'] = 'Favorieten';
$string['mycourses_none'] = 'Hier staat nog niets.';
$string['card_favourite'] = '{$a} toevoegen aan favorieten';
$string['card_unfavourite'] = '{$a} verwijderen uit favorieten';
$string['dash_kpi_courses'] = 'Ingeschreven cursussen';
$string['dash_kpi_completed'] = 'Voltooid';
$string['dash_kpi_due'] = 'Deze week in te leveren';
$string['dash_kpi_progress'] = 'Gemiddelde voortgang';
$string['dash_activity'] = 'Je activiteit deze week';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Deze week nog geen activiteit.';
$string['dash_hero_next'] = 'Volgende: {$a}';
$string['steps_eyebrow'] = 'Zo werkt het';
$string['steps_title'] = 'Begin in drie stappen met leren';
$string['step1_title'] = 'Kies je hogeschool';
$string['step1_desc'] = 'Kies op de inlogpagina je partnerhogeschool of GT4T.';
$string['step2_title'] = 'Log in met je account';
$string['step2_desc'] = 'Gebruik het account van je hogeschool of maak er een aan.';
$string['step3_title'] = 'Ga verder waar je gebleven was';
$string['step3_desc'] = 'Je dashboard toont je volgende activiteit, deadlines en cijfers.';
$string['cta_title'] = 'Klaar om te leren?';
$string['cta_text'] = 'Cursussen van {$a->partners} partners uit {$a->countries} landen op één plek.';
$string['nav_discover'] = 'Ontdekken';
$string['nav_guide'] = 'Platformgids';
$string['nav_search'] = 'Wat wil je leren?';
$string['dash_resume'] = 'Verder leren';
$string['dash_recommended'] = 'Meer cursussen voor jou';
$string['dash_allcategories'] = 'Alle cursussen';
$string['card_activities'] = 'Activiteiten: {$a}';
$string['card_people'] = 'Personen: {$a}';
$string['card_enrolled'] = 'Ingeschreven';
$string['card_open'] = 'Open inschrijving';
$string['lesson_previous'] = 'Vorige';
$string['lesson_next'] = 'Volgende';
$string['shell_mypartner'] = 'Mijn partner';
$string['shell_siteadmin'] = 'Sitebeheer';
$string['dash_hello'] = 'Welkom terug, {$a}';
$string['dash_summary_overdue'] = 'Te laat: {$a}. Begin hier.';
$string['dash_summary_grading'] = 'Inzendingen die op je beoordeling wachten: {$a}.';
$string['dash_summary_todo'] = 'Deadline in de komende 30 dagen: {$a}.';
$string['dash_summary_clear'] = 'Er staat op dit moment niets open.';
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

// Dashboard cards.
$string['dash_news_label'] = 'Nieuw';
$string['dash_news_title'] = 'Platformgids: zo werkt DIVERSE';
$string['dash_news_text'] = 'Korte video\'s over cursussen vinden, live lessen volgen en de cursusassistent iets vragen.';
$string['dash_news_action'] = 'Nu bekijken';
$string['dash_progress_title'] = 'Leervoortgang';
$string['dash_progress_help'] = 'Activiteiten met voltooiingsregistratie in je cursussen';
$string['dash_progress_total'] = 'Activiteiten totaal';
$string['dash_progress_done'] = 'voltooid';
$string['dash_state_completed'] = 'Voltooid';
$string['dash_state_started'] = 'Bezig';
$string['dash_state_overdue'] = 'Te laat';
$string['dash_state_notstarted'] = 'Niet begonnen';
$string['dash_week_help'] = 'Activiteiten die je elke dag voltooide';
$string['dash_week_activities'] = 'Activiteiten';
$string['dash_week_more'] = '{$a} activiteiten meer dan vorige week';
$string['dash_week_fewer'] = '{$a} activiteiten minder dan vorige week';
$string['dash_week_same'] = 'Evenveel activiteiten als vorige week';
$string['dash_deadlines'] = 'Deadlines';
$string['dash_due_in'] = 'Over {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'Nr.';
$string['dash_col_progress'] = 'Voortgang';
$string['dash_col_next'] = 'Volgende';
$string['dash_col_teacher'] = 'Docent';
$string['dash_notvisited'] = 'nog niet bezocht';
$string['dash_goal_title'] = 'Weekdoel';
$string['dash_goal_help'] = 'Hoeveel activiteiten je elke week wilt afronden';
$string['dash_goal_change'] = 'Doel wijzigen';
$string['dash_goal_choice'] = '{$a} per week';
$string['dash_goal_this'] = 'Activiteiten deze week';
$string['dash_goal_reached'] = 'Doel bereikt!';
$string['dash_goal_keep'] = 'Ga zo door!';
$string['dash_goal_left'] = 'nog {$a}';
$string['dash_goal_weeks'] = '{$a} weken';
$string['dash_goal_total'] = 'Totaal voltooid';
$string['dash_goal_average'] = 'Weekgemiddelde';
$string['dash_goal_streak'] = 'Reeks';
$string['dash_nothing_to_grade'] = 'Er wacht niets op beoordeling.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Openen';

// My courses: search, order and view.
$string['mycourses_search'] = 'Zoek in mijn cursussen';
$string['mycourses_nomatch'] = 'Geen cursus komt overeen met je zoekopdracht.';
$string['mycourses_lastvisited'] = 'Laatst bezocht';
$string['mycourses_view'] = 'Weergave';
$string['mycourses_cards'] = 'Kaarten';
$string['mycourses_list'] = 'Lijst';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'Hierna';
$string['course_aside'] = 'Deze cursus';
$string['course_live_next'] = 'Volgende live les';
$string['course_live_room'] = 'Live klaslokaal';
$string['course_live_none'] = 'Er is nog geen live les gepland.';
$string['course_live_join'] = 'Deelnemen aan de les';
$string['course_nodeadlines'] = 'Er staat niets open in deze cursus.';
$string['course_assistant'] = 'Cursusassistent';
$string['course_assistant_text'] = 'Vraag naar de teksten, de deadlines of een woord dat je niet kent.';
$string['course_assistant_open'] = 'Vraag de assistent';
$string['course_teachers'] = 'Docenten';
$string['course_message'] = 'Stuur een bericht naar {$a}';

// Colour palette: the switch in the top bar.
$string['palette'] = 'Kleuren';
$string['palette_orange'] = 'Oranje';
$string['palette_green'] = 'Groen';

// My courses: the line under the title.
$string['mycourses_summary'] = '{$a->courses} cursussen · {$a->inprogress} bezig';

// Dashboard cards: time, row menu, overdue button.
$string['dash_goal_timeweek'] = 'Tijd deze week';
$string['dash_goal_time'] = '{$a->h} u {$a->m} m';
$string['dash_opencourse'] = 'Cursus openen';
$string['dash_submit'] = 'Inleveren';

// Course page: progress of a section.
$string['course_section_done'] = '{$a->done} van {$a->total} voltooid';

// Login page: the way back to the public site.
$string['login_back'] = 'Terug naar home';

// Colour palette (user menu).
$string['palette_togreen'] = 'Groene kleuren (GT4T)';
$string['palette_toorange'] = 'Oranje kleuren (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'Het DIVERSE-thema slaat geen persoonlijke gegevens op over gebruikers.';
$string['privacy:metadata:preference:palette'] = 'Het kleurenpalet dat de gebruiker voor het platform heeft gekozen.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'Het weekdoel dat de gebruiker op het dashboard koos: hoeveel activiteiten per week.';
