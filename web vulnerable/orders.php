<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    redirect('/login.php');
}

// A5: user_id is trusted from request, exposing other users' orders.
$userId = $_GET['user_id'] ?? current_user_id();
$orders = db()->query("SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>
<h1>Orders</h1>
<form method="get">
    <label>User ID</label>
    <input name="user_id" value="<?= $userId ?>">
    <button>View</button>
</form>
<table>
    <tr><th>ID</th><th>Total</th><th>Address</th><th>Date</th></tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td>$<?= $order['total'] ?></td>
            <td><?= $order['address'] ?></td>
            <td><?= $order['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
