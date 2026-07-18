<?php
require_once __DIR__ . '/auth.php';
require_login();

/* ---------- Общая логика городов (карта полей + помощники) ---------- */
require_once __DIR__ . '/city-lib.php';

/**
 * Разбирает CSV в [slug => данные города].
 * Понимает разделители «;» и «,», BOM, строку «sep=;» и кодировку Windows-1251.
 * @return array{0: array<string,array>, 1: string[]} [города, ошибки]
 */
function parse_cities_csv(string $raw): array {
    $errors = [];
    if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) $raw = substr($raw, 3); // срезаем BOM
    if (!mb_check_encoding($raw, 'UTF-8')) $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1251');

    $fh = fopen('php://temp', 'r+');
    fwrite($fh, $raw);
    rewind($fh);

    $first = fgets($fh);
    if ($first === false) { fclose($fh); return [[], ['Файл пуст.']]; }

    $delim = ';';
    if (stripos(ltrim($first), 'sep=') === 0) {
        $sepChar = substr(trim($first), 4, 1);   // строку «sep=;» пропускаем, заголовок — следующая строка
        if ($sepChar !== '') $delim = $sepChar;
    } else {
        $delim = substr_count($first, ';') >= substr_count($first, ',') ? ';' : ',';
        rewind($fh);
    }

    $header = fgetcsv($fh, 0, $delim);
    if (!$header) { fclose($fh); return [[], ['Не найдена строка заголовков.']]; }
    $header = array_map(fn($h) => strtolower(trim((string)$h)), $header);

    $slugIdx = array_search('slug', $header, true);
    if ($slugIdx === false) { fclose($fh); return [[], ['В заголовке нет обязательной колонки "slug".']]; }

    $cities = [];
    $rowNum = 1;
    while (($row = fgetcsv($fh, 0, $delim)) !== false) {
        $rowNum++;
        if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) continue; // пустая строка

        $slug = strtolower(trim((string)($row[$slugIdx] ?? '')));
        $slug = (string)preg_replace('/[^a-z0-9_-]/', '', $slug);
        if ($slug === '') { $errors[] = "Строка {$rowNum}: пропущена — пустой или некорректный slug."; continue; }

        $data = [];
        foreach ($header as $i => $col) {
            if ($col === 'slug' || !array_key_exists($col, CITY_CSV_MAP)) continue;
            $val = trim((string)($row[$i] ?? ''));
            if ($val === '') continue;
            set_city_path($data, CITY_CSV_MAP[$col], $val);
        }
        $cities[$slug] = $data;
    }
    fclose($fh);

    if (empty($cities) && empty($errors)) $errors[] = 'В файле нет ни одной строки с данными.';
    return [$cities, $errors];
}

/* ---------- Скачивание CSV-шаблона ---------- */
if (($_GET['tpl'] ?? '') === 'csv') {
    $cols = array_merge(['slug'], array_keys(CITY_CSV_MAP));
    $examples = [
        ['kyiv', 'Киев', 'Веб-разработчик в Киеве | Сайты под ключ',
         'Создаю сайты в Киеве, которые приносят заявки и продажи. Замените на реальное описание.',
         'Открыт для новых проектов в Киеве', 'Создаю сайты в ', 'Киеве', ' которые помогают бизнесу расти',
         'Вводный текст для Киева. Замените на реальный.', 'Обо мне — веб-разработчик в Киеве',
         'Первый абзац о себе для Киева. Замените на реальный.', 'Второй абзац о себе. Замените на реальный.',
         'Прозрачные тарифы для Киева.', 'Готовы начать проект в Киеве?', 'Оставьте заявку — отвечу быстро.',
         'Обсудим ваш проект в Киеве?', 'Первая консультация бесплатна.'],
        ['lviv', 'Львов', 'Веб-разработчик во Львове | Сайты под ключ',
         'Создаю сайты во Львове, которые приносят заявки и продажи. Замените на реальное описание.',
         'Открыт для новых проектов во Львове', 'Создаю сайты во ', 'Львове', ' которые помогают бизнесу расти',
         'Вводный текст для Львова. Замените на реальный.', 'Обо мне — веб-разработчик во Львове',
         'Первый абзац о себе для Львова. Замените на реальный.', 'Второй абзац о себе. Замените на реальный.',
         'Прозрачные тарифы для Львова.', 'Готовы начать проект во Львове?', 'Оставьте заявку — отвечу быстро.',
         'Обсудим ваш проект во Львове?', 'Первая консультация бесплатна.'],
    ];
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="cities-template.csv"');
    echo "\xEF\xBB\xBF";   // BOM — чтобы Excel открыл кириллицу правильно
    echo "sep=;\r\n";       // подсказка Excel: разделитель — точка с запятой
    $out = fopen('php://output', 'w');
    fputcsv($out, $cols, ';');
    foreach ($examples as $row) fputcsv($out, $row, ';');
    fclose($out);
    exit;
}

/* ---------- XML -> массив (структура cities.json) ---------- */

// Теги, дети которых — это список (массив), а не объект.
const CITY_LIST_TAGS = ['stats', 'items', 'steps', 'plans', 'features', 'tags', 'service_options', 'contacts'];

function xml_node_to_array(SimpleXMLElement $node): mixed {
    if ($node->count() === 0) {
        return trim((string)$node); // лист — просто текст
    }
    if (in_array($node->getName(), CITY_LIST_TAGS, true)) {
        $list = [];
        foreach ($node->children() as $child) {
            $list[] = xml_node_to_array($child);
        }
        return $list;
    }
    $obj = [];
    foreach ($node->children() as $child) {
        $obj[$child->getName()] = xml_node_to_array($child);
    }
    return $obj;
}

// "true"/"1"/"да"/"yes" -> true, остальное -> false
function to_bool(mixed $v): bool {
    return in_array(strtolower(trim((string)$v)), ['1', 'true', 'yes', 'да', 'on'], true);
}

/**
 * Разбирает XML-строку в [slug => данные города].
 * @return array{0: array<string,array>, 1: string[]} [города, ошибки]
 */
function parse_cities_xml(string $raw): array {
    $errors = [];
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw);
    if ($xml === false) {
        foreach (libxml_get_errors() as $e) {
            $errors[] = 'Строка ' . $e->line . ': ' . trim($e->message);
        }
        libxml_clear_errors();
        return [[], $errors ?: ['Не удалось разобрать XML — проверьте структуру файла.']];
    }

    $cities = [];
    foreach ($xml->city as $cityNode) {
        $slug = strtolower(trim((string)$cityNode['slug']));
        $slug = preg_replace('/[^a-z0-9_-]/', '', $slug);
        if ($slug === '') {
            $errors[] = 'Пропущен город без корректного slug (атрибут slug="...").';
            continue;
        }

        $data = [];
        foreach ($cityNode->children() as $section) {
            $data[$section->getName()] = xml_node_to_array($section);
        }

        // Нормализуем булев флаг «выделенный тариф».
        if (!empty($data['pricing']['plans']) && is_array($data['pricing']['plans'])) {
            foreach ($data['pricing']['plans'] as &$plan) {
                if (isset($plan['highlighted'])) $plan['highlighted'] = to_bool($plan['highlighted']);
            }
            unset($plan);
        }

        $cities[$slug] = $data;
    }

    if (empty($cities) && empty($errors)) {
        $errors[] = 'В файле не найдено ни одного <city slug="...">.';
    }
    return [$cities, $errors];
}

/* ---------- Обработка загрузки ---------- */

$report = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'import') {
    csrf_check();

    $mode = ($_POST['mode'] ?? 'merge') === 'replace' ? 'replace' : 'merge';
    $raw  = '';

    if (!empty($_FILES['xml']['tmp_name']) && $_FILES['xml']['error'] === UPLOAD_ERR_OK) {
        $raw = (string)file_get_contents($_FILES['xml']['tmp_name']);
    } elseif (!empty($_POST['xml_text'])) {
        $raw = (string)$_POST['xml_text'];
    }

    if (trim($raw) === '') {
        $report = ['ok' => false, 'errors' => ['Загрузите XML-файл или вставьте XML в поле.'], 'imported' => []];
    } else {
        [$parsed, $errors] = parse_cities_xml($raw);
        if (!empty($parsed)) {
            $existing = read_json_file('cities.json');
            $result   = $mode === 'replace' ? $parsed : array_replace($existing, $parsed);
            $saved    = write_json_file('cities.json', $result);
            $report = [
                'ok'       => $saved,
                'errors'   => $saved ? $errors : array_merge($errors, ['Не удалось записать data/cities.json (проверьте права на запись).']),
                'imported' => array_keys($parsed),
                'mode'     => $mode,
            ];
            if ($saved) flash_set('Импортировано городов: ' . count($parsed));
        } else {
            $report = ['ok' => false, 'errors' => $errors, 'imported' => []];
        }
    }
}

/* ---------- Обработка загрузки CSV ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'import_csv') {
    csrf_check();

    $mode = ($_POST['mode_csv'] ?? 'merge') === 'replace' ? 'replace' : 'merge';

    if (empty($_FILES['csv']['tmp_name']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        $report = ['ok' => false, 'errors' => ['Загрузите CSV-файл.'], 'imported' => []];
    } else {
        $raw = (string)file_get_contents($_FILES['csv']['tmp_name']);
        [$parsed, $errors] = parse_cities_csv($raw);
        if (!empty($parsed)) {
            $existing = read_json_file('cities.json');
            $result   = $mode === 'replace' ? $parsed : array_replace($existing, $parsed);
            $saved    = write_json_file('cities.json', $result);
            $report = [
                'ok'       => $saved,
                'errors'   => $saved ? $errors : array_merge($errors, ['Не удалось записать data/cities.json (проверьте права на запись).']),
                'imported' => array_keys($parsed),
                'mode'     => $mode,
            ];
            if ($saved) flash_set('Импортировано городов из CSV: ' . count($parsed));
        } else {
            $report = ['ok' => false, 'errors' => $errors, 'imported' => []];
        }
    }
}

/* ---------- Удаление одного города ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $slugDel = strtolower(trim((string)($_POST['slug'] ?? '')));
    $slugDel = (string)preg_replace('/[^a-z0-9_-]/', '', $slugDel);
    $all = read_json_file('cities.json');
    if ($slugDel !== '' && isset($all[$slugDel])) {
        unset($all[$slugDel]);
        write_json_file('cities.json', $all);
        flash_set('Город удалён.');
    }
    header('Location: cities.php');
    exit;
}

$cities = read_json_file('cities.json');
$flash  = flash_get();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Города — Админка</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-header">
  <div class="admin-logo">Pavel<span>.dev</span> · Админка</div>
  <div class="admin-nav">
    <a href="index.php">Дашборд</a>
    <a href="projects.php">Проекты</a>
    <a href="posts.php">Посты</a>
    <a href="cities.php" class="active">Города</a>
    <a href="leads.php">Заявки</a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Города (рекламные страницы)</h1>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
      <a href="city-form.php" class="btn btn-primary">+ Добавить город</a>
      <a href="cities.php?tpl=csv" class="btn btn-ghost">⬇ CSV-шаблон</a>
      <a href="../data/cities.xml" download class="btn btn-ghost">Образец XML</a>
    </div>
  </div>
  <?php if ($flash): ?><div class="flash"><?= esc($flash) ?></div><?php endif; ?>

  <?php if ($report): ?>
    <?php if ($report['ok']): ?>
    <div class="flash">Готово. Импортировано городов: <?= count($report['imported']) ?>
      (режим: <?= $report['mode'] === 'replace' ? 'полная замена' : 'добавление/обновление' ?>).</div>
    <?php else: ?>
    <div class="flash" style="background:#3a1220;border-color:#7a2540;color:#ffd0da;">Импорт не выполнен.</div>
    <?php endif; ?>
    <?php if (!empty($report['errors'])): ?>
    <div class="empty" style="text-align:left;">
      <b>Замечания:</b>
      <ul style="margin:.5rem 0 0 1rem;">
        <?php foreach ($report['errors'] as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="admin-item" style="display:block;padding:1.25rem;">
    <h3 style="margin-top:0;">Массовая заливка городов через CSV (Excel / Google Sheets)</h3>
    <p style="opacity:.8;">Нажмите «Скачать CSV-шаблон» выше, откройте в Excel или Google Sheets,
      заполните строки (<b>1 строка = 1 страница города</b>) и загрузите файл обратно.
      Обязательна только колонка <code>slug</code> (адрес страницы, латиницей: <code>kyiv</code>, <code>lviv</code>).
      Каждый город доступен по адресу <code>/city-SLUG</code>.
      Пустые ячейки → на странице останется «рыба» с автоподстановкой названия города.</p>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="import_csv">
      <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">

      <div class="form-group" style="margin-bottom:1rem;">
        <label>CSV-файл</label><br>
        <input type="file" name="csv" accept=".csv,text/csv,text/plain" required>
      </div>

      <div class="form-group" style="margin-bottom:1rem;">
        <label style="font-weight:400;"><input type="radio" name="mode_csv" value="merge" checked> Добавить / обновить (существующие города сохраняются)</label><br>
        <label style="font-weight:400;"><input type="radio" name="mode_csv" value="replace"> Полная замена (удалить все прежние города)</label>
      </div>

      <button type="submit" class="btn btn-primary">Импортировать CSV</button>
    </form>
    <p style="opacity:.6;font-size:.85rem;margin-bottom:0;">Колонки: slug, city_name, seo_title, seo_description,
      hero_badge, hero_heading_prefix, hero_heading_highlight, hero_heading_suffix, hero_subtext,
      about_heading, about_paragraph_1, about_paragraph_2, pricing_subtext, cta_heading, cta_text,
      contact_heading, contact_subtext.</p>
  </div>

  <div class="admin-item" style="display:block;padding:1.25rem;">
    <h3 style="margin-top:0;">Массовая заливка городов через XML</h3>
    <p style="opacity:.8;">Загрузите XML-файл (формат — как в образце) или вставьте XML текстом.
      Каждый <code>&lt;city slug="..."&gt;</code> создаёт страницу <code>/city-SLUG</code>.
      Пропущенные поля заменяются «рыбой».</p>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="import">
      <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">

      <div class="form-group" style="margin-bottom:1rem;">
        <label>XML-файл</label><br>
        <input type="file" name="xml" accept=".xml,text/xml,application/xml">
      </div>

      <div class="form-group" style="margin-bottom:1rem;">
        <label>…или вставьте XML текстом</label>
        <textarea name="xml_text" rows="8" style="width:100%;font-family:monospace;" placeholder="<cities> ... </cities>"></textarea>
      </div>

      <div class="form-group" style="margin-bottom:1rem;">
        <label style="font-weight:400;"><input type="radio" name="mode" value="merge" checked> Добавить / обновить (существующие города сохраняются)</label><br>
        <label style="font-weight:400;"><input type="radio" name="mode" value="replace"> Полная замена (удалить все прежние города)</label>
      </div>

      <button type="submit" class="btn btn-primary">Импортировать</button>
    </form>
  </div>

  <div class="admin-top" style="margin-top:2rem;">
    <h1 style="font-size:1.2rem;">Города (<?= count($cities) ?>)</h1>
    <a href="city-form.php" class="btn btn-primary">+ Добавить город вручную</a>
  </div>
  <?php if (empty($cities)): ?>
  <div class="empty">Городов пока нет. Добавьте вручную кнопкой выше или залейте CSV/XML.</div>
  <?php else: ?>
  <div class="admin-list">
    <?php foreach ($cities as $slug => $city): ?>
    <div class="admin-item">
      <div class="admin-item-body">
        <h3><?= esc($city['city_name'] ?? $slug) ?></h3>
        <p>slug: <?= esc((string)$slug) ?> · <?= esc(city_url((string)$slug)) ?></p>
      </div>
      <div class="admin-item-actions">
        <a href="<?= esc(city_url((string)$slug)) ?>" target="_blank" class="btn btn-ghost btn-sm">Смотреть</a>
        <a href="<?= esc('city-form.php?slug=' . urlencode((string)$slug)) ?>" class="btn btn-ghost btn-sm">Изменить</a>
        <form method="POST" onsubmit="return confirm('Удалить город «<?= esc(addslashes($city['city_name'] ?? (string)$slug)) ?>»?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="slug" value="<?= esc((string)$slug) ?>">
          <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
          <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
