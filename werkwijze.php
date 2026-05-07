<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'werkwijze';
$page_key = 'werkwijze';

$process = $CONTENT['process'] ?? [];
$ba = $CONTENT['before_after'] ?? [];
$site = $CONTENT['site'] ?? [];

$site_url = rtrim($CONTENT['seo']['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
$page_schemas = [
    schema_breadcrumbs([
        ['name' => 'Home',      'url' => $site_url . '/'],
        ['name' => 'Werkwijze', 'url' => $site_url . '/werkwijze.php'],
    ]),
];

if (!empty($process)) {
    $steps = [];
    $i = 1;
    foreach ($process as $s) {
        $steps[] = [
            '@type' => 'HowToStep',
            'position' => $i++,
            'name' => (string)($s['title'] ?? ''),
            'text' => (string)($s['long'] ?? $s['short'] ?? ''),
        ];
    }
    $page_schemas[] = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => 'Hoe SpinGuard uw woning spinvrij maakt',
        'description' => 'Stappenplan van aanvraag tot langdurige bescherming.',
        'step' => $steps,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

$sections = $CONTENT['sections'] ?? [];
$ctas = $CONTENT['ctas'] ?? [];
$timeline = $CONTENT['timeline']['items'] ?? [];
function wsec($key, $field, $default = '') { global $sections; return $sections[$key][$field] ?? $default; }
require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero">
    <div class="container">
      <span class="eyebrow on-dark"><?= e(wsec('werkwijze_hero','eyebrow','Werkwijze')) ?></span>
      <h1><?= e(wsec('werkwijze_hero','heading','Zo werken wij bij SpinGuard.')) ?></h1>
      <p class="lead"><?= e(wsec('werkwijze_hero','intro','Een eenvoudig proces voor een spinvrij resultaat. Van eerste contact tot langdurige bescherming — transparant, professioneel en zonder verrassingen.')) ?></p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Werkwijze']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <!-- 5-stappen process (interactief) -->
  <section id="proces" class="section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?= e(wsec('home_process','eyebrow','Werkwijze')) ?></span>
        <h2 style="margin-top:16px;"><?= e(wsec('home_process','heading','Onze aanpak in '.count($process).' stappen.')) ?></h2>
        <p><?= e(wsec('home_process','intro','Van eerste contact tot langdurige bescherming — transparant en zonder verrassingen.')) ?></p>
      </div>

      <div class="process-wrap" id="processWrap" data-process='<?= e(json_encode($process, JSON_UNESCAPED_UNICODE)) ?>'>
        <div class="process-list" id="processList"></div>
        <div class="process-detail" id="processDetail"></div>
      </div>
    </div>
  </section>

  <!-- Timeline / day-of overview -->
  <?php if (!empty($timeline)): ?>
  <section class="section section-soft">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?= e(wsec('werkwijze_timeline','eyebrow','Op de dag zelf')) ?></span>
        <h2 style="margin-top:16px;"><?= e(wsec('werkwijze_timeline','heading','Wat te verwachten?')) ?></h2>
        <p><?= e(wsec('werkwijze_timeline','intro','Een typische behandeling duurt 1–2 uur. U hoeft thuis niet aanwezig te zijn — toegang tot gevels en buitenruimte volstaat.')) ?></p>
      </div>
      <div class="timeline">
        <?php foreach ($timeline as $s): ?>
          <div class="tl-row">
            <div class="tl-time"><?= e($s['time'] ?? '') ?></div>
            <div class="tl-bullet"></div>
            <div class="tl-card">
              <h4><?= e($s['title'] ?? '') ?></h4>
              <p><?= e($s['text'] ?? '') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Materials & approach -->
  <?php
    $mat = $CONTENT['materials'] ?? [];
    $mat_enabled = array_key_exists('enabled', $mat) ? !empty($mat['enabled']) : true;
    $mat_eyebrow = $mat['eyebrow'] ?? 'Materialen & aanpak';
    $mat_heading = $mat['heading'] ?? 'Veilig voor mens, dier en plant.';
    $mat_intro   = $mat['intro']   ?? 'Wij werken uitsluitend met EU-toegelaten middelen die — eenmaal opgedroogd — geen risico vormen voor uw gezin of huisdieren. De middelen zijn watergebaseerd, geurarm en biologisch afbreekbaar.';
    $mat_card_eyebrow = $mat['card_eyebrow'] ?? 'Onze toolkit';
    $mat_bullets = $mat['bullets'] ?? [
      'Watergebaseerd, geurarm',
      'Geen schade aan gevel, kozijnen of planten',
      'Veilig voor kinderen en huisdieren na opdroging',
      'Werkzaam tegen alle in NL voorkomende spinsoorten',
      'Toegelaten door het Ctgb',
    ];
    $mat_tools = $mat['tools'] ?? [
      ['icon' => 'Spray',  'title' => 'Sprayer',       'subtitle' => 'Lage druk · gericht'],
      ['icon' => 'Doc',    'title' => 'Rapportage',    'subtitle' => 'Per e-mail'],
      ['icon' => 'Shield', 'title' => 'Garantie',      'subtitle' => '6 maanden'],
      ['icon' => 'Camera', 'title' => "Foto's vooraf", 'subtitle' => 'WhatsApp'],
    ];
  ?>
  <?php if ($mat_enabled): ?>
  <section class="section">
    <div class="container">
      <div class="materials-wrap">
        <div>
          <span class="eyebrow"><?= e($mat_eyebrow) ?></span>
          <h2 style="margin-top:16px;"><?= e($mat_heading) ?></h2>
          <p style="color:var(--muted); margin-top:18px; font-size:17px;"><?= e($mat_intro) ?></p>
          <ul class="check-list">
            <?php foreach ($mat_bullets as $b): ?>
              <li><span class="cl-tick"><?= icon('Check', 14) ?></span><?= e($b) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="materials-card">
          <div class="materials-head">
            <span class="eyebrow on-dark"><?= e($mat_card_eyebrow) ?></span>
          </div>
          <div class="materials-grid">
            <?php foreach ($mat_tools as $m): ?>
              <div class="material-tile">
                <div class="mt-icon"><?= icon($m['icon'] ?? 'Sparkle', 20, '#d6c1f5') ?></div>
                <div>
                  <div class="mt-k"><?= e($m['title'] ?? '') ?></div>
                  <div class="mt-v"><?= e($m['subtitle'] ?? '') ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Before/After slider (gedeelde sectie) -->
  <section id="resultaat" class="section section-soft">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?= icon('Sparkle', 14) ?> Het verschil</span>
        <h2 style="margin-top:16px;"><?= e($ba['title'] ?? 'Direct zichtbaar resultaat.') ?></h2>
        <p><?= e($ba['subtitle'] ?? '') ?></p>
      </div>

      <div class="ba-wrap">
        <div class="ba-slider" id="baSlider" style="--pos:50%;">
          <div class="ba-side ba-before">
            <?php if (!empty($ba['before_image'])): ?>
              <div class="ba-photo-real" style="background-image: url('<?= e(b($ba['before_image'])) ?>');"></div>
              <span class="ba-tag before">Before</span>
            <?php else: ?>
              <div class="ba-photo">
                <div style="position:absolute; inset:0; background:repeating-linear-gradient(135deg, rgba(255,255,255,.04) 0 12px, transparent 12px 24px);"></div>
                <div class="ba-side-content" style="color:#d6c1f5; position:relative;">
                  <?= icon('Spider', 56, '#d6c1f5') ?>
                  <div class="label-big">Vóór behandeling</div>
                </div>
              </div>
              <span class="ba-tag before">Before</span>
            <?php endif; ?>
          </div>
          <div class="ba-side ba-after">
            <?php if (!empty($ba['after_image'])): ?>
              <div class="ba-photo-real" style="background-image: url('<?= e(b($ba['after_image'])) ?>');"></div>
              <span class="ba-tag after">After</span>
            <?php else: ?>
              <div class="ba-photo">
                <div style="position:absolute; inset:0; background:repeating-linear-gradient(135deg, rgba(255,255,255,.18) 0 12px, transparent 12px 24px);"></div>
                <div class="ba-side-content" style="color:#1f1140; position:relative;">
                  <?= icon('Sparkle', 56, '#5a2ea3') ?>
                  <div class="label-big">Na behandeling</div>
                </div>
              </div>
              <span class="ba-tag after">After</span>
            <?php endif; ?>
          </div>
          <div class="ba-handle"></div>
        </div>
        <div>
          <h3 style="font-size:28px; margin-bottom:16px;">Het verschil van één behandeling.</h3>
          <p style="color:var(--muted); margin-bottom:22px; font-size:16px;">
            Onze behandeling verwijdert webben, nesten en spinneneitjes uit alle hoeken, kozijnen en gevels. Het resultaat is direct zichtbaar.
          </p>
          <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
            <?php foreach (($ba['bullets'] ?? []) as $b): ?>
              <li style="display:flex; gap:10px; align-items:center; font-size:15px;">
                <span style="width:24px; height:24px; border-radius:50%; background:var(--violet-50); color:var(--violet-700); display:grid; place-items:center; flex-shrink:0;">
                  <?= icon('Check', 14) ?>
                </span>
                <?= e($b) ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg" style="margin-top:28px;">
            Vraag uw offerte aan <?= icon('Arrow', 16, 'white') ?>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section">
    <div class="container">
      <div class="cta-banner">
        <h2><?= e($ctas['werkwijze']['title'] ?? 'Klaar om te starten?') ?></h2>
        <p><?= e($ctas['werkwijze']['text'] ?? 'Stuur ons een foto van uw pand. Wij sturen binnen 24 uur een vrijblijvende offerte op maat.') ?></p>
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
