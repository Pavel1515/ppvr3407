<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/functions.php';

// Email address that receives new leads (kept as a best-effort backup).
$to = "ppvr3407@gmail.com";

// --- Телеграм-бот для заявок ---
// Секрет берём из переменных окружения, а если их нет — из data/secrets.php
// (эта папка закрыта от веба через data/.htaccess и не попадает в git).
// НИКОГДА не вписывай токен прямо в этот файл: он в git и виден как текст на не-PHP хостинге.
$tgToken = getenv('TELEGRAM_BOT_TOKEN') ?: '';
$tgChat  = getenv('TELEGRAM_CHAT_ID') ?: '';
if (($tgToken === '' || $tgChat === '') && is_file(__DIR__ . '/data/secrets.php')) {
    $secrets = require __DIR__ . '/data/secrets.php';
    if (is_array($secrets)) {
        if ($tgToken === '') $tgToken = (string)($secrets['TELEGRAM_BOT_TOKEN'] ?? '');
        if ($tgChat  === '') $tgChat  = (string)($secrets['TELEGRAM_CHAT_ID'] ?? '');
    }
}

function clean_field(string $value): string
{
    $value = trim($value);
    $value = str_replace(["\r", "\n"], '', $value); // block header injection
    return $value;
}

// Отправка заявки боту в Телеграм. Best-effort: молча выходит, если не настроено.
function send_telegram(string $token, string $chatId, string $text): void
{
    if ($token === '' || $chatId === '') return;

    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $payload = json_encode([
        'chat_id'                  => $chatId,
        'text'                     => $text,
        'parse_mode'               => 'HTML',
        'disable_web_page_preview' => true,
    ], JSON_UNESCAPED_UNICODE);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);
        curl_exec($ch);
        curl_close($ch);
        return;
    }

    // Фолбэк без cURL.
    @file_get_contents($url, false, stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'content'       => $payload,
            'timeout'       => 10,
            'ignore_errors' => true,
        ],
    ]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Honeypot: real users never fill this hidden field, bots usually do
$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    header("Location: thanks.htm");
    exit;
}

$name    = clean_field($_POST['name'] ?? '');
$email   = clean_field($_POST['email'] ?? '');
$phone   = clean_field($_POST['phone'] ?? '');
$service = clean_field($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '') {
    $errors[] = 'name';
}
// Email необязателен: проверяем формат только если он заполнен.
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
// Нужен хотя бы один способ связи — email или телефон.
if ($email === '' && $phone === '') {
    $errors[] = 'contact';
}
if ($message === '') {
    $errors[] = 'message';
}

if (!empty($errors)) {
    header("Location: index.php?error=1#contact");
    exit;
}

// --- Save the lead to data/leads.json (primary storage) ---
$leads = read_json_file('leads.json');
$leads[] = [
    'id'         => next_id($leads),
    'name'       => $name,
    'email'      => $email,
    'phone'      => $phone,
    'service'    => $service,
    'message'    => $message,
    'is_read'    => false,
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
    'created_at' => date('Y-m-d H:i:s'),
];
write_json_file('leads.json', $leads);

// --- Уведомление в Телеграм ---
// Для parse_mode=HTML экранируем только < > & (кавычки трогать нельзя).
$tgEsc = static fn(string $v): string => htmlspecialchars($v, ENT_NOQUOTES, 'UTF-8');
$tgText  = "🔔 <b>Новая заявка с сайта</b>\n\n";
$tgText .= "👤 <b>Имя:</b> " . $tgEsc($name) . "\n";
$tgText .= "✉️ <b>Email:</b> " . $tgEsc($email !== '' ? $email : '—') . "\n";
$tgText .= "📞 <b>Телефон:</b> " . $tgEsc($phone !== '' ? $phone : '—') . "\n";
$tgText .= "🛠 <b>Тип проекта:</b> " . $tgEsc($service !== '' ? $service : '—') . "\n\n";
$tgText .= "💬 <b>Сообщение:</b>\n" . $tgEsc($message);
send_telegram($tgToken, $tgChat, $tgText);

// --- Best-effort email notification (may silently fail on some hosts) ---
$subject = "Новая заявка с сайта от {$name}";
$body  = "Имя: {$name}\n";
$body .= "Email: " . ($email !== '' ? $email : '-') . "\n";
$body .= "Телефон: " . ($phone !== '' ? $phone : '-') . "\n";
$body .= "Тип проекта: " . ($service !== '' ? $service : '-') . "\n\n";
$body .= "Сообщение:\n{$message}\n";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$headers  = "From: no-reply@{$host}\r\n";
if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $headers .= "Reply-To: {$email}\r\n";
}
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

header("Location: thanks.htm");
exit;
