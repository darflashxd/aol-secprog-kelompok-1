<?php

session_start();

require_once '../config/database.php';
$sellerId = 2;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/seller/index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$categoryId = $_POST['category_id'] ?? null;
$price = $_POST['price'] ?? null;
$stock = $_POST['stock'] ?? null;

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
    INSERT INTO products
        (seller_id, category_id, name, description, price, stock)
    VALUES
        (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $sellerId,
    $categoryId,
    $name,
    $description,
    $price,
    $stock
]);

header('Location: ../views/seller/index.php');
exit;
