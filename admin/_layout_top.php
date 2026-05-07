<?php
$current = basename($_SERVER['PHP_SELF'], '.php');
$BASE = admin_base_url();

// Count nieuwe leads voor sidebar badge (alleen als ingelogd)
$sidebar_new_leads = 0;
if (function_exists('admin_is_logged_in') && admin_is_logged_in()) {
    if (!defined('SPINGUARD_INC')) define('SPINGUARD_INC', true);
    if (!function_exists('leads_load')) require __DIR__ . '/../inc/leads_store.php';
    $_all_leads = leads_load();
    foreach ($_all_leads as $_l) {
        if (($_l['status'] ?? 'nieuw') === 'nieuw') $sidebar_new_leads++;
    }
}
?><!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SpinGuard Admin</title>
  <link rel="stylesheet" href="<?= htmlspecialchars($BASE) ?>/admin/css/admin.css?v=9" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <script>window.SPINGUARD_BASE = <?= json_encode($BASE) ?>;</script>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= htmlspecialchars($BASE) ?>/assets/spinguard-logo.png" alt="SpinGuard" />
      <strong>Admin</strong>
    </div>
    <nav>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/dashboard.php" class="<?= $current==='dashboard' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
        Dashboard
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/edit.php" class="<?= $current==='edit' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 L20 10 L11 19 H5 V13 Z"/><path d="M13 5 L19 11"/></svg>
        Content bewerken
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/photos.php" class="<?= $current==='photos' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M3 17 L9 13 L15 17 L21 13"/></svg>
        Foto's
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php" class="<?= $current==='leads' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7 L12 13 L21 7"/></svg>
        Aanvragen
        <?php if ($sidebar_new_leads > 0): ?>
          <span class="sidebar-badge"><?= $sidebar_new_leads ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/security.php" class="<?= $current==='security' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 L4 6 V12 C4 16.5 7.5 19.8 12 21 C16.5 19.8 20 16.5 20 12 V6 Z"/><path d="M9 12 l2 2 4-4"/></svg>
        Beveiliging
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/wachtwoord.php" class="<?= $current==='wachtwoord' ? 'active' : '' ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11 V8 C8 5.8 9.8 4 12 4 C14.2 4 16 5.8 16 8 V11"/></svg>
        Wachtwoord
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/" target="_blank">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 H20 V10 M10 14 L20 4 M19 14 V19 C19 20 18 21 17 21 H5 C4 21 3 20 3 19 V7 C3 6 4 5 5 5 H10"/></svg>
        Bekijk website
      </a>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/logout.php" style="margin-top:14px; color:#ffa080;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21 H5 C4 21 3 20 3 19 V5 C3 4 4 3 5 3 H9 M16 17 L21 12 L16 7 M21 12 H9"/></svg>
        Uitloggen
      </a>
    </nav>
    <div class="footer-info">
      SpinGuard Admin v1.0
    </div>
  </aside>

  <main class="main">
