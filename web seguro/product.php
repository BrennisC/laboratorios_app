<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';

$id = int_param('id', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    verify_csrf();
    $comment = trim($_POST['comment'] ?? '');
    if ($comment !== '' && strlen($comment) <= 1000) {
        $stmt = db()->prepare('INSERT INTO reviews (product_id, user_id, comment, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute([$id, current_user_id(), $comment]);
    }
}

$stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND active = true');
$stmt->execute([$id]);
$product = $stmt->fetch();

$reviewsStmt = db()->prepare('SELECT r.*, u.name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? ORDER BY r.id DESC');
$reviewsStmt->execute([$id]);
$reviews = $reviewsStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<?php if (!$product): ?>
    <h1>Product not found</h1>
<?php else: ?>
    <article class="panel">
        <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" style="max-width: 100%; border-radius: 12px;">
        <h1><?= e($product['name']) ?></h1>
        <p><?= e($product['description']) ?></p>
        <p><strong>$<?= e($product['price']) ?></strong></p>
        <a class="button" href="/cart.php?action=add&id=<?= e($product['id']) ?>">Add to cart</a>
    </article>
    <section class="panel">
        <h2>Reviews</h2>
        <?php if (is_logged_in()): ?>
            <form method="post">
                <?= csrf_field() ?>
                <label>Comment</label>
                <textarea name="comment" maxlength="1000" required></textarea>
                <button>Post review</button>
            </form>
        <?php else: ?>
            <p><a href="/login.php">Login</a> to post a review.</p>
        <?php endif; ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card">
                <strong><?= e($review['name']) ?></strong>
                <p><?= e($review['comment']) ?></p>
                <small><?= e($review['created_at']) ?></small>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
