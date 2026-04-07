<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function biodataTableExists(mysqli $conn): bool
{
    $res = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
    return $res && mysqli_num_rows($res) > 0;
}

function ensureAlasanTesTable(mysqli $conn): bool
{
    // Legacy table was removed. Keep function for backward compatibility.
    return true;
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

function usersStatusTesColumnExists(mysqli $conn): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $res = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'status_tes'");
    $cached = $res && mysqli_num_rows($res) > 0;
    return $cached;
}

function getStatusTesPeserta(mysqli $conn, string $nip): ?string
{
    if (!usersStatusTesColumnExists($conn)) {
        return null;
    }

    $stmt = $conn->prepare("SELECT status_tes FROM users WHERE nip = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (empty($row['status_tes'])) {
        return null;
    }

    return strtolower(trim((string) $row['status_tes']));
}

function pesertaWajibIsiAlasanTes(mysqli $conn, string $nip): bool
{
    $statusTes = getStatusTesPeserta($conn, $nip);
    if ($statusTes === null) {
        return false;
    }

    // Peserta wajib isi alasan jika sesi baru (belum) atau sudah menyelesaikan seluruh tes (selesai).
    return in_array($statusTes, ['belum', 'selesai'], true);
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

    if (pesertaWajibIsiAlasanTes($conn, $nip)) {
        header("Location: " . $redirectPath . "?reason_required=1");
        exit;
    }
}
