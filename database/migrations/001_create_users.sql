-- ============================================================
-- Migration 001: Users table
-- ============================================================
-- Stores all user accounts (organizers + guests).
-- Auth via Google OAuth + WhatsApp phone verification.
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `google_id` VARCHAR(255) UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(20),
    `phone_verified` BOOLEAN NOT NULL DEFAULT FALSE,
    `display_name` VARCHAR(255),
    `avatar_url` VARCHAR(500),
    `language` VARCHAR(5) NOT NULL DEFAULT 'fr',
    `banned` BOOLEAN NOT NULL DEFAULT FALSE,
    `banned_reason` TEXT,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_google_id` (`google_id`),
    INDEX `idx_email` (`email`),
    INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('001_create_users');
