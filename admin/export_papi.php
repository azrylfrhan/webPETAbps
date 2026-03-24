<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

$query = "SELECT u.nama, u.nip, u.jabatan, u.satuan_kerja, h.*
          FROM hasil_papi h
          JOIN users u ON h.nip = u.nip
          WHERE u.role = 'peserta'
          ORDER BY u.nama ASC";
$result = mysqli_query($conn, $query);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

$roles = ['G','L','I','T','V','S','R','D','C','E'];
$needs = ['N','A','P','X','B','O','Z','K','F','W'];

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Tes-2_Bag-2.xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:11px;">

    <tr>
        <td colspan="26" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL TES 2 BAG. 2
        </td>
    </tr>
    <tr>
        <td colspan="26" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
            Tanggal Export: <?= date('d F Y') ?> &nbsp;|&nbsp; Total Peserta: <?= count($rows) ?>
        </td>
    </tr>

    <tr style="text-align:center; font-weight:bold; font-size:11px;">
        <td style="background-color:#0F1E3C; color:#fff; width:35px;">No</td>
        <td style="background-color:#0F1E3C; color:#fff; width:130px;">NIP</td>
        <td style="background-color:#0F1E3C; color:#fff; width:180px;">Nama Pegawai</td>
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

    <tr><td colspan="26" style="border:none; padding:4px;"></td></tr>
    <tr>
        <td colspan="26" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Roles:</b> G=Hard Intense Worked &nbsp;|&nbsp; L=Leadership Role &nbsp;|&nbsp; I=Ease in Decision Making &nbsp;|&nbsp; T=Theoretical Type &nbsp;|&nbsp; V=Vigorous Type &nbsp;|&nbsp; S=Social Adability &nbsp;|&nbsp; R=Self-Conditioning &nbsp;|&nbsp; D=Interest in Details &nbsp;|&nbsp; C=Organized Type &nbsp;|&nbsp; E=Emotional Restraint
        </td>
    </tr>
    <tr>
        <td colspan="26" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Needs:</b> N=Finish a Task &nbsp;|&nbsp; A=Achieve &nbsp;|&nbsp; P=Control Others &nbsp;|&nbsp; X=Be Noticed &nbsp;|&nbsp; B=Belong to Groups &nbsp;|&nbsp; O=Closeness &nbsp;|&nbsp; Z=Change &nbsp;|&nbsp; K=Forceful &nbsp;|&nbsp; F=Support Authority &nbsp;|&nbsp; W=Rules &amp; Supervision
        </td>
    </tr>
</table>