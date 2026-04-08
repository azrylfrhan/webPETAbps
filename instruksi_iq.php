<?php
include 'backend/auth_check.php';
require_once 'backend/config.php';

$nama = $_SESSION['nama'] ?? 'Peserta';
$nip = $_SESSION['nip'] ?? '-';

$sub_tes = isset($_GET['sub']) ? intval($_GET['sub']) : 1;

// Ambil instruksi dari iq_sections berdasarkan urutan
$query = "SELECT id, nama_bagian, waktu_detik, instruksi FROM iq_sections WHERE urutan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sub_tes);
$stmt->execute();
$result = $stmt->get_result();
$section = $result->fetch_assoc();
$stmt->close();

if (!$section) {
    echo "Data instruksi sub-tes belum ada di database.";
    exit();
}

// Ambil contoh soal untuk section ini
$query2 = "SELECT id, pertanyaan, jawaban_benar FROM iq_example_questions WHERE section_id = ? LIMIT 1";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("i", $section['id']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$example = $result2->fetch_assoc();
$stmt2->close();

// Ambil opsi jawaban untuk contoh soal
$options = [];
if ($example) {
    $query3 = "SELECT label, opsi_text FROM iq_example_options WHERE example_question_id = ? ORDER BY label ASC";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("i", $example['id']);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    while ($opt = $result3->fetch_assoc()) {
        $options[] = $opt;
    }
    $stmt3->close();
}

$durasiAktifDetik = (int)($section['waktu_detik'] ?? 0);
if ($durasiAktifDetik <= 0) {
    $durasiIstDetik = [
        1 => 6 * 60,
        2 => 6 * 60,
        3 => 7 * 60,
        4 => 8 * 60,
        5 => 10 * 60,
        6 => 10 * 60,
        7 => 7 * 60,
        8 => 9 * 60,
        9 => 6 * 60,
    ];
    $durasiAktifDetik = $durasiIstDetik[$sub_tes] ?? 360;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Tes 1 (IQ) | PETA</title>
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

<!-- HEADER -->
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

<!-- MAIN CONTENT -->
<main class="bg-grid px-4 py-8 sm:px-6 sm:py-10">
    <div class="mx-auto w-full max-w-6xl">

    <!-- HEADER SECTION -->
    <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-[#0f1e3c] via-[#1c3f75] to-[#5b9df3] p-5 text-white shadow-xl sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Persiapan Tes</p>
        <h1 class="mt-3 text-2xl font-extrabold leading-tight sm:text-4xl">Instruksi Tes 1 — Kelompok Soal <?php echo str_pad($sub_tes, 2, '0', STR_PAD_LEFT); ?></h1>
        <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base"><?php echo htmlspecialchars($section['nama_bagian']); ?></p>
        <p class="mt-2 text-sm text-blue-100/90">Baca petunjuk dengan teliti sebelum memulai sub-tes.</p>
        <div class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm ring-1 ring-white/25">
            <span class="font-semibold">NIP:</span>
            <span><?= htmlspecialchars($nip); ?></span>
        </div>
    </section>

    <!-- CONTENT SECTION -->
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        
        <!-- INSTRUKSI -->
        <div class="mb-8">
            <h2 class="mb-4 text-2xl font-bold text-slate-800">🔍 Cara Mengerjakan</h2>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($section['instruksi'] ?? 'Tidak ada instruksi.')); ?>
                </div>
            </div>
        </div>

        <!-- CONTOH SOAL -->
        <?php if ($example && count($options) > 0): ?>
        <div class="mb-8">
            <h2 class="mb-4 text-2xl font-bold text-slate-800">📝 Contoh Soal</h2>
            <div class="rounded-2xl border-2 border-dashed border-blue-300 bg-blue-50 p-6">
                <p class="mb-6 text-lg font-bold text-slate-800">
                    <?php echo htmlspecialchars($example['pertanyaan']); ?>
                </p>
                
                <div class="mb-6 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($options as $opt): ?>
                    <label class="flex items-center gap-3 rounded-xl border border-blue-200 bg-white p-4 cursor-pointer transition hover:bg-blue-100 hover:border-blue-400">
                        <input type="radio" name="opsi_contoh" value="<?php echo strtolower($opt['label']); ?>" class="w-4 h-4 text-blue-600 flex-shrink-0">
                        <span class="text-sm font-medium text-slate-700">
                            <strong><?php echo htmlspecialchars($opt['label']); ?>.</strong> <?php echo htmlspecialchars($opt['opsi_text']); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <button onclick="cekJawaban()" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">
                    Cek Jawaban Contoh
                </button>
                <p id="feedback" class="mt-4 text-center text-base font-bold hidden"></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- INFO BOX -->
        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-bold text-slate-500 uppercase">Waktu Tersedia:</p>
                <p class="mt-2 text-2xl font-bold text-navy"><?php echo ($durasiAktifDetik / 60); ?> Menit</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-bold text-slate-500 uppercase">Jenis Soal:</p>
                <p class="mt-2 text-lg font-bold text-slate-700"><?php echo htmlspecialchars($section['nama_bagian']); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-bold text-slate-500 uppercase">Status Persiapan:</p>
                <p id="status_persiapan" class="mt-2 text-base font-bold text-slate-500">⏳ Menunggu jawaban benar</p>
            </div>
        </div>

        <!-- ACTION BUTTON -->
        <div class="border-t pt-8">
            <form id="form_mulai_tes" method="GET" action="tes_proses/tes_iq/tes-iq.php" class="space-y-4">
                <input type="hidden" name="sub" value="<?php echo $sub_tes; ?>">
                <input type="hidden" name="reset" value="1">
                
                <button id="btn_mulai" type="submit" disabled class="w-full px-8 py-4 bg-slate-200 text-slate-400 rounded-2xl text-lg font-bold cursor-not-allowed transition">
                    ⏸️ Jawab Contoh Soal Terlebih Dahulu
                </button>

                <p class="text-center text-xs text-slate-500">
                    💡 Jawab contoh soal dengan benar untuk mengaktifkan tombol "Mulai Tes"
                </p>
            </form>
        </div>

    </section>

    </div>
</main>

<script>
    // Clear localStorage when starting new test
    function clearTestProgress() {
        // Hapus semua progress IQ yang tersimpan
        const keysToDelete = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.includes('peta_progress')) {
                keysToDelete.push(key);
            }
        }
        keysToDelete.forEach(key => localStorage.removeItem(key));
    }

    // Attach clear progress to form submit
    document.getElementById('form_mulai_tes').addEventListener('submit', function(e) {
        clearTestProgress();
    });

    function cekJawaban() {
        const terpilih = document.querySelector('input[name="opsi_contoh"]:checked');
        const feedback = document.getElementById('feedback');
        const btnMulai = document.getElementById('btn_mulai');
        const statusPersiapan = document.getElementById('status_persiapan');
        const kunci = "<?php echo strtolower($example['jawaban_benar'] ?? 'a'); ?>";

        if (!terpilih) {
            alert("Pilih salah satu jawaban dulu!");
            return;
        }

        feedback.classList.remove('hidden');
        if (terpilih.value === kunci) {
            feedback.innerHTML = "✓ Jawaban Benar! Anda siap untuk memulai tes.";
            feedback.className = "mt-4 text-center text-base font-bold text-green-600";
            
            btnMulai.disabled = false;
            btnMulai.className = "w-full px-8 py-4 bg-green-600 text-white rounded-2xl text-lg font-bold hover:bg-green-700 cursor-pointer transition";
            btnMulai.innerHTML = "✓ Mulai Tes Sekarang →";
            
            statusPersiapan.textContent = "✅ Siap untuk memulai tes";
            statusPersiapan.className = "mt-2 text-base font-bold text-green-600";
        } else {
            feedback.innerHTML = "✗ Jawaban Salah. Silakan coba lagi.";
            feedback.className = "mt-4 text-center text-base font-bold text-red-500";
        }
    }
</script>

<?php include __DIR__ . '/backend/logout_modal.php'; ?>
</body>
</html>