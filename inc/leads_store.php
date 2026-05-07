<?php
/**
 * SpinGuard — Lead store
 * Beheert leads in een eenvoudig JSON-bestand met conversion-tracking.
 *
 * Statuses:
 *   nieuw           → net binnen, nog geen contact
 *   gecontacteerd   → contact gehad, nog geen beslissing
 *   gewonnen        → klant geworden (sale!) — sale_value vastleggen
 *   verloren        → ging niet door — lost_reason vastleggen
 *   gearchiveerd    → handmatig opgeruimd
 */
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

const LEAD_STATUSES = ['nieuw', 'gecontacteerd', 'gewonnen', 'verloren', 'gearchiveerd'];

function leads_file() {
    $dir = __DIR__ . '/../content/leads';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir . '/leads.json';
}

function leads_load() {
    $f = leads_file();
    if (!is_file($f)) return [];
    $j = json_decode(@file_get_contents($f), true);
    return is_array($j) ? $j : [];
}

function leads_save($leads) {
    $f = leads_file();
    return @file_put_contents($f, json_encode($leads, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

/**
 * Backwards-compatibele defaults voor lead-velden.
 * Bestaande leads zonder de nieuwe velden krijgen leeg/0 bij weergave.
 */
function lead_defaults() {
    return [
        'id'             => '',
        'created'        => date('Y-m-d H:i:s'),
        'updated'        => '',
        'status'         => 'nieuw',
        'name'           => '',
        'phone'          => '',
        'email'          => '',
        'postcode'       => '',
        'type'           => '',
        'message'        => '',
        'ip'             => '',
        'note'           => '',
        'utm_source'     => '',
        'utm_medium'     => '',
        'utm_campaign'   => '',
        // Nieuw: conversion tracking
        'sale_value'     => 0,         // € deal-waarde wanneer gewonnen
        'lost_reason'    => '',        // reden bij verloren
        'follow_up_date' => '',        // YYYY-MM-DD voor terugbel-afspraak
        'tags'           => [],        // array van labels
        'photos'         => [],        // array van ['name','path','size','mime']
    ];
}

function lead_normalize($lead) {
    return array_merge(lead_defaults(), is_array($lead) ? $lead : []);
}

function lead_create($data) {
    $leads = leads_load();
    $id = bin2hex(random_bytes(8));
    $lead = array_merge(lead_defaults(), [
        'id'      => $id,
        'created' => date('Y-m-d H:i:s'),
        'status'  => 'nieuw',
    ], is_array($data) ? $data : []);
    array_unshift($leads, $lead);
    if (count($leads) > 500) $leads = array_slice($leads, 0, 500);
    leads_save($leads);
    return $lead;
}

function lead_update($id, $changes) {
    $leads = leads_load();
    foreach ($leads as &$l) {
        if (($l['id'] ?? '') === $id) {
            foreach ($changes as $k => $v) {
                $l[$k] = $v;
            }
            $l['updated'] = date('Y-m-d H:i:s');
            leads_save($leads);
            return $l;
        }
    }
    return null;
}

function lead_delete($id) {
    $leads = leads_load();
    $leads = array_values(array_filter($leads, fn($l) => ($l['id'] ?? '') !== $id));
    return leads_save($leads);
}

function leads_filter($status = null) {
    $leads = leads_load();
    if ($status === null) return $leads;
    return array_values(array_filter($leads, fn($l) => ($l['status'] ?? 'nieuw') === $status));
}

/**
 * Telt per status + totaal + conversion + omzet.
 * Optionele $since_ts beperkt tot leads created >= $since_ts (UNIX).
 */
function leads_stats($since_ts = null) {
    $leads = leads_load();
    $stats = [
        'nieuw' => 0, 'gecontacteerd' => 0, 'gewonnen' => 0,
        'verloren' => 0, 'gearchiveerd' => 0, 'totaal' => 0,
        'revenue' => 0.0, 'avg_deal' => 0.0, 'conversion_rate' => 0.0,
    ];
    $won_count = 0;
    foreach ($leads as $l) {
        if ($since_ts !== null) {
            $ts = strtotime($l['created'] ?? '');
            if (!$ts || $ts < $since_ts) continue;
        }
        $stats['totaal']++;
        $s = $l['status'] ?? 'nieuw';
        if (isset($stats[$s])) $stats[$s]++;
        if ($s === 'gewonnen') {
            $stats['revenue'] += (float)($l['sale_value'] ?? 0);
            $won_count++;
        }
    }
    if ($won_count > 0) $stats['avg_deal'] = $stats['revenue'] / $won_count;
    $closed = $stats['gewonnen'] + $stats['verloren'];
    if ($closed > 0) $stats['conversion_rate'] = ($stats['gewonnen'] / $closed) * 100;
    return $stats;
}

/**
 * Aantal leads per dag voor de laatste $days dagen (default 30).
 * Returns ['YYYY-MM-DD' => count, ...] in chronologische volgorde.
 */
function leads_per_day($days = 30) {
    $leads = leads_load();
    $out = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $out[date('Y-m-d', strtotime("-$i days"))] = 0;
    }
    foreach ($leads as $l) {
        $d = substr($l['created'] ?? '', 0, 10);
        if (isset($out[$d])) $out[$d]++;
    }
    return $out;
}

/**
 * Top values voor een gegeven veld. Returns ['waarde' => count] gesorteerd descending.
 */
function leads_top_field($field, $limit = 5, $since_ts = null) {
    $leads = leads_load();
    $counts = [];
    foreach ($leads as $l) {
        if ($since_ts !== null) {
            $ts = strtotime($l['created'] ?? '');
            if (!$ts || $ts < $since_ts) continue;
        }
        $v = trim((string)($l[$field] ?? ''));
        if ($v === '') continue;
        $counts[$v] = ($counts[$v] ?? 0) + 1;
    }
    arsort($counts);
    return array_slice($counts, 0, $limit, true);
}

/**
 * Hot leads: status nieuw, ouder dan $hours uur. Sorteert oudst-eerst.
 */
function leads_hot($hours = 24) {
    $leads = leads_load();
    $cutoff = time() - ($hours * 3600);
    $hot = [];
    foreach ($leads as $l) {
        if (($l['status'] ?? 'nieuw') !== 'nieuw') continue;
        $ts = strtotime($l['created'] ?? '');
        if (!$ts || $ts > $cutoff) continue;
        $hot[] = lead_normalize($l);
    }
    usort($hot, fn($a, $b) => strtotime($a['created']) <=> strtotime($b['created']));
    return $hot;
}

/**
 * Follow-ups die vandaag (of eerder) staan en niet al gewonnen/verloren zijn.
 */
function leads_followups_due() {
    $leads = leads_load();
    $today = date('Y-m-d');
    $due = [];
    foreach ($leads as $l) {
        $f = trim((string)($l['follow_up_date'] ?? ''));
        if ($f === '' || $f > $today) continue;
        $s = $l['status'] ?? 'nieuw';
        if (in_array($s, ['gewonnen', 'verloren', 'gearchiveerd'], true)) continue;
        $due[] = lead_normalize($l);
    }
    usort($due, fn($a, $b) => strcmp($a['follow_up_date'], $b['follow_up_date']));
    return $due;
}
