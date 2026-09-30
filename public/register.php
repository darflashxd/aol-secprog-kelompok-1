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
    <title>Register - ShopSecure</title>
</head>
<body>
    <h2>Register</h2>
    <?php if ($error !== ''): ?><p role="alert"><?= e($error) ?></p><?php endif; ?>
    <form action="/actions/do_register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div>
            <label>Nama Lengkap:</label>
            <input type="text" name="name" maxlength="100" autocomplete="name" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" maxlength="150" autocomplete="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" minlength="8" autocomplete="new-password" required>
        </div>
        <small>Password minimal 8 karakter dan harus mengandung huruf besar, huruf kecil, angka, serta simbol.</small>
        <div>
            <label>Role:</label>
            <select name="role" required>
                <option value="customer">Customer</option>
                <option value="seller">Seller</option>
            </select>
        </div>
        <button type="submit">Daftar</button>
    </form>
    <br>
    <a href="/login.php">Sudah punya akun? Login</a>
</body>
</html>
