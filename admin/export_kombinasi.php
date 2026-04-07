<?php
require_once '../backend/config.php';
include '../backend/auth_check.php';

// Get date filter parameters
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

// Sanitasi filter tanggal
$tgl_mulai_safe = '';
$tgl_akhir_safe = '';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $tgl_mulai_safe = mysqli_real_escape_string($conn, $tgl_mulai);
    $tgl_akhir_safe = mysqli_real_escape_string($conn, $tgl_akhir);
}

$has_biodata = false;
$cek_biodata = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
if ($cek_biodata && mysqli_num_rows($cek_biodata) > 0) {
    $has_biodata = true;
}

$has_unified = false;
$cek_attempts = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
$cek_iq_res = mysqli_query($conn, "SHOW TABLES LIKE 'iq_attempt_results'");
$cek_msdt_res = mysqli_query($conn, "SHOW TABLES LIKE 'msdt_attempt_results'");
$cek_papi_res = mysqli_query($conn, "SHOW TABLES LIKE 'papi_attempt_results'");
if (
    $cek_attempts && mysqli_num_rows($cek_attempts) > 0 &&
    $cek_iq_res && mysqli_num_rows($cek_iq_res) > 0 &&
    $cek_msdt_res && mysqli_num_rows($cek_msdt_res) > 0 &&
    $cek_papi_res && mysqli_num_rows($cek_papi_res) > 0
) {
    $has_unified = true;
}

$rows = [];

if ($has_unified) {
    $filter_iq_attempt = '';
    $filter_msdt_attempt = '';
    $filter_papi_attempt = '';
    if ($tgl_mulai_safe !== '' && $tgl_akhir_safe !== '') {
        $filter_iq_attempt = " AND DATE(tanggal_mulai) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
        $filter_msdt_attempt = " AND DATE(tanggal_mulai) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
        $filter_papi_attempt = " AND DATE(tanggal_mulai) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
    }

    $query = "
    SELECT
        u.nip,
        u.nama,
        u.jabatan,
        u.satuan_kerja,
        " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,

        iq.tanggal_mulai AS iq_tanggal,
        COALESCE(iq.se, 0) AS iq_se,
        COALESCE(iq.wa, 0) AS iq_wa,
        COALESCE(iq.an, 0) AS iq_an,
        COALESCE(iq.ge, 0) AS iq_ge,
        COALESCE(iq.ra, 0) AS iq_ra,
        COALESCE(iq.zr, 0) AS iq_zr,
        COALESCE(iq.fa, 0) AS iq_fa,
        COALESCE(iq.wu, 0) AS iq_wu,
        COALESCE(iq.me, 0) AS iq_me,

        msdt.to_score,
        msdt.ro_score,
        msdt.e_score,
        msdt.o_score,
        msdt.Ds,
        msdt.Mi,
        msdt.Au,
        msdt.Co,
        msdt.Bu,
        msdt.Dv,
        msdt.Ba,
        msdt.E_dim,
        msdt.dominant_model,
        msdt.tanggal_mulai AS msdt_tanggal,

        papi.G,
        papi.L,
        papi.I,
        papi.T,
        papi.V,
        papi.S,
        papi.R,
        papi.D,
        papi.C,
        papi.E,
        papi.N,
        papi.A,
        papi.P,
        papi.X,
        papi.B,
        papi.O,
        papi.K,
        papi.F,
        papi.W,
        papi.Z,
        papi.tanggal_mulai AS papi_tanggal
    FROM users u
    " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "

    LEFT JOIN (
        SELECT ta.nip, ta.tanggal_mulai,
               COALESCE(NULLIF(iar.se, 0), NULLIF(calc.SE, 0), usercalc.SE, 0) AS se,
               COALESCE(NULLIF(iar.wa, 0), NULLIF(calc.WA, 0), usercalc.WA, 0) AS wa,
               COALESCE(NULLIF(iar.an, 0), NULLIF(calc.AN, 0), usercalc.AN, 0) AS an,
               COALESCE(NULLIF(iar.ge, 0), NULLIF(calc.GE, 0), usercalc.GE, 0) AS ge,
               COALESCE(NULLIF(iar.ra, 0), NULLIF(calc.RA, 0), usercalc.RA, 0) AS ra,
               COALESCE(NULLIF(iar.zr, 0), NULLIF(calc.ZR, 0), usercalc.ZR, 0) AS zr,
               COALESCE(NULLIF(iar.fa, 0), NULLIF(calc.FA, 0), usercalc.FA, 0) AS fa,
               COALESCE(NULLIF(iar.wu, 0), NULLIF(calc.WU, 0), usercalc.WU, 0) AS wu,
               COALESCE(NULLIF(iar.me, 0), NULLIF(calc.ME, 0), usercalc.ME, 0) AS me
        FROM test_attempts ta
        JOIN (
            SELECT
                ta.nip,
                MAX(ta.id) AS picked_id
            FROM test_attempts ta
            WHERE ta.test_type = 'iq' AND ta.status = 'finished' $filter_iq_attempt
            GROUP BY ta.nip
        ) latest ON latest.picked_id = ta.id
        LEFT JOIN iq_attempt_results iar ON iar.attempt_id = ta.id
        LEFT JOIN (
            SELECT
                iaa.attempt_id,
                SUM(CASE WHEN s.urutan = 1 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
                SUM(CASE WHEN s.urutan = 2 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
                SUM(CASE WHEN s.urutan = 3 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
                SUM(CASE WHEN s.urutan = 4 THEN COALESCE((
                    SELECT MAX(fa.nilai)
                    FROM iq_fill_answers fa
                    WHERE fa.question_id = iaa.question_id
                      AND FIND_IN_SET(LOWER(TRIM(iaa.jawaban_user)), REPLACE(LOWER(fa.jawaban), ', ', ',')) > 0
                ), 0) ELSE 0 END) AS GE,
                SUM(CASE WHEN s.urutan = 5 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
                SUM(CASE WHEN s.urutan = 6 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
                SUM(CASE WHEN s.urutan = 7 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
                SUM(CASE WHEN s.urutan = 8 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
                SUM(CASE WHEN s.urutan = 9 AND LOWER(TRIM(iaa.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
            FROM iq_attempt_answers iaa
            JOIN iq_questions q ON q.id = iaa.question_id
            JOIN iq_sections s ON s.id = q.section_id
            GROUP BY iaa.attempt_id
        ) calc ON calc.attempt_id = ta.id
        LEFT JOIN (
            SELECT
                ua.user_nip,
                SUM(CASE WHEN s.urutan = 1 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
                SUM(CASE WHEN s.urutan = 2 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
                SUM(CASE WHEN s.urutan = 3 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
                SUM(CASE WHEN s.urutan = 4 THEN COALESCE((
                    SELECT MAX(fa.nilai)
                    FROM iq_fill_answers fa
                    WHERE fa.question_id = ua.question_id
                      AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)), REPLACE(LOWER(fa.jawaban), ', ', ',')) > 0
                ), 0) ELSE 0 END) AS GE,
                SUM(CASE WHEN s.urutan = 5 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
                SUM(CASE WHEN s.urutan = 6 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
                SUM(CASE WHEN s.urutan = 7 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
                SUM(CASE WHEN s.urutan = 8 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
                SUM(CASE WHEN s.urutan = 9 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
            FROM iq_user_answers ua
            JOIN iq_questions q ON q.id = ua.question_id
            JOIN iq_sections s ON s.id = q.section_id
            GROUP BY ua.user_nip
        ) usercalc ON usercalc.user_nip = ta.nip
    ) iq ON iq.nip = u.nip

    LEFT JOIN (
        SELECT ta.nip, ta.tanggal_mulai,
               mar.TO_score AS to_score,
               mar.RO_score AS ro_score,
               mar.E_score AS e_score,
               mar.O_score AS o_score,
               mar.Ds, mar.Mi, mar.Au, mar.Co, mar.Bu, mar.Dv, mar.Ba, mar.E_dim, mar.dominant_model
        FROM test_attempts ta
        JOIN (
            SELECT
                ta.nip,
                MAX(ta.id) AS picked_id
            FROM test_attempts ta
            WHERE ta.test_type = 'msdt' AND ta.status = 'finished' $filter_msdt_attempt
            GROUP BY ta.nip
        ) latest ON latest.picked_id = ta.id
        LEFT JOIN msdt_attempt_results mar ON mar.attempt_id = ta.id
    ) msdt ON msdt.nip = u.nip

    LEFT JOIN (
        SELECT ta.nip, ta.tanggal_mulai,
               par.G, par.L, par.I, par.T, par.V, par.S, par.R, par.D, par.C, par.E,
               par.N, par.A, par.P, par.X, par.B, par.O, par.K, par.F, par.W, par.Z
        FROM test_attempts ta
        JOIN (
            SELECT
                ta.nip,
                MAX(ta.id) AS picked_id
            FROM test_attempts ta
            WHERE ta.test_type = 'papi' AND ta.status = 'finished' $filter_papi_attempt
            GROUP BY ta.nip
        ) latest ON latest.picked_id = ta.id
        LEFT JOIN papi_attempt_results par ON par.attempt_id = ta.id
    ) papi ON papi.nip = u.nip

    WHERE u.role = 'peserta'
      AND (iq.nip IS NOT NULL OR msdt.nip IS NOT NULL OR papi.nip IS NOT NULL)
    ORDER BY u.nama ASC
    ";
} else {
    $where_iq = '';
    $where_msdt = '';
    $where_papi = '';
    if ($tgl_mulai_safe !== '' && $tgl_akhir_safe !== '') {
        $where_iq = "AND DATE(h3.tanggal) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
        $where_msdt = "AND DATE(h1.tanggal_tes) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
        $where_papi = "AND DATE(h2.tanggal_tes) BETWEEN '$tgl_mulai_safe' AND '$tgl_akhir_safe'";
    }

    $query = "
    SELECT
        u.nip,
        u.nama,
        u.jabatan,
        u.satuan_kerja,
        " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,

        h3.tanggal AS iq_tanggal,
        COALESCE(iqagg.SE, 0) AS iq_se,
        COALESCE(iqagg.WA, 0) AS iq_wa,
        COALESCE(iqagg.AN, 0) AS iq_an,
        COALESCE(iqagg.GE, 0) AS iq_ge,
        COALESCE(iqagg.RA, 0) AS iq_ra,
        COALESCE(iqagg.ZR, 0) AS iq_zr,
        COALESCE(iqagg.FA, 0) AS iq_fa,
        COALESCE(iqagg.WU, 0) AS iq_wu,
        COALESCE(iqagg.ME, 0) AS iq_me,

        h1.to_score,
        h1.ro_score,
        h1.e_score,
        h1.o_score,
        h1.Ds,
        h1.Mi,
        h1.Au,
        h1.Co,
        h1.Bu,
        h1.Dv,
        h1.Ba,
        h1.E_dim,
        h1.dominant_model,
        h1.tanggal_tes AS msdt_tanggal,

        h2.G,
        h2.L,
        h2.I,
        h2.T,
        h2.V,
        h2.S,
        h2.R,
        h2.D,
        h2.C,
        h2.E,
        h2.N,
        h2.A,
        h2.P,
        h2.X,
        h2.B,
        h2.O,
        h2.K,
        h2.F,
        h2.W,
        h2.Z,
        h2.tanggal_tes AS papi_tanggal
    FROM users u
    " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
    LEFT JOIN (
        SELECT
            ua.user_nip,
            SUM(CASE WHEN s.urutan = 1 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
            SUM(CASE WHEN s.urutan = 2 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
            SUM(CASE WHEN s.urutan = 3 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
            SUM(CASE WHEN s.urutan = 4 THEN COALESCE((
                SELECT MAX(nilai)
                FROM iq_fill_answers fa
                WHERE fa.question_id = ua.question_id
                  AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)), REPLACE(LOWER(fa.jawaban), ', ', ',')) > 0
            ), 0) ELSE 0 END) AS GE,
            SUM(CASE WHEN s.urutan = 5 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
            SUM(CASE WHEN s.urutan = 6 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
            SUM(CASE WHEN s.urutan = 7 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
            SUM(CASE WHEN s.urutan = 8 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
            SUM(CASE WHEN s.urutan = 9 AND LOWER(TRIM(ua.jawaban_user)) = LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
        FROM iq_user_answers ua
        JOIN iq_questions q ON ua.question_id = q.id
        JOIN iq_sections s ON q.section_id = s.id
        GROUP BY ua.user_nip
    ) iqagg ON iqagg.user_nip = u.nip
    LEFT JOIN iq_results h3 ON u.nip = h3.user_id $where_iq
    LEFT JOIN hasil_msdt h1 ON u.nip = h1.nip $where_msdt
    LEFT JOIN hasil_papi h2 ON u.nip = h2.nip $where_papi
    WHERE u.role = 'peserta'
      AND (h3.user_id IS NOT NULL OR h1.nip IS NOT NULL OR h2.nip IS NOT NULL)
    ORDER BY u.nama ASC
    ";
}

$result = mysqli_query($conn, $query);
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}

// header export excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Kombinasi_" . date('Ymd') . ".xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:10px;">

    <tr>
        <td colspan="51" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL KOMBINASI (TES 1 + TES 2 BAGIAN 1 + TES 2 BAGIAN 2)
        </td>
    </tr>
    <tr>
        <td colspan="51" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
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
        
        <!-- TES 1 IQ (tanpa skor total, gunakan per-subtes seperti export Tes 1) -->
        <td style="background-color:#FBBF24; color:#000; width:45px;">SE</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">WA</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">AN</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">GE</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">RA</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">ZR</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">FA</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">WU</td>
        <td style="background-color:#FBBF24; color:#000; width:45px;">ME</td>
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
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_se'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_wa'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_an'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_ge'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_ra'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_zr'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_fa'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_wu'] ?? 0) ?></td>
        <td style="text-align:center; background-color:#FEF3C7;"><?= (int)($r['iq_me'] ?? 0) ?></td>
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
