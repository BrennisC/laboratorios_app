<?php
require_once __DIR__ . '/db.php';

$q = $_GET['q'] ?? '';
$results = [];

if ($q !== '') {
    // VULN: A05 - Injection through LIKE clause.
    $sql = "SELECT * FROM products WHERE active = true AND (name LIKE '%$q%' OR description LIKE '%$q%')";
    $results = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

include __DIR__ . '/includes/header.php';
?>
<h1>Search Products</h1>
<form method="get">
    <input name="q" placeholder="Search products" value="<?= $q ?>">
    <button>Search</button>
</form>
<?php if ($q !== ''): ?>
    <p>Results for: <?= $q ?></p>
<?php endif; ?>
<section class="grid">
    <?php foreach ($results as $product): ?>
        <article class="card">
            <h2><?= $product['name'] ?></h2>
            <p><?= $product['description'] ?></p>
            <a href="/product.php?id=<?= $product['id'] ?>">Open</a>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
