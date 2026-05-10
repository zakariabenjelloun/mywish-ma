<?php
declare(strict_types=1);

/**
 * Global helper functions
 *
 * Loaded once at the start of every request via index.php
 */

if (!function_exists('e')) {
    /**
     * Escape HTML for safe output. ALWAYS use this for user data in templates.
     *
     * Usage: <?= e($user['name']) ?>
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate (or retrieve) the CSRF token for the current session.
     */
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Output a hidden CSRF input field for forms.
     *
     * Usage in template:
     *   <form method="POST">
     *     <?= csrf_field() ?>
     *     ...
     *   </form>
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Verify the CSRF token from a POST request.
     * Throws if invalid.
     */
    function csrf_verify(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
            http_response_code(419);
            throw new \RuntimeException('CSRF token mismatch — please refresh and try again.');
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL and stop execution.
     */
    function redirect(string $url, int $status = 302): never
    {
        header("Location: {$url}", true, $status);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to the previous page.
     */
    function back(): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($url);
    }
}

if (!function_exists('old')) {
    /**
     * Get an old form input value (after a validation error).
     *
     * Usage in template:
     *   <input value="<?= e(old('email')) ?>">
     */
    function old(string $key, ?string $default = null): ?string
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Set or get a flash message (one-time message between requests).
     *
     * Set: flash('success', 'Event created!');
     * Get: $msg = flash('success');
     */
    function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return $value;
        }

        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('auth')) {
    /**
     * Get the currently authenticated user (or null).
     */
    function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('auth_check')) {
    /**
     * Check if a user is authenticated.
     */
    function auth_check(): bool
    {
        return !empty($_SESSION['user']);
    }
}

if (!function_exists('auth_required')) {
    /**
     * Redirect to login if not authenticated.
     */
    function auth_required(string $redirectTo = '/auth/google'): void
    {
        if (!auth_check()) {
            $_SESSION['_intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            redirect($redirectTo);
        }
    }
}

if (!function_exists('config')) {
    /**
     * Quick alias for Env::get()
     */
    function config(string $key, ?string $default = null): ?string
    {
        return \MyWish\Config\Env::get($key, $default);
    }
}

if (!function_exists('logger')) {
    /**
     * Write a line to the app log.
     */
    function logger(string $message, string $level = 'info'): void
    {
        $logFile = STORAGE_PATH . '/logs/app-' . date('Y-m-d') . '.log';
        $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for an asset in public/assets/
     *
     * Usage: <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
     */
    function asset(string $path): string
    {
        $base = rtrim(\MyWish\Config\Env::get('APP_URL', ''), '/');
        return $base . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Generate a full URL.
     */
    function url(string $path = ''): string
    {
        $base = rtrim(\MyWish\Config\Env::get('APP_URL', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('view')) {
    /**
     * Quick alias for View::render()
     */
    function view(string $template, array $data = []): string
    {
        return \MyWish\Core\View::render($template, $data);
    }
}

if (!function_exists('db')) {
    /**
     * Quick alias for Database::get()
     */
    function db(): \MyWish\Core\Database
    {
        return \MyWish\Core\Database::get();
    }
}

if (!function_exists('json_response')) {
    /**
     * Send a JSON response and stop execution.
     */
    function json_response(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('slugify')) {
    /**
     * Convert a string to a URL-safe slug.
     */
    function slugify(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
        return trim($str, '-');
    }
}
