<?php
// Baris 1: Proteksi halaman
require_once __DIR__ . '/backend/auth_check.php'; 
$nama = $_SESSION['nama'] ?? 'Peserta';
$nip = $_SESSION['nip'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tes | PETA</title>
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
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-navy text-white shadow-sm">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
        <div class="flex items-center gap-3">
            <img src="images/logobps.png" alt="Logo BPS" class="h-10 w-auto object-contain">
            <div>
                <p class="text-[11px] uppercase tracking-[0.22em] text-blue-200">Portal Tes Psikologi</p>
                <p class="text-sm font-bold sm:text-lg">PETA — Pemetaan Potensi Pegawai</p>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="hidden text-blue-100 sm:inline">Halo, <strong><?= htmlspecialchars($nama); ?></strong></span>
            <a href="logout.php" data-logout-url="logout.php" class="js-logout-trigger rounded-xl border border-white/20 bg-white/10 px-4 py-2 font-semibold text-white transition hover:bg-white/20">Logout</a>
        </div>
    </div>
</header>

<main class="px-4 py-8 sm:px-6 sm:py-10">
    <div class="mx-auto w-full max-w-4xl">

    <section class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-[#0f1e3c] via-[#1c3f75] to-[#5b9df3] p-6 text-white shadow-xl sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Ringkasan Hasil</p>
        <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Hasil Tes Psikologi</h1>
        <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">Hasil digunakan sebagai bahan pemetaan potensi pegawai untuk kebutuhan internal.</p>
        <div class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm ring-1 ring-white/25">
            <span class="font-semibold">NIP:</span>
            <span><?= htmlspecialchars($nip); ?></span>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-bold text-slate-800">Ringkasan Penilaian</h2>

        <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 px-6 py-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Total Skor</p>
            <h1 id="totalScore" class="mt-2 text-5xl font-black text-blue-800">0</h1>
        </div>

        <div class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kategori</p>
                <h3 id="resultCategory" class="mt-1 text-lg font-bold text-slate-800">-</h3>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Deskripsi</p>
                <p id="resultDescription" class="mt-1 text-sm leading-relaxed text-slate-700">-</p>
            </div>
        </div>

        <div class="mt-6">
            <button class="w-full rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-blue-700" onclick="window.location.href='dashboard.php'">
                Kembali ke Beranda
            </button>
        </div>
    </section>

    </div>
</main>

<script src="main.js"></script>
<?php include __DIR__ . '/backend/logout_modal.php'; ?>
</body>
</html>
