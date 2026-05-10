<nav style="background:rgba(10,10,10,.7);backdrop-filter:blur(20px);border-bottom:1px solid #2D2D30;padding:16px 20px;position:sticky;top:0;z-index:100;">
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;">
        <a href="/" style="display:flex;align-items:center;gap:12px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;font-size:19px;color:#FAFAF9;text-decoration:none;letter-spacing:-.02em;">
            <span style="width:38px;height:38px;background:linear-gradient(135deg,#EA580C,#FB923C);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 0 32px rgba(234,88,12,.3);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
            </span>
            MyWish.ma
        </a>

        <?php if (auth_check()): ?>
            <a href="/dashboard" style="color:#A1A1AA;text-decoration:none;font-size:14px;font-weight:500;">Mon dashboard</a>
        <?php else: ?>
            <a href="/auth/google" style="background:#EA580C;color:white;padding:12px 20px;border-radius:12px;font-weight:600;font-size:14px;text-decoration:none;box-shadow:0 0 32px rgba(234,88,12,.3);">Créer ma page</a>
        <?php endif; ?>
    </div>
</nav>
