<?php
require_once __DIR__ . '/lib/security.php';
require_admin();

$selected = $_POST['command'] ?? 'server_time';
$output = '';
$commands = [
    'server_time' => 'Server time',
    'php_version' => 'PHP version',
    'current_role' => 'Current role',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (!array_key_exists($selected, $commands)) {
        log_security_event('rce_attempt_blocked', 'Rejected non-allowlisted diagnostic command');
        $selected = 'server_time';
        $output = 'Command rejected. Only allowlisted diagnostics are available.';
    } elseif ($selected === 'server_time') {
        $output = date(DATE_ATOM);
    } elseif ($selected === 'php_version') {
        $output = PHP_VERSION;
    } elseif ($selected === 'current_role') {
        $output = current_user_role();
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Safe RCE Demo</h1>
<section class="panel">
    <h2>Command Execution Defense</h2>
    <p>The vulnerable app sends user input to the operating system. This safe version never calls shell functions and only exposes fixed diagnostics.</p>
    <form method="post">
        <?= csrf_field() ?>
        <label>Diagnostic</label>
        <select name="command">
            <?php foreach ($commands as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $selected === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button>Run diagnostic</button>
    </form>
</section>

<?php if ($output !== ''): ?>
    <section class="panel">
        <h2>Output</h2>
        <pre><?= e($output) ?></pre>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
