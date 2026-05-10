# 💾 MyWish.ma — Database Conventions

> Conventions for working with MySQL in MyWish.ma.
>
> **Source of truth for the schema**: `database/migrations/`

---

## 🎯 Database engine and config

- **Engine**: MySQL 8.0+
- **Storage engine**: InnoDB (for transactions + foreign keys)
- **Charset**: `utf8mb4` (for full Unicode + emoji support)
- **Collation**: `utf8mb4_unicode_ci`

These are configured automatically in the migration template and in `Database::__construct()`.

---

## 📋 Naming conventions

### Table names

- **lowercase, snake_case**
- **plural** (`users`, `events`, `cagnottes`, `contributions`)
- ❌ NOT `User`, `Users`, `User_Events`
- ✅ YES `users`, `event_authorized_users`

### Column names

- **lowercase, snake_case**
- ✅ `created_at`, `is_premium`, `event_date`
- ❌ `createdAt`, `isPremium`, `EventDate`

### Foreign keys

- Format: `<table_singular>_id`
- ✅ `user_id`, `event_id`, `cagnotte_id`
- ❌ `userId`, `idUser`, `fk_user`

### Indexes

- Format: `idx_<column_name>` for single-column
- Format: `idx_<col1>_<col2>` for composite
- Format: `unique_<column>` for unique indexes (other than primary key)

---

## 🔧 Standard columns

Every table should have:

```sql
`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-- ... other columns ...
`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
```

Soft deletes (when needed):

```sql
`deleted_at` TIMESTAMP NULL,
INDEX `idx_deleted_at` (`deleted_at`),
```

---

## 🔐 Querying patterns

### ✅ ALWAYS use prepared statements

```php
// ✅ GOOD
$user = db()->fetchOne(
    'SELECT * FROM users WHERE email = :email LIMIT 1',
    ['email' => $email]
);

// ❌ BAD — SQL injection
$user = db()->fetchOne("SELECT * FROM users WHERE email = '{$email}'");
```

### ✅ Use the Database class helpers

```php
// SELECT one
$user = db()->fetchOne('SELECT * FROM users WHERE id = :id', ['id' => 1]);

// SELECT all
$events = db()->fetchAll(
    'SELECT * FROM events WHERE owner_id = :owner_id ORDER BY event_date DESC',
    ['owner_id' => $userId]
);

// SELECT a single value
$count = db()->fetchValue(
    'SELECT COUNT(*) FROM events WHERE status = :status',
    ['status' => 'published']
);

// INSERT
$newId = db()->insert('users', [
    'email' => 'test@test.com',
    'display_name' => 'Test User',
    'language' => 'fr',
]);

// UPDATE
$rowsAffected = db()->update(
    'users',
    ['display_name' => 'New Name'],
    'id = :id',
    ['id' => 1]
);

// DELETE
$rowsAffected = db()->delete('users', 'id = :id', ['id' => 1]);
```

### ✅ Use transactions for multi-step writes

```php
$db = db();
try {
    $db->beginTransaction();

    $eventId = $db->insert('events', [...]);
    $db->insert('cagnottes', ['event_id' => $eventId, ...]);
    $db->insert('payment_methods', ['event_id' => $eventId, ...]);

    $db->commit();
} catch (\Throwable $e) {
    $db->rollback();
    throw $e;
}
```

---

## 🛡️ Security rules

### 1. Validate input BEFORE inserting

```php
// ✅ GOOD
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new \InvalidArgumentException('Invalid email');
}
db()->insert('users', ['email' => $email]);

// ❌ BAD — trusting user input
db()->insert('users', ['email' => $_POST['email']]);
```

### 2. Hash passwords with `password_hash()` (if you ever store passwords)

```php
$hash = password_hash($plainPassword, PASSWORD_ARGON2ID);

// To verify:
if (password_verify($plainPassword, $storedHash)) { ... }
```

### 3. Never expose internal IDs in URLs (use slugs or UUIDs for public)

```php
// ✅ GOOD — slug-based public URL
GET /event/yasmine-karim-mariage

// ❌ BAD — sequential ID exposes scale
GET /event/42
```

### 4. Always escape output

```php
// In templates:
<h1><?= e($event['title']) ?></h1>          // ✅ escaped
<h1><?= $event['title'] ?></h1>             // ❌ XSS risk
```

---

## 📝 Migration process

### Creating a new migration

```bash
# Use the helper script
./scripts/new-migration.sh "add_phone_verified_to_users"

# Creates: database/migrations/003_add_phone_verified_to_users.sql
# (or whatever the next number is)
```

### Migration template

```sql
-- ============================================================
-- Migration NNN: Short title
-- ============================================================
-- What this migration does and why.
-- ROLLBACK: <how to undo this migration>
-- ============================================================

-- Your SQL changes here
ALTER TABLE `users` ADD COLUMN `phone_verified` BOOLEAN NOT NULL DEFAULT FALSE;

-- Track this migration (last line)
INSERT IGNORE INTO `migrations` (`name`) VALUES ('NNN_short_title');
```

### Applying migrations

See [`database/migrations/README.md`](../database/migrations/README.md) for full process.

**Quick version**:
1. Apply on **local** first → test
2. Commit + push to `dev` branch
3. Pull on cPanel dev → apply on `database_dev` → test
4. Merge `dev` → `main`
5. Pull on cPanel prod → **BACKUP DB** → apply on `database_prod`

---

## 🔄 Common queries

### Get current user's events

```php
$userId = auth()['id'];
$events = db()->fetchAll('
    SELECT id, title, event_date, status, slug
    FROM events
    WHERE owner_id = :owner_id
    AND deleted_at IS NULL
    ORDER BY event_date DESC
', ['owner_id' => $userId]);
```

### Get event with its kitty stats

```php
$event = db()->fetchOne('
    SELECT
        e.*,
        u.display_name AS owner_name,
        (SELECT SUM(amount) FROM contributions
         WHERE event_id = e.id AND status = "validated") AS total_validated,
        (SELECT SUM(amount) FROM contributions
         WHERE event_id = e.id AND status = "pending") AS total_pending,
        (SELECT COUNT(*) FROM rsvps
         WHERE event_id = e.id AND response = "yes") AS rsvp_count
    FROM events e
    JOIN users u ON e.owner_id = u.id
    WHERE e.slug = :slug
    LIMIT 1
', ['slug' => $slug]);
```

### Paginated list with COUNT

```php
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$events = db()->fetchAll(
    'SELECT * FROM events
     WHERE status = "published"
     ORDER BY event_date DESC
     LIMIT :limit OFFSET :offset',
    ['limit' => $perPage, 'offset' => $offset]
);

$total = (int) db()->fetchValue(
    'SELECT COUNT(*) FROM events WHERE status = "published"'
);

$totalPages = (int) ceil($total / $perPage);
```

---

## 🚨 Things to NEVER do

1. ❌ Never use `SELECT *` in production code (be explicit about columns)
2. ❌ Never run a query inside a loop (N+1 problem) → use JOINs or batch
3. ❌ Never store passwords in plain text
4. ❌ Never use `MD5` or `SHA1` for passwords (use `password_hash`)
5. ❌ Never trust `$_GET` or `$_POST` directly — validate, sanitize
6. ❌ Never expose database errors to users (log them, show generic message)
7. ❌ Never modify production DB directly without going through dev first
8. ❌ Never delete data without `WHERE` clause (always test with SELECT first)
9. ❌ Never store secrets (API keys, passwords) in DB without encryption
10. ❌ Never DROP TABLE in production without a backup

---

## 🎯 Performance tips

### 1. Index columns used in WHERE / JOIN / ORDER BY

```sql
ALTER TABLE events ADD INDEX idx_owner_id (owner_id);
ALTER TABLE events ADD INDEX idx_status_date (status, event_date);
```

### 2. Use `LIMIT` on all queries that return lists

```sql
SELECT * FROM events WHERE status = 'published' LIMIT 100;
```

### 3. Use `EXPLAIN` to debug slow queries

```sql
EXPLAIN SELECT ... FROM ... WHERE ...;
```

### 4. Cache expensive queries (later, with Redis or filesystem)

For MVP: don't optimize prematurely. Measure first.

---

## 🆘 Backups

### Manual backup (recommended weekly)

```bash
# SSH into cPanel server
ssh USERNAME@yourhost

# Create backup
mkdir -p ~/backups
mysqldump -u USERNAME_mywish_prod_user -p USERNAME_mywish_prod \
  --single-transaction --quick \
  > ~/backups/prod_$(date +%Y%m%d_%H%M%S).sql

# Compress
gzip ~/backups/prod_$(date +%Y%m%d_%H%M%S).sql
```

### Automated backups (set up via cPanel cron)

Edit cron via cPanel → Cron Jobs:

```
0 3 * * * mysqldump -u USERNAME_mywish_prod_user -p'PASSWORD' USERNAME_mywish_prod | gzip > /home/USERNAME/backups/auto_$(date +\%Y\%m\%d).sql.gz
```

(Runs daily at 3am)

### cPanel "Backup Wizard"

The cPanel itself has a Full Backup tool — useful for emergencies, but slow for daily use.

---

## 📚 Resources

- [MySQL 8 docs](https://dev.mysql.com/doc/refman/8.0/en/)
- [PHP PDO docs](https://www.php.net/manual/en/book.pdo.php)
- [Use The Index, Luke!](https://use-the-index-luke.com/) — SQL performance guide
- [SQL injection prevention](https://owasp.org/www-community/attacks/SQL_Injection)
