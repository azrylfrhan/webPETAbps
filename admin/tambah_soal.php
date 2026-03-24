<?php 
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Logika penyimpanan soal
if (isset($_POST['tambah'])) {
    $kode_tes = mysqli_real_escape_string($conn, $_POST['kode_tes']);
    $nomor_soal = mysqli_real_escape_string($conn, $_POST['nomor_soal']);
    $pertanyaan_a = mysqli_real_escape_string($conn, $_POST['pertanyaan_a']);
    $pertanyaan_b = mysqli_real_escape_string($conn, $_POST['pertanyaan_b']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO soal (kode_tes, nomor_soal, pertanyaan_a, pertanyaan_b, status) 
              VALUES ('$kode_tes', '$nomor_soal', '$pertanyaan_a', '$pertanyaan_b', '$status')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: kelola_soal.php?status=tambah_berhasil");
    } else {
        $error = "Gagal menambah soal: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal Baru | Admin BPS</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }

        input, textarea, select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            color: #0F172A;
            background: #F8FAFC;
            outline: none;
            transition: all 0.2s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #2563EB;
            background: white;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        textarea { resize: vertical; min-height: 90px; }
    </style>
</head>

<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-navy tracking-tight">Tambah Soal Baru</h1>
            <p class="text-slate-500 text-sm mt-1">Silakan isi detail soal untuk modul tes psikologi.</p>
        </div>
        <a href="kelola_soal.php"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Alert Error -->
    <?php if(isset($error)): ?>
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium">
        <span class="text-lg">❌</span>
        <?= $error ?>
    </div>
    <?php endif; ?>

    <!-- Form Container -->
    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

        <form action="" method="POST">

            <!-- Jenis Tes -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jenis Tes</label>
                <select name="kode_tes" required>
                    <option value="KEPRIBADIAN">Kepribadian — Bagian 1 (MSDT)</option>
                    <option value="KEPRIBADIAN2">Kepribadian — Bagian 2 (PAPI)</option>
                    <option value="IQ">Tes IQ</option>
                </select>
            </div>

            <!-- Nomor Soal -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Soal</label>
                <input type="number" name="nomor_soal" placeholder="Contoh: 1" required>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-100 my-6"></div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Isi Pernyataan</p>

            <!-- Pernyataan A -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                    <span class="inline-flex w-5 h-5 rounded bg-blue-600 text-white text-[10px] font-black items-center justify-center mr-1">A</span>
                    Pernyataan A
                </label>
                <textarea name="pertanyaan_a" placeholder="Masukkan pernyataan pilihan A..." required></textarea>
            </div>

            <!-- Pernyataan B -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                    <span class="inline-flex w-5 h-5 rounded bg-blue-400 text-white text-[10px] font-black items-center justify-center mr-1">B</span>
                    Pernyataan B
                </label>
                <textarea name="pertanyaan_b" placeholder="Masukkan pernyataan pilihan B..." required></textarea>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-100 my-6"></div>

            <!-- Status Soal -->
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Status Soal</label>
                <select name="status">
                    <option value="aktif">✅ Aktif</option>
                    <option value="nonaktif">⛔ Non-Aktif</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <button type="submit" name="tambah"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md shadow-blue-200 hover:shadow-blue-300 hover:-translate-y-0.5 transition-all">
                    💾 Simpan Soal
                </button>
                <a href="kelola_soal.php"
                   class="text-sm font-semibold text-slate-400 hover:text-slate-600 transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

</body>
</html>