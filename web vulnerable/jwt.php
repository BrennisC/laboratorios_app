<?php
require_once __DIR__ . '/db.php';

function b64url($data) { return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); }

$email = $_POST['email'] ?? 'admin@example.com';
$user = db()->query("SELECT * FROM users WHERE email = '$email' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// VULN: A07/A04 - Predictable weak JWT secret and user-controlled email lookup.
$header = b64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
$body = b64url(json_encode(['sub' => $user['id'] ?? 0, 'email' => $email, 'role' => $user['role'] ?? 'guest']));
$signature = b64url(hash_hmac('sha256', "$header.$body", 'jwt-secret', true));
$token = "$header.$body.$signature";

include __DIR__ . '/includes/header.php';
?>
<h1>API Token</h1>
<section class="panel">
    <form method="post"><label>Email</label><input name="email" value="<?= htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><button>Generate token</button></form>
</section>
<section class="panel"><h2>JWT</h2><textarea rows="5"><?= htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
