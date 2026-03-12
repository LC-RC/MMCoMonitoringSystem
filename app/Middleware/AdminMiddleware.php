<?php
/**
 * Admin Middleware
 * Requires admin role
 * MM&Co Accounting Review Center Management System
 */

namespace App\Middleware;

use App\Core\Auth;

class AdminMiddleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            header('Location: ' . url('/login'));
            exit;
        }
        if (!Auth::isAdmin()) {
            header('HTTP/1.1 403 Forbidden');
            require dirname(__DIR__, 2) . '/app/Views/errors/403.php';
            exit;
        }
        return true;
    }
}
