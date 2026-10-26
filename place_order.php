<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $code, array $body): never
{
    http_response_code($code);
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed.']);
}

// 1. Must be signed in
$user = current_user();
if (!$user) {
    respond(401, ['ok' => false, 'error' => 'login_required']);
}

// 2. CSRF
if (!csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    respond(403, ['ok' => false, 'error' => 'Your session expired. Refresh the page and try again.']);
}

// 3. Validate input
$data = json_decode((string)file_get_contents('php://input'), true);
if (!is_array($data) || !isset($data['items']) || !is_array($data['items'])) {
    respond(400, ['ok' => false, 'error' => 'Invalid request.']);
}

$address = trim((string)($data['address'] ?? ''));
$phone   = trim((string)($data['phone'] ?? ''));
$payment = (string)($data['payment'] ?? 'cash');

if (mb_strlen($address) < 5 || mb_strlen($address) > 255) {
    respond(422, ['ok' => false, 'error' => 'Enter a delivery address (at least 5 characters).']);
}
if (!preg_match('/^\+?[0-9 ]{9,15}$/', $phone)) {
    respond(422, ['ok' => false, 'error' => 'Enter a valid phone number.']);
}
if (!in_array($payment, ['cash', 'mtn_momo', 'airtel_money'], true)) {
    respond(422, ['ok' => false, 'error' => 'Choose a payment method.']);
}

// Merge duplicate lines, clamp quantities
$qtys = [];
foreach ($data['items'] as $line) {
    $id  = (int)($line['id'] ?? 0);
    $qty = (int)($line['qty'] ?? 0);
    if ($id < 1 || $qty < 1) {
        continue;
    }
    $qtys[$id] = min(20, ($qtys[$id] ?? 0) + $qty);
}
if (!$qtys || count($qtys) > 30) {
    respond(422, ['ok' => false, 'error' => 'Your cart is empty.']);
}

try {
    $pdo = db();

    // 4. Prices come from the database, never from the browser
    $ids = array_keys($qtys);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $pdo->prepare("SELECT id, name, price FROM products WHERE is_active = 1 AND id IN ($in)");
    $st->execute($ids);
    $products = [];
    foreach ($st->fetchAll() as $row) {
        $products[(int)$row['id']] = $row;
    }
    if (count($products) !== count($qtys)) {
        respond(422, ['ok' => false, 'error' => 'Some items in your cart are no longer available. Please refresh the menu.']);
    }

    $subtotal = 0;
    foreach ($qtys as $id => $qty) {
        $subtotal += (int)$products[$id]['price'] * $qty;
    }
    $fee   = $subtotal >= FREE_DELIVERY_OVER ? 0 : DELIVERY_FEE;
    $total = $subtotal + $fee;

    // 5. Save order + items atomically
    $pdo->beginTransaction();
    $pdo->prepare(
        'INSERT INTO orders (user_id, subtotal, delivery_fee, total, address, phone, payment_method)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    )->execute([$user['id'], $subtotal, $fee, $total, $address, $phone, $payment]);
    $orderId = (int)$pdo->lastInsertId();

    $ins = $pdo->prepare('INSERT INTO order_items (order_id, product_id, name, unit_price, qty) VALUES (?, ?, ?, ?, ?)');
    foreach ($qtys as $id => $qty) {
        $ins->execute([$orderId, $id, $products[$id]['name'], $products[$id]['price'], $qty]);
    }
    $pdo->commit();

    respond(200, ['ok' => true, 'order_id' => $orderId, 'total' => $total, 'total_formatted' => money($total)]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('place_order failed: ' . $e->getMessage());
    respond(500, ['ok' => false, 'error' => 'We could not place your order. Please try again.']);
}
