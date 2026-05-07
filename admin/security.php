<?php
require __DIR__ . '/config.php';
admin_require_login();

$activity = get_recent_activity(100);

// Health check uitvoeren
$health = [];

// Mail check (kan PHP mail()?)
$mail_ok = function_exists('mail');
$health[] = [
    'label' => 'PHP mail() functie',
    'status' => $mail_ok ? 'ok' : 'fail',
    'detail' => $mail_ok ? 'Beschikbaar — contactformulier kan e-mails sturen' : 'Niet beschikbaar — vraag hosting-support',
];

// Schrijfrechten content
$content_ok = is_writable(__DIR__ . '/../content');
$health[] = [
    'label' => 'Content schrijfrechten',
    'status' => $content_ok ? 'ok' : 'fail',
    'detail' => $content_ok ? 'OK — admin kan content opslaan' : 'GEEN schrijfrechten op content/ — chmod 755',
];

// Schrijfrechten uploads
$uploads_ok = is_writable(__DIR__ . '/../uploads');
$health[] = [
    'label' => 'Uploads schrijfrechten',
    'status' => $uploads_ok ? 'ok' : 'fail',
    'detail' => $uploads_ok ? 'OK — foto-upload werkt' : 'GEEN schrijfrechten op uploads/ — chmod 755',
];

// HTTPS check
$https = !empty($_SERVER['HTTPS']);
$health[] = [
    'label' => 'HTTPS / SSL',
    'status' => $https ? 'ok' : 'warn',
    'detail' => $https ? 'Actief — alle verkeer versleuteld' : 'Site werkt nog op HTTP. Activeer SSL via hosting!',
];

// Config schrijfrechten (voor wachtwoord wijziging)
$config_ok = is_writable(__FILE__) || is_writable(__DIR__ . '/config.php');
$health[] = [
    'label' => 'Wachtwoord wijzigbaar',
    'status' => $config_ok ? 'ok' : 'warn',
    'detail' => $config_ok ? 'OK' : 'admin/config.php niet schrijfbaar — chmod 644',
];

// ZipArchive (voor backup)
$zip_ok = class_exists('ZipArchive');
$health[] = [
    'label' => 'Backup mogelijk',
    'status' => $zip_ok ? 'ok' : 'warn',
    'detail' => $zip_ok ? 'PHP zip extensie beschikbaar' : 'PHP zip extensie ontbreekt — vraag hosting-support',
];

// PHP versie
$php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
$health[] = [
    'label' => 'PHP versie',
    'status' => $php_ok ? 'ok' : 'warn',
    'detail' => 'Versie ' . PHP_VERSION . ($php_ok ? ' (OK)' : ' — verouderd, upgrade naar 8.x')
];

// Aantal recente login-pogingen mislukt
$lockouts = _read_lockouts();
$active_lockouts = 0;
foreach ($lockouts as $ip => $data) {
    if (!empty($data['locked_until']) && $data['locked_until'] > time()) $active_lockouts++;
}
$health[] = [
    'label' => 'Brute-force bescherming',
    'status' => 'ok',
    'detail' => $active_lockouts > 0 ? "$active_lockouts IP(s) momenteel vergrendeld" : 'Geen actieve lockouts',
];

include __DIR__ . '/_layout_top.php';
?>
<div class="main-head">
  <div>
    <h1>Beveiliging</h1>
    <p>Status van je site, login-activiteit en backup-tools.</p>
  </div>
  <div class="head-actions">
    <a href="<?= htmlspecialchars($BASE) ?>/admin/api-backup.php" class="btn btn-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Download backup
    </a>
  </div>
</div>

<div class="card">
  <h2>🩺 Health check</h2>
  <p class="muted">Status van je site. Klik op rode/oranje items voor de oplossing.</p>
  <div class="health-grid">
    <?php foreach ($health as $h): ?>
      <div class="health-item <?= $h['status'] ?>">
        <div class="h-icon">
          <?= $h['status'] === 'ok' ? '✓' : ($h['status'] === 'warn' ? '!' : '✗') ?>
        </div>
        <div>
          <h4><?= htmlspecialchars($h['label']) ?></h4>
          <p><?= htmlspecialchars($h['detail']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <h2>📋 Activiteit log</h2>
  <p class="muted">Laatste 100 acties (logins, content-updates, leads).</p>
  <?php if (empty($activity)): ?>
    <p class="text-muted">Nog geen activiteit.</p>
  <?php else: ?>
    <pre class="log" style="max-height:520px;"><?php
      foreach ($activity as $line) {
        $cls = '';
        if (str_contains($line, '[login]')) $cls = 'color:#6cefa3;';
        elseif (str_contains($line, '[login_fail]') || str_contains($line, '[lockout]')) $cls = 'color:#ff6b6b;';
        elseif (str_contains($line, '[content_save]')) $cls = 'color:#9d92c4;';
        elseif (str_contains($line, '[lead_')) $cls = 'color:#fbbf24;';
        echo '<span style="' . $cls . '">' . htmlspecialchars($line) . "</span>\n";
      }
    ?></pre>
  <?php endif; ?>
</div>

<div class="card">
  <h2>🔒 Beveiligingstips</h2>
  <ul style="margin:0; padding-left:20px; color:var(--text); line-height:1.7;">
    <li>Gebruik een <strong>sterk wachtwoord</strong> van minimaal 12 tekens (cijfers + symbolen)</li>
    <li>Activeer <strong>HTTPS</strong> via je hosting (Let's Encrypt, gratis)</li>
    <li>Download <strong>regelmatig backups</strong> (knop bovenaan deze pagina)</li>
    <li>Check deze activity log <strong>maandelijks</strong> op verdachte logins</li>
    <li>Geef je wachtwoord <strong>nooit door</strong> via e-mail of WhatsApp</li>
    <li>Het systeem vergrendelt IPs <strong>automatisch</strong> na 5 mislukte pogingen (15 min)</li>
    <li>Sessie wordt automatisch <strong>uitgelogd na 1 uur</strong> inactiviteit</li>
  </ul>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
