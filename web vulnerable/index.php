<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/includes/header.php';

$products = db()->query('SELECT * FROM products WHERE active = true')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="notice">
    <strong>Training warning:</strong> this store is intentionally vulnerable. Use it only on your local machine.
</section>
<h1>Product Store</h1>
<p class="muted">Practice OWASP Top 10 2017 vulnerabilities in a realistic PHP shop.</p>
<p><a class="button" href="/machine.php">Start HTB-style machine</a></p>
<section class="grid">
    <?php foreach ($products as $product): ?>
        <article class="card">
            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
            <h2><?= $product['name'] ?></h2>
            <p><?= $product['description'] ?></p>
            <p><strong>$<?= $product['price'] ?></strong></p>
            <a class="button" href="/product.php?id=<?= $product['id'] ?>">View product</a>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
