<?php
require_once __DIR__ . '/inc/functions.php';

$projects = read_json_file('projects.json');
$slug = trim((string)($_GET['slug'] ?? ''));
$project = $slug !== '' ? find_by_slug($projects, $slug) : null;

if (!$project) {
  http_response_code(404);
}

// Другие проекты (последние, кроме текущего).
$more = [];
if ($project) {
  $more = array_values(array_filter($projects, fn($p) => ($p['slug'] ?? '') !== ($project['slug'] ?? '')));
  usort($more, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
  $more = array_slice($more, 0, 3);
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
    <?php if (!empty($project['image'])): ?>
      <meta property="og:image" content="<?= esc($project['image']) ?>"><?php endif; ?>
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="assets/favicon.ico">
  <link rel="apple-touch-icon" href="assets/favicon-64.png">
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <?php $navBase = 'index.php';
  include __DIR__ . '/inc/header.php'; ?>

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
        <figure class="entry-image reveal">
          <img src="<?= esc($project['image']) ?>" alt="<?= esc($project['title']) ?>">
        </figure>
      <?php endif; ?>
    </section>

    <section>
      <article class="entry-body reveal">
        <?php if (!empty($project['summary'])): ?>
          <p class="entry-lead"><?= esc($project['summary']) ?></p>
        <?php endif; ?>
        <?= render_article($project['content'] ?? $project['summary'] ?? '') ?>
      </article>
    </section>

    <?php if (!empty($more)): ?>
    <section>
      <div class="entry-related reveal">
        <div class="entry-related-head">
          <span class="eyebrow">Портфолио</span>
          <h2>Ещё проекты</h2>
        </div>
        <div class="portfolio-grid">
          <?php foreach ($more as $p): ?>
            <a href="project.php?slug=<?= esc($p['slug'] ?? '') ?>" class="portfolio-card">
              <img src="<?= esc($p['image'] ?? '') ?>" alt="<?= esc($p['title'] ?? '') ?>" class="portfolio-img">
              <div class="portfolio-overlay">
                <span><?= esc($p['category'] ?? '') ?></span>
                <h3><?= esc($p['title'] ?? '') ?></h3>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section>
      <div class="cta-band reveal">
        <h2>Хотите похожий результат?</h2>
        <p>Обсудим ваш проект — отвечу в течение нескольких часов.</p>
        <a href="index.php#contact" class="btn btn-primary">Оставить заявку →</a>
      </div>
    </section>

  <?php endif; ?>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>