(() => {
  'use strict';

  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const body = document.body;

  const LOGGED_IN  = body.dataset.loggedIn === '1';
  const USER_PHONE = body.dataset.phone || '';
  const FREE_OVER  = parseInt(body.dataset.freeOver, 10) || 0;
  const FEE        = parseInt(body.dataset.fee, 10) || 0;
  const CURRENCY   = body.dataset.currency || 'UGX';
  const CSRF       = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const REDUCED    = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const MAX_QTY    = 20;
  const fmt = n => `${CURRENCY} ${Number(n).toLocaleString('en-US')}`;
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const store = {
    get(key, fallback) { try { const v = JSON.parse(localStorage.getItem(key)); return v ?? fallback; } catch (e) { return fallback; } },
    set(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) {} }
  };

  /* ---------- Navbar: transparent -> solid on pages that start with a hero ---------- */
  const navbar = $('#navbar');
  if (navbar && navbar.dataset.solid === '0') {
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 40);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------- Mobile menu ---------- */
  const menuToggle = $('#menu-toggle'), mobileMenu = $('#mobile-menu');
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      mobileMenu.hidden = !mobileMenu.hidden;
      menuToggle.setAttribute('aria-expanded', String(!mobileMenu.hidden));
      if (navbar) navbar.classList.add('scrolled');
    });
    $$('a', mobileMenu).forEach(a => a.addEventListener('click', () => { mobileMenu.hidden = true; }));
  }

  /* ---------- Toast ---------- */
  const toastEl = $('#toast'); let toastTimer;
  function toast(msg) {
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('show'), 2400);
  }

  /* ---------- Scroll reveal, counters, lazy videos ---------- */
  const io = 'IntersectionObserver' in window ? new IntersectionObserver((entries, obs) => {
    entries.forEach(en => {
      if (!en.isIntersecting) return;
      const el = en.target;
      if (el.matches('.reveal-up')) el.classList.add('in');
      if (el.dataset.count !== undefined) countUp(el);
      if (el.matches('[data-lazy-video]')) loadVideo(el);
      obs.unobserve(el);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: .12 }) : null;

  function countUp(el) {
    const end = parseFloat(el.dataset.count), dec = parseInt(el.dataset.decimals || '0', 10);
    if (REDUCED || !end) return;
    const t0 = performance.now(), dur = 1400;
    const tick = now => {
      const p = Math.min(1, (now - t0) / dur), eased = 1 - Math.pow(1 - p, 3);
      el.textContent = (end * eased).toFixed(dec);
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  function loadVideo(video) {
    $$('source[data-src]', video).forEach(s => { s.src = s.dataset.src; s.removeAttribute('data-src'); });
    video.load();
    if (!REDUCED) video.play().catch(() => {});
  }

  function observe(els) {
    els.forEach(el => io ? io.observe(el) : (el.classList.add('in'), el.matches('[data-lazy-video]') && loadVideo(el)));
  }
  observe($$('.reveal-up, [data-count], [data-lazy-video]'));

  if (REDUCED) $$('video[autoplay]').forEach(v => { v.removeAttribute('autoplay'); v.pause(); });

  /* ---------- Cart state (kept in localStorage so it survives login) ---------- */
  const KEY = 'bs_cart';
  let cart = store.get(KEY, []);
  if (!Array.isArray(cart)) cart = [];
  cart = cart.filter(i => i && i.id && i.qty > 0);
  const save = () => store.set(KEY, cart);

  const countEl = $('#cart-count'), listEl = $('#cart-items');
  const subEl = $('#cart-subtotal'), delEl = $('#cart-delivery'), totalEl = $('#cart-total');
  const drawer = $('#cart-drawer'), overlay = $('#cart-overlay');
  const checkoutBtn = $('#checkout-btn'), checkoutForm = $('#checkout-form'), clearBtn = $('#clear-cart');
  const backBtn = $('#back-to-cart');
  const coError = $('#co-error'), orderDone = $('#order-done'), cartView = $('#cart-view'), cartFoot = $('#cart-foot');
  const fab = $('#cart-fab'), freeMeter = $('#free-meter');

  let stage = 'cart'; // 'cart' -> 'details'

  const productFrom = el => ({
    id: String(el.dataset.id),
    name: el.dataset.name,
    price: parseInt(el.dataset.price, 10),
    image: el.dataset.image || ''
  });
  const qtyOf = id => (cart.find(i => i.id === String(id)) || {}).qty || 0;

  function setQty(product, qty) {
    qty = Math.max(0, Math.min(MAX_QTY, qty));
    const found = cart.find(i => i.id === product.id);
    if (found) found.qty = qty;
    else if (qty > 0) cart.push({ ...product, qty });
    cart = cart.filter(i => i.qty > 0);
    save(); render();
  }

  function totals() {
    const subtotal = cart.reduce((n, i) => n + i.qty * i.price, 0);
    const fee = subtotal === 0 ? 0 : (subtotal >= FREE_OVER ? 0 : FEE);
    return { subtotal, fee, total: subtotal + fee };
  }

  function setStage(next) {
    stage = next;
    checkoutForm.hidden = next !== 'details';
    backBtn.hidden = next !== 'details';
    checkoutBtn.textContent = next === 'details' ? 'Place Order' : (LOGGED_IN ? 'Checkout' : 'Log in to order');
    if (next === 'details') {
      const phone = $('#co-phone');
      if (!phone.value && USER_PHONE) phone.value = USER_PHONE;
      checkoutForm.scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth', block: 'start' });
      setTimeout(() => $('#co-address').focus({ preventScroll: true }), 350);
    }
  }

  function render() {
    const { subtotal, fee, total } = totals();
    const qty = cart.reduce((n, i) => n + i.qty, 0);
    countEl.textContent = qty;
    $('#drawer-count').textContent = qty ? `(${qty})` : '';
    subEl.textContent = fmt(subtotal);
    delEl.textContent = subtotal === 0 ? '–' : (fee === 0 ? 'Free' : fmt(fee));
    totalEl.textContent = fmt(total);
    checkoutBtn.disabled = qty === 0;
    clearBtn.hidden = qty === 0;

    // Free delivery meter
    const pct = FREE_OVER ? Math.min(100, subtotal / FREE_OVER * 100) : 100;
    $('#free-bar').style.width = pct + '%';
    freeMeter.classList.toggle('unlocked', subtotal >= FREE_OVER && subtotal > 0);
    $('#free-text').innerHTML = subtotal === 0
      ? `Free delivery on orders over <strong>${fmt(FREE_OVER)}</strong>.`
      : subtotal >= FREE_OVER
        ? '&#127881; You\'ve unlocked <strong>free delivery</strong>!'
        : `Add <strong>${fmt(FREE_OVER - subtotal)}</strong> more for free delivery.`;

    // Floating cart button
    fab.hidden = qty === 0;
    $('#fab-count').textContent = qty;
    $('#fab-total').textContent = fmt(total);

    syncCards();

    if (!cart.length) {
      listEl.innerHTML = `<li class="cart-empty">
        <div class="empty-icon">&#128722;</div>
        <p>Your cart is empty.</p><p class="muted small">Add something tasty from the menu!</p>
        <a href="products.php" class="btn btn-ghost btn-sm mt-16">Browse the menu</a></li>`;
      if (stage !== 'cart') setStage('cart');
      return;
    }
    listEl.innerHTML = cart.map(i => `
      <li class="cart-line" data-id="${esc(i.id)}">
        <img src="${esc(i.image)}" alt="">
        <div class="info">
          <p class="name">${esc(i.name)}</p>
          <p class="lp">${fmt(i.price)} <span class="muted">&times; ${i.qty}</span></p>
          <div class="qty">
            <button class="qty-dec" aria-label="Decrease quantity">&minus;</button>
            <span>${i.qty}</span>
            <button class="qty-inc" aria-label="Increase quantity"${i.qty >= MAX_QTY ? ' disabled' : ''}>+</button>
          </div>
        </div>
        <div class="line-end">
          <strong>${fmt(i.qty * i.price)}</strong>
          <button class="remove" aria-label="Remove ${esc(i.name)}">Remove</button>
        </div>
      </li>`).join('');
  }

  /* Cards show "Add" until the item is in the cart, then a live -/+ stepper. */
  function syncCards() {
    $$('.dish-card').forEach(card => {
      const q = qtyOf(card.dataset.id);
      const add = $('.add-to-cart', card), stepper = $('.stepper', card);
      if (!add || !stepper) return;
      add.hidden = q > 0;
      stepper.hidden = q === 0;
      $('.step-qty', stepper).textContent = q;
      $('.step-inc', stepper).disabled = q >= MAX_QTY;
      card.classList.toggle('in-cart', q > 0);
    });
  }

  /* ---------- Drawer open / close ---------- */
  let lastFocus = null;
  function openCart() {
    lastFocus = document.activeElement;
    overlay.hidden = false;
    requestAnimationFrame(() => { overlay.classList.add('open'); drawer.classList.add('open'); });
    drawer.setAttribute('aria-hidden', 'false');
    body.classList.add('no-scroll');
    setTimeout(() => $('#cart-close').focus(), 50);
  }
  function closeCart() {
    if (!drawer.classList.contains('open')) return;
    overlay.classList.remove('open'); drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    body.classList.remove('no-scroll');
    setTimeout(() => { overlay.hidden = true; }, 300);
    if (lastFocus) lastFocus.focus();
  }
  const showCart = () => { resetDoneView(); openCart(); };
  $('#cart-toggle').addEventListener('click', showCart);
  fab.addEventListener('click', showCart);
  $('#cart-close').addEventListener('click', closeCart);
  overlay.addEventListener('click', closeCart);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeCart(); closeQuickView(); } });

  function resetDoneView() {
    orderDone.hidden = true; cartView.hidden = false; cartFoot.hidden = false; freeMeter.hidden = false;
  }

  /* ---------- Fly-to-cart animation ---------- */
  function flyToCart(fromEl) {
    const img = fromEl && (fromEl.matches('img') ? fromEl : $('img', fromEl));
    const target = $('#cart-toggle');
    if (REDUCED || !img || !target || !img.animate) return bumpCount();
    const a = img.getBoundingClientRect(), b = target.getBoundingClientRect();
    const ghost = img.cloneNode();
    ghost.className = 'fly-ghost';
    Object.assign(ghost.style, { left: a.left + 'px', top: a.top + 'px', width: a.width + 'px', height: a.height + 'px' });
    document.body.appendChild(ghost);
    const dx = b.left + b.width / 2 - (a.left + a.width / 2), dy = b.top + b.height / 2 - (a.top + a.height / 2);
    ghost.animate([
      { transform: 'translate(0,0) scale(1)', opacity: .95, borderRadius: '18px' },
      { transform: `translate(${dx * .55}px, ${dy * .55 - 80}px) scale(.45)`, opacity: .9, offset: .55 },
      { transform: `translate(${dx}px, ${dy}px) scale(.08)`, opacity: .3, borderRadius: '50%' }
    ], { duration: 750, easing: 'cubic-bezier(.5,0,.3,1)' }).onfinish = () => { ghost.remove(); bumpCount(); };
  }
  function bumpCount() {
    countEl.classList.remove('bump'); void countEl.offsetWidth; countEl.classList.add('bump');
  }

  /* ---------- Add / change items from cards ---------- */
  document.addEventListener('click', e => {
    const holder = e.target.closest('[data-product]');
    if (!holder) return;
    const product = productFrom(holder);

    if (e.target.closest('.add-to-cart')) {
      if (qtyOf(product.id) >= MAX_QTY) return toast(`You can order up to ${MAX_QTY} of each item`);
      setQty(product, qtyOf(product.id) + 1);
      flyToCart($('.dish-img img', holder) || $('img', holder));
      toast(`${product.name} added to cart`);
    } else if (e.target.closest('.step-inc')) {
      setQty(product, qtyOf(product.id) + 1); bumpCount();
    } else if (e.target.closest('.step-dec')) {
      setQty(product, qtyOf(product.id) - 1);
      if (!qtyOf(product.id)) toast(`${product.name} removed`);
    } else if (e.target.closest('.quick-view')) {
      openQuickView(holder);
    } else if (e.target.closest('.fav-btn')) {
      toggleFav(holder);
    }
  });

  listEl.addEventListener('click', e => {
    const li = e.target.closest('.cart-line'); if (!li) return;
    const item = cart.find(i => i.id === li.dataset.id); if (!item) return;
    if (e.target.closest('.qty-inc')) setQty(item, item.qty + 1);
    else if (e.target.closest('.qty-dec')) setQty(item, item.qty - 1);
    else if (e.target.closest('.remove')) {
      li.classList.add('leaving');
      setTimeout(() => { setQty(item, 0); toast(`${item.name} removed`); }, REDUCED ? 0 : 220);
    }
  });

  clearBtn.addEventListener('click', () => {
    if (!cart.length || !confirm('Remove everything from your cart?')) return;
    cart = []; save(); render();
  });
  backBtn.addEventListener('click', () => setStage('cart'));

  /* ---------- Favourites ---------- */
  const FAV_KEY = 'bs_favs';
  let favs = new Set((store.get(FAV_KEY, []) || []).map(String));
  function paintFavs() {
    $$('.dish-card').forEach(card => {
      const on = favs.has(card.dataset.id), btn = $('.fav-btn', card);
      if (btn) { btn.classList.toggle('on', on); btn.setAttribute('aria-pressed', String(on)); }
    });
    const fc = $('#fav-count'); if (fc) fc.textContent = favs.size;
  }
  function toggleFav(card) {
    const id = card.dataset.id;
    favs.has(id) ? favs.delete(id) : favs.add(id);
    store.set(FAV_KEY, [...favs]);
    paintFavs();
    const btn = $('.fav-btn', card);
    if (btn) { btn.classList.remove('pop'); void btn.offsetWidth; btn.classList.add('pop'); }
    toast(favs.has(id) ? `Saved ${card.dataset.name} to favourites` : 'Removed from favourites');
    if (menu.filter === 'favs') applyMenu();
  }

  /* ---------- Quick view modal ---------- */
  const qv = $('#qv');
  let qvProduct = null, qvQty = 1, qvSource = null;
  function paintQvQty() {
    $('#qv-qty').textContent = qvQty;
    $('#qv-add').textContent = `Add ${qvQty} to cart · ${fmt(qvQty * qvProduct.price)}`;
  }
  function openQuickView(card) {
    qvSource = card;
    qvProduct = productFrom(card);
    qvQty = 1;
    $('#qv-img').src = qvProduct.image;
    $('#qv-img').alt = qvProduct.name;
    $('#qv-title').textContent = qvProduct.name;
    $('#qv-desc').textContent = card.dataset.desc || '';
    $('#qv-cat').textContent = card.dataset.categoryName || '';
    $('#qv-price').textContent = fmt(qvProduct.price);
    const badge = $('#qv-badge');
    badge.textContent = card.dataset.badge || ''; badge.hidden = !card.dataset.badge;
    paintQvQty();
    lastFocus = document.activeElement;
    qv.hidden = false;
    requestAnimationFrame(() => qv.classList.add('open'));
    body.classList.add('no-scroll');
    setTimeout(() => $('#qv-add').focus(), 60);
  }
  function closeQuickView() {
    if (!qv || qv.hidden) return;
    qv.classList.remove('open');
    body.classList.remove('no-scroll');
    setTimeout(() => { qv.hidden = true; }, 250);
    if (lastFocus) lastFocus.focus();
  }
  if (qv) {
    qv.addEventListener('click', e => { if (e.target.closest('[data-close]')) closeQuickView(); });
    $('#qv-inc').addEventListener('click', () => { qvQty = Math.min(MAX_QTY, qvQty + 1); paintQvQty(); });
    $('#qv-dec').addEventListener('click', () => { qvQty = Math.max(1, qvQty - 1); paintQvQty(); });
    $('#qv-add').addEventListener('click', () => {
      setQty(qvProduct, qtyOf(qvProduct.id) + qvQty);
      flyToCart($('#qv-img'));
      toast(`${qvQty} × ${qvProduct.name} added to cart`);
      closeQuickView();
    });
  }

  /* ---------- 3D tilt + glare on cards (mouse only) ---------- */
  if (!REDUCED && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.addEventListener('pointermove', e => {
      const card = e.target.closest('.dish-card');
      if (!card) return;
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
      card.style.setProperty('--ry', ((x - .5) * 8).toFixed(2) + 'deg');
      card.style.setProperty('--rx', ((.5 - y) * 8).toFixed(2) + 'deg');
      card.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
      card.style.setProperty('--my', (y * 100).toFixed(1) + '%');
    });
    document.addEventListener('pointerout', e => {
      const card = e.target.closest('.dish-card');
      if (card && !card.contains(e.relatedTarget)) { card.style.removeProperty('--rx'); card.style.removeProperty('--ry'); }
    });
  }

  /* ---------- Menu: category tabs + search + sort (products page) ---------- */
  const grid = $('#menu-grid');
  const menu = { filter: 'all', query: '', sort: 'featured' };
  const cards = grid ? $$('.dish-card', grid) : [];
  cards.forEach((c, i) => { c.dataset.order = i; });

  function applyMenu() {
    if (!grid) return;
    const q = menu.query.trim().toLowerCase();
    let shown = 0;
    cards.forEach(card => {
      const inCat = menu.filter === 'all' || (menu.filter === 'favs' ? favs.has(card.dataset.id) : card.dataset.category === menu.filter);
      const hay = `${card.dataset.name} ${card.dataset.desc} ${card.dataset.categoryName}`.toLowerCase();
      const match = inCat && (!q || hay.includes(q));
      const wasHidden = card.classList.contains('hidden-card');
      card.classList.toggle('hidden-card', !match);
      if (match) {
        shown++;
        card.classList.add('in');
        if (wasHidden) { card.classList.remove('reveal'); void card.offsetWidth; card.classList.add('reveal'); }
      }
    });
    const sorters = {
      featured: (a, b) => a.dataset.order - b.dataset.order,
      'price-asc': (a, b) => a.dataset.price - b.dataset.price,
      'price-desc': (a, b) => b.dataset.price - a.dataset.price,
      name: (a, b) => a.dataset.name.localeCompare(b.dataset.name)
    };
    [...cards].sort(sorters[menu.sort]).forEach(c => grid.appendChild(c));
    $('#result-count').textContent = `${shown} item${shown === 1 ? '' : 's'}`;
    $('#empty-state').hidden = shown > 0;
  }

  function setFilter(f) {
    menu.filter = f;
    $$('.tab').forEach(t => {
      const on = t.dataset.filter === f;
      t.classList.toggle('active', on);
      t.setAttribute('aria-selected', String(on));
    });
    applyMenu();
  }

  if (grid) {
    $$('.tab').forEach(tab => tab.addEventListener('click', () => setFilter(tab.dataset.filter)));
    const search = $('#menu-search');
    let searchTimer;
    search.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => { menu.query = search.value; applyMenu(); }, 120);
    });
    search.addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); $('#menu-toolbar').scrollIntoView({ behavior: 'smooth' }); }
    });
    document.addEventListener('keydown', e => {
      if (e.key === '/' && !/input|textarea|select/i.test(document.activeElement.tagName)) { e.preventDefault(); search.focus(); }
    });
    $('#menu-sort').addEventListener('change', e => { menu.sort = e.target.value; applyMenu(); });
    $('#clear-filters').addEventListener('click', () => {
      search.value = ''; menu.query = ''; $('#menu-sort').value = 'featured'; menu.sort = 'featured'; setFilter('all');
    });
    $$('[data-filter-link]').forEach(btn => btn.addEventListener('click', () => {
      setFilter(btn.dataset.filterLink);
      $('#menu-toolbar').scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth' });
    }));
    const cat = new URLSearchParams(location.search).get('cat');
    if (cat && $(`.tab[data-filter="${CSS.escape(cat)}"]`)) {
      setFilter(cat);
      setTimeout(() => $('#menu-toolbar').scrollIntoView({ behavior: 'smooth' }), 400);
    }
  }

  /* ---------- Story player: chapters auto-advance like stories ---------- */
  const player = $('#story-player');
  if (player) {
    const video = $('.story-video', player);
    const chapters = $$('.chapter', player);
    const caption = $('.story-caption', player);
    const toggle = $('.story-toggle', player);
    const CHAPTER_MAX = 12;           // seconds shown per chapter
    let current = 0, wantPlay = false, inView = false;

    const limit = () => Math.min(video.duration || CHAPTER_MAX, CHAPTER_MAX);

    function show(i, autoplay) {
      current = (i + chapters.length) % chapters.length;
      const ch = chapters[current];
      chapters.forEach((c, n) => {
        c.classList.toggle('active', n === current);
        c.classList.toggle('seen', n < current);
        $('.chapter-bar i', c).style.width = n < current ? '100%' : '0%';
      });
      video.poster = ch.dataset.poster;
      video.src = ch.dataset.src;
      caption.classList.remove('swap'); void caption.offsetWidth; caption.classList.add('swap');
      $('.story-chapter', caption).textContent = `Chapter ${String(current + 1).padStart(2, '0')}`;
      $('.story-title', caption).textContent = ch.dataset.title;
      $('.story-text', caption).textContent = ch.dataset.text;
      $('.story-count b', player).textContent = String(current + 1).padStart(2, '0');
      if (autoplay) play();
    }
    function play() {
      wantPlay = true;
      if (!video.src) { show(current, false); }
      video.play().then(() => player.classList.add('playing')).catch(() => player.classList.remove('playing'));
      toggle.setAttribute('aria-label', 'Pause story');
    }
    function pause(user) {
      if (user) wantPlay = false;
      video.pause();
      player.classList.remove('playing');
      toggle.setAttribute('aria-label', 'Play story');
    }

    video.addEventListener('timeupdate', () => {
      const p = Math.min(1, video.currentTime / limit());
      $('.chapter-bar i', chapters[current]).style.width = (p * 100) + '%';
      if (video.currentTime >= limit()) show(current + 1, true);
    });
    video.addEventListener('ended', () => show(current + 1, true));
    chapters.forEach((c, i) => c.addEventListener('click', () => show(i, true)));
    toggle.addEventListener('click', () => (video.paused ? play() : pause(true)));
    $('.story-stage', player).addEventListener('click', e => {
      if (!e.target.closest('.story-toggle')) video.paused ? play() : pause(true);
    });

    // Start when the player scrolls into view, pause when it leaves.
    wantPlay = !REDUCED;
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(([en]) => {
        inView = en.isIntersecting;
        if (inView && wantPlay) play();
        else if (!inView) pause(false);
      }, { threshold: .45 }).observe(player);
    }
    document.addEventListener('visibilitychange', () => { if (document.hidden) pause(false); else if (inView && wantPlay) play(); });
  }

  /* ---------- Reviews slider ---------- */
  const reviews = $('#reviews');
  if (reviews) {
    const track = $('.reviews-track', reviews), slides = $$('.review', reviews), dots = $('.dots', reviews);
    let idx = 0, timer;
    dots.innerHTML = slides.map((_, i) => `<button type="button" role="tab" aria-label="Review ${i + 1}"></button>`).join('');
    const go = i => {
      idx = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${idx * 100}%)`;
      $$('button', dots).forEach((d, n) => d.setAttribute('aria-selected', String(n === idx)));
    };
    const auto = () => { clearInterval(timer); if (!REDUCED) timer = setInterval(() => go(idx + 1), 6000); };
    $('.rev-prev', reviews).addEventListener('click', () => { go(idx - 1); auto(); });
    $('.rev-next', reviews).addEventListener('click', () => { go(idx + 1); auto(); });
    $$('button', dots).forEach((d, i) => d.addEventListener('click', () => { go(i); auto(); }));
    reviews.addEventListener('mouseenter', () => clearInterval(timer));
    reviews.addEventListener('mouseleave', auto);
    let sx = null;
    track.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
      if (sx === null) return;
      const dx = e.changedTouches[0].clientX - sx;
      if (Math.abs(dx) > 40) { go(idx + (dx < 0 ? 1 : -1)); auto(); }
      sx = null;
    });
    go(0); auto();
  }

  /* ---------- Countdown to midnight (daily deal) ---------- */
  $$('[data-countdown]').forEach(el => {
    const pad = n => String(n).padStart(2, '0');
    const tick = () => {
      const now = new Date(), end = new Date(now); end.setHours(24, 0, 0, 0);
      let s = Math.max(0, Math.floor((end - now) / 1000));
      $('[data-h]', el).textContent = pad(Math.floor(s / 3600)); s %= 3600;
      $('[data-m]', el).textContent = pad(Math.floor(s / 60));
      $('[data-s]', el).textContent = pad(s % 60);
    };
    tick(); setInterval(tick, 1000);
  });

  /* ---------- "Open now" badge from the footer hours ---------- */
  const badge = $('#open-badge');
  if (badge) {
    const now = new Date(), day = now.getDay(), h = now.getHours() + now.getMinutes() / 60;
    const row = $$('#hours li').find(li => {
      const [a, b = a] = li.dataset.days.split('-').map(Number);
      return day >= a && day <= b;
    });
    if (row) {
      const open = h >= +row.dataset.open && h < +row.dataset.close;
      row.classList.add('today');
      badge.textContent = open ? 'Open now' : 'Closed';
      badge.classList.toggle('closed', !open);
      badge.hidden = false;
    }
  }

  /* ---------- Back to top ---------- */
  const toTop = $('#to-top');
  if (toTop) {
    const onScroll = () => toTop.classList.toggle('show', window.scrollY > 700);
    window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: REDUCED ? 'auto' : 'smooth' }));
  }

  /* ---------- Checkout (login required) ---------- */
  function goLogin() {
    window.location.href = 'login.php?next=' + encodeURIComponent('products.php?checkout=1');
  }

  function showError(msg) { coError.textContent = msg; coError.hidden = !msg; }

  checkoutBtn.addEventListener('click', async () => {
    if (!cart.length) return;

    // Must be signed in to order. The cart stays in localStorage while they log in.
    if (!LOGGED_IN) return goLogin();

    if (stage === 'cart') { setStage('details'); return; }

    const phone = $('#co-phone').value.trim();
    const address = $('#co-address').value.trim();
    const payment = ($('input[name="payment"]:checked', checkoutForm) || {}).value || 'cash';

    if (address.length < 5) return showError('Enter your delivery address.');
    if (!/^\+?[0-9 ]{9,15}$/.test(phone)) return showError('Enter a valid phone number.');
    showError('');

    checkoutBtn.disabled = true;
    const original = checkoutBtn.textContent;
    checkoutBtn.textContent = 'Placing order…';

    try {
      const res = await fetch('place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
        credentials: 'same-origin',
        body: JSON.stringify({
          items: cart.map(i => ({ id: parseInt(i.id, 10), qty: i.qty })),
          phone, address, payment
        })
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401 || data.error === 'login_required') return goLogin();
      if (!res.ok || !data.ok) {
        showError(data.error || 'Something went wrong. Please try again.');
        return;
      }

      // Success
      cart = []; save(); render(); setStage('cart');
      cartView.hidden = true; cartFoot.hidden = true; freeMeter.hidden = true; orderDone.hidden = false;
      $('#order-done-text').textContent =
        `Order #${data.order_id} · ${data.total_formatted}. We're preparing it now and will call you on ${phone} if we need anything.`;
    } catch (err) {
      showError('Network problem. Check your connection and try again.');
    } finally {
      checkoutBtn.disabled = cart.length === 0;
      if (stage === 'details') checkoutBtn.textContent = original;
      else setStage(stage);
    }
  });

  /* ---------- Newsletter (demo only; wire to your own handler) ---------- */
  const nl = $('#newsletter-form');
  if (nl) nl.addEventListener('submit', e => { e.preventDefault(); nl.reset(); toast('Thanks for subscribing!'); });

  /* ---------- Init ---------- */
  setStage('cart');
  render();
  paintFavs();

  // Keep cart badges in sync if the cart changes in another tab.
  window.addEventListener('storage', e => {
    if (e.key === KEY) { cart = store.get(KEY, []).filter(i => i && i.id && i.qty > 0); render(); }
    if (e.key === FAV_KEY) { favs = new Set(store.get(FAV_KEY, []).map(String)); paintFavs(); applyMenu(); }
  });

  // After logging in from the cart we land on products.php?checkout=1: reopen the cart at the details step.
  const params = new URLSearchParams(window.location.search);
  if (params.get('checkout') === '1') {
    if (cart.length) { openCart(); if (LOGGED_IN) setStage('details'); }
    history.replaceState(null, '', window.location.pathname);
  }
})();
