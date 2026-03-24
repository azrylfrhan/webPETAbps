<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';

// PERBAIKAN: Menambahkan LEFT JOIN untuk tabel iq_results
// Kita menggunakan u.id = h3.user_id sesuai dengan struktur tabel iq_results yang Anda buat
$queryPegawai = "
SELECT 
    u.nip, 
    u.nama, 
    u.satuan_kerja,
    h1.tanggal_tes AS tgl_msdt,
    h2.tanggal_tes AS tgl_papi,
    h3.tanggal AS tgl_iq
FROM users u
LEFT JOIN hasil_msdt h1 ON u.nip = h1.nip
LEFT JOIN hasil_papi h2 ON u.nip = h2.nip
LEFT JOIN iq_results h3 ON u.nip = h3.user_id
WHERE u.role = 'peserta' AND (h1.nip IS NOT NULL OR h2.nip IS NOT NULL OR h3.user_id IS NOT NULL)
ORDER BY u.nama ASC";

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

    <div class="flex items-start justify-between mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Laporan Hasil Tes Pegawai</h1>
            <p class="text-slate-500 text-sm mt-1">Silakan pilih kategori tes untuk melihat analisis mendalam.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="export_iq.php"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm">
                📥 Excel Tes 1
            </a>
            <a href="export_msdt.php"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors shadow-sm">
                📥 Excel Tes 2 Bag. 1
            </a>
            <a href="export_papi.php"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold bg-cyan-500 hover:bg-cyan-600 text-white transition-colors shadow-sm">
                📥 Excel Tes 2 Bag. 2 
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-navy">Daftar Hasil Tes Pegawai</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-l-lg w-10">No</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Informasi Pegawai</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Unit Kerja</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-r-lg">Tes 1</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Tes 2 Bag. 1</th>
                        <th class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Tes 2 Bag. 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
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
                            <?= htmlspecialchars($row['satuan_kerja']); ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['tgl_iq']): ?>
                                <a href="hasil_iq.php?nip=<?= $row['nip']; ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 hover:bg-amber-500 text-amber-700 hover:text-white transition-all">
                                    📊 Lihat Tes 1
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['tgl_msdt']): ?>
                                <a href="hasil_msdt.php?nip=<?= $row['nip']; ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 hover:bg-blue-500 text-blue-700 hover:text-white transition-all">
                                    📊 Lihat Tes 2 Bag. 1
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-400">
                                    Belum Ada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3.5 border-b border-slate-100 text-center">
                            <?php if($row['tgl_papi']): ?>
                                <a href="hasil_papi.php?nip=<?= $row['nip']; ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-violet-100 hover:bg-violet-500 text-violet-700 hover:text-white transition-all">
                                    📊 Lihat Tes 2 Bag. 2
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
    </div>

</div>

</body>
</html>