<?php
/**
 * views/events/dashboard.php — Organizer's event list.
 *
 * Wrapped by layouts/default (MyWish header + avatar dropdown).
 * Data: $events (array of event rows for current user, status='active'), $title.
 */

$flashSuccess = flash('success');
$flashError   = flash('error');

// Short French date for cards
$moisShort = ['janv', 'févr', 'mars', 'avril', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'];
?>

<section class="container-mywish py-10">

    <?php if ($flashSuccess): ?>
    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
        <?= e($flashSuccess) ?>
    </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
        <?= e($flashError) ?>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-display font-extrabold text-3xl md:text-4xl tracking-tight mb-2">Mes événements</h1>
            <p class="text-text-secondary text-sm">Gérez vos pages d'événements actives.</p>
        </div>
        <a href="/events/new" class="btn-primary flex-shrink-0">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Créer un nouvel événement
        </a>
    </div>

    <?php if (empty($events)): ?>

        <!-- ═══════ Empty state ═══════ -->
        <div class="card text-center py-12 max-w-xl mx-auto">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary/10 text-primary-soft flex items-center justify-center">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </div>
            <h2 class="font-display font-bold text-xl mb-2">Vous n'avez pas encore créé d'événement.</h2>
            <p class="text-text-secondary text-sm mb-6">Lancez-vous : 4 étapes, 2 minutes, une page partageable.</p>
            <a href="/events/new" class="btn-primary">Créer mon premier événement</a>
        </div>

    <?php else: ?>

        <!-- ═══════ Events grid ═══════ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($events as $event):
                $ts          = strtotime($event['event_date']);
                $shortDate   = (int) date('j', $ts) . ' ' . $moisShort[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
                $shareText   = 'Venez célébrer avec moi : ' . url('/e/' . $event['slug']);
                $whatsappUrl = 'https://wa.me/?text=' . urlencode($shareText);
            ?>
            <div class="card flex flex-col hover:border-primary/50 transition-colors">
                <!-- Photo / fallback -->
                <?php if (!empty($event['hero_photo_url'])): ?>
                    <img src="<?= e($event['hero_photo_url']) ?>" alt="" class="w-full h-40 object-cover rounded-xl mb-4">
                <?php else: ?>
                    <div class="w-full h-40 rounded-xl mb-4 bg-gradient-to-br from-primary via-primary-soft to-gold opacity-80 flex items-center justify-center">
                        <span class="font-display font-bold text-4xl text-white drop-shadow-lg">
                            <?= e(mb_strtoupper(mb_substr($event['title'], 0, 1))) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Type badge + date -->
                <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                    <span class="badge badge-primary"><?= e(eventTypeLabel($event['type'])) ?></span>
                    <span class="text-xs text-text-muted"><?= e($shortDate) ?></span>
                </div>

                <!-- Title -->
                <h3 class="font-display font-bold text-lg mb-2 leading-tight line-clamp-2">
                    <?= e($event['title']) ?>
                </h3>

                <!-- Slug -->
                <div class="text-xs text-text-muted font-mono mb-4 truncate" title="<?= e($event['slug']) ?>">
                    /e/<?= e($event['slug']) ?>
                </div>

                <!-- Actions (mt-auto pushes to bottom of card) -->
                <div class="mt-auto space-y-2">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="/e/<?= e($event['slug']) ?>" target="_blank" rel="noopener"
                           class="btn-secondary text-xs py-2 inline-flex items-center justify-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Voir
                        </a>
                        <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener"
                           class="text-xs py-2 inline-flex items-center justify-center gap-1.5 rounded-xl font-semibold transition-colors"
                           style="background:#25D366; color:white;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                            </svg>
                            Partager
                        </a>
                    </div>
                    <form method="POST" action="/events/<?= (int) $event['id'] ?>/archive"
                          onsubmit="return confirm('Archiver cet événement ? Il deviendra invisible aux invités.')">
                        <?= csrf_field() ?>
                        <button type="submit"
                                class="w-full text-xs py-2 rounded-xl border border-red-400/30 text-red-400 hover:bg-red-500/10 transition-colors font-semibold">
                            🗑️ Archiver
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</section>
