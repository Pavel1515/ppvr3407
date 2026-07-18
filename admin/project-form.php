<?php
require_once __DIR__ . '/auth.php';
require_login();

$projects = read_json_file('projects.json');
$editing = null;
if (!empty($_GET['id'])) {
    $editing = find_by_id($projects, (int)$_GET['id']);
}

$errors = [];
$values = $editing ?? [
    'title' => '', 'slug' => '', 'category' => '', 'summary' => '', 'content' => '',
    'client' => '', 'year' => '', 'image' => '', 'seo_title' => '', 'seo_description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $values = [
        'title' => trim((string)($_POST['title'] ?? '')),
        'slug' => trim((string)($_POST['slug'] ?? '')),
        'category' => trim((string)($_POST['category'] ?? '')),
        'summary' => trim((string)($_POST['summary'] ?? '')),
        'content' => trim((string)($_POST['content'] ?? '')),
        'client' => trim((string)($_POST['client'] ?? '')),
        'year' => trim((string)($_POST['year'] ?? '')),
        'seo_title' => trim((string)($_POST['seo_title'] ?? '')),
        'seo_description' => trim((string)($_POST['seo_description'] ?? '')),
        'image' => $editing['image'] ?? '',
    ];

    if ($values['title'] === '') $errors[] = 'Укажите название проекта.';

    if (empty($errors)) {
        $slugBase = slugify($values['slug'] !== '' ? $values['slug'] : $values['title']);
        $slug = unique_slug($projects, $slugBase, $id);
        $existingImage = $editing['image'] ?? '';
        $image = handle_image_upload('image', $existingImage);

        if ($id) {
            foreach ($projects as &$p) {
                if ((int)($p['id'] ?? 0) === $id) {
                    $p = array_merge($p, $values, ['slug' => $slug, 'image' => $image ?: '']);
                    break;
                }
            }
            unset($p);
        } else {
            $projects[] = array_merge($values, [
                'id' => next_id($projects),
                'slug' => $slug,
                'image' => $image ?: '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        write_json_file('projects.json', $projects);
        flash_set($id ? 'Проект обновлён.' : 'Проект добавлен.');
        header('Location: projects.php');
        exit;
    }
}

$pageTitle = $editing ? 'Редактировать проект' : 'Новый проект';
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
    <a href="projects.php" class="active">Проекты</a>
    <a href="posts.php">Посты</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1><?= esc($pageTitle) ?></h1>
    <a href="projects.php" class="btn btn-ghost">← К списку</a>
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
        <label for="title">Название проекта</label>
        <input id="title" type="text" name="title" required value="<?= esc($values['title']) ?>">
      </div>
      <div class="form-group">
        <label for="slug">URL-адрес (slug)</label>
        <input id="slug" type="text" name="slug" placeholder="оставьте пустым — сгенерируется само" value="<?= esc($values['slug']) ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="category">Категория (показывается на карточке)</label>
        <input id="category" type="text" name="category" placeholder="например: Лендинг · WordPress" value="<?= esc($values['category']) ?>">
      </div>
      <div class="form-group">
        <label for="image">Изображение</label>
        <?php if (!empty($values['image'])): ?>
        <div class="current-image"><img src="<?= esc('../' . $values['image']) ?>" alt=""><span class="hint">Текущее изображение — загрузите новое, чтобы заменить</span></div>
        <?php endif; ?>
        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="client">Клиент</label>
        <input id="client" type="text" name="client" value="<?= esc($values['client']) ?>">
      </div>
      <div class="form-group">
        <label for="year">Год</label>
        <input id="year" type="text" name="year" value="<?= esc($values['year']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="summary">Краткое описание</label>
      <textarea id="summary" name="summary" rows="2"><?= esc($values['summary']) ?></textarea>
    </div>

    <div class="form-group">
      <label for="content">Полное описание (страница проекта)</label>
      <textarea id="content" name="content" rows="7"><?= esc($values['content']) ?></textarea>
      <span class="hint">Абзацы — через пустую строку. Форматирование: <code>## Заголовок</code>, <code>### Подзаголовок</code>, <code>- пункт списка</code>, <code>1. нумерация</code>, <code>&gt; цитата</code>, <code>---</code> разделитель, <code>**жирный**</code>, <code>`код`</code>, <code>[текст](https://ссылка)</code>.</span>
    </div>

    <div class="seo-box">
      <h4>SEO</h4>
      <div class="form-group">
        <label for="seo_title">SEO-тайтл</label>
        <input id="seo_title" type="text" name="seo_title" placeholder="если пусто — используется название проекта" value="<?= esc($values['seo_title']) ?>">
      </div>
      <div class="form-group">
        <label for="seo_description">SEO-дескрипшн</label>
        <textarea id="seo_description" name="seo_description" rows="2" placeholder="если пусто — используется краткое описание"><?= esc($values['seo_description']) ?></textarea>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="projects.php" class="btn btn-ghost">Отмена</a>
    </div>
  </form>
</div>
</body>
</html>
