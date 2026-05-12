-- ============================================================
-- COMBINED MIGRATIONS 003 → 007 (Sprint 3 Phase C.1)
-- ============================================================
-- Generated 2026-05-11. Paste this entire file into phpMyAdmin → SQL tab,
-- then click "Go" to apply all 5 migrations in one shot.
--
-- ⚠️ ONE-SHOT FILE: NOT safe to re-run.
--   - Migration 003 fails on rerun (ADD COLUMN without IF NOT EXISTS).
--   - Migration 004 fails on rerun (CHANGE COLUMN renames — old names gone).
--   - Migrations 005/006/007 are safe (CREATE TABLE IF NOT EXISTS).
-- If you need to re-apply, use the individual NNN_*.sql files and check
-- the `migrations` table first to see which are already recorded.
--
-- Order matters: 007 (pledges) has a FK to 005 (cagnottes). Order is preserved.
-- ============================================================


-- ════════════════════════════════════════════════════════════
-- BEGIN 003_extend_users_for_settings
-- ════════════════════════════════════════════════════════════
-- Adds 3 BOOLEAN columns to drive future SMTP email triggers (Sprint 5+).
-- Storage now, send-logic later.

ALTER TABLE `users`
    ADD COLUMN `notify_on_rsvp`      BOOLEAN NOT NULL DEFAULT TRUE  AFTER `language`,
    ADD COLUMN `notify_on_pledge`    BOOLEAN NOT NULL DEFAULT TRUE  AFTER `notify_on_rsvp`,
    ADD COLUMN `notify_digest_daily` BOOLEAN NOT NULL DEFAULT FALSE AFTER `notify_on_pledge`;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('003_extend_users_for_settings');
-- END 003 ─────────────────────────────────────────────────────


-- ════════════════════════════════════════════════════════════
-- BEGIN 004_refine_events_schema
-- ════════════════════════════════════════════════════════════
-- Four coordinated changes:
--   1. ENUM type → ('wedding', 'anniversary', 'birth', 'engagement', 'other')
--   2. Rename location_general → location_city,  location_precise → location_address
--   3. ENUM status → ('draft', 'active', 'archived', 'suspended')
--   4. Add welcome_message_tone ENUM('formal', 'warm', 'casual')

-- Defensive: migrate any 'published' rows to 'active' BEFORE the MODIFY
-- removes 'published' from the ENUM. No-op on empty table.
UPDATE `events` SET `status` = 'active' WHERE `status` = 'published';

ALTER TABLE `events`
    MODIFY COLUMN `type`   ENUM('wedding', 'anniversary', 'birth', 'engagement', 'other') NOT NULL,
    CHANGE COLUMN `location_general` `location_city`    VARCHAR(255) NULL,
    CHANGE COLUMN `location_precise` `location_address` VARCHAR(500) NULL,
    MODIFY COLUMN `status` ENUM('draft', 'active', 'archived', 'suspended') NOT NULL DEFAULT 'draft',
    ADD COLUMN `welcome_message_tone` ENUM('formal', 'warm', 'casual') NULL AFTER `welcome_message`;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('004_refine_events_schema');
-- END 004 ─────────────────────────────────────────────────────


-- ════════════════════════════════════════════════════════════
-- BEGIN 005_create_cagnottes
-- ════════════════════════════════════════════════════════════
-- Optional kitty attached to an event. MyWish never holds the money.
-- target_amount NULL-able (cagnotte type 'other' allows no fixed goal).
-- UNIQUE (event_id, status) caps at 1 active + 1 archived cagnotte per event.

CREATE TABLE IF NOT EXISTS `cagnottes` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id`      INT UNSIGNED NOT NULL,
    `type`          ENUM('travel', 'furniture', 'free_gift', 'other') NOT NULL,
    `title`         VARCHAR(200) NOT NULL,
    `description`   TEXT,
    `target_amount` DECIMAL(10, 2) NULL,
    `currency`      VARCHAR(3) NOT NULL DEFAULT 'MAD',
    `photo_url`     VARCHAR(500),
    `status`        ENUM('active', 'archived') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY `uniq_event_active` (`event_id`, `status`),
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,

    INDEX `idx_event_id` (`event_id`),
    INDEX `idx_status`   (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('005_create_cagnottes');
-- END 005 ─────────────────────────────────────────────────────


-- ════════════════════════════════════════════════════════════
-- BEGIN 006_create_rsvps
-- ════════════════════════════════════════════════════════════
-- Guest responses. Guests are NOT users (no account needed to reply).
-- status: soft-delete via 'archived' (organizer can hide a stale reply).

CREATE TABLE IF NOT EXISTS `rsvps` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id`     INT UNSIGNED NOT NULL,
    `guest_name`   VARCHAR(200) NOT NULL,
    `guest_email`  VARCHAR(255),
    `guest_phone`  VARCHAR(20),
    `response`     ENUM('yes', 'no', 'maybe') NOT NULL,
    `guests_count` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `message`      TEXT,
    `status`       ENUM('active', 'archived') NOT NULL DEFAULT 'active',
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,

    INDEX `idx_event_id` (`event_id`),
    INDEX `idx_response` (`response`),
    INDEX `idx_status`   (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('006_create_rsvps');
-- END 006 ─────────────────────────────────────────────────────


-- ════════════════════════════════════════════════════════════
-- BEGIN 007_create_pledges
-- ════════════════════════════════════════════════════════════
-- One row per guest "promise to contribute". MyWish never holds money.
-- status lifecycle: pending → validated / rejected / expired.
-- Rejected/expired pledges stay for audit (not soft-deleted).

CREATE TABLE IF NOT EXISTS `pledges` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cagnotte_id`  INT UNSIGNED NOT NULL,
    `guest_name`   VARCHAR(200) NOT NULL,
    `guest_email`  VARCHAR(255),
    `guest_phone`  VARCHAR(20),
    `amount`       DECIMAL(10, 2) NOT NULL,
    `currency`     VARCHAR(3) NOT NULL DEFAULT 'MAD',
    `message`      TEXT,
    `proof_url`    VARCHAR(500),
    `status`       ENUM('pending', 'validated', 'rejected', 'expired') NOT NULL DEFAULT 'pending',
    `pledged_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `validated_at` TIMESTAMP NULL,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`cagnotte_id`) REFERENCES `cagnottes`(`id`) ON DELETE CASCADE,

    INDEX `idx_cagnotte_id` (`cagnotte_id`),
    INDEX `idx_status`      (`status`),
    INDEX `idx_pledged_at`  (`pledged_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('007_create_pledges');
-- END 007 ─────────────────────────────────────────────────────


-- ════════════════════════════════════════════════════════════
-- DONE — verify by running:
--   SELECT name, executed_at FROM migrations ORDER BY id;
-- You should see entries 003 through 007 in the result.
-- ════════════════════════════════════════════════════════════
