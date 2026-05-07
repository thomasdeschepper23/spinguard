/* SpinGuard — frontend interactiviteit. Geen React, geen Babel — pure JS. */
(function () {
  'use strict';

  // ============================================
  // Cookie consent (GDPR/AVG)
  // ============================================
  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/;SameSite=Lax";
  }
  function getCookie(name) {
    const m = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]+)'));
    return m ? m[2] : null;
  }
  const banner = document.getElementById('cookieBanner');
  if (banner) {
    banner.querySelectorAll('[data-cookie]').forEach(btn => {
      btn.addEventListener('click', () => {
        const choice = btn.dataset.cookie; // 'all' | 'essential'
        setCookie('spinguard_consent', choice, 365);
        banner.classList.add('hidden');
        if (choice === 'all') {
          // Reload zodat tracking-scripts server-side worden geïnjecteerd
          location.reload();
        }
      });
    });
  }

  // ============================================
  // Conversion tracking — vuurt events af bij belangrijke acties
  // (alleen actief als tracking-IDs ingesteld zijn én cookie-consent gegeven is)
  // ============================================
  const T = window.SPINGUARD_TRACKING || {};

  function fireMetaPixel(event, params) {
    if (window.fbq) try { window.fbq('track', event, params || {}); } catch(e) {}
  }
  function fireGA4(event, params) {
    if (window.gtag) try { window.gtag('event', event, params || {}); } catch(e) {}
  }
  function fireGoogleAdsConversion() {
    if (window.gtag && T.googleAds && T.googleAdsLabel) {
      try {
        window.gtag('event', 'conversion', {
          'send_to': T.googleAds + '/' + T.googleAdsLabel
        });
      } catch(e) {}
    }
  }
  function fireDataLayer(eventName, extra) {
    if (window.dataLayer) try { window.dataLayer.push(Object.assign({event: eventName}, extra || {})); } catch(e) {}
  }

  // === Lead succes (na succesvol verzenden contactformulier) ===
  if (location.search.indexOf('sent=1') !== -1) {
    fireMetaPixel('Lead', { content_name: 'Contact form', currency: 'EUR' });
    fireGA4('generate_lead', { method: 'contact_form' });
    fireGoogleAdsConversion();
    fireDataLayer('lead_submitted', { method: 'contact_form' });
  }

  // === WhatsApp click ===
  document.addEventListener('click', (e) => {
    const wa = e.target.closest('a[href*="wa.me/"]');
    if (wa) {
      fireMetaPixel('Contact', { content_name: 'WhatsApp click' });
      fireGA4('whatsapp_click', { event_category: 'engagement', event_label: 'WhatsApp' });
      fireDataLayer('whatsapp_click');
      return;
    }
    // === Phone click ===
    const tel = e.target.closest('a[href^="tel:"]');
    if (tel) {
      fireMetaPixel('Contact', { content_name: 'Phone call' });
      fireGA4('phone_click', { event_category: 'engagement', event_label: 'Phone' });
      fireDataLayer('phone_click');
      return;
    }
    // === Email click ===
    const mail = e.target.closest('a[href^="mailto:"]');
    if (mail) {
      fireMetaPixel('Contact', { content_name: 'Email click' });
      fireGA4('email_click', { event_category: 'engagement', event_label: 'Email' });
      fireDataLayer('email_click');
    }
  });

  // === Contact form submit (vuurt voordat redirect plaatsvindt) ===
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', () => {
      fireDataLayer('form_submit_attempt', { form: 'contact' });
      // Echte conversion gebeurt na redirect naar ?sent=1 (zie boven)
    });
  }

  // === Foto-upload preview ===
  // Houdt eigen lijst bij omdat <input type=file> niet ondersteunt om individuele
  // bestanden te verwijderen via DOM. We maken een DataTransfer en assignen .files.
  const photoInput    = document.getElementById('photoInput');
  const photoPreviews = document.getElementById('photoPreviews');
  const photoDrop     = document.getElementById('photoDrop');
  const photoError    = document.getElementById('photoError');
  if (photoInput && photoPreviews && photoDrop) {
    const MAX_PHOTOS = 5;
    const MAX_BYTES  = 8 * 1024 * 1024;
    const ALLOWED    = /^image\/(jpeg|png|webp|heic|heif)$/i;
    let chosen = []; // File[]

    function showError(msg) {
      if (!photoError) return;
      if (!msg) { photoError.hidden = true; photoError.textContent = ''; return; }
      photoError.hidden = false;
      photoError.textContent = msg;
    }

    function syncInput() {
      const dt = new DataTransfer();
      chosen.forEach(f => dt.items.add(f));
      photoInput.files = dt.files;
    }

    function render() {
      photoPreviews.innerHTML = '';
      photoPreviews.hidden = chosen.length === 0;
      chosen.forEach((file, idx) => {
        const wrap = document.createElement('div');
        wrap.className = 'photo-preview';
        const img = document.createElement('img');
        img.alt = file.name;
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Verwijder foto');
        btn.textContent = '×';
        btn.addEventListener('click', (ev) => {
          ev.preventDefault();
          chosen.splice(idx, 1);
          syncInput();
          render();
        });
        wrap.appendChild(img);
        wrap.appendChild(btn);
        photoPreviews.appendChild(wrap);
      });
    }

    function addFiles(fileList) {
      showError('');
      const incoming = Array.from(fileList || []);
      let rejected = 0;
      for (const f of incoming) {
        if (chosen.length >= MAX_PHOTOS) { rejected++; continue; }
        if (!ALLOWED.test(f.type) && !/\.(jpe?g|png|webp|heic|heif)$/i.test(f.name)) { rejected++; continue; }
        if (f.size > MAX_BYTES) { rejected++; continue; }
        chosen.push(f);
      }
      syncInput();
      render();
      if (rejected > 0) {
        showError(rejected + ' bestand(en) overgeslagen — max 5 foto\'s, alleen JPG/PNG/WebP/HEIC tot 8 MB.');
      }
    }

    photoInput.addEventListener('change', () => {
      addFiles(photoInput.files);
      // Reset value zodat dezelfde file later opnieuw gekozen kan worden
      // (we synct daarna terug via syncInput)
    });

    ['dragenter','dragover'].forEach(ev => photoDrop.addEventListener(ev, e => {
      e.preventDefault(); e.stopPropagation();
      photoDrop.classList.add('is-drag');
    }));
    ['dragleave','drop'].forEach(ev => photoDrop.addEventListener(ev, e => {
      e.preventDefault(); e.stopPropagation();
      photoDrop.classList.remove('is-drag');
    }));
    photoDrop.addEventListener('drop', e => {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length) addFiles(dt.files);
    });
  }


  // ===== Sticky header on scroll =====
  const hdr = document.getElementById('siteHeader');
  if (hdr) {
    const onScroll = () => hdr.classList.toggle('scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // ===== Mobile menu toggle =====
  const toggle = document.getElementById('menuToggle');
  const menu   = document.getElementById('mobileMenu');
  if (toggle && menu) {
    let open = false;
    const setOpen = (v) => {
      open = v;
      menu.classList.toggle('open', v);
      document.body.classList.toggle('menu-open', v);
      toggle.setAttribute('aria-expanded', v ? 'true' : 'false');
      document.body.style.overflow = v ? 'hidden' : '';
      toggle.innerHTML = v
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6 L18 18 M18 6 L6 18"/></svg>'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7 H20 M4 12 H20 M4 17 H20"/></svg>';
    };
    toggle.addEventListener('click', () => setOpen(!open));
    // Close-knop in de menu zelf
    const closeBtn = document.getElementById('mobileMenuClose');
    if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));
    // Sluit als op een link wordt geklikt
    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
    // Sluit met Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && open) setOpen(false);
    });
  }

  // ===== Reveal-on-scroll =====
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('in');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    reveals.forEach(el => io.observe(el));
  }

  // ===== FAQ accordion =====
  document.querySelectorAll('[data-faq]').forEach(item => {
    const btn = item.querySelector('button');
    if (!btn) return;
    btn.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      // Sluit alle in dezelfde lijst
      const list = item.parentElement;
      list.querySelectorAll('[data-faq]').forEach(other => {
        other.classList.remove('open');
        const ob = other.querySelector('button');
        if (ob) ob.setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // ===== SVG-icoon helper =====
  function svgIcon(name, size, color) {
    const c = color || 'currentColor';
    const s = size || 22;
    const svgs = {
      Building: '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7 H10 M14 7 H15 M9 11 H10 M14 11 H15 M9 15 H10 M14 15 H15"/><path d="M11 21 V18 H13 V21"/>',
      Houses:   '<path d="M3 12 L7 8 L11 12 V20 H3 Z"/><path d="M11 12 L15 8 L19 12 M21 12 L17 8"/><path d="M11 20 H21 V12"/>',
      HouseDouble: '<path d="M2 12 L7 7 L12 12 V20 H2 Z"/><path d="M12 12 L17 7 L22 12 V20 H12 Z"/>',
      HouseStand:  '<path d="M4 11 L12 4 L20 11 V20 H4 Z"/><path d="M9 20 V14 H15 V20"/><path d="M11 14 V20"/>',
      Camera:   '<path d="M3 8 H7 L9 5 H15 L17 8 H21 V19 H3 Z"/><circle cx="12" cy="13" r="4"/>',
      Doc:      '<path d="M6 3 H14 L18 7 V21 H6 Z"/><path d="M14 3 V7 H18"/><path d="M9 12 H15 M9 16 H15"/>',
      Spray:    '<rect x="8" y="9" width="7" height="12" rx="1"/><path d="M9 9 V6 H14 V9"/><path d="M14 4 H18 M14 7 H19 M16 10 H20"/>',
      Shield:   '<path d="M12 3 L4 6 V12 C4 16.5 7.5 19.8 12 21 C16.5 19.8 20 16.5 20 12 V6 Z"/><path d="M9 12 l2 2 4-4"/>',
      Sparkle:  '<path d="M12 4 L13.5 9 L18 10.5 L13.5 12 L12 17 L10.5 12 L6 10.5 L10.5 9 Z"/><path d="M19 4 L19.5 5.5 L21 6 L19.5 6.5 L19 8 L18.5 6.5 L17 6 L18.5 5.5 Z"/><path d="M5 16 L5.5 17.5 L7 18 L5.5 18.5 L5 20 L4.5 18.5 L3 18 L4.5 17.5 Z"/>',
      Check:    '<path d="M5 12 L10 17 L19 7"/>',
    };
    const g = svgs[name] || svgs.Building;
    return `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none" stroke="${c}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${g}</svg>`;
  }

  // ===== Hero price picker =====
  const picker = document.getElementById('heroPicker');
  if (picker) {
    let prices = [];
    try { prices = JSON.parse(picker.dataset.prices || '[]'); } catch (e) { prices = []; }
    const list = document.getElementById('priceTileList');
    const cta  = document.getElementById('heroQuoteBtn');
    let active = Math.min(1, prices.length - 1);

    function render() {
      if (!list) return;
      list.innerHTML = prices.map((p, i) => `
        <div class="price-tile${i === active ? ' active' : ''}" data-i="${i}" role="button" tabindex="0">
          <div class="price-tile-icon">${svgIcon(p.icon || 'Building', 20, '#d6c1f5')}</div>
          <div>
            <div class="price-tile-name">${escapeHtml(p.name)}</div>
            <div class="price-tile-meta">${escapeHtml(p.meta || '')}</div>
          </div>
          <div class="price-tile-price">
            ${p.price ? '€' + p.price : '—'}
            <small>${p.price ? 'vanaf' : 'op aanvraag'}</small>
          </div>
        </div>
      `).join('');
      list.querySelectorAll('.price-tile').forEach(tile => {
        tile.addEventListener('click', () => { active = +tile.dataset.i; render(); });
        tile.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); active = +tile.dataset.i; render(); }
        });
      });
      if (cta && prices[active]) cta.textContent = 'Offerte voor ' + prices[active].name;
    }
    render();
  }

  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ===== Process step picker =====
  const procWrap = document.getElementById('processWrap');
  if (procWrap) {
    let steps = [];
    try { steps = JSON.parse(procWrap.dataset.process || '[]'); } catch (e) {}
    const list   = document.getElementById('processList');
    const detail = document.getElementById('processDetail');
    let active = 0;

    function pad(n) { return String(n).padStart(2, '0'); }
    function renderList() {
      if (!list) return;
      list.innerHTML = steps.map((s, i) => `
        <div class="process-step${i === active ? ' active' : ''}" data-i="${i}" role="button" tabindex="0">
          <div class="step-num">${pad(i+1)}</div>
          <div>
            <h3>${escapeHtml(s.title)}</h3>
            <p>${escapeHtml(s.short)}</p>
          </div>
        </div>
      `).join('');
      list.querySelectorAll('.process-step').forEach(el => {
        el.addEventListener('click', () => { active = +el.dataset.i; renderList(); renderDetail(); });
        el.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); active = +el.dataset.i; renderList(); renderDetail(); }
        });
      });
    }
    function renderDetail() {
      if (!detail || !steps[active]) return;
      const s = steps[active];
      const detailsHtml = (s.details || []).map(d =>
        '<div>' + svgIcon('Check', 16) + escapeHtml(d) + '</div>'
      ).join('');
      detail.innerHTML = `
        <div class="process-detail-num">${pad(active+1)}</div>
        <h3>${escapeHtml(s.title)}</h3>
        <p>${escapeHtml(s.long)}</p>
        <div class="features">${detailsHtml}</div>
        <div style="margin-top:32px; position:relative;">
          <a href="${(window.SPINGUARD_WA || '#')}" target="_blank" rel="noopener" class="btn btn-primary">
            Start uw aanvraag
          </a>
        </div>
      `;
    }
    renderList(); renderDetail();
  }

  // WA-link voor process button
  const waBtn = document.querySelector('.fab');
  if (waBtn) window.SPINGUARD_WA = waBtn.getAttribute('href');

  // ===== Quick Quote Calculator =====
  const calc = document.getElementById('quickQuote');
  if (calc) {
    const state = { type: null, basePrice: 0, floors: 0, sides: 0 };
    const steps = calc.querySelectorAll('.calc-step');
    const result = document.getElementById('calcResult');
    const priceEl = document.getElementById('calcPrice');
    const quoteBtn = document.getElementById('calcQuoteBtn');
    const resetBtn = document.getElementById('calcReset');

    function showStep(n) {
      steps.forEach(s => s.style.display = (parseInt(s.dataset.step) === n ? 'block' : 'none'));
    }
    function updatePrice() {
      const total = state.basePrice + state.floors + state.sides;
      if (state.basePrice === 0) {
        priceEl.textContent = 'Op aanvraag';
      } else {
        priceEl.textContent = '€' + total + ',-';
      }
    }
    calc.querySelectorAll('.calc-options').forEach(group => {
      const field = group.dataset.field;
      group.querySelectorAll('.calc-opt').forEach(btn => {
        btn.addEventListener('click', () => {
          group.querySelectorAll('.calc-opt').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          if (field === 'type') {
            state.type = btn.dataset.value;
            state.basePrice = parseInt(btn.dataset.price) || 0;
            setTimeout(() => showStep(2), 250);
          } else if (field === 'floors') {
            state.floors = parseInt(btn.dataset.modifier) || 0;
            setTimeout(() => showStep(3), 250);
          } else if (field === 'sides') {
            state.sides = parseInt(btn.dataset.modifier) || 0;
            updatePrice();
            setTimeout(() => {
              steps.forEach(s => s.style.display = 'none');
              result.style.display = 'block';
              result.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 250);
          }
        });
      });
    });
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        state.basePrice = 0; state.floors = 0; state.sides = 0;
        result.style.display = 'none';
        calc.querySelectorAll('.calc-opt').forEach(b => b.classList.remove('active'));
        showStep(1);
      });
    }
  }

  // ===== Gallery filter =====
  document.querySelectorAll('.gf-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;
      document.querySelectorAll('.gf-btn').forEach(b => b.classList.toggle('active', b === btn));
      document.querySelectorAll('.gallery-card').forEach(card => {
        if (filter === 'all' || card.dataset.category === filter) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ===== Before/After slider =====
  const ba = document.getElementById('baSlider');
  if (ba) {
    let dragging = false;
    function move(clientX) {
      const rect = ba.getBoundingClientRect();
      const p = Math.max(2, Math.min(98, ((clientX - rect.left) / rect.width) * 100));
      ba.style.setProperty('--pos', p + '%');
      // Update after side clip
      const after = ba.querySelector('.ba-after');
      if (after) after.style.clipPath = `inset(0 calc(100% - ${p}%) 0 0)`;
    }
    function start(e) {
      dragging = true;
      move(e.touches ? e.touches[0].clientX : e.clientX);
      e.preventDefault();
    }
    function drag(e) {
      if (!dragging) return;
      move(e.touches ? e.touches[0].clientX : e.clientX);
    }
    function stop() { dragging = false; }
    ba.addEventListener('mousedown', start);
    ba.addEventListener('touchstart', start, { passive: false });
    window.addEventListener('mousemove', drag);
    window.addEventListener('touchmove', drag, { passive: true });
    window.addEventListener('mouseup', stop);
    window.addEventListener('touchend', stop);
    // Init
    move(ba.getBoundingClientRect().left + ba.getBoundingClientRect().width * 0.5);
  }

  // ===========================================
  // Reviews slider (klantervaringen carousel)
  // ===========================================
  document.querySelectorAll('[data-slider]').forEach(slider => {
    const track = slider.querySelector('[data-slider-track]');
    const prev  = slider.querySelector('[data-slider-prev]');
    const next  = slider.querySelector('[data-slider-next]');
    const dotsWrap = slider.querySelector('[data-slider-dots]');
    if (!track) return;
    const slides = Array.from(track.children);
    if (!slides.length) return;

    function pageWidth() {
      const first = slides[0];
      if (!first) return track.clientWidth;
      const styles = getComputedStyle(track);
      const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
      return first.getBoundingClientRect().width + gap;
    }
    function visibleCount() {
      return Math.max(1, Math.round(track.clientWidth / pageWidth()));
    }
    function totalPages() {
      return Math.max(1, slides.length - visibleCount() + 1);
    }
    function currentPage() {
      return Math.round(track.scrollLeft / pageWidth());
    }
    function goTo(page) {
      const max = totalPages() - 1;
      const p = Math.max(0, Math.min(max, page));
      track.scrollTo({ left: p * pageWidth(), behavior: 'smooth' });
    }

    if (prev) prev.addEventListener('click', () => goTo(currentPage() - 1));
    if (next) next.addEventListener('click', () => goTo(currentPage() + 1));

    function renderDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';
      const pages = totalPages();
      if (pages <= 1) { dotsWrap.style.display = 'none'; return; }
      dotsWrap.style.display = '';
      for (let i = 0; i < pages; i++) {
        const b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', `Ga naar pagina ${i+1}`);
        b.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(b);
      }
      updateDots();
    }
    function updateDots() {
      if (!dotsWrap) return;
      const cur = currentPage();
      Array.from(dotsWrap.children).forEach((d, i) => d.classList.toggle('is-active', i === cur));
    }
    track.addEventListener('scroll', () => {
      cancelAnimationFrame(track._raf);
      track._raf = requestAnimationFrame(updateDots);
    });

    function updateArrows() {
      const overflow = track.scrollWidth > track.clientWidth + 4;
      [prev, next].forEach(a => { if (a) a.style.display = overflow ? '' : 'none'; });
    }
    window.addEventListener('resize', () => { renderDots(); updateArrows(); });

    renderDots();
    updateArrows();
  });
})();
