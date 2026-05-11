<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Config\Env;
use MyWish\Models\User;

/**
 * AuthController — Google OAuth 2.0 (Authorization Code flow).
 *
 * Uses native cURL — no Composer / no SDK.
 *
 * Routes:
 *   GET  /auth/google           → redirectToGoogle()
 *   GET  /auth/google/callback  → handleGoogleCallback()
 *   POST /auth/logout           → logout()
 */
final class AuthController
{
    private const GOOGLE_AUTH_URL     = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const GOOGLE_TOKEN_URL    = 'https://oauth2.googleapis.com/token';
    private const GOOGLE_USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    /**
     * Step 1: Build the Google authorize URL and redirect the user there.
     */
    public function redirectToGoogle(): void
    {
        $clientId    = Env::get('GOOGLE_CLIENT_ID');
        $redirectUri = Env::get('GOOGLE_REDIRECT_URI');

        if (!$clientId || !$redirectUri) {
            throw new \RuntimeException(
                'Google OAuth not configured. Check GOOGLE_CLIENT_ID and GOOGLE_REDIRECT_URI in .env'
            );
        }

        // CSRF protection for the OAuth flow itself.
        $state = bin2hex(random_bytes(32));
        $_SESSION['_oauth_state'] = $state;

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
            'access_type'   => 'online',
        ];

        redirect(self::GOOGLE_AUTH_URL . '?' . http_build_query($params));
    }

    /**
     * Step 2: Google redirects back with ?code=...&state=...
     * Exchange the code for tokens, fetch the user profile, upsert in DB, log in.
     */
    public function handleGoogleCallback(): void
    {
        // Did the user deny consent?
        if (isset($_GET['error'])) {
            flash('error', 'Connexion annulée.');
            redirect('/');
        }

        // Verify state token (CSRF).
        $sentState = $_GET['state'] ?? '';
        $sessionState = $_SESSION['_oauth_state'] ?? '';
        unset($_SESSION['_oauth_state']);

        if ($sentState === '' || !hash_equals($sessionState, $sentState)) {
            flash('error', 'Session de connexion invalide. Réessayez.');
            redirect('/');
        }

        $code = $_GET['code'] ?? '';
        if ($code === '') {
            flash('error', 'Code d\'autorisation manquant.');
            redirect('/');
        }

        // Exchange code for access token.
        $token = $this->exchangeCodeForToken($code);
        if (!isset($token['access_token'])) {
            logger('Google token exchange failed: ' . json_encode($token), 'error');
            flash('error', 'Échec de l\'authentification Google.');
            redirect('/');
        }

        // Fetch user profile.
        $profile = $this->fetchUserInfo($token['access_token']);
        if (!isset($profile['sub'], $profile['email'])) {
            logger('Google userinfo missing required fields: ' . json_encode($profile), 'error');
            flash('error', 'Profil Google incomplet.');
            redirect('/');
        }

        // Only accept verified emails.
        if (isset($profile['email_verified']) && $profile['email_verified'] !== true) {
            flash('error', 'Adresse email Google non vérifiée.');
            redirect('/');
        }

        // Upsert the user.
        $user = User::createOrUpdateFromGoogle($profile);

        // Banned users cannot sign in.
        if (!empty($user['banned'])) {
            flash('error', 'Ce compte est suspendu.');
            redirect('/');
        }

        // Log the user in. Store only what we need at runtime.
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'           => (int) $user['id'],
            'email'        => $user['email'],
            'display_name' => $user['display_name'],
            'avatar_url'   => $user['avatar_url'],
        ];

        // Honor intended URL if the user was redirected here by auth_required().
        $intended = $_SESSION['_intended_url'] ?? null;
        unset($_SESSION['_intended_url']);

        redirect($intended ?: '/profile');
    }

    /**
     * Log the user out. POST + CSRF-protected.
     */
    public function logout(): void
    {
        csrf_verify();

        $_SESSION = [];

        // Wipe the session cookie too.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        redirect('/');
    }

    /**
     * POST to Google's token endpoint to exchange an authorization code.
     *
     * @return array Decoded JSON response (access_token, id_token, expires_in, ...).
     */
    private function exchangeCodeForToken(string $code): array
    {
        $payload = [
            'code'          => $code,
            'client_id'     => Env::get('GOOGLE_CLIENT_ID'),
            'client_secret' => Env::get('GOOGLE_CLIENT_SECRET'),
            'redirect_uri'  => Env::get('GOOGLE_REDIRECT_URI'),
            'grant_type'    => 'authorization_code',
        ];

        $ch = curl_init(self::GOOGLE_TOKEN_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Google token request failed: ' . $error);
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * GET Google userinfo with the Bearer access token.
     *
     * @return array Decoded JSON: sub, email, email_verified, name, picture, ...
     */
    private function fetchUserInfo(string $accessToken): array
    {
        $ch = curl_init(self::GOOGLE_USERINFO_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Google userinfo request failed: ' . $error);
        }

        return json_decode($response, true) ?? [];
    }
}
