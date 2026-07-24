<?php
require_once __DIR__ . '/lib/db.php';
include __DIR__ . '/includes/header.php';

$products = db()->query('SELECT * FROM products WHERE active = 1 ORDER BY id')->fetchAll();
?>
<section class="notice">
    <strong>Secure version:</strong> this app demonstrates the defenses missing in the vulnerable lab.
</section>
<h1>Product Store</h1>
<section class="grid">
    <?php foreach ($products as $product): ?>
        <article class="card">
            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            <h2><?= e($product['name']) ?></h2>
            <p><?= e($product['description']) ?></p>
            <p><strong>$<?= e($product['price']) ?></strong></p>
            <a class="button" href="/product.php?id=<?= e($product['id']) ?>">View product</a>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
