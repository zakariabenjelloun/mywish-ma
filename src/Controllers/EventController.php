<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Core\Database;
use MyWish\Core\View;
use MyWish\Models\Event;
use MyWish\Models\Cagnotte;
use MyWish\Models\Rsvp;
use MyWish\Models\Pledge;

/**
 * EventController — wizard (create/store), public page (show),
 * organizer dashboard, and soft-delete (destroy).
 *
 * Routes (registered in public/index.php):
 *   GET  /events/new           → create()     [auth]
 *   POST /events               → store()      [auth, csrf]
 *   GET  /e/{slug}             → show()       [public]
 *   GET  /dashboard            → dashboard()  [auth]
 *   POST /events/{id}/archive  → destroy()    [auth, csrf]
 *
 * Wizard POST shape (Sprint 3 C.3 form will conform):
 *   type             = wedding|anniversary|birth|engagement|other
 *   mariee_name, marie_name                   (wedding/engagement)
 *   birthday_name, birthday_age               (anniversary)
 *   baby_name, parents_name, baby_gender      (birth)
 *   event_name, event_description             (other)
 *   event_date       = YYYY-MM-DD (>= today)
 *   event_time       = HH:MM (optional)
 *   city (required), address (optional)
 *   custom_title (optional override)
 *   tone             = formal|warm|casual
 *   welcome_message  (custom or auto-generated)
 *   cagnotte_type    = travel|furniture|free_gift|other  (optional block)
 *   cagnotte_title (optional), cagnotte_amount (required if type≠other),
 *   cagnotte_description, cagnotte_photo (file)
 *   _csrf
 */
final class EventController
{
    private const EVENT_TYPES    = ['wedding', 'anniversary', 'birth', 'engagement', 'other'];
    private const CAGNOTTE_TYPES = ['travel', 'furniture', 'free_gift', 'other'];
    private const TONES          = ['formal', 'warm', 'casual'];

    public function create(): string
    {
        auth_required();

        // Placeholder — Sprint 3 C.3 will build the real wizard view.
        return View::render('events/wizard', [
            'layout' => 'layouts/default',
            'title'  => 'Créer ma page — MyWish.ma',
        ]);
    }

    public function store(): void
    {
        auth_required();
        csrf_verify();

        $user   = auth();
        $errors = [];

        // ── 1. Type ───────────────────────────────────────────
        $type = trim($_POST['type'] ?? '');
        if (!in_array($type, self::EVENT_TYPES, true)) {
            $errors[] = "Type d'événement invalide.";
        }

        // ── 2. Per-type names → unified $names ────────────────
        $names = [];
        switch ($type) {
            case 'wedding':
            case 'engagement':
                $primary   = trim($_POST['mariee_name'] ?? '');
                $secondary = trim($_POST['marie_name']  ?? '');
                if ($primary === '' || $secondary === '') {
                    $errors[] = 'Les deux noms sont requis.';
                }
                $names = ['primary' => $primary, 'secondary' => $secondary];
                break;
            case 'anniversary':
                $primary = trim($_POST['birthday_name'] ?? '');
                if ($primary === '') $errors[] = 'Le nom de la personne fêtée est requis.';
                $age = ($_POST['birthday_age'] ?? '') !== '' ? (int) $_POST['birthday_age'] : null;
                $names = ['primary' => $primary, 'age' => $age];
                break;
            case 'birth':
                $babyName    = trim($_POST['baby_name']    ?? '');
                $parentsName = trim($_POST['parents_name'] ?? '');
                if ($babyName === '' && $parentsName === '') {
                    $errors[] = 'Au moins le nom du bébé ou des parents est requis.';
                }
                $names = ['primary' => $babyName ?: 'Bébé', 'secondary' => $parentsName ?: null];
                break;
            case 'other':
                $primary = trim($_POST['event_name'] ?? '');
                if ($primary === '') $errors[] = "Le nom de l'événement est requis.";
                $names = ['primary' => $primary];
                break;
        }

        // ── 3. Date / time ────────────────────────────────────
        $eventDate = trim($_POST['event_date'] ?? '');
        $dateObj = $eventDate !== '' ? \DateTime::createFromFormat('Y-m-d', $eventDate) : false;
        if (!$dateObj || $dateObj->format('Y-m-d') !== $eventDate) {
            $errors[] = 'Date invalide (format AAAA-MM-JJ attendu).';
        } elseif ($dateObj < new \DateTime('today')) {
            $errors[] = "La date doit être aujourd'hui ou dans le futur.";
        }
        $eventTime = trim($_POST['event_time'] ?? '');
        if ($eventTime !== '' && !preg_match('/^\d{2}:\d{2}$/', $eventTime)) {
            $errors[] = 'Heure invalide (format HH:MM attendu).';
        }

        // ── 4. Location ───────────────────────────────────────
        $city    = trim($_POST['city']    ?? '');
        $address = trim($_POST['address'] ?? '');
        if ($city === '') $errors[] = 'La ville est requise.';

        // ── 5. Tone + welcome message ─────────────────────────
        $tone = trim($_POST['tone'] ?? '');
        if (!in_array($tone, self::TONES, true)) $errors[] = 'Ton invalide.';
        $welcomeMessage = trim($_POST['welcome_message'] ?? '');
        if ($welcomeMessage === '' && in_array($tone, self::TONES, true) && in_array($type, self::EVENT_TYPES, true)) {
            $welcomeMessage = generateWelcomeMessage($type, $names, $tone);
        }

        // ── 6. Title (custom or auto) ─────────────────────────
        $customTitle = trim($_POST['custom_title'] ?? '');
        $title = $customTitle !== ''
            ? $customTitle
            : (in_array($type, self::EVENT_TYPES, true) ? generateDefaultTitle($type, $names) : '');
        if ($title === '') $errors[] = 'Le titre est requis.';

        // ── 7. Cagnotte (optional, validated if present) ──────
        $cagnotteType = trim($_POST['cagnotte_type'] ?? '');
        $hasCagnotte = $cagnotteType !== '';
        $cagnotteData = null;
        if ($hasCagnotte) {
            if (!in_array($cagnotteType, self::CAGNOTTE_TYPES, true)) {
                $errors[] = 'Type de cagnotte invalide.';
            } else {
                $rawAmount = trim($_POST['cagnotte_amount'] ?? '');
                $amount = $rawAmount !== '' ? (float) $rawAmount : null;
                if ($cagnotteType !== 'other' && ($amount === null || $amount < 100)) {
                    $errors[] = 'Le montant de la cagnotte doit être d\'au moins 100 MAD.';
                }
                $cagnotteData = [
                    'type'          => $cagnotteType,
                    'title'         => trim($_POST['cagnotte_title']       ?? '') ?: cagnotteTypeLabel($cagnotteType),
                    'description'   => trim($_POST['cagnotte_description'] ?? '') ?: null,
                    'target_amount' => $amount,
                ];
            }
        }

        // ── 8. Halt if errors ─────────────────────────────────
        if ($errors) {
            flash('error', implode(' — ', $errors));
            $_SESSION['_old'] = $_POST;
            redirect('/events/new');
        }

        // ── 9. hero_name / hero_secondary mapping ─────────────
        $heroName      = $names['primary'] ?? '';
        $heroSecondary = match ($type) {
            'wedding', 'engagement', 'birth' => $names['secondary'] ?? null,
            default                          => null,
        };

        // ── 10. Transactional create ──────────────────────────
        $db = Database::get();
        $db->beginTransaction();
        try {
            $eventId = Event::create([
                'owner_id'             => (int) $user['id'],
                'type'                 => $type,
                'title'                => $title,
                'hero_name'            => $heroName,
                'hero_secondary'       => $heroSecondary,
                'event_date'           => $eventDate,
                'event_time'           => $eventTime ?: null,
                'location_city'        => $city,
                'location_address'     => $address ?: null,
                'welcome_message'      => $welcomeMessage,
                'welcome_message_tone' => $tone,
            ]);

            if ($hasCagnotte && $cagnotteData) {
                if (!empty($_FILES['cagnotte_photo']) && ($_FILES['cagnotte_photo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $photoUrl = uploadEventPhoto($_FILES['cagnotte_photo'], $eventId);
                    if ($photoUrl !== null) {
                        $cagnotteData['photo_url'] = $photoUrl;
                    }
                }
                $cagnotteData['event_id'] = $eventId;
                Cagnotte::create($cagnotteData);
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollback();
            logger('Event create failed: ' . $e->getMessage(), 'error');
            flash('error', 'Une erreur est survenue. Réessayez.');
            $_SESSION['_old'] = $_POST;
            redirect('/events/new');
        }

        // ── 11. Success ───────────────────────────────────────
        unset($_SESSION['_old']);
        $event = Event::findById($eventId);
        flash('success', 'Votre page est prête !');
        redirect('/e/' . $event['slug']);
    }

    public function show(string $slug): string
    {
        // Public — no auth_required.
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] === 'archived') {
            http_response_code(404);
            return '<h1 style="font-family:system-ui;text-align:center;margin-top:25vh;">404 — Événement introuvable</h1>';
        }

        $cagnotte         = Cagnotte::findByEvent((int) $event['id']);
        $validatedPledges = [];
        $totalValidated   = 0.0;
        $totalPending     = 0.0;
        if ($cagnotte) {
            $allPledges       = Pledge::findByCagnotte((int) $cagnotte['id']);
            $validatedPledges = array_values(array_filter($allPledges, fn($p) => $p['status'] === 'validated'));
            $totalValidated   = Pledge::totalAmount((int) $cagnotte['id'], 'validated');
            $totalPending     = Pledge::totalAmount((int) $cagnotte['id'], 'pending');
        }
        $rsvpCounts = Rsvp::countByResponse((int) $event['id']);

        return View::render('events/show', [
            // No 'layout' — show.php renders as standalone HTML (no MyWish header).
            'title'            => $event['title'],
            'event'            => $event,
            'cagnotte'         => $cagnotte,
            'validatedPledges' => $validatedPledges,
            'rsvpCounts'       => $rsvpCounts,
            'totalValidated'   => $totalValidated,
            'totalPending'     => $totalPending,
        ]);
    }

    public function dashboard(): string
    {
        auth_required();
        $user = auth();
        $events = Event::findByOwner((int) $user['id'], 'active');

        return View::render('events/dashboard', [
            'layout' => 'layouts/default',
            'title'  => 'Mon dashboard — MyWish.ma',
            'events' => $events,
        ]);
    }

    public function destroy(string $id): void
    {
        auth_required();
        csrf_verify();

        $event = Event::findById((int) $id);
        if (!$event) {
            flash('error', 'Événement introuvable.');
            redirect('/dashboard');
        }

        $user = auth();
        if ((int) $event['owner_id'] !== (int) $user['id']) {
            http_response_code(403);
            flash('error', 'Accès refusé.');
            redirect('/dashboard');
        }

        Event::archive((int) $event['id']);
        flash('success', 'Événement archivé.');
        redirect('/dashboard');
    }
}
