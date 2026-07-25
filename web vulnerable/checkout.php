<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    redirect('/login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? current_user_id();
    $address = $_POST['address'] ?? '';
    $total = $_POST['total'] ?? '0';

    // A5: client controls user_id and total. A1: raw SQL concatenation.
    db()->exec("INSERT INTO orders (user_id, total, address, created_at) VALUES ($userId, $total, '$address', CURRENT_TIMESTAMP)");
    $_SESSION['cart'] = [];
    $message = 'Order created';
}

$total = 0;
foreach ($_SESSION['cart'] ?? [] as $productId) {
    $price = db()->query("SELECT price FROM products WHERE id = $productId")->fetchColumn();
    $total += (float) $price;
}

include __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <h1>Checkout</h1>
    <?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
    <form method="post">
        <label>User ID</label>
        <input name="user_id" value="<?= current_user_id() ?>">
        <label>Total</label>
        <input name="total" value="<?= $total ?>">
        <label>Shipping address</label>
        <textarea name="address"><?= $_SESSION['address'] ?? '' ?></textarea>
        <button>Place order</button>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
