<?php
declare(strict_types=1);

/**
 * MyWish.ma — Front Controller
 *
 * SINGLE entry point for all HTTP requests.
 * Handles bootstrapping, routing, and dispatching.
 */

// ─────────────────────────────────────────────────
// 1. BOOTSTRAP
// ─────────────────────────────────────────────────

define('PUBLIC_PATH', __DIR__);
// On cPanel deployment, src/ is copied INSIDE the same dir as index.php
// So BASE_PATH = PUBLIC_PATH (not parent dir)
define('BASE_PATH',   __DIR__);
define('SRC_PATH',    BASE_PATH . '/src');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('DATABASE_PATH', BASE_PATH . '/database');

// PSR-4 style autoloader for the src/ namespace
spl_autoload_register(function (string $class): void {
    $prefix = 'MyWish\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = SRC_PATH . '/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helpers
require_once SRC_PATH . '/Helpers/functions.php';
require_once SRC_PATH . '/Helpers/event_types.php';
require_once SRC_PATH . '/Helpers/upload.php';

// ─────────────────────────────────────────────────
// 2. LOAD ENVIRONMENT
// ─────────────────────────────────────────────────

use MyWish\Config\Env;

// .env can be in BASE_PATH or one level up (more secure on shared hosting)
$envFile = file_exists(BASE_PATH . '/.env')
    ? BASE_PATH . '/.env'
    : dirname(BASE_PATH) . '/.env';

Env::load($envFile);

// ─────────────────────────────────────────────────
// 3. ERROR HANDLING
// ─────────────────────────────────────────────────

if (Env::get('APP_DEBUG', 'false') === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/php-' . date('Y-m-d') . '.log');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// Set timezone
date_default_timezone_set(Env::get('APP_TIMEZONE', 'Africa/Casablanca'));

// ─────────────────────────────────────────────────
// 4. SESSION
// ─────────────────────────────────────────────────

session_name(Env::get('SESSION_NAME', 'mywish_session'));
session_set_cookie_params([
    'lifetime' => (int) Env::get('SESSION_LIFETIME', '2592000'),
    'path'     => '/',
    'domain'   => '',
    'secure'   => Env::get('SESSION_SECURE', 'false') === 'true',
    'httponly' => Env::get('SESSION_HTTPONLY', 'true') === 'true',
    'samesite' => Env::get('SESSION_SAMESITE', 'Lax'),
]);
session_start();

// ─────────────────────────────────────────────────
// 5. ROUTING
// ─────────────────────────────────────────────────

use MyWish\Core\Router;

$router = new Router();

// ── Public routes
$router->get('/', 'HomeController@index');
$router->get('/e/{slug}', 'EventController@show');

// ── Auth routes
$router->get('/auth/google', 'AuthController@redirectToGoogle');
$router->get('/auth/google/callback', 'AuthController@handleGoogleCallback');
$router->post('/auth/logout', 'AuthController@logout');

// ── Organizer routes (require auth)
$router->get('/profile', 'ProfileController@index');
$router->get('/dashboard', 'EventController@dashboard');
$router->get('/events/new', 'EventController@create');
$router->post('/events', 'EventController@store');
$router->post('/events/{id}/archive', 'EventController@destroy');

// ── Public event interaction (RSVP + pledges)
$router->post('/e/{slug}/rsvp',   'RsvpController@store');
$router->post('/e/{slug}/pledge', 'PledgeController@store');

// ─────────────────────────────────────────────────
// 6. DISPATCH
// ─────────────────────────────────────────────────

try {
    $router->dispatch();
} catch (\Throwable $e) {
    // Log the error
    error_log(sprintf(
        '[%s] %s in %s:%d',
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    // Show error page
    if (Env::get('APP_DEBUG', 'false') === 'true') {
        echo '<pre style="background:#0A0A0A;color:#FAFAF9;padding:20px;font-family:monospace;">';
        echo "ERROR: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n\n";
        echo "File: " . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . ":" . $e->getLine() . "\n\n";
        echo "Trace:\n" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
        echo '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Oops, une erreur est survenue.</h1>';
        echo '<p>L\'équipe a été notifiée. Réessayez dans quelques minutes.</p>';
    }
}
