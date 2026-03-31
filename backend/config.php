<?php
// Detect Railway runtime so we can avoid accidental localhost fallback in production.
$isRailway = (bool) (getenv('RAILWAY_ENVIRONMENT') ?: getenv('RAILWAY_PROJECT_ID'));

$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: null;
$user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: null;
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: null;
$db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: null;
$port = (int) (getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);

// Support URL-based connection variables used by some providers.
$dbUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL');
if ($dbUrl) {
    $parts = parse_url($dbUrl);
    if ($parts !== false) {
        if (empty($host) && !empty($parts['host'])) {
            $host = $parts['host'];
        }
        if (empty($user) && isset($parts['user'])) {
            $user = $parts['user'];
        }
        if ($pass === null && isset($parts['pass'])) {
            $pass = $parts['pass'];
        }
        if (empty($db) && !empty($parts['path'])) {
            $db = ltrim($parts['path'], '/');
        }
        if (empty($port) && !empty($parts['port'])) {
            $port = (int) $parts['port'];
        }
    }
}

// Local development fallback (XAMPP).
if (!$isRailway) {
    $host = $host ?: 'localhost';
    $user = $user ?: 'root';
    $pass = $pass ?? '';
    $db   = $db ?: 'bps_psikotes';
}

if (empty($host) || empty($user) || empty($db)) {
    $missing = [];
    if (empty($host)) {
        $missing[] = 'MYSQLHOST';
    }
    if (empty($user)) {
        $missing[] = 'MYSQLUSER';
    }
    if (empty($db)) {
        $missing[] = 'MYSQLDATABASE';
    }
    die('Koneksi database gagal: environment variable belum lengkap (' . implode(', ', $missing) . ').');
}

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error() . 
        " | Host: $host");
}

mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>