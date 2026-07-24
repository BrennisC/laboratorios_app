<?php
require_once __DIR__ . '/db.php';

$message = '';

// A5: broken access control. Role can be supplied by query string.
$role = $_GET['role'] ?? current_user_role();

if ($role !== 'admin') {
    $message = 'You are not admin, but try adding ?role=admin to the URL.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '0';
    $image = $_POST['image'] ?? '';

    // A1 and A7: SQL injection and stored XSS in product fields.
    db()->exec("INSERT INTO products (name, description, price, image, active) VALUES ('$name', '$description', $price, '$image', 1)");
    $message = 'Product created';
}

$products = db()->query('SELECT * FROM products ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>
<h1>Admin Panel</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<?php if ($role === 'admin'): ?>
    <section class="notice">
        <strong>Admin flag:</strong> VSHOP{admin_broken_access_2017}
    </section>
<?php endif; ?>
<section class="panel">
    <h2>Create Product</h2>
    <form method="post">
        <label>Name</label>
        <input name="name">
        <label>Description</label>
        <textarea name="description"></textarea>
        <label>Price</label>
        <input name="price" value="10">
        <label>Image URL</label>
        <input name="image" value="https://picsum.photos/seed/new/500/300">
        <button>Create</button>
    </form>
</section>
<section class="panel">
    <h2>Products</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th></tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= $product['name'] ?></td>
                <td><?= $product['description'] ?></td>
                <td>$<?= $product['price'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
<p><a class="button" href="/import_xml.php">XML Importer</a> <a class="button" href="/upload.php">Upload</a> <a class="button" href="/deserialize.php">Deserialize Tool</a></p>
<?php include __DIR__ . '/includes/footer.php'; ?>
