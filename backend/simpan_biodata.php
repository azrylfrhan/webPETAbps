<?php
require_once 'config.php';
require_once 'biodata_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../biodata.php');
    exit;
}

$nip = $_SESSION['nip'] ?? '';
if (empty($nip)) {
    header('Location: ../login.php?error=akses_ditolak');
    exit;
}

if (!biodataTableExists($conn)) {
    header('Location: ../biodata.php?setup=missing_table');
    exit;
}

if (!ensureAlasanTesTable($conn)) {
    header('Location: ../biodata.php?error=save_failed');
    exit;
}

$tempatLahir = trim($_POST['tempat_lahir'] ?? '');
$tanggalLahir = trim($_POST['tanggal_lahir'] ?? '');
$email = trim($_POST['email'] ?? '');
$alasanTes = trim($_POST['alasan_tes'] ?? '');

if ($alasanTes === '') {
    header('Location: ../biodata.php?error=empty_reason');
    exit;
}

$cek = $conn->prepare('SELECT nip FROM biodata_peserta WHERE nip = ? LIMIT 1');
$cek->bind_param('s', $nip);
$cek->execute();
$exists = $cek->get_result()->fetch_assoc();
$cek->close();

$isBiodataBaru = empty($exists);

if ($isBiodataBaru) {
    if ($tempatLahir === '' || $tanggalLahir === '' || $email === '') {
        header('Location: ../biodata.php?error=empty');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../biodata.php?error=invalid_email');
        exit;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $tanggalLahir);
    $validDate = $dt && $dt->format('Y-m-d') === $tanggalLahir;
    if (!$validDate || $tanggalLahir > date('Y-m-d')) {
        header('Location: ../biodata.php?error=invalid_date');
        exit;
    }
}

$conn->begin_transaction();

try {
    if ($isBiodataBaru) {
        $sql = 'INSERT INTO biodata_peserta (nip, tempat_lahir, tanggal_lahir, email) VALUES (?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $nip, $tempatLahir, $tanggalLahir, $email);
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan biodata.');
        }
        $stmt->close();
    }

    $sqlAlasan = 'INSERT INTO riwayat_alasan_tes (nip, alasan_tes) VALUES (?, ?)';
    $stmtAlasan = $conn->prepare($sqlAlasan);
    $stmtAlasan->bind_param('ss', $nip, $alasanTes);
    if (!$stmtAlasan->execute()) {
        throw new Exception('Gagal menyimpan alasan tes.');
    }
    $stmtAlasan->close();

    $conn->commit();
    header('Location: ../dashboard.php?biodata=ok');
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../biodata.php?error=save_failed');
    exit;
}
