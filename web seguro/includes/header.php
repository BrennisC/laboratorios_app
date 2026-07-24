<?php require_once __DIR__ . '/../lib/security.php'; ?>
<?php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' https://picsum.photos; style-src 'self'; script-src 'self'; base-uri 'self'; frame-ancestors 'none'");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/index.php">SecureShop</a>
    <nav>
        <a href="/search.php">Search</a>
        <a href="/cart.php">Cart</a>
        <a href="/orders.php">Orders</a>
        <?php if (current_user_role() === 'admin'): ?>
            <a href="/admin.php">Admin</a>
            <a href="/security_logs.php">Logs</a>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <a href="/profile.php">Profile</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
