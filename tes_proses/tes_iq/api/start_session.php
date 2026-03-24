<?php
require_once('../../../backend/config.php');
require_once '../../../backend/auth_check.php';

header('Content-Type: application/json');

$nip = $_SESSION['nip'] ?? null;
if (!$nip) { echo json_encode(["success" => false]); exit; }

// Cek apakah sudah finished
$stmt0 = $conn->prepare("SELECT status FROM iq_test_sessions WHERE nip = ?");
$stmt0->bind_param("s", $nip);
$stmt0->execute();
$row = $stmt0->get_result()->fetch_assoc();

if ($row && $row['status'] === 'finished') {
    echo json_encode(["success" => false, "error" => "finished"]);
    exit;
}

// Insert jika belum ada, abaikan jika sudah ada (tidak overwrite)
$stmt = $conn->prepare("
    INSERT IGNORE INTO iq_test_sessions (nip, section, question, start_time, status)
    VALUES (?, 1, 1, NOW(), 'running')
");
$stmt->bind_param("s", $nip);
$stmt->execute();

echo json_encode(["success" => true]);