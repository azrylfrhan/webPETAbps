<?php

require_once '../../backend/config.php';
require_once '../../backend/auth_check.php';

$data = json_decode(file_get_contents("php://input"), true);

$nip = $_SESSION['nip'];

$section  = $data['section'];
$question = $data['question'];

/* update progress */

$query = "
    UPDATE iq_test_sessions
    SET section = ?, question = ?
    WHERE nip = ?
    AND status = 'running'
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iis",$section,$question,$nip);

$stmt->execute();