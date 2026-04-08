/*
|--------------------------------------------------------------------------
| SECURITY ENGINE
|--------------------------------------------------------------------------
| Fitur keamanan tes
|--------------------------------------------------------------------------
*/

/* ===============================
   DISABLE RIGHT CLICK
================================ */

document.addEventListener("contextmenu", function(e) {
    e.preventDefault();
});

/* ===============================
   DISABLE COPY PASTE CUT
================================ */

document.addEventListener("copy",  e => e.preventDefault());
document.addEventListener("paste", e => e.preventDefault());
document.addEventListener("cut",   e => e.preventDefault());

/* ===============================
   DISABLE KEYBOARD SHORTCUTS
   F12, Ctrl+U, Ctrl+Shift+I, dll
================================ */

document.addEventListener("keydown", function(e) {
    // F12
    if (e.key === "F12") { e.preventDefault(); return false; }
    // Ctrl+U (view source), Ctrl+S, Ctrl+P
    if (e.ctrlKey && ["u","s","p"].includes(e.key.toLowerCase())) { e.preventDefault(); return false; }
    // Ctrl+Shift+I / Ctrl+Shift+J (devtools)
    if (e.ctrlKey && e.shiftKey && ["i","j"].includes(e.key.toLowerCase())) { e.preventDefault(); return false; }
    // Screenshot shortcuts (best effort, tidak didukung konsisten di semua browser/OS)
    if (e.key === "PrintScreen") { e.preventDefault(); return false; }
    if (e.metaKey && e.shiftKey && ["3", "4", "5"].includes(e.key)) { e.preventDefault(); return false; }
    if (e.ctrlKey && e.shiftKey && ["s", "c"].includes(e.key.toLowerCase())) { e.preventDefault(); return false; }
});

/* ===============================
   DISABLE BACK BUTTON
   Saat tes sedang berlangsung
================================ */

let tesAktif = false; // flag: true saat soal sedang ditampilkan
let fullscreenRetryAttached = false;

function setTesAktif(aktif) {
    tesAktif = aktif;
    if (aktif) {
        // Dorong state baru agar back button tidak keluar halaman
        history.pushState(null, null, location.href);
        enableFullscreen();

        // Retry fullscreen pada interaksi user berikutnya bila browser menolak auto request.
        if (!fullscreenRetryAttached) {
            const retryFullscreen = () => {
                if (!tesAktif) return;
                if (!document.fullscreenElement) {
                    enableFullscreen();
                }
            };
            document.addEventListener('click', retryFullscreen, true);
            document.addEventListener('keydown', retryFullscreen, true);
            fullscreenRetryAttached = true;
        }
    }
}

window.addEventListener("popstate", function() {
    if (tesAktif) {
        // Cegah back, kembalikan ke halaman ini
        history.pushState(null, null, location.href);
    }
});

/* ===============================
   FULLSCREEN MODE
================================ */

function enableFullscreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        return elem.requestFullscreen().catch(() => false);
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
        return Promise.resolve(true);
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
        return Promise.resolve(true);
    }
    return Promise.resolve(false);
}

/* ===============================
   DETECT EXIT FULLSCREEN
   Dibiarkan kosong: mode privasi sudah dihapus
================================ */
