-- ============================================================
-- Migration 002: Events table
-- ============================================================
-- Core table: each row = one family event page.
-- ============================================================

CREATE TABLE IF NOT EXISTS `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `owner_id` INT UNSIGNED NOT NULL,
    `type` ENUM('birthday', 'wedding', 'baby_shower', 'birth', 'other') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `hero_name` VARCHAR(255) NOT NULL,
    `hero_secondary` VARCHAR(255),  -- For weddings (spouse name)
    `event_date` DATE NOT NULL,
    `event_time` TIME,
    `location_general` VARCHAR(255),  -- Public ("Casablanca, Anfa")
    `location_precise` VARCHAR(500),  -- Auth required
    `location_lat` DECIMAL(10, 8),
    `location_lng` DECIMAL(11, 8),
    `hero_photo_url` VARCHAR(500),
    `welcome_message` TEXT,
    `event_code` VARCHAR(20) NOT NULL,  -- 4-6 chars, modifiable
    `slug` VARCHAR(100) UNIQUE,  -- "ibrahim-8ans" for Premium
    `is_premium` BOOLEAN NOT NULL DEFAULT FALSE,
    `status` ENUM('draft', 'published', 'archived', 'suspended') NOT NULL DEFAULT 'draft',
    `visibility_rules` JSON,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `archived_at` TIMESTAMP NULL,

    FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,

    INDEX `idx_owner_id` (`owner_id`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_event_date` (`event_date`),
    INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `migrations` (`name`) VALUES ('002_create_events');
