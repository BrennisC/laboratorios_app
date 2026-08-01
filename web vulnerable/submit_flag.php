<?php
require_once __DIR__ . '/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flag = trim($_POST['flag'] ?? '');
    $stage = flag_stage($flag);
    $correct = $stage !== null ? 'true' : 'false';
    $safeFlag = str_replace("'", "''", $flag);
    $safeStage = $stage === null ? 'NULL' : "'" . str_replace("'", "''", $stage) . "'";

    db()->exec("INSERT INTO flag_submissions (flag, stage, correct, submitted_at) VALUES ('$safeFlag', $safeStage, $correct, CURRENT_TIMESTAMP)");
    $message = $stage !== null ? 'Correct flag: ' . strtoupper($stage) . ' stage unlocked.' : 'Incorrect flag.';
}

$submissions = db()->query('SELECT * FROM flag_submissions ORDER BY id DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>
<h1>Submit Flag</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel">
    <form method="post">
        <label>Flag</label>
        <input name="flag" placeholder="VSHOP{...}">
        <button>Submit</button>
    </form>
</section>
<section class="panel">
    <h2>Recent Attempts</h2>
    <table>
        <tr><th>Flag</th><th>Status</th><th>Date</th></tr>
        <?php foreach ($submissions as $submission): ?>
            <tr>
                <td><?= $submission['flag'] ?></td>
                <td><?= $submission['correct'] ? 'Correct' : 'Wrong' ?><?= $submission['stage'] ? ' · ' . htmlspecialchars($submission['stage'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?></td>
                <td><?= $submission['submitted_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
