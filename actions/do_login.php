<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Email dan password wajib diisi.";
    header('Location: /login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Kombinasi email dan password salah.";
        header('Location: /login.php');
        exit;
    }

    if ($user['status'] !== 'active') {
        $_SESSION['error'] = "Akun Anda sedang dinonaktifkan.";
        header('Location: /login.php');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'     => $user['id'],
        'name'   => $user['name'],
        'email'  => $user['email'],
        'role'   => $user['role'],
        'status' => $user['status']
    ];

    if ($user['role'] === 'admin') {
        header('Location: /views/admin/index.php');
    } elseif ($user['role'] === 'seller') {
        header('Location: /views/seller/index.php');
    } else {
        header('Location: /index.php');
    }
    exit;

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi gangguan sistem. Silakan coba lagi nanti.";
    header('Location: /login.php');
    exit;
}
