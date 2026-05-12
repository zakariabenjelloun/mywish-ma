-- ============================================================
-- Migration 003: Extend users with notification preferences
-- ============================================================
-- Adds 3 BOOLEAN columns to drive future SMTP email triggers (Sprint 5+).
-- Storage now, send-logic later.
-- ============================================================

ALTER TABLE `users`
    ADD COLUMN `notify_on_rsvp`      BOOLEAN NOT NULL DEFAULT TRUE  AFTER `language`,
    ADD COLUMN `notify_on_pledge`    BOOLEAN NOT NULL DEFAULT TRUE  AFTER `notify_on_rsvp`,
    ADD COLUMN `notify_digest_daily` BOOLEAN NOT NULL DEFAULT FALSE AFTER `notify_on_pledge`;

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('003_extend_users_for_settings');
