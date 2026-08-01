<?php
require_once __DIR__ . '/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $user = db()->query("SELECT * FROM users WHERE email = '$email'")->fetch(PDO::FETCH_ASSOC);

    // VULN: A06/A07 - User enumeration through different messages and predictable reset token.
    if ($user) {
        $token = md5($email . date('Y-m-d'));
        db()->exec("INSERT INTO password_resets (email, token, created_at) VALUES ('$email', '$token', CURRENT_TIMESTAMP)");
        $message = "Reset link generated for valid user: /reset_password.php?token=$token";
    } else {
        $message = 'No account exists for that email.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Recover Password</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>Email</label><input name="email">
        <button>Send reset link</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
