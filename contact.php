<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/functions.php';

// Email address that receives new leads (kept as a best-effort backup).
$to = "ppvr3407@gmail.com";

function clean_field(string $value): string
{
    $value = trim($value);
    $value = str_replace(["\r", "\n"], '', $value); // block header injection
    return $value;
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
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
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

// --- Best-effort email notification (may silently fail on some hosts) ---
$subject = "Новая заявка с сайта от {$name}";
$body  = "Имя: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Телефон: " . ($phone !== '' ? $phone : '-') . "\n";
$body .= "Тип проекта: " . ($service !== '' ? $service : '-') . "\n\n";
$body .= "Сообщение:\n{$message}\n";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$headers  = "From: no-reply@{$host}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

header("Location: thanks.htm");
exit;
