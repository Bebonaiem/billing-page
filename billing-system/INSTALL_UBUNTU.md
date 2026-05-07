# Billing System Ubuntu Installation

This guide installs the project on an Ubuntu server under `/var/www/billing-system`, configures Nginx, seeds the database, creates the admin account, and sets up the queue worker and scheduler.

## Requirements

- Ubuntu 22.04 or 24.04
- PHP 8.2 with FPM
- Composer
- Nginx
- MySQL or MariaDB
- Node.js and npm if you want to build assets on the server
- Git if you want the script to clone the repository

## 1. Download the project to `/var/www/`

You can either clone the repository directly or upload the project files first.

Example using Git:

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone <YOUR_REPOSITORY_URL> billing-system
cd billing-system
```

If the project is already uploaded, place it in `/var/www/billing-system` and continue.

## 2. Use the installer script

Copy `scripts/install_ubuntu.sh` to the server, make it executable, and run it as root or with sudo:

```bash
chmod +x scripts/install_ubuntu.sh
sudo ./scripts/install_ubuntu.sh
```

The script will ask for:

- the repository URL if the project is not already present
- the application URL, such as `https://billing.example.com` or `http://localhost`
- the Nginx server name, such as your domain or `localhost`
- database name, user, and password
- admin name, email, and password

## 3. Manual installation commands

If you prefer to run the commands yourself, use the sequence below.

```bash
sudo apt update
sudo apt install -y nginx git unzip curl php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl composer nodejs npm mariadb-server

cd /var/www
sudo git clone <YOUR_REPOSITORY_URL> billing-system
cd /var/www/billing-system

sudo cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
php artisan view:cache
```

## 4. Environment variables

Update `.env` with your production values:

```env
APP_NAME="Billing System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://billing.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billing_system
DB_USERNAME=billing_user
DB_PASSWORD=strong-password

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_STORE=file
```

If you are using localhost only, set `APP_URL=http://localhost`.

## 5. Default admin login

The seeder creates a default admin account:

- Email: `admin@example.com`
- Password: `password`

The installer script can also update or create the admin account using the credentials you provide during installation.

## 6. Nginx configuration

Create an Nginx site that points to the `public` directory:

```nginx
server {
    listen 80;
    server_name billing.example.com;

    root /var/www/billing-system/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/billing-system /etc/nginx/sites-enabled/billing-system
sudo nginx -t
sudo systemctl reload nginx
```

If you want HTTPS, point your domain to the server first and then run Certbot.

## 7. Queue worker and scheduler

The app uses the database queue and scheduled tasks. Make sure both are running:

```bash
sudo systemctl enable --now billing-system-queue
crontab -l | { cat; echo "* * * * * cd /var/www/billing-system && /usr/bin/php artisan schedule:run >> /dev/null 2>&1"; } | crontab -
```

## 8. Useful commands

```bash
php artisan queue:restart
php artisan optimize:clear
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
```

## Notes

- The install script sets up the project in `/var/www/billing-system`.
- For a domain, set `APP_URL` and `server_name` to your real hostname.
- For local development on the server, use `http://localhost` and `server_name localhost`.
