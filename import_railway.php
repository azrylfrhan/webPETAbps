<?php
/**
 * Railway Database Import Script
 * Membaca file SQL dan mengimportnya ke Railway MySQL
 */

// Railway connection details
$railway_host = "mysql.railway.internal";
$railway_user = "root";
$railway_pass = "fTiMPxhUEVeKsmIyMMaOFzoeBTOYQqgQ";
$railway_db = "railway";
$railway_port = 3306;

// SQL file path
$sql_file = __DIR__ . '/bps_psikotes_full.sql';

if (!file_exists($sql_file)) {
    die("❌ ERROR: File tidak ditemukan: $sql_file\n");
}

echo "🔄 Menghubungkan ke Railway MySQL...\n";

// Connect to Railway
$conn = mysqli_connect($railway_host, $railway_user, $railway_pass, $railway_db, $railway_port);

if (!$conn) {
    die("❌ Koneksi gagal: " . mysqli_connect_error() . "\n");
}

echo "✓ Koneksi berhasil ke Railway\n";
echo "🔄 Membaca file SQL (" . filesize($sql_file) . " bytes)...\n";

// Read SQL file
$sql_content = file_get_contents($sql_file);

if ($sql_content === false) {
    die("❌ ERROR: Tidak bisa membaca file SQL\n");
}

// Split SQL statements
$queries = array_filter(
    array_map(
        'trim',
        explode(';', $sql_content)
    ),
    fn($q) => !empty($q) && strpos($q, '--') !== 0
);

echo "📊 Total pernyataan SQL: " . count($queries) . "\n";
echo "🔄 Mulai import ke Railway...\n\n";

$success_count = 0;
$error_count = 0;

foreach ($queries as $index => $query) {
    $query = trim($query);
    if (empty($query)) {
        continue;
    }

    if (mysqli_query($conn, $query . ';')) {
        $success_count++;
        if ($success_count % 10 === 0) {
            echo "  ✓ {$success_count} pernyataan dijalankan\n";
        }
    } else {
        $error_count++;
        echo "  ❌ Error di pernyataan ke-{$index}: " . mysqli_error($conn) . "\n";
        echo "     Query: " . substr($query, 0, 80) . "...\n";
    }
}

echo "\n";
echo "================================\n";
echo "✓ Import selesai!\n";
echo "  ✓ Sukses: {$success_count} pernyataan\n";
echo "  ❌ Error: {$error_count} pernyataan\n";
echo "================================\n";

// Verify tables
echo "\n🔍 Verifikasi tabel di Railway:\n";
$result = mysqli_query($conn, "SHOW TABLES;");

if ($result) {
    $table_count = mysqli_num_rows($result);
    echo "  Jumlah tabel: {$table_count}\n";
    
    $tables = [];
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    echo "  Tabel: " . implode(", ", array_slice($tables, 0, 5));
    if (count($tables) > 5) {
        echo ", ... +" . (count($tables) - 5) . " tabel\n";
    } else {
        echo "\n";
    }
}

mysqli_close($conn);

echo "\n✅ Proses import Railway selesai!\n\n";
echo "🚀 Langkah berikutnya:\n";
echo "   1. Atur environment variables di Railway dashboard\n";
echo "   2. Push code ke Railway: git push railway main\n";
echo "   3. Test aplikasi di: https://[your-railway-domain].railway.app\n";
?>
