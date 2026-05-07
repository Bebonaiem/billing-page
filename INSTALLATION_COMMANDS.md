# ⚡ BillingHub - Correct Installation Commands

**IMPORTANT:** After cloning, always navigate to `billing-system/` before running any commands!

## Quick Copy-Paste Setup (Ubuntu 24.04)

```bash
# 1. System dependencies
apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
curl -sSL https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash -s -- --mariadb-server-version="mariadb-10.11"
apt update
apt -y install php8.2 php8.2-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip,intl,redis} mariadb-server nginx tar unzip git redis-server nodejs npm composer

# 2. Clone repository
mkdir -p /var/www && cd /var/www
git clone https://github.com/Bebonaiem/billing-page.git billinghub
cd billinghub/billing-system  # ← CRITICAL: Must be in this directory!

# 3. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 4. Set permissions
chmod -R 775 /var/www/billinghub/billing-system/storage /var/www/billinghub/billing-system/bootstrap/cache
chown -R www-data:www-data /var/www/billinghub

# 5. Create database (change yourPassword!)
mysql -u root -p <<EOF
CREATE USER 'billinghub'@'127.0.0.1' IDENTIFIED BY 'yourPassword';
CREATE DATABASE billinghub;
GRANT ALL PRIVILEGES ON billinghub.* TO 'billinghub'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF

# 6. Configure application (edit .env after!)
cp .env.example .env
# Edit .env: Set APP_URL, DB_USERNAME, DB_PASSWORD, DB_DATABASE

# 7. Initialize database
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force --seed
php artisan app:init

# 8. Setup queue worker service
sudo tee /etc/systemd/system/billinghub.service > /dev/null <<EOF
[Unit]
Description=BillingHub Queue Worker

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/billinghub/billing-system/artisan queue:work
StartLimitInterval=180
StartLimitBurst=30
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

# 9. Enable services
sudo systemctl daemon-reload
sudo systemctl enable --now billinghub.service
sudo systemctl enable --now redis-server

# 10. Setup cron scheduler
sudo crontab -u www-data -e
# Add line: * * * * * /usr/bin/php /var/www/billinghub/billing-system/artisan schedule:run >> /dev/null 2>&1

# 11. Configure Nginx
sudo tee /etc/nginx/sites-available/billinghub > /dev/null <<EOF
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/billinghub/billing-system/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

sudo ln -s /etc/nginx/sites-available/billinghub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# 12. Setup SSL
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com

# Done! Access at https://your-domain.com
# Login: admin@example.com
```

## ✅ Verify Installation

```bash
# Check if all services are running
sudo systemctl status php8.2-fpm
sudo systemctl status mariadb
sudo systemctl status redis-server
sudo systemctl status nginx
sudo systemctl status billinghub.service

# Test database connection
cd /var/www/billinghub/billing-system
php artisan tinker

# In tinker shell, test:
DB::connection()->getPdo();  # Should return connection object
\Redis::ping();              # Should return 'PONG'
exit

# Check cron is scheduled
sudo crontab -l -u www-data | grep artisan
```

## 🚨 Common Mistakes (Do NOT do these!)

### ❌ WRONG - Running from wrong directory:
```bash
cd /var/www/billinghub
composer install  # ← Will fail: "composer.json not found"
npm install       # ← Will fail: "package.json not found"
```

### ✅ RIGHT - Run from correct directory:
```bash
cd /var/www/billinghub/billing-system
composer install  # ← Works!
npm install       # ← Works!
```

### ❌ WRONG - Wrong Nginx root:
```nginx
root /var/www/billinghub/public;  # ← Wrong!
```

### ✅ RIGHT - Correct Nginx root:
```nginx
root /var/www/billinghub/billing-system/public;  # ← Correct!
```

### ❌ WRONG - Wrong artisan path:
```bash
/usr/bin/php /var/www/billinghub/artisan queue:work  # ← Wrong!
```

### ✅ RIGHT - Correct artisan path:
```bash
/usr/bin/php /var/www/billinghub/billing-system/artisan queue:work  # ← Correct!
```

## 📂 Project Structure Reference

```
/var/www/billinghub/
├── website/                    ← Marketing website (not needed for app)
├── installer/                  ← Web installer (alternative setup method)
├── scripts/                    ← Build scripts
└── billing-system/             ← ← ← Laravel Application (THE REAL APP)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/                 ← Web root for Nginx
    ├── resources/
    ├── routes/
    ├── storage/                ← Logs, cache, uploads
    ├── tests/
    ├── artisan                 ← PHP CLI tool
    ├── composer.json           ← ← Located HERE
    ├── package.json            ← ← Located HERE
    ├── vite.config.js
    ├── phpunit.xml
    └── .env                    ← Configuration file
```

## 🆘 Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| `composer.json not found` | Wrong directory | `cd /var/www/billinghub/billing-system` |
| `package.json not found` | Wrong directory | `cd /var/www/billinghub/billing-system` |
| `Connection refused` | Wrong Nginx root or PHP-FPM not running | Check root path, `systemctl status php8.2-fpm` |
| `SQLSTATE[HY000]: General error` | Database not created | Run MySQL commands to create user/database |
| Queue jobs not processing | Service not running | `sudo systemctl status billinghub.service` |
| Cron not running | Schedule not registered | Check `sudo crontab -l -u www-data` |

## 📞 Support

- 📖 Full documentation: [docs.html](website/docs.html)
- 🚀 Setup guide: [SETUP_GUIDE.md](SETUP_GUIDE.md)
- 🔍 Audit report: [INSTALLATION_AUDIT_REPORT.md](INSTALLATION_AUDIT_REPORT.md)

