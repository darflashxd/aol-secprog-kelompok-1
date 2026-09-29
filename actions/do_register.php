<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$role     = trim($_POST['role'] ?? 'customer');
$password = $_POST['password'] ?? '';

if (strlen($name) < 2 || strlen($name) > 100) {
    $_SESSION['error'] = "Nama lengkap harus antara 2 sampai 100 karakter.";
    header("Location: /register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Format email tidak valid.";
    header("Location: /register.php");
    exit;
}

if (!in_array($role, ['customer', 'seller'], true)) {
    $_SESSION['error'] = "Role yang dipilih tidak valid.";
    header("Location: /register.php");
    exit;
}

$hasUpper  = preg_match('/[A-Z]/', $password);
$hasLower  = preg_match('/[a-z]/', $password);
$hasNumber = preg_match('/[0-9]/', $password);
$hasSymbol = preg_match('/[^a-zA-Z0-9]/', $password);

if (strlen($password) < 8 || !$hasUpper || !$hasLower || !$hasNumber || !$hasSymbol) {
    $_SESSION['error'] = "Password minimal 8 karakter, harus ada huruf besar, huruf kecil, angka, dan simbol.";
    header("Location: /register.php");
    exit;
}

try {
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $checkStmt->execute([':email' => $email]);

    if ($checkStmt->fetch()) {
        $_SESSION['error'] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
        header("Location: /register.php");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $insertStmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, status)
        VALUES (:name, :email, :password, :role, 'active')
    ");
    $insertStmt->execute([
        ':name'     => $name,
        ':email'    => $email,
        ':password' => $hashedPassword,
        ':role'     => $role,
    ]);

    $_SESSION['success'] = "Registrasi berhasil! Silakan login dengan akun barumu.";
    header("Location: /login.php");
    exit;

} catch (PDOException $e) {
    error_log("Register Error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi gangguan sistem. Silakan coba lagi nanti.";
    header("Location: /register.php");
    exit;
}