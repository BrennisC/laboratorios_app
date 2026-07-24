<?php
require_once __DIR__ . '/config.php';

class FileWriter
{
    public $path = 'uploads/deserialized.txt';
    public $content = 'Created by insecure deserialization';

    public function __destruct()
    {
        file_put_contents(__DIR__ . '/' . $this->path, $this->content);
    }
}

$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = $_POST['payload'] ?? '';
    // A8: user-controlled serialized data.
    $result = print_r(unserialize($payload), true);
}

include __DIR__ . '/includes/header.php';
?>
<h1>Deserialize Tool</h1>
<div class="panel">
    <form method="post">
        <label>Serialized payload</label>
        <textarea name="payload" rows="6">O:10:"FileWriter":2:{s:4:"path";s:19:"uploads/pwned.txt";s:7:"content";s:17:"deserialized data";}</textarea>
        <button>Deserialize</button>
    </form>
    <?php if ($result): ?>
        <pre><?= $result ?></pre>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
