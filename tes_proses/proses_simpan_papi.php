<?php
require_once 'config.php';
require_once 'proses_papi.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['nip'])) { die("Akses ditolak."); }
$nip = $_SESSION['nip'];

if (!isset($_POST['jawaban_papi']) || count($_POST['jawaban_papi']) < 90) {
    echo "<script>alert('Mohon jawab semua 90 soal.'); window.history.back();</script>";
    exit;
}

$jawaban = $_POST['jawaban_papi'];
$hasil_skor = hitungSkorPapi($jawaban, $mapping_papi);

// Validasi Total Skor (Harus 90 sesuai standar PAPI)
if (array_sum($hasil_skor) !== 90) {
    die("Gagal: Total skor adalah " . array_sum($hasil_skor) . ". Harusnya 90. Periksa mapping soal Anda.");
}

// Pastikan urutan kolom sesuai dengan tabel hasil_papi di database Anda
$stmt = $conn->prepare("
    INSERT INTO hasil_papi (
        nip, G, L, I, T, V, S, R, D, C, E, 
        N, A, P, X, B, O, K, F, W, Z, tanggal_tes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

// "s" untuk nip (string), sisanya 20 "i" untuk skor dimensi (integer)
$stmt->bind_param(
    "siiiiiiiiiiiiiiiiiiii",
    $nip, 
    $hasil_skor['G'], $hasil_skor['L'], $hasil_skor['I'], $hasil_skor['T'], $hasil_skor['V'],
    $hasil_skor['S'], $hasil_skor['R'], $hasil_skor['D'], $hasil_skor['C'], $hasil_skor['E'],
    $hasil_skor['N'], $hasil_skor['A'], $hasil_skor['P'], $hasil_skor['X'], $hasil_skor['B'],
    $hasil_skor['O'], $hasil_skor['K'], $hasil_skor['F'], $hasil_skor['W'], $hasil_skor['Z']
);

if ($stmt->execute()) {
    header("Location: ../dashboard.php?status=tes_selesai");
} else {
    echo "Error Database: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>