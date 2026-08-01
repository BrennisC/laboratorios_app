<?php
require_once __DIR__ . '/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $creditCard = $_POST['credit_card'] ?? '';
    $address = $_POST['address'] ?? '';

    // VULN: A05/A08 - SQL injection plus mass assignment of role and sensitive fields.
    db()->exec("INSERT INTO users (name, email, password, role, credit_card, address) VALUES ('$name', '$email', '$password', '$role', '$creditCard', '$address')");
    $message = 'Account created.';
}

include __DIR__ . '/includes/header.php';
?>
<h1>Create Account</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>Name</label><input name="name">
        <label>Email</label><input name="email">
        <label>Password</label><input name="password" type="password">
        <label>Role</label><input name="role" value="user">
        <label>Credit card</label><input name="credit_card">
        <label>Address</label><textarea name="address"></textarea>
        <button>Create account</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
