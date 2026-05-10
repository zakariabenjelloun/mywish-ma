# ✅ MyWish.ma — TODO (Current Sprint)

> Active sprint tasks. Update this file as you progress.
>
> **For full sprint plan**: see `ROADMAP.md`
> **For decision history**: see `DECISIONS.md`

---

## 🎯 Current sprint: Sprint 0 — Project Setup (cPanel + GitHub)

**Goal**: Repo on GitHub + cPanel deployments working + dev environment ready.
**Duration**: ~1-2 weeks
**Started**: 2026-05-09

---

## 📦 Phase A: GitHub setup

- [x] Bootstrap created (this!)
- [ ] Extract bootstrap ZIP locally
- [ ] Create private GitHub repo `mywish-ma`
- [ ] Push to `main`:
  ```bash
  git init
  git add .
  git commit -m "chore: initial bootstrap"
  gh repo create mywish-ma --private --source=. --remote=origin
  git push -u origin main
  ```
- [ ] Create `dev` branch:
  ```bash
  git checkout -b dev
  git push -u origin dev
  ```
- [ ] Set up branch protection on `main` (require PR for merges)

---

## 🌐 Phase B: Domain & DNS

- [ ] Buy `mywish.ma` from Moroccan registrar (~150 MAD/year)
- [ ] Sign up at Cloudflare (free tier)
- [ ] Add `mywish.ma` to Cloudflare
- [ ] Update nameservers at registrar
- [ ] Wait for DNS propagation (~10 minutes)

---

## 🛠️ Phase C: cPanel hosting

- [ ] Sign up for OVH Maroc Pro (or LWS Maroc / Genious) — ~80 MAD/month
- [ ] Get cPanel access (URL + username + password)
- [ ] Verify PHP 8.2+ available (cPanel → Select PHP Version)
- [ ] Verify MySQL 8.0+ available
- [ ] Create subdomain `dev.mywish.ma` in cPanel
  - Document root: `/home/USERNAME/dev.mywish.ma/`
- [ ] Setup SSH access:
  - Generate SSH key in cPanel
  - Test: `ssh USERNAME@yourhost.com`

---

## 💾 Phase D: Databases

- [ ] Create **dev** database in cPanel:
  - Name: `mywish_dev` (cPanel prefixes: `USERNAME_mywish_dev`)
  - Create DB user with strong password
  - Grant ALL PRIVILEGES
- [ ] Create **prod** database in cPanel:
  - Name: `mywish_prod`
  - Create DB user with **DIFFERENT** strong password
  - Grant ALL PRIVILEGES
- [ ] Save credentials securely (1Password / Bitwarden / written safe)
- [ ] Test connection via phpMyAdmin

---

## 🔐 Phase E: Environment files

- [ ] SSH into server: `ssh USERNAME@yourhost.com`
- [ ] Create `.env` for **dev**:
  ```bash
  cd /home/USERNAME/
  nano dev.mywish.ma.env
  # Paste content from .env.example, fill DEV values
  ```
- [ ] Create `.env` for **prod**:
  ```bash
  nano public_html.env
  # Paste content from .env.example, fill PROD values
  # IMPORTANT: APP_DEBUG=false in prod!
  ```
- [ ] Verify `.env` location matches `index.php` lookup logic

---

## 🚀 Phase F: cPanel Git Version Control

### F.1 — SSH Deploy Key for GitHub (private repo)

- [ ] On cPanel server: `ssh-keygen -t ed25519 -f ~/.ssh/mywish_deploy -N ""`
- [ ] Copy public key: `cat ~/.ssh/mywish_deploy.pub`
- [ ] Add as GitHub Deploy Key (repo → Settings → Deploy Keys)
- [ ] Test: `ssh -T git@github.com`
- [ ] Add to `~/.ssh/config`:
  ```
  Host github.com
      HostName github.com
      User git
      IdentityFile ~/.ssh/mywish_deploy
  ```

### F.2 — Create dev repo

- [ ] cPanel → Git Version Control → Create
  - Clone URL: `git@github.com:USERNAME/mywish-ma.git`
  - Repository Path: `/home/USERNAME/repositories/mywish-dev/`
  - Repository Name: `mywish-dev`
- [ ] Switch to `dev` branch via SSH:
  ```bash
  cd ~/repositories/mywish-dev && git checkout dev
  ```
- [ ] In cPanel UI → Manage → Pull or Deploy:
  - Set DEPLOYPATH: `/home/USERNAME/dev.mywish.ma`
  - Click "Update from Remote"
  - Click "Deploy HEAD Commit"

### F.3 — Create prod repo

- [ ] cPanel → Git Version Control → Create
  - Clone URL: `git@github.com:USERNAME/mywish-ma.git`
  - Repository Path: `/home/USERNAME/repositories/mywish-prod/`
  - Repository Name: `mywish-prod`
- [ ] Branch: `main` (default)
- [ ] In cPanel UI → Manage → Pull or Deploy:
  - Set DEPLOYPATH: `/home/USERNAME/public_html`
  - Click "Update from Remote"
  - Click "Deploy HEAD Commit"

---

## 🗄️ Phase G: Apply migrations

### Dev database

- [ ] cPanel → phpMyAdmin → select `USERNAME_mywish_dev`
- [ ] SQL tab → paste content of `database/migrations/000_create_migrations_table.sql` → Go
- [ ] Repeat for `001_create_users.sql`
- [ ] Repeat for `002_create_events.sql`
- [ ] Verify: `SELECT * FROM migrations;` → 3 rows

### Prod database

- [ ] Same process for `USERNAME_mywish_prod`

---

## 🔒 Phase H: SSL + final touches

- [ ] cPanel → SSL/TLS → Manage SSL Sites → Issue Let's Encrypt for `mywish.ma`
- [ ] Same for `dev.mywish.ma`
- [ ] Wait ~5 minutes for certificates
- [ ] Uncomment HTTPS redirect in `public/.htaccess`:
  ```apache
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
  ```
- [ ] Commit + push to dev → deploy → test
- [ ] Commit + push to main → deploy → test

---

## ✅ Definition of Done for Sprint 0

When ALL of these are true:

- ✅ Code is on GitHub (`main` and `dev` branches)
- ✅ `https://mywish.ma` resolves and shows the placeholder home
- ✅ `https://dev.mywish.ma` resolves and shows the placeholder home
- ✅ Both DBs have migrations applied
- ✅ `.env` files exist on both environments with correct creds
- ✅ HTTPS works on both
- ✅ Push to `dev` branch → cPanel deploy → see changes on `dev.mywish.ma`
- ✅ Push to `main` branch → cPanel deploy → see changes on `mywish.ma`
- ✅ Storage logs writable (test by creating an error)
- ✅ Claude Code reads `CLAUDE.md` and gives contextual answers

→ Then move to **Sprint 1 — Tailwind compilation + base layout**.

---

## 🚧 Blockers / questions

(Note any blockers here as you encounter them)

- [ ] _None yet_

---

## 📝 Notes from sprint

### 2026-05-09
- Bootstrap created from chat brainstorm sessions.
- All design decisions documented in `DECISIONS.md`.
- Mockups produced and stored in `docs/mockups/`.
- Switched from initial Next.js plan to PHP/MySQL/cPanel.

---

*Last updated: 2026-05-09*

> 💡 **Tip**: When this sprint is done, run `./scripts/new-sprint.sh 1 "Tailwind base layout"` to archive and start the next.
