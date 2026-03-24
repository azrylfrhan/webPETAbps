CREATE TABLE soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_tes ENUM('KEPRIBADIAN','IQ') NOT NULL, -- ALTER TABLE soal MODIFY COLUMN kode_tes ENUM('KEPRIBADIAN', 'KEPRIBADIAN2', 'IQ') NOT NULL;
    nomor_soal INT NOT NULL,
    pertanyaan_a TEXT NOT NULL, 
    pertanyaan_b TEXT NOT NULL, 
    dimensi_a VARCHAR(5),      
    dimensi_b VARCHAR(5),       
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ALTER TABLE soal DROP COLUMN dimensi_a, DROP COLUMN dimensi_b; 