<?php
require_once __DIR__ . '/auth.php';
require_login();

$posts = read_json_file('posts.json');
$editing = null;
if (!empty($_GET['id'])) {
    $editing = find_by_id($posts, (int)$_GET['id']);
}

$errors = [];
$values = $editing ?? [
    'title' => '', 'slug' => '', 'excerpt' => '', 'content' => '',
    'image' => '', 'seo_title' => '', 'seo_description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $values = [
        'title' => trim((string)($_POST['title'] ?? '')),
        'slug' => trim((string)($_POST['slug'] ?? '')),
        'excerpt' => trim((string)($_POST['excerpt'] ?? '')),
        'content' => trim((string)($_POST['content'] ?? '')),
        'seo_title' => trim((string)($_POST['seo_title'] ?? '')),
        'seo_description' => trim((string)($_POST['seo_description'] ?? '')),
        'image' => $editing['image'] ?? '',
    ];

    if ($values['title'] === '') $errors[] = 'Укажите заголовок поста.';
    if ($values['content'] === '') $errors[] = 'Добавьте текст поста.';

    if (empty($errors)) {
        $slugBase = slugify($values['slug'] !== '' ? $values['slug'] : $values['title']);
        $slug = unique_slug($posts, $slugBase, $id);
        $existingImage = $editing['image'] ?? '';
        $image = handle_image_upload('image', $existingImage);

        if ($id) {
            foreach ($posts as &$p) {
                if ((int)($p['id'] ?? 0) === $id) {
                    $p = array_merge($p, $values, ['slug' => $slug, 'image' => $image ?: '']);
                    break;
                }
            }
            unset($p);
        } else {
            $posts[] = array_merge($values, [
                'id' => next_id($posts),
                'slug' => $slug,
                'image' => $image ?: '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        write_json_file('posts.json', $posts);
        flash_set($id ? 'Пост обновлён.' : 'Пост добавлен.');
        header('Location: posts.php');
        exit;
    }
}

$pageTitle = $editing ? 'Редактировать пост' : 'Новый пост';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($pageTitle) ?> — Админка</title>
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
    <h1><?= esc($pageTitle) ?></h1>
    <a href="posts.php" class="btn btn-ghost">← К списку</a>
  </div>

  <?php if ($errors): ?>
  <div class="flash" style="background:rgba(255,90,90,0.1); border-color:rgba(255,90,90,0.35); color:#ff8b8b;">
    <?= esc(implode(' ', $errors)) ?>
  </div>
  <?php endif; ?>

  <form class="card" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label for="title">Заголовок</label>
        <input id="title" type="text" name="title" required value="<?= esc($values['title']) ?>">
      </div>
      <div class="form-group">
        <label for="slug">URL-адрес (slug)</label>
        <input id="slug" type="text" name="slug" placeholder="оставьте пустым — сгенерируется само" value="<?= esc($values['slug']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="image">Изображение</label>
      <?php if (!empty($values['image'])): ?>
      <div class="current-image"><img src="<?= esc('../' . $values['image']) ?>" alt=""><span class="hint">Текущее изображение — загрузите новое, чтобы заменить</span></div>
      <?php endif; ?>
      <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
    </div>

    <div class="form-group">
      <label for="excerpt">Краткое описание (для карточки в блоге)</label>
      <textarea id="excerpt" name="excerpt" rows="2"><?= esc($values['excerpt']) ?></textarea>
    </div>

    <div class="form-group">
      <label for="content">Текст поста</label>
      <textarea id="content" name="content" rows="10" required><?= esc($values['content']) ?></textarea>
      <span class="hint">Разделяйте абзацы пустой строкой.</span>
    </div>

    <div class="seo-box">
      <h4>SEO</h4>
      <div class="form-group">
        <label for="seo_title">SEO-тайтл</label>
        <input id="seo_title" type="text" name="seo_title" placeholder="если пусто — используется заголовок поста" value="<?= esc($values['seo_title']) ?>">
      </div>
      <div class="form-group">
        <label for="seo_description">SEO-дескрипшн</label>
        <textarea id="seo_description" name="seo_description" rows="2" placeholder="если пусто — используется краткое описание"><?= esc($values['seo_description']) ?></textarea>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="posts.php" class="btn btn-ghost">Отмена</a>
    </div>
  </form>
</div>
</body>
</html>
