<?php
declare(strict_types=1);

function validate_image_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [false, 'Choose an image to upload.'];
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) {
        return [false, 'Upload failed. Please try again.'];
    }
    if (($file['size'] ?? 0) > config('uploads.max_size')) {
        return [false, 'File is too large.'];
    }
    $originalName = strtolower(basename((string)($file['name'] ?? '')));
    if (preg_match('/\.php\d*$/i', $originalName) || substr_count($originalName, '.') > 1) {
        return [false, 'Invalid filename.'];
    }
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    if (!in_array($extension, config('uploads.allowed_ext'), true)) {
        return [false, 'Unsupported image extension.'];
    }
    $info = getimagesize($file['tmp_name']);
    if (!$info || !in_array($info['mime'], config('uploads.allowed_mime'), true)) {
        return [false, 'Invalid image file.'];
    }
    [$width, $height] = $info;
    if ($width < config('uploads.min_width') || $height < config('uploads.min_height') || $width > config('uploads.max_width') || $height > config('uploads.max_height')) {
        return [false, 'Image dimensions are outside the allowed range.'];
    }
    return [true, $extension];
}

function save_image_upload(array $file, ?string &$error = null): ?string
{
    [$ok, $extensionOrError] = validate_image_upload($file);
    if (!$ok) {
        $error = $extensionOrError;
        return null;
    }
    $directory = config('uploads.path');
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.' . $extensionOrError;
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
        $error = 'Could not save uploaded file.';
        return null;
    }
    return $name;
}

function delete_media(?string $name): void
{
    if (!$name) {
        return;
    }
    $path = config('uploads.path') . '/' . basename($name);
    if (is_file($path)) {
        unlink($path);
    }
}
