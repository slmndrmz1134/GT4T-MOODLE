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
 * Theme DIVERSE - Turkish language pack
 *
 * @package    theme_diverse
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'DIVERSE';
$string['choosereadme'] = 'DIVERSE, DIVERSE Avrupa Üniversitesi öğrenme platformunun temasıdır: DIVERSE renk paleti ve tipografisine sahip bir Boost Union alt temasıdır.';
$string['configtitle'] = 'DIVERSE';
$string['settingsoverview_buc_desc'] = 'DIVERSE tema ayarları (Boost Union alt teması).';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Miras Alma';
$string['inheritanceinherit'] = 'Miras Al';
$string['inheritanceduplicate'] = 'Kopyala';
$string['inheritanceoptionsexplanation'] = 'Çoğu zaman miras alma sorunsuz çalışır. Ancak Boost Union\'a yapılan bazı kod değişiklikleri belirli Boost Union özellikleri için basit SCSS miras almayı engelleyebilir. DIVERSE içinde çalışmayan bir Boost Union özelliğiyle karşılaşırsanız bu ayarı \'Kopyala\' olarak değiştirmeyi deneyin; sorun çözülürse GitHub üzerinden bildirin.';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre-SCSS miras alma';
$string['prescssinheritancesetting_desc'] = 'Bu ayar ile Boost Union\'dan gelen pre-SCSS kodunun miras mı alınacağını yoksa kopyalanacağını mı belirlersiniz.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra-SCSS miras alma';
$string['extrascssinheritancesetting_desc'] = 'Bu ayar ile Boost Union\'dan gelen extra-SCSS kodunun miras mı alınacağını yoksa kopyalanacağını mı belirlersiniz.';
// ... Partner login pages.
$string['partnerlogin'] = 'Partner giriş sayfaları';
$string['partnerlogin_desc'] = 'Ziyaretçi giriş sayfasındaki "Select Partner" menüsünden bir partner üniversite seçtiğinde, giriş formunun yanındaki panelde DIVERSE paneli yerine o üniversitenin fotoğrafı, logosu ve adı görünür. Logo, her üniversitenin kendi kullanıcıları için yüklediği logodur (tenant > Appearance > Logos). Fotoğraf yoksa panelde DIVERSE deseni ve üniversitenin adı görünür.';
$string['partnerlogin_none'] = 'Giriş sayfasında listelenen partner üniversite henüz yok.';
$string['loginphoto'] = 'Giriş fotoğrafı: {$a}';
$string['loginphoto_desc'] = 'En az 1600 piksel genişliğinde yatay bir fotoğraf. Giriş formunun yanındaki paneli doldurur; alt kısmı üniversitenin adının altında koyulaştırılır.';

// Landing page (site home for visitors).
$string['nav_about'] = 'Hakkında';
$string['nav_partners'] = 'Ortaklar';
$string['nav_courses'] = 'Dersler';
$string['landing_eyebrow'] = 'Avrupa Üniversitesi Birliği';
$string['landing_title'] = 'DIVERSE';
$string['landing_tagline'] = 'Dirençli bir gelecek için inovasyon, eğitim ve sürdürülebilirlik.';
$string['landing_intro'] = 'GreenTech4Transformation (GT4T), Avrupa\'nın yeşil ve dijital dönüşümüne destek olmak için üniversiteleri, yenilikçileri ve işletmeleri bir araya getirir.';
$string['landing_getstarted'] = 'Başlayın';
$string['landing_meetpartners'] = 'Ortakları keşfedin';
$string['landing_keyfigures'] = 'Önemli rakamlar';
$string['stat_partners'] = 'Ortak üniversite';
$string['stat_projectpartners'] = 'Proje ortağı';
$string['stat_countries'] = 'Ülke';
$string['stat_courses'] = 'Mevcut ders';
$string['about_eyebrow'] = 'GT4T hakkında';
$string['about_title'] = 'Bilgiden uygulanabilir çözümlere';
$string['about_intro'] = 'GT4T, yükseköğretimi sanayi ve inovasyon ekosistemleriyle buluşturarak üniversitelerin yeşil ve dijital dönüşümdeki rolünü güçlendirir. Amacı, fikirleri ve bilgiyi daha sürdürülebilir, kapsayıcı ve geleceğe hazır bir Avrupa için uygulanabilir çözümlere dönüştürmektir.';
$string['about_support'] = 'EIT HEI Initiative tarafından desteklenir, EIT Climate-KIC tarafından yönlendirilir ve ortak finanse edilir; koordinatörü Satakunta Uygulamalı Bilimler Üniversitesi\'dir (SAMK, Finlandiya).';
$string['focus_item1_title'] = 'Döngüsel ekonomi';
$string['focus_item1_desc'] = 'Daha temiz üretim, sürdürülebilir malzemeler ve kaynakların sorumlu kullanımı.';
$string['focus_item2_title'] = 'Dijital dönüşüm';
$string['focus_item2_desc'] = 'Yapay zekâ, veri analitiği, otomasyon ve akıllı üretim becerileri.';
$string['focus_item3_title'] = 'Enerji dönüşümü';
$string['focus_item3_desc'] = 'Avrupa\'nın temiz enerjiye geçişini destekleyen inovasyon ve girişimcilik.';
$string['partners_eyebrow'] = 'Ortaklar';
$string['partners_title'] = 'GT4T\'de kimler var';
$string['partners_intro'] = 'Avrupa\'nın dört bir yanından üniversiteler, inovasyon merkezleri ve şirketler projede birlikte çalışıyor.';
$string['partners_lead'] = 'Lider ortak';
$string['courses_title'] = 'Dersleri keşfedin';
$string['courses_all'] = 'Tüm {$a} ders';
$string['footer_about'] = 'DIVERSE Avrupa Üniversitesi Birliği: dirençli bir gelecek için inovasyon, eğitim ve sürdürülebilirlik.';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Gizlilik özeti';

// Login page brand panel.
$string['login_tagline'] = 'Sınırların ötesindeki ortaklarla birlikte öğrenin';
$string['login_intro'] = 'Ortak üniversitelerimizin sunduğu ortak eğitimler ve uygulamalı inovasyon projeleri tek bir yerde.';
$string['login_partner_intro'] = '{$a} hesabınızla giriş yapın.';
$string['login_partner_member'] = 'DIVERSE Avrupa Üniversitesi Birliği üyesi';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE teması kullanıcılar hakkında hiçbir kişisel veri depolamaz.';
