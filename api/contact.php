<?php
/**
 * SpinGuard — Contact API
 *
 * Werking:
 *  - Honeypot 'website' tegen spam (moet leeg)
 *  - Validatie: naam + telefoon + postcode verplicht
 *  - mail() PHP functie naar adres uit content/site.json -> site.email
 *  - Optioneel: extra recipients, webhook, auto-reply naar klant
 *  - Lead opgeslagen in content/leads/leads.json
 *  - Backup-log in content/leads/[jaar-maand].txt
 */

define('SPINGUARD_INC', true);
require __DIR__ . '/../inc/bootstrap.php';
require __DIR__ . '/../inc/leads_store.php';

$_redirect_base = $BASE_URL;
if (substr($_redirect_base, -4) === '/api') {
    $_redirect_base = substr($_redirect_base, 0, -4);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $_redirect_base . '/contact.php?error=method');
    exit;
}

$site = $CONTENT['site'] ?? [];
$lead_settings = $CONTENT['lead_settings'] ?? [];
$to_email = $site['email'] ?? 'info@spinguard.nl';
$brand    = $site['brand'] ?? 'SpinGuard';

// Honeypot
if (!empty($_POST['website'])) {
    header('Location: ' . $_redirect_base . '/contact.php?error=spam');
    exit;
}

function clean($v) {
    if (!is_string($v)) return '';
    return trim(preg_replace('/[\r\n]+/', ' ', $v));
}
$name      = clean($_POST['name']     ?? '');
$phone     = clean($_POST['phone']    ?? '');
$email     = clean($_POST['email']    ?? '');
$postcode  = clean($_POST['postcode'] ?? '');
$type      = clean($_POST['type']     ?? '');
$message   = trim((string)($_POST['message'] ?? ''));

// Validatie
$errors = [];
if ($name === '') $errors[] = 'naam';
if ($phone === '' || !preg_match('/^[\d\s+\-()]{8,}$/', $phone)) $errors[] = 'telefoon';
if ($postcode === '') $errors[] = 'postcode';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';
if (mb_strlen($name) > 200 || mb_strlen($message) > 5000) $errors[] = 'lengte';

if (!empty($errors)) {
    header('Location: ' . $_redirect_base . '/contact.php?error=validation');
    exit;
}

// Type-pand naam
$type_name = $type;
foreach (($CONTENT['prices'] ?? []) as $p) {
    if (($p['id'] ?? '') === $type) { $type_name = $p['name']; break; }
}

// === Foto's verwerken (optioneel, max 5 stuks, max 8 MB per stuk) ===
$photos = []; // array van ['name','path','url','size','mime','tmp_for_mail']
$max_photos = 5;
$max_photo_bytes = 8 * 1024 * 1024;
$allowed_ext  = ['jpg','jpeg','png','webp','heic','heif'];
$allowed_mime = ['image/jpeg','image/png','image/webp','image/heic','image/heif'];

if (!empty($_FILES['photos']) && is_array($_FILES['photos']['name'] ?? null)) {
    $files = $_FILES['photos'];
    $count = min(count($files['name']), $max_photos);

    // Lead-id vooraf reserveren zodat we een eigen map kunnen aanmaken
    $lead_id = bin2hex(random_bytes(8));
    $upload_root = __DIR__ . '/../uploads/leads';
    $lead_dir    = $upload_root . '/' . $lead_id;
    if (!is_dir($lead_dir)) @mkdir($lead_dir, 0755, true);

    $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;

    for ($i = 0; $i < $count; $i++) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
        if (($files['size'][$i] ?? 0) <= 0 || $files['size'][$i] > $max_photo_bytes) continue;

        $orig_name = (string)($files['name'][$i] ?? '');
        $tmp       = (string)($files['tmp_name'][$i] ?? '');
        if (!is_uploaded_file($tmp)) continue;

        $ext  = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        $mime = $finfo ? finfo_file($finfo, $tmp) : '';
        if (!in_array($ext, $allowed_ext, true)) continue;
        if ($mime && !in_array($mime, $allowed_mime, true)) continue;

        $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($orig_name, PATHINFO_FILENAME));
        $safe = trim($safe, '-') ?: 'foto';
        $photo_filename = $safe . '-' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
        $dest = $lead_dir . '/' . $photo_filename;

        if (!move_uploaded_file($tmp, $dest)) continue;
        @chmod($dest, 0644);

        // Optionele resize naar max 1920px (alleen als GD beschikbaar is)
        $resize_inc = __DIR__ . '/../inc/image_resize.php';
        if (is_file($resize_inc)) {
            require_once $resize_inc;
            if (function_exists('resize_uploaded_image')) {
                @resize_uploaded_image($dest, 1920, 85);
            }
        }

        $rel  = 'uploads/leads/' . $lead_id . '/' . rawurlencode($photo_filename);
        $photos[] = [
            'name' => $photo_filename,
            'path' => '/' . $rel,
            'url'  => $rel, // wordt later voorzien van site_url
            'size' => filesize($dest) ?: $files['size'][$i],
            'mime' => $mime ?: ('image/' . ($ext === 'jpg' ? 'jpeg' : $ext)),
            'file' => $dest, // voor mail-attachment
        ];
    }
    if ($finfo) finfo_close($finfo);

    // Map opruimen als geen geldige foto's overbleven
    if (empty($photos) && is_dir($lead_dir)) {
        @rmdir($lead_dir);
        unset($lead_id);
    }
}

// === UTM-params (uit referrer of cookie) ===
$utm_source = clean($_POST['utm_source'] ?? $_COOKIE['utm_source'] ?? '');
$utm_medium = clean($_POST['utm_medium'] ?? $_COOKIE['utm_medium'] ?? '');
$utm_campaign = clean($_POST['utm_campaign'] ?? $_COOKIE['utm_campaign'] ?? '');

// === Lead opslaan in JSON store ===
// Sla per foto alleen public-velden op (geen tmp-paden in JSON)
$photos_for_lead = array_map(function ($p) {
    return [
        'name' => $p['name'],
        'path' => $p['path'],
        'size' => $p['size'],
        'mime' => $p['mime'],
    ];
}, $photos);

$lead_payload = [
    'name'         => $name,
    'phone'        => $phone,
    'email'        => $email,
    'postcode'     => $postcode,
    'type'         => $type_name,
    'message'      => $message,
    'ip'           => $_SERVER['REMOTE_ADDR'] ?? '',
    'utm_source'   => $utm_source,
    'utm_medium'   => $utm_medium,
    'utm_campaign' => $utm_campaign,
    'photos'       => $photos_for_lead,
];
if (!empty($lead_id)) $lead_payload['id'] = $lead_id; // gebruik gereserveerd id
$lead = lead_create($lead_payload);

// === Mail naar bedrijf opbouwen ===
$subject = '[' . $brand . '] Nieuwe offerte-aanvraag — ' . $name;
$body  = "Nieuwe aanvraag via " . ($site['domain'] ?? 'spinguard.nl') . "\n";
$body .= "------------------------------------------\n\n";
$body .= "Naam      : $name\n";
$body .= "Telefoon  : $phone\n";
if ($email !== '')    $body .= "E-mail    : $email\n";
$body .= "Postcode  : $postcode\n";
$body .= "Type pand : $type_name\n";
if ($message !== '') $body .= "\nToelichting:\n$message\n";
if (!empty($photos)) {
    $site_host = 'https://' . ($site['domain'] ?? 'spinguard.nl');
    $body .= "\nMeegestuurde foto's (" . count($photos) . "):\n";
    foreach ($photos as $ph) {
        $body .= "  - " . $site_host . $ph['path'] . "\n";
    }
}
if ($utm_source) $body .= "\nUTM-bron: $utm_source / $utm_medium / $utm_campaign\n";
$body .= "\n------------------------------------------\n";
$body .= "Verstuurd op " . date('d-m-Y H:i') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '?') . "\n";
$body .= "Bekijk leads: https://" . ($site['domain'] ?? 'spinguard.nl') . "/admin/leads.php\n";

// From-domain hardcoded uit site-config (geen Host-header injectie)
$from_domain = preg_replace('/[^a-z0-9.\-]/i', '', $site['domain'] ?? 'spinguard.nl');
$from_email = 'no-reply@' . ($from_domain ?: 'spinguard.nl');
// Sanitize naam voor header (strip CR/LF + quotes/komma's die headers kunnen breken)
$reply_name = preg_replace('/[\r\n",;<>]/', '', $name);
$reply_name = trim(mb_substr($reply_name, 0, 80));
$reply_to = ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
    ? ($reply_name !== '' ? "$reply_name <$email>" : $email)
    : ($brand . ' <' . $to_email . '>');
$headers = [
    'From: ' . $brand . ' <' . $from_email . '>',
    'Reply-To: ' . $reply_to,
    'X-Mailer: PHP/' . phpversion(),
];

// Bouw body + content-type op (multipart wanneer foto's bijgesloten zijn)
if (!empty($photos)) {
    $boundary = 'sg_' . bin2hex(random_bytes(12));
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

    $eol = "\r\n";
    $mime_body  = 'Dit bericht is opgemaakt in MIME-formaat.' . $eol . $eol;
    $mime_body .= '--' . $boundary . $eol;
    $mime_body .= 'Content-Type: text/plain; charset=UTF-8' . $eol;
    $mime_body .= 'Content-Transfer-Encoding: 8bit' . $eol . $eol;
    $mime_body .= $body . $eol;

    foreach ($photos as $ph) {
        if (!is_file($ph['file'])) continue;
        $content = @file_get_contents($ph['file']);
        if ($content === false) continue;
        $mime_body .= '--' . $boundary . $eol;
        $mime_body .= 'Content-Type: ' . $ph['mime'] . '; name="' . $ph['name'] . '"' . $eol;
        $mime_body .= 'Content-Transfer-Encoding: base64' . $eol;
        $mime_body .= 'Content-Disposition: attachment; filename="' . $ph['name'] . '"' . $eol . $eol;
        $mime_body .= chunk_split(base64_encode($content)) . $eol;
    }
    $mime_body .= '--' . $boundary . '--' . $eol;
    $mail_body = $mime_body;
} else {
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $mail_body = $body;
}

// Recipients (primair + extra's)
$all_recipients = [$to_email];
if (!empty($lead_settings['extra_recipients'])) {
    $extras = array_filter(array_map('trim', explode(',', $lead_settings['extra_recipients'])));
    foreach ($extras as $e) {
        if (filter_var($e, FILTER_VALIDATE_EMAIL)) $all_recipients[] = $e;
    }
}
$all_recipients = array_unique($all_recipients);

// Verzenden naar alle recipients
$mail_ok = false;
foreach ($all_recipients as $rcpt) {
    $sent = @mail($rcpt, '=?UTF-8?B?' . base64_encode($subject) . '?=', $mail_body, implode("\r\n", $headers));
    if ($sent) $mail_ok = true;
}

// === Auto-reply naar klant ===
if ($mail_ok && $email !== '' && !empty($lead_settings['auto_reply_enabled'])) {
    $ar_subject = $lead_settings['auto_reply_subject'] ?? "Bedankt voor uw aanvraag";
    $ar_message = $lead_settings['auto_reply_message'] ?? "Beste {naam}, bedankt voor uw aanvraag.";
    $ar_message = str_replace(
        ['{naam}', '{telefoon}', '{postcode}', '{type}'],
        [$name, $phone, $postcode, $type_name],
        $ar_message
    );
    $ar_headers = [
        'From: ' . $brand . ' <' . $from_email . '>',
        'Reply-To: ' . $brand . ' <' . $to_email . '>',
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/plain; charset=UTF-8',
    ];
    @mail($email, '=?UTF-8?B?' . base64_encode($ar_subject) . '?=', $ar_message, implode("\r\n", $ar_headers));
}

// === Webhook (Slack/Discord/Make.com/etc.) — SSRF-hardened ===
$webhook_url = $lead_settings['webhook_url'] ?? '';
if ($webhook_url && filter_var($webhook_url, FILTER_VALIDATE_URL)) {
    $parts = parse_url($webhook_url);
    $scheme = strtolower($parts['scheme'] ?? '');
    $host   = strtolower($parts['host'] ?? '');
    $allowed = ($scheme === 'https' || $scheme === 'http');
    // Blokkeer interne / private / metadata IPs
    if ($allowed && $host) {
        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : @gethostbyname($host);
        if ($ip && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            $allowed = false; // private (10.x, 192.168.x, 169.254.x, ::1, etc.)
        }
    }
    if ($allowed) {
        $payload = [
            'text' => "🕷️ Nieuwe SpinGuard aanvraag\n*$name* · $phone · $postcode\nType: $type_name\n" . ($message ? "Bericht: $message" : ''),
            'lead' => $lead,
        ];
        $opts = [
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\n",
                'content'       => json_encode($payload),
                'timeout'       => 5,
                'ignore_errors' => true,
                'follow_location' => 0,
            ]
        ];
        @file_get_contents($webhook_url, false, stream_context_create($opts));
    }
}

// Backup-log per maand (legacy + extra zekerheid)
$log_dir = __DIR__ . '/../content/leads';
$log_file = $log_dir . '/' . date('Y-m') . '.txt';
$log_entry = "[" . date('Y-m-d H:i:s') . "] " . ($mail_ok ? 'OK ' : 'FAIL ') . "$name | $phone | $email | $postcode | $type_name\n" .
             "  Bericht: " . str_replace("\n", " / ", $message) . "\n\n";
@file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Redirect (lead is sowieso opgeslagen, dus 'sent' ook bij mail-fail — gebruiker krijgt bevestiging)
header('Location: ' . $_redirect_base . '/contact.php?sent=1');
exit;
