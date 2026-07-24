<section class="hero">
    <p class="meta">Latest stories, guides, and updates</p>
    <h1>Read thoughtful posts from <?= e(config('site_name')) ?>.</h1>
    <p>Browse fresh articles, search by topic, or explore categories.</p>
    <a class="btn" href="<?= e(url('search')) ?>">Search articles</a>
</section>
<div class="grid">
    <section aria-label="Latest posts">
        <?php if (!$posts): ?>
            <div class="empty-state"><h2>No posts yet</h2><p>Published posts will appear here.</p></div>
        <?php endif; ?>
        <?php foreach ($posts as $post): ?>
            <article class="card post-card">
                <p class="meta"><?= e($post['category_name'] ?? 'Uncategorized') ?> · <?= e(substr($post['publish_at'], 0, 10)) ?></p>
                <h2><a href="<?= e(url('post/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h2>
                <p><?= e($post['short_description'] ?: excerpt($post['content'])) ?></p>
                <a class="btn secondary" href="<?= e(url('post/' . $post['slug'])) ?>">Read more</a>
            </article>
        <?php endforeach; ?>
        <?= $pagination ?>
    </section>
    <aside class="card" aria-label="Sidebar">
        <h2>Search</h2>
        <form action="<?= e(url('search')) ?>" method="get">
            <label for="sidebar-search">Keywords</label>
            <input id="sidebar-search" name="q" placeholder="Search posts">
            <button type="submit">Search</button>
        </form>
        <h2>Categories</h2>
        <?php foreach ($categories as $category): ?>
            <a href="<?= e(url('category/' . $category['slug'])) ?>"><?= e($category['name']) ?></a><br>
        <?php endforeach; ?>
        <h2>Popular Posts</h2>
        <?php foreach ($popular as $popularPost): ?>
            <a href="<?= e(url('post/' . $popularPost['slug'])) ?>"><?= e($popularPost['title']) ?></a><br>
        <?php endforeach; ?>
    </aside>
</div>
<div class="grid"><section><?php foreach($posts as $post): ?><article class="card"><h2><a href="<?= e(url('post/'.$post['slug'])) ?>"><?= e($post['title']) ?></a></h2><p><?= e($post['short_description'] ?: excerpt($post['content'])) ?></p><small><?= e($post['category_name'] ?? 'Uncategorized') ?> · <?= e($post['publish_at']) ?></small></article><?php endforeach; ?><?= $pagination ?></section><aside class="card"><h3>Search</h3><form action="<?= e(url('search')) ?>"><input name="q" placeholder="Search posts"></form><h3>Categories</h3><?php foreach($categories as $c): ?><a href="<?= e(url('category/'.$c['slug'])) ?>"><?= e($c['name']) ?></a><br><?php endforeach; ?><h3>Popular</h3><?php foreach($popular as $p): ?><a href="<?= e(url('post/'.$p['slug'])) ?>"><?= e($p['title']) ?></a><br><?php endforeach; ?></aside></div>
