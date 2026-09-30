<?php

require_once __DIR__ . '/../../app/includes/auth.php';
require_once __DIR__ . '/../../app/includes/security.php';
require_once __DIR__ . '/../../app/config/database.php';

/** @var PDO $pdo */

// Wajib login sebagai seller
require_role('seller');
$sellerId = (int)$_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /views/seller/index.php');
    exit;
}

if (!verify_csrf_token()) {
    http_response_code(400);
    die('Invalid CSRF token.');
}

$productId = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$categoryId = $_POST['category_id'] ?? null;
$price = $_POST['price'] ?? null;
$stock = $_POST['stock'] ?? null;

if (!filter_var($productId, FILTER_VALIDATE_INT)) {
    die('Invalid product ID.');
}

if ($name === '') {
    die('Product name is required.');
}

if (!filter_var($categoryId, FILTER_VALIDATE_INT)) {
    die('Invalid category.');
}

if (!is_numeric($price) || $price < 0) {
    die('Invalid price.');
}

if (
    filter_var(
        $stock,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0]]
    ) === false
) {
    die('Invalid stock.');
}

$stmt = $pdo->prepare("
    UPDATE products
    SET
        category_id = ?,
        name = ?,
        description = ?,
        price = ?,
        stock = ?
    WHERE id = ?
      AND seller_id = ?
");

$stmt->execute([
    $categoryId,
    $name,
    $description,
    $price,
    $stock,
    $productId,
    $sellerId
]);

header('Location: /views/seller/index.php');
exit;
