<?php
require_once 'backend/auth_check.php';
require_once 'backend/config.php';

$nip  = $_SESSION['nip'] ?? '-';
$nama = $_SESSION['nama'] ?? 'User';

$check_msdt = mysqli_query($conn, "SELECT id FROM hasil_msdt WHERE nip = '$nip'");
if (mysqli_num_rows($check_msdt) == 0) {
    header("Location: tes-kepribadian.php?error=selesaikan_bagian1");
    exit;
}

$check_papi = mysqli_query($conn, "SELECT id FROM hasil_papi WHERE nip = '$nip'");
if (mysqli_num_rows($check_papi) > 0) {
    header("Location: dashboard.php?status=tes_selesai");
    exit;
}

$query  = "SELECT * FROM soal WHERE kode_tes = 'KEPRIBADIAN2' AND status = 'aktif' ORDER BY nomor_soal ASC";
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
    <title>Tes 2 Bagian 2 | PETA</title>
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

        /* ── MODAL ── */
        #modal-belum-jawab {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.45);
            align-items: center; justify-content: center;
        }
        #modal-belum-jawab.show { display: flex; }
        #modal-belum-jawab .modal-box {
            background: #fff; border-radius: 1.25rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            max-width: 480px; width: 90%; padding: 2rem;
            animation: fadeIn 0.25s ease;
        }
        .nomor-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
            background: #fee2e2; color: #b91c1c;
            font-weight: 700; font-size: 0.8rem;
            cursor: pointer; transition: background 0.15s, transform 0.1s;
            border: 1.5px solid #fca5a5;
        }
        .nomor-btn:hover { background: #fca5a5; transform: scale(1.08); }

        /* Highlight soal yang belum dijawab saat diklik dari modal */
        .question-highlight {
            animation: highlightPulse 1.6s ease;
        }
        @keyframes highlightPulse {
            0%   { box-shadow: 0 0 0 0 rgba(220,38,38,0.4); border-color: #ef4444; }
            50%  { box-shadow: 0 0 0 8px rgba(220,38,38,0); border-color: #ef4444; }
            100% { box-shadow: none; border-color: #e2e8f0; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- HEADER -->
    <header class="bg-navy sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="images/logobps.png" alt="Logo BPS" class="h-10">
                <div>
                    <p class="text-white font-extrabold text-sm uppercase tracking-wide leading-tight">Tes 2 — Bagian 2</p>
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">PETA — Pemetaan Potensi Pegawai</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-white font-bold text-sm leading-tight"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-blue-200 text-xs"><?= htmlspecialchars($nip) ?></p>
                </div>
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
                    <p>Pilihlah satu pernyataan yang paling mencerminkan diri Anda dalam situasi kerja.</p>
                    <p>Anda diminta untuk memilih pernyataan <strong>A</strong> atau <strong>B</strong> yang paling sesuai dengan kondisi dan kepribadian Anda.</p>
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
            <form action="tes_proses/proses_simpan_papi.php" method="POST" id="form-soal">

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
                     id="soal-item-<?= $no ?>"
                     data-nomor="<?= $i+1 ?>" data-no="<?= $no ?>" data-total="<?= $total ?>">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Soal <?= $no ?></p>
                    <label class="flex items-start gap-3 border border-slate-200 rounded-xl p-4 mb-3 cursor-pointer hover:bg-slate-50 hover:border-navy transition">
                        <input type="radio" name="jawaban_papi[<?= $no ?>]" value="A" class="mt-0.5">
                        <span class="text-slate-700"><strong class="text-navy">A.</strong> <?= htmlspecialchars($row['pertanyaan_a']) ?></span>
                    </label>
                    <label class="flex items-start gap-3 border border-slate-200 rounded-xl p-4 cursor-pointer hover:bg-slate-50 hover:border-navy transition">
                        <input type="radio" name="jawaban_papi[<?= $no ?>]" value="B" class="mt-0.5">
                        <span class="text-slate-700"><strong class="text-navy">B.</strong> <?= htmlspecialchars($row['pertanyaan_b']) ?></span>
                    </label>
                </div>
                <?php endforeach; ?>

                <div class="flex justify-center mt-4 mb-16">
                    <button type="button" id="btn-submit"
                        class="bg-navy text-white px-10 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                        Kirim Jawaban Bagian 2
                    </button>
                </div>

            </form>
        </section>

    </main>

    <!-- ══════════════════════════════════════════
         MODAL — Soal Belum Dijawab
    ══════════════════════════════════════════ -->
    <div id="modal-belum-jawab" role="dialog" aria-modal="true">
        <div class="modal-box">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Ada Soal yang Belum Dijawab</h3>
            </div>
            <p class="text-slate-500 text-sm mb-1 ml-13 pl-0.5" id="modal-sub-text"></p>

            <div id="nomor-belum-list" class="flex flex-wrap gap-2 mt-4 mb-6 max-h-48 overflow-y-auto pr-1"></div>

            <p class="text-xs text-slate-400 mb-5">Klik nomor di atas untuk langsung menuju soal tersebut, lalu jawab sebelum mengirim.</p>

            <div class="flex gap-3">
                <button type="button" id="modal-close-btn"
                    class="flex-1 border border-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-slate-50 transition">
                    Kembali ke Soal
                </button>
                <button type="button" id="modal-force-submit-btn"
                    class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-red-700 transition">
                    Kirim Tetap (Tidak Lengkap)
                </button>
            </div>
        </div>
    </div>

    <script>
        const TOTAL_SOAL  = <?= $total ?>;
        const WAKTU_DETIK = <?= WAKTU_DETIK ?>;
        const STORAGE_KEY = 'peta_tes2b2_timer_<?= $nip ?>';

        const NOMOR_SOAL_LIST = <?= json_encode(array_column($all_soal, 'nomor_soal')) ?>;

        let timerInterval = null;
        let autoSubmit    = false;

        document.getElementById('start-test-btn').addEventListener('click', function () {
            document.getElementById('instruction-section').style.display = 'none';
            document.getElementById('questions-section').classList.remove('hidden');
            document.getElementById('timer-box').classList.remove('hidden');
            window.scrollTo(0, 0);
            startTimer();
        });

        function startTimer() {
            let saved     = localStorage.getItem(STORAGE_KEY);
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
                    autoSubmit = true;
                    const form = document.getElementById('form-soal');
                    form.submit();
                }
            }, 1000);
        }

        function updateDisplay(seconds) {
            const m  = Math.floor(seconds / 60);
            const s  = seconds % 60;
            const el = document.getElementById('timer-display');
            el.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (seconds <= 450) el.classList.add('timer-warning');
            else el.classList.remove('timer-warning');
        }

        // ── VALIDASI & SUBMIT ──
        function getNomorBelumDijawab() {
            return NOMOR_SOAL_LIST.filter(no => {
                return !document.querySelector(`input[name="jawaban_papi[${no}]"]:checked`);
            });
        }

        document.getElementById('btn-submit').addEventListener('click', function () {
            const belum = getNomorBelumDijawab();

            if (belum.length === 0) {
                if (confirm('Kirim jawaban sekarang? Pastikan semua soal telah terisi.')) {
                    localStorage.removeItem(STORAGE_KEY);
                    clearInterval(timerInterval);
                    document.getElementById('form-soal').submit();
                }
                return;
            }

            tampilkanModal(belum);
        });

        function tampilkanModal(belum) {
            const list = document.getElementById('nomor-belum-list');
            const sub  = document.getElementById('modal-sub-text');

            list.innerHTML = '';
            sub.textContent = `${belum.length} soal belum dijawab. Klik nomor untuk langsung menuju soal tersebut.`;

            belum.forEach(no => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'nomor-btn';
                btn.textContent = no;
                btn.title = `Pergi ke soal nomor ${no}`;
                btn.addEventListener('click', () => scrollKeNomor(no));
                list.appendChild(btn);
            });

            document.getElementById('modal-belum-jawab').classList.add('show');
        }

        function scrollKeNomor(no) {
            tutupModal();
            const el = document.getElementById(`soal-item-${no}`);
            if (!el) return;

            const headerH = document.querySelector('header').offsetHeight + 16;
            const top = el.getBoundingClientRect().top + window.scrollY - headerH;
            window.scrollTo({ top, behavior: 'smooth' });

            el.classList.remove('question-highlight');
            void el.offsetWidth;
            el.classList.add('question-highlight');
        }

        function tutupModal() {
            document.getElementById('modal-belum-jawab').classList.remove('show');
        }

        document.getElementById('modal-close-btn').addEventListener('click', tutupModal);

        document.getElementById('modal-belum-jawab').addEventListener('click', function (e) {
            if (e.target === this) tutupModal();
        });

        document.getElementById('modal-force-submit-btn').addEventListener('click', function () {
            if (confirm('Yakin ingin mengirim jawaban meskipun ada soal yang belum diisi?')) {
                tutupModal();
                localStorage.removeItem(STORAGE_KEY);
                clearInterval(timerInterval);
                document.getElementById('form-soal').submit();
            }
        });

        document.getElementById('form-soal').addEventListener('submit', function() {
            localStorage.removeItem(STORAGE_KEY);
            clearInterval(timerInterval);
        });

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