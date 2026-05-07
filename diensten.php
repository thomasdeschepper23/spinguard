<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'diensten';
$page_key = 'diensten';

$prices = $CONTENT['prices'] ?? [];
$benefits = $CONTENT['benefits'] ?? [];
$site = $CONTENT['site'] ?? [];

$site_url = rtrim($CONTENT['seo']['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
$page_schemas = [
    schema_service_catalog($prices, $site, $CONTENT['seo'] ?? []),
    schema_breadcrumbs([
        ['name' => 'Home',     'url' => $site_url . '/'],
        ['name' => 'Diensten', 'url' => $site_url . '/diensten.php'],
    ]),
];

$sections = $CONTENT['sections'] ?? [];
$ctas = $CONTENT['ctas'] ?? [];
function dsec($key, $field, $default = '') { global $sections; return $sections[$key][$field] ?? $default; }
require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero">
    <div class="container">
      <span class="eyebrow on-dark"><?= e(dsec('diensten_hero','eyebrow','Diensten & tarieven')) ?></span>
      <h1><?= e(dsec('diensten_hero','heading','Heldere prijzen voor elk type pand.')) ?></h1>
      <p class="lead"><?= e(dsec('diensten_hero','intro','Vaste tarieven, geen verborgen kosten. Inclusief inspectie, behandeling en zes maanden garantie. Vraag direct een vrijblijvende offerte aan via WhatsApp.')) ?></p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Diensten']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <section class="section section-soft">
    <div class="container">
      <div class="pricing-grid">
        <?php foreach ($prices as $p): ?>
          <article id="<?= e($p['id']) ?>" class="price-card<?= !empty($p['featured']) ? ' featured' : '' ?>">
            <div class="icon-circle"><?= icon($p['icon'] ?? 'Building', 24) ?></div>
            <h3><?= e($p['name']) ?></h3>
            <div style="font-size:13px; opacity:.75; margin-top:4px;"><?= e($p['meta'] ?? '') ?></div>
            <div class="price-row">
              <?php if (!empty($p['price'])): ?>
                <span class="from">v.a.</span>
                <span class="amount">€<?= e($p['price']) ?></span>
                <span class="from">,-</span>
              <?php else: ?>
                <span class="amount text"><?= e($p['price_label'] ?? 'Op aanvraag') ?></span>
              <?php endif; ?>
            </div>
            <ul>
              <?php foreach (($p['features'] ?? []) as $f): ?>
                <li><?= icon('Check', 16) ?><span><?= e($f) ?></span></li>
              <?php endforeach; ?>
            </ul>
            <a href="<?= e(wa_link('Hallo SpinGuard, ik wil graag een offerte voor: ' . $p['name'])) ?>" target="_blank" rel="noopener"
               class="btn <?= !empty($p['featured']) ? 'btn-light' : 'btn-ghost' ?>"
               style="margin-top:22px; width:100%; justify-content:center;">
              Offerte aanvragen
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($CONTENT['discount_note'])): ?>
        <div class="discount-note">
          <span class="badge">KORTING</span>
          <span><?= e($CONTENT['discount_note']) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Inbegrepen bij elke behandeling -->
  <section class="section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?= e(dsec('diensten_benefits','eyebrow','Inbegrepen bij elke behandeling')) ?></span>
        <h2 style="margin-top:16px;"><?= e(dsec('diensten_benefits','heading','Wat krijgt u voor uw geld?')) ?></h2>
        <p><?= e(dsec('diensten_benefits','intro','Bij elke behandeling — ongeacht het type pand — krijgt u dezelfde grondige aanpak.')) ?></p>
      </div>
      <div class="benefits-grid">
        <?php foreach ($benefits as $b): ?>
          <article class="benefit-card <?= e($b['id']) ?>">
            <div class="icon-bubble"><?= icon($b['icon'] ?? 'Spider', 26) ?></div>
            <h3><?= e($b['title']) ?></h3>
            <p><?= e($b['text']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Vergelijkingstabel: DIY vs SpinGuard -->
  <?php
    $compare = $CONTENT['compare'] ?? [];
    $compare_enabled = array_key_exists('enabled', $compare) ? !empty($compare['enabled']) : true;
    $compare_rows = $compare['rows'] ?? [
      ['label' => 'Verwijdering van webben & nesten',           'them' => 'no',   'us' => 'yes'],
      ['label' => 'Behandeling op moeilijk bereikbare plekken', 'them' => 'no',   'us' => 'yes'],
      ['label' => 'Biologisch afbreekbare middelen',            'them' => 'soms', 'us' => 'yes'],
      ['label' => 'Werkzaam tot 6 maanden',                     'them' => 'no',   'us' => 'yes'],
      ['label' => 'Garantie bij hernieuwde overlast',           'them' => 'no',   'us' => 'yes'],
      ['label' => 'Inspectie ter plekke',                       'them' => 'no',   'us' => 'yes'],
    ];
    $compare_render_dot = function($v) {
      if ($v === 'yes' || $v === true)   return '<span class="dot dot-on">✓</span>';
      if ($v === 'soms' || $v === 'mid') return '<span class="dot dot-mid">±</span>';
      return '<span class="dot dot-off">—</span>';
    };
  ?>
  <?php if ($compare_enabled): ?>
  <section class="section section-soft">
    <div class="container">
      <div class="compare-wrap">
        <div class="section-head" style="text-align:left; margin:0;">
          <span class="eyebrow"><?= e($compare['eyebrow'] ?? 'Vergelijking') ?></span>
          <h2 style="margin-top:16px;"><?= e($compare['heading'] ?? 'SpinGuard versus zelf doen.') ?></h2>
          <p style="margin-top:18px;"><?= e($compare['intro'] ?? 'Een grondige professionele behandeling werkt maandenlang door — zelfs goedkope DIY-middelen halen die diepte niet.') ?></p>
        </div>
        <div class="compare-table">
          <div class="compare-row compare-head">
            <div></div>
            <div class="col-them"><?= e($compare['col_them'] ?? 'DIY / supermarkt') ?></div>
            <div class="col-us"><?= e($compare['col_us'] ?? 'SpinGuard') ?></div>
          </div>
          <?php foreach ($compare_rows as $row): ?>
            <div class="compare-row">
              <div class="compare-label"><?= e($row['label'] ?? '') ?></div>
              <div class="col-them"><?= $compare_render_dot($row['them'] ?? 'no') ?></div>
              <div class="col-us"><?= $compare_render_dot($row['us'] ?? 'yes') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- CTA -->
  <section class="section">
    <div class="container">
      <div class="cta-banner">
        <h2><?= e($ctas['diensten']['title'] ?? 'Klaar voor een spinvrije woning?') ?></h2>
        <p><?= e($ctas['diensten']['text'] ?? 'Stuur ons een paar foto\'s van uw pand via WhatsApp. Binnen 24 uur ontvangt u een vrijblijvende offerte op maat.') ?></p>
        <div class="btn-row">
          <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-light btn-lg">
            <?= icon_whatsapp(18, '#062a13') ?> WhatsApp ons
          </a>
          <a href="<?= e(b('/contact.php')) ?>" class="btn btn-ghost btn-lg" style="color:white; border-color:rgba(255,255,255,.2);">
            Of via formulier
          </a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
