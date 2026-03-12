<?php
/**
 * Entry Point - MM&Co Accounting Review Center Management System
 */

declare(strict_types=1);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('Asia/Manila');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    $savePath = ini_get('session.save_path');
    $valid = $savePath && is_dir($savePath) && is_writable($savePath);
    if (!$valid) {
        $fallback = sys_get_temp_dir();
        if (!$fallback || !is_dir($fallback) || !is_writable($fallback)) {
            $fallback = dirname(__DIR__) . '/storage/sessions';
            if (!is_dir($fallback)) { @mkdir($fallback, 0777, true); }
        }
        ini_set('session.save_path', $fallback);
    }
    session_start();
}

// Helpers (e.g. base_path() for subfolder installs)
require dirname(__DIR__) . '/app/helpers.php';

// Autoload
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load routes
require dirname(__DIR__) . '/routes/web.php';
