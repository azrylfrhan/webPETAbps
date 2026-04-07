<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$nip         = $_SESSION['nip'] ?? null;
$attempt_id  = $_SESSION['current_attempt_id'] ?? null;
$question_id = intval($_GET['question_id'] ?? 0);

if (!$question_id || !$nip) {
    echo json_encode(["answer" => null]);
    exit;
}

if ($attempt_id) {
    $cekTable = $conn->query("SHOW TABLES LIKE 'iq_attempt_answers'");
    if ($cekTable && $cekTable->num_rows > 0) {
        $stmt = $conn->prepare("
            SELECT jawaban_user FROM iq_attempt_answers
            WHERE attempt_id = ? AND question_id = ?
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bind_param("ii", $attempt_id, $question_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        echo json_encode(["answer" => $row ? $row['jawaban_user'] : null]);
        exit;
    }
}

$stmt = $conn->prepare("
    SELECT jawaban_user FROM iq_user_answers
    WHERE user_nip = ? AND question_id = ?
    ORDER BY id DESC LIMIT 1
");
$stmt->bind_param("si", $nip, $question_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo json_encode(["answer" => $row ? $row['jawaban_user'] : null]);