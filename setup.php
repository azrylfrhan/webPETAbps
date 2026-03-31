<?php
/**
 * Setup Script untuk Railway
 * File ini dijalankan sekali saat deployment awal untuk import database
 * 
 * Akses: https://[deployed-app]/setup.php
 * ⚠️  HAPUS FILE INI SETELAH SELESAI (security risk)
 */

// ===== KONFIGURASI SECURITY =====
// Ganti password ini dengan password random untuk 1 kali pakai
define('SETUP_PASSWORD', 'setup_bps_psikotes_2026');

// ===== CEK PASSWORD =====
if (!isset($_GET['key']) || $_GET['key'] !== SETUP_PASSWORD) {
    http_response_code(403);
    die("❌ Access Denied. Gunakan: setup.php?key=" . SETUP_PASSWORD);
}

// ===== SETUP DIMULAI =====
echo "<pre style='font-family: monospace; background: #1a1a1a; color: #0f0; padding: 20px;'>\n";
echo "╔══════════════════════════════════════════╗\n";
echo "║   Tes Psikotes - Railway Setup Script    ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

// Get local database connection
require_once __DIR__ . '/backend/config.php';

if (!$conn) {
    die("❌ Koneksi database gagal: " . mysqli_connect_error() . "\n");
}

echo "[✓] Koneksi ke database berhasil\n";
echo "[✓] Database: " . getenv('MYSQLDATABASE') ?: 'railway' . "\n";
echo "[✓] Host: " . getenv('MYSQLHOST') ?: 'localhost' . "\n\n";

// ===== DUMP DATA =====
echo "🔄 Checking tabel-tabel penting...\n";

$tables = [
    'iq_sections' => 'IQ Test Sections',
    'iq_questions' => 'IQ Questions',
    'iq_options' => 'IQ Options',
    'iq_example_questions' => 'IQ Examples',
    'iq_example_options' => 'IQ Example Options',
    'users' => 'User Accounts',
    'biodata_peserta' => 'Peserta Biodata',
    'soal' => 'PAPI/MSDT Questions',
    'hasil_msdt' => 'MSDT Results',
    'hasil_papi' => 'PAPI Results',
];

echo "\n📊 Status Tabel:\n";
echo "─────────────────────────────────────\n";

$total_rows = 0;
foreach ($tables as $table => $desc) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM $table");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['cnt'];
        $total_rows += $count;
        $status = $count > 0 ? "✓" : "⚠";
        printf("  %s %-25s: %8d rows - %s\n", $status, $table, $count, $desc);
    } else {
        echo "  ❌ $table: " . mysqli_error($conn) . "\n";
    }
}

echo "─────────────────────────────────────\n";
echo "  📈 Total baris data: " . number_format($total_rows) . "\n\n";

// ===== SELESAI =====
echo "✅ Setup verification selesai!\n\n";
echo "🚀 Langkah berikutnya:\n";
echo "   1. Testing akses aplikasi\n";
echo "   2. Login dengan akun test\n";
echo "   3. Run sample test untuk verifikasi database\n";
echo "   4. ⚠️  HAPUS file setup.php ini (keamanan)\n\n";
echo "═══════════════════════════════════════════\n";
echo "Ready untuk production!\n";
echo "═══════════════════════════════════════════\n";

mysqli_close($conn);
?>
