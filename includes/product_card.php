<?php /** Expects $p: id, name, description, price, image, badge, category_slug, category_name */ ?>
<article class="dish-card reveal-up"
         data-product
         data-category="<?= e($p['category_slug']) ?>"
         data-category-name="<?= e($p['category_name'] ?? '') ?>"
         data-id="<?= (int)$p['id'] ?>"
         data-name="<?= e($p['name']) ?>"
         data-desc="<?= e($p['description']) ?>"
         data-price="<?= (int)$p['price'] ?>"
         data-image="<?= e($p['image']) ?>"
         data-badge="<?= e($p['badge'] ?? '') ?>">
  <div class="dish-img">
    <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
    <?php if (!empty($p['badge'])): ?>
      <span class="dish-badge"><?= e($p['badge']) ?></span>
    <?php endif; ?>
    <button type="button" class="fav-btn" aria-pressed="false" aria-label="Save <?= e($p['name']) ?> to favourites">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 20s-7.5-4.6-9.3-9.2C1.4 7.4 3.6 4 7 4c2 0 3.4 1 5 3 1.6-2 3-3 5-3 3.4 0 5.6 3.4 4.3 6.8C19.5 15.4 12 20 12 20Z"/></svg>
    </button>
    <button type="button" class="quick-view">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
      Quick view
    </button>
  </div>
  <div class="dish-body">
    <?php if (!empty($p['category_name'])): ?>
      <span class="dish-cat"><?= e($p['category_name']) ?></span>
    <?php endif; ?>
    <h3><?= e($p['name']) ?></h3>
    <p><?= e($p['description']) ?></p>
    <div class="dish-foot">
      <span class="price"><?= money($p['price']) ?></span>
      <div class="cart-ctl">
        <button type="button" class="btn btn-primary btn-sm add-to-cart" aria-label="Add <?= e($p['name']) ?> to cart">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
          Add
        </button>
        <div class="stepper" hidden>
          <button type="button" class="step-dec" aria-label="Remove one">&minus;</button>
          <span class="step-qty" aria-live="polite">0</span>
          <button type="button" class="step-inc" aria-label="Add one">+</button>
        </div>
      </div>
    </div>
  </div>
</article>
