<?php
// Home / Catalog Page
require_once __DIR__ . '/../app/includes/auth.php';
require_once __DIR__ . '/../app/includes/security.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ShopSecure - Home</title>
</head>
<body>
    <h1>Selamat Datang di ShopSecure</h1>
    <p>Katalog produk publik.</p>
    <nav>
        <?php if (is_logged_in()): ?>
            <span>Masuk sebagai <?= e($_SESSION['user']['name'] ?? '') ?></span> |
            <a href="/profile.php">Profile</a>
            <?php if (($_SESSION['user']['role'] ?? '') === 'seller'): ?>
                | <a href="/views/seller/index.php">Seller Dashboard</a>
            <?php elseif (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                | <a href="/views/admin/index.php">Admin Dashboard</a>
            <?php endif; ?>
            | <form action="/actions/do_logout.php" method="POST" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a href="/login.php">Login</a> |
            <a href="/register.php">Register</a>
        <?php endif; ?>
    </nav>
</body>
</html>
