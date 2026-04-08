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
    $checkUnifiedData = mysqli_query($conn, "SELECT 1 FROM test_attempts WHERE status='finished' LIMIT 1");
    if ($checkUnifiedData && mysqli_num_rows($checkUnifiedData) > 0) {
        $hasUnifiedAttempts = true;
    }
}

$countQueryLegacy = "
SELECT COUNT(*) AS total
FROM (
    SELECT r.user_id AS nip, r.tanggal AS tgl_tes
    FROM iq_results r
    JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = r.user_id COLLATE utf8mb4_unicode_ci
    WHERE u.role = 'peserta'

    UNION ALL

    SELECT m.nip AS nip, m.tanggal_tes AS tgl_tes
    FROM hasil_msdt m
    JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = m.nip COLLATE utf8mb4_unicode_ci
    WHERE u.role = 'peserta'

    UNION ALL

    SELECT p.nip AS nip, p.tanggal_tes AS tgl_tes
    FROM hasil_papi p
    JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = p.nip COLLATE utf8mb4_unicode_ci
    WHERE u.role = 'peserta'
) x
WHERE 1=1
$filterTanggalLegacy";

$countQuery = '';
if ($hasUnifiedAttempts) {
    $countQuery = "
    SELECT COUNT(*) AS total
    FROM test_attempts ta
    JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci
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
    JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci
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
        JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = r.user_id COLLATE utf8mb4_unicode_ci
        WHERE u.role = 'peserta'

        UNION ALL

        SELECT m.nip AS nip, u.nama, u.satuan_kerja, 'msdt' AS test_type, m.tanggal_tes AS tgl_tes
        FROM hasil_msdt m
        JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = m.nip COLLATE utf8mb4_unicode_ci
        WHERE u.role = 'peserta'

        UNION ALL

        SELECT p.nip AS nip, u.nama, u.satuan_kerja, 'papi' AS test_type, p.tanggal_tes AS tgl_tes
        FROM hasil_papi p
        JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = p.nip COLLATE utf8mb4_unicode_ci
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
    <link rel="icon" type="image/png" href="/images/logobps.png">
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
        #notification-modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center; }
        #notification-modal.show { display: flex; }
        .notification-box { background: white; border-radius: 16px; padding: 32px; max-width: 480px; width: 90%; box-shadow: 0 20px 50px rgba(15,30,60,0.3); animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .notification-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 16px; }
        .notification-icon.success { background: #dcfce7; color: #16a34a; }
        .notification-icon.error { background: #fee2e2; color: #dc2626; }
        .notification-icon.info { background: #dbeafe; color: #2563eb; }
        .notification-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 12px 0 8px; text-align: center; }
        .notification-message { font-size: 14px; color: #64748b; text-align: center; margin-bottom: 24px; line-height: 1.5; white-space: pre-wrap; }
        .notification-buttons { display: flex; gap: 12px; justify-content: center; }
        .notification-btn { padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .notification-btn.primary { background: #0f1e3c; color: white; }
        .notification-btn.primary:hover { opacity: 0.9; }
        .notification-btn.secondary { background: #e2e8f0; color: #64748b; }
        .notification-btn.secondary:hover { background: #cbd5e1; }
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
                            <a href="detail_pegawai.php?nip=<?= urlencode($row['nip']); ?>" class="inline-flex mt-2 items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-md transition-colors">
                                Lihat Detail Pegawai
                            </a>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-sm text-slate-500">
                            <p class="font-semibold text-slate-700 text-xs">Unit: <?= htmlspecialchars($row['satuan_kerja']); ?></p>
                            <p class="text-xs text-slate-500 mt-1">Tanggal Tes: <?= date('d/m/Y H:i', strtotime($row['tgl_tes'])); ?></p>
                            <p class="text-xs text-slate-500 mt-1">Percobaan #<?= (int)$row['attempt_number']; ?></p>
                            <p class="text-xs text-slate-500 mt-1">Alasan: <?= !empty($row['alasan_tes']) ? htmlspecialchars($row['alasan_tes']) : '-'; ?></p>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'iq'): ?>
                                <form method="POST" action="set_hasil_iq_context.php" class="inline">
                                    <input type="hidden" name="attempt_id" value="<?= (int)$row['attempt_id']; ?>">
                                    <input type="hidden" name="nip" value="<?= htmlspecialchars($row['nip']); ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 hover:bg-amber-500 text-amber-700 hover:text-white transition-all">
                                        📊 Lihat Jawaban & Skor
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'msdt'): ?>
                                <form method="POST" action="set_result_context.php" class="inline">
                                    <input type="hidden" name="target" value="hasil_msdt.php">
                                    <input type="hidden" name="nip" value="<?= htmlspecialchars($row['nip']); ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 hover:bg-blue-500 text-blue-700 hover:text-white transition-all">
                                        📊 Lihat Jawaban & Skor
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['test_type'] === 'papi'): ?>
                                <form method="POST" action="set_result_context.php" class="inline">
                                    <input type="hidden" name="target" value="hasil_papi.php">
                                    <input type="hidden" name="nip" value="<?= htmlspecialchars($row['nip']); ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-violet-100 hover:bg-violet-500 text-violet-700 hover:text-white transition-all">
                                        📊 Lihat Jawaban & Skor
                                    </button>
                                </form>
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
        btn.addEventListener('click', async function(e) {
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
                await showNotification('Format Tanggal Invalid', 'Isi kedua tanggal untuk filter, atau kosongkan keduanya untuk semua data.', 'error');
                return;
            }

            if (tglMulai && tglAkhir) {
                exportUrl += '?tgl_mulai=' + encodeURIComponent(tglMulai) + '&tgl_akhir=' + encodeURIComponent(tglAkhir);
            }
            
            window.location.href = exportUrl;
        });
    });

    // Notification Modal Function
    function showNotification(title, message, type = 'info', isConfirm = false) {
        return new Promise((resolve) => {
            const modal = document.getElementById('notification-modal');
            if (!modal) {
                console.error('Notification modal not found');
                resolve(false);
                return;
            }

            document.getElementById('notification-title').textContent = title;
            document.getElementById('notification-message').textContent = message;

            const iconEl = document.getElementById('notification-icon');
            const iconMap = { success: '✓', error: '✕', info: 'ℹ' };
            iconEl.textContent = iconMap[type] || '✓';
            const bgColorMap = { success: '#dcfce7', error: '#fee2e2', info: '#dbeafe' };
            const textColorMap = { success: '#16a34a', error: '#dc2626', info: '#2563eb' };
            iconEl.style.background = bgColorMap[type] || '#dbeafe';
            iconEl.style.color = textColorMap[type] || '#2563eb';

            const yesBtn = document.getElementById('notification-yes');
            const noBtn = document.getElementById('notification-no');
            const okBtn = document.getElementById('notification-ok');

            if (isConfirm) {
                okBtn.style.display = 'none';
                yesBtn.style.display = 'inline-block';
                noBtn.style.display = 'inline-block';
            } else {
                okBtn.style.display = 'inline-block';
                yesBtn.style.display = 'none';
                noBtn.style.display = 'none';
            }

            const newYesBtn = yesBtn.cloneNode(true);
            const newNoBtn = noBtn.cloneNode(true);
            const newOkBtn = okBtn.cloneNode(true);

            yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
            noBtn.parentNode.replaceChild(newNoBtn, noBtn);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);

            newYesBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                resolve(true);
            });
            newNoBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                resolve(false);
            });
            newOkBtn.addEventListener('click', () => {
                modal.style.display = 'none';
                resolve(true);
            });

            modal.style.display = 'flex';
        });
    }

</script>

<!-- Notification Modal -->
<div id="notification-modal">
    <div class="notification-box">
        <div id="notification-icon" class="notification-icon" style="background: #dbeafe; color: #2563eb;">ℹ</div>
        <h2 id="notification-title" class="notification-title">Judul</h2>
        <p id="notification-message" class="notification-message">Pesan</p>
        <div class="notification-buttons">
            <button id="notification-ok" type="button" class="notification-btn primary">OK</button>
            <button id="notification-yes" type="button" class="notification-btn primary" style="display: none;">Ya</button>
            <button id="notification-no" type="button" class="notification-btn secondary" style="display: none;">Tidak</button>
        </div>
    </div>
</div>

</body>
</html>