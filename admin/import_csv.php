<?php
require '../backend/config.php'; // Sesuaikan path koneksi database Anda

if (isset($_POST['import'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if ($_FILES['csv_file']['size'] > 0) {
        $handle = fopen($file, "r");
        
        // Lewati baris pertama (header CSV: No, NIP, Nama, dll)
        fgetcsv($handle, 1000, ",");

        $successCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Mapping kolom sesuai file CSV Anda
            // $data[0] = No
            $nip     = mysqli_real_escape_string($conn, $data[1]);
            $nama    = mysqli_real_escape_string($conn, $data[2]);
            $jabatan = mysqli_real_escape_string($conn, $data[3]);
            $satker  = mysqli_real_escape_string($conn, $data[4]);
            $pangkat = mysqli_real_escape_string($conn, $data[5]);

            // Logika Password: 6 angka pertama dari NIP
            $raw_password = substr($nip, 0, 6);
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

            // Gunakan INSERT ... ON DUPLICATE KEY UPDATE agar data terbaru selalu terupdate jika NIP sudah ada
            $sql = "INSERT INTO users (nip, nama, jabatan, satuan_kerja, pangkat, password, is_active, role) 
                    VALUES ('$nip', '$nama', '$jabatan', '$satker', '$pangkat', '$hashed_password', 0, 'peserta')
                    ON DUPLICATE KEY UPDATE 
                    nama='$nama', jabatan='$jabatan', satuan_kerja='$satker', pangkat='$pangkat'";

            if (mysqli_query($conn, $sql)) {
                $successCount++;
            }
        }

        fclose($handle);
        header("Location: manage_users.php?msg=Import Berhasil: $successCount data");
    } else {
        header("Location: manage_users.php?msg=File Kosong");
    }
}
?>

<form action="" method="post" enctype="multipart/form-data">
    <label>Pilih File CSV Pegawai:</label>
    <input type="file" name="csv_file" accept=".csv" required>
    <button type="submit" name="import">Unggah & Proses Data</button>
</form>