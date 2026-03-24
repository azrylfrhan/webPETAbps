CREATE TABLE iq_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    nomor_soal INT NOT NULL,
    pertanyaan TEXT,
    gambar VARCHAR(255) DEFAULT NULL,
    tipe_soal ENUM('pilihan','isian','angka','gambar') NOT NULL,
    jawaban_benar VARCHAR(100),
    
    FOREIGN KEY (section_id) REFERENCES iq_sections(id)
    ON DELETE CASCADE
);
