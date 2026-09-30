<?php
// Halaman Profil & Ganti Foto
// TODO: Tampilkan data profil user, form edit profil, dan form upload foto
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - ShopSecure</title>
</head>
<body>
    <h2>User Profile</h2>

    <h3>Edit Profil</h3>
    <form action="/actions/do_update_profile.php" method="POST">
        <div>
            <label>Nama:</label>
            <input type="text" name="name">
        </div>
        <div>
            <label>No HP:</label>
            <input type="text" name="phone">
        </div>
        <div>
            <label>Bio:</label>
            <textarea name="bio"></textarea>
        </div>
        <button type="submit">Simpan Profil</button>
    </form>

    <hr>

    <h3>Ganti Foto Profil</h3>
    <form action="/actions/do_update_photo.php" method="POST" enctype="multipart/form-data">
        <div>
            <label>Pilih File Foto:</label>
            <input type="file" name="avatar" accept="image/*">
        </div>
        <button type="submit">Upload Foto</button>
    </form>
</body>
</html>
