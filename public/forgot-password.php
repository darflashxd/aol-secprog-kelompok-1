<?php
// Form Forgot Password
// TODO: Buat form lupa password & reset password yang mengarah ke actions/do_forgot_password.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - ShopSecure</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="/actions/do_forgot_password.php" method="POST">
        <div>
            <label>Masukkan Email Akun:</label>
            <input type="email" name="email" required>
        </div>
        <button type="submit">Kirim Link Reset</button>
    </form>
    <br>
    <a href="/login.php">Kembali ke Login</a>
</body>
</html>
