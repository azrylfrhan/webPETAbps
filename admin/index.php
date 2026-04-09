<?php 
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Ringkasan dashboard
$count_pegawai = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'peserta'"))['total'] ?? 0);

$has_unified_attempts = false;
$check_unified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($check_unified && mysqli_num_rows($check_unified) > 0) {
    $check_unified_data = mysqli_query($conn, "SELECT 1 FROM test_attempts WHERE status='finished' LIMIT 1");
    if ($check_unified_data && mysqli_num_rows($check_unified_data) > 0) {
        $has_unified_attempts = true;
    }
}

if ($has_unified_attempts) {
    $count_iq = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM test_attempts WHERE test_type='iq' AND status='finished'"))['total'] ?? 0);
    $count_msdt = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM test_attempts WHERE test_type='msdt' AND status='finished'"))['total'] ?? 0);
    $count_papi = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM test_attempts WHERE test_type='papi' AND status='finished'"))['total'] ?? 0);

    $last_activity = mysqli_query($conn, "
        SELECT u.nama, UPPER(ta.test_type) as jenis, ta.tanggal_mulai AS tanggal_tes, ta.attempt_number
        FROM test_attempts ta
        JOIN users u ON u.nip COLLATE utf8mb4_unicode_ci = ta.nip COLLATE utf8mb4_unicode_ci
        WHERE ta.status='finished' AND u.role='peserta'
        ORDER BY ta.tanggal_mulai DESC, ta.id DESC
        LIMIT 10
    ");
} else {
    $count_msdt = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_msdt"))['total'] ?? 0);
    $count_papi = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_papi"))['total'] ?? 0);
    $count_iq = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM iq_results"))['total'] ?? 0);

    $last_activity = mysqli_query($conn, "
        SELECT u.nama, 'MSDT' as jenis, h.tanggal_tes, 1 AS attempt_number FROM hasil_msdt h JOIN users u ON h.nip COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci
        UNION ALL
        SELECT u.nama, 'PAPI' as jenis, h.tanggal_tes, 1 AS attempt_number FROM hasil_papi h JOIN users u ON h.nip COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci
        UNION ALL
        SELECT u.nama, 'IQ' as jenis, h.tanggal AS tanggal_tes, 1 AS attempt_number FROM iq_results h JOIN users u ON h.user_id COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci
        ORDER BY tanggal_tes DESC LIMIT 10
    ");
}

$total_hasil = $count_iq + $count_msdt + $count_papi;
$coverage = $count_pegawai > 0 ? round(($total_hasil / ($count_pegawai * 3)) * 100) : 0;
$coverage = max(0, min(100, $coverage));
$last_activity_rows = [];
if ($last_activity) {
    while ($row = mysqli_fetch_assoc($last_activity)) {
        $last_activity_rows[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Admin Dashboard - BPS Psikotes</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#0F1E3C',
                            mid:     '#162441',
                            light:   '#1E3260',
                        },
                        brand: {
                            DEFAULT: '#2563EB',
                            light:   '#3B82F6',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --ink: #0f172a;
            --sky: #0ea5e9;
            --mint: #10b981;
            --sun: #f59e0b;
            --rose: #ef4444;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .dashboard-bg {
            background:
                radial-gradient(1200px 400px at 100% -10%, rgba(14,165,233,.15), transparent 60%),
                radial-gradient(800px 340px at -10% 10%, rgba(16,185,129,.10), transparent 60%),
                #f1f5f9;
        }
        .pulse-glow {
            animation: pulseGlow 2.8s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%,100% { box-shadow: 0 0 0 0 rgba(14,165,233,.22); }
            50% { box-shadow: 0 0 0 10px rgba(14,165,233,0); }
        }
        .reveal {
            animation: revealUp .55s ease both;
        }
        @keyframes revealUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal:nth-child(2){ animation-delay: .06s; }
        .reveal:nth-child(3){ animation-delay: .12s; }
        .reveal:nth-child(4){ animation-delay: .18s; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 999px; }
    </style>
</head>

<body class="dashboard-bg flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-5 md:p-8">

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0b1f48] via-[#134b8a] to-[#0ea5e9] p-6 md:p-8 text-white mb-7 shadow-2xl reveal">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 -left-10 w-64 h-64 rounded-full bg-cyan-200/20"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-cyan-100 font-semibold">Admin Command Center</p>
                <h1 class="mt-2 text-2xl md:text-3xl font-extrabold leading-tight">Dashboard Monitoring Psikotes PETA</h1>
                <p class="mt-2 text-cyan-50/95 text-sm md:text-base">Ringkasan hasil tes, cakupan peserta, dan aktivitas terbaru dalam satu layar.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 min-w-[220px]">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/20">
                    <p class="text-xs text-cyan-100">Tanggal</p>
                    <p class="font-bold text-sm mt-1"><?= date('d M Y') ?></p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/20 pulse-glow">
                    <p class="text-xs text-cyan-100">Coverage</p>
                    <p class="font-bold text-sm mt-1"><?= $coverage ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-7">

        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold tracking-[0.14em] text-slate-400 uppercase">Peserta</p>
                <span class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4.41 0-8 2.24-8 5v1h16v-1c0-2.76-3.59-5-8-5z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 leading-none"><?= number_format($count_pegawai) ?></p>
            <p class="text-xs text-slate-500 mt-2">Total peserta terdaftar</p>
            <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-sky-500" style="width:100%"></div></div>
        </div>

        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold tracking-[0.14em] text-slate-400 uppercase">Tes 1 IQ</p>
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 21h6v-1H9v1zm3-20a7 7 0 0 0-4 12.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3.26A7 7 0 0 0 12 1z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 leading-none"><?= number_format($count_iq) ?></p>
            <p class="text-xs text-slate-500 mt-2">Attempt selesai tercatat</p>
            <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-amber-500" style="width:<?= min(100, $count_pegawai > 0 ? round(($count_iq / $count_pegawai) * 100) : 0) ?>%"></div></div>
        </div>

        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold tracking-[0.14em] text-slate-400 uppercase">Tes 2 Bagian 1</p>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM7 17H5v-2h2zm0-4H5v-2h2zm0-4H5V7h2zm12 8H9V7h10z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 leading-none"><?= number_format($count_msdt) ?></p>
            <p class="text-xs text-slate-500 mt-2">Hasil MSDT tersedia</p>
            <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-emerald-500" style="width:<?= min(100, $count_pegawai > 0 ? round(($count_msdt / $count_pegawai) * 100) : 0) ?>%"></div></div>
        </div>

        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold tracking-[0.14em] text-slate-400 uppercase">Tes 2 Bagian 2</p>
                <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5A4.5 4.5 0 0 1 6.5 4a4.93 4.93 0 0 1 3.5 1.5A4.93 4.93 0 0 1 13.5 4 4.5 4.5 0 0 1 18 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 leading-none"><?= number_format($count_papi) ?></p>
            <p class="text-xs text-slate-500 mt-2">Hasil PAPI tersedia</p>
            <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-rose-500" style="width:<?= min(100, $count_pegawai > 0 ? round(($count_papi / $count_pegawai) * 100) : 0) ?>%"></div></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 reveal">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-navy">Aktivitas Tes Terbaru</h3>
                <span class="text-xs text-slate-400 font-medium"><?= count($last_activity_rows) ?> data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-l-lg">Nama Pegawai</th>
                            <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Tes</th>
                            <th class="text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-r-lg">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($last_activity_rows)): ?>
                            <?php foreach($last_activity_rows as $row): ?>
                            <tr class="hover:bg-sky-50/60 transition-colors">
                                <td class="px-4 py-3 border-b border-slate-100">
                                    <span class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($row['nama']) ?></span>
                                </td>
                                <td class="px-4 py-3 border-b border-slate-100">
                                    <?php if($row['jenis'] === 'MSDT'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Tes 2 Bag. 1</span>
                                    <?php elseif($row['jenis'] === 'PAPI'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Tes 2 Bag. 2</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Tes 1 IQ</span>
                                    <?php endif; ?>
                                    <span class="ml-2 text-[11px] text-slate-400">#<?= (int)($row['attempt_number'] ?? 1) ?></span>
                                </td>
                                <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">
                                    <?= !empty($row['tanggal_tes']) ? date('d M Y, H:i', strtotime($row['tanggal_tes'])) : '-' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-12 text-slate-400 text-sm">Belum ada aktivitas tes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 reveal">
            <h3 class="text-base font-bold text-navy mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="status_pegawai.php" class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
                    <p class="text-sm font-semibold text-slate-800">Kelola Data Pegawai</p>
                    <p class="text-xs text-slate-500 mt-1">Import, edit, dan validasi akun peserta.</p>
                </a>
                <a href="hasil_peserta.php" class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
                    <p class="text-sm font-semibold text-slate-800">Pantau Hasil Tes</p>
                    <p class="text-xs text-slate-500 mt-1">Filter berdasarkan tanggal dan export data.</p>
                </a>
                <a href="kelola_soal.php" class="block rounded-xl border border-slate-200 p-3 hover:bg-slate-50 transition">
                    <p class="text-sm font-semibold text-slate-800">Kelola Bank Soal</p>
                    <p class="text-xs text-slate-500 mt-1">Perbarui soal IQ/MSDT/PAPI dengan cepat.</p>
                </a>
            </div>

            <div class="mt-5 rounded-xl bg-slate-900 p-4 text-slate-100">
                <p class="text-[11px] tracking-[0.14em] uppercase text-sky-300 font-semibold">Status Sistem</p>
                <p class="mt-2 text-sm">Skema data aktif: <span class="font-bold"><?= $has_unified_attempts ? 'Unified Attempts' : 'Legacy' ?></span></p>
                <p class="text-xs text-slate-300 mt-1">Total hasil tercatat: <?= number_format($total_hasil) ?></p>
            </div>
        </div>
    </div>

</div></body>
</html>