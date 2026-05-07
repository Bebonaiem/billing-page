# 🚀 BillingHub

**Complete Open-Source Billing & Invoicing System**

BillingHub is a production-ready, self-hosted billing solution designed for service providers, hosting companies, and SaaS businesses. Built with Laravel 12 and Livewire 4, it provides everything you need to manage customers, invoices, payments, and services.

![License](https://img.shields.io/badge/license-MIT-green)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Release](https://img.shields.io/badge/release-v1.0.0-blue)

## ✨ Features

### 💳 Payment Processing
- **Multiple Gateways:** Stripe, PayPal, Bank Transfer
- **Recurring Billing:** Automatic invoice generation and renewal
- **Payment Tracking:** Complete transaction history
- **Invoice Management:** Generate, send, and track invoices
- **PDF Export:** Professional invoice PDFs

### 👥 Client Management
- **Client Portal:** Self-service dashboard
- **Service Management:** View active services
- **Invoice History:** Download and track invoices
- **Support Tickets:** Submit and manage support requests
- **Payment History:** Track all transactions

### 🎛️ Admin Dashboard
- **Sales Overview:** Revenue tracking and analytics
- **Order Management:** Complete order lifecycle
- **User Management:** Client and staff accounts
- **Settings:** Comprehensive configuration options
- **Reports:** Detailed financial reports

### 🔄 Automation
- **Recurring Invoicing:** Automatic invoice generation
- **Late Fees:** Automatic late payment fees
- **Auto-Suspension:** Suspend services on non-payment
- **Email Notifications:** Automated alerts
- **Scheduled Tasks:** Cron-based automation

### 📧 Email System
- **Custom Templates:** Drag-and-drop email builder
- **Queue System:** Background email processing
- **Multiple Providers:** SMTP, Mailgun, SendGrid
- **Test Emails:** Send test messages to verify setup
- **Email Logs:** Track all sent emails

### 🎮 Pterodactyl Integration
- **Game Server Provisioning:** Auto-create servers on order
- **Node Management:** Multiple node support
- **Egg Configuration:** Predefined server types
- **Service Automation:** Seamless integration

### 🔌 Extension System
- **Custom Extensions:** Add functionality via plugins
- **Hook System:** Integrate at key events
- **Easy Installation:** One-click extension setup
- **Developer-Friendly:** Clean API for extensions

### 🎫 Support System
- **Ticket Management:** Track customer issues
- **Department Routing:** Automatic assignment
- **File Attachments:** Share files with tickets
- **Email Notifications:** Stay updated on ticket changes
- **Knowledge Base:** Optional FAQ section

### 🔐 Security
- **Laravel Authentication:** Built-in auth system
- **Role-Based Access:** Admin and client roles
- **CSRF Protection:** Token-based form security
- **SQL Injection Prevention:** Parameterized queries
- **XSS Protection:** Output escaping

### 📱 Responsive Design
- **Mobile-Friendly:** Works on all devices
- **Tailwind CSS 4:** Modern, clean interface
- **Livewire 4:** Real-time interactions
- **Progressive Enhancement:** Works without JavaScript

## 🚀 Quick Start

### Option 1: One-Liner Installation (Recommended)

```bash
curl -fsSL https://raw.githubusercontent.com/yourusername/billinghub/main/installer/quick-install.sh | bash
```

Or with wget:
```bash
wget -qO- https://raw.githubusercontent.com/yourusername/billinghub/main/installer/quick-install.sh | bash
```

This automatically:
- Clones the repository
- Checks requirements
- Installs dependencies
- Sets permissions
- Starts the installer wizard

### Option 2: Manual Git Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/billinghub.git
cd billinghub

# Install dependencies
composer install --no-dev
npm install && npm run build

# Start web server
php -S localhost:8000

# Open installer in browser
# http://localhost:8000/installer/install.php
```

### Option 3: Production Deployment (Ubuntu/Debian)

```bash
# Download and run the Ubuntu installer
curl -fsSL https://raw.githubusercontent.com/yourusername/billinghub/main/scripts/install_ubuntu.sh | bash
```

This script:
- Installs Nginx, PHP, MySQL
- Configures web server
- Sets up SSL certificate
- Configures cron jobs
- Starts queue workers

### Web Installer

Once you have the code, open `/installer/install.php` in your browser:

1. **System Check** - Verifies PHP extensions and file permissions
2. **Database Setup** - Enter MySQL credentials (auto-creates tables)
3. **Admin Account** - Create super admin user
4. **Finish** - System auto-configures and redirects to login

## 📋 Requirements

- **PHP:** 8.2 or higher
- **Database:** MySQL 8.0+ or MariaDB 10.6+
- **Web Server:** Nginx (recommended) or Apache
- **Node.js:** 18+ (for frontend assets)
- **Composer:** 2.0+ (for PHP packages)
- **Storage:** 500MB minimum, 2GB recommended

### PHP Extensions
- openssl
- pdo
- pdo_mysql
- mbstring
- json
- curl
- gd (for image handling)
- zip (for exports)

## 📁 Project Structure

```
billinghub/
├── app/                          # Laravel application code
│   ├── Console/                  # Artisan commands
│   ├── Http/Controllers/         # Request handlers
│   ├── Livewire/                 # Interactive components
│   ├── Models/                   # Database models (30 models)
│   ├── Services/                 # Business logic
│   └── Providers/                # Service providers
├── database/
│   ├── migrations/               # Database migrations (30 tables)
│   ├── factories/                # Model factories for testing
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Tailwind CSS
│   └── js/                       # JavaScript/Alpine
├── routes/
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   └── console.php               # Console commands
├── config/                       # Configuration files
├── installer/                    # Web-based installer
│   ├── install.php               # Installer UI
│   ├── setup.php                 # Setup handler
│   └── README.md                 # Installer documentation
├── website/                      # Marketing website
│   ├── index.html                # Landing page
│   ├── download.html             # Download page
│   └── docs.html                 # Documentation
├── scripts/
│   ├── package-release.sh        # Release packaging script
│   └── install_ubuntu.sh         # Ubuntu installer
├── storage/
│   ├── logs/                     # Application logs
│   ├── app/                      # Uploaded files
│   └── framework/                # Cache and sessions
├── tests/                        # Automated tests
├── vendor/                       # Composer dependencies
├── node_modules/                 # NPM dependencies
├── .env.example                  # Environment template
├── composer.json                 # PHP dependencies
├── package.json                  # NPM dependencies
├── artisan                       # Laravel CLI
└── README.md                     # This file
```

## 🗄️ Database Schema

BillingHub includes 30 database tables for comprehensive billing management:

- **users** - Customer and admin accounts
- **invoices** - Invoice records
- **invoice_items** - Line items for invoices
- **orders** - Customer orders
- **order_items** - Items in orders
- **payments** - Payment transactions
- **products** - Services/products catalog
- **categories** - Product categories
- **services** - Active customer services
- **coupons** - Discount codes
- **tickets** - Support tickets
- **ticket_attachments** - Ticket files
- **email_templates** - Email customization
- **extensions** - Installed extensions
- **credit_transactions** - Account credit history
- **payment_gateways** - Gateway configurations
- Plus 14 more supporting tables

## 🔧 Configuration

### Environment Variables (.env)

Key configuration options:

```env
# Application
APP_NAME=BillingHub
APP_URL=https://your-domain.com
APP_KEY=base64:xxxxx...

# Database
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=billinghub
DB_USERNAME=root
DB_PASSWORD=

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=

# Payment Gateways (configured via admin panel)
STRIPE_PUBLIC_KEY=
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=

# Pterodactyl (optional)
PTERODACTYL_URL=
PTERODACTYL_TOKEN=
```

### Admin Configuration

Configure via Admin Panel → Settings:
- Payment gateways (Stripe, PayPal, Bank Transfer)
- Email provider credentials
- Notification preferences
- Pterodactyl integration
- Email templates
- Product options
- Recurring billing rules

## 💻 Management

### Admin Panel
Access at: `http://your-domain.com/admin`

Features:
- Dashboard with sales overview
- Product and service management
- Order tracking and invoicing
- Payment processing
- User management
- Support ticket handling
- Extension management
- System settings

### Client Portal
Access at: `http://your-domain.com/dashboard`

Features:
- View active services
- Invoice history and downloads
- Payment submissions
- Support ticket submission
- Account information update
- Password management

## 🚀 Deployment

### Docker (Recommended)

```bash
# Build image
docker build -t billinghub .

# Run container
docker run -d \
  -e DB_HOST=mysql \
  -e MAIL_HOST=smtp.example.com \
  -p 8080:80 \
  billinghub
```

### Ubuntu/Debian

```bash
# Use the automated installer
bash scripts/install_ubuntu.sh
```

### cPanel/Shared Hosting

1. Extract to public_html
2. Create MySQL database
3. Run installer at `/installer/install.php`
4. Configure cron for scheduler

### AWS/DigitalOcean/Linode

1. Deploy code to server
2. Set up Nginx/Apache
3. Configure SSL
4. Run installer
5. Set up queue worker
6. Configure scheduler

## 📚 Documentation

Full documentation available in:
- `INSTALL.md` - Detailed installation guide
- `installer/README.md` - Installer documentation
- `website/docs.html` - User documentation
- `config/` - Configuration files
- Code comments - Inline documentation

## 🧪 Testing

Run tests:
```bash
php artisan test
php artisan test --filter=PaymentTest
```

## 🔐 Security Considerations

- Use HTTPS in production
- Keep dependencies updated: `composer update`
- Use strong database passwords
- Configure firewalls properly
- Regular backups recommended
- Monitor logs: `storage/logs/laravel.log`
- Use environment files for secrets
- Enable rate limiting

## 🛠️ Development

### Code Standards
- PSR-12 PHP standard
- Blade templating
- Livewire components
- Tailwind CSS utilities

### Adding Extensions

Extensions go in `app/Extensions/`:

```php
<?php
namespace App\Extensions;

class MyExtension {
    public function register() {
        // Register hooks
        hooks()->listen('invoice.created', function($invoice) {
            // Custom logic
        });
    }
}
```

### Database Migrations

Create migrations:
```bash
php artisan make:migration create_table_name
```

Run migrations:
```bash
php artisan migrate
```

Rollback:
```bash
php artisan migrate:rollback
```

## 📝 License

BillingHub is open-source software licensed under the MIT License. See [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

## 📞 Support

- 📖 **Documentation:** [Full docs](website/docs.html)
- 🐛 **Issues:** [GitHub Issues](https://github.com/yourusername/billinghub/issues)
- 💬 **Discussions:** [GitHub Discussions](https://github.com/yourusername/billinghub/discussions)
- 📧 **Email:** support@billinghub.local

## 🙏 Credits

Built with:
- [Laravel](https://laravel.com/) - Web application framework
- [Livewire](https://livewire.laravel.com/) - Full-stack framework for Laravel
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev/) - Lightweight JavaScript
- [MySQL](https://www.mysql.com/) - Database

## 📊 Status

**Version:** 1.0.0  
**Status:** Production Ready  
**Last Updated:** May 2026

All core features implemented and tested. Production deployment ready.

---

**Ready to get started?** [Download now](website/download.html) or [view documentation](website/docs.html)

**Questions?** Check [FAQ](website/docs.html) or [open an issue](https://github.com/yourusername/billinghub/issues)

Built with ❤️ for service providers
