<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Tangkap data dari form tambah_soal.php
    $kode_tes    = mysqli_real_escape_string($conn, $_POST['kode_tes']);
    $nomor_soal  = mysqli_real_escape_string($conn, $_POST['nomor_soal']);
    $pertanyaan_a = mysqli_real_escape_string($conn, $_POST['pertanyaan_a']);
    $pertanyaan_b = mysqli_real_escape_string($conn, $_POST['pertanyaan_b']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    // 2. Query Insert menggunakan semua variabel yang ditangkap
    $query = "INSERT INTO soal (kode_tes, nomor_soal, pertanyaan_a, pertanyaan_b, status) 
              VALUES ('$kode_tes', '$nomor_soal', '$pertanyaan_a', '$pertanyaan_b', '$status')";
    
    if (mysqli_query($conn, $query)) {
        // 3. Jika sukses, kembali ke halaman kelola soal dengan status sukses
        header("Location: kelola_soal.php?status=tambah_berhasil");
    } else {
        // Menampilkan pesan error jika query gagal
        echo "Gagal simpan ke database: " . mysqli_error($conn);
    }
    exit();
} else {
    // Jika diakses langsung tanpa POST, kembalikan ke halaman tambah soal
    header("Location: tambah_soal.php");
    exit();
}
?>