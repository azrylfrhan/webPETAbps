<?php
require_once '../backend/config.php';
require_once '../backend/auth_check.php';

$nip  = $_SESSION['nip'];
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<title>Tes IQ Bagian 9 - Hafalan</title>

<style>

/* ===============================
   PAGE STYLE
================================ */

body{
    font-family: Arial, Helvetica, sans-serif;
    background:#f4f6f9;
    margin:0;
}

.container{

    max-width:900px;
    margin:60px auto;

    background:white;
    padding:40px;

    border-radius:10px;

}

/* ===============================
   HEADER
================================ */

.header{

    text-align:center;
    margin-bottom:30px;

}

.header h2{

    margin-bottom:10px;

}

/* ===============================
   TIMER
================================ */

.timer{

    font-size:22px;
    font-weight:bold;

    color:#c0392b;

    text-align:center;
    margin-bottom:30px;

}

/* ===============================
   MEMORY LIST
================================ */

.memory-row{

    display:flex;
    font-size:20px;

    margin-bottom:12px;

}

.memory-category{

    width:160px;
    font-weight:bold;

}

.memory-colon{

    width:20px;

}

.memory-words{

    flex:1;

}

</style>

</head>

<body>


<div class="container">

<div class="header">

<h2>Hafalkan Kata Berikut</h2>

<p>Anda memiliki waktu <strong>3 menit</strong> untuk menghafal daftar kata ini.</p>

</div>


<div class="timer">

Waktu Menghafal: <span id="timer">03:00</span>

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
   TIMER 3 MENIT
================================ */

let time = 180;

let timer = setInterval(function(){

    let m = Math.floor(time / 60);
    let s = time % 60;

    document.getElementById("timer").innerText =
        m + ":" + (s < 10 ? "0" : "") + s;

    time--;

    if(time < 0){

        clearInterval(timer);

        document.getElementById("memory-list").innerHTML =
        "<h3 style='text-align:center'>Waktu Hafalan Selesai</h3>";

        setTimeout(function(){

            window.location = "tes.php?section=9";

        },2000);

    }

},1000);

</script>


</body>
</html>