<?php

require_once __DIR__ . '/../../../app/includes/auth.php';
require_once __DIR__ . '/../../../app/includes/security.php';
require_once __DIR__ . '/../../../app/config/database.php';

/** @var PDO $pdo */

// Wajib login sebagai seller
require_role('seller');
$sellerId = (int)$_SESSION['user']['id'];

// Ambil daftar produk milik seller yang login
$stmt = $pdo->prepare("
    SELECT
        products.id,
        products.name,
        products.description,
        products.price,
        products.stock,
        categories.name AS category_name
    FROM products
    LEFT JOIN categories
        ON products.category_id = categories.id
    WHERE products.seller_id = ?
    ORDER BY products.id ASC
");

$stmt->execute([$sellerId]);

$products = $stmt->fetchAll();

// Ambil kategori untuk pilihan dropdown
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
    <title>Seller Dashboard</title>
</head>

<body>

    <header>
        <nav>
            <a href="/index.php">Home</a> |
            <a href="/profile.php">Profile</a> |
            <span>Seller: <?= e($_SESSION['user']['name'] ?? '') ?></span>
        </nav>
    </header>

    <h1>Seller Dashboard</h1>

    <h2>Add Product</h2>

    <form action="/actions/do_add_product.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>Product Name</label><br>
        <input type="text" name="name" required>
        <br><br>

        <label>Description</label><br>
        <textarea name="description"></textarea>
        <br><br>

        <label>Category</label><br>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['id']) ?>">
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Price</label><br>
        <input type="number" name="price" min="0" step="0.01" required>
        <br><br>

        <label>Stock</label><br>
        <input type="number" name="stock" min="0" required>
        <br><br>

        <button type="submit">Add Product</button>

    </form>

    <hr>

    <h2>My Products</h2>

    <table border="1" cellpadding="10">

        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($products)): ?>
            <tr>
                <td colspan="7">Belum ada produk.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>

                <tr>

                    <td>
                        <?= e($product['id']) ?>
                    </td>

                    <td>
                        <?= e($product['name']) ?>
                    </td>

                    <td>
                        <?= e($product['category_name'] ?? 'No Category') ?>
                    </td>

                    <td>
                        <?= e($product['description'] ?? '') ?>
                    </td>

                    <td>
                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                    </td>

                    <td>
                        <?= e($product['stock']) ?>
                    </td>

                    <td>
                        <a href="edit.php?id=<?= e($product['id']) ?>">
                            Edit
                        </a>

                        |

                        <form action="/actions/do_delete_product.php" method="POST" style="display:inline;"
                            onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= e($product['id']) ?>">

                            <button type="submit">
                                Delete
                            </button>
                        </form>
                    </td>

                </tr>

                <?php endforeach; ?>
            <?php endif; ?>

        </tbody>

    </table>

</body>

</html>
