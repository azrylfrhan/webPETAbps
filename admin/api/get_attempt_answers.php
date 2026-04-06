<?php
require_once '../../backend/config.php';
require_once '../../backend/auth_check.php';

header('Content-Type: application/json');

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['attempt_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing attempt_id']);
    exit;
}

$attempt_id = (int)$data['attempt_id'];

try {
    $stmt = $conn->prepare("SELECT nip, test_type FROM test_attempts WHERE id = ?");
    $stmt->bind_param('i', $attempt_id);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$attempt) {
        echo json_encode(['success' => false, 'message' => 'Attempt not found']);
        exit;
    }

    $test_type = strtolower($attempt['test_type']);
    $answers = [];

    if ($test_type === 'iq') {
        $sections = [
            ['se', 1, 20], ['wa', 21, 40], ['an', 41, 60], ['ge', 61, 76],
            ['ra', 77, 96], ['zr', 97, 116], ['fa', 117, 136], ['wu', 137, 156], ['me', 157, 176]
        ];

        $fillAnswersByQuestion = [];
        $stmtFill = $conn->prepare("SELECT question_id, jawaban, nilai FROM iq_fill_answers ORDER BY question_id ASC, id ASC");
        $stmtFill->execute();
        $resultFill = $stmtFill->get_result();
        while ($fill = $resultFill->fetch_assoc()) {
            $qid = (int)$fill['question_id'];
            if (!isset($fillAnswersByQuestion[$qid])) {
                $fillAnswersByQuestion[$qid] = [];
            }
            $fillAnswersByQuestion[$qid][] = $fill;
        }
        $stmtFill->close();

        $stmt = $conn->prepare("\n            SELECT\n                q.id,\n                q.pertanyaan,\n                q.jawaban_benar,\n                ua.jawaban_user\n            FROM iq_questions q\n            LEFT JOIN iq_attempt_answers ua ON q.id = ua.question_id AND ua.attempt_id = ?\n            ORDER BY q.id ASC\n        ");
        $stmt->bind_param('i', $attempt_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        while ($q = $result->fetch_assoc()) {
            $q_id = (int)$q['id'];
            $section_name = 'Unknown';
            $q_number = $q_id;

            foreach ($sections as $section_info) {
                if ($q_id >= $section_info[1] && $q_id <= $section_info[2]) {
                    $section_name = strtoupper($section_info[0]);
                    $q_number = $q_id - $section_info[1] + 1;
                    break;
                }
            }

            $normAnswers = $fillAnswersByQuestion[$q_id] ?? [];
            $normTexts = [];
            $normValues = [];
            foreach ($normAnswers as $norm) {
                $normTexts[] = $norm['jawaban'];
                $normValues[] = (int)$norm['nilai'];
            }

            $matchedNorms = [];
            if (!empty($q['jawaban_user']) && !empty($normAnswers)) {
                $userAnswersArray = array_map('trim', explode(',', $q['jawaban_user']));
                foreach ($normAnswers as $norm) {
                    foreach ($userAnswersArray as $userSingleAnswer) {
                        if (strcasecmp(trim($userSingleAnswer), trim($norm['jawaban'])) === 0) {
                            $matchedNorms[] = $norm;
                            break;
                        }
                    }
                }
            }

            $answers[] = [
                'question_id' => $q['id'],
                'section' => $section_name,
                'question_number' => $q_number,
                'question_text' => $q['pertanyaan'],
                'user_answer' => $q['jawaban_user'],
                'correct_answer' => $q['jawaban_benar'],
                'norm_answers' => $normTexts,
                'norm_values' => $normValues,
                'matched_norms' => $matchedNorms
            ];
        }
    } elseif ($test_type === 'papi') {
        $hasAnswerTable = $conn->query("SHOW TABLES LIKE 'papi_attempt_answers'");
        if ($hasAnswerTable && $hasAnswerTable->num_rows > 0) {
            $stmt = $conn->prepare("\n                SELECT\n                    s.nomor_soal AS question_number,\n                    s.pertanyaan_a AS option_a,\n                    s.pertanyaan_b AS option_b,\n                    pa.jawaban_user,\n                    pa.mapped_dimension\n                FROM soal s\n                LEFT JOIN papi_attempt_answers pa\n                    ON pa.question_no = s.nomor_soal AND pa.attempt_id = ?\n                WHERE s.kode_tes = 'KEPRIBADIAN2'\n                ORDER BY s.nomor_soal ASC\n            ");
            $stmt->bind_param('i', $attempt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $answers[] = $row;
            }
            $stmt->close();
        }
    } else {
        $hasAnswerTable = $conn->query("SHOW TABLES LIKE 'msdt_attempt_answers'");
        if ($hasAnswerTable && $hasAnswerTable->num_rows > 0) {
            $stmt = $conn->prepare("\n                SELECT\n                    s.nomor_soal AS question_number,\n                    s.pertanyaan_a AS option_a,\n                    s.pertanyaan_b AS option_b,\n                    ma.jawaban_user\n                FROM soal s\n                LEFT JOIN msdt_attempt_answers ma\n                    ON ma.question_no = s.nomor_soal AND ma.attempt_id = ?\n                WHERE s.kode_tes = 'KEPRIBADIAN'\n                ORDER BY s.nomor_soal ASC\n            ");
            $stmt->bind_param('i', $attempt_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $answers[] = $row;
            }
            $stmt->close();
        }
    }

    echo json_encode([
        'success' => true,
        'test_type' => $test_type,
        'answers' => $answers
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
