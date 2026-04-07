<?php
require_once __DIR__ . '/backend/auth_check.php';
$nama = $_SESSION['nama'] ?? 'Peserta';
$nip = $_SESSION['nip'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Tes | PETA</title>
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
        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 30, 60, 0.08) 1px, transparent 0);
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

<main class="bg-grid px-4 py-8 sm:px-6 sm:py-10">
    <div class="mx-auto w-full max-w-4xl">

    <section class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-[#0f1e3c] via-[#1c3f75] to-[#5b9df3] p-5 text-white shadow-xl sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Persiapan Tes</p>
        <h1 class="mt-3 text-2xl font-extrabold leading-tight sm:text-4xl">Instruksi Tes Psikologi</h1>
        <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">Baca petunjuk dengan teliti sebelum memulai agar proses pengerjaan berjalan lancar.</p>
        <div class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm ring-1 ring-white/25">
            <span class="font-semibold">NIP:</span>
            <span><?= htmlspecialchars($nip); ?></span>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        <h2 class="text-2xl font-bold text-slate-800">Panduan Pengerjaan</h2>

        <p class="mt-4 text-slate-600 leading-relaxed">
            Tes ini bertujuan untuk memperoleh gambaran umum karakteristik psikologis pegawai dalam konteks kerja.
        </p>

        <ul class="mt-5 space-y-3 text-slate-700">
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">Jawablah setiap pertanyaan dengan jujur dan sesuai kondisi Anda.</li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">Tidak ada jawaban benar atau salah pada tes kepribadian.</li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">Kerjakan tes dalam satu sesi tanpa jeda panjang.</li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">Pastikan koneksi internet stabil selama pengerjaan.</li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">Data dan hasil tes bersifat rahasia untuk kebutuhan internal.</li>
        </ul>

        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800"><strong>Jumlah Soal:</strong> 60</div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800"><strong>Estimasi Waktu:</strong> ±30 menit</div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800"><strong>Jenis Skala:</strong> Skala Likert</div>
        </div>

        <div class="mt-7 flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
            <input type="checkbox" id="agree" class="mt-1 h-4 w-4 accent-blue-600">
            <label for="agree" class="text-sm text-slate-700">
                Saya telah membaca dan memahami instruksi tes
            </label>
        </div>

        <div class="mt-6">
            <button class="w-full rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold uppercase tracking-wide text-white transition enabled:hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500" id="startBtn" disabled>
                Mulai Tes
            </button>
        </div>
    </section>

    </div>
</main>

<script src="main.js"></script>
<?php include __DIR__ . '/backend/logout_modal.php'; ?>
</body>
</html>
