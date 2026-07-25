<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $attempts = db()->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = ? AND ip_address = ? AND success = false AND created_at > CURRENT_TIMESTAMP - INTERVAL '10 minutes'");
    $attempts->execute([$email ?: '', $ip]);

    if ((int) $attempts->fetchColumn() >= 5) {
        log_security_event('login_throttled', 'Too many failed login attempts');
        $error = 'Too many failed attempts. Try again later.';
    } elseif ($email) {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            db()->prepare('INSERT INTO login_attempts (email, ip_address, success, created_at) VALUES (?, ?, true, CURRENT_TIMESTAMP)')->execute([$email, $ip]);
            log_security_event('login_success', 'User logged in');
            redirect('/index.php');
        }

        db()->prepare('INSERT INTO login_attempts (email, ip_address, success, created_at) VALUES (?, ?, false, CURRENT_TIMESTAMP)')->execute([$email, $ip]);
        log_security_event('login_failed', 'Failed login for ' . $email);
        $error = 'Invalid credentials';
    } else {
        $error = 'Invalid email';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <h1>Login</h1>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>Email</label>
        <input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
        <label>Password</label>
        <input name="password" type="password" required>
        <button>Login</button>
    </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
