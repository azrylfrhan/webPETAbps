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

$sectionList = [];
$resSection = $conn->query("SELECT id, nama_bagian, urutan FROM iq_sections ORDER BY urutan ASC");
while ($row = $resSection->fetch_assoc()) {
    $sectionList[] = $row;
}

$options = [];
$stmtOpt = $conn->prepare("SELECT id, label, opsi_text, gambar_opsi FROM iq_options WHERE question_id = ? ORDER BY label ASC");
$stmtOpt->bind_param('i', $id);
$stmtOpt->execute();
$resOpt = $stmtOpt->get_result();
while ($row = $resOpt->fetch_assoc()) {
    $options[] = $row;
}
$stmtOpt->close();

$tipeList = ['pilihan' => 'Pilihan Ganda', 'isian' => 'Isian', 'angka' => 'Angka', 'gambar' => 'Gambar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_id = (int) ($_POST['section_id'] ?? 0);
    $nomor_soal  = (int) ($_POST['nomor_soal'] ?? 0);
    $pertanyaan  = trim($_POST['pertanyaan'] ?? '');
    $gambar      = trim($_POST['gambar'] ?? '');
    $tipe_soal   = trim($_POST['tipe_soal'] ?? 'pilihan');
    $jawaban_benar = trim($_POST['jawaban_benar'] ?? '');

    if ($section_id <= 0 || $nomor_soal <= 0 || !isset($tipeList[$tipe_soal])) {
        $error = 'Data utama soal tidak lengkap.';
    } elseif ($tipe_soal !== 'gambar' && $pertanyaan === '') {
        $error = 'Pertanyaan harus diisi untuk tipe soal selain gambar.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE iq_questions SET section_id = ?, nomor_soal = ?, pertanyaan = ?, gambar = ?, tipe_soal = ?, jawaban_benar = ? WHERE id = ?");
            $stmt->bind_param('iissssi', $section_id, $nomor_soal, $pertanyaan, $gambar, $tipe_soal, $jawaban_benar, $id);
            if (!$stmt->execute()) {
                throw new Exception('Gagal update soal.');
            }
            $stmt->close();

            $conn->query("DELETE FROM iq_options WHERE question_id = " . (int)$id);

            $labels = $_POST['option_label'] ?? [];
            $texts  = $_POST['option_text'] ?? [];
            $imgs   = $_POST['option_image'] ?? [];

            if (is_array($labels)) {
                $stmtIns = $conn->prepare("INSERT INTO iq_options (question_id, label, opsi_text, gambar_opsi) VALUES (?, ?, ?, ?)");
                foreach ($labels as $idx => $label) {
                    $label = strtoupper(trim((string)$label));
                    $text  = trim((string)($texts[$idx] ?? ''));
                    $img   = trim((string)($imgs[$idx] ?? ''));
                    if ($label === '') {
                        continue;
                    }
                    if ($text === '' && $img === '') {
                        continue;
                    }
                    $stmtIns->bind_param('isss', $id, $label, $text, $img);
                    if (!$stmtIns->execute()) {
                        throw new Exception('Gagal simpan opsi.');
                    }
                }
                $stmtIns->close();
            }

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
    <meta charset="UTF-8">
    <title>Edit Soal IQ | Admin BPS</title>
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
<div class="w-full max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-amber-500 text-white font-extrabold flex items-center justify-center text-sm">IQ</span>
            <div>
                <h1 class="text-xl font-extrabold text-navy tracking-tight">Edit Soal Tes IQ</h1>
                <p class="text-slate-400 text-xs mt-0.5">Section <?= h($soal['nama_bagian'] ?? '-') ?> — Nomor <?= h($soal['nomor_soal']) ?></p>
            </div>
        </div>
        <a href="kelola_soal.php"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
            ← Kembali
        </a>
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
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Section</label>
                        <select name="section_id" required>
                            <?php foreach ($sectionList as $sec): ?>
                                <option value="<?= (int)$sec['id'] ?>" <?= (int)$soal['section_id'] === (int)$sec['id'] ? 'selected' : '' ?>>
                                    <?= h($sec['urutan']) ?> - <?= h($sec['nama_bagian']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Soal</label>
                        <input type="number" name="nomor_soal" value="<?= h($soal['nomor_soal']) ?>" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tipe Soal</label>
                    <select name="tipe_soal" required>
                        <?php foreach ($tipeList as $key => $label): ?>
                            <option value="<?= h($key) ?>" <?= $soal['tipe_soal'] === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Pertanyaan <?php if($soal['tipe_soal'] !== 'gambar'): ?><span class="text-red-500">*</span><?php endif; ?></label>
                    <textarea name="pertanyaan" rows="5" <?= ($soal['tipe_soal'] !== 'gambar' ? 'required' : '') ?>><?= h($soal['pertanyaan']) ?></textarea>
                    <?php if($soal['tipe_soal'] === 'gambar'): ?><p class="text-[11px] text-slate-400 mt-2">Opsional untuk soal gambar (hanya gunakan gambar saja).</p><?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Gambar Soal (nama file)</label>
                        <input type="text" name="gambar" value="<?= h($soal['gambar']) ?>" placeholder="contoh: soal1.png">
                        <p class="text-[11px] text-slate-400 mt-2">Isi nama file yang ada di folder images/img_soal.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jawaban Benar / Kunci</label>
                        <input type="text" name="jawaban_benar" value="<?= h($soal['jawaban_benar']) ?>" placeholder="contoh: A atau jawaban teks">
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Opsi Jawaban</p>
                        <p class="text-xs text-slate-400">Kosongkan baris yang tidak dipakai</p>
                    </div>

                    <div class="space-y-4">
                        <?php
                        $presetLabels = ['A','B','C','D','E','F'];
                        $maxRows = max(6, count($options));
                        for ($i = 0; $i < $maxRows; $i++):
                            $opt = $options[$i] ?? ['label' => $presetLabels[$i] ?? '', 'opsi_text' => '', 'gambar_opsi' => ''];
                        ?>
                        <div class="grid grid-cols-12 gap-3 items-start">
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Label</label>
                                <input type="text" name="option_label[]" maxlength="1" value="<?= h($opt['label']) ?>" placeholder="A">
                            </div>
                            <div class="col-span-7">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Teks Opsi</label>
                                <input type="text" name="option_text[]" value="<?= h($opt['opsi_text']) ?>" placeholder="Isi teks opsi">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Gambar Opsi (nama file)</label>
                                <input type="text" name="option_image[]" value="<?= h($opt['gambar_opsi']) ?>" placeholder="contoh: opsiA.png">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-yellow-400 text-white shadow-md shadow-amber-200 hover:shadow-amber-300 hover:-translate-y-0.5 transition-all">
                        💾 Simpan Perubahan Soal IQ
                    </button>
                    <a href="kelola_soal.php" class="text-sm font-semibold text-slate-400 hover:text-slate-600 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
