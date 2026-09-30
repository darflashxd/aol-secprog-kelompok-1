<?php
require_once __DIR__ . '/../../app/includes/auth.php';
require_once __DIR__ . '/../../app/includes/security.php';

function login_error(string $message): never {
    $_SESSION['auth_error'] = $message;
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}
if (!verify_csrf_token()) {
    login_error('Sesi form tidak valid atau sudah kedaluwarsa. Silakan coba lagi.');
}
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
if ($email === false || !is_string($password) || $password === '') {
    login_error('Email atau password tidak valid.');
}

require_once __DIR__ . '/../../app/config/database.php';
$stmt = $pdo->prepare('SELECT id, name, email, password, role, avatar, status FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) {
    login_error('Email atau password tidak valid.');
}
if ($user['status'] !== 'active') {
    login_error('Akun ini tidak aktif. Hubungi administrator.');
}

session_regenerate_id(true);
unset($user['password'], $user['status']);
$_SESSION['user'] = $user;
unset($_SESSION['auth_error'], $_SESSION['csrf_token']);
header('Location: /index.php');
exit;
