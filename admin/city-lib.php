<?php
declare(strict_types=1);

/* ============================================================
   Общая логика городов: карта полей + помощники доступа
   к вложенной структуре. Используется и списком (cities.php),
   и ручной формой (city-form.php), и CSV-импортом.
   ============================================================ */

// Плоское поле -> путь во вложенной структуре города.
// Пустое значение не сохраняется: на странице останется «рыба» с подстановкой {{city}}.
const CITY_CSV_MAP = [
    'city_name'              => ['city_name'],
    'seo_title'              => ['seo', 'title'],
    'seo_description'        => ['seo', 'description'],
    'hero_badge'             => ['hero', 'badge'],
    'hero_heading_prefix'    => ['hero', 'heading_prefix'],
    'hero_heading_highlight' => ['hero', 'heading_highlight'],
    'hero_heading_suffix'    => ['hero', 'heading_suffix'],
    'hero_subtext'           => ['hero', 'subtext'],
    'about_heading'          => ['about', 'heading'],
    'about_paragraph_1'      => ['about', 'paragraph_1'],
    'about_paragraph_2'      => ['about', 'paragraph_2'],
    'pricing_subtext'        => ['pricing', 'subtext'],
    'cta_heading'            => ['call_to_action', 'heading'],
    'cta_text'               => ['call_to_action', 'text'],
    'contact_heading'        => ['contact', 'heading'],
    'contact_subtext'        => ['contact', 'subtext'],
];

// Метаданные полей для ручной формы (порядок = порядок отображения).
const CITY_FORM_FIELDS = [
    'city_name'              => ['label' => 'Название города',                 'type' => 'text',     'hint' => 'Например: Киев. Подставляется по всей странице вместо {{city}}.'],
    'seo_title'              => ['label' => 'SEO-заголовок (title)',           'type' => 'text',     'hint' => 'Заголовок вкладки и ссылки в Google.'],
    'seo_description'        => ['label' => 'SEO-описание (meta description)',  'type' => 'textarea', 'hint' => 'Описание под ссылкой в поиске.'],
    'hero_badge'             => ['label' => 'Плашка над заголовком',           'type' => 'text',     'hint' => ''],
    'hero_heading_prefix'    => ['label' => 'Заголовок: начало',               'type' => 'text',     'hint' => 'Например: «Создаю сайты в ».'],
    'hero_heading_highlight' => ['label' => 'Заголовок: выделенное слово',     'type' => 'text',     'hint' => 'Цветное слово, например «Киеве».'],
    'hero_heading_suffix'    => ['label' => 'Заголовок: продолжение',          'type' => 'text',     'hint' => ''],
    'hero_subtext'           => ['label' => 'Подзаголовок (под заголовком)',   'type' => 'textarea', 'hint' => ''],
    'about_heading'          => ['label' => 'Блок «Обо мне»: заголовок',       'type' => 'text',     'hint' => ''],
    'about_paragraph_1'      => ['label' => 'Обо мне: абзац 1',                'type' => 'textarea', 'hint' => ''],
    'about_paragraph_2'      => ['label' => 'Обо мне: абзац 2',                'type' => 'textarea', 'hint' => ''],
    'pricing_subtext'        => ['label' => 'Цены: подводка',                  'type' => 'textarea', 'hint' => ''],
    'cta_heading'            => ['label' => 'Призыв к действию: заголовок',     'type' => 'text',     'hint' => ''],
    'cta_text'               => ['label' => 'Призыв к действию: текст',         'type' => 'textarea', 'hint' => ''],
    'contact_heading'        => ['label' => 'Контакты: заголовок',             'type' => 'text',     'hint' => ''],
    'contact_subtext'        => ['label' => 'Контакты: подводка',              'type' => 'textarea', 'hint' => ''],
];

// Записывает значение по вложенному пути, создавая недостающие уровни.
function set_city_path(array &$arr, array $path, string $value): void {
    $ref = &$arr;
    foreach ($path as $key) {
        if (!isset($ref[$key]) || !is_array($ref[$key])) $ref[$key] = [];
        $ref = &$ref[$key];
    }
    $ref = $value;
}

// Читает строковое значение по вложенному пути (или '' если нет).
function get_city_path(array $arr, array $path): string {
    $ref = $arr;
    foreach ($path as $key) {
        if (!is_array($ref) || !isset($ref[$key])) return '';
        $ref = $ref[$key];
    }
    return is_string($ref) ? $ref : '';
}

// Удаляет лист по вложенному пути (родительские уровни не трогает).
function unset_city_path(array &$arr, array $path): void {
    $key = array_shift($path);
    if ($key === null || !isset($arr[$key])) return;
    if (empty($path)) { unset($arr[$key]); return; }
    if (is_array($arr[$key])) unset_city_path($arr[$key], $path);
}

/**
 * Применяет плоские поля формы к данным города, сохраняя вложенные секции
 * (услуги, тарифы, FAQ и т.п.), заданные ранее через XML.
 * Пустое поле удаляет лист — на странице вернётся «рыба».
 *
 * @param array<string,string> $flat  [field => value] по ключам CITY_CSV_MAP
 * @param array                $base  существующие данные города (для сохранения глубоких секций)
 */
function build_city_from_flat(array $flat, array $base = []): array {
    $data = $base;
    foreach (CITY_CSV_MAP as $field => $path) {
        $val = trim((string)($flat[$field] ?? ''));
        if ($val === '') {
            unset_city_path($data, $path);
        } else {
            set_city_path($data, $path, $val);
        }
    }
    return $data;
}
