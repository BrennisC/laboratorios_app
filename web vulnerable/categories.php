<?php
require_once __DIR__ . '/db.php';

$message = '';
if (($_GET['delete'] ?? '') !== '') {
    db()->exec('DELETE FROM categories WHERE id = ' . $_GET['delete']);
    $message = 'Category deleted.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    // VULN: A05 - CRUD fields are concatenated into SQL.
    if ($id !== '') {
        db()->exec("UPDATE categories SET name = '$name', description = '$description' WHERE id = $id");
    } else {
        db()->exec("INSERT INTO categories (name, description) VALUES ('$name', '$description')");
    }
    $message = 'Category saved.';
}

$categories = db()->query('SELECT * FROM categories ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/includes/header.php';
?>
<h1>Categories</h1>
<?php if ($message): ?><p class="notice"><?= $message ?></p><?php endif; ?>
<section class="panel"><form method="post"><label>ID for update</label><input name="id"><label>Name</label><input name="name"><label>Description</label><textarea name="description"></textarea><button>Save category</button></form></section>
<section class="panel"><table><tr><th>ID</th><th>Name</th><th>Description</th><th>Action</th></tr><?php foreach ($categories as $category): ?><tr><td><?= $category['id'] ?></td><td><?= $category['name'] ?></td><td><?= $category['description'] ?></td><td><a href="/categories.php?delete=<?= $category['id'] ?>">Delete</a></td></tr><?php endforeach; ?></table></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
