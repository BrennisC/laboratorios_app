<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';
require_login();

$stmt = db()->prepare('SELECT id, name, email, role, credit_card_last4, address FROM users WHERE id = ?');
$stmt->execute([current_user_id()]);
$user = $stmt->fetch();

include __DIR__ . '/includes/header.php';
?>
<h1>User Profile</h1>
<div class="panel">
    <p><strong>ID:</strong> <?= e($user['id']) ?></p>
    <p><strong>Name:</strong> <?= e($user['name']) ?></p>
    <p><strong>Email:</strong> <?= e($user['email']) ?></p>
    <p><strong>Role:</strong> <?= e($user['role']) ?></p>
    <p><strong>Credit card:</strong> <?= e(mask_card($user['credit_card_last4'])) ?></p>
    <p><strong>Address:</strong> <?= e($user['address']) ?></p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
