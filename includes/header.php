<?php
require_once __DIR__ . '/../config.php';
$page_title = $page_title ?? 'Blessed Snaks | Gourmet Snacks & Fast Food Delivery';
$active     = $active ?? '';
$solid_nav  = $solid_nav ?? true;      // index.php sets this to false (transparent over hero)
$user       = current_user();
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title) ?></title>
  <meta name="description" content="Delicious dishes and gourmet snacks delivered fresh to your doorstep. Order burgers, wings, fries and drinks from Blessed Snaks.">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <meta name="theme-color" content="#0b0707">
  <script>document.documentElement.classList.replace('no-js', 'js');</script>
  <link rel="preconnect" href="https://assets.mixkit.co">
  <link rel="preconnect" href="https://images.unsplash.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body
  data-logged-in="<?= $user ? '1' : '0' ?>"
  data-phone="<?= e($user['phone'] ?? '') ?>"
  data-free-over="<?= (int)FREE_DELIVERY_OVER ?>"
  data-fee="<?= (int)DELIVERY_FEE ?>"
  data-currency="<?= e(CURRENCY) ?>">

<header id="navbar" class="navbar <?= $solid_nav ? 'scrolled' : '' ?>" data-solid="<?= $solid_nav ? '1' : '0' ?>">
  <nav class="nav-inner" aria-label="Main navigation">
    <a href="index.php" class="logo" aria-label="Blessed Snaks home">
      <span class="logo-mark">B</span>
      <span class="logo-text">Blessed <em>Snaks</em></span>
    </a>

    <ul class="nav-links">
      <li><a href="index.php"    class="<?= $active === 'home'     ? 'active' : '' ?>">Home</a></li>
      <li><a href="products.php" class="<?= $active === 'menu'     ? 'active' : '' ?>">Menu</a></li>
      <li><a href="index.php"    class="<?= $active === 'Our Story'     ? 'active' : '' ?>">Our Story</a></li>
      <li><a href="index.php"    class="<?= $active === 'About Us'     ? 'active' : '' ?>">About Us</a></li>
      <li><a href="index.php"    class="<?= $active === 'Offers'     ? 'active' : '' ?>">Offers</a></li>
      <li><a href="index.php"    class="<?= $active === 'Contact'     ? 'active' : '' ?>">Contact</a></li>
    </ul>

    <div class="nav-actions">
      <button id="cart-toggle" class="icon-btn cart-btn" aria-label="Open cart">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.2 11.2a1 1 0 0 0 1 .8h8.6a1 1 0 0 0 1-.76L19.5 8H6"/><circle cx="9.5" cy="20" r="1.2"/><circle cx="16.5" cy="20" r="1.2"/></svg>
        <span id="cart-count" class="cart-count">0</span>
      </button>

      <?php if ($user): ?>
        <a href="orders.php" class="nav-text-link <?= $active === 'orders' ? 'active' : '' ?>">My Orders</a>
        <form action="logout.php" method="post" class="inline-form">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-ghost btn-sm" title="Signed in as <?= e($user['name']) ?>">Log out</button>
        </form>
      <?php else: ?>
        <a href="login.php" class="nav-text-link">Log in</a>
        <a href="register.php" class="btn btn-ghost btn-sm">Sign up</a>
      <?php endif; ?>

      <a href="products.php" class="btn btn-primary btn-sm nav-cta">Order Now</a>

      <button id="menu-toggle" class="icon-btn menu-toggle" aria-label="Toggle menu" aria-expanded="false">
        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </nav>

  <div id="mobile-menu" class="mobile-menu" hidden>
    <a href="index.php">Home</a>
    <a href="products.php">Menu</a>
    <a href="index.php#story">Our Story</a>
    <a href="index.php#about">About Us</a>
    <a href="index.php#offers">Offers</a>
    <a href="index.php#contact">Contact</a>
    <?php if ($user): ?>
      <a href="orders.php">My Orders</a>
    <?php else: ?>
      <a href="login.php">Log in</a>
      <a href="register.php">Sign up</a>
    <?php endif; ?>
    <a href="products.php" class="btn btn-primary">Order Now</a>
  </div>
</header>
