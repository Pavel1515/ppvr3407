<?php
require_once __DIR__ . '/auth.php';
require_login();

$posts = read_json_file('posts.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $posts = array_values(array_filter($posts, fn($p) => (int)($p['id'] ?? 0) !== $id));
    write_json_file('posts.json', $posts);
    flash_set('Пост удалён.');
    header('Location: posts.php');
    exit;
}

usort($posts, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Посты — Админка</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-header">
  <div class="admin-logo">Pavel<span>.dev</span> · Админка</div>
  <div class="admin-nav">
    <a href="index.php">Дашборд</a>
    <a href="projects.php">Проекты</a>
    <a href="posts.php" class="active">Посты</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Посты блога</h1>
    <a href="post-form.php" class="btn btn-primary">+ Добавить пост</a>
  </div>
  <?php if ($flash): ?><div class="flash"><?= esc($flash) ?></div><?php endif; ?>

  <?php if (empty($posts)): ?>
  <div class="empty">Постов пока нет. Добавьте первый.</div>
  <?php else: ?>
  <div class="admin-list">
    <?php foreach ($posts as $p): ?>
    <div class="admin-item">
      <img src="<?= esc('../' . ($p['image'] ?? '')) ?>" alt="">
      <div class="admin-item-body">
        <h3><?= esc($p['title'] ?? '') ?></h3>
        <p><?= esc(format_date_ru($p['created_at'] ?? '')) ?> · /post.php?slug=<?= esc($p['slug'] ?? '') ?></p>
      </div>
      <div class="admin-item-actions">
        <a href="<?= esc('../post.php?slug=' . urlencode($p['slug'] ?? '')) ?>" target="_blank" class="btn btn-ghost btn-sm">Смотреть</a>
        <a href="<?= esc('post-form.php?id=' . (int)($p['id'] ?? 0)) ?>" class="btn btn-ghost btn-sm">Изменить</a>
        <form method="POST" onsubmit="return confirm('Удалить пост «<?= esc(addslashes($p['title'] ?? '')) ?>»?');">
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
