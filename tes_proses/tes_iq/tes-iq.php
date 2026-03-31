<?php
include '../../backend/auth_check.php';
require_once '../../backend/config.php';
require_once '../../backend/biodata_check.php';

$nip  = $_SESSION['nip'] ?? '';
$nama = $_SESSION['nama'] ?? 'Peserta';

$v_engine = @filemtime(__DIR__ . '/js/engine.js') ?: time();
$v_ui = @filemtime(__DIR__ . '/js/ui.js') ?: time();
$v_security = @filemtime(__DIR__ . '/js/security.js') ?: time();
$v_timer = @filemtime(__DIR__ . '/js/timer.js') ?: time();

redirectJikaBiodataBelumLengkap($conn, $nip, '../../biodata.php');

// Cek apakah tes sudah selesai
$stmt = $conn->prepare("SELECT status FROM iq_test_sessions WHERE nip = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $nip);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if ($session && $session['status'] === 'finished') {
    // Sudah selesai — redirect ke dashboard dengan pesan
    header("Location: ../../dashboard.php?iq=sudah_selesai");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes 1 | PETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .bg-navy { background-color: #1a2b5e; }
        .text-navy { color: #1a2b5e; }
        .hover\:border-navy:hover { border-color: #1a2b5e; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-navy sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="../../images/logobps.png" alt="Logo BPS" class="h-10">
                <div>
                    <p id="section-title" class="text-white font-extrabold text-sm uppercase tracking-wide leading-tight">Memuat Tes...</p>
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">PETA — Pemetaan Potensi Pegawai</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-white font-bold text-sm leading-tight"><?= htmlspecialchars($nama) ?></p>
                <p class="text-blue-200 text-xs"><?= htmlspecialchars($nip) ?></p>
            </div>
        </div>
    </header>

    <div class="w-full bg-slate-200 h-1">
        <div id="progress-bar" class="bg-navy h-full transition-all duration-500 ease-out" style="width:0%"></div>
    </div>

    <div id="timer-box" class="hidden bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-2 flex items-center justify-end gap-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sisa Waktu</span>
            <span id="timer-display" class="text-2xl font-black text-navy font-mono tracking-tight">00:00</span>
        </div>
    </div>

    <main id="app-viewport" class="flex-1 flex items-center justify-center p-6">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-navy mx-auto"></div>
            <p class="mt-4 text-slate-500 font-semibold">Menyiapkan Perangkat Tes...</p>
        </div>
    </main>

    <script>
        const USER = { nip: "<?= $nip ?>", nama: "<?= $nama ?>" };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/security.js?v=<?= $v_security ?>"></script>
    <script src="js/timer.js?v=<?= $v_timer ?>"></script>
    <script src="js/ui.js?v=<?= $v_ui ?>"></script>
    <script src="js/engine.js?v=<?= $v_engine ?>"></script>

</body>
</html>