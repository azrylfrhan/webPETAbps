<?php
require_once 'backend/auth_check.php'; 
require_once 'backend/config.php';
require_once 'backend/biodata_check.php';

$nama = $_SESSION['nama'] ?? 'User';
$nip = $_SESSION['nip'] ?? '-';
$satuan_kerja = $_SESSION['satuan_kerja'] ?? 'BPS Sulawesi Utara'; 

if (($_SESSION['role'] ?? 'peserta') !== 'admin') {
    redirectJikaBiodataBelumLengkap($conn, $nip, 'biodata.php');
}

// Cek apakah Tes 1 (IQ) sudah selesai
$cek_iq = $conn->prepare("SELECT status FROM iq_test_sessions WHERE nip = ? ORDER BY id DESC LIMIT 1");
$cek_iq->bind_param("s", $nip);
$cek_iq->execute();
$iq_session = $cek_iq->get_result()->fetch_assoc();
$tes1_selesai = $iq_session && $iq_session['status'] === 'finished';

$cek_bagian1 = mysqli_query($conn, "SELECT id FROM hasil_msdt WHERE nip = '$nip' AND Ds IS NOT NULL");
$sudah_bagian1 = mysqli_num_rows($cek_bagian1) > 0;

$cek_bagian2 = mysqli_query($conn, "SELECT id FROM hasil_papi WHERE nip = '$nip'");
$sudah_bagian2 = mysqli_num_rows($cek_bagian2) > 0;

$url_tes2 = "tes-kepribadian.php";
$label_tes2 = "Mulai Tes 2 →";
$status_kelas2 = "btn-purple";

if (!$tes1_selesai) {
    $url_tes2 = "#";
    $label_tes2 = "🔒 Selesaikan Tes 1 Dulu";
    $status_kelas2 = "btn-disabled";
} elseif ($sudah_bagian1 && !$sudah_bagian2) {
    $url_tes2 = "tes-kepribadian2.php";
    $label_tes2 = "Lanjut ke Bagian 2 →";
} elseif ($sudah_bagian1 && $sudah_bagian2) {
    $url_tes2 = "#";
    $label_tes2 = "✓ Tes Selesai";
    $status_kelas2 = "btn-disabled";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | PETA</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-disabled {
            background-color: #ccc !important;
            cursor: not-allowed;
            pointer-events: none;
        }
        .badge-info {
            font-size: 0.8em;
            background: #eee;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <img src="images/logobps.png" alt="Logo BPS">
        <span>PETA — Pemetaan Potensi Pegawai</span>
    </div>
    <div class="user-info">
        <span>Halo, <strong><?= htmlspecialchars($nama); ?></strong></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<main class="container">

    <?php if (isset($_GET['iq']) && $_GET['iq'] == 'sudah_selesai'): ?>
        <div class="alert-success">
            ✓ Tes 1 sudah pernah Anda kerjakan sebelumnya.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'tes1_belum_selesai'): ?>
        <div class="alert-success" style="background:#fff8e1; color:#9a6700; border-left:4px solid #f59e0b;">
            ⚠ Tes 2 masih terkunci. Silakan selesaikan Tes 1 terlebih dahulu.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'tes_selesai'): ?>
        <div class="alert-success">
            ✓ <strong>Terima Kasih!</strong> Seluruh rangkaian tes Anda telah berhasil disimpan.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['biodata']) && $_GET['biodata'] == 'ok'): ?>
        <div class="alert-success">
            ✓ Biodata berhasil disimpan. Anda sekarang bisa mulai mengerjakan tes.
        </div>
    <?php endif; ?>

    <section class="welcome-banner">
        <h1>Selamat Datang di Portal PETA</h1>
        <p>Silakan pilih jenis tes yang ingin Anda ikuti</p>
    </section>

    <section class="profile-box">
        <div class="profile-item"><strong><?= htmlspecialchars($nama); ?></strong></div>
        <div class="profile-item"><span>NIP: <?= htmlspecialchars($nip); ?></span></div>
        <div class="profile-item"><span>Unit Kerja: <?= htmlspecialchars($satuan_kerja); ?></span></div>
    </section>

    <section class="test-selection">
        <div class="test-grid">

            <!-- TES 1 -->
            <div class="test-card card-blue">
                <div class="card-header">
                    <div class="icon-circle blue">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="9" y1="13" x2="15" y2="13"/>
                            <line x1="9" y1="17" x2="12" y2="17"/>
                        </svg>
                    </div>
                    <h3>Tes 1</h3>
                </div>
                <div class="card-info">
                    <div class="info-row">
                        <span>Status</span>
                        <strong><?= $tes1_selesai ? 'Selesai' : 'Tersedia' ?></strong>
                    </div>
                </div>
                <button class="btn-test <?= $tes1_selesai ? 'btn-disabled' : 'btn-blue' ?>"
                    onclick="<?= $tes1_selesai ? '' : "window.location.href='tes_proses/tes_iq/tes-iq.php'" ?>">
                    <?= $tes1_selesai ? '✓ Tes Selesai' : 'Mulai Tes 1 →' ?>
                </button>
            </div>

            <!-- TES 2 -->
            <div class="test-card card-purple">
                <div class="card-header">
                    <div class="icon-circle purple">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                            <polyline points="2 17 12 22 22 17"/>
                            <polyline points="2 12 12 17 22 12"/>
                        </svg>
                    </div>
                    <h3>Tes 2</h3>
                </div>
                <div class="card-info">
                    <div class="info-row">
                        <span>Status</span>
                        <strong>
                            <?php
                                if (!$tes1_selesai) echo "Terkunci";
                                elseif (!$sudah_bagian1) echo "Belum Mulai";
                                elseif (!$sudah_bagian2) echo "Bagian 1 Selesai";
                                else echo "Selesai";
                            ?>
                        </strong>
                    </div>
                </div>
                <button class="btn-test <?= $status_kelas2 ?>" onclick="window.location.href='<?= $url_tes2 ?>'">
                    <?= $label_tes2 ?>
                </button>
            </div>

        </div>
    </section>

    <section class="disclaimer">
        🔒 Hasil tes bersifat rahasia dan digunakan untuk keperluan internal BPS
    </section>

</main>

</body>
</html>