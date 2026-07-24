<?php
require_once __DIR__ . '/db.php';

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($action === 'add' && $id) {
    $_SESSION['cart'][] = $id;
    redirect('/cart.php');
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    redirect('/cart.php');
}

$items = [];
$total = 0;

foreach ($_SESSION['cart'] as $productId) {
    $product = db()->query("SELECT * FROM products WHERE id = $productId")->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $items[] = $product;
        $total += (float) $product['price'];
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Cart</h1>
<?php if (!$items): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table>
        <tr><th>Product</th><th>Price</th></tr>
        <?php foreach ($items as $item): ?>
            <tr><td><?= $item['name'] ?></td><td>$<?= $item['price'] ?></td></tr>
        <?php endforeach; ?>
        <tr><th>Total</th><th>$<?= $total ?></th></tr>
    </table>
    <p><a class="button" href="/checkout.php">Checkout</a> <a class="button danger" href="/cart.php?action=clear">Clear</a></p>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
