<?php
require_once __DIR__ . '/db.php';

$target = $_POST['target'] ?? 'http://files.internal/status.txt';
$output = '';

$internalRoutes = [
    'http://files.internal/status.txt' => __DIR__ . '/fileshare/status.txt',
    'http://git.internal/version.txt' => __DIR__ . '/backup/gogs_config.bak',
    'http://backup.internal/internal_nodes.txt' => __DIR__ . '/backup/internal_nodes.txt',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VULN: A05/A06 - SSRF-style trust of a user-controlled health check target.
    if (isset($internalRoutes[$target])) {
        $output = (string) @file_get_contents($internalRoutes[$target]);
    } else {
        $context = stream_context_create(['http' => ['timeout' => 2]]);
        $output = (string) @file_get_contents($target, false, $context);
    }

    if ($output === '') {
        $output = 'No response or empty response.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Node Health Check</h1>
<section class="panel">
    <p>Operations uses this tool to check internal services. It trusts user-supplied URLs.</p>
    <p class="muted">OWASP 2025: A05 Injection, A06 Insecure Design.</p>
    <form method="post">
        <label>Target URL</label>
        <input name="target" value="<?= htmlspecialchars($target, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <button>Run health check</button>
    </form>
</section>
<?php if ($output !== ''): ?>
    <section class="panel">
        <h2>Response</h2>
        <pre><?= htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
