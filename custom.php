<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$key = $_GET['p'] ?? '';
$pages = $CONTENT['custom_pages']['items'] ?? [];

$page = null;
foreach ($pages as $p) {
    if (($p['key'] ?? '') === $key) { $page = $p; break; }
}

if (!$page) {
    http_response_code(404);
    $current = '';
    $page_title = '404 — Pagina niet gevonden';
    $page_desc = 'Deze pagina bestaat niet of is verwijderd.';
    require __DIR__ . '/inc/header.php';
    ?>
    <main>
      <section class="subpage-hero">
        <div class="container">
          <span class="eyebrow on-dark">404</span>
          <h1>Pagina niet gevonden</h1>
          <p class="lead">Deze pagina bestaat niet of is verwijderd. Ga terug naar de homepage of neem contact met ons op.</p>
        </div>
      </section>
      <section class="section">
        <div class="container" style="text-align:center;">
          <a href="<?= e(b('/')) ?>" class="btn btn-primary btn-lg">Terug naar home</a>
        </div>
      </section>
    </main>
    <?php
    require __DIR__ . '/inc/footer.php';
    exit;
}

$current = 'custom_' . $key;
$page_title = ($page['title'] ?? 'Pagina') . ' — ' . ($CONTENT['site']['brand'] ?? 'SpinGuard');
$page_desc = $page['meta_description'] ?? '';

$mode   = $page['mode']   ?? 'markdown';
$layout = $page['layout'] ?? 'default';
$css    = $page['custom_css'] ?? '';

// Eigen CSS injecten in <head> via een hook in header.php — we gebruiken een globale variabele
$EXTRA_HEAD_CSS = trim($css);

// Layout 'raw' = geen header/footer (alleen html-skelet rond content)
if ($layout === 'raw') {
    $brand = $CONTENT['site']['brand'] ?? 'SpinGuard';
    $logo = $CONTENT['appearance']['logo'] ?? '/assets/spinguard-logo.png';
    ?><!doctype html>
    <html lang="nl">
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title><?= e($page_title) ?></title>
      <?php if ($page_desc): ?><meta name="description" content="<?= e($page_desc) ?>" /><?php endif; ?>
      <link rel="stylesheet" href="<?= e(b('/styles.css?v=9')) ?>" />
      <link rel="icon" type="image/png" href="<?= e(b($logo)) ?>" />
      <?php if ($EXTRA_HEAD_CSS): ?><style><?= $EXTRA_HEAD_CSS /* admin-input, raw output */ ?></style><?php endif; ?>
    </head>
    <body>
      <?= $page['content'] ?? '' /* admin-input, raw output (HTML mode) */ ?>
    </body>
    </html>
    <?php
    exit;
}

require __DIR__ . '/inc/header.php';

// Bepaal hoe de content gerenderd wordt
$render_content = function() use ($page, $mode) {
    $content = $page['content'] ?? '';
    if ($mode === 'html') {
        // Raw HTML — admin-input, niet escapen
        echo $content;
    } else {
        // Markdown
        echo simple_markdown($content);
    }
};
?>
<main>
  <?php if ($layout === 'default'): ?>
    <section class="subpage-hero">
      <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><span class="eyebrow on-dark"><?= e($page['eyebrow']) ?></span><?php endif; ?>
        <h1><?= e($page['title'] ?? 'Pagina') ?></h1>
        <?php if (!empty($page['intro'])): ?><p class="lead"><?= e($page['intro']) ?></p><?php endif; ?>
      </div>
    </section>
    <section class="section">
      <div class="container">
        <div class="legal-content reveal">
          <?php $render_content(); ?>
        </div>
      </div>
    </section>
  <?php else: /* layout=blank — geen hero, content vult zelf de hele breedte */ ?>
    <section class="section" style="padding-top: 60px;">
      <div class="container">
        <?php $render_content(); ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
