<?php
declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function clean_string(?string $value, int $max = 1000): string
{
    $value = trim((string)$value);
    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? '';
    return mb_substr($value, 0, $max);
}

function input(string $key, int $filter = FILTER_DEFAULT, int $max = 1000): string
{
    $value = filter_input(INPUT_POST, $key, $filter);
    return clean_string(is_string($value) ? $value : '', $max);
}

function query(string $key, int $filter = FILTER_DEFAULT, int $max = 1000): string
{
    $value = filter_input(INPUT_GET, $key, $filter);
    return clean_string(is_string($value) ? $value : '', $max);
}

function int_param(string $key, string $source = 'get', int $default = 0): int
{
    $inputType = $source === 'post' ? INPUT_POST : INPUT_GET;
    $value = filter_input($inputType, $key, FILTER_VALIDATE_INT);
    return is_int($value) ? $value : $default;
}

function redirect(string $path, string $message = '', string $type = 'success'): never
{
    if ($message !== '') {
        flash($type, $message);
    }
    header('Location: ' . url($path));
    exit;
}

function url(string $path = ''): string
{
    return config('base_url') . '/' . ltrim($path, '/');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf_token'] ?? ''))) {
        error_page(400, 'Bad Request', 'Your secure form token expired. Please go back and try again.');
    }
}

function random_token(): array
{
    $plain = bin2hex(random_bytes(32));
    return [$plain, hash('sha256', $plain)];
}

function slugify(string $text): string
{
    $text = strtolower(clean_string($text, 180));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: bin2hex(random_bytes(4));
}

function unique_slug(string $table, string $title, int $ignoreId = 0): string
{
    if (!in_array($table, ['posts', 'categories'], true)) {
        error_page(500, 'Server Error', 'Invalid slug target.');
    }
    $slug = slugify($title);
    $base = $slug;
    $counter = 2;
    while (db_one("SELECT id FROM {$table} WHERE slug = ? AND id <> ?", 'si', [$slug, $ignoreId])) {
        $slug = $base . '-' . $counter++;
    }
    return $slug;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function log_activity(?int $userId, string $action, string $detail = ''): void
{
    db_exec(
        'INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())',
        'isss',
        [$userId, clean_string($action, 100), clean_string($detail, 1000), $_SERVER['REMOTE_ADDR'] ?? '']
    );
}

function send_email(string $to, string $subject, string $html): bool
{
    $safeSubject = str_replace(["\r", "\n"], '', $subject);
    $headers = 'MIME-Version: 1.0' . "\r\n"
        . 'Content-type:text/html;charset=UTF-8' . "\r\n"
        . 'From: ' . config('smtp.from_name') . ' <' . config('smtp.from_email') . '>';
    return mail($to, $safeSubject, $html, $headers);
}

function render(string $template, array $data = []): void
{
    $file = dirname(__DIR__) . '/templates/' . $template . '.php';
    if (!is_file($file)) {
        error_page(500, 'Server Error', 'Template not found.');
    }
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/templates/header.php';
    require $file;
    require dirname(__DIR__) . '/templates/footer.php';
}

function error_page(int $code, string $title, string $message): never
{
    http_response_code($code);
    render('error', compact('code', 'title', 'message'));
    exit;
}

function pagination_base(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    unset($params['page']);
    $query = http_build_query($params);
    return strtok($_SERVER['REQUEST_URI'] ?? '/', '?') . ($query ? '?' . $query . '&' : '?');
}

function paginate(int $total, int $page, int $limit, string $base): string
{
    $pages = max(1, (int)ceil($total / max(1, $limit)));
    if ($pages < 2) {
        return '';
    }
    $html = '<nav class="pagination" aria-label="Pagination">';
    $links = ['First' => 1, 'Previous' => max(1, $page - 1)];
    for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++) {
        $links[(string)$i] = $i;
    }
    $links['Next'] = min($pages, $page + 1);
    $links['Last'] = $pages;
    foreach ($links as $label => $target) {
        $html .= '<a class="' . ($target === $page ? 'active' : '') . '" href="' . e($base . 'page=' . $target) . '">' . e($label) . '</a>';
    }
    return $html . '</nav>';
}

function excerpt(string $text, int $length = 160): string
{
    $plain = trim(strip_tags($text));
    return mb_strlen($plain) > $length ? mb_substr($plain, 0, $length) . '…' : $plain;
}
function config(string $key, mixed $default = null): mixed { $v=$GLOBALS['config']; foreach(explode('.',$key) as $p){ if(!is_array($v)||!array_key_exists($p,$v)) return $default; $v=$v[$p]; } return $v; }
function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function input(string $key, int $filter = FILTER_DEFAULT): ?string { $v = filter_input(INPUT_POST, $key, $filter); return is_string($v) ? trim($v) : null; }
function query(string $key, int $filter = FILTER_DEFAULT): ?string { $v = filter_input(INPUT_GET, $key, $filter); return is_string($v) ? trim($v) : null; }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function url(string $path = ''): string { return config('base_url') . '/' . ltrim($path, '/'); }
function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function csrf_token(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void { if(!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf_token'] ?? ''))) error_page(400, 'Bad Request', 'Invalid CSRF token.'); }
function random_token(): array { $plain=bin2hex(random_bytes(32)); return [$plain, hash('sha256',$plain)]; }
function slugify(string $text): string { $text=strtolower(trim($text)); $text=preg_replace('/[^a-z0-9]+/','-',$text); return trim((string)$text,'-') ?: bin2hex(random_bytes(4)); }
function unique_slug(string $table, string $title, int $ignoreId = 0): string { $slug=slugify($title); $base=$slug; $i=2; while(db_one("SELECT id FROM $table WHERE slug=? AND id<>?", 'si', [$slug,$ignoreId])) $slug=$base.'-'.$i++; return $slug; }
function log_activity(?int $userId, string $action, string $detail=''): void { db_exec('INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?,?,?,?,NOW())','isss',[$userId,$action,$detail,$_SERVER['REMOTE_ADDR'] ?? '']); }
function send_email(string $to, string $subject, string $html): bool { $headers='MIME-Version: 1.0' . "\r\n" . 'Content-type:text/html;charset=UTF-8' . "\r\n" . 'From: '.config('smtp.from_name').' <'.config('smtp.from_email').'>'; return mail($to,$subject,$html,$headers); }
function render(string $template, array $data=[]): void { extract($data); require dirname(__DIR__).'/templates/header.php'; require dirname(__DIR__).'/templates/'.$template.'.php'; require dirname(__DIR__).'/templates/footer.php'; }
function error_page(int $code, string $title, string $message): never { http_response_code($code); render('error', compact('code','title','message')); exit; }
function paginate(int $total, int $page, int $limit, string $base): string { $pages=max(1,(int)ceil($total/$limit)); if($pages<2)return ''; $h='<nav class="pagination">'; foreach(['First'=>1,'Previous'=>max(1,$page-1)] as $l=>$p)$h.='<a href="'.e($base.'page='.$p).'">'.$l.'</a>'; for($i=1;$i<=$pages;$i++)$h.='<a class="'.($i===$page?'active':'').'" href="'.e($base.'page='.$i).'">'.$i.'</a>'; foreach(['Next'=>min($pages,$page+1),'Last'=>$pages] as $l=>$p)$h.='<a href="'.e($base.'page='.$p).'">'.$l.'</a>'; return $h.'</nav>'; }
function excerpt(string $s, int $n=160): string { return mb_strlen(strip_tags($s))>$n ? mb_substr(strip_tags($s),0,$n).'…' : strip_tags($s); }
