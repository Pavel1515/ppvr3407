<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    // Detect HTTPS (directly or behind a proxy/load balancer)
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443)
        || (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');

    // Harden the session cookie: not readable from JS, HTTPS-only when available,
    // and never sent on cross-site requests.
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $isHttps,
        'samesite' => 'Strict',
    ]);
    session_name('pdadmin');
    session_start();
}

require_once __DIR__ . '/../inc/functions.php';

function require_login(): void {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }

    // Auto-logout after 2 hours of inactivity.
    $idleLimit = 2 * 60 * 60;
    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > $idleLimit) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = $now;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', is_string($token) ? $token : '')) {
        http_response_code(403);
        die('Сессия устарела. Обновите страницу и попробуйте снова.');
    }
}

function flash_set(string $message): void {
    $_SESSION['flash'] = $message;
}

function flash_get(): ?string {
    if (empty($_SESSION['flash'])) return null;
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}
