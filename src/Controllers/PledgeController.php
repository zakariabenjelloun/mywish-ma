<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Models\Event;
use MyWish\Models\Cagnotte;
use MyWish\Models\Pledge;

/**
 * PledgeController — guest "promise to contribute" from the public event page.
 *
 * Public endpoint: NO auth, NO CSRF (per Sprint 3 spec).
 *
 * Sprint 3 scope: just create the pledge with status='pending'.
 * Sprint 4 will handle:
 *   - Proof upload (screenshot of the bank transfer)
 *   - Organizer validation (pending → validated/rejected)
 *
 * Routes:
 *   POST /e/{slug}/pledge → store()
 *
 * POST shape:
 *   guest_name   (required)
 *   guest_email  (optional, validated if present)
 *   guest_phone  (optional)
 *   amount       (required, min 100 MAD)
 *   message      (optional)
 */
final class PledgeController
{
    private const MIN_AMOUNT_MAD = 100.0;

    public function store(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] === 'archived') {
            http_response_code(404);
            flash('error', 'Événement introuvable.');
            redirect('/');
        }

        $cagnotte = Cagnotte::findByEvent((int) $event['id']);
        if (!$cagnotte) {
            flash('error', "Cet événement n'a pas de cagnotte active.");
            redirect('/e/' . $slug);
        }

        $errors = [];

        $guestName = trim($_POST['guest_name'] ?? '');
        if ($guestName === '') {
            $errors[] = 'Votre nom est requis.';
        }

        $rawAmount = trim($_POST['amount'] ?? '');
        $amount    = $rawAmount !== '' ? (float) $rawAmount : null;
        if ($amount === null || $amount < self::MIN_AMOUNT_MAD) {
            $errors[] = 'Le montant minimum est de 100 MAD.';
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
            redirect('/e/' . $slug . '#cagnotte');
        }

        Pledge::create([
            'cagnotte_id' => (int) $cagnotte['id'],
            'guest_name'  => $guestName,
            'guest_email' => $guestEmail ?: null,
            'guest_phone' => $guestPhone ?: null,
            'amount'      => $amount,
            'currency'    => $cagnotte['currency'] ?? 'MAD',
            'message'     => $message ?: null,
        ]);

        unset($_SESSION['_old']);
        flash('success', 'Promesse enregistrée ! Envoyez la preuve plus tard.');
        redirect('/e/' . $slug . '#cagnotte');
    }
}
