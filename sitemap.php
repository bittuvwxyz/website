<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/xml; charset=UTF-8');
$posts = db_all("SELECT slug, updated_at FROM posts WHERE status = 'published' AND publish_at <= NOW() ORDER BY updated_at DESC");
$categories = db_all('SELECT slug, updated_at FROM categories ORDER BY name');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= e(url()) ?></loc></url>
    <?php foreach ($categories as $category): ?>
        <url><loc><?= e(url('category/' . $category['slug'])) ?></loc><lastmod><?= e(substr($category['updated_at'], 0, 10)) ?></lastmod></url>
    <?php endforeach; ?>
    <?php foreach ($posts as $post): ?>
        <url><loc><?= e(url('post/' . $post['slug'])) ?></loc><lastmod><?= e(substr($post['updated_at'], 0, 10)) ?></lastmod></url>
    <?php endforeach; ?>
</urlset>
