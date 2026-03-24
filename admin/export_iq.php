<?php
include '../backend/auth_check.php';
require_once '../backend/config.php';

$stmt = $conn->prepare("
    SELECT u.nip, u.nama, u.jabatan, u.satuan_kerja, r.skor AS total_score, r.tanggal
    FROM users u
    JOIN iq_results r ON u.nip = r.user_id
    WHERE u.role = 'peserta'
    ORDER BY u.nama ASC
");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$data_rows = [];
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
    $data_rows[] = array_merge($u, $r ?? []);
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Rekap_Hasil_Tes1_IQ_' . date('Ymd') . '.csv"');
header('Cache-Control: max-age=0');

// BOM untuk Excel agar baca UTF-8 dengan benar
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

// Info
fputcsv($out, ['REKAPITULASI HASIL TES 1 (IQ) - PETA']);
fputcsv($out, ['Tanggal Export: ' . date('d F Y'), '', 'Total Peserta: ' . count($data_rows)]);
fputcsv($out, []);

// Header
fputcsv($out, ['No','NIP','Nama Pegawai','Jabatan','Satuan Kerja','SE','WA','AN','GE','RA','ZR','FA','WU','ME','Total','Tanggal Tes']);

// Data
foreach ($data_rows as $i => $row) {
    fputcsv($out, [
        $i + 1,
        $row['nip'],
        $row['nama'],
        $row['jabatan'] ?? '-',
        $row['satuan_kerja'] ?? '-',
        (int)($row['SE'] ?? 0),
        (int)($row['WA'] ?? 0),
        (int)($row['AN'] ?? 0),
        (int)($row['GE'] ?? 0),
        (int)($row['RA'] ?? 0),
        (int)($row['ZR'] ?? 0),
        (int)($row['FA'] ?? 0),
        (int)($row['WU'] ?? 0),
        (int)($row['ME'] ?? 0),
        (int)($row['total_score'] ?? 0),
        $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-',
    ]);
}

fputcsv($out, []);
fputcsv($out, ['Keterangan: SE=Melengkapi Kalimat | WA=Mencari Kata Berbeda | AN=Hubungan Kata | GE=Kesamaan Kata | RA=Hitungan Praktis | ZR=Deret Angka | FA=Potongan Gambar | WU=Kemampuan Ruang | ME=Mengingat Kata']);

fclose($out);