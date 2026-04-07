<?php 
require_once '../backend/auth_check.php'; 
require_once '../backend/config.php';

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

$query = mysqli_query($conn, "SELECT * FROM soal WHERE id = '$id'");
$d = mysqli_fetch_assoc($query);

if (!$d) {
    header("Location: kelola_soal.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_soal = mysqli_real_escape_string($conn, $_POST['id']);
    $nomor   = mysqli_real_escape_string($conn, $_POST['nomor_soal']);
    $pa      = mysqli_real_escape_string($conn, $_POST['pertanyaan_a']);
    $pb      = mysqli_real_escape_string($conn, $_POST['pertanyaan_b']);

    $sql_update = "UPDATE soal SET 
                   nomor_soal='$nomor', 
                   pertanyaan_a='$pa', 
                   pertanyaan_b='$pb' 
                   WHERE id='$id_soal'";
    
    if (mysqli_query($conn, $sql_update)) {
        header("Location: kelola_soal.php?msg=update_berhasil");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Edit Soal | Admin BPS</title>

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
            box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #2563EB;
            background: white;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        textarea { resize: vertical; }
    </style>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-3xl">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm">
                    #<?= $d['nomor_soal'] ?>
                </span>
                <div>
                    <h1 class="text-xl font-extrabold text-navy tracking-tight">Edit Soal</h1>
                    <p class="text-slate-400 text-xs mt-0.5">Nomor <?= $d['nomor_soal'] ?> &mdash; <?= $d['kode_tes'] ?></p>
                </div>
            </div>
            <a href="kelola_soal.php"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                ← Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            <!-- Top accent bar -->
            <div class="h-1 bg-gradient-to-r from-blue-600 to-cyan-400"></div>

            <div class="p-8">

                <!-- Alert Error -->
                <?php if(isset($error)): ?>
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium">
                    <span class="text-lg">❌</span>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">

                    <!-- Nomor Soal -->
                    <div class="mb-6 w-36">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Soal</label>
                        <input type="number" name="nomor_soal" value="<?= $d['nomor_soal'] ?>" required>
                    </div>

                    <div class="border-t border-slate-100 mb-6"></div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Isi Pernyataan</p>

                    <!-- Pernyataan A & B side by side -->
                    <div class="grid grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                                <span class="inline-flex w-5 h-5 rounded bg-blue-600 text-white text-[10px] font-black items-center justify-center mr-1">A</span>
                                Pernyataan Opsi A
                            </label>
                            <textarea name="pertanyaan_a" rows="8" required><?= htmlspecialchars($d['pertanyaan_a']) ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                                <span class="inline-flex w-5 h-5 rounded bg-blue-400 text-white text-[10px] font-black items-center justify-center mr-1">B</span>
                                Pernyataan Opsi B
                            </label>
                            <textarea name="pertanyaan_b" rows="8" required><?= htmlspecialchars($d['pertanyaan_b']) ?></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md shadow-blue-200 hover:shadow-blue-300 hover:-translate-y-0.5 transition-all">
                        💾 Simpan Perubahan Soal
                    </button>

                </form>
            </div>
        </div>

    </div>

</body>
</html>