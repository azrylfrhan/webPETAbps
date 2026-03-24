<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';

$nip = mysqli_real_escape_string($conn, $_GET['nip']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE nip = '$nip' AND role = 'peserta'");
$user = mysqli_fetch_assoc($query);

if (!$user) { header("Location: status_pegawai.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pegawai | Admin BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">
    <div class="max-w-3xl mx-auto">
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

                <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                    <h3 class="text-amber-800 font-bold text-sm mb-1">Reset Akses Akun</h3>
                    <p class="text-amber-700/70 text-xs mb-4">Sistem akan membuatkan password acak baru secara otomatis.</p>
                    
                    <form action="proses_reset_pass.php" method="POST" onsubmit="return confirm('Generate password acak baru untuk pegawai ini?');">
                        <input type="hidden" name="nip" value="<?= $user['nip'] ?>">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>