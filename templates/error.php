<section class="hero" role="alert">
    <p class="meta">Error <?= (int)$code ?></p>
    <h1><?= e($title) ?></h1>
    <p><?= e($message) ?></p>
    <a class="btn" href="<?= e(url()) ?>">Return home</a>
</section>
<section class="card"><h1><?= (int)$code ?> <?= e($title) ?></h1><p><?= e($message) ?></p></section>
