# DIVERSE Moodle: Ajan ve Geliştirici Kuralları

Bu dosya, bu repoda çalışan **her yapay zekâ ajanı** (Claude Code, Codex, Cursor, Copilot, Gemini...) ve her
geliştirici içindir. Ajan her görevden önce bu dosyayı okur ve kurallara uyar. Geliştirici de ajanın ne
yapacağını Kural 3'teki plandan, ne yaptığını görev sonundaki rapordan öğrenir.

Kurallar proje sahibinin kararıdır. Bir kural işi engelliyorsa ajan kuralı kendi başına esnetmez; durumu
geliştiriciye anlatır ve karar ister.

## Proje özeti

- **Moodle 5.0.7** (sürüm 2025041407). DIVERSE European University Alliance üyesi üniversitelerin ortak LMS'i.
- **Tema:** `theme/diverse`, **Boost Union** (v5.0-r26) alt teması. Renkler ve fontlar: [DESIGN.md](DESIGN.md).
- **Partnerler:** `tool_mutenancy` + `tool_mulib` ile her üniversite bir tenant.
- **Çok dil:** `filter/multilang2` (`{mlang xx}...{mlang}`) ve EN / TR / DE / HR dil paketleri.
- **Canlı ders:** Moodle'ın kendi BigBlueButton modülü (`mod/bigbluebuttonbn`).
- **AI ders asistanı:** `local/diverse_assistant` ([docs/AI.md](docs/AI.md)). Sohbetlerin saklama süresini öğrenci seçer;
  yönetici ayarı değildir. Öğretmenler aynı panelde içerik önerileri alır; AI onaysız hiçbir şeyi değiştirmez.
- **Canlı sunucu:** cPanel (AlmaLinux), Docker yok. **Yerel test:** Docker (`docker-compose.yml`).
- **Site ayarları:** `setup/diverse_setup.php` (tekrar çalıştırılabilir, yalnızca eksik olanı ekler).
- **Geçmiş ve gerekçeler:** [docs/YAPILANLAR.md](docs/YAPILANLAR.md). Bir şeyin neden öyle yapıldığını merak
  ettiğinde değiştirmeden önce buraya bak.

---

## Kural 1: Tema ve renklerin genel yapısı bozulmaz

- Görünümle ilgili her işten önce [DESIGN.md](DESIGN.md) okunur.
- Renkler ve fontlar **yalnızca** `theme/diverse/scss/pre.scss` içinde tanımlıdır. Diğer SCSS dosyalarında renk kodu
  (`#...`, `rgb(...)`) yazılmaz, `$diverse-...` değişkenleri kullanılır. Kontrol:
  ```bash
  grep -rnE '#[0-9A-Fa-f]{3,8}\b|rgba?\(' theme/diverse/scss --include=*.scss | grep -v pre.scss
  ```
  Çıktı boş olmalı.
- Palet, fontlar, köşe yuvarlaklıkları ve sayfa genişlikleri **proje sahibinin onayı olmadan değişmez**. Onay
  gelirse değer `pre.scss` içinde değişir ve `DESIGN.md` aynı commit'te güncellenir.
- Yeni stil doğru dosyaya yazılır:

  | Dosya | İçerik |
  |---|---|
  | `pre.scss` | Renkler, fontlar, Bootstrap değişkenleri (yalnızca değişken) |
  | `post.scss` | Fontların `@font-face` tanımları, genel bileşen ayrıntıları |
  | `landing.scss` | Ziyaretçi ana sayfası |
  | `login.scss` | Giriş sayfası |
  | `platform.scss` | Dashboard, Derslerim, ders sayfası |
  | `admin.scss` | Site administration |

  Yeni bir sayfa dosyası gerekiyorsa `theme/diverse/lib.php` içindeki listeye eklenir.
- Boost Union veya Moodle şablonları (`templates/`, `layout/`) `theme/diverse` içine **kopyalanmaz**. Görünüm SCSS ile,
  davranış renderer'ı genişleterek (`classes/output/core_renderer.php`) değişir. Böylece Boost Union güncellemeleri
  temayı bozmaz.
- Platformun içinde cam efekti (glassmorphism), yıldızlı arka plan ve 3D kart kullanılmaz. Sekmeler "kutu sekme"
  (oval, aktif olan turuncu) olarak kalır.
- Erişilebilirlik: metin/zemin kontrastı en az 4.5:1 (WCAG 2.1 AA). Fontlar kendi sunucumuzdan gelir, CDN kullanılmaz.

## Kural 2: Ayarlar korunur, eklentiler bozulmaz, özel kod eklenti gibi bağımsız çalışır

**Tema ayarları değişmez.** `theme_diverse`, `theme_boost_union` ve `theme_boost` ayarları koddan, SQL'den veya
betikten değiştirilmez. Tek istisna `setup/diverse_setup.php` içindeki mevcut adımlardır. Bu betiğe yeni bir adım
eklemek proje sahibinin onayını ister.

**Başkasının kodu düzenlenmez.** Moodle çekirdeği ve üçüncü taraf eklentiler (`theme/boost_union`,
`filter/multilang2`, `admin/tool/mutenancy`, `admin/tool/mulib`, `mod/bigbluebuttonbn` ve diğerleri) olduğu gibi
kalır. Bunları düzenlemek, bir sonraki güncellemede değişikliğin kaybolması ya da güncellemenin bozulması demektir.
Özelleştirme şu iki yerden birinde yapılır:

1. Görünüm: `theme/diverse`.
2. Yeni özellik: **ayrı bir Moodle eklentisi**, tercihen `local/diverse_<ad>` (bileşen adı `local_diverse_<ad>`).

**Özel eklenti ve betik kuralları:**

- Standart Moodle eklenti yapısı: `version.php` (bağımlılıklar `dependencies` içinde), `lang/en/`, gerekiyorsa
  `db/access.php` (kendi yetkileri), `db/install.xml` + `db/upgrade.php`, `classes/`, `settings.php`.
- Yalnızca Moodle API'leri kullanılır: hook'lar, callback'ler, event'ler, `$DB`, Output API. Başka eklentinin iç
  sınıflarına ya da tablolarına doğrudan bağımlılık kurulmaz.
- Eklenti yalnızca **kendi** ayarlarını (`get_config('local_diverse_<ad>')`) ve kendi tablolarını yazar. Başka
  bileşenin ayarına yazmaz.
- Eklenti kaldırıldığında site eskisi gibi çalışır. Kendi verisini kaldırma sırasında temizler.
- CLI betikleri: `define('CLI_SCRIPT', true)`, `--dry-run` seçeneği, tekrar çalıştırılabilir (yalnızca eksik
  olanı ekler), yöneticinin bilinçli seçimini ezmez, yaptığı her şeyi ekrana yazar.

**Dil ayarları asla bozulmaz.** Site dili, dil listesi ve menüsü, `autolang`, yüklü dil paketleri, kullanıcıların
seçtiği diller ve `filter_multilang2` ayarları hiçbir işte değişmez. Bu kontrol her değişiklikten önce ve sonra
yapılır:

```bash
docker compose exec -T -u www-data moodle php setup/check_lang_settings.php > lang-before.txt
docker compose exec -T -u www-data moodle php setup/check_lang_settings.php > lang-after.txt
diff lang-before.txt lang-after.txt
```

`diff` çıktısı boş olmalı. Bu dosyalar commit'lenmez.

**Veritabanı dökümü repoda kalır.** `database/moodle.sql` proje sahibinin kararıyla repoda ve her dağıtımda yer
alır. Silinmez, `.gitignore`'a eklenmez, "temizlenmez". İçeriğini değiştirmek onay ister.

**Gizli bilgiler commit'lenmez.** `config.php`, `.env*`, `moodledata/`, BigBlueButton sunucu adresi ve gizli
anahtarı, API anahtarları repoya girmez.

**Teknik adlar değişmez.** `auth_gt4t_validator` gibi veritabanında kayıtlı bileşen adları, tablo ve ayar adları
onaysız yeniden adlandırılmaz; kullanıcı girişleri bozulur.

## Kural 3: Önce anlat, eksikse sor, bitince raporla

**Her istekten sonra, işe başlamadan önce** ajan geliştiriciye kısa bir plan yazar:

```
Anladığım:       <isteğin bir cümlelik özeti>
Değişecekler:    <dosyalar / ayarlar / veritabanı>
Nasıl:           <yöntem, 2-4 madde>
Dokunulmayacak:  <özellikle korunan şeyler: tema değerleri, dil ayarları, diğer eklentiler...>
Risk:            <ne bozulabilir, geri almak nasıl>
Test:            <Kural 4'teki hangi adımlar>
```

- **İstek eksik ya da iki anlama geliyorsa** ajan tahmin edip işe girişmez. Somut bir öneriyle sorar:
  "Şunu mu kastediyorsun: X? Önerim X, çünkü ... Yoksa Y mi olsun?" En fazla 2-3 seçenek sunar, önerdiğini ilk
  sıraya koyar.
- **Onay beklenmesi gereken işler:** Kural 1 ve 2'deki korunan alanlara dokunan her şey (tasarım değerleri, tema
  ve site ayarları, dil, veritabanı, `database/moodle.sql`, başka eklentiler), dosya silme ve geri alınması zor
  her işlem. Bunlarda ajan planı yazar ve açık bir "evet" bekler.
- Küçük, açık ve riski düşük işlerde ajan planı yazıp devam edebilir.
- **İş bitince** ajan rapor verir:
  - Değişen dosyalar ve her birinde ne değişti
  - Neden böyle yapıldı
  - Nasıl test edildi (çalıştırılan komutlar ve sonuçları)
  - **Test edilemeyenler** (açıkça yazılır, "çalışıyor" denmez)
  - Geliştiricinin yapması gerekenler (push, `upgrade.php`, önbellek temizleme, canlıda betik çalıştırma)
- Dil sade ve kısa olur. Geliştirici hangi dilde yazdıysa o dilde yanıt verilir (varsayılan Türkçe).
- Commit mesajları İngilizce, emir kipinde ve konuya odaklıdır: ne değişti ve neden. Bir commit tek konu içerir.

## Kural 4: Yerelde çalışmadan main'e push yok

- Ajan `main` dalına **kendisi push etmez**. Testler geçtikten sonra push'u geliştirici yapar.
- Mümkünse iş ayrı bir dalda yapılır (`git switch -c <konu>`) ve testlerden sonra `main`'e alınır.
- `main`'e gitmeden önce aşağıdaki kontrol listesi yerel Docker ortamında tamamlanır ve sonuçları rapora yazılır.

### Yerel ortam

Ayrıntılı kılavuz: [docker.md](docker.md). Docker Desktop açıkken, repo kök dizininde tek komut yeterlidir:

```bash
docker compose up -d --build
```

İlk açılışta veritabanı dökümü (`database/moodle.sql`) kendiliğinden yüklenir, `upgrade.php` ve
`setup/diverse_setup.php` çalışır. Site: http://localhost:8080. Moodle CLI komutları her zaman `-u www-data` ile çalıştırılır; root ile çalışan
komutlar `moodledata` içinde web sunucusunun yazamadığı önbellek dosyaları bırakır.

**Kod değişiklikleri kapsayıcıya `watch` ile gider.** Kod imajın içindedir, klasör olarak bağlanmaz: Windows'ta
bağlı klasörden okumak her sayfayı saniyelerce yavaşlatır. Çalışırken ayrı bir terminalde şu komut açık kalır:

```bash
docker compose watch
```

Repoda değiştirilen, eklenen ya da silinen her dosya birkaç saniye içinde kapsayıcıya aktarılır. `watch`
kapalıyken yapılan değişiklikler kapsayıcıya geçmez; o durumda `docker compose up -d --build moodle` ile imaj
yenilenir. Kapsayıcının içinde değiştirilen dosyalar repoya geri gelmez: kod her zaman repoda düzenlenir.

### Kontrol listesi

0. **Test edilen kod güncel mi:** `docker compose watch` açık olmalı ya da imaj değişiklikten sonra yeniden
   derlenmiş olmalı (`docker compose up -d --build moodle`).

1. **PHP sözdizimi:** değişen her PHP dosyası için
   `docker compose exec -T moodle php -l <dosya>`
2. **Sürüm değiştiyse** (`version.php` ya da yeni eklenti):
   `docker compose exec -T -u www-data moodle php admin/cli/upgrade.php --non-interactive`
3. **Önbellek:** `docker compose exec -T -u www-data moodle php admin/cli/purge_caches.php`
4. **Dil ayarları:** değişiklikten önce ve sonra `setup/check_lang_settings.php`, `diff` boş (Kural 2).
5. **Renkler:** Kural 1'deki `grep` komutunun çıktısı boş.
6. **Kurulum betiği:** `docker compose exec -T -u www-data moodle php setup/diverse_setup.php --dry-run` hatasız.
7. **Tarayıcı kontrolü:** masaüstü ve mobil genişlikte (375 px):
   - Ana sayfa (çıkış yapmış ziyaretçi) ve giriş sayfası
   - Dashboard ve Derslerim (öğrenci)
   - Bir ders sayfası (öğretmen)
   - Site administration (yönetici)
   - Sayfada PHP uyarısı/hatası yok, tarayıcı konsolunda JavaScript hatası yok.
8. Eklentinin PHPUnit/Behat testi varsa çalıştırılır.

Bir adım yapılamadıysa (ör. Docker çalışmıyor) ajan bunu raporda açıkça yazar ve push önermez.

---

## Bilinen tuzaklar

- **Döküm yalnızca ilk açılışta yüklenir** (veritabanı volume'u boşken). `database/moodle.sql` sonradan değişirse
  yerel veritabanına kendiliğinden geçmez: `docker compose down -v` ile sıfırlanır (yerel veriler silinir).
- **Kod klasörü kapsayıcıya bağlanmaz.** `docker-compose.yml` dosyasına `.:/var/www/html` bağlaması geri
  eklenmez; Windows'ta sayfaları 2-3 saniyeye çıkarır (imajın içindeyken 0,05 saniye). Değişiklikler
  `docker compose watch` ile aktarılır.
- **Kök dizindeki `.env`** dosyasını `docker compose` kendiliğinden okur. Normalde gerekmez; varsa yalnızca yerel
  değerler içerir (ör. `MOODLE_PORT=8090`, `MOODLE_URL=http://localhost:8090`). Başka değer yerel siteyi yanlış
  adresle kurar.
- **Windows'ta klonlama** `git clone -c core.longpaths=true ...` ile yapılır; bazı dosya yolları 260 karakteri aşıyor.
- **SCSS değişikliği görünmüyorsa** önbellek temizlenmemiştir (tema tasarımcı modu kapalı).
- **Canlı sunucuda Docker yok:** kod cPanel'de `git pull` ile güncellenir, ardından `admin/cli/upgrade.php` ve
  `setup/diverse_setup.php --production` çalıştırılır. Adımlar: [docs/CPANEL.md](docs/CPANEL.md). BigBlueButton
  sunucusu cPanel'e kurulamaz, ayrı sunucu gerekir.
- **Kök dizindeki `.htaccess`** canlı sunucuda `database/`, `setup/`, `docs/`, `.git`, `.env` ve `*.md` dosyalarını
  internete kapatır. Repoya kök dizinde yeni bir geliştirme klasörü ya da dosyası eklenirse `.htaccess` kuralları da
  güncellenir.

## Önemli dosyalar

| Yol | Ne |
|---|---|
| [DESIGN.md](DESIGN.md) | Renkler, fontlar, ölçüler (tasarım değerleri) |
| `theme/diverse/` | DIVERSE teması (Boost Union alt teması) |
| `setup/diverse_setup.php` | Site ayarlarını uygulayan betik (`--dry-run`, `--production`) |
| `setup/check_lang_settings.php` | Dil ayarlarının anlık görüntüsü (salt okunur) |
| [docs/CPANEL.md](docs/CPANEL.md), `setup/cpanel/` | Canlı cPanel sunucusu: kurulum, `config.php` şablonu, PHP ayarları |
| `.htaccess` | Canlı sunucuda geliştirme dosyalarını internete kapatır |
| [docker.md](docker.md), `docker-compose.yml`, `docker-entrypoint-custom.sh` | Yerel Docker kurulumu |
| `database/moodle.sql` | Veritabanı dökümü (repoda kalır) |
| [docs/YAPILANLAR.md](docs/YAPILANLAR.md) | Şimdiye kadar yapılanlar ve nedenleri, açık riskler |
| `local/diverse_assistant/`, [docs/AI.md](docs/AI.md) | AI ders asistanı eklentisi ve belgesi |
| [docs/design/README.md](docs/design/README.md) | Tasarım ilkeleri ve taslak ekranlar |
