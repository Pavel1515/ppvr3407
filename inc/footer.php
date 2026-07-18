<?php
// Expects $navBase to already be set (see header.php).
$navBase = $navBase ?? '/';
?>
<footer>
  <div class="footer-grid">
    <div class="footer-col">
      <a href="/" class="logo">Pavel<span>.dev</span></a>
      <p>Веб-разработчик · WordPress · Elementor · ИИ</p>
    </div>
    <div class="footer-col">
      <h4>Навигация</h4>
      <a href="<?= $navBase ?>#services">Услуги</a>
      <a href="<?= $navBase ?>#portfolio">Работы</a>
      <a href="/blog">Блог</a>
      <a href="<?= $navBase ?>#pricing">Цены</a>
    </div>
    <div class="footer-col">
      <h4>Контакты</h4>
      <a href="mailto:ppvr3407@gmail.com">ppvr3407@gmail.com</a>
      <a href="https://t.me/ppvr3407" target="_blank" rel="noreferrer">Telegram</a>
      <a href="tel:+380638868610">+380 63 886 8610</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 Pavel.dev. Все права защищены.</span>
    <span>Сделано с ❤️ и капелькой ИИ</span>
  </div>
</footer>

<div class="totop" id="totop">↑</div>

<script src="assets/main.js"></script>