<?php
require_once '../../../backend/config.php';
require_once '../../../backend/test_attempt_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $nip = trim($_SESSION['nip'] ?? '');
    $attempt_id = $_SESSION['current_attempt_id'] ?? null;

    if (empty($nip)) {
        echo json_encode(['status' => 'error', 'message' => 'Sesi NIP kosong.']);
        exit;
    }

    $skor_akhir = 0;
    $skor_pilgan = 0;
    $skor_isian = 0;
    $total_soal = 0;

    $use_attempt = false;
    if (!empty($attempt_id)) {
        $cek_attempts = $conn->query("SHOW TABLES LIKE 'test_attempts'");
        $cek_results = $conn->query("SHOW TABLES LIKE 'iq_attempt_results'");
        if ($cek_attempts && $cek_attempts->num_rows > 0 && $cek_results && $cek_results->num_rows > 0) {
            $use_attempt = true;
        }
    }

    if ($use_attempt) {
        completeAttempt($conn, (int)$attempt_id);

        $stmtAttempt = $conn->prepare("\n            SELECT se, wa, an, ge, ra, zr, fa, wu, me, skor_total\n            FROM iq_attempt_results\n            WHERE attempt_id = ?\n            LIMIT 1\n        ");
        $stmtAttempt->bind_param("i", $attempt_id);
        $stmtAttempt->execute();
        $rowAttempt = $stmtAttempt->get_result()->fetch_assoc();
        $stmtAttempt->close();

        if ($rowAttempt) {
            $skor_akhir = (int)($rowAttempt['skor_total'] ?? 0);
            $skor_isian = (int)($rowAttempt['ge'] ?? 0);
            $skor_pilgan =
                (int)($rowAttempt['se'] ?? 0) +
                (int)($rowAttempt['wa'] ?? 0) +
                (int)($rowAttempt['an'] ?? 0) +
                (int)($rowAttempt['ra'] ?? 0) +
                (int)($rowAttempt['zr'] ?? 0) +
                (int)($rowAttempt['fa'] ?? 0) +
                (int)($rowAttempt['wu'] ?? 0) +
                (int)($rowAttempt['me'] ?? 0);
            $total_soal = $skor_pilgan + $skor_isian;
        }
    } else {
        // QUERY X-RAY: Menghitung secara rinci
        $query_hitung = "
            SELECT 
                COUNT(ua.id) AS total_soal_ditemukan,
                
                -- Menghitung skor Bagian 4 (Isian)
                SUM(
                    CASE WHEN s.urutan = 4 THEN 
                        COALESCE((
                            SELECT MAX(nilai) 
                            FROM iq_fill_answers fa 
                            WHERE fa.question_id = ua.question_id 
                            AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)), REPLACE(LOWER(fa.jawaban), ', ', ',')) > 0
                        ), 0)
                    ELSE 0 END
                ) AS skor_bagian_4,
                
                -- Menghitung skor Bagian Lainnya (Pilihan Ganda)
                SUM(
                    CASE WHEN s.urutan != 4 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 
                    ELSE 0 END
                ) AS skor_pilihan_ganda

            FROM iq_user_answers ua
            JOIN iq_questions q ON ua.question_id = q.id
            JOIN iq_sections s ON q.section_id = s.id
            WHERE TRIM(ua.user_nip) = ? 
        ";

        $stmt = $conn->prepare($query_hitung);
        $stmt->bind_param("s", $nip);
        $stmt->execute();
        $hasil = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $total_soal = (int)($hasil['total_soal_ditemukan'] ?? 0);
        $skor_pilgan = (int)($hasil['skor_pilihan_ganda'] ?? 0);
        $skor_isian = (int)($hasil['skor_bagian_4'] ?? 0);
        $skor_akhir = $skor_pilgan + $skor_isian;

        if ($total_soal === 0) {
            echo json_encode([
                'status' => 'warning',
                'message' => 'Sistem gagal menemukan jawaban Anda di database.',
                'nip_dicari' => $nip
            ]);
            exit;
        }

        $cek_query = "SELECT id FROM iq_results WHERE user_id = ?";
        $stmt_cek = $conn->prepare($cek_query);
        $stmt_cek->bind_param("s", $nip);
        $stmt_cek->execute();
        $cek_hasil = $stmt_cek->get_result();
        $stmt_cek->close();

        if ($cek_hasil->num_rows > 0) {
            $query_save = "UPDATE iq_results SET skor = ?, tanggal = NOW() WHERE user_id = ?";
            $stmt2 = $conn->prepare($query_save);
            $stmt2->bind_param("is", $skor_akhir, $nip);
        } else {
            $query_save = "INSERT INTO iq_results (user_id, skor) VALUES (?, ?)";
            $stmt2 = $conn->prepare($query_save);
            $stmt2->bind_param("si", $nip, $skor_akhir);
        }
        $stmt2->execute();
        $stmt2->close();
    }

    $query_update_session = "UPDATE iq_test_sessions SET status = 'finished' WHERE nip = ?";
    $stmt3 = $conn->prepare($query_update_session);
    $stmt3->bind_param("s", $nip);
    $stmt3->execute();
    $stmt3->close();

    echo json_encode([
        'status' => 'success',
        'rincian_sistem' => [
            'nip_user' => $nip,
            'total_jawaban_ditemukan' => $total_soal,
            'skor_pilihan_ganda' => $skor_pilgan,
            'skor_bagian_4_isian' => $skor_isian,
            'TOTAL_SKOR_DISIMPAN' => $skor_akhir,
            'attempt_id' => $attempt_id,
            'mode' => $use_attempt ? 'attempt' : 'legacy'
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'fatal_error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>