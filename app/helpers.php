<?php
/**
 * Global helpers - available in all views.
 * base_path() is required for the app to work when run from a subfolder (e.g. /MMCoMonitoringSystem/).
 */

if (!function_exists('base_path')) {
    function base_path(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = rtrim((string) dirname($scriptName), '/');
        if ($dir === '' || $dir === '.') {
            return '';
        }
        return $dir;
    }
}

if (!function_exists('url')) {
    /** Build full path for redirects/links when app is in a subfolder */
    function url(string $path = ''): string
    {
        $base = base_path();
        if ($path === '' || $path === '/') {
            return $base ?: '/';
        }
        if (strpos($path, 'http') === 0) {
            return $path;
        }
        return $base . $path;
    }
}
