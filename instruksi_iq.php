<?php
include 'backend/auth_check.php';
require_once 'backend/config.php';

$sub_tes = isset($_GET['sub']) ? intval($_GET['sub']) : 1;

$query = "SELECT * FROM pengaturan_iq WHERE sub_tes = $sub_tes";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data pengaturan sub-tes belum ada di database.";
    exit();
}

$durasiIstDetik = [
    1 => 6 * 60,
    2 => 6 * 60,
    3 => 7 * 60,
    4 => 8 * 60,
    5 => 10 * 60,
    6 => 10 * 60,
    7 => 7 * 60,
    8 => 9 * 60,
    9 => 6 * 60,
];

$durasiAktifDetik = $durasiIstDetik[$sub_tes] ?? (int)($data['durasi_detik'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Petunjuk Kelompok Soal <?php echo str_pad($sub_tes, 2, '0', STR_PAD_LEFT); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-navy { background-color: #0f1e3c; }
        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(15, 30, 60, 0.08) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
</head>
<body class="bg-grid bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-navy p-8 text-white text-center">
            <h1 class="text-2xl font-bold mb-2">Petunjuk Kelompok Soal <?php echo str_pad($sub_tes, 2, '0', STR_PAD_LEFT); ?></h1>
            <p class="text-blue-200 text-sm font-semibold uppercase tracking-widest"><?php echo $data['nama_sub_tes']; ?></p>
            <p class="mt-3 text-sm text-blue-100/90">Baca petunjuk dengan teliti sebelum memulai sub-tes.</p>
        </div>

        <div class="p-8">
            <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-3">CARA MENGERJAKAN:</h3>
                <p class="text-slate-700 leading-relaxed">
                    <?php echo nl2br($data['instruksi']); ?>
                </p>
            </div>

            <div class="mb-8 border-2 border-dashed border-blue-200 rounded-2xl p-6 bg-blue-50/50">
                <h3 class="text-xs font-bold text-blue-600 uppercase mb-4">CONTOH SOAL:</h3>
                <p class="text-slate-800 font-bold mb-6 text-lg"><?php echo $data['contoh_soal']; ?></p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                    <label class="flex items-center p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:bg-blue-100 transition-all">
                        <input type="radio" name="opsi_contoh" value="a" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm font-medium text-slate-700">a) kucing</span>
                    </label>
                    <label class="flex items-center p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:bg-blue-100 transition-all">
                        <input type="radio" name="opsi_contoh" value="b" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm font-medium text-slate-700">b) bajing</span>
                    </label>
                    <label class="flex items-center p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:bg-blue-100 transition-all">
                        <input type="radio" name="opsi_contoh" value="c" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm font-medium text-slate-700">c) keledai</span>
                    </label>
                    <label class="flex items-center p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:bg-blue-100 transition-all">
                        <input type="radio" name="opsi_contoh" value="d" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm font-medium text-slate-700">d) lembu</span>
                    </label>
                    <label class="flex items-center p-3 bg-white border border-blue-100 rounded-xl cursor-pointer hover:bg-blue-100 transition-all">
                        <input type="radio" name="opsi_contoh" value="e" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm font-medium text-slate-700">e) anjing</span>
                    </label>
                </div>

                <button onclick="cekJawaban()" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">
                    Cek Jawaban Contoh
                </button>
                <p id="feedback" class="mt-3 text-center text-sm font-bold hidden"></p>
            </div>

            <div class="flex items-center justify-between border-t pt-8">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Waktu:</p>
                    <p class="text-xl font-bold text-navy"><?php echo ($durasiAktifDetik / 60); ?> Menit</p>
                </div>
                <button id="btn_mulai" disabled class="px-10 py-4 bg-slate-200 text-slate-400 rounded-2xl text-lg font-bold cursor-not-allowed">
                    Mulai Tes Sekarang
                </button>
            </div>
        </div>
    </div>

    <script>
        function cekJawaban() {
            const terpilih = document.querySelector('input[name="opsi_contoh"]:checked');
            const feedback = document.getElementById('feedback');
            const btnMulai = document.getElementById('btn_mulai');
            const kunci = "<?php echo strtolower($data['kunci_contoh']); ?>";

            if (!terpilih) {
                alert("Pilih salah satu jawaban dulu!");
                return;
            }

            feedback.classList.remove('hidden');
            if (terpilih.value === kunci) {
                feedback.innerHTML = "✓ Jawaban Benar! (c. keledai)";
                feedback.className = "mt-3 text-center text-sm font-bold text-green-600";
                btnMulai.disabled = false;
                btnMulai.className = "px-10 py-4 bg-navy text-white rounded-2xl text-lg font-bold hover:opacity-95 hover:shadow-xl cursor-pointer transition";
            } else {
                feedback.innerHTML = "✗ Jawaban Salah. Silakan coba lagi.";
                feedback.className = "mt-3 text-center text-sm font-bold text-red-500";
            }
        }
    </script>
</body>
</html>