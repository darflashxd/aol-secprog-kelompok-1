<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$error = $_SESSION['reset_error'] ?? '';
unset($_SESSION['reset_error']);
$validLink = is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token)
    && is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - ShopSecure</title>
</head>
<body>
    <h1>Reset Password</h1>
    <?php if ($error !== ''): ?><p role="alert"><?= e($error) ?></p><?php endif; ?>
    <?php if ($validLink): ?>
        <form action="/actions/do_forgot_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="reset">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <div>
                <label for="password">Password baru:</label>
                <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <small>Minimal 8 karakter, dengan huruf besar, huruf kecil, angka, dan simbol.</small>
            <button type="submit">Simpan Password Baru</button>
        </form>
    <?php else: ?>
        <p role="alert">Tautan reset tidak valid. Minta tautan baru.</p>
        <a href="/forgot-password.php">Minta tautan reset</a>
    <?php endif; ?>
</body>
</html>
