# Moodle Docker Kullanım Kılavuzu

Bu proje, özelleştirilmiş ve yamalanmış Moodle 5.0.7 sürümünü eklentileri (`tool_mutenancy`, `tool_mulib`) ve teması (`theme_moove`) ile birlikte Docker üzerinde tek tıkla çalıştırmanızı sağlar.

---

## 🛠️ Gereksinimler

Konteynerleri çalıştırmadan önce bilgisayarınızda aşağıdaki araçların kurulu olduğundan emin olun:
1. [Docker Desktop](https://www.docker.com/products/docker-desktop/) (veya Linux için Docker Engine & Docker Compose)
2. [Git](https://git-scm.com/)

---

## 🚀 Projeyi Çalıştırma (İlk Kurulum ve Çalıştırma)

Projeyi herhangi bir bilgisayarda ayağa kaldırmak için aşağıdaki adımları sırasıyla uygulayın:

### 1. Depoyu Klonlayın
Projeyi çalıştırmak istediğiniz bilgisayarda terminali/PowerShell'i açın ve repoyu klonlayın:
```bash
git clone https://github.com/Brmjx/GT4T-MOODLE.git
cd GT4T-MOODLE
```

### 2. Konteynerleri Derleyin ve Çalıştırın
Aşağıdaki komut Docker imajını yerel kod tabanınızla derleyecek ve konteynerleri arka planda (detached mod) başlatacaktır:
```bash
docker compose up -d --build
```
> **Not:** İlk kurulumda Moodle kodlarının kopyalanması ve veritabanının hazırlanması bilgisayarınızın hızına bağlı olarak 1-2 dakika sürebilir.

### 3. Tarayıcıda Açın ve Kurulumu Tamamlayın
Konteynerler ayağa kalktıktan sonra tarayıcınızdan aşağıdaki adreslere erişebilirsiniz:
* **Moodle:** [http://localhost:8080](http://localhost:8080) (Moodle kurulum ekranı açılacaktır. Lisans koşullarını onaylayarak kurulumu tek tıkla tamamlayabilirsiniz.)
* **phpMyAdmin (Veritabanı Yönetimi):** [http://localhost:8081](http://localhost:8081)
  * **Sunucu (Host):** `mysql`
  * **Kullanıcı adı:** `root`
  * **Şifre:** `rootpass`

---

## 🌐 Sunucu (Production) Kurulumu

Sunucuda (örneğin `thinkhub.club` üzerinde SSL/TLS reverse proxy aktif şekilde) çalıştırmak için:

### 1. `.env` Dosyası Oluşturun
Proje ana dizininde bir `.env` dosyası oluşturun ve aşağıdaki değişkenleri tanımlayın:
```env
MOODLE_URL=https://thinkhub.club
MOODLE_PORT=8080
COMPOSE_PROFILES=prod
MOODLE_SSLPROXY=true
MOODLE_REVERSEPROXY=false
MOODLE_THEMEDESIGNERMODE=false
PHP_OPCACHE_ENABLE=1
```

Tam örnek: `.env.production.example` dosyasına bakın.

#### Değişken Açıklamaları:
* **`MOODLE_URL`**: Sitenizin dışarıdan erişilecek tam adresi (örneğin `https://thinkhub.club`).
* **`MOODLE_PORT`**: Moodle konteynerinin dışa açılacağı port (Nginx üzerinden ters proxy yapılıyorsa `8080` kalabilir).
* **`COMPOSE_PROFILES`**: `prod` değeri verilerek production profilini (Nginx reverse proxy servisiyle birlikte) aktifleştirir.
* **`MOODLE_SSLPROXY`**: Moodle'ın bir SSL reverse proxy (ters proxy) arkasında çalıştığını ve SSL sonlandırma işleminin proxy üzerinde yapıldığını belirtir (Moodle `config.php` dosyasındaki `$CFG->sslproxy = true;` ayarı). HTTPS üzerinden güvenli erişim için `true` yapılmalıdır.
* **`MOODLE_REVERSEPROXY`**: Moodle'ın bir ters proxy arkasında çalıştığını belirtir (Moodle `config.php` dosyasındaki `$CFG->reverseproxy = true;` ayarı). Sunucu konfigürasyonunuza göre `false` veya `true` olarak ayarlanabilir (mevcut sunucunuzda `false` olarak ayarlanmıştır).


### 2. Konteynerleri Sunucuda Başlatın
Sunucuda aşağıdaki komutla tüm servisleri (Nginx dahil) ayağa kaldırabilirsiniz:
```bash
docker compose up -d --build
```
* **Nginx**, SSL sertifikalarını sunucudaki `/etc/letsencrypt` dizininden okuyacak ve `https://thinkhub.club` adresini güvenli bir şekilde sunacaktır.
* Yerel bilgisayarınızda `.env` dosyası oluşturmadığınız sürece `nginx` servisi başlamayacak, böylece yerel geliştirme ortamınızda sertifika hataları almayacaksınız.

---

## 🛑 Konteynerleri Durdurma

Projeyi durdurmak istediğinizde proje klasöründe şu komutu çalıştırın:
```bash
docker compose down
```
> **Önemli:** Bu komut konteynerleri durdurur ancak yüklediğiniz Moodle verilerini veya veritabanı kayıtlarınızı silmez. Verileriniz Docker hacimlerinde (volumes) güvenli bir şekilde saklanır.

---

## 🔄 Güncellemeleri Uygulama (Yeniden Derleme)

Kodda, Dockerfile'da veya eklentilerde bir değişiklik yaptığınızda, sunucuda şu komutları çalıştırın:

```bash
git pull origin main
docker compose up -d --build
docker compose exec moodle php /var/www/html/setup/moodle_init.php
docker compose exec moodle php /var/www/html/admin/cli/purge_caches.php
```

> **Not:** `git push` tek başına yeterli değildir. Moodle kodu Docker imajının içindedir; `--build` olmadan eski imaj çalışmaya devam eder. Tema ayarları ve ikonlar veritabanında saklanır; `moodle_init.php` bunları günceller.

---

## 🎨 CSS Değişikliklerini Anında Yansıtma (Rebuild Olmadan)

Tema CSS dosyasında (`theme/moove/style/moodle.css`) değişiklik yaptıktan sonra, konteyneri yeniden derlemeden (rebuild) değişiklikleri doğrudan çalışan konteynere kopyalayabilirsiniz:

```bash
docker cp "c:\Users\asus\Desktop\moodle\theme\moove\style\moodle.css" moodle-moodle-1:/var/www/html/theme/moove/style/moodle.css
```

Alternatif olarak (projenin ana dizinindeyseniz):
```bash
docker cp "theme/moove/style/moodle.css" moodle-moodle-1:/var/www/html/theme/moove/style/moodle.css
```

Kopyaladıktan sonra Moodle tema önbelleğini temizleyin:
```bash
docker exec moodle-moodle-1 php /var/www/html/admin/cli/purge_caches.php --theme
```

Ardından tarayıcıda `Ctrl + F5` ile sayfayı yenileyin.

> **⚠️ Production uyarısı:** `themedesignermode` veya `styles_debug.php` asla canlıda kullanılmamalı. PageSpeed’te 100+ CSS isteği ve ~1,3 sn “render-blocking” buna bağlıdır. Canlıda `MOODLE_THEMEDESIGNERMODE=false` olmalı (`.env` + aşağıdaki komut).

### 502 Bad Gateway (site açılmıyor)

Nginx çalışıyor ama **moodle** konteyneri yanıt vermiyorsa 502 alırsınız. Sunucuda:

```bash
cd GT4T-MOODLE
docker compose ps
docker compose logs moodle --tail 80
docker compose logs nginx --tail 30
```

`moodle` **Restarting** veya logda `purge_caches` / PHP fatal varsa:

```bash
docker compose up -d --build moodle
# Apache ayakta mı (200/303 beklenir):
curl -sI http://127.0.0.1:8080/
docker compose up -d nginx
```

Hâlâ 502 ise: `docker compose restart moodle` ve 1–2 dk bekleyin (ilk boot + cache purge uzun sürebilir).

### Canlı performans (thinkhub.club / PageSpeed)

Designer mode açıksa tarayıcı **100+** `styles_debug.php` dosyası indirir. Kapalıyken tek birleşik tema CSS kullanılır.

```bash
docker compose exec moodle php /var/www/html/admin/cli/cfg.php --name=themedesignermode --set=0
docker compose exec moodle php /var/www/html/admin/cli/purge_caches.php
docker compose up -d --build   # entrypoint + nginx gzip için
```

Kontrol: sayfa kaynağında `themedesignermode` body sınıfı **olmamalı**; Network’te `styles_debug.php` **olmamalı**.

**Logo (LCP):** Tema ayarındaki logo 120×120 px görünüyor; yüklediğiniz dosya 640×640 ise Admin → Appearance → Theme moove → logo’yu **WebP/PNG ~120–240 px** olarak yeniden yükleyin.

**Geliştirme only:** Yerelde SCSS denemek için geçici olarak `MOODLE_THEMEDESIGNERMODE=true` yapılabilir; commit/deploy öncesi mutlaka `false`.

---

## 🔍 Hata Ayıklama ve Loglar

Eğer bir servis başlamazsa veya hata alırsanız, konteyner loglarını inceleyebilirsiniz:
```bash
# Tüm servislerin loglarını görmek için
docker compose logs

# Sadece moodle servisini takip etmek için (canlı)
docker compose logs -f moodle
```

---

## ⚠️ Verileri Sıfırlama (Temiz Kurulum)

Tüm veritabanını ve yüklenen Moodle verilerini tamamen silip sıfırdan temiz bir kurulum başlatmak isterseniz:
```bash
# Konteynerleri ve oluşturulan tüm verileri (volume'lar dahil) siler
docker compose down -v
```
Ardından `docker compose up -d --build` komutuyla projeyi sıfır veriyle yeniden başlatabilirsiniz.
