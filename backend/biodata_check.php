<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function biodataTableExists(mysqli $conn): bool
{
    $res = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
    return $res && mysqli_num_rows($res) > 0;
}

function alasanTesTableExists(mysqli $conn): bool
{
    $res = mysqli_query($conn, "SHOW TABLES LIKE 'riwayat_alasan_tes'");
    return $res && mysqli_num_rows($res) > 0;
}

function ensureAlasanTesTable(mysqli $conn): bool
{
    $sql = "CREATE TABLE IF NOT EXISTS riwayat_alasan_tes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nip VARCHAR(30) NOT NULL,
        alasan_tes TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_riwayat_alasan_nip (nip)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    return mysqli_query($conn, $sql) === true;
}

function pesertaSudahIsiBiodata(mysqli $conn, string $nip): bool
{
    $sql = "SELECT nip FROM biodata_peserta WHERE nip = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return !empty($row);
}

function redirectJikaBiodataBelumLengkap(mysqli $conn, string $nip, string $redirectPath = 'biodata.php'): void
{
    if (!biodataTableExists($conn)) {
        header("Location: " . $redirectPath . "?setup=missing_table");
        exit;
    }

    if (!pesertaSudahIsiBiodata($conn, $nip)) {
        header("Location: " . $redirectPath);
        exit;
    }
}
