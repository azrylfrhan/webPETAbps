<?php
// Deteksi halaman aktif
$current = basename($_SERVER['PHP_SELF']);

$menuBaseClass = 'group relative flex items-center gap-3 rounded-xl px-4 py-2.5 text-[13.5px] font-medium transition-all duration-200';
$menuInactiveClass = 'text-slate-300 hover:text-white hover:bg-white/10';
$menuActiveClass = 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-900/40 ring-1 ring-blue-300/30';
?>

<style>
    :root {
        --admin-sidebar-width: 248px;
    }

    .admin-sidebar-shell {
        width: var(--admin-sidebar-width) !important;
    }

    @media (min-width: 1025px) {
        [class*="ml-[260px]"],
        [class*="ml-[272px]"] {
            margin-left: var(--admin-sidebar-width) !important;
        }
    }

    @media (max-width: 1024px) {
        .admin-sidebar-shell {
            position: relative !important;
            width: 100% !important;
            min-height: auto !important;
            border-right: 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: none !important;
        }

        .admin-sidebar-shell nav {
            max-height: none !important;
            overflow: visible !important;
        }

        [class*="ml-[260px]"],
        [class*="ml-[272px]"] {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 1rem !important;
        }

        .admin-sidebar-shell + * {
            min-width: 0 !important;
        }
    }

    @media (max-width: 640px) {
        .admin-sidebar-shell {
            border-radius: 0 !important;
        }

        .admin-sidebar-shell .menu-group-title {
            padding-left: 0.25rem;
        }

        [class*="ml-[260px]"],
        [class*="ml-[272px]"] {
            padding: 0.75rem !important;
        }
    }
</style>

<aside class="admin-sidebar-shell fixed top-0 left-0 z-50 flex min-h-screen w-[272px] flex-col border-r border-white/10 bg-gradient-to-b from-[#0F1E3C] via-[#102447] to-[#0b1833] text-white shadow-2xl">

    <!-- Logo -->
    <div class="border-b border-white/10 px-4 py-4">
        <div class="flex items-center gap-3">
        <img src="../images/logobps.png"
             alt="Logo BPS"
             class="h-9 w-auto object-contain flex-shrink-0">
        <div>
            <p class="text-[13px] font-bold leading-tight text-white">PETA — Pemetaan Potensi Pegawai</p>
                <p class="text-[10px] text-slate-400">Sulawesi Utara</p>
            </div>
        </div>
        <div class="mt-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-200/80">Panel Admin</p>
            <p class="mt-1 text-[11px] leading-relaxed text-slate-300">Kelola peserta, soal, dan hasil tes dalam satu tempat.</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-3">
        <div>
            <p class="menu-group-title px-2 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-500">Monitoring</p>
            <div class="space-y-1">
        <a href="index.php"
           class="<?= $menuBaseClass ?> <?= $current == 'index.php' ? $menuActiveClass : $menuInactiveClass ?>">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">DB</span>
                    <span>Dashboard</span>
        </a>
        
        <a href="status_pegawai.php"
           class="<?= $menuBaseClass ?> <?= $current == 'status_pegawai.php' ? $menuActiveClass : $menuInactiveClass ?>">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">PG</span>
                    <span>Data Pegawai</span>
        </a>

        <a href="hasil_peserta.php"
           class="<?= $menuBaseClass ?> <?= $current == 'hasil_peserta.php' ? $menuActiveClass : $menuInactiveClass ?>">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">HS</span>
                    <span>Hasil Tes</span>
        </a>
            </div>
        </div>

        <div>
            <p class="menu-group-title px-2 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-500">Bank Soal</p>
            <div class="space-y-1">

        <a href="kelola_soal.php"
           class="<?= $menuBaseClass ?> <?= in_array($current, ['kelola_soal.php', 'tambah_soal.php', 'edit_soal.php', 'hapus_soal.php']) ? $menuActiveClass : $menuInactiveClass ?>">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">SQ</span>
                    <span>Kelola Soal</span>
        </a>

        <a href="pengaturan_iq.php"
           class="<?= $menuBaseClass ?> <?= in_array($current, ['pengaturan_iq.php']) ? $menuActiveClass : $menuInactiveClass ?>">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">IQ</span>
                    <span>Pengaturan Tes IQ</span>
        </a>
            </div>
        </div>

        <div>
            <p class="menu-group-title px-2 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-500">Akun</p>
            <div class="space-y-1">
                <a href="profile.php"
                   class="<?= $menuBaseClass ?> <?= $current == 'profile.php' ? $menuActiveClass : $menuInactiveClass ?>">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10 text-[10px] font-bold">PR</span>
                    <span>Profil & Keamanan</span>
                </a>

                <a href="../logout.php" data-logout-url="../logout.php"
                         class="js-logout-trigger <?= $menuBaseClass ?> text-red-300 hover:bg-red-500/20 hover:text-white">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-red-500/20 text-[10px] font-bold text-red-100">EX</span>
                    <span>Logout</span>
        </a>
            </div>
        </div>
    </nav>

    <!-- Footer User Info -->
    <a href="profile.php" class="group flex items-center gap-3 border-t border-white/10 px-4 py-3.5 transition-all hover:bg-white/5">
        <div class="h-8 w-8 flex-shrink-0 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-xs font-bold text-white shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
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

<?php include __DIR__ . '/../../backend/logout_modal.php'; ?>