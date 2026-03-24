<?php
// Script sementara untuk set password semua pegawai = 6 digit pertama NIP
// Jalankan sekali: localhost/bps-psikotes/admin/fix_password.php
// Hapus file ini setelah selesai!

require_once '../backend/config.php';
require_once '../backend/auth_check.php';

$result = mysqli_query($conn, "SELECT nip FROM users WHERE role='peserta'");
$count  = 0;
$errors = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $nip      = $row['nip'];
    $raw_pass = substr($nip, 0, 6);
    $hashed   = password_hash($raw_pass, PASSWORD_DEFAULT);
    $nip_esc  = mysqli_real_escape_string($conn, $nip);
    if (mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE nip='$nip_esc'")) {
        $count++;
    } else {
        $errors++;
    }
}

echo "<h2>Selesai!</h2>";
echo "<p>✅ Password berhasil diset: <strong>$count</strong> pegawai</p>";
echo "<p>❌ Error: <strong>$errors</strong></p>";
echo "<p style='color:red'><strong>Hapus file ini sekarang!</strong></p>";
echo "<p><a href='status_pegawai.php'>← Kembali ke Status Pegawai</a></p>";