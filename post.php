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
<?php if (!empty($post['image'])): ?><meta property="og:image" content="<?= esc($post['image']) ?>"><?php endif; ?>
<?php endif; ?>
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
