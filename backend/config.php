<?php
// Pengaturan Database
// Priority: Environment variables (Railway) > Fallback: Localhost (XAMPP)
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "bps_psikotes";
$port = getenv('MYSQLPORT') ?: 3306;

// Membuat Koneksi dengan MySQLi
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Periksa Koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set Charset ke UTF-8 (Penting agar teks soal terbaca sempurna)
mysqli_set_charset($conn, "utf8mb4");

// Mulai session jika belum dimulai (Berguna untuk dashboard admin & login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>