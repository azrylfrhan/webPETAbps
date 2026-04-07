<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
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
    </style>
</head>
<body class="min-h-screen bg-slate-200 font-sans">

<div class="min-h-screen w-full grid lg:grid-cols-5">
    <aside class="hidden lg:flex lg:col-span-2 flex-col justify-between bg-blue-600 p-12 text-white">
        <div class="flex items-center gap-3">
            <img src="images/logobps.png" alt="Logo BPS" class="h-10 w-auto object-contain">
            <span class="text-sm font-bold tracking-wide">PETA — Pemetaan Potensi Pegawai</span>
        </div>

        <div class="max-w-md">
            <h2 class="text-5xl font-bold leading-tight">Sistem Tes Psikologi Pegawai</h2>
            <p class="mt-6 text-base leading-relaxed text-blue-100/90">
                PETA digunakan untuk memetakan potensi pegawai melalui tes intelektual dan kepribadian kerja.
            </p>
            <p class="mt-4 text-base leading-relaxed text-blue-100/80">
                Masuk menggunakan akun Anda untuk memulai proses tes sesuai jadwal.
            </p>
        </div>

        <div class="rounded-xl bg-blue-700/80 p-5 ring-1 ring-white/20">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-100">Alur singkat</p>
            <ul class="mt-3 space-y-2 text-sm leading-relaxed text-blue-100/90">
                <li>1. Login dengan NIP dan password.</li>
                <li>2. Lengkapi biodata dan alasan mengikuti tes.</li>
                <li>3. Kerjakan tes hingga selesai.</li>
            </ul>
        </div>
    </aside>

    <main class="lg:col-span-3 flex min-h-screen items-center justify-center bg-white px-6 py-10 sm:px-10">
        <div class="w-full max-w-2xl">
            <div class="mb-8 space-y-6">
                <img src="images/logobps.png" alt="Logo BPS" class="h-12 w-auto lg:hidden">
                <div class="grid grid-cols-3 items-center gap-2">
                    <div class="h-[2px] bg-blue-500 relative"><span class="absolute -top-[5px] left-0 h-3 w-3 rounded-full border-2 border-blue-600 bg-white"></span></div>
                    <div class="h-[2px] bg-slate-200 relative"><span class="absolute -top-[5px] left-1/2 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-slate-300"></span></div>
                    <div class="h-[2px] bg-slate-200 relative"><span class="absolute -top-[5px] right-0 h-2.5 w-2.5 rounded-full bg-slate-300"></span></div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                    <span class="text-blue-600">Login</span>
                    <span class="text-center">Isi Biodata</span>
                    <span class="text-right">Halaman Tes</span>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-slate-800">Masuk ke Sistem PETA</h2>
                    <p class="mt-1 text-sm text-slate-500">Gunakan NIP dan password untuk mengakses tes psikologi pegawai.</p>
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

            <form action="backend/login_process.php" method="POST" class="space-y-5">
                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">NIP</label>
                    <input type="text" name="nip" placeholder="Masukkan NIP Anda" required class="w-full rounded-md border border-gray-200 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="auth-group">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                    <input type="password" name="password" id="pass_login" placeholder="Masukkan Password" required class="w-full rounded-md border border-gray-200 px-4 py-3 pr-11 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <i class="fi fi-rr-eye toggle-password" id="btn_toggle_login"></i>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-blue-700">
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
