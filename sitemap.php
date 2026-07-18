<?php
/* ============================================================
   Динамическая карта сайта (sitemap).
   Сам подтягивает: главную, блог, контакты, все проекты
   и все статьи из data/*.json.
   Открывается как sitemap.php (или sitemap.xml — см. .htaccess).
   Городские страницы template.php НЕ включаются: на них noindex.
   ============================================================ */
require_once __DIR__ . '/inc/functions.php';

header('Content-Type: application/xml; charset=utf-8');

// --- Базовый URL сайта (определяется автоматически) ---
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$base   = $scheme . '://' . $host . $dir . '/';

// Форматирует дату в формат sitemap (YYYY-MM-DD); пустое — сегодня опустим.
function sm_date(?string $dt): ?string {
    if (!$dt) return null;
    $ts = strtotime($dt);
    return $ts !== false ? date('Y-m-d', $ts) : null;
}

// Собираем список URL: [loc, lastmod|null, changefreq, priority]
$urls = [];

// Статические страницы
$urls[] = ['',            null, 'weekly',  '1.0']; // главная
$urls[] = ['blog.php',    null, 'weekly',  '0.7'];
$urls[] = ['contact.php', null, 'monthly', '0.5'];

// Проекты
$projects = read_json_file('projects.json');
foreach ($projects as $p) {
    if (empty($p['slug'])) continue;
    $urls[] = ['project.php?slug=' . rawurlencode($p['slug']), sm_date($p['updated_at'] ?? $p['created_at'] ?? null), 'monthly', '0.6'];
}

// Статьи блога
$posts = read_json_file('posts.json');
foreach ($posts as $post) {
    if (empty($post['slug'])) continue;
    $urls[] = ['post.php?slug=' . rawurlencode($post['slug']), sm_date($post['updated_at'] ?? $post['created_at'] ?? null), 'monthly', '0.6'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$loc, $lastmod, $changefreq, $priority]) {
    echo "  <url>\n";
    echo '    <loc>' . esc($base . $loc) . "</loc>\n";
    if ($lastmod) echo '    <lastmod>' . $lastmod . "</lastmod>\n";
    echo '    <changefreq>' . $changefreq . "</changefreq>\n";
    echo '    <priority>' . $priority . "</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>' . "\n";
