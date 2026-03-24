<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    
    // 1. Generate Password Acak 8 Karakter
    $characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ'; // Tanpa karakter membingungkan seperti O, 0, I, l
    $random_password = substr(str_shuffle($characters), 0, 8);
    
    // 2. Hash password tersebut
    $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET password = '$hashed_password' WHERE nip = '$nip'";
    
    if (mysqli_query($conn, $query)) {
        // Kembali ke detail dengan membawa password asli (plain text) di URL agar bisa dibaca Admin
        header("Location: detail_pegawai.php?nip=$nip&new_pass=$random_password");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}