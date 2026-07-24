<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    redirect('/login.php');
}

// A5: IDOR, any authenticated user can request another user id.
$id = $_GET['id'] ?? current_user_id();
$user = db()->query("SELECT * FROM users WHERE id = $id")->fetch(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>
<h1>User Profile</h1>
<?php if ($user): ?>
    <div class="panel">
        <p><strong>ID:</strong> <?= $user['id'] ?></p>
        <p><strong>Name:</strong> <?= $user['name'] ?></p>
        <p><strong>Email:</strong> <?= $user['email'] ?></p>
        <p><strong>Password:</strong> <?= $user['password'] ?></p>
        <p><strong>Role:</strong> <?= $user['role'] ?></p>
        <p><strong>Credit card:</strong> <?= $user['credit_card'] ?></p>
        <p><strong>Address:</strong> <?= $user['address'] ?></p>
        <?php if ((int) $user['id'] === 2): ?>
            <p><strong>User flag:</strong> VSHOP{user_idor_profile_2017}</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
