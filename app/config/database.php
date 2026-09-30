<?php
/**
 * Database Connection Configuration (Secure PDO)
 * 
 * Security Features:
 * 1. Strict Exception Mode (PDO::ERRMODE_EXCEPTION)
 * 2. Disabled Emulated Prepares (PDO::ATTR_EMULATE_PREPARES => false) to enforce true server-side prepared statements.
 * 3. UTF-8 Multi-byte charset (utf8mb4) to prevent multibyte-truncation SQLi vulnerabilities.
 * 4. Error details are not leaked to end-users in production.
 */

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'aol_secprog';
// XAMPP's local MariaDB commonly uses root with no password. Docker overrides
// these values through the DB_USER and DB_PASS environment variables.
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // In production, log error privately, never output raw error to client
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please ensure the database server is running.");
}
