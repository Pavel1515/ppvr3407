<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$config = require __DIR__ . '/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (hash_equals((string)$config['username'], $username) && hash_equals((string)$config['password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Неверный логин или пароль.';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Вход в админку — Pavel.dev</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <h1>Вход в админку</h1>
    <p>Управление проектами и постами блога.</p>
    <?php if ($error): ?><div class="login-error"><?= esc($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label for="username">Логин</label>
        <input id="username" type="text" name="username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input id="password" type="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Войти</button>
    </form>
  </div>
</div>
</body>
</html>
