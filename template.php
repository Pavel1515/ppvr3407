<?php
/* ============================================================
   ⚠️  ШАБЛОН (TEMPLATE) — НЕ РЕАЛЬНАЯ СТРАНИЦА!
   ------------------------------------------------------------
   Все тексты берутся из data/cities.json по параметру ?city=
   Пример:  template.php?city=kyiv
   Города массово заливаются XML-файлом через админку:
   admin/cities.php  (см. образец data/cities.xml).
   Чего нет в данных — показывается «рыбой».
   Токен {{city}} в любом тексте заменяется на название города.
   ============================================================ */
require_once __DIR__ . '/inc/functions.php';

$projects = read_json_file('projects.json');
usort($projects, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$projects = array_slice($projects, 0, 8);

$posts = read_json_file('posts.json');
usort($posts, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$latestPosts = array_slice($posts, 0, 3);

/* ---------- «Рыба»-заготовка: незаполненные поля останутся такой ---------- */
$default = [
  'city_name' => '{{city}}',
  'seo' => [
    'title'       => 'ШАБЛОН — Веб-разработчик в {{city}}',
    'description' => 'РЫБА-ОПИСАНИЕ для {{city}}. Замените перед публикацией.',
  ],
  'hero' => [
    'badge'             => 'РЫБА: открыт для проектов в {{city}}',
    'heading_prefix'    => 'Рыба-заголовок для ',
    'heading_highlight' => '{{city}}',
    'heading_suffix'    => 'Lorem ipsum dolor sit amet consectetur',
    'subtext'           => 'РЫБА: вводный текст для {{city}}. Lorem ipsum dolor sit amet.',
    'button_primary'    => 'Рыба-кнопка →',
    'button_secondary'  => 'Рыба-ссылка',
    'stats' => [
      ['number' => '0', 'suffix' => '+', 'label' => 'рыба-подпись'],
      ['number' => '0', 'suffix' => '+', 'label' => 'рыба-подпись'],
      ['number' => '0', 'suffix' => '%', 'label' => 'рыба-подпись'],
      ['number' => '0', 'suffix' => '',  'label' => 'рыба-подпись'],
    ],
  ],
  'services' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок услуг', 'subtext' => 'РЫБА: описание услуг.',
    'items' => [
      ['icon' => '🖥️', 'title' => 'Рыба-услуга 1', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🎨', 'title' => 'Рыба-услуга 2', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🤖', 'title' => 'Рыба-услуга 3', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🛒', 'title' => 'Рыба-услуга 4', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '⚡', 'title' => 'Рыба-услуга 5', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🔧', 'title' => 'Рыба-услуга 6', 'text' => 'Lorem ipsum dolor sit amet.'],
    ],
  ],
  'stack' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок стека',
    'items' => [
      ['short' => '01', 'name' => 'Рыба-инструмент'], ['short' => '02', 'name' => 'Рыба-инструмент'],
      ['short' => '03', 'name' => 'Рыба-инструмент'], ['short' => '04', 'name' => 'Рыба-инструмент'],
      ['short' => '05', 'name' => 'Рыба-инструмент'], ['short' => '06', 'name' => 'Рыба-инструмент'],
      ['short' => '07', 'name' => 'Рыба-инструмент'], ['short' => '08', 'name' => 'Рыба-инструмент'],
      ['short' => '09', 'name' => 'Рыба-инструмент'], ['short' => '10', 'name' => 'Рыба-инструмент'],
    ],
  ],
  'about' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок «обо мне» для {{city}}',
    'paragraph_1' => 'РЫБА: первый абзац о себе. Lorem ipsum dolor sit amet.',
    'paragraph_2' => 'РЫБА: второй абзац о себе. Lorem ipsum dolor sit amet.',
    'badge_experience' => '⭐ Рыба-бейдж', 'badge_ai' => '🤖 Рыба-бейдж',
    'tags' => ['Рыба-тег', 'Рыба-тег', 'Рыба-тег', 'Рыба-тег', 'Рыба-тег', 'Рыба-тег'],
  ],
  'benefits' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок преимуществ', 'subtext' => 'РЫБА: подводка.',
    'items' => [
      ['icon' => '⚡', 'title' => 'Рыба-преимущество 1', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '💰', 'title' => 'Рыба-преимущество 2', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🎯', 'title' => 'Рыба-преимущество 3', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['icon' => '🔍', 'title' => 'Рыба-преимущество 4', 'text' => 'Lorem ipsum dolor sit amet.'],
    ],
  ],
  'process' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок процесса', 'subtext' => 'РЫБА: подводка.',
    'steps' => [
      ['number' => '01', 'title' => 'Рыба-этап 1', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['number' => '02', 'title' => 'Рыба-этап 2', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['number' => '03', 'title' => 'Рыба-этап 3', 'text' => 'Lorem ipsum dolor sit amet.'],
      ['number' => '04', 'title' => 'Рыба-этап 4', 'text' => 'Lorem ipsum dolor sit amet.'],
    ],
  ],
  'portfolio' => ['label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок портфолио', 'subtext' => 'РЫБА: подводка.'],
  'reviews' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок отзывов',
    'items' => [
      ['text' => '«РЫБА: отзыв. Lorem ipsum dolor sit amet».', 'initial' => 'А', 'name' => 'Рыба Имя А.', 'position' => 'рыба-должность'],
      ['text' => '«РЫБА: отзыв. Lorem ipsum dolor sit amet».', 'initial' => 'Б', 'name' => 'Рыба Имя Б.', 'position' => 'рыба-должность'],
      ['text' => '«РЫБА: отзыв. Lorem ipsum dolor sit amet».', 'initial' => 'В', 'name' => 'Рыба Имя В.', 'position' => 'рыба-должность'],
    ],
  ],
  'pricing' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок цен', 'subtext' => 'РЫБА: подводка к тарифам для {{city}}.',
    'plans' => [
      ['badge' => '', 'highlighted' => false, 'title' => 'Рыба-тариф 1', 'description' => 'Рыба-описание', 'price' => 'от 000$', 'period' => '/ проект', 'features' => ['Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'Рыба-срок'], 'button' => 'Рыба-кнопка'],
      ['badge' => 'Рыба-метка', 'highlighted' => true, 'title' => 'Рыба-тариф 2', 'description' => 'Рыба-описание', 'price' => 'от 000$', 'period' => '/ проект', 'features' => ['Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'Рыба-срок'], 'button' => 'Рыба-кнопка'],
      ['badge' => '', 'highlighted' => false, 'title' => 'Рыба-тариф 3', 'description' => 'Рыба-описание', 'price' => 'от 000$', 'period' => '/ проект', 'features' => ['Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'Рыба-срок'], 'button' => 'Рыба-кнопка'],
      ['badge' => '', 'highlighted' => false, 'title' => 'Рыба-тариф 4', 'description' => 'Рыба-описание', 'price' => '000 грн', 'period' => '/ месяц', 'features' => ['Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum'], 'button' => 'Рыба-кнопка'],
    ],
  ],
  'blog' => ['label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок блога', 'subtext' => 'РЫБА: подводка.'],
  'call_to_action' => ['heading' => 'Рыба-призыв для {{city}}', 'text' => 'РЫБА: Lorem ipsum dolor sit amet.', 'button' => 'Рыба-кнопка ↓'],
  'faq' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок вопросов',
    'items' => [
      ['question' => 'Рыба-вопрос 1?', 'answer' => 'РЫБА: Lorem ipsum dolor sit amet.'],
      ['question' => 'Рыба-вопрос 2?', 'answer' => 'РЫБА: Lorem ipsum dolor sit amet.'],
      ['question' => 'Рыба-вопрос 3?', 'answer' => 'РЫБА: Lorem ipsum dolor sit amet.'],
      ['question' => 'Рыба-вопрос 4?', 'answer' => 'РЫБА: Lorem ipsum dolor sit amet.'],
    ],
  ],
  'contact' => [
    'label' => 'Рыба-надзаголовок', 'heading' => 'Рыба-заголовок контактов для {{city}}', 'subtext' => 'РЫБА: подводка к форме.',
    'form' => [
      'error'             => 'РЫБА: Пожалуйста, заполните имя, email и сообщение корректно.',
      'name_label'        => 'Имя',        'name_placeholder'    => 'Рыба-подсказка',
      'email_label'       => 'Email',      'email_placeholder'   => 'you@mail.com',
      'phone_label'       => 'Телефон',    'phone_placeholder'   => '+000 00 000 00 00',
      'service_label'     => 'Тип проекта',
      'service_options'   => ['Рыба-опция 1', 'Рыба-опция 2', 'Рыба-опция 3', 'Рыба-опция 4'],
      'message_label'     => 'Сообщение',  'message_placeholder' => 'Рыба-подсказка',
      'submit'            => 'Рыба-кнопка →',
      'note'              => 'РЫБА: Нажимая «Отправить», вы соглашаетесь на обработку персональных данных.',
    ],
    'contacts' => [
      ['label' => 'Email',    'value' => 'рыба@mail.com',     'link' => 'mailto:example@mail.com'],
      ['label' => 'Telegram', 'value' => '@рыба',             'link' => '#'],
      ['label' => 'Телефон',  'value' => '+000 00 000 0000',  'link' => 'tel:+000000000000'],
      ['label' => 'Ответ',    'value' => 'рыба-текст',        'link' => ''],
    ],
  ],
];

// Рекурсивно заменяет токены {{city}} / {{ГОРОД}} на название города во всех строках.
function tpl_city(mixed $v, string $city): mixed {
  if (is_array($v)) return array_map(fn($x) => tpl_city($x, $city), $v);
  return is_string($v) ? str_replace(['{{city}}', '{{ГОРОД}}'], $city, $v) : $v;
}

$cities   = read_json_file('cities.json');
$slug     = preg_replace('/[^a-z0-9_-]/i', '', $_GET['city'] ?? '');
$cityData = ($slug !== '' && isset($cities[$slug])) ? $cities[$slug] : [];

$c        = array_replace_recursive($default, $cityData);
$cityName = $c['city_name'] ?? '{{city}}';
$c        = tpl_city($c, $cityName);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= esc($c['seo']['title']) ?></title>
  <meta name="description" content="<?= esc($c['seo']['description']) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="assets/favicon.ico">
  <link rel="apple-touch-icon" href="assets/favicon-64.png">
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <!-- ⚠️ ШАБЛОН — тексты из data/cities.json. Открывать как template.php?city=slug -->
  <?php $navBase = '';
  include __DIR__ . '/inc/header.php'; ?>

  <section class="hero">
    <div class="hero-blob b1"></div>
    <div class="hero-blob b2"></div>
    <div class="badge"><span class="dot"></span> <?= esc($c['hero']['badge']) ?></div>
    <h1><?= esc($c['hero']['heading_prefix']) ?><span class="grad"><?= esc($c['hero']['heading_highlight']) ?></span><br><?= esc($c['hero']['heading_suffix']) ?></h1>
    <p><?= esc($c['hero']['subtext']) ?></p>
    <div class="hero-cta">
      <a href="#contact" class="btn btn-primary"><?= esc($c['hero']['button_primary']) ?></a>
      <a href="#portfolio" class="btn btn-ghost"><?= esc($c['hero']['button_secondary']) ?></a>
    </div>
    <div class="stats">
      <?php foreach ($c['hero']['stats'] as $s): ?>
      <div class="stat"><b data-count="<?= esc($s['number']) ?>" data-suffix="<?= esc($s['suffix']) ?>">0</b><span><?= esc($s['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="services">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['services']['label']) ?></span>
      <h2><?= esc($c['services']['heading']) ?></h2>
      <p><?= esc($c['services']['subtext']) ?></p>
    </div>
    <div class="services-grid">
      <?php foreach ($c['services']['items'] as $item): ?>
      <div class="service-card reveal">
        <div class="service-icon"><?= esc($item['icon']) ?></div>
        <h3><?= esc($item['title']) ?></h3>
        <p><?= esc($item['text']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="tools" id="tools">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow"><?= esc($c['stack']['label']) ?></span>
        <h2><?= esc($c['stack']['heading']) ?></h2>
      </div>
    </div>
    <div class="marquee reveal">
      <div class="marquee-track" id="marqueeTrack">
        <?php foreach ($c['stack']['items'] as $tool): ?>
        <div class="tool-chip"><b><?= esc($tool['short']) ?></b> <?= esc($tool['name']) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="about">
    <div class="about-grid">
      <div class="about-photo reveal">
        <div class="photo-blob"></div>
        <img src="images/profile.svg" alt="Фото — <?= esc($cityName) ?>" class="photo-main">
        <div class="float-badge badge-exp"><?= esc($c['about']['badge_experience']) ?></div>
        <div class="float-badge badge-ai"><?= esc($c['about']['badge_ai']) ?></div>
      </div>
      <div class="about-text reveal">
        <span class="eyebrow"><?= esc($c['about']['label']) ?></span>
        <h2><?= esc($c['about']['heading']) ?></h2>
        <p><?= esc($c['about']['paragraph_1']) ?></p>
        <p><?= esc($c['about']['paragraph_2']) ?></p>
        <div class="skill-tags">
          <?php foreach ($c['about']['tags'] as $tag): ?>
          <span><?= esc($tag) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <section id="why-ai">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['benefits']['label']) ?></span>
      <h2><?= esc($c['benefits']['heading']) ?></h2>
      <p><?= esc($c['benefits']['subtext']) ?></p>
    </div>
    <div class="services-grid">
      <?php foreach ($c['benefits']['items'] as $item): ?>
      <div class="service-card reveal">
        <div class="service-icon"><?= esc($item['icon']) ?></div>
        <h3><?= esc($item['title']) ?></h3>
        <p><?= esc($item['text']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="process">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['process']['label']) ?></span>
      <h2><?= esc($c['process']['heading']) ?></h2>
      <p><?= esc($c['process']['subtext']) ?></p>
    </div>
    <div class="process-grid">
      <?php foreach ($c['process']['steps'] as $step): ?>
      <div class="process-card reveal">
        <span class="process-num"><?= esc($step['number']) ?></span>
        <h3><?= esc($step['title']) ?></h3>
        <p><?= esc($step['text']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="portfolio">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['portfolio']['label']) ?></span>
      <h2><?= esc($c['portfolio']['heading']) ?></h2>
      <p><?= esc($c['portfolio']['subtext']) ?></p>
    </div>
    <div class="portfolio-grid">
      <?php foreach ($projects as $project): ?>
      <a href="project.php?slug=<?= esc($project['slug'] ?? '') ?>" class="portfolio-card reveal">
        <img src="<?= esc($project['image'] ?? '') ?>" alt="<?= esc($project['title'] ?? '') ?>" class="portfolio-img">
        <div class="portfolio-overlay"><span><?= esc($project['category'] ?? '') ?></span>
          <h3><?= esc($project['title'] ?? '') ?></h3>
        </div>
      </a>
      <?php endforeach; ?>
      <?php if (empty($projects)): ?>
      <p class="empty-state">РЫБА: проекты скоро появятся.</p>
      <?php endif; ?>
    </div>
  </section>

  <section>
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['reviews']['label']) ?></span>
      <h2><?= esc($c['reviews']['heading']) ?></h2>
    </div>
    <div class="test-grid">
      <?php foreach ($c['reviews']['items'] as $review): ?>
      <div class="test-card reveal">
        <div class="stars">★★★★★</div>
        <p><?= esc($review['text']) ?></p>
        <div class="test-author">
          <div class="avatar"><?= esc($review['initial']) ?></div>
          <div><b><?= esc($review['name']) ?></b><span><?= esc($review['position']) ?></span></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="pricing">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['pricing']['label']) ?></span>
      <h2><?= esc($c['pricing']['heading']) ?></h2>
      <p><?= esc($c['pricing']['subtext']) ?></p>
    </div>
    <div class="price-grid">
      <?php foreach ($c['pricing']['plans'] as $plan): ?>
      <div class="price-card <?= !empty($plan['highlighted']) ? 'featured' : '' ?> reveal">
        <?php if (!empty($plan['badge'])): ?><div class="price-tag"><?= esc($plan['badge']) ?></div><?php endif; ?>
        <h3><?= esc($plan['title']) ?></h3>
        <p class="desc"><?= esc($plan['description']) ?></p>
        <div class="price-amount"><?= esc($plan['price']) ?> <span><?= esc($plan['period']) ?></span></div>
        <ul class="price-feats">
          <?php foreach ($plan['features'] as $feature): ?>
          <li><?= esc($feature) ?></li>
          <?php endforeach; ?>
        </ul>
        <a href="#contact" class="price-btn"><?= esc($plan['button']) ?></a>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php if (!empty($latestPosts)): ?>
  <section id="blog">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['blog']['label']) ?></span>
      <h2><?= esc($c['blog']['heading']) ?></h2>
      <p><?= esc($c['blog']['subtext']) ?></p>
    </div>
    <div class="blog-grid">
      <?php foreach ($latestPosts as $post): ?>
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
  </section>
  <?php endif; ?>

  <section>
    <div class="cta-band reveal">
      <h2><?= esc($c['call_to_action']['heading']) ?></h2>
      <p><?= esc($c['call_to_action']['text']) ?></p>
      <a href="#contact" class="btn btn-primary"><?= esc($c['call_to_action']['button']) ?></a>
    </div>
  </section>

  <section id="faq">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['faq']['label']) ?></span>
      <h2><?= esc($c['faq']['heading']) ?></h2>
    </div>
    <div class="faq">
      <?php foreach ($c['faq']['items'] as $item): ?>
      <div class="faq-item reveal">
        <div class="faq-q"><span><?= esc($item['question']) ?></span><span class="plus">+</span></div>
        <div class="faq-a"><?= esc($item['answer']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="contact">
    <div class="section-head reveal">
      <span class="eyebrow"><?= esc($c['contact']['label']) ?></span>
      <h2><?= esc($c['contact']['heading']) ?></h2>
      <p><?= esc($c['contact']['subtext']) ?></p>
    </div>
    <div class="contact-wrap reveal">
      <form class="contact-form" action="contact.php" method="POST">
        <div class="form-error" id="formError"><?= esc($c['contact']['form']['error']) ?></div>
        <div class="form-row">
          <div class="form-group">
            <label for="f-name"><?= esc($c['contact']['form']['name_label']) ?></label>
            <input id="f-name" type="text" name="name" required placeholder="<?= esc($c['contact']['form']['name_placeholder']) ?>">
          </div>
          <div class="form-group">
            <label for="f-email"><?= esc($c['contact']['form']['email_label']) ?></label>
            <input id="f-email" type="email" name="email" required placeholder="<?= esc($c['contact']['form']['email_placeholder']) ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="f-phone"><?= esc($c['contact']['form']['phone_label']) ?></label>
            <input id="f-phone" type="tel" name="phone" placeholder="<?= esc($c['contact']['form']['phone_placeholder']) ?>">
          </div>
          <div class="form-group">
            <label for="f-service"><?= esc($c['contact']['form']['service_label']) ?></label>
            <select id="f-service" name="service">
              <?php foreach ($c['contact']['form']['service_options'] as $option): ?>
              <option value="<?= esc($option) ?>"><?= esc($option) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="f-message"><?= esc($c['contact']['form']['message_label']) ?></label>
          <textarea id="f-message" name="message" rows="5" required
            placeholder="<?= esc($c['contact']['form']['message_placeholder']) ?>"></textarea>
        </div>
        <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
        <button type="submit" class="btn btn-primary form-submit"><?= esc($c['contact']['form']['submit']) ?></button>
        <p class="form-note"><?= esc($c['contact']['form']['note']) ?></p>
      </form>
      <div class="contact-side">
        <?php foreach ($c['contact']['contacts'] as $card): ?>
        <div class="contact-card"><span><?= esc($card['label']) ?></span><?php if (!empty($card['link'])): ?><a href="<?= esc($card['link']) ?>"><?= esc($card['value']) ?></a><?php else: ?><b><?= esc($card['value']) ?></b><?php endif; ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>
