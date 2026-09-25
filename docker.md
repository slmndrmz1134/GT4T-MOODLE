# Yerel Kurulum (Docker)

DIVERSE Moodle'ı kendi bilgisayarında çalıştırmak isteyen geliştiriciler için. Canlı sunucu Docker kullanmaz,
onun adımları [docs/CPANEL.md](docs/CPANEL.md) dosyasında. Projede çalışmadan önce [CLAUDE.md](CLAUDE.md)
kurallarını okuyun.

## Gereksinimler

- **Docker Desktop** (Windows / Mac) ya da Docker Engine. Docker Compose **2.22 veya üstü** (`docker compose version`).
- **Git.**
- Yaklaşık 4 GB boş disk.

## Kurulum

```bash
git clone -c core.longpaths=true https://github.com/slmndrmz1134/GT4T-MOODLE.git
cd GT4T-MOODLE
docker compose up -d --build
```

- `-c core.longpaths=true` Windows için gereklidir. Repoda Windows'un 260 karakterlik yol sınırını aşan dosyalar var;
  bu ayar olmadan klonlama "Filename too long" hatasıyla yarım kalır.
- İlk açılış 5-10 dakika sürebilir (sonraki açılışlar saniyeler): imaj derlenir, `database/moodle.sql` veritabanına yüklenir, Moodle güncellenir ve
  `setup/diverse_setup.php` DIVERSE ayarlarını uygular (tema, dil paketleri...). İlerlemeyi izlemek için:
  ```bash
  docker compose logs -f moodle
  ```
  `[entrypoint] themedesignermode disabled.` satırından sonra site hazırdır.

| Adres | Ne |
|---|---|
| http://localhost:8080 | Moodle |
| http://localhost:8081 | phpMyAdmin (sunucu `mysql`, kullanıcı `root`, şifre `rootpass`; yalnızca yerel) |

Test hesapları veritabanı dökümünden gelir. Hesap bilgilerini proje sahibinden alın.

## Kod düzenlerken

Kod imajın içindedir. Değiştirdiğiniz dosyaların çalışan sisteme geçmesi için ayrı bir terminalde şu komut açık kalır:

```bash
docker compose watch
```

Repoda değiştirilen, eklenen ya da silinen her dosya birkaç saniye içinde kapsayıcıya aktarılır. `watch` kapalıyken
yapılan değişiklikler aktarılmaz; o durumda imaj yenilenir:

```bash
docker compose up -d --build moodle
```

Değişikliğe göre ayrıca:

| Ne değişti | Komut |
|---|---|
| SCSS, şablon, dil dosyası | `docker compose exec -u www-data moodle php admin/cli/purge_caches.php` |
| `version.php` ya da yeni eklenti | `docker compose exec -u www-data moodle php admin/cli/upgrade.php --non-interactive` |
| Dil ayarları bozuldu mu? | `docker compose exec -u www-data moodle php setup/check_lang_settings.php` (önce ve sonra, bkz. CLAUDE.md) |

Moodle komutları her zaman `-u www-data` ile çalıştırılır.

## Sık kullanılan komutlar

| İş | Komut |
|---|---|
| Durdurmak | `docker compose stop` |
| Yeniden başlatmak | `docker compose up -d` |
| Kayıtlar (log) | `docker compose logs -f moodle` |
| Kapsayıcının içine girmek | `docker compose exec -u www-data moodle bash` |
| **Her şeyi sıfırlamak** | `docker compose down -v` ve ardından `docker compose up -d --build` |

`docker compose down -v` yerel veritabanını ve yüklenen dosyaları **siler**; bir sonraki açılışta döküm yeniden
yüklenir. Yerelde yaptığınız ayar ve içerik değişiklikleri kaybolur.

## Nasıl çalışır

- **Kod imajın içinde, klasör olarak bağlanmaz.** Windows'ta repo klasörünü kapsayıcıya bağlamak her sayfayı 2-3
  saniyeye çıkarıyordu (Moodle her istekte yüzlerce dosya okur). İmajın içindeyken sayfalar 0,05 saniyede açılır.
- **Veritabanı dökümü yalnızca ilk açılışta yüklenir** (veritabanı volume'u boşken). `database/moodle.sql` sonradan
  değişirse yerel veritabanına kendiliğinden geçmez; yeni dökümle başlamak için sıfırlayın.
- **Her açılışta** `admin/cli/upgrade.php` çalışır. **İlk açılışta** bir kez `setup/diverse_setup.php` çalışır
  (tekrar çalışmaması için `moodledata/.diverse_setup_done` işaret dosyası bırakır).
- Moodle'ın zamanlanmış görevleri (cron) kapsayıcının içinde her dakika çalışır.
- Önbellek ve oturumlar Redis'te tutulur.

## Sorun giderme

| Belirti | Çözüm |
|---|---|
| 8080 portu dolu | Başka bir port kullanın: repo kök dizininde `.env` dosyası oluşturup `MOODLE_PORT=8090` ve `MOODLE_URL=http://localhost:8090` yazın, sonra `docker compose up -d`. |
| Site yanlış adresle açılıyor | Kök dizindeki `.env` dosyasında başka değerler var. Silin ya da yukarıdaki gibi yerel değerler yazın. |
| Değişiklik görünmüyor | `docker compose watch` açık mı? Açıksa önbelleği temizleyin (yukarıdaki tablo). |
| Klonlarken "Filename too long" | `git clone -c core.longpaths=true ...` ile klonlayın. |
| İlk açılış bitmiyor | `docker compose logs mysql` (döküm yükleniyor mu?) ve `docker compose logs moodle`. |
| "dependency failed to start: ... mysql ... is unhealthy" | Döküm yüklemesi çok uzun sürdü. Yükleme arka planda devam eder; birkaç dakika sonra `docker compose up -d` komutunu tekrar çalıştırın. |
| Docker Desktop açılmıyor, "... .sock ... cannot be accessed" hatası | Docker Desktop'ı kapatıp Windows'u yeniden başlatın. **"Reset to factory defaults" seçmeyin:** yerel veritabanını siler. |

## Eski kurulumdan geçiş

Bu projeyi daha önce kurduysanız eski kurulum farklı bir proje adıyla ve kendi veritabanıyla çalışıyordur (örneğin
`gt4t-moodle`). Aynı portu kullandıkları için önce eskisini durdurun, sonra yenisini başlatın:

```bash
docker compose -p gt4t-moodle down
docker compose up -d --build
```

Yeni kurulum dökümden temiz bir veritabanıyla başlar. Eski veritabanı eski volume'larda durur; ihtiyaç kalmadığında
`docker volume ls` ile bulup silebilirsiniz.
