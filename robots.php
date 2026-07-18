<?php
/* Динамический robots.txt — сам подставляет адрес карты сайта. */
header('Content-Type: text/plain; charset=utf-8');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$base   = $scheme . '://' . $host . $dir . '/';

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";       // админка — закрыта от индексации
echo "Disallow: /template.php\n"; // рекламный шаблон по городам — не индексируем
echo "\n";
echo "Sitemap: {$base}sitemap.xml\n";
