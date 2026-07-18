<?php
require_once __DIR__ . '/inc/functions.php';

$posts = read_json_file('posts.json');
$slug = trim((string)($_GET['slug'] ?? ''));
$post = $slug !== '' ? find_by_slug($posts, $slug) : null;

if (!$post) {
  http_response_code(404);
}

// Похожие статьи (последние, кроме текущей).
$related = [];
if ($post) {
  $related = array_values(array_filter($posts, fn($p) => ($p['slug'] ?? '') !== ($post['slug'] ?? '')));
  usort($related, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
  $related = array_slice($related, 0, 3);
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
  <link rel="stylesheet" href="assets/style.css">
  <?php if ($post): ?><link rel="canonical" href="<?= esc(canonical_url(post_url($post['slug']))) ?>"><?php endif; ?>
</head>

<body>
  <?php $navBase = '/';
  include __DIR__ . '/inc/header.php'; ?>

  <?php if (!$post): ?>
    <section>
      <div class="not-found">
        <h1>404</h1>
        <p>Такой статьи не существует — возможно, её удалили или ссылка устарела.</p>
        <a href="<?= esc(blog_url()) ?>" class="btn btn-primary">Ко всем статьям</a>
      </div>
    </section>
  <?php else: ?>

    <section style="padding-bottom:0;">
      <div class="entry-hero reveal">
        <a href="<?= esc(blog_url()) ?>" class="entry-back">← Ко всем статьям</a>
        <span class="eyebrow">Блог</span>
        <h1><?= esc($post['title']) ?></h1>
        <div class="entry-meta">
          <span><?= esc(format_date_ru($post['created_at'])) ?></span>
          <span class="entry-meta-sep">·</span>
          <span><?= reading_time($post['content'] ?? '') ?> мин чтения</span>
        </div>
      </div>
      <?php if (!empty($post['image'])): ?>
        <figure class="entry-image reveal">
          <img src="<?= esc($post['image']) ?>" alt="<?= esc($post['title']) ?>">
        </figure>
      <?php endif; ?>
    </section>

    <section>
      <article class="entry-body reveal">
        <?php if (!empty($post['excerpt'])): ?>
          <p class="entry-lead"><?= esc($post['excerpt']) ?></p>
        <?php endif; ?>
        <?= render_article($post['content'] ?? '') ?>
      </article>
    </section>

    <?php if (!empty($related)): ?>
    <section>
      <div class="entry-related reveal">
        <div class="entry-related-head">
          <span class="eyebrow">Читать дальше</span>
          <h2>Похожие статьи</h2>
        </div>
        <div class="blog-grid">
          <?php foreach ($related as $r): ?>
            <a href="<?= esc(post_url($r['slug'] ?? '')) ?>" class="blog-card">
              <img src="<?= esc($r['image'] ?? '') ?>" alt="<?= esc($r['title'] ?? '') ?>">
              <div class="blog-card-body">
                <span class="blog-date"><?= esc(format_date_ru($r['created_at'] ?? '')) ?></span>
                <h3><?= esc($r['title'] ?? '') ?></h3>
                <p><?= esc($r['excerpt'] ?? '') ?></p>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section>
      <div class="cta-band reveal">
        <h2>Понравилась статья?</h2>
        <p>Обсудим ваш проект — отвечу в течение нескольких часов.</p>
        <a href="/#contact" class="btn btn-primary">Оставить заявку →</a>
      </div>
    </section>

  <?php endif; ?>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>