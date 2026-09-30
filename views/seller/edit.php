<?php

session_start();

require_once '../../config/database.php';

/** @var PDO $pdo */

$sellerId = 2;

$productId = $_GET['id'] ?? null;

if (!filter_var($productId, FILTER_VALIDATE_INT)) {
    die('Invalid product ID.');
}

$stmt = $pdo->prepare("
    SELECT
        id,
        category_id,
        name,
        description,
        price,
        stock
    FROM products
    WHERE id = ?
      AND seller_id = ?
");

$stmt->execute([
    $productId,
    $sellerId
]);

$product = $stmt->fetch();

if (!$product) {
    die('Product not found or access denied.');
}

$categoryStmt = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");

$categories = $categoryStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
</head>

<body>

    <h1>Edit Product</h1>

    <form action="../../actions/do_update_product.php" method="POST">

        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

        <label>Product Name</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <br><br>

        <label>Description</label><br>

        <textarea name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

        <br><br>

        <label>Category</label><br>

        <select name="category_id" required>

            <?php foreach ($categories as $category): ?>

            <option value="<?= htmlspecialchars($category['id']) ?>"
                <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
            </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label>Price</label><br>

        <input type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars($product['price']) ?>"
            required>

        <br><br>

        <label>Stock</label><br>

        <input type="number" name="stock" min="0" value="<?= htmlspecialchars($product['stock']) ?>" required>

        <br><br>

        <button type="submit">Update Product</button>

    </form>

    <br>

    <a href="index.php">Back to Seller Dashboard</a>

</body>

</html>