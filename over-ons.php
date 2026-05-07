<?php
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$current = 'over';
$page_key = 'over_ons';

$about = $CONTENT['about'] ?? [];
$site = $CONTENT['site'] ?? [];

$site_url = rtrim($CONTENT['seo']['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
$page_schemas = [
    schema_breadcrumbs([
        ['name' => 'Home',     'url' => $site_url . '/'],
        ['name' => 'Over ons', 'url' => $site_url . '/over-ons.php'],
    ]),
];

$values = $about['values'] ?? [
    ['icon' => 'Shield',  'title' => 'Betrouwbaar', 'text' => 'Vaste prijzen, heldere afspraken, garantie op werk.'],
    ['icon' => 'Leaf',    'title' => 'Duurzaam',    'text' => 'Biologisch afbreekbare middelen, veilig voor mens en dier.'],
    ['icon' => 'Clock',   'title' => 'Snel',        'text' => 'Reactie binnen 24 uur, behandeling vaak binnen een week.'],
    ['icon' => 'Sparkle', 'title' => 'Zichtbaar',   'text' => 'Direct na behandeling is het verschil duidelijk te zien.'],
];
$cities = $CONTENT['seo']['service_cities'] ?? [
    'Amsterdam','Rotterdam','Den Haag','Utrecht','Eindhoven','Groningen','Tilburg',
    'Almere','Breda','Nijmegen','Apeldoorn','Haarlem','Arnhem','Amersfoort',
    'Den Bosch','Zwolle','Leeuwarden','Maastricht'
];

$page_title = 'Over ons — ' . ($site['brand'] ?? 'SpinGuard');
$page_desc = $about['intro'] ?? 'Maak kennis met SpinGuard.';

require __DIR__ . '/inc/header.php';
?>
<main>
  <section class="subpage-hero has-photo">
    <div class="hero-photo" style="background-image: url('<?= e(b('/assets/photos/sg-hero-woning.webp')) ?>');"></div>
    <div class="container">
      <span class="eyebrow on-dark"><?= e($about['hero_eyebrow'] ?? 'Over ons') ?></span>
      <h1><?= e($about['hero_title'] ?? 'Specialist in') ?> <em><?= e($about['hero_emphasis'] ?? 'spinvrij wonen.') ?></em></h1>
      <p class="lead"><?= e($about['intro'] ?? 'Bij SpinGuard helpen wij particulieren en bedrijven met het volledig aanpakken van spinnenoverlast — van bestrijding tot langdurige preventie. Veilig voor mens, dier en milieu.') ?></p>
    </div>
  </section>

  <?php $crumbs = [['label'=>'Home','href'=>'/'],['label'=>'Over ons']]; require __DIR__ . '/inc/breadcrumbs.php'; ?>

  <!-- Story + portrait stack -->
  <section class="section">
    <div class="container">
      <div class="story-wrap">
        <div class="story-text">
          <span class="eyebrow"><?= e($about['story_eyebrow'] ?? 'Ons verhaal') ?></span>
          <h2 style="margin-top:16px;"><?= e($about['story_heading'] ?? 'Opgericht uit ergernis met halve oplossingen.') ?></h2>
          <?php
          $story = $about['story'] ?? [
            'SpinGuard is opgericht in Nijmegen en bedient inmiddels heel Nederland. Wij zijn jong, professioneel en gespecialiseerd in één ding: spinnen écht weghouden.',
            'Wij geloven dat spinnenbestrijding niet draait om eenmalig spuiten, maar om een grondige aanpak. Daarom verwijderen wij eerst alle webben en nesten, behandelen we kritieke plekken met biologisch afbreekbare middelen, en geven we zes maanden garantie op het resultaat.',
            'Of het nu om een appartement, rijtjeshuis, vrijstaande woning of bedrijfspand gaat — wij komen, inspecteren, behandelen en garanderen.',
          ];
          foreach ($story as $p): ?>
            <p><?= e($p) ?></p>
          <?php endforeach; ?>
        </div>
        <div class="story-visual">
          <div class="portrait-stack">
            <div class="portrait-card pc-1">
              <?php if (!empty($about['portrait_image_1'])): ?>
                <div class="ph ph-photo" style="background-image: url('<?= e(b($about['portrait_image_1'])) ?>');">
                  <?php if (!empty($about['portrait_label_1'])): ?>
                    <span class="ph-caption"><?= e($about['portrait_label_1']) ?></span>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="ph ph-violet">
                  <?= icon('Spider', 64, '#d6c1f5') ?>
                  <span><?= e($about['portrait_label_1'] ?? 'Team aan het werk') ?></span>
                </div>
              <?php endif; ?>
            </div>
            <div class="portrait-card pc-2">
              <?php if (!empty($about['portrait_image_2'])): ?>
                <div class="ph ph-photo" style="background-image: url('<?= e(b($about['portrait_image_2'])) ?>');">
                  <?php if (!empty($about['portrait_label_2'])): ?>
                    <span class="ph-caption ph-caption-light"><?= e($about['portrait_label_2']) ?></span>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="ph ph-paper">
                  <?= icon('Building', 48, 'var(--violet-600)') ?>
                  <span><?= e($about['portrait_label_2'] ?? 'Behandelde gevel') ?></span>
                </div>
              <?php endif; ?>
            </div>
            <div class="portrait-badge">
              <span class="pb-icon"><?= icon('Shield', 20, 'white') ?></span>
              <div>
                <small><?= e($about['founded_label'] ?? 'Sinds') ?></small>
                <strong><?= e($about['founded_year'] ?? '2024') ?></strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Values -->
  <section class="section section-soft">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?= e($about['values_eyebrow'] ?? 'Onze waarden') ?></span>
        <h2 style="margin-top:16px;"><?= e($about['values_heading'] ?? 'Waar wij voor staan.') ?></h2>
        <p><?= e($about['values_subtitle'] ?? 'Vier kernwaarden die alles wat wij doen sturen.') ?></p>
      </div>
      <div class="values-grid">
        <?php foreach ($values as $v): ?>
          <article class="value-card">
            <div class="v-icon"><?= icon($v['icon'] ?? 'Shield', 26) ?></div>
            <h3><?= e($v['title']) ?></h3>
            <p><?= e($v['text']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Service area + NL kaart -->
  <section class="section">
    <div class="container">
      <div class="area-wrap">
        <div>
          <span class="eyebrow"><?= e($about['area_eyebrow'] ?? 'Werkgebied') ?></span>
          <h2 style="margin-top:16px;"><?= e($about['area_heading'] ?? 'Heel Nederland — vanuit Nijmegen.') ?></h2>
          <p style="color:var(--muted); margin-top:18px; font-size:17px;">
            <?= e($about['area_text'] ?? 'Onze thuisbasis is Nijmegen, maar wij rijden door heel Nederland. Voor afspraken op afstand vragen wij alleen een kleine reisvergoeding bij behandelingen onder €100.') ?>
          </p>
          <div class="city-cloud">
            <?php foreach ($cities as $c): ?>
              <span class="city-pill"><?= e($c) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <?php
        // Map-data + admin instellingen
        $NL_MAP = require __DIR__ . '/inc/nl_map.php';
        $map_settings = $about['map'] ?? [];
        $active_provinces = $map_settings['active_provinces'] ?? $NL_MAP['defaults']['active_provinces'];
        $active_cities    = $map_settings['active_cities']    ?? $NL_MAP['defaults']['active_cities'];
        $hq_slug          = $map_settings['hq_city']          ?? $NL_MAP['defaults']['hq_city'];

        // Verzamel zichtbare steden + HQ
        $visible_cities = [];
        foreach ($active_cities as $slug) {
            if (isset($NL_MAP['cities'][$slug])) $visible_cities[$slug] = $NL_MAP['cities'][$slug];
        }
        // Zorg dat de hoofdvestiging altijd zichtbaar is
        if (isset($NL_MAP['cities'][$hq_slug]) && !isset($visible_cities[$hq_slug])) {
            $visible_cities[$hq_slug] = $NL_MAP['cities'][$hq_slug];
        }
        $hq = $NL_MAP['cities'][$hq_slug] ?? null;
        $show_lines_setting = $map_settings['show_lines'] ?? true;
        $show_lines = !empty($show_lines_setting) && $hq;
        ?>
        <div class="area-map" aria-label="Werkgebied kaart Nederland">
          <svg viewBox="0 0 612.54211 723.61865" preserveAspectRatio="xMidYMid meet" class="nl-map-svg" aria-hidden="true">
            <defs>
              <linearGradient id="nlGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%"   stop-color="#5a4ec0" stop-opacity=".75"/>
                <stop offset="100%" stop-color="#382d72" stop-opacity=".95"/>
              </linearGradient>
              <radialGradient id="hqGlow" cx="50%" cy="50%" r="50%">
                <stop offset="0%"   stop-color="#d6c1f5" stop-opacity=".55"/>
                <stop offset="100%" stop-color="#d6c1f5" stop-opacity="0"/>
              </radialGradient>
              <pattern id="dotGrid" width="22" height="22" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="1.2" fill="rgba(214,193,245,.13)"/>
              </pattern>
              <filter id="softGlow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="5" result="blur"/>
                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
              </filter>
              <filter id="dropShadow" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur in="SourceAlpha" stdDeviation="6"/>
                <feOffset dx="2" dy="6" result="offsetblur"/>
                <feComponentTransfer><feFuncA type="linear" slope=".4"/></feComponentTransfer>
                <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
              </filter>
            </defs>

            <!-- Subtiel dot-grid achtergrond -->
            <rect width="100%" height="100%" fill="url(#dotGrid)"/>

            <!-- Nederland silhouette — provincies krijgen 'inactive' class als ze niet aangevinkt zijn -->
            <g class="nl-provinces" filter="url(#dropShadow)">
              <?php
              $nl_svg_path = __DIR__ . '/assets/netherlands.svg';
              if (is_file($nl_svg_path)) {
                  $nl_svg_raw = file_get_contents($nl_svg_path);
                  if (preg_match('/<svg[^>]*>(.*)<\/svg>/s', $nl_svg_raw, $m)) {
                      $inner = $m[1];
                      // Markeer inactieve provincies via class
                      foreach ($NL_MAP['provinces'] as $pid => $_pname) {
                          if (!in_array($pid, $active_provinces, true)) {
                              $inner = preg_replace(
                                  '/(<path\b[^>]*\bid="' . preg_quote($pid, '/') . '")/i',
                                  '$1 class="inactive"',
                                  $inner
                              );
                          }
                      }
                      echo $inner;
                  }
              }
              ?>
            </g>

            <?php if ($show_lines): ?>
            <!-- Verbindingslijnen vanaf hoofdvestiging naar elke andere stad -->
            <g stroke="rgba(214,193,245,.18)" stroke-width="1.2" stroke-dasharray="2 5">
              <?php foreach ($visible_cities as $slug => $c):
                  if ($slug === $hq_slug) continue; ?>
                <line x1="<?= e($hq['x']) ?>" y1="<?= e($hq['y']) ?>" x2="<?= e($c['x']) ?>" y2="<?= e($c['y']) ?>"/>
              <?php endforeach; ?>
            </g>
            <?php endif; ?>

            <!-- Stad-pins -->
            <?php foreach ($visible_cities as $slug => $c):
              $is_hq = ($slug === $hq_slug);
              $x = $c['x']; $y = $c['y']; ?>
              <g>
                <?php if ($is_hq): ?>
                  <!-- Pulse rings rond hoofdvestiging -->
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="46" fill="url(#hqGlow)"/>
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="28" fill="none" stroke="#d6c1f5" stroke-width="1.5" opacity=".5">
                    <animate attributeName="r" values="20;55;20" dur="3.2s" repeatCount="indefinite"/>
                    <animate attributeName="opacity" values=".7;0;.7" dur="3.2s" repeatCount="indefinite"/>
                  </circle>
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="14" fill="#d6c1f5" filter="url(#softGlow)"/>
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="8" fill="white"/>
                <?php else: ?>
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="5.5" fill="rgba(255,255,255,.95)"/>
                  <circle cx="<?= $x ?>" cy="<?= $y ?>" r="10" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="1.5"/>
                <?php endif; ?>
                <text
                  x="<?= $x + ($c['ox'] ?? 16) ?>"
                  y="<?= $y + ($c['oy'] ?? 6) ?>"
                  fill="<?= $is_hq ? 'white' : '#e3ddf2' ?>"
                  font-size="<?= $is_hq ? '20' : '17' ?>"
                  font-family="Inter, sans-serif"
                  font-weight="<?= $is_hq ? '700' : '500' ?>"
                  text-anchor="<?= e($c['anchor'] ?? 'start') ?>"
                  letter-spacing="<?= $is_hq ? '0.02em' : '0' ?>"
                ><?= e($c['name']) ?></text>
              </g>
            <?php endforeach; ?>
          </svg>

          <div class="area-legend">
            <span><span class="dot-hq"></span><?= e(($hq['name'] ?? 'Hoofdvestiging')) ?> — hoofdvestiging</span>
            <span><span class="dot-city"></span>Werkgebied</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Banner -->
  <section class="section">
    <div class="container">
      <div class="cta-banner">
        <h2><?= e($about['cta_title'] ?? 'Klaar om kennis te maken?') ?></h2>
        <p><?= e($about['cta_text'] ?? 'Stuur ons een bericht via WhatsApp of bel ons direct. Wij helpen u graag verder.') ?></p>
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
