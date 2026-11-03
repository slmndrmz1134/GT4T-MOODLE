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
 * Theme DIVERSE - Finnish language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE on DIVERSE European University -oppimisalustan teema: Boost Union -alateema, jossa on DIVERSE-väripaletti ja -typografia.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE-teeman asetukset (Boost Union -alateema).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Periytyminen';
$string['inheritanceinherit'] = 'Peri';
$string['inheritanceduplicate'] = 'Monista';
$string['inheritanceoptionsexplanation'] = 'Periytyminen toimii useimmiten ongelmitta. Jos kohtaat ongelmia Boost Union -toiminnoissa, vaihda tämä asetus kohtaan \'Monista\' ja ilmoita ongelmasta GitHubissa.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre-SCSS-periytyminen';
$string['prescssinheritancesetting_desc'] = 'Tällä asetuksella määritetään, peritäänkö vai monistaanko Boost Unionin Pre-SCSS-koodi.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra-SCSS-periytyminen';
$string['extrascssinheritancesetting_desc'] = 'Tällä asetuksella määritetään, peritäänkö vai monistaanko Boost Unionin Extra-SCSS-koodi.';
// ... Partner login pages.
$string['partnerlogin'] = 'Kumppanien kirjautumissivut';
$string['partnerlogin_desc'] = 'Kirjautumislomakkeen vieressä oleva paneeli näyttää kuvan, nimen ja yhden lauseen: DIVERSEn, kun kumppania ei ole valittu, ja kumppanikorkeakoulun, kun vierailija valitsee sen "Select Partner" -valikosta. Ilman kuvaa näytetään DIVERSE-kuvio. Kumppanin logo korvaa DIVERSE-logon kirjautumislomakkeessa; jos sitä ei ole, käytetään logoa, jonka korkeakoulu on ladannut käyttäjilleen (tenant > Appearance > Logos).';
$string['partnerlogin_none'] = 'Kirjautumissivulla ei ole vielä kumppanikorkeakouluja.';
$string['loginphoto'] = 'Kirjautumiskuva: {$a}';
$string['loginphoto_desc'] = 'Vaakakuva, vähintään 1600 pikseliä leveä. Se täyttää kirjautumislomakkeen viereisen paneelin; alaosaa tummennetaan korkeakoulun nimen alla.';
$string['loginlogo'] = 'Kirjautumislogo: {$a}';
$string['loginlogo_desc'] = 'Näytetään kirjautumislomakkeen yläosassa, kun tämä korkeakoulu on valittu. Parhaiten toimii leveä logo läpinäkyvällä tai valkoisella taustalla (PNG tai SVG).';

// Landing page (site home for visitors).
$string['nav_about'] = 'Tietoa';
$string['nav_partners'] = 'Kumppanit';
$string['nav_courses'] = 'Kurssit';
$string['landing_eyebrow'] = 'Eurooppalainen yliopistoliiitto';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Innovaatioita, koulutusta ja kestävyyttä resilienttiä tulevaisuutta varten.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T) tuo yhteen korkeakouluja, innovaattoreita ja yrityksiä edistämään Euroopan vihreää ja digitaalista murrosta.';
$string['landing_getstarted'] = 'Aloita';
$string['landing_meetpartners'] = 'Tutustu kumppaneihin';
$string['landing_keyfigures'] = 'Avainluvut';
$string['stat_projectpartners'] = 'Hankekumppania';
$string['stat_countries'] = 'Maata';
$string['stat_courses'] = 'Saatavilla olevat kurssit';
$string['about_eyebrow'] = 'Tietoa GT4T:stä';
$string['about_title'] = 'Tiedosta käytännön ratkaisuiksi';
$string['about_intro'] = 'GT4T vahvistaa korkeakoulujen roolia vihreässä ja digitaalisessa siirtymässä yhdistämällä korkeakoulutuksen teollisuuteen ja innovaatioekosysteemeihin. Tavoitteena on muuttaa ideat ja tieto käytännön ratkaisuiksi kestävämmän, osallistavamman ja tulevaisuuteen valmiin Euroopan hyväksi.';
$string['about_support'] = 'Hanketta tukee EIT HEI Initiative, ohjaa ja osarahoittaa EIT Climate-KIC, ja sitä koordinoi Satakunnan ammattikorkeakoulu (SAMK).';
$string['focus_item1_title'] = 'Kiertotalous';
$string['focus_item1_desc'] = 'Puhtaampi tuotanto, kestävät materiaalit ja vastuullinen resurssien käyttö.';
$string['focus_item2_title'] = 'Digitaalinen murros';
$string['focus_item2_desc'] = 'Tekoälyn, data-analytiikan, automaation ja älykkään valmistuksen osaaminen.';
$string['focus_item3_title'] = 'Energiasiirtymä';
$string['focus_item3_desc'] = 'Innovaatioita ja yrittäjyyttä, jotka tukevat Euroopan siirtymää puhtaaseen energiaan.';
$string['partners_eyebrow'] = 'Kumppanit';
$string['partners_title'] = 'Ketkä ovat mukana GT4T:ssä';
$string['partners_intro'] = 'Korkeakoulut, innovaatiokeskukset ja yritykset eri puolilta Eurooppaa tekevät hankkeessa yhteistyötä.';
$string['partners_lead'] = 'Päätoteuttaja';
$string['courses_title'] = 'Tutustu kursseihin';
$string['courses_all'] = 'Kaikki {$a} kurssia';
$string['footer_about'] = 'DIVERSE-eurooppalainen yliopistoliitto: innovaatioita, koulutusta ja kestävyyttä resilienttiä tulevaisuutta varten.';
$string['footer_explore'] = 'Tutustu';
$string['footer_copyright'] = '© {$a} DIVERSE European University Alliance';
$string['footer_platform'] = 'Alusta';
$string['footer_privacy'] = 'Tietosuojayhteenveto';

// Login page brand panel.
$string['login_intro'] = 'Kumppaniyliopistojen yhteiset koulutukset ja käytännön innovaatioprojektit yhdessä paikassa.';
$string['login_partner_intro'] = 'Kirjaudu {$a} -tunnuksillasi.';
$string['login_partner_member'] = 'DIVERSE European University Alliance -liittouman jäsen';
$string['signup_intro'] = 'Luo tili ja osallistu DIVERSE-kumppaniyliopistojen yhteisille kursseille.';
$string['signup_partner_intro'] = 'Luo {$a} -tunnus.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Päänavigointi';
$string['messages_select'] = 'Valitse keskustelu luettavaksi tässä.';
$string['mycourses_all'] = 'Kaikki';
$string['mycourses_notstarted'] = 'Aloittamatta';
$string['mycourses_inprogress'] = 'Kesken';
$string['mycourses_completed'] = 'Suoritettu';
$string['mycourses_favourites'] = 'Suosikit';
$string['mycourses_none'] = 'Täällä ei ole vielä mitään.';
$string['card_favourite'] = 'Lisää {$a} suosikkeihin';
$string['card_unfavourite'] = 'Poista {$a} suosikeista';
$string['dash_kpi_courses'] = 'Kurssit';
$string['dash_kpi_completed'] = 'Suoritettu';
$string['dash_kpi_due'] = 'Palautettava tällä viikolla';
$string['dash_kpi_progress'] = 'Keskimääräinen edistyminen';
$string['dash_activity'] = 'Aktiivisuutesi tällä viikolla';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Tällä viikolla ei vielä aktiivisuutta.';
$string['dash_hero_next'] = 'Seuraavaksi: {$a}';
$string['steps_eyebrow'] = 'Näin se toimii';
$string['steps_title'] = 'Aloita oppiminen kolmessa vaiheessa';
$string['step1_title'] = 'Valitse korkeakoulusi';
$string['step1_desc'] = 'Valitse kirjautumissivulla kumppanikorkeakoulusi tai GT4T.';
$string['step2_title'] = 'Kirjaudu tunnuksillasi';
$string['step2_desc'] = 'Käytä korkeakoulusi antamaa tunnusta tai luo uusi.';
$string['step3_title'] = 'Jatka siitä, mihin jäit';
$string['step3_desc'] = 'Työpöytäsi näyttää seuraavan aktiviteetin, määräajat ja arvosanat.';
$string['cta_title'] = 'Valmis aloittamaan?';
$string['cta_text'] = 'Kursseja {$a->partners} kumppanilta {$a->countries} maasta yhdessä paikassa.';
$string['nav_discover'] = 'Löydä';
$string['nav_guide'] = 'Alustan opas';
$string['nav_search'] = 'Mitä haluat oppia?';
$string['dash_resume'] = 'Jatka oppimista';
$string['dash_recommended'] = 'Lisää kursseja sinulle';
$string['dash_allcategories'] = 'Kaikki kurssit';
$string['card_activities'] = 'Aktiviteetit: {$a}';
$string['card_people'] = 'Henkilöt: {$a}';
$string['card_enrolled'] = 'Ilmoittautunut';
$string['card_open'] = 'Avoin ilmoittautuminen';
$string['lesson_previous'] = 'Edellinen';
$string['lesson_next'] = 'Seuraava';
$string['shell_mypartner'] = 'Oma kumppani';
$string['shell_siteadmin'] = 'Sivuston ylläpito';
$string['dash_hello'] = 'Tervetuloa takaisin, {$a}';
$string['dash_summary_overdue'] = 'Myöhässä: {$a}. Aloita näistä.';
$string['dash_summary_grading'] = 'Arviointia odottavat palautukset: {$a}.';
$string['dash_summary_todo'] = 'Määräaika 30 päivän sisällä: {$a}.';
$string['dash_summary_clear'] = 'Mitään ei ole juuri nyt erääntymässä.';
$string['dash_lastvisit'] = 'edellinen käynti {$a} sitten';
$string['dash_progress'] = '{$a} % valmis';
$string['dash_todo'] = 'Tehtävät';
$string['dash_todo_empty'] = 'Juuri nyt ei ole tehtävää.';
$string['dash_overdue'] = 'Myöhässä';
$string['dash_thisweek'] = 'Tällä viikolla';
$string['dash_later'] = 'Myöhemmin';
$string['dash_allwork'] = 'Näytä kaikki {$a}';
$string['dash_recentgrades'] = 'Viimeisimmät arvosanat';
$string['dash_allgrades'] = 'Kaikki arvosanat';
$string['dash_nogrades'] = 'Arvosanasi ja palautteesi näkyvät tässä.';
$string['dash_allcourses'] = 'Kaikki kurssit';
$string['dash_nocourses'] = 'Et ole vielä millään kurssilla.';
$string['dash_teaching'] = 'Opetukseni';
$string['dash_needsgrading'] = 'Odottaa arviointia';
$string['dash_course_students'] = '{$a} opiskelijaa';
$string['dash_stat_teaching'] = 'Opettamasi kurssit';
$string['dash_stat_students'] = 'Opiskelijat';
$string['dash_stat_waiting'] = 'Arvioitavat palautukset';
$string['dash_site'] = 'Sivuston yleiskuva';
$string['dash_stat_users'] = 'Aktiiviset käyttäjät';
$string['dash_stat_recent'] = 'Paikalla 7 päivän aikana';
$string['dash_stat_courses'] = 'Näkyvät kurssit';
$string['dash_stat_partners'] = 'Kumppanit';
$string['dash_stat_pending'] = 'Hyväksyntää odottavat tilit';
$string['dash_partners'] = 'Kumppanit';
$string['dash_partner'] = 'Kumppani';
$string['dash_actions'] = 'Yleiset toiminnot';
$string['dash_browseusers'] = 'Selaa käyttäjiä';
$string['dash_managecourses'] = 'Hallitse kursseja';
$string['dash_logs'] = 'Sivuston lokit';
$string['dash_system'] = 'Järjestelmä';
$string['dash_cron_ran'] = 'Ajastetut tehtävät suoritettiin {$a} sitten';
$string['dash_cron_never'] = 'Ajastettuja tehtäviä ei ole vielä suoritettu';
$string['dash_pending_review'] = 'Tarkista hyväksyntää odottavat tilit';
$string['hero_stats'] = '{$a->sections} osiota, {$a->activities} aktiviteettia';
$string['hero_continue'] = 'Jatka';
$string['hero_start'] = 'Aloita';

// Colour palette (user menu).
$string['palette_togreen'] = 'Vihreät värit (GT4T)';
$string['palette_toorange'] = 'Oranssit värit (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE-teema ei tallenna henkilökohtaisia tietoja käyttäjistä.';
$string['privacy:metadata:preference:palette'] = 'Käyttäjän alustalle valitsema väripaletti.';
