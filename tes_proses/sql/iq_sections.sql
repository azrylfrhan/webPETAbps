CREATE TABLE iq_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_bagian VARCHAR(50) NOT NULL,
    jumlah_soal INT NOT NULL,
    waktu_detik INT NOT NULL,
    waktu_hafalan INT DEFAULT NULL,
    instruksi TEXT,
    urutan INT NOT NULL
);