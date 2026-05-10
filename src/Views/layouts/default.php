<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0A0A0A">
    <title><?= e($title ?? 'MyWish.ma') ?></title>
    <meta name="description" content="<?= e($description ?? 'La page de votre événement familial. Invitations, cagnotte et souvenirs sur un seul lien.') ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($title ?? 'MyWish.ma') ?>">
    <meta property="og:description" content="<?= e($description ?? 'Tout sur un lien WhatsApp.') ?>">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js"></script>
</head>
<body class="bg-bg-deep text-text-primary font-body antialiased">

    <?= \MyWish\Core\View::partial('partials/header') ?>

    <main>
        <?= $content ?? '' ?>
    </main>

    <?= \MyWish\Core\View::partial('partials/footer') ?>

</body>
</html>
