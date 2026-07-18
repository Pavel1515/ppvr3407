<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$config = require __DIR__ . '/config.php';
$error = '';

// --- Brute-force protection settings ---
const LOGIN_MAX_ATTEMPTS   = 5;      // failed tries before lockout
const LOGIN_LOCKOUT_SECS   = 900;    // 15 minutes locked
const LOGIN_WINDOW_SECS    = 900;    // count failures within this window

/**
 * Rewrites config.php so the plaintext password becomes a bcrypt hash.
 * Best-effort: silently does nothing if the file isn't writable.
 */
function upgrade_admin_password(string $username, string $plainPassword): void {
    $path = __DIR__ . '/config.php';
    if (!is_writable($path)) return;
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $content = "<?php\n"
        . "// Admin panel credentials. The password is stored as a bcrypt hash.\n"
        . "// To reset it, put a new 'password' => 'plaintext' line here; it will be\n"
        . "// re-hashed automatically on the next successful login.\n"
        . "return [\n"
        . "    'username' => " . var_export($username, true) . ",\n"
        . "    'password_hash' => " . var_export($hash, true) . ",\n"
        . "];\n";
    @file_put_contents($path, $content, LOCK_EX);
}

$ip  = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$now = time();

// Load and prune the per-IP attempt log (stored in the locked-down data/ dir).
$attempts = read_json_file('login_attempts.json');
foreach ($attempts as $key => $rec) {
    $until = (int)($rec['until'] ?? 0);
    $first = (int)($rec['first'] ?? 0);
    if ($until < $now && ($first + LOGIN_WINDOW_SECS) < $now) {
        unset($attempts[$key]);
    }
}

$rec        = $attempts[$ip] ?? ['fails' => 0, 'first' => $now, 'until' => 0];
$lockedFor  = max(0, (int)$rec['until'] - $now);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($lockedFor > 0) {
        $error = 'Слишком много попыток. Повторите через ' . ceil($lockedFor / 60) . ' мин.';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $userOk = hash_equals((string)($config['username'] ?? ''), $username);
        $passOk = false;
        if (!empty($config['password_hash'])) {
            $passOk = password_verify($password, (string)$config['password_hash']);
        } elseif (isset($config['password'])) {
            $passOk = hash_equals((string)$config['password'], $password);
        }

        if ($userOk && $passOk) {
            // Success: clear this IP's failures and log in.
            unset($attempts[$ip]);
            write_json_file('login_attempts.json', $attempts);

            // Opportunistically migrate a legacy plaintext password to a hash.
            if (empty($config['password_hash']) && isset($config['password'])) {
                upgrade_admin_password((string)$config['username'], $password);
            }

            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['last_activity'] = $now;
            header('Location: index.php');
            exit;
        }

        // Failure: throttle a little, then record the attempt.
        usleep(400000); // 0.4s — slows automated guessing

        if (($rec['first'] + LOGIN_WINDOW_SECS) < $now) {
            $rec = ['fails' => 0, 'first' => $now, 'until' => 0];
        }
        $rec['fails'] = (int)$rec['fails'] + 1;

        if ($rec['fails'] >= LOGIN_MAX_ATTEMPTS) {
            $rec['until'] = $now + LOGIN_LOCKOUT_SECS;
            $error = 'Слишком много попыток. Вход заблокирован на ' . (LOGIN_LOCKOUT_SECS / 60) . ' мин.';
        } else {
            $left = LOGIN_MAX_ATTEMPTS - $rec['fails'];
            $error = 'Неверный логин или пароль. Осталось попыток: ' . $left . '.';
        }

        $attempts[$ip] = $rec;
        write_json_file('login_attempts.json', $attempts);
    }
} elseif ($lockedFor > 0) {
    $error = 'Слишком много попыток. Повторите через ' . ceil($lockedFor / 60) . ' мин.';
} elseif (isset($_GET['expired'])) {
    $error = 'Сессия истекла из-за бездействия. Войдите снова.';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Вход в админку — Pavel.dev</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <h1>Вход в админку</h1>
    <p>Управление проектами и постами блога.</p>
    <?php if ($error): ?><div class="login-error"><?= esc($error) ?></div><?php endif; ?>
    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label for="username">Логин</label>
        <input id="username" type="text" name="username" required autofocus <?= $lockedFor > 0 ? 'disabled' : '' ?>>
      </div>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input id="password" type="password" name="password" required <?= $lockedFor > 0 ? 'disabled' : '' ?>>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;" <?= $lockedFor > 0 ? 'disabled' : '' ?>>Войти</button>
    </form>
  </div>
</div>
</body>
</html>
