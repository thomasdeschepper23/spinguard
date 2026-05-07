<?php
require __DIR__ . '/config.php';
admin_require_login();

$BASE = admin_base_url();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $BASE . '/admin/edit.php');
    exit;
}
if (!csrf_check($_POST['_csrf'] ?? '')) {
    header('Location: ' . $BASE . '/admin/edit.php?err=' . urlencode('Sessie verlopen, probeer opnieuw'));
    exit;
}

$CONTENT_FILE = __DIR__ . '/../content/site.json';
$existing = json_decode(@file_get_contents($CONTENT_FILE) ?: '{}', true) ?: [];

// === Deep-merge bestaande content met POST data ===
$out = $existing;

// Marker: edit.php submits _user_save=1 zodat we weten dat het volledige formulier is verstuurd.
// Lege repeaters (alle items verwijderd) sturen anders niets — dan reageert isset() niet en blijft
// de oude inhoud staan. Met deze marker forceren we leegmaken bij volledige saves.
$is_full_save = !empty($_POST['_user_save']);

// === Site ===
if (isset($_POST['site']) && is_array($_POST['site'])) {
    foreach ($_POST['site'] as $k => $v) {
        if (is_string($v)) $out['site'][$k] = trim($v);
    }
}

// === Hero (incl. trust_items uit textarea) ===
if (isset($_POST['hero']) && is_array($_POST['hero'])) {
    foreach ($_POST['hero'] as $k => $v) {
        if ($k === 'trust_items_text') {
            $out['hero']['trust_items'] = lines_to_array($v);
        } elseif (is_string($v)) {
            $out['hero'][$k] = trim($v);
        }
    }
}

// === Trust strip ===
if ($is_full_save || isset($_POST['trust_strip'])) {
    $out['trust_strip'] = [];
    if (isset($_POST['trust_strip']) && is_array($_POST['trust_strip'])) {
        foreach ($_POST['trust_strip'] as $t) {
            if (!is_array($t)) continue;
            $num = trim($t['num'] ?? '');
            $lab = trim($t['label'] ?? '');
            if ($num !== '' || $lab !== '') $out['trust_strip'][] = ['num' => $num, 'label' => $lab];
        }
    }
}

// === Prices ===
if ($is_full_save || isset($_POST['prices'])) {
    $out['prices'] = [];
    if (isset($_POST['prices']) && is_array($_POST['prices'])) {
        foreach ($_POST['prices'] as $p) {
            if (!is_array($p)) continue;
            $row = [
                'id'          => slug($p['id'] ?? ''),
                'icon'        => trim($p['icon'] ?? 'Building'),
                'name'        => trim($p['name'] ?? ''),
                'meta'        => trim($p['meta'] ?? ''),
                'price'       => $p['price'] !== '' ? (int)$p['price'] : null,
                'price_label' => trim($p['price_label'] ?? '') ?: null,
                'featured'    => !empty($p['featured']) && $p['featured'] !== '0',
                'features'    => lines_to_array($p['features_text'] ?? ''),
            ];
            if ($row['name'] !== '') $out['prices'][] = $row;
        }
    }
}

// === Discount note ===
if (isset($_POST['discount_note'])) {
    $out['discount_note'] = trim($_POST['discount_note']);
}

// === Compare table (DIY vs SpinGuard) ===
if ($is_full_save || isset($_POST['compare'])) {
    $cmp = $_POST['compare'] ?? [];
    $cmp_rows_in = is_array($cmp['rows'] ?? null) ? $cmp['rows'] : [];
    $cmp_rows = [];
    $allowed = ['yes','soms','no'];
    foreach ($cmp_rows_in as $row) {
        if (!is_array($row)) continue;
        $label = trim($row['label'] ?? '');
        if ($label === '') continue;
        $them = trim($row['them'] ?? 'no');
        $us   = trim($row['us']   ?? 'yes');
        if (!in_array($them, $allowed, true)) $them = 'no';
        if (!in_array($us,   $allowed, true)) $us   = 'yes';
        $cmp_rows[] = ['label' => $label, 'them' => $them, 'us' => $us];
    }
    $out['compare'] = [
        'enabled'  => !empty($cmp['enabled']) && $cmp['enabled'] !== '0',
        'eyebrow'  => trim($cmp['eyebrow']  ?? 'Vergelijking'),
        'heading'  => trim($cmp['heading']  ?? 'SpinGuard versus zelf doen.'),
        'intro'    => trim($cmp['intro']    ?? ''),
        'col_them' => trim($cmp['col_them'] ?? 'DIY / supermarkt'),
        'col_us'   => trim($cmp['col_us']   ?? 'SpinGuard'),
        'rows'     => $cmp_rows,
    ];
}

// === Process ===
if ($is_full_save || isset($_POST['process'])) {
    $out['process'] = [];
    if (isset($_POST['process']) && is_array($_POST['process'])) {
        foreach ($_POST['process'] as $s) {
            if (!is_array($s)) continue;
            $row = [
                'icon'    => trim($s['icon'] ?? 'Camera'),
                'title'   => trim($s['title'] ?? ''),
                'short'   => trim($s['short'] ?? ''),
                'long'    => trim($s['long'] ?? ''),
                'details' => lines_to_array($s['details_text'] ?? ''),
            ];
            if ($row['title'] !== '') $out['process'][] = $row;
        }
    }
}

// === Page-section headers (eyebrow / heading / intro per sectie) ===
if ($is_full_save || isset($_POST['sections'])) {
    $secs = $_POST['sections'] ?? [];
    if (is_array($secs)) {
        $out['sections'] = $out['sections'] ?? [];
        foreach ($secs as $key => $vals) {
            if (!is_array($vals)) continue;
            $clean_key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$key));
            if ($clean_key === '') continue;
            $out['sections'][$clean_key] = [
                'eyebrow' => trim($vals['eyebrow'] ?? ''),
                'heading' => trim($vals['heading'] ?? ''),
                'intro'   => trim($vals['intro']   ?? ''),
            ];
        }
    }
}

// === CTA banners ===
if ($is_full_save || isset($_POST['ctas'])) {
    $cs = $_POST['ctas'] ?? [];
    if (is_array($cs)) {
        $out['ctas'] = $out['ctas'] ?? [];
        foreach ($cs as $key => $vals) {
            if (!is_array($vals)) continue;
            $clean_key = preg_replace('/[^a-z0-9_]/', '', strtolower((string)$key));
            if ($clean_key === '') continue;
            $out['ctas'][$clean_key] = [
                'title' => trim($vals['title'] ?? ''),
                'text'  => trim($vals['text']  ?? ''),
            ];
        }
    }
}

// === Timeline (werkwijze "Op de dag zelf") ===
if ($is_full_save || isset($_POST['timeline']['items'])) {
    $items_in = $_POST['timeline']['items'] ?? [];
    $items_out = [];
    if (is_array($items_in)) {
        foreach ($items_in as $row) {
            if (!is_array($row)) continue;
            $title = trim($row['title'] ?? '');
            if ($title === '') continue;
            $items_out[] = [
                'time'  => trim($row['time']  ?? ''),
                'title' => $title,
                'text'  => trim($row['text']  ?? ''),
            ];
        }
    }
    $out['timeline'] = ['items' => $items_out];
}

// === Materials & approach (werkwijze.php) ===
if ($is_full_save || isset($_POST['materials'])) {
    $m = $_POST['materials'] ?? [];
    $tools_in = is_array($m['tools'] ?? null) ? $m['tools'] : [];
    $tools_out = [];
    foreach ($tools_in as $t) {
        if (!is_array($t)) continue;
        $title = trim($t['title'] ?? '');
        if ($title === '') continue;
        $tools_out[] = [
            'icon'     => trim($t['icon'] ?? 'Sparkle'),
            'title'    => $title,
            'subtitle' => trim($t['subtitle'] ?? ''),
        ];
    }
    $bullets = isset($m['bullets_text'])
        ? lines_to_array($m['bullets_text'])
        : (is_array($m['bullets'] ?? null) ? array_map('trim', $m['bullets']) : []);
    $out['materials'] = [
        'enabled'      => !empty($m['enabled']) && $m['enabled'] !== '0',
        'eyebrow'      => trim($m['eyebrow']      ?? 'Materialen & aanpak'),
        'heading'      => trim($m['heading']      ?? 'Veilig voor mens, dier en plant.'),
        'intro'        => trim($m['intro']        ?? ''),
        'card_eyebrow' => trim($m['card_eyebrow'] ?? 'Onze toolkit'),
        'bullets'      => $bullets,
        'tools'        => $tools_out,
    ];
}

// === Before/After ===
if (isset($_POST['before_after']) && is_array($_POST['before_after'])) {
    $ba = $_POST['before_after'];
    $out['before_after'] = [
        'title'        => trim($ba['title'] ?? ''),
        'subtitle'     => trim($ba['subtitle'] ?? ''),
        'before_image' => trim($ba['before_image'] ?? ''),
        'after_image'  => trim($ba['after_image'] ?? ''),
        'bullets'      => lines_to_array($ba['bullets_text'] ?? ''),
    ];
}

// === Benefits ===
if ($is_full_save || isset($_POST['benefits'])) {
    $out['benefits'] = [];
    if (isset($_POST['benefits']) && is_array($_POST['benefits'])) {
        foreach ($_POST['benefits'] as $b) {
            if (!is_array($b)) continue;
            $row = [
                'id'    => slug($b['id'] ?? ''),
                'icon'  => trim($b['icon'] ?? 'Spider'),
                'title' => trim($b['title'] ?? ''),
                'text'  => trim($b['text'] ?? ''),
            ];
            if ($row['title'] !== '') $out['benefits'][] = $row;
        }
    }
}

// === Testimonials ===
if ($is_full_save || isset($_POST['testimonials'])) {
    $out['testimonials'] = [];
    if (isset($_POST['testimonials']) && is_array($_POST['testimonials'])) {
        foreach ($_POST['testimonials'] as $t) {
            if (!is_array($t)) continue;
            $row = [
                'quote'    => trim($t['quote'] ?? ''),
                'name'     => trim($t['name'] ?? ''),
                'loc'      => trim($t['loc'] ?? ''),
                'initials' => mb_substr(trim($t['initials'] ?? ''), 0, 3),
            ];
            if ($row['quote'] !== '' && $row['name'] !== '') $out['testimonials'][] = $row;
        }
    }
}

// === Reviews settings (layout, eyebrow, Trustpilot) ===
if (isset($_POST['reviews_settings']) && is_array($_POST['reviews_settings'])) {
    $r = $_POST['reviews_settings'];
    $allowed_layouts = ['grid','compact','slider','list','trustpilot'];
    $layout = trim($r['layout'] ?? 'grid');
    if (!in_array($layout, $allowed_layouts, true)) $layout = 'grid';
    $out['reviews_settings'] = [
        'layout'              => $layout,
        'eyebrow'             => trim($r['eyebrow']  ?? 'Klantervaringen'),
        'heading'             => trim($r['heading']  ?? 'Vertrouwd door huishoudens en bedrijven.'),
        'subtitle'            => trim($r['subtitle'] ?? ''),
        'trustpilot_enabled'  => !empty($r['trustpilot_enabled']) && $r['trustpilot_enabled'] !== '0',
        'trustpilot_embed'    => trim($r['trustpilot_embed'] ?? ''),
    ];
}

// === FAQ ===
if ($is_full_save || isset($_POST['faq'])) {
    $out['faq'] = [];
    if (isset($_POST['faq']) && is_array($_POST['faq'])) {
        foreach ($_POST['faq'] as $f) {
            if (!is_array($f)) continue;
            $row = ['q' => trim($f['q'] ?? ''), 'a' => trim($f['a'] ?? '')];
            if ($row['q'] !== '') $out['faq'][] = $row;
        }
    }
}

// === About ===
if (isset($_POST['about']) && is_array($_POST['about'])) {
    $a = $_POST['about'];
    $out['about'] = $out['about'] ?? [];
    // Story alinea's uit textarea
    if (isset($a['story_text'])) {
        $story = preg_split('/\n\s*\n/', trim($a['story_text']));
        $out['about']['story'] = array_values(array_filter(array_map('trim', $story)));
    }
    // Values repeater
    if ($is_full_save || isset($a['values'])) {
        $out['about']['values'] = [];
        if (isset($a['values']) && is_array($a['values'])) {
            foreach ($a['values'] as $v) {
                if (!is_array($v)) continue;
                $row = [
                    'icon'  => trim($v['icon'] ?? 'Shield'),
                    'title' => trim($v['title'] ?? ''),
                    'text'  => trim($v['text'] ?? ''),
                ];
                if ($row['title'] !== '') $out['about']['values'][] = $row;
            }
        }
    }
    // Map instellingen (provincies + steden + HQ + verbindingslijnen)
    if (isset($a['map']) && is_array($a['map'])) {
        $m = $a['map'];
        $out['about']['map'] = [
            'active_provinces' => array_values(array_filter(array_map('trim', (array)($m['active_provinces'] ?? [])))),
            'active_cities'    => array_values(array_filter(array_map('trim', (array)($m['active_cities']    ?? [])))),
            'hq_city'          => trim((string)($m['hq_city'] ?? '')),
            'show_lines'       => !empty($m['show_lines']) && $m['show_lines'] !== '0',
        ];
    }
    // Alle overige string-velden generiek opslaan
    foreach ($a as $k => $v) {
        if (in_array($k, ['story_text', 'values', 'map'], true)) continue;
        if (is_string($v)) $out['about'][$k] = trim($v);
    }
}

// === Contact ===
if (isset($_POST['contact']) && is_array($_POST['contact'])) {
    foreach ($_POST['contact'] as $k => $v) {
        if (is_string($v)) $out['contact'][$k] = trim($v);
    }
}

// === Homepage extras (stats, trust badges, calculator, sticky bar) ===
if (isset($_POST['homepage_extras']) && is_array($_POST['homepage_extras'])) {
    $he = $_POST['homepage_extras'];
    foreach (['trust_badges_enabled','calculator_enabled','sticky_mobile_bar'] as $bool_field) {
        if (isset($he[$bool_field])) {
            $out['homepage_extras'][$bool_field] = !empty($he[$bool_field]) && $he[$bool_field] !== '0';
        }
    }
    if ($is_full_save || isset($he['trust_badges_items'])) {
        $out['homepage_extras']['trust_badges_items'] = [];
        if (isset($he['trust_badges_items']) && is_array($he['trust_badges_items'])) {
            foreach ($he['trust_badges_items'] as $b) {
                if (!is_array($b)) continue;
                $row = [
                    'icon'     => trim($b['icon'] ?? 'Shield'),
                    'title'    => trim($b['title'] ?? ''),
                    'subtitle' => trim($b['subtitle'] ?? ''),
                ];
                if ($row['title'] !== '') $out['homepage_extras']['trust_badges_items'][] = $row;
            }
        }
    }
}

// === Gallery ===
if (isset($_POST['gallery']) && is_array($_POST['gallery'])) {
    $g = $_POST['gallery'];
    $out['gallery']['title']    = trim($g['title'] ?? '');
    $out['gallery']['subtitle'] = trim($g['subtitle'] ?? '');
    if (isset($g['categories_text'])) {
        $out['gallery']['categories'] = array_values(array_filter(array_map('trim', explode(',', $g['categories_text']))));
    }
    if ($is_full_save || isset($g['items'])) {
        $out['gallery']['items'] = [];
        if (isset($g['items']) && is_array($g['items'])) {
            foreach ($g['items'] as $item) {
                if (!is_array($item)) continue;
                $images = isset($item['images_text']) ? array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $item['images_text'])))) : [];
                $row = [
                    'title'       => trim($item['title'] ?? ''),
                    'category'    => trim($item['category'] ?? ''),
                    'location'    => trim($item['location'] ?? ''),
                    'description' => trim($item['description'] ?? ''),
                    'images'      => $images,
                ];
                if ($row['title'] !== '' || !empty($images)) $out['gallery']['items'][] = $row;
            }
        }
    }
}

// === Service area page ===
if (isset($_POST['service_area_page']) && is_array($_POST['service_area_page'])) {
    foreach ($_POST['service_area_page'] as $k => $v) {
        if (is_string($v)) $out['service_area_page'][$k] = trim($v);
    }
}

// === Legal (privacy + voorwaarden) ===
if (isset($_POST['legal']) && is_array($_POST['legal'])) {
    foreach ($_POST['legal'] as $k => $v) {
        if (is_string($v)) $out['legal'][$k] = $v;
    }
}

// === Custom pages ===
if ($is_full_save || isset($_POST['custom_pages']['items'])) {
    $out['custom_pages']['items'] = [];
    if (isset($_POST['custom_pages']['items']) && is_array($_POST['custom_pages']['items'])) {
        $allowed_modes   = ['markdown', 'html'];
        $allowed_layouts = ['default', 'blank', 'raw'];
        foreach ($_POST['custom_pages']['items'] as $p) {
            if (!is_array($p)) continue;
            $key = slug($p['key'] ?? '');
            if ($key === '') continue;
            $mode   = trim($p['mode']   ?? 'markdown');
            $layout = trim($p['layout'] ?? 'default');
            if (!in_array($mode, $allowed_modes, true))     $mode = 'markdown';
            if (!in_array($layout, $allowed_layouts, true)) $layout = 'default';
            $out['custom_pages']['items'][] = [
                'key'              => $key,
                'eyebrow'          => trim($p['eyebrow'] ?? ''),
                'title'            => trim($p['title'] ?? ''),
                'intro'            => trim($p['intro'] ?? ''),
                'meta_description' => trim($p['meta_description'] ?? ''),
                'mode'             => $mode,
                'layout'           => $layout,
                'content'          => $p['content']    ?? '',
                'custom_css'       => $p['custom_css'] ?? '',
            ];
        }
    }
}

// === Appearance (theme colors + custom CSS + logo) ===
if (isset($_POST['appearance']) && is_array($_POST['appearance'])) {
    foreach ($_POST['appearance'] as $k => $v) {
        if ($k === 'custom_css') {
            $out['appearance'][$k] = is_string($v) ? $v : '';
        } elseif (is_string($v)) {
            $out['appearance'][$k] = trim($v);
        }
    }
}

// === Navigation (menu items) ===
if ($is_full_save || isset($_POST['navigation']['items'])) {
    $out['navigation']['items'] = [];
    if (isset($_POST['navigation']['items']) && is_array($_POST['navigation']['items'])) {
        foreach ($_POST['navigation']['items'] as $item) {
            if (!is_array($item)) continue;
            $label = trim($item['label'] ?? '');
            $url   = trim($item['url'] ?? '');
            if ($label === '' || $url === '') continue;
            $out['navigation']['items'][] = [
                'label' => $label,
                'url'   => $url,
                'key'   => slug($item['key'] ?? $label),
            ];
        }
    }
}

// === Footer ===
if (isset($_POST['footer']) && is_array($_POST['footer'])) {
    foreach ($_POST['footer'] as $k => $v) {
        if (is_string($v)) $out['footer'][$k] = trim($v);
    }
}

// === Announcement bar ===
if (isset($_POST['announcement']) && is_array($_POST['announcement'])) {
    $a = $_POST['announcement'];
    $out['announcement'] = [
        'enabled'    => !empty($a['enabled']) && $a['enabled'] !== '0',
        'text'       => trim($a['text'] ?? ''),
        'link_text'  => trim($a['link_text'] ?? ''),
        'link_url'   => trim($a['link_url'] ?? ''),
        'background' => trim($a['background'] ?? '#382d72'),
        'text_color' => trim($a['text_color'] ?? '#ffffff'),
    ];
}

// === Maintenance mode ===
if (isset($_POST['maintenance']) && is_array($_POST['maintenance'])) {
    $m = $_POST['maintenance'];
    $out['maintenance'] = [
        'enabled'        => !empty($m['enabled']) && $m['enabled'] !== '0',
        'title'          => trim($m['title'] ?? ''),
        'message'        => trim($m['message'] ?? ''),
        'allow_admin_ip' => !empty($m['allow_admin_ip']) && $m['allow_admin_ip'] !== '0',
    ];
}

// === Lead settings ===
if (isset($_POST['lead_settings']) && is_array($_POST['lead_settings'])) {
    $ls = $_POST['lead_settings'];
    $out['lead_settings'] = $out['lead_settings'] ?? [];
    foreach ($ls as $k => $v) {
        if ($k === 'auto_reply_enabled') {
            $out['lead_settings'][$k] = !empty($v) && $v !== '0';
        } elseif (is_string($v)) {
            $out['lead_settings'][$k] = trim($v);
        }
    }
}

// === Cookie banner texts ===
if (isset($_POST['cookie_banner']) && is_array($_POST['cookie_banner'])) {
    foreach ($_POST['cookie_banner'] as $k => $v) {
        if (is_string($v)) $out['cookie_banner'][$k] = trim($v);
    }
}

// === Tracking / Marketing ===
if (isset($_POST['tracking']) && is_array($_POST['tracking'])) {
    foreach ($_POST['tracking'] as $k => $v) {
        if ($k === 'cookie_consent_enabled') {
            $out['tracking']['cookie_consent_enabled'] = !empty($v) && $v !== '0';
        } elseif (in_array($k, ['custom_head', 'custom_body_end'], true)) {
            // Niet trimmen - kan whitespace bevatten
            $out['tracking'][$k] = is_string($v) ? $v : '';
        } elseif (is_string($v)) {
            $out['tracking'][$k] = trim($v);
        }
    }
}

// === SEO ===
if (isset($_POST['seo']) && is_array($_POST['seo'])) {
    foreach ($_POST['seo'] as $k => $v) {
        if ($k === 'pages' && is_array($v)) {
            $out['seo']['pages'] = $out['seo']['pages'] ?? [];
            foreach ($v as $page_key => $page_data) {
                if (!is_array($page_data)) continue;
                foreach ($page_data as $pk => $pv) {
                    if (is_string($pv)) $out['seo']['pages'][$page_key][$pk] = trim($pv);
                }
            }
        } elseif ($k === 'service_cities_text') {
            $out['seo']['service_cities'] = array_values(array_filter(array_map('trim', explode(',', $v))));
        } elseif ($k === 'rating_count') {
            $out['seo']['rating_count'] = (int)$v;
        } elseif (is_string($v)) {
            $out['seo'][$k] = trim($v);
        }
    }
}

// === Schrijven (atomic) ===
$json = json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if ($json === false) {
    header('Location: ' . $BASE . '/admin/edit.php?err=' . urlencode('JSON-encoding mislukt'));
    exit;
}

$tmp = $CONTENT_FILE . '.tmp';
if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
    header('Location: ' . $BASE . '/admin/edit.php?err=' . urlencode('Kon bestand niet schrijven. Controleer schrijfrechten op content/site.json (chmod 644).'));
    exit;
}
if (!@rename($tmp, $CONTENT_FILE)) {
    @unlink($tmp);
    header('Location: ' . $BASE . '/admin/edit.php?err=' . urlencode('Kon bestand niet vervangen.'));
    exit;
}

// === Backup laatste 5 versies ===
$backup_dir = __DIR__ . '/../content/backups';
if (!is_dir($backup_dir)) @mkdir($backup_dir, 0755, true);
@copy($CONTENT_FILE, $backup_dir . '/site-' . date('Y-m-d_His') . '.json');
$bk = glob($backup_dir . '/site-*.json');
if ($bk && count($bk) > 5) {
    sort($bk);
    foreach (array_slice($bk, 0, count($bk) - 5) as $old) @unlink($old);
}

// Audit log
$tab = $_POST['_tab'] ?? 'unknown';
log_activity('content_save', "Tab '$tab' bijgewerkt");

header('Location: ' . $BASE . '/admin/edit.php?saved=1' . (isset($_POST['_tab']) ? '#tab-' . urlencode($_POST['_tab']) : ''));
exit;

// === Helpers ===
function lines_to_array($t) {
    if (!is_string($t)) return [];
    $lines = preg_split('/\r?\n/', $t);
    return array_values(array_filter(array_map('trim', $lines), fn($l) => $l !== ''));
}
function slug($s) {
    $s = strtolower(trim((string)$s));
    $s = preg_replace('/[^a-z0-9\-_]+/', '-', $s);
    $s = trim(preg_replace('/-+/', '-', $s), '-');
    return $s ?: 'item';
}
