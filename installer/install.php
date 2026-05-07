<?php
/**
 * BillingHub Installation Wizard
 * 
 * This file handles the interactive installation process.
 * Users run this after extracting the BillingHub package.
 */

class BillingHubInstaller {
    private $config = [];
    private $errors = [];
    private $baseDir;

    public function __construct() {
        $this->baseDir = dirname(__DIR__);
        $this->loadConfig();
    }

    public function run() {
        // Check if already installed
        if (file_exists($this->baseDir . '/billing-system/.env')) {
            $this->showAlreadyInstalled();
            return;
        }

        // Check requirements
        if (!$this->checkRequirements()) {
            $this->showRequirementsError();
            return;
        }

        // Show installation wizard
        $this->showWizard();
    }

    private function checkRequirements() {
        $requirements = [
            'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'OpenSSL extension' => extension_loaded('openssl'),
            'PDO extension' => extension_loaded('pdo'),
            'Mbstring extension' => extension_loaded('mbstring'),
            'JSON extension' => extension_loaded('json'),
            'Curl extension' => extension_loaded('curl'),
            'File permissions' => is_writable($this->baseDir . '/billing-system'),
        ];

        foreach ($requirements as $name => $result) {
            if (!$result) {
                $this->errors[] = $name;
            }
        }

        return empty($this->errors);
    }

    private function showRequirementsError() {
        echo "<html><head><title>BillingHub - Requirements Check Failed</title>";
        echo "<style>body { font-family: Arial; background: #f5f5f5; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }";
        echo ".error { color: #d32f2f; }";
        echo "ul { margin: 10px 0; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<h2 class='error'>System Requirements Not Met</h2>";
        echo "<p>BillingHub requires the following to be installed:</p>";
        echo "<ul>";
        foreach ($this->errors as $error) {
            echo "<li class='error'>✗ {$error}</li>";
        }
        echo "</ul>";
        echo "</div></body></html>";
        exit;
    }

    private function showAlreadyInstalled() {
        echo "<html><head><title>BillingHub - Already Installed</title>";
        echo "<style>body { font-family: Arial; background: #f5f5f5; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }";
        echo ".success { color: #388e3c; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<h2 class='success'>✓ BillingHub is Already Installed</h2>";
        echo "<p>To access your installation, visit: <strong>http://your-domain.com</strong></p>";
        echo "<p><a href='http://your-domain.com'>Go to Dashboard</a></p>";
        echo "</div></body></html>";
        exit;
    }

    private function showWizard() {
        echo "<html><head><title>BillingHub Installation Wizard</title>";
        echo "<style>";
        echo "* { margin: 0; padding: 0; box-sizing: border-box; }";
        echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }";
        echo ".container { max-width: 700px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }";
        echo ".header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }";
        echo ".header h1 { font-size: 32px; margin-bottom: 10px; }";
        echo ".header p { opacity: 0.9; font-size: 16px; }";
        echo ".content { padding: 40px 30px; }";
        echo ".form-group { margin-bottom: 25px; }";
        echo "label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }";
        echo "input[type='text'], input[type='email'], input[type='password'], select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }";
        echo "input:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }";
        echo ".btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; margin-top: 20px; }";
        echo ".btn:hover { opacity: 0.9; }";
        echo ".info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 6px; margin-bottom: 25px; }";
        echo ".info-box strong { color: #1976d2; }";
        echo ".requirements-list { background: #f5f5f5; padding: 15px; border-radius: 6px; margin-bottom: 20px; }";
        echo ".requirements-list li { margin-bottom: 8px; color: #388e3c; }";
        echo ".requirements-list li.check::before { content: '✓ '; font-weight: bold; }";
        echo ".section { margin-bottom: 30px; }";
        echo ".section-title { font-size: 18px; font-weight: 600; color: #333; margin-bottom: 15px; border-bottom: 2px solid #667eea; padding-bottom: 10px; }";
        echo ".footer { background: #f5f5f5; padding: 20px 30px; text-align: center; color: #666; font-size: 14px; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>🚀 BillingHub</h1>";
        echo "<p>Installation Wizard</p>";
        echo "</div>";
        echo "<div class='content'>";
        echo "<div class='info-box'><strong>Welcome!</strong> This wizard will help you set up BillingHub in just a few minutes.</div>";

        // Check requirements passed
        echo "<div class='section'>";
        echo "<div class='section-title'>System Requirements ✓</div>";
        echo "<ul class='requirements-list'>";
        echo "<li class='check'>PHP 8.2+</li>";
        echo "<li class='check'>Database (MySQL 8.0+)</li>";
        echo "<li class='check'>Web Server (Nginx/Apache)</li>";
        echo "<li class='check'>Composer & Node.js</li>";
        echo "</ul>";
        echo "</div>";

        // Database Configuration
        echo "<div class='section'>";
        echo "<div class='section-title'>Database Configuration</div>";
        echo "<form method='POST' action='setup.php'>";
        echo "<div class='form-group'>";
        echo "<label for='db_host'>Database Host</label>";
        echo "<input type='text' id='db_host' name='db_host' value='localhost' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='db_port'>Database Port</label>";
        echo "<input type='text' id='db_port' name='db_port' value='3306' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='db_name'>Database Name</label>";
        echo "<input type='text' id='db_name' name='db_name' placeholder='billinghub' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='db_user'>Database User</label>";
        echo "<input type='text' id='db_user' name='db_user' placeholder='root' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='db_password'>Database Password</label>";
        echo "<input type='password' id='db_password' name='db_password'>";
        echo "</div>";
        echo "</div>";

        // Application Settings
        echo "<div class='section'>";
        echo "<div class='section-title'>Application Settings</div>";
        echo "<div class='form-group'>";
        echo "<label for='app_name'>Application Name</label>";
        echo "<input type='text' id='app_name' name='app_name' value='BillingHub' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='app_url'>Application URL</label>";
        echo "<input type='text' id='app_url' name='app_url' placeholder='https://yourdomain.com' required>";
        echo "</div>";
        echo "</div>";

        // Admin Account
        echo "<div class='section'>";
        echo "<div class='section-title'>Admin Account</div>";
        echo "<div class='form-group'>";
        echo "<label for='admin_name'>Admin Name</label>";
        echo "<input type='text' id='admin_name' name='admin_name' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='admin_email'>Admin Email</label>";
        echo "<input type='email' id='admin_email' name='admin_email' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='admin_password'>Admin Password</label>";
        echo "<input type='password' id='admin_password' name='admin_password' required>";
        echo "</div>";
        echo "</div>";

        // Submit Button
        echo "<button type='submit' class='btn'>Continue Installation</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='footer'>";
        echo "<p>BillingHub v1.0 | <a href='#' style='color: #666; text-decoration: none;'>Documentation</a> • <a href='#' style='color: #666; text-decoration: none;'>Support</a></p>";
        echo "</div>";
        echo "</div>";
        echo "</body></html>";
    }

    private function loadConfig() {
        // Load config from file if exists
    }
}

// Run installer
$installer = new BillingHubInstaller();
$installer->run();
