<?php
/**
 * SpinGuard Admin — JSON endpoint: AJAX foto-upload.
 * Gebruikt door de image-picker modal in edit.php.
 */
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!admin_is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Niet ingelogd']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!csrf_check($_POST['_csrf'] ?? '')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Sessie verlopen, ververs de pagina.']);
    exit;
}

if (empty($_FILES['photo'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Geen bestand ontvangen']);
    exit;
}

$f = $_FILES['photo'];
if ($f['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'Upload-fout (code ' . $f['error'] . ')']);
    exit;
}
if ($f['size'] > 10 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'error' => 'Te groot (max 10 MB)']);
    exit;
}

$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','webp','gif'];
$finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;
$mime = $finfo ? finfo_file($finfo, $f['tmp_name']) : '';
$allowed_mime = ['image/jpeg','image/png','image/webp','image/gif'];

if (!in_array($ext, $allowed) || ($mime && !in_array($mime, $allowed_mime))) {
    echo json_encode(['ok' => false, 'error' => 'Alleen JPG/PNG/WebP/GIF']);
    exit;
}

$uploads_dir = __DIR__ . '/../uploads';
if (!is_dir($uploads_dir)) @mkdir($uploads_dir, 0755, true);

$safe = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($f['name'], PATHINFO_FILENAME));
$safe = trim($safe, '-') ?: 'foto';
$name = $safe . '-' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
$dest = $uploads_dir . '/' . $name;

if (!move_uploaded_file($f['tmp_name'], $dest)) {
    echo json_encode(['ok' => false, 'error' => 'Kon bestand niet opslaan (schrijfrechten op /uploads?)']);
    exit;
}
@chmod($dest, 0644);

// Auto-resize naar max 1920px (snellere site)
define('SPINGUARD_INC', true);
require_once __DIR__ . '/../inc/image_resize.php';
resize_uploaded_image($dest, 1920, 85);

$BASE = admin_base_url();
echo json_encode([
    'ok' => true,
    'name' => $name,
    'path' => '/uploads/' . rawurlencode($name),
    'url'  => $BASE . '/uploads/' . rawurlencode($name),
    'size' => filesize($dest),
]);
