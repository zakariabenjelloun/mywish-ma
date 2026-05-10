<?php
declare(strict_types=1);

namespace MyWish\Core;

/**
 * Router — Mini router with named parameters
 *
 * Supports: GET, POST, PUT, DELETE
 * Pattern syntax: /event/{slug} → captures "slug" parameter
 *
 * Usage in public/index.php:
 *   $router = new Router();
 *   $router->get('/', 'HomeController@index');
 *   $router->get('/event/{slug}', 'EventController@show');
 *   $router->post('/api/rsvp', 'RsvpController@store');
 *   $router->dispatch();
 */
final class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:string}>> */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'DELETE' => [],
    ];

    public function get(string $pattern, string $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, string $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, string $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, string $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    private function add(string $method, string $pattern, string $handler): void
    {
        $this->routes[$method][] = ['pattern' => $pattern, 'handler' => $handler];
    }

    /**
     * Match the current request to a route and dispatch.
     */
    public function dispatch(): void
    {
        // Support method spoofing via _method form field (PUT, DELETE)
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'DELETE'], true)) {
                $method = $spoofed;
            }
        }

        // Get the path (without query string)
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Try to match each route for this method
        foreach ($this->routes[$method] ?? [] as $route) {
            $params = $this->matchPattern($route['pattern'], $uri);
            if ($params !== null) {
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // No match → 404
        $this->notFound();
    }

    /**
     * Try to match a pattern (e.g., /event/{slug}) against a URI.
     * Returns the captured params, or null if no match.
     */
    private function matchPattern(string $pattern, string $uri): ?array
    {
        // Convert {name} to (?<name>[^/]+)
        $regex = preg_replace('/\{([a-z_]+)\}/i', '(?<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        // Filter out numeric keys (only keep named params)
        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    /**
     * Call the handler (e.g., "EventController@show").
     */
    private function callHandler(string $handler, array $params): void
    {
        [$controller, $action] = explode('@', $handler);
        $class = "MyWish\\Controllers\\{$controller}";

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: {$class}");
        }

        $instance = new $class();

        if (!method_exists($instance, $action)) {
            throw new \RuntimeException("Action not found: {$class}::{$action}()");
        }

        // Call the action with params spread as args
        $output = $instance->$action(...array_values($params));

        if (is_string($output)) {
            echo $output;
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">';
        echo '<title>404 — Page introuvable</title>';
        echo '<style>body{background:#0A0A0A;color:#FAFAF9;font-family:system-ui;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center;}</style>';
        echo '</head><body><div>';
        echo '<h1 style="font-size:64px;color:#EA580C;margin:0;">404</h1>';
        echo '<p style="color:#A1A1AA;">Page introuvable</p>';
        echo '<a href="/" style="color:#FB923C;">← Retour à l\'accueil</a>';
        echo '</div></body></html>';
    }
}
