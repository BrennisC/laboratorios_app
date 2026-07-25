<?php
require_once __DIR__ . '/lib/db.php';
include __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $stmt = db()->prepare('SELECT * FROM products WHERE active = true AND (name LIKE ? OR description LIKE ?)');
    $term = '%' . $q . '%';
    $stmt->execute([$term, $term]);
    $results = $stmt->fetchAll();
}
?>
<h1>Search Products</h1>
<form method="get">
    <input name="q" placeholder="Search products" value="<?= e($q) ?>">
    <button>Search</button>
</form>
<?php if ($q !== ''): ?><p>Results for: <?= e($q) ?></p><?php endif; ?>
<section class="grid">
    <?php foreach ($results as $product): ?>
        <article class="card">
            <h2><?= e($product['name']) ?></h2>
            <p><?= e($product['description']) ?></p>
            <a href="/product.php?id=<?= e($product['id']) ?>">Open</a>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
