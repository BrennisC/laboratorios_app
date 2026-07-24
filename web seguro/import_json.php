<?php
require_once __DIR__ . '/lib/security.php';
require_admin();

$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $payload = $_POST['payload'] ?? '';
    try {
        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $allowed = [
            'name' => isset($data['name']) ? (string) $data['name'] : '',
            'price' => isset($data['price']) ? (float) $data['price'] : 0,
        ];
        $output = 'Accepted JSON fields: ' . json_encode($allowed);
    } catch (JsonException $e) {
        log_security_event('invalid_json_import', 'Admin submitted invalid JSON');
        $output = 'Invalid JSON.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Safe JSON Importer</h1>
<div class="panel">
    <form method="post">
        <?= csrf_field() ?>
        <label>JSON</label>
        <textarea name="payload" rows="6">{"name":"Safe item","price":10}</textarea>
        <button>Parse</button>
    </form>
    <?php if ($output): ?><pre><?= e($output) ?></pre><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
