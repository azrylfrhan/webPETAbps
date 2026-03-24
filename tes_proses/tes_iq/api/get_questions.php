<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

$urutan   = intval($_GET['section']); // urutan section (1,2,3,...)
$question = intval($_GET['q']);       // nomor urutan soal dalam section (1,2,3,...)

// Cari id section berdasarkan urutan
$stmt0 = $conn->prepare("SELECT id FROM iq_sections WHERE urutan = ? LIMIT 1");
$stmt0->bind_param("i", $urutan);
$stmt0->execute();
$sec_row = $stmt0->get_result()->fetch_assoc();

if (!$sec_row) {
    echo json_encode(["exists" => false]);
    exit;
}

$section_id = $sec_row['id'];
$offset     = $question - 1; // urutan ke-N = offset N-1

// Ambil soal ke-N dalam section ini, diurutkan by nomor_soal
$stmt = $conn->prepare("
    SELECT id, pertanyaan, gambar, nomor_soal
    FROM iq_questions
    WHERE section_id = ?
    ORDER BY nomor_soal ASC
    LIMIT 1 OFFSET ?
");
$stmt->bind_param("ii", $section_id, $offset);
$stmt->execute();
$soal = $stmt->get_result()->fetch_assoc();

if (!$soal) {
    echo json_encode(["exists" => false]);
    exit;
}

// Ambil opsi jawaban
$stmt2 = $conn->prepare("
    SELECT label, opsi_text, gambar_opsi
    FROM iq_options
    WHERE question_id = ?
    ORDER BY label ASC
");
$stmt2->bind_param("i", $soal['id']);
$stmt2->execute();
$options_result = $stmt2->get_result();

$options = [];
while ($row = $options_result->fetch_assoc()) {
    $options[] = $row;
}

echo json_encode([
    "exists"     => true,
    "id"         => $soal['id'],
    "pertanyaan" => $soal['pertanyaan'],
    "gambar"     => $soal['gambar'],
    "options"    => $options
]);