<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sectionId = (int) ($_POST['section_id'] ?? 0);
    $namaBagian = trim($_POST['nama_bagian'] ?? '');
    $jumlahSoal = (int) ($_POST['jumlah_soal'] ?? 0);
    $waktuDetik = (int) ($_POST['waktu_detik'] ?? 0);
    $waktuHafalan = trim($_POST['waktu_hafalan'] ?? '');
    $instruksi = trim($_POST['instruksi'] ?? '');
    $urutan = (int) ($_POST['urutan'] ?? 0);
    $gambarInstruksi = trim($_POST['gambar_instruksi'] ?? '');

    if ($sectionId <= 0 || $namaBagian === '' || $jumlahSoal <= 0 || $waktuDetik <= 0 || $urutan <= 0) {
        $flashError = 'Data pengaturan belum lengkap.';
    } else {
        $stmt = $conn->prepare("UPDATE iq_sections SET nama_bagian = ?, jumlah_soal = ?, waktu_detik = ?, waktu_hafalan = NULLIF(?, ''), instruksi = ?, urutan = ?, gambar_instruksi = ? WHERE id = ?");
        $waktuHafalanDb = trim($waktuHafalan);
        $stmt->bind_param('siissisi', $namaBagian, $jumlahSoal, $waktuDetik, $waktuHafalanDb, $instruksi, $urutan, $gambarInstruksi, $sectionId);
        if ($stmt->execute()) {
            header('Location: pengaturan_iq.php?msg=updated');
            exit;
        }
        $flashError = 'Gagal menyimpan pengaturan Tes IQ.';
        $stmt->close();
    }
}

$sections = [];
$result = $conn->query("SELECT id, nama_bagian, jumlah_soal, waktu_detik, waktu_hafalan, instruksi, urutan, gambar_instruksi FROM iq_sections ORDER BY urutan ASC");
while ($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

$selectedId = isset($_GET['section_id']) ? (int) $_GET['section_id'] : ((int)($sections[0]['id'] ?? 0));
$selectedSection = null;
foreach ($sections as $section) {
    if ((int)$section['id'] === $selectedId) {
        $selectedSection = $section;
        break;
    }
}
if (!$selectedSection && !empty($sections)) {
    $selectedSection = $sections[0];
}

$totalSections = count($sections);
$totalQuestions = array_sum(array_map(static fn($row) => (int)$row['jumlah_soal'], $sections));
$totalMinutes = array_sum(array_map(static fn($row) => (int)$row['waktu_detik'], $sections)) / 60;
$totalHafalan = array_sum(array_map(static fn($row) => (int)($row['waktu_hafalan'] ?? 0), $sections)) / 60;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Tes IQ | Admin BPS</title>
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
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 999px; }
        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 30, 60, 0.07) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex">
<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-5 md:p-8">
    <div class="mb-7 rounded-3xl bg-gradient-to-r from-[#0b1f48] via-[#134b8a] to-[#0ea5e9] p-6 md:p-8 text-white shadow-2xl">
        <p class="text-xs uppercase tracking-[0.18em] text-cyan-100 font-semibold">Admin Settings</p>
        <h1 class="mt-2 text-2xl md:text-3xl font-extrabold leading-tight">Pengaturan Tes IQ</h1>
        <p class="mt-2 text-cyan-50/95 text-sm md:text-base">Kelola isi instruksi, durasi, jumlah soal, dan gambar petunjuk untuk setiap bagian Tes 1.</p>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            ✓ Pengaturan Tes IQ berhasil disimpan.
        </div>
    <?php endif; ?>

    <?php if (!empty($flashError)): ?>
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
            <?= h($flashError) ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Bagian</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900"><?= number_format($totalSections) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Soal</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900"><?= number_format($totalQuestions) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Waktu</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900"><?= number_format($totalMinutes, 1) ?> menit</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Hafalan</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900"><?= number_format($totalHafalan, 1) ?> menit</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm self-start">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-navy">Daftar Bagian</h2>
                <span class="text-[11px] text-slate-400 font-medium"><?= count($sections) ?> item</span>
            </div>
            <div class="space-y-2">
                <?php foreach ($sections as $section): ?>
                    <a href="pengaturan_iq.php?section_id=<?= (int)$section['id'] ?>"
                       class="block rounded-2xl border px-4 py-3 transition <?= (int)$selectedSection['id'] === (int)$section['id'] ? 'border-blue-300 bg-blue-50 text-blue-900' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100' ?>">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Bagian <?= (int)$section['urutan'] ?></p>
                                <p class="mt-1 font-semibold leading-tight"><?= h($section['nama_bagian']) ?></p>
                            </div>
                            <span class="text-xs font-bold rounded-full px-2.5 py-1 bg-white border border-slate-200"><?= (int)$section['jumlah_soal'] ?> soal</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <?php if ($selectedSection): ?>
        <section class="rounded-3xl border border-slate-200 bg-white p-5 md:p-7 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Sedang Diedit</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Bagian <?= (int)$selectedSection['urutan'] ?> - <?= h($selectedSection['nama_bagian']) ?></h2>
                    <p class="mt-2 text-sm text-slate-500">Gunakan halaman ini untuk menyesuaikan instruksi yang tampil di halaman tes IQ.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?= (int)$selectedSection['jumlah_soal'] ?> soal</span>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700"><?= ((int)$selectedSection['waktu_detik'] / 60) ?> menit</span>
                    <?php if (!empty($selectedSection['waktu_hafalan'])): ?>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700"><?= ((int)$selectedSection['waktu_hafalan'] / 60) ?> menit hafalan</span>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="section_id" value="<?= (int)$selectedSection['id'] ?>">

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Nama Bagian</label>
                        <input type="text" name="nama_bagian" value="<?= h($selectedSection['nama_bagian']) ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Urutan Bagian</label>
                        <input type="number" name="urutan" value="<?= (int)$selectedSection['urutan'] ?>" min="1" max="9" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Jumlah Soal</label>
                        <input type="number" name="jumlah_soal" value="<?= (int)$selectedSection['jumlah_soal'] ?>" min="1" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Waktu Pengerjaan (detik)</label>
                        <input type="number" name="waktu_detik" value="<?= (int)$selectedSection['waktu_detik'] ?>" min="1" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Waktu Hafalan (detik, opsional)</label>
                        <input type="number" name="waktu_hafalan" value="<?= h($selectedSection['waktu_hafalan']) ?>" min="0" placeholder="Kosongkan jika tidak ada" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Gambar Instruksi (nama file / path)</label>
                        <input type="text" name="gambar_instruksi" value="<?= h($selectedSection['gambar_instruksi']) ?>" placeholder="contoh: images/img_section/contoh 7.jpeg" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Instruksi Lengkap</label>
                    <textarea name="instruksi" rows="14" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 outline-none focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100 leading-relaxed"><?= h($selectedSection['instruksi']) ?></textarea>
                    <p class="mt-2 text-xs text-slate-400">Teks ini akan ditampilkan di layar instruksi Tes 1 sebelum peserta masuk ke soal.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#0f1e3c] via-[#1b3f74] to-[#5b9df3] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:opacity-95">
                        💾 Simpan Pengaturan
                    </button>
                    <a href="kelola_soal.php" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Ke Kelola Soal</a>
                </div>
            </form>
        </section>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
