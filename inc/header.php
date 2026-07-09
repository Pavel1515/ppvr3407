<?php
// Expects $navBase to be set by the including page:
//   '' on index.php (same-page anchors like "#services")
//   'index.php' on every other page (links back like "index.php#services")
$navBase = $navBase ?? 'index.php';
?>
<div class="glow"></div>
<div class="scroll-progress" id="scrollProgress"></div>
<div class="cursor-glow" id="cursorGlow"></div>
<div class="cursor-ring" id="cursorRing"></div>
<div class="cursor-dot" id="cursorDot"></div>

<header>
  <nav class="nav wrap">
    <a href="index.php" class="logo">Pavel<span>.dev</span></a>
    <div class="nav-links">
      <a href="index.php">Главная</a>
      <a href="<?= $navBase ?>#services">Услуги</a>
      <a href="<?= $navBase ?>#tools">Инструменты</a>
      <a href="<?= $navBase ?>#about">Обо мне</a>
      <a href="<?= $navBase ?>#portfolio">Работы</a>
      <a href="blog.php">Блог</a>
      <a href="<?= $navBase ?>#pricing">Цены</a>
      <a href="<?= $navBase ?>#faq">Вопросы</a>
    </div>
    <a href="<?= $navBase ?>#contact" class="nav-cta">Обсудить проект</a>
    <button class="burger" id="burger" aria-label="Меню"><span></span><span></span><span></span></button>
  </nav>
</header>
