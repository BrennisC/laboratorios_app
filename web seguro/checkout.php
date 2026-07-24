<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';
require_login();

$message = '';

function cart_items_and_total()
{
    $items = [];
    $total = 0;
    foreach ($_SESSION['cart'] ?? [] as $productId => $quantity) {
        $stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND active = 1');
        $stmt->execute([(int) $productId]);
        $product = $stmt->fetch();
        if ($product) {
            $product['quantity'] = (int) $quantity;
            $items[] = $product;
            $total += (float) $product['price'] * (int) $quantity;
        }
    }
    return [$items, $total];
}

[$items, $total] = cart_items_and_total();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $address = trim($_POST['address'] ?? '');
    if ($items && $address !== '') {
        db()->beginTransaction();
        $stmt = db()->prepare('INSERT INTO orders (user_id, total, address, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([current_user_id(), $total, $address]);
        $orderId = db()->lastInsertId();
        $itemStmt = db()->prepare('INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        foreach ($items as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['price'], $item['quantity']]);
        }
        db()->commit();
        $_SESSION['cart'] = [];
        log_security_event('order_created', 'Order created: ' . $orderId);
        $message = 'Order created';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <h1>Checkout</h1>
    <?php if ($message): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
    <p>Total calculated server-side: <strong>$<?= e(number_format($total, 2)) ?></strong></p>
    <form method="post">
        <?= csrf_field() ?>
        <label>Shipping address</label>
        <textarea name="address" required><?= e($_SESSION['address'] ?? '') ?></textarea>
        <button>Place order</button>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
