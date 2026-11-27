<?php
require_once __DIR__ . '/../../../app/includes/auth.php';
require_once __DIR__ . '/../../../app/includes/security.php';
require_once __DIR__ . '/../../../app/config/database.php';

require_role('admin');

$userStmt = $pdo->query(
    'SELECT id, name, email, role, status, created_at FROM users ORDER BY id ASC'
);
$users = $userStmt->fetchAll();

$categoryStmt = $pdo->query(
    'SELECT id, name, description, created_at FROM categories ORDER BY name ASC'
);
$categories = $categoryStmt->fetchAll();

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);
$currentAdminId = (int) $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - ShopSecure</title>
</head>
<body>
    <header>
        <nav>
            <a href="/index.php">Home</a> |
            <a href="/profile.php">Profile</a> |
            <span>Admin: <?= e($_SESSION['user']['name'] ?? '') ?></span>
        </nav>
    </header>

    <main>
        <h1>Admin Dashboard</h1>
        <p>Kelola akun pengguna dan kategori produk.</p>

        <?php if (is_array($flash) && isset($flash['message'])): ?>
            <p role="status"><?= e((string) $flash['message']) ?></p>
        <?php endif; ?>

        <section aria-labelledby="users-heading">
            <h2 id="users-heading">Kelola User</h2>
            <p>Ubah role atau status akun. Akun yang di-suspend tidak dapat menggunakan fitur yang dilindungi autentikasi.</p>

            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$users): ?>
                        <tr><td colspan="7">Belum ada user.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= e((string) $user['id']) ?></td>
                                <td><?= e($user['name']) ?></td>
                                <td><?= e($user['email']) ?></td>
                                <td><?= e($user['role']) ?></td>
                                <td><?= e($user['status']) ?></td>
                                <td><?= e($user['created_at']) ?></td>
                                <td>
                                    <?php if ((int) $user['id'] === $currentAdminId): ?>
                                        <em>Akun admin yang sedang digunakan tidak dapat diubah dari halaman ini.</em>
                                    <?php else: ?>
                                        <form action="/actions/admin/do_update_user.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="user_id" value="<?= e((string) $user['id']) ?>">
                                            <label>
                                                Role
                                                <select name="role" required>
                                                    <?php foreach (['customer', 'seller', 'admin'] as $role): ?>
                                                        <option value="<?= e($role) ?>" <?= $user['role'] === $role ? 'selected' : '' ?>><?= e(ucfirst($role)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </label>
                                            <label>
                                                Status
                                                <select name="status" required>
                                                    <?php foreach (['active', 'suspended'] as $status): ?>
                                                        <option value="<?= e($status) ?>" <?= $user['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </label>
                                            <button type="submit">Simpan</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><strong>Catatan:</strong> Penghapusan fisik user tidak disediakan karena relasi database menggunakan ON DELETE CASCADE untuk produk dan pesanan. Suspend mempertahankan riwayat transaksi.</p>
        </section>

        <hr>

        <section aria-labelledby="categories-heading">
            <h2 id="categories-heading">Kelola Kategori</h2>

            <h3>Tambah Kategori</h3>
            <form action="/actions/admin/do_add_category.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>
                    Nama kategori
                    <input type="text" name="name" maxlength="100" required>
                </label>
                <label>
                    Deskripsi
                    <input type="text" name="description" maxlength="255">
                </label>
                <button type="submit">Tambah Kategori</button>
            </form>

            <h3>Daftar Kategori</h3>
            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$categories): ?>
                        <tr><td colspan="4">Belum ada kategori.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= e((string) $category['id']) ?></td>
                                <td colspan="2">
                                    <form action="/actions/admin/do_update_category.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="category_id" value="<?= e((string) $category['id']) ?>">
                                        <label>
                                            Nama
                                            <input type="text" name="name" maxlength="100" value="<?= e($category['name']) ?>" required>
                                        </label>
                                        <label>
                                            Deskripsi
                                            <input type="text" name="description" maxlength="255" value="<?= e($category['description'] ?? '') ?>">
                                        </label>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="/actions/admin/do_delete_category.php" method="POST" onsubmit="return confirm('Hapus kategori ini? Produk terkait akan menjadi tanpa kategori.');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="category_id" value="<?= e((string) $category['id']) ?>">
                                        <button type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p>Menghapus kategori akan mengatur category_id produk terkait menjadi NULL sesuai foreign key database.</p>
        </section>
    </main>
</body>
</html>
