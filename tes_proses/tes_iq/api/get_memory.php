<?php
require_once('../../../backend/config.php');
require_once('../../../backend/auth_check.php');

header('Content-Type: application/json');

// Ambil semua kata, group by kategori
$result = $conn->query("SELECT kategori, kata FROM iq_memory_items ORDER BY kategori, id ASC");

if (!$result) {
    echo json_encode(["items" => []]);
    exit;
}

// Group kata per kategori
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $kat = $row['kategori'];
    if (!isset($grouped[$kat])) {
        $grouped[$kat] = [];
    }
    $grouped[$kat][] = $row['kata'];
}

// Format untuk UI
$items = [];
foreach ($grouped as $kategori => $kata_list) {
    $items[] = [
        "kategori"  => $kategori,
        "kata_kata" => implode(", ", $kata_list)
    ];
}

echo json_encode(["items" => $items]);