<?php
require_once __DIR__ . '/auth.php';
require_login();

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
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Города (рекламные страницы)</h1>
    <a href="../data/cities.xml" download class="btn btn-ghost">Скачать образец XML</a>
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
    <h3 style="margin-top:0;">Массовая заливка городов через XML</h3>
    <p style="opacity:.8;">Загрузите XML-файл (формат — как в образце) или вставьте XML текстом.
      Каждый <code>&lt;city slug="..."&gt;</code> создаёт страницу <code>template.php?city=SLUG</code>.
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
    <h1 style="font-size:1.2rem;">Загруженные города (<?= count($cities) ?>)</h1>
  </div>
  <?php if (empty($cities)): ?>
  <div class="empty">Городов пока нет. Залейте XML выше.</div>
  <?php else: ?>
  <div class="admin-list">
    <?php foreach ($cities as $slug => $city): ?>
    <div class="admin-item">
      <div class="admin-item-body">
        <h3><?= esc($city['city_name'] ?? $slug) ?></h3>
        <p>slug: <?= esc((string)$slug) ?> · /template.php?city=<?= esc((string)$slug) ?></p>
      </div>
      <div class="admin-item-actions">
        <a href="<?= esc('../template.php?city=' . urlencode((string)$slug)) ?>" target="_blank" class="btn btn-ghost btn-sm">Смотреть</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
