<?php
require_once __DIR__ . '/../../app/includes/auth.php';
require_once __DIR__ . '/../../app/includes/security.php';

function register_error(string $message): never {
    $_SESSION['auth_error'] = $message;
    header('Location: /register.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}
if (!verify_csrf_token()) {
    register_error('Sesi form tidak valid atau sudah kedaluwarsa. Silakan coba lagi.');
}
$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
if (!is_string($name) || !preg_match('/\A.{1,100}\z/us', $name)) {
    register_error('Nama wajib diisi dan maksimal 100 karakter.');
}
if ($email === false || strlen($email) > 150) {
    register_error('Masukkan alamat email yang valid.');
}
if (!is_string($password) || strlen($password) < 8 || strlen($password) > 4096
    || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
    register_error('Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, angka, serta simbol.');
}
if (!in_array($role, ['customer', 'seller'], true)) {
    register_error('Role yang dipilih tidak valid.');
}

require_once __DIR__ . '/../../app/config/database.php';
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    register_error('Email sudah terdaftar. Silakan login atau gunakan email lain.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hash, 'role' => $role]);
} catch (PDOException $e) {
    // The unique constraint remains the final guard against concurrent duplicate registrations.
    if ($e->getCode() === '23000') {
        register_error('Email sudah terdaftar. Silakan login atau gunakan email lain.');
    }
    error_log('Registration failed: ' . $e->getMessage());
    register_error('Pendaftaran belum dapat diproses. Silakan coba lagi.');
}

unset($_SESSION['csrf_token']);
$_SESSION['auth_error'] = 'Pendaftaran berhasil. Silakan login.';
header('Location: /login.php');
exit;
