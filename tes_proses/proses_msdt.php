<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. VALIDASI & AMBIL INPUT (Sesuai dengan name="jawaban[nomor_soal]")
if (!isset($_POST['jawaban']) || count($_POST['jawaban']) < 64) {
    die("Gagal: Data jawaban tidak lengkap atau belum dikirim.");
}

$jawaban_user = $_POST['jawaban'];
$jawaban_indexed = [];

// Menyusun array agar urutan matriks 1-64 terjaga
for ($i = 1; $i <= 64; $i++) {
    if (!isset($jawaban_user[$i])) {
        die("Soal nomor $i belum dijawab.");
    }
    $jawaban_indexed[] = $jawaban_user[$i];
}

// 2. BENTUK MATRIKS 8x8
$M = [];
for ($i = 0; $i < 8; $i++) {
    $M[$i] = array_slice($jawaban_indexed, $i * 8, 8);
}

// 3. HITUNG SKOR A (BARIS) & B (KOLOM)
$A = array_fill(0, 8, 0);
$B = array_fill(0, 8, 0);
for ($i = 0; $i < 8; $i++) {
    for ($j = 0; $j < 8; $j++) {
        if ($M[$i][$j] === "A") { $A[$i]++; }
        if ($M[$i][$j] === "B") { $B[$j]++; }
    }
}

// 4. HITUNG 8 DIMENSI DASAR
$koreksi = [1, 2, 1, 0, 3, -1, 0, 4];
$label = ["Ds", "Mi", "Au", "Co", "Bu", "Dv", "Ba", "E"];
$dimensi = [];
for ($k = 0; $k < 8; $k++) {
    $dimensi[$label[$k]] = $A[$k] + $B[$k] + $koreksi[$k];
}

// 5. HITUNG MODEL TO / RO / E / O
$model = [];
$model["TO"] = $dimensi["Au"] + $dimensi["Co"] + $dimensi["Ba"] + $dimensi["E"];
$model["RO"] = $dimensi["Mi"] + $dimensi["Co"] + $dimensi["Dv"] + $dimensi["E"];
$model["E"]  = $dimensi["Bu"] + $dimensi["Dv"] + $dimensi["Ba"] + $dimensi["E"];
$model["O"]  = $dimensi["Ds"];

// 6. TENTUKAN MODEL DOMINAN
arsort($model);
$dominant = array_key_first($model);

// 7. DEBUG OUTPUT (Untuk Verifikasi Layar)
echo "<h2>Hasil Perhitungan Matriks MSDT</h2>";
echo "<pre>";
echo "DIMENSI DASAR:\n";
print_r($dimensi);
echo "\nSKOR MODEL:\n";
print_r($model);
echo "\nMODEL DOMINAN: <strong>$dominant</strong>";
echo "</pre>";
?>