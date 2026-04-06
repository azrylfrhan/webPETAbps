<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';

$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$filterTanggal = '';
$filterTanggalLegacy = '';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $tgl_mulai_safe = mysqli_real_escape_string($conn, $tgl_mulai);
    $tgl_akhir_safe = mysqli_real_escape_string($conn, $tgl_akhir);
    $filterTanggal = " AND DATE(ta.tanggal_mulai) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
    $filterTanggalLegacy = " AND DATE(tgl_tes) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
}

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$hasUnifiedAttempts = false;
$checkUnified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($checkUnified && mysqli_num_rows($checkUnified) > 0) {
    $hasUnifiedAttempts = true;
}

$countQueryLegacy = "
SELECT COUNT(*) AS total
FROM (
    SELECT r.user_id AS nip, r.tanggal AS tgl_tes
    FROM iq_results r
    JOIN users u ON u.nip = r.user_id
    WHERE u.role = 'peserta'

    UNION ALL

    SELECT m.nip AS nip, m.tanggal_tes AS tgl_tes
    FROM hasil_msdt m
    JOIN users u ON u.nip = m.nip
    WHERE u.role = 'peserta'

    UNION ALL

    SELECT p.nip AS nip, p.tanggal_tes AS tgl_tes
    FROM hasil_papi p
    JOIN users u ON u.nip = p.nip
    WHERE u.role = 'peserta'
) x
WHERE 1=1
$filterTanggalLegacy";

$countQuery = '';
if ($hasUnifiedAttempts) {
    $countQuery = "
    SELECT COUNT(*) AS total
    FROM test_attempts ta
    JOIN users u ON u.nip = ta.nip
    WHERE u.role = 'peserta'
        AND ta.status = 'finished'
        $filterTanggal";
} else {
    $countQuery = $countQueryLegacy;
}

try {
    $countResult = mysqli_query($conn, $countQuery);
} catch (mysqli_sql_exception $e) {
    if (stripos($e->getMessage(), "test_attempts") !== false && stripos($e->getMessage(), "doesn't exist") !== false) {
        $hasUnifiedAttempts = false;
        $countQuery = $countQueryLegacy;
        $countResult = mysqli_query($conn, $countQuery);
    } else {
        throw $e;
    }
}
if (!$countResult) {
    die("Query Error: " . mysqli_error($conn));
}

$totalRows = (int)(mysqli_fetch_assoc($countResult)['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalRows / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// Gunakan tabel unified test_attempts agar setiap percobaan tes muncul sebagai baris baru.
$queryPegawai = '';
if ($hasUnifiedAttempts) {
    $queryPegawai = "
    SELECT 
        ta.id AS attempt_id,
        ta.nip,
        u.nama,
        u.satuan_kerja,
        ta.test_type,
        ta.attempt_number,
        ta.tanggal_mulai AS tgl_tes,
        ta.alasan_tes
    FROM test_attempts ta
    JOIN users u ON u.nip = ta.nip
    WHERE u.role = 'peserta'
        AND ta.status = 'finished'
        $filterTanggal
    ORDER BY ta.tanggal_mulai DESC, ta.id DESC
    LIMIT $perPage OFFSET $offset";
} else {
    $queryPegawai = "
    SELECT 
        0 AS attempt_id,
        x.nip,
        x.nama,
        x.satuan_kerja,
        x.test_type,
        1 AS attempt_number,
        x.tgl_tes,
        NULL AS alasan_tes
    FROM (
        SELECT r.user_id AS nip, u.nama, u.satuan_kerja, 'iq' AS test_type, r.tanggal AS tgl_tes
        FROM iq_results r
        JOIN users u ON u.nip = r.user_id
        WHERE u.role = 'peserta'

        UNION ALL

        SELECT m.nip AS nip, u.nama, u.satuan_kerja, 'msdt' AS test_type, m.tanggal_tes AS tgl_tes
        FROM hasil_msdt m
        JOIN users u ON u.nip = m.nip
        WHERE u.role = 'peserta'

        UNION ALL

        SELECT p.nip AS nip, u.nama, u.satuan_kerja, 'papi' AS test_type, p.tanggal_tes AS tgl_tes
        FROM hasil_papi p
        JOIN users u ON u.nip = p.nip
        WHERE u.role = 'peserta'
    ) x
    WHERE 1=1
    $filterTanggalLegacy
    ORDER BY x.tgl_tes DESC
    LIMIT $perPage OFFSET $offset";
}

// 2. Eksekusi kueri ke variabel $resultPegawai
$resultPegawai = mysqli_query($conn, $queryPegawai);

// 3. PERBAIKAN: Menggunakan variabel $resultPegawai (bukan $result) untuk cek error
if (!$resultPegawai) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Tes Pegawai | Admin BPS</title>
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

<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <div class="mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Laporan Hasil Tes Pegawai</h1>
            <p class="text-slate-500 text-sm mt-1">Filter dan export data tes berdasarkan tanggal.</p>
        </div>
    </div>

    <!-- FILTER & EXPORT SECTION -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="mb-4">
            <h3 class="text-sm font-bold text-navy mb-3">📊 Filter & Export Data</h3>
        </div>

        <form id="filterForm" class="space-y-4" method="GET" action="">
            <div class="flex items-end gap-4 flex-wrap">
                <div class="flex-1 min-w-48">
                    <label class="text-xs font-semibold text-slate-600 block mb-2">Dari Tanggal</label>
                    <input type="date" id="tglMulai" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai); ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy">
                </div>
                <div class="flex-1 min-w-48">
                    <label class="text-xs font-semibold text-slate-600 block mb-2">Sampai Tanggal</label>
                    <input type="date" id="tglAkhir" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir); ?>" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-800 text-white transition-colors shadow-sm">
                    Terapkan Filter
                </button>
            </div>

            <?php if (!empty($tgl_mulai) && !empty($tgl_akhir)): ?>
                <p class="text-xs text-slate-500">Filter aktif: <?= date('d/m/Y', strtotime($tgl_mulai)); ?> - <?= date('d/m/Y', strtotime($tgl_akhir)); ?></p>
            <?php else: ?>
                <p class="text-xs text-slate-500">Filter kosong, menampilkan semua data hasil tes.</p>
            <?php endif; ?>

            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" class="exportBtn inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm" data-type="iq">
                    📥 Export Tes 1
                </button>
                <button type="button" class="exportBtn inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors shadow-sm" data-type="msdt">
                    📥 Export Tes 2 Bag. 1
                </button>
                <button type="button" class="exportBtn inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-cyan-500 hover:bg-cyan-600 text-white transition-colors shadow-sm" data-type="papi">
                    📥 Export Tes 2 Bag. 2
                </button>
                <button type="button" class="exportBtn inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition-colors shadow-sm" data-type="kombinasi">
                    📥 Export Kombinasi
                </button>
            </div>

        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-navy">Daftar Hasil Tes Pegawai (Urut Tes Terbaru)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-l-lg w-10">No</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Informasi Pegawai</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Detail Percobaan</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-r-lg">Tes 1</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Tes 2 Bag. 1</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Tes 2 Bag. 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $offset + 1;
                    while($row = mysqli_fetch_assoc($resultPegawai)): 
                    ?>
                    <tr class="hover:bg-blue-50/40 transition-colors">

                        <td class="px-4 py-3.5 border-b border-slate-100 text-sm text-slate-400 font-medium">
                            <?= $no++; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($row['nama']); ?></p>
                            <p class="text-xs text-slate-400 mt-0.5">NIP: <?= $row['nip']; ?></p>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-sm text-slate-500">
                            <p class="font-semibold text-slate-700 text-xs">Unit: <?= htmlspecialchars($row['satuan_kerja']); ?></p>
                            <p class="text-xs text-slate-500 mt-1">Tanggal Tes: <?= date('d/m/Y H:i', strtotime($row['tgl_tes'])); ?></p>
                            <p class="text-xs text-slate-500 mt-1">Percobaan #<?= (int)$row['attempt_number']; ?></p>
                            <p class="text-xs text-slate-500 mt-1">Alasan: <?= !empty($row['alasan_tes']) ? htmlspecialchars($row['alasan_tes']) : '-'; ?></p>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'iq'): ?>
                                <a href="hasil_iq.php?nip=<?= urlencode($row['nip']); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 hover:bg-amber-500 text-amber-700 hover:text-white transition-all">
                                    📊 Lihat Jawaban & Skor
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'msdt'): ?>
                                <a href="hasil_msdt.php?nip=<?= urlencode($row['nip']); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 hover:bg-blue-500 text-blue-700 hover:text-white transition-all">
                                    📊 Lihat Jawaban & Skor
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'papi'): ?>
                                <a href="hasil_papi.php?nip=<?= urlencode($row['nip']); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-violet-100 hover:bg-violet-500 text-violet-700 hover:text-white transition-all">
                                    📊 Lihat Jawaban & Skor
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php
            $baseParams = [];
            if (!empty($tgl_mulai)) {
                $baseParams['tgl_mulai'] = $tgl_mulai;
            }
            if (!empty($tgl_akhir)) {
                $baseParams['tgl_akhir'] = $tgl_akhir;
            }
        ?>
        <div class="mt-5 flex items-center justify-between flex-wrap gap-3">
            <p class="text-xs text-slate-500">
                Menampilkan <?= $totalRows > 0 ? ($offset + 1) : 0; ?> - <?= min($offset + $perPage, $totalRows); ?> dari <?= $totalRows; ?> data
            </p>
            <div class="flex items-center gap-2">
                <?php
                    $prevDisabled = $page <= 1;
                    $nextDisabled = $page >= $totalPages;
                    $prevParams = array_merge($baseParams, ['page' => max(1, $page - 1)]);
                    $nextParams = array_merge($baseParams, ['page' => min($totalPages, $page + 1)]);
                ?>
                <?php if ($prevDisabled): ?>
                    <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400">Sebelumnya</span>
                <?php else: ?>
                    <a href="?<?= htmlspecialchars(http_build_query($prevParams)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 transition-colors">Sebelumnya</a>
                <?php endif; ?>

                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-navy text-white">
                    Halaman <?= $page; ?> / <?= $totalPages; ?>
                </span>

                <?php if ($nextDisabled): ?>
                    <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400">Berikutnya</span>
                <?php else: ?>
                    <a href="?<?= htmlspecialchars(http_build_query($nextParams)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 transition-colors">Berikutnya</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
    const exportBtns = document.querySelectorAll('.exportBtn');
    
    exportBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tglMulai = document.getElementById('tglMulai').value;
            const tglAkhir = document.getElementById('tglAkhir').value;
            const tipeExport = this.getAttribute('data-type');
            
            let exportUrl = '';
            if (tipeExport === 'iq') {
                exportUrl = 'export_iq.php';
            } else if (tipeExport === 'msdt') {
                exportUrl = 'export_msdt.php';
            } else if (tipeExport === 'papi') {
                exportUrl = 'export_papi.php';
            } else if (tipeExport === 'kombinasi') {
                exportUrl = 'export_kombinasi.php';
            }

            if ((tglMulai && !tglAkhir) || (!tglMulai && tglAkhir)) {
                alert('Isi kedua tanggal untuk filter, atau kosongkan keduanya untuk semua data.');
                return;
            }

            if (tglMulai && tglAkhir) {
                exportUrl += '?tgl_mulai=' + encodeURIComponent(tglMulai) + '&tgl_akhir=' + encodeURIComponent(tglAkhir);
            }
            
            window.location.href = exportUrl;
        });
    });
</script>

</body>
</html>