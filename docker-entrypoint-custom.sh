#!/bin/bash
# DIVERSE Custom Entrypoint (local development only; the live site runs on cPanel, see docs/CPANEL.md)
# On first boot the mysql service imports database/moodle.sql (docker-compose.yml). This script then
# upgrades Moodle to the code in the image and applies setup/diverse_setup.php once.

set -e

# Moodle CLI scripts run as the web server user, so that the cache files they create in moodledata stay
# writable for Apache. Scripts run as root leave files that Apache cannot write.
as_www() {
  runuser -u www-data -- "$@"
}

# ── 1. Generate config.php (copied from base image entrypoint) ──────────────
if [ ! -f /var/www/html/config.php ]; then
  # Evaluate booleans for PHP
  REVERSE_PROXY_BOOL=$([ "$MOODLE_REVERSEPROXY" = "true" ] && echo "true" || echo "false")
  SSL_PROXY_BOOL=$([ "$MOODLE_SSLPROXY" = "true" ] && echo "true" || echo "false")
  # Default OFF — designer mode loads 100+ styles_debug.php URLs and destroys LCP (never use in production).
  THEME_DESIGNER_MODE_BOOL=$([ "$MOODLE_THEMEDESIGNERMODE" = "true" ] && echo "true" || echo "false")

  cat <<EOF > /var/www/html/config.php
<?php
unset(\$CFG);
global \$CFG;
\$CFG = new stdClass();

\$CFG->dbtype    = '${MOODLE_DB_TYPE:-mariadb}';
\$CFG->dblibrary = 'native';
\$CFG->dbhost    = '${MOODLE_DB_HOST:-localhost}';
\$CFG->dbname    = '${MOODLE_DB_NAME:-moodle}';
\$CFG->dbuser    = '${MOODLE_DB_USER:-moodle}';
\$CFG->dbpass    = '${MOODLE_DB_PASS:-moodle}';
\$CFG->prefix    = 'mdl_';
\$CFG->dboptions = array(
  'dbpersist' => false,
  'dbsocket'  => false,
  'dbport'    => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

\$CFG->wwwroot   = rtrim('${MOODLE_URL:-http://localhost:8080}', '/');
\$CFG->dataroot  = '${MOODLE_DATA:-/var/www/moodledata}';
\$CFG->admin     = 'admin';
\$CFG->directorypermissions = 02777;

\$CFG->smtphosts  = '${SMTP_HOST:-localhost}';
\$CFG->smtpuser   = '${SMTP_USER}';
\$CFG->smtppass   = '${SMTP_PASSWORD}';
\$CFG->smtpsecure = '${SMTP_PROTOCOL:-tls}';
\$CFG->smtpport   = '${SMTP_PORT:-587}';
\$CFG->noreplyaddress = '${MOODLE_MAIL_NOREPLY_ADDRESS:-noreply@localhost}';

\$CFG->reverseproxy = $REVERSE_PROXY_BOOL;
\$CFG->sslproxy     = $SSL_PROXY_BOOL;
\$CFG->themedesignermode = $THEME_DESIGNER_MODE_BOOL;

// ── Cache configuration (Redis + APCu) ─────────────────────────────────
if (class_exists('Redis') && getenv('MOODLE_CACHE_REDIS_HOST')) {
  \$CFG->cachestores = [
    'redis_application' => [
      'name'     => 'Redis Application Cache',
      'plugin'   => 'redis',
      'configuration' => [
        'server'     => getenv('MOODLE_CACHE_REDIS_HOST') . ':' . (getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379'),
        'prefix'     => getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_',
        'serializer' => 2,
        'compressor' => 0,
      ],
      'features' => 30,
      'modes'    => 3,
      'default'  => 1,
    ],
    'redis_session' => [
      'name'     => 'Redis Session Cache',
      'plugin'   => 'redis',
      'configuration' => [
        'server'     => getenv('MOODLE_CACHE_REDIS_HOST') . ':' . (getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379'),
        'prefix'     => getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_',
        'serializer' => 2,
        'compressor' => 0,
      ],
      'features' => 14,
      'modes'    => 2,
      'default'  => 1,
    ],
  ];

  // Point session handler to Redis-backed session cache store
  \$CFG->session_redis_host = getenv('MOODLE_CACHE_REDIS_HOST');
  \$CFG->session_redis_port = getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379';
  \$CFG->session_redis_prefix = getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_';
  \$CFG->session_redis_acquire_lock_timeout = 120;
  \$CFG->session_redis_lock_expire = 7200;
}

// APCu as local cache
if (function_exists('apcu_enabled') && apcu_enabled()) {
  \$CFG->localcachedir = '/var/www/moodledata/localcache';
}

require_once(__DIR__ . '/lib/setup.php');
EOF
  chown www-data:www-data /var/www/html/config.php
  echo "[entrypoint] config.php generated."
fi

# ── 1b. Inject cache config into existing config.php if missing ────────────
if [ -f /var/www/html/config.php ] && [ -n "$MOODLE_CACHE_REDIS_HOST" ]; then
  if ! grep -q 'Redis Application Cache' /var/www/html/config.php 2>/dev/null; then
    echo "[entrypoint] Injecting Redis cache config into existing config.php ..."
    php -r "
      \$cfg = file_get_contents('/var/www/html/config.php');
      \$inject = <<<'PHP'

// ── Cache configuration (Redis + APCu) ─────────────────────────────────
if (class_exists('Redis') && getenv('MOODLE_CACHE_REDIS_HOST')) {
  \\\$CFG->cachestores = [
    'redis_application' => [
      'name'     => 'Redis Application Cache',
      'plugin'   => 'redis',
      'configuration' => [
        'server'     => getenv('MOODLE_CACHE_REDIS_HOST') . ':' . (getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379'),
        'prefix'     => getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_',
        'serializer' => 2,
        'compressor' => 0,
      ],
      'features' => 30,
      'modes'    => 3,
      'default'  => 1,
    ],
    'redis_session' => [
      'name'     => 'Redis Session Cache',
      'plugin'   => 'redis',
      'configuration' => [
        'server'     => getenv('MOODLE_CACHE_REDIS_HOST') . ':' . (getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379'),
        'prefix'     => getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_',
        'serializer' => 2,
        'compressor' => 0,
      ],
      'features' => 14,
      'modes'    => 2,
      'default'  => 1,
    ],
  ];

  \\\$CFG->session_redis_host = getenv('MOODLE_CACHE_REDIS_HOST');
  \\\$CFG->session_redis_port = getenv('MOODLE_CACHE_REDIS_PORT') ?: '6379';
  \\\$CFG->session_redis_prefix = getenv('MOODLE_CACHE_REDIS_PREFIX') ?: 'gt4t_';
  \\\$CFG->session_redis_acquire_lock_timeout = 120;
  \\\$CFG->session_redis_lock_expire = 7200;
}

if (function_exists('apcu_enabled') && apcu_enabled()) {
  \\\$CFG->localcachedir = '/var/www/moodledata/localcache';
}

PHP;
      \$cfg = str_replace('require_once(__DIR__ . ' . \"'\"'\"'\"'/lib/setup.php'\"'\"'\"');', \$inject . \"\n\" . 'require_once(__DIR__ . ' . \"'\"'\"'\"'/lib/setup.php'\"'\"'\"');\", \$cfg);
      file_put_contents('/var/www/html/config.php', \$cfg);
    " 2>/dev/null || echo "[entrypoint] Cache injection skipped."
    echo "[entrypoint] Redis cache config injected."
  else
    echo "[entrypoint] Redis cache config already present."
  fi
fi

# ── 2. Wait for MySQL ───────────────────────────────────────────────────────
DB_HOST="${MOODLE_DB_HOST:-localhost}"
DB_USER="${MOODLE_DB_USER:-moodle}"
DB_PASS="${MOODLE_DB_PASS:-moodle}"
DB_NAME="${MOODLE_DB_NAME:-moodle}"

echo "[entrypoint] Waiting for MySQL at $DB_HOST ..."
until php -r "
  \$c = @mysqli_connect('$DB_HOST', '$DB_USER', '$DB_PASS', '$DB_NAME');
  exit(\$c ? 0 : 1);
" 2>/dev/null; do
  sleep 2
done
echo "[entrypoint] MySQL is ready."

# Files that earlier root-run scripts left in moodledata go back to the web server user.
find /var/www/moodledata ! -user www-data -exec chown www-data:www-data {} + 2>/dev/null || true

# ── 3. Auto-install Moodle database if not yet installed ────────────────────
MOODLE_INSTALLED=$(as_www php -r "
  define('CLI_SCRIPT', true);
  require('/var/www/html/config.php');
  try {
    \$v = \$DB->get_field('config', 'value', ['name' => 'version']);
    echo \$v ? '1' : '0';
  } catch (Exception \$e) {
    echo '0';
  }
" 2>/dev/null || echo '0')

if [ "$MOODLE_INSTALLED" != "1" ]; then
  # Only reached when the database is empty although the mysql service normally imports the dump on its first
  # start (for example when the database volume existed before). Installs an empty Moodle instead.
  echo "[entrypoint] Moodle not installed and no dump imported. Running install_database.php ..."

  as_www php /var/www/html/admin/cli/install_database.php \
    --lang=en \
    --fullname="DIVERSE European University" \
    --shortname="DIVERSE" \
    --adminuser=admin \
    --adminpass=Admin1234! \
    --adminemail=admin@example.com \
    --agree-license

  echo "[entrypoint] Moodle database installed."
  MOODLE_INSTALLED=1
else
  echo "[entrypoint] Moodle already installed."
fi

# ── 3a. Bring the database up to the code in the image, apply the DIVERSE setup once ──
# Never fail container start if CLI errors (set -e would stop Apache from starting).
if [ "$MOODLE_INSTALLED" = "1" ]; then
  echo "[entrypoint] Running admin/cli/upgrade.php ..."
  as_www php /var/www/html/admin/cli/upgrade.php --non-interactive || echo "[entrypoint] upgrade.php failed (continuing)."

  SETUP_DONE=/var/www/moodledata/.diverse_setup_done
  if [ ! -f "$SETUP_DONE" ]; then
    echo "[entrypoint] First boot: running setup/diverse_setup.php ..."
    if as_www php /var/www/html/setup/diverse_setup.php; then
      as_www touch "$SETUP_DONE"
    else
      echo "[entrypoint] diverse_setup.php failed (continuing); it runs again on the next boot."
    fi
  fi
fi

# ── 3b. Enforce theme designer mode from env (fixes PageSpeed / styles_debug.php flood) ──
# Never fail container start if CLI errors (set -e would block Apache → nginx 502).
if [ "$MOODLE_INSTALLED" = "1" ] && [ "$MOODLE_THEMEDESIGNERMODE" != "true" ]; then
  if grep -q 'themedesignermode' /var/www/html/config.php 2>/dev/null; then
    sed -i 's/\$CFG->themedesignermode\s*=\s*true/\$CFG->themedesignermode = false/' /var/www/html/config.php || true
    echo "[entrypoint] config.php themedesignermode set to false."
  fi
  THEME_MODE=$(as_www php /var/www/html/admin/cli/cfg.php --name=themedesignermode 2>/dev/null || echo "1")
  if [ "$THEME_MODE" != "0" ]; then
    as_www php /var/www/html/admin/cli/cfg.php --name=themedesignermode --set=0 || true
  fi
  if [ "${MOODLE_AUTO_PURGE_CACHES:-false}" = "true" ]; then
    echo "[entrypoint] Purging Moodle caches as requested by environment ..."
    as_www php /var/www/html/admin/cli/purge_caches.php || echo "[entrypoint] purge_caches failed (continuing)."
  else
    echo "[entrypoint] Skipping cache purge on container boot (optimized for load speed)."
  fi
  echo "[entrypoint] themedesignermode disabled."
fi

# ── 4. Start cron in background ─────────────────────────────────────────────
cat <<'CRONEOF' > /usr/local/bin/run-cron.sh
#!/bin/bash
while true; do
    cd /var/www/html
    runuser -u www-data -- /usr/local/bin/php admin/cli/cron.php >> /var/log/cron.log 2>&1 || \
        echo "[$(date)] Cron error, retrying in 60s" >> /var/log/cron.log
    sleep 60
done
CRONEOF
chmod +x /usr/local/bin/run-cron.sh
nohup /usr/local/bin/run-cron.sh > /dev/null 2>&1 &

# ── 5. Start Apache ─────────────────────────────────────────────────────────
exec apache2-foreground
