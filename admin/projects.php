<?php
require_once __DIR__ . '/auth.php';
require_login();

$projects = read_json_file('projects.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $projects = array_values(array_filter($projects, fn($p) => (int)($p['id'] ?? 0) !== $id));
    write_json_file('projects.json', $projects);
    flash_set('Проект удалён.');
    header('Location: projects.php');
    exit;
}

usort($projects, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Проекты — Админка</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-header">
  <div class="admin-logo">Pavel<span>.dev</span> · Админка</div>
  <div class="admin-nav">
    <a href="index.php">Дашборд</a>
    <a href="projects.php" class="active">Проекты</a>
    <a href="posts.php">Посты</a>
    <a href="cities.php">Города</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Проекты портфолио</h1>
    <a href="project-form.php" class="btn btn-primary">+ Добавить проект</a>
  </div>
  <?php if ($flash): ?><div class="flash"><?= esc($flash) ?></div><?php endif; ?>

  <?php if (empty($projects)): ?>
  <div class="empty">Проектов пока нет. Добавьте первый.</div>
  <?php else: ?>
  <div class="admin-list">
    <?php foreach ($projects as $p): ?>
    <div class="admin-item">
      <img src="<?= esc('../' . ($p['image'] ?? '')) ?>" alt="">
      <div class="admin-item-body">
        <h3><?= esc($p['title'] ?? '') ?></h3>
        <p><?= esc($p['category'] ?? '') ?> · /project.php?slug=<?= esc($p['slug'] ?? '') ?></p>
      </div>
      <div class="admin-item-actions">
        <a href="<?= esc('../project.php?slug=' . urlencode($p['slug'] ?? '')) ?>" target="_blank" class="btn btn-ghost btn-sm">Смотреть</a>
        <a href="<?= esc('project-form.php?id=' . (int)($p['id'] ?? 0)) ?>" class="btn btn-ghost btn-sm">Изменить</a>
        <form method="POST" onsubmit="return confirm('Удалить проект «<?= esc(addslashes($p['title'] ?? '')) ?>»?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)($p['id'] ?? 0) ?>">
          <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
          <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
