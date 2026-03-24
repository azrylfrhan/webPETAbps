<?php
require_once '../../backend/config.php';
require_once '../../backend/auth_check.php';

$nip  = $_SESSION['nip'] ?? 'tidak ada';
$soal = $conn->query("SELECT id, pertanyaan FROM iq_questions WHERE section_id = 1 ORDER BY nomor_soal ASC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Test Save Answer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-8 font-sans">
<div class="max-w-xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h1 class="text-lg font-bold text-slate-800 mb-1">🧪 Test Save Answer</h1>
        <p class="text-sm text-slate-500">NIP: <strong><?= htmlspecialchars($nip) ?></strong></p>
    </div>

    <?php if ($soal): ?>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h2 class="font-bold text-slate-700 mb-3">1. Kirim Jawaban Test</h2>
        <p class="text-sm text-slate-600 mb-4">Soal ID <strong><?= $soal['id'] ?></strong>: <?= htmlspecialchars(substr($soal['pertanyaan'] ?? 'soal gambar', 0, 80)) ?></p>
        <div class="flex gap-2 flex-wrap mb-4">
            <?php foreach(['A','B','C','D','E'] as $opt): ?>
            <button onclick="kirimJawaban(<?= $soal['id'] ?>, '<?= $opt ?>')"
                class="px-5 py-2 bg-slate-100 border border-slate-300 rounded-xl font-bold hover:bg-blue-50 hover:border-blue-400 transition">
                <?= $opt ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div id="hasil-kirim" class="hidden text-sm p-3 rounded-xl"></div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h2 class="font-bold text-slate-700 mb-3">2. Cek Jawaban Tersimpan</h2>
        <button onclick="cekJawaban(<?= $soal['id'] ?>)"
            class="px-6 py-2 bg-slate-800 text-white rounded-xl font-semibold hover:opacity-90 transition">
            Cek Soal ID <?= $soal['id'] ?>
        </button>
        <div id="hasil-cek" class="hidden mt-4 text-sm p-3 rounded-xl"></div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h2 class="font-bold text-slate-700 mb-3">3. Semua Jawaban di Database</h2>
        <button onclick="cekDB()"
            class="px-6 py-2 bg-green-700 text-white rounded-xl font-semibold hover:opacity-90 transition">
            Lihat Data
        </button>
        <div id="hasil-db" class="hidden mt-4"></div>
    </div>
    <?php endif; ?>

</div>
<script>
async function kirimJawaban(qid, label) {
    const el = document.getElementById("hasil-kirim");
    el.classList.remove("hidden");
    el.className = "text-sm p-3 rounded-xl bg-slate-50 border border-slate-200";
    el.textContent = "Mengirim...";
    const res  = await fetch("/bps-psikotes/tes_proses/tes_iq/api/save_answer.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ question_id: qid, answer: label })
    });
    const data = await res.json();
    if (data.success) {
        el.className = "text-sm p-3 rounded-xl bg-green-50 border border-green-200 text-green-700";
        el.textContent = `✅ Berhasil! Jawaban: ${label} | Affected rows: ${data.affected_rows}`;
    } else {
        el.className = "text-sm p-3 rounded-xl bg-red-50 border border-red-200 text-red-700";
        el.textContent = `❌ Gagal: ${data.error ?? JSON.stringify(data)}`;
    }
}

async function cekJawaban(qid) {
    const el = document.getElementById("hasil-cek");
    el.classList.remove("hidden");
    el.textContent = "Mengecek...";
    const res  = await fetch(`/bps-psikotes/tes_proses/tes_iq/api/get_last_answer.php?question_id=${qid}`);
    const data = await res.json();
    if (data.answer) {
        el.className = "mt-4 text-sm p-3 rounded-xl bg-green-50 border border-green-200 text-green-700";
        el.textContent = `✅ Jawaban tersimpan: ${data.answer}`;
    } else {
        el.className = "mt-4 text-sm p-3 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-700";
        el.textContent = `⚠️ Belum ada jawaban tersimpan`;
    }
}

async function cekDB() {
    const el = document.getElementById("hasil-db");
    el.classList.remove("hidden");
    el.innerHTML = '<p class="text-sm text-slate-500">Memuat...</p>';
    const res  = await fetch("/bps-psikotes/tes_proses/tes_iq/api/get_all_answers.php");
    const data = await res.json();
    if (!data.rows?.length) {
        el.innerHTML = '<p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded-xl">⚠️ Belum ada data</p>';
        return;
    }
    let html = `<p class="text-xs text-slate-500 mb-2">Total: ${data.rows.length} jawaban</p>
    <div class="overflow-auto rounded-xl border border-slate-200">
    <table class="w-full text-xs"><thead class="bg-slate-50">
    <tr><th class="px-3 py-2 text-left">ID</th><th class="px-3 py-2 text-left">NIP</th><th class="px-3 py-2 text-left">Q.ID</th><th class="px-3 py-2 text-left">Jawaban</th><th class="px-3 py-2 text-left">Waktu</th></tr>
    </thead><tbody>`;
    data.rows.forEach((r,i) => {
        html += `<tr class="${i%2===0?'bg-white':'bg-slate-50'}">
        <td class="px-3 py-2">${r.id}</td>
        <td class="px-3 py-2">${r.user_nip}</td>
        <td class="px-3 py-2">${r.question_id}</td>
        <td class="px-3 py-2 font-bold text-blue-700">${r.jawaban_user}</td>
        <td class="px-3 py-2 text-slate-400">${r.waktu_jawab}</td></tr>`;
    });
    html += `</tbody></table></div>`;
    el.innerHTML = html;
}
</script>
</body>
</html>