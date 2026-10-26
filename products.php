<?php
$page_title = 'Menu | Blessed Snaks';
$active     = 'menu';
$solid_nav  = false;
require __DIR__ . '/includes/header.php';

$categories = get_categories();
$products   = get_products();

$counts = array_count_values(array_column($products, 'category_slug'));
?>
<main>
  <!-- ========================= PAGE HERO (video) ========================= -->
  <section class="page-hero">
    <video class="hero-video" autoplay loop muted playsinline preload="metadata" poster="<?= e(mixkit(47191, 'poster')) ?>">
      <source src="<?= e(mixkit(47191)) ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="container page-hero-inner">
      <nav class="breadcrumb fade-down" aria-label="Breadcrumb"><a href="index.php">Home</a><span>/</span><span aria-current="page">Menu</span></nav>
      <h1 class="fade-down" style="animation-delay:.1s">Our <em>Menu</em></h1>
      <p class="lead fade-up" style="animation-delay:.2s">Burgers, wings, pizza, drinks and desserts, cooked fresh the moment you order.</p>
      <form class="menu-search fade-up" style="animation-delay:.3s" role="search" onsubmit="return false">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" id="menu-search" placeholder="Search burgers, wings, shakes…" aria-label="Search the menu" autocomplete="off">
        <kbd>/</kbd>
      </form>
    </div>
  </section>

  <section class="section menu-section">
    <div class="container">
      <?php if (!$user): ?>
        <div class="notice">
          <span>Want to order? <a href="login.php?next=products.php%3Fcheckout%3D1">Log in</a> or <a href="register.php?next=products.php%3Fcheckout%3D1">create an account</a>. Your cart will be waiting.</span>
        </div>
      <?php endif; ?>

      <div class="menu-toolbar" id="menu-toolbar">
        <div id="filter-tabs" class="tabs" role="tablist" aria-label="Menu categories">
          <button class="tab active" data-filter="all" role="tab" aria-selected="true">All <span class="tab-count"><?= count($products) ?></span></button>
          <?php foreach ($categories as $cat): ?>
            <button class="tab" data-filter="<?= e($cat['slug']) ?>" role="tab" aria-selected="false">
              <?= e($cat['name']) ?> <span class="tab-count"><?= (int)($counts[$cat['slug']] ?? 0) ?></span>
            </button>
          <?php endforeach; ?>
          <button class="tab tab-fav" data-filter="favs" role="tab" aria-selected="false">&#9829; Saved <span class="tab-count" id="fav-count">0</span></button>
        </div>
        <div class="toolbar-right">
          <span id="result-count" class="muted small" aria-live="polite"><?= count($products) ?> items</span>
          <label class="sort">
            <span class="sr-only">Sort by</span>
            <select id="menu-sort">
              <option value="featured">Sort: Featured</option>
              <option value="price-asc">Price: low to high</option>
              <option value="price-desc">Price: high to low</option>
              <option value="name">Name: A to Z</option>
            </select>
          </label>
        </div>
      </div>

      <?php if ($products): ?>
        <div id="menu-grid" class="dish-grid">
          <?php foreach ($products as $p): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
        <div id="empty-state" class="empty empty-box" hidden>
          <div class="empty-icon">&#127828;</div>
          <h3>Nothing matches that yet</h3>
          <p class="muted">Try a different search or category.</p>
          <button type="button" id="clear-filters" class="btn btn-ghost btn-sm mt-16">Clear filters</button>
        </div>
      <?php else: ?>
        <p class="empty">No menu items yet. Import <code>schema.sql</code> and check your database settings in <code>config.php</code>.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ========================= PERKS ========================= -->
  <section class="section-tight perks-wrap">
    <div class="container perks">
      <div class="perk reveal-up">
        <span class="feature-icon"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg></span>
        <div><strong>Free delivery</strong><p class="muted small">On orders over <?= e(money(FREE_DELIVERY_OVER)) ?></p></div>
      </div>
      <div class="perk reveal-up" style="--d:.08s">
        <span class="feature-icon"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2.5M9 2h6"/></svg></span>
        <div><strong>~30 minutes</strong><p class="muted small">Average delivery time</p></div>
      </div>
      <div class="perk reveal-up" style="--d:.16s">
        <span class="feature-icon"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c.3 2.2-.8 3.4-1.9 4.6C9 8.8 8 10 8 12a4 4 0 0 0 8 0c0-1.2-.5-2-1-2.8 1.8.8 3 2.8 3 5A6 6 0 0 1 6 14.5C6 9 11 8 12 3Z"/></svg></span>
        <div><strong>Cooked to order</strong><p class="muted small">Never reheated</p></div>
      </div>
      <div class="perk reveal-up" style="--d:.24s">
        <span class="feature-icon"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="3"/><path d="M11 18h2"/></svg></span>
        <div><strong>Pay on delivery</strong><p class="muted small">Cash, MTN MoMo, Airtel Money</p></div>
      </div>
    </div>
  </section>

  <section class="section section-tight">
    <div class="container">
      <div class="offer offer-hot combo-banner reveal-up">
        <div>
          <p class="eyebrow light">Combo Deal</p>
          <h3>Burger + Fries + Drink, save 15%</h3>
          <p>Build it your way: any burger, a side of golden fries and an ice-cold drink.</p>
        </div>
        <button type="button" class="btn btn-light" data-filter-link="mains">Pick your burger</button>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
