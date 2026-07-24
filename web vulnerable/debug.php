<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/includes/header.php';
?>
<h1>Debug Console</h1>
<div class="panel">
    <p><strong>Environment:</strong> <?= APP_ENV ?></p>
    <p><strong>Debug:</strong> <?= DEBUG_MODE ? 'enabled' : 'disabled' ?></p>
    <p><strong>Database host:</strong> <?= DB_HOST ?>:<?= DB_PORT ?></p>
    <p><strong>Database name:</strong> <?= DB_NAME ?></p>
    <p><strong>Database user:</strong> <?= DB_USER ?></p>
    <p><strong>Database password:</strong> <?= DB_PASS ?></p>
    <p><strong>Upload dir:</strong> <?= UPLOAD_DIR ?></p>
    <p><strong>Secret:</strong> <?= APP_SECRET ?></p>
    <p><strong>Recon flag:</strong> VSHOP{recon_debug_leak_2017}</p>
    <h2>Users</h2>
    <pre><?php print_r(db()->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC)); ?></pre>
    <h2>Session</h2>
    <pre><?php print_r($_SESSION); ?></pre>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
