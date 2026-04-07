<?php
include '../backend/auth_check.php';
require_once '../backend/config.php';

// Get date filter parameters
$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$where_date = '';
if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $where_date = "AND DATE(r.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
}

$has_biodata = false;
$cek_biodata = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
if ($cek_biodata && mysqli_num_rows($cek_biodata) > 0) {
    $has_biodata = true;
}

$sql = "
    SELECT u.nip, u.nama,
           " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
        u.jabatan, u.satuan_kerja, r.tanggal
    FROM users u
    JOIN iq_results r ON u.nip = r.user_id
    " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
    WHERE u.role = 'peserta' $where_date
    ORDER BY u.nama ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Check if using unified schema
$has_unified = false;
$cek_unified = mysqli_query($conn, "SHOW TABLES LIKE 'test_attempts'");
if ($cek_unified && mysqli_num_rows($cek_unified) > 0) {
    $cek_results = mysqli_query($conn, "SHOW TABLES LIKE 'iq_attempt_results'");
    if ($cek_results && mysqli_num_rows($cek_results) > 0) {
        $has_unified = true;
    }
}

$data_rows = [];

if ($has_unified) {
    // Use unified schema (test_attempts + iq_attempt_results)
    $where_attempt_date = '';
    if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
        $where_attempt_date = "AND DATE(ta.tanggal_mulai) BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
    }

    $sql_unified = "
        SELECT u.nip, u.nama,
               " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
               u.jabatan, u.satuan_kerja, ta.tanggal_mulai AS tanggal,
               COALESCE(NULLIF(iar.se, 0), NULLIF(calc.SE, 0), usercalc.SE, 0) AS SE,
               COALESCE(NULLIF(iar.wa, 0), NULLIF(calc.WA, 0), usercalc.WA, 0) AS WA,
               COALESCE(NULLIF(iar.an, 0), NULLIF(calc.AN, 0), usercalc.AN, 0) AS AN,
               COALESCE(NULLIF(iar.ge, 0), NULLIF(calc.GE, 0), usercalc.GE, 0) AS GE,
               COALESCE(NULLIF(iar.ra, 0), NULLIF(calc.RA, 0), usercalc.RA, 0) AS RA,
               COALESCE(NULLIF(iar.zr, 0), NULLIF(calc.ZR, 0), usercalc.ZR, 0) AS ZR,
               COALESCE(NULLIF(iar.fa, 0), NULLIF(calc.FA, 0), usercalc.FA, 0) AS FA,
               COALESCE(NULLIF(iar.wu, 0), NULLIF(calc.WU, 0), usercalc.WU, 0) AS WU,
               COALESCE(NULLIF(iar.me, 0), NULLIF(calc.ME, 0), usercalc.ME, 0) AS ME,
               COALESCE(
                   NULLIF(iar.skor_total, 0),
                   NULLIF(
                       COALESCE(calc.SE, 0) + COALESCE(calc.WA, 0) + COALESCE(calc.AN, 0) +
                       COALESCE(calc.GE, 0) + COALESCE(calc.RA, 0) + COALESCE(calc.ZR, 0) +
                       COALESCE(calc.FA, 0) + COALESCE(calc.WU, 0) + COALESCE(calc.ME, 0),
                       0
                   ),
                   0
               ) AS total_score
        FROM users u
        JOIN (
            SELECT
                ta.nip,
                MAX(ta.id) AS picked_id
            FROM test_attempts ta
            WHERE ta.test_type = 'iq' AND ta.status = 'finished' $where_attempt_date
            GROUP BY ta.nip
        ) latest ON latest.nip = u.nip
        JOIN test_attempts ta ON ta.id = latest.picked_id
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
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta'
        ORDER BY u.nama ASC
    ";
    $result = mysqli_query($conn, $sql_unified);
    while ($row = mysqli_fetch_assoc($result)) {
        $data_rows[] = $row;
    }
} else {
    // Fall back to legacy schema
    $sql = "
        SELECT u.nip, u.nama,
               " . ($has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE())" : "NULL") . " AS usia,
            u.jabatan, u.satuan_kerja, r.tanggal
        FROM users u
        JOIN iq_results r ON u.nip = r.user_id
        " . ($has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "") . "
        WHERE u.role = 'peserta' $where_date
        ORDER BY u.nama ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($users as $u) {
        $stmt_r = $conn->prepare("
            SELECT 
                SUM(CASE WHEN s.urutan=1 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS SE,
                SUM(CASE WHEN s.urutan=2 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WA,
                SUM(CASE WHEN s.urutan=3 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS AN,
                SUM(CASE WHEN s.urutan=4 THEN COALESCE((SELECT MAX(nilai) FROM iq_fill_answers fa WHERE fa.question_id=ua.question_id AND FIND_IN_SET(LOWER(TRIM(ua.jawaban_user)),REPLACE(LOWER(fa.jawaban),', ',','))>0),0) ELSE 0 END) AS GE,
                SUM(CASE WHEN s.urutan=5 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS RA,
                SUM(CASE WHEN s.urutan=6 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ZR,
                SUM(CASE WHEN s.urutan=7 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS FA,
                SUM(CASE WHEN s.urutan=8 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS WU,
                SUM(CASE WHEN s.urutan=9 AND LOWER(TRIM(ua.jawaban_user))=LOWER(TRIM(q.jawaban_benar)) THEN 1 ELSE 0 END) AS ME
            FROM iq_user_answers ua
            JOIN iq_questions q ON ua.question_id=q.id
            JOIN iq_sections s ON q.section_id=s.id
            WHERE ua.user_nip=?
        ");
        $stmt_r->bind_param("s", $u['nip']);
        $stmt_r->execute();
        $r = $stmt_r->get_result()->fetch_assoc();
        $sub_scores = $r ?? [];
        $total_score = array_sum(array_map('intval', $sub_scores));
        $data_rows[] = array_merge($u, $sub_scores, ['total_score' => $total_score]);
    }
}

// Generate Excel file (.xls format)
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Hasil_Tes-1_IQ_" . date('Ymd') . ".xls");
header("Cache-Control: max-age=0");
?>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-family:Arial; font-size:11px;">

    <tr>
        <td colspan="17" style="font-size:15px; font-weight:bold; color:#0F1E3C; border:none; padding:8px 5px 2px;">
            REKAPITULASI HASIL TES 1 (IQ) - PETA
        </td>
    </tr>
    <tr>
        <td colspan="17" style="font-size:10px; color:#64748B; border:none; padding:0 5px 10px;">
            Tanggal Export: <?= date('d F Y') ?> &nbsp;|&nbsp; Total Peserta: <?= count($data_rows) ?>
        </td>
    </tr>

    <tr style="text-align:center; font-weight:bold; font-size:11px;">
        <td style="background-color:#0F1E3C; color:#fff; width:35px;">No</td>
        <td style="background-color:#0F1E3C; color:#fff; width:130px;">NIP</td>
        <td style="background-color:#0F1E3C; color:#fff; width:180px;">Nama Pegawai</td>
        <td style="background-color:#0F1E3C; color:#fff; width:60px;">Usia</td>
        <td style="background-color:#0F1E3C; color:#fff; width:150px;">Jabatan</td>
        <td style="background-color:#0F1E3C; color:#fff; width:130px;">Satuan Kerja</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">SE</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">WA</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">AN</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">GE</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">RA</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">ZR</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">FA</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">WU</td>
        <td style="background-color:#1E40AF; color:#fff; width:40px;">ME</td>
        <td style="background-color:#4C1D95; color:#fff; width:50px;">Total</td>
        <td style="background-color:#0F1E3C; color:#fff; width:90px;">Tanggal Tes</td>
    </tr>

    <?php foreach ($data_rows as $i => $row):
        $bg = ($i % 2 === 0) ? '#FFFFFF' : '#F8FAFC';
    ?>
    <tr style="background-color:<?= $bg ?>; font-size:11px;">
        <td style="text-align:center;"><?= $i+1 ?></td>
        <td style="text-align:center; font-family:monospace;">'<?= htmlspecialchars($row['nip']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td style="text-align:center;"><?= isset($row['usia']) && $row['usia'] !== null ? (int)$row['usia'] : '-' ?></td>
        <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['satuan_kerja'] ?? '-') ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['SE'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['WA'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['AN'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['GE'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['RA'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['ZR'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['FA'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['WU'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#1d4ed8;"><?= (int)($row['ME'] ?? 0) ?></td>
        <td style="text-align:center; font-weight:bold; color:#5b21b6;"><?= (int)($row['total_score'] ?? 0) ?></td>
        <td style="text-align:center;"><?= $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-' ?></td>
    </tr>
    <?php endforeach; ?>

    <tr><td colspan="17" style="border:none; padding:4px;"></td></tr>
    <tr>
        <td colspan="17" style="font-size:9px; color:#64748B; border:1px solid #E2E8F0; background:#F8FAFC; padding:5px;">
            <b>Keterangan:</b> SE=Melengkapi Kalimat &nbsp;|&nbsp; WA=Mencari Kata Berbeda &nbsp;|&nbsp; AN=Hubungan Kata &nbsp;|&nbsp; GE=Kesamaan Kata &nbsp;|&nbsp; RA=Hitungan Praktis &nbsp;|&nbsp; ZR=Deret Angka &nbsp;|&nbsp; FA=Potongan Gambar &nbsp;|&nbsp; WU=Kemampuan Ruang &nbsp;|&nbsp; ME=Mengingat Kata
        </td>
    </tr>
</table>