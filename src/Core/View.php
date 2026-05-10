<?php
declare(strict_types=1);

namespace MyWish\Core;

/**
 * View — Simple PHP template renderer
 *
 * Templates are plain .php files in src/Views/.
 * Variables are extracted into the local scope.
 *
 * SECURITY: ALWAYS use `e()` helper to escape user data in templates.
 *
 * Usage in controllers:
 *   return View::render('events/show', ['event' => $event]);
 *
 * Layout support:
 *   In your view, set $layout = 'layouts/default' to wrap the view in a layout.
 */
final class View
{
    private static string $viewsPath = '';

    public static function setPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/');
    }

    /**
     * Render a view template and return the HTML.
     *
     * @param string $template e.g., "events/show"
     * @param array  $data     associative array of variables to expose
     */
    public static function render(string $template, array $data = []): string
    {
        if (self::$viewsPath === '') {
            self::$viewsPath = SRC_PATH . '/Views';
        }

        $file = self::$viewsPath . '/' . $template . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: {$file}");
        }

        // Render the inner view
        $content = self::renderFile($file, $data);

        // Check if a layout was set
        if (isset($data['layout'])) {
            $layoutFile = self::$viewsPath . '/' . $data['layout'] . '.php';
            if (file_exists($layoutFile)) {
                $data['content'] = $content;
                return self::renderFile($layoutFile, $data);
            }
        }

        return $content;
    }

    /**
     * Render a partial within another view (no layout wrapping).
     *
     * Usage in template:
     *   <?= View::partial('partials/header', ['title' => 'Home']) ?>
     */
    public static function partial(string $template, array $data = []): string
    {
        if (self::$viewsPath === '') {
            self::$viewsPath = SRC_PATH . '/Views';
        }

        $file = self::$viewsPath . '/' . $template . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Partial not found: {$file}");
        }

        return self::renderFile($file, $data);
    }

    /**
     * Render a single file with extracted variables.
     */
    private static function renderFile(string $file, array $data): string
    {
        // Extract data into local scope (overwrite reserved names safely)
        extract($data, EXTR_SKIP);

        ob_start();
        include $file;
        return ob_get_clean();
    }
}
