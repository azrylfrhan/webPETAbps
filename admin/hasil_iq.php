<?php
include '../backend/auth_check.php';
require_once '../backend/config.php';

$nip_filter = trim($_GET['nip'] ?? '');

// Ambil peserta yang dipilih atau semua peserta yang sudah selesai tes IQ
if ($nip_filter !== '') {
    $query_users = "
        SELECT u.nama, u.nip, u.satuan_kerja, r.tanggal
        FROM users u
        JOIN iq_results r ON u.nip = r.user_id
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
        JOIN iq_results r ON u.nip = r.user_id
        WHERE u.role = 'peserta'
        ORDER BY u.nama ASC
    ";
    $stmt = $conn->prepare($query_users);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
            <p class="text-slate-500 text-sm mt-1">Laporan kemampuan kognitif per sub-bagian tes.</p>
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

    <!-- Cards -->
    <div class="space-y-6" id="grid">
    <?php foreach ($users as $idx => $u):
        // Ambil rincian per subtes
        $stmt_r = $conn->prepare("
            SELECT 
                SUM(CASE WHEN s.urutan=1 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
                SUM(CASE WHEN s.urutan=2 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
                SUM(CASE WHEN s.urutan=3 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
                SUM(CASE WHEN s.urutan=4 THEN COALESCE((SELECT MAX(nilai) FROM iq_fill_answers fa WHERE fa.question_id=ua.question_id AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)),REPLACE(LOWER(fa.jawaban),', ',','))>0),0) ELSE 0 END) AS GE,
                SUM(CASE WHEN s.urutan=5 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
                SUM(CASE WHEN s.urutan=6 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
                SUM(CASE WHEN s.urutan=7 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
                SUM(CASE WHEN s.urutan=8 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
                SUM(CASE WHEN s.urutan=9 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
            FROM iq_user_answers ua
            JOIN iq_questions q ON ua.question_id=q.id
            JOIN iq_sections s ON q.section_id=s.id
            WHERE ua.user_nip=?
        ");
        $stmt_r->bind_param("s", $u['nip']);
        $stmt_r->execute();
        $rincian = $stmt_r->get_result()->fetch_assoc();
        $chart_values = array_values($rincian);
    ?>
    <div class="card" data-s="<?= strtolower($u['nama'].' '.$u['nip'].' '.($u['satuan_kerja']??'')) ?>">

        <!-- Profile banner -->
        <div class="relative bg-gradient-to-r from-[#0F1E3C] to-[#1E3260] text-white rounded-t-2xl px-6 py-5 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="absolute -right-4 -bottom-8 w-32 h-32 rounded-full bg-white/5"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-1">Laporan Hasil Evaluasi Individu</p>
            <h2 class="text-xl font-extrabold uppercase tracking-tight"><?= htmlspecialchars($u['nama']) ?></h2>
            <div class="flex items-center gap-4 mt-2 text-sm text-blue-200">
                <span>NIP: <?= $u['nip'] ?></span>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span><?= htmlspecialchars($u['satuan_kerja'] ?? '-') ?></span>
                <?php if ($u['tanggal']): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Tanggal Tes: <?= date('d F Y', strtotime($u['tanggal'])) ?></span>
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
    $stmt_r = $conn->prepare("
        SELECT 
            SUM(CASE WHEN s.urutan=1 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
            SUM(CASE WHEN s.urutan=2 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
            SUM(CASE WHEN s.urutan=3 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
            SUM(CASE WHEN s.urutan=4 THEN COALESCE((SELECT MAX(nilai) FROM iq_fill_answers fa WHERE fa.question_id=ua.question_id AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)),REPLACE(LOWER(fa.jawaban),', ',','))>0),0) ELSE 0 END) AS GE,
            SUM(CASE WHEN s.urutan=5 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
            SUM(CASE WHEN s.urutan=6 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
            SUM(CASE WHEN s.urutan=7 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
            SUM(CASE WHEN s.urutan=8 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
            SUM(CASE WHEN s.urutan=9 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
        FROM iq_user_answers ua
        JOIN iq_questions q ON ua.question_id=q.id
        JOIN iq_sections s ON q.section_id=s.id
        WHERE ua.user_nip=?
    ");
    $stmt_r->bind_param("s", $u['nip']);
    $stmt_r->execute();
    $r2 = $stmt_r->get_result()->fetch_assoc();
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