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
$string['partnerlogin_desc'] = 'Giriş formunun yanındaki panel bir fotoğraf, bir ad ve tek bir cümle gösterir: partner seçilmemişken DIVERSE\'ün, ziyaretçi "Select Partner" menüsünden bir partner üniversite seçtiğinde o üniversitenin. Fotoğraf yoksa DIVERSE deseni görünür. Partnerin logosu giriş formundaki DIVERSE logosunun yerine geçer; yüklenmemişse üniversitenin kendi kullanıcıları için yüklediği logo (tenant > Appearance > Logos) kullanılır.';
$string['partnerlogin_none'] = 'Giriş sayfasında listelenen partner üniversite henüz yok.';
$string['loginphoto'] = 'Giriş fotoğrafı: {$a}';
$string['loginphoto_desc'] = 'En az 1600 piksel genişliğinde yatay bir fotoğraf. Giriş formunun yanındaki paneli doldurur; alt kısmı üniversitenin adının altında koyulaştırılır.';
$string['loginlogo'] = 'Giriş logosu: {$a}';
$string['loginlogo_desc'] = 'Bu üniversite seçildiğinde giriş formunun üstünde görünür. Saydam ya da beyaz zeminli, yatay bir logo (PNG veya SVG) en iyi sonucu verir.';

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
$string['footer_explore'] = 'Keşfet';
$string['footer_copyright'] = '© {$a} DIVERSE Avrupa Üniversitesi Birliği';
$string['footer_platform'] = 'Platform';
$string['footer_privacy'] = 'Gizlilik özeti';

// Login page brand panel.
$string['login_intro'] = 'Ortak üniversitelerimizin sunduğu ortak eğitimler ve uygulamalı inovasyon projeleri tek bir yerde.';
$string['login_partner_intro'] = '{$a} hesabınızla giriş yapın.';
$string['login_partner_member'] = 'DIVERSE Avrupa Üniversitesi Birliği üyesi';
$string['signup_intro'] = 'Hesabınızı oluşturun ve DIVERSE ortak üniversitelerinin ortak derslerine katılın.';
$string['signup_partner_intro'] = '{$a} hesabınızı oluşturun.';

// Application: navigation, dashboard and course header.
$string['shell_nav'] = 'Ana gezinme';
$string['messages_select'] = 'Okumak için bir konuşma seçin.';
$string['mycourses_all'] = 'Tümü';
$string['mycourses_notstarted'] = 'Başlanmadı';
$string['mycourses_inprogress'] = 'Devam ediyor';
$string['mycourses_completed'] = 'Tamamlandı';
$string['mycourses_favourites'] = 'Favoriler';
$string['mycourses_none'] = 'Burada henüz bir şey yok.';
$string['card_favourite'] = '{$a} dersini favorilere ekle';
$string['card_unfavourite'] = '{$a} dersini favorilerden çıkar';
$string['dash_kpi_courses'] = 'Kayıtlı dersler';
$string['dash_kpi_completed'] = 'Tamamlanan';
$string['dash_kpi_due'] = 'Bu hafta teslim';
$string['dash_kpi_progress'] = 'Ortalama ilerleme';
$string['dash_activity'] = 'Bu haftaki etkinliğin';
$string['dash_activity_day'] = '{$a->day}: {$a->count}';
$string['dash_activity_none'] = 'Bu hafta henüz etkinlik yok.';
$string['dash_hero_next'] = 'Sıradaki: {$a}';
$string['steps_eyebrow'] = 'Nasıl çalışır';
$string['steps_title'] = 'Üç adımda öğrenmeye başlayın';
$string['step1_title'] = 'Üniversitenizi seçin';
$string['step1_desc'] = 'Giriş sayfasında partner üniversitenizi ya da GT4T\'yi seçin.';
$string['step2_title'] = 'Hesabınızla giriş yapın';
$string['step2_desc'] = 'Üniversitenizin verdiği hesabı kullanın ya da yeni bir hesap oluşturun.';
$string['step3_title'] = 'Kaldığınız yerden devam edin';
$string['step3_desc'] = 'Panonuz sıradaki etkinliğinizi, teslim tarihlerinizi ve notlarınızı gösterir.';
$string['cta_title'] = 'Öğrenmeye hazır mısınız?';
$string['cta_text'] = '{$a->countries} ülkeden {$a->partners} ortağın dersleri tek bir yerde.';
$string['nav_discover'] = 'Keşfet';
$string['nav_guide'] = 'Platform rehberi';
$string['nav_search'] = 'Ne öğrenmek istiyorsunuz?';
$string['dash_resume'] = 'Öğrenmeye devam edin';
$string['dash_recommended'] = 'Sizin için diğer dersler';
$string['dash_allcategories'] = 'Tüm dersler';
$string['card_activities'] = '{$a} etkinlik';
$string['card_people'] = '{$a} kişi';
$string['card_enrolled'] = 'Kayıtlısınız';
$string['card_open'] = 'Kayıt açık';
$string['lesson_previous'] = 'Önceki';
$string['lesson_next'] = 'Sonraki';
$string['shell_mypartner'] = 'Partnerim';
$string['shell_siteadmin'] = 'Site yönetimi';
$string['dash_hello'] = 'Tekrar hoş geldiniz, {$a}';
$string['dash_summary_overdue'] = 'Geciken iş: {$a}. Buradan başlayın.';
$string['dash_summary_grading'] = 'Notunuzu bekleyen teslim: {$a}.';
$string['dash_summary_todo'] = 'Önümüzdeki 30 günde teslim: {$a}.';
$string['dash_summary_clear'] = 'Şu anda bekleyen bir iş yok.';
$string['dash_lastvisit'] = 'son ziyaret {$a} önce';
$string['dash_progress'] = '%{$a} tamamlandı';
$string['dash_todo'] = 'Yapılacaklar';
$string['dash_todo_empty'] = 'Şu anda yapılacak bir iş yok.';
$string['dash_overdue'] = 'Geciken';
$string['dash_thisweek'] = 'Bu hafta';
$string['dash_later'] = 'Daha sonra';
$string['dash_allwork'] = '{$a} işin tümünü gör';
$string['dash_recentgrades'] = 'Son notlar';
$string['dash_allgrades'] = 'Tüm notlar';
$string['dash_nogrades'] = 'Notlarınız ve geri bildirimleriniz burada görünecek.';
$string['dash_allcourses'] = 'Tüm dersler';
$string['dash_nocourses'] = 'Henüz hiçbir derse kayıtlı değilsiniz.';
$string['dash_teaching'] = 'Verdiğim dersler';
$string['dash_needsgrading'] = 'Not bekleyenler';
$string['dash_course_students'] = '{$a} öğrenci';
$string['dash_stat_teaching'] = 'Verdiğiniz ders';
$string['dash_stat_students'] = 'Öğrenci';
$string['dash_stat_waiting'] = 'Notlanacak teslim';
$string['dash_site'] = 'Site özeti';
$string['dash_stat_users'] = 'Etkin kullanıcı';
$string['dash_stat_recent'] = 'Son 7 günde giriş yapan';
$string['dash_stat_courses'] = 'Görünür ders';
$string['dash_stat_partners'] = 'Partner';
$string['dash_stat_pending'] = 'Onay bekleyen hesap';
$string['dash_partners'] = 'Partnerler';
$string['dash_partner'] = 'Partner';
$string['dash_actions'] = 'Sık yapılan işler';
$string['dash_browseusers'] = 'Kullanıcılara göz at';
$string['dash_managecourses'] = 'Dersleri yönet';
$string['dash_logs'] = 'Site günlükleri';
$string['dash_system'] = 'Sistem';
$string['dash_cron_ran'] = 'Zamanlanmış görevler {$a} önce çalıştı';
$string['dash_cron_never'] = 'Zamanlanmış görevler henüz çalışmadı';
$string['dash_pending_review'] = 'Onay bekleyen hesapları incele';
$string['hero_stats'] = '{$a->sections} bölüm, {$a->activities} etkinlik';
$string['hero_continue'] = 'Devam et';
$string['hero_start'] = 'Başla';

// Colour palette (user menu).
$string['palette_togreen'] = 'Yeşil renkler (GT4T)';
$string['palette_toorange'] = 'Turuncu renkler (DIVERSE)';

// Privacy API.
$string['privacy:metadata'] = 'DIVERSE teması kullanıcılar hakkında hiçbir kişisel veri depolamaz.';
$string['privacy:metadata:preference:palette'] = 'Kullanıcının platform için seçtiği renk paleti.';
