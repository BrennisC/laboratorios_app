<?php
require_once __DIR__ . '/db.php';

$message = '';
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

// VULN: A01 - Admin/user management exposed without server-side authorization.
if ($action === 'delete' && $id !== '') {
    db()->exec("DELETE FROM users WHERE id = $id");
    $message = 'User deleted.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? 'changeme';

    if ($id !== '') {
        db()->exec("UPDATE users SET name = '$name', email = '$email', role = '$role', password = '$password' WHERE id = $id");
        $message = 'User updated.';
    } else {
        db()->exec("INSERT INTO users (name, email, password, role, credit_card, address) VALUES ('$name', '$email', '$password', '$role', '', '')");
        $message = 'User created.';
    }
}

$users = db()->query('SELECT * FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/includes/header.php';
?>
<h1>User Management</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>ID for update</label><input name="id">
        <label>Name</label><input name="name">
        <label>Email</label><input name="email">
        <label>Role</label><input name="role" value="user">
        <label>Password</label><input name="password" value="changeme">
        <button>Save user</button>
    </form>
</section>
<section class="panel"><table><tr><th>ID</th><th>Email</th><th>Role</th><th>Action</th></tr><?php foreach ($users as $user): ?><tr><td><?= $user['id'] ?></td><td><?= $user['email'] ?></td><td><?= $user['role'] ?></td><td><a href="/users.php?action=delete&id=<?= $user['id'] ?>">Delete</a></td></tr><?php endforeach; ?></table></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
