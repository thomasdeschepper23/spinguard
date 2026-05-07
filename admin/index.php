<?php
require __DIR__ . '/config.php';

$BASE = admin_base_url();
if (admin_needs_setup()) {
    header('Location: ' . $BASE . '/admin/setup.php');
    exit;
}
if (admin_is_logged_in()) {
    header('Location: ' . $BASE . '/admin/dashboard.php');
    exit;
}

$error = '';
$locked_remaining = lockout_seconds_remaining();
if ($locked_remaining > 0) {
    $minutes = ceil($locked_remaining / 60);
    $error = "Te veel mislukte pogingen. Probeer opnieuw over $minutes minu" . ($minutes==1?'ut':'ten') . ".";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $locked_remaining === 0) {
    if (!csrf_check($_POST['_csrf'] ?? '')) {
        $error = 'Ongeldige sessie. Probeer opnieuw.';
    } else {
        $u = $_POST['username'] ?? '';
        $p = $_POST['password'] ?? '';
        if (admin_login($u, $p)) {
            header('Location: ' . $BASE . '/admin/dashboard.php');
            exit;
        }
        if (is_locked_out()) {
            $minutes = ceil(lockout_seconds_remaining() / 60);
            $error = "Te veel mislukte pogingen. Account vergrendeld voor $minutes minuten.";
        } else {
            $remaining = MAX_LOGIN_ATTEMPTS - count(_read_lockouts()[_client_ip()]['attempts'] ?? []);
            $error = "Onjuiste gebruikersnaam of wachtwoord. Nog $remaining poging" . ($remaining==1?'':'en') . " over.";
        }
    }
}
$tok = csrf_token();
?><!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin — SpinGuard</title>
  <link rel="stylesheet" href="<?= htmlspecialchars($BASE) ?>/admin/css/admin.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="logo-line">
        <img src="<?= htmlspecialchars($BASE) ?>/assets/spinguard-logo.png" alt="SpinGuard" />
        <strong>Admin</strong>
      </div>
      <h1>Inloggen</h1>
      <p class="sub">Log in om de website-content aan te passen.</p>

      <?php if ($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <form method="post" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
        <div class="field">
          <label>Gebruikersnaam</label>
          <input type="text" name="username" value="admin" autofocus required />
        </div>
        <div class="field">
          <label>Wachtwoord</label>
          <input type="password" name="password" required />
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
          Inloggen
        </button>
      </form>

      <p style="margin-top:18px; font-size:12.5px; color:var(--muted); text-align:center;">
        <a href="<?= htmlspecialchars($BASE) ?>/" style="color:var(--muted);">← Terug naar website</a>
      </p>
    </div>
  </div>
</body>
</html>
