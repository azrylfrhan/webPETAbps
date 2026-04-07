<?php
require_once 'config.php';
require_once 'biodata_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../biodata.php');
    exit;
}

$nip = $_SESSION['nip'] ?? '';
if (empty($nip)) {
    header('Location: ../login.php?error=akses_ditolak');
    exit;
}

if (!biodataTableExists($conn)) {
    header('Location: ../biodata.php?setup=missing_table');
    exit;
}

if (!ensureAlasanTesTable($conn)) {
    header('Location: ../biodata.php?error=save_failed');
    exit;
}

$tempatLahir = trim($_POST['tempat_lahir'] ?? '');
$tanggalLahir = trim($_POST['tanggal_lahir'] ?? '');
$email = trim($_POST['email'] ?? '');
$alasanTes = trim($_POST['alasan_tes'] ?? '');

if ($alasanTes === '') {
    header('Location: ../biodata.php?error=empty_reason');
    exit;
}

$cek = $conn->prepare('SELECT nip FROM biodata_peserta WHERE nip = ? LIMIT 1');
$cek->bind_param('s', $nip);
$cek->execute();
$exists = $cek->get_result()->fetch_assoc();
$cek->close();

$isBiodataBaru = empty($exists);

if ($isBiodataBaru) {
    if ($tempatLahir === '' || $tanggalLahir === '' || $email === '') {
        header('Location: ../biodata.php?error=empty');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../biodata.php?error=invalid_email');
        exit;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $tanggalLahir);
    $validDate = $dt && $dt->format('Y-m-d') === $tanggalLahir;
    if (!$validDate || $tanggalLahir > date('Y-m-d')) {
        header('Location: ../biodata.php?error=invalid_date');
        exit;
    }
}

$conn->begin_transaction();

try {
    if ($isBiodataBaru) {
        $sql = 'INSERT INTO biodata_peserta (nip, tempat_lahir, tanggal_lahir, email) VALUES (?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $nip, $tempatLahir, $tanggalLahir, $email);
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan biodata.');
        }
        $stmt->close();
    }

    // Save reason into unified history table if available.
    // It updates the latest running attempt first; if none exists, create a placeholder IQ attempt.
    $hasTestAttempts = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
    if ($hasTestAttempts && mysqli_num_rows($hasTestAttempts) > 0) {
        $stmtLatest = $conn->prepare("SELECT id FROM test_attempts WHERE nip = ? AND status = 'running' ORDER BY tanggal_mulai DESC LIMIT 1");
        if (!$stmtLatest) {
            throw new Exception('Gagal menyiapkan penyimpanan alasan tes.');
        }
        $stmtLatest->bind_param('s', $nip);
        $stmtLatest->execute();
        $latest = $stmtLatest->get_result()->fetch_assoc();
        $stmtLatest->close();

        if ($latest) {
            $stmtReason = $conn->prepare("UPDATE test_attempts SET alasan_tes = ? WHERE id = ?");
            $stmtReason->bind_param('si', $alasanTes, $latest['id']);
            if (!$stmtReason->execute()) {
                throw new Exception('Gagal menyimpan alasan tes.');
            }
            $stmtReason->close();
        } else {
            $stmtNext = $conn->prepare("SELECT COALESCE(MAX(attempt_number), 0) + 1 AS next_attempt FROM test_attempts WHERE nip = ? AND test_type = 'iq'");
            $stmtNext->bind_param('s', $nip);
            $stmtNext->execute();
            $next = $stmtNext->get_result()->fetch_assoc();
            $stmtNext->close();

            $nextAttempt = (int)($next['next_attempt'] ?? 1);
            $stmtInsert = $conn->prepare("INSERT INTO test_attempts (nip, test_type, attempt_number, alasan_tes, status) VALUES (?, 'iq', ?, ?, 'incomplete')");
            $stmtInsert->bind_param('sis', $nip, $nextAttempt, $alasanTes);
            if (!$stmtInsert->execute()) {
                throw new Exception('Gagal menyimpan alasan tes.');
            }
            $stmtInsert->close();
        }
    }

    if (usersStatusTesColumnExists($conn)) {
        $stmtStatus = $conn->prepare("UPDATE users SET status_tes = 'proses' WHERE nip = ?");
        if ($stmtStatus) {
            $stmtStatus->bind_param('s', $nip);
            if (!$stmtStatus->execute()) {
                throw new Exception('Gagal memperbarui status tes peserta.');
            }
            $stmtStatus->close();
        }
    }

    $conn->commit();
    header('Location: ../dashboard.php?biodata=ok');
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../biodata.php?error=save_failed');
    exit;
}
