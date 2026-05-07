<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'privacy';
$page_key = 'privacy';
$legal = $CONTENT['legal'] ?? [];
$page_title = ($legal['privacy_title'] ?? 'Privacy') . ' — ' . ($CONTENT['site']['brand'] ?? 'SpinGuard');
$page_desc = 'Privacyverklaring van ' . ($CONTENT['site']['brand'] ?? 'SpinGuard') . '.';

require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero">
    <div class="container">
      <span class="eyebrow on-dark">Juridisch</span>
      <h1><?= e($legal['privacy_title'] ?? 'Privacyverklaring') ?></h1>
      <p class="lead">Hoe wij omgaan met uw persoonsgegevens.</p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Privacy']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <section class="section">
    <div class="container">
      <div class="legal-content reveal">
        <?= simple_markdown($legal['privacy_content'] ?? '') ?>
      </div>
      <p class="text-muted" style="margin-top:32px; font-size:13px;">
        Laatst bijgewerkt: <?= date('d-m-Y') ?>
      </p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
