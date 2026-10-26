<?php $footer_categories = get_categories(); ?>
<footer id="contact" class="footer">
  <!-- Newsletter band -->
  <div class="container footer-cta">
    <div>
      <p class="eyebrow">The Blessed Club</p>
      <h3>Offers &amp; new menu drops, straight to your inbox</h3>
      <p class="muted small">One short email a week. Unsubscribe any time.</p>
    </div>
    <!-- Point action at your own handler (e.g. subscribe.php) when ready. -->
    <form id="newsletter-form" action="#" method="post" class="newsletter">
      <input type="email" name="email" required placeholder="you@example.com" aria-label="Email address">
      <button type="submit" class="btn btn-primary">Subscribe</button>
    </form>
  </div>

  <div class="container footer-grid">
    <div class="footer-brand">
      <a href="index.php" class="logo">
        <span class="logo-mark">B</span>
        <span class="logo-text">Blessed <em>Snaks</em></span>
      </a>
      <p class="muted mt-16">Gourmet burgers, crispy snacks and cold drinks, cooked to order in Kampala and delivered hot to your doorstep.</p>
      <div class="socials">
        <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M14 8h3V4h-3c-2.8 0-4 1.7-4 4.3V10H7v4h3v8h4v-8h3l1-4h-4V8.6c0-.4.3-.6.7-.6Z"/></svg></a>
        <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/></svg></a>
        <a href="#" aria-label="X"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.8 3h3.1l-6.8 7.8L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5Z"/></svg></a>
        <a href="#" aria-label="TikTok"><svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor"><path d="M16.6 3c.4 2.3 1.9 3.8 4.4 4v3.3c-1.6 0-3.1-.5-4.4-1.4v6.3A6.2 6.2 0 1 1 10.4 9v3.4a2.9 2.9 0 1 0 2.9 2.8V3h3.3Z"/></svg></a>
        <a href="#" aria-label="WhatsApp"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.6 0-3.1-.4-4.4-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.4.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 1.9 2.9 4.6 4 1.7.7 2.4.8 3.2.7.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.5-.3Z"/></svg></a>
      </div>
    </div>

    <div>
      <h4>Explore</h4>
      <ul class="footer-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Full Menu</a></li>
        <li><a href="index.php#story">Our Story</a></li>
        <li><a href="index.php#about">About Us</a></li>
        <li><a href="index.php#offers">Special Offers</a></li>
        <li><a href="orders.php">My Orders</a></li>
      </ul>
    </div>

    <div>
      <h4>Menu</h4>
      <ul class="footer-links">
        <?php foreach ($footer_categories as $cat): ?>
          <li><a href="products.php?cat=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <?php if (!$footer_categories): ?>
          <li><a href="products.php">Burgers &amp; Mains</a></li>
          <li><a href="products.php">Snacks</a></li>
          <li><a href="products.php">Drinks</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <div>
      <h4>Contact</h4>
      <ul class="contact-list">
        <li><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg><span>Your street address, Kampala, Uganda</span></li>
        <li><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg><a href="tel:+256700000000">+256 700 000 000</a></li>
        <li><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><a href="mailto:hello@blessedsnacks.com">hello@blessedsnacks.com</a></li>
      </ul>
    </div>

    <div>
      <h4>Opening Hours <span id="open-badge" class="open-badge" hidden></span></h4>
      <ul class="hours" id="hours">
        <li data-days="1-5" data-open="9" data-close="22"><span>Mon - Fri</span><span>9:00 AM - 10:00 PM</span></li>
        <li data-days="6" data-open="9" data-close="23"><span>Saturday</span><span>9:00 AM - 11:00 PM</span></li>
        <li data-days="0" data-open="10" data-close="21"><span>Sunday</span><span>10:00 AM - 9:00 PM</span></li>
      </ul>
    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; <?= date('Y') ?> Blessed Snaks. All rights reserved.
      <span class="legal"><a href="#">Privacy</a><a href="#">Terms</a><a href="#">Refunds</a></span>
    </p>
    <div class="pay-badges" aria-label="Accepted payment options">
      <span>MTN MoMo</span><span>Airtel Money</span><span>Cash on Delivery</span>
    </div>
    <p class="credits">Videos: <a href="https://mixkit.co" target="_blank" rel="noopener">Mixkit</a> &middot; Photos: <a href="https://unsplash.com" target="_blank" rel="noopener">Unsplash</a></p>
  </div>
</footer>

<button type="button" id="to-top" class="to-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 14 6-6 6 6"/></svg>
</button>

<!-- Floating cart summary (shows once the cart has items) -->
<button type="button" id="cart-fab" class="cart-fab" hidden>
  <span class="fab-count" id="fab-count">0</span>
  <span>View cart</span>
  <strong id="fab-total">UGX 0</strong>
</button>

<!-- Quick view -->
<div id="qv" class="modal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="qv-title">
    <button type="button" class="icon-btn modal-close" data-close aria-label="Close">&#10005;</button>
    <div class="qv-media"><img id="qv-img" src="" alt=""><span id="qv-badge" class="dish-badge" hidden></span></div>
    <div class="qv-body">
      <span id="qv-cat" class="dish-cat"></span>
      <h2 id="qv-title"></h2>
      <p id="qv-desc" class="muted"></p>
      <ul class="qv-perks">
        <li>Cooked fresh to order</li>
        <li>Packed hot in sealed, insulated bags</li>
        <li>Delivered in about 30 minutes</li>
      </ul>
      <div class="qv-buy">
        <span id="qv-price" class="price"></span>
        <div class="stepper stepper-lg">
          <button type="button" id="qv-dec" aria-label="Decrease quantity">&minus;</button>
          <span id="qv-qty">1</span>
          <button type="button" id="qv-inc" aria-label="Increase quantity">+</button>
        </div>
      </div>
      <button type="button" id="qv-add" class="btn btn-primary btn-block btn-lg">Add to cart</button>
    </div>
  </div>
</div>

<!-- Cart drawer -->
<div id="cart-overlay" class="overlay" hidden></div>
<aside id="cart-drawer" class="drawer" aria-label="Shopping cart" aria-hidden="true">
  <div class="drawer-head">
    <h2>Your Cart <span id="drawer-count" class="drawer-count"></span></h2>
    <button id="cart-close" class="icon-btn" aria-label="Close cart">&#10005;</button>
  </div>

  <div id="free-meter" class="free-meter">
    <p id="free-text">Add items to unlock free delivery.</p>
    <div class="meter"><span id="free-bar"></span></div>
  </div>

  <div id="cart-view" class="drawer-body">
    <ul id="cart-items" class="cart-items"></ul>

    <form id="checkout-form" class="checkout-form" hidden novalidate>
      <h3>Delivery details</h3>
      <label>Phone number
        <input type="tel" id="co-phone" name="phone" placeholder="+256 700 000 000" required>
      </label>
      <label>Delivery address
        <textarea id="co-address" name="address" rows="2" placeholder="Area, street, landmark" required></textarea>
      </label>
      <fieldset class="pay-options">
        <legend>Payment method</legend>
        <label class="pay-opt"><input type="radio" name="payment" value="cash" checked><span>&#128181; Cash</span></label>
        <label class="pay-opt"><input type="radio" name="payment" value="mtn_momo"><span>MTN MoMo</span></label>
        <label class="pay-opt"><input type="radio" name="payment" value="airtel_money"><span>Airtel Money</span></label>
      </fieldset>
      <p class="muted small">You pay on delivery. Our rider will confirm the total.</p>
      <p id="co-error" class="form-error" role="alert" hidden></p>
    </form>
  </div>

  <div id="order-done" class="drawer-body order-done" hidden>
    <div class="check">&#10003;</div>
    <h3>Order placed!</h3>
    <p class="muted" id="order-done-text"></p>
    <ol class="track">
      <li class="done">Received</li><li>Preparing</li><li>On the way</li><li>Delivered</li>
    </ol>
    <a href="orders.php" class="btn btn-primary">View my orders</a>
  </div>

  <div id="cart-foot" class="drawer-foot">
    <dl class="totals">
      <div><dt>Subtotal</dt><dd id="cart-subtotal">UGX 0</dd></div>
      <div><dt>Delivery</dt><dd id="cart-delivery">&ndash;</dd></div>
      <div class="grand"><dt>Total</dt><dd id="cart-total">UGX 0</dd></div>
    </dl>
    <button id="checkout-btn" class="btn btn-primary btn-block" disabled>Checkout</button>
    <button id="back-to-cart" class="link-btn" hidden>&larr; Back to cart</button>
    <button id="clear-cart" class="link-btn">Clear cart</button>
  </div>
</aside>

<div id="toast" class="toast" role="status" aria-live="polite"></div>

<script src="assets/js/app.js" defer></script>
</body>
</html>
