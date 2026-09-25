# DIVERSE Tasarım Rehberi

DIVERSE platformunun görsel dili. Renkler [diverse-university.eu](https://diverse-university.eu/) sitesinden alındı ve
tüm metin/zemin çiftleri **WCAG 2.1 AA** kontrast şartını (normal metin ≥ 4.5:1) karşılayacak şekilde ayarlandı.

- **Tema kodu:** `theme/diverse` (Boost Union alt teması). Palet `theme/diverse/scss/pre.scss` içinde tanımlı,
  açıklaması [`DESIGN.md`](../../DESIGN.md) içinde.
- **Taslaklar:** `docs/design/mockups/` (kaynak dosyalar) ve çevrimiçi tuval:
  https://claude.ai/artifact/6RSY4o46VHzDGv2ZyLaot8 (sahibi paylaşana kadar yalnızca ona açık).

## Renkler, fontlar ve ölçüler

Tüm değerler (SCSS değişken adları, renk kodları, kontrast oranları, fontlar, köşe yuvarlaklıkları, genişlikler)
kök dizindeki [`DESIGN.md`](../../DESIGN.md) dosyasında tutulur. Değerler iki yerde yazılıp birbirinden
ayrılmasın diye burada tekrar edilmez.

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
- [x] Landing page (ziyaretçiler için ana sayfa) ve giriş sayfası marka paneli `theme_diverse` içinde kodlandı;
  yerel Docker'da masaüstü ve mobilde test edildi.
- [x] Yerel test ortamında varsayılan tema `diverse` yapıldı (canlı sunucuda henüz değil).
- [ ] Resmi DIVERSE logo dosyasını eklemek (şimdilik yazı logosu).
- [ ] Landing ve giriş metinlerinin Türkçe, Almanca ve Hırvatça çevirileri.
- [ ] Boost Union ayarları: partner flavours (logolar), footer, yasal sayfalar (Künye, erişilebilirlik beyanı).
- [ ] Dashboard ve ders sayfası ince ayarları (giriş yapmış kullanıcıyla test).
