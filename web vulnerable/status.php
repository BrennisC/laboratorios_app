<?php
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    redirect('/login.php');
}

$solved = solved_stages();
$achievements = [
    'recon' => ['Recon complete', 'Found leaked internals and the first flag.'],
    'user' => ['Initial foothold', 'Accessed user-owned data through IDOR.'],
    'admin' => ['Admin surface reached', 'Abused broken access control on the admin panel.'],
    'rce' => ['Code execution proven', 'Executed commands or uploaded executable content.'],
    'root' => ['Root objective', 'Read the final local flag.'],
];

include __DIR__ . '/includes/header.php';
?>
<h1>Lab Status</h1>
<section class="panel">
    <p>This page shows practice progress. It unlocks achievements only after valid flag submissions.</p>
</section>
<section class="grid">
    <?php foreach ($achievements as $stage => $achievement): ?>
        <article class="card achievement <?= isset($solved[$stage]) ? 'unlocked' : 'locked' ?>">
            <h2><?= isset($solved[$stage]) ? '[x]' : '[ ]' ?> <?= htmlspecialchars($achievement[0], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($achievement[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p class="muted">Stage: <?= htmlspecialchars($stage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
