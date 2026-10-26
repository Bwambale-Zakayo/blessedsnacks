<?php
require_once __DIR__ . '/config.php';
require_login('orders.php');

$user   = current_user();
$orders = [];
$error  = '';

try {
    $st = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 50');
    $st->execute([$user['id']]);
    $orders = $st->fetchAll();

    $items = [];
    if ($orders) {
        $ids = array_column($orders, 'id');
        $in  = implode(',', array_fill(0, count($ids), '?'));
        $q   = db()->prepare("SELECT order_id, name, unit_price, qty FROM order_items WHERE order_id IN ($in) ORDER BY id");
        $q->execute($ids);
        foreach ($q->fetchAll() as $row) {
            $items[(int)$row['order_id']][] = $row;
        }
    }
} catch (Throwable $e) {
    $error = 'We could not load your orders right now.';
}

$status_labels = [
    'pending'          => 'Received',
    'preparing'        => 'Preparing',
    'out_for_delivery' => 'On the way',
    'delivered'        => 'Delivered',
    'cancelled'        => 'Cancelled',
];
$pay_labels = ['cash' => 'Cash on delivery', 'mtn_momo' => 'MTN MoMo', 'airtel_money' => 'Airtel Money'];

$page_title = 'My Orders | Blessed Snaks';
$active     = 'orders';
$solid_nav  = true;
require __DIR__ . '/includes/header.php';
?>
<main class="page-top">
  <section class="section">
    <div class="container narrow">
      <div class="section-head">
        <p class="eyebrow">Your account</p>
        <h1 class="h1-sm">My orders</h1>
        <p class="muted">Signed in as <?= e($user['name']) ?> (<?= e($user['email']) ?>)</p>
      </div>

      <?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
      <?php elseif (!$orders): ?>
        <div class="empty">
          <p>You haven't placed any orders yet.</p>
          <a href="products.php" class="btn btn-primary mt-16">Browse the menu</a>
        </div>
      <?php else: ?>
        <?php foreach ($orders as $o): ?>
          <article class="order-card">
            <header>
              <div>
                <strong>Order #<?= (int)$o['id'] ?></strong>
                <span class="muted small"><?= e(date('j M Y, g:i A', strtotime((string)$o['created_at']))) ?></span>
              </div>
              <span class="status status-<?= e($o['status']) ?>"><?= e($status_labels[$o['status']] ?? $o['status']) ?></span>
            </header>
            <ul class="order-lines">
              <?php foreach ($items[(int)$o['id']] ?? [] as $it): ?>
                <li>
                  <span><?= (int)$it['qty'] ?> &times; <?= e($it['name']) ?></span>
                  <span><?= e(money((int)$it['unit_price'] * (int)$it['qty'])) ?></span>
                </li>
              <?php endforeach; ?>
              <li class="muted"><span>Delivery</span><span><?= (int)$o['delivery_fee'] === 0 ? 'Free' : e(money($o['delivery_fee'])) ?></span></li>
              <li class="grand"><span>Total</span><span><?= e(money($o['total'])) ?></span></li>
            </ul>
            <footer class="muted small">
              <?= e($pay_labels[$o['payment_method']] ?? $o['payment_method']) ?> &middot; <?= e($o['address']) ?> &middot; <?= e($o['phone']) ?>
            </footer>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
