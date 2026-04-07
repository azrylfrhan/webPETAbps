<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$urutan   = intval($_GET['section']); // urutan section (1,2,3,...)
$question = intval($_GET['q']);       // nomor urutan soal dalam section (1,2,3,...)

function getAnsweredQuestionIds($conn, $attempt_id, $nip, $section_id) {
    $ids = [];

    if (!empty($attempt_id)) {
        $cekTable = $conn->query("SHOW TABLES LIKE 'iq_attempt_answers'");
        if ($cekTable && $cekTable->num_rows > 0) {
            $stmt = $conn->prepare("\n                SELECT DISTINCT a.question_id\n                FROM iq_attempt_answers a\n                JOIN iq_questions q ON q.id = a.question_id\n                WHERE a.attempt_id = ? AND q.section_id = ?\n            ");
            $stmt->bind_param("ii", $attempt_id, $section_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $ids[(int)$row['question_id']] = true;
            }
            $stmt->close();
            return $ids;
        }
    }

    $stmt = $conn->prepare("\n        SELECT DISTINCT a.question_id\n        FROM iq_user_answers a\n        JOIN iq_questions q ON q.id = a.question_id\n        WHERE a.user_nip = ? AND q.section_id = ?\n    ");
    $stmt->bind_param("si", $nip, $section_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $ids[(int)$row['question_id']] = true;
    }
    $stmt->close();

    return $ids;
}

// Cari id section berdasarkan urutan
$stmt0 = $conn->prepare("SELECT id FROM iq_sections WHERE urutan = ? LIMIT 1");
$stmt0->bind_param("i", $urutan);
$stmt0->execute();
$sec_row = $stmt0->get_result()->fetch_assoc();

if (!$sec_row) {
    echo json_encode(["exists" => false]);
    exit;
}

$section_id = $sec_row['id'];
$offset     = $question - 1; // urutan ke-N = offset N-1
$nip        = $_SESSION['nip'] ?? '';
$attempt_id = $_SESSION['current_attempt_id'] ?? null;
$answered_ids = getAnsweredQuestionIds($conn, $attempt_id, $nip, $section_id);

if ($question <= 0) {
    $stmt = $conn->prepare("\n        SELECT id, pertanyaan, gambar, nomor_soal\n        FROM iq_questions\n        WHERE section_id = ?\n        ORDER BY nomor_soal ASC\n    ");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $question_id = (int)$row['id'];
        $questions[] = [
            'id' => $question_id,
            'nomor_soal' => (int)$row['nomor_soal'],
            'pertanyaan' => $row['pertanyaan'],
            'gambar' => $row['gambar'],
            'answered' => isset($answered_ids[$question_id])
        ];
    }

    echo json_encode([
        "exists" => true,
        "questions" => $questions
    ]);
    exit;
}

// Ambil soal ke-N dalam section ini, diurutkan by nomor_soal
$stmt = $conn->prepare("
    SELECT id, pertanyaan, gambar, nomor_soal
    FROM iq_questions
    WHERE section_id = ?
    ORDER BY nomor_soal ASC
    LIMIT 1 OFFSET ?
");
$stmt->bind_param("ii", $section_id, $offset);
$stmt->execute();
$soal = $stmt->get_result()->fetch_assoc();

if (!$soal) {
    echo json_encode(["exists" => false]);
    exit;
}

// Ambil opsi jawaban
$stmt2 = $conn->prepare("
    SELECT label, opsi_text, gambar_opsi
    FROM iq_options
    WHERE question_id = ?
    ORDER BY label ASC
");
$stmt2->bind_param("i", $soal['id']);
$stmt2->execute();
$options_result = $stmt2->get_result();

$options = [];
while ($row = $options_result->fetch_assoc()) {
    $options[] = $row;
}

echo json_encode([
    "exists"     => true,
    "id"         => $soal['id'],
    "pertanyaan" => $soal['pertanyaan'],
    "gambar"     => $soal['gambar'],
    "options"    => $options,
    "answered"   => isset($answered_ids[(int)$soal['id']])
]);