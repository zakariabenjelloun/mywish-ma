<nav class="sticky top-0 z-50 backdrop-blur-xl bg-bg-deep/70 border-b border-bg-higher">
    <div class="container-mywish flex items-center justify-between py-4">

        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 font-display font-extrabold text-lg tracking-tight text-text-primary no-underline">
            <span class="w-10 h-10 rounded-xl bg-gradient-primary flex items-center justify-center text-white shadow-glow-primary">
                <!-- Gift icon (Lucide) -->
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </span>
            MyWish.ma
        </a>

        <!-- Right side: login OR dashboard link -->
        <?php if (auth_check()): ?>
            <a href="/dashboard" class="btn-ghost">
                Mon dashboard
            </a>
        <?php else: ?>
            <a href="/auth/google" class="btn-primary">
                Créer ma page
            </a>
        <?php endif; ?>

    </div>
</nav>