<?php include __DIR__ . '/includes/header.php'; ?>
<h1>Internal Node Tracker</h1>
<section class="panel">
    <p>This dashboard simulates an internal node inventory exposed by poor access control.</p>
    <p class="muted">OWASP 2025: A01 Broken Access Control, A02 Security Misconfiguration.</p>
</section>

<section class="grid">
    <article class="card">
        <h2>files.internal</h2>
        <p>SecureShare file portal.</p>
        <p><strong>Status:</strong> degraded</p>
        <p><a class="button secondary" href="/files.php">Open file portal</a></p>
    </article>
    <article class="card">
        <h2>git.internal</h2>
        <p>Internal code host used by developers.</p>
        <p><strong>Status:</strong> online</p>
        <p><a class="button secondary" href="/repo.php">Open repo view</a></p>
    </article>
    <article class="card">
        <h2>backup.internal</h2>
        <p>Legacy backup index referenced by operations.</p>
        <p><strong>Status:</strong> unknown</p>
        <p><a class="button secondary" href="/files.php?path=../backup/internal_nodes.txt">Investigate leak</a></p>
    </article>
</section>

<section class="panel notebook">
    <h2>Practice Question</h2>
    <p>If a public user can read internal node names, what later attacks become easier?</p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
