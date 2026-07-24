<?php
declare(strict_types=1);

return [
    'site_name' => getenv('SITE_NAME') ?: 'Procedural Blog CMS',
    'base_url' => rtrim(getenv('BASE_URL') ?: 'http://localhost', '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'website',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
    ],
    'smtp' => [
        'from_email' => getenv('MAIL_FROM') ?: 'noreply@example.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Procedural Blog CMS',
    ],
    'uploads' => [
        'path' => dirname(__DIR__) . '/storage/uploads',
        'public_proxy' => 'media.php',
        'max_size' => 2 * 1024 * 1024,
        'allowed_mime' => ['image/jpeg', 'image/png', 'image/webp'],
        'allowed_ext' => ['jpg', 'jpeg', 'png', 'webp'],
        'min_width' => 100,
        'min_height' => 100,
        'max_width' => 4000,
        'max_height' => 4000,
    ],
    'session_lifetime' => 1800,
    'pagination_limit' => 10,
    'security' => [
        'csrf_key' => getenv('CSRF_KEY') ?: 'change-this-long-random-secret',
        'verification_ttl' => 86400,
        'reset_ttl' => 3600,
        'login_window' => 900,
        'login_max_attempts' => 5,
        'lockout_minutes' => 15,
        'https_only' => filter_var(getenv('HTTPS_ONLY') ?: '0', FILTER_VALIDATE_BOOLEAN),
    ],
];