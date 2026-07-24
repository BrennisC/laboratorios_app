<?php
// Deliberately insecure configuration for OWASP lab usage only.
ini_set('display_errors', '1');
error_reporting(E_ALL);

define('APP_NAME', 'VulnShop');
define('APP_ENV', 'development');
define('DEBUG_MODE', true);
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'vulnshop_db');
define('DB_USER', 'vulnshop_user');
define('DB_PASS', 'vulnshop_pass123');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('APP_SECRET', 'hardcoded-secret-key-123');

session_name('VULNSHOPSESSID');
session_start();

function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

function current_user_role()
{
    return $_SESSION['role'] ?? 'guest';
}

function is_logged_in()
{
    return current_user_id() !== null;
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}
