<?php
require_once __DIR__ . '/inc/functions.php';

$posts = read_json_file('posts.json');
usort($posts, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

$pageTitle = 'Блог — Павел, веб-разработчик';
$pageDescription = 'Статьи о веб-разработке, WordPress, Elementor и использовании ИИ в создании сайтов.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include __DIR__ . '/inc/analytics.php'; ?>
  <title><?= esc($pageTitle) ?></title>
  <meta name="description" content="<?= esc($pageDescription) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="assets/favicon.ico">
  <link rel="apple-touch-icon" href="assets/favicon-64.png">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="canonical" href="<?= esc(canonical_url(blog_url())) ?>">
</head>

<body>
  <?php $navBase = '/';
  include __DIR__ . '/inc/header.php'; ?>

  <section class="hero" style="padding-bottom:40px;">
    <div class="hero-blob b1"></div>
    <div class="hero-blob b2"></div>
    <span class="eyebrow">Блог</span>
    <h1 style="font-size:clamp(30px,5vw,52px);">Статьи о сайтах, WordPress и ИИ</h1>
    <p>Делюсь опытом веб-разработки, разбираю инструменты и рассказываю, как ускоряю работу с помощью ИИ.</p>
  </section>

  <section>
    <?php if (empty($posts)): ?>
      <p class="empty-state">Постов пока нет — совсем скоро здесь появятся статьи.</p>
    <?php else: ?>
      <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
          <a href="<?= esc(post_url($post['slug'] ?? '')) ?>" class="blog-card reveal">
            <img src="<?= esc($post['image'] ?? '') ?>" alt="<?= esc($post['title'] ?? '') ?>">
            <div class="blog-card-body">
              <span class="blog-date"><?= esc(format_date_ru($post['created_at'] ?? '')) ?></span>
              <h3><?= esc($post['title'] ?? '') ?></h3>
              <p><?= esc($post['excerpt'] ?? '') ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>