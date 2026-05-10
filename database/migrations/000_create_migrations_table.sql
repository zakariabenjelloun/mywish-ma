-- ============================================================
-- Migration 000: Migrations tracking table
-- ============================================================
-- This table tracks which migrations have been applied.
-- It must be created BEFORE any other migration.
--
-- Run manually via cPanel phpMyAdmin or via scripts/migrate.php
-- ============================================================

CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `executed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
