<?php
declare(strict_types=1);

namespace MyWish\Models;

use MyWish\Core\Database;

/**
 * Cagnotte model — at most ONE active cagnotte per event.
 *
 * Database enforces uniqueness via UNIQUE (event_id, status) — see migration 005.
 *
 * target_amount is nullable: cagnotte type 'other' can have no fixed goal.
 * Other types require a target (validated at Controller level, not in this model).
 *
 * Currency defaults to 'MAD' but the column allows any 3-letter code (future-proof).
 */
final class Cagnotte
{
    private static function db(): Database
    {
        return Database::get();
    }

    public static function findById(int $id): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `cagnottes` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    /**
     * Return the active cagnotte for an event, or null if none exists.
     * Archived cagnottes are NOT returned by this method.
     */
    public static function findByEvent(int $eventId): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `cagnottes`
             WHERE `event_id` = :event_id AND `status` = :status
             LIMIT 1',
            ['event_id' => $eventId, 'status' => 'active']
        );
    }

    /**
     * Required: event_id, type, title.
     * Optional: description, target_amount, currency (default 'MAD'), photo_url.
     * Auto-set: status='active'.
     */
    public static function create(array $data): int
    {
        $row = [
            'event_id'      => (int) $data['event_id'],
            'type'          => $data['type'],
            'title'         => $data['title'],
            'description'   => $data['description']   ?? null,
            'target_amount' => $data['target_amount'] ?? null,
            'currency'      => $data['currency']      ?? 'MAD',
            'photo_url'     => $data['photo_url']     ?? null,
            'status'        => 'active',
        ];

        return self::db()->insert('cagnottes', $row);
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['type', 'title', 'description', 'target_amount', 'currency', 'photo_url'];
        $row = array_intersect_key($data, array_flip($allowed));
        if (!$row) return false;

        return self::db()->update('cagnottes', $row, 'id = :id', ['id' => $id]) > 0;
    }

    public static function archive(int $id): bool
    {
        return self::db()->update(
            'cagnottes',
            ['status' => 'archived'],
            'id = :id',
            ['id' => $id]
        ) > 0;
    }
}
