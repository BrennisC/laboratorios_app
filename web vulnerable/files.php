<?php
$baseDir = __DIR__ . '/fileshare';
$path = $_GET['path'] ?? 'readme.txt';
$fullPath = $baseDir . '/' . $path;
$content = '';

// VULN: A01/A02 - Path is concatenated without normalization or traversal checks.
if (is_file($fullPath)) {
    $content = (string) @file_get_contents($fullPath);
} else {
    $content = 'File not found.';
}

include __DIR__ . '/includes/header.php';
?>
<h1>SecureShare Files</h1>
<section class="panel">
    <p>Internal file sharing portal. The path parameter is intentionally unsafe for lab practice.</p>
    <p>Examples: <a href="/files.php?path=readme.txt">readme.txt</a> · <a href="/files.php?path=status.txt">status.txt</a></p>
    <form method="get">
        <label>File path</label>
        <input name="path" value="<?= htmlspecialchars($path, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <button>Open file</button>
    </form>
</section>
<section class="panel">
    <h2>File Content</h2>
    <pre><?= htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></pre>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
