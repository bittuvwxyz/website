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
function validate_image_upload(array $file): array { if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)return [false,'Upload failed.']; if($file['size']>config('uploads.max_size'))return [false,'File is too large.']; $name=strtolower($file['name']); if(preg_match('/\.php/i',$name)||substr_count($name,'.')>1)return [false,'Invalid filename.']; $ext=pathinfo($name,PATHINFO_EXTENSION); if(!in_array($ext,config('uploads.allowed_ext'),true))return [false,'Unsupported extension.']; $info=getimagesize($file['tmp_name']); if(!$info||!in_array($info['mime'],config('uploads.allowed_mime'),true))return [false,'Invalid image MIME.']; [$w,$h]=$info; if($w<config('uploads.min_width')||$h<config('uploads.min_height')||$w>config('uploads.max_width')||$h>config('uploads.max_height'))return [false,'Invalid image dimensions.']; return [true,$ext]; }
function save_image_upload(array $file): ?string { [$ok,$ext]=validate_image_upload($file); if(!$ok)return null; $dir=config('uploads.path'); if(!is_dir($dir))mkdir($dir,0755,true); $name=bin2hex(random_bytes(16)).'.'.$ext; return move_uploaded_file($file['tmp_name'],$dir.'/'.$name)?$name:null; }
function delete_media(?string $name): void { if($name){ $path=config('uploads.path').'/'.basename($name); if(is_file($path))unlink($path); } }
