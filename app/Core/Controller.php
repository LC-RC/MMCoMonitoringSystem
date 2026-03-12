<?php
/**
 * Base Controller
 * Provides common controller functionality
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

abstract class Controller
{
    protected array $config;
    protected ?object $user = null;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/app.php';
        $this->user = Auth::user();
    }

    /**
     * Render view with data
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $user = $this->user;
        $config = $this->config;
        $theme = ThemeHelper::getTheme($this->user);
        $viewPath = dirname(__DIR__, 2) . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new \RuntimeException("View not found: {$view}");
        }
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        header('Location: ' . url($url), true, $statusCode);
        exit;
    }

    /**
     * Get JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Require CSRF validation or abort
     */
    protected function requireCsrf(): void
    {
        if (!$this->validateCsrf()) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        }
    }
}
