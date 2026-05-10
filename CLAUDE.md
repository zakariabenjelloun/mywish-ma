# 🤖 CLAUDE.md — Context for Claude Code

> This file is automatically read by Claude Code at the start of every session.
> It contains the essential context to work on MyWish.ma without asking questions already answered.
>
> **For AI assistants reading this**: respect every decision in this file and `/docs`. Don't reintroduce ideas that have already been rejected.

---

## 🎯 What this project is

**MyWish.ma** = the page of your family event. Invitations, kitty (cagnotte), and souvenirs, all on one WhatsApp link.

**Target market** : Moroccan families + Moroccan diaspora (Europe, North America)

**Pitch in one line** :
> Khotba, anniversaire, mariage… Tout sur un lien.

**Founder context**: not a developer. Will work with Claude Code as primary coding assistant. May hire a freelance later.

---

## 🛠️ Tech stack

```yaml
Backend:
  language: PHP 8.2+
  framework: NONE (vanilla PHP, modern style — classes, namespaces, PSR-4 autoload)
  reasoning: Founder is not dev; simpler to debug and maintain on shared hosting
  router: Custom mini-router (~50 lines, in src/Core/Router.php)
  orm: PDO direct (parametrized queries, no ORM magic)
  templating: Plain PHP views (no Blade, no Twig — keep it simple)

Frontend:
  styling: TailwindCSS — pre-compiled to a single CSS file (no build step on server)
  reactivity: Alpine.js (lightweight, served from CDN)
  icons: Lucide via CDN or inline SVG (NO emojis in functional UI)
  fonts: Plus Jakarta Sans (display) + Inter (body) — Google Fonts CDN

Database:
  engine: MySQL 8.0
  client: PDO with prepared statements
  migrations: Plain .sql files in database/migrations/, numbered, applied manually or via script

External services:
  oauth: Google (via League OAuth2 client or custom)
  whatsapp: WhatsApp Cloud API (custom HTTP via cURL)
  email: PHPMailer with SMTP (Resend or Brevo)
  images: Cloudinary (upload via API)
  payments: Stripe (PHP SDK) + CMI (Moroccan, custom integration) + PayPal SDK

Hosting:
  provider: OVH Maroc (Pro hosting plan ~80 MAD/month)
  panel: cPanel
  deployment: cPanel Git Version Control + .cpanel.yml script
  ssl: Let's Encrypt (free via cPanel)
  cdn: Cloudflare (free + DDoS protection)
```

---

## 🌐 Environment architecture

```
Local (VS Code)
    ↓ git push
GitHub (private repo)
    ├─ branch: dev    →  cPanel Git Version Control  →  /home/USER/dev.mywish.ma/  →  database_dev
    └─ branch: main   →  cPanel Git Version Control  →  /home/USER/public_html/    →  database_prod
```

**NEVER deploy to prod without testing on dev first.**

---

## 🎨 Design System (SOURCE OF TRUTH)

**ALWAYS** see `/docs/DESIGN-SYSTEM.md` for full details.

Quick reference:

```css
/* Brand */
--primary: #EA580C;        /* peach saturated — main CTAs */
--primary-soft: #FB923C;   /* hover */
--primary-deep: #C2410C;   /* pressed */
--gold: #FCD34D;           /* premium accents */

/* Dark surfaces */
--bg-deep: #0A0A0A;        /* main background */
--bg-raised: #18181B;      /* cards */
--bg-high: #27272A;        /* hover, modals */

/* Text */
--text-primary: #FAFAF9;
--text-secondary: #A1A1AA;
--text-muted: #71717A;
```

**Typography**:
- Headings: `Plus Jakarta Sans` (500-800) — letter-spacing tight
- Body: `Inter` (400-700)

**Icons**: **Lucide only** (line, 1.8 stroke-width). NO emojis in functional UI.

---

## 📁 Project structure

```
mywish-php/
├── public/                    ← Web root (= public_html on prod, dev.mywish.ma on dev)
│   ├── index.php              ← SINGLE ENTRY POINT (front controller)
│   ├── .htaccess              ← URL rewriting + security headers
│   └── assets/                ← CSS, JS, images
├── src/                       ← Application code (NEVER directly accessible via web)
│   ├── Config/                ← Configuration loader (reads .env)
│   ├── Core/                  ← Router, Database, View, Session, Csrf
│   ├── Controllers/           ← HTTP handlers
│   ├── Models/                ← Database models (PDO-based)
│   ├── Views/                 ← PHP templates
│   └── Helpers/               ← Utility functions
├── database/
│   ├── migrations/            ← Numbered .sql files (e.g., 001_create_users.sql)
│   └── seeds/                 ← Test data SQL
├── storage/                   ← Hors Git
│   ├── logs/                  ← Error logs
│   ├── cache/                 ← View cache (later)
│   └── uploads/               ← User uploads (proof of payment, etc.)
├── docs/                      ← Documentation (versioned)
├── scripts/                   ← Bash helpers
├── .env                       ← Local config (NEVER commit)
├── .env.example               ← Template (committed)
├── .gitignore
├── .cpanel.yml                ← Deployment script for cPanel
└── CLAUDE.md                  ← This file
```

---

## 🚫 DO NOT

These are non-negotiable rules:

1. **NEVER commit .env** or any file with secrets
2. **NEVER commit storage/uploads/** — user content stays on server
3. **NEVER commit storage/logs/** — logs only on server
4. **NEVER use `mysql_*` functions** (deprecated) — always PDO with prepared statements
5. **NEVER concatenate user input into SQL** — always use placeholders (`:name`, `?`)
6. **NEVER use emojis in functional UI** → Lucide icons (line, 1.8 stroke-width)
   - Decorative emojis OK in titles/badges (e.g., "🇲🇦 Made in Morocco" badge)
7. **NEVER deploy to prod without testing on dev first**
8. **NEVER store DB credentials in code** — always read from `.env`
9. **NEVER trust user input** — sanitize, validate, escape (htmlspecialchars on output)
10. **NEVER use `eval()`, `exec()`, `shell_exec()`** with user input
11. **NEVER expose PHP errors in production** — log them to file
12. **NEVER use light mode at MVP** — dark only
13. **NEVER touch kitty money** — MyWish never holds funds; direct organizer ↔ guest payment
14. **NEVER use SMS** — WhatsApp Cloud API only
15. **NEVER charge commission on kitty** — revenue from Premium 99 MAD + partner subs only

---

## ✅ DO

1. **Mobile-first**: 90% of traffic. Test on 375px viewport first.
2. **Privacy by default**: public reads OK, but all actions require auth + event code.
3. **French UI** at MVP (AR + EN later in V2).
4. **All storage in DB** (MySQL) — no localStorage for user data.
5. **Validate paiement avec preuve** — kitty contributions go through `pending → validated` flow.
6. **Branding "MyWish.ma" partout** (free AND paid pages) for viral acquisition.
7. **Always run on `dev` first**, then merge to `main` for prod.
8. **Use prepared statements** for ALL database queries.
9. **Escape ALL output** with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` or use a `e()` helper.
10. **Generate CSRF tokens** for all forms.

---

## 📋 Current sprint

See `/docs/TODO.md` for what we're working on right now.

Current phase: **Sprint 0 — Project Setup (cPanel + GitHub)**

---

## 🗺️ Where to find things

| What | Where |
|------|-------|
| Full strategy | `/docs/MASTER-PLAN.md` |
| Design tokens & components | `/docs/DESIGN-SYSTEM.md` |
| Decisions log | `/docs/DECISIONS.md` |
| Sprint plan | `/docs/ROADMAP.md` |
| Active todos | `/docs/TODO.md` |
| Visual mockups | `/docs/mockups/` |
| Deployment workflow | `/docs/DEPLOYMENT.md` |
| Database conventions | `/docs/DATABASE.md` |
| App code | `/src/` |
| Web root | `/public/` |

---

## 🎯 Code conventions

```php
<?php
// File naming
// - Classes: PascalCase (EventController.php)
// - Helpers: snake_case files with snake_case functions

// Always declare strict types
declare(strict_types=1);

// Always namespace
namespace MyWish\Controllers;

// Use modern PHP 8 features
class EventController
{
    public function __construct(
        private Database $db,
        private View $view,
    ) {}

    public function show(string $slug): string
    {
        // Always type-hint parameters and return types
        $event = $this->db->fetchOne(
            'SELECT * FROM events WHERE slug = :slug LIMIT 1',
            ['slug' => $slug]
        );

        if (!$event) {
            return $this->view->render('errors/404');
        }

        return $this->view->render('events/show', ['event' => $event]);
    }
}
```

### SQL conventions

```php
// ✅ GOOD — prepared statement with named parameters
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);

// ❌ BAD — concatenation (SQL injection risk)
$pdo->query("SELECT * FROM users WHERE email = '$email'");
```

### Output escaping

```php
// ✅ GOOD — escaped with helper
<h1><?= e($event['title']) ?></h1>

// ❌ BAD — raw output (XSS risk)
<h1><?= $event['title'] ?></h1>
```

---

## 🤝 How to ask Claude Code for help

Good prompts:
- ✅ "Implement the EventController based on `/docs/mockups/03-page-ibrahim-dark.html`"
- ✅ "Create migration 003_create_events.sql based on `/docs/MASTER-PLAN.md` section 4"
- ✅ "Refactor src/Models/User.php to use prepared statements"

Avoid:
- ❌ "Make it pretty" → reference the design system
- ❌ "Add Composer + Laravel" → not allowed at MVP, see DECISIONS.md

---

## 🔍 When in doubt

1. Read `/docs/MASTER-PLAN.md` (full strategic context)
2. Read `/docs/DECISIONS.md` (chronological reasoning)
3. Read `/docs/DEPLOYMENT.md` (cPanel workflow)
4. Read `/docs/DATABASE.md` (DB conventions, migration process)
5. Check `/docs/mockups/` (visual examples)
6. If still unclear → **ask in the chat**, don't guess.

---

## 📅 Last update

`2026-05-09` — Initial bootstrap: PHP/MySQL/cPanel stack with v2 dark design system.

---

*Built with care for the Moroccan family event market 🇲🇦*
