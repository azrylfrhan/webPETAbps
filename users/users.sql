CREATE TABLE users (
    nip VARCHAR(30) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    satuan_kerja VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ALTER TABLE users ADD role ENUM('admin','peserta') DEFAULT 'peserta'; --
-- Tambahkan kolom status dan izin akses
-- ALTER TABLE users 
-- ADD COLUMN is_active TINYINT(1) DEFAULT 0, 
-- ADD COLUMN pangkat VARCHAR(100),
-- ADD COLUMN status_tes ENUM('belum', 'proses', 'selesai') DEFAULT 'belum';

-- Pastikan kolom role sudah ada (jika belum, aktifkan baris ini)
-- ALTER TABLE users ADD role ENUM('admin','peserta') DEFAULT 'peserta';