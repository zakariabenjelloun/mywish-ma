<?php
declare(strict_types=1);

namespace MyWish\Models;

use MyWish\Core\Database;

/**
 * Pledge model — guest "promise to contribute" to a cagnotte.
 *
 * Lifecycle status (4 values, not soft-delete):
 *   pending   → just promised, no proof yet
 *   validated → organizer confirmed payment received
 *   rejected  → organizer refused
 *   expired   → no proof uploaded after N days (cron job, V2)
 *
 * MyWish never holds the money — the pledge tracks the off-platform transfer.
 */
final class Pledge
{
    private static function db(): Database
    {
        return Database::get();
    }

    public static function findById(int $id): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `pledges` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    /**
     * All pledges for a cagnotte, newest first. No status filter —
     * organizers need to see pending + validated + rejected + expired in one view.
     */
    public static function findByCagnotte(int $cagnotteId): array
    {
        return self::db()->fetchAll(
            'SELECT * FROM `pledges`
             WHERE `cagnotte_id` = :cagnotte_id
             ORDER BY `pledged_at` DESC, `id` DESC',
            ['cagnotte_id' => $cagnotteId]
        );
    }

    /**
     * Required: cagnotte_id, guest_name, amount.
     * Optional: guest_email, guest_phone, currency (default 'MAD'),
     *           message, proof_url.
     * Auto-set: status='pending', pledged_at=NOW().
     */
    public static function create(array $data): int
    {
        $row = [
            'cagnotte_id' => (int) $data['cagnotte_id'],
            'guest_name'  => $data['guest_name'],
            'guest_email' => $data['guest_email'] ?? null,
            'guest_phone' => $data['guest_phone'] ?? null,
            'amount'      => $data['amount'],
            'currency'    => $data['currency']    ?? 'MAD',
            'message'     => $data['message']     ?? null,
            'proof_url'   => $data['proof_url']   ?? null,
            'status'      => 'pending',
        ];

        return self::db()->insert('pledges', $row);
    }

    /**
     * Sum of pledge amounts for a cagnotte, filtered by status.
     * Default 'validated' — only counts money the organizer has confirmed.
     *
     * Returns 0.0 if no pledges match.
     */
    public static function totalAmount(int $cagnotteId, string $status = 'validated'): float
    {
        $sum = self::db()->fetchValue(
            'SELECT COALESCE(SUM(`amount`), 0)
             FROM `pledges`
             WHERE `cagnotte_id` = :cagnotte_id AND `status` = :status',
            ['cagnotte_id' => $cagnotteId, 'status' => $status]
        );

        return (float) $sum;
    }
}
