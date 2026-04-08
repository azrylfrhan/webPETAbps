<?php
/**
 * Setup Script - Import Data Contoh Soal IQ
 * 
 * Akses: http://localhost/kak%20misel/Tes-Psikotes/setup_iq_examples.php
 * Setelah selesai, hapus file ini!
 */

require_once 'backend/config.php';

// Fungsi untuk check dan insert data
function setupExampleQuestions($conn) {
    // Check apakah sudah ada data
    $check = $conn->query("SELECT COUNT(*) as total FROM iq_example_questions");
    $row = $check->fetch_assoc();
    
    if ($row['total'] > 0) {
        return ['status' => 'ok', 'message' => 'Data contoh soal sudah ada (' . $row['total'] . ' soal)'];
    }

    // Data contoh soal untuk setiap section
    $examples = [
        // Section 1
        [
            'section_id' => 1,
            'pertanyaan' => 'Seekor kuda mempunyai kesamaan terbanyak dengan seekor...',
            'jawaban_benar' => 'c',
            'opsi' => [
                ['a', 'kucing'],
                ['b', 'bajing'],
                ['c', 'keledai'],
                ['d', 'lembu'],
                ['e', 'anjing']
            ]
        ],
        // Section 2
        [
            'section_id' => 2,
            'pertanyaan' => 'Manakah kata yang tidak memiliki kesamaan dengan keempat kata yang lain?',
            'jawaban_benar' => 'c',
            'opsi' => [
                ['a', 'meja'],
                ['b', 'kursi'],
                ['c', 'burung'],
                ['d', 'lemari'],
                ['e', 'tempat tidur']
            ]
        ],
        // Section 3
        [
            'section_id' => 3,
            'pertanyaan' => 'HUTAN : POHON = TEMBOK : ...',
            'jawaban_benar' => 'a',
            'opsi' => [
                ['a', 'batu bata'],
                ['b', 'rumah'],
                ['c', 'semen'],
                ['d', 'putih'],
                ['e', 'dinding']
            ]
        ],
        // Section 4
        [
            'section_id' => 4,
            'pertanyaan' => 'Carilah satu perkataan yang meliputi pengertian kedua kata: Ayam – Itik',
            'jawaban_benar' => 'b',
            'opsi' => [
                ['a', 'hewan peliharaan'],
                ['b', 'unggas'],
                ['c', 'binatang bersayap'],
                ['d', 'hewan air'],
                ['e', 'burung']
            ]
        ],
        // Section 5
        [
            'section_id' => 5,
            'pertanyaan' => 'Sebatang pensil harganya 25 rupiah. Berapakah harga 3 batang?',
            'jawaban_benar' => 'a',
            'opsi' => [
                ['a', '75'],
                ['b', '96'],
                ['c', '87'],
                ['d', '68'],
                ['e', '79']
            ]
        ],
        // Section 6
        [
            'section_id' => 6,
            'pertanyaan' => 'Lanjutkan deret berikut: 2 4 6 8 10 12 14 ?',
            'jawaban_benar' => 'b',
            'opsi' => [
                ['a', '15'],
                ['b', '16'],
                ['c', '27'],
                ['d', '18'],
                ['e', '19']
            ]
        ],
        // Section 7
        [
            'section_id' => 7,
            'pertanyaan' => 'Potongan-potongan gambar di atas jika disusun akan membentuk gambar nomor...',
            'jawaban_benar' => 'a',
            'opsi' => [
                ['a', 'Gambar A'],
                ['b', 'Gambar B'],
                ['c', 'Gambar C'],
                ['d', 'Gambar D'],
                ['e', 'Gambar E']
            ]
        ],
        // Section 8
        [
            'section_id' => 8,
            'pertanyaan' => 'Manakah kubus berikut yang memiliki tanda yang sama dengan kubus pada soal?',
            'jawaban_benar' => 'a',
            'opsi' => [
                ['a', 'Kubus A'],
                ['b', 'Kubus B'],
                ['c', 'Kubus C'],
                ['d', 'Kubus D'],
                ['e', 'Kubus E']
            ]
        ],
        // Section 9
        [
            'section_id' => 9,
            'pertanyaan' => 'Quintet (yang dimulai dengan huruf Q) termasuk dalam jenis...',
            'jawaban_benar' => 'e',
            'opsi' => [
                ['a', 'bunga'],
                ['b', 'perkakas'],
                ['c', 'negara'],
                ['d', 'hewan'],
                ['e', 'kesenian']
            ]
        ]
    ];

    $inserted = 0;
    foreach ($examples as $ex) {
        // Insert pertanyaan
        $stmt = $conn->prepare("INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $ex['section_id'], $ex['pertanyaan'], $ex['jawaban_benar']);
        
        if ($stmt->execute()) {
            $question_id = $conn->insert_id;
            
            // Insert opsi
            foreach ($ex['opsi'] as $opt) {
                $stmt2 = $conn->prepare("INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES (?, ?, ?)");
                $stmt2->bind_param("iss", $question_id, $opt[0], $opt[1]);
                $stmt2->execute();
                $stmt2->close();
            }
            
            $inserted++;
        }
        $stmt->close();
    }

    return ['status' => 'success', 'message' => "Berhasil insert $inserted contoh soal"];
}

// Jalankan setup
$result = setupExampleQuestions($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Data Contoh Soal IQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Setup Data Contoh Soal IQ</h1>
        
        <?php if ($result['status'] === 'success'): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <p class="text-green-800 font-semibold">✓ <?php echo $result['message']; ?></p>
            </div>
            <p class="text-slate-600 mb-4">Data contoh soal berhasil disetup! Anda sekarang bisa mengakses halaman instruksi IQ.</p>
            <a href="instruksi_iq.php?sub=1" class="w-full block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold">
                Coba Instruksi IQ →
            </a>
            <p class="text-xs text-slate-500 mt-4 text-center">
                ⚠️ Perhatian: Silakan hapus file ini (setup_iq_examples.php) setelah setup selesai!
            </p>
        <?php else: ?>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 font-semibold">ℹ️ <?php echo $result['message']; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
