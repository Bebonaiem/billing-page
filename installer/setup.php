<?php
/**
 * BillingHub Setup Handler
 * Processes the installation form and configures the application
 */

class SetupHandler {
    private $baseDir;
    private $billingDir;

    public function __construct() {
        $this->baseDir = dirname(__DIR__);
        $this->billingDir = $this->baseDir . '/billing-system';
    }

    public function handle() {
        // Validate input
        if (!$this->validateInput()) {
            $this->showError('Invalid input provided');
            return;
        }

        try {
            // Test database connection
            if (!$this->testDatabaseConnection()) {
                $this->showError('Failed to connect to database. Please check your credentials.');
                return;
            }

            // Create database
            if (!$this->createDatabase()) {
                $this->showError('Failed to create database');
                return;
            }

            // Configure .env
            if (!$this->configureEnv()) {
                $this->showError('Failed to configure .env file');
                return;
            }

            // Run Laravel setup
            if (!$this->runLaravelSetup()) {
                $this->showError('Failed to run Laravel setup');
                return;
            }

            // Show success
            $this->showSuccess();
        } catch (Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    private function validateInput() {
        return !empty($_POST['db_host']) &&
               !empty($_POST['db_name']) &&
               !empty($_POST['db_user']) &&
               !empty($_POST['app_url']) &&
               !empty($_POST['admin_email']);
    }

    private function testDatabaseConnection() {
        try {
            $connection = new PDO(
                "mysql:host={$_POST['db_host']}:{$_POST['db_port']}",
                $_POST['db_user'],
                $_POST['db_password'] ?? ''
            );
            return $connection !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function createDatabase() {
        try {
            $connection = new PDO(
                "mysql:host={$_POST['db_host']}:{$_POST['db_port']}",
                $_POST['db_user'],
                $_POST['db_password'] ?? ''
            );

            $dbName = $_POST['db_name'];
            $connection->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function configureEnv() {
        $envPath = $this->billingDir . '/.env';
        
        if (!file_exists($envPath . '.example')) {
            return false;
        }

        $env = file_get_contents($envPath . '.example');

        // Replace values
        $env = str_replace('DB_HOST=localhost', "DB_HOST={$_POST['db_host']}", $env);
        $env = str_replace('DB_PORT=3306', "DB_PORT={$_POST['db_port']}", $env);
        $env = str_replace('DB_DATABASE=laravel', "DB_DATABASE={$_POST['db_name']}", $env);
        $env = str_replace('DB_USERNAME=root', "DB_USERNAME={$_POST['db_user']}", $env);
        $env = str_replace('DB_PASSWORD=', "DB_PASSWORD={$_POST['db_password']}", $env);
        $env = str_replace('APP_URL=http://localhost', "APP_URL={$_POST['app_url']}", $env);
        $env = str_replace('APP_NAME=BillingHub', "APP_NAME={$_POST['app_name']}", $env);

        // Generate APP_KEY if needed
        if (!str_contains($env, 'APP_KEY=base64:')) {
            $key = base64_encode(random_bytes(32));
            $env = str_replace('APP_KEY=', "APP_KEY=base64:{$key}", $env);
        }

        return file_put_contents($envPath, $env) !== false;
    }

    private function runLaravelSetup() {
        $commands = [
            'cd ' . escapeshellarg($this->billingDir) . ' && php artisan migrate --force',
            'cd ' . escapeshellarg($this->billingDir) . ' && php artisan db:seed --force',
            'cd ' . escapeshellarg($this->billingDir) . ' && php artisan create:admin-user ' . 
                escapeshellarg($_POST['admin_email']) . ' ' .
                escapeshellarg($_POST['admin_password']) . ' ' .
                escapeshellarg($_POST['admin_name']),
        ];

        foreach ($commands as $command) {
            exec($command, $output, $return);
            if ($return !== 0) {
                return false;
            }
        }

        return true;
    }

    private function showError($message) {
        echo "<html><head><title>Setup Error</title>";
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".error { color: #d32f2f; }";
        echo "h2 { color: #d32f2f; }";
        echo "a { color: #667eea; text-decoration: none; }";
        echo "a:hover { text-decoration: underline; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<h2 class='error'>✗ Setup Failed</h2>";
        echo "<p>{$message}</p>";
        echo "<p><a href='install.php'>← Back to Installation</a></p>";
        echo "</div></body></html>";
        exit;
    }

    private function showSuccess() {
        echo "<html><head><title>Setup Complete</title>";
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 40px 30px; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; }";
        echo ".success { color: #388e3c; }";
        echo "h2 { font-size: 28px; margin-bottom: 20px; }";
        echo ".icon { font-size: 60px; margin-bottom: 20px; }";
        echo ".btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; margin-top: 20px; text-decoration: none; display: inline-block; }";
        echo ".credentials { background: #f5f5f5; padding: 20px; border-radius: 6px; margin: 20px 0; text-align: left; }";
        echo ".credentials p { margin: 8px 0; font-family: monospace; }";
        echo "strong { color: #333; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<div class='icon'>✓</div>";
        echo "<h2 class='success'>Setup Complete!</h2>";
        echo "<p>BillingHub has been successfully installed and configured.</p>";
        
        echo "<div class='credentials'>";
        echo "<p><strong>Admin Email:</strong> {$_POST['admin_email']}</p>";
        echo "<p><strong>Application URL:</strong> {$_POST['app_url']}</p>";
        echo "</div>";

        echo "<p>You can now access your BillingHub installation:</p>";
        echo "<a href=\"{$_POST['app_url']}/admin\" class='btn'>Login to Dashboard</a>";
        echo "</div></body></html>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new SetupHandler();
    $handler->handle();
} else {
    header('Location: install.php');
    exit;
}
