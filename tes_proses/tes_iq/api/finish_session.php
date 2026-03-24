<?php
require_once('../../../backend/config.php');
require_once '../../../backend/auth_check.php';

header('Content-Type: application/json');

$nip = $_SESSION['nip'] ?? null;
if (!$nip) { echo json_encode(["success" => false, "error" => "no nip"]); exit; }

// Update SEMUA session running milik user ini jadi finished
$stmt = $conn->prepare("
    UPDATE iq_test_sessions
    SET status = 'finished'
    WHERE nip = ?
");
$stmt->bind_param("s", $nip);
$result = $stmt->execute();

echo json_encode([
    "success"       => $result,
    "affected_rows" => $stmt->affected_rows,
    "nip"           => $nip
]);