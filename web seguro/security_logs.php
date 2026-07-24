<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';
require_admin();

$logs = db()->query('SELECT * FROM security_logs ORDER BY id DESC LIMIT 100')->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>Security Logs</h1>
<table>
    <tr><th>ID</th><th>User</th><th>Type</th><th>Message</th><th>IP</th><th>Date</th></tr>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= e($log['id']) ?></td>
            <td><?= e($log['user_id'] ?? 'guest') ?></td>
            <td><?= e($log['event_type']) ?></td>
            <td><?= e($log['message']) ?></td>
            <td><?= e($log['ip_address']) ?></td>
            <td><?= e($log['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
