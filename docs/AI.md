# AI Ders Asistanı (`local_diverse_assistant`)

Öğrenciler her dersin sağ kenarındaki **AI asistan** sekmesinden açılan panelde, o dersin içeriği hakkında bir yapay
zekâ modeliyle yazışır: özet, açıklama, örnek, kendini sınama soruları. Dersi düzenleyebilen **öğretmenler** aynı
panelde içerik asistanını görür: AI yeni sayfa, metin düzeltme, çeviri önerir; öğretmen onaylayınca derse uygulanır.
Bu belge eklentinin ne yaptığını, verinin nereye gittiğini, nasıl kurulup test edildiğini anlatır.

- **Kod:** [`local/diverse_assistant/`](../local/diverse_assistant/) (kendi başına çalışan Moodle eklentisi, Kural 2)
- **Servisler:** OpenAI, Claude (Anthropic), Google Gemini ve OpenAI uyumlu servisler (Mistral, OpenRouter, Ollama...).
  Her servisin anahtarı ayrı saklanır; servisler arasında istenen zaman geçilir.

## Öğrenci ne görür

- Ders sayfasında ve dersin etkinlik sayfalarında sağ kenarda dikey **AI asistan** sekmesi. Tıklayınca sağda panel
  açılır; geniş ekranda sayfa kayar, telefonda panel tam ekran açılır. Panel açık kalırsa sayfa değişince de açık gelir.
- İlk kullanımda bilgilendirme: hangi servisin kullanıldığı, AI'ın hata yapabileceği, kişisel bilgi yazılmaması,
  saklama tercihinin öğrenciye ait olduğu. "Anladım, başla" ile sohbet açılır.
- Hazır öneriler (Bu dersi özetle, Bu sayfayı basitçe açıkla, Anahtar kavramlar, Bana 3 soru sor) ya da serbest soru.
  Cevap yazılırken parça parça akar; "Durdur" ile kesilebilir.
- **Ayarlar (dişli):** "Sohbetlerimi sakla": Kaydetme (varsayılan) / 1 gün / 7 gün / 30 gün / 1 yıl / Ben silene
  kadar. "Tüm sohbetlerimi sil" düğmesi. Kayıt açıksa **Kayıtlı sohbetler** listesi görünür (açma, silme).
- Öğrencinin derste bitmemiş bir quiz denemesi varken asistan cevap vermez (ayardan kapatılabilir).

## Öğretmen ne görür (öğretmen modu)

Dersi düzenleme yetkisi olan öğretmen (`local/diverse_assistant:teach` + `moodle/course:manageactivities`; varsayılan
editingteacher ve manager) paneli açınca **Öğretmen modu** etiketiyle içerik asistanını görür. Öğretmen "Rolü
değiştir > Öğrenci" yaparsa öğrencinin gördüğü asistanı görür.

- **Ne isteyebilir:** yeni sayfa ("3. bölüme sözleşme türleri hakkında bir sayfa ekle"), yeni metin ve medya alanı,
  bir etkinliğin adını/açıklamasını ya da sayfa içeriğini yeniden yazma ("bu sayfayı sadeleştir"), bölüm adı ve özeti,
  **çok dilli yapma** ("bu sayfayı İngilizce ve Türkçe yap": metin `{mlang en}…{mlang}{mlang tr}…{mlang}` bloklarıyla
  yazılır). Hazır öneriler de var (etkinlik sayfasında: sadeleştir, 4 dile çevir, örnek ekle; ders sayfasında: yeni
  sayfa, bölüm özetleri, ders yapısını gözden geçir).
- **AI hiçbir şeyi kendisi değiştirmez.** Değişiklikler cevabın altında **öneri kartı** olarak gelir: ne değişiyor,
  hangi bölümde, "Mevcut" ve "Önerilen" metin (çok dilli metinlerde her dil EN/TR/DE/HR etiketiyle görünür). Kartta:
  - **Uygula:** sayfa, metin ve medya alanı ve bölümlerde tek tıkla derse uygulanır (Moodle'ın kendi işlevleriyle:
    olaylar, kayıtlar, geri dönüşüm kutusu normal çalışır). Uygulanınca **Görüntüle** ve **Geri al** çıkar.
  - **Formda aç:** Moodle'ın düzenleme formu AI metniyle dolu açılır; öğretmen kontrol edip **Kaydet**'e basar.
    Ödev, quiz, forum gibi etkinliklerde yalnızca bu yol var: bu etkinliklerin ayarlarını (ör. ödevin teslim türleri)
    yalnızca kendi formları doğru kaydeder, formsuz yazmak onları bozar. Öğretmen zaten o formdaysa düğme
    **Forma yerleştir** olur ve sayfa değişmez.
  - **Geri al:** uygulanan değişiklik 30 gün içinde geri alınır (yeni etkinlik silinir ve geri dönüşüm kutusuna gider;
    değişen metin eski haline döner).
  - **Reddet.**
- **Güvenlik kilitleri:** öneriden sonra metin başkası tarafından değiştirildiyse "Uygula" üzerine yazmaz; uygulandıktan
  sonra elle düzenlendiyse "Geri al" o düzenlemeyi silmez. AI yalnızca metnini **tamamen gördüğü** etkinlik ve bölümleri
  değiştirebilir (ders çok uzunsa öğretmen o etkinliği açıp orada sormalı). AI'ın yazdığı HTML, kullanıcı girdisi gibi
  temizlenir (script vb. atılır). Bir cevapta en fazla 5 öneri.
- **Düzenleme formlarında "Editöre ekle":** öğretmen bir etkinliğin ya da bölümün düzenleme formundayken AI'ın
  cevabının altında bu düğme çıkar; cevap, son tıklanan TinyMCE editörüne imlecin olduğu yere eklenir.
- Öğretmen modunda quiz kilidi uygulanmaz; saatlik soru sınırı ve saklama tercihi öğrencilerdeki gibidir.

## Veri nereye gider (gizlilik)

| Ne | Nereye | Ne kadar |
|---|---|---|
| Soru, sohbetin önceki mesajları, öğrencinin görebildiği ders içeriği | Seçilen AI servisi (OpenAI, Claude, Gemini...) | Servisin kendi saklama kuralı |
| Kayıtlı sohbetler | Moodle veritabanı (`local_diverse_assistant_conv`, `_msg`) | **Öğrencinin seçtiği süre**; varsayılan hiç |
| "Kaydetme" seçiliyken sohbet | Yalnızca tarayıcı sekmesi (`sessionStorage`) | Sekme kapanınca / oturum değişince silinir |
| Kullanım satırı (metin yok: kullanıcı, ders, token sayısı, zaman) | Moodle veritabanı (`local_diverse_assistant_use`) | 30 gün (saatlik sınır ve toplamlar için) |
| Öğretmen modu: dersin kayıtlı metinleri (adlar, açıklamalar, sayfalar, bölüm özetleri; gizli etkinlikler dahil) | Seçilen AI servisi | Servisin kendi saklama kuralı |
| Öğretmen modu: öneriler (önerilen metin ve geri alma için eski metin) | Moodle veritabanı (`local_diverse_assistant_prop`) | Son işlemden 30 gün sonra; kayıtlı sohbetle birlikte ya da "Tüm sohbetlerimi sil" ile daha erken |

- **Servise asla gitmeyenler:** öğrencinin adı, e-posta adresi, Moodle kimliği (OpenAI'ın `user` alanı da gönderilmez),
  forum mesajları, ödev teslimleri, quiz soruları, gizli ya da kısıtlı etkinlikler.
- **Ders içeriği** her etkinliğin kendi resmi web servis fonksiyonuyla okunur (`mod_page_get_pages_by_courses` vb.),
  yani Moodle'ın görünürlük ve yetki kontrolleri uygulanır; başka eklentilerin tablolarına doğrudan erişilmez.
  Filtreler açık okunduğu için çok dil filtresi yalnızca öğrencinin dilini bırakır.
- **Öğretmen modunda** metinler filtresiz, kayıtlı halleriyle okunur (Moodle'ın düzenleme öncesi kontrolü
  `can_update_moduleinfo()` ile): `{mlang}` bloklarının tüm dilleri ve resim bağlantıları (`@@PLUGINFILE@@`) korunur;
  yoksa düzenlenen metinde diğer diller silinirdi. Öğrenci verisi (teslim, forum mesajı, not) burada da gönderilmez.
- **Yöneticiler sohbetleri göremez**; ayar sayfasında yalnızca toplam soru/kullanıcı/token sayıları var. Saklama süresi
  bir yönetici ayarı değildir, öğrencinin kendi tercihidir.
- Süresi dolan sohbetleri saatlik zamanlanmış görev (`\local_diverse_assistant\task\cleanup`) siler; öğrenci daha kısa
  bir süre seçerse eski sohbetler anında silinir. Kullanıcı ya da ders silinince verileri de silinir.
- Moodle gizlilik API'si tam: veri dışa aktarma, kullanıcı/ders bazında silme (GDPR talepleri).
- **"Yalnızca AB" ayarı** (varsayılan kapalı): açılırsa asistan yalnızca AB'de işlemeyi garanti eden bağlantıyla
  çalışır (OpenAI için AB veri yerleşimli proje ve `eu.api.openai.com`). İki senaryo da hazır; karar verilince yalnızca
  bu ayar ve anahtar değişir. Claude ve Gemini'nin doğrudan API'lerinde yalnızca AB seçeneği yok (Claude'da konum yalnızca
  "us" ya da "global"); AB şartı gelirse bunlar için AWS Bedrock / Google Vertex AI bağlantısı eklenmeli.
- **Gemini ücretsiz katmanı:** Google, ücretsiz katmandaki istekleri ürünlerini geliştirmek için kullanabilir ve kota çok
  düşüktür (denemede `gemini-3.8-flash` için dakikada 5 soru). Öğrencilere açmadan önce anahtarın Google Cloud
  projesinde faturalandırma açılmalı.

> Hukuki not: servisle veri işleme sözleşmesi (AVV/DPA) ve gerekirse etki değerlendirmesi (DPIA) üniversitelerin veri
> koruma sorumlularının kararıdır. **Canlı site HTTP ile açılırsa sorular ağda şifrelenmeden gider**; canlıda AI'ı
> açmadan önce alan adı ve HTTPS önerilir.

## Yönetici kurulumu

Site yönetimi > Eklentiler > Yerel eklentiler > **DIVERSE AI asistan** (`/admin/settings.php?section=local_diverse_assistant`)

1. **Servis:** OpenAI, Claude (Anthropic), Google Gemini ya da OpenAI uyumlu servis (+ API adresi).
2. **API anahtarı:** her servisin kendi alanı var, yalnızca seçili servisinki görünür.

   | Servis | Anahtar nereden | Başlangıcı |
   |---|---|---|
   | OpenAI | platform.openai.com > API keys (ödeme tanımlı olmalı; ChatGPT aboneliği API anahtarı değildir) | `sk-` |
   | Claude | console.anthropic.com > API keys | `sk-ant-` |
   | Gemini | aistudio.google.com > Get API key (ödeme tanımlı proje) | `AIza` |

   **Otomatik bağlanma:** Claude ya da Gemini anahtarı hangi alana yapıştırılırsa yapıştırılsın kendi servisine
   kaydedilir ve etkin servis o olur (durum kutusu bunu bildirir). Anahtarlar Moodle'ın şifreleme anahtarıyla
   (`moodledata/secret/`) şifreli saklanır ve sayfaya bir daha yazılmaz; alan boş bırakılırsa kayıtlı anahtar değişmez,
   "Kayıtlı anahtarı sil" ile silinir. Bu klasör kaybolursa anahtarlar yeniden girilir.
3. **Kaydet:** sayfanın üstündeki durum kutusu bağlantıyı kendiliğinden kontrol eder: "Bağlandı: …, model …" ya da
   hatanın nedeni (anahtar reddedildi...). "Bağlantıyı şimdi kontrol et" ile tekrar denenir.
4. **Model:** kontrol sonrası menü, anahtarın kullanabileceği sohbet modelleriyle dolar (gömme, ses, görsel, video, eski
   tarihli sürümler listelenmez). Seçili model yeni serviste yoksa (ör. OpenAI'dan Claude'a geçince) servisin önerileni
   kendiliğinden seçilir: `gpt-6-luna` (OpenAI), `claude-opus-5` (Claude), `gemini-3.8-flash` (Gemini). Menüden daha ucuz
   modeller de seçilebilir (ör. Claude Sonnet / Haiku, Gemini Flash-Lite).
   **Düşünme düzeyi:** Düşük önerilir; yalnızca destekleyen modellere gönderilir (OpenAI'da `reasoning_effort`, Claude'da
   `effort`, Gemini'de `reasoning_effort`).
5. **Asistanı aç** kutusunu işaretleyip kaydedin. Sekme, `local/diverse_assistant:use` yetkisi olan herkese görünür
   (varsayılan: öğrenci, öğretmen, yönetici).

Diğer ayarlar: kullanıcı başına saatlik soru (varsayılan 30), gönderilen ders içeriği uzunluğu (varsayılan 60.000
karakter ≈ 15.000 token), quiz sırasında duraklatma (açık).

**Maliyet (1M token başına girdi / çıktı):** GPT-6 Luna 0,10 $ / 0,50 $; Claude Opus 5 5 $ / 25 $, Claude Sonnet 5
2 $ / 10 $, Claude Haiku 4.5 1 $ / 5 $. Ders içeriği ~20.000 token varsayımıyla bir soru GPT-6 Luna'da yaklaşık
0,002-0,003 $, Claude Opus 5'te yaklaşık 0,1 $ tutar. Claude'da ders içeriği önbelleğe alınır: aynı derste art arda
sorularda içerik çok daha ucuz okunur. Gemini fiyatları için Google'ın fiyat sayfasına bakın.

## Nasıl çalışır (geliştiriciler için)

| Dosya | Görev |
|---|---|
| `classes/hook_callbacks.php`, `classes/output/panel.php`, `templates/panel.mustache` | Paneli ders sayfalarına ekler (`before_footer_html_generation` hook; tema dosyası kopyalanmaz) |
| `amd/src/assistant.js`, `amd/src/repository.js` | Panel davranışı, akış okuma, `sessionStorage` |
| `stream.php` | Soruyu alır, cevabı **server-sent events** ile akıtır; AI beklenirken oturum kilidini bırakır |
| `classes/local/chat_service.php` | Kontroller (izin, sınır, quiz), geçmiş, sistem talimatı, kayıt |
| `classes/local/course_materials.php` | Ders içeriğini düz metin olarak toplar |
| `classes/local/provider/` | Servis bağlayıcıları: `provider` (soyut), `openai_compatible`, `openai`, `gemini`, `anthropic`, `factory`, akış çözücüler |
| `vendor/`, `composer.json`, `thirdpartylibs.xml` | Resmi Anthropic PHP SDK'sı (Claude için) ve bağımlılıkları |
| `classes/local/retention.php`, `classes/local/store.php` | Öğrencinin saklama tercihi, veritabanı işlemleri |
| `classes/local/connection.php`, `settings.php`, `testconnection.php` | Ayarlar ve bağlantı durumu |
| `classes/privacy/provider.php` | Gizlilik API'si |
| `classes/local/teacher/course_content.php` | Öğretmen modu: dersin kayıtlı metinleri, `[cmid=N]` / `[section=N]` kimlikleriyle; hangi öğelerin tamamen gönderildiği |
| `classes/local/teacher/tools.php` | Modelin çağırabileceği araçlar (`propose_new_page`, `propose_new_label`, `propose_activity_update`, `propose_section_update`) |
| `classes/local/teacher/proposals.php` | Araç çağrılarını kontrol eder, öneri olarak saklar, kart verisini hazırlar |
| `classes/local/teacher/applier.php` | Uygula / Geri al / Reddet (Moodle'ın `create_module`, `update_module`, `course_delete_module`, `formatactions::section()->update`) |
| `classes/external/proposal_action.php`, `amd/src/editor.js`, `templates/proposal.mustache` | Kart düğmeleri, formu doldurma ve "Editöre ekle" (TinyMCE) |

- **Servisler nasıl bağlanıyor:**
  - OpenAI ve OpenAI uyumlu servisler: Chat Completions API, Moodle'ın `curl` sınıfıyla akış.
  - Gemini: Google'ın resmi OpenAI uyumlu uç noktası (`generativelanguage.googleapis.com/v1beta/openai`), aynı kod.
  - Claude: resmi **Anthropic PHP SDK'sı** (`vendor/anthropic-ai/sdk`, v0.51). SDK'ya Moodle'ın kendi HTTP istemcisi
    (`\core\http_client`) verilir; proxy ve engellenen adres ayarları Claude isteklerinde de geçerlidir. Ders içeriğini
    taşıyan sistem talimatı önbelleğe alınır. Opus 5 / Fable 5 ailesinde Anthropic'in güvenlik sınıflandırıcısı bir
    soruyu reddederse `fallbacks: "default"` (beta `server-side-fallback-2026-07-01`) ile soru önerilen yedek modelde
    yeniden çalışır; zincirin tamamı reddederse öğrenci anlaşılır bir mesaj görür.
- **Servis yoğunken:** servis "aşırı yoğun" (500/502/503/504, Claude'da 529) derse ve öğrenciye henüz metin gitmediyse
  istek 1 ve 2,5 saniye arayla iki kez daha denenir (Claude'da bunu SDK yapar). Yine olmazsa ya da modelin kotası
  dolduysa (429), aynı modelin başka sürümleri denenir (ör. `gemini-3.8-flash` → `gemini-3.7-flash` → `gemini-3.6-flash`;
  önizleme sürümleri kullanılmaz). Öğrenci ancak hepsi başarısız olursa "AI servisi şu an çok yoğun" mesajını görür.
  Ayar sayfasındaki durum kutusu son yedek model kullanımını ve son cevaplanamayan soruyu (servisin hata metniyle, öğrenci
  bilgisi olmadan) gösterir; Gemini ücretsiz katman kotası görülürse ayrıca uyarır.
- **Öğretmen modunda araç çağırma (tool calling):** modele dört "öneri" aracı verilir; araç çağrıları çalıştırılmaz,
  yalnızca öneri kartına dönüşür. OpenAI ve Gemini'de Chat Completions `tools`, Claude'da SDK'nın `tools` parametresi.
  OpenAI'ın Chat Completions API'si GPT-6 modellerinde araçları düşünme düzeyiyle birlikte kabul etmiyor; öğretmen
  modunda bu modellere `reasoning_effort: none` gider (başka bir model aynı hatayı verirse bir kez düşünmeden yeniden
  sorulur). Önceki önerilerin durumu (bekliyor/uygulandı) sonraki sorularda modele hatırlatılır.
- **Sayfa güncellemesi nasıl güvenli:** etkinliğin verisi, düzenleme formunun yüklediği gibi yüklenir
  (`get_moduleinfo_data()` ve formun kendi `data_preprocessing()`'i), yalnızca metinler değiştirilip `update_module()`
  ile kaydedilir; görünüm ayarları, tamamlama, erişim kısıtları aynen kalır. Bu yalnızca sayfa ve metin ve medya alanı
  için yapılır; diğer etkinlikler formda açılır.
- **Gemini akış ortasında hata:** Gemini yoğunken bazen cevabın ortasında akışa SSE biçiminde olmayan bir hata gövdesi
  (`[{"error":{"code":503,...}}]`) yazıp bağlantıyı kapatıyor. Akış çözücü bunu yakalar ve "servis yoğun" hatası verir;
  yarım cevap tamamlanmış gibi görünmez.
- **SDK'yı güncellemek:** eklenti klasöründe `composer update anthropic-ai/sdk`. Moodle'ın zaten içerdiği Guzzle ve
  PSR paketleri `composer.json`'da "sağlanıyor" olarak işaretli, tekrar kurulmaz; `thirdpartylibs.xml`'deki sürümü
  güncelleyin.
- **Yeni servis eklemek:** `classes/local/provider/` altına `provider`'dan türeyen bir sınıf, `factory`'de bir satır,
  dil dosyalarında adı ve anahtar açıklaması. Anahtar alanı ve model menüsü kendiliğinden gelir; sohbet kodu değişmez.
- Moodle 5.0'ın hazır AI altyapısı (`core_ai`) kullanılmadı: tek mesajlık çalışıyor, akış yok ve her isteği çekirdek
  tablolara süresiz yazıyor; öğrencinin kendi saklama tercihi bununla mümkün değil.
- `stream.php` cPanel'de sunucu tamponlaması yüzünden akmayabilir; o durumda cevap tek parça gelir, işlev bozulmaz.
  Sunucunun dışarıya bağlanmasına güvenlik duvarı izin vermeli (443 portu: api.openai.com, api.anthropic.com,
  generativelanguage.googleapis.com).
- Arayüz metinleri EN/TR/DE/HR (`lang/`); renkler yalnızca temanın `--bs-*` değişkenlerinden (Kural 1).

## Yerelde test

```bash
# Kod değişikliği kapsayıcıya: docker compose watch (ya da docker compose up -d --build moodle)
docker compose exec -T -u www-data moodle php admin/cli/upgrade.php --non-interactive
docker compose exec -T -u www-data moodle php admin/cli/purge_caches.php
```

JavaScript değişince derleme (repo kökünde, Node 22): `npx npm@10 ci` (bir kez; npm 11 Moodle'ın kilit dosyasını kabul
etmiyor), ardından `npx grunt amd --root=local/diverse_assistant`.

**PHPUnit** (yalnızca kapsayıcıda, repoya bir şey eklemez; kapsayıcı yeniden oluşturulunca tekrarlanır):

```bash
docker compose exec -T moodle bash -c 'php -r "copy(\"https://getcomposer.org/download/latest-stable/composer.phar\", \"/tmp/composer.phar\");" && cd /var/www/html && COMPOSER_ALLOW_SUPERUSER=1 php /tmp/composer.phar install --no-interaction && chown -R www-data:www-data vendor'
# config.php'de require_once(... setup.php) satırından önce:
#   $CFG->phpunit_prefix = 'phpu_';  $CFG->phpunit_dataroot = '/var/www/phpunitdata';
docker compose exec -T moodle bash -c 'mkdir -p /var/www/phpunitdata && chown www-data:www-data /var/www/phpunitdata'
docker compose exec -T -u www-data -e COMPOSER_HOME=/tmp/composer-www moodle php admin/tool/phpunit/cli/init.php
docker compose exec -T -u www-data moodle vendor/bin/phpunit --testsuite local_diverse_assistant_testsuite
```

`init.php` composer'ı güncellemeye çalışır; `COMPOSER_HOME` verilmezse `www-data` kullanıcısı yazamadığı için durur.

74 test: akış çözücü; yoğunlukta yeniden deneme ve yedek model; OpenAI, Gemini ve Claude bağlayıcıları (sahte HTTP; Claude'da gerçek SDK, Moodle'ın HTTP
istemcisi üzerinden); anahtar alanı ve servisler arası otomatik geçiş; model menüsü; ders içeriğinin gizlilik kuralları
(gizli/kısıtlı etkinlik, forum mesajı, çok dil); kişisel veri gönderilmemesi; saatlik sınır; quiz kilidi; saklama
tercihi ve temizlik görevi; gizlilik API'si. Öğretmen modu: araç çağrılarının okunması (OpenAI, Gemini, Claude), akış
ortasındaki hata gövdesi, öğretmenin gördüğü ham içerik, öneri kontrolleri, yeni sayfa/sayfa güncelleme/etiket/bölüm
için uygula ve geri al, ayarların korunması, araya giren değişiklikte üzerine yazmama, ödevin forma yönlendirilmesi,
başka öğretmenin öneriye erişememesi, önerilerin sohbet geçmişinde saklanması.

## Sonraki adımlar

- AB şartı gelirse: Claude için AWS Bedrock (Frankfurt) ya da Vertex AI, Gemini için Vertex AI (AB bölgesi), Azure
  OpenAI.
- PDF ve dosya içeriği, kitap bölümleri; cevaplarda kaynak etkinliğe link.
- Öğretmen modu için: quiz sorusu önerme (soru bankası), kitap bölümleri; OpenAI'da düşünerek araç kullanmak için
  Responses API'ye geçiş.
