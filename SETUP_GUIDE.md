# 🚀 BillingHub Installation - Quick Setup Guide

**Status**: ✅ All commands verified and working

## ⚡ 5-Minute Installation (Ubuntu/Debian)

### Step 1: Install System Dependencies
```bash
apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
curl -sSL https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash -s -- --mariadb-server-version="mariadb-10.11"
apt update
apt -y install php8.2 php8.2-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip,intl,redis} mariadb-server nginx tar unzip git redis-server nodejs npm composer
```

### Step 2: Clone Repository
```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/Bebonaiem/billing-page.git billinghub
cd billinghub/billing-system
```

⚠️ **Important**: Make sure you're in `billing-system/` directory before continuing!

### Step 3: Install PHP & Node Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

### Step 4: Set Permissions
```bash
chmod -R 775 /var/www/billinghub/billing-system/storage /var/www/billinghub/billing-system/bootstrap/cache
chown -R www-data:www-data /var/www/billinghub
```

### Step 5: Create Database
```bash
mysql -u root -p
```

Then run (replace `yourPassword` with a strong password):
```sql
CREATE USER 'billinghub'@'127.0.0.1' IDENTIFIED BY 'yourPassword';
CREATE DATABASE billinghub;
GRANT ALL PRIVILEGES ON billinghub.* TO 'billinghub'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;
quit
```

### Step 6: Setup Environment
```bash
cp .env.example .env
# Edit .env and set:
# - APP_URL=https://your-domain.com
# - DB_USERNAME=billinghub
# - DB_PASSWORD=yourPassword
# - DB_DATABASE=billinghub
```

### Step 7: Initialize Application
```bash
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force --seed
php artisan app:init
```

### Step 8: Configure Queue & Scheduler

**Create cron job:**
```bash
sudo crontab -u www-data -e
```

Add this line:
```
* * * * * /usr/bin/php /var/www/billinghub/billing-system/artisan schedule:run >> /dev/null 2>&1
```

**Create queue worker service:**
```bash
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
```

Start services:
```bash
sudo systemctl enable --now billinghub.service
sudo systemctl enable --now redis-server
```

### Step 9: Configure Nginx
```bash
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
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/billinghub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 10: Setup SSL Certificate
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### Step 11: Login & Setup Admin
```bash
# Default admin credentials
Email: admin@example.com

# To create additional admin users:
php artisan app:user:create
```

---

## 🔧 Project Structure

After cloning, your directory structure looks like:
```
/var/www/billinghub/
├── website/              ← Marketing website
├── installer/            ← Installation wizard  
├── scripts/              ← Build scripts
└── billing-system/       ← ← Laravel Application ROOT
    ├── app/
    ├── config/
    ├── database/
    ├── public/           ← Web root
    ├── routes/
    ├── storage/
    ├── bootstrap/
    ├── artisan           ← Artisan CLI
    ├── composer.json     ← PHP dependencies
    ├── package.json      ← Node dependencies
    └── .env              ← Configuration
```

**Important:** All Laravel commands must be run from `/var/www/billinghub/billing-system/`

---

## ✅ Verification Checklist

After installation, verify everything is working:

- [ ] Database connection works (`php artisan tinker`)
- [ ] Redis connection works (`redis-cli ping`)
- [ ] Admin can login at `https://your-domain.com`
- [ ] Queue worker is running (`sudo systemctl status billinghub.service`)
- [ ] Cron job is scheduled (`sudo crontab -l -u www-data | grep artisan`)
- [ ] SSL certificate is active (`https://your-domain.com` shows no warnings)

---

## 🆘 Troubleshooting

### Composer: composer.json not found
**Error**: `Composer could not find a composer.json file`  
**Fix**: Make sure you're in `/var/www/billinghub/billing-system/` directory
```bash
cd /var/www/billinghub/billing-system
composer install
```

### npm: package.json not found
**Error**: `npm ERR! enoent ENOENT: no such file or directory, open '/var/www/billinghub/package.json'`  
**Fix**: Same as above, you need to be in the `billing-system/` subdirectory
```bash
cd /var/www/billinghub/billing-system
npm install
```

### Nginx: Connection refused
**Error**: `Connection refused` when accessing website  
**Fix**: Check that php8.2-fpm is running and nginx is pointing to correct socket
```bash
sudo systemctl status php8.2-fpm
sudo systemctl status nginx
sudo nginx -t  # Check config syntax
```

### Queue worker not processing jobs
**Error**: Jobs pile up in queue  
**Fix**: Check that the billinghub.service is running
```bash
sudo systemctl status billinghub.service
sudo systemctl restart billinghub.service
```

---

## 📧 Quick Admin Setup
After installation, log in with:
- **Email**: `admin@example.com`
- **Password**: See `database/seeders/DatabaseSeeder.php` or create a new user

To create a new admin user:
```bash
cd /var/www/billinghub/billing-system
php artisan app:user:create
```

The command will prompt you for email, name, and password.

---

## 🔐 Security Reminders

- ✅ Change default admin password immediately after login
- ✅ Keep your `APP_KEY` in `.env` backed up securely
- ✅ Use strong database password
- ✅ Enable SSL/TLS (Certbot is free)
- ✅ Keep PHP, MySQL, and system packages updated
- ✅ Configure firewall to restrict access

---

## 📚 Further Configuration

- [Email Setup](docs.html#page-email)
- [Database Configuration](docs.html#page-database)
- [Payment Gateways](docs.html#page-payments)
- [Pterodactyl Integration](docs.html#page-pterodactyl)

