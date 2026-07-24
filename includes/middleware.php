<?php
declare(strict_types=1);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'");
function method_allowed(array $methods): void { if(!in_array($_SERVER['REQUEST_METHOD'],$methods,true)) error_page(405,'Method Not Allowed','This HTTP method is not allowed here.'); }