# DIVERSE Moodle

DIVERSE European University Alliance üyesi üniversitelerin (Algebra Bernays University, İstanbul Beykent Üniversitesi,
TH Rosenheim) ortak öğrenme platformu. [Moodle](https://moodle.org) 5.0.7 üzerine kuruludur.

*A shared learning platform for members of the DIVERSE European University Alliance, built on Moodle 5.0.7.
The project documentation is in Turkish.*

## Hızlı başlangıç (yerel)

[Docker Desktop](https://www.docker.com/products/docker-desktop/) ve Git kurulu olmalı:

```bash
git clone -c core.longpaths=true https://github.com/slmndrmz1134/GT4T-MOODLE.git
cd GT4T-MOODLE
docker compose up -d --build
```

İlk açılış birkaç dakika sürer, ardından site http://localhost:8080 adresinde açılır. Kod düzenlerken ayrı bir
terminalde `docker compose watch` açık kalır. Ayrıntılar: [docker.md](docker.md).

## Kılavuzlar

| Belge | İçerik |
|---|---|
| [CLAUDE.md](CLAUDE.md) | **Önce bunu okuyun.** Geliştiriciler ve yapay zekâ ajanları için proje kuralları |
| [docker.md](docker.md) | Yerel kurulum (Docker), günlük komutlar, sorun giderme |
| [docs/CPANEL.md](docs/CPANEL.md) | Canlı sunucu (cPanel) kurulumu ve güncelleme |
| [DESIGN.md](DESIGN.md) | Renkler, fontlar, ölçüler (tasarım değerleri) |
| [docs/design/README.md](docs/design/README.md) | Tasarım ilkeleri ve taslak ekranlar |
| [docs/YAPILANLAR.md](docs/YAPILANLAR.md) | Şimdiye kadar yapılanlar, nedenleri, açık riskler ve yapılacaklar |
| [AGENTS.md](AGENTS.md) | Diğer yapay zekâ ajanları için CLAUDE.md'ye yönlendirme |

## Temel kurallar

Tamamı [CLAUDE.md](CLAUDE.md) içinde. Kısaca:

1. Tema ve renkler bozulmaz; renkler ve fontlar yalnızca `theme/diverse/scss/pre.scss` içinde tanımlıdır.
2. Tema ayarları, dil ayarları ve diğer eklentiler bozulmaz; yeni özellik kendi başına çalışan bir eklenti olarak yazılır.
3. Her değişiklikten önce ne yapılacağı anlatılır, belirsiz isteklerde sorulur.
4. Yerelde test edilmeden `main` dalına push yapılmaz.

## Yapı

| Yol | Ne |
|---|---|
| `theme/diverse/` | DIVERSE teması (Boost Union alt teması) |
| `setup/` | Kurulum betikleri: `diverse_setup.php`, `check_lang_settings.php`, `cpanel/` |
| `database/moodle.sql` | Veritabanı dökümü |
| `docs/` | Belgeler |
| Geri kalanı | Moodle çekirdeği ve eklentiler |

## Lisans

Moodle ve bu projedeki kod [GNU GPL v3 veya sonrası](https://www.gnu.org/copyleft/gpl.html) lisanslıdır. Fontların
lisansları `theme/diverse/fonts/` altındadır.
