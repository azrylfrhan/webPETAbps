<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';
require_once '../backend/test_attempt_functions.php';

$nip = mysqli_real_escape_string($conn, $_GET['nip']);
$has_biodata = false;
$cek_biodata = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
if ($cek_biodata && mysqli_num_rows($cek_biodata) > 0) {
    $has_biodata = true;
}

$select_usia = $has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE()) AS usia" : "NULL AS usia";
$select_tempat_lahir = $has_biodata ? "b.tempat_lahir" : "NULL AS tempat_lahir";
$select_tanggal_lahir = $has_biodata ? "b.tanggal_lahir" : "NULL AS tanggal_lahir";
$select_email = $has_biodata ? "b.email" : "NULL AS email";
$join_biodata = $has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip COLLATE utf8mb4_unicode_ci = u.nip COLLATE utf8mb4_unicode_ci" : "";

$sql = "SELECT u.*, $select_usia, $select_tempat_lahir, $select_tanggal_lahir, $select_email
        FROM users u
        $join_biodata
        WHERE u.nip = '$nip' AND u.role = 'peserta'";

$query = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($query);

if (!$user) { header("Location: status_pegawai.php"); exit(); }

// Get all test attempts from unified table
$all_attempts = [];
$has_attempts_table = false;
$is_legacy_mode = false;

$cek_unified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($cek_unified && mysqli_num_rows($cek_unified) > 0) {
    $has_attempts_table = true;
    
    // Get all types
    $iq_attempts = getAttemptHistoryGeneric($conn, 'iq', $nip);
    $papi_attempts = getAttemptHistoryGeneric($conn, 'papi', $nip);
    $msdt_attempts = getAttemptHistoryGeneric($conn, 'msdt', $nip);
    
    // Add test_type identifier and combine
    foreach ($iq_attempts as $attempt) {
        $attempt['test_type'] = 'iq';
        $all_attempts[] = $attempt;
    }
    foreach ($papi_attempts as $attempt) {
        $attempt['test_type'] = 'papi';
        $all_attempts[] = $attempt;
    }
    foreach ($msdt_attempts as $attempt) {
        $attempt['test_type'] = 'msdt';
        $all_attempts[] = $attempt;
    }
    
    // Sort by date descending
    usort($all_attempts, function($a, $b) {
        return strtotime($b['tanggal_mulai']) - strtotime($a['tanggal_mulai']);
    });
} else {
    $is_legacy_mode = true;

    $legacy_iq = mysqli_query($conn, "SELECT id, skor, tanggal FROM iq_results WHERE user_id = '$nip' ORDER BY tanggal DESC, id DESC");
    $legacy_iq_attempt = 0;
    if ($legacy_iq) {
        while ($row = mysqli_fetch_assoc($legacy_iq)) {
            $legacy_iq_attempt++;
            $all_attempts[] = [
                'attempt_id' => null,
                'attempt_number' => $legacy_iq_attempt,
                'tanggal_mulai' => $row['tanggal'] ?: date('Y-m-d H:i:s'),
                'tanggal_selesai' => $row['tanggal'] ?: date('Y-m-d H:i:s'),
                'alasan_tes' => null,
                'status' => 'finished',
                'test_type' => 'iq',
                'skor_total' => (int)($row['skor'] ?? 0)
            ];
        }
    }

    $legacy_msdt = mysqli_query($conn, "SELECT * FROM hasil_msdt WHERE nip = '$nip' ORDER BY tanggal_tes DESC, id DESC");
    $legacy_msdt_attempt = 0;
    if ($legacy_msdt) {
        while ($row = mysqli_fetch_assoc($legacy_msdt)) {
            $legacy_msdt_attempt++;
            $all_attempts[] = [
                'attempt_id' => null,
                'attempt_number' => $legacy_msdt_attempt,
                'tanggal_mulai' => $row['tanggal_tes'] ?: date('Y-m-d H:i:s'),
                'tanggal_selesai' => $row['tanggal_tes'] ?: date('Y-m-d H:i:s'),
                'alasan_tes' => null,
                'status' => 'finished',
                'test_type' => 'msdt',
                'TO_score' => (int)($row['TO_score'] ?? 0),
                'RO_score' => (int)($row['RO_score'] ?? 0),
                'E_score' => (int)($row['E_score'] ?? 0),
                'O_score' => (int)($row['O_score'] ?? 0)
            ];
        }
    }

    $legacy_papi = mysqli_query($conn, "SELECT * FROM hasil_papi WHERE nip = '$nip' ORDER BY tanggal_tes DESC, id DESC");
    $legacy_papi_attempt = 0;
    if ($legacy_papi) {
        while ($row = mysqli_fetch_assoc($legacy_papi)) {
            $legacy_papi_attempt++;
            $all_attempts[] = [
                'attempt_id' => null,
                'attempt_number' => $legacy_papi_attempt,
                'tanggal_mulai' => $row['tanggal_tes'] ?: date('Y-m-d H:i:s'),
                'tanggal_selesai' => $row['tanggal_tes'] ?: date('Y-m-d H:i:s'),
                'alasan_tes' => null,
                'status' => 'finished',
                'test_type' => 'papi',
                'G' => (int)($row['G'] ?? 0), 'L' => (int)($row['L'] ?? 0), 'I' => (int)($row['I'] ?? 0),
                'T' => (int)($row['T'] ?? 0), 'V' => (int)($row['V'] ?? 0), 'S' => (int)($row['S'] ?? 0),
                'R' => (int)($row['R'] ?? 0), 'D' => (int)($row['D'] ?? 0), 'C' => (int)($row['C'] ?? 0),
                'E' => (int)($row['E'] ?? 0), 'N' => (int)($row['N'] ?? 0), 'A' => (int)($row['A'] ?? 0),
                'P' => (int)($row['P'] ?? 0), 'X' => (int)($row['X'] ?? 0), 'B' => (int)($row['B'] ?? 0),
                'O' => (int)($row['O'] ?? 0), 'K' => (int)($row['K'] ?? 0), 'F' => (int)($row['F'] ?? 0),
                'W' => (int)($row['W'] ?? 0), 'Z' => (int)($row['Z'] ?? 0)
            ];
        }
    }

    usort($all_attempts, function($a, $b) {
        return strtotime($b['tanggal_mulai']) - strtotime($a['tanggal_mulai']);
    });
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Detail Pegawai | Admin BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #notification-modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center; }
        #notification-modal.show { display: flex; }
        .notification-box { background: white; border-radius: 16px; padding: 32px; max-width: 480px; width: 90%; box-shadow: 0 20px 50px rgba(15,30,60,0.3); animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .notification-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 16px; }
        .notification-icon.success { background: #dcfce7; color: #16a34a; }
        .notification-icon.error { background: #fee2e2; color: #dc2626; }
        .notification-icon.info { background: #dbeafe; color: #2563eb; }
        .notification-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 12px 0 8px; text-align: center; }
        .notification-message { font-size: 14px; color: #64748b; text-align: center; margin-bottom: 24px; line-height: 1.5; white-space: pre-wrap; }
        .notification-buttons { display: flex; gap: 12px; justify-content: center; }
        .notification-btn { padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .notification-btn.primary { background: #0f1e3c; color: white; }
        .notification-btn.primary:hover { opacity: 0.9; }
        .notification-btn.secondary { background: #e2e8f0; color: #64748b; }
        .notification-btn.secondary:hover { background: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">
    <div class="w-full">
        <a href="status_pegawai.php" class="inline-flex items-center text-sm text-slate-500 hover:text-navy mb-6 transition-colors">
            ← Kembali ke Daftar
        </a>

        <?php if(isset($_GET['new_pass'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-emerald-800 text-sm font-bold">Password berhasil di-reset!</p>
                <p class="text-emerald-600 text-xs">Berikan password ini kepada pegawai: <span class="bg-white px-2 py-1 rounded border font-mono text-lg select-all"><?= htmlspecialchars($_GET['new_pass']) ?></span></p>
            </div>
            <button onclick="navigator.clipboard.writeText('<?= $_GET['new_pass'] ?>')" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg font-bold">Salin</button>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-navy p-6 text-white">
                <h2 class="text-xl font-bold">Profil Lengkap Pegawai</h2>
                <p class="text-slate-400 text-sm mt-1">NIP: <?= $user['nip'] ?></p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <p class="text-slate-700 font-semibold border-b border-slate-100 pb-2 mt-1"><?= htmlspecialchars($user['nama']) ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Unit Kerja</label>
                        <p class="text-slate-700 font-semibold border-b border-slate-100 pb-2 mt-1"><?= htmlspecialchars($user['satuan_kerja']) ?></p>
                    </div>
                </div>

                <div class="mb-10 bg-sky-50 rounded-xl p-6 border border-sky-100">
                    <div class="mb-4 pb-3 border-b border-sky-200">
                        <h3 class="text-sm font-bold text-slate-700">Biodata Peserta</h3>
                        <p class="text-xs text-slate-500 mt-1">Usia dihitung otomatis dari tanggal lahir dan tanggal hari ini.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tempat Lahir</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['tempat_lahir'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Lahir</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= !empty($user['tanggal_lahir']) ? date('d/m/Y', strtotime($user['tanggal_lahir'])) : '-' ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Usia (Otomatis)</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= isset($user['usia']) && $user['usia'] !== null ? (int)$user['usia'] . ' tahun' : '-' ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                        <p class="text-slate-700 font-semibold mt-2 break-all"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                    </div>
                    </div>

                    <?php if (empty($user['tanggal_lahir']) && empty($user['email']) && empty($user['tempat_lahir'])): ?>
                    <div class="mt-5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        Biodata belum diisi oleh peserta.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Jabatan & Pangkat Section -->
                <div class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 rounded-xl p-6 border border-slate-200">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</label>
                        <p id="jabatan-display" class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['jabatan'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pangkat/Golongan</label>
                        <p id="pangkat-display" class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['pangkat_golongan'] ?? '-') ?></p>
                    </div>
                    <div class="col-span-2 pt-4 border-t border-slate-200">
                        <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                            ✏️ Edit Informasi
                        </button>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                    <h3 class="text-amber-800 font-bold text-sm mb-1">Reset Akses Akun</h3>
                    <p class="text-amber-700/70 text-xs mb-4">Sistem akan membuatkan password acak baru secara otomatis.</p>
                    
                    <form action="proses_reset_pass.php" method="POST" onsubmit="confirmResetPassword(this); return false;">
                        <input type="hidden" name="nip" value="<?= $user['nip'] ?>">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                            Reset Password
                        </button>
                    </form>
                </div>

                <!-- Unified Test Attempts History Section -->
                <div class="mt-8 bg-white rounded-xl p-6 border border-slate-200">
                    <h3 class="text-2xl font-bold text-navy">Daftar Hasil Tes Pegawai</h3>
                    <p class="text-sm text-slate-500 mt-2 mb-5">Setiap percobaan tes tersimpan sebagai baris baru dan tidak menimpa riwayat sebelumnya.</p>

                    <?php if ($is_legacy_mode): ?>
                        <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4">
                            ⚠️ Menampilkan mode kompatibilitas (data legacy). Klik tombol tes untuk membuka jawaban pada halaman hasil tes yang sesuai.
                        </div>
                    <?php endif; ?>

                    <?php if (empty($all_attempts)): ?>
                        <div class="text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                            Belum ada percobaan tes untuk pegawai ini.
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Tes</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Alasan Mengikuti</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tes 1</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tes 2 Bag. 1</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tes 2 Bag. 2</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <?php foreach ($all_attempts as $idx => $attempt): ?>
                                    <?php
                                        $isFinished = ($attempt['status'] === 'finished');
                                        $scoreIq = ($attempt['skor_total'] ?? null);
                                        $scoreMsdt = ($attempt['TO_score'] ?? null);
                                        $scorePapi = null;
                                        if ($attempt['test_type'] === 'papi') {
                                            $sum = 0;
                                            foreach (['G','L','I','T','V','S','R','D','C','E','N','A','P','X','B','O','K','F','W','Z'] as $k) {
                                                $sum += (int)($attempt[$k] ?? 0);
                                            }
                                            $scorePapi = $sum;
                                        }
                                    ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-4 py-4 text-slate-500 font-semibold"><?= $idx + 1 ?></td>
                                        <td class="px-4 py-4 text-slate-700 font-semibold whitespace-nowrap">
                                            <?= date('d/m/Y H:i', strtotime($attempt['tanggal_mulai'])) ?>
                                        </td>
                                        <td class="px-4 py-4 text-slate-700 leading-relaxed">
                                            <?= !empty($attempt['alasan_tes']) ? nl2br(htmlspecialchars($attempt['alasan_tes'])) : '<span class="text-slate-400">-</span>' ?>
                                        </td>

                                        <!-- TES 1 (IQ) -->
                                        <td class="px-4 py-4 text-center">
                                            <?php if ($attempt['test_type'] === 'iq'): ?>
                                                <?php if ($isFinished): ?>
                                                    <?php if (!empty($attempt['attempt_id'])): ?>
                                                        <a href="lihat_jawaban_tes.php?attempt_id=<?= (int)$attempt['attempt_id'] ?>&test_type=iq&nip=<?= urlencode($nip) ?>&title=<?= urlencode('Percobaan #' . $attempt['attempt_number']) ?>"
                                                           class="inline-flex items-center gap-1 text-xs bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-full font-bold transition-all">
                                                            📊 Lihat Jawaban & Skor
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="lihat_jawaban_tes.php?test_type=iq&nip=<?= urlencode($nip) ?>&title=<?= urlencode('Riwayat IQ') ?>"
                                                           class="inline-flex items-center gap-1 text-xs bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-full font-bold transition-all">
                                                            📊 Lihat Jawaban & Skor
                                                        </a>
                                                    <?php endif; ?>
                                                    <p class="text-[11px] text-slate-500 mt-1">Skor: <?= $scoreIq !== null ? (int)$scoreIq : '-' ?></p>
                                                <?php else: ?>
                                                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full font-bold">Sedang Berjalan</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-xs bg-slate-100 text-slate-400 px-3 py-1.5 rounded-full font-bold">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- TES 2 BAG. 1 (MSDT) -->
                                        <td class="px-4 py-4 text-center">
                                            <?php if ($attempt['test_type'] === 'msdt'): ?>
                                                <?php if ($isFinished): ?>
                                                    <?php if (!empty($attempt['attempt_id'])): ?>
                                                        <a href="hasil_msdt.php?nip=<?= urlencode($nip) ?>"
                                                           class="inline-flex items-center gap-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1.5 rounded-full font-bold transition-all">
                                                            📊 Lihat Jawaban & Skor
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 text-xs bg-slate-100 text-slate-400 px-3 py-1.5 rounded-full font-bold">Riwayat lama</span>
                                                    <?php endif; ?>
                                                    <p class="text-[11px] text-slate-500 mt-1">TO: <?= $scoreMsdt !== null ? (int)$scoreMsdt : '-' ?></p>
                                                <?php else: ?>
                                                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full font-bold">Sedang Berjalan</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-xs bg-slate-100 text-slate-400 px-3 py-1.5 rounded-full font-bold">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- TES 2 BAG. 2 (PAPI) -->
                                        <td class="px-4 py-4 text-center">
                                            <?php if ($attempt['test_type'] === 'papi'): ?>
                                                <?php if ($isFinished): ?>
                                                    <?php if (!empty($attempt['attempt_id'])): ?>
                                                        <a href="hasil_papi.php?nip=<?= urlencode($nip) ?>"
                                                           class="inline-flex items-center gap-1 text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1.5 rounded-full font-bold transition-all">
                                                            📊 Lihat Jawaban & Skor
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 text-xs bg-slate-100 text-slate-400 px-3 py-1.5 rounded-full font-bold">Riwayat lama</span>
                                                    <?php endif; ?>
                                                    <p class="text-[11px] text-slate-500 mt-1">Total: <?= $scorePapi !== null ? (int)$scorePapi : '-' ?></p>
                                                <?php else: ?>
                                                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full font-bold">Sedang Berjalan</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-xs bg-slate-100 text-slate-400 px-3 py-1.5 rounded-full font-bold">Belum Ada</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Jawaban -->
<div id="answersModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-navy p-6 text-white sticky top-0 z-10">
            <h3 class="text-lg font-bold" id="answersModalTitle">Jawaban Tes</h3>
            <button onclick="closeAnswersModal()" class="absolute top-6 right-6 text-white hover:text-gray-200 text-2xl">×</button>
        </div>
        
        <div id="answersContent" class="p-6">
            <!-- Answers will be loaded here -->
        </div>
    </div>
</div>

<!-- Modal Edit Informasi -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-navy p-6 text-white">
            <h3 class="text-lg font-bold">Edit Informasi Pegawai</h3>
            <p class="text-slate-400 text-sm mt-1">NIP: <?= $user['nip'] ?></p>
        </div>
        
        <form id="editForm" method="POST" action="proses_edit_pegawai.php" class="p-6 space-y-5">
            <input type="hidden" name="nip" value="<?= $user['nip'] ?>">
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Nama Lengkap</label>
                <input type="text" 
                       name="nama" 
                       id="nama-input"
                       value="<?= htmlspecialchars($user['nama'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="Masukkan nama lengkap">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Unit Kerja</label>
                <input type="text" 
                       name="satuan_kerja" 
                       id="satuan_kerja-input"
                       value="<?= htmlspecialchars($user['satuan_kerja'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., BPS Sulawesi Utara">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Jabatan</label>
                <input type="text" 
                       name="jabatan" 
                       id="jabatan-input"
                       value="<?= htmlspecialchars($user['jabatan'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., Statistisi Muda">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Pangkat/Golongan</label>
                <input type="text" 
                       name="pangkat" 
                       id="pangkat-input"
                       value="<?= htmlspecialchars($user['pangkat_golongan'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., III/b">
            </div>
            
            <div class="pt-4 border-t border-slate-200 flex gap-3">
                <button type="button" 
                        onclick="closeEditModal()" 
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal ketika klik di luar modal
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Handle form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('proses_edit_pegawai.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display values
            document.querySelector('.grid.grid-cols-2.gap-6.mb-10 > div:nth-child(1) > p').innerText = data.nama;
            document.querySelector('.grid.grid-cols-2.gap-6.mb-10 > div:nth-child(2) > p').innerText = data.satuan_kerja;
            document.getElementById('jabatan-display').innerText = data.jabatan;
            document.getElementById('pangkat-display').innerText = data.pangkat;
            closeEditModal();
            
            // Show success notification
            showNotification('✓ Data berhasil diperbarui!', 'success');
        } else {
            showNotification('❌ ' + (data.error || 'Gagal menyimpan data'), 'error');
        }
    })
    .catch(error => {
        showNotification('❌ Terjadi kesalahan: ' + error, 'error');
    });
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-6 right-6 px-6 py-3 rounded-lg text-white font-semibold shadow-lg z-50 animate-pulse
        ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`;
    notification.innerText = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal dengan ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeAnswersModal();
    }
});

// Functions for Answers Modal
function showAttemptAnswers(attemptId, testType, attemptTitle) {
    const badge = testType === 'iq' ? '🧠 IQ' : (testType === 'papi' ? '🧩 PAPI' : '🎭 MSDT');
    document.getElementById('answersModalTitle').innerText = '📋 ' + badge + ' - ' + attemptTitle;
    document.getElementById('answersContent').innerHTML = '<p class="text-center text-slate-500">Memuat jawaban...</p>';
    document.getElementById('answersModal').classList.remove('hidden');

    // Fetch answers from server
    fetch('api/get_attempt_answers.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            attempt_id: attemptId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<div class="space-y-4">';

            if (!data.answers || data.answers.length === 0) {
                html += '<div class="text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg p-4">Belum ada data jawaban tersimpan untuk attempt ini.</div>';
            } else if (data.test_type === 'iq') {
                data.answers.forEach((item) => {
                    const sectionName = item.section;
                    const isGe = sectionName === 'GE' || (item.norm_answers && item.norm_answers.length > 0);
                    const isCorrect = isGe
                        ? (item.matched_norms && item.matched_norms.length > 0)
                        : (item.user_answer && item.correct_answer && item.user_answer.trim().toUpperCase() === item.correct_answer.trim().toUpperCase());

                    const userAnswerDisplay = item.user_answer || '(tidak dijawab)';
                    const normBadge = isGe && item.norm_answers && item.norm_answers.length
                        ? item.norm_answers.map((norm, idx) => {
                            const matched = item.matched_norms && item.matched_norms.some(m => String(m.jawaban).trim().toLowerCase() === String(norm).trim().toLowerCase());
                            return `<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-semibold ${matched ? 'bg-green-100 border-green-300 text-green-700' : 'bg-white border-green-200 text-green-700'}">${norm}${item.norm_values && item.norm_values[idx] !== undefined ? `<span class="text-green-500">•</span><span>Nilai ${item.norm_values[idx]}</span>` : ''}</span>`;
                        }).join(' ')
                        : '';

                    html += `
                        <div class="border border-slate-200 rounded-lg p-3 ${isCorrect ? 'bg-green-50 border-green-300' : 'bg-slate-50'}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-bold text-slate-600 uppercase">${sectionName} - Soal ${item.question_number}</p>
                                    <p class="text-sm text-slate-700 mt-1">${item.question_text || 'Soal tidak ditemukan'}</p>
                                </div>
                                ${isCorrect ? '<span class="text-xs bg-green-500 text-white px-2 py-1 rounded font-bold">✓ Benar</span>' :
                                  item.user_answer ? '<span class="text-xs bg-red-500 text-white px-2 py-1 rounded font-bold">✗ Salah</span>' :
                                  '<span class="text-xs bg-gray-400 text-white px-2 py-1 rounded font-bold">- Tidak Dijawab</span>'}
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <p class="text-slate-600 font-semibold">Jawaban Anda:</p>
                                    <p class="text-slate-800 font-mono bg-white px-2 py-1 rounded border border-slate-200 mt-1">${userAnswerDisplay}</p>
                                </div>
                                <div>
                                    <p class="text-slate-600 font-semibold">Jawaban Benar:</p>
                                    ${isGe ? `<div class="mt-1 flex flex-wrap gap-2">${normBadge || '<span class="text-slate-500">(norma belum diisi)</span>'}</div>` : `<p class="text-emerald-800 font-mono bg-white px-2 py-1 rounded border border-emerald-300 mt-1">${item.correct_answer || '-'}</p>`}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                data.answers.forEach((item) => {
                    const answerBadge = item.user_answer
                        ? `<span class="text-xs bg-blue-500 text-white px-2 py-1 rounded font-bold">${item.user_answer}</span>`
                        : '<span class="text-xs bg-gray-400 text-white px-2 py-1 rounded font-bold">-</span>';

                    html += `
                        <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-xs font-bold text-slate-600 uppercase">Soal ${item.question_number}</p>
                                ${answerBadge}
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                <div class="bg-white border border-slate-200 rounded p-2">
                                    <p class="text-slate-500 font-semibold mb-1">Pilihan A</p>
                                    <p class="text-slate-700">${item.option_a || '-'}</p>
                                </div>
                                <div class="bg-white border border-slate-200 rounded p-2">
                                    <p class="text-slate-500 font-semibold mb-1">Pilihan B</p>
                                    <p class="text-slate-700">${item.option_b || '-'}</p>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-slate-600">
                                <span class="font-semibold">Jawaban dipilih:</span> ${item.user_answer || '(tidak dijawab)'}
                                ${data.test_type === 'papi' ? `<span class="ml-2"><span class="font-semibold">Dimensi:</span> ${item.mapped_dimension || '-'}</span>` : ''}
                            </div>
                        </div>
                    `;
                });
            }
            
            html += '</div>';
            document.getElementById('answersContent').innerHTML = html;
        } else {
            document.getElementById('answersContent').innerHTML = '<p class="text-red-600 text-sm">Gagal memuat jawaban: ' + (data.message || 'Unknown error') + '</p>';
        }
    })
    .catch(error => {
        document.getElementById('answersContent').innerHTML = '<p class="text-red-600 text-sm">Terjadi kesalahan: ' + error + '</p>';
    });
}

function closeAnswersModal() {
    document.getElementById('answersModal').classList.add('hidden');
}

// Close answers modal when clicking outside
document.getElementById('answersModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAnswersModal();
    }
});

// Notification Modal Function
function showNotification(title, message, type = 'info', isConfirm = false) {
    return new Promise((resolve) => {
        const modal = document.getElementById('notification-modal');
        if (!modal) {
            console.error('Notification modal not found');
            resolve(false);
            return;
        }

        document.getElementById('notification-title').textContent = title;
        document.getElementById('notification-message').textContent = message;

        const iconEl = document.getElementById('notification-icon');
        const iconMap = { success: '✓', error: '✕', info: 'ℹ' };
        iconEl.textContent = iconMap[type] || '✓';
        const bgColorMap = { success: '#dcfce7', error: '#fee2e2', info: '#dbeafe' };
        const textColorMap = { success: '#16a34a', error: '#dc2626', info: '#2563eb' };
        iconEl.style.background = bgColorMap[type] || '#dbeafe';
        iconEl.style.color = textColorMap[type] || '#2563eb';

        const yesBtn = document.getElementById('notification-yes');
        const noBtn = document.getElementById('notification-no');
        const okBtn = document.getElementById('notification-ok');

        if (isConfirm) {
            okBtn.style.display = 'none';
            yesBtn.style.display = 'inline-block';
            noBtn.style.display = 'inline-block';
        } else {
            okBtn.style.display = 'inline-block';
            yesBtn.style.display = 'none';
            noBtn.style.display = 'none';
        }

        const newYesBtn = yesBtn.cloneNode(true);
        const newNoBtn = noBtn.cloneNode(true);
        const newOkBtn = okBtn.cloneNode(true);

        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
        noBtn.parentNode.replaceChild(newNoBtn, noBtn);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);

        newYesBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(true);
        });
        newNoBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(false);
        });
        newOkBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(true);
        });

        modal.style.display = 'flex';
    });
}

async function confirmResetPassword(form) {
    const confirmed = await showNotification('Reset Password', 'Generate password acak baru untuk pegawai ini?', 'info', true);
    if (confirmed) {
        form.submit();
    }
    return false;
}

</script>

<!-- Notification Modal -->
<div id="notification-modal">
    <div class="notification-box">
        <div id="notification-icon" class="notification-icon" style="background: #dbeafe; color: #2563eb;">ℹ</div>
        <h2 id="notification-title" class="notification-title">Judul</h2>
        <p id="notification-message" class="notification-message">Pesan</p>
        <div class="notification-buttons">
            <button id="notification-ok" type="button" class="notification-btn primary">OK</button>
            <button id="notification-yes" type="button" class="notification-btn primary" style="display: none;">Ya</button>
            <button id="notification-no" type="button" class="notification-btn secondary" style="display: none;">Tidak</button>
        </div>
    </div>
</div>

</body>
</html>