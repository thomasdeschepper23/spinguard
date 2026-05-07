<?php
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }
// Verwacht $crumbs = [['label' => '...', 'href' => '...'], ...]
$crumbs = $crumbs ?? [];
if (empty($crumbs)) return;
?>
<nav class="crumbs" aria-label="Kruimelpad">
  <div class="container">
    <?php foreach ($crumbs as $i => $c): ?>
      <?php if ($i > 0): ?><span class="crumb-sep">/</span><?php endif; ?>
      <?php if (!empty($c['href'])): ?>
        <a href="<?= e(b($c['href'])) ?>"><?= e($c['label']) ?></a>
      <?php else: ?>
        <span aria-current="page"><?= e($c['label']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</nav>
