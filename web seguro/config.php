<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

define('APP_NAME', 'SecureShop');
define('APP_ENV', 'local');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'secureshop_db');
define('DB_USER', 'secureshop_user');
define('DB_PASS', 'secureshop_pass123');

session_name('SECURESHOPSESSID');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
