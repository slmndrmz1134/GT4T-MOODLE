# DIVERSE Tasarım Değerleri (Design Tokens)

Platformun renkleri, fontları, köşe yuvarlaklıkları ve ölçüleri bu dosyada toplanır. Temayı değiştiren herkes
(geliştirici ya da yapay zekâ ajanı) önce bu dosyayı okur. Kurallar için [CLAUDE.md](CLAUDE.md) dosyasına bakın.

- **Tek kaynak:** [`theme/diverse/scss/pre.scss`](theme/diverse/scss/pre.scss). Renkler ve fontlar yalnızca
  burada tanımlanır. Diğer SCSS dosyaları bu değişkenleri kullanır, kendi renk kodunu (`#...`, `rgb(...)`) yazmaz.
- **Bu dosya ile `pre.scss` aynı olmalı.** Bir değer değişirse ikisi aynı commit'te güncellenir.
- **Değer değiştirmek proje sahibinin onayını ister.** Aşağıdaki "Bir değeri değiştirmek" bölümüne bakın.
- Tasarım ilkeleri ve taslak ekranlar: [`docs/design/README.md`](docs/design/README.md).

## Renkler

Renkler [diverse-university.eu](https://diverse-university.eu/) sitesinden alındı. Tüm metin/zemin çiftleri
**WCAG 2.1 AA** şartını (normal metin en az 4.5:1) karşılar. Tek istisna: proje sahibinin kararıyla (Kasım 2026)
butonlar ve linkler de marka turuncusu `#FF671F`; beyaz yazı ve beyaz zemindeki link 2.9:1 ile AA'nın altında.

| SCSS değişkeni | Renk | Ne için | Kontrast |
|---|---|---|---|
| `$diverse-brand-orange` | `#FF671F` | Büyük yüzeyler, landing blokları, grafikler. Üstünde **koyu** metin. Asla gövde metni ya da beyaz yazılı buton zemini değil. | Koyu metinle 5.43:1 |
| `$diverse-action-orange` | `#FF671F` | Butonlar (beyaz yazı), linkler, aktif sekme | Beyazla 2.9:1 (AA altında, proje sahibinin kararı) |
| `$diverse-action-orange-hover` | `#E5560F` | Buton/link hover ve basılı hali | Beyazla 3.7:1 |
| `$diverse-home-hover` | `#F57B2A` | Navbardaki Ana sayfa (Home) butonunun hover metin rengi | Beyazla 3.1:1 |
| `$diverse-ink` | `#1F2328` | Gövde metni, başlıklar, koyu footer zemini | Beyazla 15.8:1 |
| `$diverse-grey` | `#63666A` | İkincil metin, açıklamalar | Beyazla 5.77:1 |
| `$diverse-soft-orange` | `#FFF4ED` | Duyuru bantları, vurgulu alanlar, seçili satır | — |
| `$diverse-peach` | `#FFD8C2` | Dekoratif kareler, ders kapakları | — |
| `$diverse-surface` | `#F7F7F8` | Platform içi sayfa zemini, çekmeceler | — |
| `$diverse-border` | `#E4E4E7` | Kart ve ayırıcı kenarlıkları | — |
| `$diverse-border-strong` | `#C9C9CF` | Form alanı ve ikincil buton kenarlıkları | — |
| `$diverse-success` | `#1F7A4D` | Tamamlandı işaretleri | Beyazla 5.32:1 |
| `$diverse-success-soft` | `#EAF5EF` | Başarı zemini | — |
| `$diverse-danger` | `#B42318` | Yalnızca hata mesajları (marka turuncusundan belirgin şekilde koyu ve kırmızı) | Beyazla 6.57:1 |
| `$diverse-on-dark-muted` | `#D4D4D8` | Koyu footer üzerindeki ikincil metin | Koyu zeminde 9.9:1 |
| `$diverse-track` | `#EDEDEF` | İlerleme çubuğu zemini | — |

Bootstrap/Moodle eşleşmesi (`pre.scss` içinde): `$primary` = etkileşim turuncusu, `$link-color` = etkileşim
turuncusu, `$link-hover-color` = hover turuncusu, `$body-color` = ink, `$body-secondary-color` = gri,
`$success` / `$danger` = yukarıdaki başarı / hata renkleri. Moodle'ın etkinlik ikonu renkleri de palete bağlıdır:
değerlendirme, iletişim, iş birliği ve yönetim ikonları etkileşim turuncusu, içerik ikonları ink.

`pre.scss`, Boost ve Boost Union ayarlarından **sonra** yüklenir. Yöneticinin Boost Union ayarlarına girdiği marka
rengi bu değerleri ezmez. Renk değişikliği admin panelinden değil, `pre.scss` üzerinden yapılır.

### Yeşil palet (GT4T, kullanıcı seçer)

Varsayılan palet turuncudur. Her kullanıcı, kullanıcı menüsündeki "Yeşil renkler (GT4T)" bağlantısıyla kendi
hesabında GT4T iletişim rehberinin (2025/12) yeşil paletine geçebilir; seçim kullanıcı tercihi
`theme_diverse_palette` olarak saklanır. Ziyaretçiler her zaman turuncu paleti görür. Fontlar, köşeler ve ölçüler
iki palette aynıdır (proje sahibinin kararı, Kasım 2026).

Tema ikinci kez derlenir: `pre.scss` içindeki `@if $diverse-palette == green` bloğu aynı değişkenlere yeşil
değerler verir. Değişken adları değişmez; yeşil palette "orange" adlı değişkenler paletin ana rengini taşır.
Kurs kartı desenlerinin yeşil renkleri `classes/local/palette.php` içindedir.

| SCSS değişkeni | Yeşil palette | Kontrast |
|---|---|---|
| `$diverse-brand-orange` | `#19B35C` (GT4T yeşili) | Koyu yeşil metinle 5.13:1 |
| `$diverse-action-orange` | `#127A42` | Beyazla 5.4:1, açık yeşil zeminde 5.04:1 |
| `$diverse-action-orange-hover` | `#053225` (GT4T koyu yeşili) | Beyazla 14.1:1 |
| `$diverse-home-hover` | `#19B35C` | — |
| `$diverse-ink` | `#053225` | Beyazla 14.1:1 |
| `$diverse-grey` | `#55625C` | Beyazla 6.39:1, açık yeşilde 5.96:1 |
| `$diverse-soft-orange` | `#EFFBE4` | — |
| `$diverse-peach` | `#D4F7A8` (GT4T limesinin açık tonu) | Koyu yeşil metinle 11.86:1 |
| `$diverse-surface` | `#F7F9F8` | — |
| `$diverse-border` / `-strong` | `#E1E7E3` / `#C3CCC7` | — |
| `$diverse-on-dark-muted` | `#CFE0D8` | Koyu yeşilde 10.28:1 |
| `$diverse-track` | `#E6EEE9` | — |

Başarı, hata ve diğer renkler iki palette aynıdır.

### Koyu tema (kullanıcı seçer, iki palette de)

Proje sahibinin onayı, Kasım 2026 (taslak: `docs/design/mockups/*-wide.html`, sol menüdeki ay düğmesi). Sol menüde
yardımın üstündeki ay/güneş düğmesi, telefonda kullanıcı menüsü açık ve koyu arasında geçirir; seçim kullanıcı
tercihi `theme_diverse_mode` olarak saklanır. Seçim yoksa cihazın ayarı geçerlidir (koyu CSS
`prefers-color-scheme: dark` koşuluyla eklenir). Koyu tema yalnızca giriş yapılmış uygulama sayfalarındadır;
ziyaretçi ana sayfası ve giriş sayfası açık kalır.

Tema her palet için bir kez daha derlenir (`styles_palette.php?variant=dark|green-dark`): `pre.scss` içindeki
`@if $diverse-mode == dark` bloğu aynı değişkenlere koyu değerler verir. `$white` kartların ve alanların rengi olur,
"ink" yazının rengi; Bootstrap'in gri tonları ters çevrilir. Sol menüdeki logonun arkasında zemin yoktur; koyu temada logo beyaz çizilir
(landing sayfasındaki gibi, `filter: brightness(0) invert(1)`).

| SCSS değişkeni | Koyu turuncu | Koyu yeşil | Kontrast (kartta) |
|---|---|---|---|
| `$white` (kart) | `#1A1D21` | `#161D1A` | — |
| `$diverse-surface` (sayfa) | `#111316` | `#0E1311` | — |
| `$diverse-ink` (yazı) | `#ECEDEF` | `#E8EFEB` | 14.6:1 / 14.9:1 |
| `$diverse-grey` | `#A3A7AD` | `#9DABA4` | 7.4:1 / 7.3:1 |
| `$diverse-action-orange` | `#FF671F` | `#3DD27F` | 5.9:1 / 9.1:1; düğme yazısı kart renginde |
| `$diverse-action-orange-hover` | `#FF9B6B` | `#6EE2A1` | — |
| `$diverse-soft-orange` / `$diverse-peach` | `#2B1C14` / `#4A2B1B` | `#14261B` / `#1F3B24` | — |
| `$diverse-border` / `-strong` | `#2C3036` / `#3F444C` | `#26302B` / `#38443E` | — |
| `$diverse-success` / `$diverse-danger` | `#4CC38A` / `#F97066` | aynı | — |
| `$diverse-on-dark-muted` | `#4B5058` | `#46524C` | Açık "ink" zeminde 7.2:1 |

## Fontlar

| Kullanım | Font | Kalınlık | Dosya |
|---|---|---|---|
| Başlıklar | **Plus Jakarta Sans** | 700 | `theme/diverse/fonts/plus-jakarta-sans-*.woff2` |
| Arayüz ve gövde | **Plus Jakarta Sans** | 400–600 (buton 600) | Aynı dosyalar |

- Yedek fontlar: `system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`.
- Gövde metni en az **16 px** (`$font-size-base: 1rem`).
- Fontlar **kendi sunucumuzdan** gelir (`@font-face` tanımları `post.scss` içinde, lisanslar `fonts/` altında
  ve `thirdpartylibs.xml` içinde, SIL OFL 1.1). Google Fonts veya başka bir CDN kullanılmaz: Almanya'da
  GDPR ihlali sayılabiliyor (partnerlerden biri TH Rosenheim).
- Plus Jakarta Sans deneme amaçlıdır (Kasım 2026, proje sahibinin kararı). Eski fontlar repoda duruyor: geri dönmek için `pre.scss`
  içinde gövde `"Inter"`, başlık `"Source Sans 3"` yapılır. Source Sans 3, DIVERSE'in lisanslı Myriad Pro'suna en yakın ücretsiz fonttur.

## Köşe yuvarlaklıkları

| Öğe | Değer | SCSS değişkeni |
|---|---|---|
| Butonlar | `.875rem` (14 px); küçük butonlar (`btn-sm`) tam yuvarlak | `$diverse-control-radius`, `$btn-border-radius`, `$btn-border-radius-sm` |
| Kartlar | `1.25rem` (20 px) | `$card-border-radius`, `$border-radius-xl` |
| Form alanları | `.875rem` (14 px), butonlarla aynı | `$diverse-control-radius`, `$input-border-radius` |
| Genel (menü, uyarı, küçük kutu) | `.875rem` / büyük `1rem` | `$border-radius`, `$border-radius-lg` |
| Sekmeler | Köşesiz, `0` | `platform.scss` |
| İlerleme çubuğu | Oval, `50rem` | `post.scss` |

## Ölçüler ve yerleşim

| Öğe | Değer | Nerede |
|---|---|---|
| Platform sayfaları (dashboard, Derslerim, Keşfet, ders sayfası...) | Tam genişlik: sol menünün yanından ekranın sonuna kadar, yanlarda `1.5rem` (proje sahibinin kararı, Kasım 2026) | `platform.scss`; Boost Union ayarı `coursecontentmaxwidth` (1280px) değişmedi, SCSS onu ezer |
| Okuma genişliği | `48rem`: ders özeti, bölüm metinleri, etkinlik açıklamaları, sayfa ve kitap içeriği | `platform.scss`: `$diverse-reading-width` |
| Landing yan boşluğu | `4rem` masaüstü, `1.25rem` mobil | `landing.scss`: `$diverse-gutter`, `$diverse-gutter-sm` |
| Giriş sayfası marka paneli | Ekranın `42vw` kadarı | `login.scss`: `$diverse-login-panel-width` |
| Sol ikon menüsü | `4.5rem` (72 px), `md` ve üstü; adlar üzerine gelince ikonun yanında çıkar, üst çubuk menünün yanından başlar | `shell.scss`: `$diverse-rail-width` |
| Mobil alt menü | `4rem` (64 px), `md` altı | `shell.scss`: `$diverse-tabbar-height` |
| Tıklanabilir alan | En az 44 × 44 px | Butonlar, ikonlu linkler, menü öğeleri |
| İlerleme çubuğu | `.375rem` yükseklik, oval | `post.scss` |

Sayfaya özel ölçüler (yan boşluk, panel genişliği) kendi SCSS dosyasının başında tanımlıdır, çünkü yalnızca o sayfayı
ilgilendirir. Renkler ise her zaman `pre.scss` içindedir.

## Bileşenler

- **Butonlar:** yuvarlak köşeli (14 px, proje sahibinin kararı, Kasım 2026), etkileşim turuncusu zemin ve beyaz yazı. Hover'da `$diverse-action-orange-hover`.
- **Sekmeler:** kenarlıksız ve köşesiz düz yazı; altta `$diverse-border` çizgisi. Pasif sekme gri yazı, aktif sekme ink
  yazı ve altında 3 px etkileşim turuncusu çizgi (`platform.scss`). Ders ve yönetim sayfalarının hepsinde, Panom'daki
  kategori satırında da aynıdır.
- **Kartlar:** beyaz zemin, 1 px `$diverse-border` kenarlık, 20 px köşe (`$card-border-radius`). Sayfa zemini `$diverse-surface` olur,
  böylece kartlar ayrışır. Boş kart gösterilmez.
- **Ders sayfaları:** ders başlığı, sekmeler ve ders bölümleri kart değildir; `$diverse-border` ayraçlarıyla ayrılır. Başlık
  ve müfredat paneli dersin bütün sekmelerinde aynı yerdedir (`platform.scss`, `shell.scss`).
- **Hover ve odak:** hover'da renk `.15s` geçişle değişir, ikon düğmelerinde kutu çıkmaz. Fareyle tıklama iz bırakmaz; klavyede
  2 px etkileşim turuncusu çizgi ikonun kenarına oturur; butonlarda ve form alanlarında çizgi, hover'daki kenarlık gibi
  1 px'tir (`post.scss`, `$focus-ring-width`).
- **Üst menü:** beyaz zemin, sekmelerle aynı alt çizgi; ikon yok. Yönetim bağlantıları "Administration" açılır menüsündedir.
  Telefonda alt menü ink zemin, ikon ve altında etiket; aktif öğe marka turuncusu zemin ve **koyu** yazı (5.43:1).
- **Sol menü:** tepede kullanıcının partnerinin kare logosu (partner görünümündeki "Compact logo"; yoksa DIVERSE işareti).
  Logoya tıklayınca menü 15rem genişler, tam logo ve yerlerin adları görünür; tercih kullanıcıda saklanır (`shell.scss`).
- **Açılır paneller:** bildirimler ve mesajlar üst çubukta düğmenin altında açılan beyaz kart olur (24rem). Mesajlar
  Moodle'ın kendi mesaj çekmecesidir, bütün görünümleri aynen kalır; telefonda tam ekran açılır (`shell.scss`).
- **Hareket:** menüler ve paneller kısa bir yay efektiyle açılır, sayfa blokları sırayla yukarı kayarak belirir,
  grafikler kendini çizer, ders kartları hover'da 3 px kalkar (`motion.scss`). Yalnızca CSS; sistemde "hareketi azalt"
  seçili kullanıcıda hiçbiri çalışmaz.
- **Rakam kutuları:** beyaz kart, büyük rakam, gri etiket. Dikkat isteyen rakam yumuşak turuncu zemin alır.
- **İkonlar:** Lucide (`pix/icons`, `icons.scss`), mesaj balonu DIVERSE'e özel oval çizim. Moodle'ın işaretlemesi değişmez, yalnızca
  çizim değişir; listede olmayan ikon Moodle'ınki kalır. Zil okunmamış bildirim varken dolu görünür.
- **Ders kartları:** kayıtlı olunan derste sağ üstte favori yıldızı, kapakta etkinlik sayacı, üstte tür etiketi, altta ilerleme ve eylem düğmesi (Başla, Devam et, Görüntüle). Başlık 700, `1.125rem`, ink renk. Hover'da etkileşim turuncusu.
- **Etiketler (eyebrow, rozet, ders kodu):** butonlarla aynı köşe (`$btn-border-radius`), oval değil.
- **Landing hero'su:** `pix/hero.jpg` (amfide öğrenciler, Vitaly Gariev, Unsplash lisansı; proje sahibinin seçimi), tüm ekran,
  üzerinde soldan sağa açılan ink katmanı (`$diverse-photo-shade-strong` → `$diverse-photo-shade` → `$diverse-photo-shade-soft`)
  ve beyaz yazı (`$diverse-on-photo`), sağda panonun bir resmi. Sayfa tam genişlik (Bilal'in taslağı, Kasım 2026:
  `docs/design/mockups/Landing-wide.html`), yan boşluk `clamp(1rem, 5vw, 6rem)`; üst menü de aynı boşlukla hizalanır.
  Üst menü hero'da şeffaf, logo ve linkler beyaz (logonun arkasında beyaz kutu yok); sayfa kayınca beyaz olur.
  Fotoğraf ve turuncu bant üzerindeki çizgi/yarı saydam beyazlar `$diverse-on-photo-*`, fotoğraf üstünde yüzen kartların
  gölgesi `$diverse-float-shadow` (hepsi `pre.scss`). Platformda ve landing'de dekoratif kare desen kullanılmaz
  (proje sahibinin kararı, Kasım 2026); kare desen yalnızca giriş sayfasında kalır.
- **Ders kapakları:** dersin görseli, yoksa marka renkli zemin üzerinde dersin baş harfleri.
- **Sayfanın tek koyu yüzeyi** (sıradaki canlı ders kartı, telefondaki alt sekme çubuğu, video oynatıcı, ink ders kapağı):
  `$diverse-contrast` üzerinde `$diverse-on-contrast` yazı. Açık temada ink ve beyaz; koyu temada ink yazı rengi olduğundan
  sayfanın en koyu rengi (`$diverse-surface`) ve açık yazı, beyaz leke olmaz (Kasım 2026).
- **Takvim:** her gün yuvarlak köşeli bir kutu (`$diverse-surface`), bugün yumuşak turuncu zeminde, numarası turuncu dairede;
  etkinlikler türünün renginde kısa çizgili satırlar (ders: turuncu, site: ink, grup: yeşil, kişisel: gri). Ay görünümünün
  yanında, geniş ekranda, yaklaşan etkinlikler gün gün kartlar halinde, üstte aynı renkte çizgi (`calendar_agenda`).
- **Başlıklar:** kendi başlık satırı olmayan sayfalarda (takvim, tercihler, profil, notlar...) Moodle'ın sayfa başlığı geniş
  ekranda arama ile aynı satırda, pano ve Derslerim başlığıyla aynı boyutta (`1.5rem`) ve yükseklikte durur.
- **Landing kareleri:** yalnızca `landing.scss` içindeki `$diverse-tiles` eşleşmeleri kullanılır
  (ink/turuncu, soft/ink, turuncu/ink, şeftali/ink...). Yeni zemin/yazı eşleşmesi kontrast ölçülmeden eklenmez.

## Kullanılmayacaklar

- Platformun içinde cam efekti (glassmorphism), yıldızlı arka plan, 3D kartlar.
  Landing page gösterişli olabilir, platformun içi sade kalır.
- Marka turuncusu (`#FF671F`) üzerinde beyaz yazı, marka turuncusuyla gövde metni.
- `pre.scss` dışında renk kodu, yeni font ailesi, CDN'den font.
- Tema dosyası kopyalayıp düzenlemek (Boost Union şablonlarını `theme/diverse` içine kopyalamak). Görünüm SCSS ve
  renderer üzerinden değişir, böylece Boost Union güncellemeleri temayı bozmaz.

## Bir değeri değiştirmek

1. Proje sahibine hangi değerin neden değişeceğini söyleyin, onay alın.
2. Değeri yalnızca `theme/diverse/scss/pre.scss` içinde değiştirin ve bu dosyadaki tabloyu aynı commit'te güncelleyin.
3. Metin rengi ya da zemin değiştiyse kontrastı ölçün (en az 4.5:1, büyük başlıkta en az 3:1).
4. Yerelde önbelleği temizleyip landing, giriş, dashboard, ders sayfası ve Site administration sayfalarına
   masaüstü ve mobil genişlikte bakın ([CLAUDE.md](CLAUDE.md), Kural 4).
