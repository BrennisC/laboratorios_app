<?php
require_once __DIR__ . '/db.php';
$userId = $_GET['user_id'] ?? current_user_id() ?? 1;
// VULN: A01/A05 - user_id is trusted and concatenated.
$rows = db()->query("SELECT * FROM user_history WHERE user_id = $userId ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/includes/header.php';
?>
<h1>User History</h1>
<section class="panel"><p>Viewing history for user ID: <?= $userId ?></p><table><tr><th>ID</th><th>Action</th><th>IP</th><th>Date</th></tr><?php foreach ($rows as $row): ?><tr><td><?= $row['id'] ?></td><td><?= $row['action'] ?></td><td><?= $row['ip_address'] ?></td><td><?= $row['created_at'] ?></td></tr><?php endforeach; ?></table></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
