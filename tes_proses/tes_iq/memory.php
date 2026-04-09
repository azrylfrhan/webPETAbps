<?php
require_once '../backend/config.php';
require_once '../backend/auth_check.php';

$nip  = $_SESSION['nip'];
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tes IQ Bagian 9 - Hafalan</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

/* ===============================
   PAGE STYLE
================================ */

body{font-family:'Plus Jakarta Sans', sans-serif;background:#f1f5f9;margin:0;color:#1e293b;}

.container{
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:32px;
    border-radius:24px;
    border:1px solid #e2e8f0;
    box-shadow:0 12px 30px rgba(15, 23, 42, 0.08);
}

/* ===============================
   HEADER
================================ */

.header{
    text-align:center;
    margin-bottom:24px;
}

.header h2{
    margin-bottom:10px;
    color:#0f1e3c;
    font-size:30px;
    font-weight:800;
}

/* ===============================
   TIMER
================================ */

.timer{
    font-size:24px;
    font-weight:800;
    color:#b91c1c;
    text-align:center;
    margin-bottom:26px;
    padding:14px;
    border-radius:14px;
    border:1px solid #fecaca;
    background:#fef2f2;
}

/* ===============================
   MEMORY LIST
================================ */

.memory-row{
    display:flex;
    font-size:18px;
    margin-bottom:10px;
    padding:12px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#f8fafc;
}

.memory-category{
    width:160px;
    font-weight:700;
    color:#0f1e3c;
}

.memory-colon{
    width:20px;
    color:#475569;
}

.memory-words{
    flex:1;
    color:#334155;
}

</style>

<script src="../../test_timer_alert.js"></script>

</head>

<body>


<header class="bg-[#0f1e3c] text-white shadow-md">
<div style="max-width:1100px;margin:0 auto;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;">
    <div style="display:flex;align-items:center;gap:10px;">
        <img src="../../images/logobps.png" alt="Logo BPS" style="height:40px;width:auto;object-fit:contain;">
        <div>
            <p style="font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#93c5fd;margin:0;">Tes IQ</p>
            <p style="font-weight:700;font-size:14px;margin:0;">PETA — Pemetaan Potensi Pegawai</p>
        </div>
    </div>
    <div style="text-align:right;">
        <p style="font-size:13px;font-weight:700;margin:0;"><?= htmlspecialchars($nama) ?></p>
        <p style="font-size:12px;color:#bfdbfe;margin:0;"><?= htmlspecialchars($nip) ?></p>
    </div>
</div>
</header>


<div class="container">

<div class="header">

<h2>Hafalkan Kata Berikut</h2>

<p style="margin:0;color:#475569;line-height:1.6;">Anda memiliki waktu <strong>2 menit</strong> untuk menghafal daftar kata berikut sebelum lanjut ke soal hafalan.</p>

</div>


<div class="timer">

Waktu Menghafal: <span id="timer">02:00</span>

</div>


<div id="memory-list"></div>


</div>



<script>

/* ===============================
   LOAD MEMORY WORDS
================================ */

fetch("api/get_memory.php")

.then(res => res.json())

.then(data => {

    let grouped = {};

    data.forEach(item => {

        if(!grouped[item.kategori]){

            grouped[item.kategori] = [];

        }

        grouped[item.kategori].push(item.kata);

    });

    let html = "";

    for(let kategori in grouped){

        html += `
        <div class="memory-row">

            <div class="memory-category">
                ${kategori}
            </div>

            <div class="memory-colon">
                :
            </div>

            <div class="memory-words">
                ${grouped[kategori].join(", ")}
            </div>

        </div>
        `;

    }

    document.getElementById("memory-list").innerHTML = html;

});


/* ===============================
    TIMER 2 MENIT
================================ */

let time = 120;

if (window.TestTimerAlert) {
    window.TestTimerAlert.reset('iq-memory');
}

let timer = setInterval(function(){

    let m = Math.floor(time / 60);
    let s = time % 60;

    document.getElementById("timer").innerText =
        m + ":" + (s < 10 ? "0" : "") + s;

    if (window.TestTimerAlert) {
        window.TestTimerAlert.warn({
            key: 'iq-memory',
            remaining: time,
            threshold: 30,
            title: 'Waktu Menghafal Hampir Habis',
            message: 'Sisa waktu hafalan tinggal 30 detik. Segera lanjut ke soal berikutnya.',
            type: 'info'
        });
    }

    time--;

    if(time < 0){

        clearInterval(timer);

        document.getElementById("memory-list").innerHTML =
        "<h3 style='text-align:center;color:#0f1e3c'>Waktu hafalan selesai</h3>";

        setTimeout(function(){

            window.location = "tes.php?section=9";

        },2000);

    }

},1000);

</script>


</body>
</html>