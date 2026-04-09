/* =========================================================
   UI CONTROLLER
========================================================= */

const API_BASE  = 'api';
const IMG_SOAL  = '../../images/img_soal/';
const IMG_SEC   = '../../';

let CURRENT_EXAMPLE = null;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function extractInlineOptions(line) {
    const matches = [...String(line).matchAll(/([A-E])\.\s*([^A-E]+?)(?=\s+[A-E]\.\s*|$)/g)];
    return matches
        .map(match => ({ label: match[1], text: match[2].trim() }))
        .filter(item => item.label && item.text);
}

function formatInstructionText(text, sectionUrutan = 0) {
    const lines = String(text ?? '')
        .replace(/\r\n/g, '\n')
        .split('\n')
        .map(line => line.trim())
        .filter(Boolean);

    const sectionCleanCopy = {
        1: {
            intro: 'Pada bagian ini, setiap kalimat memiliki satu kata yang hilang. Anda harus memilih kata yang paling tepat untuk melengkapi kalimat tersebut.',
            steps: [
                'Baca kalimat dengan saksama.',
                'Bandingkan semua pilihan jawaban.',
                'Pilih kata yang paling sesuai secara makna.',
                'Setiap soal hanya memiliki satu jawaban benar.'
            ]
        },
        2: {
            intro: 'Pada bagian ini, terdapat lima kata. Empat kata memiliki kesamaan, sedangkan satu kata berbeda sendiri.',
            steps: [
                'Temukan empat kata yang sejenis.',
                'Pilih satu kata yang paling berbeda dari keempat kata lainnya.',
                'Pastikan pilihan Anda konsisten dengan hubungan antar kata.'
            ]
        },
        3: {
            intro: 'Pada bagian ini, Anda perlu mencari hubungan antara dua kata pertama, lalu mencocokkannya dengan kata yang tepat pada pilihan jawaban.',
            steps: [
                'Perhatikan hubungan kata pertama dan kedua.',
                'Cari hubungan yang sama pada kata ketiga dan pilihan jawaban.',
                'Pilih jawaban yang memiliki pola hubungan yang sama.'
            ]
        },
        4: {
            intro: 'Pada bagian ini, Anda diminta mencari satu kata yang dapat mewakili dua kata yang diberikan.',
            steps: [
                'Baca dua kata yang tersedia dengan saksama.',
                'Temukan kata umum yang mencakup keduanya.',
                'Tulis atau pilih jawaban yang paling tepat.'
            ]
        },
        5: {
            intro: 'Pada bagian ini, Anda mengerjakan soal hitungan sederhana.',
            steps: [
                'Baca pertanyaan dengan teliti.',
                'Hitung secara cepat dan cermat.',
                'Pilih jawaban angka yang benar.'
            ]
        },
        6: {
            intro: 'Pada bagian ini, Anda diminta melanjutkan pola deret angka.',
            steps: [
                'Temukan pola penambahan, pengurangan, atau urutan tertentu.',
                'Lanjutkan deret sesuai pola tersebut.',
                'Pilih angka berikutnya yang paling tepat.'
            ]
        },
        7: {
            intro: 'Pada bagian ini, Anda akan melihat potongan gambar yang harus disusun menjadi bentuk tertentu.',
            steps: [
                'Perhatikan tiap potongan gambar.',
                'Bayangkan potongan tersebut disusun kembali.',
                'Pilih bentuk yang paling sesuai dengan hasil susunan.'
            ]
        },
        8: {
            intro: 'Pada bagian ini, Anda akan membandingkan kubus yang dapat diputar atau digulingkan secara mental.',
            steps: [
                'Perhatikan tanda pada setiap sisi kubus.',
                'Bayangkan putaran atau gulingan kubus.',
                'Pilih kubus yang memiliki susunan tanda yang sama.'
            ]
        },
        9: {
            intro: 'Pada bagian ini, Anda diminta menghafal daftar kata terlebih dahulu, lalu menjawab pertanyaan setelah waktu hafalan selesai.',
            steps: [
                'Gunakan waktu hafalan untuk mengingat kata-kata.',
                'Perhatikan kategori dan huruf awal kata.',
                'Saat masuk ke soal, pilih jawaban berdasarkan kata yang dihafal.'
            ]
        }
    };

    const clean = sectionCleanCopy[Number(sectionUrutan)] || null;

    if (clean) {
        const introCard = `
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 text-blue-700">i</span>
                    Ringkasannya
                </div>
                <p class="mt-3 text-sm leading-relaxed text-slate-700 sm:text-[15px]">${escapeHtml(clean.intro)}</p>
            </div>`;

        const stepsCard = `
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Langkah Mengerjakan</p>
                <ol class="mt-3 space-y-2 text-sm leading-relaxed text-slate-700 sm:text-[15px]">
                    ${clean.steps.map(step => `<li class="rounded-xl border border-slate-200 bg-white px-3 py-2">${escapeHtml(step)}</li>`).join('')}
                </ol>
            </div>`;

        return `<div class="space-y-3">${introCard}${stepsCard}</div>`;
    }

    if (lines.length === 0) {
        return `
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                Instruksi belum tersedia.
            </div>`;
    }

    const blocks = [];
    let introLines = [];
    let exampleMode = false;
    let answerLine = null;

    const flushIntro = () => {
        if (introLines.length === 0) return;
        blocks.push(`
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <p class="text-sm leading-relaxed text-slate-700 sm:text-[15px]">${introLines.map(escapeHtml).join('<br>')}</p>
            </div>`);
        introLines = [];
    };

    const flushAnswer = () => {
        if (!answerLine) return;
        blocks.push(`
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-relaxed text-emerald-800">
                ${escapeHtml(answerLine)}
            </div>`);
        answerLine = null;
    };

    lines.forEach((line) => {
        if (/^contoh\s*:?.*$/i.test(line)) {
            flushIntro();
            flushAnswer();
            exampleMode = true;
            blocks.push(`
                <div class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] text-blue-700 ring-1 ring-blue-100">
                    Contoh
                </div>`);
            return;
        }

        if (/^jawaban yang benar/i.test(line) || /^oleh karena itu/i.test(line) || /^jawabannya adalah/i.test(line)) {
            flushIntro();
            answerLine = line;
            return;
        }

        const options = extractInlineOptions(line);
        if (options.length >= 2) {
            flushIntro();
            flushAnswer();
            blocks.push(`
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        ${options.map(option => `
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                <span class="font-bold text-navy">${escapeHtml(option.label)}.</span>
                                <span>${escapeHtml(option.text)}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>`);
            return;
        }

        if (/^[A-E]\.\s*/i.test(line)) {
            flushIntro();
            flushAnswer();
            const parts = line.split(/\s*(?=[A-E]\.)/).map(part => part.trim()).filter(Boolean);
            const optionCards = parts.map(part => {
                const match = part.match(/^([A-E])\.\s*(.*)$/i);
                if (!match) return '';
                return `
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                        <span class="font-bold text-navy">${escapeHtml(match[1])}.</span>
                        <span>${escapeHtml(match[2])}</span>
                    </div>`;
            }).join('');
            blocks.push(`
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        ${optionCards}
                    </div>
                </div>`);
            return;
        }

        if (exampleMode && !answerLine && introLines.length > 0) {
            // Setelah contoh dimulai, baris berikutnya tetap masuk ke bagian contoh.
            introLines.push(line);
            return;
        }

        if (line.length > 120) {
            flushIntro();
            blocks.push(`
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-relaxed text-slate-700">
                    ${escapeHtml(line)}
                </div>`);
            return;
        }

        introLines.push(line);
    });

    flushIntro();
    flushAnswer();

    return `<div class="space-y-3">${blocks.join('')}</div>`;
}

const UI = {

/* =========================================================
   INSTRUCTION SCREEN
========================================================= */

renderInstruction(section, example){

    CURRENT_EXAMPLE = example;

    let instrGambar = "";
    if (section.gambar_instruksi) {
        instrGambar = `
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-center">
            <img src="${IMG_SEC}${section.gambar_instruksi}"
                 class="mx-auto w-full max-h-[28rem] object-contain p-4"
                 onerror="this.style.display='none'">
        </div>`;
    }

    let html = `
    <div class="min-h-screen bg-[#f5f8fd] bg-[radial-gradient(circle_at_top,rgba(15,30,60,0.06),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(91,157,243,0.08),transparent_25%)]">
        <div class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
                <section class="min-w-0">
                    <div class="rounded-[2rem] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,30,60,0.08)] overflow-hidden">
                        <div class="bg-gradient-to-r from-[#0f1e3c] via-[#1b3f74] to-[#5b9df3] px-5 py-5 text-white sm:px-8">
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-blue-100">Petunjuk Pengerjaan</p>
                            <h2 class="mt-2 text-2xl font-black sm:text-4xl">Instruksi Bagian ${section.urutan}</h2>
                            <p class="mt-2 text-sm text-blue-100/90 sm:text-base">${escapeHtml(section.nama_bagian)}</p>
                        </div>
                        <div class="px-5 py-6 sm:px-8 sm:py-8">
                            <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-relaxed text-slate-600 sm:text-[15px]">
                                ${formatInstructionText(section.instruksi, section.urutan)}
                            </div>
                            ${instrGambar}
                            <button onclick="UI.renderExample()"
                                class="w-full rounded-2xl bg-navy px-8 py-4 text-base font-bold text-white transition hover:opacity-90 sm:text-lg">
                                Mulai Kerjakan Soal
                            </button>
                        </div>
                    </div>
                </section>

                <aside class="min-w-0 lg:sticky lg:top-6 self-start space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Ringkasan</p>
                        <p class="mt-2 text-xl font-black text-slate-900">Bagian ${section.urutan}</p>
                        <p class="mt-1 text-sm text-slate-500">${escapeHtml(section.nama_bagian)}</p>
                        <div class="mt-4 grid gap-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Arah pengerjaan</p>
                                <p class="mt-1 text-sm font-medium text-slate-700">Baca instruksi, pahami contoh, lalu lanjut ke tes utama.</p>
                            </div>
                            <div class="rounded-2xl bg-blue-50 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-500">Tujuan</p>
                                <p class="mt-1 text-sm font-medium text-blue-800">Membantu Anda memahami pola soal sebelum tes dimulai.</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Catatan</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Jika instruksi terlihat panjang, itu memang sengaja ditata bertahap agar lebih mudah dibaca. Gulir ke bawah untuk tombol mulai.</p>
                    </div>
                </aside>
            </div>
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
    <div class="min-h-screen bg-grid bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden">

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
    <div class="min-h-screen bg-[#f5f8fd] bg-[radial-gradient(circle_at_top,rgba(15,30,60,0.06),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(91,157,243,0.08),transparent_25%)]">
        <div class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
                <section class="min-w-0">
                    <div class="rounded-[2rem] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,30,60,0.08)] overflow-hidden">
                        <div class="bg-gradient-to-r from-[#0f1e3c] via-[#1b3f74] to-[#5b9df3] px-5 py-5 text-white sm:px-8">
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-blue-100">Contoh Soal</p>
                            <h2 class="mt-2 text-2xl font-black sm:text-4xl">Latihan Singkat</h2>
                            <p class="mt-2 text-sm text-blue-100/90 sm:text-base">Jawab contoh soal berikut sebelum memulai tes.</p>
                        </div>
                        <div class="px-5 py-6 sm:px-8 sm:py-8">`;

    if (example.gambar) {
        html += `
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-center">
            <img src="${IMG_SEC}images/img_section/${example.gambar}"
                 class="mx-auto w-full max-h-[28rem] object-contain p-4"
                 onerror="this.style.display='none'">
        </div>`;
    }

    if (example.pertanyaan) {
        html += `
        <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-slate-700 leading-relaxed">
            ${escapeHtml(example.pertanyaan)}
        </div>`;
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
                    </div>
                </section>

                <aside class="min-w-0 lg:sticky lg:top-6 self-start space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Panduan</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">Jawab contoh soal dengan benar agar tombol mulai tes aktif.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Langkah</p>
                        <ol class="mt-3 space-y-3 text-sm text-slate-600">
                            <li class="rounded-2xl bg-slate-50 px-4 py-3">1. Baca contoh soal.</li>
                            <li class="rounded-2xl bg-slate-50 px-4 py-3">2. Pilih jawaban yang menurut Anda benar.</li>
                            <li class="rounded-2xl bg-slate-50 px-4 py-3">3. Klik tombol mulai tes setelah benar.</li>
                        </ol>
                    </div>
                </aside>
            </div>
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

renderQuestion(data, nomor, total, navigatorHtml = "", actionHtml = "", answeredPercent = 0, answeredCount = 0){

    const hasOptions   = data.options && data.options.length > 0;
    const hasGambarOpt = hasOptions && data.options.some(o => o.gambar_opsi);

    let html = `
    <div class="min-h-screen bg-[#f5f8fd] bg-[radial-gradient(circle_at_top,rgba(15,30,60,0.06),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(91,157,243,0.08),transparent_25%)]">
        <div class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="min-w-0">
                    <div class="rounded-[2rem] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,30,60,0.08)] overflow-hidden">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-8">
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-sky-600/80">Peta IQ</p>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Question ${nomor}/${total}</h2>
                                    <p class="mt-2 text-sm leading-relaxed text-slate-500">Pilih soal mana saja di bagian ini, simpan jawaban, lalu lanjut saat semua soal sudah terisi.</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">${answeredPercent}%</span>
                            </div>
                        </div>
                        <div class="px-5 py-6 sm:px-8 sm:py-8">`;

    if (data.gambar) {
        html += `
                        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-center">
                            <img src="${IMG_SOAL}${data.gambar}"
                                 class="mx-auto max-h-64 w-full object-contain p-4"
                                 onerror="this.style.display='none'">
                        </div>`;
    }

    if (data.pertanyaan) {
        html += `<p class="mb-6 text-base font-medium leading-relaxed text-slate-800">${data.pertanyaan}</p>`;
    }

    if (hasOptions) {
        if (hasGambarOpt) {
            html += `<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">`;
            data.options.forEach(opt => {
                html += `
                <button data-label="${opt.label}" onclick="saveAnswerOnly(${data.id},'${opt.label}')"
                    class="option-btn group rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-500 hover:shadow-md">
                    <div class="flex items-center justify-between gap-3">
                        <span data-option-badge="${opt.label}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">${opt.label}</span>
                        <span class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Pilihan</span>
                    </div>
                    <img src="${IMG_SOAL}${opt.gambar_opsi}"
                         class="mt-3 max-h-28 w-full rounded-xl border border-slate-100 object-contain bg-slate-50 p-2"
                         onerror="this.style.display='none'">
                </button>`;
            });
            html += `</div>`;
        } else {
            html += `<div class="space-y-3">`;
            data.options.forEach(opt => {
                html += `
                <button data-label="${opt.label}" onclick="saveAnswerOnly(${data.id},'${opt.label}')"
                    class="option-btn group block w-full rounded-2xl border border-slate-200 bg-white p-4 text-left text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-md">
                    <div class="flex items-start gap-4">
                        <span data-option-badge="${opt.label}" class="inline-flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-slate-100 text-sm font-black text-slate-700">${opt.label}</span>
                        <span class="pt-1 text-[15px] leading-relaxed"><strong class="text-slate-900">${opt.label}.</strong> ${opt.opsi_text ?? ""}</span>
                    </div>
                </button>`;
            });
            html += `</div>`;
        }
    } else {
        html += `
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="text" id="soal-input-${data.id}" placeholder="Ketik jawaban Anda..."
                class="flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-700 shadow-sm outline-none transition focus:border-navy focus:ring-4 focus:ring-navy/10">
            <button id="btn-next-isian"
                class="inline-flex items-center justify-center rounded-2xl bg-navy px-6 py-3 font-semibold text-white transition hover:opacity-90">Lanjut →</button>
        </div>`;
    }

    html += `
                        ${actionHtml}
                        </div>
                    </div>
                </section>
                <aside class="min-w-0 lg:sticky lg:top-6 self-start space-y-3">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 shadow-sm backdrop-blur">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Terjawab</p>
                                <p class="mt-2 text-3xl font-black text-slate-900">${answeredPercent}%</p>
                                <p class="text-sm text-slate-500">${answeredCount} dari ${total} soal</p>
                            </div>
                            <div class="rounded-2xl bg-navy/5 px-3 py-2 text-right">
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Bagian aktif</p>
                                <p class="mt-1 text-sm font-black text-navy">${nomor}/${total}</p>
                            </div>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-navy via-[#285aa8] to-[#5b9df3] transition-all duration-500" style="width:${answeredPercent}%"></div>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 shadow-sm backdrop-blur">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Petunjuk</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Klik nomor soal di panel kanan untuk lompat, atau gunakan tombol lanjut jika semua jawaban sudah lengkap.</p>
                    </div>
                    ${navigatorHtml}
                </aside>
            </div>
        </div>
    </div>`;

    document.getElementById("app-viewport").innerHTML = html;
    document.getElementById("app-viewport").className = "";

    // Load jawaban terakhir & highlight
    if (hasOptions) {
        fetch(`${API_BASE}/get_last_answer.php?question_id=${data.id}`)
            .then(r => r.json())
            .then(res => {
                if (!res.answer) return;
                const normalizedSaved = String(res.answer).trim().toLowerCase();
                // Highlight opsi yang tersimpan dengan outline hijau
                const btns = document.querySelectorAll(".option-btn");
                btns.forEach(btn => {
                    const normalizedLabel = String(btn.dataset.label || "").trim().toLowerCase();
                    if (normalizedLabel === normalizedSaved) {
                        btn.classList.remove("border-slate-200", "hover:bg-slate-50", "hover:border-navy", "hover:border-emerald-500", "hover:bg-emerald-50");
                        btn.classList.add("border-emerald-500", "bg-emerald-50", "ring-2", "ring-emerald-200");

                        const badge = btn.querySelector("[data-option-badge]") || btn.querySelector("span.inline-flex");
                        if (badge) {
                            badge.classList.remove("bg-slate-100", "text-slate-700");
                            badge.classList.add("bg-emerald-500", "text-white");
                        }
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
                .finally(() => {
                    // Mark sebagai answered dan touched
                    answeredQuestionNumbers.add(globalQuestionNumber);
                    answeredQuestionIds.add(data.id);
                    touchedQuestionNumbers.add(globalQuestionNumber);
                    // Re-render untuk update button state
                    loadQuestion(globalQuestionNumber, { silent: true });
                });
        });
        inputEl.addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                const val = this.value.trim();
                if (!val) return;
                saveAnswer(data.id, val)
                    .catch(err => console.error("Error saving answer:", err))
                    .finally(() => {
                        // Mark sebagai answered dan touched
                        answeredQuestionNumbers.add(globalQuestionNumber);
                        answeredQuestionIds.add(data.id);
                        touchedQuestionNumbers.add(globalQuestionNumber);
                        // Re-render untuk update button state
                        loadQuestion(globalQuestionNumber);
                    });
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
    <div class="min-h-screen bg-grid bg-slate-100 flex items-center justify-center p-6">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 w-full max-w-md p-10 text-center">
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