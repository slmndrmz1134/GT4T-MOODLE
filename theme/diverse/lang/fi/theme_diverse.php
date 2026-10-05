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
$string['footer_platform'] = 'Alusta';
$string['footer_privacy'] = 'Tietosuojayhteenveto';

// Login page brand panel.
$string['login_intro'] = 'Kumppaniyliopistojen yhteiset koulutukset ja käytännön innovaatioprojektit yhdessä paikassa.';
$string['login_partner_intro'] = 'Kirjaudu {$a} -tunnuksillasi.';
$string['login_partner_member'] = 'DIVERSE European University Alliance -liittouman jäsen';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE-teema ei tallenna henkilökohtaisia tietoja käyttäjistä.';
