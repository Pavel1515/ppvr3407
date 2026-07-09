<?php
require_once __DIR__ . '/auth.php';
require_login();

$projects = read_json_file('projects.json');
$posts = read_json_file('posts.json');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Админка — Pavel.dev</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-header">
  <div class="admin-logo">Pavel<span>.dev</span> · Админка</div>
  <div class="admin-nav">
    <a href="index.php" class="active">Дашборд</a>
    <a href="projects.php">Проекты</a>
    <a href="posts.php">Посты</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Добро пожаловать, Павел</h1>
  </div>
  <div class="admin-list">
    <a href="projects.php" class="admin-item">
      <div class="admin-item-body">
        <h3>Проекты портфолио</h3>
        <p><?= count($projects) ?> проект(ов) · SEO-тайтлы и описания для каждой страницы</p>
      </div>
      <div class="admin-item-actions"><span class="btn btn-ghost btn-sm">Открыть →</span></div>
    </a>
    <a href="posts.php" class="admin-item">
      <div class="admin-item-body">
        <h3>Посты блога</h3>
        <p><?= count($posts) ?> пост(ов) · SEO-тайтлы и описания для каждой статьи</p>
      </div>
      <div class="admin-item-actions"><span class="btn btn-ghost btn-sm">Открыть →</span></div>
    </a>
    <a href="../index.php" target="_blank" class="admin-item">
      <div class="admin-item-body">
        <h3>Открыть сайт</h3>
        <p>Посмотреть, как сайт выглядит для посетителей</p>
      </div>
      <div class="admin-item-actions"><span class="btn btn-ghost btn-sm">На сайт →</span></div>
    </a>
  </div>
</div>
</body>
</html>
