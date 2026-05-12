-- ============================================================
-- Migration 007: Pledges table (cagnotte contributions)
-- ============================================================
-- One row per guest "promise to contribute". MyWish never holds money:
--   1. Guest pledges amount + payment method (via UI)
--   2. Guest pays externally and uploads proof (screenshot)
--   3. Organizer reviews + validates or rejects
--
-- status lifecycle:
--   pending   → just promised, no proof yet
--   validated → organizer confirmed payment received
--   rejected  → organizer refused (e.g., fake screenshot)
--   expired   → no proof uploaded after N days (cron job, V2)
--
-- This status is a LIFECYCLE, not soft-delete. Rejected/expired pledges
-- stay in the table for audit; the organizer reads them as "not counted".
-- ============================================================

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

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('007_create_pledges');
