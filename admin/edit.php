<?php
require __DIR__ . '/config.php';
admin_require_login();

$CONTENT_FILE = __DIR__ . '/../content/site.json';
$raw = @file_get_contents($CONTENT_FILE);
$content = json_decode($raw ?: '{}', true) ?: [];

// Map data (provincies + steden lijst)
if (!defined('SPINGUARD_INC')) define('SPINGUARD_INC', true);
$NL_MAP = require __DIR__ . '/../inc/nl_map.php';

$tok = csrf_token();
$saved = isset($_GET['saved']);
$err   = $_GET['err'] ?? '';

// Helper voor JSON output in input
function fld($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

// Image-picker widget: hidden input + visuele preview + knoppen
function image_picker($name, $value, $base) {
    $id = 'pk-' . preg_replace('/[\[\]]+/', '-', trim($name, '[]'));
    $has = !empty($value);
    $url = $has ? $base . str_replace('/uploads/', '/uploads/', $value) : '';
    ?>
    <div class="image-picker" data-picker>
      <input type="hidden" name="<?= fld($name) ?>" value="<?= fld($value) ?>" id="<?= $id ?>" data-picker-input />
      <div class="image-picker-preview <?= $has ? 'has-image' : 'is-empty' ?>"
           <?= $has ? 'style="background-image:url(\''.fld($url).'\')"' : '' ?>
           data-picker-preview>
        <?php if (!$has): ?>
          <div class="picker-empty-state">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="5" width="18" height="14" rx="2"/>
              <circle cx="9" cy="11" r="2"/>
              <path d="M3 17 L9 13 L15 17 L21 13"/>
            </svg>
            <span>Nog geen foto gekozen</span>
          </div>
        <?php endif; ?>
        <div class="picker-overlay">
          <button type="button" class="btn btn-light btn-sm" data-picker-open="<?= $id ?>">
            <?= $has ? 'Andere foto kiezen' : 'Foto kiezen' ?>
          </button>
        </div>
      </div>
      <div class="image-picker-actions">
        <button type="button" class="btn btn-ghost btn-sm" data-picker-open="<?= $id ?>">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M3 17 L9 13 L15 17 L21 13"/></svg>
          <?= $has ? 'Andere foto' : 'Kies foto' ?>
        </button>
        <button type="button" class="btn btn-ghost btn-sm" data-picker-clear="<?= $id ?>" <?= !$has ? 'style="display:none"' : '' ?>>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6 L18 18 M18 6 L6 18"/></svg>
          Leegmaken
        </button>
      </div>
    </div>
    <?php
}

include __DIR__ . '/_layout_top.php';
?>
<div class="main-head">
  <div>
    <h1>Content bewerken</h1>
    <p>Pas alle teksten, prijzen en gegevens aan. Wijzigingen zijn meteen live.</p>
  </div>
  <div class="head-actions">
    <a href="<?= htmlspecialchars($BASE) ?>/" target="_blank" class="btn btn-ghost">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 H20 V10 M10 14 L20 4 M19 14 V19 C19 20 18 21 17 21 H5 C4 21 3 20 3 19 V7 C3 6 4 5 5 5 H10"/></svg>
      Bekijk website
    </a>
  </div>
</div>

<?php if ($saved): ?>
  <div class="notice success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 12 l3 3 5-6"/></svg>
    <strong>Opgeslagen.</strong> Je wijzigingen zijn live.
  </div>
<?php endif; ?>
<?php if ($err): ?>
  <div class="notice error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v5"/></svg>
    <?= htmlspecialchars($err) ?>
  </div>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($BASE) ?>/admin/save.php" id="editForm">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
  <input type="hidden" name="_user_save" value="1" />
  <input type="hidden" name="_tab" id="currentTab" value="site" />

  <div class="tabs-wrap">
    <div class="tabs" id="tabs">
      <span class="tab-group-label">Algemeen</span>
      <button type="button" data-tab="site" class="active" onclick="adminShowTab('site')">Bedrijfsgegevens</button>
      <button type="button" data-tab="hero" onclick="adminShowTab('hero')">Hero</button>

      <span class="tab-divider" aria-hidden="true"></span>
      <span class="tab-group-label">Content</span>
      <button type="button" data-tab="prices" onclick="adminShowTab('prices')">Prijzen <span class="tab-count"><?= count($content['prices'] ?? []) ?></span></button>
      <button type="button" data-tab="process" onclick="adminShowTab('process')">Werkwijze <span class="tab-count"><?= count($content['process'] ?? []) ?></span></button>
      <button type="button" data-tab="benefits" onclick="adminShowTab('benefits')">Voordelen <span class="tab-count"><?= count($content['benefits'] ?? []) ?></span></button>
      <button type="button" data-tab="testimonials" onclick="adminShowTab('testimonials')">Reviews <span class="tab-count"><?= count($content['testimonials'] ?? []) ?></span></button>
      <button type="button" data-tab="faq" onclick="adminShowTab('faq')">FAQ <span class="tab-count"><?= count($content['faq'] ?? []) ?></span></button>
      <button type="button" data-tab="about" onclick="adminShowTab('about')">Over ons</button>

      <span class="tab-divider" aria-hidden="true"></span>
      <span class="tab-group-label">Pagina's</span>
      <button type="button" data-tab="page_texts" onclick="adminShowTab('page_texts')">Pagina-teksten</button>
      <button type="button" data-tab="legal" onclick="adminShowTab('legal')">Juridisch</button>
      <button type="button" data-tab="custom_pages" onclick="adminShowTab('custom_pages')">Eigen pagina's</button>

      <span class="tab-divider" aria-hidden="true"></span>
      <span class="tab-group-label">Vormgeving</span>
      <button type="button" data-tab="appearance" onclick="adminShowTab('appearance')">Vormgeving</button>
      <button type="button" data-tab="navigation" onclick="adminShowTab('navigation')">Menu</button>
      <button type="button" data-tab="footer" onclick="adminShowTab('footer')">Footer</button>

      <span class="tab-divider" aria-hidden="true"></span>
      <span class="tab-group-label">Instellingen</span>
      <button type="button" data-tab="leads_settings" onclick="adminShowTab('leads_settings')">Leads</button>
      <button type="button" data-tab="marketing" onclick="adminShowTab('marketing')">Marketing</button>
      <button type="button" data-tab="other" onclick="adminShowTab('other')">SEO</button>
      <button type="button" data-tab="advanced" onclick="adminShowTab('advanced')">Geavanceerd</button>
    </div>
  </div>

  <script>
    /* Inline tab-switcher — onafhankelijk van admin.js zodat tabs ALTIJD werken */
    function adminShowTab(id) {
      var tabsEl = document.getElementById('tabs');
      if (!tabsEl) return;
      var btns = tabsEl.querySelectorAll('button');
      for (var i = 0; i < btns.length; i++) {
        btns[i].classList.toggle('active', btns[i].getAttribute('data-tab') === id);
      }
      var panels = document.querySelectorAll('.tab-panel');
      for (var j = 0; j < panels.length; j++) {
        panels[j].classList.toggle('active', panels[j].getAttribute('data-panel') === id);
      }
      var f = document.getElementById('currentTab');
      if (f) f.value = id;
      try { if (history.replaceState) history.replaceState(null, '', '#tab-' + id); } catch (e) {}
      try { window.scrollTo(0, 0); } catch (e) {}
    }
    /* Init op page-load: open tab uit URL hash als die er is */
    (function(){
      var hash = (location.hash || '').replace('#tab-', '');
      if (hash) adminShowTab(hash);
    })();
  </script>

  <!-- ============ TAB: SITE ============ -->
  <div class="tab-panel active" data-panel="site" id="tab-site">
    <div class="card">
      <h2>Bedrijfsgegevens</h2>
      <p class="muted">Naam, contactgegevens en KvK. Deze info verschijnt in de header, footer en op alle pagina's.</p>
      <div class="field-row">
        <div class="field"><label>Bedrijfsnaam</label>
          <input type="text" name="site[brand]" value="<?= fld($content['site']['brand'] ?? '') ?>" /></div>
        <div class="field"><label>Tagline / slogan</label>
          <input type="text" name="site[tagline]" value="<?= fld($content['site']['tagline'] ?? '') ?>" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Telefoon <span class="hint">(zichtbaar)</span></label>
          <input type="text" name="site[phone]" value="<?= fld($content['site']['phone'] ?? '') ?>" /></div>
        <div class="field"><label>Telefoon-link <span class="hint">(tel:+31...)</span></label>
          <input type="text" name="site[phone_href]" value="<?= fld($content['site']['phone_href'] ?? '') ?>" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>E-mailadres <span class="hint">(ontvangt aanvragen)</span></label>
          <input type="email" name="site[email]" value="<?= fld($content['site']['email'] ?? '') ?>" /></div>
        <div class="field"><label>WhatsApp-nummer <span class="hint">(zonder + en spaties, bv. 31242340061)</span></label>
          <input type="text" name="site[whatsapp_number]" value="<?= fld($content['site']['whatsapp_number'] ?? '') ?>" /></div>
      </div>
      <div class="field"><label>Standaard WhatsApp-bericht</label>
        <input type="text" name="site[whatsapp_message]" value="<?= fld($content['site']['whatsapp_message'] ?? '') ?>" /></div>
      <div class="field-row">
        <div class="field"><label>Adres / locatie</label>
          <input type="text" name="site[address]" value="<?= fld($content['site']['address'] ?? '') ?>" /></div>
        <div class="field"><label>Werkgebied</label>
          <input type="text" name="site[service_area]" value="<?= fld($content['site']['service_area'] ?? '') ?>" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Domeinnaam</label>
          <input type="text" name="site[domain]" value="<?= fld($content['site']['domain'] ?? '') ?>" /></div>
        <div class="field"><label>KvK-nummer</label>
          <input type="text" name="site[kvk]" value="<?= fld($content['site']['kvk'] ?? '') ?>" /></div>
        <div class="field"><label>BTW-nummer</label>
          <input type="text" name="site[btw]" value="<?= fld($content['site']['btw'] ?? '') ?>" /></div>
      </div>
    </div>

    <div class="card">
      <h2>Sociale media</h2>
      <p class="muted">Volledige URL's. Laat leeg om het icoon te verbergen.</p>
      <div class="field"><label>Instagram</label>
        <input type="url" name="site[instagram]" placeholder="https://instagram.com/..." value="<?= fld($content['site']['instagram'] ?? '') ?>" /></div>
      <div class="field"><label>TikTok</label>
        <input type="url" name="site[tiktok]" placeholder="https://tiktok.com/@..." value="<?= fld($content['site']['tiktok'] ?? '') ?>" /></div>
      <div class="field"><label>Facebook</label>
        <input type="url" name="site[facebook]" placeholder="https://facebook.com/..." value="<?= fld($content['site']['facebook'] ?? '') ?>" /></div>
    </div>
  </div>

  <!-- ============ TAB: HERO ============ -->
  <div class="tab-panel" data-panel="hero" id="tab-hero">
    <div class="card">
      <h2>Hero (homepage bovenkant)</h2>
      <p class="muted">De grote tekst en knoppen die bezoekers als eerste zien.</p>
      <div class="field"><label>Eyebrow / kleine label</label>
        <input type="text" name="hero[eyebrow]" value="<?= fld($content['hero']['eyebrow'] ?? '') ?>" /></div>
      <div class="field-row">
        <div class="field"><label>Titel — eerste regel</label>
          <input type="text" name="hero[title_line1]" value="<?= fld($content['hero']['title_line1'] ?? '') ?>" /></div>
        <div class="field"><label>Titel — accent (paars cursief)</label>
          <input type="text" name="hero[title_line2_emphasis]" value="<?= fld($content['hero']['title_line2_emphasis'] ?? '') ?>" /></div>
      </div>
      <div class="field"><label>Ondertitel / lead</label>
        <textarea name="hero[subtitle]" rows="3"><?= fld($content['hero']['subtitle'] ?? '') ?></textarea></div>
      <div class="field-row">
        <div class="field"><label>Knop 1 (primair)</label>
          <input type="text" name="hero[cta_primary]" value="<?= fld($content['hero']['cta_primary'] ?? '') ?>" /></div>
        <div class="field"><label>Knop 2 (secundair)</label>
          <input type="text" name="hero[cta_secondary]" value="<?= fld($content['hero']['cta_secondary'] ?? '') ?>" /></div>
      </div>
      <div class="field"><label>Vertrouwens-items <span class="hint">(één per regel, max 3)</span></label>
        <textarea name="hero[trust_items_text]" rows="3" placeholder="Een per regel"><?= fld(implode("\n", $content['hero']['trust_items'] ?? [])) ?></textarea></div>
    </div>

    <div class="card">
      <h2>Vertrouwens-balk (statistieken)</h2>
      <p class="muted">De vier getallen direct onder de hero.</p>
      <div id="trust-list">
        <?php foreach (($content['trust_strip'] ?? []) as $i => $t): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong>Stat <?= $i+1 ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Cijfer</label>
                <input type="text" name="trust_strip[<?= $i ?>][num]" value="<?= fld($t['num'] ?? '') ?>" /></div>
              <div class="field"><label>Label</label>
                <input type="text" name="trust_strip[<?= $i ?>][label]" value="<?= fld($t['label'] ?? '') ?>" /></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="trust"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Stat toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: PRICES ============ -->
  <div class="tab-panel" data-panel="prices" id="tab-prices">
    <div class="card">
      <h2>Prijzen & diensten</h2>
      <p class="muted">Beheer de prijspakketten. Markeer er één als "uitgelicht" voor de paarse highlight-kaart.</p>
      <div id="prices-list">
        <?php foreach (($content['prices'] ?? []) as $i => $p): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head">
              <strong>📦 <?= fld($p['name'] ?? 'Pakket') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>ID <span class="hint">(unieke code, geen spaties)</span></label>
                <input type="text" name="prices[<?= $i ?>][id]" value="<?= fld($p['id'] ?? '') ?>" /></div>
              <div class="field"><label>Naam</label>
                <input type="text" name="prices[<?= $i ?>][name]" value="<?= fld($p['name'] ?? '') ?>" /></div>
            </div>
            <div class="field-row">
              <div class="field"><label>Icoon</label>
                <select name="prices[<?= $i ?>][icon]">
                  <?php foreach (['Building','Houses','HouseDouble','HouseStand','Home','Spider','Shield'] as $ic): ?>
                    <option value="<?= $ic ?>" <?= ($p['icon'] ?? '') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                  <?php endforeach; ?>
                </select></div>
              <div class="field"><label>Subtitel / meta</label>
                <input type="text" name="prices[<?= $i ?>][meta]" value="<?= fld($p['meta'] ?? '') ?>" /></div>
            </div>
            <div class="field-row">
              <div class="field"><label>Prijs in € <span class="hint">(getal, leeg = "Op aanvraag")</span></label>
                <input type="number" min="0" name="prices[<?= $i ?>][price]" value="<?= fld($p['price'] ?? '') ?>" /></div>
              <div class="field"><label>Tekst i.p.v. prijs <span class="hint">(als prijs leeg)</span></label>
                <input type="text" name="prices[<?= $i ?>][price_label]" value="<?= fld($p['price_label'] ?? '') ?>" placeholder="Op aanvraag" /></div>
              <div class="field"><label>Uitgelicht?</label>
                <select name="prices[<?= $i ?>][featured]">
                  <option value="0" <?= empty($p['featured']) ? 'selected' : '' ?>>Nee</option>
                  <option value="1" <?= !empty($p['featured']) ? 'selected' : '' ?>>Ja (paars highlight)</option>
                </select></div>
            </div>
            <div class="field"><label>Kenmerken <span class="hint">(één per regel)</span></label>
              <textarea name="prices[<?= $i ?>][features_text]" rows="4"><?= fld(implode("\n", $p['features'] ?? [])) ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="price"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Pakket toevoegen</button>
    </div>

    <div class="card">
      <h2>Korting-melding</h2>
      <p class="muted">Bijv. 20% korting bij meerdere panden. Laat leeg om te verbergen.</p>
      <div class="field"><label>Tekst</label>
        <input type="text" name="discount_note" value="<?= fld($content['discount_note'] ?? '') ?>" /></div>
    </div>

    <?php
      $cmp = $content['compare'] ?? [];
      $cmp_enabled  = array_key_exists('enabled', $cmp) ? !empty($cmp['enabled']) : true;
      $cmp_eyebrow  = $cmp['eyebrow']  ?? 'Vergelijking';
      $cmp_heading  = $cmp['heading']  ?? 'SpinGuard versus zelf doen.';
      $cmp_intro    = $cmp['intro']    ?? 'Een grondige professionele behandeling werkt maandenlang door — zelfs goedkope DIY-middelen halen die diepte niet.';
      $cmp_col_them = $cmp['col_them'] ?? 'DIY / supermarkt';
      $cmp_col_us   = $cmp['col_us']   ?? 'SpinGuard';
      $cmp_rows     = $cmp['rows'] ?? [
        ['label' => 'Verwijdering van webben & nesten',           'them' => 'no',   'us' => 'yes'],
        ['label' => 'Behandeling op moeilijk bereikbare plekken', 'them' => 'no',   'us' => 'yes'],
        ['label' => 'Biologisch afbreekbare middelen',            'them' => 'soms', 'us' => 'yes'],
        ['label' => 'Werkzaam tot 6 maanden',                     'them' => 'no',   'us' => 'yes'],
        ['label' => 'Garantie bij hernieuwde overlast',           'them' => 'no',   'us' => 'yes'],
        ['label' => 'Inspectie ter plekke',                       'them' => 'no',   'us' => 'yes'],
      ];
    ?>
    <div class="card">
      <h2>Vergelijkingstabel — DIY versus SpinGuard</h2>
      <p class="muted">De tabel onderaan de Diensten-pagina. Vink rijen aan/uit met de drie statusopties: ✓ ja, ± soms, — nee.</p>

      <div class="field" style="margin-top:8px;">
        <label class="check-item" style="max-width:max-content;">
          <input type="hidden" name="compare[enabled]" value="0" />
          <input type="checkbox" name="compare[enabled]" value="1" <?= $cmp_enabled ? 'checked' : '' ?> />
          <span>Tabel tonen op de Diensten-pagina</span>
        </label>
      </div>

      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="compare[eyebrow]" value="<?= fld($cmp_eyebrow) ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="compare[heading]" value="<?= fld($cmp_heading) ?>" /></div>
      </div>
      <div class="field"><label>Introtekst</label>
        <textarea name="compare[intro]" rows="2"><?= fld($cmp_intro) ?></textarea></div>
      <div class="field-row">
        <div class="field"><label>Kop linker kolom</label>
          <input type="text" name="compare[col_them]" value="<?= fld($cmp_col_them) ?>" /></div>
        <div class="field"><label>Kop rechter kolom</label>
          <input type="text" name="compare[col_us]" value="<?= fld($cmp_col_us) ?>" /></div>
      </div>

      <h3 style="margin-top:20px; margin-bottom:8px;">Rijen</h3>
      <div id="compare-rows-list">
        <?php foreach ($cmp_rows as $i => $row): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head">
              <strong><?= fld($row['label'] ?? 'Rij') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field"><label>Omschrijving</label>
              <input type="text" name="compare[rows][<?= $i ?>][label]" value="<?= fld($row['label'] ?? '') ?>" /></div>
            <div class="field-row">
              <div class="field"><label><?= fld($cmp_col_them) ?></label>
                <select name="compare[rows][<?= $i ?>][them]">
                  <option value="yes"  <?= ($row['them'] ?? '') === 'yes'  ? 'selected' : '' ?>>✓ Ja</option>
                  <option value="soms" <?= ($row['them'] ?? '') === 'soms' ? 'selected' : '' ?>>± Soms</option>
                  <option value="no"   <?= (($row['them'] ?? 'no') === 'no') ? 'selected' : '' ?>>— Nee</option>
                </select></div>
              <div class="field"><label><?= fld($cmp_col_us) ?></label>
                <select name="compare[rows][<?= $i ?>][us]">
                  <option value="yes"  <?= (($row['us'] ?? 'yes') === 'yes') ? 'selected' : '' ?>>✓ Ja</option>
                  <option value="soms" <?= ($row['us'] ?? '') === 'soms' ? 'selected' : '' ?>>± Soms</option>
                  <option value="no"   <?= ($row['us'] ?? '') === 'no'   ? 'selected' : '' ?>>— Nee</option>
                </select></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="compare-row"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Rij toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: PROCESS ============ -->
  <div class="tab-panel" data-panel="process" id="tab-process">
    <div class="card">
      <h2>Werkwijze (stappenplan)</h2>
      <p class="muted">De stappen van uw werkproces. Elk stapje krijgt een eigen detailpaneel.</p>
      <div id="process-list">
        <?php foreach (($content['process'] ?? []) as $i => $s): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head">
              <strong>Stap <?= $i+1 ?>: <?= fld($s['title'] ?? '') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Titel</label>
                <input type="text" name="process[<?= $i ?>][title]" value="<?= fld($s['title'] ?? '') ?>" /></div>
              <div class="field"><label>Icoon</label>
                <select name="process[<?= $i ?>][icon]">
                  <?php foreach (['Camera','Doc','Spray','Sparkle','Shield','Check','Spider','Home'] as $ic): ?>
                    <option value="<?= $ic ?>" <?= ($s['icon'] ?? '') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                  <?php endforeach; ?>
                </select></div>
            </div>
            <div class="field"><label>Korte beschrijving <span class="hint">(in stappenlijst)</span></label>
              <input type="text" name="process[<?= $i ?>][short]" value="<?= fld($s['short'] ?? '') ?>" /></div>
            <div class="field"><label>Lange beschrijving <span class="hint">(in detail-paneel)</span></label>
              <textarea name="process[<?= $i ?>][long]" rows="3"><?= fld($s['long'] ?? '') ?></textarea></div>
            <div class="field"><label>Bullet-points <span class="hint">(één per regel)</span></label>
              <textarea name="process[<?= $i ?>][details_text]" rows="3"><?= fld(implode("\n", $s['details'] ?? [])) ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="process"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Stap toevoegen</button>
    </div>

    <div class="card">
      <h2>Before/After sectie</h2>
      <p class="muted">Foto's voor de schuifbalk op de werkwijze-pagina. Klik op een vak om een foto te kiezen of meteen te uploaden.</p>
      <div class="field"><label>Titel</label>
        <input type="text" name="before_after[title]" value="<?= fld($content['before_after']['title'] ?? '') ?>" /></div>
      <div class="field"><label>Subtitel</label>
        <input type="text" name="before_after[subtitle]" value="<?= fld($content['before_after']['subtitle'] ?? '') ?>" /></div>
      <div class="field-row">
        <div class="field">
          <label>Before-foto <span class="hint">(vóór behandeling)</span></label>
          <?php image_picker('before_after[before_image]', $content['before_after']['before_image'] ?? '', $BASE); ?>
        </div>
        <div class="field">
          <label>After-foto <span class="hint">(na behandeling)</span></label>
          <?php image_picker('before_after[after_image]', $content['before_after']['after_image'] ?? '', $BASE); ?>
        </div>
      </div>
      <div class="field"><label>Bullets <span class="hint">(één per regel)</span></label>
        <textarea name="before_after[bullets_text]" rows="4"><?= fld(implode("\n", $content['before_after']['bullets'] ?? [])) ?></textarea></div>
    </div>

    <?php
      $mat = $content['materials'] ?? [];
      $mat_enabled = array_key_exists('enabled', $mat) ? !empty($mat['enabled']) : true;
      $mat_tools = $mat['tools'] ?? [
        ['icon' => 'Spray',  'title' => 'Sprayer',       'subtitle' => 'Lage druk · gericht'],
        ['icon' => 'Doc',    'title' => 'Rapportage',    'subtitle' => 'Per e-mail'],
        ['icon' => 'Shield', 'title' => 'Garantie',      'subtitle' => '6 maanden'],
        ['icon' => 'Camera', 'title' => "Foto's vooraf", 'subtitle' => 'WhatsApp'],
      ];
    ?>
    <div class="card">
      <h2>Materialen & aanpak — sectie</h2>
      <p class="muted">De "Veilig voor mens, dier en plant" sectie op de werkwijze-pagina, met de "Onze toolkit" kaart rechts.</p>

      <div class="field" style="margin-top:8px;">
        <label class="check-item" style="max-width:max-content;">
          <input type="hidden" name="materials[enabled]" value="0" />
          <input type="checkbox" name="materials[enabled]" value="1" <?= $mat_enabled ? 'checked' : '' ?> />
          <span>Sectie tonen op werkwijze-pagina</span>
        </label>
      </div>

      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="materials[eyebrow]" value="<?= fld($mat['eyebrow'] ?? 'Materialen & aanpak') ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="materials[heading]" value="<?= fld($mat['heading'] ?? 'Veilig voor mens, dier en plant.') ?>" /></div>
      </div>
      <div class="field"><label>Introtekst</label>
        <textarea name="materials[intro]" rows="3"><?= fld($mat['intro'] ?? 'Wij werken uitsluitend met EU-toegelaten middelen die — eenmaal opgedroogd — geen risico vormen voor uw gezin of huisdieren. De middelen zijn watergebaseerd, geurarm en biologisch afbreekbaar.') ?></textarea></div>
      <div class="field"><label>Bullets <span class="hint">(één per regel)</span></label>
        <textarea name="materials[bullets_text]" rows="5"><?= fld(implode("\n", $mat['bullets'] ?? ['Watergebaseerd, geurarm','Geen schade aan gevel, kozijnen of planten','Veilig voor kinderen en huisdieren na opdroging','Werkzaam tegen alle in NL voorkomende spinsoorten','Toegelaten door het Ctgb'])) ?></textarea></div>

      <div class="field" style="margin-top:20px;">
        <label>Eyebrow op donkere kaart</label>
        <input type="text" name="materials[card_eyebrow]" value="<?= fld($mat['card_eyebrow'] ?? 'Onze toolkit') ?>" />
      </div>

      <h3 style="margin-top:20px; margin-bottom:8px;">Toolkit-tegels (4 stuks)</h3>
      <div id="materials-tools-list">
        <?php foreach ($mat_tools as $i => $t): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head">
              <strong><?= fld($t['title'] ?? 'Tegel') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Icoon</label>
                <select name="materials[tools][<?= $i ?>][icon]">
                  <?php foreach (['Spray','Doc','Shield','Camera','Sparkle','Check','Leaf','Clock','Home','Spider'] as $ic): ?>
                    <option value="<?= $ic ?>" <?= ($t['icon'] ?? '') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                  <?php endforeach; ?>
                </select></div>
              <div class="field"><label>Titel</label>
                <input type="text" name="materials[tools][<?= $i ?>][title]" value="<?= fld($t['title'] ?? '') ?>" /></div>
              <div class="field"><label>Subtitel</label>
                <input type="text" name="materials[tools][<?= $i ?>][subtitle]" value="<?= fld($t['subtitle'] ?? '') ?>" /></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="material-tool"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Tegel toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: BENEFITS ============ -->
  <div class="tab-panel" data-panel="benefits" id="tab-benefits">
    <div class="card">
      <h2>Voordelen / waarom-kiezen</h2>
      <p class="muted">De vier kaartjes onder de "Waarom SpinGuard"-titel.</p>
      <div id="benefits-list">
        <?php foreach (($content['benefits'] ?? []) as $i => $b): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong><?= fld($b['title'] ?? 'Voordeel') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>ID <span class="hint">(b1, b2, b3, b4 — bepaalt kleur)</span></label>
                <input type="text" name="benefits[<?= $i ?>][id]" value="<?= fld($b['id'] ?? '') ?>" /></div>
              <div class="field"><label>Icoon</label>
                <select name="benefits[<?= $i ?>][icon]">
                  <?php foreach (['Spider','Home','Clock','Leaf','Shield','Sparkle','Check'] as $ic): ?>
                    <option value="<?= $ic ?>" <?= ($b['icon'] ?? '') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                  <?php endforeach; ?>
                </select></div>
            </div>
            <div class="field"><label>Titel</label>
              <input type="text" name="benefits[<?= $i ?>][title]" value="<?= fld($b['title'] ?? '') ?>" /></div>
            <div class="field"><label>Tekst</label>
              <textarea name="benefits[<?= $i ?>][text]" rows="2"><?= fld($b['text'] ?? '') ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="benefit"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Voordeel toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: TESTIMONIALS ============ -->
  <div class="tab-panel" data-panel="testimonials" id="tab-testimonials">
    <?php
      $rev_settings = $content['reviews_settings'] ?? [];
      $rev_layout   = $rev_settings['layout']   ?? 'grid';
      $rev_eyebrow  = $rev_settings['eyebrow']  ?? 'Klantervaringen';
      $rev_heading  = $rev_settings['heading']  ?? 'Vertrouwd door huishoudens en bedrijven.';
      $rev_subtitle = $rev_settings['subtitle'] ?? 'Een greep uit ervaringen van klanten — van rijtjeswoning tot parkeergarage.';
      $rev_tp_enabled = !empty($rev_settings['trustpilot_enabled']);
      $rev_tp_embed   = $rev_settings['trustpilot_embed'] ?? '';
    ?>
    <div class="card">
      <h2>Reviews — sectie titels</h2>
      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="reviews_settings[eyebrow]" value="<?= fld($rev_eyebrow) ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="reviews_settings[heading]" value="<?= fld($rev_heading) ?>" /></div>
      </div>
      <div class="field"><label>Subtitel</label>
        <input type="text" name="reviews_settings[subtitle]" value="<?= fld($rev_subtitle) ?>" /></div>
    </div>

    <div class="card">
      <h2>Weergave-stijl</h2>
      <p class="muted">Kies hoe de reviews op de website getoond worden.</p>
      <div class="layout-picker">
        <?php
          $layout_options = [
            'grid'       => ['label' => 'Raster (3 kolommen)',     'hint' => 'Klassiek 3-koloms grid. Werkt goed met 3, 6 of 9 reviews.'],
            'compact'    => ['label' => 'Compact (2 kolommen)',    'hint' => 'Smaller 2-koloms layout, ideaal voor 2-4 reviews.'],
            'slider'     => ['label' => 'Slider / carousel',       'hint' => 'Swipebare carousel met pijltjes. Goed voor 5+ reviews.'],
            'list'       => ['label' => 'Lijst (1 grote per rij)', 'hint' => 'Volledige breedte, één review per rij. Voor uitgebreide reviews.'],
            'trustpilot' => ['label' => 'Trustpilot widget',       'hint' => 'Toon de officiële Trustpilot-widget in plaats van eigen reviews.'],
          ];
          foreach ($layout_options as $key => $opt):
        ?>
          <label class="layout-option <?= $rev_layout === $key ? 'is-selected' : '' ?>">
            <input type="radio" name="reviews_settings[layout]" value="<?= fld($key) ?>" <?= $rev_layout === $key ? 'checked' : '' ?> />
            <strong><?= fld($opt['label']) ?></strong>
            <small><?= fld($opt['hint']) ?></small>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card" id="trustpilotCard" style="<?= $rev_layout === 'trustpilot' ? '' : 'display:none' ?>">
      <h2>Trustpilot integratie</h2>
      <p class="muted">Plak hier de embed-code van je Trustpilot widget. Ga naar je Trustpilot Business-dashboard → "Get widgets" → kopieer de code.</p>
      <div class="field">
        <label class="check-item" style="max-width:max-content;">
          <input type="hidden" name="reviews_settings[trustpilot_enabled]" value="0" />
          <input type="checkbox" name="reviews_settings[trustpilot_enabled]" value="1" <?= $rev_tp_enabled ? 'checked' : '' ?> />
          <span>Trustpilot widget actief</span>
        </label>
      </div>
      <div class="field">
        <label>Embed-code <span class="hint">(volledige <code>&lt;div&gt;</code> + <code>&lt;script&gt;</code>)</span></label>
        <textarea name="reviews_settings[trustpilot_embed]" rows="6" placeholder="Plak hier de Trustpilot widget embed-code..." style="font-family:ui-monospace,SFMono-Regular,monospace; font-size:13px;"><?= fld($rev_tp_embed) ?></textarea>
      </div>
      <p class="text-muted text-sm" style="margin-top:6px;">💡 De code wordt 1-op-1 op de pagina geplaatst, wijzig dus niets in de embed.</p>
    </div>

    <div class="card">
      <h2>Klantreviews</h2>
      <p class="muted">Gebruik echte reviews indien mogelijk — die overtuigen het meest.</p>
      <div id="testim-list">
        <?php foreach (($content['testimonials'] ?? []) as $i => $t): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong><?= fld($t['name'] ?? 'Review') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field"><label>Quote</label>
              <textarea name="testimonials[<?= $i ?>][quote]" rows="3"><?= fld($t['quote'] ?? '') ?></textarea></div>
            <div class="field-row">
              <div class="field"><label>Naam</label>
                <input type="text" name="testimonials[<?= $i ?>][name]" value="<?= fld($t['name'] ?? '') ?>" /></div>
              <div class="field"><label>Locatie / type</label>
                <input type="text" name="testimonials[<?= $i ?>][loc]" value="<?= fld($t['loc'] ?? '') ?>" /></div>
              <div class="field"><label>Initialen <span class="hint">(2 letters)</span></label>
                <input type="text" maxlength="3" name="testimonials[<?= $i ?>][initials]" value="<?= fld($t['initials'] ?? '') ?>" /></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="testimonial"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Review toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: FAQ ============ -->
  <div class="tab-panel" data-panel="faq" id="tab-faq">
    <div class="card">
      <h2>Veelgestelde vragen</h2>
      <p class="muted">Goede FAQ-vragen helpen bij SEO en geven bezoekers vertrouwen.</p>
      <div id="faq-list">
        <?php foreach (($content['faq'] ?? []) as $i => $f): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong>Vraag <?= $i+1 ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field"><label>Vraag</label>
              <input type="text" name="faq[<?= $i ?>][q]" value="<?= fld($f['q'] ?? '') ?>" /></div>
            <div class="field"><label>Antwoord</label>
              <textarea name="faq[<?= $i ?>][a]" rows="3"><?= fld($f['a'] ?? '') ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="faq"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Vraag toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: ABOUT ============ -->
  <div class="tab-panel" data-panel="about" id="tab-about">
    <div class="card">
      <h2>Over ons — hero (boven aan de pagina)</h2>
      <div class="field-row">
        <div class="field"><label>Eyebrow <span class="hint">(klein label boven titel)</span></label>
          <input type="text" name="about[hero_eyebrow]" value="<?= fld($content['about']['hero_eyebrow'] ?? 'Over ons') ?>" /></div>
        <div class="field"><label>Titel — eerste woorden</label>
          <input type="text" name="about[hero_title]" value="<?= fld($content['about']['hero_title'] ?? 'Specialist in') ?>" /></div>
        <div class="field"><label>Titel — accent (paars cursief)</label>
          <input type="text" name="about[hero_emphasis]" value="<?= fld($content['about']['hero_emphasis'] ?? 'spinvrij wonen.') ?>" /></div>
      </div>
      <div class="field"><label>Intro / lead-tekst</label>
        <textarea name="about[intro]" rows="3"><?= fld($content['about']['intro'] ?? '') ?></textarea></div>
    </div>

    <div class="card">
      <h2>Ons verhaal sectie</h2>
      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="about[story_eyebrow]" value="<?= fld($content['about']['story_eyebrow'] ?? 'Ons verhaal') ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="about[story_heading]" value="<?= fld($content['about']['story_heading'] ?? 'Opgericht uit ergernis met halve oplossingen.') ?>" /></div>
      </div>
      <div class="field"><label>Verhaal <span class="hint">(elke alinea op een nieuwe regel — laat een lege regel ertussen)</span></label>
        <textarea name="about[story_text]" rows="8"><?= fld(implode("\n\n", $content['about']['story'] ?? [])) ?></textarea></div>
    </div>

    <div class="card">
      <h2>Portretten + "Sinds" badge</h2>
      <p class="muted">De twee gestapelde "polaroid" cards rechts van het verhaal + paars badge. Upload eigen foto's of laat leeg voor het standaard icoon.</p>
      <div class="field-row">
        <div class="field">
          <label>Foto bovenste card <span class="hint">(paarse card — laat leeg voor spider icoon)</span></label>
          <?php image_picker('about[portrait_image_1]', $content['about']['portrait_image_1'] ?? '', $BASE); ?>
        </div>
        <div class="field">
          <label>Foto onderste card <span class="hint">(witte card — laat leeg voor building icoon)</span></label>
          <?php image_picker('about[portrait_image_2]', $content['about']['portrait_image_2'] ?? '', $BASE); ?>
        </div>
      </div>
      <div class="field-row">
        <div class="field"><label>Label bovenste card <span class="hint">(tekst onder/op de foto)</span></label>
          <input type="text" name="about[portrait_label_1]" value="<?= fld($content['about']['portrait_label_1'] ?? 'Team aan het werk') ?>" /></div>
        <div class="field"><label>Label onderste card <span class="hint">(tekst onder/op de foto)</span></label>
          <input type="text" name="about[portrait_label_2]" value="<?= fld($content['about']['portrait_label_2'] ?? 'Behandelde gevel') ?>" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Badge tekst <span class="hint">(boven het jaartal)</span></label>
          <input type="text" name="about[founded_label]" value="<?= fld($content['about']['founded_label'] ?? 'Sinds') ?>" /></div>
        <div class="field"><label>Jaar <span class="hint">(in badge)</span></label>
          <input type="text" name="about[founded_year]" value="<?= fld($content['about']['founded_year'] ?? '2024') ?>" /></div>
      </div>
    </div>

    <div class="card">
      <h2>Werkgebied sectie — teksten</h2>
      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="about[area_eyebrow]" value="<?= fld($content['about']['area_eyebrow'] ?? 'Werkgebied') ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="about[area_heading]" value="<?= fld($content['about']['area_heading'] ?? 'Heel Nederland — vanuit Nijmegen.') ?>" /></div>
      </div>
      <div class="field"><label>Beschrijving</label>
        <textarea name="about[area_text]" rows="3"><?= fld($content['about']['area_text'] ?? 'Onze thuisbasis is Nijmegen, maar wij rijden door heel Nederland. Voor afspraken op afstand vragen wij alleen een kleine reisvergoeding bij behandelingen onder €100.') ?></textarea></div>
      <p class="text-muted text-sm" style="margin-top:6px;">💡 De pills-lijst onder de tekst wordt gegenereerd uit tab <strong>SEO → Lokale SEO → Service-steden</strong>. De kaart zelf stel je hieronder in.</p>
    </div>

    <div class="card">
      <h2>Kaart van Nederland — instellingen</h2>
      <p class="muted">Pas precies aan welk gebied jullie bedienen. Niet aangevinkte provincies worden grijs op de kaart, niet aangevinkte steden krijgen geen pin.</p>

      <?php
        $map = $content['about']['map'] ?? [];
        $active_provinces = $map['active_provinces'] ?? $NL_MAP['defaults']['active_provinces'];
        $active_cities    = $map['active_cities']    ?? $NL_MAP['defaults']['active_cities'];
        $hq_city          = $map['hq_city']          ?? $NL_MAP['defaults']['hq_city'];
        $show_lines       = array_key_exists('show_lines', $map) ? !empty($map['show_lines']) : true;
      ?>

      <h3 style="margin-top:24px; margin-bottom:8px;">Provincies tonen</h3>
      <p class="text-muted text-sm" style="margin-top:0;">Aangevinkt = ingekleurd op de kaart, uitgevinkt = grijs.</p>
      <div class="check-grid">
        <?php foreach ($NL_MAP['provinces'] as $pid => $pname):
          $checked = in_array($pid, $active_provinces, true) ? 'checked' : ''; ?>
          <label class="check-item">
            <input type="checkbox" name="about[map][active_provinces][]" value="<?= fld($pid) ?>" <?= $checked ?> />
            <span><?= fld($pname) ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <h3 style="margin-top:24px; margin-bottom:8px;">Steden tonen op de kaart</h3>
      <p class="text-muted text-sm" style="margin-top:0;">Vink steden aan voor zichtbare pins. De radio knop ernaast bepaalt de hoofdvestiging (pulserende pin).</p>
      <div class="check-grid check-grid-cities">
        <?php foreach ($NL_MAP['cities'] as $slug => $c):
          $checked = in_array($slug, $active_cities, true) ? 'checked' : '';
          $is_hq   = ($hq_city === $slug) ? 'checked' : ''; ?>
          <div class="check-item-row">
            <label class="check-item">
              <input type="checkbox" name="about[map][active_cities][]" value="<?= fld($slug) ?>" <?= $checked ?> />
              <span><?= fld($c['name']) ?></span>
            </label>
            <label class="hq-radio" title="Hoofdvestiging">
              <input type="radio" name="about[map][hq_city]" value="<?= fld($slug) ?>" <?= $is_hq ?> />
              <span>HQ</span>
            </label>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="field" style="margin-top:24px;">
        <label class="check-item">
          <input type="hidden" name="about[map][show_lines]" value="0" />
          <input type="checkbox" name="about[map][show_lines]" value="1" <?= $show_lines ? 'checked' : '' ?> />
          <span>Toon verbindingslijnen vanaf hoofdvestiging naar elke stad</span>
        </label>
      </div>
    </div>

    <div class="card">
      <h2>CTA banner onderaan</h2>
      <div class="field"><label>Titel</label>
        <input type="text" name="about[cta_title]" value="<?= fld($content['about']['cta_title'] ?? 'Klaar om kennis te maken?') ?>" /></div>
      <div class="field"><label>Tekst</label>
        <textarea name="about[cta_text]" rows="2"><?= fld($content['about']['cta_text'] ?? 'Stuur ons een bericht via WhatsApp of bel ons direct. Wij helpen u graag verder.') ?></textarea></div>
    </div>

    <div class="card">
      <h2>Onze waarden — sectie titels</h2>
      <div class="field-row">
        <div class="field"><label>Eyebrow</label>
          <input type="text" name="about[values_eyebrow]" value="<?= fld($content['about']['values_eyebrow'] ?? 'Onze waarden') ?>" /></div>
        <div class="field"><label>Heading (H2)</label>
          <input type="text" name="about[values_heading]" value="<?= fld($content['about']['values_heading'] ?? 'Waar wij voor staan.') ?>" /></div>
      </div>
      <div class="field"><label>Subtitel onder heading</label>
        <input type="text" name="about[values_subtitle]" value="<?= fld($content['about']['values_subtitle'] ?? 'Vier kernwaarden die alles wat wij doen sturen.') ?>" /></div>
    </div>

    <div class="card">
      <h2>Waarden — kaartjes (4 stuks)</h2>
      <div id="values-list">
        <?php foreach (($content['about']['values'] ?? []) as $i => $v): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong><?= fld($v['title'] ?? '') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Icoon</label>
                <select name="about[values][<?= $i ?>][icon]">
                  <?php foreach (['Shield','Leaf','Clock','Sparkle','Check','Home','Spider'] as $ic): ?>
                    <option value="<?= $ic ?>" <?= ($v['icon'] ?? '') === $ic ? 'selected' : '' ?>><?= $ic ?></option>
                  <?php endforeach; ?>
                </select></div>
              <div class="field"><label>Titel</label>
                <input type="text" name="about[values][<?= $i ?>][title]" value="<?= fld($v['title'] ?? '') ?>" /></div>
            </div>
            <div class="field"><label>Tekst</label>
              <input type="text" name="about[values][<?= $i ?>][text]" value="<?= fld($v['text'] ?? '') ?>" /></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="value"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Waarde toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: OTHER ============ -->
  <div class="tab-panel" data-panel="other" id="tab-other">
    <div class="card">
      <h2>Contact-pagina</h2>
      <div class="field"><label>Titel</label>
        <input type="text" name="contact[title]" value="<?= fld($content['contact']['title'] ?? '') ?>" /></div>
      <div class="field"><label>Subtitel</label>
        <input type="text" name="contact[subtitle]" value="<?= fld($content['contact']['subtitle'] ?? '') ?>" /></div>
      <div class="field"><label>Form-intro <span class="hint">(tekst in de paarse linkerbalk)</span></label>
        <textarea name="contact[form_intro]" rows="2"><?= fld($content['contact']['form_intro'] ?? '') ?></textarea></div>
    </div>
    <div class="card">
      <h2>SEO — Algemeen (Google)</h2>
      <p class="muted">Deze waarden worden gebruikt als fallback en in zoekresultaten.</p>
      <div class="field"><label>Standaard page title</label>
        <input type="text" name="seo[title]" value="<?= fld($content['seo']['title'] ?? '') ?>" /></div>
      <div class="field"><label>Standaard meta description <span class="hint">(150-160 tekens ideaal)</span></label>
        <textarea name="seo[description]" rows="3"><?= fld($content['seo']['description'] ?? '') ?></textarea></div>
      <div class="field"><label>Keywords <span class="hint">(door komma gescheiden)</span></label>
        <input type="text" name="seo[keywords]" value="<?= fld($content['seo']['keywords'] ?? '') ?>" /></div>
      <div class="field"><label>Site URL <span class="hint">(belangrijk voor canonical + Open Graph!)</span></label>
        <input type="url" name="seo[site_url]" value="<?= fld($content['seo']['site_url'] ?? '') ?>" placeholder="https://spinguard.nl" /></div>
    </div>

    <div class="card">
      <h2>SEO — Per pagina</h2>
      <p class="muted">Eigen titel + beschrijving per pagina (overschrijft standaard).</p>
      <?php foreach (['home'=>'Homepage','diensten'=>'Diensten','werkwijze'=>'Werkwijze','over_ons'=>'Over ons','contact'=>'Contact'] as $key=>$label): ?>
        <div class="repeater-item">
          <div class="ri-head"><strong><?= $label ?></strong></div>
          <div class="field"><label>Titel <span class="hint">(50-60 tekens ideaal)</span></label>
            <input type="text" name="seo[pages][<?= $key ?>][title]" value="<?= fld($content['seo']['pages'][$key]['title'] ?? '') ?>" /></div>
          <div class="field"><label>Beschrijving <span class="hint">(150-160 tekens)</span></label>
            <textarea name="seo[pages][<?= $key ?>][description]" rows="2"><?= fld($content['seo']['pages'][$key]['description'] ?? '') ?></textarea></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <h2>Lokale SEO (Google Maps)</h2>
      <p class="muted">Helpt je vindbaar te maken voor lokale zoekopdrachten zoals "spinnenbestrijding Nijmegen".</p>
      <div class="field-row">
        <div class="field"><label>Stad (hoofdvestiging)</label>
          <input type="text" name="seo[geo_city]" value="<?= fld($content['seo']['geo_city'] ?? '') ?>" placeholder="Nijmegen" /></div>
        <div class="field"><label>Provincie</label>
          <input type="text" name="seo[geo_region]" value="<?= fld($content['seo']['geo_region'] ?? '') ?>" placeholder="Gelderland" /></div>
        <div class="field"><label>Postcode</label>
          <input type="text" name="seo[geo_postcode]" value="<?= fld($content['seo']['geo_postcode'] ?? '') ?>" placeholder="6500" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Latitude <span class="hint">(via Google Maps)</span></label>
          <input type="text" name="seo[geo_latitude]" value="<?= fld($content['seo']['geo_latitude'] ?? '') ?>" placeholder="51.8425625" /></div>
        <div class="field"><label>Longitude</label>
          <input type="text" name="seo[geo_longitude]" value="<?= fld($content['seo']['geo_longitude'] ?? '') ?>" placeholder="5.8528014" /></div>
      </div>
      <div class="field"><label>Openingstijden <span class="hint">(formaat: Mo-Fr 08:00-18:00, Sa 09:00-16:00)</span></label>
        <input type="text" name="seo[opening_hours]" value="<?= fld($content['seo']['opening_hours'] ?? '') ?>" /></div>
      <div class="field"><label>Service-steden <span class="hint">(komma-gescheiden, helpt voor lokale zoekopdrachten)</span></label>
        <textarea name="seo[service_cities_text]" rows="3"><?= fld(implode(", ", $content['seo']['service_cities'] ?? [])) ?></textarea></div>
    </div>

    <div class="card">
      <h2>Reviews (voor sterren in Google)</h2>
      <p class="muted">Aggregate rating verschijnt mogelijk als sterren in zoekresultaten.</p>
      <div class="field-row">
        <div class="field"><label>Gemiddelde score <span class="hint">(0-5)</span></label>
          <input type="number" step="0.1" min="0" max="5" name="seo[rating_value]" value="<?= fld($content['seo']['rating_value'] ?? '5.0') ?>" /></div>
        <div class="field"><label>Aantal reviews</label>
          <input type="number" min="0" name="seo[rating_count]" value="<?= fld($content['seo']['rating_count'] ?? '0') ?>" /></div>
      </div>
    </div>
  </div>

  <!-- ============ TAB: MARKETING / TRACKING ============ -->
  <div class="tab-panel" data-panel="marketing" id="tab-marketing">
    <div class="card">
      <h2>📊 Marketing & Analytics tracking</h2>
      <p class="muted">Voeg hieronder de IDs toe van je analytics- en advertentieplatformen. <strong>De codes laden alleen na cookie-toestemming van bezoekers</strong> (GDPR/AVG-compliant).</p>

      <div class="notice info" style="margin-bottom:18px;">
        💡 <strong>Tip:</strong> Gebruik <strong>Google Tag Manager (GTM)</strong> als je meerdere pixels wil beheren. Eén ID hieronder = alle tracking via GTM web-interface beheren. Anders: vul de losse velden in.
      </div>
    </div>

    <div class="card">
      <h2>Google Tag Manager <span class="hint" style="font-weight:400; font-size:13px; color:var(--muted);">(aanbevolen — beheert al het andere)</span></h2>
      <p class="muted">Eén container voor alles: GA4, Google Ads, Meta Pixel, etc. Beheer welke tags wanneer afvuren via tagmanager.google.com.</p>
      <div class="field">
        <label>GTM Container ID</label>
        <input type="text" name="tracking[gtm_id]" value="<?= fld($content['tracking']['gtm_id'] ?? '') ?>" placeholder="GTM-XXXXXXX" />
      </div>
    </div>

    <div class="card">
      <h2>Google Analytics 4</h2>
      <p class="muted">Voor inzicht in bezoekersgedrag. Gratis. <a href="https://analytics.google.com" target="_blank">analytics.google.com</a></p>
      <div class="field">
        <label>Measurement ID</label>
        <input type="text" name="tracking[ga4_id]" value="<?= fld($content['tracking']['ga4_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" />
        <small style="color:var(--muted); font-size:12px;">Je vindt dit in GA4 → Admin → Data Streams → Web → Measurement ID.</small>
      </div>
    </div>

    <div class="card">
      <h2>Meta (Facebook/Instagram) Pixel</h2>
      <p class="muted">Voor retargeting via Facebook/Instagram-advertenties. <a href="https://business.facebook.com/events_manager" target="_blank">business.facebook.com</a></p>
      <div class="field">
        <label>Pixel ID</label>
        <input type="text" name="tracking[meta_pixel_id]" value="<?= fld($content['tracking']['meta_pixel_id'] ?? '') ?>" placeholder="1234567890123456" />
        <small style="color:var(--muted); font-size:12px;">15-16 cijfers. Te vinden in Meta Events Manager → Data Sources.</small>
      </div>
      <p class="muted" style="margin-top:14px; font-size:12.5px;">
        <strong>Wat wordt automatisch getracked?</strong><br>
        ✓ PageView (op elke pagina)<br>
        ✓ Lead (na succesvol contactformulier)<br>
        ✓ Contact (bij WhatsApp / telefoon / e-mail klik)
      </p>
    </div>

    <div class="card">
      <h2>Google Ads conversie-tracking</h2>
      <p class="muted">Om te meten hoeveel leads je advertenties opleveren. <a href="https://ads.google.com" target="_blank">ads.google.com</a></p>
      <div class="field-row">
        <div class="field">
          <label>Conversion ID</label>
          <input type="text" name="tracking[google_ads_id]" value="<?= fld($content['tracking']['google_ads_id'] ?? '') ?>" placeholder="AW-123456789" />
        </div>
        <div class="field">
          <label>Conversion Label <span class="hint">(voor lead-actie)</span></label>
          <input type="text" name="tracking[google_ads_conversion_label]" value="<?= fld($content['tracking']['google_ads_conversion_label'] ?? '') ?>" placeholder="abc123XYZ" />
        </div>
      </div>
      <small style="color:var(--muted); font-size:12px;">Maak in Google Ads een conversie-actie aan ("Lead via formulier"). Beide ID's vind je daar bij "Tag installeren".</small>
    </div>

    <div class="card">
      <h2>Andere platformen</h2>
      <div class="field-row">
        <div class="field">
          <label>TikTok Pixel ID</label>
          <input type="text" name="tracking[tiktok_pixel_id]" value="<?= fld($content['tracking']['tiktok_pixel_id'] ?? '') ?>" placeholder="C4A1B2C3D4E5F6G7H8" />
        </div>
        <div class="field">
          <label>LinkedIn Partner ID</label>
          <input type="text" name="tracking[linkedin_partner_id]" value="<?= fld($content['tracking']['linkedin_partner_id'] ?? '') ?>" placeholder="1234567" />
        </div>
      </div>
    </div>

    <div class="card">
      <h2>Geavanceerd: eigen code</h2>
      <p class="muted">Voor extra scripts (bv. Hotjar, Microsoft Clarity, custom HTML). Plak hier de hele &lt;script&gt;-tag.</p>
      <div class="field">
        <label>Custom code in &lt;head&gt;</label>
        <textarea name="tracking[custom_head]" rows="5" style="font-family:ui-monospace,monospace;font-size:13px;" placeholder="&lt;script&gt;...&lt;/script&gt;"><?= fld($content['tracking']['custom_head'] ?? '') ?></textarea>
      </div>
      <div class="field">
        <label>Custom code voor einde &lt;body&gt;</label>
        <textarea name="tracking[custom_body_end]" rows="5" style="font-family:ui-monospace,monospace;font-size:13px;" placeholder="&lt;script&gt;...&lt;/script&gt;"><?= fld($content['tracking']['custom_body_end'] ?? '') ?></textarea>
      </div>
    </div>

    <div class="card">
      <h2>🍪 Cookie consent banner</h2>
      <p class="muted">Verplicht in EU bij gebruik van marketing/analytics cookies. Tracking-codes laden alleen na "Akkoord".</p>
      <div class="field">
        <label>Cookie-banner aan?</label>
        <select name="tracking[cookie_consent_enabled]">
          <option value="1" <?= !empty($content['tracking']['cookie_consent_enabled']) ? 'selected' : '' ?>>Ja, banner tonen + tracking pas na toestemming (aanbevolen)</option>
          <option value="0" <?= empty($content['tracking']['cookie_consent_enabled']) ? 'selected' : '' ?>>Nee, geen banner — tracking direct laden (NIET AVG-compliant!)</option>
        </select>
      </div>
      <p style="font-size:12.5px; color:var(--muted); margin-top:8px;">
        💡 Wil je een uitgebreide cookie-tool met categorie-keuzes (analytics/marketing/etc.)? Gebruik <a href="https://www.cookieyes.com" target="_blank">CookieYes</a> of <a href="https://www.cookiebot.com" target="_blank">Cookiebot</a> en plak hun code in "Custom code in &lt;head&gt;" hierboven.
      </p>
    </div>
  </div>

  <!-- ============ TAB: APPEARANCE (Vormgeving) ============ -->
  <div class="tab-panel" data-panel="appearance" id="tab-appearance">
    <div class="card">
      <h2>🎨 Logo</h2>
      <p class="muted">Klik op het logo om een ander te kiezen of te uploaden.</p>
      <div style="max-width:200px;">
        <?php image_picker('appearance[logo]', $content['appearance']['logo'] ?? '/assets/spinguard-logo.png', $BASE); ?>
      </div>
    </div>

    <div class="card">
      <h2>📱 Mobile sticky bar</h2>
      <p class="muted">Onderaan-balk op mobiel met WhatsApp + Bel-knoppen — altijd zichtbaar voor snelle conversie.</p>
      <div class="field"><label>Sticky bar tonen?</label>
        <select name="homepage_extras[sticky_mobile_bar]">
          <option value="1" <?= !empty($content['homepage_extras']['sticky_mobile_bar']) ? 'selected' : '' ?>>Ja (aanbevolen)</option>
          <option value="0" <?= empty($content['homepage_extras']['sticky_mobile_bar']) ? 'selected' : '' ?>>Nee</option>
        </select></div>
    </div>

    <div class="card">
      <h2>Kleurenpalet</h2>
      <p class="muted">Pas de hoofdkleur en accentkleuren aan. Wijzigingen zichtbaar na opslaan + refresh.</p>
      <?php
      $appColors = [
        'primary_color'       => ['Primair (paars)',           '#382d72'],
        'primary_color_dark'  => ['Primair donker',            '#2a2059'],
        'primary_color_light' => ['Primair licht (accent)',    '#9d92c4'],
        'ink_color'           => ['Achtergrond donker (hero)', '#0d1224'],
      ];
      foreach ($appColors as $k => [$label, $default]):
        $val = $content['appearance'][$k] ?? $default;
      ?>
        <div class="field">
          <label><?= $label ?></label>
          <div class="color-picker">
            <input type="color" value="<?= fld($val) ?>" />
            <input type="text" name="appearance[<?= $k ?>]" value="<?= fld($val) ?>" placeholder="<?= $default ?>" />
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ============ TAB: NAVIGATION ============ -->
  <div class="tab-panel" data-panel="navigation" id="tab-navigation">
    <div class="card">
      <h2>📋 Hoofdmenu</h2>
      <p class="muted">Bepaal welke items in het navigatiemenu staan en in welke volgorde.</p>
      <div id="nav-list">
        <?php foreach (($content['navigation']['items'] ?? []) as $i => $item): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong><?= fld($item['label'] ?? 'Menu item') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Label <span class="hint">(zichtbaar in menu)</span></label>
                <input type="text" name="navigation[items][<?= $i ?>][label]" value="<?= fld($item['label'] ?? '') ?>" /></div>
              <div class="field"><label>URL <span class="hint">(/page.php of https://...)</span></label>
                <input type="text" name="navigation[items][<?= $i ?>][url]" value="<?= fld($item['url'] ?? '') ?>" /></div>
              <div class="field"><label>Key <span class="hint">(unieke ID, lowercase)</span></label>
                <input type="text" name="navigation[items][<?= $i ?>][key]" value="<?= fld($item['key'] ?? '') ?>" /></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="nav"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg> Menu-item toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: FOOTER + ANNOUNCEMENT + COOKIES ============ -->
  <div class="tab-panel" data-panel="footer" id="tab-footer">
    <div class="card">
      <h2>📢 Aankondigingsbalk (boven aan de site)</h2>
      <p class="muted">Toon een paarse balk bovenaan voor aanbiedingen. Bv. "Zomeractie 20% korting!"</p>
      <div class="field"><label>Aan/uit</label>
        <select name="announcement[enabled]">
          <option value="0" <?= empty($content['announcement']['enabled']) ? 'selected' : '' ?>>Uit (verbergen)</option>
          <option value="1" <?= !empty($content['announcement']['enabled']) ? 'selected' : '' ?>>Aan (tonen)</option>
        </select></div>
      <div class="field"><label>Tekst</label>
        <input type="text" name="announcement[text]" value="<?= fld($content['announcement']['text'] ?? '') ?>" placeholder="Zomeractie: 20% korting bij 3+ panden!" /></div>
      <div class="field-row">
        <div class="field"><label>Link tekst (optioneel)</label>
          <input type="text" name="announcement[link_text]" value="<?= fld($content['announcement']['link_text'] ?? '') ?>" placeholder="Vraag offerte aan" /></div>
        <div class="field"><label>Link URL</label>
          <input type="text" name="announcement[link_url]" value="<?= fld($content['announcement']['link_url'] ?? '') ?>" placeholder="/contact.php" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Achtergrondkleur</label>
          <div class="color-picker">
            <input type="color" value="<?= fld($content['announcement']['background'] ?? '#382d72') ?>" />
            <input type="text" name="announcement[background]" value="<?= fld($content['announcement']['background'] ?? '#382d72') ?>" />
          </div></div>
        <div class="field"><label>Tekstkleur</label>
          <div class="color-picker">
            <input type="color" value="<?= fld($content['announcement']['text_color'] ?? '#ffffff') ?>" />
            <input type="text" name="announcement[text_color]" value="<?= fld($content['announcement']['text_color'] ?? '#ffffff') ?>" />
          </div></div>
      </div>
    </div>

    <div class="card">
      <h2>📄 Footer teksten</h2>
      <div class="field"><label>Tagline (eerste alinea in footer)</label>
        <textarea name="footer[tagline]" rows="3"><?= fld($content['footer']['tagline'] ?? '') ?></textarea></div>
      <div class="field"><label>Extra tekst <span class="hint">(optioneel, kleinere tekst eronder)</span></label>
        <textarea name="footer[extra_text]" rows="2"><?= fld($content['footer']['extra_text'] ?? '') ?></textarea></div>
      <div class="field"><label>Copyright tekst <span class="hint">(leeg = automatisch © jaar bedrijfsnaam)</span></label>
        <input type="text" name="footer[copyright_text]" value="<?= fld($content['footer']['copyright_text'] ?? '') ?>" /></div>
    </div>

    <div class="card">
      <h2>🍪 Cookie banner teksten</h2>
      <p class="muted">Pas de exacte tekst van het cookie-banner aan.</p>
      <div class="field"><label>Titel</label>
        <input type="text" name="cookie_banner[title]" value="<?= fld($content['cookie_banner']['title'] ?? 'Cookies & privacy') ?>" /></div>
      <div class="field"><label>Bericht</label>
        <textarea name="cookie_banner[message]" rows="3"><?= fld($content['cookie_banner']['message'] ?? '') ?></textarea></div>
      <div class="field-row">
        <div class="field"><label>Knop "Akkoord" tekst</label>
          <input type="text" name="cookie_banner[accept_label]" value="<?= fld($content['cookie_banner']['accept_label'] ?? 'Akkoord met alle') ?>" /></div>
        <div class="field"><label>Knop "Weigeren" tekst</label>
          <input type="text" name="cookie_banner[decline_label]" value="<?= fld($content['cookie_banner']['decline_label'] ?? 'Alleen noodzakelijk') ?>" /></div>
      </div>
    </div>
  </div>

  <!-- ============ TAB: LEADS SETTINGS ============ -->
  <div class="tab-panel" data-panel="leads_settings" id="tab-leads_settings">
    <div class="card">
      <h2>📨 Lead afhandeling</h2>
      <p class="muted">Hoe nieuwe aanvragen worden verstuurd en opgevolgd.</p>

      <div class="field"><label>Extra ontvangers <span class="hint">(komma-gescheiden e-mails die ook een kopie krijgen)</span></label>
        <input type="text" name="lead_settings[extra_recipients]" value="<?= fld($content['lead_settings']['extra_recipients'] ?? '') ?>" placeholder="partner@example.com, monteur@example.com" /></div>

      <div class="field"><label>Webhook URL <span class="hint">(Slack/Discord/Make/Zapier — POST endpoint)</span></label>
        <input type="url" name="lead_settings[webhook_url]" value="<?= fld($content['lead_settings']['webhook_url'] ?? '') ?>" placeholder="https://hooks.slack.com/services/..." /></div>
      <small style="color:var(--muted); font-size:12px;">Bij elke nieuwe lead wordt een POST-request gestuurd met JSON-payload: {text: "...", lead: {...}}.</small>
    </div>

    <div class="card">
      <h2>↩ Auto-reply naar klant</h2>
      <p class="muted">Stuur de klant automatisch een bevestigingsmail na het invullen van het formulier.</p>
      <div class="field"><label>Auto-reply aan/uit</label>
        <select name="lead_settings[auto_reply_enabled]">
          <option value="1" <?= !empty($content['lead_settings']['auto_reply_enabled']) ? 'selected' : '' ?>>Aan (aanbevolen)</option>
          <option value="0" <?= empty($content['lead_settings']['auto_reply_enabled']) ? 'selected' : '' ?>>Uit</option>
        </select></div>
      <div class="field"><label>Onderwerp</label>
        <input type="text" name="lead_settings[auto_reply_subject]" value="<?= fld($content['lead_settings']['auto_reply_subject'] ?? 'Bedankt voor uw aanvraag') ?>" /></div>
      <div class="field"><label>Bericht <span class="hint">(gebruik {naam}, {telefoon}, {postcode}, {type} voor variabelen)</span></label>
        <textarea name="lead_settings[auto_reply_message]" rows="10"><?= fld($content['lead_settings']['auto_reply_message'] ?? '') ?></textarea></div>
    </div>
  </div>

  <!-- ============ TAB: ADVANCED (Maintenance + Custom CSS) ============ -->
  <div class="tab-panel" data-panel="advanced" id="tab-advanced">
    <div class="card">
      <h2>🚧 Onderhoudsmodus</h2>
      <p class="muted">Zet de site tijdelijk offline (bezoekers zien een onderhouds-pagina, jij blijft inloggen via /admin/).</p>
      <div class="field"><label>Status</label>
        <select name="maintenance[enabled]">
          <option value="0" <?= empty($content['maintenance']['enabled']) ? 'selected' : '' ?>>Site online (normaal)</option>
          <option value="1" <?= !empty($content['maintenance']['enabled']) ? 'selected' : '' ?>>🚨 Onderhoudsmodus (offline)</option>
        </select></div>
      <div class="field"><label>Titel</label>
        <input type="text" name="maintenance[title]" value="<?= fld($content['maintenance']['title'] ?? 'We zijn even bezig') ?>" /></div>
      <div class="field"><label>Bericht</label>
        <textarea name="maintenance[message]" rows="4"><?= fld($content['maintenance']['message'] ?? '') ?></textarea></div>
      <div class="field"><label>Admin sessie omzeilt onderhoudsmodus?</label>
        <select name="maintenance[allow_admin_ip]">
          <option value="1" <?= !empty($content['maintenance']['allow_admin_ip']) ? 'selected' : '' ?>>Ja (jij ziet de echte site, bezoekers zien onderhoud)</option>
          <option value="0" <?= empty($content['maintenance']['allow_admin_ip']) ? 'selected' : '' ?>>Nee (iedereen ziet onderhoud)</option>
        </select></div>
    </div>

    <div class="card">
      <h2>💻 Custom CSS</h2>
      <p class="muted">Eigen CSS toevoegen aan de site. Geavanceerd — alleen als je weet wat je doet.</p>
      <div class="field">
        <label>CSS code</label>
        <textarea name="appearance[custom_css]" rows="10" style="font-family:ui-monospace,monospace;font-size:13px;"><?= fld($content['appearance']['custom_css'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- ============ TAB: PAGE TEXTS (sectie-headers + CTAs + timeline) ============ -->
  <div class="tab-panel" data-panel="page_texts" id="tab-page_texts">
    <?php
      $secs   = $content['sections'] ?? [];
      $ctasx  = $content['ctas'] ?? [];
      $tlx    = $content['timeline']['items'] ?? [];

      $sec_groups = [
        'Homepage' => [
          'home_prices'   => 'Prijzen-sectie',
          'home_process'  => 'Werkwijze-sectie',
          'home_ba'       => 'Before/After-sectie',
          'home_benefits' => 'Voordelen-sectie',
          'home_faq'      => 'FAQ-sectie',
        ],
        'Diensten-pagina' => [
          'diensten_hero'     => 'Hero (boven)',
          'diensten_benefits' => 'Inbegrepen-sectie',
        ],
        'Werkwijze-pagina' => [
          'werkwijze_hero'     => 'Hero (boven)',
          'werkwijze_timeline' => 'Timeline-sectie',
          'werkwijze_ba'       => 'Before/After-sectie',
        ],
        'Contact-pagina' => [
          'contact_faq' => 'FAQ-sectie',
        ],
      ];
    ?>

    <div class="card">
      <h2>📝 Pagina-teksten</h2>
      <p class="muted">Eyebrow (klein label), heading (grote titel) en intro-tekst van élke sectie op de website. Eén plek voor alle koppen — pas hier aan zonder code.</p>
    </div>

    <?php foreach ($sec_groups as $group_name => $sec_keys): ?>
      <div class="card">
        <h2><?= fld($group_name) ?></h2>
        <?php foreach ($sec_keys as $key => $label):
          $s = $secs[$key] ?? [];
        ?>
          <div class="section-block">
            <div class="section-block-title"><?= fld($label) ?></div>
            <div class="field-row">
              <div class="field"><label>Eyebrow <span class="hint">(klein label boven)</span></label>
                <input type="text" name="sections[<?= fld($key) ?>][eyebrow]" value="<?= fld($s['eyebrow'] ?? '') ?>" /></div>
              <div class="field"><label>Heading (H2)</label>
                <input type="text" name="sections[<?= fld($key) ?>][heading]" value="<?= fld($s['heading'] ?? '') ?>" /></div>
            </div>
            <div class="field"><label>Intro / paragraaf onder heading</label>
              <textarea name="sections[<?= fld($key) ?>][intro]" rows="2"><?= fld($s['intro'] ?? '') ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div class="card">
      <h2>🎯 CTA-banners (onderaan pagina)</h2>
      <p class="muted">De grote oproep-blokken onderaan elke pagina met "WhatsApp ons" knop.</p>
      <?php foreach (['diensten' => 'Diensten-pagina CTA', 'werkwijze' => 'Werkwijze-pagina CTA'] as $cta_key => $cta_label):
        $c = $ctasx[$cta_key] ?? [];
      ?>
        <div class="section-block">
          <div class="section-block-title"><?= fld($cta_label) ?></div>
          <div class="field"><label>Titel</label>
            <input type="text" name="ctas[<?= fld($cta_key) ?>][title]" value="<?= fld($c['title'] ?? '') ?>" /></div>
          <div class="field"><label>Tekst eronder</label>
            <textarea name="ctas[<?= fld($cta_key) ?>][text]" rows="2"><?= fld($c['text'] ?? '') ?></textarea></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <h2>⏱ Timeline "Op de dag zelf" (werkwijze-pagina)</h2>
      <p class="muted">De stappen die de klant op de dag zelf kan verwachten — tijd, titel en korte beschrijving.</p>
      <div id="timeline-list">
        <?php foreach ($tlx as $i => $row): ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head">
              <strong><?= fld($row['title'] ?? 'Stap') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>
            <div class="field-row">
              <div class="field"><label>Tijd <span class="hint">(bv. "08:30" of "0:00")</span></label>
                <input type="text" name="timeline[items][<?= $i ?>][time]" value="<?= fld($row['time'] ?? '') ?>" /></div>
              <div class="field"><label>Titel</label>
                <input type="text" name="timeline[items][<?= $i ?>][title]" value="<?= fld($row['title'] ?? '') ?>" /></div>
            </div>
            <div class="field"><label>Beschrijving</label>
              <textarea name="timeline[items][<?= $i ?>][text]" rows="2"><?= fld($row['text'] ?? '') ?></textarea></div>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="timeline-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Stap toevoegen</button>
    </div>
  </div>

  <!-- ============ TAB: LEGAL ============ -->
  <div class="tab-panel" data-panel="legal" id="tab-legal">
    <div class="card">
      <h2>🛡 Privacyverklaring</h2>
      <p class="muted">Verplicht voor AVG/GDPR. Pas aan met je echte gegevens.</p>
      <div class="field"><label>Pagina-titel</label>
        <input type="text" name="legal[privacy_title]" value="<?= fld($content['legal']['privacy_title'] ?? '') ?>" /></div>
      <div class="field"><label>Inhoud <span class="hint">(markdown — # heading, ## subheading, **vet**, * lijst)</span></label>
        <textarea name="legal[privacy_content]" rows="20" style="font-family:ui-monospace,monospace; font-size:13px;"><?= fld($content['legal']['privacy_content'] ?? '') ?></textarea></div>
    </div>

    <div class="card">
      <h2>📋 Algemene voorwaarden</h2>
      <div class="field"><label>Pagina-titel</label>
        <input type="text" name="legal[voorwaarden_title]" value="<?= fld($content['legal']['voorwaarden_title'] ?? '') ?>" /></div>
      <div class="field"><label>Inhoud <span class="hint">(markdown)</span></label>
        <textarea name="legal[voorwaarden_content]" rows="20" style="font-family:ui-monospace,monospace; font-size:13px;"><?= fld($content['legal']['voorwaarden_content'] ?? '') ?></textarea></div>
    </div>
  </div>

  <!-- ============ TAB: CUSTOM PAGES ============ -->
  <div class="tab-panel" data-panel="custom_pages" id="tab-custom_pages">
    <div class="card">
      <h2>📄 Eigen pagina's bouwen</h2>
      <p class="muted">Maak eigen pagina's zonder developer. Twee modi: <strong>Markdown</strong> (eenvoudig — koppen, lijsten, links) of <strong>HTML</strong> (volledige controle — eigen layout, CSS, scripts).</p>
      <div id="custom-list">
        <?php
          $live_base = rtrim($BASE ?? '', '/');
          foreach (($content['custom_pages']['items'] ?? []) as $i => $p):
            $page_key = $p['key'] ?? '';
            $full_url = $live_base . '/custom.php?p=' . rawurlencode($page_key);
            $page_mode = $p['mode'] ?? 'markdown';
            $layout    = $p['layout'] ?? 'default';
        ?>
          <div class="repeater-item" data-idx="<?= $i ?>">
            <div class="ri-head"><strong><?= fld($p['title'] ?? 'Pagina') ?></strong>
              <div class="ri-actions">
                <button type="button" class="icon-btn" data-action="up">↑</button>
                <button type="button" class="icon-btn" data-action="down">↓</button>
                <button type="button" class="icon-btn danger" data-action="remove">×</button>
              </div>
            </div>

            <?php if ($page_key !== ''): ?>
            <div class="custom-page-url">
              <span class="cpu-label">Live URL:</span>
              <a href="<?= fld($full_url) ?>" target="_blank" rel="noopener" class="cpu-link"><?= fld($full_url) ?></a>
              <button type="button" class="btn btn-ghost btn-sm cpu-copy" data-copy="<?= fld($full_url) ?>">📋 Kopieer</button>
            </div>
            <?php endif; ?>

            <div class="field-row">
              <div class="field"><label>Key <span class="hint">(URL-deel, bv. "spinnen-info")</span></label>
                <input type="text" name="custom_pages[items][<?= $i ?>][key]" value="<?= fld($page_key) ?>" /></div>
              <div class="field"><label>Eyebrow (klein label)</label>
                <input type="text" name="custom_pages[items][<?= $i ?>][eyebrow]" value="<?= fld($p['eyebrow'] ?? '') ?>" placeholder="Info" /></div>
            </div>
            <div class="field"><label>Titel</label>
              <input type="text" name="custom_pages[items][<?= $i ?>][title]" value="<?= fld($p['title'] ?? '') ?>" /></div>
            <div class="field"><label>Intro <span class="hint">(alleen zichtbaar bij standaard layout)</span></label>
              <textarea name="custom_pages[items][<?= $i ?>][intro]" rows="2"><?= fld($p['intro'] ?? '') ?></textarea></div>
            <div class="field"><label>Meta description (SEO)</label>
              <input type="text" name="custom_pages[items][<?= $i ?>][meta_description]" value="<?= fld($p['meta_description'] ?? '') ?>" /></div>

            <div class="field-row">
              <div class="field"><label>Inhoud-type</label>
                <select name="custom_pages[items][<?= $i ?>][mode]">
                  <option value="markdown" <?= $page_mode==='markdown'?'selected':'' ?>>📝 Markdown (eenvoudig)</option>
                  <option value="html"     <?= $page_mode==='html'    ?'selected':'' ?>>💻 HTML (volledige controle)</option>
                </select></div>
              <div class="field"><label>Layout</label>
                <select name="custom_pages[items][<?= $i ?>][layout]">
                  <option value="default" <?= $layout==='default'?'selected':'' ?>>Standaard (met header, hero, footer)</option>
                  <option value="blank"   <?= $layout==='blank'  ?'selected':'' ?>>Blanco (alleen menu + footer, geen hero)</option>
                  <option value="raw"     <?= $layout==='raw'    ?'selected':'' ?>>Volledig blanco (geen header/footer — alles zelf)</option>
                </select></div>
            </div>

            <div class="field"><label>Inhoud <span class="hint" id="hint-mode-<?= $i ?>">
              <?php if ($page_mode === 'html'): ?>HTML — gebruik <code>&lt;h2&gt;</code>, <code>&lt;div&gt;</code>, <code>&lt;img&gt;</code>, eigen <code>&lt;style&gt;</code> en <code>&lt;script&gt;</code> als je wilt.
              <?php else: ?>Markdown — <code># kop</code>, <code>**vet**</code>, <code>* lijst</code>, <code>[link](url)</code>.<?php endif; ?>
              </span></label>
              <textarea name="custom_pages[items][<?= $i ?>][content]" rows="14" style="font-family:ui-monospace,monospace; font-size:13px;"><?= fld($p['content'] ?? '') ?></textarea></div>

            <details style="margin-top:8px;">
              <summary style="cursor:pointer; font-weight:600; font-size:13px; color:var(--violet-700); padding:6px 0;">🎨 Eigen CSS (optioneel)</summary>
              <div class="field" style="margin-top:8px;"><label>CSS <span class="hint">(wordt geladen als <code>&lt;style&gt;</code> in <code>&lt;head&gt;</code>)</span></label>
                <textarea name="custom_pages[items][<?= $i ?>][custom_css]" rows="8" style="font-family:ui-monospace,monospace; font-size:13px;" placeholder=".my-class { color: red; }"><?= fld($p['custom_css'] ?? '') ?></textarea></div>
            </details>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn-add" data-add="custom_page"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg> Pagina toevoegen</button>
    </div>

    <div class="card">
      <h2>💡 Voorbeelden van wat je kunt bouwen</h2>
      <ul class="muted" style="line-height:1.8;">
        <li><strong>📰 Info-pagina</strong> — markdown-modus, standaard layout. Bv. "Spinnen-soorten in NL" voor SEO.</li>
        <li><strong>🎨 Landingspagina</strong> — HTML-modus + blanco layout. Eigen design voor specifieke campagne (Google Ads, Instagram).</li>
        <li><strong>👥 Vacature-pagina</strong> — markdown-modus, standaard layout. "Wij zoeken een collega".</li>
        <li><strong>📍 Lokale SEO</strong> — markdown, één pagina per stad. Bv. "Spinnenbestrijding Rotterdam".</li>
        <li><strong>🚀 Volledig zelf gebouwd</strong> — HTML + raw layout + eigen CSS. Geen header/footer, jij bouwt alles.</li>
      </ul>
    </div>
  </div>

  <div class="save-bar">
    <div class="save-status" id="saveStatus">
      <span class="dot"></span>
      <span class="label">Alle wijzigingen opgeslagen</span>
    </div>
    <div class="save-actions">
      <a href="<?= htmlspecialchars($BASE) ?>/" target="_blank" class="btn btn-ghost">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 H20 V10 M10 14 L20 4"/></svg>
        Voorbeeld
      </a>
      <button type="submit" class="btn btn-primary btn-lg">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Wijzigingen opslaan
      </button>
    </div>
  </div>
</form>

<!-- ============ Image Picker Modal ============ -->
<div class="picker-modal" id="pickerModal" hidden>
  <div class="picker-backdrop" data-picker-close></div>
  <div class="picker-dialog">
    <div class="picker-head">
      <div>
        <h2>Kies een foto</h2>
        <p>Selecteer een foto uit je bibliotheek of upload een nieuwe.</p>
      </div>
      <button type="button" class="picker-close-btn" data-picker-close aria-label="Sluiten">×</button>
    </div>

    <label class="picker-upload" id="pickerUpload">
      <input type="file" accept="image/*" hidden />
      <div class="picker-upload-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <div>
        <strong>Upload nieuwe foto</strong>
        <small>JPG, PNG, WebP of GIF · max 10 MB</small>
      </div>
    </label>

    <div class="picker-grid-wrap">
      <div class="picker-grid" id="pickerGrid">
        <div class="picker-loading">Foto's laden…</div>
      </div>
    </div>
  </div>
</div>

<script>
  window.SPINGUARD_CSRF = <?= json_encode($tok) ?>;
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
