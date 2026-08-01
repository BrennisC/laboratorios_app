<?php
require_once __DIR__ . '/db.php';
$solved = is_logged_in() ? solved_stages() : [];
$checks = [
    'recon' => ['Recon', 'Review public pages, robots.txt, backups and debug output.', '/debug.php', 'What sensitive information is exposed?'],
    'user' => ['Access Control', 'Login and test whether numeric IDs expose another user profile.', '/profile.php?id=2', 'Does the backend verify ownership?'],
    'admin' => ['Admin Access', 'Compare the admin page behavior with and without request parameters.', '/admin.php', 'Where is the role decision coming from?'],
    'nodes' => ['Internal Node Discovery', 'Use the node tracker to identify internal services that are not visible from the storefront.', '/nodes.php', 'What internal hosts does the app trust?'],
    'ssrf' => ['Health Check SSRF', 'Test whether the health check fetches internal URLs supplied by the user.', '/healthcheck.php', 'Can a user make the server request internal resources?'],
    'files' => ['File Sharing Traversal', 'Inspect the file sharing route and test whether path input is safely constrained.', '/files.php', 'Can path input escape the intended directory?'],
    'repo' => ['Code Host Leak', 'Review the internal repo view for leaked configuration and credentials.', '/repo.php', 'What secrets were committed by mistake?'],
    'rce' => ['Execution Surface', 'Inspect upload and command features for unsafe server-side behavior.', '/admin.php?role=admin#upload', 'Can user-controlled content become executable?'],
    'root' => ['Local File Read', 'Test parsers and import features with controlled lab payloads.', '/import_xml.php', 'Can the app read files it should not expose?'],
];

include __DIR__ . '/includes/header.php';
?>
<h1>Interactive Practice Guide</h1>
<section class="panel">
    <p>This guide keeps the app normal, but gives students a structured way to find vulnerabilities instead of randomly clicking payloads.</p>
    <p><strong>Method:</strong> observe the feature, write a hypothesis, test it safely, then explain the real-world impact.</p>
</section>

<section class="grid">
    <?php foreach ($checks as $stage => $check): ?>
        <article class="card guide-card <?= isset($solved[$stage]) ? 'unlocked' : '' ?>">
            <p class="tag"><?= isset($solved[$stage]) ? 'Solved' : 'Practice' ?> · <?= htmlspecialchars(strtoupper($stage), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($check[0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($check[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p><strong>Question:</strong> <?= htmlspecialchars($check[3], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p><a class="button secondary" href="<?= htmlspecialchars($check[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Investigate</a></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel notebook">
    <h2>Student Notes</h2>
    <p><strong>Entry point:</strong> Which page or parameter did you test?</p>
    <p><strong>Weak control:</strong> What validation, authorization or sanitization is missing?</p>
    <p><strong>Proof:</strong> What result proves the vulnerability?</p>
    <p><strong>Impact:</strong> What could this expose or allow in a real system?</p>
    <p><strong>Fix:</strong> What specific change would reduce the risk?</p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
