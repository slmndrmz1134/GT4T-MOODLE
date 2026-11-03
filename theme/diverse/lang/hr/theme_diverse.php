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
 * Theme DIVERSE - Croatian language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE je tema platforme za učenje DIVERSE European University: podtema teme Boost Union s paletom boja i tipografijom saveza DIVERSE.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'Postavke teme DIVERSE (podtema teme Boost Union).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Nasljeđivanje';
$string['inheritanceinherit'] = 'Naslijedi';
$string['inheritanceduplicate'] = 'Dupliciraj';
$string['inheritanceoptionsexplanation'] = 'U većini slučajeva nasljeđivanje funkcionira bez poteškoća. Međutim, ako naiđete na probleme s Boost Union značajkama u temi DIVERSE, promijenite ovu postavku na \'Dupliciraj\' i prijavite problem na GitHubu.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Nasljeđivanje Pre-SCSS';
$string['prescssinheritancesetting_desc'] = 'Određuje treba li pre-SCSS kod iz teme Boost Union biti naslijeđen ili dupliciran.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Nasljeđivanje Extra-SCSS';
$string['extrascssinheritancesetting_desc'] = 'Određuje treba li extra-SCSS kod iz teme Boost Union biti naslijeđen ili dupliciran.';
// ... Partner login pages.
$string['partnerlogin'] = 'Stranice za prijavu partnera';
$string['partnerlogin_desc'] = 'Ploča pokraj obrasca za prijavu prikazuje fotografiju, naziv i jednu rečenicu: DIVERSE-ove dok partner nije odabran, a partnerskog sveučilišta kada ga posjetitelj odabere u izborniku "Select Partner". Bez fotografije prikazuje se DIVERSE uzorak. Logotip partnera zamjenjuje DIVERSE logotip na obrascu za prijavu; ako ga nema, koristi se logotip koji je sveučilište prenijelo za svoje korisnike (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Na stranici za prijavu još nema navedenih partnerskih sveučilišta.';
$string['loginphoto'] = 'Fotografija za prijavu: {$a}';
$string['loginphoto_desc'] = 'Vodoravna fotografija, široka najmanje 1600 piksela. Ispunjava ploču pokraj obrasca za prijavu; donji dio je zatamnjen ispod naziva sveučilišta.';
$string['loginlogo'] = 'Logotip za prijavu: {$a}';
$string['loginlogo_desc'] = 'Prikazuje se na vrhu obrasca za prijavu kada je odabrano ovo sveučilište. Najbolje odgovara širok logotip na prozirnoj ili bijeloj pozadini (PNG ili SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'O nama';
$string['nav_partners'] = 'Partneri';
$string['nav_courses'] = 'Kolegiji';
$string['landing_eyebrow'] = 'Savez europskih sveučilišta';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Inovacije, obrazovanje i održivost za otpornu budućnost.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) okuplja sveučilišta, inovatore i poduzeća kako bi potaknuo zelenu i digitalnu transformaciju Europe.';
$string['landing_getstarted'] = 'Započnite';
$string['landing_meetpartners'] = 'Upoznajte partnere';
$string['landing_keyfigures'] = 'Ključne brojke';
$string['stat_projectpartners'] = 'Partneri projekta';
$string['stat_countries'] = 'Zemlje';
$string['stat_courses'] = 'Dostupni e-kolegiji';
$string['about_eyebrow'] = 'O projektu GT4T';
$string['about_title'] = 'Od znanja do praktičnih rješenja';
$string['about_intro'] = 'GT4T jača ulogu sveučilišta u zelenoj i digitalnoj tranziciji povezujući visoko obrazovanje s industrijom i inovacijskim ekosustavima. Cilj je pretvoriti ideje i znanje u praktična rješenja za održiviju, uključiviju i za budućnost spremnu Europu.';
$string['about_support'] = 'Projekt podržava EIT HEI Initiative, vodi i sufinancira EIT Climate-KIC, a koordinira ga Satakunta University of Applied Sciences (SAMK, Finska).';
$string['focus_item1_title'] = 'Kružno gospodarstvo';
$string['focus_item1_desc'] = 'Čišća proizvodnja, održivi materijali i odgovorno korištenje resursa.';
$string['focus_item2_title'] = 'Digitalna transformacija';
$string['focus_item2_desc'] = 'Vještine umjetne inteligencije, analize podataka, automatizacije i pametne proizvodnje.';
$string['focus_item3_title'] = 'Energetska tranzicija';
$string['focus_item3_desc'] = 'Inovacije i poduzetništvo koji podupiru prelazak Europe na čistu energiju.';
$string['partners_eyebrow'] = 'Partneri';
$string['partners_title'] = 'Tko sudjeluje u GT4T-u';
$string['partners_intro'] = 'Sveučilišta, inovacijski centri i poduzeća iz cijele Europe zajedno rade na projektu.';
$string['partners_lead'] = 'Vodeći partner';
$string['courses_title'] = 'Istražite kolegije';
$string['courses_all'] = 'Svi kolegiji ({$a})';
$string['footer_about'] = 'Savez europskih sveučilišta DIVERSE: inovacije, obrazovanje i održivost za otpornu budućnost.';
$string['footer_explore'] = 'Istraži';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
$string['footer_platform'] = 'Platforma';
$string['footer_privacy'] = 'Sažetak o privatnosti';

// Login page brand panel.
$string['login_intro'] = 'Zajedničke edukacije i praktični inovacijski projekti naših partnerskih sveučilišta, na jednom mjestu.';
$string['login_partner_intro'] = 'Prijavite se svojim računom ustanove {$a}.';
$string['login_partner_member'] = 'Član Europskog sveučilišnog saveza DIVERSE';
$string['signup_intro'] = 'Izradite račun i pridružite se zajedničkim kolegijima partnerskih sveučilišta saveza DIVERSE.';
$string['signup_partner_intro'] = 'Izradite svoj račun ustanove {$a}.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Glavna navigacija';
$string['messages_select'] = 'Odaberite razgovor da biste ga ovdje pročitali.';
$string['mycourses_all'] = 'Svi';
$string['mycourses_notstarted'] = 'Nije započeto';
$string['mycourses_inprogress'] = 'U tijeku';
$string['mycourses_completed'] = 'Završeno';
$string['mycourses_favourites'] = 'Favoriti';
$string['mycourses_none'] = 'Ovdje još nema ničega.';
$string['card_favourite'] = 'Dodaj {$a} u favorite';
$string['card_unfavourite'] = 'Ukloni {$a} iz favorita';
$string['dash_kpi_courses'] = 'Upisani kolegiji';
$string['dash_kpi_completed'] = 'Završeno';
$string['dash_kpi_due'] = 'Rok ovaj tjedan';
$string['dash_kpi_progress'] = 'Prosječan napredak';
$string['dash_activity'] = 'Tvoja aktivnost ovaj tjedan';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Ovaj tjedan još nema aktivnosti.';
$string['dash_hero_next'] = 'Sljedeće: {$a}';
$string['steps_eyebrow'] = 'Kako funkcionira';
$string['steps_title'] = 'Počni učiti u tri koraka';
$string['step1_title'] = 'Odaberi svoje sveučilište';
$string['step1_desc'] = 'Na stranici za prijavu odaberi partnersko sveučilište ili GT4T.';
$string['step2_title'] = 'Prijavi se svojim računom';
$string['step2_desc'] = 'Koristi račun koji ti je dalo sveučilište ili izradi novi.';
$string['step3_title'] = 'Nastavi gdje si stao';
$string['step3_desc'] = 'Nadzorna ploča prikazuje sljedeću aktivnost, rokove i ocjene.';
$string['cta_title'] = 'Spreman za učenje?';
$string['cta_text'] = 'Kolegiji {$a->partners} partnera iz {$a->countries} zemalja na jednom mjestu.';
$string['landing_headline'] = 'Učite {$a} S jednog mjesta.';
$string['landing_headline_word'] = 'diljem Europe.';
$string['landing_browse'] = 'Pregledajte kolegije';
$string['stat_languages'] = 'Jezika';
$string['nav_platform'] = 'Platforma';
$string['platform_eyebrow'] = 'Platforma';
$string['platform_title'] = 'Sve što kolegiju treba.';
$string['platform_intro'] = 'Nastava uživo, AI asistent, vaš jezik i vaš napredak, zajedno na jednoj nadzornoj ploči.';
$string['tile_live_title'] = 'Nastava uživo';
$string['tile_live_desc'] = 'Pridružite se nastavniku i kolegama sa svih partnerskih sveučilišta izravno sa stranice kolegija.';
$string['tile_ai_title'] = 'AI asistent za kolegij';
$string['tile_ai_desc'] = 'Pitajte o gradivu kolegija u bilo koje doba. Vi odlučujete koliko se dugo razgovor čuva.';
$string['tile_ai_question'] = 'Što poglavlje 3 kaže o ugovorima u EU?';
$string['tile_ai_answer'] = 'Obrađuje tri točke: sklapanje ugovora, obveze stranaka i…';
$string['tile_lang_title'] = '{$a} jezika';
$string['tile_lang_desc'] = 'Cijela platforma na vašem jeziku, a kolegiji se mijenjaju s njom.';
$string['tile_progress_title'] = 'Vaš napredak na prvi pogled';
$string['tile_progress_desc'] = 'Vaša sljedeća aktivnost, rokovi i ocjene čekaju vas na nadzornoj ploči.';
$string['preview_live'] = 'Nastava uživo';
$string['preview_deadlines'] = 'Rokovi';
$string['preview_term'] = 'Ovaj semestar';
$string['preview_done'] = '18 aktivnosti završeno';
$string['footer_legal'] = 'Pravne informacije';
$string['nav_discover'] = 'Otkrij';
$string['nav_guide'] = 'Vodič za platformu';
$string['nav_search'] = 'Što želite naučiti?';
$string['dash_resume'] = 'Nastavite učiti';
$string['dash_recommended'] = 'Još kolegija za vas';
$string['dash_allcategories'] = 'Svi kolegiji';
$string['card_activities'] = 'Aktivnosti: {$a}';
$string['card_people'] = 'Osoba: {$a}';
$string['card_enrolled'] = 'Upisani ste';
$string['card_open'] = 'Otvoren upis';
$string['lesson_previous'] = 'Prethodno';
$string['lesson_next'] = 'Sljedeće';
$string['shell_mypartner'] = 'Moj partner';
$string['shell_siteadmin'] = 'Administracija sjedišta';
$string['dash_hello'] = 'Dobro došli natrag, {$a}';
$string['dash_summary_overdue'] = 'Zakašnjelih stavki: {$a}. Počnite ovdje.';
$string['dash_summary_grading'] = 'Predaja koje čekaju vašu ocjenu: {$a}.';
$string['dash_summary_todo'] = 'Rok u sljedećih 30 dana: {$a}.';
$string['dash_summary_clear'] = 'Trenutačno nema obveza.';
$string['dash_lastvisit'] = 'zadnji posjet prije {$a}';
$string['dash_progress'] = '{$a} % dovršeno';
$string['dash_todo'] = 'Za napraviti';
$string['dash_todo_empty'] = 'Trenutačno nema obveza.';
$string['dash_overdue'] = 'Zakašnjelo';
$string['dash_thisweek'] = 'Ovaj tjedan';
$string['dash_later'] = 'Kasnije';
$string['dash_allwork'] = 'Prikaži svih {$a} stavki';
$string['dash_recentgrades'] = 'Nedavne ocjene';
$string['dash_allgrades'] = 'Sve ocjene';
$string['dash_nogrades'] = 'Ovdje će se prikazati vaše ocjene i povratne informacije.';
$string['dash_allcourses'] = 'Svi kolegiji';
$string['dash_nocourses'] = 'Još niste upisani ni na jedan kolegij.';
$string['dash_teaching'] = 'Moja nastava';
$string['dash_needsgrading'] = 'Čeka ocjenjivanje';
$string['dash_course_students'] = '{$a} studenata';
$string['dash_stat_teaching'] = 'Kolegiji koje predajete';
$string['dash_stat_students'] = 'Studenti';
$string['dash_stat_waiting'] = 'Predaje za ocjenjivanje';
$string['dash_site'] = 'Pregled sjedišta';
$string['dash_stat_users'] = 'Aktivni korisnici';
$string['dash_stat_recent'] = 'Aktivni u zadnjih 7 dana';
$string['dash_stat_courses'] = 'Vidljivi kolegiji';
$string['dash_stat_partners'] = 'Partneri';
$string['dash_stat_pending'] = 'Računi koji čekaju odobrenje';
$string['dash_partners'] = 'Partneri';
$string['dash_partner'] = 'Partner';
$string['dash_actions'] = 'Česti zadaci';
$string['dash_browseusers'] = 'Pregled korisnika';
$string['dash_managecourses'] = 'Upravljanje kolegijima';
$string['dash_logs'] = 'Zapisnici sjedišta';
$string['dash_system'] = 'Sustav';
$string['dash_cron_ran'] = 'Zakazani zadaci pokrenuti su prije {$a}';
$string['dash_cron_never'] = 'Zakazani zadaci još nisu pokrenuti';
$string['dash_pending_review'] = 'Pregledaj račune koji čekaju odobrenje';
$string['hero_stats'] = 'Odjeljaka: {$a->sections}, aktivnosti: {$a->activities}';
$string['hero_continue'] = 'Nastavi';
$string['hero_start'] = 'Započni';

// Dashboard cards.
$string['dash_news_label'] = 'Novo';
$string['dash_news_title'] = 'Vodič za platformu: kako DIVERSE radi';
$string['dash_news_text'] = 'Kratki videozapisi o pronalaženju kolegija, nastavi uživo i pitanjima asistentu kolegija.';
$string['dash_news_action'] = 'Pogledaj';
$string['dash_progress_title'] = 'Napredak u učenju';
$string['dash_progress_help'] = 'Aktivnosti s praćenjem dovršenosti u tvojim kolegijima';
$string['dash_progress_total'] = 'Ukupno aktivnosti';
$string['dash_progress_done'] = 'dovršeno';
$string['dash_state_completed'] = 'Dovršeno';
$string['dash_state_started'] = 'U tijeku';
$string['dash_state_overdue'] = 'Zakašnjelo';
$string['dash_state_notstarted'] = 'Nije započeto';
$string['dash_week_help'] = 'Aktivnosti koje si dovršio/la svaki dan';
$string['dash_week_activities'] = 'Aktivnosti';
$string['dash_week_more'] = '{$a} aktivnosti više nego prošli tjedan';
$string['dash_week_fewer'] = '{$a} aktivnosti manje nego prošli tjedan';
$string['dash_week_same'] = 'Jednako aktivnosti kao prošli tjedan';
$string['dash_deadlines'] = 'Rokovi';
$string['dash_due_in'] = 'Rok za {$a}';
$string['strftimedaydateshort'] = '%a %d %b';
$string['dash_col_number'] = 'Br.';
$string['dash_col_progress'] = 'Napredak';
$string['dash_col_next'] = 'Sljedeće';
$string['dash_col_teacher'] = 'Nastavnik';
$string['dash_notvisited'] = 'još nije otvoreno';
$string['dash_goal_title'] = 'Tjedni cilj';
$string['dash_goal_help'] = 'Koliko aktivnosti želiš dovršiti svaki tjedan';
$string['dash_goal_change'] = 'Promijeni cilj';
$string['dash_goal_choice'] = '{$a} tjedno';
$string['dash_goal_this'] = 'Aktivnosti ovaj tjedan';
$string['dash_goal_reached'] = 'Cilj postignut!';
$string['dash_goal_keep'] = 'Samo naprijed!';
$string['dash_goal_left'] = 'još {$a}';
$string['dash_goal_weeks'] = '{$a} tjedana';
$string['dash_goal_total'] = 'Ukupno dovršeno';
$string['dash_goal_average'] = 'Tjedni prosjek';
$string['dash_goal_streak'] = 'Niz';
$string['dash_nothing_to_grade'] = 'Ništa ne čeka ocjenjivanje.';

// Dashboard cards: button of a deadline.
$string['dash_open'] = 'Otvori';

// My courses: search, order and view.
$string['mycourses_search'] = 'Pretraži moje kolegije';
$string['mycourses_nomatch'] = 'Nijedan kolegij ne odgovara pretrazi.';
$string['mycourses_lastvisited'] = 'Zadnji posjet';
$string['mycourses_view'] = 'Prikaz';
$string['mycourses_cards'] = 'Kartice';
$string['mycourses_list'] = 'Popis';

// Course page: next step and the column next to the sections.
$string['course_upnext'] = 'Sljedeće';
$string['course_aside'] = 'Ovaj kolegij';
$string['course_live_next'] = 'Sljedeća nastava uživo';
$string['course_live_room'] = 'Učionica uživo';
$string['course_live_none'] = 'Nastava uživo još nije zakazana.';
$string['course_live_join'] = 'Pridruži se nastavi';
$string['course_nodeadlines'] = 'U ovom kolegiju nema rokova.';
$string['course_assistant'] = 'Asistent kolegija';
$string['course_assistant_text'] = 'Pitaj o literaturi, rokovima ili riječi koju ne poznaješ.';
$string['course_assistant_open'] = 'Pitaj asistenta';
$string['course_teachers'] = 'Nastavnici';
$string['course_message'] = 'Pošalji poruku: {$a}';

// Colour palette: the switch in the top bar.
$string['palette'] = 'Boje';
$string['palette_orange'] = 'Narančasta';
$string['palette_green'] = 'Zelena';

// My courses: the line under the title.
$string['mycourses_summary'] = '{$a->courses} kolegija · {$a->inprogress} u tijeku';

// Dashboard cards: time, row menu, overdue button.
$string['dash_goal_timeweek'] = 'Vrijeme ovaj tjedan';
$string['dash_goal_time'] = '{$a->h} h {$a->m} min';
$string['dash_opencourse'] = 'Otvori kolegij';
$string['dash_submit'] = 'Predaj';

// Course page: progress of a section.
$string['course_section_done'] = '{$a->done} od {$a->total} dovršeno';

// Login page: the way back to the public site.
$string['login_back'] = 'Natrag na početnu';

// Site administration: the tiles above the menu.
$string['admin_shortcuts'] = 'Brzi pristup';

// The rail: its logo shows and hides the names of the places.
$string['rail_expand'] = 'Prikaži nazive izbornika';
$string['privacy:metadata:preference:railopen'] = 'Drži li korisnik izbornik lijevo otvorenim, s nazivima pokraj ikona.';

// Light or dark mode (rail, user menu).
$string['mode_dark'] = 'Tamni način';
$string['mode_light'] = 'Svijetli način';
$string['privacy:metadata:preference:mode'] = 'Je li korisnik odabrao svijetli ili tamni način; bez odabira odlučuje uređaj.';

// Colour palette (user menu).
$string['palette_togreen'] = 'Zelene boje (GT4T)';
$string['palette_toorange'] = 'Narančaste boje (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'Tema DIVERSE ne pohranjuje osobne podatke o korisnicima.';
$string['privacy:metadata:preference:palette'] = 'Paleta boja koju je korisnik odabrao za platformu.';

// Privacy API: weekly goal.
$string['privacy:metadata:preference:weeklygoal'] = 'Tjedni cilj koji je korisnik odabrao na nadzornoj ploči: koliko aktivnosti tjedno.';
