# 🚀 MyWish.ma — Deployment Workflow

> The complete dev → prod workflow with cPanel Git Version Control.
>
> **CRITICAL RULE**: Never deploy to production without testing on `dev` first.

---

## 🌍 Environments overview

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│  💻 LOCAL                                                    │
│  - URL:  http://localhost:8000                              │
│  - DB:   mywish_local                                       │
│  - Branch: any (your working branch)                        │
│  - Used for: development, debugging                         │
│                                                              │
│        ↓ git push origin dev                                 │
│                                                              │
│  🧪 DEV (cPanel)                                            │
│  - URL:  https://dev.mywish.ma                              │
│  - DB:   USERNAME_mywish_dev                                │
│  - Branch: dev                                              │
│  - Path: /home/USERNAME/dev.mywish.ma/                      │
│  - Used for: testing before prod                            │
│                                                              │
│        ↓ git checkout main && git merge dev && git push      │
│                                                              │
│  🚀 PROD (cPanel)                                           │
│  - URL:  https://mywish.ma                                  │
│  - DB:   USERNAME_mywish_prod                               │
│  - Branch: main                                             │
│  - Path: /home/USERNAME/public_html/                        │
│  - Used for: real users                                     │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## 🔄 Complete workflow

### Step 1 — Develop locally

```bash
# Make sure you're on the dev branch
git checkout dev
git pull origin dev   # get latest changes (if working with a team)

# Code your feature
# ... edit files in VS Code, with Claude Code help ...

# Test locally
php -S localhost:8000 -t public/
# Visit http://localhost:8000
```

### Step 2 — Pre-commit checks

Before committing, run through this checklist:

- [ ] No `var_dump()`, `dd()`, `print_r()`, `console.log()` left in code
- [ ] No hardcoded credentials in any file
- [ ] All new SQL uses prepared statements
- [ ] All new output is escaped with `e()`
- [ ] All new forms have `csrf_field()`
- [ ] If you added a migration: tested it on local DB
- [ ] Code follows conventions in `CLAUDE.md`

```bash
# Run the pre-deploy check script
./scripts/pre-deploy-check.sh
```

### Step 3 — Commit and push to dev

```bash
git add .
git status   # verify what you're committing

# Use Conventional Commits format
git commit -m "feat: add Google OAuth login"
# Other examples:
# git commit -m "fix: kitty contribution validation bug"
# git commit -m "docs: update DESIGN-SYSTEM.md"
# git commit -m "chore: bump PHP version requirement"

git push origin dev
```

### Step 4 — Deploy to dev environment

In **cPanel → Git Version Control → mywish-dev** repo:

1. Click "**Update from Remote**"
   - This pulls the latest commits from GitHub into the cPanel repo
2. Wait for the success message
3. Click "**Deploy HEAD Commit**"
   - This runs `.cpanel.yml` to copy files to `/home/USERNAME/dev.mywish.ma/`
4. Check the deployment log for errors

If a NEW migration was added:

5. Open **cPanel → phpMyAdmin → USERNAME_mywish_dev**
6. Click "SQL" tab
7. Copy-paste the new migration `.sql` file
8. Click "Go"
9. Verify with: `SELECT * FROM migrations ORDER BY id DESC LIMIT 5;`

### Step 5 — Test on dev.mywish.ma

```
✅ Open https://dev.mywish.ma
✅ Test the new feature
✅ Test on mobile (375px viewport)
✅ Check storage/logs/ for errors:
   ssh USERNAME@yourhost "tail -50 ~/dev.mywish.ma/storage/logs/php-$(date +%Y-%m-%d).log"
✅ Check the browser console for JS errors
```

If something is broken → fix locally, push to `dev` again, redeploy.

### Step 6 — Merge to main (only when dev is validated)

```bash
# Switch to main
git checkout main
git pull origin main   # get latest

# Merge dev into main
git merge dev

# Push to GitHub
git push origin main
```

### Step 7 — Deploy to production

In **cPanel → Git Version Control → mywish-prod** repo:

1. Click "**Update from Remote**"
2. Click "**Deploy HEAD Commit**"

If migrations need to be applied:

3. **BACKUP THE PROD DATABASE FIRST**:
   ```bash
   ssh USERNAME@yourhost
   mysqldump -u USERNAME_mywish_prod_user -p USERNAME_mywish_prod > ~/backups/prod_$(date +%Y%m%d_%H%M%S).sql
   ```
4. Apply the migration via phpMyAdmin (same as Step 4 but on the prod DB)
5. Verify

### Step 8 — Test production

```
✅ Open https://mywish.ma
✅ Test the same flows as on dev
✅ Check storage/logs for errors
✅ Test critical paths: auth, RSVP, contribution
```

If something is broken in production → see "Emergency rollback" below.

---

## 🚨 Emergency rollback

If a production deploy breaks something critical:

### Option 1 — Roll back to previous commit (fastest)

```bash
# Locally
git log --oneline   # find the previous good commit hash
git checkout main
git reset --hard <good-commit-hash>
git push origin main --force-with-lease  # ⚠️ careful

# Then in cPanel prod: Update from Remote → Deploy HEAD Commit
```

### Option 2 — Revert specific commits

```bash
git revert <bad-commit-hash>
git push origin main

# cPanel: Update from Remote → Deploy HEAD Commit
```

### Option 3 — Restore database from backup

```bash
ssh USERNAME@yourhost
mysql -u USERNAME_mywish_prod_user -p USERNAME_mywish_prod < ~/backups/prod_YYYYMMDD_HHMMSS.sql
```

---

## ⚠️ Pre-deploy precautions checklist

Before EVERY production deploy:

### Code

- [ ] All commits reviewed
- [ ] Tested on `dev.mywish.ma`
- [ ] No PHP errors in dev logs
- [ ] No JS errors in browser console
- [ ] Mobile tested (375px)
- [ ] Critical paths tested (login, create event, RSVP, contribute)

### Database

- [ ] If new migrations: applied on dev first
- [ ] Production DB backed up
- [ ] Migration is idempotent (safe to run twice)
- [ ] Rollback plan documented (if migration is destructive)

### Environment

- [ ] `.env` on prod has correct values
- [ ] `APP_DEBUG=false` in prod `.env`
- [ ] All secrets are in `.env`, not in code
- [ ] Cron jobs (if any) won't break during deploy

### Communication

- [ ] If deploying during peak hours: notify users beforehand
- [ ] Keep an eye on errors for 30 min post-deploy

---

## 📊 Monitoring after deploy

### Check logs

```bash
# SSH into prod
ssh USERNAME@yourhost

# Watch PHP errors in real-time
tail -f ~/public_html/storage/logs/php-$(date +%Y-%m-%d).log

# Watch app logs
tail -f ~/public_html/storage/logs/app-$(date +%Y-%m-%d).log

# Check Apache access log (cPanel-specific path)
tail -f ~/access-logs/mywish.ma
```

### Quick health check

```bash
# From your local machine
curl -I https://mywish.ma
# Should return: HTTP/2 200

curl https://mywish.ma | grep -i "MyWish"
# Should find the brand text
```

---

## 🔧 cPanel-specific tips

### "Update from Remote" doesn't pull new commits

- ✅ Verify the cPanel repo's branch is correct (dev or main)
- ✅ Check the deploy SSH key has read access to the GitHub repo
- ✅ Try via SSH: `cd ~/repositories/mywish-dev && git pull`

### Deploy succeeds but site is broken

- ✅ Check `.cpanel.yml` syntax (YAML is whitespace-sensitive)
- ✅ Verify the DEPLOYPATH is set correctly in cPanel
- ✅ Verify file permissions: `chmod -R 755` for files, `chmod -R 775` for `storage/`

### Need to deploy NOW but cPanel UI is down

```bash
# Manual deploy via SSH
ssh USERNAME@yourhost
cd ~/repositories/mywish-prod
git pull origin main
cp -R public/. ~/public_html/
cp -R src/ ~/public_html/src/
cp -R database/ ~/public_html/database/
```

### How to see what's currently deployed

```bash
ssh USERNAME@yourhost
cat ~/public_html/.last-deploy.txt   # set by .cpanel.yml
cd ~/repositories/mywish-prod && git log -1 --oneline
```

---

## 📝 Best practices

### Branching strategy

- `main` = production (protected, only merge from `dev`)
- `dev` = staging (where features are tested)
- `feature/*` = optional, for big features (merge to `dev` via PR)
- `hotfix/*` = emergency fixes (merge directly to `main` if critical, also to `dev`)

### Commit messages (Conventional Commits)

```
feat: add Google OAuth login
fix: kitty contribution validation bug
docs: update DEPLOYMENT.md
style: format event card spacing
refactor: extract auth logic to AuthService
test: add tests for RSVP flow
chore: bump PHP version
perf: cache event queries
ci: update GitHub Actions workflow
```

### Deploy frequency

- **Dev**: deploy as often as you push (multiple times/day OK)
- **Prod**: deploy when feature is fully validated on dev (1-3 times/week typical)

### Avoid Friday afternoon deploys

If something breaks Friday at 5pm, you're stuck until Monday. Deploy Mon-Thu mornings ideally.

---

## 🎯 Daily workflow summary

```
Morning:
  git pull origin dev
  php -S localhost:8000 -t public/

Coding session (with Claude Code):
  ... develop ...
  ... test locally ...

Before lunch:
  git add . && git commit -m "feat: ..." && git push origin dev
  cPanel dev: Update + Deploy
  Test on dev.mywish.ma

Afternoon:
  ... more coding ...

End of day (if dev is stable):
  git checkout main && git merge dev && git push
  cPanel prod: Update + Deploy
  Test on mywish.ma
  Check logs for 30 min
```
