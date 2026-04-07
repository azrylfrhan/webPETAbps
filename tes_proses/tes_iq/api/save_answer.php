<?php
require_once('../../../backend/config.php');
require_once '../../../backend/auth_check.php';
require_once '../../../backend/test_attempt_functions.php';

header('Content-Type: application/json');

$raw         = file_get_contents("php://input");
$data        = json_decode($raw, true);
$question_id = $data['question_id'] ?? $data['question'] ?? null;
$answer      = $data['answer'] ?? null;
$nip         = $_SESSION['nip'] ?? null;
$attempt_id  = $_SESSION['current_attempt_id'] ?? null;

if (!$question_id || !$answer || !$nip) {
    echo json_encode(["success" => false, "error" => "Data tidak lengkap", "debug" => ["nip"=>$nip,"qid"=>$question_id,"ans"=>$answer]]);
    exit;
}

// Save to main iq_user_answers table
$stmt = $conn->prepare("
    INSERT INTO iq_user_answers (user_nip, question_id, jawaban_user)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE jawaban_user = VALUES(jawaban_user)
");
$stmt->bind_param("sis", $nip, $question_id, $answer);
$result = $stmt->execute();

// If attempt id is missing in session, recover latest running attempt to keep history separated per attempt.
if (!$attempt_id) {
    $currentAttempt = getCurrentAttempt($conn, $nip);
    if ($currentAttempt && ($currentAttempt['status'] ?? '') === 'running') {
        $attempt_id = (int)$currentAttempt['id'];
        $_SESSION['current_attempt_id'] = $attempt_id;
    }
}

// Also save to iq_attempt_answers if table exists and attempt_id is set
if ($attempt_id) {
    $cek_table = $conn->query("SHOW TABLES LIKE 'iq_attempt_answers'");
    if ($cek_table && $cek_table->num_rows > 0) {
        $stmt_attempt = $conn->prepare("
            INSERT INTO iq_attempt_answers (attempt_id, user_nip, question_id, jawaban_user)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE jawaban_user = VALUES(jawaban_user)
        ");
        $stmt_attempt->bind_param("isis", $attempt_id, $nip, $question_id, $answer);
        $stmt_attempt->execute();
        $stmt_attempt->close();
    }
}

echo json_encode([
    "success"       => $result,
    "affected_rows" => $stmt->affected_rows,
    "error"         => $result ? null : $conn->error,
    "attempt_id"    => $attempt_id
]);