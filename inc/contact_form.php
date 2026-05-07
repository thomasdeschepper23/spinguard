<?php
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }
$site = $CONTENT['site'] ?? [];
$prices = $CONTENT['prices'] ?? [];
$contact = $CONTENT['contact'] ?? [];

// Voor gebruik na POST
$form_status  = $form_status ?? null;   // 'success' | 'error' | null
$form_message = $form_message ?? '';
?>
<div class="contact-wrap">
  <div class="contact-info">
    <h3>Direct contact</h3>
    <p><?= e($contact['form_intro'] ?? 'De snelste route naar een offerte is via WhatsApp.') ?></p>
    <div class="contact-channels">
      <a class="channel wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">
        <span class="icon-cube"><?= icon_whatsapp(22, '#062a13') ?></span>
        <div>
          <div class="channel-label">WhatsApp — snelst</div>
          <div class="channel-value"><?= e($site['phone'] ?? '') ?></div>
        </div>
      </a>
      <a class="channel" href="<?= e($site['phone_href'] ?? '#') ?>">
        <span class="icon-cube"><?= icon('Phone', 20, 'white') ?></span>
        <div>
          <div class="channel-label">Bel ons</div>
          <div class="channel-value"><?= e($site['phone'] ?? '') ?></div>
        </div>
      </a>
      <a class="channel" href="mailto:<?= e($site['email'] ?? '') ?>">
        <span class="icon-cube"><?= icon('Mail', 20, 'white') ?></span>
        <div>
          <div class="channel-label">E-mail</div>
          <div class="channel-value"><?= e($site['email'] ?? '') ?></div>
        </div>
      </a>
      <div class="channel" style="cursor:default;">
        <span class="icon-cube"><?= icon('Pin', 20, 'white') ?></span>
        <div>
          <div class="channel-label">Werkgebied</div>
          <div class="channel-value"><?= e($site['service_area'] ?? 'Nederland') ?></div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($form_status === 'success'): ?>
    <div class="contact-form" style="justify-content:center;">
      <div class="form-success">
        <span class="check"><?= icon('Check', 20, 'white') ?></span>
        <div>
          <h4>Bedankt voor uw aanvraag!</h4>
          <p>We hebben uw bericht goed ontvangen en reageren binnen 24 uur. Wilt u sneller contact? Stuur dan een WhatsApp-bericht naar <?= e($site['phone'] ?? '') ?>.</p>
        </div>
      </div>
      <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:18px; align-self:flex-start;">
        <?= icon_whatsapp(18, '#062a13') ?>
        WhatsApp ons direct
      </a>
    </div>
  <?php else: ?>
    <form class="contact-form" method="post" action="<?= e(b('/api/contact.php')) ?>" id="contactForm" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="_redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? '/contact.php') ?>" />
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute; left:-10000px; width:1px; height:1px;" aria-hidden="true" />

      <?php if ($form_status === 'error'): ?>
        <div class="notice error" style="color:#7a2a10; background:#fff4f0; border:1px solid #ffa080; padding:12px 14px; border-radius:10px;">
          <?= e($form_message) ?>
        </div>
      <?php endif; ?>

      <div class="field-row">
        <div class="field">
          <label>Naam <span class="req">*</span></label>
          <input type="text" name="name" required placeholder="Voor- en achternaam" value="<?= e($_POST['name'] ?? '') ?>" />
        </div>
        <div class="field">
          <label>Telefoon <span class="req">*</span></label>
          <input type="tel" name="phone" required placeholder="06 12 34 56 78" value="<?= e($_POST['phone'] ?? '') ?>" />
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>E-mail (optioneel)</label>
          <input type="email" name="email" placeholder="naam@voorbeeld.nl" value="<?= e($_POST['email'] ?? '') ?>" />
        </div>
        <div class="field">
          <label>Postcode <span class="req">*</span></label>
          <input type="text" name="postcode" required placeholder="6500 AA" value="<?= e($_POST['postcode'] ?? '') ?>" />
        </div>
      </div>

      <div class="field">
        <label>Type pand</label>
        <select name="type">
          <?php foreach ($prices as $p): ?>
            <option value="<?= e($p['id']) ?>" <?= (($_POST['type'] ?? 'rijtjeshuis') === $p['id']) ? 'selected' : '' ?>>
              <?= e($p['name']) ?><?= !empty($p['price']) ? ' — vanaf €' . e($p['price']) : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label>Toelichting (optioneel)</label>
        <textarea name="message" rows="4" placeholder="Vertel kort wat u graag behandeld wilt hebben…"><?= e($_POST['message'] ?? '') ?></textarea>
      </div>

      <div class="field">
        <label>Foto's van de situatie <span class="hint">(optioneel · max 5 · JPG/PNG/WebP/HEIC, 8 MB per foto)</span></label>
        <label class="photo-drop" for="photoInput" id="photoDrop">
          <input type="file" name="photos[]" id="photoInput" accept="image/jpeg,image/png,image/webp,image/heic,image/heif" multiple hidden />
          <div class="photo-drop-inner">
            <span class="photo-drop-icon"><?= icon('Camera', 22, 'currentColor') ?></span>
            <div>
              <strong>Klik of sleep foto's hierheen</strong>
              <small>Help ons sneller een offerte op maat maken</small>
            </div>
          </div>
        </label>
        <div class="photo-previews" id="photoPreviews" hidden></div>
        <div class="field-error" id="photoError" hidden></div>
      </div>

      <div class="form-foot">
        <button type="submit" class="btn btn-primary btn-lg">
          Verstuur aanvraag <?= icon('Arrow', 16, 'white') ?>
        </button>
        <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-lg" style="background:#25d366; color:#062a13; border-color:transparent;">
          <?= icon_whatsapp(18, '#062a13') ?>
          Of via WhatsApp
        </a>
      </div>
      <small style="color:var(--muted); font-size:12.5px;">We bellen niet ongevraagd. Uw gegevens blijven privé.</small>
    </form>
  <?php endif; ?>
</div>
