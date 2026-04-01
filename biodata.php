<?php
require_once 'backend/auth_check.php';
require_once 'backend/config.php';
require_once 'backend/biodata_check.php';

$role = $_SESSION['role'] ?? 'peserta';
if ($role === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$nip = $_SESSION['nip'] ?? '';
$nama = $_SESSION['nama'] ?? 'Peserta';

$setupMissing = isset($_GET['setup']) && $_GET['setup'] === 'missing_table';
$biodataAda = false;
$biodata = [
    'tempat_lahir' => '',
    'tanggal_lahir' => '',
    'email' => ''
];

if (biodataTableExists($conn) && !empty($nip)) {
    $stmt = $conn->prepare("SELECT tempat_lahir, tanggal_lahir, email FROM biodata_peserta WHERE nip = ? LIMIT 1");
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!empty($found)) {
        $biodataAda = true;
        $biodata = $found;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Biodata | PETA</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .biodata-note {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
            line-height: 1.5;
        }
        .danger-msg {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 0.86em;
            text-align: center;
        }
        .success-msg {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 0.86em;
            text-align: center;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <img src="images/logobps.png" alt="Logo BPS">
        <span>PETA - Pemetaan Potensi Pegawai</span>
    </div>
    <div class="user-info">
        <span>Halo, <strong><?= htmlspecialchars($nama) ?></strong></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<div class="auth-container" style="max-width: 520px; margin-top: 45px;">
    <div class="auth-card">
        <h2>Lengkapi Biodata</h2>
        <p style="text-align:center; color:#64748b; margin-bottom: 18px; font-size: 14px;">
            Isi biodata satu kali, lalu isi alasan mengikuti tes setiap kali akan mulai tes.
        </p>

        <?php if ($biodataAda): ?>
            <div class="success-msg">
                Biodata sudah tersimpan. Anda hanya perlu mengisi alasan mengikuti tes untuk melanjutkan.
            </div>
        <?php endif; ?>

        <?php if ($setupMissing): ?>
            <div class="danger-msg">
                Tabel biodata belum tersedia. Silakan jalankan SQL di file users/biodata_peserta.sql terlebih dahulu.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'saved'): ?>
            <div class="success-msg">Biodata berhasil disimpan. Silakan lanjut ke dashboard tes.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="danger-msg">
                <?php
                    if ($_GET['error'] === 'empty') {
                        echo 'Semua field wajib diisi.';
                    } elseif ($_GET['error'] === 'invalid_email') {
                        echo 'Format email tidak valid.';
                    } elseif ($_GET['error'] === 'invalid_date') {
                        echo 'Tanggal lahir tidak valid.';
                    } elseif ($_GET['error'] === 'empty_reason') {
                        echo 'Alasan mengikuti tes wajib diisi.';
                    } else {
                        echo 'Gagal menyimpan biodata. Silakan coba lagi.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="backend/simpan_biodata.php" method="POST">
            <div class="auth-group">
                <label>NIP</label>
                <input type="text" value="<?= htmlspecialchars($nip) ?>" readonly>
            </div>

            <div class="auth-group">
                <label>Tempat Lahir</label>
                <input
                    type="text"
                    name="tempat_lahir"
                    maxlength="100"
                    placeholder="Contoh: Manado"
                    value="<?= htmlspecialchars($biodata['tempat_lahir'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                >
            </div>

            <div class="auth-group">
                <label>Tanggal Lahir</label>
                <input
                    type="date"
                    name="tanggal_lahir"
                    max="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars($biodata['tanggal_lahir'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                >
                <p class="biodata-note">Usia dihitung otomatis dari tanggal lahir dan tanggal hari ini.</p>
            </div>

            <div class="auth-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    maxlength="120"
                    placeholder="nama@domain.com"
                    value="<?= htmlspecialchars($biodata['email'] ?? '') ?>"
                    <?= $biodataAda ? 'readonly' : 'required' ?>
                >
            </div>

            <div class="auth-group">
                <label>Alasan Mengikuti Tes</label>
                <textarea
                    name="alasan_tes"
                    rows="4"
                    maxlength="1000"
                    placeholder="Tuliskan alasan Anda mengikuti tes hari ini"
                    required
                ></textarea>
            </div>

            <div class="action" style="margin-top: 10px;">
                <button type="submit" class="btn-primary"><?= $biodataAda ? 'Simpan Alasan & Lanjut Tes' : 'Simpan Biodata & Lanjut Tes' ?></button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
