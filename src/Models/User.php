<?php
declare(strict_types=1);

namespace MyWish\Models;

use MyWish\Core\Database;

/**
 * User model — stores all accounts (organizers + guests).
 *
 * Auth flow: Google OAuth populates google_id, email, display_name, avatar_url.
 * phone / phone_verified will be populated by the WhatsApp flow (later sprint).
 */
final class User
{
    private static function db(): Database
    {
        return Database::get();
    }

    public static function findById(int $id): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `users` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    public static function findByGoogleId(string $googleId): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `users` WHERE `google_id` = :google_id LIMIT 1',
            ['google_id' => $googleId]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return self::db()->fetchOne(
            'SELECT * FROM `users` WHERE `email` = :email LIMIT 1',
            ['email' => $email]
        );
    }

    /**
     * Upsert a user from a Google userinfo payload.
     *
     * Matching strategy:
     *   1. Match by google_id (existing Google-linked account).
     *   2. Otherwise match by email (link Google to a pre-existing account).
     *   3. Otherwise create a new user.
     *
     * Google is the source of truth for display_name and avatar_url —
     * they are refreshed on every login.
     *
     * @param array{sub:string, email:string, name?:string, picture?:string} $google
     * @return array The fresh user row.
     */
    public static function createOrUpdateFromGoogle(array $google): array
    {
        $db = self::db();

        $googleId   = $google['sub'];
        $email      = $google['email'];
        $name       = $google['name'] ?? null;
        $picture    = $google['picture'] ?? null;

        $existing = self::findByGoogleId($googleId) ?? self::findByEmail($email);

        if ($existing) {
            $db->update(
                'users',
                [
                    'google_id'    => $googleId,
                    'email'        => $email,
                    'display_name' => $name,
                    'avatar_url'   => $picture,
                ],
                'id = :id',
                ['id' => $existing['id']]
            );

            return self::findById((int) $existing['id']);
        }

        $id = $db->insert('users', [
            'google_id'    => $googleId,
            'email'        => $email,
            'display_name' => $name,
            'avatar_url'   => $picture,
        ]);

        return self::findById($id);
    }
}
