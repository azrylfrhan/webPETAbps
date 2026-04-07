<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Get date filter parameters
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$where_date = '';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $where_date = "AND DATE(h.tanggal_tes) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
}

$has_biodata = false;
$cek_biodata = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
if ($cek_biodata && mysqli_num_rows($cek_biodata) > 0) {
    $has_biodata = true;
}

$query = "SELECT u.nama, u.nip,
                 " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
                 u.jabatan, u.satuan_kerja, h.*
          FROM hasil_papi h
          JOIN users u ON h.nip = u.nip
          " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
          WHERE u.role = 'peserta' $where_date
          ORDER BY u.nama ASC";
$result = mysqli_query($conn, $query);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

$roles = ['G','L','I','T','V','S','R','D','C','E'];
$needs = ['N','A','P','X','B','O','Z','K','F','W'];

// Check if using unified schema
$has_unified = false;
$cek_unified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($cek_unified && mysqli_num_rows($cek_unified) > 0) {
    $cek_results = mysqli_query($conn, "SHOW TABLES LIKE 'papi_attempt_results'");
    if ($cek_results && mysqli_num_rows($cek_results) > 0) {
        $has_unified = true;
    }
}

if ($has_unified) {
    // Use unified schema
    $query_unified = "
        SELECT u.nip, u.nama,
               " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
               u.jabatan, u.satuan_kerja,
               ta.nip AS nip_verify,
               COALESCE(par.G, 0) AS G,
               COALESCE(par.L, 0) AS L,
               COALESCE(par.I, 0) AS I,
               COALESCE(par.T, 0) AS T,
               COALESCE(par.V, 0) AS V,
               COALESCE(par.S, 0) AS S,
               COALESCE(par.R, 0) AS R,
               COALESCE(par.D, 0) AS D,
               COALESCE(par.C, 0) AS C,
               COALESCE(par.E, 0) AS E,
               COALESCE(par.N, 0) AS N,
               COALESCE(par.A, 0) AS A,
               COALESCE(par.P, 0) AS P,
               COALESCE(par.X, 0) AS X,
               COALESCE(par.B, 0) AS B,
               COALESCE(par.O, 0) AS O,
               COALESCE(par.K, 0) AS K,
               COALESCE(par.F, 0) AS F,
               COALESCE(par.W, 0) AS W,
               COALESCE(par.Z, 0) AS Z,
               ta.tanggal_mulai AS tanggal_tes
        FROM test_attempts ta
        JOIN users u ON u.nip = ta.nip
        LEFT JOIN papi_attempt_results par ON par.attempt_id = ta.id
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta' AND ta.test_type = 'papi' AND ta.status = 'finished'
        " . (!empty($where_date) ? str_replace('h.tanggal_tes', 'ta.tanggal_mulai', $where_date) : "") . "
        ORDER BY u.nama ASC
    ";
    $query_unified = "
        SELECT u.nip, u.nama,
               " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
               u.jabatan, u.satuan_kerja,
               COALESCE(par.G, 0) AS G,
               COALESCE(par.L, 0) AS L,
               COALESCE(par.I, 0) AS I,
               COALESCE(par.T, 0) AS T,
               COALESCE(par.V, 0) AS V,
               COALESCE(par.S, 0) AS S,
               COALESCE(par.R, 0) AS R,
               COALESCE(par.D, 0) AS D,
               COALESCE(par.C, 0) AS C,
               COALESCE(par.E, 0) AS E,
               COALESCE(par.N, 0) AS N,
               COALESCE(par.A, 0) AS A,
               COALESCE(par.P, 0) AS P,
               COALESCE(par.X, 0) AS X,
               COALESCE(par.B, 0) AS B,
               COALESCE(par.O, 0) AS O,
               COALESCE(par.K, 0) AS K,
               COALESCE(par.F, 0) AS F,
               COALESCE(par.W, 0) AS W,
               COALESCE(par.Z, 0) AS Z,
               ta.tanggal_mulai AS tanggal_tes
        FROM test_attempts ta
        JOIN users u ON u.nip = ta.nip
        LEFT JOIN papi_attempt_results par ON par.attempt_id = ta.id
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta' AND ta.test_type = 'papi' AND ta.status = 'finished'
        " . (!empty($where_date) ? str_replace('h.tanggal_tes', 'ta.tanggal_mulai', $where_date) : "") . "
        ORDER BY u.nama ASC
    ";
    $result = mysqli_query($conn, $query_unified);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
} else {
    // Fall back to legacy
    $query = "SELECT u.nama, u.nip,
                     " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
                     u.jabatan, u.satuan_kerja, h.*
              FROM hasil_papi h
              JOIN users u ON h.nip = u.nip
              " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
              WHERE u.role = 'peserta' $where_date
              ORDER BY u.nama ASC";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
}

$roles = ['G','L','I','T','V','S','R','D','C','E'];
$needs = ['N','A','P','X','B','O','Z','K','F','W'];

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Tes-2_Bag-2.xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:11px;">

    <tr>
        <td colspan="27" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL TES 2 BAG. 2
        </td>
    </tr>
    <tr>
        <td colspan="27" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
            Tanggal Export: <?= date('d F Y') ?> &nbsp;|&nbsp; Total Peserta: <?= count($rows) ?>
        </td>
    </tr>

    <tr style="text-align:center; font-weight:bold; font-size:11px;">
        <td style="background-color:#0F1E3C; color:#fff; width:35px;">No</td>
        <td style="background-color:#0F1E3C; color:#fff; width:130px;">NIP</td>
        <td style="background-color:#0F1E3C; color:#fff; width:180px;">Nama Pegawai</td>
        <td style="background-color:#0F1E3C; color:#fff; width:60px;">Usia</td>
        <td style="background-color:#0F1E3C; color:#fff; width:150px;">Jabatan</td>
        <td style="background-color:#0F1E3C; color:#fff; width:130px;">Satuan Kerja</td>
        <?php foreach ($roles as $k): ?>
        <td style="background-color:#1E40AF; color:#fff; width:40px;"><?= $k ?></td>
        <?php endforeach; ?>
        <?php foreach ($needs as $k): ?>
        <td style="background-color:#4C1D95; color:#fff; width:40px;"><?= $k ?></td>
        <?php endforeach; ?>
        <td style="background-color:#0F1E3C; color:#fff; width:90px;">Tanggal Tes</td>
    </tr>

    <?php foreach ($rows as $i => $r):
        $bg = ($i % 2 === 0) ? '#FFFFFF' : '#F8FAFC';
    ?>
    <tr style="background-color:<?= $bg ?>; font-size:11px;">
        <td style="text-align:center;"><?= $i+1 ?></td>
        <td style="text-align:center; font-family:monospace;">'<?= htmlspecialchars($r['nip']) ?></td>
        <td><?= htmlspecialchars($r['nama']) ?></td>
        <td style="text-align:center;"><?= isset($r['usia']) && $r['usia'] !== null ? (int)$r['usia'] : '-' ?></td>
        <td><?= htmlspecialchars($r['jabatan'] ?? '-') ?></td>
        <td><?= htmlspecialchars($r['satuan_kerja'] ?? '-') ?></td>
        <?php foreach ($roles as $k): ?>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($r[$k] ?? 0) ?></td>
        <?php endforeach; ?>
        <?php foreach ($needs as $k): ?>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)($r[$k] ?? 0) ?></td>
        <?php endforeach; ?>
        <td style="text-align:center;"><?= !empty($r['tanggal_tes']) ? date('d/m/Y', strtotime($r['tanggal_tes'])) : '-' ?></td>
    </tr>
    <?php endforeach; ?>

    <tr><td colspan="27" style="border:none; padding:4px;"></td></tr>
    <tr>
        <td colspan="27" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Roles:</b> G=Hard Intense Worked &nbsp;|&nbsp; L=Leadership Role &nbsp;|&nbsp; I=Ease in Decision Making &nbsp;|&nbsp; T=Theoretical Type &nbsp;|&nbsp; V=Vigorous Type &nbsp;|&nbsp; S=Social Adability &nbsp;|&nbsp; R=Self-Conditioning &nbsp;|&nbsp; D=Interest in Details &nbsp;|&nbsp; C=Organized Type &nbsp;|&nbsp; E=Emotional Restraint
        </td>
    </tr>
    <tr>
        <td colspan="27" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Needs:</b> N=Finish a Task &nbsp;|&nbsp; A=Achieve &nbsp;|&nbsp; P=Control Others &nbsp;|&nbsp; X=Be Noticed &nbsp;|&nbsp; B=Belong to Groups &nbsp;|&nbsp; O=Closeness &nbsp;|&nbsp; Z=Change &nbsp;|&nbsp; K=Forceful &nbsp;|&nbsp; F=Support Authority &nbsp;|&nbsp; W=Rules &amp; Supervision
        </td>
    </tr>
</table>