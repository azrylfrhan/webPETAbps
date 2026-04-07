<?php 
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Mengambil ringkasan data untuk dashboard
$count_pegawai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'peserta'"))['total'];
$count_msdt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_msdt"))['total'];
$count_papi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_papi"))['total'];

// Mengambil jumlah hasil Tes IQ dari tabel iq_results
$count_iq = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM iq_results"))['total'];

// Mengambil 5 aktivitas terbaru dengan menambahkan UNION untuk hasil Tes IQ
$last_activity = mysqli_query($conn, "
    SELECT u.nama, 'MSDT' as jenis, h.tanggal_tes FROM hasil_msdt h JOIN users u ON h.nip = u.nip 
    UNION 
    SELECT u.nama, 'PAPI' as jenis, h.tanggal_tes FROM hasil_papi h JOIN users u ON h.nip = u.nip 
    UNION
    SELECT u.nama, 'IQ' as jenis, h.tanggal AS tanggal_tes FROM iq_results h JOIN users u ON h.user_id = u.nip
    ORDER BY tanggal_tes DESC LIMIT 5
");
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Sidebar active link indicator */
        nav a.active {
            background: linear-gradient(135deg, #2563EB, #3B82F6);
            color: white;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }
        nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #38BDF8;
            border-radius: 0 3px 3px 0;
        }

        /* Card top border accent */
        .card-blue::before  { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#2563EB,#38BDF8); border-radius:12px 12px 0 0; }
        .card-green::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#10B981,#34D399); border-radius:12px 12px 0 0; }
        .card-purple::before{ content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#8B5CF6,#A78BFA); border-radius:12px 12px 0 0; }
        .card-amber::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#F59E0B,#FCD34D); border-radius:12px 12px 0 0; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <div class="flex items-start justify-between mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Selamat Datang, Admin</h1>
            <p class="text-slate-500 text-sm mt-1">Sistem Informasi PETA — Pemetaan Potensi Pegawai BPS Provinsi Sulawesi Utara.</p>
        </div>
        <div class="text-right text-sm text-slate-400">
            <?= date('l, d F Y') ?>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <div class="relative bg-white rounded-xl p-6 flex items-center gap-5 shadow-sm border border-slate-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 card-blue overflow-hidden">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-blue-50 flex-shrink-0">
                👥
            </div>
            <div>
                <p class="text-3xl font-extrabold text-navy tracking-tight"><?= $count_pegawai ?></p>
                <p class="text-slate-400 text-xs font-medium mt-1">Total Pegawai</p>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-6 flex items-center gap-5 shadow-sm border border-slate-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 card-green overflow-hidden">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-emerald-50 flex-shrink-0">
                📋
            </div>
            <div>
                <p class="text-3xl font-extrabold text-navy tracking-tight"><?= $count_iq ?></p>
                <p class="text-slate-400 text-xs font-medium mt-1">Hasil Tes 1</p>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-6 flex items-center gap-5 shadow-sm border border-slate-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 card-purple overflow-hidden">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-violet-50 flex-shrink-0">
                📋
            </div>
            <div>
                <p class="text-3xl font-extrabold text-navy tracking-tight"><?= $count_msdt ?></p>
                <p class="text-slate-400 text-xs font-medium mt-1">Hasil Tes 2 - Bag. 1</p>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-6 flex items-center gap-5 shadow-sm border border-slate-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 card-amber overflow-hidden">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-amber-50 flex-shrink-0">
                📋
            </div>
            <div>
                <p class="text-3xl font-extrabold text-navy tracking-tight"><?= $count_papi ?></p>
                <p class="text-slate-400 text-xs font-medium mt-1">Hasil Tes 2 - Bag. 2</p>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-navy">Aktivitas Tes Terbaru</h3>
            <span class="text-xs text-slate-400 font-medium">5 data terakhir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-l-lg">Nama Pegawai</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50">Jenis Tes</th>
                        <th class="text-left text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-3 bg-slate-50 rounded-r-lg">Tanggal & Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($last_activity)): ?>
                    <tr class="hover:bg-blue-50/40 transition-colors">
                        <td class="px-4 py-3 border-b border-slate-100">
                            <span class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($row['nama']) ?></span>
                        </td>
                        <td class="px-4 py-3 border-b border-slate-100">
                            <?php if($row['jenis'] == 'MSDT'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                    Tes 2 - Bag. 1
                                </span>
                            <?php elseif($row['jenis'] == 'PAPI'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-violet-100 text-violet-700">
                                    Tes 2 - Bag. 2
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                    Tes 1
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">
                            <?= date('d M Y, H:i', strtotime($row['tanggal_tes'])) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if(mysqli_num_rows($last_activity) == 0): ?>
                    <tr>
                        <td colspan="3" class="text-center py-12 text-slate-400 text-sm">
                            <div class="text-4xl mb-3">🕒</div>
                            Belum ada aktivitas tes.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div></body>
</html>