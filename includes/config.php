<?php
// Prevent direct access & configure session options
if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    session_start();
}

// Load .env.local (local dev overrides) or .env (production)
$envPath = file_exists(__DIR__ . '/../.env.local')
    ? __DIR__ . '/../.env.local'
    : __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name  = trim($name);
            // Strip inline comments starting with #
            if (strpos($value, '#') !== false) {
                $value = explode('#', $value, 2)[0];
            }
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Site Configuration
define('SITE_NAME', 'HK Garage');
define('SITE_SUBTITLE', 'Officina Meccanica Villa Landri');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8000'); // Adjust for deployment

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'hkgarage');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Mail Configuration (PHPMailer with Aruba SMTP)
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtps.aruba.it');
define('MAIL_PORT', getenv('MAIL_PORT') ?: 465);
define('MAIL_USER', getenv('MAIL_USER') ?: 'hkgarage24@gmail.com');
define('MAIL_PASS', getenv('MAIL_PASS') ?: '');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'hkgarage24@gmail.com');
define('MAIL_FROM_NAME', 'HK Garage');
define('GARAGE_NOTIFICATION_EMAIL', getenv('GARAGE_NOTIFICATION_EMAIL') ?: 'hkgarage24@gmail.com');

// Timezone
date_default_timezone_set('Europe/Rome');

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide raw errors in production, keep clean logs
