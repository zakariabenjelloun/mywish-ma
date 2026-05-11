<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Core\View;

/**
 * ProfileController — the signed-in user's profile page.
 */
final class ProfileController
{
    public function index(): string
    {
        auth_required();

        $user = auth();

        $displayName = $user['display_name'] ?? '';
        $firstName   = $displayName !== ''
            ? explode(' ', trim($displayName), 2)[0]
            : strtok((string) ($user['email'] ?? ''), '@');

        return View::render('profile', [
            'layout'     => 'layouts/default',
            'title'      => 'Mon profil — MyWish.ma',
            'user'       => $user,
            'firstName'  => $firstName,
        ]);
    }
}
