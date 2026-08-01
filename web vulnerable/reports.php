<?php
require_once __DIR__ . '/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $createdBy = $_POST['created_by'] ?? current_user_id() ?? 0;
    // VULN: A01/A05 - Report owner is client-controlled and SQL is concatenated.
    db()->exec("INSERT INTO reports (title, content, created_by, created_at) VALUES ('$title', '$content', $createdBy, CURRENT_TIMESTAMP)");
}
$reports = db()->query('SELECT * FROM reports ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/includes/header.php';
?>
<h1>Reports</h1>
<section class="panel"><form method="post"><label>Title</label><input name="title"><label>Content</label><textarea name="content"></textarea><label>Created by</label><input name="created_by" value="<?= current_user_id() ?? 1 ?>"><button>Create report</button></form></section>
<section class="panel"><table><tr><th>ID</th><th>Title</th><th>Owner</th></tr><?php foreach ($reports as $report): ?><tr><td><?= $report['id'] ?></td><td><?= $report['title'] ?></td><td><?= $report['created_by'] ?></td></tr><?php endforeach; ?></table></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
