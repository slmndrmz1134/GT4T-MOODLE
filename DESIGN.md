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
**WCAG 2.1 AA** şartını (normal metin en az 4.5:1) karşılar.

| SCSS değişkeni | Renk | Ne için | Kontrast |
|---|---|---|---|
| `$diverse-brand-orange` | `#FF671F` | Büyük yüzeyler, landing blokları, grafikler. Üstünde **koyu** metin. Asla gövde metni ya da beyaz yazılı buton zemini değil. | Koyu metinle 5.43:1 |
| `$diverse-action-orange` | `#C2410C` | Butonlar (beyaz yazı), linkler, aktif sekme | Beyazla 5.18:1 |
| `$diverse-action-orange-hover` | `#9A3412` | Buton/link hover ve basılı hali | Beyazla 7.31:1 |
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
`$success` / `$danger` = yukarıdaki başarı / hata renkleri.

`pre.scss`, Boost ve Boost Union ayarlarından **sonra** yüklenir. Yöneticinin Boost Union ayarlarına girdiği marka
rengi bu değerleri ezmez. Renk değişikliği admin panelinden değil, `pre.scss` üzerinden yapılır.

## Fontlar

| Kullanım | Font | Kalınlık | Dosya |
|---|---|---|---|
| Başlıklar | **Source Sans 3** | 700 | `theme/diverse/fonts/source-sans-3-variable.woff2` |
| Arayüz ve gövde | **Inter** | 400–600 (buton 600) | `theme/diverse/fonts/inter-variable.woff2`, `inter-variable-italic.woff2` |

- Yedek fontlar: `system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`.
- Gövde metni en az **16 px** (`$font-size-base: 1rem`).
- Fontlar **kendi sunucumuzdan** gelir (`@font-face` tanımları `post.scss` içinde, lisanslar `fonts/` altında
  ve `thirdpartylibs.xml` içinde, SIL OFL 1.1). Google Fonts veya başka bir CDN kullanılmaz: Almanya'da
  GDPR ihlali sayılabiliyor (partnerlerden biri TH Rosenheim).
- Source Sans 3, DIVERSE'in kullandığı lisanslı Myriad Pro'ya en yakın ücretsiz seçenektir.

## Köşe yuvarlaklıkları

| Öğe | Değer | SCSS değişkeni |
|---|---|---|
| Butonlar (her boy) | Oval (pill), `50rem` | `$btn-border-radius`, `-sm`, `-lg` |
| Kartlar | `1rem` (16 px) | `$card-border-radius`, `$border-radius-xl` |
| Form alanları | `.625rem` (10 px), küçükler `.5rem` | `$input-border-radius`, `-lg`, `-sm` |
| Genel | `.5rem` / büyük `.75rem` | `$border-radius`, `$border-radius-lg` |
| Sekmeler ve ilerleme çubuğu | Oval, `50rem` | `admin.scss`, `post.scss` |

## Ölçüler ve yerleşim

| Öğe | Değer | Nerede |
|---|---|---|
| Ders sayfası içerik genişliği | `1000px` (Boost Union varsayılanı 830px) | Boost Union ayarı `coursecontentmaxwidth`, `setup/diverse_setup.php` yazar |
| Dashboard ve Derslerim genişliği | `80rem` (1280 px) | `platform.scss` |
| Landing yan boşluğu | `4rem` masaüstü, `1.25rem` mobil | `landing.scss`: `$diverse-gutter`, `$diverse-gutter-sm` |
| Giriş sayfası marka paneli | Ekranın `42vw` kadarı | `login.scss`: `$diverse-login-panel-width` |
| Tıklanabilir alan | En az 44 × 44 px | Butonlar, ikonlu linkler, menü öğeleri |
| İlerleme çubuğu | `.375rem` yükseklik, oval | `post.scss` |

Sayfaya özel ölçüler (yan boşluk, panel genişliği) kendi SCSS dosyasının başında tanımlıdır, çünkü yalnızca o sayfayı
ilgilendirir. Renkler ise her zaman `pre.scss` içindedir.

## Bileşenler

- **Butonlar:** oval, etkileşim turuncusu zemin ve beyaz yazı. Hover'da `$diverse-action-orange-hover`.
- **Sekmeler ("kutu sekmeler"):** her sekme oval bir kutudur. Pasif sekme beyaz zemin, `$diverse-border-strong`
  kenarlık ve ink yazı. Aktif sekme etkileşim turuncusu zemin ve beyaz yazı (örnek: Site administration, `admin.scss`).
- **Kartlar:** beyaz zemin, 1 px `$diverse-border` kenarlık, 16 px köşe. Sayfa zemini `$diverse-surface` olur,
  böylece kartlar ayrışır. Boş kart gösterilmez.
- **Ders kartları:** başlık Source Sans 3 700, `1.125rem`, ink renk. Hover'da etkileşim turuncusu.
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
