<?php
require_once 'backend/config.php';
require_once 'backend/auth_check.php';

$nip  = $_SESSION['nip'];
$nama = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT nip FROM hasil_msdt WHERE nip='$nip'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: dashboard.php?status=tes_selesai");
    exit;
}

$query  = "SELECT * FROM soal WHERE kode_tes = 'KEPRIBADIAN' ORDER BY nomor_soal ASC";
$result = mysqli_query($conn, $query);

$all_soal = [];
while ($row = mysqli_fetch_assoc($result)) $all_soal[] = $row;
$total = count($all_soal);

define('WAKTU_DETIK', 45 * 60); // 45 menit
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes 2 Bagian 1 | PETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .bg-navy { background-color: #1a2b5e; }
        .text-navy { color: #1a2b5e; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        input[type="radio"] { accent-color: #1a2b5e; width: 16px; height: 16px; flex-shrink: 0; }
        .timer-warning { color: #dc2626 !important; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.6; } }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- HEADER -->
    <header class="bg-navy sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="images/logobps.png" alt="Logo BPS" class="h-10">
                <div>
                    <p class="text-white font-extrabold text-sm uppercase tracking-wide leading-tight">Tes 2 — Bagian 1</p>
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">PETA — Pemetaan Potensi Pegawai</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-white font-bold text-sm leading-tight"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-blue-200 text-xs"><?= htmlspecialchars($nip) ?></p>
                </div>
                <!-- TIMER — tersembunyi saat instruksi -->
                <div id="timer-box" class="hidden bg-white/10 border border-white/20 px-4 py-2 rounded-xl text-center">
                    <p class="text-[9px] font-black text-blue-200 uppercase tracking-widest">Sisa Waktu</p>
                    <p id="timer-display" class="text-xl font-black text-white font-mono">45:00</p>
                </div>
            </div>
        </div>
    </header>

    <!-- PROGRESS BAR -->
    <div class="w-full bg-slate-200 h-1">
        <div id="progress-bar" class="bg-navy h-full transition-all duration-500" style="width:0%"></div>
    </div>

    <main class="flex-1 p-6">

        <!-- INSTRUKSI -->
        <section id="instruction-section" class="flex items-center justify-center min-h-[calc(100vh-5rem)]">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-2xl p-10 fade-in">
                <h2 class="text-2xl font-bold text-navy mb-6">Instruksi Tes</h2>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-slate-600 leading-relaxed mb-6 space-y-3">
                    <p>Pada halaman-halaman berikut, Anda akan membaca sejumlah pernyataan mengenai tindakan yang mungkin Anda lakukan dalam tugas Anda di unit kerja.</p>
                    <p>Anda diminta untuk memilih pernyataan <strong>A</strong> atau <strong>B</strong> yang paling sesuai dengan diri Anda, atau paling mungkin Anda lakukan.</p>
                </div>
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-8">
                    <span class="text-blue-600 text-xl">⏱</span>
                    <p class="text-blue-700 text-sm font-semibold">Waktu pengerjaan: <strong>45 menit</strong>. Timer mulai saat Anda klik tombol di bawah.</p>
                </div>
                <button type="button" id="start-test-btn"
                    class="bg-navy text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Mulai Kerjakan Soal
                </button>
            </div>
        </section>

        <!-- SOAL -->
        <section id="questions-section" class="hidden max-w-2xl mx-auto fade-in">
            <form action="tes_proses/proses_simpan_tes.php" method="POST" id="form-soal">

                <div class="flex justify-between items-center mb-3 mt-2">
                    <span id="soal-counter" class="text-sm font-semibold text-slate-500">Soal 1 dari <?= $total ?></span>
                    <span id="soal-pct" class="text-sm font-semibold text-slate-400">0%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-1.5 mb-6">
                    <div id="soal-progress" class="bg-navy h-1.5 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>

                <?php foreach ($all_soal as $i => $row):
                    $no = $row['nomor_soal']; ?>
                <div class="question-item bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-6"
                     data-nomor="<?= $i+1 ?>" data-total="<?= $total ?>">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Soal <?= $no ?></p>
                    <label class="flex items-start gap-3 border border-slate-200 rounded-xl p-4 mb-3 cursor-pointer hover:bg-slate-50 hover:border-navy transition">
                        <input type="radio" name="jawaban[<?= $no ?>]" value="A" class="mt-0.5">
                        <span class="text-slate-700"><strong class="text-navy">A.</strong> <?= htmlspecialchars($row['pertanyaan_a']) ?></span>
                    </label>
                    <label class="flex items-start gap-3 border border-slate-200 rounded-xl p-4 cursor-pointer hover:bg-slate-50 hover:border-navy transition">
                        <input type="radio" name="jawaban[<?= $no ?>]" value="B" class="mt-0.5">
                        <span class="text-slate-700"><strong class="text-navy">B.</strong> <?= htmlspecialchars($row['pertanyaan_b']) ?></span>
                    </label>
                </div>
                <?php endforeach; ?>

                <div class="flex justify-center mt-4 mb-16">
                    <button type="submit" id="btn-submit"
                        onclick="return confirm('Kirim jawaban sekarang? Pastikan semua soal telah terisi.')"
                        class="bg-navy text-white px-10 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                        Kirim Jawaban Bagian 1
                    </button>
                </div>

            </form>
        </section>

    </main>

    <script>
        const TOTAL_SOAL  = <?= $total ?>;
        const WAKTU_DETIK = <?= WAKTU_DETIK ?>;
        const STORAGE_KEY = 'peta_tes2b1_timer_<?= $nip ?>';

        let timerInterval = null;

        // ── START BUTTON ──
        document.getElementById('start-test-btn').addEventListener('click', function () {
            document.getElementById('instruction-section').style.display = 'none';
            document.getElementById('questions-section').classList.remove('hidden');
            document.getElementById('timer-box').classList.remove('hidden');
            window.scrollTo(0, 0);
            startTimer();
        });

        // ── TIMER ──
        function startTimer() {
            // Ambil sisa waktu dari localStorage kalau ada
            let saved = localStorage.getItem(STORAGE_KEY);
            let remaining = saved ? parseInt(saved) : WAKTU_DETIK;
            if (isNaN(remaining) || remaining <= 0) remaining = WAKTU_DETIK;

            updateDisplay(remaining);

            timerInterval = setInterval(() => {
                remaining--;
                localStorage.setItem(STORAGE_KEY, remaining);
                updateDisplay(remaining);

                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    localStorage.removeItem(STORAGE_KEY);
                    alert('Waktu habis! Jawaban akan dikirim otomatis.');
                    document.getElementById('form-soal').submit();
                }
            }, 1000);
        }

        function updateDisplay(seconds) {
            const m   = Math.floor(seconds / 60);
            const s   = seconds % 60;
            const el  = document.getElementById('timer-display');
            el.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            // Merah + pulse saat < 5 menit
            if (seconds <= 300) el.classList.add('timer-warning');
            else el.classList.remove('timer-warning');
        }

        // Hapus timer dari storage saat submit manual
        document.getElementById('form-soal').addEventListener('submit', function() {
            localStorage.removeItem(STORAGE_KEY);
            clearInterval(timerInterval);
        });

        // ── PROGRESS BAR ──
        function updateProgress() {
            const cards = document.querySelectorAll('.question-item');
            let visible = 0;
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight * 0.6) visible = parseInt(card.dataset.nomor);
            });
            if (visible < 1) visible = 1;
            const pct = Math.round((visible / TOTAL_SOAL) * 100);
            document.getElementById('soal-counter').textContent = `Soal ${visible} dari ${TOTAL_SOAL}`;
            document.getElementById('soal-pct').textContent     = `${pct}%`;
            document.getElementById('soal-progress').style.width = `${pct}%`;
            document.getElementById('progress-bar').style.width  = `${pct}%`;
        }
        window.addEventListener('scroll', updateProgress);
    </script>

</body>
</html>