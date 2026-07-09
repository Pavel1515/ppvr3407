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
<title><?= esc($pageTitle) ?></title>
<meta name="description" content="<?= esc($pageDescription) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="glow"></div>
<div class="scroll-progress" id="scrollProgress"></div>
<div class="cursor-glow" id="cursorGlow"></div>
<div class="cursor-ring" id="cursorRing"></div>
<div class="cursor-dot" id="cursorDot"></div>

<header>
  <nav class="nav wrap">
    <div class="logo">Pavel<span>.dev</span></div>
    <div class="nav-links">
      <a href="index.php#services">Услуги</a>
      <a href="index.php#about">Обо мне</a>
      <a href="index.php#portfolio">Работы</a>
      <a href="blog.php">Блог</a>
      <a href="index.php#pricing">Цены</a>
      <a href="index.php#faq">Вопросы</a>
    </div>
    <a href="index.php#contact" class="nav-cta">Обсудить проект</a>
    <button class="burger" id="burger" aria-label="Меню"><span></span><span></span><span></span></button>
  </nav>
</header>

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
    <a href="post.php?slug=<?= esc($post['slug'] ?? '') ?>" class="blog-card reveal">
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

<footer>
  <div class="footer-grid">
    <div class="footer-col">
      <div class="logo">Pavel<span>.dev</span></div>
      <p>Веб-разработчик · WordPress · Elementor · ИИ</p>
    </div>
    <div class="footer-col">
      <h4>Навигация</h4>
      <a href="index.php#services">Услуги</a>
      <a href="index.php#portfolio">Работы</a>
      <a href="blog.php">Блог</a>
      <a href="index.php#pricing">Цены</a>
    </div>
    <div class="footer-col">
      <h4>Контакты</h4>
      <a href="mailto:hello@ranked.net.au">hello@ranked.net.au</a>
      <a href="#">Telegram</a>
      <a href="#">WhatsApp</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 Pavel.dev. Все права защищены.</span>
    <span>Сделано с ❤️ и капелькой ИИ</span>
  </div>
</footer>

<div class="totop" id="totop">↑</div>

<script src="assets/main.js"></script>
</body>
</html>
