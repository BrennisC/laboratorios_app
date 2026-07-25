<?php require_once __DIR__ . '/../config.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/styles.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>
<header class="topbar">
    <a class="brand" href="/index.php">VulnShop</a>
    <nav>
        <a href="/machine.php">Machine</a>
        <a href="/search.php">Search</a>
        <a href="/cart.php">Cart</a>
        <a href="/orders.php">Orders</a>
        <a href="/admin.php">Admin</a>
        <a href="/debug.php">Debug</a>
        <a href="/admin.php?role=admin#upload">Upload</a>
        <a href="/rce.php">RCE</a>
        <?php if (is_logged_in()): ?>
            <a href="/profile.php?id=<?= current_user_id() ?>">Profile</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
