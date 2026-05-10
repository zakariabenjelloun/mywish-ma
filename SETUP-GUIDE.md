# 🚀 MyWish.ma — Setup Guide (PHP/MySQL/cPanel)

> Step-by-step instructions to set up the project from scratch.
>
> **Estimated time**: 1-2 hours for full setup (local + GitHub + cPanel + first deploy)

---

## 📋 Prerequisites

Before starting, make sure you have:

- ✅ **PHP 8.2+** locally ([download](https://www.php.net/downloads.php) or use XAMPP/MAMP/Laragon)
- ✅ **MySQL 8.0+** locally
- ✅ **Git** ([download](https://git-scm.com/))
- ✅ **GitHub account** ([signup](https://github.com/signup))
- ✅ **cPanel hosting** with SSH access (OVH Maroc Pro, LWS Maroc, Genious...)
- ✅ **Domain** `mywish.ma` (~150 MAD/year)
- ✅ **VS Code** ([download](https://code.visualstudio.com/))
- ✅ **Claude Code CLI** (recommended): `npm install -g @anthropic-ai/claude-code`

### Recommended VS Code extensions

- **PHP Intelephense**
- **PHP Debug**
- **Tailwind CSS IntelliSense**
- **GitLens**
- **DotENV**
- **MySQL** (cweijan.vscode-mysql-client2)

---

## 🎯 Phase 1: Local development setup (15 min)

### 1.1 — Get the bootstrap on GitHub

```bash
# 1. Unzip the bootstrap into a new folder
unzip mywish-php-bootstrap.zip -d mywish-ma
cd mywish-ma

# 2. Initialize Git
git init
git add .
git commit -m "chore: initial bootstrap"

# 3. Create GitHub repo (private!)
# Either via gh CLI:
gh repo create mywish-ma --private --source=. --remote=origin
git push -u origin main

# Or via github.com → New repository → push manually
```

### 1.2 — Create the dev branch

```bash
git checkout -b dev
git push -u origin dev
```

You now have **two branches**:
- `main` → for production
- `dev` → for testing

### 1.3 — Set up local environment

```bash
# Run the local setup script
chmod +x scripts/setup-local.sh
./scripts/setup-local.sh
```

This will:
- ✅ Verify PHP version
- ✅ Verify MySQL is accessible
- ✅ Create `.env` from `.env.example`
- ✅ Generate APP_KEY
- ✅ Create `storage/` directories
- ✅ Display next steps

### 1.4 — Edit your local `.env`

```bash
nano .env
# (or open in VS Code)
```

Set at minimum:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=mywish_local
DB_USER=root
DB_PASS=        # your local MySQL password
```

### 1.5 — Create the local database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE mywish_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 1.6 — Run migrations locally

```bash
mysql -u root -p mywish_local < database/migrations/000_create_migrations_table.sql
mysql -u root -p mywish_local < database/migrations/001_create_users.sql
mysql -u root -p mywish_local < database/migrations/002_create_events.sql
```

### 1.7 — Start the local server

```bash
php -S localhost:8000 -t public/
```

Visit `http://localhost:8000` → you should see the MyWish.ma landing placeholder. 🎉

---

## 🎯 Phase 2: cPanel hosting setup (30 min)

### 2.1 — Domain configuration

1. Buy `mywish.ma` from [trustname.ma](https://trustname.ma) or similar (~150 MAD/year)
2. In cPanel → **Zone Editor** (or via Cloudflare if you use it):
   - `A` record `mywish.ma` → server IP
   - `A` record `dev.mywish.ma` → server IP

### 2.2 — Create the dev subdomain

In cPanel → **Subdomains**:

- Subdomain: `dev`
- Domain: `mywish.ma`
- Document root: `/home/USERNAME/dev.mywish.ma/`

This creates the folder `dev.mywish.ma/` in your home directory.

### 2.3 — Create the two MySQL databases

In cPanel → **MySQL Databases**:

#### Dev database
- Name: `mywish_dev` (cPanel will prefix it: `USERNAME_mywish_dev`)
- Create user: `mywish_dev_user` with strong password
- Add user to database with **ALL PRIVILEGES**

#### Prod database
- Name: `mywish_prod` (becomes `USERNAME_mywish_prod`)
- Create user: `mywish_prod_user` with **DIFFERENT strong password**
- Add user to database with **ALL PRIVILEGES**

⚠️ **Save these credentials securely** — you'll need them for `.env` files.

### 2.4 — Set up SSH access (highly recommended)

In cPanel → **SSH Access**:

1. Generate an SSH key (or upload your existing public key)
2. Authorize the key
3. Note the SSH connection info: `ssh USERNAME@yourhost.com -p PORT`

Test from your terminal:
```bash
ssh USERNAME@yourhost.com -p 22
```

### 2.5 — Create `.env` on the server (critical!)

The `.env` is **NEVER in Git** — you must create it manually on the server.

#### For DEV environment

```bash
# SSH into the server
ssh USERNAME@yourhost.com

# Navigate to one level above the dev document root
cd /home/USERNAME/

# Create the .env file
nano dev.mywish.ma.env
```

Paste this content (adjust values):

```env
APP_ENV=dev
APP_DEBUG=true
APP_URL=https://dev.mywish.ma
APP_NAME="MyWish.ma (DEV)"
APP_TIMEZONE=Africa/Casablanca
APP_KEY=GENERATE_VIA_PHP_RAND   # see below

DB_HOST=localhost
DB_PORT=3306
DB_NAME=USERNAME_mywish_dev
DB_USER=USERNAME_mywish_dev_user
DB_PASS=YourDevPassword123!
DB_CHARSET=utf8mb4

# ... fill in other vars ...
```

Generate `APP_KEY`:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

⚠️ **Place the `.env` carefully**:
- **Option A (more secure)**: place at `/home/USERNAME/dev.mywish.ma.env` (one level above web root). The PHP code will look for it there.
- **Option B (simpler)**: place at `/home/USERNAME/dev.mywish.ma/.env`. The `.htaccess` blocks web access to `.env` files, so it's safe.

#### For PROD environment

Same process, but file at `/home/USERNAME/public_html.env` or `/home/USERNAME/public_html/.env`:

```env
APP_ENV=prod
APP_DEBUG=false   # ← MUST BE FALSE IN PROD
APP_URL=https://mywish.ma
APP_NAME=MyWish.ma
APP_TIMEZONE=Africa/Casablanca
APP_KEY=DIFFERENT_KEY_FROM_DEV

DB_HOST=localhost
DB_NAME=USERNAME_mywish_prod
DB_USER=USERNAME_mywish_prod_user
DB_PASS=YourProdPassword456!

# ... fill in other vars ...
```

### 2.6 — Apply migrations on dev database

In cPanel → **phpMyAdmin** → select `USERNAME_mywish_dev`:

1. Click "**SQL**" tab
2. Open `database/migrations/000_create_migrations_table.sql`, copy content, paste, "Go"
3. Repeat for `001_create_users.sql` and `002_create_events.sql`
4. Verify with: `SELECT * FROM migrations;` → should show 3 rows

Repeat for the **prod database** when you're ready to deploy to prod.

---

## 🎯 Phase 3: cPanel Git Version Control (20 min)

### 3.1 — Connect cPanel Git to GitHub

If your repo is **private** (recommended), generate a deploy key:

```bash
# On the cPanel server (via SSH)
ssh-keygen -t ed25519 -f ~/.ssh/mywish_deploy -N ""
cat ~/.ssh/mywish_deploy.pub
```

Copy the public key, then in **GitHub → repo → Settings → Deploy Keys → Add deploy key**:
- Title: `cPanel OVH`
- Key: paste the public key
- Allow write access: **NO** (read-only is enough for deploy)

Then on the server, add to `~/.ssh/config`:
```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/mywish_deploy
```

Test:
```bash
ssh -T git@github.com
# Should say: Hi <username>! You've successfully authenticated...
```

### 3.2 — Create the DEV repository in cPanel

In cPanel → **Git Version Control**:

1. Click "**Create**"
2. **Clone URL**: `git@github.com:<your-username>/mywish-ma.git`
3. **Repository Path**: `/home/USERNAME/repositories/mywish-dev/`
4. **Repository Name**: `mywish-dev`
5. Click "**Create**"

After clone, on the repo's **Manage** page:

6. Switch to the `dev` branch:
   - In SSH: `cd ~/repositories/mywish-dev && git checkout dev`
7. **Pull or Deploy** tab:
   - Set **DEPLOYPATH** = `/home/USERNAME/dev.mywish.ma`
   - Click "**Update from Remote**"
   - Click "**Deploy HEAD Commit**"

→ The `.cpanel.yml` script copies the right files into `dev.mywish.ma/`.

### 3.3 — Create the PROD repository in cPanel

Same process:
1. Clone same GitHub URL
2. **Repository Path**: `/home/USERNAME/repositories/mywish-prod/`
3. **Repository Name**: `mywish-prod`
4. Branch: `main` (default, no switch needed)
5. **Pull or Deploy**:
   - Set **DEPLOYPATH** = `/home/USERNAME/public_html`
   - "Update from Remote" → "Deploy HEAD Commit"

### 3.4 — Test the deployments

Visit:
- `https://dev.mywish.ma` → should show the placeholder page
- `https://mywish.ma` → should show the placeholder page

If you see a blank page or error:
- Check `storage/logs/` for PHP errors
- Verify `.env` exists and has correct DB credentials
- Verify migrations are applied
- See the "Troubleshooting" section in `docs/DEPLOYMENT.md`

---

## 🎯 Phase 4: SSL + final touches (15 min)

### 4.1 — Install SSL certificates

In cPanel → **SSL/TLS** → **Manage SSL Sites**:

For both `mywish.ma` and `dev.mywish.ma`:
- Use **Let's Encrypt** (free, automatic) via cPanel's **AutoSSL** feature
- Wait ~5 minutes for certificates to install

Verify:
- `https://mywish.ma` → padlock icon visible
- `https://dev.mywish.ma` → padlock icon visible

### 4.2 — Force HTTPS

In `public/.htaccess`, uncomment these lines:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Commit + push + deploy.

### 4.3 — Set up Cloudflare (optional but recommended)

1. Sign up at [cloudflare.com](https://cloudflare.com)
2. Add `mywish.ma`
3. Update nameservers at your registrar
4. Enable:
   - Always use HTTPS: ON
   - Auto Minify CSS/JS: ON
   - Brotli compression: ON
   - DDoS protection: ON (default)

---

## ✅ Definition of Done

When ALL of these are true, Sprint 0 is complete:

- ✅ Code is on GitHub (`main` and `dev` branches)
- ✅ `mywish.ma` resolves and shows the placeholder
- ✅ `dev.mywish.ma` resolves and shows the placeholder
- ✅ Both DBs are created and have migrations applied
- ✅ `.env` files exist on both environments (with correct creds)
- ✅ HTTPS works on both
- ✅ cPanel auto-deploy works (push to `dev` → see changes on `dev.mywish.ma`)
- ✅ Claude Code reads `CLAUDE.md` and gives contextual answers

→ Then move to **Sprint 1 — Authentication**.

---

## 🤖 Working with Claude Code

```bash
cd mywish-ma
claude

# Claude reads CLAUDE.md automatically.
# Then ask:
> "Implement the AuthController for Google OAuth based on the spec in MASTER-PLAN.md section 5"
> "Create migration 003 for the cagnottes table"
> "Build the home page based on docs/mockups/02-landing-dark.html"
```

---

## 🆘 Troubleshooting

### "Database connection failed"

- ✅ Check `.env` is in the right place
- ✅ Check DB credentials match cPanel MySQL
- ✅ Check DB user is added to DB with privileges

### "Blank page on dev.mywish.ma"

- ✅ Check `storage/logs/php-YYYY-MM-DD.log` for PHP errors
- ✅ Check `APP_DEBUG=true` in dev `.env` (temporarily)
- ✅ Check `.htaccess` is being read (test with `<h1>hello</h1>` in `index.php`)

### "404 on every page except homepage"

- ✅ Check `mod_rewrite` is enabled (cPanel → PHP Selector)
- ✅ Verify `.htaccess` is in `public/` and was deployed

### "cPanel deploy doesn't update files"

- ✅ Click "**Update from Remote**" FIRST
- ✅ Then "**Deploy HEAD Commit**"
- ✅ Check `.cpanel.yml` syntax (YAML is whitespace-sensitive)
- ✅ Check `Last Deployment` log for errors

### "Permission denied" errors

```bash
# SSH into server, then:
chmod -R 755 /home/USERNAME/dev.mywish.ma
chmod -R 775 /home/USERNAME/dev.mywish.ma/storage
```

---

## 🎉 You're ready!

Welcome to MyWish.ma. Let's build something Moroccan families will love. 🇲🇦💛

If you get stuck:
- 📖 Read the docs in `docs/`
- 🤖 Ask Claude Code in your terminal
- 💬 Ask Claude in the web chat for strategy / brainstorming
- 🐛 Open a GitHub Issue
