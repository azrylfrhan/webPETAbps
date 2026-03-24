<?php
/**
 * Mapping PAPI Kostick berdasarkan grid image_0e94df.png & image_0e36ec.png
 * A = Arah Horizontal (Roles - bagian atas)
 * B = Arah Diagonal (Needs - bagian bawah)
 */
$mapping_papi = [
    // Baris 1: Horizontal menuju G
    1 => ['A'=>'G', 'B'=>'E'], 11 => ['A'=>'G', 'B'=>'C'], 21 => ['A'=>'G', 'B'=>'D'], 31 => ['A'=>'G', 'B'=>'R'], 
    41 => ['A'=>'G', 'B'=>'S'], 51 => ['A'=>'G', 'B'=>'V'], 61 => ['A'=>'G', 'B'=>'T'], 71 => ['A'=>'G', 'B'=>'I'], 81 => ['A'=>'G', 'B'=>'L'],
    
    // Baris 2: Horizontal menuju L
    2 => ['B'=>'N', 'A'=>'A'], 12 => ['A'=>'L', 'B'=>'E'], 22 => ['A'=>'L', 'B'=>'C'], 32 => ['A'=>'L', 'B'=>'D'], 
    42 => ['A'=>'L', 'B'=>'R'], 52 => ['A'=>'L', 'B'=>'S'], 62 => ['A'=>'L', 'B'=>'V'], 72 => ['A'=>'L', 'B'=>'T'], 82 => ['A'=>'L', 'B'=>'I'],

    // Baris 3: Horizontal menuju I
    3 => ['B'=>'A', 'A'=>'P'], 13 => ['B'=>'N', 'A'=>'P'], 23 => ['A'=>'I', 'B'=>'E'], 33 => ['A'=>'I', 'B'=>'C'], 
    43 => ['A'=>'I', 'B'=>'D'], 53 => ['A'=>'I', 'B'=>'R'], 63 => ['A'=>'I', 'B'=>'S'], 73 => ['A'=>'I', 'B'=>'V'], 83 => ['A'=>'I', 'B'=>'T'],

    // Baris 4: Horizontal menuju T
    4 => ['B'=>'P', 'A'=>'X'], 14 => ['B'=>'A', 'A'=>'X'], 24 => ['B'=>'N', 'A'=>'X'], 34 => ['A'=>'T', 'B'=>'E'], 
    44 => ['A'=>'T', 'B'=>'C'], 54 => ['A'=>'T', 'B'=>'D'], 64 => ['A'=>'T', 'B'=>'R'], 74 => ['A'=>'T', 'B'=>'S'], 84 => ['A'=>'T', 'B'=>'V'],

    // Baris 5: Horizontal menuju V
    5 => ['B'=>'X', 'A'=>'B'], 15 => ['B'=>'P', 'A'=>'B'], 25 => ['B'=>'A', 'A'=>'B'], 35 => ['B'=>'N', 'A'=>'B'], 
    45 => ['A'=>'V', 'B'=>'E'], 55 => ['A'=>'V', 'B'=>'C'], 65 => ['A'=>'V', 'B'=>'D'], 75 => ['A'=>'V', 'B'=>'R'], 85 => ['A'=>'V', 'B'=>'S'],

    // Baris 6: Horizontal menuju S
    6 => ['B'=>'B', 'A'=>'O'], 16 => ['B'=>'X', 'A'=>'O'], 26 => ['B'=>'P', 'A'=>'O'], 36 => ['B'=>'A', 'A'=>'O'], 
    46 => ['B'=>'N', 'A'=>'O'], 56 => ['A'=>'S', 'B'=>'E'], 66 => ['A'=>'S', 'B'=>'C'], 76 => ['A'=>'S', 'B'=>'D'], 86 => ['A'=>'S', 'B'=>'R'],

    // Baris 7: Horizontal menuju R
    7 => ['B'=>'O', 'A'=>'Z'], 17 => ['B'=>'B', 'A'=>'Z'], 27 => ['B'=>'X', 'A'=>'Z'], 37 => ['B'=>'P', 'A'=>'Z'], 
    47 => ['B'=>'A', 'A'=>'Z'], 57 => ['B'=>'N', 'A'=>'Z'], 67 => ['A'=>'R', 'B'=>'E'], 77 => ['A'=>'R', 'B'=>'C'], 87 => ['A'=>'R', 'B'=>'D'],

    // Baris 8: Horizontal menuju D
    8 => ['B'=>'Z', 'A'=>'K'], 18 => ['B'=>'O', 'A'=>'K'], 28 => ['B'=>'B', 'A'=>'K'], 38 => ['B'=>'X', 'A'=>'K'], 
    48 => ['B'=>'P', 'A'=>'K'], 58 => ['B'=>'A', 'A'=>'K'], 68 => ['B'=>'N', 'A'=>'K'], 78 => ['A'=>'D', 'B'=>'E'], 88 => ['A'=>'D', 'B'=>'C'],

    // Baris 9: Horizontal menuju C
    9 => ['B'=>'K', 'A'=>'F'], 19 => ['B'=>'Z', 'A'=>'F'], 29 => ['B'=>'O', 'A'=>'F'], 39 => ['B'=>'B', 'A'=>'F'], 
    49 => ['B'=>'X', 'A'=>'F'], 59 => ['B'=>'P', 'A'=>'F'], 69 => ['B'=>'A', 'A'=>'F'], 79 => ['B'=>'N', 'A'=>'F'], 89 => ['A'=>'C', 'B'=>'E'],

    // Baris 10: Jalur Diagonal menuju E (A tetap Horizontal, B tetap Diagonal)
    10 => ['B'=>'F', 'A'=>'W'], 20 => ['B'=>'K', 'A'=>'W'], 30 => ['B'=>'Z', 'A'=>'W'], 40 => ['B'=>'O', 'A'=>'W'], 
    50 => ['B'=>'B', 'A'=>'W'], 60 => ['B'=>'X', 'A'=>'W'], 70 => ['B'=>'P', 'A'=>'W'], 80 => ['B'=>'A', 'A'=>'W'], 90 => ['A'=>'E', 'B'=>'N']
];

/**
 * Fungsi hitung skor untuk 20 dimensi PAPI Kostick
 */
function hitungSkorPapi($jawaban_user, $mapping) {
    // Inisialisasi 20 dimensi
    $skor = [
        "G"=>0, "L"=>0, "I"=>0, "T"=>0, "V"=>0, "S"=>0, "R"=>0, "D"=>0, "C"=>0, "E"=>0,
        "N"=>0, "A"=>0, "P"=>0, "X"=>0, "B"=>0, "O"=>0, "Z"=>0, "K"=>0, "F"=>0, "W"=>0
    ];

    foreach ($jawaban_user as $no => $pilihan) {
        if (isset($mapping[$no])) {
            $dimensi = $mapping[$no][$pilihan];
            $skor[$dimensi]++;
        }
    }
    return $skor;
}
?>