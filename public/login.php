<?php
require_once __DIR__ . '/../app/includes/auth.php';
require_once __DIR__ . '/../app/includes/security.php';
if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}
$error = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ShopSecure</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error !== ''): ?><p role="alert"><?= e($error) ?></p><?php endif; ?>
    <form action="/actions/do_login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div>
            <label>Email:</label>
            <input type="email" name="email" maxlength="150" autocomplete="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <br>
    <a href="/register.php">Belum punya akun? Register</a> | 
    <a href="/forgot-password.php">Lupa password?</a>
</body>
</html>
