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
 * DIVERSE AI assistant - Language strings (Turkish)
 *
 * @package    local_diverse_assistant
 * @copyright  2026 DIVERSE European University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'API anahtarı';
$string['apikey_desc_anthropic'] = 'console.anthropic.com > API keys adresinden (sk-ant- ile başlar).';
$string['apikey_desc_gemini'] = 'aistudio.google.com > Get API key adresinden (AIza ile başlar). Ödeme tanımlı bir projenin anahtarını kullanın: ücretsiz katmanda Google soruları ürünlerini geliştirmek için kullanabilir.';
$string['apikey_desc_openai'] = 'platform.openai.com > API keys adresinden (sk- ile başlar; hesapta ödeme tanımlı olmalı). ChatGPT aboneliği API anahtarı değildir.';
$string['apikey_desc_openaicompatible'] = 'Aşağıdaki API adresindeki servisin anahtarı.';
$string['apikey_placeholder_anthropic'] = 'sk-ant-...';
$string['apikey_placeholder_gemini'] = 'AIza...';
$string['apikey_placeholder_openai'] = 'sk-...';
$string['apikey_placeholder_openaicompatible'] = 'API anahtarı';
$string['apikey_placeholder_set'] = 'Kayıtlı bir anahtar var. Değiştirmek için yenisini yapıştırın.';
$string['apikey_remove'] = 'Kayıtlı anahtarı sil';
$string['apikey_saved'] = 'Kayıtlı bir anahtar var (şifreli).';
$string['apply_error_changed'] = 'Asistan bu öneriyi yaptıktan sonra metin değiştirilmiş, bu yüzden uygulanmadı. Güncel sürümle çalışması için asistana yeniden sorun.';
$string['apply_error_formonly'] = 'Bu etkinlik yalnızca kendi düzenleme formunda değiştirilebilir. "Formda aç"ı kullanın.';
$string['apply_error_missing'] = 'Etkinlik ya da bölüm artık yok.';
$string['apply_error_status'] = 'Bu öneri zaten işlendi.';
$string['back'] = 'Geri';
$string['closepanel'] = 'Kapat';
$string['connectionheading'] = 'AI servisi';
$string['connectionheading_desc'] = 'Servisi seçin, API anahtarını yapıştırın ve kaydedin. Anahtarlar şifreli saklanır ve bir daha gösterilmez; boş bırakılan anahtar alanı kayıtlı anahtarı korur. Herhangi bir anahtar alanına yapıştırılan Claude (sk-ant-…) ya da Gemini (AIza…) anahtarı kendiliğinden o servise geçer. Kaydettikten sonra bağlantı kontrol edilir (yukarıda gösterilir) ve Model menüsünde anahtarın kullanabileceği modeller listelenir.';
$string['delete'] = 'Sil';
$string['deleteconversation'] = 'Sohbeti sil';
$string['deleteconversation_confirm'] = '"{$a}" sohbeti silinsin mi? Bu işlem geri alınamaz.';
$string['deletehistory'] = 'Tüm sohbetlerimi sil';
$string['deletehistory_confirm'] = 'Tüm derslerdeki kayıtlı sohbetleriniz silinsin mi? Bu işlem geri alınamaz.';
$string['deletehistory_desc'] = 'Sunucuda kayıtlı tüm sohbetlerinizi (bütün derslerde) ve asistanın size önerdiği değişiklikleri siler.';
$string['diverse_assistant:teach'] = 'Ders içeriği yazmak ve düzenlemek için AI asistanı kullanma';
$string['diverse_assistant:use'] = 'AI ders asistanını kullanma';
$string['emptytitle'] = 'Bu ders hakkında istediğinizi sorun.';
$string['enabled'] = 'Asistanı aç';
$string['enabled_desc'] = 'AI asistan sekmesini ders sayfalarında, kullanma izni olan herkese gösterir (varsayılan olarak öğrenciler ve öğretmenler; yetki: local/diverse_assistant:use). Dersi düzenleyebilen öğretmenler yeni sayfa ve değişiklik öneren içerik asistanını görür (yetki: local/diverse_assistant:teach).';
$string['endpoint'] = 'API adresi';
$string['endpoint_desc'] = 'OpenAI uyumlu API\'nin sürüm dahil adresi, ör. https://api.mistral.ai/v1. Yerel adreslere (ör. Ollama) ayrıca Site yönetimi > Genel > Güvenlik > HTTP güvenliği altında izin verilmelidir.';
$string['endpointineu'] = 'Bu servis verileri AB\'de işliyor';
$string['endpointineu_desc'] = 'Yalnızca servis, isteklerin Avrupa Birliği\'nde işlendiğini sözleşmeyle garanti ediyorsa işaretleyin. "Yalnızca AB" açıkken gereklidir.';
$string['errorauth'] = 'AI servisi API anahtarını kabul etmedi.';
$string['errorbusy'] = 'AI servisi şu an çok yoğun. Lütfen biraz sonra tekrar deneyin.';
$string['errorconnection'] = 'AI servisine ulaşılamadı. Lütfen daha sonra tekrar deneyin.';
$string['errorempty'] = 'AI servisi boş bir cevap gönderdi. Lütfen tekrar deneyin.';
$string['errorforbidden'] = 'AI servisi bu API anahtarıyla gelen isteği reddetti.';
$string['errorgeneric'] = 'Bir şeyler ters gitti. Lütfen tekrar deneyin.';
$string['errorlimit'] = 'Saatte {$a} soru sınırına ulaştınız. Lütfen daha sonra tekrar deneyin.';
$string['errormodel'] = 'AI servisi seçilen modeli ya da adresi tanımıyor.';
$string['errornoendpoint'] = 'AI servisi için API adresi girilmemiş.';
$string['errornomessage'] = 'Lütfen bir soru yazın.';
$string['errornomodel'] = 'AI servisi için model girilmemiş.';
$string['errornotconfigured'] = 'AI servisi henüz kurulmadı.';
$string['errornotfound'] = 'Bu sohbet artık yok.';
$string['errorratelimit'] = 'AI servisi meşgul ya da kullanım sınırı doldu. Lütfen daha sonra tekrar deneyin.';
$string['errorrefusal'] = 'AI bu soruyu cevaplamayı reddetti. Lütfen soruyu farklı bir şekilde sorun.';
$string['errorservice'] = 'AI servisi bir hata bildirdi. Lütfen daha sonra tekrar deneyin.';
$string['errortoolong'] = 'Soru çok uzun (en fazla {$a} karakter).';
$string['euonly'] = 'Yalnızca AB';
$string['euonly_desc'] = 'İşaretliyse asistan yalnızca Avrupa Birliği\'nde işlemeyi garanti eden bir bağlantıyla çalışır: AB veri yerleşimi adresiyle OpenAI ya da AB\'de olduğu onaylanmış uyumlu bir servis. Claude ve Gemini API\'lerinde yalnızca AB seçeneği yoktur. Aksi halde öğrenciler asistanın kullanılamadığını görür.';
$string['field_content'] = 'İçerik';
$string['field_description'] = 'Açıklama';
$string['field_name'] = 'Ad';
$string['field_sectionname'] = 'Bölüm adı';
$string['field_summary'] = 'Bölüm özeti';
$string['field_text'] = 'Metin';
$string['footnote'] = 'AI hata yapabilir. Kişisel bilgi paylaşmayın.';
$string['history'] = 'Kayıtlı sohbetler';
$string['historydeleted'] = 'Kayıtlı sohbetleriniz silindi.';
$string['historyempty'] = 'Bu derste henüz kayıtlı sohbet yok.';
$string['insertedineditor'] = 'Editöre eklendi.';
$string['insertineditor'] = 'Editöre ekle';
$string['limitsheading'] = 'Sınırlar';
$string['maxcontextchars'] = 'Gönderilen ders içeriği (karakter)';
$string['maxcontextchars_desc'] = 'Ders içeriği her soruyla birlikte gönderilir. Daha uzun içerik bu uzunlukta kesilir. 60.000 karakter yaklaşık 15.000 token eder.';
$string['model'] = 'Model';
$string['model_desc'] = 'Bağlantı kontrolünden sonra menü, anahtarın sohbet modellerini listeler. Önerilen: gpt-6-luna (OpenAI), claude-opus-5 (Claude), gemini-3.8-flash (Gemini).';
$string['model_recommended'] = '{$a} (önerilen)';
$string['newchat'] = 'Yeni sohbet';
$string['noeditor'] = 'Önce formdaki bir metin alanına tıklayın, sonra ekleyin.';
$string['notice_accept'] = 'Anladım, başla';
$string['notice_body'] = 'Bu asistan yapay zekâ kullanır ({$a}). Cevapları oluşturmak için sorularınız ve bu dersin içeriği bu servise gönderilir. Adınız ve e-posta adresiniz gönderilmez.';
$string['notice_mistakes'] = 'Cevaplar yanlış ya da eksik olabilir. Önemli bilgileri ders içeriğinden kontrol edin.';
$string['notice_personal'] = 'Sorularınıza kişisel bilgi (kendinizin ya da başkalarının) yazmayın.';
$string['notice_retention'] = 'Sohbetlerinizin kaydedilip kaydedilmeyeceğine ve ne kadar süre tutulacağına Ayarlar\'dan siz karar verirsiniz. Varsayılan olarak hiçbir şey kaydedilmez.';
$string['notice_teacher'] = 'Öğretmen modu: asistan bu dersin metinlerini (gizli etkinlikler dahil) görür, yeni sayfa ve değişiklik önerebilir. Siz bir öneriyi uygulamadıkça derste hiçbir şey değişmez; uygulanan bir değişikliği {$a} gün içinde geri alabilirsiniz.';
$string['notice_title'] = 'Başlamadan önce';
$string['openairegion'] = 'Veri bölgesi';
$string['openairegion_desc'] = 'AB adresini yalnızca anahtarın ait olduğu OpenAI projesi Avrupa veri yerleşimiyle oluşturulduysa kullanın.';
$string['openairegion_eu'] = 'Avrupa Birliği (eu.api.openai.com)';
$string['openairegion_global'] = 'Varsayılan (api.openai.com)';
$string['paneltitle'] = 'AI asistan';
$string['placeholder'] = 'Bu ders hakkında sorun…';
$string['pluginname'] = 'DIVERSE AI asistan';
$string['prefill_done'] = 'Asistanın önerisi forma yerleştirildi. Kontrol edip kaydedin.';
$string['prefill_failed'] = 'Öneri bu forma yerleştirilemedi.';
$string['privacy:metadata:aiservice'] = 'Cevap vermek için soru; sohbetin önceki mesajları ve kullanıcının görebildiği ders içeriğiyle birlikte site yöneticisinin seçtiği AI servisine gönderilir. Kullanıcının adı, e-posta adresi ve Moodle kimliği gönderilmez.';
$string['privacy:metadata:aiservice:coursecontent'] = 'Öğretmen modunda: dersin kayıtlı metinleri (adlar, açıklamalar, sayfalar, bölüm özetleri), gizli etkinlikler dahil';
$string['privacy:metadata:aiservice:coursematerials'] = 'Kullanıcının görebildiği, öğretmenlerin yazdığı ders içeriği';
$string['privacy:metadata:aiservice:history'] = 'Sohbetin önceki mesajları';
$string['privacy:metadata:aiservice:message'] = 'Soru';
$string['privacy:metadata:conv'] = 'Kullanıcının kaydetmeyi seçtiği sohbetler; kullanıcının seçtiği süre dolunca silinir.';
$string['privacy:metadata:conv:courseid'] = 'Sohbetin ilgili olduğu ders';
$string['privacy:metadata:conv:timecreated'] = 'Sohbetin başladığı zaman';
$string['privacy:metadata:conv:timemodified'] = 'Son mesajın zamanı';
$string['privacy:metadata:conv:title'] = 'İlk sorunun başı';
$string['privacy:metadata:conv:userid'] = 'Sohbetin sahibi olan kullanıcı';
$string['privacy:metadata:msg'] = 'Kayıtlı sohbetlerin mesajları';
$string['privacy:metadata:msg:content'] = 'Mesajın metni';
$string['privacy:metadata:msg:role'] = 'Mesajın soru mu cevap mı olduğu';
$string['privacy:metadata:msg:timecreated'] = 'Mesajın yazıldığı zaman';
$string['privacy:metadata:preference:notice'] = 'Kullanıcının ilk sohbetten önce gösterilen bilgilendirmeyi okuduğu zaman';
$string['privacy:metadata:preference:retention'] = 'Kullanıcının sohbetlerinin ne kadar süre tutulacağı';
$string['privacy:metadata:prop'] = 'Asistanın bir öğretmene önerdiği değişiklikler; uygulanan değişiklikler geri alınabilsin diye son işlemden sonra 30 gün saklanır.';
$string['privacy:metadata:prop:courseid'] = 'Ders';
$string['privacy:metadata:prop:original'] = 'Değişiklikten önceki ders metinleri';
$string['privacy:metadata:prop:proposed'] = 'Önerilen metinler';
$string['privacy:metadata:prop:status'] = 'Önerinin beklemede, uygulanmış, geri alınmış ya da reddedilmiş olduğu';
$string['privacy:metadata:prop:timecreated'] = 'Değişikliğin önerildiği zaman';
$string['privacy:metadata:prop:timemodified'] = 'Önerinin son işlendiği zaman';
$string['privacy:metadata:prop:userid'] = 'Değişikliğin önerildiği öğretmen';
$string['privacy:metadata:use'] = 'Cevaplanan her soru için metin içermeyen bir satır; saatlik sınır ve kullanım toplamları için. 30 gün tutulur.';
$string['privacy:metadata:use:completiontokens'] = 'Cevabın token cinsinden uzunluğu';
$string['privacy:metadata:use:courseid'] = 'Ders';
$string['privacy:metadata:use:prompttokens'] = 'İsteğin token cinsinden uzunluğu';
$string['privacy:metadata:use:timecreated'] = 'Sorunun sorulduğu zaman';
$string['privacy:metadata:use:userid'] = 'Soruyu soran kullanıcı';
$string['privacyheading'] = 'Veri koruma';
$string['privacyheading_desc'] = 'Sohbetlerinin kaydedilip kaydedilmeyeceğine ve ne kadar tutulacağına öğrenciler kendileri karar verir (varsayılan: kaydedilmez); yöneticiler sohbetleri okuyamaz. Servise soru, sohbet ve ders içeriği gider; öğrencinin adı, e-posta adresi ve Moodle kimliği asla gitmez.';
$string['proposal_after'] = 'Önerilen';
$string['proposal_applied'] = 'Derse uygulandı.';
$string['proposal_apply'] = 'Uygula';
$string['proposal_before'] = 'Mevcut';
$string['proposal_discard'] = 'Reddet';
$string['proposal_discarded'] = 'Öneri reddedildi.';
$string['proposal_error_activity'] = '{$a} numaralı etkinlik bu derste yok.';
$string['proposal_error_invalid'] = 'Asistan eksik bir öneri gönderdi. Lütfen yeniden sorun.';
$string['proposal_error_nochange'] = '"{$a}" için yapılan öneri hiçbir şeyi değiştirmiyor.';
$string['proposal_error_nopermission'] = 'Bu değişikliği yapma yetkiniz yok.';
$string['proposal_error_notincluded'] = 'Asistan "{$a}" metinlerinin tamamını göremediği için onları buradan değiştiremez. O etkinliği açıp orada sorun.';
$string['proposal_error_section'] = '{$a} numaralı bölüm bu derste yok.';
$string['proposal_error_toomany'] = 'Bir cevaptaki yalnızca ilk {$a} öneri tutuldu.';
$string['proposal_fillform'] = 'Forma yerleştir';
$string['proposal_formhint'] = 'Bu etkinliğin yalnızca kendi düzenleme formunun doğru kaydettiği ayarları var: formda açın, kontrol edin ve kaydedin.';
$string['proposal_missing'] = 'Etkinlik ya da bölüm artık yok.';
$string['proposal_newlabel'] = 'Yeni metin ve medya alanı';
$string['proposal_newpage'] = 'Yeni sayfa: {$a}';
$string['proposal_openform'] = 'Formda aç';
$string['proposal_status_applied'] = 'Uygulandı';
$string['proposal_status_discarded'] = 'Reddedildi';
$string['proposal_status_pending'] = 'Kararınızı bekliyor';
$string['proposal_status_undone'] = 'Geri alındı';
$string['proposal_undo'] = 'Geri al';
$string['proposal_undo_confirm_new'] = 'Bu önerinin oluşturduğu etkinlik silinsin mi? Geri dönüşüm kutusu açıksa oraya gider.';
$string['proposal_undo_confirm_update'] = 'Değişiklikten önceki metinler geri yüklensin mi?';
$string['proposal_undone'] = 'Değişiklik geri alındı.';
$string['proposal_updateactivity'] = '{$a->type}: {$a->name}';
$string['proposal_updatesection'] = 'Bölüm: {$a}';
$string['proposal_view'] = 'Görüntüle';
$string['proposal_where'] = 'Yer: {$a}';
$string['provider'] = 'Servis';
$string['provider_anthropic'] = 'Claude (Anthropic)';
$string['provider_desc'] = 'Soruları hangi AI servisi cevaplasın. Her servisin anahtarı ayrı saklanır; servisler arasında istediğiniz zaman geçiş yapabilirsiniz.';
$string['provider_gemini'] = 'Google Gemini';
$string['provider_openai'] = 'OpenAI';
$string['provider_openaicompatible'] = 'OpenAI uyumlu servis (özel adres)';
$string['quizlock'] = 'Quiz sırasında duraklat';
$string['quizlock_desc'] = 'Öğrencinin derste bitmemiş bir quiz denemesi varken asistan cevap vermez.';
$string['reason_disabled'] = 'AI asistan kapalı.';
$string['reason_euonly'] = 'AI asistan kullanılamıyor: site yalnızca verileri AB\'de işleyen servislere izin veriyor.';
$string['reason_nocapability'] = 'Bu derste AI asistanı kullanma izniniz yok.';
$string['reason_notconfigured'] = 'AI asistan henüz kurulmadı.';
$string['reason_quiz'] = 'Bu derste bitmemiş bir quiz denemeniz varken AI asistan duraklatılır.';
$string['reasoningeffort'] = 'Düşünme düzeyi';
$string['reasoningeffort_default'] = 'Modelin varsayılanı';
$string['reasoningeffort_desc'] = 'Modelin cevaplamadan önce ne kadar düşüneceği: daha yüksek düzey daha dikkatli ama daha yavaş ve pahalı cevaplar verir. Yalnızca destekleyen modellere gönderilir (GPT-5 ve sonrası, o serisi; Claude Opus 4.5+, Sonnet 4.6+; Gemini). Düşünmenin kapatılamadığı modellerde "Yok" modelin varsayılanını korur.';
$string['reasoningeffort_high'] = 'Yüksek';
$string['reasoningeffort_low'] = 'Düşük (sohbet için önerilir)';
$string['reasoningeffort_medium'] = 'Orta';
$string['reasoningeffort_none'] = 'Yok';
$string['retention_days'] = '{$a} gün';
$string['retention_desc'] = 'Buna yalnızca siz karar verirsiniz. Daha kısa bir süre seçerseniz eski sohbetler hemen silinir. "Kaydetme" seçeneğinde sohbet yalnızca bu tarayıcı sekmesinde kalır.';
$string['retention_forever'] = 'Ben silene kadar';
$string['retention_notsaved'] = 'Kaydetme';
$string['retention_oneday'] = '1 gün';
$string['retention_title'] = 'Sohbetlerimi sakla';
$string['retentionsaved'] = 'Tercihiniz kaydedildi.';
$string['retentionstatus'] = 'Sohbetler: {$a}';
$string['retry'] = 'Tekrar dene';
$string['send'] = 'Gönder';
$string['settings'] = 'Ayarlar';
$string['status_checked'] = 'son kontrol: {$a}';
$string['status_disabled'] = 'Asistan kapalı: öğrenciler henüz görmüyor.';
$string['status_error'] = 'Bağlantı yok:';
$string['status_euwarning'] = '"Yalnızca AB" açık ama bu bağlantı AB\'de işlemeyi garanti etmiyor: öğrenciler asistanı kullanamaz.';
$string['status_freetier'] = 'Bu Gemini anahtarı Google\'ın ücretsiz katmanında: model başına dakikada yalnızca birkaç soruya izin var ve Google soruları ürünlerini geliştirmek için kullanabilir. Öğrenciler kullanmadan önce anahtarın Google Cloud projesinde faturalandırmayı açın.';
$string['status_lasterror'] = 'Son cevaplanamayan soru ({$a->time}, {$a->model}): {$a->error}';
$string['status_lastfallback'] = '{$a->time}: {$a->from} aşırı yoğundu, bu yüzden {$a->to} cevap verdi.';
$string['status_modelchanged'] = '"{$a->old}" modeli bu serviste yok, bu yüzden "{$a->new}" seçildi. Aşağıdaki Model menüsünden başka birini seçebilirsiniz.';
$string['status_modelmissing'] = 'Bu anahtar "{$a}" modelini kullanamıyor. Aşağıdaki Model menüsünden başka bir model seçip kaydedin.';
$string['status_models'] = '{$a} sohbet modeli erişilebilir';
$string['status_nokey'] = 'Henüz API anahtarı yok. Aşağıya yapıştırıp kaydedin.';
$string['status_ok'] = 'Bağlandı: {$a->provider}, model {$a->model}';
$string['status_pending'] = 'Bağlantı, bu ayar sayfası açıldığında kontrol edilir.';
$string['status_switched'] = 'Yapıştırılan anahtar {$a} servisine ait: o servis için kaydedildi ve etkin servis o oldu.';
$string['status_usage'] = 'Son 24 saat: {$a->dayquestions} soru. Son 7 gün: {$a->weekusers} kullanıcıdan {$a->weekquestions} soru, {$a->weektokens} token.';
$string['stop'] = 'Durdur';
$string['stopped'] = 'Durduruldu.';
$string['suggestion_concepts'] = 'Anahtar kavramları listele';
$string['suggestion_page'] = 'Bu sayfayı basitçe açıkla';
$string['suggestion_quiz'] = 'Kendimi sınamam için bana 3 soru sor';
$string['suggestion_summary'] = 'Bu dersi özetle';
$string['task_cleanup'] = 'Süresi dolan AI asistan sohbetlerini ve önerilerini sil';
$string['teacher_emptytitle'] = 'Bu derste ne üzerinde çalışalım?';
$string['teacher_placeholder'] = 'Yeni sayfa, yeniden yazım, çeviri isteyin…';
$string['teacher_suggestion_examples'] = 'Bu sayfaya örnekler ve kısa bir özet ekle';
$string['teacher_suggestion_newpage'] = 'Dersin sonraki konusu için bir sayfa taslağı hazırla';
$string['teacher_suggestion_review'] = 'Dersin yapısını gözden geçir ve iyileştirme öner';
$string['teacher_suggestion_simplify'] = 'Bu metni daha açık ve okunur yap';
$string['teacher_suggestion_summaries'] = 'Bölümler için kısa özetler yaz';
$string['teacher_suggestion_translate'] = 'Bu metni çok dilli yap: İngilizce, Türkçe, Almanca ve Hırvatça';
$string['teachermode'] = 'Öğretmen modu';
$string['testconnection'] = 'Bağlantıyı şimdi kontrol et';
$string['thinking'] = 'Düşünüyor…';
$string['toggle'] = 'AI asistan';
$string['truncated'] = 'Cevap çok uzun olduğu için kesildi.';
$string['undo_error_changed'] = 'Öneri uygulandıktan sonra içerik değiştirildiği için otomatik olarak geri alınamıyor.';
$string['userhourlylimit'] = 'Kullanıcı başına saatlik soru';
$string['userhourlylimit_desc'] = 'Yüksek maliyete karşı korur. 0 sınırsız demektir.';
$string['writingproposal'] = 'Öneri yazılıyor…';
