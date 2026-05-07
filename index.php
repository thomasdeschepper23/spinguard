<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'home';
$page_key = 'home';

// SEO schemas voor homepage: FAQ + Service catalog
$page_schemas = [];
if (!empty($CONTENT['faq'])) $page_schemas[] = schema_faq($CONTENT['faq']);
if (!empty($CONTENT['prices'])) $page_schemas[] = schema_service_catalog($CONTENT['prices'], $CONTENT['site'] ?? [], $CONTENT['seo'] ?? []);

$hero    = $CONTENT['hero'] ?? [];
$prices  = $CONTENT['prices'] ?? [];
$process = $CONTENT['process'] ?? [];
$benefits = $CONTENT['benefits'] ?? [];
$ba      = $CONTENT['before_after'] ?? [];
$testim  = $CONTENT['testimonials'] ?? [];
$rev_set = $CONTENT['reviews_settings'] ?? [];
$sections = $CONTENT['sections'] ?? [];
function sec($key, $field, $default = '') {
    global $sections;
    return $sections[$key][$field] ?? $default;
}
$faq     = $CONTENT['faq'] ?? [];
$trust   = $CONTENT['trust_strip'] ?? [];
$contact = $CONTENT['contact'] ?? [];
$site    = $CONTENT['site'] ?? [];
$extras  = $CONTENT['homepage_extras'] ?? [];

require __DIR__ . '/inc/header.php';
?>

<main>
  <!-- ============ HERO ============ -->
  <section id="top" class="hero">
    <svg class="hero-web" viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
      <defs>
        <radialGradient id="webGrad" cx="80%" cy="20%" r="70%">
          <stop offset="0%" stop-color="rgba(255,255,255,.45)"/>
          <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
        </radialGradient>
      </defs>
      <g stroke="url(#webGrad)" stroke-width="1" fill="none">
        <?php for ($i = 0; $i < 14; $i++): ?>
          <?php $angle = (M_PI / 2) + ($i / 14) * M_PI; $x = 1100 + cos($angle) * 1400; $y = 100 + sin($angle) * 1400; ?>
          <line x1="1100" y1="100" x2="<?= $x ?>" y2="<?= $y ?>" />
        <?php endfor; ?>
        <?php foreach ([120, 240, 360, 500, 660, 820] as $r): ?>
          <circle cx="1100" cy="100" r="<?= $r ?>" />
        <?php endforeach; ?>
      </g>
    </svg>
    <div class="container hero-grid">
      <div>
        <span class="eyebrow on-dark"><?= e($hero['eyebrow'] ?? 'Professionele spinnenbestrijding') ?></span>
        <h1>
          <?= e($hero['title_line1'] ?? 'Bescherm uw woning') ?>
          <em><?= e($hero['title_line2_emphasis'] ?? 'tegen spinnen.') ?></em>
        </h1>
        <p class="hero-sub"><?= e($hero['subtitle'] ?? '') ?></p>
        <div class="hero-ctas">
          <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
            <?= e($hero['cta_primary'] ?? 'Offerte aanvragen') ?>
          </a>
          <a href="<?= e(b('/werkwijze.php')) ?>" class="btn btn-ghost btn-lg" style="color:white; border-color:rgba(255,255,255,.18);">
            <?= e($hero['cta_secondary'] ?? 'Onze werkwijze') ?>
          </a>
        </div>
        <div class="hero-trust">
          <?php foreach (($hero['trust_items'] ?? []) as $t): ?>
            <div class="hero-trust-item">
              <?= icon('Check', 16, 'var(--violet-300)') ?>
              <?= e($t) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Price picker card -->
      <div class="hero-card" id="heroPicker" data-prices='<?= e(json_encode(array_slice($prices, 0, 4), JSON_UNESCAPED_UNICODE)) ?>'>
        <div class="hero-card-head">
          <div>
            <div style="font-size:12px; color:#9c95c2; letter-spacing:.1em; text-transform:uppercase;">Direct prijsindicatie</div>
            <div style="font-family:var(--font-display); font-weight:700; font-size:18px; color:white; margin-top:4px;">Kies je woningtype</div>
          </div>
          <span class="pill">Live</span>
        </div>
        <div class="price-tile-list" id="priceTileList"></div>
        <div class="hero-card-foot">
          <small>Inclusief inspectie en zes maanden garantie</small>
          <a href="<?= e(b('/contact.php')) ?>" id="heroQuoteBtn" class="btn btn-light" style="padding:10px 16px; font-size:13px;">Offerte aanvragen</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ HOME GALLERY ============ -->
  <?php $home_gallery = $sections['home_gallery'] ?? []; if (!empty($home_gallery['enabled']) && !empty($home_gallery['items'])): ?>
  <section class="section section-gallery">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e($home_gallery['eyebrow'] ?? 'Ons werk in beeld') ?></span>
        <h2 style="margin-top:16px;"><?= e($home_gallery['heading'] ?? 'Echt gedaan, echt resultaat.') ?></h2>
        <p><?= e($home_gallery['intro'] ?? '') ?></p>
      </div>
      <div class="home-gallery reveal">
        <?php foreach ($home_gallery['items'] as $i => $g):
          $img = $g['image'] ?? ''; if ($img === '') continue;
          $cap = $g['caption'] ?? '';
        ?>
          <figure class="home-gallery-item" style="--i: <?= (int)$i ?>;">
            <img src="<?= e(b($img)) ?>" alt="<?= e($cap) ?>" loading="lazy" />
            <?php if ($cap !== ''): ?><figcaption><?= e($cap) ?></figcaption><?php endif; ?>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ============ TRUST STRIP ============ -->
  <section class="trust-strip">
    <div class="container">
      <div class="trust-grid">
        <?php foreach ($trust as $s): ?>
          <div>
            <div class="stat-num"><?= e($s['num']) ?></div>
            <div class="stat-label"><?= e($s['label']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ DIENSTEN ============ -->
  <section id="diensten" class="section section-soft">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e(sec('home_prices','eyebrow','Tarieven')) ?></span>
        <h2 style="margin-top:16px;"><?= e(sec('home_prices','heading','Heldere prijzen per type pand.')) ?></h2>
        <p><?= e(sec('home_prices','intro','Inclusief inspectie, behandeling en zes maanden garantie. Geen verborgen kosten, geen kleine lettertjes.')) ?></p>
      </div>

      <div class="pricing-grid">
        <?php foreach ($prices as $p): ?>
          <article id="<?= e($p['id']) ?>" class="price-card<?= !empty($p['featured']) ? ' featured' : '' ?> reveal">
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
        <div class="discount-note reveal">
          <span class="badge">KORTING</span>
          <span><?= e($CONTENT['discount_note']) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ WERKWIJZE ============ -->
  <section id="proces" class="section">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e(sec('home_process','eyebrow','Werkwijze')) ?></span>
        <h2 style="margin-top:16px;"><?= e(sec('home_process','heading','Onze aanpak in '.count($process).' stappen.')) ?></h2>
        <p><?= e(sec('home_process','intro','Van eerste contact tot langdurige bescherming — transparant en zonder verrassingen.')) ?></p>
      </div>

      <div class="process-wrap" id="processWrap" data-process='<?= e(json_encode($process, JSON_UNESCAPED_UNICODE)) ?>'>
        <div class="process-list" id="processList"></div>
        <div class="process-detail" id="processDetail"></div>
      </div>
    </div>
  </section>

  <!-- ============ BEFORE / AFTER ============ -->
  <section id="resultaat" class="section section-soft">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= icon('Sparkle', 14) ?> <?= e(sec('home_ba','eyebrow','Het verschil')) ?></span>
        <h2 style="margin-top:16px;"><?= e($ba['title'] ?? sec('home_ba','heading','Direct zichtbaar resultaat.')) ?></h2>
        <p><?= e($ba['subtitle'] ?? sec('home_ba','intro','Sleep de schuifbalk om het verschil te zien.')) ?></p>
      </div>

      <div class="ba-wrap">
        <div class="ba-slider" id="baSlider" style="--pos:50%;">
          <div class="ba-side ba-before" id="baBefore">
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
          <div class="ba-side ba-after" id="baAfter">
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
            Onze behandeling verwijdert webben, nesten en spinneneitjes uit alle hoeken, kozijnen en gevels. Het resultaat is direct zichtbaar — uw woning of bedrijfspand ziet er meteen schoner en verzorgder uit.
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

  <!-- ============ BENEFITS ============ -->
  <section class="section">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e(sec('home_benefits','eyebrow','Waarom SpinGuard')) ?></span>
        <h2 style="margin-top:16px;"><?= e(sec('home_benefits','heading','De standaard in spinvrij wonen.')) ?></h2>
        <p><?= e(sec('home_benefits','intro','Vier redenen waarom particulieren en bedrijven voor SpinGuard kiezen.')) ?></p>
      </div>
      <div class="benefits-grid">
        <?php foreach ($benefits as $b): ?>
          <article class="benefit-card <?= e($b['id']) ?> reveal">
            <div class="icon-bubble"><?= icon($b['icon'] ?? 'Spider', 26) ?></div>
            <h3><?= e($b['title']) ?></h3>
            <p><?= e($b['text']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ TESTIMONIALS ============ -->
  <?php
    $rev_layout = $rev_set['layout'] ?? 'grid';
    $rev_eyebrow  = $rev_set['eyebrow']  ?? 'Klantervaringen';
    $rev_heading  = $rev_set['heading']  ?? 'Vertrouwd door huishoudens en bedrijven.';
    $rev_subtitle = $rev_set['subtitle'] ?? 'Een greep uit ervaringen van klanten — van rijtjeswoning tot parkeergarage.';
    $row_class = 'testimonial-row layout-' . $rev_layout;
  ?>
  <section class="section section-dark">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow on-dark"><?= e($rev_eyebrow) ?></span>
        <h2 style="margin-top:16px; color:white;"><?= e($rev_heading) ?></h2>
        <?php if ($rev_subtitle): ?><p><?= e($rev_subtitle) ?></p><?php endif; ?>
      </div>

      <?php if ($rev_layout === 'trustpilot' && !empty($rev_set['trustpilot_enabled']) && !empty($rev_set['trustpilot_embed'])): ?>
        <div class="trustpilot-wrap reveal">
          <?= $rev_set['trustpilot_embed'] /* admin-managed embed code, not user-input */ ?>
        </div>
      <?php elseif ($rev_layout === 'slider'): ?>
        <div class="t-slider reveal" data-slider>
          <div class="t-slider-track" data-slider-track>
            <?php foreach ($testim as $t): ?>
              <div class="t-slide">
                <div class="t-card">
                  <div class="t-stars">
                    <?php for ($i=0; $i<5; $i++) echo icon('Star', 16, 'var(--violet-400)'); ?>
                  </div>
                  <p class="t-quote">"<?= e($t['quote']) ?>"</p>
                  <div class="t-author">
                    <div class="t-avatar"><?= e($t['initials']) ?></div>
                    <div>
                      <div class="t-meta-name"><?= e($t['name']) ?></div>
                      <div class="t-meta-loc"><?= e($t['loc']) ?></div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="t-slider-arrow t-slider-prev" aria-label="Vorige" data-slider-prev>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <button type="button" class="t-slider-arrow t-slider-next" aria-label="Volgende" data-slider-next>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
          </button>
          <div class="t-slider-dots" data-slider-dots></div>
        </div>
      <?php else: ?>
        <div class="<?= e($row_class) ?>">
          <?php foreach ($testim as $t): ?>
            <div class="t-card reveal">
              <div class="t-stars">
                <?php for ($i=0; $i<5; $i++) echo icon('Star', 16, 'var(--violet-400)'); ?>
              </div>
              <p class="t-quote">"<?= e($t['quote']) ?>"</p>
              <div class="t-author">
                <div class="t-avatar"><?= e($t['initials']) ?></div>
                <div>
                  <div class="t-meta-name"><?= e($t['name']) ?></div>
                  <div class="t-meta-loc"><?= e($t['loc']) ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ FAQ ============ -->
  <section id="faq" class="section section-soft">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow"><?= e(sec('home_faq','eyebrow','Veelgestelde vragen')) ?></span>
        <h2 style="margin-top:16px;"><?= e(sec('home_faq','heading','Antwoorden op uw vragen.')) ?></h2>
        <p><?= e(sec('home_faq','intro','Niet gevonden wat u zoekt? Stuur ons een bericht via WhatsApp.')) ?></p>
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

  <!-- ============ CONTACT ============ -->
  <section id="contact" class="section">
    <div class="container">
      <div class="section-head reveal">
        <span class="eyebrow">Contact</span>
        <h2 style="margin-top:16px;"><?= e($contact['title'] ?? 'Vraag uw vrijblijvende offerte aan.') ?></h2>
        <p><?= e($contact['subtitle'] ?? 'Stuur enkele foto\'s via WhatsApp of vul het formulier in.') ?></p>
      </div>

      <?php include __DIR__ . '/inc/contact_form.php'; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
