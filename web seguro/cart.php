<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$action = $_GET['action'] ?? '';
$id = int_param('id', 0);

if ($action === 'add' && $id > 0) {
    $stmt = db()->prepare('SELECT id FROM products WHERE id = ? AND active = true');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    redirect('/cart.php');
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    redirect('/cart.php');
}

$items = [];
$total = 0;
foreach ($_SESSION['cart'] as $productId => $quantity) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND active = true');
    $stmt->execute([(int) $productId]);
    $product = $stmt->fetch();
    if ($product) {
        $product['quantity'] = (int) $quantity;
        $items[] = $product;
        $total += (float) $product['price'] * (int) $quantity;
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Cart</h1>
<?php if (!$items): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table>
        <tr><th>Product</th><th>Qty</th><th>Price</th></tr>
        <?php foreach ($items as $item): ?>
            <tr><td><?= e($item['name']) ?></td><td><?= e($item['quantity']) ?></td><td>$<?= e($item['price']) ?></td></tr>
        <?php endforeach; ?>
        <tr><th colspan="2">Total</th><th>$<?= e(number_format($total, 2)) ?></th></tr>
    </table>
    <p><a class="button" href="/checkout.php">Checkout</a> <a class="button danger" href="/cart.php?action=clear">Clear</a></p>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
