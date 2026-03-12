<?php
/**
 * Authentication Middleware
 * Protects routes - requires login
 * MM&Co Accounting Review Center Management System
 */

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . url('/login'));
            exit;
        }
        return true;
    }
}
