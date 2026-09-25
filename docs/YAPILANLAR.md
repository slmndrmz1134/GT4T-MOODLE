# DIVERSE Moodle: Yapılanlar ve Nedenleri

24–25 Eylül 2026 tarihli çalışmaların özeti. Her başlıkta **ne yapıldı** ve **neden** ayrı ayrı yazılı.
Bu belge, projeye sonradan katılan birinin kararların gerekçesini anlaması için hazırlandı.

- **Proje:** Algebra Bernays University (Zagreb), İstanbul Beykent Üniversitesi ve TH Rosenheim için Moodle 5.0.7
  tabanlı öğrenme platformu. Üç üniversite de DIVERSE European University Alliance üyesi.
- **Repo:** https://github.com/slmndrmz1134/GT4T-MOODLE
- **Hedef:** Yerel geliştirme Docker'da, canlı sistem cPanel sunucusunda (AlmaLinux).

---

## Kısa özet

| # | Konu | Durum |
|---|---|---|
| 1 | Temiz repo, SQL dahil | ✓ |
| 2 | Kopyalamada kaybolan 18.564 dosyanın geri getirilmesi | ✓ |
| 3 | Yeni tema: `theme_diverse` (Boost Union alt teması), DIVERSE renkleri, fontlar | ✓ |
| 4 | GT4T → DIVERSE isim değişikliği | ✓ |
| 5 | Landing page ve giriş sayfası | ✓ |
| 6 | Dashboard, Kurslarım ve ders sayfası düzenlemeleri | ✓ |
| 7 | Site administration kartlar ve sekmeler | ✓ |
| 8 | Çok dil: `filter_multilang2` + TR/DE/HR dil paketleri | ✓ |
| 9 | Kurulum betiği `setup/diverse_setup.php` | ✓ |
| 10 | Debug ayarları ve admin sayfalarındaki uyarılar | ✓ |
| 11 | BigBlueButton canlı ders | Modül açık, **sunucu kararı bekliyor** |

---

## 1. Temiz repo, SQL dahil

**Yapılan**
- `moodle1.zip` içindeki `moodle.sql` projeye eklendi (`database/moodle.sql`) ve sıfırdan yeni bir git geçmişi başlatıldı.
- Eski `.git` klasörü ve proje içindeki boş ikinci `GT4T-MOODLE/.git` klasörü silinmedi; yedeğe taşındı
  (`D:\GT4T-MOODLE\_eski_git_yedek\`).
- `.gitignore` dosyasına `.env`, `.env.save*` ve `nginx/.htpasswd` eklendi.

**Neden**
- GitHub'a yalnızca `.gitignore` gidiyordu. Sebep, proje içindeki ikinci `.git` klasörüydü: git o klasörü ayrı bir repo
  (alt modül) sanıyordu.
- İstenen şey eski ayarlardan bağımsız, sıfırdan bir repoydu.
- SQL'in repoda kalması ve her kurulumla canlıya gitmesi **proje sahibinin kararı**. Dosyada kullanıcı e-postaları
  ve şifre hash'leri olduğu için riskler ayrıca bildirildi (bkz. §12).

## 2. Kaybolan dosyaların geri getirilmesi

**Yapılan**
- Zip'teki kod kopyalama sırasında `lib/aws-sdk` klasöründe kesilmişti: `mod`, `theme`, `question`, `login`, `user`,
  `pix` klasörleri ve `lib`'in yarısı eksikti (30.560 dosyadan 18.568'i).
- Eksik dosyalar eski git yedeğinden (orijinal ekip reposu, commit `fca66c19`) aynen geri getirildi.
- Resmi kaynaklarla karşılaştırıldı: Moodle 5.0.7 çekirdeği 29.095/29.095 dosya tam; mutenancy ve mulib tam.
- Eski repoda hiç bulunmayan `theme/boost_union/config.php`, Boost Union'ın resmi `v5.0-r26` sürümünden eklendi.
- `.gitignore`'daki `config.php` kuralı `/config.php` olarak düzeltildi.

**Neden**
- Kod yarım olduğu için site çalışamazdı: giriş sayfası, temalar ve CSS'i sunan dosyalar yoktu.
- Eski `.gitignore` kuralı yalnızca kökteki `config.php`'yi değil, bütün eklenti ve tema `config.php` dosyalarını
  (`theme/boost/config.php` vb.) dışarıda bırakıyordu. Boost Union'ın `config.php` dosyasının eski repoda da hiç
  olmamasının sebebi bu.

## 3. Yeni tema: `theme_diverse`

**Yapılan**
- Boost Union'ın resmi alt tema şablonundan (Boost Union Child) yeni bir tema oluşturuldu.
- DIVERSE sitesinin renkleri alındı ve erişilebilirlik (WCAG 2.1 AA) için ayarlandı:
  - marka turuncusu `#FF671F` yalnızca büyük yüzeylerde, üzerinde koyu metinle;
  - butonlar ve linkler için etkileşim turuncusu `#C2410C`;
  - metin `#1F2328`, gri `#63666A`.
- Fontlar (Inter ve Source Sans 3, OFL lisanslı) temaya gömüldü ve Moodle'ın kendisi tarafından sunuluyor.
- Tasarım rehberi ve onaylanan taslaklar `docs/design/` altında.

**Neden**
- Eski Moove teması ekip tarafından değiştirilmiş (fork) ve çekirdek dosyalara müdahale edilmişti. Her güncellemede
  bu değişiklikler kaybolurdu.
- Boost Union düzenli güncellenen, üniversitelerin kullandığı bir tema. Partner bazlı görünüm (flavours), duyuru
  bantları, yasal sayfalar gibi özellikleri kod yazmadan sağlıyor. Alt tema sayesinde bizim değişikliklerimiz
  Boost Union güncellemelerinden etkilenmiyor.
- DIVERSE'in ana turuncusu beyaz üzerinde 2,91:1 kontrast veriyor. AB'deki erişilebilirlik şartı normal metin için
  en az 4,5:1; bu yüzden butonlar ve metinler için koyu bir ton seçildi.
- Fontları Google'dan çekmek yerine kendi sunucumuzdan sunuyoruz; Almanya'da Google Fonts CDN kullanımı GDPR ihlali
  sayılabiliyor.
- Veritabanındaki eski marka renkleri (`#FF5723`, `#FFF005`) temanın SCSS'i tarafından eziliyor.

## 4. GT4T → DIVERSE

**Yapılan**
- Tema adı `theme_diverse` oldu (henüz kurulmamış olduğu için güvenle değiştirildi).
- Site adı "DIVERSE European University" / "DIVERSE" oldu (SQL, kurulum betikleri).
- Görünen metinler değişti: yönetici giriş sayfası, kayıt eklentisinin görünen adı, taslaklar, dokümanlar.

**Neden ve bilinçli istisnalar**
- Partner üniversiteler DIVERSE üyesi; proje sahibi ismin her yerde DIVERSE olmasını istedi.
- Değiştirilmeyenler:
  - `auth_gt4t_validator` eklentisinin kod adı: 4 kullanıcının giriş yöntemi olarak veritabanında kayıtlı;
    değişirse bu kişiler giriş yapamaz.
  - Redis önbellek öneki ve geçmiş log kayıtları: sadece iç kullanım için, kullanıcı görmüyor.
- GT4T projesine özgü bilgiler ("EIT tarafından fonlanır, SAMK koordinatörlüğünde") DIVERSE için kullanılmadı;
  yerine DIVERSE'in resmi sloganı kondu. Aksi halde DIVERSE hakkında yanlış bir iddia olurdu.

## 5. Landing page ve giriş sayfası

**Yapılan**
- Ziyaretçilerin gördüğü ana sayfa: hero, canlı sayılar (partner, ders, kullanıcı), "How we collaborate",
  "Long-term impact", partner kartları, öne çıkan dersler ve footer.
- Giriş sayfasında solda turuncu marka paneli, sağda form; mobilde sadece form.
- Bütün metinler tema dil dosyasında (`lang/en/theme_diverse.php`).

**Neden bu yöntem**
- Boost Union'ın hiçbir dosyası kopyalanmadı veya değiştirilmedi; yalnızca renderer sınıfı genişletildi
  (`main_content()` ve `render_login()` metotları). Böylece Boost Union güncellendiğinde bu sayfalar bozulmaz;
  navbar, dil menüsü ve partner seçimi aynen çalışır.
- Partner kategorileri Mutenancy tarafından ziyaretçiden gizleniyor. Bu yüzden kartlarda yalnızca isim ve ders sayısı
  var; her kart partnerin kendi giriş sayfasına gidiyor. Bu isimler zaten giriş sayfasındaki "Select Partner"
  listesinde herkese açık.
- Kapak resmi dosyası okunamayan derslerde desenli kapak kullanılıyor; eksik dosyalar PHP uyarısına yol açıyordu.

## 6. Dashboard, Kurslarım ve ders sayfası

**Yapılan**
- Dashboard ve Kurslarım 1280px genişliğe çıktı; ders kartları DIVERSE renklerinde.
- Kapak resmi olmayan derslere Moodle'ın ürettiği desenler palete bağlandı; eskiden rastgele mavi ve griydi.
- Varsayılan dashboard'a "Son erişilen dersler" bloğu eklendi (Zaman çizelgesi ile Takvim arasına).
- Ders sayfası genişliği 830px'ten 1000px'e çıktı; etkinlik adları koyu ve kalın.

**Neden**
- Kart ızgarası olan sayfalarda 830px çok dardı; ders sayfasında ise okunabilirlik için orta bir genişlik seçildi.
- Ders sayfası genişliği temaya sabit yazılmadı, Boost Union'ın kendi ayarıyla yapıldı; yönetim panelinden
  değiştirilebilir kalsın diye.

## 7. Site administration

**Yapılan**
- Üst sekmeler kutu (pill) şeklinde, aktif olan turuncu.
- Her kategori beyaz bir kart; kartlar ekran genişliğine göre sütunlara diziliyor.
- Link içermeyen boş kategori kartları gizleniyor.

**Neden**
- Standart görünüm uzun ve dağınık bir listeydi. Değişiklik yalnızca bu sayfanın CSS'i; Moodle'ın, eklentilerin
  ve ayarların hiçbirine dokunulmadı.

## 8. Çok dil

**Yapılan**
- `filter_multilang2` 2.0.5.5 kuruldu (kodu kurulumdan önce incelendi). Başlıklarda da çalışıyor. Kullanım:
  `{mlang en}Welcome{mlang}{mlang tr}Hoş geldiniz{mlang}`.
- Türkçe, Almanca ve Hırvatça dil paketleri kuruldu (yerel test sitesine).

**Neden ve kural**
- Proje sahibinin tercihi: siteyi elle çevirmek yerine ileride kullanılacak bir çok dil altyapısı.
- **Kural:** Dil ayarları ve çok dil eklentisinin ayarları hiçbir değişiklikte bozulmayacak. Her işlemden önce ve
  sonra dil ayarlarının anlık görüntüsü karşılaştırıldı: site dili, dil listesi, dil menüsü, dil paketleri,
  kullanıcı dilleri ve eklenti ayarları. Hepsinde birebir aynı kaldı.
- Dil paketleri `moodledata`'da duruyor; canlıda kurulum betiği eksikleri kuruyor.

## 9. Kurulum betiği: `setup/diverse_setup.php`

**Yapılan**
- Veritabanında duran site ayarlarını canlı sunucuda tek komutla uygulayan betik:
  tema, ders sayfası genişliği, debug görüntüleme, çok dil filtresi, dil paketleri, dashboard bloğu,
  BigBlueButton ve (`--production` ile) canlı debug seviyesi.

**Tasarım ilkeleri**
- **Sadece eksik olanı ekler; tekrar çalıştırmak güvenli.** Yöneticinin bilerek seçtiği değerlere dokunmaz
  (örneğin kapatılmış bir filtre ya da farklı bir ders sayfası genişliği).
- **Dil ayarlarına dokunmaz.**
- `--dry-run` hiçbir şeyi değiştirmeden planı gösterir.
- `--reset-dashboards` isteğe bağlı, çünkü kullanıcıların kişisel dashboard düzenini siler.
- BBB sunucu adresi ve anahtarı gibi gizli bilgileri **asla** yazmaz; bunlar repoya girmemeli.
- Web'den çalıştırılamaz (Moodle'ın standart `CLI_SCRIPT` koruması).

**Neden**
- Yerel test sitesinde yapılan ayarlar yalnızca test veritabanında. SQL'i test verisiyle yeniden dışa aktarmak
  yerine, tekrarlanabilir ve denetlenebilir bir betik tercih edildi.

**Canlıda kullanım** (kod yüklenip `admin/cli/upgrade.php` çalıştırıldıktan sonra):

```bash
php setup/diverse_setup.php --dry-run
php setup/diverse_setup.php --production --reset-dashboards
```

`--reset-dashboards` yalnızca ilk kurulumda kullanılmalı.

## 10. Debug ve admin uyarıları

**Yapılan**
- Debug mesajlarının sayfada gösterilmesi kapatıldı (`debugdisplay = 0`); hatalar sunucu loguna yazılmaya devam ediyor.
- `--production` seçeneği debug seviyesini Minimal yapıyor ve geliştiriciye özel ayarları kontrol ediyor.

**Neden**
- Admin sayfalarındaki `getimagesize(...)` uyarıları, `moodledata`'da olmayan 4 Algebra dersi kapak resminden
  geliyordu. Uyarıyı Boost Union'ın kodu üretiyor; sayfada görünmesinin sebebi ise veritabanında debug
  görüntülemenin açık olmasıydı. Canlıda zaten kapalı olmalı, çünkü sunucu yollarını açığa çıkarıyor.
- Yerelde geliştirici modu korunuyor; `--production` verilmezse debug seviyesine dokunulmuyor.

## 11. BigBlueButton (canlı ders)

**Yapılan**
- BBB, Moodle 5.0'ın çekirdek modülü (`mod_bigbluebuttonbn`) olarak zaten kuruluydu; açıldı. Ek eklenti gerekmedi.
- Dersteki öğretmenler varsayılan olarak moderatör yapıldı; Moodle'ın varsayılanında sadece etkinliği oluşturan
  kişi moderatör.
- Blindside test sunucusunun hâlâ çalıştığı ve test anahtarını kabul ettiği doğrulandı.
- Business Law dersine örnek bir canlı ders eklendi (28 Eylül 2026, 10:00 İstanbul). **Sadece yerel test
  veritabanında.** Takvimde ve ders sayfasında doğru göründüğü kontrol edildi.

**Neden**
- Çekirdek modül Moodle ekibi ve BBB'yi geliştiren Blindside tarafından birlikte bakılıyor; takvim, Zaman çizelgesi,
  yetkiler ve kayıtlar hazır.
- Ortak derslerde birden fazla öğretmen olduğu için hepsinin dersi yönetebilmesi gerekiyor.

**Neden ayrı sunucu gerekiyor**
- BBB gerçek zamanlı ses ve görüntü sunucusu: çok çekirdek, açık UDP portları, root yetkisi ve Ubuntu 22.04 istiyor.
- cPanel'li AlmaLinux sunucuya kurulamaz. BBB AlmaLinux'u desteklemiyor; ayrıca cPanel'in web sunucusu, portları
  ve paketleriyle çakışır. cPanel olduğu gibi kalacak.
- 3–5 kişilik bir ders için 4 çekirdek / 8 GB RAM'li ayrı bir Ubuntu VPS yeterli.

**Jitsi karşılaştırması (özet)**
- Jitsi daha hafif, ama Moodle'da üçüncü parti eklentiyle (`mod_jitsi`) çalışıyor. Kayıt için ek bir sunucu (Jibri)
  gerekiyor ve ders araçları daha sınırlı.
- Üniversite kullanımı için BBB daha uygun: kayıt, sunum, beyaz tahta, katılım takibi hazır. Ayrı sunucu şartı
  ikisinde de aynı.

## 12. Hâlâ açık olan riskler

Kod incelemesinde bulunan ve henüz düzeltilmeyen konular:

- **SQL repoda ve repo herkese açık.** Dosyada kullanıcı e-postaları, şifre hash'leri ve LTI özel anahtarları var.
  Proje sahibinin kararıyla SQL repoda kalıyor. Şifrelerin (hepsi `123456`) ve LTI anahtarlarının değiştirilmesi
  öneriliyor.
- **Kayıt doğrulaması fiilen kapalı.** `auth_gt4t_validator`, API adresi `example.com` olduğu için herkesi doğrulanmış
  sayıyor ve e-posta onayı olmadan hesap açıp giriş yaptırıyor.
- **`admin/login.php`** giriş formundaki güvenlik anahtarını (login token) kontrol etmiyor.
- **`mod/forum/templates/big_search_form.mustache`** içinde çözülmemiş merge conflict işaretleri kalmış.
- **Algebra ve Beykent partnerlerinde kullanıcı sınırı 10** (`memberlimit`); sonraki kayıtlar takılır.
- **`moodledata` kayıp:** 50 yüklenmiş dosya (logolar, kapaklar, ikonlar) yok; yeniden yüklenmeleri gerekiyor.
- Partner adında yazım hatası: "Algebra University **Collage**".
- Çekirdek dosyalarda eski ekibin yaptığı değişiklikler (`index.php`, `admin/index.php`, `login/*`) duruyor.
- İncelenen **Edwiser Reports** eklentisi kurulmadı: herhangi bir giriş yapmış kullanıcının kendine yönetici yetkisi
  verebildiği bir güvenlik açığı var.

## 13. Yerel test ortamı (Docker)

- Test ortamı eski kurulumlardan ayrı çalışıyor (`docker compose -p gt4t-test`, http://localhost:8080).
  Repo klasörü konteynere bağlı; koddaki değişiklik anında görünür.
- `C:\Users\SELMAN\Desktop\moodle\moodle` klasöründen gelen ve sürekli çöken eski yığın durduruldu; konteynerleri ve
  imajı silindi. **Eski volume'lar silinmedi** (içlerinde eski veritabanları var).
- Test veritabanı `database/moodle.sql`'den yüklendi; üzerine kurulum betiğinin ayarları ve örnek BBB dersi eklendi.
- Giriş gerektiren sayfalar, proje sahibinin Chrome'daki oturumu üzerinden (Claude in Chrome) kontrol edildi;
  şifre girilmedi.

## 14. Bekleyen kararlar ve sonraki adımlar

1. **BBB sunucusu:** Test sunucusuyla bir kerelik deneme (açık onay gerekiyor) → ardından ayrı bir Ubuntu VPS
   veya barındırma hizmeti.
2. Resmi DIVERSE logo dosyası (şimdilik yazı logosu).
3. Landing ve giriş metinlerinin çok dil eklentisiyle yönetilebilmesi için tema ayarlarına taşınması.
4. Boost Union ayarları: partner logoları (flavours), footer, yasal sayfalar (Künye, erişilebilirlik beyanı).
5. Site varsayılan saat dilimi (şu an Europe/London).
6. §12'deki güvenlik konularının düzeltilmesi.
7. Yerel Docker'ın hızlandırılması (proje WSL2 diskine taşınabilir).

## 15. Commit listesi

| Commit | Tarih | Açıklama |
|---|---|---|
| `c0fc3ead` | 2026-09-24 | İlk commit: Moodle kodu + veritabanı dökümü |
| `1001cd98` | 2026-09-25 | Kopyalamada kaybolan dosyaların geri getirilmesi, `.gitignore` düzeltmesi |
| `a17f7fab` | 2026-09-25 | Boost Union alt teması ve tasarım rehberi; Boost Union `config.php` |
| `a264dcae` | 2026-09-25 | GT4T → DIVERSE, fontlar |
| `2ee09357` | 2026-09-25 | Form alanlarına bitişik butonlar |
| `9f426bd6` | 2026-09-25 | Landing page ve giriş marka paneli |
| `b2462565` | 2026-09-25 | Çok dil eklentisi `filter_multilang2` |
| `4a6339b8` | 2026-09-25 | Dashboard, Kurslarım, ders sayfası stilleri |
| `22bbdb6f` | 2026-09-25 | Kurulum betiği `setup/diverse_setup.php` |
| `0baaa264` | 2026-09-25 | Ders sayfası genişliği 1000px |
| `e4038f41` | 2026-09-25 | Site administration kartlar ve sekmeler; debug görüntüleme kapalı |
| `186eaf7a` | 2026-09-25 | `--production` seçeneği |
| `538b8cbf` | 2026-09-25 | BigBlueButton'ın açılması |
