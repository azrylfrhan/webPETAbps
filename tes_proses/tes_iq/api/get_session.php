<?php
require_once '../../backend/config.php';
require_once '../../backend/auth_check.php';

header('Content-Type: application/json');

$nip = $_SESSION['nip'];

/* cek apakah sudah ada session */

$query = "
    SELECT *
    FROM iq_test_sessions
    WHERE nip = ?
    AND status = 'running'
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s",$nip);
$stmt->execute();

$result = $stmt->get_result();
$data   = $result->fetch_assoc();

if($data){

    echo json_encode([
        "resume" => true,
        "section" => $data['section'],
        "question" => $data['question']
    ]);

}else{

    echo json_encode([
        "resume" => false
    ]);

}