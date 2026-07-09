<?php
declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');

function read_json_file(string $name): array {
    $path = DATA_DIR . '/' . $name;
    if (!file_exists($path)) return [];
    $raw = file_get_contents($path);
    $data = json_decode((string)$raw, true);
    return is_array($data) ? $data : [];
}

function write_json_file(string $name, array $data): bool {
    $path = DATA_DIR . '/' . $name;
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return $json !== false && file_put_contents($path, $json) !== false;
}

function slugify(string $text): string {
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y',
        'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
        'х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    $text = mb_strtolower(trim($text), 'UTF-8');
    $translit = '';
    foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
        $translit .= $map[$ch] ?? $ch;
    }
    $translit = preg_replace('/[^a-z0-9]+/', '-', $translit);
    $translit = trim((string)$translit, '-');
    return $translit !== '' ? $translit : 'item-' . substr(md5((string)microtime(true)), 0, 6);
}

function unique_slug(array $items, string $slug, ?int $excludeId = null): string {
    $base = $slug;
    $i = 2;
    while (true) {
        $collision = false;
        foreach ($items as $item) {
            if (($item['slug'] ?? '') === $slug && (int)($item['id'] ?? 0) !== $excludeId) {
                $collision = true;
                break;
            }
        }
        if (!$collision) return $slug;
        $slug = $base . '-' . $i;
        $i++;
    }
}

function next_id(array $items): int {
    $max = 0;
    foreach ($items as $item) {
        $max = max($max, (int)($item['id'] ?? 0));
    }
    return $max + 1;
}

function find_by_slug(array $items, string $slug): ?array {
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $slug) return $item;
    }
    return null;
}

function find_by_id(array $items, int $id): ?array {
    foreach ($items as $item) {
        if ((int)($item['id'] ?? 0) === $id) return $item;
    }
    return null;
}

function esc(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function nl2p(string $text): string {
    $text = trim($text);
    if ($text === '') return '';
    $blocks = preg_split('/\n{2,}/', $text);
    $html = '';
    foreach ($blocks as $block) {
        $block = nl2br(esc(trim($block)));
        if ($block !== '') $html .= "<p>{$block}</p>\n";
    }
    return $html;
}

function format_date_ru(string $datetime): string {
    $months = [1=>'янв',2=>'фев',3=>'мар',4=>'апр',5=>'мая',6=>'июн',7=>'июл',8=>'авг',9=>'сен',10=>'окт',11=>'ноя',12=>'дек'];
    $ts = strtotime($datetime);
    if ($ts === false) return $datetime;
    return (int)date('j', $ts) . ' ' . $months[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

/**
 * Handles an uploaded image field. Returns the stored relative path
 * (e.g. "images/uploads/xxxx.jpg") or $existing if nothing new was uploaded.
 */
function handle_image_upload(string $fieldName, ?string $existing = null): ?string {
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) return $existing;

    $allowed = ['jpg'=>true,'jpeg'=>true,'png'=>true,'webp'=>true,'gif'=>true,'svg'=>true];
    $ext = strtolower(pathinfo((string)$_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) return $existing;
    if ($_FILES[$fieldName]['size'] > 5 * 1024 * 1024) return $existing;

    $uploadDir = ROOT_DIR . '/images/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $target = $uploadDir . $filename;

    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
        return 'images/uploads/' . $filename;
    }
    return $existing;
}
