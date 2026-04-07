<?php
session_start();
require '../backend/config.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- LOGIKA 1: PROSES IMPORT CSV ---
if (isset($_POST['import_csv'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if ($_FILES['csv_file']['size'] > 0) {
        $handle = fopen($file, "r");
        fgetcsv($handle, 1000, ","); // Lompat header

        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $nip     = mysqli_real_escape_string($conn, $data[1]);
            $nama    = mysqli_real_escape_string($conn, $data[2]);
            $jabatan = mysqli_real_escape_string($conn, $data[3]);
            $satker  = mysqli_real_escape_string($conn, $data[4]);
            $pangkat_golongan = mysqli_real_escape_string($conn, $data[5]);

            // Password: 6 angka depan NIP
            $raw_pass = substr($nip, 0, 6);
            $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (nip, nama, jabatan, satuan_kerja, pangkat_golongan, password, is_active, role) 
                    VALUES ('$nip', '$nama', '$jabatan', '$satker', '$pangkat_golongan', '$hashed_pass', 0, 'peserta')
                    ON DUPLICATE KEY UPDATE 
                    nama='$nama', 
                    jabatan='$jabatan', 
                    pangkat_golongan='$pangkat_golongan', 
                    satuan_kerja='$satker'";

            if(mysqli_query($conn, $sql)) $count++;
        }
        fclose($handle);
        echo "<script>alert('$count Data Berhasil Diimpor'); window.location='admin_pegawai.php';</script>";
    }
}

// --- LOGIKA 2: AKTIVASI / NON-AKTIF MASSAL ---
if (isset($_POST['bulk_action'])) {
    $nips = $_POST['selected_nip'];
    $action = $_POST['action_type']; // 'aktifkan', 'nonaktifkan', atau 'reset'

    if (!empty($nips)) {
        $nip_list = implode("','", $nips);
        if ($action == 'aktifkan') {
            $query = "UPDATE users SET is_active = 1 WHERE nip IN ('$nip_list')";
        } else if ($action == 'nonaktifkan') {
            $query = "UPDATE users SET is_active = 0 WHERE nip IN ('$nip_list')";
        } else if ($action == 'reset') {
            $query = "UPDATE users SET is_active = 1, status_tes = 'belum' WHERE nip IN ('$nip_list')";
        }
        mysqli_query($conn, $query);
        echo "<script>alert('Status Berhasil Diperbarui'); window.location='admin_pegawai.php';</script>";
    }
}

// Ambil data pegawai untuk tabel
$filter_satker = isset($_GET['satker']) ? mysqli_real_escape_string($conn, $_GET['satker']) : '';
$where_clause = $filter_satker ? "WHERE role='peserta' AND satuan_kerja='$filter_satker'" : "WHERE role='peserta'";
$users_query = mysqli_query($conn, "SELECT * FROM users $where_clause ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Manajemen Pegawai | Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-section { padding: 20px; max-width: 1200px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 0.8em; color: white; }
        .bg-success { background: #28a745; }
        .bg-danger { background: #dc3545; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="admin-section">
    <h2>Manajemen Peserta Tes</h2>

    <div class="card">
        <h3>Import Dataset Pegawai (CSV)</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit" name="import_csv" class="btn-primary">Unggah Data</button>
        </form>
        <p style="font-size: 0.8em; color: #666; margin-top: 10px;">Format: No, NIP, Nama, Jabatan, Satker, Pangkat/Golongan</p>
    </div>

    <div class="card">
        <form action="" method="POST">
            <div class="toolbar">
                <div>
                    <button type="submit" name="bulk_action" value="aktifkan" class="btn-primary" style="background: #28a745;">Aktifkan Terpilih</button>
                    <button type="submit" name="bulk_action" value="nonaktifkan" class="btn-primary" style="background: #6c757d;">Matikan Terpilih</button>
                    <button type="submit" name="bulk_action" value="reset" class="btn-primary" style="background: #ffc107; color: #000;">Reset & Izinkan Tes Ulang</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all"></th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Satker</th>
                        <th>Pangkat/Golongan</th>
                        <th>Status Akses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($users_query)): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_nip[]" value="<?= $row['nip']; ?>" class="user-checkbox"></td>
                        <td><?= $row['nip']; ?></td>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['satuan_kerja']; ?></td>
                        <td><?= $row['pangkat_golongan']; ?></td>
                        <td>
                            <span class="status-badge <?= $row['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $row['is_active'] ? 'Aktif (Bisa Login)' : 'Non-aktif'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
    // Script Pilih Semua Checkbox
    document.getElementById('select_all').onclick = function() {
        let checkboxes = document.getElementsByClassName('user-checkbox');
        for (let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
</script>

</body>
</html>