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
let currentSectionQuestions = [];
let currentQuestionData = null;
let sectionQuestionsPromise = Promise.resolve();
let answeredQuestionIds = new Set();
let answeredQuestionNumbers = new Set();
let touchedQuestionNumbers = new Set(); // Soal yang sudah dijawab ATAU di-skip
let sectionTimeExpired = false;

const IQ_API_BASE = 'api';

const STORAGE_KEY = `peta_progress_${USER.nip}`;
const IS_RESET_START = new URLSearchParams(window.location.search).get('reset') === '1';
const LOADER_MIN_VISIBLE_MS = 320;

let loaderShownAt = 0;
let loaderHideTimer = null;
let loaderIsVisible = false;

function showTransitionLoader(message = 'Memuat...') {
    const loader = document.getElementById('transition-loader');
    const textEl = document.getElementById('transition-loader-text');
    if (textEl) textEl.textContent = message;
    if (!loader) return;

    if (loaderHideTimer) {
        clearTimeout(loaderHideTimer);
        loaderHideTimer = null;
    }

    if (!loaderIsVisible) {
        loaderShownAt = Date.now();
        loaderIsVisible = true;
    }

    loader.classList.add('show');
}

function hideTransitionLoader() {
    const loader = document.getElementById('transition-loader');
    if (!loader || !loaderIsVisible) return;

    const elapsed = Date.now() - loaderShownAt;
    const remaining = Math.max(0, LOADER_MIN_VISIBLE_MS - elapsed);

    if (loaderHideTimer) {
        clearTimeout(loaderHideTimer);
    }

    loaderHideTimer = setTimeout(() => {
        loader.classList.remove('show');
        loaderIsVisible = false;
        loaderHideTimer = null;
    }, remaining);
}

function renderPreparationScreen() {
    const viewport = document.getElementById('app-viewport');
    if (!viewport) return;

    viewport.className = 'bg-grid flex-1 flex items-center justify-center p-6';
    viewport.innerHTML = `
        <div class="w-full max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 shadow-xl">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Persiapan Tes</p>
            <h2 class="mt-2 text-2xl font-black text-navy">Menyiapkan Perangkat Tes</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Pastikan koneksi internet stabil, perangkat terisi daya, dan Anda siap fokus sampai tes selesai.
            </p>
            <div class="mt-5 space-y-2 text-sm text-slate-600">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">1. Tutup aplikasi/tab lain yang tidak diperlukan.</div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">2. Aktifkan mode layar penuh untuk pengalaman tes yang optimal.</div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">3. Setelah mulai, tes tidak bisa diulang dari awal.</div>
            </div>
            <button id="btn-start-preparation" type="button" class="mt-6 w-full rounded-2xl bg-navy px-6 py-3 text-sm font-bold text-white transition hover:opacity-90">
                Saya Siap, Mulai Tes
            </button>
        </div>
    `;

    const startBtn = document.getElementById('btn-start-preparation');
    if (startBtn) {
        startBtn.addEventListener('click', async () => {
            startBtn.disabled = true;
            startBtn.classList.add('opacity-70', 'cursor-not-allowed');
            startBtn.textContent = 'Menyiapkan Tes...';

            showTransitionLoader('Menyiapkan Perangkat Tes...');
            try {
                await enableFullscreen();
            } catch (e) {
                console.warn('Fullscreen request blocked:', e);
            }

            startSession();
            initializeTestFlow();
        });
    }
}

function initializeTestFlow() {
    const saved = loadProgress();

    // Check if this is a fresh start from instruction page (reset=1)
    if (saved && saved.questionNumber > 0 && !IS_RESET_START) {
        hideTransitionLoader();
        showNotification(
            'Lanjutkan Sesi?',
            `Anda memiliki sesi tes yang belum selesai di Bagian ${saved.sectionId}, Soal ${saved.questionNumber}.\n\nLanjutkan dari soal terakhir?`,
            'info',
            true
        ).then((resume) => {
            if (resume) {
                showTransitionLoader('Memuat progres terakhir...');
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
                        sectionQuestionsPromise = loadSectionQuestions(data.section.urutan);
                        Promise.resolve(sectionQuestionsPromise).finally(() => {
                            startTimer(remainingTime);
                            loadQuestion(saved.questionNumber);
                        });
                    })
                    .finally(() => hideTransitionLoader());
                return;
            }
            clearProgress();
            showTransitionLoader('Menyiapkan bagian pertama...');
            loadSection(1);
        });
        return;
    }

    if (IS_RESET_START) {
        clearProgress();
    }

    showTransitionLoader('Menyiapkan bagian pertama...');
    loadSection(1);
}

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
   NOTIFICATION MODAL SYSTEM
========================================================= */

function showNotification(title, message, type = 'info', isConfirm = false) {
    return new Promise((resolve) => {
        const modal = document.getElementById('notification-modal');
        if (!modal) {
            console.error('Notification modal not found');
            resolve(false);
            return;
        }

        document.getElementById('notification-title').textContent = title;
        document.getElementById('notification-message').textContent = message;

        const iconEl = document.getElementById('notification-icon');
        const iconMap = {
            success: '✓',
            error: '✕',
            info: 'ℹ'
        };
        iconEl.textContent = iconMap[type] || '✓';
        iconEl.className = `notification-icon ${type}`;

        const yesBtn = document.getElementById('notification-yes');
        const noBtn = document.getElementById('notification-no');
        const okBtn = document.getElementById('notification-ok');

        if (isConfirm) {
            okBtn.style.display = 'none';
            yesBtn.style.display = 'inline-block';
            noBtn.style.display = 'inline-block';
        } else {
            okBtn.style.display = 'inline-block';
            yesBtn.style.display = 'none';
            noBtn.style.display = 'none';
        }

        const newYesBtn = yesBtn.cloneNode(true);
        const newNoBtn = noBtn.cloneNode(true);
        const newOkBtn = okBtn.cloneNode(true);

        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
        noBtn.parentNode.replaceChild(newNoBtn, noBtn);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);

        newYesBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(true);
        });
        newNoBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(false);
        });
        newOkBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            resolve(true);
        });

        modal.style.display = 'flex';
    });
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
    renderPreparationScreen();
});

/* =========================================================
   LOAD SECTION
========================================================= */

function loadSection(id) {
    showTransitionLoader('Memuat instruksi bagian...');
    fetch(`${IQ_API_BASE}/get_section.php?id=${id}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("get_section parse error:", text); return; }

            if (!data.section) { finishTest(); return; }

            const sectionTitleEl = document.getElementById("section-title");
            if (sectionTitleEl) {
                sectionTitleEl.innerText = data.section.nama_bagian || `Bagian ${id}`;
            }

            globalSectionId      = data.section.urutan;
            totalQuestions       = parseInt(data.section.jumlah_soal);
            sectionTime          = parseInt(data.section.waktu_detik) || 300;
            waktuHafalan         = parseInt(data.section.waktu_hafalan) || 0;
            globalQuestionNumber = 1;
            sectionTimeExpired   = false;
            answeredQuestionIds  = new Set();
            answeredQuestionNumbers = new Set();
            touchedQuestionNumbers = new Set();
            currentSectionQuestions = [];
            sectionQuestionsPromise = loadSectionQuestions(data.section.urutan);

            clearInterval(timerInterval);
            timerInterval = null;
            document.getElementById("timer-box").classList.add("hidden");

            // Jika section punya waktu_hafalan, tampilkan halaman hafalan dulu
            if (waktuHafalan > 0) {
                loadMemoryItems(data.section.id);
            } else if (IS_RESET_START && data.section.urutan === 1) {
                startExam();
            } else {
                UI.renderInstruction(data.section, data.example);
            }
        })
        .catch(err => console.error("Error loading section:", err))
        .finally(() => hideTransitionLoader());
}

function loadSectionQuestions(sectionUrutan) {
    return fetch(`${IQ_API_BASE}/get_questions.php?section=${sectionUrutan}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("section question list parse error:", text); return []; }

            currentSectionQuestions = Array.isArray(data.questions) ? data.questions : [];
            if (currentSectionQuestions.length > 0) {
                // RESET answered tracking untuk bagian baru
                answeredQuestionNumbers.clear();
                answeredQuestionIds.clear();
                touchedQuestionNumbers.clear();
                
                totalQuestions = currentSectionQuestions.length;
                currentSectionQuestions.forEach(q => {
                    const nomorSoal = parseInt(q.nomor_soal, 10);
                    if (q.answered) {
                        answeredQuestionIds.add(parseInt(q.id, 10));
                        if (!Number.isNaN(nomorSoal)) {
                            answeredQuestionNumbers.add(nomorSoal);
                            touchedQuestionNumbers.add(nomorSoal); // Ditandai sebagai "touched"
                        }
                    }
                });
            }

            return currentSectionQuestions;
        })
        .catch(err => {
            console.error("Error loading section questions:", err);
            currentSectionQuestions = [];
            return [];
        });
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
    Promise.resolve(sectionQuestionsPromise).finally(() => {
        globalQuestionNumber = 1;
        if (typeof setTesAktif === "function") setTesAktif(true);
        enableFullscreen();
        clearInterval(timerInterval);
        timerInterval = null;
        startTimer(sectionTime);
        loadQuestion(1);
    });
}

/* =========================================================
   LOAD QUESTION
========================================================= */

function loadQuestion(questionNumber = globalQuestionNumber, options = {}) {
    const silent = Boolean(options && options.silent);
    if (!silent) {
        showTransitionLoader('Memuat soal...');
    }
    globalQuestionNumber = questionNumber;
    
    // Jika user mencoba melompat ke soal melebihi total soal (ingin lanjut section)
    if (globalQuestionNumber > totalQuestions) {
        // Validasi semua soal sudah dijawab sebelum lanjut section
        if (!isSectionFullyAnswered()) {
            const unansweredList = Array.from({ length: totalQuestions }, (_, i) => i + 1)
                .filter(no => !answeredQuestionNumbers.has(no));
            
            showNotification('Soal Belum Lengkap', `Soal yang belum dijawab:\n${unansweredList.join(', ')}`, 'error');
            
            // Redirect ke soal pertama yang belum dijawab (non-recursive)
            if (unansweredList.length > 0) {
                globalQuestionNumber = unansweredList[0];
                // Continue dengan normal fetch flow di bawah
            } else {
                if (!silent) {
                    hideTransitionLoader();
                }
                return;
            }
        } else {
            // Jika sudah fully answered, boleh lanjut ke section berikutnya
            nextSection();
            if (!silent) {
                hideTransitionLoader();
            }
            return;
        }
    }

    saveProgress();

    fetch(`${IQ_API_BASE}/get_questions.php?section=${globalSectionId}&q=${globalQuestionNumber}`)
        .then(res => res.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error("get_questions parse error:", text); return; }

            if (!data.exists) { nextSection(); return; }

            currentQuestionData = data;
            if (data.answered) {
                answeredQuestionIds.add(parseInt(data.id, 10));
                answeredQuestionNumbers.add(globalQuestionNumber);
                touchedQuestionNumbers.add(globalQuestionNumber); // Mark sebagai touched jika sudah dijawab
            }

            const canFinishSection = isSectionFullyAnswered();
            const remainingCount = getRemainingQuestionCount();
            const answeredCount = getAnsweredQuestionCount();
            const answeredPercent = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;

            UI.renderQuestion(
                data,
                globalQuestionNumber,
                totalQuestions,
                buildQuestionNavigator(),
                buildQuestionActions(canFinishSection, remainingCount),
                answeredPercent,
                answeredCount
            );
        })
        .catch(err => console.error("Error loading question:", err))
        .finally(() => {
            if (!silent) {
                hideTransitionLoader();
            }
        });
}

function getAnsweredQuestionCount() {
    return answeredQuestionNumbers.size;
}

function isSectionFullyAnswered() {
    if (totalQuestions <= 0) return false;
    return answeredQuestionNumbers.size >= totalQuestions;
}

function getRemainingQuestionCount() {
    return Math.max(0, totalQuestions - getAnsweredQuestionCount());
}

function buildQuestionNavigator() {
    const questions = currentSectionQuestions.length > 0
        ? currentSectionQuestions
        : Array.from({ length: totalQuestions }, (_, index) => ({
            id: index + 1,
            nomor_soal: index + 1,
            answered: answeredQuestionIds.has(index + 1)
        }));

    if (!questions.length) return "";

    const buttons = questions.map((question, index) => {
        const parsedNumber = parseInt(question.nomor_soal, 10);
        const questionNumber = !Number.isNaN(parsedNumber) ? parsedNumber : (index + 1);
        const isActive = questionNumber === globalQuestionNumber;
        const isAnswered = answeredQuestionNumbers.has(questionNumber) || question.answered;
        const isTouched = touchedQuestionNumbers.has(questionNumber);
        
        // Disabled HANYA jika belum "touched" AND bukan soal pertama AND bukan soal aktif
        const isDisabled = !isTouched && questionNumber !== 1 && !isActive;
        
        const classes = [
            "question-nav-btn",
            "inline-flex items-center justify-center rounded-xl border text-sm font-bold transition px-3 py-2",
            isDisabled
                ? "bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed"
                : isActive
                    ? "bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-300"  // Active = Blue (regardless of answered status)
                    : isAnswered
                        ? "bg-emerald-50 text-emerald-800 border-emerald-400 hover:border-emerald-500 hover:bg-emerald-100"
                        : "bg-white text-slate-500 border-slate-200 hover:border-navy hover:text-navy"
        ].join(" ");

        return `
            <button type="button" onclick="${isDisabled ? 'return false' : `goToQuestion(${questionNumber})`}" ${isDisabled ? 'disabled' : ''} class="${classes}">
                <span class="flex items-center gap-1.5">
                    <span>${questionNumber}</span>
                    ${isAnswered && !isActive ? '<span class="h-2 w-2 rounded-full bg-emerald-500"></span>' : ''}
                    ${isDisabled ? '<span class="text-[10px]" title="Soal belum disentuh">🔒</span>' : ''}
                </span>
            </button>
        `;
    }).join("");

    return `
        <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_20px_50px_rgba(15,30,60,0.08)]">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Navigasi Soal</p>
                    <p class="mt-1 text-sm text-slate-500">Klik nomor soal yang sudah dijawab atau di-skip.</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-navy"></span>Aktif</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Terjawab</span>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-5 gap-2 sm:grid-cols-6 lg:grid-cols-5 xl:grid-cols-6">
                ${buttons}
            </div>
        </div>
    `;
}

function buildQuestionActions(canFinishSection, remainingCount) {
    const isLastQuestion = globalQuestionNumber === totalQuestions;
    const isCurrentAnswered = answeredQuestionNumbers.has(globalQuestionNumber);
    
    let nextButtonText = isLastQuestion ? "Selesai & Lanjut Bagian Berikutnya" : "Lanjut ke Soal Berikutnya →";
    let nextButtonClass = isCurrentAnswered 
        ? "bg-navy text-white hover:opacity-90"
        : "bg-slate-300 text-slate-500 cursor-not-allowed";
    let nextButtonAttr = isCurrentAnswered ? "" : "disabled";

    return `
        <div class="mt-6 border-t border-slate-100 pt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-500">
                ${isCurrentAnswered
                    ? "Soal sudah dijawab. Lanjut ke soal berikutnya atau lewati."
                    : `Soal ini belum dijawab. Pilih jawaban atau lewati soal.`}
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
            <button type="button" onclick="skipQuestionAndMarkTouched()"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 font-semibold text-slate-600 shadow-sm transition hover:border-navy hover:text-navy">
                Lewati Soal
            </button>
            <button type="button" onclick="nextQuestionOrSection()" ${nextButtonAttr}
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold shadow-sm transition ${nextButtonClass}">
                ${nextButtonText}
            </button>
            </div>
        </div>
    `;
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

// Hanya save jawaban, tidak auto-navigate
async function saveAnswerOnly(questionId, label) {
    try {
        await saveAnswer(questionId, label);
        answeredQuestionIds.add(parseInt(questionId, 10));
        answeredQuestionNumbers.add(globalQuestionNumber);
        touchedQuestionNumbers.add(globalQuestionNumber);
    } catch (e) {
        console.error("Error saving answer:", e);
    }
    // Re-render untuk update button state
    loadQuestion(globalQuestionNumber, { silent: true });
}

async function answerAndNext(questionId, label) {
    try {
        await saveAnswer(questionId, label);
        answeredQuestionIds.add(parseInt(questionId, 10));
        answeredQuestionNumbers.add(globalQuestionNumber);
        touchedQuestionNumbers.add(globalQuestionNumber);
    } catch (e) {
        console.error("Error saving answer:", e);
    }
    nextQuestion();
}

/* =========================================================
   NEXT QUESTION
========================================================= */

function nextQuestion() {
    loadQuestion(globalQuestionNumber + 1);
}

function syncAnsweredQuestionFromCurrentData() {
    if (!currentQuestionData) return;
    if (currentQuestionData.answered) {
        answeredQuestionIds.add(currentQuestionData.id);
    }
}

async function persistCurrentOpenAnswer() {
    if (!currentQuestionData) return;

    const hasOptions = currentQuestionData.options && currentQuestionData.options.length > 0;
    if (hasOptions) return;

    const inputEl = document.getElementById(`soal-input-${currentQuestionData.id}`);
    if (!inputEl) return;

    const value = inputEl.value.trim();
    if (!value) return;

    try {
        await saveAnswer(currentQuestionData.id, value);
        answeredQuestionIds.add(parseInt(currentQuestionData.id, 10));
        answeredQuestionNumbers.add(globalQuestionNumber);
    } catch (error) {
        console.error("Error saving typed answer:", error);
    }
}

async function goToQuestion(questionNumber) {
    // Hanya bisa navigasi ke soal pertama atau soal yang sudah "touched"
    if (questionNumber !== 1 && !touchedQuestionNumbers.has(questionNumber)) {
        await showNotification('Akses Terbatas', 'Anda hanya bisa memilih soal yang sudah dijawab atau di-skip.', 'error');
        return;
    }
    
    try {
        await persistCurrentOpenAnswer();
    } catch (error) {
        console.error("Error persisting answer:", error);
    }
    
    loadQuestion(questionNumber);
}

async function skipQuestion() {
    try {
        await persistCurrentOpenAnswer();
    } catch (error) {
        console.error("Error persisting answer:", error);
    }
    
    // Jika soal saat ini belum dijawab, minta konfirmasi
    const isCurrentAnswered = answeredQuestionNumbers.has(globalQuestionNumber);
    if (!isCurrentAnswered) {
        showNotification(
            'Konfirmasi Skip',
            `Soal nomor ${globalQuestionNumber} belum dijawab.\n\nLanjut ke soal berikutnya tanpa menjawab?`,
            'info',
            true
        ).then((confirmSkip) => {
            if (confirmSkip) {
                nextQuestion();
            }
        });
        return;
    }
    
    nextQuestion();
}

// Fungsi baru: skip soal dan mark sebagai "touched"
async function skipQuestionAndMarkTouched() {
    try {
        await persistCurrentOpenAnswer();
    } catch (error) {
        console.error("Error persisting answer:", error);
    }
    
    // Mark soal saat ini sebagai "touched" (bahkan jika belum dijawab)
    touchedQuestionNumbers.add(globalQuestionNumber);
    
    // Lanjut ke soal berikutnya atau section berikutnya
    if (globalQuestionNumber < totalQuestions) {
        loadQuestion(globalQuestionNumber + 1);
    } else {
        // Sudah di soal terakhir, cek validasi untuk lanjut section
        checkAndFinishSection();
    }
}

// Fungsi baru: lanjut ke soal/section berikutnya
async function nextQuestionOrSection() {
    const isCurrentAnswered = answeredQuestionNumbers.has(globalQuestionNumber);
    
    if (!isCurrentAnswered) {
        await showNotification('Soal Belum Dijawab', 'Soal ini harus dijawab sebelum melanjut.', 'error');
        return;
    }
    
    try {
        await persistCurrentOpenAnswer();
    } catch (error) {
        console.error("Error persisting answer:", error);
    }
    
    // Mark sebagai touched
    touchedQuestionNumbers.add(globalQuestionNumber);
    
    // Jika bukan soal terakhir, lanjut ke soal berikutnya
    if (globalQuestionNumber < totalQuestions) {
        loadQuestion(globalQuestionNumber + 1);
    } else {
        // Soal terakhir - cek validasi semua soal dijawab sebelum lanjut section
        checkAndFinishSection();
    }
}

// Fungsi baru: validasi dan finish section
async function checkAndFinishSection() {
    if (!isSectionFullyAnswered()) {
        // Buat modal untuk tunjukkan soal yang belum dijawab
        const unansweredList = Array.from({ length: totalQuestions }, (_, i) => i + 1)
            .filter(no => !answeredQuestionNumbers.has(no));
        
        showIncompleteModal(unansweredList);
        return;
    }
    
    // Semua sudah dijawab, lanjut ke section berikutnya
    nextSection();
}

// Fungsi tambahan: tampilkan modal untuk soal yang belum dijawab
function showIncompleteModal(unansweredList) {
    const html = `
    <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4">
        <div class="rounded-3xl bg-white shadow-2xl max-w-md w-full p-8">
            <div class="rounded-2xl bg-red-50 border border-red-100 px-4 py-3 mb-4">
                <p class="text-sm font-bold text-red-700">⚠️ Soal Belum Lengkap</p>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-3">Ada ${unansweredList.length} Soal yang Belum Dijawab</h3>
            <p class="text-sm text-slate-600 mb-4">Mohon selesaikan soal berikut terlebih dahulu sebelum melanjutkan ke bagian berikutnya:</p>
            
            <div class="bg-slate-50 rounded-xl p-4 mb-6 max-h-40 overflow-y-auto">
                <div class="flex flex-wrap gap-2">
                    ${unansweredList.map(no => `
                    <button onclick="goToQuestion(${no}); closeIncompleteModal();" 
                        class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg text-sm transition">
                        Soal ${no}
                    </button>
                    `).join('')}
                </div>
            </div>
            
            <button onclick="closeIncompleteModal()" 
                class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-3 rounded-xl transition">
                Kembali Mengerjakan
            </button>
        </div>
    </div>
    `;
    
    const modalEl = document.createElement('div');
    modalEl.id = 'incomplete-modal';
    modalEl.innerHTML = html;
    document.body.appendChild(modalEl);
}

function closeIncompleteModal() {
    const modal = document.getElementById('incomplete-modal');
    if (modal) modal.remove();
}

async function finishCurrentSection() {
    if (sectionTimeExpired) {
        nextSection();
        return;
    }

    if (!isSectionFullyAnswered()) {
        await showNotification('Bagian Belum Lengkap', 'Semua soal pada bagian ini harus dijawab dulu sebelum lanjut.', 'error');
        return;
    }

    await persistCurrentOpenAnswer();
    showNotification(
        'Lanjut Bagian',
        'Lanjut ke bagian berikutnya? Soal yang belum dijawab akan tetap kosong.',
        'info',
        true
    ).then((proceed) => {
        if (proceed) {
            nextSection();
        }
    });
}

/* =========================================================
   NEXT SECTION
========================================================= */

function nextSection() {
    showTransitionLoader('Memuat bagian berikutnya...');
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
        .catch(() => finishTest())
        .finally(() => hideTransitionLoader());
}

/* =========================================================
   TIMER
========================================================= */

function getTimerWarningThreshold(seconds) {
    if (seconds <= 180) {
        return 30;
    }

    return 300;
}

function startTimer(seconds) {
    remainingTime = seconds;
    clearInterval(timerInterval);
    timerInterval = null;

    const timerWarningKey = `iq-${globalSectionId || 'timer'}`;
    const warningThreshold = getTimerWarningThreshold(seconds);

    if (window.TestTimerAlert) {
        window.TestTimerAlert.reset(timerWarningKey);
    }

    document.getElementById("timer-box").classList.remove("hidden");

    timerInterval = setInterval(() => {
        remainingTime--;
        saveProgress();

        const m = Math.floor(remainingTime / 60);
        const s = remainingTime % 60;
        document.getElementById("timer-display").innerText =
            String(m).padStart(2,"0") + ":" + String(s).padStart(2,"0");

        if (window.TestTimerAlert) {
            window.TestTimerAlert.warn({
                key: timerWarningKey,
                remaining: remainingTime,
                threshold: warningThreshold,
                title: 'Waktu Hampir Habis',
                        message: warningThreshold <= 30
                            ? 'Sisa waktu tinggal 30 detik. Segera selesaikan jawaban Anda.'
                            : 'Sisa waktu tinggal 5 menit. Periksa jawaban Anda sekarang.',
                type: 'info'
            });
        }

        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            timerInterval = null;
            handleTimeExpired();
        }
    }, 1000);
}

async function handleTimeExpired() {
    sectionTimeExpired = true;
    lockQuestionControlsOnTimeout();

    try {
        await persistCurrentOpenAnswer();
    } catch (error) {
        console.error("Error saving answer on timeout:", error);
    }

    nextSection();
}

function lockQuestionControlsOnTimeout() {
    const viewport = document.getElementById("app-viewport");
    if (!viewport) return;

    const controls = viewport.querySelectorAll("button, input, textarea, select");
    controls.forEach(control => {
        if (control.tagName === "BUTTON") {
            control.disabled = true;
        } else {
            control.readOnly = true;
            control.disabled = true;
        }
    });

    const notice = document.createElement("div");
    notice.className = "fixed inset-x-4 bottom-4 z-50 rounded-2xl bg-navy px-4 py-3 text-white shadow-2xl";
    notice.innerHTML = `
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-bold">Waktu habis</p>
                <p class="text-xs text-blue-100">Bagian ini akan dilanjutkan otomatis.</p>
            </div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-lg font-black">→</span>
        </div>
    `;
    document.body.appendChild(notice);

    setTimeout(() => notice.remove(), 1500);
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
            <div class="text-center p-10">
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