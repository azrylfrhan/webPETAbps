-- Insert data contoh soal untuk setiap section IQ

-- Section 1: Analogi Kata
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(1, 'Seekor kuda mempunyai kesamaan terbanyak dengan seekor...', 'c');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(1, 'a', 'kucing'),
(1, 'b', 'bajing'),
(1, 'c', 'keledai'),
(1, 'd', 'lembu'),
(1, 'e', 'anjing');

-- Section 2: Kesamaan Kata
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(2, 'Manakah kata yang tidak memiliki kesamaan dengan keempat kata yang lain?', 'c');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(2, 'a', 'meja'),
(2, 'b', 'kursi'),
(2, 'c', 'burung'),
(2, 'd', 'lemari'),
(2, 'e', 'tempat tidur');

-- Section 3: Analogi Hubungan
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(3, 'HUTAN : POHON = TEMBOK : ...', 'a');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(3, 'a', 'batu bata'),
(3, 'b', 'rumah'),
(3, 'c', 'semen'),
(3, 'd', 'putih'),
(3, 'e', 'dinding');

-- Section 4: Pengertian Umum
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(4, 'Carilah satu perkataan yang meliputi pengertian kedua kata: Ayam – Itik', 'b');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(4, 'a', 'hewan peliharaan'),
(4, 'b', 'unggas'),
(4, 'c', 'binatang bersayap'),
(4, 'd', 'hewan air'),
(4, 'e', 'burung');

-- Section 5: Soal Hitungan
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(5, 'Sebatang pensil harganya 25 rupiah. Berapakah harga 3 batang?', 'a');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(5, 'a', '75'),
(5, 'b', '96'),
(5, 'c', '87'),
(5, 'd', '68'),
(5, 'e', '79');

-- Section 6: Deret Angka
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(6, 'Lanjutkan deret berikut: 2 4 6 8 10 12 14 ?', 'b');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(6, 'a', '15'),
(6, 'b', '16'),
(6, 'c', '27'),
(6, 'd', '18'),
(6, 'e', '19');

-- Section 7: Membentuk Bangun Ruang (akan ditampilkan dengan gambar)
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(7, 'Potongan-potongan gambar di atas jika disusun akan membentuk gambar nomor...', 'a');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(7, 'a', 'Gambar A'),
(7, 'b', 'Gambar B'),
(7, 'c', 'Gambar C'),
(7, 'd', 'Gambar D'),
(7, 'e', 'Gambar E');

-- Section 8: Rotasi Kubus (akan ditampilkan dengan gambar)
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(8, 'Manakah kubus berikut yang memiliki tanda yang sama dengan kubus pada soal?', 'a');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(8, 'a', 'Kubus A'),
(8, 'b', 'Kubus B'),
(8, 'c', 'Kubus C'),
(8, 'd', 'Kubus D'),
(8, 'e', 'Kubus E');

-- Section 9: Hafalan Kata (Memory Test)
INSERT INTO iq_example_questions (section_id, pertanyaan, jawaban_benar) VALUES
(9, 'Quintet (yang dimulai dengan huruf Q) termasuk dalam jenis...', 'e');

INSERT INTO iq_example_options (example_question_id, label, opsi_text) VALUES
(9, 'a', 'bunga'),
(9, 'b', 'perkakas'),
(9, 'c', 'negara'),
(9, 'd', 'hewan'),
(9, 'e', 'kesenian');
