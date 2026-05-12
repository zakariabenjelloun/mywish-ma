<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Models\Event;
use MyWish\Models\Rsvp;

/**
 * RsvpController — guest replies from the public event page.
 *
 * Public endpoint: NO auth, NO CSRF (per Sprint 3 spec — reCAPTCHA will land in Sprint 5).
 *
 * Routes:
 *   POST /e/{slug}/rsvp → store()
 *
 * POST shape:
 *   guest_name   (required)
 *   guest_email  (optional, validated if present)
 *   guest_phone  (optional, no format validation — Moroccan/international variability)
 *   response     = yes|no|maybe (required)
 *   guests_count (optional, default 1, capped at 50)
 *   message      (optional)
 */
final class RsvpController
{
    private const RESPONSES = ['yes', 'no', 'maybe'];

    public function store(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] === 'archived') {
            http_response_code(404);
            flash('error', 'Événement introuvable.');
            redirect('/');
        }

        $errors = [];

        $guestName = trim($_POST['guest_name'] ?? '');
        if ($guestName === '') {
            $errors[] = 'Votre nom est requis.';
        }

        $response = trim($_POST['response'] ?? '');
        if (!in_array($response, self::RESPONSES, true)) {
            $errors[] = 'Réponse invalide.';
        }

        $guestsCount = isset($_POST['guests_count']) ? (int) $_POST['guests_count'] : 1;
        if ($guestsCount < 1 || $guestsCount > 50) {
            $errors[] = "Nombre d'invités invalide (1 à 50).";
        }

        $guestEmail = trim($_POST['guest_email'] ?? '');
        if ($guestEmail !== '' && !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        $guestPhone = trim($_POST['guest_phone'] ?? '');
        $message    = trim($_POST['message']     ?? '');

        if ($errors) {
            flash('error', implode(' — ', $errors));
            $_SESSION['_old'] = $_POST;
            redirect('/e/' . $slug . '#rsvp');
        }

        Rsvp::create([
            'event_id'     => (int) $event['id'],
            'guest_name'   => $guestName,
            'guest_email'  => $guestEmail ?: null,
            'guest_phone'  => $guestPhone ?: null,
            'response'     => $response,
            'guests_count' => $guestsCount,
            'message'      => $message ?: null,
        ]);

        unset($_SESSION['_old']);
        flash('success', 'Merci pour votre réponse !');
        redirect('/e/' . $slug . '#rsvp');
    }
}
