-- ============================================================
-- Migration 006: RSVPs table (guest responses)
-- ============================================================
-- Stores responses from guests. Guests are NOT users (no account needed
-- to reply) — they're identified by name + optional email/phone.
--
-- response: yes / no / maybe
-- guests_count: how many people they're bringing (incl. themselves), 1+
-- status: soft-delete via 'archived' (organizer can hide a stale reply)
-- ============================================================

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

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('006_create_rsvps');
