<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Get date filter parameters
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

// bangun kondisi WHERE berdasarkan filter
$where_iq = '';
$where_msdt = '';
$where_papi = '';

if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $where_iq = "AND DATE(h3.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
    $where_msdt = "AND DATE(h1.tanggal_tes) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
    $where_papi = "AND DATE(h2.tanggal_tes) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
}

// query gabungan data 3 tes
$query = "
SELECT 
    u.nip, 
    u.nama, 
    u.jabatan,
    u.satuan_kerja,
    TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE()) AS usia,
    
    h3.skor AS iq_total_score,
    h3.tanggal AS iq_tanggal,
    
    h1.to_score, h1.ro_score, h1.e_score, h1.o_score,
    h1.Ds, h1.Mi, h1.Au, h1.Co, h1.Bu, h1.Dv, h1.Ba, h1.E_dim,
    h1.dominant_model, 
    h1.tanggal_tes AS msdt_tanggal,
    
    h2.G, h2.L, h2.I, h2.T, h2.V, h2.S, h2.R, h2.D, h2.C, h2.E,
    h2.N, h2.A, h2.P, h2.X, h2.B, h2.O, h2.K, h2.F, h2.W, h2.Z,
    h2.tanggal_tes AS papi_tanggal
FROM users u
LEFT JOIN biodata_peserta b ON b.nip = u.nip
LEFT JOIN iq_results h3 ON u.nip = h3.user_id $where_iq
LEFT JOIN hasil_msdt h1 ON u.nip = h1.nip $where_msdt
LEFT JOIN hasil_papi h2 ON u.nip = h2.nip $where_papi
WHERE u.role = 'peserta' 
  AND (h3.user_id IS NOT NULL OR h1.nip IS NOT NULL OR h2.nip IS NOT NULL)
ORDER BY u.nama ASC
";

$result = mysqli_query($conn, $query);
$rows = [];
while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;

// header export excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Kombinasi_" . date('Ymd') . ".xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:10px;">

    <tr>
        <td colspan="40" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL KOMBINASI (TES 1 + TES 2 BAGIAN 1 + TES 2 BAGIAN 2)
        </td>
    </tr>
    <tr>
        <td colspan="40" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
            Tanggal Export: <?= date('d F Y') ?> &nbsp;|&nbsp; Total Peserta: <?= count($rows) ?> 
            <?php if (!empty($tgl_mulai) && !empty($tgl_akhir)): ?>
            &nbsp;|&nbsp; Filter: Dari <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir)) ?>
            <?php endif; ?>
        </td>
    </tr>

    <tr style="text-align:center; font-weight:bold; font-size:10px;">
        <td style="background-color:#0F1E3C; color:#fff; width:30px;">No</td>
        <td style="background-color:#0F1E3C; color:#fff; width:100px;">NIP</td>
        <td style="background-color:#0F1E3C; color:#fff; width:140px;">Nama</td>
        <td style="background-color:#0F1E3C; color:#fff; width:60px;">Usia</td>
        <td style="background-color:#0F1E3C; color:#fff; width:120px;">Jabatan</td>
        <td style="background-color:#0F1E3C; color:#fff; width:120px;">Satuan Kerja</td>
        
        <!-- TES 1 IQ -->
        <td style="background-color:#FBBF24; color:#000; width:80px;">Skor Tes 1</td>
        <td style="background-color:#FBBF24; color:#000; width:100px;">Tgl Tes 1</td>
        
        <!-- TES 2 BAG 1 (MSDT) -->
        <td style="background-color:#10B981; color:#fff; width:60px;">TO</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">RO</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">E</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">O</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Ds</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Mi</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Au</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Co</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Bu</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Dv</td>
        <td style="background-color:#10B981; color:#fff; width:60px;">Ba</td>
        <td style="background-color:#10B981; color:#fff; width:80px;">E_dim</td>
        <td style="background-color:#10B981; color:#fff; width:100px;">Dominant Model</td>
        <td style="background-color:#10B981; color:#fff; width:100px;">Tgl Tes 2 Bag 1</td>
        
        <!-- TES 2 BAG 2 (PAPI) -->
        <td style="background-color:#06B6D4; color:#000; width:50px;">G</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">L</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">I</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">T</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">V</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">S</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">R</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">D</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">C</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">E</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">N</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">A</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">P</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">X</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">B</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">O</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">K</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">F</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">W</td>
        <td style="background-color:#06B6D4; color:#000; width:50px;">Z</td>
        <td style="background-color:#06B6D4; color:#000; width:100px;">Tgl Tes 2 Bag 2</td>
    </tr>

    <?php 
    $no = 1;
    foreach($rows as $r): 
    ?>
    <tr style="font-size:9px;">
        <td style="text-align:center; border-right:1px solid #ddd;"><?= $no++ ?></td>
        <td style="border-right:1px solid #ddd;"><?= $r['nip'] ?></td>
        <td style="border-right:1px solid #ddd;"><?= htmlspecialchars($r['nama']) ?></td>
        <td style="text-align:center; border-right:1px solid #ddd;"><?= $r['usia'] ?? '-' ?></td>
        <td style="border-right:1px solid #ddd;"><?= htmlspecialchars($r['jabatan']) ?></td>
        <td style="border-right:1px solid #ddd;"><?= htmlspecialchars($r['satuan_kerja']) ?></td>
        
        <!-- TES 1 -->
        <td style="text-align:center; background-color:#FEF3C7; border-right:1px solid #ddd;"><?= $r['iq_total_score'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#FEF3C7; border-right:1px solid #ddd;"><?= $r['iq_tanggal'] ? date('d/m/Y', strtotime($r['iq_tanggal'])) : '-' ?></td>
        
        <!-- TES 2 BAG 1 -->
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['to_score'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['ro_score'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['e_score'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['o_score'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Ds'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Mi'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Au'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Co'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Bu'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Dv'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['Ba'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['E_dim'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5;"><?= $r['dominant_model'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#D1FAE5; border-right:1px solid #ddd;"><?= $r['msdt_tanggal'] ? date('d/m/Y', strtotime($r['msdt_tanggal'])) : '-' ?></td>
        
        <!-- TES 2 BAG 2 -->
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['G'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['L'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['I'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['T'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['V'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['S'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['R'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['D'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['C'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['E'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['N'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['A'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['P'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['X'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['B'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['O'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['K'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['F'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['W'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['Z'] ?? '-' ?></td>
        <td style="text-align:center; background-color:#CFFAFE;"><?= $r['papi_tanggal'] ? date('d/m/Y', strtotime($r['papi_tanggal'])) : '-' ?></td>
    </tr>
    <?php endforeach; ?>

</table>
