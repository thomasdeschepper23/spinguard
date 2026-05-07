<?php
/**
 * Marketing & Analytics tracking — head section
 * Tracking laadt ALLEEN na cookie-consent (GDPR/AVG compliant).
 *
 * Server-side check: $_COOKIE['spinguard_consent'] === 'all'
 * Als consent is gegeven worden de scripts gerenderd.
 */
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

$t = $CONTENT['tracking'] ?? [];
$consent_required = !empty($t['cookie_consent_enabled']);
$has_consent = !$consent_required || (isset($_COOKIE['spinguard_consent']) && $_COOKIE['spinguard_consent'] === 'all');

// Maak tracking config beschikbaar voor JS (zonder de scripts zelf te laden)
?>
<script>
window.SPINGUARD_TRACKING = {
    consent: <?= $has_consent ? 'true' : 'false' ?>,
    consentRequired: <?= $consent_required ? 'true' : 'false' ?>,
    ga4: <?= json_encode($t['ga4_id'] ?? '') ?>,
    metaPixel: <?= json_encode($t['meta_pixel_id'] ?? '') ?>,
    gtm: <?= json_encode($t['gtm_id'] ?? '') ?>,
    googleAds: <?= json_encode($t['google_ads_id'] ?? '') ?>,
    googleAdsLabel: <?= json_encode($t['google_ads_conversion_label'] ?? '') ?>,
    linkedinPartnerId: <?= json_encode($t['linkedin_partner_id'] ?? '') ?>,
    tiktokPixel: <?= json_encode($t['tiktok_pixel_id'] ?? '') ?>
};
</script>

<?php if ($has_consent): ?>

  <?php if (!empty($t['gtm_id'])): ?>
  <!-- Google Tag Manager (vervangt los GA4 en kan ook Meta Pixel/Ads bevatten) -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','<?= htmlspecialchars($t['gtm_id'], ENT_QUOTES) ?>');</script>
  <!-- End Google Tag Manager -->
  <?php endif; ?>

  <?php if (!empty($t['ga4_id']) && empty($t['gtm_id'])): ?>
  <!-- Google Analytics 4 (alleen als geen GTM gebruikt wordt) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($t['ga4_id'], ENT_QUOTES) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= htmlspecialchars($t['ga4_id'], ENT_QUOTES) ?>', { anonymize_ip: true });
  </script>
  <?php endif; ?>

  <?php if (!empty($t['google_ads_id']) && empty($t['gtm_id'])): ?>
  <!-- Google Ads Conversion Tracking -->
  <?php if (empty($t['ga4_id'])): // alleen gtag.js loader laden als nog niet door GA4 geladen ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($t['google_ads_id'], ENT_QUOTES) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
  </script>
  <?php endif; ?>
  <script>
    gtag('config', '<?= htmlspecialchars($t['google_ads_id'], ENT_QUOTES) ?>');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['meta_pixel_id'])): ?>
  <!-- Meta (Facebook) Pixel -->
  <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= htmlspecialchars($t['meta_pixel_id'], ENT_QUOTES) ?>');
    fbq('track', 'PageView');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['tiktok_pixel_id'])): ?>
  <!-- TikTok Pixel -->
  <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
      ttq.load('<?= htmlspecialchars($t['tiktok_pixel_id'], ENT_QUOTES) ?>');
      ttq.page();
    }(window, document, 'ttq');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['linkedin_partner_id'])): ?>
  <!-- LinkedIn Insight Tag -->
  <script type="text/javascript">
    _linkedin_partner_id = "<?= htmlspecialchars($t['linkedin_partner_id'], ENT_QUOTES) ?>";
    window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
    window._linkedin_data_partner_ids.push(_linkedin_partner_id);
    (function(l) { if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])}; window.lintrk.q=[]} var s = document.getElementsByTagName("script")[0]; var b = document.createElement("script"); b.type = "text/javascript";b.async = true; b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b, s);})(window.lintrk);
  </script>
  <?php endif; ?>

  <?php if (!empty($t['custom_head'])): ?>
  <!-- Custom head code (uit admin) -->
  <?= $t['custom_head'] /* Bewust niet escapen — admin kan eigen scripts plakken */ ?>
  <?php endif; ?>

<?php endif; // has_consent ?>
