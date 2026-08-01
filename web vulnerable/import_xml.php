<?php
require_once __DIR__ . '/db.php';

$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xmlText = $_POST['xml'] ?? '';

    // VULN: A05 - Injection. XXE enabled deliberately.
    libxml_disable_entity_loader(false);
    $dom = new DOMDocument();
    $dom->loadXML($xmlText, LIBXML_NOENT | LIBXML_DTDLOAD);
    $output = $dom->textContent;
}

include __DIR__ . '/includes/header.php';
?>
<h1>XML Product Importer</h1>
<div class="panel">
    <form method="post">
        <label>XML</label>
        <textarea name="xml" rows="10"><!DOCTYPE product [ <!ENTITY xxe SYSTEM "file:///etc/passwd"> ]><product><name>&xxe;</name></product></textarea>
        <button>Import</button>
    </form>
    <?php if ($output): ?>
        <h2>Parsed Content</h2>
        <pre><?= $output ?></pre>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
