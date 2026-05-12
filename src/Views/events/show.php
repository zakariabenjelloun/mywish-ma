<?php
/**
 * views/events/show.php — Public event page (standalone HTML).
 * Accessed via /e/{slug}. No MyWish header by design — the event is the star.
 *
 * Data: $event, $cagnotte (or null), $validatedPledges, $rsvpCounts,
 *       $totalValidated, $totalPending, $title.
 */

// French date formatting (no intl extension dependency)
$jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
$mois  = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$ts    = strtotime($event['event_date']);
$dateFr = $jours[(int) date('w', $ts)] . ' ' . (int) date('j', $ts) . ' ' . $mois[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);

// Countdown target — ISO format for JS Date parsing
$countdownTime   = $event['event_time'] ?: '00:00:00';
$countdownTarget = $event['event_date'] . 'T' . $countdownTime;

// Cagnotte progression %
$progressPercent = 0;
if ($cagnotte && !empty($cagnotte['target_amount']) && (float) $cagnotte['target_amount'] > 0) {
    $progressPercent = min(100, ($totalValidated / (float) $cagnotte['target_amount']) * 100);
}

// Google Maps URL (only if we have at least a city)
$mapsQuery = trim(($event['location_address'] ?? '') . ', ' . ($event['location_city'] ?? ''), ', ');
$mapsUrl   = $mapsQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapsQuery) : null;

// OG description fallback
$ogDescription = $event['welcome_message'] ?: ($event['hero_name'] ?? 'Un événement à célébrer ensemble.');
$ogDescription = mb_substr(strip_tags($ogDescription), 0, 200);

// Flash messages
$flashSuccess = flash('success');
$flashError   = flash('error');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0A0A0A">
    <title><?= e($event['title']) ?> — MyWish.ma</title>
    <meta name="description" content="<?= e($ogDescription) ?>">

    <!-- Open Graph (WhatsApp / FB / Twitter link preview) -->
    <meta property="og:title" content="<?= e($event['title']) ?>">
    <meta property="og:description" content="<?= e($ogDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url('/e/' . $event['slug'])) ?>">
    <?php if (!empty($event['hero_photo_url'])): ?>
    <meta property="og:image" content="<?= e($event['hero_photo_url']) ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js"></script>
</head>
<body class="bg-bg-deep text-text-primary font-body antialiased">

    <!-- Flash toasts (top-right) -->
    <?php if ($flashSuccess): ?>
        <div class="fixed top-4 right-4 z-50 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-300 text-sm max-w-xs shadow-lg">
            <?= e($flashSuccess) ?>
        </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="fixed top-4 right-4 z-50 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm max-w-xs shadow-lg">
            <?= e($flashError) ?>
        </div>
    <?php endif; ?>

    <!-- ═══════ HERO ═══════ -->
    <section class="relative">
        <div class="relative h-[60vh] min-h-[360px] md:h-[70vh] md:min-h-[480px] overflow-hidden">
            <?php if (!empty($event['hero_photo_url'])): ?>
                <img src="<?= e($event['hero_photo_url']) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-bg-deep/40 to-bg-deep"></div>
            <?php else: ?>
                <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary-soft to-gold opacity-90"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-bg-deep/40 to-bg-deep"></div>
            <?php endif; ?>

            <div class="relative h-full flex flex-col items-center justify-center text-center px-5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold mb-6 bg-gold/15 text-gold border border-gold/30">
                    🇲🇦 Conçu au Maroc
                </span>
                <h1 class="font-display font-extrabold text-4xl md:text-6xl lg:text-7xl tracking-tighter mb-3 text-white drop-shadow-[0_4px_24px_rgba(0,0,0,0.6)] max-w-4xl">
                    <?= e($event['title']) ?>
                </h1>
                <?php if (!empty($event['hero_name'])): ?>
                <p class="text-xl md:text-2xl text-white/85 font-display font-semibold drop-shadow-[0_2px_12px_rgba(0,0,0,0.6)]">
                    <?= e($event['hero_name']) ?><?php if (!empty($event['hero_secondary'])): ?> &amp; <?= e($event['hero_secondary']) ?><?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ═══════ COUNTDOWN ═══════ -->
    <section class="container-mywish py-12 -mt-16 relative z-10 max-w-2xl">
        <div class="card">
            <div class="text-center mb-5">
                <div class="text-xs font-semibold uppercase tracking-widest text-text-muted">Plus que</div>
            </div>
            <div id="countdown" data-target="<?= e($countdownTarget) ?>" class="grid grid-cols-4 gap-2 sm:gap-4">
                <div class="text-center">
                    <div class="bg-bg-deep border border-bg-higher rounded-xl py-4 mb-2 font-display font-extrabold text-3xl sm:text-5xl text-primary-soft" data-cd="days">--</div>
                    <div class="text-xs uppercase tracking-wider text-text-muted">Jours</div>
                </div>
                <div class="text-center">
                    <div class="bg-bg-deep border border-bg-higher rounded-xl py-4 mb-2 font-display font-extrabold text-3xl sm:text-5xl text-primary-soft" data-cd="hours">--</div>
                    <div class="text-xs uppercase tracking-wider text-text-muted">Heures</div>
                </div>
                <div class="text-center">
                    <div class="bg-bg-deep border border-bg-higher rounded-xl py-4 mb-2 font-display font-extrabold text-3xl sm:text-5xl text-primary-soft" data-cd="minutes">--</div>
                    <div class="text-xs uppercase tracking-wider text-text-muted">Minutes</div>
                </div>
                <div class="text-center">
                    <div class="bg-bg-deep border border-bg-higher rounded-xl py-4 mb-2 font-display font-extrabold text-3xl sm:text-5xl text-gold" data-cd="seconds">--</div>
                    <div class="text-xs uppercase tracking-wider text-text-muted">Secondes</div>
                </div>
            </div>
            <div id="countdownDone" class="hidden text-center font-display font-bold text-2xl text-gold mt-2">
                🎉 L'événement a eu lieu !
            </div>
        </div>
    </section>

    <!-- ═══════ MESSAGE D'ACCUEIL ═══════ -->
    <?php if (!empty($event['welcome_message'])): ?>
    <section class="container-mywish py-8 max-w-2xl">
        <div class="card text-center">
            <p class="text-lg leading-relaxed text-text-primary whitespace-pre-line">
                <?= e($event['welcome_message']) ?>
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══════ INFOS (date / heure / lieu) ═══════ -->
    <section class="container-mywish py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Date -->
            <div class="card text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-primary/10 text-primary-soft flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="text-xs uppercase tracking-widest text-text-muted mb-1">Date</div>
                <div class="font-display font-bold text-base"><?= e($dateFr) ?></div>
            </div>
            <!-- Heure -->
            <div class="card text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-primary/10 text-primary-soft flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="text-xs uppercase tracking-widest text-text-muted mb-1">Heure</div>
                <div class="font-display font-bold text-base">
                    <?= $event['event_time'] ? e(substr($event['event_time'], 0, 5)) : 'À confirmer' ?>
                </div>
            </div>
            <!-- Lieu -->
            <div class="card text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-primary/10 text-primary-soft flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div class="text-xs uppercase tracking-widest text-text-muted mb-1">Lieu</div>
                <div class="font-display font-bold text-base mb-3">
                    <?= e($event['location_city'] ?? '') ?>
                    <?php if (!empty($event['location_address'])): ?>
                    <br><span class="font-medium text-sm text-text-secondary"><?= e($event['location_address']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($mapsUrl): ?>
                <a href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener" class="btn-secondary text-xs py-2 px-3 inline-flex items-center gap-1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Voir sur Google Maps
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ═══════ CAGNOTTE (conditional) ═══════ -->
    <?php if ($cagnotte): ?>
    <section class="container-mywish py-8 max-w-3xl" id="cagnotte" x-data="{modalOpen: false}">
        <div class="card">
            <?php if (!empty($cagnotte['photo_url'])): ?>
            <img src="<?= e($cagnotte['photo_url']) ?>" alt="" class="w-full max-h-64 object-cover rounded-xl mb-5">
            <?php endif; ?>

            <h2 class="font-display font-extrabold text-2xl md:text-3xl tracking-tighter mb-2">
                <?= e($cagnotte['title']) ?>
            </h2>
            <?php if (!empty($cagnotte['description'])): ?>
            <p class="text-text-secondary mb-6 leading-relaxed whitespace-pre-line"><?= e($cagnotte['description']) ?></p>
            <?php endif; ?>

            <?php if (!empty($cagnotte['target_amount'])): ?>
            <!-- Progress -->
            <div class="mb-2 flex items-baseline justify-between flex-wrap gap-2">
                <div>
                    <span class="font-display font-extrabold text-2xl text-primary-soft"><?= number_format($totalValidated, 0, ',', ' ') ?></span>
                    <span class="text-text-muted"> / <?= number_format((float) $cagnotte['target_amount'], 0, ',', ' ') ?> <?= e($cagnotte['currency']) ?></span>
                </div>
                <span class="badge badge-primary font-bold"><?= round($progressPercent) ?>%</span>
            </div>
            <div class="w-full bg-bg-higher rounded-full h-3 overflow-hidden mb-6">
                <div class="h-full bg-gradient-to-r from-primary-deep via-primary to-gold transition-all duration-500" style="width: <?= $progressPercent ?>%"></div>
            </div>
            <?php endif; ?>

            <!-- Validated participants -->
            <?php if (!empty($validatedPledges)): ?>
            <div class="space-y-2 mb-6">
                <?php foreach ($validatedPledges as $p): ?>
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-bg-deep/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-primary-soft flex items-center justify-center font-bold text-sm text-white">
                            <?= e(mb_strtoupper(mb_substr($p['guest_name'], 0, 1))) ?>
                        </div>
                        <span class="text-sm text-text-primary"><?= e($p['guest_name']) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-display font-bold text-sm"><?= number_format((float) $p['amount'], 0, ',', ' ') ?> <?= e($p['currency']) ?></span>
                        <span class="text-green-400" title="Validé">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- CTA -->
            <div class="text-center">
                <button type="button" @click="modalOpen = true" class="btn-primary px-6 py-3 text-base">
                    💝 Je participe à la cagnotte
                </button>
            </div>
        </div>

        <!-- Pledge Modal -->
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4"
             @click="modalOpen = false"
             @keydown.escape.window="modalOpen = false">
            <div @click.stop class="bg-bg-raised rounded-2xl p-6 max-w-md w-full border border-bg-higher max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="font-display font-bold text-xl">Je participe à la cagnotte</h3>
                    <button type="button" @click="modalOpen = false" class="text-text-secondary hover:text-text-primary p-1" aria-label="Fermer">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form method="POST" action="/e/<?= e($event['slug']) ?>/pledge" x-data="{amount: ''}">
                    <?= csrf_field() ?>
                    <div class="space-y-4">
                        <input type="text" name="guest_name" placeholder="Votre nom" required
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        <input type="email" name="guest_email" placeholder="Email (optionnel)"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        <div class="relative">
                            <input type="number" name="amount" x-model="amount" min="100" step="50" placeholder="Montant (min 100)" required
                                   class="w-full px-4 py-3 pr-16 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-text-muted text-sm font-semibold">MAD</span>
                        </div>
                        <textarea name="message" rows="2" placeholder="Un petit mot (optionnel)"
                                  class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors resize-y"></textarea>

                        <p class="text-xs text-text-secondary p-3 rounded-lg bg-bg-deep/50 border border-bg-higher leading-relaxed">
                            Vous promettez <span class="font-bold text-primary-soft" x-text="amount || '—'"></span> MAD. Après validation par l'organisateur, vous recevrez les instructions de paiement.
                        </p>
                        <button type="submit" class="btn-primary w-full">Valider ma promesse</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══════ RSVP ═══════ -->
    <section class="container-mywish py-8 max-w-2xl" id="rsvp">
        <div class="card">
            <h2 class="font-display font-extrabold text-2xl md:text-3xl tracking-tighter mb-2 text-center">Vous serez là ?</h2>
            <p class="text-text-secondary text-center mb-6 text-sm">Confirmez votre présence pour aider à l'organisation.</p>

            <form method="POST" action="/e/<?= e($event['slug']) ?>/rsvp" x-data="{response: '', guests_count: 1}">
                <?= csrf_field() ?>

                <div class="space-y-4 mb-6">
                    <input type="text" name="guest_name" placeholder="Votre nom" required
                           class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    <input type="email" name="guest_email" placeholder="Email (optionnel)"
                           class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                </div>

                <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-4">
                    <button type="button" @click="response='yes'"
                            class="flex flex-col items-center gap-2 py-4 px-2 rounded-xl border-2 transition-colors cursor-pointer"
                            :class="response === 'yes' ? 'border-green-400 bg-green-500/10 text-green-300' : 'border-bg-higher bg-bg-raised text-text-secondary hover:border-green-400/40'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span class="text-sm font-semibold">Oui je viens</span>
                    </button>
                    <button type="button" @click="response='no'"
                            class="flex flex-col items-center gap-2 py-4 px-2 rounded-xl border-2 transition-colors cursor-pointer"
                            :class="response === 'no' ? 'border-red-400 bg-red-500/10 text-red-300' : 'border-bg-higher bg-bg-raised text-text-secondary hover:border-red-400/40'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span class="text-sm font-semibold">Empêché</span>
                    </button>
                    <button type="button" @click="response='maybe'"
                            class="flex flex-col items-center gap-2 py-4 px-2 rounded-xl border-2 transition-colors cursor-pointer"
                            :class="response === 'maybe' ? 'border-yellow-400 bg-yellow-500/10 text-yellow-300' : 'border-bg-higher bg-bg-raised text-text-secondary hover:border-yellow-400/40'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span class="text-sm font-semibold">Peut-être</span>
                    </button>
                </div>
                <input type="hidden" name="response" :value="response">

                <div x-show="response === 'yes'" x-transition class="mb-4">
                    <label class="block text-sm font-medium text-text-secondary mb-2">Combien de personnes au total ?</label>
                    <input type="number" name="guests_count" x-model="guests_count" min="1" max="50"
                           class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                </div>

                <textarea name="message" rows="2" placeholder="Un mot pour l'organisateur (optionnel)"
                          class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors resize-y mb-4"></textarea>

                <button type="submit" :disabled="!response"
                        class="btn-primary w-full"
                        :class="!response ? 'opacity-40 cursor-not-allowed' : ''">
                    Confirmer ma réponse
                </button>
            </form>
        </div>
    </section>

    <!-- ═══════ FOOTER ═══════ -->
    <footer class="container-mywish py-12 text-center">
        <a href="/" class="inline-flex items-center gap-2 text-sm text-text-muted hover:text-primary-soft transition-colors no-underline mb-3">
            <span>Propulsé par</span>
            <span class="font-display font-bold text-text-secondary">MyWish.ma</span>
            <span>🇲🇦</span>
        </a>
        <div>
            <a href="/events/new" class="text-xs text-text-muted hover:text-primary-soft transition-colors">
                Créer mon propre événement →
            </a>
        </div>
    </footer>

    <!-- Countdown JS (vanilla) -->
    <script>
        (function () {
            const container = document.getElementById('countdown');
            const doneEl    = document.getElementById('countdownDone');
            if (!container) return;

            const targetStr = container.dataset.target;
            const target    = new Date(targetStr).getTime();
            if (isNaN(target)) return;

            const elDays    = container.querySelector('[data-cd="days"]');
            const elHours   = container.querySelector('[data-cd="hours"]');
            const elMinutes = container.querySelector('[data-cd="minutes"]');
            const elSeconds = container.querySelector('[data-cd="seconds"]');

            let timer;
            function tick() {
                const diff = target - Date.now();
                if (diff <= 0) {
                    container.classList.add('hidden');
                    if (doneEl) doneEl.classList.remove('hidden');
                    clearInterval(timer);
                    return;
                }
                const days    = Math.floor(diff / 86400000);
                const hours   = Math.floor((diff / 3600000) % 24);
                const minutes = Math.floor((diff / 60000) % 60);
                const seconds = Math.floor((diff / 1000) % 60);
                elDays.textContent    = String(days).padStart(2, '0');
                elHours.textContent   = String(hours).padStart(2, '0');
                elMinutes.textContent = String(minutes).padStart(2, '0');
                elSeconds.textContent = String(seconds).padStart(2, '0');
            }
            tick();
            timer = setInterval(tick, 1000);
        })();
    </script>

</body>
</html>
