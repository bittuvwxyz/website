<?php
declare(strict_types=1);

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