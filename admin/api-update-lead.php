<?php
/**
 * SpinGuard Admin — JSON endpoint: update lead (status / sale / notitie / verwijderen)
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

$id = trim($_POST['id'] ?? '');
$action = trim($_POST['action'] ?? '');

if ($id === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Geen ID']);
    exit;
}

if ($action === 'delete') {
    $ok = lead_delete($id);
    log_activity('lead_delete', "Lead $id verwijderd");
    echo json_encode(['ok' => $ok]);
    exit;
}

$changes = [];

// Status (uitgebreide lijst met gewonnen/verloren)
if (isset($_POST['status'])) {
    $status = trim($_POST['status']);
    if (in_array($status, LEAD_STATUSES, true)) {
        $changes['status'] = $status;
    }
}

// Sale value (€) — geaccepteerd als getal/decimaal
if (isset($_POST['sale_value'])) {
    $sv = str_replace(',', '.', trim((string)$_POST['sale_value']));
    if ($sv === '' || !is_numeric($sv)) {
        $changes['sale_value'] = 0;
    } else {
        $changes['sale_value'] = max(0, round((float)$sv, 2));
    }
}

// Reden bij verloren
if (isset($_POST['lost_reason'])) {
    $changes['lost_reason'] = mb_substr(trim((string)$_POST['lost_reason']), 0, 500);
}

// Follow-up datum (YYYY-MM-DD of leeg)
if (isset($_POST['follow_up_date'])) {
    $f = trim((string)$_POST['follow_up_date']);
    if ($f === '' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $f)) {
        $changes['follow_up_date'] = $f;
    }
}

// Tags (array of komma-gescheiden string)
if (isset($_POST['tags'])) {
    $raw = $_POST['tags'];
    if (is_string($raw)) $raw = array_map('trim', explode(',', $raw));
    if (is_array($raw)) {
        $tags = [];
        foreach ($raw as $t) {
            $t = trim((string)$t);
            if ($t === '') continue;
            $tags[] = mb_substr($t, 0, 40);
            if (count($tags) >= 8) break;
        }
        $changes['tags'] = $tags;
    }
}

// Notitie
if (isset($_POST['note'])) {
    $changes['note'] = mb_substr(trim((string)$_POST['note']), 0, 2000);
}

if (empty($changes)) {
    echo json_encode(['ok' => false, 'error' => 'Geen wijzigingen']);
    exit;
}

$lead = lead_update($id, $changes);
log_activity('lead_update', "Lead $id bijgewerkt: " . json_encode($changes));
echo json_encode(['ok' => true, 'lead' => $lead]);
