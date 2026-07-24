<?php
require __DIR__ . '/includes/bootstrap.php';
$name=basename($_GET['file'] ?? ''); $path=config('uploads.path').'/'.$name; if(!$name||!is_file($path)) error_page(404,'Not Found','Media not found.'); $mime=mime_content_type($path); if(!in_array($mime,config('uploads.allowed_mime'),true)) error_page(403,'Forbidden','Invalid media.'); header('Content-Type: '.$mime); readfile($path);
