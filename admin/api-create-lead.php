<?php
/**
 * SpinGuard Admin — Handmatig lead toevoegen (bv. uit WhatsApp / telefoongesprek)
 */
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!admin_is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Niet ingelogd']);
    exit;
}

if (!csrf_check($_POST['_csrf'] ?? '')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Sessie verlopen']);
    exit;
}

define('SPINGUARD_INC', true);
require __DIR__ . '/../inc/leads_store.php';

// Verplichte velden
$name  = trim((string)($_POST['name']  ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Naam en telefoon zijn verplicht']);
    exit;
}

// Status validatie
$status = trim((string)($_POST['status'] ?? 'nieuw'));
if (!in_array($status, LEAD_STATUSES, true)) $status = 'nieuw';

// Optionele velden
$email          = trim((string)($_POST['email'] ?? ''));
$postcode       = trim((string)($_POST['postcode'] ?? ''));
$type           = trim((string)($_POST['type'] ?? ''));
$message        = trim((string)($_POST['message'] ?? ''));
$source         = trim((string)($_POST['source'] ?? 'handmatig'));  // bv. whatsapp, telefoon, email
$note           = mb_substr(trim((string)($_POST['note'] ?? '')), 0, 2000);
$sale_value     = 0;
if (isset($_POST['sale_value'])) {
    $sv = str_replace(',', '.', trim((string)$_POST['sale_value']));
    if (is_numeric($sv)) $sale_value = max(0, round((float)$sv, 2));
}
$follow_up_date = '';
if (!empty($_POST['follow_up_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['follow_up_date'])) {
    $follow_up_date = $_POST['follow_up_date'];
}
$tags = [];
if (!empty($_POST['tags'])) {
    $raw = is_array($_POST['tags']) ? $_POST['tags'] : explode(',', $_POST['tags']);
    foreach ($raw as $t) {
        $t = trim((string)$t);
        if ($t === '') continue;
        $tags[] = mb_substr($t, 0, 40);
        if (count($tags) >= 8) break;
    }
}

$lead = lead_create([
    'name'           => mb_substr($name, 0, 120),
    'phone'          => mb_substr($phone, 0, 40),
    'email'          => mb_substr($email, 0, 120),
    'postcode'       => mb_substr($postcode, 0, 20),
    'type'           => mb_substr($type, 0, 60),
    'message'        => mb_substr($message, 0, 2000),
    'status'         => $status,
    'note'           => $note,
    'sale_value'     => $sale_value,
    'follow_up_date' => $follow_up_date,
    'tags'           => $tags,
    'utm_source'     => $source,  // gebruik utm_source-kolom om "handmatig/whatsapp/telefoon" bij te houden
    'utm_medium'     => 'manual',
    'utm_campaign'   => '',
    'ip'             => $_SERVER['REMOTE_ADDR'] ?? '',
]);

log_activity('lead_create_manual', "Handmatige lead toegevoegd: {$name} (bron: {$source})");
echo json_encode(['ok' => true, 'lead' => $lead]);
