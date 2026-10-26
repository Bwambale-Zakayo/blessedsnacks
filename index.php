<?php
$page_title = 'Blessed Snaks | Gourmet Snacks & Fast Food Delivery';
$active     = 'home';
$solid_nav  = false;
require __DIR__ . '/includes/header.php';

$featured = get_products(8, true);
$pick     = $featured[0] ?? null;

$chapters = [
    ['id' => 40522, 'title' => 'Fresh from the market',
     'text' => 'Every morning starts at the market. We pick crisp greens, ripe tomatoes and quality meat, then wash and prep everything by hand.'],
    ['id' => 12041, 'title' => 'Fired on the grill',
     'text' => 'Your order hits the grill the moment you place it. Nothing sits under a heat lamp, and nothing is reheated.'],
    ['id' => 24601, 'title' => 'Packed hot, on the move',
     'text' => 'Sealed in insulated bags and handed straight to a rider, your food leaves the kitchen still sizzling.'],
    ['id' => 47978, 'title' => 'That first bite',
     'text' => 'About 30 minutes after you tap "Order", it\'s at your door. Hot, fresh and exactly how you like it.'],
];
?>
<main>
  <!-- ========================= HERO ========================= -->
  <section id="home" class="hero">
    <video class="hero-video" autoplay loop muted playsinline preload="metadata" poster="<?= e(mixkit(47159, 'poster')) ?>">
      <source src="<?= e(mixkit(47159)) ?>" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-veil"></div>

    <div class="container hero-grid">
      <div class="hero-copy">
        <span class="pill fade-down"><i class="dot"></i> Kitchen open &middot; delivering hot &amp; fresh</span>
        <h1 class="fade-down" style="animation-delay:.15s">Blessed Snaks, <em>blessed flavour.</em></h1>
        <p class="lead fade-up" style="animation-delay:.3s">Flame-grilled burgers, crispy golden snacks and ice-cold drinks, cooked to order in our Kampala kitchen and rushed to your door while it's still hot.</p>
        <div class="hero-btns fade-up" style="animation-delay:.45s">
          <a href="products.php" class="btn btn-primary btn-lg glow">Order Now</a>
          <a href="#story" class="btn btn-ghost btn-lg play-link">
            <span class="play-ring"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
            Watch our story
          </a>
        </div>
        <dl class="stats fade-up" style="animation-delay:.6s">
          <div><dt><span data-count="30">30</span><small>min</small></dt><dd>Avg. delivery</dd></div>
          <div><dt><span data-count="4.9" data-decimals="1">4.9</span>&#9733;</dt><dd>Customer rating</dd></div>
          <div><dt><span data-count="<?= max(20, count(get_products())) ?>">20</span>+</dt><dd>Menu items</dd></div>
        </dl>
      </div>

      <div class="hero-card-wrap fade-up" style="animation-delay:.5s">
        <?php if ($pick): ?>
          <div class="glass float-card pick-card" data-product
               data-id="<?= (int)$pick['id'] ?>" data-name="<?= e($pick['name']) ?>" data-price="<?= (int)$pick['price'] ?>"
               data-image="<?= e($pick['image']) ?>" data-desc="<?= e($pick['description']) ?>"
               data-category-name="<?= e($pick['category_name']) ?>">
            <span class="pick-tag">&#128293; Today's pick</span>
            <img src="<?= e($pick['image']) ?>" alt="<?= e($pick['name']) ?>" class="pick-img">
            <h2><?= e($pick['name']) ?></h2>
            <p class="glass-meta"><?= e($pick['description']) ?></p>
            <div class="pick-foot">
              <span class="price"><?= e(money($pick['price'])) ?></span>
              <button type="button" class="btn btn-primary btn-sm add-to-cart">Add to cart</button>
            </div>
          </div>
        <?php else: ?>
          <div class="glass float-card">
            <div class="glass-icon">&#127839;</div>
            <h2>Delicious dishes &amp; gourmet snacks delivered fresh to your doorstep</h2>
            <p class="glass-meta">Open daily &middot; 9:00 AM &ndash; 11:00 PM</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <a href="#featured" class="scroll-cue" aria-label="Scroll to menu"><span></span></a>
  </section>

  <!-- ========================= MARQUEE ========================= -->
  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <?php for ($i = 0; $i < 2; $i++): ?>
        <span>Flame-grilled burgers</span><i>&#10022;</i>
        <span>Crispy wings</span><i>&#10022;</i>
        <span>Golden fries</span><i>&#10022;</i>
        <span>Stone-baked pizza</span><i>&#10022;</i>
        <span>Thick milkshakes</span><i>&#10022;</i>
        <span>Fresh smoothies</span><i>&#10022;</i>
        <span>Sweet treats</span><i>&#10022;</i>
      <?php endfor; ?>
    </div>
  </div>

  <!-- ========================= FEATURED MENU ========================= -->
  <section id="featured" class="section">
    <div class="container">
      <div class="section-head reveal-up">
        <p class="eyebrow">Our Menu</p>
        <h2>Crave-worthy favourites</h2>
        <p class="muted">Freshly made to order. Tap <strong>Add</strong>, adjust quantities right on the card, and check out when you're ready.</p>
      </div>

      <?php if ($featured): ?>
        <div class="dish-grid">
          <?php foreach ($featured as $p): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="empty">Menu items will appear here once the database is set up.</p>
      <?php endif; ?>

      <div class="center mt-40"><a href="products.php" class="btn btn-ghost btn-lg">Explore the full menu &rarr;</a></div>
    </div>
  </section>

  <!-- ========================= STORY (video chapters) ========================= -->
  <section id="story" class="section story-section">
    <div class="container">
      <div class="section-head reveal-up">
        <p class="eyebrow">Our Story</p>
        <h2>From the market to your doorstep</h2>
        <p class="muted">Four short chapters on how every Blessed Snaks order is made. Pick a chapter, or sit back and let it play.</p>
      </div>

      <div class="story-player reveal-up" id="story-player">
        <div class="story-stage">
          <video class="story-video" muted playsinline preload="none"
                 poster="<?= e(mixkit($chapters[0]['id'], 'poster')) ?>"></video>
          <div class="story-shade"></div>
          <div class="story-caption" aria-live="polite">
            <span class="story-chapter">Chapter 01</span>
            <h3 class="story-title"><?= e($chapters[0]['title']) ?></h3>
            <p class="story-text"><?= e($chapters[0]['text']) ?></p>
          </div>
          <button type="button" class="story-toggle" aria-label="Play story">
            <svg class="i-play" viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
            <svg class="i-pause" viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M7 5h4v14H7zM13 5h4v14h-4z"/></svg>
          </button>
          <span class="story-count"><b>01</b> / <?= sprintf('%02d', count($chapters)) ?></span>
        </div>

        <ol class="story-chapters">
          <?php foreach ($chapters as $i => $c): ?>
            <li>
              <button type="button" class="chapter<?= $i === 0 ? ' active' : '' ?>"
                      data-src="<?= e(mixkit($c['id'])) ?>" data-poster="<?= e(mixkit($c['id'], 'poster')) ?>"
                      data-title="<?= e($c['title']) ?>" data-text="<?= e($c['text']) ?>">
                <img src="<?= e(mixkit($c['id'], 'poster')) ?>" alt="" loading="lazy">
                <span class="chapter-meta">
                  <small>Chapter <?= sprintf('%02d', $i + 1) ?></small>
                  <strong><?= e($c['title']) ?></strong>
                  <span class="chapter-bar"><i></i></span>
                </span>
              </button>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <!-- ========================= HOW IT WORKS ========================= -->
  <section id="how" class="section section-alt">
    <div class="container">
      <div class="section-head reveal-up">
        <p class="eyebrow">How It Works</p>
        <h2>From craving to doorstep in 3 steps</h2>
      </div>
      <div class="steps">
        <div class="step reveal-up">
          <div class="step-icon"><svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg></div>
          <span class="step-no">STEP 01</span>
          <h3>Choose Dish</h3>
          <p>Browse the menu, peek with quick view and add your favourite burgers, snacks and drinks to your cart.</p>
        </div>
        <div class="step reveal-up" style="--d:.12s">
          <div class="step-icon"><svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c.3 2.2-.8 3.4-1.9 4.6C9 8.8 8 10 8 12a4 4 0 0 0 8 0c0-1.2-.5-2-1-2.8 1.8.8 3 2.8 3 5A6 6 0 0 1 6 14.5C6 9 11 8 12 3Z"/></svg></div>
          <span class="step-no">STEP 02</span>
          <h3>Fast Preparation</h3>
          <p>Our chefs cook your order fresh, never reheated, and pack it in sealed, insulated bags.</p>
        </div>
        <div class="step reveal-up" style="--d:.24s">
          <div class="step-icon"><svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3 5 13.5h6L10 21l8-10.5h-6L13 3Z"/></svg></div>
          <span class="step-no">STEP 03</span>
          <h3>Instant Delivery</h3>
          <p>A rider brings it straight to your door, usually in about 30 minutes. Pay cash, MTN MoMo or Airtel Money.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================= WHY US ========================= -->
  <section class="section">
    <div class="container">
      <div class="section-head reveal-up">
        <p class="eyebrow">Why Blessed Snaks</p>
        <h2>Fast food, done properly</h2>
      </div>
      <div class="features">
        <article class="feature reveal-up">
          <span class="feature-icon"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 21h10M12 17v4M5 3h14l-1.5 9a5.5 5.5 0 0 1-11 0L5 3Z"/></svg></span>
          <h3>Cooked to order</h3>
          <p>Every burger, wing and fry is made when you order. No heat lamps, no shortcuts.</p>
        </article>
        <article class="feature reveal-up" style="--d:.1s">
          <span class="feature-icon"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2.5M9 2h6"/></svg></span>
          <h3>30-minute delivery</h3>
          <p>Dedicated riders across our delivery zones mean your food arrives hot and on time.</p>
        </article>
        <article class="feature reveal-up" style="--d:.2s">
          <span class="feature-icon"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
          <h3>Sealed &amp; hygienic</h3>
          <p>A spotless kitchen, gloved hands and tamper-sealed, insulated packaging on every order.</p>
        </article>
        <article class="feature reveal-up" style="--d:.3s">
          <span class="feature-icon"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="3"/><path d="M11 18h2"/></svg></span>
          <h3>Pay your way</h3>
          <p>Cash on delivery, MTN MoMo or Airtel Money. Pay when your food arrives.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ========================= ABOUT ========================= -->
  <section id="about" class="section section-alt">
    <div class="container about-grid">
      <div class="about-img reveal-up">
        <video autoplay loop muted playsinline preload="none" poster="<?= e(mixkit(16599, 'poster')) ?>" data-lazy-video>
          <source data-src="<?= e(mixkit(16599)) ?>" type="video/mp4">
        </video>
        <div class="about-badge"><strong>100%</strong><span>Made fresh daily</span></div>
      </div>
      <div class="reveal-up">
        <p class="eyebrow">About Us</p>
        <h2 class="h2-left">Food made with care, served with a smile</h2>
        <p class="muted mt-16">Blessed Snaks started with a simple belief: fast food doesn't have to feel rushed. We combine quality ingredients, honest recipes and quick delivery so every bite feels like a treat.</p>
        <p class="muted mt-16">Our menu is built around the classics Kampala loves, from double-stacked burgers to crispy samosas and thick milkshakes, each one seasoned, grilled and plated by people who care how it tastes.</p>
        <ul class="ticks">
          <li>Fresh ingredients sourced daily from local markets</li>
          <li>Hygienic kitchen and sealed, insulated packaging</li>
          <li>Friendly riders and reliable delivery times</li>
        </ul>
        <a href="products.php" class="btn btn-primary mt-24">See what's cooking</a>
      </div>
    </div>
  </section>

  <!-- ========================= TESTIMONIALS ========================= -->
  <!-- Sample reviews for the demo. Replace them with real customer feedback before launch. -->
  <section class="section">
    <div class="container narrow">
      <div class="section-head reveal-up">
        <p class="eyebrow">Happy Customers</p>
        <h2>What our regulars say</h2>
      </div>
      <div class="reviews reveal-up" id="reviews">
        <div class="reviews-track">
          <figure class="review">
            <div class="stars" aria-label="5 out of 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <blockquote>"The Signature Burger arrived hotter than when I pick food up myself. The caramelised onions are unreal."</blockquote>
            <figcaption><span class="avatar">K</span><span><strong>Belden K.</strong><small>Kololo</small></span></figcaption>
          </figure>
          <figure class="review">
            <div class="stars" aria-label="5 out of 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <blockquote>"Ordered wings for the match and they came in 25 minutes. Paid with MoMo at the door. Easiest thing ever."</blockquote>
            <figcaption><span class="avatar">M</span><span><strong>Oren M.</strong><small>Ntinda</small></span></figcaption>
          </figure>
          <figure class="review">
            <div class="stars" aria-label="5 out of 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <blockquote>"Their milkshakes are thick the way they should be, and the fries actually stay crispy. Our family Friday treat."</blockquote>
            <figcaption><span class="avatar">B</span><span><strong>Kayo B.</strong><small>Muyenga</small></span></figcaption>
          </figure>
        </div>
        <div class="reviews-nav">
          <button type="button" class="icon-btn rev-prev" aria-label="Previous review">&#8592;</button>
          <div class="dots" role="tablist"></div>
          <button type="button" class="icon-btn rev-next" aria-label="Next review">&#8594;</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================= OFFERS ========================= -->
  <section id="offers" class="section section-tight">
    <div class="container offers">
      <div class="offer offer-hot reveal-up">
        <p class="eyebrow light">Combo Deal</p>
        <h3>Burger + Fries + Drink</h3>
        <p>Save 15% when you order the full combo, every day.</p>
        <div class="countdown" data-countdown aria-label="Today's deal ends in">
          <span><b data-h>00</b><small>hrs</small></span>
          <span><b data-m>00</b><small>min</small></span>
          <span><b data-s>00</b><small>sec</small></span>
        </div>
        <a href="products.php?cat=mains" class="btn btn-light">Order Combo</a>
      </div>
      <div class="offer offer-dark reveal-up" style="--d:.1s">
        <p class="eyebrow">Every Day</p>
        <h3>Free delivery on orders over <?= e(money(FREE_DELIVERY_OVER)) ?></h3>
        <p>Valid every day within our delivery zones. Your cart shows how close you are.</p>
        <a href="products.php" class="btn btn-primary">Start Ordering</a>
      </div>
    </div>
  </section>

  <!-- ========================= CTA BAND (video) ========================= -->
  <section class="cta-band">
    <video autoplay loop muted playsinline preload="none" poster="<?= e(mixkit(12016, 'poster')) ?>" data-lazy-video>
      <source data-src="<?= e(mixkit(12016)) ?>" type="video/mp4">
    </video>
    <div class="cta-shade"></div>
    <div class="container cta-inner reveal-up">
      <h2>Hungry already?</h2>
      <p>Your next favourite meal is about 30 minutes away.</p>
      <div class="hero-btns center-flex">
        <a href="products.php" class="btn btn-primary btn-lg glow">Order Now</a>
        <a href="tel:+256700000000" class="btn btn-ghost btn-lg">Call +256 700 000 000</a>
      </div>
    </div>
  </section>

  <!-- ========================= FAQ ========================= -->
  <section class="section">
    <div class="container narrow">
      <div class="section-head reveal-up">
        <p class="eyebrow">FAQ</p>
        <h2>Good to know</h2>
      </div>
      <div class="faq reveal-up">
        <details open>
          <summary>Where do you deliver?</summary>
          <p>We deliver across central Kampala and the surrounding suburbs. Enter your address at checkout and we'll call you if you're outside our zones.</p>
        </details>
        <details>
          <summary>How much is delivery?</summary>
          <p>Delivery is a flat <?= e(money(DELIVERY_FEE)) ?>, and it's free on any order of <?= e(money(FREE_DELIVERY_OVER)) ?> or more.</p>
        </details>
        <details>
          <summary>How can I pay?</summary>
          <p>Pay on delivery with cash, MTN Mobile Money or Airtel Money. Your rider will confirm the total when they arrive.</p>
        </details>
        <details>
          <summary>Do I need an account to order?</summary>
          <p>You can browse and fill your cart freely. You'll sign in or create a quick account at checkout so we can contact you and you can track your orders.</p>
        </details>
        <details>
          <summary>How do I track my order?</summary>
          <p>Open <strong>My Orders</strong> after signing in to see each order's status, from Received to Preparing, On the way and Delivered.</p>
        </details>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
