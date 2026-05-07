# BillingHub Web Installer

This directory contains the web-based installation wizard for BillingHub, making it easy for non-technical users to install the system.

## Overview

The installer provides a user-friendly interface for:
- System requirement validation
- Database configuration
- Application setup
- Admin account creation
- Automatic Laravel configuration

## Files

- **install.php** - Main installer interface with requirement checks
- **setup.php** - Handles form submission and configures the application

## Usage

1. Extract the BillingHub package to your web server
2. Open `http://your-domain.com/installer/install.php` in your browser
3. Follow the wizard steps
4. System will auto-configure everything
5. You'll be redirected to login after successful setup

## How It Works

### Phase 1: Requirements Check
The installer verifies:
- PHP version (8.2+)
- Required PHP extensions (OpenSSL, PDO, Mbstring, JSON, Curl)
- File permissions
- Directory writability

### Phase 2: User Input
Collects:
- Database host, port, name, user, password
- Application name and URL
- Admin account details (name, email, password)

### Phase 3: Setup Execution
The setup.php script:
1. Tests database connection
2. Creates database if needed
3. Configures .env file with provided values
4. Runs Laravel migrations
5. Seeds initial data
6. Creates admin user

### Phase 4: Completion
User is redirected to login with new credentials

## System Requirements

- PHP 8.2+
- MySQL 8.0+ or MariaDB
- 500MB+ free disk space
- Write permissions on installation directory

## Troubleshooting

### Database Connection Failed
- Verify host, port, username, password
- Ensure MySQL service is running
- Check if user has database creation privileges

### File Permission Errors
- Ensure web server has write access to billing-system directory
- Run: `chmod -R 755 /path/to/billing-system`

### White Screen After Setup
- Check Laravel logs at `storage/logs/laravel.log`
- Ensure APP_KEY is properly set in .env

## Configuration After Setup

After installation, configure:
1. **Email Provider** - Set up SMTP in .env
2. **Payment Gateways** - Add API keys in Admin Settings
3. **Pterodactyl** - If using game server provisioning
4. **SSL Certificate** - For HTTPS support

## Security Notes

- The installer should be removed after setup for security
- Change admin password immediately after setup
- Use strong database passwords
- Enable HTTPS on production servers

## Support

For issues or questions, visit our documentation at https://docs.billinghub.local or open an issue on GitHub.
