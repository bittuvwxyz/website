# Procedural PHP Blog CMS

A production-oriented PHP 8.2+ and MySQL 8 blog/CMS built with procedural PHP, MySQLi prepared statements, HTML5, CSS3, vanilla JavaScript, Apache rewrites, reusable helpers, and no Composer, OOP, framework, Bootstrap, or external dependency.

## Features

- Registration, email verification, login, logout, forgot/reset password, secure sessions, HTTPOnly/SameSite/optional HTTPS cookies, session timeout/regeneration, role-based authorization, login throttling, account lockout, and activity logging.
- Admin dashboard with user/post/category statistics, recent users, recent posts, role permissions, users listing, category CRUD with search, and post CRUD with draft/published status.
- Public homepage, single post pages, category pages, search, pagination helper, latest/popular/related posts, sidebars, breadcrumbs, SEO slugs, canonical URLs, Open Graph, Twitter Card, robots.txt, and friendly URLs.
- Media validation for JPG/JPEG/PNG/WEBP, MIME/extension/size/dimension checks, random filenames, PHP/double-extension prevention, storage outside public root, replacement, deletion, and proxy serving.
- Security helpers for CSRF, escaping, sanitization, prepared statements, XSS reduction, clickjacking headers, CSP, directory traversal prevention, one-time tokens, expiration checks, password hashing, password verification, logs, and custom error pages.

## Requirements

- PHP 8.2 or newer with `mysqli`, `mbstring`, and GD/image metadata support.
- MySQL 8 or newer.
- Apache with `mod_rewrite` and optional `mod_headers`.

## Installation

1. Upload the project to Apache hosting.
2. Create a MySQL database and import `database.sql`.
3. Copy or edit environment values for `SITE_NAME`, `BASE_URL`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`, `MAIL_FROM`, `MAIL_FROM_NAME`, `APP_TIMEZONE`, `HTTPS_ONLY`, and `CSRF_KEY`.
4. Ensure `logs/` and `storage/uploads/` are writable by PHP and keep `storage/` outside direct public access when possible.
5. Point the web root at this directory and confirm `.htaccess` rewrites requests to `index.php`.
6. Create an administrator manually by inserting a user with the `admin` role and a `password_hash()` generated password, then set `email_verified=1` and `status='active'`.

## Configuration Guide

All configuration lives in `config/config.php`. Set the base URL, timezone, SMTP sender, upload constraints, session lifetime, pagination limit, token expiration, lockout window, and HTTPS-only cookie behavior there or through environment variables.

## SMTP Setup

The default `send_email()` helper uses PHP `mail()` for shared-hosting compatibility. Configure your host's SMTP/sendmail integration or replace the helper internals with your provider's SMTP relay while preserving the same procedural function signature.

## Folder Permissions

- `logs/`: writable for application and PHP error logs.
- `storage/uploads/`: writable for validated media files and not browsable directly.
- Source directories should not be writable in production.

## Database Import

Run:

```bash
mysql -u YOUR_USER -p YOUR_DATABASE < database.sql
```

Every query in the application uses MySQLi prepared statements through helpers in `includes/db.php`.

## Security Notes

Use HTTPS in production and set `HTTPS_ONLY=1`. Change `CSRF_KEY`, restrict database privileges, keep backups encrypted, review logs, configure server-level rate limiting if available, and disable PHP execution in upload/storage directories.

## Deployment Guide

Deploy files, import the database, configure environment variables, set permissions, enable Apache rewrite/modules, create the first admin, test registration email, and verify error pages and media uploads.

## Backup & Restore

Back up the MySQL database with `mysqldump`, archive `storage/uploads/`, and retain `config` environment values securely. Restore by importing SQL, restoring uploads, and validating file permissions.
