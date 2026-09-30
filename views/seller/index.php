<?php

session_start();

require_once '../../config/database.php';
$sellerId = 2;

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard</title>
</head>

<body>

    <h1>Seller Dashboard</h1>

    <h2>Add Product</h2>

    <form action="../../actions/do_add_product.php" method="POST">

        <label>Product Name</label><br>
        <input type="text" name="name" required>
        <br><br>

        <label>Description</label><br>
        <textarea name="description"></textarea>
        <br><br>

        <label>Category</label><br>
        <select name="category_id" required>
            <option value="1">Hardware Security Keys</option>
            <option value="2">Network Defense Devices</option>
            <option value="3">Cybersecurity Books</option>
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

            <?php foreach ($products as $product): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($product['id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($product['name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($product['category_name'] ?? 'No Category') ?>
                </td>

                <td>
                    <?= htmlspecialchars($product['description'] ?? '') ?>
                </td>

                <td>
                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                </td>

                <td>
                    <?= htmlspecialchars($product['stock']) ?>
                </td>

                <td>
                    <a href="edit.php?id=<?= htmlspecialchars($product['id']) ?>">
                        Edit
                    </a>

                    |

                    <form action="../../actions/do_delete_product.php" method="POST" style="display:inline;"
                        onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

                        <button type="submit">
                            Delete
                        </button>
                    </form>
                </td>

            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</body>

</html>