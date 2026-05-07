<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'contact';
$page_key = 'contact';

$contact = $CONTENT['contact'] ?? [];
$faq = $CONTENT['faq'] ?? [];
$site = $CONTENT['site'] ?? [];
$sections = $CONTENT['sections'] ?? [];
function csec($key, $field, $default = '') { global $sections; return $sections[$key][$field] ?? $default; }

$site_url = rtrim($CONTENT['seo']['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
$page_schemas = [
    schema_breadcrumbs([
        ['name' => 'Home',    'url' => $site_url . '/'],
        ['name' => 'Contact', 'url' => $site_url . '/contact.php'],
    ]),
];
if (!empty($faq)) $page_schemas[] = schema_faq($faq);

// ContactPage schema
$page_schemas[] = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => 'Contact SpinGuard',
    'description' => $CONTENT['seo']['pages']['contact']['description'] ?? '',
    'url' => $site_url . '/contact.php',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Status uit query (na redirect van api/contact.php)
$form_status = null;
$form_message = '';
if (isset($_GET['sent']) && $_GET['sent'] === '1') {
    $form_status = 'success';
} elseif (isset($_GET['error'])) {
    $form_status = 'error';
    $errors_map = [
        'validation' => 'Sommige velden zijn niet correct ingevuld. Controleer naam, telefoon en postcode.',
        'spam'       => 'Uw bericht kon niet verstuurd worden (spamfilter). Probeer het via WhatsApp.',
        'mail'       => 'Er ging iets mis bij het versturen van het e-mail. Stuur ons een WhatsApp-bericht.',
        'method'     => 'Ongeldige aanvraag.',
    ];
    $form_message = $errors_map[$_GET['error']] ?? 'Er ging iets mis. Probeer het opnieuw of via WhatsApp.';
}

require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero has-photo">
    <div class="hero-photo" style="background-image: url('<?= e(b('/assets/photos/sg-bestrijding.webp')) ?>');"></div>
    <div class="container">
      <span class="eyebrow on-dark">Contact</span>
      <h1>Vraag uw vrijblijvende <em>offerte aan.</em></h1>
      <p class="lead"><?= e($contact['subtitle'] ?? 'Stuur enkele foto\'s via WhatsApp of vul het formulier in. Wij reageren binnen 24 uur.') ?></p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Contact']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <section class="section">
    <div class="container">
      <?php include __DIR__ . '/inc/contact_form.php'; ?>
    </div>
  </section>

  <?php if (!empty($faq)): ?>
  <section id="faq" class="section section-soft">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e(csec('contact_faq','eyebrow','Veelgestelde vragen')) ?></span>
        <h2 style="margin-top:16px;"><?= e(csec('contact_faq','heading','Antwoorden op uw vragen.')) ?></h2>
        <p><?= e(csec('contact_faq','intro','Niet gevonden wat u zoekt? Stuur ons een WhatsApp-bericht.')) ?></p>
      </div>
      <div class="faq-list">
        <?php foreach ($faq as $i => $f): ?>
          <div class="faq-item<?= $i === 0 ? ' open' : '' ?>" data-faq>
            <button type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
              <span><?= e($f['q']) ?></span>
              <span class="chev"><?= icon('Plus', 14) ?></span>
            </button>
            <div class="faq-body"><?= e($f['a']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
