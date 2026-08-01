<?php
require_once __DIR__ . '/db.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $reset = db()->query("SELECT * FROM password_resets WHERE token = '$token' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    // VULN: A05/A07 - Predictable token and SQL concatenation update password directly.
    if ($reset) {
        db()->exec("UPDATE users SET password = '$password' WHERE email = '{$reset['email']}'");
        $message = 'Password changed.';
    } else {
        $message = 'Invalid token.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Reset Password</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>Token</label><input name="token" value="<?= htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <label>New password</label><input name="password" type="password">
        <button>Change password</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
