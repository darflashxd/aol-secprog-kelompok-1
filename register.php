<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - ShopSecure</title>
</head>
<body>
    <h2>Register</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/actions/do_register.php" method="POST">
        <div>
            <label>Nama Lengkap:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Role:</label>
            <select name="role">
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
