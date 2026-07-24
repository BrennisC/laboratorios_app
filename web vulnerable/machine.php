<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/includes/header.php';

$totalSubmissions = (int) db()->query('SELECT COUNT(*) FROM flag_submissions')->fetchColumn();
$correctSubmissions = (int) db()->query('SELECT COUNT(*) FROM flag_submissions WHERE correct = 1')->fetchColumn();
?>
<section class="hero">
    <p class="tag">Retired Machine · Easy/Medium · PHP · OWASP Top 10 2017</p>
    <h1>VulnShop</h1>
    <p>A small online store was rushed into production with debug mode enabled, weak authentication, broken authorization and unsafe parsers.</p>
    <p>Your mission is to enumerate the app, compromise a user, reach admin functionality and read the final system flag.</p>
    <a class="button" href="/submit_flag.php">Submit flags</a>
    <a class="button secondary" href="/hints.php">View hints</a>
</section>

<section class="grid">
    <article class="card">
        <h2>Recon Flag</h2>
        <p>Find exposed application internals.</p>
        <p class="muted">Category: A3/A6</p>
    </article>
    <article class="card">
        <h2>User Flag</h2>
        <p>Abuse weak object ownership checks.</p>
        <p class="muted">Category: A5</p>
    </article>
    <article class="card">
        <h2>Admin Flag</h2>
        <p>Reach administrative functionality without a real admin session.</p>
        <p class="muted">Category: A5/A2</p>
    </article>
    <article class="card">
        <h2>Root Flag</h2>
        <p>Use an unsafe parser to read a local file.</p>
        <p class="muted">Category: A4</p>
    </article>
</section>

<section class="panel">
    <h2>Progress</h2>
    <p><strong>Correct submissions:</strong> <?= $correctSubmissions ?></p>
    <p><strong>Total attempts:</strong> <?= $totalSubmissions ?></p>
</section>

<section class="panel">
    <h2>Rules of Engagement</h2>
    <p>Run this only on localhost. Do not use these techniques against systems you do not own or have explicit permission to test.</p>
    <p>Target URL: <code>http://127.0.0.1:8000</code></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
