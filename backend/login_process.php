<?php
session_start();
require 'config.php'; 
require_once 'biodata_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitasi input
    $nip      = mysqli_real_escape_string($conn, $_POST['nip']);
    $password = $_POST['password'];

    if (empty($nip) || empty($password)) {
        header("Location: ../login.php?error=empty");
        exit();
    }

    // 1. Cari user berdasarkan NIP
    $query  = "SELECT * FROM users WHERE nip = '$nip'";
    $result = mysqli_query($conn, $query);
    $user   = mysqli_fetch_assoc($result);

    if ($user) {
        // 2. Verifikasi password (menggunakan password_hash saat impor CSV nanti)
        if (password_verify($password, $user['password'])) {
            
            // 3. Cek Izin Akses (is_active)
            // Admin selalu bisa login, Peserta harus diaktifkan (is_active = 1)
            if ($user['role'] !== 'admin' && $user['is_active'] == 0) {
                header("Location: ../login.php?error=akses_ditolak");
                exit();
            }

            // 4. Set Session (Termasuk Pangkat)
            $_SESSION['nip']          = $user['nip'];
            $_SESSION['nama']         = $user['nama'];
            $_SESSION['role']         = $user['role'];
            $_SESSION['jabatan']      = $user['jabatan'];
            $_SESSION['pangkat']      = $user['pangkat']; // Data pangkat masuk ke session
            $_SESSION['satuan_kerja'] = $user['satuan_kerja'];

            // 5. Redirect berdasarkan Role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                if (!biodataTableExists($conn) || !pesertaSudahIsiBiodata($conn, $user['nip'])) {
                    header("Location: ../biodata.php");
                } else {
                    header("Location: ../dashboard.php");
                }
            }
            exit();

        } else {
            // Password salah
            header("Location: ../login.php?error=wrong");
            exit();
        }
    } else {
        // NIP tidak ditemukan di database
        header("Location: ../login.php?error=wrong");
        exit();
    }
} else {
    // Jika akses langsung ke file ini tanpa POST
    header("Location: ../login.php");
    exit();
}