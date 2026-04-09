<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$testTypeParam = strtolower(trim($_GET['test_type'] ?? ''));
$nipParam = trim((string)($_GET['nip'] ?? ''));
$title = trim($_GET['title'] ?? 'Detail Jawaban');

$allowLegacyIq = ($attemptId <= 0 && $testTypeParam === 'iq' && $nipParam !== '');

if ($attemptId <= 0 && !$allowLegacyIq) {
    if ($testTypeParam === 'iq' && $nipParam !== '') {
        header('Location: hasil_iq.php?nip=' . urlencode($nipParam));
        exit;
    }
    if ($testTypeParam === 'msdt' && $nipParam !== '') {
        header('Location: hasil_msdt.php?nip=' . urlencode($nipParam));
        exit;
    }
    if ($testTypeParam === 'papi' && $nipParam !== '') {
        header('Location: hasil_papi.php?nip=' . urlencode($nipParam));
        exit;
    }

    header('Location: hasil_peserta.php');
    exit;
}

$attempt = null;
if ($allowLegacyIq) {
    $stmt = $conn->prepare("SELECT ta.*, u.nama, u.satuan_kerja, u.jabatan FROM test_attempts ta JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci WHERE ta.nip = ? AND ta.test_type = 'iq' ORDER BY ta.tanggal_mulai DESC, ta.id DESC LIMIT 1");
    $stmt->bind_param('s', $nipParam);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($attempt) {
        $attemptId = (int)($attempt['id'] ?? 0);
    } else {
        $stmtUser = $conn->prepare("SELECT nip, nama, satuan_kerja, jabatan FROM users WHERE nip = ? LIMIT 1");
        $stmtUser->bind_param('s', $nipParam);
        $stmtUser->execute();
        $userRow = $stmtUser->get_result()->fetch_assoc();
        $stmtUser->close();

        if ($userRow) {
            $attempt = [
                'id' => 0,
                'nip' => $userRow['nip'],
                'nama' => $userRow['nama'],
                'satuan_kerja' => $userRow['satuan_kerja'],
                'jabatan' => $userRow['jabatan'],
                'test_type' => 'iq',
                'attempt_number' => 0,
                'tanggal_mulai' => null,
                'status' => 'finished',
            ];
        }
    }
} elseif ($testTypeParam !== '') {
    $stmt = $conn->prepare("SELECT ta.*, u.nama, u.satuan_kerja, u.jabatan FROM test_attempts ta JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci WHERE ta.id = ? AND ta.test_type = ? LIMIT 1");
    $stmt->bind_param('is', $attemptId, $testTypeParam);
} else {
    $stmt = $conn->prepare("SELECT ta.*, u.nama, u.satuan_kerja, u.jabatan FROM test_attempts ta JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci WHERE ta.id = ? LIMIT 1");
    $stmt->bind_param('i', $attemptId);
}
if (!$allowLegacyIq) {
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$attempt) {
    if ($testTypeParam === 'iq' && $nipParam !== '') {
        header('Location: hasil_iq.php?nip=' . urlencode($nipParam));
        exit;
    }
    if ($testTypeParam === 'msdt' && $nipParam !== '') {
        header('Location: hasil_msdt.php?nip=' . urlencode($nipParam));
        exit;
    }
    if ($testTypeParam === 'papi' && $nipParam !== '') {
        header('Location: hasil_papi.php?nip=' . urlencode($nipParam));
        exit;
    }

    header('Location: hasil_peserta.php');
    exit;
}

$testType = strtolower($attempt['test_type']);
$answers = [];
$summary = [];
$questionCount = 0;
$answeredCount = 0;
$correctCount = null;

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function answerBadge($answer) {
    if ($answer === null || $answer === '') {
        return '<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-400">Belum Dijawab</span>';
    }
    return '<span class="inline-flex items-center rounded-full bg-navy px-3 py-1 text-xs font-semibold text-white">' . h($answer) . '</span>';
}

function summaryValue(array $summary, array $keys, $default = 0) {
    foreach ($keys as $key) {
        if (array_key_exists($key, $summary) && $summary[$key] !== null && $summary[$key] !== '') {
            return $summary[$key];
        }
        $upperKey = strtoupper($key);
        if (array_key_exists($upperKey, $summary) && $summary[$upperKey] !== null && $summary[$upperKey] !== '') {
            return $summary[$upperKey];
        }
        $lowerKey = strtolower($key);
        if (array_key_exists($lowerKey, $summary) && $summary[$lowerKey] !== null && $summary[$lowerKey] !== '') {
            return $summary[$lowerKey];
        }
    }
    return $default;
}

function loadSingleRow(mysqli $conn, string $sql, string $type = '', $param = null): array {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }
    if ($type !== '') {
        $stmt->bind_param($type, $param);
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return $row;
}

function normalizeAnswerRow(array $row): array {
    foreach ($row as $key => $value) {
        $row[strtolower((string)$key)] = $value;
        $row[strtoupper((string)$key)] = $value;
    }
    return $row;
}

function parseAnswerTokens(string $answer): array {
    $tokens = preg_split('/[,;\r\n]+/', $answer);
    if ($tokens === false) {
        return [];
    }

    $clean = [];
    foreach ($tokens as $token) {
        $trimmed = trim((string)$token);
        if ($trimmed !== '') {
            $clean[] = $trimmed;
        }
    }

    return array_values(array_unique($clean));
}

if ($testType === 'iq') {
    $sectionCodeMap = [
        1 => 'SE',
        2 => 'WA',
        3 => 'AN',
        4 => 'GE',
        5 => 'RA',
        6 => 'ZR',
        7 => 'FA',
        8 => 'WU',
        9 => 'ME',
    ];

    $sections = [
        ['se', 1, 20], ['wa', 21, 40], ['an', 41, 60], ['ge', 61, 76],
        ['ra', 77, 96], ['zr', 97, 116], ['fa', 117, 136], ['wu', 137, 156], ['me', 157, 176]
    ];

    $summaryStmt = $conn->prepare("SELECT se, wa, an, ge, ra, zr, fa, wu, me, skor_total FROM iq_attempt_results WHERE attempt_id = ? LIMIT 1");
    $summaryStmt->bind_param('i', $attemptId);
    $summaryStmt->execute();
    $summary = $summaryStmt->get_result()->fetch_assoc() ?: [];
    $summaryStmt->close();

    if (empty($summary)) {
        $summary = loadSingleRow($conn, "SELECT skor AS skor_total, tanggal AS tanggal_hitung FROM iq_results WHERE user_id = (SELECT id FROM users WHERE nip = ? LIMIT 1) ORDER BY tanggal DESC LIMIT 1", 's', $attempt['nip']);
    }

    $fillAnswersByQuestion = [];
    $stmtFill = $conn->prepare("SELECT question_id, jawaban, nilai FROM iq_fill_answers ORDER BY question_id ASC, id ASC");
    if ($stmtFill) {
        $stmtFill->execute();
        $fillResult = $stmtFill->get_result();
        while ($fill = $fillResult->fetch_assoc()) {
            $qid = (int)$fill['question_id'];
            if (!isset($fillAnswersByQuestion[$qid])) {
                $fillAnswersByQuestion[$qid] = [];
            }
            $fillAnswersByQuestion[$qid][] = $fill;
        }
        $stmtFill->close();
    }

    $optionsByQuestion = [];
    $stmtOpt = $conn->prepare("SELECT question_id, label, opsi_text, gambar_opsi FROM iq_options ORDER BY question_id ASC, label ASC");
    if ($stmtOpt) {
        $stmtOpt->execute();
        $optResult = $stmtOpt->get_result();
        while ($opt = $optResult->fetch_assoc()) {
            $qid = (int)$opt['question_id'];
            if (!isset($optionsByQuestion[$qid])) {
                $optionsByQuestion[$qid] = [];
            }
            $optionsByQuestion[$qid][] = $opt;
        }
        $stmtOpt->close();
    }

    $iqRows = [];
    $hasAnsweredFromAttempt = false;

    $stmt = $conn->prepare("SELECT q.id, q.nomor_soal, q.section_id, q.pertanyaan, q.gambar, q.jawaban_benar, s.urutan AS section_urutan, ua.jawaban_user FROM iq_questions q JOIN iq_sections s ON q.section_id = s.id LEFT JOIN iq_attempt_answers ua ON ua.question_id = q.id AND ua.attempt_id = ? ORDER BY s.urutan ASC, q.nomor_soal ASC, q.id ASC");
    $stmt->bind_param('i', $attemptId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['jawaban_user'])) {
            $hasAnsweredFromAttempt = true;
        }
        $iqRows[] = $row;
    }
    $stmt->close();

    if (!$hasAnsweredFromAttempt) {
        $iqRows = [];
        $stmt = $conn->prepare("SELECT q.id, q.nomor_soal, q.section_id, q.pertanyaan, q.gambar, q.jawaban_benar, s.urutan AS section_urutan, ua.jawaban_user FROM iq_questions q JOIN iq_sections s ON q.section_id = s.id LEFT JOIN iq_user_answers ua ON ua.question_id = q.id AND ua.user_nip = ? ORDER BY s.urutan ASC, q.nomor_soal ASC, q.id ASC");
        $stmt->bind_param('s', $attempt['nip']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $iqRows[] = $row;
        }
        $stmt->close();
    }

    foreach ($iqRows as $q) {
        $qId = (int)$q['id'];
        $sectionUrutan = (int)($q['section_urutan'] ?? 0);
        $sectionName = $sectionCodeMap[$sectionUrutan] ?? 'UNKNOWN';
        $qNumber = (int)($q['nomor_soal'] ?? 0);
        if ($qNumber <= 0) {
            $qNumber = $qId;
        }

        $normAnswers = $fillAnswersByQuestion[$qId] ?? [];
        $normTexts = [];
        $normValues = [];
        foreach ($normAnswers as $norm) {
            $normTexts[] = $norm['jawaban'];
            $normValues[] = (int)$norm['nilai'];
        }

        $matchedNorms = [];
        if (!empty($q['jawaban_user']) && !empty($normAnswers)) {
            $userAnswersArray = parseAnswerTokens((string)$q['jawaban_user']);
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
            'section' => $sectionName,
            'section_urutan' => $sectionUrutan,
            'question_number' => $qNumber,
            'question_text' => $q['pertanyaan'],
            'question_image' => $q['gambar'] ?? null,
            'user_answer' => $q['jawaban_user'],
            'correct_answer' => $q['jawaban_benar'],
            'options' => $optionsByQuestion[$qId] ?? [],
            'norm_answers' => $normTexts,
            'norm_values' => $normValues,
            'matched_norms' => $matchedNorms
        ];
    }

    $questionCount = count($answers);
    foreach ($answers as $row) {
        if (!empty($row['user_answer'])) {
            $answeredCount++;
        }
        if (!empty($row['user_answer']) && !empty($row['correct_answer']) && strcasecmp(trim((string)$row['user_answer']), trim((string)$row['correct_answer'])) === 0) {
            if ($correctCount === null) {
                $correctCount = 0;
            }
            $correctCount++;
        }
    }

    $iqKeys = ['se', 'wa', 'an', 'ge', 'ra', 'zr', 'fa', 'wu', 'me'];
    $computedIqSummary = array_fill_keys($iqKeys, 0);
    foreach ($answers as $row) {
        $sectionKey = strtolower((string)($row['section'] ?? ''));
        if (!array_key_exists($sectionKey, $computedIqSummary)) {
            continue;
        }

        $userAnswer = trim((string)($row['user_answer'] ?? ''));
        if ($userAnswer === '') {
            continue;
        }

        if ($sectionKey === 'ge') {
            $geScore = 0;
            if (!empty($row['matched_norms']) && is_array($row['matched_norms'])) {
                foreach ($row['matched_norms'] as $normRow) {
                    $geScore += (int)($normRow['nilai'] ?? 0);
                }
            } elseif (!empty($row['correct_answer']) && strcasecmp($userAnswer, trim((string)$row['correct_answer'])) === 0) {
                $geScore = 1;
            }
            $computedIqSummary[$sectionKey] += $geScore;
            continue;
        }

        $correctAnswer = trim((string)($row['correct_answer'] ?? ''));
        if ($correctAnswer !== '' && strcasecmp($userAnswer, $correctAnswer) === 0) {
            $computedIqSummary[$sectionKey]++;
        }
    }
    $computedIqSummary['skor_total'] = array_sum($computedIqSummary);

    $storedSectionsTotal = 0;
    foreach ($iqKeys as $scoreKey) {
        $storedSectionsTotal += (int)summaryValue($summary, [$scoreKey], 0);
    }
    $storedTotal = (int)summaryValue($summary, ['skor_total', 'skor', 'score'], 0);
    if ($answeredCount > 0 && $storedSectionsTotal === 0) {
        foreach ($iqKeys as $scoreKey) {
            $summary[$scoreKey] = $computedIqSummary[$scoreKey];
        }
        if ($storedTotal === 0) {
            $summary['skor_total'] = $computedIqSummary['skor_total'];
        }
    } elseif ($storedTotal === 0 && $storedSectionsTotal > 0) {
        $summary['skor_total'] = $storedSectionsTotal;
    }
} elseif ($testType === 'papi') {
    $summaryStmt = $conn->prepare("SELECT * FROM papi_attempt_results WHERE attempt_id = ? LIMIT 1");
    $summaryStmt->bind_param('i', $attemptId);
    $summaryStmt->execute();
    $summary = $summaryStmt->get_result()->fetch_assoc() ?: [];
    $summaryStmt->close();

    if (empty($summary)) {
        $summary = loadSingleRow($conn, "SELECT * FROM hasil_papi WHERE nip = ? ORDER BY tanggal_tes DESC LIMIT 1", 's', $attempt['nip']);
    }

    $papiTestCode = 'KEPRIBADIAN2';
    $codeCheckStmt = $conn->prepare("SELECT 1 FROM soal WHERE kode_tes = 'KEPRIBADIAN2' LIMIT 1");
    $codeCheckStmt->execute();
    $hasPapi2 = $codeCheckStmt->get_result()->num_rows > 0;
    $codeCheckStmt->close();

    if (!$hasPapi2) {
        $papiTestCode = 'KEPRIBADIAN';
    }

    $stmt = $conn->prepare("SELECT COALESCE(NULLIF(s.nomor_soal, 0), s.id) AS question_no, COALESCE(NULLIF(s.pertanyaan_a, ''), '-') AS pertanyaan_a, COALESCE(NULLIF(s.pertanyaan_b, ''), '-') AS pertanyaan_b, pa.jawaban_user, pa.mapped_dimension FROM soal s LEFT JOIN papi_attempt_answers pa ON pa.attempt_id = ? AND (pa.question_no = COALESCE(NULLIF(s.nomor_soal, 0), s.id) OR pa.question_no = s.id) WHERE s.kode_tes = ? ORDER BY COALESCE(NULLIF(s.nomor_soal, 0), s.id) ASC");
    $stmt->bind_param('is', $attemptId, $papiTestCode);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $answers[] = normalizeAnswerRow($row);
    }
    $stmt->close();

    $questionCount = count($answers);
    foreach ($answers as $row) {
        if (!empty($row['jawaban_user'])) {
            $answeredCount++;
        }
    }
} elseif ($testType === 'msdt') {
    $summaryStmt = $conn->prepare("SELECT * FROM msdt_attempt_results WHERE attempt_id = ? LIMIT 1");
    $summaryStmt->bind_param('i', $attemptId);
    $summaryStmt->execute();
    $summary = $summaryStmt->get_result()->fetch_assoc() ?: [];
    $summaryStmt->close();

    if (empty($summary)) {
        $summary = loadSingleRow($conn, "SELECT * FROM hasil_msdt WHERE nip = ? ORDER BY tanggal_tes DESC LIMIT 1", 's', $attempt['nip']);
    }

    $stmt = $conn->prepare("SELECT COALESCE(NULLIF(s.nomor_soal, 0), s.id) AS question_no, COALESCE(NULLIF(s.pertanyaan_a, ''), '-') AS pertanyaan_a, COALESCE(NULLIF(s.pertanyaan_b, ''), '-') AS pertanyaan_b, ma.jawaban_user FROM soal s LEFT JOIN msdt_attempt_answers ma ON ma.attempt_id = ? AND (ma.question_no = COALESCE(NULLIF(s.nomor_soal, 0), s.id) OR ma.question_no = s.id) WHERE s.kode_tes = 'KEPRIBADIAN' ORDER BY COALESCE(NULLIF(s.nomor_soal, 0), s.id) ASC");
    $stmt->bind_param('i', $attemptId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $answers[] = normalizeAnswerRow($row);
    }
    $stmt->close();
    $questionCount = count($answers);
    foreach ($answers as $row) {
        if (!empty($row['jawaban_user'])) {
            $answeredCount++;
        }
    }
}

$fallbackBackUrl = 'detail_pegawai.php?nip=' . urlencode((string)($attempt['nip'] ?? $nipParam));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jawaban | Admin BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { navy: { DEFAULT: '#0F1E3C' } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">
    <div class="mb-8 pb-6 border-b border-slate-200 flex items-start justify-between gap-4">
        <div>
            <a id="btn-back-page" href="<?= h($fallbackBackUrl) ?>" class="inline-flex items-center text-sm text-slate-500 hover:text-navy mb-4 transition-colors">← Kembali ke Halaman Sebelumnya</a>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight"><?= h($title ?: 'Detail Jawaban') ?></h1>
            <p class="text-slate-500 text-sm mt-1"><?= h($attempt['nama']) ?> · <?= h(strtoupper($testType)) ?> · Percobaan #<?= h($attempt['attempt_number'] ?? '-') ?></p>
        </div>
        <div class="text-right text-sm text-slate-500">
            <p class="font-semibold text-slate-700"><?= h($attempt['satuan_kerja'] ?? '-') ?></p>
            <p><?= h($attempt['jabatan'] ?? '-') ?></p>
            <p class="mt-1">Tanggal: <?= !empty($attempt['tanggal_mulai']) ? date('d/m/Y H:i', strtotime($attempt['tanggal_mulai'])) : '-' ?></p>
            <p>Status: <span class="font-semibold text-slate-700"><?= h($attempt['status'] ?? '-') ?></span></p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4 mb-6">
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Soal Tersedia</p>
            <p class="mt-2 text-2xl font-extrabold text-navy"><?= (int)$questionCount ?></p>
        </div>
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Sudah Dijawab</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-600"><?= (int)$answeredCount ?></p>
        </div>
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Belum Dijawab</p>
            <p class="mt-2 text-2xl font-extrabold text-rose-600"><?= max(0, (int)$questionCount - (int)$answeredCount) ?></p>
        </div>
        <div class="rounded-2xl bg-white p-5 border border-slate-200 shadow-sm">
            <?php if ($testType === 'papi'): ?>
                <?php
                    $dominantRole = '';
                    $roleKeys = ['G','L','I','T','V','S','R','D','C','E'];
                    $bestRoleValue = null;
                    foreach ($roleKeys as $roleKey) {
                        $roleValue = (int)summaryValue($summary, [$roleKey], 0);
                        if ($bestRoleValue === null || $roleValue > $bestRoleValue) {
                            $bestRoleValue = $roleValue;
                            $dominantRole = $roleKey;
                        }
                    }
                ?>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Dominan</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-800"><?= h(summaryValue($summary, ['dominant_model'], $dominantRole ?: '-')) ?></p>
            <?php elseif ($testType === 'msdt'): ?>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">TO Score</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-800"><?= h(summaryValue($summary, ['TO_score', 'to_score'], 0)) ?></p>
            <?php else: ?>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Skor</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-800"><?= h(summaryValue($summary, ['skor_total', 'skor', 'score'], '-')) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($testType === 'iq'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-navy mb-4">Ringkasan Skor IQ</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                <?php foreach (['se' => 'SE', 'wa' => 'WA', 'an' => 'AN', 'ge' => 'GE', 'ra' => 'RA', 'zr' => 'ZR', 'fa' => 'FA', 'wu' => 'WU', 'me' => 'ME'] as $key => $label): ?>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-400 uppercase"><?= h($label) ?></div>
                        <div class="text-lg font-extrabold text-navy mt-1"><?= h(summaryValue($summary, [$key], 0)) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif ($testType === 'msdt'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-navy mb-4">Ringkasan MSDT</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <?php foreach (['TO_score' => 'TO', 'RO_score' => 'RO', 'E_score' => 'E', 'O_score' => 'O'] as $key => $label): ?>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-400 uppercase"><?= h($label) ?></div>
                        <div class="text-lg font-extrabold text-navy mt-1"><?= h($summary[$key] ?? 0) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif ($testType === 'papi'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-navy mb-4">Ringkasan PAPI</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                <?php foreach (['G','L','I','T','V','S','R','D','C','E','N','A','P','X','B','O','K','F','W','Z'] as $label): ?>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                        <div class="text-xs font-bold text-slate-400 uppercase"><?= h($label) ?></div>
                        <div class="text-lg font-extrabold text-navy mt-1"><?= h($summary[$label] ?? 0) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php if ($testType === 'iq'): ?>
            <?php foreach ($answers as $idx => $item): ?>
                <?php
                    $section = strtoupper((string)($item['section'] ?? '-'));
                    $displayNo = (int)($item['question_number'] ?? 0);
                    if ($displayNo <= 0) {
                        $displayNo = $idx + 1;
                    }
                    $userAnswer = $item['user_answer'] ?? '';
                    $correctAnswer = $item['correct_answer'] ?? '';
                    $isCorrect = !empty($userAnswer) && !empty($correctAnswer) && strcasecmp(trim((string)$userAnswer), trim((string)$correctAnswer)) === 0;
                    $isStatusCorrect = $isCorrect;
                    if ($section === 'GE' && !empty($userAnswer)) {
                        $geMatchCount = 0;
                        if (!empty($item['norm_answers']) && is_array($item['norm_answers'])) {
                            $userAnswerTokens = parseAnswerTokens((string)$userAnswer);
                            foreach ($userAnswerTokens as $token) {
                                foreach ($item['norm_answers'] as $normText) {
                                    if (strcasecmp($token, trim((string)$normText)) === 0) {
                                        $geMatchCount++;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($geMatchCount > 0) {
                            $isStatusCorrect = true;
                        } else {
                            $isStatusCorrect = !empty($correctAnswer) && strcasecmp(trim((string)$userAnswer), trim((string)$correctAnswer)) === 0;
                        }
                    }
                    $isImageSection = in_array((int)($item['section_urutan'] ?? 0), [4, 8, 9], true);
                    $statusOutlineClass = 'border-slate-200';
                    if (!empty($userAnswer)) {
                        $statusOutlineClass = $isStatusCorrect ? 'border-emerald-400 ring-1 ring-emerald-200' : 'border-rose-400 ring-1 ring-rose-200';
                    }
                    $questionImage = trim((string)($item['question_image'] ?? ''));
                    $questionImageSrc = $questionImage !== '' ? '../images/img_soal/' . ltrim($questionImage, '/\\') : '';
                ?>
                <div class="bg-white rounded-2xl border <?= h($statusOutlineClass) ?> shadow-sm p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400"><?= h($section) ?> · Soal <?= $displayNo ?></p>
                            <p class="mt-2 text-slate-700 font-medium leading-relaxed"><?= h($item['question_text'] ?? '-') ?></p>
                        </div>
                        <div>
                            <?php if (empty($userAnswer)): ?>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Tidak Dijawab</span>
                            <?php elseif ($isStatusCorrect): ?>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Benar</span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Salah</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isImageSection): ?>
                        <?php if ($questionImageSrc !== ''): ?>
                            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Gambar Soal</p>
                                <img src="<?= h($questionImageSrc) ?>" alt="Soal <?= $displayNo ?>" class="mx-auto max-h-56 w-auto rounded-lg border border-slate-200 bg-white p-2 object-contain">
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($item['options']) && is_array($item['options'])): ?>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <?php foreach ($item['options'] as $opt): ?>
                                    <?php
                                        $optLabel = strtoupper((string)($opt['label'] ?? ''));
                                        $isSelected = $userAnswer !== '' && strcasecmp($userAnswer, $optLabel) === 0;
                                        $isRightOption = $correctAnswer !== '' && strcasecmp($correctAnswer, $optLabel) === 0;
                                        $optionClass = 'border-slate-200';
                                        if ($isSelected && $isRightOption) {
                                            $optionClass = 'border-emerald-500 ring-1 ring-emerald-200';
                                        } elseif ($isSelected && !$isRightOption) {
                                            $optionClass = 'border-rose-500 ring-1 ring-rose-200';
                                        } elseif (!$isSelected && $isRightOption) {
                                            $optionClass = 'border-emerald-300';
                                        }
                                        $optionImage = trim((string)($opt['gambar_opsi'] ?? ''));
                                        $optionImageSrc = $optionImage !== '' ? '../images/img_soal/' . ltrim($optionImage, '/\\') : '';
                                    ?>
                                    <div class="rounded-xl border <?= h($optionClass) ?> bg-white p-3">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700">Pilihan <?= h($optLabel) ?></span>
                                            <?php if ($isSelected): ?>
                                                <span class="text-[11px] font-bold text-slate-500">Dipilih</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($optionImageSrc !== ''): ?>
                                            <img src="<?= h($optionImageSrc) ?>" alt="Pilihan <?= h($optLabel) ?>" class="mx-auto mb-2 max-h-32 w-auto rounded border border-slate-200 bg-slate-50 p-1 object-contain">
                                        <?php endif; ?>
                                        <?php if (!empty($opt['opsi_text'])): ?>
                                            <p class="text-sm text-slate-700"><?= h($opt['opsi_text']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 grid gap-3 md:grid-cols-2 text-sm">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jawaban Pegawai</p>
                                <div class="mt-2 text-slate-800 font-semibold"><?= h($userAnswer ?: '-') ?></div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jawaban Benar</p>
                                <?php if ($section === 'GE' && !empty($item['norm_answers']) && is_array($item['norm_answers'])): ?>
                                    <ul class="mt-2 space-y-1">
                                        <?php foreach ($item['norm_answers'] as $i => $normText): ?>
                                            <li class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-white px-2 py-1">
                                                <span class="text-slate-700"><?= h($normText) ?></span>
                                                <span class="inline-flex items-center rounded bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">Poin <?= h((string)($item['norm_values'][$i] ?? 0)) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="mt-2 text-slate-800 font-semibold"><?= h($correctAnswer ?: '-') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 grid gap-3 md:grid-cols-2 text-sm">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jawaban Pegawai</p>
                                <div class="mt-2 text-slate-800 font-semibold"><?= h($userAnswer ?: '-') ?></div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jawaban Benar</p>
                                <?php if ($section === 'GE' && !empty($item['norm_answers']) && is_array($item['norm_answers'])): ?>
                                    <ul class="mt-2 space-y-1">
                                        <?php foreach ($item['norm_answers'] as $i => $normText): ?>
                                            <li class="flex items-center justify-between gap-3 rounded-md border border-slate-200 bg-white px-2 py-1">
                                                <span class="text-slate-700"><?= h($normText) ?></span>
                                                <span class="inline-flex items-center rounded bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">Poin <?= h((string)($item['norm_values'][$i] ?? 0)) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="mt-2 text-slate-800 font-semibold"><?= h($correctAnswer ?: '-') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($answers as $idx => $item): ?>
                <?php
                    $displayNo = (int)($item['question_no'] ?? 0);
                    if ($displayNo <= 0) {
                        $displayNo = $idx + 1;
                    }
                    $userAnswer = $item['jawaban_user'] ?? '';
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Soal <?= $displayNo ?></p>
                            <div class="mt-2 text-slate-700 font-medium leading-relaxed">
                                <p>Pilihan A: <?= h($item['pertanyaan_a'] ?? '-') ?></p>
                                <p class="mt-1">Pilihan B: <?= h($item['pertanyaan_b'] ?? '-') ?></p>
                            </div>
                        </div>
                        <div>
                            <?= answerBadge($userAnswer) ?>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-slate-600">
                        <?php if ($testType === 'papi' && !empty($item['mapped_dimension'])): ?>
                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Dimensi: <?= h($item['mapped_dimension']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
(function () {
    const backBtn = document.getElementById('btn-back-page');
    if (!backBtn) return;

    backBtn.addEventListener('click', function (event) {
        event.preventDefault();

        if (window.history.length > 1) {
            window.history.back();
            return;
        }

        const referrer = document.referrer || '';
        if (referrer) {
            try {
                const refUrl = new URL(referrer);
                if (refUrl.origin === window.location.origin) {
                    window.location.href = refUrl.pathname + refUrl.search + refUrl.hash;
                    return;
                }
            } catch (error) {
                // Abaikan URL referrer yang tidak valid dan pakai fallback.
            }
        }

        window.location.href = backBtn.getAttribute('href') || 'hasil_peserta.php';
    });
})();
</script>
</body>
</html>
