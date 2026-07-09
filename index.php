<?php
require_once __DIR__ . '/inc/functions.php';

$projects = read_json_file('projects.json');
usort($projects, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$projects = array_slice($projects, 0, 8);

$posts = read_json_file('posts.json');
usort($posts, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$latestPosts = array_slice($posts, 0, 3);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Павел — Веб-разработчик | Сайты под ключ</title>
  <meta name="description"
    content="Создаю сайты на WordPress, Elementor и с помощью ИИ более 5 лет. Быстро, красиво, с гарантией результата.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Unbounded:wght@500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <?php $navBase = '';
  include __DIR__ . '/inc/header.php'; ?>

  <section class="hero">
    <div class="hero-blob b1"></div>
    <div class="hero-blob b2"></div>
    <div class="badge"><span class="dot"></span> Открыт для новых проектов</div>
    <h1>Создаю сайты, которые <span class="grad">продают</span>,<br> а не просто существуют</h1>
    <p>Веб-разработчик с опытом 5+ лет. Совмещаю WordPress, Elementor и искусственный интеллект, чтобы делать сайты
      быстрее, дешевле и без потери качества.</p>
    <div class="hero-cta">
      <a href="#contact" class="btn btn-primary">Заказать сайт →</a>
      <a href="#portfolio" class="btn btn-ghost">Смотреть работы</a>
    </div>
    <div class="stats">
      <div class="stat"><b data-count="5" data-suffix="+">0</b><span>лет опыта</span></div>
      <div class="stat"><b data-count="120" data-suffix="+">0</b><span>проектов сдано</span></div>
      <div class="stat"><b data-count="98" data-suffix="%">0</b><span>довольных клиентов</span></div>
      <div class="stat"><b data-count="14" data-suffix="">0</b><span>дней в среднем на сайт</span></div>
    </div>
  </section>

  <section id="services">
    <div class="section-head reveal">
      <span class="eyebrow">Услуги</span>
      <h2>Чем я могу помочь</h2>
      <p>От простого лендинга до интернет-магазина — под ключ, с дизайном и техподдержкой.</p>
    </div>
    <div class="services-grid">
      <div class="service-card reveal">
        <div class="service-icon">🖥️</div>
        <h3>Сайты на WordPress</h3>
        <p>Гибкие, управляемые сайты на самой популярной CMS в мире. Легко редактировать самостоятельно после сдачи.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🎨</div>
        <h3>Вёрстка в Elementor</h3>
        <p>Быстрая и точная вёрстка по дизайну или референсам — адаптивно под любые экраны, без визуальных багов.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🤖</div>
        <h3>ИИ в разработке</h3>
        <p>Использую нейросети для текстов, изображений и ускорения разработки — быстрее в 2-3 раза без потери качества.
        </p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🛒</div>
        <h3>Интернет-магазины</h3>
        <p>WooCommerce под ключ: каталог, оплата, доставка, интеграции с CRM и складом.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">⚡</div>
        <h3>Оптимизация скорости</h3>
        <p>Ускоряю уже готовые сайты: кэш, сжатие, CDN — Core Web Vitals в зелёной зоне.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🔧</div>
        <h3>Поддержка и развитие</h3>
        <p>Веду проекты после сдачи: обновления, правки, новые разделы, безопасность.</p>
      </div>
    </div>
  </section>

  <section class="tools" id="tools">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Стек</span>
        <h2>С чем я работаю</h2>
      </div>
    </div>
    <div class="marquee reveal">
      <div class="marquee-track" id="marqueeTrack">
        <div class="tool-chip"><b>WP</b> WordPress</div>
        <div class="tool-chip"><b>El</b> Elementor Pro</div>
        <div class="tool-chip"><b>AI</b> ChatGPT / Claude</div>
        <div class="tool-chip"><b>Fig</b> Figma</div>
        <div class="tool-chip"><b>Woo</b> WooCommerce</div>
        <div class="tool-chip"><b>JS</b> JavaScript</div>
        <div class="tool-chip"><b>Git</b> Git / GitHub</div>
        <div class="tool-chip"><b>SEO</b> SEO-оптимизация</div>
        <div class="tool-chip"><b>PHP</b> PHP</div>
        <div class="tool-chip"><b>MySQL</b> Базы данных</div>
      </div>
    </div>
  </section>

  <section id="about">
    <div class="about-grid">
      <div class="about-photo reveal">
        <div class="photo-blob"></div>
        <img src="images/profile.svg" alt="Павел — веб-разработчик" class="photo-main">
        <div class="float-badge badge-exp">⭐ 5+ лет опыта</div>
        <div class="float-badge badge-ai">🤖 ИИ в работе</div>
      </div>
      <div class="about-text reveal">
        <span class="eyebrow">Обо мне</span>
        <h2>Павел — веб-разработчик и практик ИИ-инструментов</h2>
        <p>Более 5 лет делаю сайты для малого и среднего бизнеса: от лендингов до интернет-магазинов. Специализируюсь на
          WordPress и Elementor — это даёт клиентам гибкость управлять сайтом самостоятельно после сдачи проекта.</p>
        <p>Активно использую ИИ-инструменты на всех этапах — от генерации текстов и изображений до ускорения рутинной
          разработки. Это позволяет делать сайты быстрее без потери качества и держать цены разумными.</p>
        <div class="skill-tags">
          <span>WordPress</span>
          <span>Elementor</span>
          <span>WooCommerce</span>
          <span>ИИ-инструменты</span>
          <span>SEO</span>
          <span>UI/UX</span>
        </div>
      </div>
    </div>
  </section>

  <section id="why-ai">
    <div class="section-head reveal">
      <span class="eyebrow">Почему ИИ</span>
      <h2>Зачем я использую ИИ в разработке</h2>
      <p>Это не замена моей работе, а инструмент, который убирает рутину и ускоряет результат для вас.</p>
    </div>
    <div class="services-grid">
      <div class="service-card reveal">
        <div class="service-icon">⚡</div>
        <h3>Молниеносная скорость запуска</h3>
        <p>Генерация базовой структуры, первых текстов и дизайна за секунды, что сокращает время разработки в разы.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">💰</div>
        <h3>Снижение затрат</h3>
        <p>Меньше ручного труда — ниже итоговая стоимость проекта для клиента.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🎯</div>
        <h3>Персонализация</h3>
        <p>ИИ помогает создавать адаптивный контент под разные сегменты аудитории.</p>
      </div>
      <div class="service-card reveal">
        <div class="service-icon">🔍</div>
        <h3>SEO-оптимизация</h3>
        <p>Мгновенный подбор ключевых запросов и создание релевантных мета-тегов для быстрого старта в поиске.</p>
      </div>
    </div>
  </section>

  <section id="process">
    <div class="section-head reveal">
      <span class="eyebrow">Как я работаю</span>
      <h2>Процесс от заявки до запуска</h2>
      <p>Прозрачно и без сюрпризов — вы знаете, что происходит на каждом этапе.</p>
    </div>
    <div class="process-grid">
      <div class="process-card reveal">
        <span class="process-num">01</span>
        <h3>Заявка и брифинг</h3>
        <p>Обсуждаем цели, аудиторию и бюджет. Формируем ТЗ.</p>
      </div>
      <div class="process-card reveal">
        <span class="process-num">02</span>
        <h3>Дизайн и прототип</h3>
        <p>Собираю визуальную концепцию, согласовываем структуру.</p>
      </div>
      <div class="process-card reveal">
        <span class="process-num">03</span>
        <h3>Разработка</h3>
        <p>Верстаю и настраиваю сайт с ИИ-ускорением рутинных задач.</p>
      </div>
      <div class="process-card reveal">
        <span class="process-num">04</span>
        <h3>Тесты и запуск</h3>
        <p>Проверяю на всех устройствах, публикую и обучаю вас работе с сайтом.</p>
      </div>
    </div>
  </section>

  <section id="portfolio">
    <div class="section-head reveal">
      <span class="eyebrow">Портфолио</span>
      <h2>Избранные проекты</h2>
      <p>Несколько примеров того, что получают клиенты.</p>
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
        <p class="empty-state">Проекты скоро появятся.</p>
      <?php endif; ?>
    </div>
  </section>

  <section>
    <div class="section-head reveal">
      <span class="eyebrow">Отзывы</span>
      <h2>Что говорят клиенты</h2>
    </div>
    <div class="test-grid">
      <div class="test-card reveal">
        <div class="stars">★★★★★</div>
        <p>«Сайт сделали быстрее, чем обещали, и результат превзошёл ожидания. Заявки пошли уже на первой неделе».</p>
        <div class="test-author">
          <div class="avatar">М</div>
          <div><b>Марина К.</b><span>владелица студии интерьера</span></div>
        </div>
      </div>
      <div class="test-card reveal">
        <div class="stars">★★★★★</div>
        <p>«Наконец разработчик, который объясняет всё простым языком и не пропадает после оплаты. Рекомендую».</p>
        <div class="test-author">
          <div class="avatar">Д</div>
          <div><b>Дмитрий С.</b><span>основатель Fitlab</span></div>
        </div>
      </div>
      <div class="test-card reveal">
        <div class="stars">★★★★★</div>
        <p>«Перенесли магазин на WooCommerce без единого дня простоя. Конверсия выросла на 30%».</p>
        <div class="test-author">
          <div class="avatar">Е</div>
          <div><b>Елена В.</b><span>Bloom маркетплейс</span></div>
        </div>
      </div>
    </div>
  </section>

  <section id="pricing">
    <div class="section-head reveal">
      <span class="eyebrow">Цены</span>
      <h2>Прозрачные тарифы</h2>
      <p>Финальная стоимость зависит от объёма — точный расчёт после брифинга.</p>
    </div>
    <div class="price-grid">
      <div class="price-card reveal">
        <h3>Лендинг</h3>
        <p class="desc">Для презентации услуги или продукта</p>
        <div class="price-amount">от 350$ <span>/ проект</span></div>
        <ul class="price-feats">
          <li>1 страница, адаптивная вёрстка</li>
          <li>Дизайн на основе референсов</li>
          <li>Форма заявки + аналитика</li>
          <li>Срок: 5-7 дней</li>
        </ul>
        <a href="#contact" class="price-btn">Выбрать</a>
      </div>
      <div class="price-card featured reveal">
        <div class="price-tag">Популярный</div>
        <h3>Корпоративный сайт</h3>
        <p class="desc">Полноценное представительство компании</p>
        <div class="price-amount">от 800$ <span>/ проект</span></div>
        <ul class="price-feats">
          <li>До 8 страниц</li>
          <li>Индивидуальный дизайн</li>
          <li>SEO-настройка и скорость</li>
          <li>Срок: 10-14 дней</li>
        </ul>
        <a href="#contact" class="price-btn">Выбрать</a>
      </div>
      <div class="price-card reveal">
        <h3>Интернет-магазин</h3>
        <p class="desc">WooCommerce под ключ</p>
        <div class="price-amount">от 1400$ <span>/ проект</span></div>
        <ul class="price-feats">
          <li>Каталог и оплата онлайн</li>
          <li>Интеграция с доставкой/CRM</li>
          <li>Обучение работе с сайтом</li>
          <li>Срок: от 21 дня</li>
        </ul>
        <a href="#contact" class="price-btn">Выбрать</a>
      </div>
    </div>
  </section>

  <?php if (!empty($latestPosts)): ?>
    <section id="blog">
      <div class="section-head reveal">
        <span class="eyebrow">Блог</span>
        <h2>Последние статьи</h2>
        <p>Пишу о веб-разработке, WordPress, Elementor и использовании ИИ в работе.</p>
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
      <h2>Готовы начать свой проект?</h2>
      <p>Оставьте заявку в форме ниже — отвечу в течение нескольких часов.</p>
      <a href="#contact" class="btn btn-primary">Оставить заявку ↓</a>
    </div>
  </section>

  <section id="faq">
    <div class="section-head reveal">
      <span class="eyebrow">Вопросы</span>
      <h2>Частые вопросы</h2>
    </div>
    <div class="faq">
      <div class="faq-item reveal">
        <div class="faq-q"><span>Сколько времени занимает разработка сайта?</span><span class="plus">+</span></div>
        <div class="faq-a">В среднем от 5 до 21 дня в зависимости от сложности проекта. Точные сроки фиксируем в ТЗ до
          старта работ.</div>
      </div>
      <div class="faq-item reveal">
        <div class="faq-q"><span>Вы делаете дизайн сами или нужен дизайнер?</span><span class="plus">+</span></div>
        <div class="faq-a">Могу собрать дизайн сам на основе референсов и с помощью ИИ-инструментов, либо работать по
          готовому макету из Figma.</div>
      </div>
      <div class="faq-item reveal">
        <div class="faq-q"><span>Что будет после сдачи сайта?</span><span class="plus">+</span></div>
        <div class="faq-a">Обучаю вас редактировать сайт самостоятельно и предлагаю пакеты поддержки на выбор — от
          разовых правок до полного сопровождения.</div>
      </div>
      <div class="faq-item reveal">
        <div class="faq-q"><span>Работаете ли вы с оплатой поэтапно?</span><span class="plus">+</span></div>
        <div class="faq-a">Да, стандартно 50% предоплата на старте и 50% после сдачи проекта. Для крупных проектов
          возможна разбивка на этапы.</div>
      </div>
    </div>
  </section>

  <section id="contact">
    <div class="section-head reveal">
      <span class="eyebrow">Контакты</span>
      <h2>Обсудим ваш проект?</h2>
      <p>Заполните форму — отвечу в течение нескольких часов. Первая консультация бесплатна.</p>
    </div>
    <div class="contact-wrap reveal">
      <form class="contact-form" action="contact.php" method="POST">
        <div class="form-error" id="formError">Пожалуйста, заполните имя, email и сообщение корректно и попробуйте
          снова.</div>
        <div class="form-row">
          <div class="form-group">
            <label for="f-name">Имя</label>
            <input id="f-name" type="text" name="name" required placeholder="Как к вам обращаться">
          </div>
          <div class="form-group">
            <label for="f-email">Email</label>
            <input id="f-email" type="email" name="email" required placeholder="you@mail.com">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="f-phone">Телефон</label>
            <input id="f-phone" type="tel" name="phone" placeholder="+380 99 123 45 67">
          </div>
          <div class="form-group">
            <label for="f-service">Тип проекта</label>
            <select id="f-service" name="service">
              <option value="Лендинг">Лендинг</option>
              <option value="Корпоративный сайт">Корпоративный сайт</option>
              <option value="Интернет-магазин">Интернет-магазин</option>
              <option value="Другое">Другое</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="f-message">Сообщение</label>
          <textarea id="f-message" name="message" rows="5" required
            placeholder="Расскажите коротко о проекте"></textarea>
        </div>
        <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">
        <button type="submit" class="btn btn-primary form-submit">Отправить заявку →</button>
        <p class="form-note">Нажимая «Отправить», вы соглашаетесь на обработку персональных данных.</p>
      </form>
      <div class="contact-side">
        <div class="contact-card"><span>Email</span><a href="mailto:ppvr3407@gmail.com">ppvr3407@gmail.com</a></div>
        <div class="contact-card"><span>Telegram</span><a href="https://t.me/ppvr3407" target="_blank"
            rel="noreferrer">@ppvr3407</a></div>
        <div class="contact-card"><span>Телефон</span><a href="tel:+380638868610">+380 63 886 8610</a></div>
        <div class="contact-card"><span>Ответ</span><b>в течение нескольких часов</b></div>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/inc/footer.php'; ?>
</body>

</html>