#!/usr/bin/env bash

set -euo pipefail

APP_NAME="Billing System"
APP_DIR="/var/www/billing-system"
PHP_BIN="/usr/bin/php"
COMPOSER_BIN="composer"
NPM_BIN="npm"

prompt_default() {
    local prompt_text="$1"
    local default_value="$2"
    local input_value

    read -r -p "${prompt_text} [${default_value}]: " input_value
    echo "${input_value:-$default_value}"
}

require_command() {
    local command_name="$1"

    if ! command -v "$command_name" >/dev/null 2>&1; then
        echo "Missing required command: ${command_name}"
        exit 1
    fi
}

if [ "${EUID}" -ne 0 ]; then
    echo "Run this script with sudo or as root."
    exit 1
fi

require_command php
require_command composer
require_command nginx
require_command sed
require_command mysql

REPO_URL=$(prompt_default "Repository URL (leave blank if /var/www/billing-system already exists)" "")
APP_URL=$(prompt_default "Application URL" "http://localhost")
SERVER_NAME=$(prompt_default "Nginx server_name" "localhost")
DB_NAME=$(prompt_default "Database name" "billing_system")
DB_USER=$(prompt_default "Database user" "billing_user")
DB_PASSWORD=$(prompt_default "Database password" "billing_password")
ADMIN_NAME=$(prompt_default "Admin full name" "Admin User")
ADMIN_FIRST_NAME=$(prompt_default "Admin first name" "Admin")
ADMIN_LAST_NAME=$(prompt_default "Admin last name" "User")
ADMIN_EMAIL=$(prompt_default "Admin email" "admin@example.com")
ADMIN_PASSWORD=$(prompt_default "Admin password" "password")

if [ ! -d "$APP_DIR" ]; then
    if [ -z "$REPO_URL" ]; then
        echo "$APP_DIR does not exist and no repository URL was provided."
        exit 1
    fi

    mkdir -p /var/www
    git clone "$REPO_URL" "$APP_DIR"
fi

cd "$APP_DIR"

if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i "s|^APP_NAME=.*$|APP_NAME=\"${APP_NAME}\"|" .env
sed -i "s|^APP_ENV=.*$|APP_ENV=production|" .env
sed -i "s|^APP_DEBUG=.*$|APP_DEBUG=false|" .env
sed -i "s|^APP_URL=.*$|APP_URL=${APP_URL}|" .env
sed -i "s|^DB_CONNECTION=.*$|DB_CONNECTION=mysql|" .env
sed -i "s|^DB_DATABASE=.*$|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|^DB_USERNAME=.*$|DB_USERNAME=${DB_USER}|" .env
sed -i "s|^DB_PASSWORD=.*$|DB_PASSWORD=${DB_PASSWORD}|" .env
sed -i "s|^QUEUE_CONNECTION=.*$|QUEUE_CONNECTION=database|" .env
sed -i "s|^SESSION_DRIVER=.*$|SESSION_DRIVER=file|" .env
sed -i "s|^CACHE_STORE=.*$|CACHE_STORE=file|" .env

sql_escape() {
    printf '%s' "${1//\'/\'\'}"
}

DB_NAME_SAFE=$(printf '%s' "$DB_NAME" | tr -cd '[:alnum:]_')
DB_USER_SAFE=$(printf '%s' "$DB_USER" | tr -cd '[:alnum:]_')
DB_NAME_SAFE=${DB_NAME_SAFE:-billing_system}
DB_USER_SAFE=${DB_USER_SAFE:-billing_user}

DB_NAME_SQL=$(sql_escape "$DB_NAME_SAFE")
DB_USER_SQL=$(sql_escape "$DB_USER_SAFE")
DB_PASSWORD_SQL=$(sql_escape "$DB_PASSWORD")

mysql <<SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME_SQL} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER_SQL}'@'localhost' IDENTIFIED BY '${DB_PASSWORD_SQL}';
GRANT ALL PRIVILEGES ON ${DB_NAME_SQL}.* TO '${DB_USER_SQL}'@'localhost';
FLUSH PRIVILEGES;
SQL

if [ -f package-lock.json ] && command -v npm >/dev/null 2>&1; then
    npm ci
    npm run build
elif command -v npm >/dev/null 2>&1; then
    npm install
    npm run build
fi

composer install --no-dev --optimize-autoloader
php artisan key:generate --force

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true

ADMIN_EMAIL="$ADMIN_EMAIL" ADMIN_NAME="$ADMIN_NAME" ADMIN_FIRST_NAME="$ADMIN_FIRST_NAME" ADMIN_LAST_NAME="$ADMIN_LAST_NAME" ADMIN_PASSWORD="$ADMIN_PASSWORD" php artisan tinker --execute='
\App\Models\User::updateOrCreate(
    ["email" => getenv("ADMIN_EMAIL")],
    [
        "name" => getenv("ADMIN_NAME"),
        "first_name" => getenv("ADMIN_FIRST_NAME"),
        "last_name" => getenv("ADMIN_LAST_NAME"),
        "password" => getenv("ADMIN_PASSWORD"),
        "status" => "active",
        "email_verified_at" => now(),
    ]
);'

chown -R www-data:www-data "$APP_DIR"
find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;

cat >/etc/nginx/sites-available/billing-system <<EOF
server {
    listen 80;
    server_name ${SERVER_NAME};

    root ${APP_DIR}/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/billing-system /etc/nginx/sites-enabled/billing-system
rm -f /etc/nginx/sites-enabled/default

cat >/etc/systemd/system/billing-system-queue.service <<EOF
[Unit]
Description=Billing System Queue Worker
After=network.target mysql.service mariadb.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
ExecStart=${PHP_BIN} ${APP_DIR}/artisan queue:work --sleep=3 --tries=3 --queue=default,emails
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable --now billing-system-queue

(crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1") | crontab -

php artisan optimize
php artisan view:cache

nginx -t
systemctl reload nginx

echo "Installation complete."
echo "App URL: ${APP_URL}"
echo "Admin login: ${ADMIN_EMAIL} / ${ADMIN_PASSWORD}"
