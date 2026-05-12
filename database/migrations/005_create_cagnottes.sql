-- ============================================================
-- Migration 005: Cagnottes table (1 per event, sometimes 0)
-- ============================================================
-- Stores the optional "kitty" attached to an event.
-- MyWish never holds the money — see pledges table for the validation flow.
--
-- target_amount is NULL-able: cagnotte type 'other' allows no fixed goal.
-- UNIQUE (event_id, status) caps at 1 active + 1 archived cagnotte per event.
-- ============================================================

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

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('005_create_cagnottes');
