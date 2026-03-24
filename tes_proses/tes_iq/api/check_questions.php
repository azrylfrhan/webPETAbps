<?php

require_once('../../../backend/config.php');

header('Content-Type: application/json');

$section=intval($_GET['section']);
$q=intval($_GET['q']);

$query="
SELECT id
FROM iq_questions
WHERE section_id=?
AND nomor_soal=?
";

$stmt=$conn->prepare($query);
$stmt->bind_param("ii",$section,$q);
$stmt->execute();

$res=$stmt->get_result();

if($res->num_rows>0){
    echo json_encode(["exists"=>true]);
}else{
    echo json_encode(["exists"=>false]);
}