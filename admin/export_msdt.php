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
                 u.jabatan, u.satuan_kerja,
                 h.to_score, h.ro_score, h.e_score, h.o_score,
                 h.Ds, h.Mi, h.Au, h.Co, h.Bu, h.Dv, h.Ba, h.E_dim,
                 h.dominant_model, h.tanggal_tes
          FROM hasil_msdt h
          JOIN users u ON h.nip = u.nip
          " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
          WHERE u.role = 'peserta' $where_date
          ORDER BY u.nama ASC";
$result = mysqli_query($conn, $query);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

// Check if using unified schema
$has_unified = false;
$cek_unified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($cek_unified && mysqli_num_rows($cek_unified) > 0) {
    $cek_results = mysqli_query($conn, "SHOW TABLES LIKE 'msdt_attempt_results'");
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
               0 AS to_score, 0 AS ro_score, 0 AS e_score, 0 AS o_score,
               COALESCE(mar.Ds, 0) AS Ds,
               COALESCE(mar.Mi, 0) AS Mi,
               COALESCE(mar.Au, 0) AS Au,
               COALESCE(mar.Co, 0) AS Co,
               COALESCE(mar.Bu, 0) AS Bu,
               COALESCE(mar.Dv, 0) AS Dv,
               COALESCE(mar.Ba, 0) AS Ba,
               COALESCE(mar.Ba, 0) AS E_dim,
               '-' AS dominant_model,
               ta.tanggal_mulai AS tanggal_tes
        FROM test_attempts ta
        JOIN users u ON u.nip = ta.nip
        LEFT JOIN msdt_attempt_results mar ON mar.attempt_id = ta.id
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta' AND ta.test_type = 'msdt' AND ta.status = 'finished'
        " . (!empty($where_date) ? str_replace('h.tanggal_tes', 'ta.tanggal_mulai', $where_date) : "") . "
        ORDER BY u.nama ASC
    ";
    $query_unified = "
        SELECT u.nip, u.nama,
               " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
               u.jabatan, u.satuan_kerja,
               COALESCE(mar.TO_score, 0) AS to_score,
               COALESCE(mar.RO_score, 0) AS ro_score,
               COALESCE(mar.E_score, 0) AS e_score,
               COALESCE(mar.O_score, 0) AS o_score,
               COALESCE(mar.Ds, 0) AS Ds,
               COALESCE(mar.Mi, 0) AS Mi,
               COALESCE(mar.Au, 0) AS Au,
               COALESCE(mar.Co, 0) AS Co,
               COALESCE(mar.Bu, 0) AS Bu,
               COALESCE(mar.Dv, 0) AS Dv,
               COALESCE(mar.Ba, 0) AS Ba,
               COALESCE(mar.E_dim, 0) AS E_dim,
               COALESCE(mar.dominant_model, '-') AS dominant_model,
               ta.tanggal_mulai AS tanggal_tes
        FROM test_attempts ta
        JOIN users u ON u.nip = ta.nip
        LEFT JOIN msdt_attempt_results mar ON mar.attempt_id = ta.id
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta' AND ta.test_type = 'msdt' AND ta.status = 'finished'
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
                     u.jabatan, u.satuan_kerja,
                     h.to_score, h.ro_score, h.e_score, h.o_score,
                     h.Ds, h.Mi, h.Au, h.Co, h.Bu, h.Dv, h.Ba, h.E_dim,
                     h.dominant_model, h.tanggal_tes
              FROM hasil_msdt h
              JOIN users u ON h.nip = u.nip
              " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
              WHERE u.role = 'peserta' $where_date
              ORDER BY u.nama ASC";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
}

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Tes-2_Bag-1.xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:11px;">

    <tr>
        <td colspan="21" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL TES 2 BAG. 1
        </td>
    </tr>
    <tr>
        <td colspan="21" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
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
        <td style="background-color:#1E40AF; color:#fff; width:40px;">TO</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">RO</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">E</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">O</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Ds</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Mi</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Au</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Co</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Bu</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Dv</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">Ba</td>
        <td style="background-color:#4C1D95; color:#fff; width:40px;">E</td>
        <td style="background-color:#0F1E3C; color:#fff; width:100px;">Model Dominan</td>
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
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)$r['to_score'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)$r['ro_score'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)$r['e_score'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)$r['o_score'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Ds'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Mi'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Au'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Co'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Bu'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Dv'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['Ba'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)$r['E_dim'] ?></td>
        <td style="text-align:center; font-weight:bold; color:#0F1E3C;"><?= htmlspecialchars($r['dominant_model'] ?? '-') ?></td>
        <td style="text-align:center;"><?= $r['tanggal_tes'] ? date('d/m/Y', strtotime($r['tanggal_tes'])) : '-' ?></td>
    </tr>
    <?php endforeach; ?>

    <tr><td colspan="21" style="border:none; padding:4px;"></td></tr>
    <tr>
        <td colspan="21" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Dimensi Utama:</b> TO=Task Orientation &nbsp;|&nbsp; RO=Relationship Orientation &nbsp;|&nbsp; E=Extroversion &nbsp;|&nbsp; O=Openness
        </td>
    </tr>
    <tr>
        <td colspan="21" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Sub Dimensi:</b> Ds=Directing &nbsp;|&nbsp; Mi=Motivating &nbsp;|&nbsp; Au=Autonomy &nbsp;|&nbsp; Co=Coaching &nbsp;|&nbsp; Bu=Bureaucratic &nbsp;|&nbsp; Dv=Developing &nbsp;|&nbsp; Ba=Balancing &nbsp;|&nbsp; E=Empowering
        </td>
    </tr>
</table>