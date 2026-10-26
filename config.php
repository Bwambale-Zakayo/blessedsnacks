<?php
declare(strict_types=1);

/* ---------- Settings (edit these, or set environment variables) ---------- */
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'blessed_snaks');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('CURRENCY', 'UGX');
define('FREE_DELIVERY_OVER', 50000);   // free delivery when subtotal is at least this
define('DELIVERY_FEE', 3000);          // otherwise this flat fee applies

/* ---------- Session ---------- */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ---------- Database ---------- */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}

/* ---------- Helpers ---------- */
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(float|int|string $n): string
{
    return CURRENCY . ' ' . number_format((float)$n);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Stock clips are free-licence videos from Mixkit (mixkit.co), streamed from their CDN. */
function mixkit(int $id, string $kind = 'video'): string
{
    return $kind === 'poster'
        ? "https://assets.mixkit.co/videos/$id/$id-thumb-720-0.jpg"
        : "https://assets.mixkit.co/videos/$id/$id-720.mp4";
}

/** Only allow redirects to our own pages, e.g. "products.php?checkout=1". */
function safe_next(?string $next, string $fallback = 'products.php'): string
{
    $next = (string)$next;
    return preg_match('/^[a-z_]+\.php(\?[a-z0-9=&_]*)?$/i', $next) ? $next : $fallback;
}

/* ---------- Auth ---------- */
function current_user(): ?array
{
    static $user = false;
    if ($user === false) {
        $user = null;
        if (!empty($_SESSION['uid'])) {
            try {
                $st = db()->prepare('SELECT id, name, email, phone FROM users WHERE id = ?');
                $st->execute([(int)$_SESSION['uid']]);
                $user = $st->fetch() ?: null;
            } catch (Throwable $e) {
                $user = null;
            }
        }
    }
    return $user;
}

function require_login(string $next = 'products.php'): void
{
    if (!current_user()) {
        redirect('login.php?next=' . urlencode($next));
    }
}

function login_user(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/* ---------- Data ---------- */
function get_categories(): array
{
    try {
        return db()->query('SELECT id, name, slug FROM categories ORDER BY sort_order, name')->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function get_products(?int $limit = null, bool $featured_first = false): array
{
    try {
        $sql = 'SELECT p.id, p.name, p.description, p.price, p.image, p.badge,
                       c.slug AS category_slug, c.name AS category_name
                FROM products p JOIN categories c ON c.id = p.category_id
                WHERE p.is_active = 1
                ORDER BY ' . ($featured_first ? 'p.badge IS NULL, ' : '') . 'p.id';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        return db()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}
