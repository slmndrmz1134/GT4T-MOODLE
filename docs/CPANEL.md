# cPanel Sunucusuna Kurulum

DIVERSE Moodle'ı canlı cPanel (AlmaLinux) sunucusuna kurma ve güncelleme adımları. Sunucuda Docker kullanılmaz:
repo doğrudan sitenin kök klasörüne alınır.

| Dosya | Ne işe yarar |
|---|---|
| [`setup/cpanel/config.php.dist`](../setup/cpanel/config.php.dist) | Moodle `config.php` şablonu (şifresiz). Sunucuda kopyalanıp doldurulur. |
| [`setup/cpanel/php.ini`](../setup/cpanel/php.ini) | PHP ayarları (MultiPHP INI Editor'e yapıştırılır). |
| [`.htaccess`](../.htaccess) | `database/moodle.sql`, `.git`, `.env`, `setup/`, `docs/` gibi dosyaları internete kapatır. |
| [`setup/diverse_setup.php`](../setup/diverse_setup.php) | DIVERSE site ayarları (`--production` ile canlı sunucu ayarları). |

Site bir alan adıyla değil, **sunucunun IP adresiyle** ve HTTP üzerinden açılır (`http://SUNUCU-IP`). Aşağıda
`KULLANICI` cPanel hesap adı, `SUNUCU-IP` bu hesaba atanmış IP adresidir (örnek: `203.0.113.10`). Komutlar
cPanel > **Terminal** (veya SSH) içinde çalıştırılır.

## Gereksinimler

- **PHP 8.2 veya 8.3.** cPanel > MultiPHP Manager.
- **PHP eklentileri:** intl, gd, zip, soap, sodium, mbstring, xml, curl, opcache, mysqlnd, fileinfo, exif, iconv
  (WHM > EasyApache 4 > PHP Extensions).
- **Veritabanı:** MySQL 8.0+ veya MariaDB 10.6.7+.
- **mod_rewrite** açık (cPanel'de varsayılan olarak açıktır).
- Hesaba atanmış **ayrı (dedicated) bir IP adresi** (aşağıda 0. adım).

## İlk kurulum

### 0. Hesap ve IP adresi (WHM)

cPanel, IP adresine gelen istekleri normalde kendi karşılama sayfasına yönlendirir. Sitenin `http://SUNUCU-IP`
adresinde açılması için IP adresi yalnızca bu hesaba ait olmalıdır:

1. WHM > **Create a New Account**: cPanel her hesap için bir alan adı ister. Alan adı kullanılmayacağı için
   herhangi bir ad yazılabilir (örnek: `diverse-moodle.local`); bu adın DNS kaydı gerekmez.
2. WHM > **IP Functions > Change Site's IP Address**: hesaba sunucunun ayrı (dedicated) IP adreslerinden birini
   atayın. Sunucunun ana (paylaşılan) IP adresi bu iş için kullanılamayabilir; sunucunun tek IP'si varsa hosting
   firmasından ek IP isteyin.
3. Tarayıcıda `http://SUNUCU-IP` açıldığında cPanel karşılama sayfası yerine hesabın `public_html` klasörü
   gelmelidir.

### 1. PHP ayarları

1. cPanel > **MultiPHP Manager**: sitenin PHP sürümünü 8.2 veya 8.3 yapın.
2. cPanel > **MultiPHP INI Editor** > Editor Mode > sitenin klasörü: `setup/cpanel/php.ini` içeriğini yapıştırın.
   Opcache satırları yalnızca WHM > MultiPHP INI Editor (sunucu geneli) içinde çalışır.

Terminalde kullanılacak PHP, sitenin sürümüyle aynı olmalı. Sürüm 8.3 ise yoldaki `82` yerine `83` yazın:

```bash
PHP=/opt/cpanel/ea-php82/root/usr/bin/php
```

### 2. Kodu siteye alın

Sitenin kök klasörü boş olmalıdır (içinde yalnızca `cgi-bin` ve `.well-known` olabilir):

```bash
cd ~/public_html
git init
git remote add origin https://github.com/slmndrmz1134/GT4T-MOODLE.git
git fetch origin
git checkout -t origin/main
```

Site bir subdomain ise `~/public_html` yerine o subdomain'in klasörü kullanılır.

### 3. Veri klasörü

`moodledata`, `public_html` klasörünün **dışında** olmalıdır:

```bash
mkdir ~/moodledata
chmod 2770 ~/moodledata
```

### 4. Veritabanı

1. cPanel > **MySQL Databases**: `KULLANICI_moodle` adında bir veritabanı ve kullanıcı oluşturun. Kullanıcıya
   veritabanı üzerinde **ALL PRIVILEGES** verin.
2. Dökümü yükleyin (şifre sorulur):

```bash
mysql -u KULLANICI_moodle -p --default-character-set=utf8mb4 KULLANICI_moodle < ~/public_html/database/moodle.sql
```

Döküm **yalnızca ilk kurulumda** yüklenir. Canlı veritabanının üzerine tekrar yüklemek tüm canlı verileri siler.

### 5. config.php

```bash
cd ~/public_html
cp setup/cpanel/config.php.dist config.php
chmod 640 config.php
```

`config.php` içinde `CHANGE` yazan her değeri doldurun (cPanel > File Manager > Edit): veritabanı adı, kullanıcı,
şifre, `wwwroot` (`http://SUNUCU-IP`), `dataroot` (`/home/KULLANICI/moodledata`) ve `noreplyaddress`. Sunucuda
MariaDB varsa `dbtype` değerini `mariadb` yapın. `config.php` git'e girmez; `git pull` onu değiştirmez.

### 6. HTTP ile çalışmak

Alan adı olmadığı için SSL sertifikası alınamaz ve site HTTP ile çalışır. Bunun sonuçları:

- Şifreler ağda şifrelenmeden gider; tarayıcı adres çubuğunda "Güvenli değil" yazar.
- Moodle mobil uygulaması HTTPS ister, bağlanamaz.

Deneme için sorun değildir. Gerçek derslere başlamadan önce alan adı ve HTTPS önerilir. Geçiş: alan adını hesaba
ekleyin, cPanel > SSL/TLS Status > **Run AutoSSL**, `config.php` içinde `wwwroot` değerini `https://alan-adi`
yapın ve 7. adımdaki `replace.php` komutunu `--search="http://SUNUCU-IP" --replace="https://alan-adi"` ile
çalıştırın. `.htaccess`, sertifika kontrolü için `.well-known` klasörünü zaten açık bırakır.

### 7. Moodle'ı hazırlayın

```bash
cd ~/public_html
$PHP admin/cli/upgrade.php --non-interactive
$PHP admin/tool/replace/cli/replace.php --search="https://thinkhub.club" --replace="http://SUNUCU-IP" --shorten --non-interactive
$PHP admin/cli/purge_caches.php
$PHP setup/diverse_setup.php --production --dry-run
$PHP setup/diverse_setup.php --production
```

- `replace.php`: dökümdeki içerikte eski adrese (`thinkhub.club`) giden linkleri yeni adrese çevirir.
- `diverse_setup.php`: önce `--dry-run` ile ne değişeceğini gösterir, sonra uygular. Dil ayarlarına dokunmaz.

### 8. Cron

cPanel > **Cron Jobs** > her dakika (`* * * * *`):

```bash
/opt/cpanel/ea-php82/root/usr/bin/php /home/KULLANICI/public_html/admin/cli/cron.php >/dev/null 2>&1
```

Cron çalışmazsa e-postalar gitmez, takvim ve bildirimler güncellenmez.

### 9. Şifreler

Dökümdeki hesapların şifreleri herkese açık repoda duruyor. Site açılmadan önce:

1. Yönetici şifresini değiştirin (şifre sorulur):
   ```bash
   $PHP admin/cli/reset_password.php --username=admin
   ```
2. Diğer tüm kullanıcılar için: Site administration > Users > Bulk user actions > tümünü seç >
   **Force password change**. Kullanıcılar ilk girişte yeni şifre belirler.

### 10. Güvenlik kontrolü

Kendi bilgisayarınızdan:

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://SUNUCU-IP/database/moodle.sql
```

Aynı kontrolü `/.git/config`, `/.env`, `/setup/diverse_setup.php` ve `/CLAUDE.md` için yapın. Hepsi **403**
dönmeli. Ana sayfa (`http://SUNUCU-IP/`) **200** dönmeli. 403 yerine 200 dönüyorsa `.htaccess` çalışmıyor demektir;
site açılmadan önce düzeltilmelidir.

Son olarak Site administration > **Notifications** ve **Server > Environment** sayfalarında kırmızı uyarı kalmadığını
kontrol edin.

## Güncelleme

Yeni kod `main` dalına geldikten sonra (Kural 4: önce yerelde test edilmiş olmalı):

```bash
cd ~/public_html
git pull
$PHP admin/cli/upgrade.php --non-interactive
$PHP admin/cli/purge_caches.php
$PHP setup/diverse_setup.php --production
```

`git pull` veritabanı dökümünü günceller ama canlı veritabanına **yüklemez**; canlı veriler korunur.

Güncelleme sırasında kullanıcıları dışarıda tutmak için başa `$PHP admin/cli/maintenance.php --enable`, sona
`$PHP admin/cli/maintenance.php --disable` ekleyin.

## Sorun giderme

| Belirti | Çözüm |
|---|---|
| `.htaccess` sonrası her sayfada 500 hatası | Sunucu `Options` satırına izin vermiyor: `.htaccess` içindeki `Options -Indexes` satırını silin. |
| "max_input_vars must be at least 5000" | 1. adımdaki PHP ayarları uygulanmamış; doğru klasör ve PHP sürümü seçili mi kontrol edin. |
| Moodle `moodledata` klasörüne yazamıyor | PHP başka bir kullanıcıyla çalışıyor: `config.php` içinde `directorypermissions` değerini `02777` yapın. |
| IP açılınca cPanel karşılama sayfası çıkıyor | IP adresi hesaba atanmamış: 0. adım. |
| Sayfa stilsiz ya da yanlış adrese yönleniyor | `wwwroot` tarayıcıdaki adresle aynı olmalı (`http://SUNUCU-IP`), sonunda `/` olmamalı. |
| Değişiklik görünmüyor | `$PHP admin/cli/purge_caches.php` |

## Canlı ders (BigBlueButton)

BigBlueButton sunucusu cPanel'e kurulamaz (Ubuntu ister, sunucunun portlarıyla çakışır), ayrı bir sunucu gerekir.
BigBlueButton sunucusunun kendisi için ise alan adı ve HTTPS şarttır: tarayıcılar kamera ve mikrofona yalnızca
HTTPS sayfalarda izin verir. Moodle'ın HTTP ile çalışması buna engel değildir, ders BigBlueButton sunucusunun
sayfasında açılır. Sunucu hazır olunca adres ve gizli anahtar Site administration > Plugins > Activity modules > BigBlueButton
sayfasına girilir. Bu bilgiler repoya yazılmaz.
