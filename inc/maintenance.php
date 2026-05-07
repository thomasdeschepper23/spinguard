<?php
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

http_response_code(503);
header('Retry-After: 3600');

$site = $CONTENT['site'] ?? [];
$m = $CONTENT['maintenance'] ?? [];
$appearance = $CONTENT['appearance'] ?? [];
$primary = $appearance['primary_color']      ?? '#382d72';
$ink     = $appearance['ink_color']           ?? '#0d1224';
$logo    = $appearance['logo']                ?? '/assets/spinguard-logo.png';
?><!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e($m['title'] ?? 'Onderhoud') ?> — <?= e($site['brand'] ?? 'SpinGuard') ?></title>
<style>
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh; display: grid; place-items: center;
    font-family: "Inter", system-ui, -apple-system, sans-serif;
    background: linear-gradient(135deg, <?= e($ink) ?> 0%, #1c1640 100%);
    color: white; padding: 24px;
  }
  .card {
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 24px;
    padding: 48px 36px; max-width: 520px; text-align: center;
    backdrop-filter: blur(12px);
    box-shadow: 0 30px 80px rgba(0,0,0,.5);
  }
  img { height: 60px; margin-bottom: 24px; }
  h1 {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: clamp(28px, 4vw, 38px);
    font-weight: 700; margin: 0 0 14px;
    color: white; letter-spacing: -.02em;
  }
  p { color: #b8b2d6; line-height: 1.6; margin: 0 0 22px; font-size: 15px; }
  .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
  .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 20px; border-radius: 999px;
    font-weight: 600; font-size: 14.5px; text-decoration: none;
  }
  .btn-primary { background: #25d366; color: #062a13; }
  .btn-ghost {
    background: transparent; color: white;
    border: 1.5px solid rgba(255,255,255,.2);
  }
</style>
</head>
<body>
<div class="card">
  <img src="<?= e($logo) ?>" alt="" />
  <h1><?= e($m['title'] ?? 'We zijn even bezig') ?></h1>
  <p><?= nl2br(e($m['message'] ?? 'Onze website wordt momenteel bijgewerkt. Probeer het later opnieuw.')) ?></p>
  <?php if (!empty($site['phone'])): ?>
  <div class="actions">
    <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary">
      💬 WhatsApp <?= e($site['phone']) ?>
    </a>
    <a href="<?= e($site['phone_href'] ?? '#') ?>" class="btn btn-ghost">
      📞 Bel direct
    </a>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
