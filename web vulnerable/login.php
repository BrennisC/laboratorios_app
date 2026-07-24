<?php
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // A1: SQL Injection, A2: plaintext password comparison and no session regeneration.
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $user = db()->query($sql)->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        redirect('/index.php');
    }

    $error = 'Invalid credentials';
}

include __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <h1>Login</h1>
    <?php if ($error): ?><p class="notice"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input name="email" value="<?= $_POST['email'] ?? '' ?>">
        <label>Password</label>
        <input name="password" type="password">
        <button>Login</button>
    </form>
    <p class="muted">Try SQL injection in the email or password fields.</p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
