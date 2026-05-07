/* SpinGuard Admin — vanilla JS */
(function () {
  'use strict';

  // ===========================================
  // Toast notifications
  // ===========================================
  function toast(message, type = 'success', duration = 3500) {
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.innerHTML = `
      <span>${message}</span>
      <button class="close" aria-label="Sluiten">×</button>
    `;
    document.body.appendChild(t);
    const close = () => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); };
    t.querySelector('.close').addEventListener('click', close);
    setTimeout(close, duration);
  }
  window.toast = toast;

  // ===========================================
  // Tabs — defensief, met event-delegation zodat ook child-clicks werken
  // ===========================================
  function showTab(id) {
    var tabsEl = document.getElementById('tabs');
    if (!tabsEl) return;
    var btns = tabsEl.querySelectorAll('button');
    for (var i = 0; i < btns.length; i++) {
      btns[i].classList.toggle('active', btns[i].getAttribute('data-tab') === id);
    }
    var panels = document.querySelectorAll('.tab-panel');
    for (var j = 0; j < panels.length; j++) {
      panels[j].classList.toggle('active', panels[j].getAttribute('data-panel') === id);
    }
    try { if (history.replaceState) history.replaceState(null, '', '#tab-' + id); } catch (e) {}
    var tabField = document.getElementById('currentTab');
    if (tabField) tabField.value = id;
    try { window.scrollTo(0, 0); } catch (e) {}
  }
  window.spinguardShowTab = showTab; // globaal beschikbaar als noodgreep

  (function initTabs(){
    var tabsEl = document.getElementById('tabs');
    if (!tabsEl) return;
    // Event-delegation: vangt klikken op buttons OF op hun child elementen (span, count, etc.)
    tabsEl.addEventListener('click', function(ev) {
      var target = ev.target;
      // Loop omhoog tot we een button met data-tab vinden
      while (target && target !== tabsEl) {
        if (target.tagName === 'BUTTON' && target.getAttribute('data-tab')) {
          showTab(target.getAttribute('data-tab'));
          return;
        }
        target = target.parentNode;
      }
    });
    // Initial hash
    var hash = (location.hash || '').replace('#tab-', '');
    if (hash) showTab(hash);
  })();

  // ===========================================
  // Repeater: remove / move / add
  // ===========================================
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('[data-action]');
    if (!btn) return;
    const item = btn.closest('.repeater-item');
    if (!item) return;
    const action = btn.dataset.action;
    if (action === 'remove') {
      if (confirm('Dit item verwijderen?')) {
        item.style.transition = 'opacity .2s, transform .2s';
        item.style.opacity = '0';
        item.style.transform = 'translateX(-10px)';
        setTimeout(() => { item.remove(); markUnsaved(); }, 200);
      }
    } else if (action === 'up') {
      const prev = item.previousElementSibling;
      if (prev && prev.classList.contains('repeater-item')) { item.parentNode.insertBefore(item, prev); markUnsaved(); }
    } else if (action === 'down') {
      const next = item.nextElementSibling;
      if (next && next.classList.contains('repeater-item')) { item.parentNode.insertBefore(next, item); markUnsaved(); }
    }
  });

  document.querySelectorAll('[data-add]').forEach(btn => {
    btn.addEventListener('click', () => {
      const kind = btn.dataset.add;
      const list = btn.previousElementSibling;
      if (!list) return;
      const idx = list.querySelectorAll('.repeater-item').length;
      const html = templateFor(kind, idx);
      if (html) {
        list.insertAdjacentHTML('beforeend', html);
        markUnsaved();
        const last = list.lastElementChild;
        last.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const firstInput = last.querySelector('input, textarea, select');
        if (firstInput) setTimeout(() => firstInput.focus(), 300);
      }
    });
  });

  function templateFor(kind, i) {
    const head = (label) => `
      <div class="ri-head"><strong>${label}</strong>
        <div class="ri-actions">
          <button type="button" class="icon-btn" data-action="up" title="Omhoog">↑</button>
          <button type="button" class="icon-btn" data-action="down" title="Omlaag">↓</button>
          <button type="button" class="icon-btn danger" data-action="remove" title="Verwijderen">×</button>
        </div>
      </div>`;
    const sel = (name, value, opts) => {
      const o = opts.map(v => `<option value="${v}"${v === value ? ' selected' : ''}>${v}</option>`).join('');
      return `<select name="${name}">${o}</select>`;
    };
    if (kind === 'price') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuw pakket')}
        <div class="field-row">
          <div class="field"><label>ID</label><input type="text" name="prices[${i}][id]" value="nieuw-${i}" /></div>
          <div class="field"><label>Naam</label><input type="text" name="prices[${i}][name]" /></div>
        </div>
        <div class="field-row">
          <div class="field"><label>Icoon</label>${sel(`prices[${i}][icon]`, 'Building', ['Building','Houses','HouseDouble','HouseStand','Home','Spider','Shield'])}</div>
          <div class="field"><label>Subtitel</label><input type="text" name="prices[${i}][meta]" /></div>
        </div>
        <div class="field-row">
          <div class="field"><label>Prijs €</label><input type="number" min="0" name="prices[${i}][price]" /></div>
          <div class="field"><label>Tekst i.p.v. prijs</label><input type="text" name="prices[${i}][price_label]" placeholder="Op aanvraag" /></div>
          <div class="field"><label>Uitgelicht?</label>${sel(`prices[${i}][featured]`, '0', ['0','1'])}</div>
        </div>
        <div class="field"><label>Kenmerken (één per regel)</label><textarea name="prices[${i}][features_text]" rows="3"></textarea></div>
      </div>`;
    }
    if (kind === 'process') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe stap')}
        <div class="field-row">
          <div class="field"><label>Titel</label><input type="text" name="process[${i}][title]" /></div>
          <div class="field"><label>Icoon</label>${sel(`process[${i}][icon]`, 'Camera', ['Camera','Doc','Spray','Sparkle','Shield','Check','Spider','Home'])}</div>
        </div>
        <div class="field"><label>Korte beschrijving</label><input type="text" name="process[${i}][short]" /></div>
        <div class="field"><label>Lange beschrijving</label><textarea name="process[${i}][long]" rows="3"></textarea></div>
        <div class="field"><label>Bullet-points (één per regel)</label><textarea name="process[${i}][details_text]" rows="3"></textarea></div>
      </div>`;
    }
    if (kind === 'benefit') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuw voordeel')}
        <div class="field-row">
          <div class="field"><label>ID</label><input type="text" name="benefits[${i}][id]" value="b${i+1}" /></div>
          <div class="field"><label>Icoon</label>${sel(`benefits[${i}][icon]`, 'Spider', ['Spider','Home','Clock','Leaf','Shield','Sparkle','Check'])}</div>
        </div>
        <div class="field"><label>Titel</label><input type="text" name="benefits[${i}][title]" /></div>
        <div class="field"><label>Tekst</label><textarea name="benefits[${i}][text]" rows="2"></textarea></div>
      </div>`;
    }
    if (kind === 'testimonial') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe review')}
        <div class="field"><label>Quote</label><textarea name="testimonials[${i}][quote]" rows="3"></textarea></div>
        <div class="field-row">
          <div class="field"><label>Naam</label><input type="text" name="testimonials[${i}][name]" /></div>
          <div class="field"><label>Locatie / type</label><input type="text" name="testimonials[${i}][loc]" /></div>
          <div class="field"><label>Initialen</label><input type="text" maxlength="3" name="testimonials[${i}][initials]" /></div>
        </div>
      </div>`;
    }
    if (kind === 'faq') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe vraag')}
        <div class="field"><label>Vraag</label><input type="text" name="faq[${i}][q]" /></div>
        <div class="field"><label>Antwoord</label><textarea name="faq[${i}][a]" rows="3"></textarea></div>
      </div>`;
    }
    if (kind === 'value') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe waarde')}
        <div class="field-row">
          <div class="field"><label>Icoon</label>${sel(`about[values][${i}][icon]`, 'Shield', ['Shield','Leaf','Clock','Sparkle','Check','Home','Spider'])}</div>
          <div class="field"><label>Titel</label><input type="text" name="about[values][${i}][title]" /></div>
        </div>
        <div class="field"><label>Tekst</label><input type="text" name="about[values][${i}][text]" /></div>
      </div>`;
    }
    if (kind === 'trustbadge') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe badge')}
        <div class="field-row">
          <div class="field"><label>Icoon</label>${sel(`homepage_extras[trust_badges_items][${i}][icon]`, 'Shield', ['Shield','Doc','Check','Leaf','Star','Sparkle','Spider','Home','Clock'])}</div>
          <div class="field"><label>Titel</label><input type="text" name="homepage_extras[trust_badges_items][${i}][title]" /></div>
          <div class="field"><label>Subtitel</label><input type="text" name="homepage_extras[trust_badges_items][${i}][subtitle]" /></div>
        </div>
      </div>`;
    }
    if (kind === 'gallery_item') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuw project')}
        <div class="field-row">
          <div class="field"><label>Titel</label><input type="text" name="gallery[items][${i}][title]" /></div>
          <div class="field"><label>Categorie</label><input type="text" name="gallery[items][${i}][category]" placeholder="Woningen" /></div>
          <div class="field"><label>Locatie</label><input type="text" name="gallery[items][${i}][location]" placeholder="Nijmegen" /></div>
        </div>
        <div class="field"><label>Beschrijving</label><textarea name="gallery[items][${i}][description]" rows="2"></textarea></div>
        <div class="field"><label>Foto-paden (één per regel)</label><textarea name="gallery[items][${i}][images_text]" rows="3" style="font-family:ui-monospace,monospace; font-size:13px;" placeholder="/uploads/foto1.jpg"></textarea></div>
      </div>`;
    }
    if (kind === 'custom_page') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe pagina')}
        <div class="field-row">
          <div class="field"><label>Key (URL-deel)</label><input type="text" name="custom_pages[items][${i}][key]" placeholder="mijn-pagina" /></div>
          <div class="field"><label>Eyebrow</label><input type="text" name="custom_pages[items][${i}][eyebrow]" /></div>
        </div>
        <div class="field"><label>Titel</label><input type="text" name="custom_pages[items][${i}][title]" /></div>
        <div class="field"><label>Intro</label><textarea name="custom_pages[items][${i}][intro]" rows="2"></textarea></div>
        <div class="field"><label>Meta description</label><input type="text" name="custom_pages[items][${i}][meta_description]" /></div>
        <div class="field-row">
          <div class="field"><label>Inhoud-type</label>
            <select name="custom_pages[items][${i}][mode]">
              <option value="markdown" selected>📝 Markdown (eenvoudig)</option>
              <option value="html">💻 HTML (volledige controle)</option>
            </select></div>
          <div class="field"><label>Layout</label>
            <select name="custom_pages[items][${i}][layout]">
              <option value="default" selected>Standaard (header, hero, footer)</option>
              <option value="blank">Blanco (alleen menu + footer)</option>
              <option value="raw">Volledig blanco (geen header/footer)</option>
            </select></div>
        </div>
        <div class="field"><label>Inhoud</label><textarea name="custom_pages[items][${i}][content]" rows="14" style="font-family:ui-monospace,monospace; font-size:13px;"></textarea></div>
        <details style="margin-top:8px;">
          <summary style="cursor:pointer; font-weight:600; font-size:13px; color:var(--violet-700); padding:6px 0;">🎨 Eigen CSS (optioneel)</summary>
          <div class="field" style="margin-top:8px;"><label>CSS</label><textarea name="custom_pages[items][${i}][custom_css]" rows="8" style="font-family:ui-monospace,monospace; font-size:13px;" placeholder=".my-class { color: red; }"></textarea></div>
        </details>
      </div>`;
    }
    if (kind === 'nav') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuw menu-item')}
        <div class="field-row">
          <div class="field"><label>Label</label><input type="text" name="navigation[items][${i}][label]" /></div>
          <div class="field"><label>URL</label><input type="text" name="navigation[items][${i}][url]" placeholder="/page.php" /></div>
          <div class="field"><label>Key</label><input type="text" name="navigation[items][${i}][key]" placeholder="unieke-id" /></div>
        </div>
      </div>`;
    }
    if (kind === 'trust') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe stat')}
        <div class="field-row">
          <div class="field"><label>Cijfer</label><input type="text" name="trust_strip[${i}][num]" /></div>
          <div class="field"><label>Label</label><input type="text" name="trust_strip[${i}][label]" /></div>
        </div>
      </div>`;
    }
    if (kind === 'timeline-item') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe stap')}
        <div class="field-row">
          <div class="field"><label>Tijd</label><input type="text" name="timeline[items][${i}][time]" placeholder="08:30" /></div>
          <div class="field"><label>Titel</label><input type="text" name="timeline[items][${i}][title]" /></div>
        </div>
        <div class="field"><label>Beschrijving</label><textarea name="timeline[items][${i}][text]" rows="2"></textarea></div>
      </div>`;
    }
    if (kind === 'material-tool') {
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe tegel')}
        <div class="field-row">
          <div class="field"><label>Icoon</label>${sel(`materials[tools][${i}][icon]`, 'Sparkle', ['Spray','Doc','Shield','Camera','Sparkle','Check','Leaf','Clock','Home','Spider'])}</div>
          <div class="field"><label>Titel</label><input type="text" name="materials[tools][${i}][title]" /></div>
          <div class="field"><label>Subtitel</label><input type="text" name="materials[tools][${i}][subtitle]" /></div>
        </div>
      </div>`;
    }
    if (kind === 'compare-row') {
      const cmpSel = (name, def) => `<select name="${name}">
        <option value="yes"${def==='yes'?' selected':''}>✓ Ja</option>
        <option value="soms"${def==='soms'?' selected':''}>± Soms</option>
        <option value="no"${def==='no'?' selected':''}>— Nee</option>
      </select>`;
      return `<div class="repeater-item" data-idx="${i}">${head('Nieuwe rij')}
        <div class="field"><label>Omschrijving</label><input type="text" name="compare[rows][${i}][label]" /></div>
        <div class="field-row">
          <div class="field"><label>Linker kolom</label>${cmpSel(`compare[rows][${i}][them]`, 'no')}</div>
          <div class="field"><label>Rechter kolom</label>${cmpSel(`compare[rows][${i}][us]`, 'yes')}</div>
        </div>
      </div>`;
    }
    return '';
  }

  // ===========================================
  // Form: re-index repeater items voor submit
  // ===========================================
  const form = document.getElementById('editForm');
  if (form) {
    form.addEventListener('submit', () => {
      ['prices-list','process-list','benefits-list','testim-list','faq-list','values-list','trust-list','nav-list','trust-badges-list','gallery-list','custom-list','compare-rows-list','materials-tools-list','timeline-list'].forEach(listId => {
        const list = document.getElementById(listId);
        if (!list) return;
        const items = list.querySelectorAll('.repeater-item');
        items.forEach((item, idx) => {
          item.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/\[(\d+)\]/, `[${idx}]`);
          });
          item.dataset.idx = idx;
        });
      });
      isSaving = true;
    });
  }

  // ===========================================
  // Unsaved changes detection
  // ===========================================
  let isDirty = false;
  let isSaving = false;
  const status = document.getElementById('saveStatus');

  function markUnsaved() {
    if (!form) return;
    isDirty = true;
    if (status) {
      status.classList.add('unsaved');
      status.querySelector('.label').textContent = 'Niet-opgeslagen wijzigingen';
    }
  }

  if (form) {
    // Detect changes
    form.addEventListener('input', markUnsaved);
    form.addEventListener('change', markUnsaved);

    // Warn before leaving with unsaved changes
    window.addEventListener('beforeunload', (e) => {
      if (isDirty && !isSaving) {
        e.preventDefault();
        e.returnValue = '';
        return '';
      }
    });
  }

  // ===========================================
  // Photo upload — robust drag & drop + click
  // ===========================================
  const upload = document.getElementById('uploadArea');
  const fileInput = document.getElementById('fileInput');
  const uploadForm = document.getElementById('uploadForm');

  if (upload && fileInput && uploadForm) {
    // Click op label triggert input automatisch (HTML for/label binding via nested input)
    // Zorg ervoor dat dit niet dubbel triggert:
    upload.addEventListener('click', (e) => {
      // Alleen voorkomen als klik op icon — laat label-default werken
    });

    // Drag & drop
    ['dragenter', 'dragover'].forEach(ev => {
      upload.addEventListener(ev, (e) => {
        e.preventDefault();
        e.stopPropagation();
        upload.classList.add('dragover');
      });
    });
    ['dragleave', 'drop'].forEach(ev => {
      upload.addEventListener(ev, (e) => {
        e.preventDefault();
        e.stopPropagation();
        upload.classList.remove('dragover');
      });
    });
    upload.addEventListener('drop', (e) => {
      if (e.dataTransfer.files.length) {
        // Validatie
        const f = e.dataTransfer.files[0];
        if (!f.type.startsWith('image/')) {
          toast('Alleen afbeeldingen zijn toegestaan.', 'error');
          return;
        }
        if (f.size > 10 * 1024 * 1024) {
          toast('Bestand te groot (max 10 MB).', 'error');
          return;
        }
        fileInput.files = e.dataTransfer.files;
        showUploading();
        uploadForm.submit();
      }
    });

    fileInput.addEventListener('change', () => {
      if (fileInput.files.length) {
        const f = fileInput.files[0];
        if (f.size > 10 * 1024 * 1024) {
          toast('Bestand te groot (max 10 MB).', 'error');
          fileInput.value = '';
          return;
        }
        showUploading();
        uploadForm.submit();
      }
    });

    function showUploading() {
      upload.querySelector('.upload-title').textContent = 'Bezig met uploaden…';
      upload.querySelector('.upload-sub').innerHTML = '<strong>Even geduld</strong>';
      upload.style.opacity = '.7';
      upload.style.pointerEvents = 'none';
    }
  }

  // ===========================================
  // Image Picker (modal)
  // ===========================================
  const pickerModal = document.getElementById('pickerModal');
  const pickerGrid = document.getElementById('pickerGrid');
  const pickerUpload = document.getElementById('pickerUpload');
  let pickerActiveInputId = null;
  let pickerPhotos = [];

  function openPicker(inputId) {
    if (!pickerModal) return;
    pickerActiveInputId = inputId;
    pickerModal.hidden = false;
    document.body.style.overflow = 'hidden';
    loadPickerPhotos();
  }
  function closePicker() {
    if (!pickerModal) return;
    pickerModal.hidden = true;
    document.body.style.overflow = '';
    pickerActiveInputId = null;
  }

  function loadPickerPhotos() {
    pickerGrid.innerHTML = '<div class="picker-loading">Foto\'s laden…</div>';
    fetch((window.SPINGUARD_BASE || '') + '/admin/api-list-photos.php', { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        pickerPhotos = data.photos || [];
        renderPickerGrid();
      })
      .catch(() => {
        pickerGrid.innerHTML = '<div class="picker-empty-grid"><p>Kon foto\'s niet laden.</p></div>';
      });
  }

  function renderPickerGrid() {
    if (!pickerPhotos.length) {
      pickerGrid.innerHTML = `
        <div class="picker-empty-grid">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M3 17 L9 13 L15 17 L21 13"/></svg>
          <p>Nog geen foto's. Upload er één hierboven.</p>
        </div>`;
      return;
    }
    const currentValue = pickerActiveInputId ? document.getElementById(pickerActiveInputId).value : '';
    pickerGrid.innerHTML = pickerPhotos.map(p => `
      <div class="picker-item ${p.path === currentValue ? 'selected' : ''}" data-path="${escapeAttr(p.path)}" data-url="${escapeAttr(p.url)}" title="${escapeAttr(p.name)}">
        <div class="picker-thumb" style="background-image:url('${escapeAttr(p.url)}')"></div>
        <div class="picker-name">${escapeHtml(p.name)}</div>
      </div>
    `).join('');
    pickerGrid.querySelectorAll('.picker-item').forEach(item => {
      item.addEventListener('click', () => selectPickerPhoto(item.dataset.path, item.dataset.url));
    });
  }

  function selectPickerPhoto(path, url) {
    if (!pickerActiveInputId) return;
    const input = document.getElementById(pickerActiveInputId);
    if (!input) return;
    input.value = path;
    updatePickerPreview(input, path, url);
    markUnsaved();
    closePicker();
    if (typeof toast === 'function') toast('Foto gekoppeld', 'success', 2000);
  }

  function updatePickerPreview(input, path, url) {
    const wrapper = input.closest('.image-picker');
    if (!wrapper) return;
    const preview = wrapper.querySelector('[data-picker-preview]');
    const clearBtn = wrapper.querySelector('[data-picker-clear]');
    const openBtns = wrapper.querySelectorAll('[data-picker-open]');
    if (path) {
      preview.classList.remove('is-empty');
      preview.classList.add('has-image');
      preview.style.backgroundImage = `url('${url || path}')`;
      const empty = preview.querySelector('.picker-empty-state');
      if (empty) empty.remove();
      // Voeg overlay toe als die ontbreekt
      if (!preview.querySelector('.picker-overlay')) {
        const inputId = input.id;
        const overlay = document.createElement('div');
        overlay.className = 'picker-overlay';
        overlay.innerHTML = `<button type="button" class="btn btn-light btn-sm" data-picker-open="${inputId}">Andere foto kiezen</button>`;
        preview.appendChild(overlay);
        overlay.querySelector('button').addEventListener('click', () => openPicker(inputId));
      }
      if (clearBtn) clearBtn.style.display = '';
      openBtns.forEach(b => {
        const txt = b.querySelector('svg') ? b.lastChild : b;
        if (b.textContent.trim().includes('Kies') || b.textContent.trim().includes('Foto')) {
          // Update text
          const svg = b.querySelector('svg');
          b.innerHTML = (svg ? svg.outerHTML : '') + ' Andere foto';
        }
      });
    } else {
      preview.classList.add('is-empty');
      preview.classList.remove('has-image');
      preview.style.backgroundImage = '';
      if (!preview.querySelector('.picker-empty-state')) {
        const empty = document.createElement('div');
        empty.className = 'picker-empty-state';
        empty.innerHTML = `
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M3 17 L9 13 L15 17 L21 13"/></svg>
          <span>Nog geen foto gekozen</span>`;
        preview.appendChild(empty);
      }
      if (clearBtn) clearBtn.style.display = 'none';
    }
  }

  function escapeAttr(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;'); }
  function escapeHtml(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

  // Open knoppen
  document.addEventListener('click', (ev) => {
    const openBtn = ev.target.closest('[data-picker-open]');
    if (openBtn) {
      ev.preventDefault();
      openPicker(openBtn.dataset.pickerOpen);
      return;
    }
    const clearBtn = ev.target.closest('[data-picker-clear]');
    if (clearBtn) {
      ev.preventDefault();
      const id = clearBtn.dataset.pickerClear;
      const input = document.getElementById(id);
      if (input) {
        input.value = '';
        updatePickerPreview(input, '', '');
        markUnsaved();
      }
      return;
    }
    const closeBtn = ev.target.closest('[data-picker-close]');
    if (closeBtn) { ev.preventDefault(); closePicker(); }
  });

  // Sluit met ESC
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape' && pickerModal && !pickerModal.hidden) closePicker();
  });

  // Upload binnen picker (AJAX)
  if (pickerUpload) {
    const uploadInput = pickerUpload.querySelector('input[type="file"]');
    pickerUpload.addEventListener('click', (e) => {
      // label-default → input click
    });
    uploadInput.addEventListener('change', () => {
      if (!uploadInput.files.length) return;
      const f = uploadInput.files[0];
      if (f.size > 10 * 1024 * 1024) { toast('Te groot (max 10 MB)', 'error'); return; }
      uploadPickerFile(f);
      uploadInput.value = '';
    });
    // Drag & drop op modal
    ['dragover', 'dragenter'].forEach(ev => {
      pickerUpload.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); pickerUpload.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
      pickerUpload.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); pickerUpload.classList.remove('dragover'); });
    });
    pickerUpload.addEventListener('drop', e => {
      if (e.dataTransfer.files.length) {
        const f = e.dataTransfer.files[0];
        if (!f.type.startsWith('image/')) { toast('Alleen afbeeldingen', 'error'); return; }
        if (f.size > 10 * 1024 * 1024) { toast('Te groot (max 10 MB)', 'error'); return; }
        uploadPickerFile(f);
      }
    });
  }

  async function uploadPickerFile(file) {
    pickerUpload.classList.add('uploading');
    pickerUpload.querySelector('strong').textContent = 'Bezig met uploaden…';
    try {
      const fd = new FormData();
      fd.append('photo', file);
      fd.append('_csrf', window.SPINGUARD_CSRF || '');
      const resp = await fetch((window.SPINGUARD_BASE || '') + '/admin/api-upload-photo.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      });
      const data = await resp.json();
      if (data.ok) {
        toast('Foto geüpload!', 'success');
        await new Promise(r => setTimeout(r, 200));
        loadPickerPhotos();
        // Auto-selecteer na 600ms
        setTimeout(() => {
          if (pickerActiveInputId) selectPickerPhoto(data.path, data.url);
        }, 600);
      } else {
        toast(data.error || 'Upload mislukt', 'error');
      }
    } catch (e) {
      toast('Upload mislukt: ' + e.message, 'error');
    } finally {
      pickerUpload.classList.remove('uploading');
      pickerUpload.querySelector('strong').textContent = 'Upload nieuwe foto';
    }
  }

  // ===========================================
  // Lead inbox actions
  // ===========================================
  const leadsList = document.querySelector('.leads-list');
  if (leadsList) {
    const csrf = leadsList.dataset.csrf;
    const apiUrl = (window.SPINGUARD_BASE || '') + '/admin/api-update-lead.php';

    async function postLead(payload) {
      const fd = new FormData();
      fd.append('_csrf', csrf);
      Object.entries(payload).forEach(([k,v]) => fd.append(k, v));
      try {
        const r = await fetch(apiUrl, { method:'POST', body:fd, credentials:'same-origin' });
        return await r.json();
      } catch (e) { return { ok: false, error: 'Netwerkfout' }; }
    }

    function getCurrentValue(card, selector, prefix='') {
      const el = card.querySelector(selector);
      if (!el) return '';
      return (el.textContent || '').replace(prefix, '').trim();
    }

    // Click acties: note / followup / tags / delete
    leadsList.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-lead-action]');
      if (!btn || btn.tagName === 'SELECT') return;
      const card = btn.closest('.lead-card');
      if (!card) return;
      const id = card.dataset.id;
      const action = btn.dataset.leadAction;

      if (action === 'delete') {
        if (!confirm('Deze aanvraag definitief verwijderen?')) return;
        const d = await postLead({ id, action: 'delete' });
        if (d.ok) {
          card.style.transition = 'opacity .2s, transform .2s';
          card.style.opacity = '0'; card.style.transform = 'translateX(-20px)';
          setTimeout(() => { card.remove(); if (typeof toast === 'function') toast('Verwijderd', 'success'); }, 200);
        }
      } else if (action === 'note') {
        const current = getCurrentValue(card, '.lead-note', '📝');
        const note = prompt('Notitie bij deze lead:', current);
        if (note === null) return;
        const d = await postLead({ id, note });
        if (d.ok) { if (typeof toast === 'function') toast('Notitie opgeslagen', 'success'); setTimeout(() => location.reload(), 600); }
      } else if (action === 'followup') {
        var _fEl = card.querySelector('.lead-link[style*="fff3d9"]');
        const cur = (_fEl ? _fEl.textContent : '').replace('⏰', '').trim();
        const today = new Date().toISOString().slice(0,10);
        const input = prompt('Follow-up datum (YYYY-MM-DD), leeg om te wissen:', cur || today);
        if (input === null) return;
        const v = input.trim();
        if (v !== '' && !/^\d{4}-\d{2}-\d{2}$/.test(v)) {
          alert('Gebruik formaat YYYY-MM-DD, bv. 2026-05-15');
          return;
        }
        const d = await postLead({ id, follow_up_date: v });
        if (d.ok) { if (typeof toast === 'function') toast(v ? 'Follow-up gepland' : 'Follow-up gewist', 'success'); setTimeout(() => location.reload(), 600); }
      } else if (action === 'tags') {
        const existing = Array.from(card.querySelectorAll('.lead-tag')).map(el => el.textContent.trim()).join(', ');
        const input = prompt('Tags (komma-gescheiden, bv. "spoed, particulier"):', existing);
        if (input === null) return;
        const d = await postLead({ id, tags: input });
        if (d.ok) { if (typeof toast === 'function') toast('Tags opgeslagen', 'success'); setTimeout(() => location.reload(), 600); }
      }
    });

    // Status dropdown — change event
    leadsList.addEventListener('change', async (e) => {
      const sel = e.target.closest('.lead-status-select');
      if (!sel) return;
      const card = sel.closest('.lead-card');
      if (!card) return;
      const id = card.dataset.id;
      const newStatus = sel.value;
      var _statusEl = card.querySelector('.lead-status');
      var _match = _statusEl ? _statusEl.classList.value.match(/status-(\w+)/) : null;
      const oldStatus = (_match && _match[1]) ? _match[1] : 'nieuw';

      const payload = { id, status: newStatus };

      if (newStatus === 'gewonnen') {
        const v = prompt('💰 Dealwaarde (€) — laat leeg voor 0:', '');
        if (v === null) { sel.value = oldStatus; return; }
        const num = parseFloat((v || '0').replace(',', '.'));
        if (!isNaN(num) && num >= 0) payload.sale_value = num;
      }
      if (newStatus === 'verloren') {
        const reason = prompt('❌ Reden waarom verloren? (optioneel)', '');
        if (reason === null) { sel.value = oldStatus; return; }
        payload.lost_reason = reason;
      }

      const d = await postLead(payload);
      if (d.ok) {
        if (typeof toast === 'function') toast('Status: ' + newStatus, 'success');
        setTimeout(() => location.reload(), 700);
      } else {
        sel.value = oldStatus;
        if (typeof toast === 'function') toast('Fout: ' + (d.error || 'onbekend'), 'error');
      }
    });
  }

  // ===========================================
  // Lead handmatig toevoegen (modal)
  // ===========================================
  const addLeadBtn = document.getElementById('openAddLead');
  const addLeadModal = document.getElementById('addLeadModal');
  if (addLeadBtn && addLeadModal) {
    const form = document.getElementById('addLeadForm');
    const submitBtn = document.getElementById('addLeadSubmit');
    function openModal() {
      addLeadModal.hidden = false;
      document.body.style.overflow = 'hidden';
      const firstInput = form.querySelector('input[name="name"]');
      if (firstInput) setTimeout(() => firstInput.focus(), 100);
    }
    function closeModal() {
      addLeadModal.hidden = true;
      document.body.style.overflow = '';
      form.reset();
    }
    addLeadBtn.addEventListener('click', openModal);
    addLeadModal.addEventListener('click', (e) => {
      if (e.target.closest('[data-close]')) closeModal();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !addLeadModal.hidden) closeModal();
    });
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      submitBtn.disabled = true;
      submitBtn.textContent = 'Bezig…';
      try {
        const fd = new FormData(form);
        const r = await fetch((window.SPINGUARD_BASE || '') + '/admin/api-create-lead.php', {
          method: 'POST', body: fd, credentials: 'same-origin'
        });
        const d = await r.json();
        if (d.ok) {
          if (typeof toast === 'function') toast('Lead toegevoegd', 'success');
          closeModal();
          setTimeout(() => location.reload(), 600);
        } else {
          if (typeof toast === 'function') toast('Fout: ' + (d.error || 'onbekend'), 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Lead opslaan';
        }
      } catch (err) {
        if (typeof toast === 'function') toast('Netwerkfout', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Lead opslaan';
      }
    });
  }

  // ===========================================
  // Lead bulk actions
  // ===========================================
  const bulkBar = document.getElementById('bulkActions');
  const bulkCount = document.getElementById('bulkCount');
  const selectAll = document.getElementById('selectAllLeads');
  const bulkDeselect = document.getElementById('bulkDeselect');
  if (bulkBar && leadsList) {
    const csrf = leadsList.dataset.csrf;
    function getSelected() {
      return Array.from(document.querySelectorAll('.lead-select:checked'))
        .map(cb => cb.closest('.lead-card'))
        .filter(Boolean);
    }
    function updateBulkBar() {
      const sel = getSelected();
      if (sel.length === 0) {
        bulkBar.style.display = 'none';
      } else {
        bulkBar.style.display = 'flex';
        bulkCount.textContent = sel.length + (sel.length === 1 ? ' geselecteerd' : ' geselecteerd');
      }
    }
    document.addEventListener('change', (e) => {
      if (e.target.matches('.lead-select')) updateBulkBar();
    });
    if (selectAll) {
      selectAll.addEventListener('change', () => {
        document.querySelectorAll('.lead-select').forEach(cb => { cb.checked = selectAll.checked; });
        updateBulkBar();
      });
    }
    if (bulkDeselect) {
      bulkDeselect.addEventListener('click', () => {
        document.querySelectorAll('.lead-select').forEach(cb => cb.checked = false);
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
      });
    }
    bulkBar.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-bulk]');
      if (!btn) return;
      const action = btn.dataset.bulk;
      const cards = getSelected();
      if (cards.length === 0) return;
      if (action === 'delete' && !confirm(`${cards.length} leads definitief verwijderen?`)) return;

      let done = 0;
      for (const card of cards) {
        const fd = new FormData();
        fd.append('_csrf', csrf);
        fd.append('id', card.dataset.id);
        if (action === 'delete') {
          fd.append('action', 'delete');
        } else {
          fd.append('status', action);
        }
        try {
          const r = await fetch((window.SPINGUARD_BASE || '') + '/admin/api-update-lead.php', { method:'POST', body:fd, credentials:'same-origin' });
          if ((await r.json()).ok) done++;
        } catch (e) {}
      }
      if (typeof toast === 'function') toast(`${done}/${cards.length} bijgewerkt`, 'success');
      setTimeout(() => location.reload(), 700);
    });
  }

  // ===========================================
  // Color picker sync (color input ↔ text input)
  // ===========================================
  document.querySelectorAll('.color-picker').forEach(group => {
    const colorInput = group.querySelector('input[type="color"]');
    const textInput  = group.querySelector('input[type="text"]');
    if (colorInput && textInput) {
      colorInput.addEventListener('input', () => { textInput.value = colorInput.value; markUnsaved(); });
      textInput.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(textInput.value)) colorInput.value = textInput.value;
        markUnsaved();
      });
    }
  });

  // ===========================================
  // Copy-to-clipboard (foto paden)
  // ===========================================
  document.querySelectorAll('[data-copy]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const txt = btn.dataset.copy;
      try {
        await navigator.clipboard.writeText(txt);
        const orig = btn.innerHTML;
        btn.classList.add('copied');
        btn.innerHTML = '✓ Gekopieerd!';
        toast('Pad gekopieerd: ' + txt, 'success', 2500);
        setTimeout(() => {
          btn.classList.remove('copied');
          btn.innerHTML = orig;
        }, 1800);
      } catch (e) {
        // Fallback voor oudere browsers
        const ta = document.createElement('textarea');
        ta.value = txt;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); toast('Pad gekopieerd', 'success'); }
        catch (_) { toast('Kon niet kopiëren', 'error'); }
        ta.remove();
      }
    });
  });

  // ===========================================
  // Layout-picker (review weergave) — visuele state + Trustpilot card toggle
  // ===========================================
  try {
    var layoutRadios = document.querySelectorAll('.layout-picker input[type="radio"]');
    for (var i = 0; i < layoutRadios.length; i++) {
      (function(radio){
        radio.addEventListener('change', function() {
          var picker = radio.closest('.layout-picker');
          if (picker) {
            var opts = picker.querySelectorAll('.layout-option');
            for (var j = 0; j < opts.length; j++) opts[j].classList.remove('is-selected');
            var own = radio.closest('.layout-option');
            if (own) own.classList.add('is-selected');
          }
          var tpCard = document.getElementById('trustpilotCard');
          if (tpCard && radio.name === 'reviews_settings[layout]') {
            tpCard.style.display = radio.value === 'trustpilot' ? '' : 'none';
          }
        });
      })(layoutRadios[i]);
    }
  } catch (e) {
    if (window.console) console.warn('layout-picker init failed:', e);
  }

  // ===========================================
  // Foto-lightbox (admin/leads.php)
  // ===========================================
  (function initPhotoLightbox() {
    const box     = document.getElementById('photoLightbox');
    const img     = document.getElementById('photoLightboxImg');
    const counter = document.getElementById('photoLightboxCounter');
    const openBtn = document.getElementById('photoLightboxOpen');
    const closeBtn= document.getElementById('photoLightboxClose');
    const prevBtn = document.getElementById('photoLightboxPrev');
    const nextBtn = document.getElementById('photoLightboxNext');
    if (!box || !img) return;

    let currentList = []; // array van anchors uit huidige lead
    let currentIdx  = 0;

    function show(idx) {
      if (!currentList.length) return;
      currentIdx = (idx + currentList.length) % currentList.length;
      const a = currentList[currentIdx];
      const url = a.getAttribute('href');
      img.src = url;
      img.alt = a.querySelector('img')?.alt || '';
      openBtn.href = url;
      counter.textContent = (currentIdx + 1) + ' / ' + currentList.length;
      const single = currentList.length <= 1;
      prevBtn.hidden = single;
      nextBtn.hidden = single;
    }

    function open(list, idx) {
      currentList = Array.from(list);
      box.hidden = false;
      box.setAttribute('aria-hidden', 'false');
      document.body.classList.add('lightbox-open');
      show(idx);
    }

    function close() {
      box.hidden = true;
      box.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('lightbox-open');
      img.src = '';
      currentList = [];
    }

    // Click op een thumbnail → open lightbox met alle foto's uit DEZE lead-card
    document.addEventListener('click', (e) => {
      const a = e.target.closest('.lead-photo');
      if (!a) return;
      const card = a.closest('.lead-card');
      if (!card) return;
      const list = card.querySelectorAll('.lead-photo');
      const idx  = Array.from(list).indexOf(a);
      e.preventDefault();
      open(list, idx);
    });

    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', () => show(currentIdx - 1));
    nextBtn.addEventListener('click', () => show(currentIdx + 1));

    // Klik op donker backdrop (niet op figure) → sluiten
    box.addEventListener('click', (e) => {
      if (e.target === box) close();
    });

    // Toetsenbord: ← → Esc
    document.addEventListener('keydown', (e) => {
      if (box.hidden) return;
      if (e.key === 'Escape')      { e.preventDefault(); close(); }
      else if (e.key === 'ArrowLeft')  { e.preventDefault(); show(currentIdx - 1); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); show(currentIdx + 1); }
    });
  })();
})();
