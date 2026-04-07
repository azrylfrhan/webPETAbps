<?php
require_once 'backend/auth_check.php';
require_once 'backend/config.php';
require_once 'backend/biodata_check.php';

$role = $_SESSION['role'] ?? 'peserta';
if ($role === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$nip = $_SESSION['nip'] ?? '';
$nama = $_SESSION['nama'] ?? 'Peserta';

$setupMissing = isset($_GET['setup']) && $_GET['setup'] === 'missing_table';
$biodataAda = false;
$biodata = [
    'tempat_lahir' => '',
    'tanggal_lahir' => '',
    'email' => ''
];

if (biodataTableExists($conn) && !empty($nip)) {
    $stmt = $conn->prepare("SELECT tempat_lahir, tanggal_lahir, email FROM biodata_peserta WHERE nip = ? LIMIT 1");
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!empty($found)) {
        $biodataAda = true;
        $biodata = $found;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Biodata | PETA</title>
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

        .hero-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(120deg, #0f1e3c, #12306b, #1d4ed8, #2563eb, #0f1e3c);
            background-size: 300% 300%;
            animation: gradientShift 14s ease infinite;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: -20%;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.16), transparent 28%),
                radial-gradient(circle at 80% 30%, rgba(255,255,255,0.10), transparent 26%),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.08), transparent 30%);
            animation: floatGlow 10s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes floatGlow {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(2.5%, -2%, 0) scale(1.06); }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-200 font-sans">

<div class="min-h-screen w-full grid lg:grid-cols-5 bg-white">
    <aside class="hero-panel hidden lg:flex lg:col-span-2 flex-col justify-between p-12 text-white">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="images/logobps.png" alt="Logo BPS" class="h-11 w-auto object-contain">
                    <span class="text-sm font-bold tracking-wide">PETA - Pemetaan Potensi Pegawai</span>
                </div>
                <a href="logout.php" data-logout-url="logout.php" class="js-logout-trigger rounded-xl bg-white/15 px-4 py-2 text-xs font-bold text-white transition hover:bg-white/25">Logout</a>
            </div>

            <div class="max-w-md">
                <h2 class="text-5xl font-bold leading-tight">Biodata Peserta</h2>
                <p class="mt-6 text-base leading-relaxed text-blue-100/90">
                    Lengkapi data diri dasar dengan benar agar identitas peserta tercatat rapi di sistem.
                </p>
                <p class="mt-4 text-base leading-relaxed text-blue-100/80">
                    Informasi seperti tempat lahir, tanggal lahir, dan email digunakan untuk keperluan administrasi.
                </p>
            </div>

            <div class="rounded-xl bg-blue-700/80 p-5 ring-1 ring-white/20">
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Panduan singkat</p>
                <ul class="mt-3 space-y-2 text-sm leading-relaxed text-blue-100/90">
                    <li>1. Gunakan data pribadi yang valid dan aktif.</li>
                    <li>2. Pastikan email dapat dihubungi jika diperlukan admin.</li>
                    <li>3. Simpan data setelah semua kolom terisi dengan benar.</li>
                </ul>
            </div>

            <div class="mt-4 rounded-xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm">
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Data yang dicatat</p>
                <div class="mt-3 space-y-2 text-sm leading-relaxed text-blue-100/90">
                    <p>• NIP sebagai identitas akun peserta.</p>
                    <p>• Tempat lahir dan tanggal lahir untuk verifikasi data dasar.</p>
                    <p>• Email untuk kontak administrasi bila diperlukan.</p>
                </div>
            </div>
    </aside>

    <main class="lg:col-span-3 flex min-h-screen items-center justify-center bg-white px-4 py-8 sm:px-8 sm:py-10 lg:px-10">
        <div class="w-full max-w-2xl">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between lg:hidden">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <img src="images/logobps.png" alt="Logo BPS" class="h-10 w-auto object-contain">
                    <span class="text-xs font-bold tracking-wide text-navy sm:text-sm">PETA - Pemetaan Potensi Pegawai</span>
                </div>
                <a href="logout.php" data-logout-url="logout.php" class="js-logout-trigger w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto">Logout</a>
            </div>

            <div class="mb-8 space-y-4 sm:space-y-6">
                <div class="grid grid-cols-3 items-center gap-2">
                    <div class="h-[2px] bg-blue-500 relative"><span class="absolute -top-[5px] left-0 h-2.5 w-2.5 rounded-full bg-blue-500"></span></div>
                    <div class="h-[2px] bg-blue-500 relative"><span class="absolute -top-[5px] left-1/2 h-3 w-3 -translate-x-1/2 rounded-full border-2 border-blue-600 bg-white"></span></div>
                    <div class="h-[2px] bg-slate-200 relative"><span class="absolute -top-[5px] right-0 h-2.5 w-2.5 rounded-full bg-slate-300"></span></div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400 sm:text-[11px]">
                    <span class="text-blue-600">Login</span>
                    <span class="text-center text-blue-600">Biodata</span>
                    <span class="text-right">Selesai</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 sm:text-3xl">Lengkapi Data Diri</h2>
                    <p class="mt-1 text-sm leading-relaxed text-slate-500">Isi identitas dasar dengan benar agar data peserta tersimpan lengkap.</p>
                </div>
            </div>

        <?php if ($biodataAda): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                Biodata sudah tersimpan.
            </div>
        <?php endif; ?>

        <?php if ($setupMissing): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                Tabel biodata belum tersedia. Silakan jalankan SQL di file users/biodata_peserta.sql terlebih dahulu.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'saved'): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">Biodata berhasil disimpan. Silakan lanjut ke dashboard tes.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                <?php
                    if ($_GET['error'] === 'empty') {
                        echo 'Semua field wajib diisi.';
                    } elseif ($_GET['error'] === 'invalid_email') {
                        echo 'Format email tidak valid.';
                    } elseif ($_GET['error'] === 'invalid_date') {
                        echo 'Tanggal lahir tidak valid.';
                    } elseif ($_GET['error'] === 'empty_reason') {
                        echo 'Alasan mengikuti tes wajib diisi.';
                    } else {
                        echo 'Gagal menyimpan biodata. Silakan coba lagi.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="backend/simpan_biodata.php" method="POST" class="space-y-5">
            <div class="auth-group">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">NIP</label>
                <input type="text" value="<?= htmlspecialchars($nip) ?>" readonly class="w-full rounded-md border border-gray-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 cursor-not-allowed">
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Tempat Lahir</label>
                <input
                    type="text"
                    name="tempat_lahir"
                    maxlength="100"
                    placeholder="Contoh: Manado"
                    value="<?= htmlspecialchars($biodata['tempat_lahir'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                    class="w-full rounded-md border border-gray-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 <?= $biodataAda ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : 'bg-white' ?>"
                >
                </div>
                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Tanggal Lahir</label>
                <input
                    type="date"
                    name="tanggal_lahir"
                    max="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars($biodata['tanggal_lahir'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                    class="w-full rounded-md border border-gray-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 <?= $biodataAda ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : 'bg-white' ?>"
                >
                <p class="mt-2 text-xs text-slate-500">Usia dihitung otomatis dari tanggal lahir dan tanggal hari ini.</p>
                </div>
            </div>

            <div class="auth-group">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Email</label>
                <input
                    type="email"
                    name="email"
                    maxlength="120"
                    placeholder="nama@domain.com"
                    value="<?= htmlspecialchars($biodata['email'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                    class="w-full rounded-md border border-gray-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 <?= $biodataAda ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : 'bg-white' ?>"
                >
            </div>

            <div class="auth-group">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Alasan Mengikuti Tes</label>
                <textarea
                    name="alasan_tes"
                    rows="4"
                    maxlength="1000"
                    placeholder="Tuliskan alasan Anda mengikuti tes hari ini"
                    required
                    class="w-full min-h-[120px] resize-y rounded-md border border-gray-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                ></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-blue-700"><?= $biodataAda ? 'Simpan Alasan & Lanjut Tes' : 'Simpan Biodata & Lanjut Tes' ?></button>
            </div>
        </form>

        <p class="mt-6 text-center text-xs text-slate-500">*Aplikasi ini hanya digunakan untuk keperluan internal.</p>
        </div>
    </main>
</div>

<?php include __DIR__ . '/backend/logout_modal.php'; ?>

</body>
</html>
