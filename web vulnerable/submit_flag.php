<?php
require_once __DIR__ . '/db.php';

$message = '';
$flags = challenge_flags();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flag = trim($_POST['flag'] ?? '');
    $correct = in_array($flag, $flags, true) ? 1 : 0;
    $safeFlag = str_replace("'", "''", $flag);

    db()->exec("INSERT INTO flag_submissions (flag, correct, submitted_at) VALUES ('$safeFlag', $correct, NOW())");
    $message = $correct ? 'Correct flag.' : 'Incorrect flag.';
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
                <td><?= $submission['correct'] ? 'Correct' : 'Wrong' ?></td>
                <td><?= $submission['submitted_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
