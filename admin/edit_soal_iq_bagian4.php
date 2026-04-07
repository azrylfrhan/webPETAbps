<?php
require_once '../backend/auth_check.php';
require_once '../backend/config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$cek = $conn->prepare("SELECT q.*, s.nama_bagian, s.urutan FROM iq_questions q LEFT JOIN iq_sections s ON s.id = q.section_id WHERE q.id = ? LIMIT 1");
$cek->bind_param('i', $id);
$cek->execute();
$soal = $cek->get_result()->fetch_assoc();
$cek->close();

if (!$soal) {
    header('Location: kelola_soal.php?msg=iq_not_found');
    exit;
}

if ((int)($soal['urutan'] ?? 0) !== 4) {
    header('Location: edit_soal_iq.php?id=' . $id);
    exit;
}

$normAnswers = [];
$stmtNorm = $conn->prepare("SELECT id, jawaban, nilai FROM iq_fill_answers WHERE question_id = ? ORDER BY id ASC");
$stmtNorm->bind_param('i', $id);
$stmtNorm->execute();
$resNorm = $stmtNorm->get_result();
while ($row = $resNorm->fetch_assoc()) {
    $normAnswers[] = $row;
}
$stmtNorm->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan = trim($_POST['pertanyaan'] ?? '');
    $gambar = trim($_POST['gambar'] ?? '');
    $jawaban_benar = trim($_POST['jawaban_benar'] ?? '');
    $norm_jawaban = $_POST['norm_jawaban'] ?? [];
    $norm_nilai = $_POST['norm_nilai'] ?? [];

    $cleanNorm = [];
    if (is_array($norm_jawaban)) {
        foreach ($norm_jawaban as $idx => $jawaban) {
            $jawaban = trim((string)$jawaban);
            $nilai = isset($norm_nilai[$idx]) ? (int)$norm_nilai[$idx] : 0;
            if ($jawaban === '') {
                continue;
            }
            $cleanNorm[] = ['jawaban' => $jawaban, 'nilai' => $nilai];
        }
    }

    if ($pertanyaan === '') {
        $error = 'Pertanyaan harus diisi.';
    } elseif (count($cleanNorm) === 0) {
        $error = 'Minimal isi satu jawaban norma untuk bagian 4.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE iq_questions SET pertanyaan = ?, gambar = ?, jawaban_benar = ? WHERE id = ?");
            $stmt->bind_param('sssi', $pertanyaan, $gambar, $jawaban_benar, $id);
            if (!$stmt->execute()) {
                throw new Exception('Gagal update soal bagian 4.');
            }
            $stmt->close();

            $conn->query("DELETE FROM iq_fill_answers WHERE question_id = " . (int)$id);

            $stmtInsert = $conn->prepare("INSERT INTO iq_fill_answers (question_id, jawaban, nilai) VALUES (?, ?, ?)");
            foreach ($cleanNorm as $item) {
                $stmtInsert->bind_param('isi', $id, $item['jawaban'], $item['nilai']);
                if (!$stmtInsert->execute()) {
                    throw new Exception('Gagal menyimpan jawaban norma.');
                }
            }
            $stmtInsert->close();

            $conn->commit();
            header('Location: kelola_soal.php?msg=iq_update_success');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Edit Soal IQ Bagian 4 | Admin BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }, colors: { navy: { DEFAULT: '#0F1E3C' } } } }
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
<div class="w-full max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-amber-500 text-white font-extrabold flex items-center justify-center text-sm">IQ</span>
            <div>
                <h1 class="text-xl font-extrabold text-navy tracking-tight">Edit Soal IQ Bagian 4</h1>
                <p class="text-slate-400 text-xs mt-0.5">Section <?= h($soal['nama_bagian'] ?? '-') ?> — Nomor <?= h($soal['nomor_soal']) ?></p>
            </div>
        </div>
        <a href="kelola_soal.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">← Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-amber-500 to-yellow-400"></div>
        <div class="p-8">
            <?php if(isset($error)): ?>
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium">
                    <span class="text-lg">❌</span><?= h($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="grid grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Pertanyaan / Kata</label>
                        <textarea name="pertanyaan" rows="4" required><?= h($soal['pertanyaan']) ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Gambar Soal (nama file)</label>
                        <input type="text" name="gambar" value="<?= h($soal['gambar']) ?>" placeholder="contoh: soal4.png">
                        <p class="text-[11px] text-slate-400 mt-2">Isi nama file jika bagian 4 memakai gambar, kalau tidak kosongkan.</p>
                        <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600">
                            Nilai jawaban bagian 4 disimpan di tabel iq_fill_answers, bukan di kolom jawaban_benar tunggal.
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Keterangan Kunci / Label (opsional)</label>
                    <input type="text" name="jawaban_benar" value="<?= h($soal['jawaban_benar']) ?>" placeholder="contoh: kata-kata yang diterima atau label internal">
                </div>

                <div class="border-t border-slate-100 pt-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Daftar Jawaban Norma & Nilai</p>
                        <button type="button" id="addNormBtn" class="text-xs font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition-colors">+ Tambah Baris</button>
                    </div>

                    <div id="normRows" class="space-y-4">
                        <?php if (empty($normAnswers)): ?>
                            <div class="grid grid-cols-12 gap-3 items-start norm-row">
                                <div class="col-span-8">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jawaban Norma</label>
                                    <input type="text" name="norm_jawaban[]" placeholder="contoh: Rumput">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nilai</label>
                                    <input type="number" name="norm_nilai[]" value="1" min="0">
                                </div>
                                <div class="col-span-1 pt-7">
                                    <button type="button" class="removeNormBtn w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold">×</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($normAnswers as $norm): ?>
                                <div class="grid grid-cols-12 gap-3 items-start norm-row">
                                    <div class="col-span-8">
                                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jawaban Norma</label>
                                        <input type="text" name="norm_jawaban[]" value="<?= h($norm['jawaban']) ?>" placeholder="contoh: Rumput">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nilai</label>
                                        <input type="number" name="norm_nilai[]" value="<?= (int)$norm['nilai'] ?>" min="0">
                                    </div>
                                    <div class="col-span-1 pt-7">
                                        <button type="button" class="removeNormBtn w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold">×</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-yellow-400 text-white shadow-md shadow-amber-200 hover:shadow-amber-300 hover:-translate-y-0.5 transition-all">💾 Simpan Perubahan</button>
                    <a href="kelola_soal.php" class="text-sm font-semibold text-slate-400 hover:text-slate-600 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="normRowTemplate">
    <div class="grid grid-cols-12 gap-3 items-start norm-row">
        <div class="col-span-8">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jawaban Norma</label>
            <input type="text" name="norm_jawaban[]" placeholder="contoh: Rumput">
        </div>
        <div class="col-span-3">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nilai</label>
            <input type="number" name="norm_nilai[]" value="1" min="0">
        </div>
        <div class="col-span-1 pt-7">
            <button type="button" class="removeNormBtn w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 font-bold">×</button>
        </div>
    </div>
</template>

<script>
document.getElementById('addNormBtn').addEventListener('click', function () {
    const template = document.getElementById('normRowTemplate');
    document.getElementById('normRows').appendChild(template.content.cloneNode(true));
});

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('removeNormBtn')) {
        const rows = document.querySelectorAll('.norm-row');
        if (rows.length > 1) {
            event.target.closest('.norm-row').remove();
        }
    }
});
</script>
</body>
</html>