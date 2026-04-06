<?php
require_once '../backend/config.php';
require_once '../backend/test_attempt_functions.php';
require_once 'proses_papi.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['nip'])) { die("Akses ditolak."); }
$nip = $_SESSION['nip'];

if (!isset($_POST['jawaban_papi']) || count($_POST['jawaban_papi']) < 90) {
    echo "<script>alert('Mohon jawab semua 90 soal.'); window.history.back();</script>";
    exit;
}

$jawaban = $_POST['jawaban_papi'];
$hasil_skor = hitungSkorPapi($jawaban, $mapping_papi);

$conn->begin_transaction();

try {
    // Reuse running attempt or create a new one for PAPI
    $attempt_id = $_SESSION['current_attempt_id_papi'] ?? null;
    if (!$attempt_id) {
        $stmtAttempt = $conn->prepare("SELECT id FROM test_attempts WHERE nip = ? AND test_type = 'papi' AND status = 'running' ORDER BY tanggal_mulai DESC LIMIT 1");
        $stmtAttempt->bind_param('s', $nip);
        $stmtAttempt->execute();
        $attemptRow = $stmtAttempt->get_result()->fetch_assoc();
        $stmtAttempt->close();

        if ($attemptRow) {
            $attempt_id = (int)$attemptRow['id'];
        } else {
            $attempt_id = createTestAttemptGeneric($conn, 'papi', $nip, 'Tes PAPI oleh peserta');
        }
        $_SESSION['current_attempt_id_papi'] = $attempt_id;
    }

    if (!$attempt_id) {
        throw new Exception('Gagal membuat riwayat attempt PAPI.');
    }

// Validasi Total Skor (Harus 90 sesuai standar PAPI)
if (array_sum($hasil_skor) !== 90) {
    throw new Exception("Gagal: Total skor adalah " . array_sum($hasil_skor) . ". Harusnya 90. Periksa mapping soal Anda.");
}

// Pastikan urutan kolom sesuai dengan tabel hasil_papi di database Anda
$stmt = $conn->prepare("
    INSERT INTO hasil_papi (
        nip, G, L, I, T, V, S, R, D, C, E, 
        N, A, P, X, B, O, K, F, W, Z, tanggal_tes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

// "s" untuk nip (string), sisanya 20 "i" untuk skor dimensi (integer)
$stmt->bind_param(
    "siiiiiiiiiiiiiiiiiiii",
    $nip, 
    $hasil_skor['G'], $hasil_skor['L'], $hasil_skor['I'], $hasil_skor['T'], $hasil_skor['V'],
    $hasil_skor['S'], $hasil_skor['R'], $hasil_skor['D'], $hasil_skor['C'], $hasil_skor['E'],
    $hasil_skor['N'], $hasil_skor['A'], $hasil_skor['P'], $hasil_skor['X'], $hasil_skor['B'],
    $hasil_skor['O'], $hasil_skor['K'], $hasil_skor['F'], $hasil_skor['W'], $hasil_skor['Z']
);

if ($stmt->execute()) {
    $stmt->close();

    // Save PAPI result into attempt table
    $stmtAttemptResult = $conn->prepare("
        INSERT INTO papi_attempt_results (
            attempt_id, user_nip, G, L, I, T, V, S, R, D, C, E,
            N, A, P, X, B, O, K, F, W, Z
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            G = VALUES(G), L = VALUES(L), I = VALUES(I), T = VALUES(T), V = VALUES(V),
            S = VALUES(S), R = VALUES(R), D = VALUES(D), C = VALUES(C), E = VALUES(E),
            N = VALUES(N), A = VALUES(A), P = VALUES(P), X = VALUES(X), B = VALUES(B),
            O = VALUES(O), K = VALUES(K), F = VALUES(F), W = VALUES(W), Z = VALUES(Z)
    ");
    $stmtAttemptResult->bind_param(
        "isiiiiiiiiiiiiiiiiiiii",
        $attempt_id,
        $nip,
        $hasil_skor['G'], $hasil_skor['L'], $hasil_skor['I'], $hasil_skor['T'], $hasil_skor['V'],
        $hasil_skor['S'], $hasil_skor['R'], $hasil_skor['D'], $hasil_skor['C'], $hasil_skor['E'],
        $hasil_skor['N'], $hasil_skor['A'], $hasil_skor['P'], $hasil_skor['X'], $hasil_skor['B'],
        $hasil_skor['O'], $hasil_skor['K'], $hasil_skor['F'], $hasil_skor['W'], $hasil_skor['Z']
    );
    if (!$stmtAttemptResult->execute()) {
        throw new Exception('Gagal menyimpan hasil attempt PAPI.');
    }
    $stmtAttemptResult->close();

    // Save raw answers per question
    $hasAnswerTable = $conn->query("SHOW TABLES LIKE 'papi_attempt_answers'");
    if ($hasAnswerTable && $hasAnswerTable->num_rows > 0) {
        $stmtAnswer = $conn->prepare("
            INSERT INTO papi_attempt_answers (attempt_id, user_nip, question_no, jawaban_user, mapped_dimension)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE jawaban_user = VALUES(jawaban_user), mapped_dimension = VALUES(mapped_dimension)
        ");

        foreach ($jawaban as $no => $pilihan) {
            $questionNo = (int)$no;
            $answer = strtoupper(trim($pilihan));
            $mappedDimension = isset($mapping_papi[$questionNo][$answer]) ? $mapping_papi[$questionNo][$answer] : null;
            $stmtAnswer->bind_param('isiss', $attempt_id, $nip, $questionNo, $answer, $mappedDimension);
            if (!$stmtAnswer->execute()) {
                throw new Exception('Gagal menyimpan jawaban PAPI per soal.');
            }
        }
        $stmtAnswer->close();
    }

    // Mark attempt as finished
    if (!completeAttemptGeneric($conn, 'papi', $attempt_id)) {
        throw new Exception('Gagal menyelesaikan attempt PAPI.');
    }

    unset($_SESSION['current_attempt_id_papi']);
    $conn->commit();
    header("Location: ../dashboard.php?status=tes_selesai");
} else {
    throw new Exception("Error Database: " . $stmt->error);
}
$conn->close();
} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage();
}
?>