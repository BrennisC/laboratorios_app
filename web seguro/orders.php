<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';
require_login();

$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([current_user_id()]);
$orders = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>My Orders</h1>
<table>
    <tr><th>ID</th><th>Total</th><th>Address</th><th>Date</th></tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= e($order['id']) ?></td>
            <td>$<?= e($order['total']) ?></td>
            <td><?= e($order['address']) ?></td>
            <td><?= e($order['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
