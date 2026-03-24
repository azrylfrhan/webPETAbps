<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$nip = $_SESSION['nip'];

$stmt = $conn->prepare("
    SELECT id, user_nip, question_id, jawaban_user, waktu_jawab
    FROM iq_user_answers
    WHERE user_nip = ?
    ORDER BY id DESC
    LIMIT 50
");
$stmt->bind_param("s", $nip);
$stmt->execute();
$res  = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) $rows[] = $row;

echo json_encode(["rows" => $rows]);