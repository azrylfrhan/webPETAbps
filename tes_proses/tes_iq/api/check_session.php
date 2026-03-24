<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$nip = $_SESSION['nip'] ?? null;
if (!$nip) { echo json_encode(["status" => "no_session"]); exit; }

$stmt = $conn->prepare("
    SELECT status FROM iq_test_sessions
    WHERE nip = ?
    ORDER BY id DESC LIMIT 1
");
$stmt->bind_param("s", $nip);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo json_encode(["status" => $row ? $row['status'] : "none"]);