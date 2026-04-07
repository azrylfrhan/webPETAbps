<!DOCTYPE html>
<?php
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}
$logoPath = $basePath . '/images/logobps.png';
?>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($logoPath) ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | PETA — Pemetaan Potensi Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
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
        .auth-group { position: relative; }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 40px;
            cursor: pointer;
            color: #64748b;
            z-index: 10;
        }
        .toggle-password:hover { color: #4f46e5; }

        .hero-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(120deg, #0f1e3c, #12306b, #1d4ed8, #2563eb, #0f1e3c);
            background-size: 300% 300%;
            animation: gradientShift 14s ease infinite;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: -20%;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.16), transparent 28%),
                radial-gradient(circle at 80% 30%, rgba(255,255,255,0.10), transparent 26%),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.08), transparent 30%);
            animation: floatGlow 10s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes floatGlow {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(2.5%, -2%, 0) scale(1.06); }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-200 font-sans">

<div class="min-h-screen w-full grid lg:grid-cols-5">
    <aside class="hero-panel hidden lg:flex lg:col-span-2 flex-col justify-between p-12 text-white">
        <div class="flex items-center gap-3">
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo BPS" class="h-10 w-auto object-contain">
            <span class="text-sm font-bold tracking-wide">PETA — Pemetaan Potensi Pegawai</span>
        </div>

        <div class="max-w-md">
            <h2 class="text-5xl font-bold leading-tight">Sistem Tes Psikologi Pegawai</h2>
            <p class="mt-6 text-base leading-relaxed text-blue-100/90">
                PETA digunakan untuk memetakan potensi Pegawai Badan Pusat Statistik melalui tes intelektual dan kepribadian kerja.
            </p>
            <p class="mt-4 text-base leading-relaxed text-blue-100/80">
                Masuk menggunakan akun Anda untuk memulai proses tes sesuai jadwal.
            </p>
        </div>

        <div class="mt-4 rounded-xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Alur singkat</p>
            <ul class="mt-3 space-y-2 text-sm leading-relaxed text-blue-100/90">
                <li>1. Login dengan NIP dan password.</li>
                <li>2. Lengkapi biodata dan alasan mengikuti tes.</li>
                <li>3. Kerjakan tes hingga selesai.</li>
            </ul>
        </div>

        <div class="mt-4 rounded-xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Sebelum masuk</p>
            <div class="mt-3 space-y-2 text-sm leading-relaxed text-blue-100/90">
                <p>• Pastikan NIP aktif dan password sesuai.</p>
                <p>• Gunakan perangkat yang nyaman untuk mengerjakan tes.</p>
                <p>• Riwayat tes tetap tersimpan per percobaan dan per tanggal.</p>
            </div>
        </div>

        <div class="mt-4 rounded-xl border border-white/15 bg-white/10 p-4 text-sm leading-relaxed text-blue-100/90 backdrop-blur-sm">
            <p class="font-semibold text-white">Bantuan singkat</p>
            <p class="mt-1">Jika login gagal, cek status akun atau hubungi admin untuk aktivasi jadwal tes.</p>
        </div>
    </aside>

    <main class="lg:col-span-3 flex min-h-screen items-start lg:items-center justify-center bg-white px-4 py-6 sm:px-8 sm:py-8 lg:px-10">
        <div class="w-full max-w-xl lg:max-w-2xl">
            <div class="mb-6 space-y-3 sm:space-y-4">
                <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo BPS" class="h-10 w-auto sm:h-12 lg:hidden">
                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800 lg:hidden">
                    Login untuk melanjutkan pengisian biodata dan pengerjaan tes psikologi.
                </div>
                <div class="grid grid-cols-3 items-center gap-2">
                    <div class="h-[2px] bg-blue-500 relative"><span class="absolute -top-[5px] left-0 h-3 w-3 rounded-full border-2 border-blue-600 bg-white"></span></div>
                    <div class="h-[2px] bg-slate-200 relative"><span class="absolute -top-[5px] left-1/2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-slate-300"></span></div>
                    <div class="h-[2px] bg-slate-200 relative"><span class="absolute -top-[5px] right-0 h-2.5 w-2.5 rounded-full bg-slate-300"></span></div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400 sm:text-[11px]">
                    <span class="text-blue-600">Login</span>
                    <span class="text-center">Isi Biodata</span>
                    <span class="text-right">Halaman Tes</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 sm:text-[2rem] leading-tight">Masuk ke Sistem PETA</h2>
                    <p class="mt-1 text-sm leading-relaxed text-slate-500 max-w-xl">Gunakan NIP dan password untuk mengakses tes psikologi pegawai.</p>
                </div>
            </div>

            <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">Yang perlu disiapkan</p>
                        <h3 class="mt-1 text-base font-bold text-slate-800 leading-tight">Pastikan data login dan biodata siap sebelum tes dimulai.</h3>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700 border border-blue-100">Satu akun untuk satu pegawai</span>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-sm font-semibold text-slate-800">1. Login dengan NIP aktif</p>
                        <p class="mt-1 text-[13px] leading-relaxed text-slate-500">Gunakan kredensial yang diberikan admin agar sistem dapat memeriksa status jadwal Anda.</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <p class="text-sm font-semibold text-slate-800">2. Simpan progres dengan aman</p>
                        <p class="mt-1 text-[13px] leading-relaxed text-slate-500">Jawaban direkam per percobaan sehingga hasil setiap tes tetap terpisah.</p>
                    </div>
                </div>
            </div>

            <div class="mb-5 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-sky-50 p-4">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">i</div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Informasi penting</p>
                        <p class="mt-1 text-[13px] leading-relaxed text-slate-600">Jika login gagal, pastikan NIP, password, dan status aktivasi tes sudah benar. Bila tes sudah pernah dikerjakan, hasil akan tetap tersimpan sesuai tanggal pengerjaan.</p>
                    </div>
                </div>
            </div>

            <?php if(isset($_GET['error'])): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                <?php 
                    if($_GET['error'] == 'wrong') echo "NIP atau Password salah!";
                    elseif($_GET['error'] == 'akses_ditolak') echo "Maaf, akun Anda belum diaktifkan oleh Admin untuk jadwal hari ini.";
                    elseif($_GET['error'] == 'empty') echo "Harap isi NIP dan Password!";
                ?>
            </div>
            <?php endif; ?>

            <form action="backend/login_process.php" method="POST" class="space-y-4 sm:space-y-5">
                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">NIP</label>
                    <input type="text" name="nip" placeholder="Masukkan NIP Anda" required class="w-full rounded-md border border-gray-200 px-4 py-3.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:py-3">
                </div>

                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                    <input type="password" name="password" id="pass_login" placeholder="Masukkan Password" required class="w-full rounded-md border border-gray-200 px-4 py-3.5 pr-11 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 sm:py-3">
                    <i class="fi fi-rr-eye toggle-password" id="btn_toggle_login"></i>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-3.5 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-blue-700 sm:py-3">
                        Login
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-xs text-slate-500">PETA — Pemetaan Potensi Pegawai</p>
        </div>
    </main>

</div>

<script>
    const btnToggle = document.querySelector('#btn_toggle_login');
    const inputPass = document.querySelector('#pass_login');

    if (btnToggle && inputPass) {
        btnToggle.addEventListener('click', function() {
            if (inputPass.type === 'password') {
                inputPass.type = 'text';
                this.classList.replace('fi-rr-eye', 'fi-rr-eye-crossed');
            } else {
                inputPass.type = 'password';
                this.classList.replace('fi-rr-eye-crossed', 'fi-rr-eye');
            }
        });
    }
</script>

</body>
</html>
