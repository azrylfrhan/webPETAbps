<?php
require_once('../../../backend/config.php');
require_once '../../../backend/auth_check.php';

header('Content-Type: application/json');

$raw         = file_get_contents("php://input");
$data        = json_decode($raw, true);
$question_id = $data['question_id'] ?? $data['question'] ?? null;
$answer      = $data['answer'] ?? null;
$nip         = $_SESSION['nip'] ?? null;

if (!$question_id || !$answer || !$nip) {
    echo json_encode(["success" => false, "error" => "Data tidak lengkap", "debug" => ["nip"=>$nip,"qid"=>$question_id,"ans"=>$answer]]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO iq_user_answers (user_nip, question_id, jawaban_user)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE jawaban_user = VALUES(jawaban_user)
");
$stmt->bind_param("sis", $nip, $question_id, $answer);
$result = $stmt->execute();

echo json_encode([
    "success"       => $result,
    "affected_rows" => $stmt->affected_rows,
    "error"         => $result ? null : $conn->error
]);