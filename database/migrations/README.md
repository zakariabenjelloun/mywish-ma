# 💾 Database Migrations

> **Source of truth** for the database schema.
>
> Each migration is a numbered `.sql` file that documents one schema change.
> Migrations are committed to Git so the team has a full history of DB changes.

---

## 📋 Convention

### File naming

```
NNN_short_description.sql
```

Examples:
- `000_create_migrations_table.sql`
- `001_create_users.sql`
- `002_create_events.sql`
- `003_add_phone_verified_to_users.sql`
- `004_create_cagnottes.sql`

**Rules**:
- 3-digit zero-padded prefix (000-999)
- Snake_case description
- Use verbs: `create_`, `add_`, `remove_`, `rename_`, `alter_`
- One change per file (don't mix create + alter in same file)

### File template

```sql
-- ============================================================
-- Migration NNN: Short title
-- ============================================================
-- Brief description of what this migration does and why.
-- ============================================================

-- Your SQL here (CREATE TABLE, ALTER TABLE, etc.)
CREATE TABLE IF NOT EXISTS `xxx` (...);

-- Track this migration (always last line)
INSERT IGNORE INTO `migrations` (`name`) VALUES ('NNN_short_description');
```

---

## 🚀 How to apply migrations

### Option A — cPanel phpMyAdmin (manual, simplest)

For each new migration that's NOT already applied:

1. Login to **cPanel → phpMyAdmin**
2. Select the right database (`xxx_dev` or `xxx_prod`)
3. Click "**SQL**" tab
4. Open the migration `.sql` file in a text editor
5. Copy-paste the content into the SQL box
6. Click "**Go**"
7. Verify the migration is now in the `migrations` table

### Option B — Via SSH (faster if you have SSH access)

```bash
# Connect via SSH
ssh user@yourhost.com

# Run a migration
mysql -u DB_USER -p DB_NAME < database/migrations/003_add_phone_verified_to_users.sql
```

### Option C — Via the migration script (semi-automatic)

```bash
# From the project root
php scripts/migrate.php
```

This runs all NEW migrations (skips those already in the `migrations` table).

---

## ⚠️ CRITICAL RULES

### 1. ALWAYS apply on `dev` first, then `prod`

```
1. Write the migration on local
2. Apply to local DB → test
3. Commit + push to dev branch
4. Pull on dev cPanel → apply migration to database_dev → test
5. If OK: merge dev → main
6. Pull on prod cPanel → apply migration to database_prod
```

### 2. NEVER edit a migration once it's been applied

If you need to fix something, create a NEW migration:

```
003_add_phone_verified_to_users.sql      ← already applied, DON'T touch
004_fix_phone_verified_default_value.sql  ← create this instead
```

### 3. Always make migrations REVERSIBLE if possible

For destructive changes (DROP, RENAME), include a comment with the rollback:

```sql
-- ============================================================
-- Migration 010: Drop legacy `old_field` from users
-- ============================================================
-- ROLLBACK: ALTER TABLE users ADD COLUMN old_field VARCHAR(255);
-- ============================================================

ALTER TABLE `users` DROP COLUMN `old_field`;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('010_drop_old_field_from_users');
```

### 4. ALWAYS use `IF NOT EXISTS` / `IF EXISTS` when possible

This makes migrations idempotent (safe to run twice):

```sql
CREATE TABLE IF NOT EXISTS ...
DROP INDEX IF EXISTS ...
ALTER TABLE ... ADD COLUMN IF NOT EXISTS ...  -- MySQL 8.0.29+
```

### 5. Backup the production DB before risky migrations

For migrations that ALTER/DROP existing tables with data:

```bash
# Via cPanel: Files → Backups → Generate/Download a Full Backup
# Or via SSH:
mysqldump -u DB_USER -p DB_NAME > backup_$(date +%Y%m%d_%H%M%S).sql
```

Store backups in `~/backups/` on the server (NOT in Git).

---

## 📊 Tracking applied migrations

The `migrations` table contains one row per applied migration:

```sql
SELECT * FROM migrations ORDER BY id;

-- id | name                            | executed_at
-- ---+---------------------------------+--------------------
-- 1  | 001_create_users                | 2026-05-09 14:23:01
-- 2  | 002_create_events               | 2026-05-09 14:23:15
-- 3  | 003_create_cagnottes            | 2026-05-09 14:25:42
```

To see which migrations are NOT yet applied:

```bash
# Compare files in database/migrations/ with rows in `migrations` table
ls database/migrations/*.sql | sed 's|.*/||;s|\.sql$||' > /tmp/files.txt
mysql -u DB_USER -p DB_NAME -e "SELECT name FROM migrations" -N | sort > /tmp/applied.txt
comm -23 /tmp/files.txt /tmp/applied.txt
```

---

## 🌱 Seeds (test data)

Seeds are in `database/seeds/` and are SEPARATE from migrations.
Apply them only on dev (NEVER prod):

```bash
mysql -u DB_USER -p DB_NAME_DEV < database/seeds/001_test_users.sql
```

---

## 🆘 If a migration fails halfway through

1. **Don't panic.** Check the error message.
2. **Don't run the migration again** until you fix the partial state.
3. **Manually rollback** the partial changes if possible.
4. **Fix the migration file**.
5. **Try again**.
6. If you can't recover → restore from backup.

---

## 📝 Best practices checklist

Before committing a new migration:

- [ ] File is named correctly (`NNN_verb_description.sql`)
- [ ] File starts with the standard header comment
- [ ] SQL uses `IF NOT EXISTS` / `IF EXISTS` where possible
- [ ] Foreign keys have `ON DELETE` and `ON UPDATE` clauses
- [ ] Indexes are added for foreign keys and frequently queried columns
- [ ] CHARSET is `utf8mb4` and COLLATION is `utf8mb4_unicode_ci`
- [ ] ENGINE is `InnoDB` (for transactions and foreign keys)
- [ ] Last line: `INSERT IGNORE INTO migrations VALUES (...)`
- [ ] Tested locally
- [ ] Tested on dev database
- [ ] Documented in commit message
