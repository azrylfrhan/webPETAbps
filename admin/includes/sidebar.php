<?php
// Deteksi halaman aktif
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-[260px] min-h-screen bg-[#0F1E3C] text-white fixed top-0 left-0 flex flex-col z-50">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5">
        <img src="../images/logobps.png"
             alt="Logo BPS"
             class="h-10 w-auto object-contain flex-shrink-0">
        <div>
            <p class="text-sm font-bold leading-tight text-white">PETA — Pemetaan Potensi Pegawai</p>
            <p class="text-[11px] text-slate-400">Sulawesi Utara</p>
        </div>
    </div>

    <!-- Nav Label -->
    <p class="px-5 pt-5 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Menu Utama</p>

    <!-- Navigation -->
    <nav class="flex-1 px-3 space-y-0.5">
        <a href="index.php"
           class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200
                  <?= $current == 'index.php' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-900/40' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <span>🏠</span> Dashboard
        </a>
        
        <a href="status_pegawai.php"
           class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200
                  <?= $current == 'status_pegawai.php' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-900/40' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <span>👥</span> Data Pegawai
        </a>

        <a href="hasil_peserta.php"
           class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200
                  <?= $current == 'hasil_peserta.php' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-900/40' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
            <span>📄</span> Hasil Tes
        </a>

        <p class="px-4 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Pengaturan</p>

        <a href="../logout.php"
           class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200 text-red-400 hover:text-white hover:bg-red-500/20">
            <span>🚪</span> Logout
        </a>
    </nav>

    <!-- Footer User Info -->
    <a href="profile.php" class="px-5 py-4 border-t border-white/5 flex items-center gap-3 hover:bg-white/5 transition-all group">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-xs font-bold flex-shrink-0 text-white shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <?= isset($_SESSION['nama']) ? strtoupper(substr($_SESSION['nama'], 0, 1)) : 'A' ?>
        </div>
        <div class="overflow-hidden">
            <p class="text-xs font-semibold text-white truncate">
                <?= $_SESSION['nama'] ?? 'Administrator' ?>
            </p>
            <p class="text-[10px] text-slate-500 group-hover:text-blue-400 transition-colors">Lihat Profil & Keamanan</p>
        </div>
    </a>

</aside>