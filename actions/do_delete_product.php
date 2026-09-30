<?php

session_start();

require_once '../config/database.php';

/** @var PDO $pdo */

$sellerId = 2;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/seller/index.php');
    exit;
}

$productId = $_POST['id'] ?? null;

if (!filter_var($productId, FILTER_VALIDATE_INT)) {
    die('Invalid product ID.');
}

$stmt = $pdo->prepare("
    DELETE FROM products
    WHERE id = ?
      AND seller_id = ?
");

$stmt->execute([
    $productId,
    $sellerId
]);

header('Location: ../views/seller/index.php');
exit;
