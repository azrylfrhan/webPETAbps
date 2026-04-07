<?php
require_once 'backend/auth_check.php'; 
require_once 'backend/config.php';
require_once 'backend/biodata_check.php';

$nama = $_SESSION['nama'] ?? 'User';
$nip = $_SESSION['nip'] ?? '-';
$satuan_kerja = $_SESSION['satuan_kerja'] ?? 'BPS Sulawesi Utara'; 

if (($_SESSION['role'] ?? 'peserta') !== 'admin') {
    redirectJikaBiodataBelumLengkap($conn, $nip, 'biodata.php');
}

// Cek apakah Tes 1 (IQ) sudah selesai
$cek_iq = $conn->prepare("SELECT status FROM iq_test_sessions WHERE nip = ? ORDER BY id DESC LIMIT 1");
$cek_iq->bind_param("s", $nip);
$cek_iq->execute();
$iq_session = $cek_iq->get_result()->fetch_assoc();
$tes1_selesai = $iq_session && $iq_session['status'] === 'finished';

$cek_bagian1 = mysqli_query($conn, "SELECT id FROM hasil_msdt WHERE nip = '$nip' AND Ds IS NOT NULL");
$sudah_bagian1 = mysqli_num_rows($cek_bagian1) > 0;

$cek_bagian2 = mysqli_query($conn, "SELECT id FROM hasil_papi WHERE nip = '$nip'");
$sudah_bagian2 = mysqli_num_rows($cek_bagian2) > 0;

$url_tes2 = "tes-kepribadian.php";
$label_tes2 = "Mulai Tes 2 →";
$status_kelas2 = "btn-purple";

if (!$tes1_selesai) {
    $url_tes2 = "#";
    $label_tes2 = "🔒 Selesaikan Tes 1 Dulu";
    $status_kelas2 = "btn-disabled";
} elseif ($sudah_bagian1 && !$sudah_bagian2) {
    $url_tes2 = "tes-kepribadian2.php";
    $label_tes2 = "Lanjut ke Bagian 2 →";
} elseif ($sudah_bagian1 && $sudah_bagian2) {
    $url_tes2 = "#";
    $label_tes2 = "✓ Tes Selesai";
    $status_kelas2 = "btn-disabled";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | PETA</title>
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
        .btn-disabled {
            background-color: #cbd5e1 !important;
            color: #64748b !important;
            cursor: not-allowed;
            pointer-events: none;
        }
        .bg-grid {
            background-image:
                radial-gradient(circle at 1px 1px, rgba(15, 30, 60, 0.08) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-100 text-slate-800">

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-navy text-white shadow-sm">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-3 px-3 py-3 sm:px-6 sm:py-4">
        <div class="flex items-center gap-3">
            <img src="images/logobps.png" alt="Logo BPS" class="h-10 w-auto object-contain">
            <div>
                <p class="hidden text-[11px] uppercase tracking-[0.22em] text-blue-200 sm:block">Portal Tes Psikologi</p>
                <p class="text-xs font-bold sm:text-lg">PETA — Pemetaan Potensi Pegawai</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm sm:gap-3">
            <span class="hidden text-blue-100 sm:inline">Halo, <strong><?= htmlspecialchars($nama); ?></strong></span>
            <a href="logout.php" data-logout-url="logout.php" class="js-logout-trigger rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20 sm:px-4 sm:text-sm">Logout</a>
        </div>
    </div>
</header>

<main class="bg-grid">
    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 sm:py-10">

    <?php if (isset($_GET['iq']) && $_GET['iq'] == 'sudah_selesai'): ?>
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            ✓ Tes 1 sudah pernah Anda kerjakan sebelumnya.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'tes1_belum_selesai'): ?>
        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
            ⚠ Tes 2 masih terkunci. Silakan selesaikan Tes 1 terlebih dahulu.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'tes_selesai'): ?>
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            ✓ <strong>Terima Kasih!</strong> Seluruh rangkaian tes Anda telah berhasil disimpan.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['biodata']) && $_GET['biodata'] == 'ok'): ?>
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            ✓ Biodata berhasil disimpan. Anda sekarang bisa mulai mengerjakan tes.
        </div>
    <?php endif; ?>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-[#0f1e3c] via-[#1c3f75] to-[#5b9df3] p-5 text-white shadow-xl sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Dashboard Peserta</p>
                <h1 class="mt-3 text-2xl font-extrabold leading-tight sm:text-4xl">Selamat Datang di Portal PETA</h1>
                <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">Silakan pilih jenis tes yang ingin Anda ikuti dan pantau progres Anda secara bertahap.</p>
            </div>
            <div class="rounded-2xl bg-white/15 px-4 py-3 text-sm text-blue-50 ring-1 ring-white/25 backdrop-blur">
                <p class="font-semibold">Akun aktif</p>
                <p><?= htmlspecialchars($nip); ?></p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-3 sm:p-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Peserta</p>
            <p class="mt-1 text-lg font-bold text-slate-800"><?= htmlspecialchars($nama); ?></p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">NIP</p>
            <p class="mt-1 text-base font-semibold text-slate-800"><?= htmlspecialchars($nip); ?></p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Unit Kerja</p>
            <p class="mt-1 text-base font-semibold text-slate-800"><?= htmlspecialchars($satuan_kerja); ?></p>
        </div>
    </section>

    <section class="mt-6">
        <div class="grid gap-6 lg:grid-cols-2">

            <!-- TES 1 -->
            <div class="rounded-3xl border border-blue-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-blue-100">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="9" y1="13" x2="15" y2="13"/>
                            <line x1="9" y1="17" x2="12" y2="17"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 sm:text-2xl">Tes 1</h3>
                            <p class="text-sm text-slate-500">Tes intelektual (IQ)</p>
                        </div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold <?= $tes1_selesai ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' ?>">
                        <?= $tes1_selesai ? 'Selesai' : 'Tersedia' ?>
                    </span>
                </div>
                <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Status pengerjaan</span>
                        <strong class="text-slate-800"><?= $tes1_selesai ? 'Selesai' : 'Belum Dikerjakan' ?></strong>
                    </div>
                </div>
                <button class="mt-5 w-full rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wide text-white transition <?= $tes1_selesai ? 'btn-disabled' : 'bg-blue-600 hover:bg-blue-700' ?>"
                    onclick="<?= $tes1_selesai ? '' : "window.location.href='tes_proses/tes_iq/tes-iq.php'" ?>">
                    <?= $tes1_selesai ? '✓ Tes Selesai' : 'Mulai Tes 1 →' ?>
                </button>
            </div>

            <!-- TES 2 -->
            <div class="rounded-3xl border border-violet-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-violet-100">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                            <polyline points="2 17 12 22 22 17"/>
                            <polyline points="2 12 12 17 22 12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 sm:text-2xl">Tes 2</h3>
                            <p class="text-sm text-slate-500">Tes kepribadian (MSDT & PAPI)</p>
                        </div>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold <?= !$tes1_selesai ? 'bg-amber-100 text-amber-700' : (($sudah_bagian1 && $sudah_bagian2) ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700') ?>">
                        <?php
                            if (!$tes1_selesai) echo "Terkunci";
                            elseif (!$sudah_bagian1) echo "Belum Mulai";
                            elseif (!$sudah_bagian2) echo "Bagian 1 Selesai";
                            else echo "Selesai";
                        ?>
                    </span>
                </div>
                <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Status pengerjaan</span>
                        <strong class="text-slate-800">
                            <?php
                                if (!$tes1_selesai) echo "Terkunci";
                                elseif (!$sudah_bagian1) echo "Belum Mulai";
                                elseif (!$sudah_bagian2) echo "Bagian 1 Selesai";
                                else echo "Selesai";
                            ?>
                        </strong>
                    </div>
                </div>
                <button class="mt-5 w-full rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wide text-white transition <?= $status_kelas2 === 'btn-disabled' ? 'btn-disabled' : 'bg-violet-600 hover:bg-violet-700' ?>" onclick="window.location.href='<?= $url_tes2 ?>'">
                    <?= $label_tes2 ?>
                </button>
            </div>

        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-600 shadow-sm">
        <strong>🔒 Kerahasiaan Data:</strong> Hasil tes bersifat rahasia dan digunakan hanya untuk keperluan internal BPS.
    </section>

    </div>
</main>

<?php include __DIR__ . '/backend/logout_modal.php'; ?>

</body>
</html>