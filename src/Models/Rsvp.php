<?php
declare(strict_types=1);

namespace MyWish\Models;

use MyWish\Core\Database;

/**
 * RSVP model — guest responses to an event invitation.
 *
 * Guests are NOT users (no account needed to reply). Identity is captured
 * via guest_name + optional email/phone.
 *
 * Soft delete via status='archived' exists at the DB level (migration 006)
 * but is not exposed on this model — the Controller can update the row
 * directly via Database::update() if it ever needs to hide a stale reply.
 */
final class Rsvp
{
    private static function db(): Database
    {
        return Database::get();
    }

    public static function findById(int $id): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `rsvps` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    /**
     * All active RSVPs for an event, newest first.
     * Archived RSVPs are excluded.
     */
    public static function findByEvent(int $eventId): array
    {
        return self::db()->fetchAll(
            'SELECT * FROM `rsvps`
             WHERE `event_id` = :event_id AND `status` = :status
             ORDER BY `created_at` DESC, `id` DESC',
            ['event_id' => $eventId, 'status' => 'active']
        );
    }

    /**
     * Required: event_id, guest_name, response (yes|no|maybe).
     * Optional: guest_email, guest_phone, guests_count (default 1), message.
     * Auto-set: status='active'.
     */
    public static function create(array $data): int
    {
        $row = [
            'event_id'     => (int) $data['event_id'],
            'guest_name'   => $data['guest_name'],
            'guest_email'  => $data['guest_email']  ?? null,
            'guest_phone'  => $data['guest_phone']  ?? null,
            'response'     => $data['response'],
            'guests_count' => (int) ($data['guests_count'] ?? 1),
            'message'      => $data['message']      ?? null,
            'status'       => 'active',
        ];

        return self::db()->insert('rsvps', $row);
    }

    /**
     * Counts of active RSVPs grouped by response type.
     * Always returns all 3 keys (zero when no responses of that type).
     *
     * @return array{yes:int, no:int, maybe:int}
     */
    public static function countByResponse(int $eventId): array
    {
        $rows = self::db()->fetchAll(
            'SELECT `response`, COUNT(*) AS `n`
             FROM `rsvps`
             WHERE `event_id` = :event_id AND `status` = :status
             GROUP BY `response`',
            ['event_id' => $eventId, 'status' => 'active']
        );

        $out = ['yes' => 0, 'no' => 0, 'maybe' => 0];
        foreach ($rows as $r) {
            $out[$r['response']] = (int) $r['n'];
        }
        return $out;
    }
}
