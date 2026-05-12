-- ============================================================
-- Migration 004: Refine events schema for Sprint 3 wizard
-- ============================================================
-- Four coordinated changes (decisions logged 2026-05-11):
--   1. ENUM type: birthday/baby_shower removed, anniversary/engagement added
--      → ('wedding', 'anniversary', 'birth', 'engagement', 'other')
--   2. Rename location_general → location_city
--                location_precise → location_address
--   3. ENUM status: rename 'published' → 'active' for cross-table consistency
--      → ('draft', 'active', 'archived', 'suspended')
--   4. New column: welcome_message_tone ('formal' | 'warm' | 'casual')
--
-- Table `events` is empty (confirmed) — no data migration risk.
-- The UPDATE statement below is defensive: harmless on empty table,
-- prevents data loss if this migration is ever re-run on a populated DB.
-- ============================================================

-- Defensive: migrate any 'published' rows to 'active' BEFORE the MODIFY
-- removes 'published' from the ENUM. No-op on empty table.
UPDATE `events` SET `status` = 'active' WHERE `status` = 'published';

-- Apply all schema changes in one ALTER TABLE
ALTER TABLE `events`
    MODIFY COLUMN `type`   ENUM('wedding', 'anniversary', 'birth', 'engagement', 'other') NOT NULL,
    CHANGE COLUMN `location_general` `location_city`    VARCHAR(255) NULL,
    CHANGE COLUMN `location_precise` `location_address` VARCHAR(500) NULL,
    MODIFY COLUMN `status` ENUM('draft', 'active', 'archived', 'suspended') NOT NULL DEFAULT 'draft',
    ADD COLUMN `welcome_message_tone` ENUM('formal', 'warm', 'casual') NULL AFTER `welcome_message`;

-- Track this migration
INSERT IGNORE INTO `migrations` (`name`) VALUES ('004_refine_events_schema');
