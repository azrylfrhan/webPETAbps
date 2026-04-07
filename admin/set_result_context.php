<?php
include '../backend/auth_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$target = trim($_POST['target'] ?? '');
$nip = trim($_POST['nip'] ?? '');
$attempt_id = isset($_POST['attempt_id']) ? (int)$_POST['attempt_id'] : 0;

$allowedTargets = ['hasil_iq.php', 'hasil_msdt.php', 'hasil_papi.php', 'detail_pegawai.php'];
if (!in_array($target, $allowedTargets, true)) {
    header('Location: hasil_peserta.php');
    exit;
}

$_SESSION['admin_result_context'] = [
    'target' => $target,
    'nip' => $nip,
    'attempt_id' => $attempt_id,
    'updated_at' => time(),
];

header('Location: ' . $target);
exit;