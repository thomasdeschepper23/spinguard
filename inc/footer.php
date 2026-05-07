<?php
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }
$site = $CONTENT['site'] ?? [];
$prices = $CONTENT['prices'] ?? [];
$footer = $CONTENT['footer'] ?? [];
$_logo_path = $CONTENT['appearance']['logo'] ?? '/assets/spinguard-logo.png';
$_default_tag = 'Professionele spinnenbestrijding voor woningen en bedrijfspanden in ' . ($site['service_area'] ?? 'heel Nederland') . '. Duurzaam, veilig en met zes maanden garantie.';
$_navigation = $CONTENT['navigation']['items'] ?? [];
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <a href="<?= e(b('/')) ?>" class="logo">
          <span class="logo-mark lg"><img src="<?= e(b($_logo_path)) ?>" alt="<?= e($site['brand'] ?? 'SpinGuard') ?> logo" width="52" height="52" loading="lazy" /></span>
        </a>
        <p class="footer-tag">
          <?= e($footer['tagline'] ?? $_default_tag) ?>
        </p>
        <?php if (!empty($footer['extra_text'])): ?>
        <p class="footer-tag" style="margin-top:12px; font-size:13px;"><?= nl2br(e($footer['extra_text'])) ?></p>
        <?php endif; ?>
        <div class="social-row">
          <?php if (!empty($site['instagram'])): ?>
            <a href="<?= e($site['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon_social('instagram') ?></a>
          <?php endif; ?>
          <?php if (!empty($site['tiktok'])): ?>
            <a href="<?= e($site['tiktok']) ?>" target="_blank" rel="noopener" aria-label="TikTok"><?= icon_social('tiktok') ?></a>
          <?php endif; ?>
          <?php if (!empty($site['facebook'])): ?>
            <a href="<?= e($site['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icon_social('facebook') ?></a>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <h5>Navigatie</h5>
        <ul>
          <?php foreach ($_navigation as $item): ?>
            <li><a href="<?= e(b($item['url'])) ?>"><?= e($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h5>Diensten</h5>
        <ul>
          <?php foreach ($prices as $p): ?>
            <li><a href="<?= e(b('/diensten.php')) ?>#<?= e($p['id']) ?>"><?= e($p['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h5>Contact</h5>
        <ul>
          <li><a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">WhatsApp <?= e($site['phone'] ?? '') ?></a></li>
          <li><a href="<?= e($site['phone_href'] ?? '#') ?>">Bel <?= e($site['phone'] ?? '') ?></a></li>
          <li><a href="mailto:<?= e($site['email'] ?? '') ?>"><?= e($site['email'] ?? '') ?></a></li>
          <li><?= e($site['address'] ?? '') ?></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>
        <?= e($footer['copyright_text'] ?? ('© ' . date('Y') . ' ' . ($site['brand'] ?? 'SpinGuard') . '. Alle rechten voorbehouden.')) ?>
        ·
        <a href="<?= e(b('/privacy.php')) ?>" style="color:inherit; text-decoration:underline;">Privacy</a>
        ·
        <a href="<?= e(b('/voorwaarden.php')) ?>" style="color:inherit; text-decoration:underline;">Voorwaarden</a>
      </span>
      <span>
        <?= e($site['domain'] ?? '') ?>
        <?php if (!empty($site['kvk'])): ?> · KvK <?= e($site['kvk']) ?><?php endif; ?>
        <?php if (!empty($site['btw'])): ?> · BTW <?= e($site['btw']) ?><?php endif; ?>
      </span>
    </div>
  </div>
</footer>

<a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="fab" aria-label="Stuur ons een WhatsApp-bericht">
  <?= icon_whatsapp(28, '#062a13') ?>
</a>

<?php if (!empty($CONTENT['homepage_extras']['sticky_mobile_bar'])): ?>
<div class="mobile-sticky-bar">
  <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="msb-wa">
    <?= icon_whatsapp(18, '#062a13') ?> WhatsApp
  </a>
  <a href="<?= e($site['phone_href'] ?? '#') ?>" class="msb-call">
    <?= icon('Phone', 16, 'white') ?> Bel direct
  </a>
</div>
<script>document.body.classList.add('has-sticky-bar');</script>
<?php endif; ?>

<?php
// Cookie consent banner — toont alleen als nog geen keuze gemaakt is
$_t = $CONTENT['tracking'] ?? [];
$_cb = $CONTENT['cookie_banner'] ?? [];
$_consent_enabled = !empty($_t['cookie_consent_enabled']);
$_has_choice = isset($_COOKIE['spinguard_consent']);
if ($_consent_enabled && !$_has_choice):
?>
<div id="cookieBanner" class="cookie-banner" role="dialog" aria-labelledby="cookieBannerTitle">
  <div class="cookie-banner-inner">
    <div class="cookie-banner-text">
      <strong id="cookieBannerTitle">🍪 <?= e($_cb['title'] ?? 'Cookies & privacy') ?></strong>
      <p><?= e($_cb['message'] ?? 'Wij gebruiken essentiële cookies voor de werking van de site.') ?></p>
    </div>
    <div class="cookie-banner-actions">
      <button type="button" class="btn btn-ghost btn-sm" data-cookie="essential"><?= e($_cb['decline_label'] ?? 'Alleen noodzakelijk') ?></button>
      <button type="button" class="btn btn-primary btn-sm" data-cookie="all"><?= e($_cb['accept_label'] ?? 'Akkoord met alle') ?></button>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="<?= e(b('/js/main.js?v=8')) ?>"></script>
<?php require __DIR__ . '/tracking_body.php'; ?>
</body>
</html>
