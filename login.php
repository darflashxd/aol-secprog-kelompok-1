<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: /views/admin/index.php');
    } elseif ($_SESSION['user']['role'] === 'seller') {
        header('Location: /views/seller/index.php');
    } else {
        header('Location: /index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ShopSecure</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success']); ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
        <p style="color: blue;"><?= htmlspecialchars($_SESSION['info']); ?></p>
        <?php unset($_SESSION['info']); ?>
    <?php endif; ?>

    <form action="/actions/do_login.php" method="POST">
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <br>
    <a href="/register.php">Belum punya akun? Register</a> | 
    <a href="/forgot-password.php">Lupa password?</a>
</body>
</html>
