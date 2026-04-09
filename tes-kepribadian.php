<?php
require_once 'backend/config.php';
require_once 'backend/auth_check.php';
require_once 'backend/test_attempt_functions.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$nip  = $_SESSION['nip'];
$nama = $_SESSION['nama'];

$cek = mysqli_query($conn, "SELECT nip FROM hasil_msdt WHERE nip='$nip'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: dashboard.php?status=tes_selesai");
    exit;
}

// Ensure running attempt exists for MSDT
$attempt_msdt_id = null;
$stmtAttempt = $conn->prepare("SELECT id FROM test_attempts WHERE nip = ? AND test_type = 'msdt' AND status = 'running' ORDER BY tanggal_mulai DESC LIMIT 1");
$stmtAttempt->bind_param('s', $nip);
$stmtAttempt->execute();
$attemptRow = $stmtAttempt->get_result()->fetch_assoc();
$stmtAttempt->close();

if ($attemptRow) {
    $attempt_msdt_id = (int)$attemptRow['id'];
} else {
    $attempt_msdt_id = createTestAttemptGeneric($conn, 'msdt', $nip, 'Mulai Tes MSDT oleh peserta');
}

if ($attempt_msdt_id) {
    $_SESSION['current_attempt_id_msdt'] = $attempt_msdt_id;
}

$query  = "SELECT * FROM soal WHERE kode_tes = 'KEPRIBADIAN' ORDER BY nomor_soal ASC";
$result = mysqli_query($conn, $query);

$all_soal = [];
while ($row = mysqli_fetch_assoc($result)) $all_soal[] = $row;
$total = count($all_soal);

define('WAKTU_DETIK', 30 * 60); // 30 menit
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes 2 Bagian 1 | PETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .bg-navy { background-color: #0f1e3c; }
        .text-navy { color: #0f1e3c; }
        .hover\:border-navy:hover { border-color: #0f1e3c; }
        input[type="radio"] { accent-color: #1a2b5e; width: 16px; height: 16px; flex-shrink: 0; }
        .timer-warning { color: #dc2626 !important; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.6; } }
        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 30, 60, 0.07) 1px, transparent 0);
            background-size: 22px 22px;
        }

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
        /* ── Alert Modal ── */
        #notification-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        #notification-modal.show { display: flex; }
        .notification-box {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 480px;
            width: 100%;
            padding: 2rem;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .notification-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .notification-icon.success { background: #dcfce7; color: #15803d; }
        .notification-icon.error { background: #fee2e2; color: #b91c1c; }
        .notification-icon.info { background: #dbeafe; color: #0369a1; }
        .notification-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .notification-message {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            white-space: pre-wrap;
        }
        .notification-buttons {
            display: flex;
            gap: 0.75rem;
        }
        .notification-btn {
            flex: 1;
            padding: 0.625rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .notification-btn.primary {
            background: #0f1e3c;
            color: #fff;
        }
        .notification-btn.primary:hover { background: #1b3f74; }
        .notification-btn.secondary {
            background: #e2e8f0;
            color: #1e293b;
        }
        .notification-btn.secondary:hover { background: #cbd5e1; }

    </style>
    <script src="test_timer_alert.js"></script>
</head>
<body class="min-h-screen overflow-x-hidden flex flex-col">

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
            <div class="text-right">
                <p class="text-white font-bold text-sm leading-tight"><?= htmlspecialchars($nama) ?></p>
                <p class="text-blue-200 text-xs"><?= htmlspecialchars($nip) ?></p>
            </div>
        </div>
    </header>

    <!-- PROGRESS BAR -->
    <div class="w-full bg-slate-200 h-1">
        <div id="progress-bar" class="bg-navy h-full transition-all duration-500" style="width:0%"></div>
    </div>

    <div id="timer-box" class="hidden bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-2 flex items-center justify-end gap-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sisa Waktu</span>
            <span id="timer-display" class="text-2xl font-black text-navy font-mono tracking-tight">30:00</span>
        </div>
    </div>

    <main class="bg-grid flex-1 p-6">

        <!-- INSTRUKSI -->
        <section id="instruction-section" class="flex items-center justify-center min-h-[calc(100vh-5rem)]">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-2xl p-6 sm:p-10">
                <div class="mb-6 rounded-2xl bg-gradient-to-r from-[#0f1e3c] via-[#1b3f74] to-[#5b9df3] px-5 py-4 text-white">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-blue-100">Petunjuk Pengerjaan</p>
                    <h2 class="mt-1 text-2xl font-bold">Instruksi Tes 2 Bagian 1</h2>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-slate-600 leading-relaxed mb-6 space-y-3">
                    <p>Pada halaman-halaman berikut, Anda akan membaca sejumlah pernyataan mengenai tindakan yang mungkin Anda lakukan dalam tugas Anda di unit kerja.</p>
                    <p>Anda diminta untuk memilih pernyataan <strong>A</strong> atau <strong>B</strong> yang paling sesuai dengan diri Anda, atau paling mungkin Anda lakukan.</p>
                </div>
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-8">
                    <span class="text-blue-600 text-xl">⏱</span>
                    <p class="text-blue-700 text-sm font-semibold">Waktu pengerjaan: <strong>30 menit</strong>. Timer mulai saat Anda klik tombol di bawah.</p>
                </div>
                <button type="button" id="start-test-btn"
                    class="w-full bg-navy text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Mulai Kerjakan Soal
                </button>
            </div>
        </section>

        <!-- SOAL -->
        <section id="questions-section" class="hidden max-w-2xl mx-auto">
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
                 <div class="question-item bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-8 mb-6"
                     id="soal-item-<?= $no ?>"
                     data-nomor="<?= $i+1 ?>" data-no="<?= $no ?>" data-total="<?= $total ?>">
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

                <div class="mt-4 mb-16">
                    <button type="button" id="btn-submit"
                        class="w-full bg-navy text-white px-10 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                        Kirim Jawaban Bagian 1
                    </button>
                </div>

            </form>
        </section>

    </main>

    <!-- ══════════════════════════════════════════
         MODAL — Notifikasi (Alert/Confirm)
    ══════════════════════════════════════════ -->
    <div id="notification-modal" role="dialog" aria-modal="true">
        <div class="notification-box">
            <div class="notification-icon" id="notification-icon">ℹ️</div>
            <div class="notification-title" id="notification-title">Pesan</div>
            <div class="notification-message" id="notification-message">Pesan notifikasi</div>
            <div class="notification-buttons">
                <button type="button" class="notification-btn secondary" id="notification-btn-cancel" style="display:none;">
                    Batal
                </button>
                <button type="button" class="notification-btn primary" id="notification-btn-ok">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MODAL — Soal Belum Dijawab
    ══════════════════════════════════════════ -->
    <div id="modal-belum-jawab" role="dialog" aria-modal="true">
        <div class="modal-box">
            <!-- Icon + judul -->
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Ada Soal yang Belum Dijawab</h3>
            </div>
            <p class="text-slate-500 text-sm mb-1 ml-13 pl-0.5" id="modal-sub-text"></p>

            <!-- Daftar nomor soal belum dijawab -->
            <div id="nomor-belum-list" class="flex flex-wrap gap-2 mt-4 mb-6 max-h-48 overflow-y-auto pr-1"></div>

            <p class="text-xs text-slate-400 mb-5">Klik nomor di atas untuk langsung menuju soal tersebut, lalu jawab sebelum mengirim.</p>

            <!-- Tombol aksi -->
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
        const STORAGE_KEY = 'peta_tes2b1_timer_<?= $nip ?>';
        // Kumpulkan nomor soal dari PHP
        const NOMOR_SOAL_LIST = <?= json_encode(array_column($all_soal, 'nomor_soal')) ?>;

        let timerInterval = null;
        let tesAktif = false;
        let notificationResolve = null;
        let notificationType = 'alert'; // 'alert' atau 'confirm'

        // ── NOTIFICATION MODAL ──
        function showNotification(title, message, type = 'info', isConfirm = false) {
            return new Promise((resolve) => {
                const modal = document.getElementById('notification-modal');
                const icon = document.getElementById('notification-icon');
                const titleEl = document.getElementById('notification-title');
                const msgEl = document.getElementById('notification-message');
                const btnOk = document.getElementById('notification-btn-ok');
                const btnCancel = document.getElementById('notification-btn-cancel');

                titleEl.textContent = title;
                msgEl.textContent = message;

                // Set icon
                if (type === 'success') {
                    icon.className = 'notification-icon success';
                    icon.textContent = '✓';
                } else if (type === 'error') {
                    icon.className = 'notification-icon error';
                    icon.textContent = '✕';
                } else {
                    icon.className = 'notification-icon info';
                    icon.textContent = 'ℹ';
                }

                // Set button visibility
                if (isConfirm) {
                    btnCancel.style.display = '';
                    btnOk.textContent = 'Ya';
                } else {
                    btnCancel.style.display = 'none';
                    btnOk.textContent = 'OK';
                }

                // Clear old listeners
                const newOk = btnOk.cloneNode(true);
                const newCancel = btnCancel.cloneNode(true);
                btnOk.parentNode.replaceChild(newOk, btnOk);
                btnCancel.parentNode.replaceChild(newCancel, btnCancel);

                document.getElementById('notification-btn-ok').addEventListener('click', () => {
                    modal.classList.remove('show');
                    resolve(true);
                });

                document.getElementById('notification-btn-cancel').addEventListener('click', () => {
                    modal.classList.remove('show');
                    resolve(false);
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('show');
                        resolve(false);
                    }
                }, { once: true });

                modal.classList.add('show');
            });
        }

        function enableFullscreen() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) elem.requestFullscreen();
            else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
            else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
        }

        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('keydown', function(e) {
            const key = String(e.key || '').toLowerCase();
            if (e.key === 'F12') { e.preventDefault(); return false; }
            if (e.ctrlKey && ['u', 's', 'p'].includes(key)) { e.preventDefault(); return false; }
            if (e.ctrlKey && e.shiftKey && ['i', 'j', 'c', 's'].includes(key)) { e.preventDefault(); return false; }
            if (e.key === 'PrintScreen') { e.preventDefault(); return false; }
            if (e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)) { e.preventDefault(); return false; }
        });

        // ── START BUTTON ──
        document.getElementById('start-test-btn').addEventListener('click', function () {
            document.getElementById('instruction-section').style.display = 'none';
            document.getElementById('questions-section').classList.remove('hidden');
            document.getElementById('timer-box').classList.remove('hidden');
            window.scrollTo(0, 0);
            tesAktif = true;
            enableFullscreen();
            startTimer();
        });

        // ── TIMER ──
        function startTimer() {
            let saved = localStorage.getItem(STORAGE_KEY);
            let remaining = saved ? parseInt(saved) : WAKTU_DETIK;
            if (isNaN(remaining) || remaining <= 0) remaining = WAKTU_DETIK;
            if (window.TestTimerAlert) {
                window.TestTimerAlert.reset('msdt-main');
            }
            updateDisplay(remaining);

            timerInterval = setInterval(() => {
                remaining--;
                localStorage.setItem(STORAGE_KEY, remaining);
                updateDisplay(remaining);
                if (window.TestTimerAlert) {
                    window.TestTimerAlert.warn({
                        key: 'msdt-main',
                        remaining: remaining,
                        threshold: 300,
                        title: 'Waktu Hampir Habis',
                        message: 'Sisa waktu tinggal 5 menit. Periksa jawaban Anda sekarang.',
                        type: 'info'
                    });
                }

                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    localStorage.removeItem(STORAGE_KEY);
                    showNotification('Waktu Habis', 'Jawaban akan dikirim otomatis.', 'info').then(() => {
                        document.getElementById('form-soal').submit();
                    });
                }
            }, 1000);
        }

        function updateDisplay(seconds) {
            const m   = Math.floor(seconds / 60);
            const s   = seconds % 60;
            const el  = document.getElementById('timer-display');
            el.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (seconds <= 300) el.classList.add('timer-warning');
            else el.classList.remove('timer-warning');
        }

        // ── VALIDASI & SUBMIT ──
        function getNomorBelumDijawab() {
            return NOMOR_SOAL_LIST.filter(no => {
                return !document.querySelector(`input[name="jawaban[${no}]"]:checked`);
            });
        }

        document.getElementById('btn-submit').addEventListener('click', function () {
            const belum = getNomorBelumDijawab();

            if (belum.length === 0) {
                // Semua sudah dijawab — langsung submit
                showNotification('Konfirmasi', 'Kirim jawaban sekarang? Pastikan semua soal telah terisi.', 'info', true).then((confirmed) => {
                    if (confirmed) {
                        localStorage.removeItem(STORAGE_KEY);
                        clearInterval(timerInterval);
                        document.getElementById('form-soal').submit();
                    }
                });
                return;
            }

            // Ada yang belum — tampilkan modal
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

            // Scroll ke soal, dengan offset header
            const headerH = document.querySelector('header').offsetHeight + 16;
            const top = el.getBoundingClientRect().top + window.scrollY - headerH;
            window.scrollTo({ top, behavior: 'smooth' });

            // Beri highlight sementara
            el.classList.remove('question-highlight');
            void el.offsetWidth; // reflow untuk restart animasi
            el.classList.add('question-highlight');
        }

        function tutupModal() {
            document.getElementById('modal-belum-jawab').classList.remove('show');
        }

        document.getElementById('modal-close-btn').addEventListener('click', tutupModal);

        // Klik di luar modal box → tutup
        document.getElementById('modal-belum-jawab').addEventListener('click', function (e) {
            if (e.target === this) tutupModal();
        });

        // Kirim paksa (tidak lengkap)
        document.getElementById('modal-force-submit-btn').addEventListener('click', function () {
            showNotification('Konfirmasi', 'Yakin ingin mengirim jawaban meskipun ada soal yang belum diisi?', 'error', true).then((confirmed) => {
                if (confirmed) {
                    tutupModal();
                    localStorage.removeItem(STORAGE_KEY);
                    clearInterval(timerInterval);
                    document.getElementById('form-soal').submit();
                }
            });
        });

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