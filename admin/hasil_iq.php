<?php
include '../backend/auth_check.php';
require_once '../backend/config.php';
require_once '../backend/test_attempt_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$context = $_SESSION['admin_result_context'] ?? [];
$nip_filter = trim($_GET['nip'] ?? '');
$attempt_filter = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;

if ($attempt_filter === 0 && !empty($context['target']) && $context['target'] === 'hasil_iq.php') {
    $attempt_filter = (int)($context['attempt_id'] ?? 0);
}

if ($nip_filter === '' && !empty($context['target']) && $context['target'] === 'hasil_iq.php') {
    $nip_filter = trim($context['nip'] ?? '');
}

$sessionAttemptId = isset($_SESSION['admin_hasil_iq_attempt_id']) ? (int)$_SESSION['admin_hasil_iq_attempt_id'] : 0;
$sessionNip = trim($_SESSION['admin_hasil_iq_nip'] ?? '');

if ($attempt_filter === 0 && $sessionAttemptId > 0) {
    $attempt_filter = $sessionAttemptId;
}

if ($nip_filter === '' && $sessionNip !== '') {
    $nip_filter = $sessionNip;
}

$hasUnifiedAttempts = false;
$cekUnifiedAttempts = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
$cekIqAttemptResults = mysqli_query($conn, "SHOW TABLES LIKE 'iq_attempt_results'");
if ($cekUnifiedAttempts && mysqli_num_rows($cekUnifiedAttempts) > 0 && $cekIqAttemptResults && mysqli_num_rows($cekIqAttemptResults) > 0) {
    $cekUnifiedFinished = mysqli_query($conn, "SELECT 1 FROM test_attempts WHERE test_type = 'iq' AND status = 'finished' LIMIT 1");
    if ($cekUnifiedFinished && mysqli_num_rows($cekUnifiedFinished) > 0) {
        $hasUnifiedAttempts = true;
    }
}

function hitungIqAttemptRincian($conn, $attemptId) {
    $rincian = [
        'SE' => 0,
        'WA' => 0,
        'AN' => 0,
        'GE' => 0,
        'RA' => 0,
        'ZR' => 0,
        'FA' => 0,
        'WU' => 0,
        'ME' => 0,
    ];

    $stmt = $conn->prepare("\n        SELECT ua.question_id, ua.jawaban_user, q.jawaban_benar, s.urutan\n        FROM iq_attempt_answers ua\n        JOIN iq_questions q ON ua.question_id = q.id\n        JOIN iq_sections s ON q.section_id = s.id\n        WHERE ua.attempt_id = ?\n        ORDER BY ua.question_id ASC\n    ");
    $stmt->bind_param('i', $attemptId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $kode = null;
        switch ((int)$row['urutan']) {
            case 1: $kode = 'SE'; break;
            case 2: $kode = 'WA'; break;
            case 3: $kode = 'AN'; break;
            case 4: $kode = 'GE'; break;
            case 5: $kode = 'RA'; break;
            case 6: $kode = 'ZR'; break;
            case 7: $kode = 'FA'; break;
            case 8: $kode = 'WU'; break;
            case 9: $kode = 'ME'; break;
        }

        if ($kode === null) {
            continue;
        }

        if ($kode === 'GE') {
            $stmtFill = $conn->prepare("\n                SELECT COALESCE(MAX(nilai), 0) AS nilai_maks\n                FROM iq_fill_answers\n                WHERE question_id = ?\n                  AND FIND_IN_SET(LOWER(TRIM(?)), REPLACE(LOWER(jawaban), ', ', ',')) > 0\n            ");
            $stmtFill->bind_param('is', $row['question_id'], $row['jawaban_user']);
            $stmtFill->execute();
            $rowFill = $stmtFill->get_result()->fetch_assoc();
            $stmtFill->close();
            $rincian[$kode] += (int)($rowFill['nilai_maks'] ?? 0);
        } else {
            if (strcasecmp(trim((string)$row['jawaban_user']), trim((string)$row['jawaban_benar'])) === 0) {
                $rincian[$kode]++;
            }
        }
    }

    $stmt->close();
    return $rincian;
}

function fetchIqAttemptById($conn, $attemptId) {
    $stmt = $conn->prepare("\n        SELECT\n            ta.id AS attempt_id,\n            ta.nip,\n            u.nama,\n            u.satuan_kerja,\n            ta.attempt_number,\n            ta.tanggal_mulai,\n            ta.tanggal_selesai,\n            COALESCE(r.se, 0) AS se,\n            COALESCE(r.wa, 0) AS wa,\n            COALESCE(r.an, 0) AS an,\n            COALESCE(r.ge, 0) AS ge,\n            COALESCE(r.ra, 0) AS ra,\n            COALESCE(r.zr, 0) AS zr,\n            COALESCE(r.fa, 0) AS fa,\n            COALESCE(r.wu, 0) AS wu,\n            COALESCE(r.me, 0) AS me,\n            COALESCE(r.skor_total, 0) AS skor_total\n        FROM test_attempts ta\n        JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci\n        LEFT JOIN iq_attempt_results r ON r.attempt_id = ta.id\n        WHERE ta.id = ? AND ta.test_type = 'iq' AND ta.status = 'finished'\n        LIMIT 1\n    ");
    $stmt->bind_param('i', $attemptId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    $rincian = [
        'SE' => (int)$row['se'],
        'WA' => (int)$row['wa'],
        'AN' => (int)$row['an'],
        'GE' => (int)$row['ge'],
        'RA' => (int)$row['ra'],
        'ZR' => (int)$row['zr'],
        'FA' => (int)$row['fa'],
        'WU' => (int)$row['wu'],
        'ME' => (int)$row['me'],
    ];

    if (array_sum($rincian) === 0) {
        $rincian = hitungIqAttemptRincian($conn, $attemptId);
        $row['skor_total'] = array_sum($rincian);
    }

    $row['rincian'] = $rincian;
    return $row;
}

// Ambil peserta yang dipilih atau semua peserta yang sudah selesai tes IQ
if ($hasUnifiedAttempts) {
    if ($attempt_filter > 0) {
        $singleAttempt = fetchIqAttemptById($conn, $attempt_filter);
        $users = $singleAttempt ? [$singleAttempt] : [];
    } elseif ($nip_filter !== '') {
        $query_users = "
            SELECT
                ta.id AS attempt_id,
                ta.nip,
                u.nama,
                u.satuan_kerja,
                ta.attempt_number,
                ta.tanggal_mulai,
                ta.tanggal_selesai,
                COALESCE(r.se, 0) AS se,
                COALESCE(r.wa, 0) AS wa,
                COALESCE(r.an, 0) AS an,
                COALESCE(r.ge, 0) AS ge,
                COALESCE(r.ra, 0) AS ra,
                COALESCE(r.zr, 0) AS zr,
                COALESCE(r.fa, 0) AS fa,
                COALESCE(r.wu, 0) AS wu,
                COALESCE(r.me, 0) AS me,
                COALESCE(r.skor_total, 0) AS skor_total
            FROM test_attempts ta
            JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci
            LEFT JOIN iq_attempt_results r ON r.attempt_id = ta.id
            WHERE u.role = 'peserta' AND ta.test_type = 'iq' AND ta.status = 'finished' AND ta.nip = ?
            ORDER BY ta.tanggal_mulai DESC, ta.id DESC
        ";
        $stmt = $conn->prepare($query_users);
        $stmt->bind_param('s', $nip_filter);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $query_users = "
            SELECT
                ta.id AS attempt_id,
                ta.nip,
                u.nama,
                u.satuan_kerja,
                ta.attempt_number,
                ta.tanggal_mulai,
                ta.tanggal_selesai,
                COALESCE(r.se, 0) AS se,
                COALESCE(r.wa, 0) AS wa,
                COALESCE(r.an, 0) AS an,
                COALESCE(r.ge, 0) AS ge,
                COALESCE(r.ra, 0) AS ra,
                COALESCE(r.zr, 0) AS zr,
                COALESCE(r.fa, 0) AS fa,
                COALESCE(r.wu, 0) AS wu,
                COALESCE(r.me, 0) AS me,
                COALESCE(r.skor_total, 0) AS skor_total
            FROM test_attempts ta
            JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci
            LEFT JOIN iq_attempt_results r ON r.attempt_id = ta.id
            WHERE u.role = 'peserta' AND ta.test_type = 'iq' AND ta.status = 'finished'
            ORDER BY ta.tanggal_mulai DESC, ta.id DESC
        ";
        $stmt = $conn->prepare($query_users);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} else {
    if ($nip_filter !== '') {
        $query_users = "
            SELECT u.nama, u.nip, u.satuan_kerja, r.tanggal
            FROM users u
            JOIN iq_results r ON u.nip COLLATE utf8mb4_unicode_ci = r.user_id COLLATE utf8mb4_unicode_ci
            WHERE u.role = 'peserta' AND u.nip = ?
            LIMIT 1
        ";
        $stmt = $conn->prepare($query_users);
        $stmt->bind_param('s', $nip_filter);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $query_users = "
            SELECT u.nama, u.nip, u.satuan_kerja, r.tanggal
            FROM users u
            JOIN iq_results r ON u.nip COLLATE utf8mb4_unicode_ci = r.user_id COLLATE utf8mb4_unicode_ci
            WHERE u.role = 'peserta'
            ORDER BY u.nama ASC
        ";
        $stmt = $conn->prepare($query_users);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$labels_info = [
    'SE' => 'Melengkapi Kalimat',
    'WA' => 'Mencari Kata Berbeda',
    'AN' => 'Hubungan Kata',
    'GE' => 'Kesamaan Kata',
    'RA' => 'Hitungan Praktis',
    'ZR' => 'Deret Angka',
    'FA' => 'Potongan Gambar',
    'WU' => 'Kemampuan Ruang',
    'ME' => 'Mengingat Kata',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Hasil Tes 1 | Admin PETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']},colors:{navy:{DEFAULT:'#0F1E3C'}}}}}</script>
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;}
        ::-webkit-scrollbar{width:6px;height:6px;}
        ::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:10px;}
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Hasil Tes 1</h1>
            <p class="text-slate-500 text-sm mt-1">Setiap kartu mewakili satu percobaan tes sesuai tanggal dan waktu pengerjaan.</p>
        </div>
        <a href="hasil_peserta.php"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
            ← Kembali
        </a>
    </div>

    <?php if (empty($users)): ?>
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-16 text-center text-slate-400">
        <div class="text-5xl mb-4">📭</div>
        <p class="text-sm">Belum ada peserta yang menyelesaikan tes.</p>
    </div>
    <?php else: ?>
    <?php if ($attempt_filter > 0): ?>
    <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        Menampilkan satu percobaan yang dipilih.
    </div>
    <?php endif; ?>

    <!-- Cards -->
    <div class="space-y-6" id="grid">
    <?php $chartData = []; ?>
    <?php foreach ($users as $idx => $u):
        if ($hasUnifiedAttempts) {
            $rincian = isset($u['rincian']) ? $u['rincian'] : [
                'SE' => (int)($u['se'] ?? 0),
                'WA' => (int)($u['wa'] ?? 0),
                'AN' => (int)($u['an'] ?? 0),
                'GE' => (int)($u['ge'] ?? 0),
                'RA' => (int)($u['ra'] ?? 0),
                'ZR' => (int)($u['zr'] ?? 0),
                'FA' => (int)($u['fa'] ?? 0),
                'WU' => (int)($u['wu'] ?? 0),
                'ME' => (int)($u['me'] ?? 0),
            ];
            if (array_sum($rincian) === 0 && !empty($u['attempt_id'])) {
                $rincian = hitungIqAttemptRincian($conn, (int)$u['attempt_id']);
                $u['skor_total'] = array_sum($rincian);
            }
            $chart_values = array_values($rincian);
        } else {
        // Ambil rincian per subtes
        $stmt_r = $conn->prepare("\n            SELECT \n                SUM(CASE WHEN s.urutan=1 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,\n                SUM(CASE WHEN s.urutan=2 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,\n                SUM(CASE WHEN s.urutan=3 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,\n                SUM(CASE WHEN s.urutan=4 THEN COALESCE((SELECT MAX(nilai) FROM iq_fill_answers fa WHERE fa.question_id=ua.question_id AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)),REPLACE(LOWER(fa.jawaban),', ',','))>0),0) ELSE 0 END) AS GE,\n                SUM(CASE WHEN s.urutan=5 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,\n                SUM(CASE WHEN s.urutan=6 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,\n                SUM(CASE WHEN s.urutan=7 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,\n                SUM(CASE WHEN s.urutan=8 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,\n                SUM(CASE WHEN s.urutan=9 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME\n            FROM iq_user_answers ua\n            JOIN iq_questions q ON ua.question_id=q.id\n            JOIN iq_sections s ON q.section_id=s.id\n            WHERE ua.user_nip=?\n        ");
        $stmt_r->bind_param("s", $u['nip']);
        $stmt_r->execute();
        $rincian = $stmt_r->get_result()->fetch_assoc();
        $chart_values = array_values($rincian);
        }
    ?>
    <?php $chartData[$idx] = $chart_values; $scoreIq = $hasUnifiedAttempts ? ($u['skor_total'] ?? null) : null; ?>
    <div class="card" data-s="<?= strtolower($u['nama'].' '.$u['nip'].' '.($u['satuan_kerja']??'')) ?>">

        <!-- Profile banner -->
        <div class="relative bg-gradient-to-r from-[#0F1E3C] to-[#1E3260] text-white rounded-t-2xl px-6 py-5 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="absolute -right-4 -bottom-8 w-32 h-32 rounded-full bg-white/5"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-1">Laporan Hasil Evaluasi Individu</p>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl font-extrabold uppercase tracking-tight"><?= htmlspecialchars($u['nama']) ?></h2>
                <?php if ($hasUnifiedAttempts && !empty($u['attempt_number'])): ?>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-white border border-white/10">Percobaan #<?= (int)$u['attempt_number'] ?></span>
                <?php endif; ?>
                <?php if ($scoreIq !== null): ?>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-white border border-white/10">Skor Total: <?= (int)$scoreIq ?></span>
                <?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-blue-200">
                <span>NIP: <?= $u['nip'] ?></span>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span><?= htmlspecialchars($u['satuan_kerja'] ?? '-') ?></span>
                <?php if (!empty($u['tanggal_mulai'])): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Mulai: <?= date('d F Y H:i', strtotime($u['tanggal_mulai'])) ?></span>
                <?php endif; ?>
                <?php if (!empty($u['tanggal_selesai'])): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Selesai: <?= date('d F Y H:i', strtotime($u['tanggal_selesai'])) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Body: Chart + Tabel -->
        <div class="grid grid-cols-5 gap-0 bg-white rounded-b-2xl shadow-sm border border-slate-100 border-t-0">

            <!-- Line Chart -->
            <div class="col-span-2 p-6 border-r border-slate-100">
                <h3 class="text-sm font-bold text-navy mb-4 text-center">Profil Kemampuan Kognitif</h3>
                <div style="height:240px">
                    <canvas id="chart_<?= $idx ?>"></canvas>
                </div>
            </div>

            <!-- Tabel subtes -->
            <div class="col-span-3 p-6">
                <h3 class="text-sm font-bold text-navy mb-4 pb-3 border-b border-slate-100">
                    Sub-Tes <span class="text-blue-500">(Kemampuan Kognitif)</span>
                </h3>
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50 rounded-l-lg w-12">Kode</th>
                            <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50">Keterangan</th>
                            <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50 rounded-r-lg w-16">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labels_info as $kode => $ket): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 text-blue-700 text-xs font-extrabold">
                                    <?= $kode ?>
                                </span>
                            </td>
                            <td class="px-3 py-2.5 border-b border-slate-100 text-sm text-slate-600"><?= $ket ?></td>
                            <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-navy font-extrabold text-sm">
                                    <?= $rincian[$kode] ?? 0 ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>

<script>
<?php foreach ($users as $idx => $u):
    if ($hasUnifiedAttempts && isset($chartData[$idx])) {
        $r2 = array_combine(['SE','WA','AN','GE','RA','ZR','FA','WU','ME'], $chartData[$idx]);
    } else {
        $stmt_r = $conn->prepare("\n            SELECT \n                SUM(CASE WHEN s.urutan=1 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,\n                SUM(CASE WHEN s.urutan=2 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,\n                SUM(CASE WHEN s.urutan=3 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,\n                SUM(CASE WHEN s.urutan=4 THEN COALESCE((SELECT MAX(nilai) FROM iq_fill_answers fa WHERE fa.question_id=ua.question_id AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)),REPLACE(LOWER(fa.jawaban),', ',','))>0),0) ELSE 0 END) AS GE,\n                SUM(CASE WHEN s.urutan=5 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,\n                SUM(CASE WHEN s.urutan=6 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,\n                SUM(CASE WHEN s.urutan=7 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,\n                SUM(CASE WHEN s.urutan=8 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,\n                SUM(CASE WHEN s.urutan=9 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME\n            FROM iq_user_answers ua\n            JOIN iq_questions q ON ua.question_id=q.id\n            JOIN iq_sections s ON q.section_id=s.id\n            WHERE ua.user_nip=?\n        ");
        $stmt_r->bind_param("s", $u['nip']);
        $stmt_r->execute();
        $r2 = $stmt_r->get_result()->fetch_assoc();
    }
?>
new Chart(document.getElementById('chart_<?= $idx ?>'), {
    type: 'line',
    data: {
        labels: ['SE','WA','AN','GE','RA','ZR','FA','WU','ME'],
        datasets:[{
            data: [<?= implode(',', array_values($r2)) ?>],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.05)',
            borderWidth: 3,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#2563eb',
            pointBorderWidth: 3,
            pointRadius: 5,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { borderDash:[5,5] }, ticks: { font:{ size:10 } } },
            x: { grid: { display:false }, ticks: { font:{ weight:'bold', size:11 } } }
        }
    }
});
<?php endforeach; ?>
</script>
</body>
</html>