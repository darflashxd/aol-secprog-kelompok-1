<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';
$message = $_SESSION['recovery_message'] ?? '';
$devResetUrl = $_SESSION['dev_reset_url'] ?? '';
unset($_SESSION['recovery_message'], $_SESSION['dev_reset_url']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - ShopSecure</title>
</head>
<body>
    <h1>Lupa Password</h1>
    <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
    <?php if ($devResetUrl !== ''): ?>
        <p>Mode development: email belum terkirim. Gunakan tautan reset ini:</p>
        <p><a href="<?= e($devResetUrl) ?>">Buka form reset password</a></p>
    <?php endif; ?>
    <form action="/actions/do_forgot_password.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="request">
        <div>
            <label for="email">Email akun:</label>
            <input id="email" type="email" name="email" maxlength="150" autocomplete="email" required>
        </div>
        <button type="submit">Kirim Link Reset</button>
    </form>
    <p><a href="/login.php">Kembali ke Login</a></p>
</body>
</html>
