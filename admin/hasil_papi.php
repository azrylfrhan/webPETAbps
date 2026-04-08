<?php 
require_once '../backend/config.php';
include '../backend/auth_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$context = $_SESSION['admin_result_context'] ?? [];
$nip_filter = trim($_GET['nip'] ?? '');
if ($nip_filter === '' && !empty($context['target']) && $context['target'] === 'hasil_papi.php') {
    $nip_filter = trim($context['nip'] ?? '');
}

if ($nip_filter !== '') {
    $stmt = $conn->prepare("SELECT u.nama, u.satuan_kerja, h.* 
              FROM hasil_papi h 
              JOIN users u ON h.nip COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci
              WHERE u.role = 'peserta' AND u.nip = ?
              LIMIT 1");
    $stmt->bind_param('s', $nip_filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT u.nama, u.satuan_kerja, h.* 
              FROM hasil_papi h 
              JOIN users u ON h.nip COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci
              WHERE u.role = 'peserta'
              ORDER BY u.nama ASC";
    $result = mysqli_query($conn, $query);
}

$rows   = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

$roles = [
    'G'=>'Hard Intense Worked','L'=>'Leadership Role','I'=>'Ease in Decision Making',
    'T'=>'Theoretical Type','V'=>'Vigorous Type','S'=>'Social Adability',
    'R'=>'Self-Conditioning','D'=>'Interest in Details','C'=>'Organized Type','E'=>'Emotional Restraint'
];
$needs = [
    'N'=>'Need to Finish a Task','A'=>'Need to Achieve','P'=>'Need to Control Others',
    'X'=>'Need to be Noticed','B'=>'Need to Belong to Groups','O'=>'Need for Closeness',
    'Z'=>'Need for Change','K'=>'Need to be Forceful','F'=>'Need to Support Authority','W'=>'Need for Rules and Supervision'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Hasil Tes 2 Bagian 2 | Admin PETA</title>
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
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Hasil Tes 2</h1>
            <p class="text-slate-500 text-sm mt-1">Laporan evaluasi tes 2 individu bagian 2.</p>
        </div>
        <a href="hasil_peserta.php"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
            ← Kembali
        </a>
    </div>

    <?php if (empty($rows)): ?>
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-16 text-center text-slate-400">
        <div class="text-5xl mb-4">📭</div>
        <p class="text-sm">Belum ada peserta yang menyelesaikan tes.</p>
    </div>
    <?php else: ?>

    <div class="space-y-6" id="grid">
    <?php foreach ($rows as $idx => $p): ?>
    <div class="card" data-s="<?= strtolower($p['nama'].' '.$p['nip'].' '.($p['satuan_kerja']??'')) ?>">

        <!-- Profile banner -->
        <div class="relative bg-gradient-to-r from-[#0F1E3C] to-[#1E3260] text-white rounded-t-2xl px-6 py-5 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="absolute -right-4 -bottom-8 w-32 h-32 rounded-full bg-white/5"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-1">Laporan Hasil Evaluasi Individu</p>
            <h2 class="text-xl font-extrabold uppercase tracking-tight"><?= htmlspecialchars($p['nama']) ?></h2>
            <div class="flex items-center gap-4 mt-2 text-sm text-blue-200">
                <span>NIP: <?= $p['nip'] ?></span>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span><?= htmlspecialchars($p['satuan_kerja'] ?? '-') ?></span>
                <?php if (!empty($p['tanggal_tes'])): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Tanggal Tes: <?= date('d F Y', strtotime($p['tanggal_tes'])) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Body -->
        <div class="bg-white rounded-b-2xl shadow-sm border border-slate-100 border-t-0">

            <!-- Baris 1: Radar + Roles -->
            <div class="grid grid-cols-5 gap-0 border-b border-slate-100">
                <div class="col-span-2 p-6 border-r border-slate-100">
                    <h3 class="text-sm font-bold text-navy mb-4 text-center">Profil kemampuan (Radar)</h3>
                    <div style="height:220px">
                        <canvas id="radar_<?= $idx ?>"></canvas>
                    </div>
                </div>
                <div class="col-span-3 p-6">
                    <h3 class="text-sm font-bold text-navy mb-4 pb-3 border-b border-slate-100">
                        Roles <span class="text-blue-500">(Peran Kerja)</span>
                    </h3>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50 rounded-l-lg w-12">Kode</th>
                                <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50">Keterangan Dimensi</th>
                                <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-3 py-2 bg-slate-50 rounded-r-lg w-16">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $kode => $ket): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 text-blue-700 text-xs font-extrabold"><?= $kode ?></span>
                                </td>
                                <td class="px-3 py-2.5 border-b border-slate-100 text-sm text-slate-600"><?= $ket ?></td>
                                <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-navy font-extrabold text-sm"><?= $p[$kode] ?? 0 ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Baris 2: Needs (full width grid) -->
            <div class="p-6">
                <h3 class="text-sm font-bold text-navy mb-4 pb-3 border-b border-slate-100">
                    Needs <span class="text-violet-500">(Kebutuhan & Motivasi)</span>
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <?php foreach ($needs as $kode => $ket): ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-violet-200 hover:bg-violet-50/40 transition-all">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-violet-100 text-violet-700 text-xs font-extrabold flex-shrink-0"><?= $kode ?></span>
                        <p class="text-sm text-slate-600 flex-1"><?= $ket ?></p>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-navy font-extrabold text-sm flex-shrink-0"><?= $p[$kode] ?? 0 ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    </div>
    <?php endif; ?>
</div>

<script>
<?php foreach ($rows as $idx => $p):
    $all_keys = array_merge(array_keys($roles), array_keys($needs));
    $vals = array_map(fn($k) => $p[$k] ?? 0, $all_keys);
?>
new Chart(document.getElementById('radar_<?= $idx ?>'), {
    type: 'radar',
    data: {
        labels: <?= json_encode($all_keys) ?>,
        datasets:[{
            data: [<?= implode(',', $vals) ?>],
            fill: true,
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: 'rgb(37,99,235)',
            pointBackgroundColor: 'rgb(37,99,235)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(37,99,235)',
        }]
    },
    options: {
        elements: { line: { borderWidth: 2.5 } },
        scales: {
            r: {
                angleLines: { display:true, color:'#E2E8F0' },
                grid: { color:'#F1F5F9' },
                suggestedMin: 0, suggestedMax: 9,
                ticks: { display:false, stepSize:1 },
                pointLabels: { font:{ size:11, weight:'bold' }, color:'#475569' }
            }
        },
        plugins: { legend: { display:false } },
        responsive: true,
        maintainAspectRatio: false,
    }
});
<?php endforeach; ?>

</script>
</body>
</html>