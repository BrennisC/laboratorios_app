<?php
require_once __DIR__ . '/lib/security.php';
require_admin();

$message = '';
$uploaded = null;
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'application/pdf' => 'pdf',
    'text/plain' => 'txt',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $file = $_FILES['file'] ?? null;
    if (!$file || !isset($file['error']) || is_array($file['error'])) {
        $message = 'Invalid upload request.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed.';
    } elseif (($file['size'] ?? 0) <= 0 || $file['size'] > MAX_UPLOAD_BYTES) {
        $message = 'File must be between 1 byte and 1 MB.';
    } else {
        $originalName = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $tmpPath = (string) $file['tmp_name'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath) ?: '';
        $expectedExtension = $allowedTypes[$mime] ?? null;

        if ($expectedExtension === null || $extension !== $expectedExtension) {
            log_security_event('upload_rejected', 'Rejected upload with invalid MIME type or extension');
            $message = 'Only JPG, PNG, PDF and TXT files are allowed.';
        } else {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0750, true);
            }

            $storedName = bin2hex(random_bytes(16)) . '.' . $expectedExtension;
            $targetPath = UPLOAD_DIR . '/' . $storedName;

            if (move_uploaded_file($tmpPath, $targetPath)) {
                chmod($targetPath, 0640);
                log_security_event('safe_upload_created', 'Admin uploaded a validated file');
                $uploaded = [
                    'original' => $originalName,
                    'stored' => $storedName,
                    'mime' => $mime,
                    'size' => $file['size'],
                ];
                $message = 'File uploaded safely.';
            } else {
                $message = 'Could not store the file.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Safe File Upload</h1>
<?php if ($message): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
<section class="panel">
    <h2>Upload Controls</h2>
    <p>This version rejects executable extensions by allowing only verified JPG, PNG, PDF and TXT files. Files are renamed with random server-side names.</p>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label>File</label>
        <input name="file" type="file" accept=".jpg,.jpeg,.png,.pdf,.txt" required>
        <button>Upload safely</button>
    </form>
</section>

<?php if ($uploaded): ?>
    <section class="panel">
        <h2>Stored File</h2>
        <table>
            <tr><th>Original name</th><td><?= e($uploaded['original']) ?></td></tr>
            <tr><th>Stored name</th><td><?= e($uploaded['stored']) ?></td></tr>
            <tr><th>MIME type</th><td><?= e($uploaded['mime']) ?></td></tr>
            <tr><th>Size</th><td><?= e($uploaded['size']) ?> bytes</td></tr>
        </table>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
