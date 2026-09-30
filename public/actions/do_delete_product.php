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

header('Location: /views/seller/index.php');
exit;
