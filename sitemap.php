<?php
header('Content-Type: application/xml; charset=utf-8');
define('SPINGUARD_INC', true);
require __DIR__ . '/inc/bootstrap.php';

$site = $CONTENT['site'] ?? [];
$seo  = $CONTENT['seo']  ?? [];
$domain = rtrim($seo['site_url'] ?? ('https://' . ($site['domain'] ?? 'spinguard.nl')), '/');

// Lastmod: van content/site.json mtime
$content_file = __DIR__ . '/content/site.json';
$lastmod = file_exists($content_file) ? date('Y-m-d', filemtime($content_file)) : date('Y-m-d');

$pages = [
    ['loc' => '/',              'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => '/diensten.php',  'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => '/werkwijze.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => '/over-ons.php',  'priority' => '0.7', 'changefreq' => 'yearly'],
    ['loc' => '/contact.php',   'priority' => '0.9', 'changefreq' => 'monthly'],
];

// Verzamel geüploade foto's voor image sitemap
$uploads_dir = __DIR__ . '/uploads';
$images = [];
if (is_dir($uploads_dir)) {
    foreach (scandir($uploads_dir) as $f) {
        if (preg_match('/\.(jpe?g|png|webp)$/i', $f)) {
            $images[] = $domain . '/uploads/' . rawurlencode($f);
        }
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($pages as $p): ?>
  <url>
    <loc><?= e($domain . $p['loc']) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq><?= $p['changefreq'] ?></changefreq>
    <priority><?= $p['priority'] ?></priority>
    <?php if ($p['loc'] === '/' && !empty($images)): foreach ($images as $img): ?>
    <image:image>
      <image:loc><?= e($img) ?></image:loc>
    </image:image>
    <?php endforeach; endif; ?>
  </url>
<?php endforeach; ?>
</urlset>
