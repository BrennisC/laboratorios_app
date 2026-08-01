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
$writtenFile = __DIR__ . '/uploads/pwned.txt';
$writtenFileUrl = '/uploads/pwned.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = $_POST['payload'] ?? '';
    // VULN: A08 - Software or Data Integrity Failures. User-controlled serialized data.
    $result = print_r(unserialize($payload), true);
}

include __DIR__ . '/includes/header.php';
?>
<h1>Deserialize Tool</h1>
<div class="panel">
    <p>This lab intentionally unserializes user-controlled PHP objects. The sample payload abuses the <code>FileWriter</code> destructor to write a file under <code>uploads/</code>.</p>
    <form method="post">
        <label>Serialized payload</label>
        <textarea name="payload" rows="6">O:10:"FileWriter":2:{s:4:"path";s:19:"uploads/pwned.txt";s:7:"content";s:17:"deserialized data";}</textarea>
        <button>Deserialize</button>
    </form>
    <?php if ($result): ?>
        <pre><?= $result ?></pre>
    <?php endif; ?>
    <?php if (is_file($writtenFile)): ?>
        <p>Written file: <a href="<?= $writtenFileUrl ?>"><?= $writtenFileUrl ?></a></p>
        <pre><?= htmlspecialchars(file_get_contents($writtenFile), ENT_QUOTES, 'UTF-8') ?></pre>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
