<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../inc/functions.php';

function require_login(): void {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
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
