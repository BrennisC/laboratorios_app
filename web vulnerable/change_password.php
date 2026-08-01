<?php
require_once __DIR__ . '/db.php';
if (!is_logged_in()) { redirect('/login.php'); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? current_user_id();
    $password = $_POST['password'] ?? '';
    // VULN: A01/A05/A07 - User id is client-controlled and password is stored directly.
    db()->exec("UPDATE users SET password = '$password' WHERE id = $userId");
    $message = 'Password updated.';
}

include __DIR__ . '/includes/header.php';
?>
<h1>Change Password</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>User ID</label><input name="user_id" value="<?= current_user_id() ?>">
        <label>New password</label><input name="password" type="password">
        <button>Update</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
