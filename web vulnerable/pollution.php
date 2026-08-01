<?php
$role = $_GET['role'] ?? 'user';
if (is_array($role)) {
    $role = end($role);
}

// VULN: A01/A06 - Parameter pollution lets duplicated role parameters influence authorization logic.
include __DIR__ . '/includes/header.php';
?>
<h1>Preference Preview</h1>
<section class="panel">
    <p>Effective role: <?= htmlspecialchars((string) $role, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
    <?php if ($role === 'admin'): ?><p class="notice">Admin preview enabled.</p><?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
