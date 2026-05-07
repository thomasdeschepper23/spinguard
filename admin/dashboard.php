<?php
require __DIR__ . '/config.php';
admin_require_login();

$CONTENT_FILE = __DIR__ . '/../content/site.json';
$raw = @file_get_contents($CONTENT_FILE);
$content = json_decode($raw ?: '{}', true) ?: [];

$saved = isset($_GET['saved']);
$err   = $_GET['err'] ?? '';
$tok = csrf_token();

define('SPINGUARD_INC', true);
require __DIR__ . '/../inc/leads_store.php';

// === Statistieken voor deze maand + vorige maand (trend delta) ===
$start_this_month = strtotime(date('Y-m-01 00:00:00'));
$start_last_month = strtotime(date('Y-m-01 00:00:00', strtotime('first day of last month')));
$end_last_month   = $start_this_month - 1;

$stats_total      = leads_stats();                        // alle tijd
$stats_this_month = leads_stats($start_this_month);

// vorige maand: filter handmatig (we hebben alleen since_ts)
$prev_leads = array_filter(leads_load(), function($l) use ($start_last_month, $end_last_month) {
    $ts = strtotime($l['created'] ?? '');
    return $ts && $ts >= $start_last_month && $ts <= $end_last_month;
});
$prev_count = count($prev_leads);
$prev_won = 0; $prev_revenue = 0.0; $prev_closed = 0;
foreach ($prev_leads as $l) {
    if (($l['status'] ?? '') === 'gewonnen') { $prev_won++; $prev_revenue += (float)($l['sale_value'] ?? 0); $prev_closed++; }
    elseif (($l['status'] ?? '') === 'verloren') { $prev_closed++; }
}
$prev_conv = $prev_closed > 0 ? ($prev_won / $prev_closed) * 100 : 0;
$prev_avg = $prev_won > 0 ? $prev_revenue / $prev_won : 0;

function delta_pct($cur, $prev) {
    if ($prev <= 0) return $cur > 0 ? 100 : 0;
    return round((($cur - $prev) / $prev) * 100);
}

$delta_leads = delta_pct($stats_this_month['totaal'], $prev_count);
$delta_conv  = $prev_conv > 0 ? round($stats_this_month['conversion_rate'] - $prev_conv, 1) : 0;
$delta_rev   = delta_pct($stats_this_month['revenue'], $prev_revenue);
$delta_avg   = delta_pct($stats_this_month['avg_deal'], $prev_avg);

// === Hot leads + follow-ups ===
$hot_leads = leads_hot(24);
$follow_ups = leads_followups_due();

// === Trend per dag (laatste 30 dagen) ===
$per_day = leads_per_day(30);
$max_day = max($per_day) ?: 1;

// === Top breakdowns ===
$top_types  = leads_top_field('type', 5);
$top_postcodes = leads_top_field('postcode', 5);
$top_utm    = leads_top_field('utm_source', 5);

// === Recent leads ===
$recent_leads = array_slice(leads_load(), 0, 5);

// === Health check ===
$health_warnings = [];
if (!is_writable(__DIR__ . '/../content')) $health_warnings[] = 'content/ is niet schrijfbaar';
if (!is_writable(__DIR__ . '/../uploads')) $health_warnings[] = 'uploads/ is niet schrijfbaar';
if (empty($_SERVER['HTTPS'])) $health_warnings[] = 'HTTPS is niet actief — activeer SSL via hosting';
if (!empty($content['maintenance']['enabled'])) $health_warnings[] = '🚨 Onderhoudsmodus staat AAN — bezoekers zien onderhoudspagina';

function fmt_eur($v) {
    return '€' . number_format((float)$v, 0, ',', '.');
}
function delta_label($d, $type = 'pct') {
    if ($d === 0) return '<span class="delta neutral">±0</span>';
    $cls = $d > 0 ? 'up' : 'down';
    $sign = $d > 0 ? '+' : '';
    $suffix = $type === 'pct' ? '%' : 'pp';
    return '<span class="delta ' . $cls . '">' . $sign . $d . $suffix . '</span>';
}

include __DIR__ . '/_layout_top.php';
?>

<div class="main-head">
  <div>
    <h1>Welkom terug 👋</h1>
    <p>Overzicht van leads, omzet en site — bijgewerkt op <?= date('d-m-Y H:i') ?>.</p>
  </div>
  <div class="head-actions">
    <a href="<?= htmlspecialchars($BASE) ?>/" target="_blank" class="btn btn-ghost">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 H20 V10 M10 14 L20 4 M19 14 V19 C19 20 18 21 17 21 H5 C4 21 3 20 3 19 V7 C3 6 4 5 5 5 H10"/></svg>
      Bekijk website
    </a>
    <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php" class="btn btn-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7 L12 13 L21 7"/></svg>
      Aanvragen
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

<?php foreach ($health_warnings as $w): ?>
  <div class="notice error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v4M12 17v.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
    <?= htmlspecialchars($w) ?>
  </div>
<?php endforeach; ?>

<!-- ============ BUSINESS METRICS — DEZE MAAND ============ -->
<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-head">
      <span class="metric-label">Nieuwe aanvragen</span>
      <span class="metric-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7 L12 13 L21 7"/></svg></span>
    </div>
    <div class="metric-value"><?= $stats_this_month['totaal'] ?></div>
    <div class="metric-foot">
      Deze maand · <?= delta_label($delta_leads) ?> vs vorige
    </div>
  </div>

  <div class="metric-card">
    <div class="metric-head">
      <span class="metric-label">Conversion rate</span>
      <span class="metric-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17 L9 11 L13 15 L21 7"/><path d="M14 7 H21 V14"/></svg></span>
    </div>
    <div class="metric-value"><?= number_format($stats_this_month['conversion_rate'], 0) ?>%</div>
    <div class="metric-foot">
      <?= $stats_this_month['gewonnen'] ?> gewonnen / <?= $stats_this_month['gewonnen'] + $stats_this_month['verloren'] ?> beslist · <?= delta_label($delta_conv, 'pp') ?>
    </div>
  </div>

  <div class="metric-card">
    <div class="metric-head">
      <span class="metric-label">Omzet deze maand</span>
      <span class="metric-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1 V23 M17 5 H9.5 C8 5 7 6 7 7.5 C7 9 8 10 9.5 10 H14.5 C16 10 17 11 17 12.5 C17 14 16 15 14.5 15 H6"/></svg></span>
    </div>
    <div class="metric-value"><?= fmt_eur($stats_this_month['revenue']) ?></div>
    <div class="metric-foot">
      <?= delta_label($delta_rev) ?> vs vorige maand
    </div>
  </div>

  <div class="metric-card">
    <div class="metric-head">
      <span class="metric-label">Gem. dealwaarde</span>
      <span class="metric-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12 H16 M12 8 V16"/></svg></span>
    </div>
    <div class="metric-value"><?= fmt_eur($stats_this_month['avg_deal']) ?></div>
    <div class="metric-foot">
      <?= delta_label($delta_avg) ?> vs vorige maand
    </div>
  </div>
</div>

<!-- ============ HOT LEADS + FOLLOW-UPS ============ -->
<?php if (!empty($hot_leads) || !empty($follow_ups)): ?>
<div class="action-grid">
  <?php if (!empty($hot_leads)): ?>
  <div class="card action-card hot">
    <div class="action-head">
      <div>
        <h2 class="action-title">🔥 Hot leads</h2>
        <p class="action-sub"><?= count($hot_leads) ?> nieuwe aanvragen wachten al langer dan 24 uur</p>
      </div>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php?status=nieuw" class="btn btn-ghost btn-sm">Alle nieuw →</a>
    </div>
    <div class="action-list">
      <?php foreach (array_slice($hot_leads, 0, 4) as $l):
        $waited_h = round((time() - strtotime($l['created'])) / 3600);
      ?>
        <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php#<?= htmlspecialchars($l['id']) ?>" class="action-row">
          <strong><?= htmlspecialchars($l['name']) ?></strong>
          <span class="action-meta"><?= htmlspecialchars($l['type'] ?: 'Onbekend') ?> · <?= htmlspecialchars($l['postcode']) ?></span>
          <span class="action-badge urgent"><?= $waited_h ?>u</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($follow_ups)): ?>
  <div class="card action-card followup">
    <div class="action-head">
      <div>
        <h2 class="action-title">⏰ Follow-ups vandaag</h2>
        <p class="action-sub"><?= count($follow_ups) ?> leads om vandaag terug te bellen</p>
      </div>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php" class="btn btn-ghost btn-sm">Alles →</a>
    </div>
    <div class="action-list">
      <?php foreach (array_slice($follow_ups, 0, 4) as $l): ?>
        <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php#<?= htmlspecialchars($l['id']) ?>" class="action-row">
          <strong><?= htmlspecialchars($l['name']) ?></strong>
          <span class="action-meta">📞 <?= htmlspecialchars($l['phone']) ?></span>
          <span class="action-badge"><?= htmlspecialchars(date('d M', strtotime($l['follow_up_date']))) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ============ TREND + LEADS PER STATUS ============ -->
<div class="dash-row">
  <!-- Trend chart -->
  <div class="card chart-card">
    <div class="card-head">
      <h2>Aanvragen — laatste 30 dagen</h2>
      <span class="muted-sm">totaal: <?= array_sum($per_day) ?></span>
    </div>
    <?php if (array_sum($per_day) === 0): ?>
      <div class="empty-mini">Nog geen leads in de laatste 30 dagen.</div>
    <?php else: ?>
      <div class="bar-chart">
        <?php
          $i = 0;
          foreach ($per_day as $date => $count):
            $h = $count > 0 ? max(8, round(($count / $max_day) * 100)) : 2;
            $weekday = date('D', strtotime($date));
            $is_first_of_month = (int)substr($date, 8, 2) === 1;
            $show_label = $is_first_of_month || $i === 0 || $i === count($per_day) - 1;
        ?>
          <div class="bar-col" title="<?= htmlspecialchars($date) ?>: <?= $count ?> leads">
            <div class="bar-fill" style="height: <?= $h ?>%;"<?= $count > 0 ? ' data-count="'.$count.'"' : '' ?>></div>
            <?php if ($show_label): ?>
              <small><?= date('d/m', strtotime($date)) ?></small>
            <?php endif; ?>
          </div>
        <?php
            $i++;
          endforeach;
        ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Status breakdown -->
  <div class="card status-card">
    <div class="card-head"><h2>Lead status — totaal</h2></div>
    <div class="status-rows">
      <?php
        $status_meta = [
          'nieuw'         => ['🆕', 'Nieuw', 'st-new'],
          'gecontacteerd' => ['📞', 'Gecontacteerd', 'st-done'],
          'gewonnen'      => ['✅', 'Gewonnen', 'st-won'],
          'verloren'      => ['❌', 'Verloren', 'st-lost'],
          'gearchiveerd'  => ['📁', 'Gearchiveerd', 'st-arch'],
        ];
        foreach ($status_meta as $key => [$emoji, $label, $cls]):
          $n = $stats_total[$key] ?? 0;
          $pct = $stats_total['totaal'] > 0 ? ($n / $stats_total['totaal']) * 100 : 0;
      ?>
        <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php?status=<?= $key ?>" class="status-row <?= $cls ?>">
          <span class="status-emoji"><?= $emoji ?></span>
          <span class="status-label"><?= $label ?></span>
          <span class="status-bar"><span style="width: <?= $pct ?>%;"></span></span>
          <strong class="status-count"><?= $n ?></strong>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ============ TOP-OVERZICHTEN ============ -->
<div class="dash-row">
  <div class="card">
    <div class="card-head"><h2>🏠 Meest gevraagd type</h2></div>
    <?php if (empty($top_types)): ?>
      <div class="empty-mini">Nog geen data.</div>
    <?php else: ?>
      <div class="top-list">
        <?php $max = max($top_types); foreach ($top_types as $type => $n): ?>
          <div class="top-row">
            <span class="top-label"><?= htmlspecialchars($type) ?></span>
            <span class="top-bar"><span style="width: <?= ($n/$max)*100 ?>%;"></span></span>
            <strong class="top-count"><?= $n ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-head"><h2>📍 Top postcodes</h2></div>
    <?php if (empty($top_postcodes)): ?>
      <div class="empty-mini">Nog geen data.</div>
    <?php else: ?>
      <div class="top-list">
        <?php $max = max($top_postcodes); foreach ($top_postcodes as $pc => $n): ?>
          <div class="top-row">
            <span class="top-label"><?= htmlspecialchars($pc) ?></span>
            <span class="top-bar"><span style="width: <?= ($n/$max)*100 ?>%;"></span></span>
            <strong class="top-count"><?= $n ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-head"><h2>📡 Top traffic-bron</h2></div>
    <?php if (empty($top_utm)): ?>
      <div class="empty-mini">Nog geen UTM-data.<br><span class="muted-sm">Voeg <code>?utm_source=...</code> toe aan ad-links om bron te tracken.</span></div>
    <?php else: ?>
      <div class="top-list">
        <?php $max = max($top_utm); foreach ($top_utm as $src => $n): ?>
          <div class="top-row">
            <span class="top-label"><?= htmlspecialchars($src) ?></span>
            <span class="top-bar"><span style="width: <?= ($n/$max)*100 ?>%;"></span></span>
            <strong class="top-count"><?= $n ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ============ RECENTE LEADS ============ -->
<?php if (!empty($recent_leads)): ?>
<div class="card">
  <div class="card-head">
    <h2>Recente aanvragen</h2>
    <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php" class="btn btn-ghost btn-sm">Alles bekijken →</a>
  </div>
  <div class="leads-list">
    <?php foreach ($recent_leads as $l):
      $l = lead_normalize($l);
      $status = $l['status'];
      $statusClass = ['nieuw'=>'st-new','gecontacteerd'=>'st-done','gewonnen'=>'st-won','verloren'=>'st-lost','gearchiveerd'=>'st-arch'][$status] ?? 'st-new';
    ?>
      <a href="<?= htmlspecialchars($BASE) ?>/admin/leads.php#<?= htmlspecialchars($l['id']) ?>" class="lead-card <?= $statusClass ?>" style="padding:12px 14px; text-decoration:none;">
        <div class="lead-head" style="margin-bottom:4px;">
          <div class="lead-meta">
            <strong class="lead-name" style="font-size:14.5px;"><?= htmlspecialchars($l['name']) ?></strong>
            <span class="lead-status status-<?= $status ?>"><?= ucfirst($status) ?></span>
          </div>
          <span class="lead-date"><?= htmlspecialchars($l['created']) ?></span>
        </div>
        <div class="lead-row" style="font-size:12.5px; margin:0;">
          <span>📞 <?= htmlspecialchars($l['phone']) ?></span>
          <span>📍 <?= htmlspecialchars($l['postcode']) ?></span>
          <span>🏠 <?= htmlspecialchars($l['type']) ?></span>
          <?php if (($l['sale_value'] ?? 0) > 0): ?>
            <span style="color:var(--success); font-weight:600;">💰 <?= fmt_eur($l['sale_value']) ?></span>
          <?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ============ QUICK EDIT LINKS (compact) ============ -->
<div class="card">
  <div class="card-head">
    <h2>Snel naar...</h2>
    <a href="<?= htmlspecialchars($BASE) ?>/admin/edit.php" class="btn btn-ghost btn-sm">Alle content →</a>
  </div>
  <div class="quick-grid">
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/edit.php#tab-site">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9 H20 M9 4 V20"/></svg>
      Bedrijfsgegevens
    </a>
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/edit.php#tab-prices">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1 V23 M17 5 H9.5 C8 5 7 6 7 7.5 C7 9 8 10 9.5 10 H14.5 C16 10 17 11 17 12.5 C17 14 16 15 14.5 15 H6"/></svg>
      Prijzen
    </a>
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/edit.php#tab-testimonials">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 L14.6 8.5 L21.5 9 L16.2 13.5 L18 20.3 L12 16.7 L6 20.3 L7.8 13.5 L2.5 9 L9.4 8.5 Z"/></svg>
      Reviews
    </a>
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/edit.php#tab-faq">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.5 9 C10 7.5 11 7 12 7 C13.5 7 14.5 8 14.5 9.5 C14.5 11 13 11.5 12 12.5 V14"/></svg>
      FAQ
    </a>
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/photos.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Foto's
    </a>
    <a class="quick-link" href="<?= htmlspecialchars($BASE) ?>/admin/edit.php#tab-other">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21 L16 16"/></svg>
      SEO
    </a>
  </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
