<?php
require __DIR__ . '/config.php';

$BASE = admin_base_url();
// Als al ingesteld → naar login
if (!admin_needs_setup()) {
    header('Location: ' . $BASE . '/admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = $_POST['password']  ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($pass1) < 8) {
        $error = 'Wachtwoord moet minimaal 8 tekens zijn.';
    } elseif ($pass1 !== $pass2) {
        $error = 'Wachtwoorden komen niet overeen.';
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $cfg_path = __DIR__ . '/config.php';
        $cfg = file_get_contents($cfg_path);
        // preg_replace_callback voorkomt dat $-tekens in de bcrypt hash
        // als backreferences worden geïnterpreteerd
        $new = preg_replace_callback(
            "/(const ADMIN_PASSWORD_HASH\s*=\s*)'[^']*';/",
            function($m) use ($hash) {
                return $m[1] . "'" . str_replace("'", "\\'", $hash) . "';";
            },
            $cfg, 1, $count
        );
        if ($count === 1 && @file_put_contents($cfg_path, $new) !== false) {
            // Direct inloggen
            admin_session_start();
            session_regenerate_id(true);
            $_SESSION['admin_ok'] = true;
            $_SESSION['admin_expires'] = time() + ADMIN_SESSION_LIFETIME;
            header('Location: ' . $BASE . '/admin/dashboard.php');
            exit;
        } else {
            $error = 'Kon config.php niet opslaan. Controleer of het bestand schrijfbaar is (chmod 644).';
        }
    }
}
?><!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Eerste keer instellen — SpinGuard Admin</title>
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
      <h1>Eerste keer instellen</h1>
      <p class="sub">Stel uw wachtwoord in voor het admin paneel. Bewaar dit goed — er is geen "wachtwoord vergeten" functie.</p>

      <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <div class="field">
          <label>Gebruikersnaam <span class="hint">(vast)</span></label>
          <input type="text" value="admin" disabled />
        </div>
        <div class="field">
          <label>Nieuw wachtwoord <span class="hint">(min. 8 tekens)</span></label>
          <input type="password" name="password" required minlength="8" autofocus />
        </div>
        <div class="field">
          <label>Wachtwoord nogmaals</label>
          <input type="password" name="password2" required minlength="8" />
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
          Wachtwoord instellen
        </button>
      </form>
    </div>
  </div>
</body>
</html>
