<?php include __DIR__ . '/includes/header.php'; ?>
<h1>Internal Repo Viewer</h1>
<section class="panel">
    <p>This page simulates a poorly exposed internal code host. Developers left operational notes and backup references in the repository.</p>
    <p class="muted">OWASP 2025: A02 Security Misconfiguration, A03 Software Supply Chain Failures, A04 Cryptographic Failures.</p>
</section>

<section class="grid">
    <article class="card">
        <h2>vulnshop-app</h2>
        <p>Last commit: disable strict file checks for migration.</p>
        <p><a class="button secondary" href="/files.php?path=../backup/gogs_config.bak">View leaked config</a></p>
    </article>
    <article class="card">
        <h2>ops-backups</h2>
        <p>Last commit: add db.zip recovery note.</p>
        <p><a class="button secondary" href="/files.php?path=../backup/db_zip_note.txt">View backup note</a></p>
    </article>
    <article class="card">
        <h2>legacy-health</h2>
        <p>Last commit: add health check routes for internal nodes.</p>
        <p><a class="button secondary" href="/healthcheck.php">Open health check</a></p>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
