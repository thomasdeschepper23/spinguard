<?php
/**
 * SpinGuard Admin — JSON endpoint: lijst alle geüploade foto's.
 * Gebruikt door de image-picker modal.
 */
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!admin_is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$BASE = admin_base_url();
$uploads_dir = __DIR__ . '/../uploads';
$photos = [];

if (is_dir($uploads_dir)) {
    $files = array_filter(scandir($uploads_dir), function($f) {
        return preg_match('/\.(jpe?g|png|webp|gif)$/i', $f);
    });
    // Sorteer op modificatie-tijd, nieuwste eerst
    $files_with_time = [];
    foreach ($files as $f) {
        $files_with_time[] = ['name' => $f, 'mtime' => filemtime($uploads_dir . '/' . $f)];
    }
    usort($files_with_time, fn($a, $b) => $b['mtime'] - $a['mtime']);

    foreach ($files_with_time as $f) {
        $photos[] = [
            'name' => $f['name'],
            'path' => '/uploads/' . rawurlencode($f['name']),
            'url'  => $BASE . '/uploads/' . rawurlencode($f['name']),
            'size' => filesize($uploads_dir . '/' . $f['name']),
            'mtime' => $f['mtime'],
        ];
    }
}

echo json_encode(['ok' => true, 'photos' => $photos]);
