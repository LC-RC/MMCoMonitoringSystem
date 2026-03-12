<?php
/**
 * Router Class
 * Handles request routing and middleware
 * MM&Co Accounting Review Center Management System
 */

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * Add GET route
     */
    public function get(string $path, array $handler, array $middleware = []): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Add POST route
     */
    public function post(string $path, array $handler, array $middleware = []): self
    {
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Add route to collection
     */
    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $handler[0],
            'action' => $handler[1] ?? 'index',
            'middleware' => $middleware,
        ];
    }

    /**
     * Run router - match and dispatch
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // When app is in a subfolder (e.g. /MMCoMonitoringSystem/), strip that base path so routes match
        $basePath = rtrim((string) dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($basePath !== '' && $basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }

        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Run middleware
                foreach ($route['middleware'] as $m) {
                    $middlewareClass = "App\\Middleware\\{$m}";
                    if (class_exists($middlewareClass)) {
                        $mw = new $middlewareClass();
                        if (!$mw->handle()) {
                            return;
                        }
                    }
                }

                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }

        http_response_code(404);
        require dirname(__DIR__, 2) . '/app/Views/errors/404.php';
    }
}
