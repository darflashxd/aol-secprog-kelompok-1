<?php
require_once __DIR__ . '/../../../app/includes/auth.php';
require_login();
if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit('403 Forbidden');
}
?>
<h1>Admin Dashboard</h1>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Halaman manajemen untuk role Admin.</p>
</body>
</html>
