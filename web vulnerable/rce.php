<?php
require_once __DIR__ . '/db.php';

$command = $_POST['command'] ?? 'whoami';
$output = '';
$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
$isLocalRequest = in_array($remoteAddress, ['127.0.0.1', '::1'], true);

if (!$isLocalRequest) {
    http_response_code(403);
    exit('This lab endpoint is available only from localhost.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Deliberately vulnerable: user input is executed by the operating system.
    $output = shell_exec($command . ' 2>&1') ?? '';
}

include __DIR__ . '/includes/header.php';
?>
<h1>Remote Command Execution Lab</h1>
<section class="panel">
    <p>This endpoint is intentionally vulnerable for local lab practice. Do not expose it to a real network.</p>
    <form method="post">
        <label>Command</label>
        <input name="command" value="<?= htmlspecialchars($command, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <button>Run command</button>
    </form>
</section>

<?php if ($output !== ''): ?>
    <section class="panel">
        <h2>Command Output</h2>
        <pre><?= htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
