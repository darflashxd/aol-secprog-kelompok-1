<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

function redirect_to(string $path): never {
    header('Location: ' . $path);
    exit;
}

function set_recovery_message(string $message): never {
    $_SESSION['recovery_message'] = $message;
    redirect_to('/forgot-password.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
    http_response_code(400);
    exit('Permintaan tidak valid. Muat ulang halaman dan coba lagi.');
}

$action = $_POST['action'] ?? 'request';
require_once __DIR__ . '/../config/database.php';

if ($action === 'request') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    if ($email === false || strlen($email) > 150) {
        set_recovery_message('Jika email terdaftar, instruksi pemulihan akan dikirim.');
    }

    $stmt = $pdo->prepare('SELECT email FROM users WHERE email = :email AND status = \'active\' LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $pdo->beginTransaction();
        try {
            $delete = $pdo->prepare('DELETE FROM password_resets WHERE email = :email');
            $delete->execute(['email' => $user['email']]);
            // Use the database clock for both token creation and expiry checks.
            $insert = $pdo->prepare('INSERT INTO password_resets (email, token_hash, expires_at) VALUES (:email, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))');
            $insert->execute(['email' => $user['email'], 'token_hash' => $tokenHash]);
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Password reset request failed: ' . $e->getMessage());
            set_recovery_message('Jika email terdaftar, instruksi pemulihan akan dikirim.');
        }

        $appUrl = rtrim(getenv('APP_URL') ?: '', '/');
        if ($appUrl === '') {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            if (!preg_match('/\A[a-zA-Z0-9.-]+(?::[0-9]{1,5})?\z/', $host)) {
                $host = 'localhost';
            }
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $appUrl = $scheme . '://' . $host;
        }
        $resetUrl = $appUrl . '/reset-password.php?token=' . rawurlencode($token) . '&email=' . rawurlencode($user['email']);
        $subject = 'Reset password ShopSecure';
        $body = "Gunakan tautan berikut untuk membuat password baru (berlaku 1 jam):\n\n{$resetUrl}\n\nJika Anda tidak meminta reset password, abaikan email ini.";
        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $sent = mail($user['email'], $subject, $body, $headers);

        // Local development can show the link when PHP mail is not configured.
        // Never expose reset tokens in production responses.
        if (getenv('APP_ENV') === 'development') {
            $_SESSION['dev_reset_url'] = $resetUrl;
        }
        if (!$sent) {
            error_log('Password reset email could not be sent. Configure PHP mail transport.');
        }
    }

    set_recovery_message('Jika email terdaftar, instruksi pemulihan akan dikirim.');
}

if ($action === 'reset') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($email === false || !is_string($token) || !preg_match('/\A[a-f0-9]{64}\z/', $token)) {
        set_recovery_message('Tautan reset tidak valid atau sudah kedaluwarsa.');
    }
    if (!is_string($password) || strlen($password) < 8 || strlen($password) > 4096
        || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)
        || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $_SESSION['reset_error'] = 'Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, angka, serta simbol.';
        redirect_to('/reset-password.php?token=' . rawurlencode($token) . '&email=' . rawurlencode((string) $email));
    }

    $tokenHash = hash('sha256', $token);
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT id FROM password_resets WHERE email = :email AND token_hash = :token_hash AND expires_at > NOW() LIMIT 1 FOR UPDATE');
        $stmt->execute(['email' => $email, 'token_hash' => $tokenHash]);
        $reset = $stmt->fetch();
        if (!$reset) {
            $pdo->rollBack();
            set_recovery_message('Tautan reset tidak valid atau sudah kedaluwarsa.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE users SET password = :password WHERE email = :email');
        $update->execute(['password' => $passwordHash, 'email' => $email]);
        $delete = $pdo->prepare('DELETE FROM password_resets WHERE email = :email');
        $delete->execute(['email' => $email]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Password reset failed: ' . $e->getMessage());
        set_recovery_message('Reset password belum dapat diproses. Coba minta tautan baru.');
    }

    $_SESSION['auth_error'] = 'Password berhasil diubah. Silakan login.';
    redirect_to('/login.php');
}

http_response_code(400);
exit('Aksi tidak valid.');
