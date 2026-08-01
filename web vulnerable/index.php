<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/includes/header.php';

$products = db()->query('SELECT * FROM products WHERE active = true ORDER BY id ASC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="shop-hero">
    <div>
        <p class="tag">Tech Store · Fast Delivery · Business Essentials</p>
        <h1>Upgrade your workspace with reliable tech.</h1>
        <p>Find laptops, accessories, storage and office devices selected for students, developers and small teams.</p>
        <div class="hero-actions">
            <a class="button" href="/search.php">Shop Products</a>
            <a class="button secondary" href="/cart.php">View Cart</a>
        </div>
    </div>
    <aside class="promo-card">
        <p class="tag">Featured Deal</p>
        <h2>Portable SSD 1TB</h2>
        <p>Fast storage for backups, projects and daily work.</p>
        <strong>$110</strong>
        <a class="button" href="/product.php?id=7">View Deal</a>
    </aside>
</section>

<section class="trust-strip">
    <span>Secure checkout</span>
    <span>Student-friendly prices</span>
    <span>Team-ready devices</span>
    <span>Fast local delivery</span>
</section>

<section class="section-heading">
    <p class="tag">Catalog</p>
    <h2>Popular Products</h2>
    <p class="muted">Browse the most requested devices and accessories.</p>
</section>

<section class="grid">
    <?php foreach ($products as $product): ?>
        <article class="card product-card">
            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            <h2><?= htmlspecialchars($product['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($product['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p class="price">$<?= htmlspecialchars($product['price'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <a class="button" href="/product.php?id=<?= $product['id'] ?>">View Product</a>
        </article>
    <?php endforeach; ?>
</section>

<section class="panel callout shop-callout">
    <div>
        <h2>Need something specific?</h2>
        <p>Search the full catalog or sign in to review your saved orders.</p>
    </div>
    <a class="button" href="/search.php">Search Catalog</a>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
