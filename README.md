# 🎁 MyWish.ma

> The page of your family event. Invitations, kitty (cagnotte), and souvenirs, all on one WhatsApp link.

[![Status](https://img.shields.io/badge/status-pre--MVP-orange)](./docs/ROADMAP.md)
[![Stack](https://img.shields.io/badge/stack-PHP%208.2%20%2B%20MySQL-purple)]()
[![Hosting](https://img.shields.io/badge/hosting-cPanel-blue)]()
[![Made in](https://img.shields.io/badge/made%20in-🇲🇦%20Morocco-EA580C)]()

---

## 🎯 Vision

MyWish.ma is the digital home for Moroccan family events — anniversaires, mariages, baby showers, naissances. One WhatsApp link replaces 5 different tools.

**Target market**: Moroccan families + diaspora (5M+ people)

---

## 🛠️ Tech stack

```yaml
Backend:    PHP 8.2+ (vanilla, no framework — simple to maintain)
Database:   MySQL 8.0
Frontend:   TailwindCSS (precompiled) + Alpine.js (CDN)
Icons:      Lucide (line, no emojis in functional UI)
Hosting:    OVH Maroc (cPanel) — ~80 MAD/month
Deployment: cPanel Git Version Control (auto-deploy on push)
```

---

## 📦 Project structure

```
mywish-php/
├── 🤖 CLAUDE.md             # AI assistant context (READ FIRST)
├── 📖 README.md             # This file
├── 🚀 SETUP-GUIDE.md        # How to get started
├── 🚫 .gitignore            # Files excluded from Git
├── 🔐 .env.example          # Env vars template (.env is gitignored)
├── 📋 .cpanel.yml           # cPanel deployment script
│
├── 📘 docs/                 # Living documentation
│   ├── MASTER-PLAN.md       # Full product strategy
│   ├── DESIGN-SYSTEM.md     # Design tokens & components (dark theme)
│   ├── DECISIONS.md         # Decision log (ADRs)
│   ├── ROADMAP.md           # Sprints & milestones
│   ├── TODO.md              # Current sprint tasks
│   ├── DEPLOYMENT.md        # cPanel workflow
│   ├── DATABASE.md          # DB conventions
│   └── mockups/             # Visual mockups (HTML)
│
├── 🌐 public/               # Web root (= public_html in production)
│   ├── index.php            # Single entry point (front controller)
│   ├── .htaccess            # URL rewriting + security
│   └── assets/              # CSS, JS, images
│
├── 📂 src/                  # App code (NEVER directly accessible via web)
│   ├── Config/Env.php       # .env loader
│   ├── Core/                # Database, Router, View
│   ├── Controllers/
│   ├── Models/
│   ├── Views/               # PHP templates
│   └── Helpers/             # Global functions (e, csrf_field, etc.)
│
├── 💾 database/
│   ├── migrations/          # Versioned SQL files
│   └── seeds/               # Test data
│
├── 📝 storage/              # Hors Git (logs, uploads, cache)
└── 🛠️ scripts/              # Helper bash scripts
```

---

## 🚀 Quick start

### Prerequisites

- ✅ PHP 8.2+ installed locally (or use the built-in dev server)
- ✅ MySQL or MariaDB installed locally
- ✅ Git
- ✅ A GitHub account
- ✅ A cPanel hosting account (OVH Maroc, LWS, Genious, etc.)
- ✅ VS Code (recommended)
- ✅ Claude Code CLI (optional but recommended): `npm install -g @anthropic-ai/claude-code`

### Local setup (5 minutes)

```bash
# 1. Clone the repo
git clone https://github.com/<your-username>/mywish-ma.git
cd mywish-ma

# 2. Run setup
chmod +x scripts/setup-local.sh
./scripts/setup-local.sh

# 3. Edit .env with your local DB credentials
nano .env

# 4. Create the local database
mysql -u root -p -e "CREATE DATABASE mywish_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# 5. Run migrations
mysql -u root -p mywish_local < database/migrations/000_create_migrations_table.sql
mysql -u root -p mywish_local < database/migrations/001_create_users.sql
mysql -u root -p mywish_local < database/migrations/002_create_events.sql

# 6. Start the dev server
php -S localhost:8000 -t public/

# 7. Visit http://localhost:8000
```

For complete setup instructions including cPanel deployment, see [`SETUP-GUIDE.md`](./SETUP-GUIDE.md).

---

## 🌍 Environments

| Env | Branch | URL | Database |
|-----|--------|-----|----------|
| **Local** | any | `http://localhost:8000` | `mywish_local` |
| **Dev** | `dev` | `https://dev.mywish.ma` | `xxx_mywish_dev` |
| **Prod** | `main` | `https://mywish.ma` | `xxx_mywish_prod` |

---

## 🔄 Workflow

```
1. Code in VS Code (with Claude Code help)
2. Test locally: php -S localhost:8000 -t public/
3. Commit + push to dev branch
4. cPanel: Update from Remote → Deploy HEAD Commit
5. Test on dev.mywish.ma
6. If ok → merge dev to main
7. cPanel prod: Update from Remote → Deploy HEAD Commit
8. Test on mywish.ma 🎉
```

Full workflow details: [`docs/DEPLOYMENT.md`](./docs/DEPLOYMENT.md)

---

## 🎨 Design System

This project follows a **"Calm festivity" dark premium** design language:
- 🍑 Peach saturated (`#EA580C`) — primary
- ✨ Gold (`#FCD34D`) — premium accents
- 🌑 Dark surfaces (`#0A0A0A` to `#27272A`)
- 🎭 Lucide icons (NO emojis in functional UI)
- 🔤 Plus Jakarta Sans + Inter typography

See [`docs/DESIGN-SYSTEM.md`](./docs/DESIGN-SYSTEM.md) for full reference.

Visual mockups in [`docs/mockups/`](./docs/mockups/) — open them in your browser.

---

## 💰 Business model

| Tier | Price | Limits |
|------|-------|--------|
| 🆓 Free | 0 MAD | 15 guests max, MyWish branding visible |
| 💎 Premium | 99 MAD/event | Unlimited guests, custom slug |

**Additional revenue**: Partner directory subscriptions (Bronze 99 / Silver 299 / Gold 599 MAD/month).

**MyWish never touches kitty money** — direct payments between organizer and guests.

---

## 🔐 Security essentials

- ✅ ALL queries use **prepared statements** (never concatenate user input into SQL)
- ✅ ALL output is **escaped** with `e()` helper or `htmlspecialchars()`
- ✅ ALL forms include **CSRF tokens** (`csrf_field()`)
- ✅ `.env` is **gitignored** (never commit secrets)
- ✅ `src/`, `database/`, `storage/` are **blocked** by `.htaccess`
- ✅ HTTPS is **forced** via cPanel (Let's Encrypt)

---

## 🤖 Working with Claude Code

This repo is optimized for [Claude Code](https://docs.claude.com/en/docs/claude-code/overview).

```bash
cd mywish-ma
claude

# Claude reads CLAUDE.md automatically and has full context.
# Ask things like:
# "Implement the EventController based on docs/mockups/03-page-ibrahim-dark.html"
# "Create migration 003 for the cagnottes table from MASTER-PLAN.md section 4"
# "Refactor src/Models/User.php to use prepared statements"
```

---

## 🗺️ Roadmap

- ✅ **Pre-MVP** (current): Strategy, design, mockups, bootstrap
- 🚧 **MVP** (~12-15 weeks): Auth, event creation, RSVP, kitty, Premium
- 📅 **V1** (Months 4-6): Marketplace, more templates, QR codes
- 🌍 **V2** (Months 7-12): PWA, multi-langue, B2B
- 📱 **V3** (Months 13+): Native iOS/Android apps

Full roadmap: [`docs/ROADMAP.md`](./docs/ROADMAP.md)

---

## 🎯 Current focus

**Sprint 0 — Project Setup (cPanel + GitHub)**

See [`docs/TODO.md`](./docs/TODO.md) for active tasks.

---

## 📞 Contact

- 🌍 Website: [mywish.ma](https://mywish.ma) (soon)
- 💼 Founder: [Your name]
- 📧 Email: [your@email.com]

---

## 📜 License

This project is proprietary. All rights reserved © 2026 MyWish.ma.

---

*Built with care for Moroccan families and the diaspora 🇲🇦💛*
