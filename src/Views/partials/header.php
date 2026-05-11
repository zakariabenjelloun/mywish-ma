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

        <!-- Right side: account dropdown OR signup CTA -->
        <?php if (auth_check()):
            $authUser    = auth();
            $authName    = $authUser['display_name'] ?? '';
            $authFirst   = $authName !== ''
                ? explode(' ', trim($authName), 2)[0]
                : (string) strtok((string) ($authUser['email'] ?? ''), '@');
            $authAvatar  = $authUser['avatar_url'] ?? '';
            $authInitial = mb_strtoupper(mb_substr($authFirst, 0, 1));
        ?>
            <div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-haspopup="true"
                    class="flex items-center gap-2 rounded-full pl-1 pr-3 py-1 hover:bg-bg-higher transition-colors"
                >
                    <?php if ($authAvatar !== ''): ?>
                        <img
                            src="<?= e($authAvatar) ?>"
                            alt=""
                            referrerpolicy="no-referrer"
                            class="w-9 h-9 rounded-full object-cover ring-1 ring-bg-higher"
                        >
                    <?php else: ?>
                        <span class="w-9 h-9 rounded-full bg-gradient-primary flex items-center justify-center text-white font-display font-bold text-sm">
                            <?= e($authInitial) ?>
                        </span>
                    <?php endif; ?>

                    <span class="font-display font-semibold text-sm text-text-primary hidden sm:inline">
                        <?= e($authFirst) ?>
                    </span>

                    <!-- Chevron-down icon (Lucide) -->
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-cloak
                    class="absolute right-0 mt-2 w-52 rounded-xl bg-bg-raised border border-bg-higher shadow-lg overflow-hidden"
                    role="menu"
                >
                    <a
                        href="/profile"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-higher transition-colors no-underline"
                        role="menuitem"
                    >
                        <!-- User icon (Lucide) -->
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Mon profil
                    </a>

                    <form action="/auth/logout" method="POST" class="border-t border-bg-higher">
                        <?= csrf_field() ?>
                        <button
                            type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm text-text-primary hover:bg-bg-higher transition-colors text-left"
                            role="menuitem"
                        >
                            <!-- Log-out icon (Lucide) -->
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="/auth/google" class="btn-primary">
                Créer ma page
            </a>
        <?php endif; ?>

    </div>
</nav>
