<?php
declare(strict_types=1);

namespace MyWish\Config;

/**
 * Env — Simple .env file loader (no Composer dependency)
 *
 * Reads KEY=VALUE pairs from .env and exposes them via Env::get().
 * Supports comments (#) and quoted values.
 */
final class Env
{
    private static array $vars = [];
    private static bool $loaded = false;

    /**
     * Load variables from a .env file.
     */
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($path) || !is_readable($path)) {
            // In production, .env MUST exist. Throw to fail fast.
            if (($_SERVER['APP_ENV'] ?? 'local') === 'prod') {
                throw new \RuntimeException(".env file not found at: {$path}");
            }
            // In local/dev, log a warning and continue with empty vars.
            error_log("Warning: .env file not found at {$path}");
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            // Parse KEY=VALUE
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Strip surrounding quotes if present
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$vars[$key] = $value;

            // Also set in $_ENV and getenv() for compatibility
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }

        self::$loaded = true;
    }

    /**
     * Get an env variable. Falls back to default if not set.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return self::$vars[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Get an env variable as a boolean.
     */
    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Get an env variable as an integer.
     */
    public static function int(string $key, int $default = 0): int
    {
        $value = self::get($key);
        return $value !== null ? (int) $value : $default;
    }

    /**
     * Check if a variable is defined.
     */
    public static function has(string $key): bool
    {
        return isset(self::$vars[$key]);
    }

    /**
     * Get all loaded variables (for debugging — DO NOT log this in production).
     */
    public static function all(): array
    {
        return self::$vars;
    }
}
