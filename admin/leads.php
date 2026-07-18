<?php
require_once __DIR__ . '/auth.php';
require_login();

$leads = read_json_file('leads.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        $leads = array_values(array_filter($leads, fn($l) => (int)($l['id'] ?? 0) !== $id));
        write_json_file('leads.json', $leads);
        flash_set('Заявка удалена.');
    } elseif ($action === 'toggle_read') {
        foreach ($leads as &$l) {
            if ((int)($l['id'] ?? 0) === $id) {
                $l['is_read'] = empty($l['is_read']);
                break;
            }
        }
        unset($l);
        write_json_file('leads.json', $leads);
    }
    header('Location: leads.php');
    exit;
}

// Newest first
usort($leads, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
$unread = count(array_filter($leads, fn($l) => empty($l['is_read'])));
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Заявки — Админка</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="admin.css">
<style>
  .lead-card { display: block; }
  .lead-card.unread { border-left: 3px solid var(--accent, #7c5cff); }
  .lead-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .lead-badge { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
    background: var(--accent, #7c5cff); color: #fff; padding: 2px 8px; border-radius: 100px; }
  .lead-meta { color: #8a8f99; font-size: 13px; margin: 4px 0 10px; }
  .lead-meta a { color: inherit; text-decoration: underline; }
  .lead-message { white-space: pre-wrap; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07);
    border-radius: 10px; padding: 12px 14px; margin-top: 6px; }
  .lead-fields { display: flex; flex-wrap: wrap; gap: 6px 18px; font-size: 14px; }
  .lead-fields b { color: #8a8f99; font-weight: 600; }
</style>
</head>
<body>
<div class="admin-header">
  <div class="admin-logo">Pavel<span>.dev</span> · Админка</div>
  <div class="admin-nav">
    <a href="index.php">Дашборд</a>
    <a href="projects.php">Проекты</a>
    <a href="posts.php">Посты</a>
    <a href="cities.php">Города</a>
    <a href="leads.php" class="active">Заявки<?php if ($unread): ?> (<?= $unread ?>)<?php endif; ?></a>
  </div>
  <a href="logout.php" class="admin-logout">Выйти</a>
</div>
<div class="admin-wrap">
  <div class="admin-top">
    <h1>Заявки<?php if ($unread): ?> · <?= $unread ?> новых<?php endif; ?></h1>
  </div>
  <?php if ($flash): ?><div class="flash"><?= esc($flash) ?></div><?php endif; ?>

  <?php if (empty($leads)): ?>
  <div class="empty">Заявок пока нет.</div>
  <?php else: ?>
  <div class="admin-list">
    <?php foreach ($leads as $l): ?>
    <div class="admin-item lead-card<?= empty($l['is_read']) ? ' unread' : '' ?>">
      <div class="admin-item-body">
        <div class="lead-head">
          <h3><?= esc($l['name'] ?? '') ?></h3>
          <?php if (empty($l['is_read'])): ?><span class="lead-badge">Новая</span><?php endif; ?>
        </div>
        <div class="lead-meta">
          <?= esc(format_date_ru($l['created_at'] ?? '')) ?>,
          <?= esc(substr((string)($l['created_at'] ?? ''), 11, 5)) ?>
          <?php if (!empty($l['ip'])): ?> · IP <?= esc($l['ip']) ?><?php endif; ?>
        </div>
        <div class="lead-fields">
          <span><b>Email:</b> <a href="mailto:<?= esc($l['email'] ?? '') ?>"><?= esc($l['email'] ?? '') ?></a></span>
          <?php if (!empty($l['phone'])): ?><span><b>Телефон:</b> <a href="tel:<?= esc($l['phone']) ?>"><?= esc($l['phone']) ?></a></span><?php endif; ?>
          <?php if (!empty($l['service'])): ?><span><b>Тип проекта:</b> <?= esc($l['service']) ?></span><?php endif; ?>
        </div>
        <?php if (!empty($l['message'])): ?>
        <div class="lead-message"><?= esc($l['message']) ?></div>
        <?php endif; ?>
      </div>
      <div class="admin-item-actions">
        <form method="POST">
          <input type="hidden" name="action" value="toggle_read">
          <input type="hidden" name="id" value="<?= (int)($l['id'] ?? 0) ?>">
          <input type="hidden" name="csrf" value="<?= esc(csrf_token()) ?>">
          <button type="submit" class="btn btn-ghost btn-sm"><?= empty($l['is_read']) ? 'Прочитано' : 'В новые' ?></button>
        </form>
        <form method="POST" onsubmit="return confirm('Удалить заявку от «<?= esc(addslashes($l['name'] ?? '')) ?>»?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)($l['id'] ?? 0) ?>">
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
