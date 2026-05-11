<section class="container-mywish py-16 lg:py-24 max-w-lg">

    <h1 class="font-display font-extrabold text-3xl md:text-4xl mb-8 tracking-tight">
        Bonjour <?= e($firstName) ?> <span aria-hidden="true">👋</span>
    </h1>

    <div class="card">
        <div class="flex items-center gap-4">
            <?php if (!empty($user['avatar_url'])): ?>
                <img
                    src="<?= e($user['avatar_url']) ?>"
                    alt=""
                    referrerpolicy="no-referrer"
                    class="w-16 h-16 rounded-full object-cover ring-2 ring-bg-higher"
                >
            <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-gradient-primary flex items-center justify-center text-white font-display font-bold text-xl">
                    <?= e(mb_strtoupper(mb_substr($firstName, 0, 1))) ?>
                </div>
            <?php endif; ?>

            <div class="min-w-0">
                <div class="font-display font-bold text-lg text-text-primary truncate">
                    <?= e($user['display_name'] ?? '') ?>
                </div>
                <div class="text-sm text-text-secondary truncate">
                    <?= e($user['email'] ?? '') ?>
                </div>
            </div>
        </div>
    </div>

    <form action="/auth/logout" method="POST" class="mt-8">
        <?= csrf_field() ?>
        <button type="submit" class="btn-secondary inline-flex items-center gap-2">
            <!-- Log-out icon (Lucide) -->
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Se déconnecter
        </button>
    </form>

</section>
