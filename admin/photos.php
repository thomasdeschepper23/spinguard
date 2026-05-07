<?php
require __DIR__ . '/config.php';
admin_require_login();

$uploads_dir = __DIR__ . '/../uploads';
if (!is_dir($uploads_dir)) @mkdir($uploads_dir, 0755, true);

$msg = '';
$err = '';

// === Upload ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['photo'])) {
    if (!csrf_check($_POST['_csrf'] ?? '')) {
        $err = 'Sessie verlopen, probeer opnieuw.';
    } else {
        $f = $_FILES['photo'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $err = 'Upload-fout (code ' . $f['error'] . ').';
        } elseif ($f['size'] > 10 * 1024 * 1024) {
            $err = 'Bestand te groot (max 10 MB).';
        } else {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;
            $mime = $finfo ? finfo_file($finfo, $f['tmp_name']) : '';
            $allowed_mime = ['image/jpeg','image/png','image/webp','image/gif'];
            if (!in_array($ext, $allowed) || ($mime && !in_array($mime, $allowed_mime))) {
                $err = 'Alleen afbeeldingen toegestaan (JPG, PNG, WebP, GIF).';
            } else {
                $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($f['name'], PATHINFO_FILENAME));
                $safe = trim($safe, '-') ?: 'foto';
                $name = $safe . '-' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
                $dest = $uploads_dir . '/' . $name;
                if (move_uploaded_file($f['tmp_name'], $dest)) {
                    @chmod($dest, 0644);
                    // Auto-resize naar max 1920px
                    define('SPINGUARD_INC', true);
                    require_once __DIR__ . '/../inc/image_resize.php';
                    resize_uploaded_image($dest, 1920, 85);
                    $msg = 'Foto ' . $name . ' geüpload (geoptimaliseerd).';
                } else {
                    $err = 'Kon bestand niet opslaan. Controleer schrijfrechten op /uploads.';
                }
            }
        }
    }
}

// === Verwijderen ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete'])) {
    if (csrf_check($_POST['_csrf'] ?? '')) {
        $f = basename($_POST['delete']);
        $path = $uploads_dir . '/' . $f;
        if (is_file($path) && @unlink($path)) {
            $msg = 'Foto verwijderd.';
        } else {
            $err = 'Kon niet verwijderen.';
        }
    }
}

$photos = is_dir($uploads_dir) ? array_values(array_filter(scandir($uploads_dir), function($f) {
    return preg_match('/\.(jpe?g|png|webp|gif)$/i', $f);
})) : [];
rsort($photos);

$tok = csrf_token();
include __DIR__ . '/_layout_top.php';
?>
<div class="main-head">
  <div>
    <h1>Foto's beheren</h1>
    <p>Upload afbeeldingen voor de website. Kopieer het pad om in een tekstveld te plakken.</p>
  </div>
  <div class="head-actions">
    <a href="<?= htmlspecialchars($BASE) ?>/" target="_blank" class="btn btn-ghost">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 4 H20 V10 M10 14 L20 4 M19 14 V19 C19 20 18 21 17 21 H5 C4 21 3 20 3 19 V7 C3 6 4 5 5 5 H10"/></svg>
      Bekijk website
    </a>
  </div>
</div>

<?php if ($msg): ?>
  <div class="notice success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12 l3 3 5-6"/></svg>
    <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>
<?php if ($err): ?>
  <div class="notice error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8 V13 M12 16 v.5"/></svg>
    <?= htmlspecialchars($err) ?>
  </div>
<?php endif; ?>

<div class="card">
  <h2>Upload nieuwe foto</h2>
  <p class="muted">Sleep een foto in het vak hieronder, of klik om te selecteren.</p>

  <form method="post" enctype="multipart/form-data" id="uploadForm">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
    <label class="upload-area" id="uploadArea">
      <input type="file" name="photo" id="fileInput" accept="image/*" />
      <div class="upload-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
      </div>
      <p class="upload-title">Sleep een foto hierin</p>
      <p class="upload-sub">Of <strong>klik om te kiezen</strong> · JPG, PNG, WebP of GIF · max 10 MB</p>
    </label>
  </form>
</div>

<div class="card">
  <div class="flex between mb-md">
    <h2 class="mb-0">Geüpload <span class="text-muted text-sm" style="font-weight:400;">(<?= count($photos) ?>)</span></h2>
    <?php if (!empty($photos)): ?>
      <span class="text-muted text-sm">💡 Klik "Kopieer pad" en plak in een veld zoals Before/After-foto</span>
    <?php endif; ?>
  </div>

  <?php if (empty($photos)): ?>
    <div class="empty">
      <div class="empty-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M3 17 L9 13 L15 17 L21 13"/></svg>
      </div>
      <h3>Nog geen foto's</h3>
      <p>Upload je eerste foto via het vak hierboven.</p>
    </div>
  <?php else: ?>
    <div class="photo-grid">
      <?php foreach ($photos as $p): ?>
        <?php $url = $BASE . '/uploads/' . rawurlencode($p); $copyUrl = '/uploads/' . rawurlencode($p); ?>
        <div class="photo-card">
          <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="thumb" style="background-image:url('<?= htmlspecialchars($url) ?>');"></a>
          <div class="meta">
            <div class="filename" title="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></div>
            <div class="actions">
              <button type="button" class="copy-btn" data-copy="<?= htmlspecialchars($copyUrl) ?>">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15 H4 C2.9 15 2 14.1 2 13 V4 C2 2.9 2.9 2 4 2 H13 C14.1 2 15 2.9 15 4 V5"/></svg>
                Kopieer pad
              </button>
              <form method="post" style="display:inline;" onsubmit="return confirm('Foto verwijderen?');">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($tok) ?>" />
                <input type="hidden" name="delete" value="<?= htmlspecialchars($p) ?>" />
                <button type="submit" class="delete-btn" title="Verwijderen">×</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
