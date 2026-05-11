<footer class="border-t border-bg-higher mt-16 py-12">
    <div class="container-mywish text-center">

        <!-- Logo + tagline -->
        <div class="inline-flex items-center gap-3 mb-3 font-display font-extrabold text-text-secondary">
            <span class="w-7 h-7 rounded-lg bg-gradient-primary flex items-center justify-center text-white text-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </span>
            MyWish.ma
        </div>

        <p class="text-text-muted text-sm mb-2">
            © <?= date('Y') ?> · Conçu au Maroc 🇲🇦
        </p>

        <p class="text-text-disabled text-xs">
            <a href="/cgu" class="hover:text-text-secondary transition-colors mx-2">CGU</a>
            ·
            <a href="/privacy" class="hover:text-text-secondary transition-colors mx-2">Confidentialité</a>
            ·
            <a href="/legal" class="hover:text-text-secondary transition-colors mx-2">Mentions légales</a>
        </p>

    </div>
</footer>