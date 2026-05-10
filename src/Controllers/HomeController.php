<?php
declare(strict_types=1);

namespace MyWish\Controllers;

use MyWish\Core\View;

/**
 * HomeController — Landing page
 */
final class HomeController
{
    public function index(): string
    {
        return View::render('home', [
            'layout' => 'layouts/default',
            'title'  => 'MyWish.ma — La page de votre événement familial',
        ]);
    }
}
