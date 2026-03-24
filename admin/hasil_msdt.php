<?php include '../backend/auth_check.php'; ?>
<?php require_once '../backend/config.php';

$query = "SELECT u.nama, u.satuan_kerja, u.jabatan,
                 h.nip, h.to_score, h.ro_score, h.e_score, h.o_score,
                 h.Ds, h.Mi, h.Au, h.Co, h.Bu, h.Dv, h.Ba, h.E_dim,
                 h.dominant_model, h.tanggal_tes
          FROM hasil_msdt h
          JOIN users u ON h.nip = u.nip
          WHERE u.role = 'peserta'
          ORDER BY u.nama ASC";

$result = mysqli_query($conn, $query);
$rows   = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

// Keterangan dimensi sub-MSDT
$sub_dims = [
    'Ds' => 'Directing Style',
    'Mi' => 'Motivating Style',
    'Au' => 'Autonomy Style',
    'Co' => 'Coaching Style',
    'Bu' => 'Bureaucratic Style',
    'Dv' => 'Developing Style',
    'Ba' => 'Balancing Style',
    'E_dim' => 'Empowering Style',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Tes 2 Bagian 1 | Admin PETA</title>
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
    <div class="flex items-start justify-between mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Hasil Tes 2</h1>
            <p class="text-slate-500 text-sm mt-1">Laporan evaluasi tes 2 individu bagian 1.</p>
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
    <div class="space-y-6">
    <?php foreach ($rows as $idx => $p):
        $dims_main = [
            ['TO', $p['to_score'], '#3b82f6', '#eff6ff', '#1d4ed8'],
            ['RO', $p['ro_score'], '#8b5cf6', '#f5f3ff', '#5b21b6'],
            ['E',  $p['e_score'],  '#10b981', '#ecfdf5', '#047857'],
            ['O',  $p['o_score'],  '#f59e0b', '#fffbeb', '#92400e'],
        ];
        $max = 64;
    ?>
    <div class="bg-white rounded-b-2xl shadow-sm border border-slate-100 overflow-hidden">

        <!-- Profile banner -->
        <div class="relative bg-gradient-to-r from-[#0F1E3C] to-[#1E3260] text-white px-6 py-5 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="absolute -right-4 -bottom-8 w-32 h-32 rounded-full bg-white/5"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-1">Laporan Hasil Evaluasi Individu</p>
            <h2 class="text-xl font-extrabold uppercase tracking-tight"><?= htmlspecialchars($p['nama']) ?></h2>
            <div class="flex items-center gap-4 mt-2 text-sm text-blue-200 flex-wrap">
                <span>NIP: <?= $p['nip'] ?></span>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span><?= htmlspecialchars($p['satuan_kerja'] ?? '-') ?></span>
                <?php if (!empty($p['tanggal_tes'])): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Tanggal Tes: <?= date('d F Y', strtotime($p['tanggal_tes'])) ?></span>
                <?php endif; ?>
                <?php if (!empty($p['dominant_model'])): ?>
                <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                <span>Model Dominan: <strong class="text-white"><?= htmlspecialchars($p['dominant_model']) ?></strong></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Baris 1: Radar + Dimensi Utama -->
        <div class="grid grid-cols-5 gap-0 border-b border-slate-100">

            <!-- Radar -->
            <div class="col-span-2 p-6 border-r border-slate-100">
                <h3 class="text-sm font-bold text-navy mb-4 text-center">Profil kemampuan (TO/RO/E/O)</h3>
                <div style="height:220px">
                    <canvas id="radar_<?= $idx ?>"></canvas>
                </div>
            </div>

            <!-- 4 Dimensi Utama -->
            <div class="col-span-3 p-6">
                <h3 class="text-sm font-bold text-navy mb-4 pb-3 border-b border-slate-100">
                    Dimensi Utama <span class="text-blue-500">(TO, RO, E, O)</span>
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
                        <?php
                        $main_labels = [
                            'TO' => 'Task Orientation',
                            'RO' => 'Relationship Orientation',
                            'E'  => 'Extroversion',
                            'O'  => 'Openness (Ds)',
                        ];
                        $main_scores = ['TO'=>$p['to_score'],'RO'=>$p['ro_score'],'E'=>$p['e_score'],'O'=>$p['o_score']];
                        $main_colors = ['TO'=>'bg-blue-100 text-blue-700','RO'=>'bg-violet-100 text-violet-700','E'=>'bg-emerald-100 text-emerald-700','O'=>'bg-amber-100 text-amber-700'];
                        foreach ($main_labels as $kode => $ket): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg <?= $main_colors[$kode] ?> text-xs font-extrabold"><?= $kode ?></span>
                            </td>
                            <td class="px-3 py-2.5 border-b border-slate-100 text-sm text-slate-600"><?= $ket ?></td>
                            <td class="px-3 py-2.5 border-b border-slate-100 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-navy font-extrabold text-sm"><?= $main_scores[$kode] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Baris 2: Sub Dimensi (Ds, Mi, Au, Co, Bu, Dv, Ba, E_dim) -->
        <div class="p-6">
            <h3 class="text-sm font-bold text-navy mb-4 pb-3 border-b border-slate-100">
                Sub Dimensi <span class="text-violet-500">(Gaya Kepemimpinan)</span>
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <?php foreach ($sub_dims as $kode => $ket): ?>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-violet-200 hover:bg-violet-50/40 transition-all">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-violet-100 text-violet-700 text-xs font-extrabold flex-shrink-0"><?= $kode === 'E_dim' ? 'E' : $kode ?></span>
                    <p class="text-sm text-slate-600 flex-1"><?= $ket ?></p>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-navy font-extrabold text-sm flex-shrink-0"><?= $p[$kode] ?? 0 ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
<?php foreach ($rows as $idx => $p): ?>
new Chart(document.getElementById('radar_<?= $idx ?>'), {
    type: 'radar',
    data: {
        labels: ['TO','RO','E','O'],
        datasets:[{
            data: [<?= $p['to_score'] ?>,<?= $p['ro_score'] ?>,<?= $p['e_score'] ?>,<?= $p['o_score'] ?>],
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
                angleLines: { display: true, color: '#E2E8F0' },
                grid: { color: '#F1F5F9' },
                suggestedMin: 0, suggestedMax: 50,
                ticks: { display: false, stepSize: 10 },
                pointLabels: { font: { size: 11, weight: 'bold' }, color: '#475569' }
            }
        },
        plugins: { legend: { display: false } },
        responsive: true,
        maintainAspectRatio: false,
    }
});
<?php endforeach; ?>
</script>
</body>
</html>