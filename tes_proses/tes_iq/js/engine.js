/* =========================================================
   ENGINE TES IQ
========================================================= */

let globalSectionId      = 1;
let globalQuestionNumber = 1;
let totalQuestions       = 0;
let sectionTime          = 0;
let waktuHafalan         = 0;
let timerInterval        = null;
let remainingTime        = 0;

const IQ_API_BASE = 'api';

const STORAGE_KEY = `peta_progress_${USER.nip}`;

/* =========================================================
   SAVE & LOAD PROGRESS
========================================================= */

function saveProgress() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        sectionId      : globalSectionId,
        questionNumber : globalQuestionNumber,
        totalQuestions : totalQuestions,
        sectionTime    : sectionTime,
        remainingTime  : remainingTime,
        timestamp      : Date.now()
    }));
}

function loadProgress() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch(e) { return null; }
}

function clearProgress() {
    localStorage.removeItem(STORAGE_KEY);
}

/* =========================================================
   START APP
========================================================= */

/* =========================================================
   SESSION MANAGEMENT
========================================================= */

function startSession() {
    fetch(`${IQ_API_BASE}/start_session.php`, { method: "POST" });
}

function finishSession() {
    // keepalive agar request tetap jalan walau halaman mau pindah
    fetch(`${IQ_API_BASE}/finish_session.php`, {
        method: "POST",
        keepalive: true
    });
}

/* =========================================================
   START APP
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    startSession(); // Buat/cek session saat halaman dibuka
    const saved = loadProgress();

    if (saved && saved.questionNumber > 0) {
        const resume = confirm(
            `Anda memiliki sesi tes yang belum selesai di Bagian ${saved.sectionId}, Soal ${saved.questionNumber}.\n\nLanjutkan dari soal terakhir?`
        );
        if (resume) {
            globalSectionId      = saved.sectionId;
            globalQuestionNumber = saved.questionNumber;
            totalQuestions       = saved.totalQuestions;
            sectionTime          = saved.sectionTime;
            remainingTime        = saved.remainingTime > 0 ? saved.remainingTime : saved.sectionTime;

            fetch(`${IQ_API_BASE}/get_section.php?id=${globalSectionId}`)
                .then(res => res.text())
                .then(text => {
                    let data;
                    try { data = JSON.parse(text); } catch(e) { loadSection(1); return; }
                    if (!data.section) { loadSection(1); return; }
                    document.getElementById("section-title").innerText = data.section.nama_bagian;
                    startTimer(remainingTime);
                    loadQuestion();
                });
            return;
        } else {
            clearProgress();
        }
    }

    loadSection(1);
});

/* =========================================================
   LOAD SECTION
========================================================= */

function loadSection(id) {
    fetch(`${IQ_API_BASE}/get_section.php?id=${id}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("get_section parse error:", text); return; }

            if (!data.section) { finishTest(); return; }

            globalSectionId      = data.section.urutan;
            totalQuestions       = parseInt(data.section.jumlah_soal);
            sectionTime          = parseInt(data.section.waktu_detik) || 300;
            waktuHafalan         = parseInt(data.section.waktu_hafalan) || 0;
            globalQuestionNumber = 1;

            clearInterval(timerInterval);
            timerInterval = null;
            document.getElementById("timer-box").classList.add("hidden");

            // Jika section punya waktu_hafalan, tampilkan halaman hafalan dulu
            if (waktuHafalan > 0) {
                loadMemoryItems(data.section.id);
            } else {
                UI.renderInstruction(data.section, data.example);
            }
        })
        .catch(err => console.error("Error loading section:", err));
}

/* =========================================================
   LOAD MEMORY ITEMS (untuk bagian hafalan)
========================================================= */

function loadMemoryItems(sectionDbId) {
    fetch(`${IQ_API_BASE}/get_memory.php?section_id=${sectionDbId}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("get_memory parse error:", text); startExam(); return; }

            UI.renderMemory(data.items || [], waktuHafalan);
        })
        .catch(() => startExam()); // fallback: langsung ke soal
}

/* =========================================================
   START EXAM
========================================================= */

function startExam() {
    globalQuestionNumber = 1;
    if (typeof setTesAktif === "function") setTesAktif(true);
    enableFullscreen();
    clearInterval(timerInterval);
    timerInterval = null;
    startTimer(sectionTime);
    loadQuestion();
}

/* =========================================================
   LOAD QUESTION
========================================================= */

function loadQuestion() {
    if (globalQuestionNumber > totalQuestions) { nextSection(); return; }

    saveProgress();

    fetch(`${IQ_API_BASE}/get_questions.php?section=${globalSectionId}&q=${globalQuestionNumber}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("get_questions parse error:", text); return; }

            if (!data.exists) { nextSection(); return; }

            UI.renderQuestion(data, globalQuestionNumber, totalQuestions);
        })
        .catch(err => console.error("Error loading question:", err));
}

/* =========================================================
   SAVE ANSWER
========================================================= */

function saveAnswer(questionId, label) {
    return fetch(`${IQ_API_BASE}/save_answer.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ question_id: questionId, answer: label })
    });
}

async function answerAndNext(questionId, label) {
    try {
        await saveAnswer(questionId, label);
    } catch (e) {
        console.error("Error saving answer:", e);
    }
    nextQuestion();
}

/* =========================================================
   NEXT QUESTION
========================================================= */

function nextQuestion() {
    globalQuestionNumber++;
    if (globalQuestionNumber > totalQuestions) { nextSection(); return; }
    loadQuestion();
}

/* =========================================================
   NEXT SECTION
========================================================= */

function nextSection() {
    clearInterval(timerInterval);
    timerInterval = null;

    const nextId = globalSectionId + 1;

    fetch(`${IQ_API_BASE}/get_section.php?id=${nextId}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { finishTest(); return; }

            if (!data.section) { finishTest(); return; }

            loadSection(nextId);
        })
        .catch(() => finishTest());
}

/* =========================================================
   TIMER
========================================================= */

function startTimer(seconds) {
    remainingTime = seconds;
    clearInterval(timerInterval);
    timerInterval = null;

    document.getElementById("timer-box").classList.remove("hidden");

    timerInterval = setInterval(() => {
        remainingTime--;
        saveProgress();

        const m = Math.floor(remainingTime / 60);
        const s = remainingTime % 60;
        document.getElementById("timer-display").innerText =
            String(m).padStart(2,"0") + ":" + String(s).padStart(2,"0");

        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            timerInterval = null;
            nextSection();
        }
    }, 1000);
}


/* =========================================================
   FINISH TEST
========================================================= */
function finishTest() {
    clearInterval(timerInterval);
    timerInterval = null;
    
    // Tampilkan pesan loading agar peserta tidak menutup halaman
    const viewport = document.getElementById("app-viewport");
    if (viewport) {
        viewport.innerHTML = `
            <div class="text-center p-10 fade-in">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-navy mx-auto mb-4"></div>
                <h2 class="text-xl font-bold text-navy">Memproses Hasil Tes...</h2>
                <p class="text-slate-500 mt-2">Mohon tunggu sebentar, data Anda sedang disimpan.</p>
            </div>
        `;
    }

    // Memanggil API finish_test.php untuk menghitung dan menyimpan skor ke database
    fetch(`${IQ_API_BASE}/finish_test.php`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                console.log("Skor berhasil disimpan ke iq_results:", data.skor);
            } else {
                console.error("Gagal menyimpan skor:", data.message);
            }
            // Setelah berhasil disimpan, baru tampilkan layar selesai
            UI.renderFinish();
        })
        .catch(err => {
            console.error("Network Error saat menyimpan hasil:", err);
            // Tetap tampilkan layar selesai jika koneksi terputus
            UI.renderFinish(); 
        });
}