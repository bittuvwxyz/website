<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($meta_title ?? config('site_name')) ?></title>
    <meta name="description" content="<?= e($meta_description ?? 'Latest articles and updates from ' . config('site_name')) ?>">
    <meta name="keywords" content="<?= e($meta_keywords ?? '') ?>">
    <link rel="canonical" href="<?= e(url(trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/'))) ?>">
    <meta property="og:title" content="<?= e($meta_title ?? config('site_name')) ?>">
    <meta property="og:description" content="<?= e($meta_description ?? '') ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebSite","name":"<?= e(config('site_name')) ?>","url":"<?= e(config('base_url')) ?>"}</script>
</head>
<body>
<a class="skip-link" href="#content">Skip to content</a>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?= e(url()) ?>"><?= e(config('site_name')) ?></a>
        <button class="nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false">Menu</button>
        <nav id="site-nav" class="site-nav" data-open="false" aria-label="Primary navigation">
            <a href="<?= e(url()) ?>">Home</a>
            <a href="<?= e(url('search')) ?>">Search</a>
            <?php if (is_logged_in()): ?>
                <a href="<?= e(url('admin')) ?>">Dashboard</a>
                <a href="<?= e(url('profile')) ?>">Profile</a>
                <a href="<?= e(url('logout')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= e(url('login')) ?>">Login</a>
                <a href="<?= e(url('register')) ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main id="content" tabindex="-1">
<?php foreach (consume_flash() as $flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endforeach; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($meta_title ?? config('site_name')) ?></title><meta name="description" content="<?= e($meta_description ?? '') ?>"><meta name="keywords" content="<?= e($meta_keywords ?? '') ?>"><link rel="canonical" href="<?= e(url(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),'/'))) ?>"><meta property="og:title" content="<?= e($meta_title ?? config('site_name')) ?>"><meta property="og:description" content="<?= e($meta_description ?? '') ?>"><meta name="twitter:card" content="summary_large_image"><link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>"></head><body><header><a class="brand" href="<?= e(url()) ?>"><?= e(config('site_name')) ?></a><nav><a href="<?= e(url()) ?>">Home</a><?php if(is_logged_in()): ?><a href="<?= e(url('admin')) ?>">Dashboard</a><a href="<?= e(url('profile')) ?>">Profile</a><a href="<?= e(url('logout')) ?>">Logout</a><?php else: ?><a href="<?= e(url('login')) ?>">Login</a><a href="<?= e(url('register')) ?>">Register</a><?php endif; ?></nav></header><main>
