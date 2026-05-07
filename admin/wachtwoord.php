<?php
require __DIR__ . '/config.php';
admin_require_login();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? '')) {
        $err = 'Sessie verlopen.';
    } else {
        $current = $_POST['current'] ?? '';
        $new = $_POST['new'] ?? '';
        $new2 = $_POST['new2'] ?? '';
        if (!password_verify($current, ADMIN_PASSWORD_HASH)) {
            $err = 'Huidig wachtwoord is onjuist.';
        } elseif (strlen($new) < 8) {
            $err = 'Nieuw wachtwoord moet minimaal 8 tekens zijn.';
        } elseif ($new !== $new2) {
            $err = 'Nieuwe wachtwoorden komen niet overeen.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $cfg_path = __DIR__ . '/config.php';
            $cfg = file_get_contents($cfg_path);
            $patched = preg_replace_callback(
                "/(const ADMIN_PASSWORD_HASH\s*=\s*)'[^']*';/",
                function($m) use ($hash) {
                    return $m[1] . "'" . str_replace("'", "\\'", $hash) . "';";
                },
                $cfg, 1, $count
            );
            if ($count === 1 && @file_put_contents($cfg_path, $patched) !== false) {
                $msg = 'Wachtwoord gewijzigd. Bewaar het goed.';
            } else {
                $err = 'Kon config.php niet bijwerken (controleer schrijfrechten).';
            }
        }
    }
}

$tok = csrf_token();
include __DIR__ . '/_layout_top.php';
?>
<div class="main-head">
  <div>
    <h1>Wachtwoord wijzigen</h1>
    <p>Stel een nieuw wachtwoord in voor het admin paneel.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="notice success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 12 l3 3 5-6"/></svg>
    <strong>Succes!</strong> <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>
<?php if ($err): ?>
  <div class="notice error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v5"/></svg>
    <?= htmlspecialchars($err) ?>
  </div>
<?php endif; ?>

<div class="card" style="max-width:520px;">
  <h2>Nieuw wachtwoord instellen</h2>
  <p class="muted">Vul het huidige wachtwoord in en kies een nieuw, sterk wachtwoord.</p>

  <form method="post" autocomplete="off">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
    <div class="field">
      <label>Huidig wachtwoord</label>
      <input type="password" name="current" required autofocus />
    </div>
    <div class="field">
      <label>Nieuw wachtwoord <span class="hint">(min. 8 tekens)</span></label>
      <input type="password" name="new" required minlength="8" />
    </div>
    <div class="field">
      <label>Nieuw wachtwoord nogmaals</label>
      <input type="password" name="new2" required minlength="8" />
    </div>
    <button type="submit" class="btn btn-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11 V8 C8 5.8 9.8 4 12 4 C14.2 4 16 5.8 16 8 V11"/></svg>
      Wachtwoord wijzigen
    </button>
  </form>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
