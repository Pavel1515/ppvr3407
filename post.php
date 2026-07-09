<?php
require_once __DIR__ . '/inc/functions.php';

$posts = read_json_file('posts.json');
$slug = trim((string)($_GET['slug'] ?? ''));
$post = $slug !== '' ? find_by_slug($posts, $slug) : null;

if (!$post) {
  http_response_code(404);
}

$pageTitle = $post ? ($post['seo_title'] ?: $post['title']) . ' — Pavel.dev' : 'Статья не найдена — Pavel.dev';
$pageDescription = $post ? ($post['seo_description'] ?: $post['excerpt']) : 'Запрошенная статья не найдена.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle) ?></title>
  <meta name="description" content="<?= esc($pageDescription) ?>">
  <?php if ($post): ?>
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= esc($post['seo_title'] ?: $post['title']) ?>">
    <meta property="og:description" content="<?= esc($post['seo_description'] ?: $post['excerpt']) ?>">
    <?php if (!empty($post['image'])): ?>
      <meta property="og:image" content="<?= esc($post['image']) ?>"><?php endif; ?>
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="assets/favicon.ico">
  <link rel="apple-touch-icon" href="assets/favicon-64.png">
</head>

<body>
  <?php $navBase = 'index.php';
  include __DIR__ . '/inc/header.php'; ?>

  <?php if (!$post): ?>
    <section>
      <div class="not-found">
        <h1>404</h1>
        <p>Такой статьи не существует — возможно, её удалили или ссылка устарела.</p>
        <a href="blog.php" class="btn btn-primary">Ко всем статьям</a>
      </div>
    </section>
  <?php else: ?>

    <section style="padding-bottom:0;">
      <div class="entry-hero reveal">
        <a href="blog.php" class="entry-back">← Ко всем статьям</a>
        <span class="eyebrow">Блог</span>
        <h1><?= esc($post['title']) ?></h1>
        <div class="entry-meta"><?= esc(format_date_ru($post['created_at'])) ?></div>
      </div>
      <?php if (!empty($post['image'])): ?>
        <div class="entry-image reveal">
          <img src="<?= esc($post['image']) ?>" alt="<?= esc($post['title']) ?>">
        </div>
      <?php endif; ?>
    </section>

    <section>
      <div class="entry-body reveal">
        <?= nl2p($post['content'] ?? '') ?>
      </div>
    </section>

    <section>
      <div class="cta-band reveal">
        <h2>Понравилась статья?</h2>
        <p>Обсудим ваш проект — отвечу в течение нескольких часов.</p>
        <a href="index.php#contact" class="btn btn-primary">Оставить заявку →</a>
      </div>
    </section>

  <?php endif; ?>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>