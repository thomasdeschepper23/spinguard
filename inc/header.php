<?php
// Gedeelde header voor alle pagina's
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

// === Maintenance mode check (voor alles wat niet admin is) ===
$_maint = $CONTENT['maintenance'] ?? [];
if (!empty($_maint['enabled'])) {
    // Allow admin sessions to bypass
    $bypass = false;
    if (!empty($_maint['allow_admin_ip'])) {
        if (file_exists(__DIR__ . '/../admin/config.php')) {
            require_once __DIR__ . '/../admin/config.php';
            if (function_exists('admin_is_logged_in') && admin_is_logged_in()) $bypass = true;
        }
    }
    if (!$bypass && !isset($_GET['preview'])) {
        require __DIR__ . '/maintenance.php';
        exit;
    }
}

$current = $current ?? '';
$site = $CONTENT['site'] ?? [];
$seo  = $CONTENT['seo']  ?? [];
$appearance = $CONTENT['appearance'] ?? [];
$navigation = $CONTENT['navigation']['items'] ?? [
    ['label'=>'Home','url'=>'/','key'=>'home'],
    ['label'=>'Diensten','url'=>'/diensten.php','key'=>'diensten'],
    ['label'=>'Werkwijze','url'=>'/werkwijze.php','key'=>'werkwijze'],
    ['label'=>'Over ons','url'=>'/over-ons.php','key'=>'over'],
    ['label'=>'Contact','url'=>'/contact.php','key'=>'contact'],
];
$announcement = $CONTENT['announcement'] ?? [];

// Logo: kan custom geüpload zijn
$logo_path = $appearance['logo'] ?? '/assets/spinguard-logo.png';

// Per-pagina SEO override (zet in PHP file: $page_key = 'home'|'diensten'|...)
$page_key = $page_key ?? $current;
$page_seo = ($seo['pages'] ?? [])[$page_key] ?? [];
$page_title = $page_title ?? ($page_seo['title']       ?? $seo['title']       ?? 'SpinGuard');
$page_desc  = $page_desc  ?? ($page_seo['description'] ?? $seo['description'] ?? '');

// Canonical URL
$site_url = rtrim($seo['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');
$canonical = $site_url . ($_SERVER['REQUEST_URI'] ?? '/');
// Strip query string + trailing slash voor cleane canonical
$canonical = strtok($canonical, '?');
if (substr($canonical, -1) === '/' && $canonical !== $site_url . '/') {
    $canonical = rtrim($canonical, '/');
}

$og_image = $site_url . ($seo['default_og_image'] ?? '/assets/spinguard-logo.png');

// Optional schemas (set door pagina via $page_schemas)
$page_schemas = $page_schemas ?? [];
?><!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="theme-color" content="#0c0a1a" />
  <title><?= e($page_title) ?></title>

  <meta name="description" content="<?= e($page_desc) ?>" />
  <?php if (!empty($seo['keywords'])): ?>
  <meta name="keywords" content="<?= e($seo['keywords']) ?>" />
  <?php endif; ?>
  <meta name="author" content="<?= e($site['brand'] ?? 'SpinGuard') ?>" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="googlebot" content="index, follow" />
  <meta name="format-detection" content="telephone=yes" />
  <meta name="geo.region" content="NL-<?= e(substr($seo['geo_region'] ?? 'GE', 0, 2)) ?>" />
  <?php if (!empty($seo['geo_city'])): ?>
  <meta name="geo.placename" content="<?= e($seo['geo_city']) ?>" />
  <?php endif; ?>
  <?php if (!empty($seo['geo_latitude']) && !empty($seo['geo_longitude'])): ?>
  <meta name="geo.position" content="<?= e($seo['geo_latitude']) ?>;<?= e($seo['geo_longitude']) ?>" />
  <meta name="ICBM" content="<?= e($seo['geo_latitude']) ?>, <?= e($seo['geo_longitude']) ?>" />
  <?php endif; ?>

  <link rel="canonical" href="<?= e($canonical) ?>" />

  <!-- Open Graph -->
  <meta property="og:locale" content="nl_NL" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= e($page_title) ?>" />
  <meta property="og:description" content="<?= e($page_desc) ?>" />
  <meta property="og:url" content="<?= e($canonical) ?>" />
  <meta property="og:site_name" content="<?= e($site['brand'] ?? 'SpinGuard') ?>" />
  <meta property="og:image" content="<?= e($og_image) ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= e($page_title) ?>" />
  <meta name="twitter:description" content="<?= e($page_desc) ?>" />
  <meta name="twitter:image" content="<?= e($og_image) ?>" />

  <link rel="icon" type="image/png" href="<?= e(b($logo_path)) ?>" />
  <link rel="apple-touch-icon" href="<?= e(b($logo_path)) ?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(b('/styles.css?v=10')) ?>" />

  <!-- Theme overrides uit admin -->
  <style>
    :root {
      --violet-600: <?= e($appearance['primary_color']       ?? '#382d72') ?>;
      --violet-700: <?= e($appearance['primary_color_dark']  ?? '#2a2059') ?>;
      --violet-300: <?= e($appearance['primary_color_light'] ?? '#9d92c4') ?>;
      --ink-900:    <?= e($appearance['ink_color']           ?? '#0d1224') ?>;
    }
    <?= $appearance['custom_css'] ?? '' ?>
    <?php if (!empty($EXTRA_HEAD_CSS)): ?>
    /* === Custom-pagina CSS === */
    <?= $EXTRA_HEAD_CSS /* admin-input, raw uitvoer */ ?>
    <?php endif; ?>
  </style>

  <script>window.SPINGUARD_BASE = <?= json_encode($BASE_URL) ?>;</script>

  <!-- ===== Schema.org JSON-LD: LocalBusiness ===== -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "PestControlService",
    "@id": "<?= e($site_url) ?>/#business",
    "name": "<?= e($site['brand'] ?? 'SpinGuard') ?>",
    "alternateName": "SpinGuard Spinnenbestrijding",
    "description": <?= json_encode($seo['description'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
    "url": "<?= e($site_url) ?>",
    "telephone": "<?= e($site['phone'] ?? '') ?>",
    "email": "<?= e($site['email'] ?? '') ?>",
    "image": "<?= e($og_image) ?>",
    "logo": "<?= e($og_image) ?>",
    "priceRange": "<?= e($seo['price_range'] ?? '€€') ?>",
    "currenciesAccepted": "EUR",
    "paymentAccepted": "Cash, Bank transfer, iDEAL",
    <?php if (!empty($seo['opening_hours'])): ?>
    "openingHours": <?= json_encode($seo['opening_hours']) ?>,
    <?php endif; ?>
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "NL",
      "addressRegion": <?= json_encode($seo['geo_region'] ?? 'Gelderland') ?>,
      "addressLocality": <?= json_encode($seo['geo_city'] ?? ($site['address'] ?? 'Nijmegen')) ?>,
      "postalCode": <?= json_encode($seo['geo_postcode'] ?? '') ?>
    },
    <?php if (!empty($seo['geo_latitude'])): ?>
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": <?= json_encode($seo['geo_latitude']) ?>,
      "longitude": <?= json_encode($seo['geo_longitude']) ?>
    },
    <?php endif; ?>
    "areaServed": [
      <?php
        $cities = $seo['service_cities'] ?? ['Nederland'];
        $area_items = array_map(function($c){ return '{"@type":"City","name":'.json_encode($c).'}'; }, $cities);
        echo implode(",\n      ", $area_items);
      ?>
    ],
    "sameAs": [
      <?php
        $links = array_filter([
          $site['instagram'] ?? '',
          $site['tiktok']    ?? '',
          $site['facebook']  ?? '',
        ]);
        echo implode(', ', array_map('json_encode', $links));
      ?>
    ],
    <?php if (!empty($seo['rating_count']) && (int)$seo['rating_count'] > 0): ?>
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": <?= json_encode($seo['rating_value'] ?? '5.0') ?>,
      "reviewCount": <?= (int)$seo['rating_count'] ?>,
      "bestRating": "5",
      "worstRating": "1"
    },
    <?php endif; ?>
    "knowsAbout": ["Spinnenbestrijding", "Ongediertebestrijding", "Webbenverwijdering", "Gevelreiniging", "Preventie"],
    "slogan": <?= json_encode($site['tagline'] ?? '') ?>
  }
  </script>

  <?php
  // Extra schemas (FAQPage, Service, BreadcrumbList) per pagina
  foreach ($page_schemas as $schema):
  ?>
  <script type="application/ld+json"><?= $schema ?></script>
  <?php endforeach; ?>

  <?php require __DIR__ . '/tracking_head.php'; ?>
</head>
<body>
  <?php if (!empty($announcement['enabled']) && !empty($announcement['text'])): ?>
  <div class="announcement-bar" style="background:<?= e($announcement['background'] ?? '#382d72') ?>; color:<?= e($announcement['text_color'] ?? '#fff') ?>;">
    <div class="container">
      <span><?= e($announcement['text']) ?></span>
      <?php if (!empty($announcement['link_text']) && !empty($announcement['link_url'])): ?>
        <a href="<?= e(safe_url($announcement['link_url'])) ?>" style="color:inherit; text-decoration:underline; font-weight:600; margin-left:8px;">
          <?= e($announcement['link_text']) ?> →
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <header class="site-header" id="siteHeader">
    <div class="container">
      <nav class="nav" aria-label="Hoofdmenu">
        <a href="<?= e(b('/')) ?>" class="logo" aria-label="<?= e($site['brand'] ?? 'SpinGuard') ?> — naar home">
          <span class="logo-mark"><img src="<?= e(b($logo_path)) ?>" alt="<?= e($site['brand'] ?? 'SpinGuard') ?> logo" width="40" height="40" /></span>
        </a>
        <div class="nav-links">
          <?php foreach ($navigation as $item): ?>
            <a href="<?= e(safe_url(b($item['url']))) ?>"<?= ($current === ($item['key'] ?? ''))?' class="active"':'' ?>><?= e($item['label']) ?></a>
          <?php endforeach; ?>
        </div>
        <div class="nav-cta">
          <a href="<?= e($site['phone_href'] ?? '#') ?>" class="btn btn-ghost">
            <?= icon('Phone', 16) ?>
            <?= e($site['phone'] ?? '') ?>
          </a>
          <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary">
            Offerte aanvragen
          </a>
          <button id="menuToggle" class="menu-toggle" aria-label="Menu openen" aria-expanded="false">
            <?= icon('Menu', 20) ?>
          </button>
        </div>
      </nav>
    </div>
  </header>
  <div id="mobileMenu" class="mobile-menu">
    <button type="button" id="mobileMenuClose" class="mobile-menu-close" aria-label="Menu sluiten">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6 L18 18 M18 6 L6 18"/></svg>
    </button>
    <?php foreach ($navigation as $item): ?>
      <a href="<?= e(safe_url(b($item['url']))) ?>"><?= e($item['label']) ?></a>
    <?php endforeach; ?>
    <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
      <?= icon_whatsapp(18, '#25D366') ?> WhatsApp ons
    </a>
    <a href="<?= e($site['phone_href'] ?? '#') ?>" class="btn btn-ghost btn-lg">
      <?= icon('Phone', 16) ?> <?= e($site['phone'] ?? '') ?>
    </a>
  </div>
