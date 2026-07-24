<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_role(['admin', 'author', 'editor']);

$action = query('action') ?: 'dashboard';
$id = int_param('id');
$user = current_user();

if ($action === 'dashboard') {
    $stats = [
        'users' => db_one('SELECT COUNT(*) c FROM users')['c'] ?? 0,
        'posts' => db_one('SELECT COUNT(*) c FROM posts')['c'] ?? 0,
        'published' => db_one("SELECT COUNT(*) c FROM posts WHERE status = 'published'")['c'] ?? 0,
        'drafts' => db_one("SELECT COUNT(*) c FROM posts WHERE status = 'draft'")['c'] ?? 0,
        'categories' => db_one('SELECT COUNT(*) c FROM categories')['c'] ?? 0,
    ];
    $recentPosts = db_all('SELECT title, created_at FROM posts ORDER BY created_at DESC LIMIT 5');
    $recentUsers = db_all('SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5');
    render('admin/dashboard', compact('stats', 'recentPosts', 'recentUsers', 'user'));
} elseif ($action === 'categories') {
    require_role(['admin', 'editor']);
    $search = query('q');
    $cats = db_all('SELECT * FROM categories WHERE name LIKE ? ORDER BY name', 's', ['%' . $search . '%']);
    render('admin/categories', compact('cats'));
} elseif ($action === 'save_category') {
    require_role(['admin', 'editor']);
    verify_csrf();

    $name = input('name');
    if ($name === '') {
        flash('error', 'Category name is required.');
        redirect('admin/?action=categories');
    }

    $slugInput = input('slug');
    if ($slugInput !== '' && validate_slug_value($slugInput)) {
        flash('error', 'Category slug is invalid.');
        redirect('admin/?action=categories');
    }

    $slug = unique_slug('categories', $slugInput ?: $name, $id);
    $description = input('description');
    if ($id > 0) {
        db_exec('UPDATE categories SET name = ?, slug = ?, description = ?, updated_at = NOW() WHERE id = ?', 'sssi', [$name, $slug, $description, $id]);
    } else {
        db_insert('INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', 'sss', [$name, $slug, $description]);
    }
    redirect('admin/?action=categories', 'Category saved.');
} elseif ($action === 'delete_category') {
    require_role('admin');
    verify_csrf();
    db_exec('DELETE FROM categories WHERE id = ?', 'i', [$id]);
    redirect('admin/?action=categories', 'Category deleted.');
} elseif ($action === 'posts') {
    if (has_role('author')) {
        $posts = db_all('SELECT p.*, c.name category FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE p.user_id = ? ORDER BY p.created_at DESC', 'i', [(int)$user['id']]);
    } else {
        $posts = db_all('SELECT p.*, c.name category FROM posts p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC');
    }
    render('admin/posts', compact('posts'));
} elseif ($action === 'edit_post') {
    $post = $id ? db_one('SELECT * FROM posts WHERE id = ?', 'i', [$id]) : null;
    if ($post && has_role('author') && (int)$post['user_id'] !== (int)$user['id']) {
        error_page(403, 'Forbidden', 'Authors can edit own posts only.');
    }
    $categories = db_all('SELECT * FROM categories ORDER BY name');
    render('admin/post_form', compact('post', 'categories'));
} elseif ($action === 'save_post') {
    verify_csrf();

    $title = input('title');
    if ($title === '') {
        flash('error', 'Post title is required.');
        redirect('admin/?action=edit_post&id=' . $id);
    }

    $slug = unique_slug('posts', input('slug') ?: $title, $id);
    $image = clean_string($_POST['existing_image'] ?? '', 255) ?: null;
    if (!empty($_FILES['featured_image']['name'])) {
        $uploadError = null;
        $uploaded = save_image_upload($_FILES['featured_image'], $uploadError);
        if (!$uploaded) {
            flash('error', $uploadError ?: 'Featured image upload failed.');
            redirect('admin/?action=edit_post&id=' . $id);
        }
        $image = $uploaded;
    }

    $publishDate = input('publish_date') ?: date('Y-m-d');
    $publishTime = input('publish_time') ?: date('H:i');
    if (validate_date_value($publishDate) || validate_time_value($publishTime)) {
        flash('error', 'Enter a valid publish date and time.');
        redirect('admin/?action=edit_post&id=' . $id);
    }

    $status = in_array($_POST['status'] ?? 'draft', ['draft', 'published'], true) ? $_POST['status'] : 'draft';
    $data = [
        $title,
        $slug,
        int_param('category_id', 'post'),
        $image,
        input('short_description', FILTER_DEFAULT, 500),
        clean_string($_POST['content'] ?? '', 100000),
        input('meta_title', FILTER_DEFAULT, 255),
        input('meta_description', FILTER_DEFAULT, 255),
        input('meta_keywords', FILTER_DEFAULT, 255),
        $publishDate . ' ' . $publishTime . ':00',
        $status,
    ];

    if ($id > 0) {
        $post = db_one('SELECT * FROM posts WHERE id = ?', 'i', [$id]);
        if ($post && has_role('author') && (int)$post['user_id'] !== (int)$user['id']) {
            error_page(403, 'Forbidden', 'Authors can update own posts only.');
        }
        db_exec('UPDATE posts SET title = ?, slug = ?, category_id = ?, featured_image = ?, short_description = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, publish_at = ?, status = ?, updated_at = NOW() WHERE id = ?', 'ssissssssssi', [...$data, $id]);
    } else {
        db_insert('INSERT INTO posts (title, slug, category_id, featured_image, short_description, content, meta_title, meta_description, meta_keywords, publish_at, status, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', 'ssissssssssi', [...$data, (int)$user['id']]);
    }
    redirect('admin/?action=posts', 'Post saved.');
} elseif ($action === 'delete_post') {
    verify_csrf();
    $post = db_one('SELECT * FROM posts WHERE id = ?', 'i', [$id]);
    if ($post && has_role('author') && (int)$post['user_id'] !== (int)$user['id']) {
        error_page(403, 'Forbidden', 'Authors can delete own posts only.');
    }
    db_exec('DELETE FROM posts WHERE id = ?', 'i', [$id]);
    redirect('admin/?action=posts', 'Post deleted.');
} elseif ($action === 'users') {
    require_role('admin');
    $users = db_all('SELECT u.*, r.name role FROM users u JOIN roles r ON r.id = u.role_id ORDER BY u.created_at DESC');
    render('admin/users', compact('users'));
} else {
    error_page(404, 'Not Found', 'Unknown admin action.');
}
