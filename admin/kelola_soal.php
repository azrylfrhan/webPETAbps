<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';

// Ambil semua soal dari database
$query = "SELECT * FROM soal ORDER BY kode_tes, nomor_soal ASC";
$result = mysqli_query($conn, $query);

// Kelompokkan soal berdasarkan jenisnya
$soals = ['KEPRIBADIAN' => [], 'KEPRIBADIAN2' => [], 'IQ' => []];
while($row = mysqli_fetch_assoc($result)) {
    $soals[$row['kode_tes']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Soal | Admin BPS</title>

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

        /* Tab indicator animasi */
        .tab-btn { position: relative; transition: all 0.2s; }
        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 2px;
            background: transparent;
            border-radius: 2px;
            transition: background 0.2s;
        }
        .tab-btn.active::after { background: #2563EB; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Manajemen Bank Soal</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola soal untuk semua jenis tes psikologi.</p>
        </div>
        <a href="tambah_soal.php"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md shadow-blue-200 hover:shadow-blue-300 hover:-translate-y-0.5 transition-all">
            + Tambah Soal Baru
        </a>
    </div>

        <!-- Search Nomor Soal -->
        <div class="mb-5">
            <div class="relative max-w-sm">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">#</span>
                <input id="search-no" type="text" inputmode="numeric" placeholder="Cari nomor soal, contoh: 12"
                    class="w-full pl-8 pr-8 py-2.5 text-sm rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
                <button type="button" id="clear-no" onclick="clearNoSearch()"
                    class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 text-sm">✕</button>
            </div>
            <p class="text-xs text-slate-400 mt-2">Pencarian berlaku untuk tab yang sedang aktif.</p>
        </div>

    <!-- Tabs -->
    <div class="flex items-center gap-1 border-b-2 border-slate-200 mb-6">
        <button class="tab-btn active px-5 py-2.5 text-sm font-semibold text-blue-600 rounded-t-lg"
                onclick="openTab(event, 'msdt')">
            📝 Bagian 1 (MSDT)
            <span class="ml-2 text-xs font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">
                <?= count($soals['KEPRIBADIAN']) ?>
            </span>
        </button>
        <button class="tab-btn px-5 py-2.5 text-sm font-semibold text-slate-400 hover:text-slate-600 rounded-t-lg"
                onclick="openTab(event, 'papi')">
            📝 Bagian 2 (PAPI)
            <span class="ml-2 text-xs font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                <?= count($soals['KEPRIBADIAN2']) ?>
            </span>
        </button>
        <button class="tab-btn px-5 py-2.5 text-sm font-semibold text-slate-400 hover:text-slate-600 rounded-t-lg"
                onclick="openTab(event, 'iq')">
            🧠 Tes IQ
            <span class="ml-2 text-xs font-bold bg-amber-50 text-amber-500 px-2 py-0.5 rounded-full">
                <?= count($soals['IQ']) ?>
            </span>
        </button>
    </div>

    <!-- Tab: MSDT -->
    <div id="msdt" class="tab-content active">
        <?php if(empty($soals['KEPRIBADIAN'])): ?>
            <div class="text-center py-16 text-slate-400">
                <div class="text-5xl mb-3">📭</div>
                <p class="text-sm">Belum ada soal Bagian 1 (MSDT).</p>
            </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach($soals['KEPRIBADIAN'] as $s): ?>
            <div class="question-card bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-no="<?= (int)$s['nomor_soal'] ?>">
                <!-- Card Header -->
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            <?= $s['nomor_soal'] ?>
                        </span>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">MSDT</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="edit_soal.php?id=<?= $s['id'] ?>"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white transition-all">
                            ✏️ Edit
                        </a>
                        <a href="hapus_soal.php?id=<?= $s['id'] ?>"
                           onclick="return confirm('Hapus soal ini?')"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition-all">
                            🗑️ Hapus
                        </a>
                    </div>
                </div>
                <!-- Pilihan -->
                <div class="grid grid-cols-2 gap-4 p-5">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Pilihan A</span>
                        <p class="text-sm text-slate-700"><?= htmlspecialchars($s['pertanyaan_a']) ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Pilihan B</span>
                        <p class="text-sm text-slate-700"><?= htmlspecialchars($s['pertanyaan_b']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="search-empty hidden text-center py-12 text-slate-400 text-sm">Nomor soal tidak ditemukan di tab ini.</div>
        <?php endif; ?>
    </div>

    <!-- Tab: PAPI -->
    <div id="papi" class="tab-content">
        <?php if(empty($soals['KEPRIBADIAN2'])): ?>
            <div class="text-center py-16 text-slate-400">
                <div class="text-5xl mb-3">📭</div>
                <p class="text-sm">Belum ada soal Bagian 2 (PAPI).</p>
            </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach($soals['KEPRIBADIAN2'] as $s): ?>
            <div class="question-card bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-no="<?= (int)$s['nomor_soal'] ?>">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            <?= $s['nomor_soal'] ?>
                        </span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">PAPI</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="edit_soal.php?id=<?= $s['id'] ?>"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white transition-all">
                            ✏️ Edit
                        </a>
                        <a href="hapus_soal.php?id=<?= $s['id'] ?>"
                           onclick="return confirm('Hapus soal ini?')"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition-all">
                            🗑️ Hapus
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 p-5">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Pilihan A</span>
                        <p class="text-sm text-slate-700"><?= htmlspecialchars($s['pertanyaan_a']) ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Pilihan B</span>
                        <p class="text-sm text-slate-700"><?= htmlspecialchars($s['pertanyaan_b']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="search-empty hidden text-center py-12 text-slate-400 text-sm">Nomor soal tidak ditemukan di tab ini.</div>
        <?php endif; ?>
    </div>

    <!-- Tab: IQ -->
    <div id="iq" class="tab-content">
        <?php if(empty($soals['IQ'])): ?>
            <div class="text-center py-16 text-slate-400">
                <div class="text-5xl mb-3">🧠</div>
                <p class="text-sm font-medium">Belum ada soal Tes IQ.</p>
                <a href="tambah_soal.php?kode=IQ"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors">
                    + Tambah Soal IQ
                </a>
            </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach($soals['IQ'] as $s): ?>
            <div class="question-card bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-no="<?= (int)$s['nomor_soal'] ?>">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-amber-50">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            <?= $s['nomor_soal'] ?>
                        </span>
                        <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2.5 py-1 rounded-full">IQ</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="edit_soal.php?id=<?= $s['id'] ?>"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white transition-all">
                            ✏️ Edit
                        </a>
                        <a href="hapus_soal.php?id=<?= $s['id'] ?>"
                           onclick="return confirm('Hapus soal ini?')"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition-all">
                            🗑️ Hapus
                        </a>
                    </div>
                </div>
                <div class="p-5">
                    <p class="text-sm font-medium text-slate-700 mb-4"><?= htmlspecialchars($s['pertanyaan_a']) ?></p>
                    <div class="grid grid-cols-2 gap-3">
                        <?php
                        $opsi = ['pertanyaan_a'=>'A','pertanyaan_b'=>'B','pertanyaan_c'=>'C','pertanyaan_d'=>'D'];
                        foreach($opsi as $col => $label):
                            if(!empty($s[$col])):
                        ?>
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Pilihan <?= $label ?></span>
                            <p class="text-sm text-slate-700"><?= htmlspecialchars($s[$col]) ?></p>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="search-empty hidden text-center py-12 text-slate-400 text-sm">Nomor soal tidak ditemukan di tab ini.</div>
        <?php endif; ?>
    </div>

</div>

<script>
    function applyNoSearch() {
        const input = document.getElementById('search-no');
        const kw = input.value.trim();
        document.getElementById('clear-no').classList.toggle('hidden', kw === '');

        document.querySelectorAll('.tab-content').forEach(tab => {
            const isActive = tab.classList.contains('active');
            const cards = tab.querySelectorAll('.question-card');
            if (!cards.length) return;

            let visible = 0;
            cards.forEach(card => {
                const no = card.dataset.no || '';
                const match = (kw === '') || no.includes(kw);
                card.classList.toggle('hidden', !match || !isActive);
                if (match && isActive) visible++;
            });

            const empty = tab.querySelector('.search-empty');
            if (empty) {
                empty.classList.toggle('hidden', kw === '' || visible > 0 || !isActive);
            }
        });
    }

    function clearNoSearch() {
        document.getElementById('search-no').value = '';
        applyNoSearch();
        document.getElementById('search-no').focus();
    }

    document.getElementById('search-no').addEventListener('input', applyNoSearch);

    function openTab(evt, tabName) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('active');
            el.style.display = '';
        });

        // Reset semua tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'text-blue-600');
            btn.classList.add('text-slate-400');
        });

        // Tampilkan tab yang dipilih
        document.getElementById(tabName).classList.add('active');

        // Aktifkan button yang diklik
        evt.currentTarget.classList.add('active', 'text-blue-600');
        evt.currentTarget.classList.remove('text-slate-400');

        applyNoSearch();
    }

    applyNoSearch();
</script>

</body>
</html>