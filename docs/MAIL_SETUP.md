# Giden e-posta (Gmail SMTP)

Moodle'ın e-postaları (şifre sıfırlama, bildirimler) Gmail SMTP ile gider. Lokal Docker ve sunucu **aynı kodu**
kullanır: `config.php` ayarları ortam değişkenlerinden okur, şifre hiçbir dosyaya yazılmaz.

- Lokal: `docker-entrypoint-custom.sh` içindeki `config.php` şablonu
- Sunucu: [`setup/cpanel/config.php.dist`](../setup/cpanel/config.php.dist)

```php
if (getenv('SMTP_USER')) {
    $CFG->smtphosts      = getenv('SMTP_HOST') ?: 'smtp.gmail.com:587';
    $CFG->smtpsecure     = getenv('SMTP_SECURE') ?: 'tls';
    $CFG->smtpauthtype   = 'LOGIN';
    $CFG->smtpuser       = getenv('SMTP_USER');
    $CFG->smtppass       = (string)getenv('SMTP_PASS');
    $CFG->noreplyaddress = getenv('SMTP_FROM') ?: getenv('SMTP_USER');
}
```

`SMTP_USER` boşsa hiçbir şey ayarlanmaz; site e-postasız açılmaya devam eder (şifre sıfırlama "cannotmailconfirm"
hatası verir).

| Değişken | Zorunlu | Varsayılan | Açıklama |
|---|---|---|---|
| `SMTP_USER` | evet | | Gmail adresi |
| `SMTP_PASS` | evet | | Gmail **uygulama şifresi** (hesap şifresi değil) |
| `SMTP_HOST` | | `smtp.gmail.com:587` | `sunucu:port` |
| `SMTP_SECURE` | | `tls` | 465 portu için `ssl` |
| `SMTP_FROM` | | `SMTP_USER` | Gönderen adres; Gmail yalnızca hesabın kendisinden ya da doğrulanmış takma adından gönderir |

## Gmail uygulama şifresi

1. Google hesabında **2 adımlı doğrulama** açık olmalı (myaccount.google.com > Güvenlik).
2. https://myaccount.google.com/apppasswords > ad olarak "Moodle" > **Oluştur**.
3. Çıkan 16 karakterlik şifreyi **boşluksuz** `SMTP_PASS` olarak girin. Sızarsa aynı sayfadan silip yenisini alın.

**Sınır:** normal Gmail hesabı günde yaklaşık **500 alıcıya** gönderir (Google Workspace hesabı ~2.000). Aşılınca
gönderim 24 saate kadar durur. Ders başına çok öğrenciye bildirim giden canlı kullanımda bu sınır yetmeyebilir;
o zaman üniversitenin SMTP sunucusu ya da bir e-posta servisi aynı değişkenlerle bağlanır.

## Lokal (Docker)

```bash
cp .env.example .env      # .env git'e girmez; değerleri doldurun
docker compose up -d --build moodle
```

`--build` gerekir: `config.php` şablonu imajdaki entrypoint betiğinde. Kapsayıcı yeniden oluşturulunca `config.php`
yeniden üretilir; ona elle eklenen satırlar (ör. PHPUnit ayarları) gider.

## Sunucu (Docker yok)

**cPanel'de en kolay yol: şifre dosyası.** `config.php`, `public_html`'in yanındaki `.moodle-smtp.env` dosyasını
okur (bkz. [`setup/cpanel/config.php.dist`](../setup/cpanel/config.php.dist)). Dosya web kökünün dışındadır, site ve
cron aynı dosyayı kullanır; Apache, php-fpm ya da cron satırında ayar gerekmez. Canlı sunucuda (Ekim 2026) bu yol
kullanılıyor, hesap `beykenthinkhub@gmail.com`:

```bash
# /home/thinkhub/.moodle-smtp.env  (sahibi thinkhub, chmod 600)
SMTP_USER=beykenthinkhub@gmail.com
SMTP_FROM=beykenthinkhub@gmail.com
SMTP_PASS=uygulamasifresi16k
```

Aşağıdaki yollar, şifre dosyası kullanılmak istenmezse geçerlidir.

Değişkenleri web sunucusu PHP'ye verir. Şifreyi repodaki `.htaccess`'e yazmayın: dosya git'te ve `git pull` onu
değiştirir.

**Apache (mod_php ya da Apache + php-fpm):** VirtualHost'a ya da onun içerdiği bir dosyaya:

```apache
SetEnv SMTP_USER "hesap@gmail.com"
SetEnv SMTP_PASS "abcdabcdabcdabcd"
```

cPanel'de VirtualHost'a eklenen dosya: `/etc/apache2/conf.d/userdata/ssl/2_4/KULLANICI/ALAN_ADI/smtp.conf` (HTTP için
`std/2_4/...` altında da). Dosya `root` sahipli ve `600` olsun. Ardından:

```bash
/scripts/rebuildhttpdconf && /scripts/restartsrv_httpd     # cPanel
systemctl restart httpd    # ya da: systemctl restart apache2 (Debian/Ubuntu)
```

**php-fpm havuzu (Nginx + php-fpm ya da Apache + php-fpm):** havuz dosyasına (ör.
`/etc/php/8.2/fpm/pool.d/www.conf`):

```ini
env[SMTP_USER] = hesap@gmail.com
env[SMTP_PASS] = abcdabcdabcdabcd
```

`clear_env = yes` (varsayılan) sistem ortam değişkenlerini PHP'den gizler; `env[...]` satırları yine geçer. Bu yüzden
`clear_env = no` yapmayın, değişkenleri `env[...]` ile verin. Ardından `systemctl restart php8.2-fpm` (RHEL/AlmaLinux:
`systemctl restart php-fpm`, cPanel: `/scripts/restartsrv_apache_php_fpm`). cPanel havuz dosyalarını kendisi yeniden
üretebilir; elle yapılan değişiklik kaybolursa Apache `SetEnv` yolunu kullanın.

**Cron:** `admin/cli/cron.php` komut satırındaki PHP ile çalışır ve Apache'nin ya da php-fpm'in değişkenlerini görmez.
Forum bildirimleri gibi e-postaların çoğu cron'dan gider. Değişkenleri bir dosyaya yazın (`chmod 600`):

```bash
# ~/.moodle-smtp.env
export SMTP_USER="hesap@gmail.com"
export SMTP_PASS="abcdabcdabcdabcd"
```

ve cron satırında okuyun:

```cron
* * * * * . $HOME/.moodle-smtp.env; /usr/local/bin/php /home/KULLANICI/public_html/admin/cli/cron.php >/dev/null 2>&1
```

## Kontrol

```bash
nc -vz smtp.gmail.com 587     # "succeeded" / "open" görünmeli
```

Bağlanamıyorsa sunucu dışarıya SMTP'yi engelliyordur: WHM > Security Center > **SMTP Restrictions** (açıkken yalnızca
sunucunun posta servisi dış SMTP'ye bağlanabilir), CSF güvenlik duvarında `SMTP_BLOCK` ya da barındırma firmasının
güvenlik duvarı. 465 açıksa `SMTP_HOST=smtp.gmail.com:465` ve `SMTP_SECURE=ssl` de olur.

Moodle'da deneme: Site yönetimi > Sunucu > E-posta > **Giden e-posta yapılandırmasını test et**
(`/admin/testoutgoingmailconf.php`), sonra giriş sayfasındaki "Şifremi unuttum".

| Hata | Sebep |
|---|---|
| `SMTP connect() failed` | Port kapalı ya da `SMTP_HOST` yanlış |
| `Could not authenticate` / `535` | Uygulama şifresi yanlış ya da 2 adımlı doğrulama kapalı; hesap şifresi kullanılmış |
| Gönderen adres hesabınkine dönüyor | `SMTP_FROM` Gmail'de doğrulanmış bir takma ad değil |
| Bir süre sonra gönderim duruyor | Günlük gönderim sınırı aşıldı |
