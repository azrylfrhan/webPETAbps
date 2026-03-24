<?php
include '../backend/auth_check.php';
require_once '../backend/config.php';

$nip_sess = $_SESSION['nip'];
$message = "";
$status = "";

// 1. AMBIL DATA ADMIN TERBARU
$query_admin = "SELECT nama, nip, satuan_kerja, jabatan FROM users WHERE nip = ?";
$stmt_admin = $conn->prepare($query_admin);
$stmt_admin->bind_param("s", $nip_sess);
$stmt_admin->execute();
$data_admin = $stmt_admin->get_result()->fetch_assoc();

$nama = $data_admin['nama'];
$nip = $data_admin['nip'];
$satker = $data_admin['satuan_kerja'] ?? 'Badan Pusat Statistik';
$jabatan = $data_admin['jabatan'] ?? 'Administrator'; // Default jika kosong

// 2. PROSES UPDATE PASSWORD
if (isset($_POST['update_password'])) {
    $pass_baru = $_POST['new_password'];
    $konfirmasi = $_POST['confirm_password'];

    if (strlen($pass_baru) < 6) {
        $message = "Password minimal 6 karakter!";
        $status = "error";
    } elseif ($pass_baru === $konfirmasi) {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        $query_up = "UPDATE users SET password = ? WHERE nip = ?";
        $stmt_up = $conn->prepare($query_up);
        $stmt_up->bind_param("ss", $hash, $nip);
        
        if ($stmt_up->execute()) {
            $message = "Password berhasil diperbarui!";
            $status = "success";
        } else {
            $message = "Gagal memperbarui database.";
            $status = "error";
        }
    } else {
        $message = "Konfirmasi password tidak cocok!";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - PETA BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { navy: { DEFAULT: '#0F1E3C', dark: '#081226' } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-wrapper input { width: 100%; padding-right: 3.5rem; }
        .btn-toggle {
            position: absolute; right: 0.75rem; height: 100%; display: flex;
            align-items: center; padding: 0 0.5rem; cursor: pointer;
            color: #94A3B8; border: none; background: transparent;
        }
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen text-slate-700">

<?php include 'includes/sidebar.php'; ?>

<main class="ml-[260px] flex-1 p-10">
    <div class="max-w-4xl mx-auto">
        
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold text-navy tracking-tight">Profil Pengguna</h1>
            <p class="text-slate-500 mt-1">Kelola informasi data diri dan keamanan akun Anda.</p>
        </header>

        <?php if ($message): ?>
        <div class="mb-8 p-4 rounded-2xl font-bold text-sm flex items-center gap-3 shadow-sm <?= $status == 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>">
            <span><?= $status == 'success' ? '✅' : '⚠️' ?></span>
            <?= $message ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-indigo-500 text-white rounded-[2rem] flex items-center justify-center text-3xl font-black mx-auto mb-6 shadow-xl shadow-blue-200">
                        <?= strtoupper(substr($nama, 0, 1)) ?>
                    </div>
                    
                    <h2 class="text-xl font-extrabold text-navy leading-tight"><?= htmlspecialchars($nama) ?></h2>
                    <div class="mt-2 inline-block px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100">
                        <?= htmlspecialchars($jabatan) ?>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-slate-100 text-left space-y-5">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">NIP</span>
                            <span class="font-bold text-slate-700"><?= $nip ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Satuan Kerja</span>
                            <span class="font-bold text-slate-700 leading-tight"><?= htmlspecialchars($satker) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-navy flex items-center gap-2 text-sm uppercase tracking-wider">
                            🛡️ Keamanan Akun
                        </h3>
                    </div>

                    <form action="" method="POST" class="p-8 space-y-6">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Baru</label>
                                <div class="password-wrapper">
                                    <input type="password" id="new_password" name="new_password" required placeholder="Min. 6 Karakter" 
                                        class="p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm font-medium">
                                    <button type="button" class="btn-toggle" onclick="togglePass('new_password', this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="icon-svg" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Password Baru</label>
                                <div class="password-wrapper">
                                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password" 
                                        class="p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all text-sm font-medium">
                                    <button type="button" class="btn-toggle" onclick="togglePass('confirm_password', this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="icon-svg" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="update_password" 
                            class="w-full py-4 bg-navy text-white font-bold rounded-2xl hover:bg-navy-dark transition-all shadow-xl shadow-navy/20 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                            Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.icon-svg');
        if (input.type === "password") {
            input.type = "text";
            icon.setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18');
        } else {
            input.type = "password";
            icon.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
        }
    }
</script>
</body>
</html>