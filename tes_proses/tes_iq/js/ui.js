/* =========================================================
   UI CONTROLLER
========================================================= */

const API_BASE  = 'api';
const IMG_SOAL  = '../../images/img_soal/';
const IMG_SEC   = '../../';

let CURRENT_EXAMPLE = null;

const UI = {

/* =========================================================
   INSTRUCTION SCREEN
========================================================= */

renderInstruction(section, example){

    CURRENT_EXAMPLE = example;

    let instrGambar = "";
    if (section.gambar_instruksi) {
        instrGambar = `
        <div class="mb-4 text-center">
            <img src="${IMG_SEC}${section.gambar_instruksi}"
                 class="mx-auto rounded-lg max-h-64"
                 onerror="this.style.display='none'">
        </div>`;
    }

    let html = `
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-2xl p-10 fade-in">
            <h2 class="text-2xl font-bold text-navy mb-6">Instruksi Tes</h2>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 mb-8">
                ${instrGambar}
                <div class="text-slate-600 whitespace-pre-line leading-relaxed">
                    ${section.instruksi}
                </div>
            </div>
            <button onclick="UI.renderExample()"
                class="bg-navy text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                Mulai Kerjakan Soal
            </button>
        </div>
    </div>`;

    document.getElementById("section-title").innerText = section.nama_bagian;
    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";
},


/* =========================================================
   MEMORY / HAFALAN SCREEN
   Dipanggil dari engine jika section punya waktu_hafalan
========================================================= */

renderMemory(items, waktuHafalan){

    let rowsHtml = "";
    items.forEach((item, i) => {
        const kata = item.kata_kata.split(",").map(k => k.trim());
        const bgRow = i % 2 === 0 ? "bg-white" : "bg-slate-50";
        rowsHtml += `
        <tr class="${bgRow}">
            <td class="px-6 py-4 font-black text-navy text-sm uppercase tracking-wide whitespace-nowrap border-r border-slate-200 w-36">
                ${item.kategori}
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    ${kata.map(k => `
                    <span class="bg-navy/5 border border-navy/15 text-navy font-semibold text-sm px-3 py-1 rounded-lg">
                        ${k}
                    </span>`).join("")}
                </div>
            </td>
        </tr>`;
    });

    const mm = String(Math.floor(waktuHafalan/60)).padStart(2,"0");
    const ss = String(waktuHafalan % 60).padStart(2,"0");

    let html = `
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-2xl fade-in overflow-hidden">

            <!-- Header -->
            <div class="px-8 pt-8 pb-5 flex items-center justify-between border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-black text-navy">Hafalkan Kata-Kata Ini</h2>
                    <p class="text-slate-400 text-sm mt-1">Halaman berpindah otomatis saat waktu habis</p>
                </div>
                <div class="text-center bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 min-w-[100px]">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sisa Waktu</p>
                    <p id="memory-timer" class="text-3xl font-black text-navy font-mono leading-none">${mm}:${ss}</p>
                </div>
            </div>

            <!-- Tabel kata -->
            <div class="overflow-hidden">
                <table class="w-full border-collapse">
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                </table>
            </div>

            <!-- Progress bar -->
            <div class="px-8 py-5 border-t border-slate-100">
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div id="memory-progress" class="bg-navy h-2 rounded-full transition-all duration-1000" style="width:100%"></div>
                </div>
            </div>

        </div>
    </div>`;

    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";

    let remaining    = waktuHafalan;
    const timerEl    = document.getElementById("memory-timer");
    const progressEl = document.getElementById("memory-progress");

    const memInterval = setInterval(() => {
        remaining--;
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        timerEl.innerText = String(m).padStart(2,"0") + ":" + String(s).padStart(2,"0");
        progressEl.style.width = `${(remaining / waktuHafalan) * 100}%`;

        if (remaining <= 0) {
            clearInterval(memInterval);
            startExam();
        }
    }, 1000);
},


/* =========================================================
   EXAMPLE QUESTION
========================================================= */

renderExample(){

    const example = CURRENT_EXAMPLE;

    if (!example) {
        startExam();
        return;
    }

    const hasOptions   = example.options && example.options.length > 0;
    const hasGambarOpt = hasOptions && example.options.some(o => o.gambar_opsi);

    let html = `
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-2xl p-10 fade-in">
            <h2 class="text-xl font-bold text-navy mb-2">Contoh Soal</h2>
            <p class="text-slate-400 text-sm mb-6">Jawab contoh soal berikut sebelum memulai tes</p>`;

    if (example.gambar) {
        html += `
        <div class="mb-6 text-center">
            <img src="${IMG_SEC}images/img_section/${example.gambar}"
                 class="mx-auto rounded-lg max-h-64"
                 onerror="this.style.display='none'">
        </div>`;
    }

    if (example.pertanyaan) {
        html += `<p class="text-slate-700 text-base mb-6 leading-relaxed">${example.pertanyaan}</p>`;
    }

    if (hasOptions) {
        if (hasGambarOpt) {
            html += `<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">`;
            example.options.forEach(opt => {
                html += `
                <button onclick="UI.checkExample('${opt.label}','${example.jawaban_benar}')"
                    class="border border-slate-200 rounded-xl p-3 hover:bg-slate-50 hover:border-navy transition text-center">
                    <img src="${IMG_SOAL}${opt.gambar_opsi}"
                         class="mx-auto mb-2 max-h-24 rounded"
                         onerror="this.style.display='none'">
                    <span class="text-sm font-bold text-navy">${opt.label}</span>
                </button>`;
            });
            html += `</div>`;
        } else {
            html += `<div class="space-y-3">`;
            example.options.forEach(opt => {
                html += `
                <button onclick="UI.checkExample('${opt.label}','${example.jawaban_benar}')"
                    class="block w-full text-left border border-slate-200 rounded-xl p-4 hover:bg-slate-50 hover:border-navy transition text-slate-700">
                    <strong class="text-navy">${opt.label}.</strong> ${opt.opsi_text}
                </button>`;
            });
            html += `</div>`;
        }
    } else {
        html += `
        <div class="flex gap-3 items-center">
            <input type="text" id="example-input" placeholder="Ketik jawaban Anda..."
                class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/10 transition">
            <button id="btn-cek-isian"
                class="bg-navy text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition">Cek</button>
        </div>`;
    }

    html += `
            <div id="example-feedback" class="hidden mt-6"></div>
        </div>
    </div>`;

    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";

    if (!hasOptions) {
        document.getElementById("btn-cek-isian").addEventListener("click", () => {
            UI.checkExampleIsian(document.getElementById("example-input").value.trim(), example.jawaban_benar);
        });
        document.getElementById("example-input").addEventListener("keydown", function(e) {
            if (e.key === "Enter") UI.checkExampleIsian(this.value.trim(), example.jawaban_benar);
        });
    }
},


/* =========================================================
   VALIDATE — PILIHAN GANDA
========================================================= */

checkExample(answer, correct){
    const feedback = document.getElementById("example-feedback");
    if (answer === correct) {
        feedback.innerHTML = `
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-center">
            <p class="font-semibold mb-3">✔ Jawaban benar! Anda siap memulai tes.</p>
            <button id="btn-mulai-tes" class="bg-navy text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">Mulai Tes</button>
        </div>`;
        feedback.classList.remove("hidden");
        document.getElementById("btn-mulai-tes").addEventListener("click", () => startExam());
    } else {
        feedback.innerHTML = `
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-center">
            Jawaban belum tepat. Silakan coba lagi.
        </div>`;
        feedback.classList.remove("hidden");
    }
},


/* =========================================================
   VALIDATE — ISIAN
========================================================= */

checkExampleIsian(answer, correct){
    const feedback = document.getElementById("example-feedback");
    if (answer.toLowerCase() === correct.toString().toLowerCase()) {
        feedback.innerHTML = `
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-center">
            <p class="font-semibold mb-3">✔ Jawaban benar! Anda siap memulai tes.</p>
            <button id="btn-mulai-tes" class="bg-navy text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">Mulai Tes</button>
        </div>`;
        feedback.classList.remove("hidden");
        document.getElementById("btn-mulai-tes").addEventListener("click", () => startExam());
    } else {
        feedback.innerHTML = `
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-center">
            Jawaban belum tepat. Silakan coba lagi.
        </div>`;
        feedback.classList.remove("hidden");
    }
},


/* =========================================================
   RENDER QUESTION
========================================================= */

renderQuestion(data, nomor, total){

    const pct          = Math.round((nomor / total) * 100);
    const hasOptions   = data.options && data.options.length > 0;
    const hasGambarOpt = hasOptions && data.options.some(o => o.gambar_opsi);

    let html = `
    <div class="min-h-screen bg-slate-100 flex items-start justify-center p-6 pt-10">
        <div class="w-full max-w-2xl fade-in">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm font-semibold text-slate-500">Soal ${nomor} dari ${total}</span>
                <span class="text-sm font-semibold text-slate-400">${pct}%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-1.5 mb-6">
                <div class="bg-navy h-1.5 rounded-full transition-all duration-500" style="width:${pct}%"></div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">`;

    if (data.gambar) {
        html += `
        <div class="mb-6 text-center">
            <img src="${IMG_SOAL}${data.gambar}"
                 class="mx-auto rounded-lg max-h-64"
                 onerror="this.style.display='none'">
        </div>`;
    }

    if (data.pertanyaan) {
        html += `<p class="text-slate-800 text-base font-medium mb-6 leading-relaxed">${data.pertanyaan}</p>`;
    }

    if (hasOptions) {
        if (hasGambarOpt) {
            html += `<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">`;
            data.options.forEach(opt => {
                html += `
                <button data-label="${opt.label}" onclick="answerAndNext(${data.id},'${opt.label}')"
                    class="option-btn border border-slate-200 rounded-xl p-3 hover:bg-slate-50 hover:border-navy transition text-center">
                    <img src="${IMG_SOAL}${opt.gambar_opsi}"
                         class="mx-auto mb-2 max-h-24 rounded"
                         onerror="this.style.display='none'">
                    <span class="text-sm font-bold text-navy">${opt.label}</span>
                </button>`;
            });
            html += `</div>`;
        } else {
            html += `<div class="space-y-3">`;
            data.options.forEach(opt => {
                html += `
                <button data-label="${opt.label}" onclick="answerAndNext(${data.id},'${opt.label}')"
                    class="option-btn block w-full text-left border border-slate-200 rounded-xl p-4 hover:bg-slate-50 hover:border-navy transition text-slate-700">
                    <strong class="text-navy">${opt.label}.</strong> ${opt.opsi_text ?? ""}
                </button>`;
            });
            html += `</div>`;
        }
    } else {
        html += `
        <div class="flex gap-3 items-center">
            <input type="text" id="soal-input-${data.id}" placeholder="Ketik jawaban Anda..."
                class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/10 transition">
            <button id="btn-next-isian"
                class="bg-navy text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition">Lanjut →</button>
        </div>`;
    }

    html += `</div></div></div>`;

    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";

    // Load jawaban terakhir & highlight
    if (hasOptions) {
        fetch(`${API_BASE}/get_last_answer.php?question_id=${data.id}`)
            .then(r => r.json())
            .then(res => {
                if (!res.answer) return;
                // Highlight tombol yang sudah dijawab
                const btns = document.querySelectorAll(".option-btn");
                btns.forEach(btn => {
                    if (btn.dataset.label === res.answer) {
                        btn.classList.remove("border-slate-200", "hover:bg-slate-50", "hover:border-navy");
                        btn.classList.add("border-navy", "bg-navy/5", "ring-2", "ring-navy/20");
                    }
                });
            })
            .catch(() => {});
    } else {
        const inputEl = document.getElementById(`soal-input-${data.id}`);
        // Load jawaban isian sebelumnya
        fetch(`${API_BASE}/get_last_answer.php?question_id=${data.id}`)
            .then(r => r.json())
            .then(res => { if (res.answer) inputEl.value = res.answer; })
            .catch(() => {});

        document.getElementById("btn-next-isian").addEventListener("click", () => {
            const val = inputEl.value.trim();
            if (!val) return;
            saveAnswer(data.id, val)
                .catch(err => console.error("Error saving answer:", err))
                .finally(() => nextQuestion());
        });
        inputEl.addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                const val = this.value.trim();
                if (!val) return;
                saveAnswer(data.id, val)
                    .catch(err => console.error("Error saving answer:", err))
                    .finally(() => nextQuestion());
            }
        });
    }
},


/* =========================================================
   FINISH SCREEN
========================================================= */

async renderFinish(){
    // Tunggu finish_session selesai dulu sebelum tampil tombol dashboard
    try {
        await fetch(`${API_BASE}/finish_session.php`, {
            method: "POST", keepalive: true
        });
    } catch(e) {}

    let html = `
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 w-full max-w-md p-10 text-center fade-in">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-navy mb-3">Tes Selesai</h2>
            <p class="text-slate-500 mb-8">Terima kasih telah menyelesaikan tes. Hasil Anda telah berhasil disimpan.</p>
            <a href="../../dashboard.php"
                class="inline-block bg-navy text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                Kembali ke Dashboard
            </a>
        </div>
    </div>`;

    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";
}

};