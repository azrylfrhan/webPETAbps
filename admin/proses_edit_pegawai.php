<?php
require_once '../backend/auth_admin.php';
require_once '../backend/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$nip = isset($_POST['nip']) ? mysqli_real_escape_string($conn, $_POST['nip']) : '';
$nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : '';
$satuan_kerja = isset($_POST['satuan_kerja']) ? mysqli_real_escape_string($conn, $_POST['satuan_kerja']) : '';
$jabatan = isset($_POST['jabatan']) ? mysqli_real_escape_string($conn, $_POST['jabatan']) : '';
$pangkat = isset($_POST['pangkat']) ? mysqli_real_escape_string($conn, $_POST['pangkat']) : '';

if (empty($nip)) {
    echo json_encode(['success' => false, 'error' => 'NIP tidak boleh kosong']);
    exit;
}

// Verifikasi bahwa NIP ada dan role = peserta
$cek = mysqli_query($conn, "SELECT nip FROM users WHERE nip = '$nip' AND role = 'peserta'");
if (mysqli_num_rows($cek) === 0) {
    echo json_encode(['success' => false, 'error' => 'Pegawai tidak ditemukan']);
    exit;
}

// Update database
$query = "UPDATE users SET nama = '$nama', satuan_kerja = '$satuan_kerja', jabatan = '$jabatan', pangkat = '$pangkat' WHERE nip = '$nip'";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        'success' => true, 
        'nama' => htmlspecialchars($nama),
        'satuan_kerja' => htmlspecialchars($satuan_kerja),
        'jabatan' => htmlspecialchars($jabatan),
        'pangkat' => htmlspecialchars($pangkat),
        'message' => 'Data berhasil diperbarui'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Gagal mengupdate database: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
