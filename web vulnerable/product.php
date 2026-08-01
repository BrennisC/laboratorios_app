<?php
require_once __DIR__ . '/db.php';

$id = $_GET['id'] ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Anonymous';
    $comment = $_POST['comment'] ?? '';
    // VULN: A05 - SQL injection and stored XSS.
    db()->exec("INSERT INTO reviews (product_id, user_name, comment, created_at) VALUES ($id, '$name', '$comment', CURRENT_TIMESTAMP)");
}

// VULN: A05 - SQL injection in numeric id.
$product = db()->query("SELECT * FROM products WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
$reviews = db()->query("SELECT * FROM reviews WHERE product_id = $id ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>
<?php if (!$product): ?>
    <h1>Product not found</h1>
<?php else: ?>
    <article class="panel">
        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" style="max-width: 100%; border-radius: 12px;">
        <h1><?= $product['name'] ?></h1>
        <p><?= $product['description'] ?></p>
        <p><strong>$<?= $product['price'] ?></strong></p>
        <a class="button" href="/cart.php?action=add&id=<?= $product['id'] ?>">Add to cart</a>
    </article>
    <section class="panel">
        <h2>Reviews</h2>
        <form method="post">
            <label>Name</label>
            <input name="name" value="<?= $_SESSION['name'] ?? '' ?>">
            <label>Comment</label>
            <textarea name="comment"></textarea>
            <button>Post review</button>
        </form>
        <?php foreach ($reviews as $review): ?>
            <div class="card">
                <strong><?= $review['user_name'] ?></strong>
                <p><?= $review['comment'] ?></p>
                <small><?= $review['created_at'] ?></small>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
