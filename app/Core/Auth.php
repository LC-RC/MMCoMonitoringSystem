<?php
/**
 * Authentication Helper
 * Session-based auth with bcrypt
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

class Auth
{
    /**
     * Get current authenticated user or null
     */
    public static function user(): ?object
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Check if user has admin role
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && $user->role === 'admin';
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        $user = self::user();
        return $user && $user->role === $role;
    }

    /**
     * Require authentication - redirect if not logged in
     */
    public static function requireAuth(): bool
    {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . url('/login'));
            exit;
        }
        return true;
    }

    /**
     * Require admin role
     */
    public static function requireAdmin(): bool
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            header('HTTP/1.1 403 Forbidden');
            require dirname(__DIR__, 2) . '/app/Views/errors/403.php';
            exit;
        }
        return true;
    }

    /**
     * Attempt login (returns bool for backward compatibility)
     */
    public static function attempt(string $email, string $password): bool
    {
        $result = self::attemptWithReason($email, $password);
        return $result['success'];
    }

    /**
     * Attempt login and return success plus reason on failure.
     * Returns: ['success' => true, 'user' => $user] or ['success' => false, 'reason' => 'email_not_found'|'wrong_password']
     */
    public static function attemptWithReason(string $email, string $password): array
    {
        $user = (new \App\Models\User)->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'reason' => 'email_not_found'];
        }
        if (!password_verify($password, $user->password)) {
            return ['success' => false, 'reason' => 'wrong_password'];
        }

        session_regenerate_id(true);
        unset($user->password);
        $_SESSION['user'] = $user;
        return ['success' => true, 'user' => $user];
    }

    /**
     * Logout
     */
    public static function logout(): void
    {
        session_destroy();
        header('Location: ' . url('/login'));
        exit;
    }

    /**
     * Generate CSRF token
     */
    public static function csrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
