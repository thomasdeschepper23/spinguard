<?php
require __DIR__ . '/config.php';
admin_require_login();
define('SPINGUARD_INC', true);
require __DIR__ . '/../inc/leads_store.php';

$tok = csrf_token();

// === Filters ===
$status_filter = $_GET['status'] ?? 'all';
$range_filter  = $_GET['range']  ?? 'all';   // all | week | month | quarter | year
$search        = trim($_GET['q'] ?? '');

$all_leads = leads_load();
$stats     = leads_stats();

// Pas filters toe
$leads = $all_leads;
if ($status_filter !== 'all') {
    $leads = array_values(array_filter($leads, fn($l) => ($l['status'] ?? 'nieuw') === $status_filter));
}
if ($range_filter !== 'all') {
    $cutoffs = [
        'week'    => strtotime('-7 days'),
        'month'   => strtotime('-30 days'),
        'quarter' => strtotime('-90 days'),
        'year'    => strtotime('-365 days'),
    ];
    if (isset($cutoffs[$range_filter])) {
        $cut = $cutoffs[$range_filter];
        $leads = array_values(array_filter($leads, function($l) use ($cut) {
            $t = strtotime($l['created'] ?? '');
            return $t && $t >= $cut;
        }));
    }
}
if ($search !== '') {
    $needle = mb_strtolower($search);
    $leads = array_values(array_filter($leads, function($l) use ($needle) {
        $hay = mb_strtolower(($l['name'] ?? '') . ' ' . ($l['phone'] ?? '') . ' ' . ($l['email'] ?? '') . ' ' . ($l['postcode'] ?? '') . ' ' . ($l['type'] ?? '') . ' ' . ($l['message'] ?? '') . ' ' . ($l['note'] ?? ''));
        return mb_strpos($hay, $needle) !== false;
    }));
}

$site_email = json_decode(@file_get_contents(__DIR__.'/../content/site.json') ?: '{}', true)['site']['email'] ?? 'info@spinguard.nl';

function tab_url($base, $current_status, $current_range, $current_q, $status_override = null, $range_override = null) {
    $params = [];
    $s = $status_override !== null ? $status_override : $current_status;
    $r = $range_override !== null ? $range_override : $current_range;
    if ($s !== 'all') $params['status'] = $s;
    if ($r !== 'all') $params['range'] = $r;
    if ($current_q !== '') $params['q'] = $current_q;
    return $base . '/admin/leads.php' . ($params ? '?' . http_build_query($params) : '');
}

include __DIR__ . '/_layout_top.php';
?>
<div class="main-head">
  <div>
    <h1>Aanvragen <span class="muted-sm" style="font-weight:500;">(<?= count($leads) ?> getoond · <?= $stats['totaal'] ?> totaal)</span></h1>
    <p>Beheer alle inkomende leads. Markeer als verkocht/verloren, voeg notitie toe, plan terugbel-afspraken.</p>
  </div>
  <div class="head-actions">
    <a href="<?= htmlspecialchars($BASE) ?>/admin/api-export-leads.php" class="btn btn-ghost">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </a>
    <button type="button" class="btn btn-primary" id="openAddLead">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
      Lead toevoegen
    </button>
  </div>
</div>

<!-- Modal: handmatig lead toevoegen (WhatsApp/telefoon-gesprekken) -->
<div class="lead-modal" id="addLeadModal" hidden>
  <div class="lead-modal-backdrop" data-close></div>
  <div class="lead-modal-dialog">
    <div class="lead-modal-head">
      <h2>Lead handmatig toevoegen</h2>
      <button type="button" class="lead-modal-close" data-close aria-label="Sluiten">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6 L18 18 M18 6 L6 18"/></svg>
      </button>
    </div>
    <p class="muted" style="margin: -8px 0 16px;">Voor leads die binnenkomen via WhatsApp, telefoon of e-mail. Ze verschijnen direct in het overzicht.</p>
    <form id="addLeadForm">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
      <div class="field-row">
        <div class="field"><label>Naam *</label>
          <input type="text" name="name" required autocomplete="off" /></div>
        <div class="field"><label>Telefoon *</label>
          <input type="tel" name="phone" required autocomplete="off" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>E-mail</label>
          <input type="email" name="email" autocomplete="off" /></div>
        <div class="field"><label>Postcode</label>
          <input type="text" name="postcode" autocomplete="off" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Type pand</label>
          <select name="type">
            <option value="">— Kies —</option>
            <option value="Appartement">Appartement</option>
            <option value="Rijtjeshuis">Rijtjeshuis</option>
            <option value="2-onder-1-kap">2-onder-1-kap</option>
            <option value="Vrijstaande woning">Vrijstaande woning</option>
            <option value="Bedrijfspand">Bedrijfspand</option>
            <option value="Anders">Anders</option>
          </select></div>
        <div class="field"><label>Bron</label>
          <select name="source">
            <option value="whatsapp">WhatsApp</option>
            <option value="telefoon">Telefoon</option>
            <option value="email">E-mail</option>
            <option value="instagram">Instagram</option>
            <option value="aanbeveling">Aanbeveling</option>
            <option value="handmatig">Anders / handmatig</option>
          </select></div>
      </div>
      <div class="field"><label>Bericht / vraag</label>
        <textarea name="message" rows="3" placeholder="Wat heeft de klant gevraagd?"></textarea></div>
      <div class="field-row">
        <div class="field"><label>Beginstatus</label>
          <select name="status">
            <option value="nieuw">🆕 Nieuw</option>
            <option value="gecontacteerd" selected>📞 Gecontacteerd</option>
            <option value="gewonnen">✅ Gewonnen (sale!)</option>
            <option value="verloren">❌ Verloren</option>
          </select></div>
        <div class="field"><label>Dealwaarde (€) <span class="hint">(alleen bij gewonnen)</span></label>
          <input type="number" name="sale_value" min="0" step="0.01" placeholder="0" /></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Follow-up datum <span class="hint">(optioneel)</span></label>
          <input type="date" name="follow_up_date" /></div>
        <div class="field"><label>Tags <span class="hint">(komma-gescheiden)</span></label>
          <input type="text" name="tags" placeholder="spoed, particulier" /></div>
      </div>
      <div class="field"><label>Interne notitie</label>
        <textarea name="note" rows="2" placeholder="Eigen notities, context, afspraken…"></textarea></div>
      <div class="lead-modal-foot">
        <button type="button" class="btn btn-ghost" data-close>Annuleren</button>
        <button type="submit" class="btn btn-primary" id="addLeadSubmit">Lead opslaan</button>
      </div>
    </form>
  </div>
</div>

<!-- Status filter tabs -->
<div class="leads-filter-bar">
  <div class="leads-filter-tabs">
    <?php
      $status_tabs = [
        'all'           => ['Alle',          $stats['totaal']],
        'nieuw'         => ['Nieuw',         $stats['nieuw']],
        'gecontacteerd' => ['Gecontacteerd', $stats['gecontacteerd']],
        'gewonnen'      => ['Gewonnen',      $stats['gewonnen']],
        'verloren'      => ['Verloren',      $stats['verloren']],
        'gearchiveerd'  => ['Archief',       $stats['gearchiveerd']],
      ];
      foreach ($status_tabs as $key => [$label, $count]):
        $active = $status_filter === $key ? ' is-active' : '';
    ?>
      <a href="<?= htmlspecialchars(tab_url($BASE, $status_filter, $range_filter, $search, $key)) ?>" class="filter-tab tab-<?= $key ?><?= $active ?>">
        <?= htmlspecialchars($label) ?> <span class="filter-count"><?= $count ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <form method="get" class="leads-filter-extra">
    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>" />
    <select name="range" onchange="this.form.submit()" class="filter-select">
      <option value="all"     <?= $range_filter==='all'    ?'selected':'' ?>>Alle tijd</option>
      <option value="week"    <?= $range_filter==='week'   ?'selected':'' ?>>Laatste 7 dagen</option>
      <option value="month"   <?= $range_filter==='month'  ?'selected':'' ?>>Laatste 30 dagen</option>
      <option value="quarter" <?= $range_filter==='quarter'?'selected':'' ?>>Laatste 3 maanden</option>
      <option value="year"    <?= $range_filter==='year'   ?'selected':'' ?>>Laatste jaar</option>
    </select>
    <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Zoek naam, telefoon, postcode…" class="filter-search" />
    <button type="submit" class="btn btn-ghost btn-sm">Zoek</button>
    <?php if ($search !== '' || $range_filter !== 'all'): ?>
      <a href="<?= htmlspecialchars(tab_url($BASE, $status_filter, 'all', '')) ?>" class="btn btn-ghost btn-sm">Wissen</a>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <?php if (empty($leads)): ?>
    <div class="empty">
      <div class="empty-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7 L12 13 L21 7"/></svg>
      </div>
      <h3>Geen aanvragen<?= $search !== '' ? ' gevonden' : '' ?></h3>
      <p><?php if ($search !== '' || $status_filter !== 'all' || $range_filter !== 'all'): ?>
        Probeer een ander filter of wis de zoekterm.
      <?php else: ?>
        Aanvragen via het contactformulier verschijnen hier.
        <br><span class="muted-sm">📧 Notificaties gaan naar: <strong><?= htmlspecialchars($site_email) ?></strong></span>
      <?php endif; ?></p>
    </div>
  <?php else: ?>
    <div class="bulk-actions" id="bulkActions" style="display:none; padding:14px 18px; background:var(--violet-50); border:1px solid var(--violet-200); border-radius:12px; margin-bottom:14px; align-items:center; gap:14px; flex-wrap:wrap;">
      <strong id="bulkCount" style="color:var(--violet-700);">0 geselecteerd</strong>
      <button type="button" class="btn btn-ghost btn-sm" data-bulk="gecontacteerd">📞 Markeer gecontacteerd</button>
      <button type="button" class="btn btn-success btn-sm" data-bulk="gewonnen">✅ Markeer gewonnen</button>
      <button type="button" class="btn btn-ghost btn-sm" data-bulk="verloren">❌ Markeer verloren</button>
      <button type="button" class="btn btn-ghost btn-sm" data-bulk="gearchiveerd">📁 Archiveer</button>
      <button type="button" class="btn btn-danger btn-sm" data-bulk="delete">Verwijder</button>
      <button type="button" class="btn btn-ghost btn-sm" id="bulkDeselect" style="margin-left:auto;">Deselecteer</button>
    </div>

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; padding:8px 12px;">
      <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--muted);">
        <input type="checkbox" id="selectAllLeads" /> Selecteer alle
      </label>
    </div>

    <div class="leads-list" data-csrf="<?= htmlspecialchars($tok) ?>">
      <?php foreach ($leads as $raw):
        $l = lead_normalize($raw);
        $status = $l['status'];
        $statusClass = ['nieuw'=>'st-new','gecontacteerd'=>'st-done','gewonnen'=>'st-won','verloren'=>'st-lost','gearchiveerd'=>'st-arch'][$status] ?? 'st-new';
      ?>
        <div class="lead-card <?= $statusClass ?>" data-id="<?= htmlspecialchars($l['id']) ?>" id="<?= htmlspecialchars($l['id']) ?>">
          <div class="lead-head">
            <div class="lead-meta">
              <input type="checkbox" class="lead-select" style="margin-right:8px; cursor:pointer; transform:scale(1.2);" />
              <strong class="lead-name"><?= htmlspecialchars($l['name']) ?></strong>
              <span class="lead-status status-<?= $status ?>"><?= ucfirst($status) ?></span>
              <?php if ($status === 'gewonnen' && (float)$l['sale_value'] > 0): ?>
                <span class="lead-sale-badge">💰 €<?= number_format((float)$l['sale_value'], 0, ',', '.') ?></span>
              <?php endif; ?>
              <?php foreach ($l['tags'] ?? [] as $tag): ?>
                <span class="lead-tag"><?= htmlspecialchars($tag) ?></span>
              <?php endforeach; ?>
            </div>
            <span class="lead-date"><?= htmlspecialchars($l['created']) ?></span>
          </div>
          <div class="lead-body">
            <div class="lead-row">
              <a href="tel:<?= htmlspecialchars($l['phone']) ?>" class="lead-link">📞 <?= htmlspecialchars($l['phone']) ?></a>
              <?php if (!empty($l['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($l['email']) ?>" class="lead-link">✉ <?= htmlspecialchars($l['email']) ?></a>
              <?php endif; ?>
              <span class="lead-link">📍 <?= htmlspecialchars($l['postcode']) ?></span>
              <span class="lead-link">🏠 <?= htmlspecialchars($l['type']) ?></span>
              <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^\d]/', '', $l['phone'])) ?>" target="_blank" rel="noopener" class="lead-link wa">💬 WhatsApp</a>
              <?php if (!empty($l['follow_up_date'])): ?>
                <span class="lead-link" style="background:#fff3d9; color:#7a4f00;">⏰ <?= htmlspecialchars(date('d M Y', strtotime($l['follow_up_date']))) ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($l['message'])): ?>
              <div class="lead-msg"><?= nl2br(htmlspecialchars($l['message'])) ?></div>
            <?php endif; ?>
            <?php if (!empty($l['photos']) && is_array($l['photos'])): ?>
              <div class="lead-photos">
                <?php foreach ($l['photos'] as $ph):
                  $href = htmlspecialchars($BASE . ($ph['path'] ?? ''));
                ?>
                  <a href="<?= $href ?>" target="_blank" rel="noopener" class="lead-photo">
                    <img src="<?= $href ?>" alt="" loading="lazy" />
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($l['utm_source'])): ?>
              <div class="lead-utm">via <strong><?= htmlspecialchars($l['utm_source']) ?></strong> · <?= htmlspecialchars($l['utm_medium']) ?> · <?= htmlspecialchars($l['utm_campaign']) ?></div>
            <?php endif; ?>
            <?php if (!empty($l['note'])): ?>
              <div class="lead-note">📝 <?= nl2br(htmlspecialchars($l['note'])) ?></div>
            <?php endif; ?>
            <?php if (!empty($l['lost_reason'])): ?>
              <div class="lead-note" style="background:#fde4e4; color:#7a1a1a;">❌ Reden: <?= htmlspecialchars($l['lost_reason']) ?></div>
            <?php endif; ?>
          </div>
          <div class="lead-actions">
            <select class="lead-status-select" data-lead-action="status">
              <option value="nieuw"         <?= $status==='nieuw'        ?'selected':'' ?>>🆕 Nieuw</option>
              <option value="gecontacteerd" <?= $status==='gecontacteerd'?'selected':'' ?>>📞 Gecontacteerd</option>
              <option value="gewonnen"      <?= $status==='gewonnen'     ?'selected':'' ?>>✅ Gewonnen</option>
              <option value="verloren"      <?= $status==='verloren'     ?'selected':'' ?>>❌ Verloren</option>
              <option value="gearchiveerd"  <?= $status==='gearchiveerd' ?'selected':'' ?>>📁 Gearchiveerd</option>
            </select>
            <button type="button" class="btn btn-ghost btn-sm" data-lead-action="note">📝 Notitie</button>
            <button type="button" class="btn btn-ghost btn-sm" data-lead-action="followup">⏰ Follow-up</button>
            <button type="button" class="btn btn-ghost btn-sm" data-lead-action="tags">🏷 Tags</button>
            <button type="button" class="btn btn-danger btn-sm" data-lead-action="delete">Verwijderen</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
