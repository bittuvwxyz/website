<?php
declare(strict_types=1);
function validate_username(string $u): ?string { return preg_match('/^[A-Za-z0-9_]{3,30}$/',$u)?null:'Username must be 3-30 letters, numbers, or underscores.'; }
function validate_email_address(string $e): ?string { return filter_var($e,FILTER_VALIDATE_EMAIL)?null:'Enter a valid email address.'; }
function validate_required(array $data, array $fields): array { $errors=[]; foreach($fields as $f) if(trim((string)($data[$f]??''))==='') $errors[$f]='This field is required.'; return $errors; }
function validate_password_pair(string $p, string $c): ?string { if($p!==$c)return 'Passwords do not match.'; return password_ok($p)?null:'Password must be 8+ chars with uppercase, lowercase, and number.'; }