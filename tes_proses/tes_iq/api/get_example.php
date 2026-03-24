<?php

require_once('../../../backend/config.php');

header('Content-Type: application/json');

$section = intval($_GET['section']);

$query = "
SELECT 
    id,
    pertanyaan,
    jawaban_benar
FROM iq_example_questions
WHERE section_id = ?
LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $section);
$stmt->execute();

$result = $stmt->get_result();
$example = $result->fetch_assoc();

if(!$example){
    echo json_encode(["exists"=>false]);
    exit;
}

/* ambil opsi */

$query2 = "
SELECT label, opsi_text
FROM iq_example_options
WHERE example_question_id = ?
ORDER BY label ASC
";

$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("i", $example['id']);
$stmt2->execute();

$res2 = $stmt2->get_result();

$options=[];

while($row=$res2->fetch_assoc()){
    $options[]=$row;
}

echo json_encode([
    "exists"=>true,
    "pertanyaan"=>$example['pertanyaan'],
    "jawaban"=>$example['jawaban_benar'],
    "options"=>$options
]);