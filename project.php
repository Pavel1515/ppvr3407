<?php
require_once __DIR__ . '/inc/functions.php';

$projects = read_json_file('projects.json');
$slug = trim((string)($_GET['slug'] ?? ''));
$project = $slug !== '' ? find_by_slug($projects, $slug) : null;

if (!$project) {
    http_response_code(404);
}

$pageTitle = $project ? ($project['seo_title'] ?: $project['title']) . ' — Pavel.dev' : 'Проект не найден — Pavel.dev';
$pageDescription = $project ? ($project['seo_description'] ?: $project['summary']) : 'Запрошенный проект не найден.';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($pageTitle) ?></title>
<meta name="description" content="<?= esc($pageDescription) ?>">
<?php if ($project): ?>
<meta property="og:type" content="article">
<meta property="og:title" content="<?= esc($project['seo_title'] ?: $project['title']) ?>">
<meta property="og:description" content="<?= esc($project['seo_description'] ?: $project['summary']) ?>">
<?php if (!empty($project['image'])): ?><meta property="og:image" content="<?= esc($project['image']) ?>"><?php endif; ?>
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

<?php if (!$project): ?>
<section>
  <div class="not-found">
    <h1>404</h1>
    <p>Такого проекта не существует — возможно, его удалили или ссылка устарела.</p>
    <a href="index.php#portfolio" class="btn btn-primary">К портфолио</a>
  </div>
</section>
<?php else: ?>

<section style="padding-bottom:0;">
  <div class="entry-hero reveal">
    <a href="index.php#portfolio" class="entry-back">← К портфолио</a>
    <span class="eyebrow"><?= esc($project['category']) ?></span>
    <h1><?= esc($project['title']) ?></h1>
    <?php if (!empty($project['client']) || !empty($project['year'])): ?>
    <div class="entry-facts">
      <?php if (!empty($project['client'])): ?><div><span>Клиент</span><b><?= esc($project['client']) ?></b></div><?php endif; ?>
      <?php if (!empty($project['year'])): ?><div><span>Год</span><b><?= esc($project['year']) ?></b></div><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($project['image'])): ?>
  <div class="entry-image reveal">
    <img src="<?= esc($project['image']) ?>" alt="<?= esc($project['title']) ?>">
  </div>
  <?php endif; ?>
</section>

<section>
  <div class="entry-body reveal">
    <?= nl2p($project['content'] ?? $project['summary'] ?? '') ?>
  </div>
</section>

<section>
  <div class="cta-band reveal">
    <h2>Хотите похожий результат?</h2>
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
