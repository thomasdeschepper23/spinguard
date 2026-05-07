<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'voorwaarden';
$page_key = 'voorwaarden';
$legal = $CONTENT['legal'] ?? [];
$page_title = ($legal['voorwaarden_title'] ?? 'Algemene voorwaarden') . ' — ' . ($CONTENT['site']['brand'] ?? 'SpinGuard');
$page_desc = 'Algemene voorwaarden van ' . ($CONTENT['site']['brand'] ?? 'SpinGuard') . '.';

require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero">
    <div class="container">
      <span class="eyebrow on-dark">Juridisch</span>
      <h1><?= e($legal['voorwaarden_title'] ?? 'Algemene voorwaarden') ?></h1>
      <p class="lead">De voorwaarden waaronder wij onze diensten leveren.</p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Voorwaarden']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <section class="section">
    <div class="container">
      <div class="legal-content reveal">
        <?= simple_markdown($legal['voorwaarden_content'] ?? '') ?>
      </div>
      <p class="text-muted" style="margin-top:32px; font-size:13px;">
        Laatst bijgewerkt: <?= date('d-m-Y') ?>
      </p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
