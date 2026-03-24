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
});

/* ===============================
   DISABLE BACK BUTTON
   Saat tes sedang berlangsung
================================ */

let tesAktif = false; // flag: true saat soal sedang ditampilkan

function setTesAktif(aktif) {
    tesAktif = aktif;
    if (aktif) {
        // Dorong state baru agar back button tidak keluar halaman
        history.pushState(null, null, location.href);
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
        elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    }
}

/* ===============================
   DETECT EXIT FULLSCREEN
   Hanya paksa fullscreen saat tes aktif
================================ */

document.addEventListener("fullscreenchange", () => {
    if (tesAktif && !document.fullscreenElement) {
        alert("Tes harus dalam mode fullscreen. Klik OK untuk melanjutkan.");
        enableFullscreen();
    }
});

document.addEventListener("webkitfullscreenchange", () => {
    if (tesAktif && !document.webkitFullscreenElement) {
        alert("Tes harus dalam mode fullscreen. Klik OK untuk melanjutkan.");
        enableFullscreen();
    }
});