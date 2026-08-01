<?php
require_once __DIR__ . '/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $body = $_POST['message'] ?? '';
    // VULN: A05 - Stored XSS and SQL injection in contact form.
    db()->exec("INSERT INTO contacts (name, email, message, created_at) VALUES ('$name', '$email', '$body', CURRENT_TIMESTAMP)");
    $message = 'Message sent.';
}
$contacts = db()->query('SELECT * FROM contacts ORDER BY id DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/includes/header.php';
?>
<h1>Contact</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel"><form method="post"><label>Name</label><input name="name"><label>Email</label><input name="email"><label>Message</label><textarea name="message"></textarea><button>Send</button></form></section>
<section class="panel"><h2>Recent messages</h2><?php foreach ($contacts as $contact): ?><article><strong><?= $contact['name'] ?></strong><p><?= $contact['message'] ?></p></article><?php endforeach; ?></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
