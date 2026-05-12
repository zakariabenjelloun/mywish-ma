<?php
declare(strict_types=1);

namespace MyWish\Models;

use MyWish\Core\Database;

/**
 * Event model — one row per family event page.
 *
 * Lifecycle: 'active' from creation, 'archived' on soft-delete.
 * The 'draft' and 'suspended' enum values exist for future use (moderation).
 *
 * Slug strategy: kebab-case of the title. On collision, append "-xxxxx"
 * (5-char random alphanumeric). Retries up to 5 times before throwing.
 *
 * hero_name / hero_secondary mapping is the Controller's job (see EventController).
 */
final class Event
{
    private static function db(): Database
    {
        return Database::get();
    }

    public static function findById(int $id): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `events` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `events` WHERE `slug` = :slug LIMIT 1',
            ['slug' => $slug]
        );
    }

    public static function findByOwner(int $userId, string $status = 'active'): array
    {
        return self::db()->fetchAll(
            'SELECT * FROM `events`
             WHERE `owner_id` = :owner_id AND `status` = :status
             ORDER BY `event_date` ASC, `id` DESC',
            ['owner_id' => $userId, 'status' => $status]
        );
    }

    /**
     * Required: owner_id, type, title, hero_name, event_date.
     * Optional: hero_secondary, event_time, location_city, location_address,
     *           hero_photo_url, welcome_message, welcome_message_tone.
     * Auto-set: slug, event_code, status='active'.
     */
    public static function create(array $data): int
    {
        $row = [
            'owner_id'             => (int) $data['owner_id'],
            'type'                 => $data['type'],
            'title'                => $data['title'],
            'hero_name'            => $data['hero_name'],
            'hero_secondary'       => $data['hero_secondary']       ?? null,
            'event_date'           => $data['event_date'],
            'event_time'           => $data['event_time']           ?? null,
            'location_city'        => $data['location_city']        ?? null,
            'location_address'     => $data['location_address']     ?? null,
            'hero_photo_url'       => $data['hero_photo_url']       ?? null,
            'welcome_message'      => $data['welcome_message']      ?? null,
            'welcome_message_tone' => $data['welcome_message_tone'] ?? null,
            'slug'                 => self::generateSlug($data['title']),
            'event_code'           => self::generateEventCode(),
            'status'               => 'active',
        ];

        return self::db()->insert('events', $row);
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = [
            'type', 'title', 'hero_name', 'hero_secondary',
            'event_date', 'event_time',
            'location_city', 'location_address',
            'hero_photo_url',
            'welcome_message', 'welcome_message_tone',
        ];
        $row = array_intersect_key($data, array_flip($allowed));
        if (!$row) return false;

        return self::db()->update('events', $row, 'id = :id', ['id' => $id]) > 0;
    }

    public static function archive(int $id): bool
    {
        return self::db()->execute(
            'UPDATE `events` SET `status` = :status, `archived_at` = NOW() WHERE `id` = :id',
            ['status' => 'archived', 'id' => $id]
        ) > 0;
    }

    /**
     * Generate a unique URL-safe slug. On collision, retry up to 5 times
     * with a 5-char random suffix before throwing.
     */
    public static function generateSlug(string $title): string
    {
        $base = self::slugify($title);
        if ($base === '') $base = 'event';

        if (!self::slugExists($base)) {
            return $base;
        }

        for ($i = 0; $i < 5; $i++) {
            $candidate = $base . '-' . self::randomString(5);
            if (!self::slugExists($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Could not generate unique slug after 5 attempts');
    }

    private static function slugify(string $title): string
    {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title);
        if ($ascii === false) $ascii = $title;
        $ascii = strtolower($ascii);
        $ascii = preg_replace('/[^a-z0-9]+/', '-', $ascii);
        return trim($ascii, '-');
    }

    private static function slugExists(string $slug): bool
    {
        return self::db()->fetchOne(
            'SELECT `id` FROM `events` WHERE `slug` = :slug LIMIT 1',
            ['slug' => $slug]
        ) !== null;
    }

    /**
     * 6-char alphanumeric event code (skips I/O/0/1 for clarity).
     * For V2 private-event access; populated now to satisfy NOT NULL.
     */
    private static function generateEventCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        return self::randomString(6, $alphabet);
    }

    private static function randomString(int $length, string $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789'): string
    {
        $out = '';
        $max = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $out .= $alphabet[random_int(0, $max)];
        }
        return $out;
    }
}
