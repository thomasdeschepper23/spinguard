<?php
/**
 * Marketing & Analytics tracking — body section
 * NoScript fallbacks + custom body-end code.
 */
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

$t = $CONTENT['tracking'] ?? [];
$consent_required = !empty($t['cookie_consent_enabled']);
$has_consent = !$consent_required || (isset($_COOKIE['spinguard_consent']) && $_COOKIE['spinguard_consent'] === 'all');

if (!$has_consent) return;
?>

<?php if (!empty($t['gtm_id'])): ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($t['gtm_id'], ENT_QUOTES) ?>"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>

<?php if (!empty($t['meta_pixel_id'])): ?>
<!-- Meta Pixel (noscript) -->
<noscript><img height="1" width="1" style="display:none" alt=""
  src="https://www.facebook.com/tr?id=<?= htmlspecialchars($t['meta_pixel_id'], ENT_QUOTES) ?>&ev=PageView&noscript=1"/></noscript>
<?php endif; ?>

<?php if (!empty($t['linkedin_partner_id'])): ?>
<!-- LinkedIn Insight Tag (noscript) -->
<noscript><img height="1" width="1" style="display:none;" alt=""
  src="https://px.ads.linkedin.com/collect/?pid=<?= htmlspecialchars($t['linkedin_partner_id'], ENT_QUOTES) ?>&fmt=gif"/></noscript>
<?php endif; ?>

<?php if (!empty($t['custom_body_end'])): ?>
<!-- Custom body code (uit admin) -->
<?= $t['custom_body_end'] /* Bewust niet escapen */ ?>
<?php endif; ?>
