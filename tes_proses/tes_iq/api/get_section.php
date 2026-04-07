<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$section_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

/* AMBIL DATA SECTION */
$stmt = $conn->prepare("
    SELECT id, urutan, nama_bagian, jumlah_soal, waktu_detik,
           waktu_hafalan, instruksi, gambar_instruksi
    FROM iq_sections
    WHERE urutan = ?
");
$stmt->bind_param("i", $section_id);
$stmt->execute();
$section = $stmt->get_result()->fetch_assoc();

if (!$section) {
    echo json_encode(["section" => null]);
    exit;
}

// Durasi standar IST (detik)
$durasiIst = [
    1 => ['waktu_detik' => 6 * 60,  'waktu_hafalan' => null],
    2 => ['waktu_detik' => 6 * 60,  'waktu_hafalan' => null],
    3 => ['waktu_detik' => 7 * 60,  'waktu_hafalan' => null],
    4 => ['waktu_detik' => 8 * 60,  'waktu_hafalan' => null],
    5 => ['waktu_detik' => 10 * 60, 'waktu_hafalan' => null],
    6 => ['waktu_detik' => 10 * 60, 'waktu_hafalan' => null],
    7 => ['waktu_detik' => 7 * 60,  'waktu_hafalan' => null],
    8 => ['waktu_detik' => 9 * 60,  'waktu_hafalan' => null],
    9 => ['waktu_detik' => 6 * 60,  'waktu_hafalan' => 2 * 60],
];

$urutan = (int)($section['urutan'] ?? 0);
if (isset($durasiIst[$urutan])) {
    $section['waktu_detik'] = $durasiIst[$urutan]['waktu_detik'];
    $section['waktu_hafalan'] = $durasiIst[$urutan]['waktu_hafalan'];
}

/* AMBIL CONTOH SOAL — include gambar soal */
$stmt2 = $conn->prepare("
    SELECT id, pertanyaan, jawaban_benar, gambar
    FROM iq_example_questions
    WHERE section_id = ?
    LIMIT 1
");
$stmt2->bind_param("i", $section['id']);
$stmt2->execute();
$example = $stmt2->get_result()->fetch_assoc();

if ($example) {
    /* AMBIL OPSI — include gambar_opsi */
    $stmt3 = $conn->prepare("
        SELECT label, opsi_text, gambar_opsi
        FROM iq_example_options
        WHERE example_question_id = ?
        ORDER BY label ASC
    ");
    $stmt3->bind_param("i", $example['id']);
    $stmt3->execute();
    $res3    = $stmt3->get_result();
    $options = [];
    while ($row = $res3->fetch_assoc()) {
        $options[] = $row;
    }
    $example['options'] = $options;
}

echo json_encode([
    "section" => $section,
    "example" => $example
]);