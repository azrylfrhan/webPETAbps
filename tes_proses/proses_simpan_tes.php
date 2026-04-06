<?php
require_once '../backend/config.php';
require_once '../backend/test_attempt_functions.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['nip'])) {
    die("Akses ditolak.");
}

$nip = $_SESSION['nip'];

$cek = mysqli_query($conn, "SELECT nip FROM hasil_msdt WHERE nip='$nip'");
if (mysqli_num_rows($cek) > 0) {
    die("Anda sudah pernah mengikuti tes.");
}

// ============================
// VALIDASI 64 SOAL
// ============================
if (!isset($_POST['jawaban']) || count($_POST['jawaban']) != 64) {
    die("Mohon jawab semua 64 soal.");
}

$jawaban_user = $_POST['jawaban'];
$jawaban_indexed = [];

$conn->begin_transaction();

try {
    // Reuse running attempt or create a new one for MSDT
    $attempt_id = $_SESSION['current_attempt_id_msdt'] ?? null;
    if (!$attempt_id) {
        $stmtAttempt = $conn->prepare("SELECT id FROM test_attempts WHERE nip = ? AND test_type = 'msdt' AND status = 'running' ORDER BY tanggal_mulai DESC LIMIT 1");
        $stmtAttempt->bind_param('s', $nip);
        $stmtAttempt->execute();
        $attemptRow = $stmtAttempt->get_result()->fetch_assoc();
        $stmtAttempt->close();

        if ($attemptRow) {
            $attempt_id = (int)$attemptRow['id'];
        } else {
            $attempt_id = createTestAttemptGeneric($conn, 'msdt', $nip, 'Tes MSDT oleh peserta');
        }
        $_SESSION['current_attempt_id_msdt'] = $attempt_id;
    }

    if (!$attempt_id) {
        throw new Exception('Gagal membuat riwayat attempt MSDT.');
    }

for ($i = 1; $i <= 64; $i++) {
    if (!isset($jawaban_user[$i]) || !in_array($jawaban_user[$i], ["A","B"])) {
        throw new Exception("Jawaban nomor $i tidak valid.");
    }
    $jawaban_indexed[] = $jawaban_user[$i];
}


// ============================
// BENTUK MATRIKS 8x8 (REVISI)
// ============================
$M = [];
for ($i = 0; $i < 8; $i++) {
    // Mengambil 8 jawaban untuk setiap baris
    $M[$i] = array_slice($jawaban_indexed, $i * 8, 8);
}

// ============================
// HITUNG A (BARIS) & B (KOLOM) (REVISI)
// ============================
$A = array_fill(0, 8, 0);
$B = array_fill(0, 8, 0);

for ($i = 0; $i < 8; $i++) {
    for ($j = 0; $j < 8; $j++) {
        if ($M[$i][$j] === "A") {
            $A[$i]++; // Hitung A secara horizontal (per baris)
        }
        if ($M[$i][$j] === "B") {
            $B[$j]++; // Hitung B secara vertikal (per kolom)
        }
    }
}


// ============================
// HITUNG 8 DIMENSI
// ============================
$koreksi = [1, 2, 1, 0, 3, -1, 0, 4];
$label = ["Ds", "Mi", "Au", "Co", "Bu", "Dv", "Ba", "E"];

$dimensi = [];

for ($k = 0; $k < 8; $k++) {
    $dimensi[$label[$k]] = $A[$k] + $B[$k] + $koreksi[$k];
}


// ============================
// HITUNG MODEL TO / RO / E / O
// ============================
$to_score = $dimensi["Au"] + $dimensi["Co"] + $dimensi["Ba"] + $dimensi["E"];
$ro_score = $dimensi["Mi"] + $dimensi["Co"] + $dimensi["Dv"] + $dimensi["E"];
$e_score  = $dimensi["Bu"] + $dimensi["Dv"] + $dimensi["Ba"] + $dimensi["E"];
$o_score  = $dimensi["Ds"];

// ============================
// SIMPAN KE DATABASE
// ============================

// Hapus dulu jika sudah ada (karena 1 pegawai = 1 tes)
$stmt_delete = $conn->prepare("DELETE FROM hasil_msdt WHERE nip = ?");
$stmt_delete->bind_param("s", $nip);
$stmt_delete->execute();
$stmt_delete->close();

// Insert baru
$stmt = $conn->prepare("
    INSERT INTO hasil_msdt (
        nip, Ds, Mi, Au, Co, Bu, Dv, Ba, E_dim,
        TO_score, RO_score, E_score, O_score, tanggal_tes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "siiiiiiiiiiii",
    $nip,
    $dimensi["Ds"],
    $dimensi["Mi"],
    $dimensi["Au"],
    $dimensi["Co"],
    $dimensi["Bu"],
    $dimensi["Dv"],
    $dimensi["Ba"],
    $dimensi["E"],
    $to_score,
    $ro_score,
    $e_score,
    $o_score
);

if ($stmt->execute()) {
    $stmt->close();

    // Save MSDT result into attempt table
    $stmtAttemptResult = $conn->prepare("
        INSERT INTO msdt_attempt_results (
            attempt_id, user_nip, Ds, Mi, Au, Co, Bu, Dv, Ba, E_dim,
            TO_score, RO_score, E_score, O_score
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            Ds = VALUES(Ds), Mi = VALUES(Mi), Au = VALUES(Au), Co = VALUES(Co),
            Bu = VALUES(Bu), Dv = VALUES(Dv), Ba = VALUES(Ba), E_dim = VALUES(E_dim),
            TO_score = VALUES(TO_score), RO_score = VALUES(RO_score),
            E_score = VALUES(E_score), O_score = VALUES(O_score)
    ");
    $stmtAttemptResult->bind_param(
        "isiiiiiiiiiiii",
        $attempt_id,
        $nip,
        $dimensi["Ds"],
        $dimensi["Mi"],
        $dimensi["Au"],
        $dimensi["Co"],
        $dimensi["Bu"],
        $dimensi["Dv"],
        $dimensi["Ba"],
        $dimensi["E"],
        $to_score,
        $ro_score,
        $e_score,
        $o_score
    );
    if (!$stmtAttemptResult->execute()) {
        throw new Exception('Gagal menyimpan hasil attempt MSDT.');
    }
    $stmtAttemptResult->close();

    // Save raw answers per question
    $hasAnswerTable = $conn->query("SHOW TABLES LIKE 'msdt_attempt_answers'");
    if ($hasAnswerTable && $hasAnswerTable->num_rows > 0) {
        $stmtAnswer = $conn->prepare("
            INSERT INTO msdt_attempt_answers (attempt_id, user_nip, question_no, jawaban_user)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE jawaban_user = VALUES(jawaban_user)
        ");

        for ($i = 1; $i <= 64; $i++) {
            $answer = strtoupper(trim($jawaban_user[$i]));
            $stmtAnswer->bind_param('isis', $attempt_id, $nip, $i, $answer);
            if (!$stmtAnswer->execute()) {
                throw new Exception('Gagal menyimpan jawaban MSDT per soal.');
            }
        }
        $stmtAnswer->close();
    }

    // Mark attempt as finished
    if (!completeAttemptGeneric($conn, 'msdt', $attempt_id)) {
        throw new Exception('Gagal menyelesaikan attempt MSDT.');
    }

    unset($_SESSION['current_attempt_id_msdt']);
    $conn->commit();
    header("Location: ../dashboard.php?status=sukses");
    exit;
} else {
    throw new Exception("Error Database: " . $stmt->error);
}
} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage();
}
?>
