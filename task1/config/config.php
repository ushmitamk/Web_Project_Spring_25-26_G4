<?php
// XAMPP default database settings. Change these only if your MySQL username/password differs.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ecommerce_store');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// For local lab use, show PHP errors while testing. Set to false before public hosting.
define('APP_DEBUG', true);

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Dynamic base URL, e.g. /p4_ecommerce_store/public when running from XAMPP htdocs.
if (!defined('BASE_URL')) {
    $scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
    $scriptDir = rtrim($scriptDir, '/');
    define('BASE_URL', ($scriptDir === '/' ? '' : $scriptDir));
}
