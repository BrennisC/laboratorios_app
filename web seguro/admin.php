<?php
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/security.php';
require_admin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $image = filter_input(INPUT_POST, 'image', FILTER_VALIDATE_URL);

    if ($name !== '' && $description !== '' && $price !== false && $price > 0 && $image) {
        $stmt = db()->prepare('INSERT INTO products (name, description, price, image, active) VALUES (?, ?, ?, ?, true)');
        $stmt->execute([$name, $description, $price, $image]);
        log_security_event('admin_product_created', 'Admin created product');
        $message = 'Product created';
    } else {
        $message = 'Invalid product data';
    }
}

$products = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<h1>Admin Panel</h1>
<?php if ($message): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
<section class="panel">
    <h2>Create Product</h2>
    <form method="post">
        <?= csrf_field() ?>
        <label>Name</label>
        <input name="name" required maxlength="180">
        <label>Description</label>
        <textarea name="description" required></textarea>
        <label>Price</label>
        <input name="price" type="number" min="0.01" step="0.01" required>
        <label>Image URL</label>
        <input name="image" type="url" required value="https://picsum.photos/seed/secure-new/500/300">
        <button>Create</button>
    </form>
</section>
<section class="panel">
    <h2>Products</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th></tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= e($product['id']) ?></td>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['description']) ?></td>
                <td>$<?= e($product['price']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
<p><a class="button" href="/import_xml.php">Safe XML Import</a> <a class="button" href="/import_json.php">Safe JSON Import</a> <a class="button" href="/upload.php">Safe Upload</a> <a class="button" href="/rce.php">Safe RCE Demo</a></p>
<?php include __DIR__ . '/includes/footer.php'; ?>
