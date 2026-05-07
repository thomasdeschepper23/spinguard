<?php
/**
 * SpinGuard Admin — backup download
 * Maakt een ZIP van content/ + uploads/ en stuurt naar de browser.
 */
require __DIR__ . '/config.php';
admin_require_login();

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    echo "ZipArchive extensie niet beschikbaar op deze hosting. Vraag je hostingprovider om PHP zip extensie te activeren.";
    exit;
}

$tmp = tempnam(sys_get_temp_dir(), 'sgbu_') . '.zip';
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    echo "Kon ZIP niet maken.";
    exit;
}

function add_dir(ZipArchive $zip, $dir, $base) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        $rel  = $base . '/' . $item;
        if (is_dir($path)) {
            add_dir($zip, $path, $rel);
        } else {
            $zip->addFile($path, $rel);
        }
    }
}

add_dir($zip, __DIR__ . '/../content', 'content');
add_dir($zip, __DIR__ . '/../uploads', 'uploads');
$zip->close();

log_activity('backup', "Backup gedownload");

$filename = 'spinguard-backup-' . date('Y-m-d_His') . '.zip';
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmp));
readfile($tmp);
@unlink($tmp);
