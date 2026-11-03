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
$string['footer_explore'] = 'Naršyti';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
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
$string['messages_select'] = 'Pasirinkite pokalbį, kad jį čia perskaitytumėte.';
$string['mycourses_all'] = 'Visi';
$string['mycourses_notstarted'] = 'Nepradėti';
$string['mycourses_inprogress'] = 'Vykdomi';
$string['mycourses_completed'] = 'Baigti';
$string['mycourses_favourites'] = 'Mėgstami';
$string['mycourses_none'] = 'Čia dar nieko nėra.';
$string['card_favourite'] = 'Pridėti {$a} prie mėgstamų';
$string['card_unfavourite'] = 'Pašalinti {$a} iš mėgstamų';
$string['dash_kpi_courses'] = 'Kursai';
$string['dash_kpi_completed'] = 'Baigti';
$string['dash_kpi_due'] = 'Šią savaitę pateikti';
$string['dash_kpi_progress'] = 'Vidutinė pažanga';
$string['dash_activity'] = 'Tavo veikla šią savaitę';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Šią savaitę veiklos dar nėra.';
$string['dash_hero_next'] = 'Toliau: {$a}';
$string['steps_eyebrow'] = 'Kaip tai veikia';
$string['steps_title'] = 'Pradėk mokytis trimis žingsniais';
$string['step1_title'] = 'Pasirink savo universitetą';
$string['step1_desc'] = 'Prisijungimo puslapyje pasirink partnerį universitetą arba GT4T.';
$string['step2_title'] = 'Prisijunk savo paskyra';
$string['step2_desc'] = 'Naudok universiteto suteiktą paskyrą arba susikurk naują.';
$string['step3_title'] = 'Tęsk ten, kur baigei';
$string['step3_desc'] = 'Skydelis rodo kitą veiklą, terminus ir įvertinimus.';
$string['cta_title'] = 'Pasiruošęs mokytis?';
$string['cta_text'] = '{$a->partners} partnerių iš {$a->countries} šalių kursai vienoje vietoje.';
$string['nav_discover'] = 'Atrasti';
$string['nav_guide'] = 'Platformos gidas';
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

// Dashboard cards.
$string['dash_news_label'] = 'Naujienos';
$string['dash_news_title'] = 'Platformos gidas: kaip veikia DIVERSE';
$string['dash_news_text'] = 'Trumpi vaizdo įrašai apie kursų paiešką, tiesiogines pamokas ir kurso asistentą.';
$string['dash_news_action'] = 'Žiūrėti';
$string['dash_progress_title'] = 'Mokymosi pažanga';
$string['dash_progress_help'] = 'Jūsų kursų veiklos, kurių atlikimas sekamas';
$string['dash_progress_total'] = 'Iš viso veiklų';
$string['dash_progress_done'] = 'atlikta';
$string['dash_state_completed'] = 'Atlikta';
$string['dash_state_started'] = 'Vykdoma';
$string['dash_state_overdue'] = 'Pavėluota';
$string['dash_state_notstarted'] = 'Nepradėta';
$string['dash_week_help'] = 'Kiekvieną dieną atliktos veiklos';
$string['dash_week_activities'] = 'Veiklos';
$string['dash_week_more'] = '{$a} veiklomis daugiau nei praėjusią savaitę';
$string['dash_week_fewer'] = '{$a} veiklomis mažiau nei praėjusią savaitę';
$string['dash_week_same'] = 'Tiek pat veiklų kaip praėjusią savaitę';
$string['dash_deadlines'] = 'Terminai';
$string['dash_due_in'] = 'Liko {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'Nr.';
$string['dash_col_progress'] = 'Pažanga';
$string['dash_col_next'] = 'Toliau';
$string['dash_col_teacher'] = 'Dėstytojas';
$string['dash_notvisited'] = 'dar neaplankyta';
$string['dash_goal_title'] = 'Savaitės tikslas';
$string['dash_goal_help'] = 'Kiek veiklų norite atlikti kas savaitę';
$string['dash_goal_change'] = 'Keisti tikslą';
$string['dash_goal_choice'] = '{$a} per savaitę';
$string['dash_goal_this'] = 'Veiklos šią savaitę';
$string['dash_goal_reached'] = 'Tikslas pasiektas!';
$string['dash_goal_keep'] = 'Taip ir toliau!';
$string['dash_goal_left'] = 'liko {$a}';
$string['dash_goal_weeks'] = '{$a} sav.';
$string['dash_goal_total'] = 'Iš viso atlikta';
$string['dash_goal_average'] = 'Savaitės vidurkis';
$string['dash_goal_streak'] = 'Serija';
$string['dash_nothing_to_grade'] = 'Nieko nereikia vertinti.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Atidaryti';

// My courses: search, order and view.
$string['mycourses_search'] = 'Ieškoti mano kursuose';
$string['mycourses_nomatch'] = 'Paieškos atitinkančių kursų nėra.';
$string['mycourses_lastvisited'] = 'Paskutinis apsilankymas';
$string['mycourses_view'] = 'Rodinys';
$string['mycourses_cards'] = 'Kortelės';
$string['mycourses_list'] = 'Sąrašas';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'Toliau';
$string['course_aside'] = 'Šis kursas';
$string['course_live_next'] = 'Kita tiesioginė pamoka';
$string['course_live_room'] = 'Tiesioginė klasė';
$string['course_live_none'] = 'Tiesioginių pamokų dar nesuplanuota.';
$string['course_live_join'] = 'Prisijungti prie pamokos';
$string['course_nodeadlines'] = 'Šiame kurse nėra artėjančių terminų.';
$string['course_assistant'] = 'Kurso asistentas';
$string['course_assistant_text'] = 'Klauskite apie skaitinius, terminus ar nežinomą žodį.';
$string['course_assistant_open'] = 'Klausti asistento';
$string['course_teachers'] = 'Dėstytojai';
$string['course_message'] = 'Siųsti žinutę: {$a}';

// Colour palette: the switch in the top bar.
$string['palette'] = 'Spalvos';
$string['palette_orange'] = 'Oranžinė';
$string['palette_green'] = 'Žalia';

// My courses: the line under the title.
$string['mycourses_summary'] = '{$a->courses} kursai · {$a->inprogress} vykdomi';

// Dashboard cards: time, row menu, overdue button.
$string['dash_goal_timeweek'] = 'Laikas šią savaitę';
$string['dash_goal_time'] = '{$a->h} val. {$a->m} min.';
$string['dash_opencourse'] = 'Atidaryti kursą';
$string['dash_submit'] = 'Pateikti';

// Course page: progress of a section.
$string['course_section_done'] = 'Atlikta {$a->done} iš {$a->total}';

// Colour palette (user menu).
$string['palette_togreen'] = 'Žalios spalvos (GT4T)';
$string['palette_toorange'] = 'Oranžinės spalvos (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE tema nesaugo jokių asmens duomenų apie jokį naudotoją.';
$string['privacy:metadata:preference:palette'] = 'Naudotojo platformai pasirinkta spalvų paletė.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'Naudotojo valdymo skydelyje pasirinktas savaitės tikslas: kiek veiklų atlikti per savaitę.';
