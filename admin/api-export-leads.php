<?php
/**
 * SpinGuard Admin — CSV export van alle leads
 */
require __DIR__ . '/config.php';
admin_require_login();
define('SPINGUARD_INC', true);
require __DIR__ . '/../inc/leads_store.php';

$leads = leads_load();
$status = $_GET['status'] ?? '';
if ($status) {
    $leads = array_values(array_filter($leads, fn($l) => ($l['status'] ?? '') === $status));
}

log_activity('export_csv', "CSV export van " . count($leads) . " leads");

$filename = 'spinguard-leads-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM voor Excel
fputcsv($out, ['ID','Datum','Status','Naam','Telefoon','E-mail','Postcode','Type pand','Bericht','UTM bron','UTM medium','UTM campagne','IP','Notitie']);

foreach ($leads as $l) {
    fputcsv($out, [
        $l['id'] ?? '',
        $l['created'] ?? '',
        $l['status'] ?? 'nieuw',
        $l['name'] ?? '',
        $l['phone'] ?? '',
        $l['email'] ?? '',
        $l['postcode'] ?? '',
        $l['type'] ?? '',
        $l['message'] ?? '',
        $l['utm_source'] ?? '',
        $l['utm_medium'] ?? '',
        $l['utm_campaign'] ?? '',
        $l['ip'] ?? '',
        $l['note'] ?? '',
    ]);
}
fclose($out);
