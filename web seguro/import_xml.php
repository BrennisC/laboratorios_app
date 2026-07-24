<?php
require_once __DIR__ . '/lib/security.php';
require_admin();

$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $xmlText = $_POST['xml'] ?? '';
    $previous = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $loaded = $dom->loadXML($xmlText, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_use_internal_errors($previous);
    $output = $loaded ? 'XML parsed without external network/entity expansion.' : 'Invalid XML.';
}

include __DIR__ . '/includes/header.php';
?>
<h1>Safe XML Importer</h1>
<div class="panel">
    <form method="post">
        <?= csrf_field() ?>
        <label>XML</label>
        <textarea name="xml" rows="8"><product><name>Safe item</name></product></textarea>
        <button>Import</button>
    </form>
    <?php if ($output): ?><p class="notice"><?= e($output) ?></p><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
