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

$tempatLahir = trim($_POST['tempat_lahir'] ?? '');
$tanggalLahir = trim($_POST['tanggal_lahir'] ?? '');
$email = trim($_POST['email'] ?? '');

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

$cek = $conn->prepare('SELECT nip FROM biodata_peserta WHERE nip = ? LIMIT 1');
$cek->bind_param('s', $nip);
$cek->execute();
$exists = $cek->get_result()->fetch_assoc();
$cek->close();

if (!empty($exists)) {
    header('Location: ../biodata.php?error=already_exists');
    exit;
}

$sql = 'INSERT INTO biodata_peserta (nip, tempat_lahir, tanggal_lahir, email) VALUES (?, ?, ?, ?)';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $nip, $tempatLahir, $tanggalLahir, $email);

if ($stmt->execute()) {
    header('Location: ../dashboard.php?biodata=ok');
    exit;
}

header('Location: ../biodata.php?error=save_failed');
exit;
