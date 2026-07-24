<?php
declare(strict_types=1);

function validate_username(string $username): ?string
{
    return preg_match('/^[A-Za-z0-9_]{3,30}$/', $username) ? null : 'Username must be 3-30 characters and may contain letters, numbers, and underscores.';
}

function validate_email_address(string $email): ?string
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address.';
}

function validate_password_pair(string $password, string $confirm): ?string
{
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }
    return password_ok($password) ? null : 'Password must be at least 8 characters and include uppercase, lowercase, and a number.';
}

function validate_slug_value(string $slug): ?string
{
    return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) ? null : 'Slug may contain lowercase letters, numbers, and single hyphens only.';
}

function validate_date_value(string $date): ?string
{
    $parts = explode('-', $date);
    return count($parts) === 3 && checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]) ? null : 'Enter a valid date.';
}

function validate_time_value(string $time): ?string
{
    return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) ? null : 'Enter a valid time.';
}

function validate_required(array $data, array $fields): array
{
    $errors = [];
    foreach ($fields as $field) {
        if (trim((string)($data[$field] ?? '')) === '') {
            $errors[$field] = 'This field is required.';
        }
    }
    return $errors;
}
