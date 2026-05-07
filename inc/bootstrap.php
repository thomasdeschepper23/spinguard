<?php
// SpinGuard — gedeelde initialisatie. Wordt door iedere pagina aan het begin geladen.

// Voorkomt directe toegang vanuit URL
if (!defined('SPINGUARD_INC')) define('SPINGUARD_INC', true);

// Pad naar content JSON
$CONTENT_FILE = __DIR__ . '/../content/site.json';

// === BASE_URL: werkt in zowel root als subdirectory (bv. localhost/spinguard) ===
$_script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
$BASE_URL = rtrim(dirname($_script), '/');
if ($BASE_URL === '/' || $BASE_URL === '.' || $BASE_URL === '\\') $BASE_URL = '';

/** Voeg BASE_URL toe aan een absoluut pad */
function b($path) {
    global $BASE_URL;
    if (!is_string($path) || $path === '' || $path[0] !== '/') return $path;
    return $BASE_URL . $path;
}

// Laad content uit JSON. Als bestand kapot is, val terug op leeg object.
function load_content() {
    global $CONTENT_FILE;
    if (!file_exists($CONTENT_FILE)) return [];
    $raw = file_get_contents($CONTENT_FILE);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

$CONTENT = load_content();

// Helpers
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// WhatsApp-link bouwen
function wa_link($custom_text = null) {
    global $CONTENT;
    $num = $CONTENT['site']['whatsapp_number'] ?? '31242340061';
    $msg = $custom_text ?? ($CONTENT['site']['whatsapp_message'] ?? 'Hallo SpinGuard, ik wil graag een vrijblijvende offerte aanvragen.');
    return 'https://wa.me/' . $num . '?text=' . rawurlencode($msg);
}

// Inline SVG icon (verkleind icoon-systeem, zonder externe library)
function icon($name, $size = 22, $color = 'currentColor') {
    $s = (int)$size;
    $c = htmlspecialchars($color, ENT_QUOTES, 'UTF-8');
    $svgs = [
        'Spider'  => '<circle cx="12" cy="13" r="3.2" fill="'.$c.'"/><path d="M12 9.8 V6.5"/><circle cx="11" cy="6" r=".7" fill="'.$c.'"/><circle cx="13" cy="6" r=".7" fill="'.$c.'"/><path d="M9.4 11.5 L5 8.5 M5 8.5 L3 10.5"/><path d="M9.4 13.8 L4.5 14 M4.5 14 L2.5 16.5"/><path d="M14.6 11.5 L19 8.5 M19 8.5 L21 10.5"/><path d="M14.6 13.8 L19.5 14 M19.5 14 L21.5 16.5"/><path d="M10.5 16 L9 19.5 M9 19.5 L6.5 20.5"/><path d="M13.5 16 L15 19.5 M15 19.5 L17.5 20.5"/>',
        'Shield'  => '<path d="M12 3 L4 6 V12 C4 16.5 7.5 19.8 12 21 C16.5 19.8 20 16.5 20 12 V6 Z"/><path d="M9 12 l2 2 4-4"/>',
        'Sparkle' => '<path d="M12 4 L13.5 9 L18 10.5 L13.5 12 L12 17 L10.5 12 L6 10.5 L10.5 9 Z"/><path d="M19 4 L19.5 5.5 L21 6 L19.5 6.5 L19 8 L18.5 6.5 L17 6 L18.5 5.5 Z"/><path d="M5 16 L5.5 17.5 L7 18 L5.5 18.5 L5 20 L4.5 18.5 L3 18 L4.5 17.5 Z"/>',
        'Clock'   => '<circle cx="12" cy="12" r="8"/><path d="M12 7 V12 L15 14"/>',
        'Leaf'    => '<path d="M5 19 C5 11 11 5 19 5 C19 13 13 19 5 19 Z"/><path d="M5 19 L13 11"/>',
        'Home'    => '<path d="M4 11 L12 4 L20 11 V20 H14 V14 H10 V20 H4 Z"/>',
        'Building'=> '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7 H10 M14 7 H15 M9 11 H10 M14 11 H15 M9 15 H10 M14 15 H15"/><path d="M11 21 V18 H13 V21"/>',
        'Houses'  => '<path d="M3 12 L7 8 L11 12 V20 H3 Z"/><path d="M11 12 L15 8 L19 12 M21 12 L17 8"/><path d="M11 20 H21 V12"/>',
        'HouseDouble' => '<path d="M2 12 L7 7 L12 12 V20 H2 Z"/><path d="M12 12 L17 7 L22 12 V20 H12 Z"/>',
        'HouseStand'  => '<path d="M4 11 L12 4 L20 11 V20 H4 Z"/><path d="M9 20 V14 H15 V20"/><path d="M11 14 V20"/>',
        'Camera'  => '<path d="M3 8 H7 L9 5 H15 L17 8 H21 V19 H3 Z"/><circle cx="12" cy="13" r="4"/>',
        'Doc'     => '<path d="M6 3 H14 L18 7 V21 H6 Z"/><path d="M14 3 V7 H18"/><path d="M9 12 H15 M9 16 H15"/>',
        'Spray'   => '<rect x="8" y="9" width="7" height="12" rx="1"/><path d="M9 9 V6 H14 V9"/><path d="M14 4 H18 M14 7 H19 M16 10 H20"/>',
        'Check'   => '<path d="M5 12 L10 17 L19 7"/>',
        'Plus'    => '<path d="M12 5 V19 M5 12 H19"/>',
        'Arrow'   => '<path d="M5 12 H19 M13 6 L19 12 L13 18"/>',
        'Phone'   => '<path d="M5 4 H9 L11 9 L8.5 10.5 C9.5 13 11 14.5 13.5 15.5 L15 13 L20 15 V19 C20 19.5 19.5 20 19 20 C11 20 4 13 4 5 C4 4.5 4.5 4 5 4 Z"/>',
        'Mail'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7 L12 13 L21 7"/>',
        'Pin'     => '<path d="M12 21 C16 16 19 12.5 19 9 C19 5.1 15.9 2 12 2 C8.1 2 5 5.1 5 9 C5 12.5 8 16 12 21 Z"/><circle cx="12" cy="9" r="2.5"/>',
        'Star'    => '<path d="M12 2 L14.6 8.5 L21.5 9 L16.2 13.5 L18 20.3 L12 16.7 L6 20.3 L7.8 13.5 L2.5 9 L9.4 8.5 Z"/>',
        'Menu'    => '<path d="M4 7 H20 M4 12 H20 M4 17 H20"/>',
        'Close'   => '<path d="M6 6 L18 18 M18 6 L6 18"/>',
    ];
    $svg = $svgs[$name] ?? $svgs['Spider'];
    $fill_only = in_array($name, ['Star']);
    if ($fill_only) {
        return '<svg width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24" fill="'.$c.'">'.$svg.'</svg>';
    }
    return '<svg width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24" fill="none" stroke="'.$c.'" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$svg.'</svg>';
}

// Veilige URL render voor admin-gestuurde links (blokkeert javascript: / data: / vbscript:)
function safe_url($url) {
    if (!is_string($url)) return '#';
    $u = trim($url);
    if ($u === '' || $u === '#') return '#';
    // Sta toe: relatief, absolute http(s), mailto, tel, anchor (#) of query (?)
    if (preg_match('~^(https?:|mailto:|tel:|/|#|\?)~i', $u)) return $u;
    // Alles met andere scheme (javascript:, data:, vbscript:, file:) → onschadelijk maken
    if (preg_match('~^[a-z][a-z0-9+.\-]*:~i', $u)) return '#';
    return $u; // relatieve path zonder leading slash, OK
}

// WhatsApp icon (gevuld, andere stijl)
function icon_whatsapp($size = 24, $color = 'currentColor') {
    $s = (int)$size;
    $c = htmlspecialchars($color, ENT_QUOTES, 'UTF-8');
    return '<svg width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24" fill="'.$c.'">
      <path d="M12 2 C6.5 2 2 6.5 2 12 C2 13.8 2.5 15.5 3.4 17 L2 22 L7.2 20.6 C8.6 21.4 10.3 21.8 12 21.8 C17.5 21.8 22 17.4 22 11.9 C22 6.4 17.5 2 12 2 Z M12 20.1 C10.5 20.1 9.1 19.7 7.9 19 L7.6 18.8 L4.5 19.6 L5.3 16.6 L5.1 16.3 C4.4 15.1 4 13.6 4 12 C4 7.6 7.6 4 12 4 C16.4 4 20 7.6 20 12 C20 16.4 16.4 20.1 12 20.1 Z M16.5 14.4 C16.3 14.3 15.1 13.7 14.9 13.6 C14.7 13.5 14.5 13.5 14.3 13.7 C14.1 14 13.7 14.5 13.5 14.7 C13.4 14.9 13.2 14.9 13 14.8 C12.4 14.5 11.5 14.2 10.5 13.3 C9.7 12.6 9.2 11.7 9 11.4 C8.9 11.2 9 11.1 9.1 10.9 C9.2 10.8 9.3 10.6 9.5 10.5 C9.6 10.4 9.6 10.2 9.7 10.1 C9.8 10 9.7 9.8 9.7 9.7 C9.6 9.6 9.2 8.4 9 8 C8.8 7.6 8.7 7.6 8.5 7.6 C8.4 7.6 8.2 7.6 8.1 7.6 C7.9 7.6 7.6 7.7 7.4 7.9 C7.2 8.2 6.6 8.7 6.6 9.9 C6.6 11.1 7.5 12.2 7.6 12.4 C7.7 12.5 9.2 14.9 11.6 15.9 C13.4 16.7 13.8 16.6 14.2 16.5 C14.6 16.5 15.6 15.9 15.8 15.4 C16 14.9 16 14.4 16 14.4 C16 14.3 16 14.3 15.8 14.2 Z"/>
    </svg>';
}

// === Simple markdown renderer (voor legal/custom pages) ===
function simple_markdown($md) {
    if (!is_string($md) || $md === '') return '';
    $html = '';
    $lines = preg_split("/\r?\n/", $md);
    $in_list = false;
    foreach ($lines as $line) {
        $line = rtrim($line);
        if ($line === '') {
            if ($in_list) { $html .= "</ul>\n"; $in_list = false; }
            $html .= "\n";
            continue;
        }
        // Headers
        if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $m)) {
            if ($in_list) { $html .= "</ul>\n"; $in_list = false; }
            $level = strlen($m[1]);
            $html .= "<h$level>" . _md_inline($m[2]) . "</h$level>\n";
            continue;
        }
        // List item
        if (preg_match('/^[-*]\s+(.+)$/', $line, $m)) {
            if (!$in_list) { $html .= "<ul>\n"; $in_list = true; }
            $html .= "<li>" . _md_inline($m[1]) . "</li>\n";
            continue;
        }
        if ($in_list) { $html .= "</ul>\n"; $in_list = false; }
        $html .= "<p>" . _md_inline($line) . "</p>\n";
    }
    if ($in_list) $html .= "</ul>\n";
    return $html;
}
function _md_inline($t) {
    $t = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
    $t = preg_replace('/\*\*([^\*]+)\*\*/', '<strong>$1</strong>', $t);
    $t = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $t);
    $t = preg_replace('/`([^`]+)`/', '<code>$1</code>', $t);
    $t = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $t);
    return $t;
}

// === SEO Schema generators ===
function schema_faq($faq_items) {
    if (empty($faq_items)) return '';
    $entities = [];
    foreach ($faq_items as $item) {
        $entities[] = [
            '@type' => 'Question',
            'name'  => (string)($item['q'] ?? ''),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => (string)($item['a'] ?? ''),
            ],
        ];
    }
    return json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        'mainEntity' => $entities,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function schema_breadcrumbs($items) {
    if (empty($items)) return '';
    $list = [];
    $i = 1;
    foreach ($items as $it) {
        $list[] = [
            '@type'    => 'ListItem',
            'position' => $i++,
            'name'     => (string)$it['name'],
            'item'     => (string)$it['url'],
        ];
    }
    return json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => $list,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function schema_service_catalog($prices, $site, $seo) {
    if (empty($prices)) return '';
    $base_url = rtrim($seo['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
    $services = [];
    foreach ($prices as $p) {
        $offer = [
            '@type' => 'Offer',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
            'url' => $base_url . '/diensten.php#' . ($p['id'] ?? ''),
        ];
        if (!empty($p['price'])) {
            $offer['price'] = (string)$p['price'];
            $offer['priceSpecification'] = [
                '@type' => 'PriceSpecification',
                'price' => (float)$p['price'],
                'priceCurrency' => 'EUR',
                'valueAddedTaxIncluded' => true,
            ];
        }
        $services[] = [
            '@type' => 'Service',
            'name' => 'Spinnenbestrijding ' . ($p['name'] ?? ''),
            'description' => (string)($p['meta'] ?? '') . '. ' . implode('. ', $p['features'] ?? []),
            'provider' => ['@type' => 'PestControlService', 'name' => $site['brand'] ?? 'SpinGuard'],
            'areaServed' => ['@type' => 'Country', 'name' => 'Nederland'],
            'offers' => $offer,
        ];
    }
    return json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'ItemList',
        'itemListElement' => $services,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Sociale media iconen
function icon_social($name, $size = 18) {
    $s = (int)$size;
    $svgs = [
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.7" fill="none"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7" fill="none"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
        'tiktok'    => '<path d="M16 4 C16 6.2 17.8 8 20 8 V11 C18.5 11 17.1 10.5 16 9.7 V15.5 C16 18.5 13.5 21 10.5 21 C7.5 21 5 18.5 5 15.5 C5 12.5 7.5 10 10.5 10 V13 C9.1 13 8 14.1 8 15.5 C8 16.9 9.1 18 10.5 18 C11.9 18 13 16.9 13 15.5 V4 Z" fill="currentColor"/>',
        'facebook'  => '<path d="M14 8 H17 V5 H14 C12.3 5 11 6.3 11 8 V10 H8 V13 H11 V21 H14 V13 H17 L18 10 H14 V8.5 C14 8.2 14.2 8 14.5 8 Z" fill="currentColor"/>',
    ];
    $svg = $svgs[$name] ?? '';
    return '<svg width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24">'.$svg.'</svg>';
}
