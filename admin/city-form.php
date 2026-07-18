<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/city-lib.php';

function city_slug_clean(string $s): string {
    $s = strtolower(trim($s));
    return (string)preg_replace('/[^a-z0-9_-]/', '', $s);
}

$cities = read_json_file('cities.json');

// Какой город редактируем (пусто = создаём новый).
$origSlug = city_slug_clean((string)($_GET['slug'] ?? ''));
$editing  = ($origSlug !== '' && isset($cities[$origSlug]));
$cityData = $editing ? $cities[$origSlug] : [];

$errors  = [];
$slugVal = $origSlug;
$vals    = [];
foreach (CITY_FORM_FIELDS as $f => $meta) {
    $vals[$f] = $editing ? get_city_path($cityData, CITY_CSV_MAP[$f]) : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $origSlug = city_slug_clean((string)($_POST['orig_slug'] ?? ''));
    $editing  = ($origSlug !== '' && isset($cities[$origSlug]));
    $slugVal  = trim((string)($_POST['slug'] ?? ''));
    foreach (CITY_FORM_FIELDS as $f => $meta) {
        $vals[$f] = trim((string)($_POST[$f] ?? ''));
    }

    $newSlug = city_slug_clean($slugVal);
    if ($newSlug === '')          $errors[] = 'Укажите slug (адрес страницы, латиницей).';
    if ($vals['city_name'] === '') $errors[] = 'Укажите название города.';
    if ($newSlug !== '' && $newSlug !== $origSlug && isset($cities[$newSlug])) {
        $errors[] = 'Город с таким адресом уже есть: /city-' . $newSlug;
    }

    if (empty($errors)) {
        $base = $editing ? $cities[$origSlug] : [];      // сохраняем глубокие секции (услуги, тарифы, FAQ…)
        $data = build_city_from_flat($vals, $base);

        if ($editing && $origSlug !== $newSlug) {
            unset($cities[$origSlug]);                   // переименование slug
        }
        $cities[$newSlug] = $data;

        if (write_json_file('cities.json', $cities)) {
            flash_set($editing ? 'Город обновлён.' : 'Город добавлен.');
            header('Location: cities.php');
            exit;
        }
        $errors[] = 'Не удалось записать data/cities.json (проверьте права на запись).';
    }
}

$pageTitle = $editing ? 'Редактировать город' : 'Новый город';
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
    <a href="posts.php">Посты</a>
    <a href="cities.php" class="active">Города</a>
    <a href="leads.php">Заявки</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1><?= esc($pageTitle) ?></h1>
    <a href="cities.php" class="btn btn-ghost">← К списку</a>
  </div>

  <?php if ($errors): ?>
  <div class="flash" style="background:rgba(255,90,90,0.1); border-color:rgba(255,90,90,0.35); color:#ff8b8b;">
    <?= esc(implode(' ', $errors)) ?>
  </div>
  <?php endif; ?>

  <form class="card" method="POST">
    <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
    <input type="hidden" name="orig_slug" value="<?= esc($origSlug) ?>">

    <div class="form-group">
      <label for="slug">Адрес страницы (slug)</label>
      <input id="slug" type="text" name="slug" required value="<?= esc($slugVal) ?>"
             placeholder="например: kyiv">
      <span class="hint">Только латиница, цифры, «-» и «_». Итоговый адрес: <code>/city-<?= esc(city_slug_clean($slugVal) ?: 'slug') ?></code>. При изменении меняется и ссылка на страницу.</span>
    </div>

    <?php foreach (CITY_FORM_FIELDS as $f => $meta): ?>
    <div class="form-group">
      <label for="<?= esc($f) ?>"><?= esc($meta['label']) ?></label>
      <?php if ($meta['type'] === 'textarea'): ?>
        <textarea id="<?= esc($f) ?>" name="<?= esc($f) ?>" rows="2"><?= esc($vals[$f]) ?></textarea>
      <?php else: ?>
        <input id="<?= esc($f) ?>" type="text" name="<?= esc($f) ?>" value="<?= esc($vals[$f]) ?>">
      <?php endif; ?>
      <?php if (!empty($meta['hint'])): ?><span class="hint"><?= esc($meta['hint']) ?></span><?php endif; ?>
    </div>
    <?php endforeach; ?>

    <p class="hint" style="margin-bottom:1rem;">Пустые поля не сохраняются — на странице для них останется «рыба» с автоподстановкой названия города. Глубокие блоки (услуги, тарифы, отзывы, FAQ), заданные через XML-импорт, сохраняются без изменений.</p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="cities.php" class="btn btn-ghost">Отмена</a>
    </div>
  </form>
</div>
</body>
</html>
