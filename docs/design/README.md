# DIVERSE Tasarım Rehberi

DIVERSE platformunun görsel dili. Renkler [diverse-university.eu](https://diverse-university.eu/) sitesinden alındı ve
tüm metin/zemin çiftleri **WCAG 2.1 AA** kontrast şartını (normal metin ≥ 4.5:1) karşılayacak şekilde ayarlandı.

- **Tema kodu:** `theme/diverse` (Boost Union alt teması). Palet `theme/diverse/scss/pre.scss` içinde tanımlı.
- **Taslaklar:** `docs/design/mockups/` (kaynak dosyalar) ve çevrimiçi tuval:
  https://claude.ai/artifact/6RSY4o46VHzDGv2ZyLaot8 (sahibi paylaşana kadar yalnızca ona açık).

## Renk paleti

| Rol | Renk | Nerede | Kontrast |
|---|---|---|---|
| Marka turuncusu | `#FF671F` | Büyük yüzeyler, landing page blokları, grafikler. Üstüne **koyu** metin. Asla gövde metni ya da buton yazısı değil. | Koyu metinle 5.43:1 |
| Etkileşim turuncusu | `#C2410C` | Butonlar (beyaz yazı), linkler, aktif sekme. Hover: `#9A3412` | Beyazla 5.18:1 |
| Koyu metin (ink) | `#1F2328` | Gövde metni ve başlıklar | 15.8:1 |
| Gri | `#63666A` | İkincil metin, açıklamalar | 5.77:1 |
| Açık turuncu | `#FFF4ED` | Duyuru bantları, vurgulu alanlar, seçili satır | — |
| Şeftali | `#FFD8C2` | Dekoratif kareler, ders kapakları | — |
| Yüzey | `#F7F7F8` | Platform içi sayfa zemini, çekmeceler | — |
| Kenarlık | `#E4E4E7` / `#C9C9CF` | Kart kenarları / form ve ikincil buton kenarları | — |
| Başarı | `#1F7A4D` (zemin `#EAF5EF`) | Tamamlandı işaretleri | 5.32:1 |
| Hata | `#B42318` | Sadece hata mesajları. Marka turuncusundan belirgin şekilde koyu ve kırmızı. | 6.57:1 |

## Tipografi

- **Başlıklar:** Source Sans 3, 700. DIVERSE'in kullandığı lisanslı Myriad Pro'ya en yakın ücretsiz (OFL) seçenek.
- **Arayüz ve gövde:** Inter, 400–600. Gövde metni en az 16 px.
- Fontlar **kendi sunucumuzdan** sunulmalı. Google Fonts CDN'den yüklemek Almanya'da GDPR ihlali sayılabiliyor
  (partnerlerden biri TH Rosenheim).

## Şekiller

- Butonlar: oval (pill), DIVERSE'teki gibi.
- Kartlar: 16 px köşe, `#E4E4E7` ince kenarlık.
- Form alanları: 10 px köşe, 50 px yükseklik.
- Tıklanabilir alanlar en az 44 × 44 px.

## Tasarım ilkeleri

1. **Landing page gösterişli, platformun içi sade.** İç ekranlarda beyaz zemin ve bol boşluk kullanılır;
   turuncu yalnızca vurgu içindir. Cam efekti (glassmorphism), yıldızlı arka plan ve 3D kartlar kullanılmaz.
2. **Dashboard "şimdi ne yapmalıyım?" sorusunu cevaplar:** önce yaklaşan işler, sonra ilerleme çubuklu ders kartları.
3. **Üst menüde en fazla 5 öğe.** Dil seçici (EN / TR / DE / HR) her ekranda görünür.
4. **Partner markası:** DIVERSE çerçevesi sabit kalır; her partnerin logosu Boost Union *flavours* ile gösterilir.
5. **Önce mobil**, animasyon minimumda.

## Taslak ekranlar (`mockups/`)

| Dosya | Ekran |
|---|---|
| `Main.dc.html` | Landing page (ziyaretçi) |
| `Login.dc.html` | Giriş: önce üniversite seçimi |
| `Dashboard.dc.html` | Öğrenci dashboard'u |
| `Course.dc.html` | Ders sayfası (Business Law) |
| `Mobile.dc.html` | Mobil dashboard |
| `Style.dc.html` | Renk ve tipografi sayfası |

Dosyalar Claude Design tuval formatındadır (`.dc.html`). Tarayıcıda tek başına açıldıklarında düzen görünür ancak
bazı dinamik alanlar (`{{brand}}` rengi) boş kalır; eksiksiz görünüm için yukarıdaki çevrimiçi tuvali kullanın.
Taslaklardaki ilerleme yüzdeleri ve tarihler örnektir.

## Durum

- [x] `theme_diverse` oluşturuldu: palet, tipografi ve şekiller.
- [x] Inter ve Source Sans 3 font dosyaları `theme/diverse/fonts/` altına eklendi (OFL 1.1, Moodle'ın kendisi sunuyor).
- [ ] Landing page ve giriş sayfasını `theme_moove` içinden `theme_diverse`'e taşımak.
- [ ] Varsayılan temayı `diverse` yapmak ve Boost Union ayarlarını (flavours, footer, yasal sayfalar) yapılandırmak.
