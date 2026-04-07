-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Mar 2026 pada 04.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bps_psikotes`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `biodata_peserta`
--

CREATE TABLE `biodata_peserta` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `email` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `biodata_peserta`
--

INSERT INTO `biodata_peserta` (`id`, `nip`, `tempat_lahir`, `tanggal_lahir`, `email`, `created_at`, `updated_at`) VALUES
(1, '10386446990001', 'SONDER', '2004-09-28', 'aasasasa@gmail.com', '2026-03-30 08:00:37', '2026-03-30 08:00:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_msdt`
--

CREATE TABLE `hasil_msdt` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `Ds` int(11) DEFAULT NULL,
  `Mi` int(11) DEFAULT NULL,
  `Au` int(11) DEFAULT NULL,
  `Co` int(11) DEFAULT NULL,
  `Bu` int(11) DEFAULT NULL,
  `Dv` int(11) DEFAULT NULL,
  `Ba` int(11) DEFAULT NULL,
  `E_dim` int(11) DEFAULT NULL,
  `TO_score` int(11) DEFAULT NULL,
  `RO_score` int(11) DEFAULT NULL,
  `E_score` int(11) DEFAULT NULL,
  `O_score` int(11) DEFAULT NULL,
  `dominant_model` varchar(5) DEFAULT NULL,
  `tanggal_tes` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_msdt`
--

INSERT INTO `hasil_msdt` (`id`, `nip`, `Ds`, `Mi`, `Au`, `Co`, `Bu`, `Dv`, `Ba`, `E_dim`, `TO_score`, `RO_score`, `E_score`, `O_score`, `dominant_model`, `tanggal_tes`) VALUES
(7, '230211060035', 9, 9, 7, 5, 10, 8, 10, 16, 38, 38, 44, 9, NULL, '2026-02-13 14:10:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_papi`
--

CREATE TABLE `hasil_papi` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `G` tinyint(4) DEFAULT 0,
  `L` tinyint(4) DEFAULT 0,
  `I` tinyint(4) DEFAULT 0,
  `T` tinyint(4) DEFAULT 0,
  `V` tinyint(4) DEFAULT 0,
  `S` tinyint(4) DEFAULT 0,
  `R` tinyint(4) DEFAULT 0,
  `D` tinyint(4) DEFAULT 0,
  `C` tinyint(4) DEFAULT 0,
  `E` tinyint(4) DEFAULT 0,
  `N` tinyint(4) DEFAULT 0,
  `A` tinyint(4) DEFAULT 0,
  `P` tinyint(4) DEFAULT 0,
  `X` tinyint(4) DEFAULT 0,
  `B` tinyint(4) DEFAULT 0,
  `O` tinyint(4) DEFAULT 0,
  `K` tinyint(4) DEFAULT 0,
  `F` tinyint(4) DEFAULT 0,
  `W` tinyint(4) DEFAULT 0,
  `Z` tinyint(4) DEFAULT 0,
  `tanggal_tes` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_papi`
--

INSERT INTO `hasil_papi` (`id`, `nip`, `G`, `L`, `I`, `T`, `V`, `S`, `R`, `D`, `C`, `E`, `N`, `A`, `P`, `X`, `B`, `O`, `K`, `F`, `W`, `Z`, `tanggal_tes`) VALUES
(4, '230211060035', 9, 0, 8, 1, 7, 2, 6, 3, 5, 4, 5, 4, 6, 3, 7, 2, 1, 9, 0, 8, '2026-02-20 07:23:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_example_options`
--

CREATE TABLE `iq_example_options` (
  `id` int(11) NOT NULL,
  `example_question_id` int(11) NOT NULL,
  `label` char(1) NOT NULL,
  `opsi_text` varchar(255) DEFAULT NULL,
  `gambar_opsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_example_options`
--

INSERT INTO `iq_example_options` (`id`, `example_question_id`, `label`, `opsi_text`, `gambar_opsi`) VALUES
(1, 1, 'A', 'duka', NULL),
(2, 1, 'B', 'putus asa', NULL),
(3, 1, 'C', 'sengsara', NULL),
(4, 1, 'D', 'cinta', NULL),
(5, 1, 'E', 'benci', NULL),
(6, 2, 'A', 'duduk', NULL),
(7, 2, 'B', 'berbaring', NULL),
(8, 2, 'C', 'berdiri', NULL),
(9, 2, 'D', 'berjalan', NULL),
(10, 2, 'E', 'berjongkok', NULL),
(11, 3, 'A', 'hujan', NULL),
(12, 3, 'B', 'hari', NULL),
(13, 3, 'C', 'lembab', NULL),
(14, 3, 'D', 'angin', NULL),
(15, 3, 'E', 'kering', NULL),
(16, 5, 'A', '50', NULL),
(17, 5, 'B', '70', NULL),
(18, 5, 'C', '87', NULL),
(19, 5, 'D', '60', NULL),
(20, 5, 'E', '67', NULL),
(21, 6, 'A', '15', NULL),
(22, 6, 'B', '16', NULL),
(23, 6, 'C', '17', NULL),
(24, 6, 'D', '14', NULL),
(25, 6, 'E', '10', NULL),
(26, 7, 'A', 'gambar A', NULL),
(27, 7, 'B', 'gambar B', NULL),
(28, 7, 'C', 'gambar C', NULL),
(29, 7, 'D', 'gambar D', NULL),
(30, 7, 'E', 'gambar E', NULL),
(31, 8, 'A', 'gambar A', NULL),
(32, 8, 'B', 'gambar B', NULL),
(33, 8, 'C', 'gambar C', NULL),
(34, 8, 'D', 'gambar D', NULL),
(35, 8, 'E', 'gambar E', NULL),
(36, 9, 'A', 'bunga', NULL),
(37, 9, 'B', 'perkakas', NULL),
(38, 9, 'C', 'negara', NULL),
(39, 9, 'D', 'kesenian', NULL),
(40, 9, 'E', 'hewan', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_example_questions`
--

CREATE TABLE `iq_example_questions` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban_benar` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_example_questions`
--

INSERT INTO `iq_example_questions` (`id`, `section_id`, `pertanyaan`, `jawaban_benar`, `gambar`) VALUES
(1, 1, 'Lawannya \"harapan\" adalah', 'B', NULL),
(2, 2, 'Manakah yang tidak memiliki kesamaan?', 'D', NULL),
(3, 3, 'GELAP : TERANG = BASAH : ... ', 'E', NULL),
(4, 4, 'Gaun – celana ...', 'pakaian', NULL),
(5, 5, 'Husin menempuh 15 km dalam 1 jam. Berapa km yang ditempuh dalam 4 jam ?', 'D', NULL),
(6, 6, '9 7 10 8 11 9 12 ?', 'E', NULL),
(7, 7, 'Hasil susunan potongan-potongan pada contoh 2 (gambar kedua) adalah bentuk...', 'E', 'contoh 7.jpeg'),
(8, 8, 'Mencari posisi kubus (gambar kedua) yang sama setelah diputar/digulingkan', 'E', 'contoh 8.jpeg'),
(9, 9, 'Kata yang mempunyai huruf permulaan R (Rusa) adalah suatu ...', 'E', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_fill_answers`
--

CREATE TABLE `iq_fill_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `jawaban` varchar(255) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_fill_answers`
--

INSERT INTO `iq_fill_answers` (`id`, `question_id`, `jawaban`, `nilai`) VALUES
(1, 61, 'Bunga', 2),
(2, 61, 'Tumbuh-tumbuhan', 1),
(3, 61, 'Kembang', 2),
(4, 61, 'Tangkal', 1),
(5, 61, 'Perdu', 2),
(6, 61, 'Harum', 1),
(7, 62, 'Alat indera', 2),
(8, 62, 'Organ', 1),
(9, 62, 'Indera', 2),
(10, 62, 'Alat tubuh', 1),
(11, 62, 'Panca indera', 2),
(12, 63, 'Hablur', 2),
(13, 63, 'Berkilauan', 1),
(14, 63, 'Kristal', 2),
(15, 63, 'Mengkilat', 1),
(16, 63, 'Zat arang', 2),
(17, 63, 'Bening', 1),
(18, 64, 'Cuaca', 2),
(19, 64, 'basah', 1),
(20, 64, 'Air', 1),
(21, 64, 'Gejala alam', 1),
(22, 64, 'Musim', 1),
(23, 65, 'Pembawa berita', 2),
(24, 65, 'Pos', 1),
(25, 65, 'PTT', 1),
(26, 65, 'Alat perhubungan', 2),
(27, 65, 'Telekomunikasi', 1),
(28, 65, 'Alat sarana', 2),
(29, 65, 'komunikasi', 2),
(30, 65, 'Perhubungan', 1),
(31, 65, 'Komunikasi', 1),
(32, 66, 'Alat optic', 2),
(33, 66, 'Lensa', 1),
(34, 66, 'Optic', 2),
(35, 67, 'Alat pencernaan', 2),
(36, 67, 'Jalan makanan', 1),
(37, 67, 'Perut', 1),
(38, 67, 'isi perut', 1),
(39, 67, 'Pencernaan makanan', 1),
(40, 67, 'Organ', 1),
(41, 67, 'organ tubuh', 1),
(42, 67, 'Saluran makanan', 1),
(43, 68, 'Penyebut jumlah', 2),
(44, 68, 'Mengukur', 1),
(45, 68, 'Pengertian jumlah', 2),
(46, 68, 'Ukuran', 1),
(47, 68, 'Jumlah', 2),
(48, 68, 'Kapasitas', 1),
(49, 68, 'Kuantitas', 2),
(50, 69, 'Bibit', 2),
(51, 69, 'bakal', 2),
(52, 69, 'Sel', 1),
(53, 69, 'Alat pembiak', 2),
(54, 69, 'Pembiakan', 1),
(55, 69, 'Permulaan', 2),
(56, 69, 'Reproduksi', 1),
(57, 69, 'Penghidupan', 2),
(58, 69, 'Embrio', 1),
(59, 70, 'Symbol', 2),
(60, 70, 'Nama', 1),
(61, 70, 'Lambang', 2),
(62, 70, 'Tanda pengenal', 1),
(63, 70, 'Tanda', 2),
(64, 71, 'Makhluk hidup', 2),
(65, 71, 'Tumbuh', 1),
(66, 71, 'Organisme', 2),
(67, 71, 'Biologi', 1),
(68, 71, 'Ilmu hayat', 1),
(69, 71, 'Makhluk', 1),
(70, 72, 'Wadah', 2),
(71, 72, 'Alat', 1),
(72, 72, 'Tempat Pengisi', 2),
(73, 72, 'Tempat sesuatu', 1),
(74, 72, 'Tempat menyimpan', 2),
(75, 72, 'Tempat', 1),
(76, 72, 'Tempat menampung', 2),
(77, 72, 'Perkakas', 1),
(78, 73, 'Pengertian waktu', 2),
(79, 73, 'Waktu', 1),
(80, 73, 'masa', 1),
(81, 73, 'saat', 1),
(82, 73, 'Batas', 2),
(83, 73, 'Lamanya', 1),
(84, 74, 'Sifat – watak', 2),
(85, 74, 'Sifat', 1),
(86, 74, 'Sifat – karakter', 2),
(87, 74, 'Watak', 1),
(88, 75, 'Regulator harga', 2),
(89, 75, 'Dagang', 1),
(90, 75, 'niaga', 1),
(91, 75, 'Pengertian ekonomi', 2),
(92, 75, 'Penjualan', 1),
(93, 75, 'pembelian', 1),
(94, 75, 'Istilah ekonomi', 2),
(95, 75, 'Jual beli', 1),
(96, 75, 'Ekonomi', 1),
(97, 75, 'Transaksi', 1),
(98, 76, 'Pengertian ruang', 2),
(99, 76, 'Arah', 1),
(100, 76, 'letak', 1),
(101, 76, 'Penyebut ruang', 2),
(102, 76, 'Penentuan daerah', 1),
(103, 76, 'Posisi', 2),
(104, 76, 'Tempat', 1),
(105, 76, 'Truang', 1),
(106, 76, 'Penunjuk tempat', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_memory_items`
--

CREATE TABLE `iq_memory_items` (
  `id` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `kata` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_memory_items`
--

INSERT INTO `iq_memory_items` (`id`, `kategori`, `kata`) VALUES
(1, 'BUNGA', 'Flamboyan'),
(2, 'BUNGA', 'Lily'),
(3, 'BUNGA', 'Soka'),
(4, 'BUNGA', 'Yasmin'),
(5, 'BUNGA', 'Dahlia'),
(6, 'PERKAKAS', 'Wajan'),
(7, 'PERKAKAS', 'Jarum'),
(8, 'PERKAKAS', 'Palu'),
(9, 'PERKAKAS', 'Cangkul'),
(10, 'PERKAKAS', 'Kikir'),
(11, 'BURUNG', 'Itik'),
(12, 'BURUNG', 'Elang'),
(13, 'BURUNG', 'Walet'),
(14, 'BURUNG', 'Tekukur'),
(15, 'BURUNG', 'Nuri'),
(16, 'KESENIAN', 'Gamelan'),
(17, 'KESENIAN', 'Opera'),
(18, 'KESENIAN', 'Arca'),
(19, 'KESENIAN', 'Quintet'),
(20, 'KESENIAN', 'Ukiran'),
(21, 'HEWAN', 'Musang'),
(22, 'HEWAN', 'Rusa'),
(23, 'HEWAN', 'Beruang'),
(24, 'HEWAN', 'Zebra'),
(25, 'HEWAN', 'Harimau');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_options`
--

CREATE TABLE `iq_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `label` char(1) NOT NULL,
  `opsi_text` varchar(255) DEFAULT NULL,
  `gambar_opsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_options`
--

INSERT INTO `iq_options` (`id`, `question_id`, `label`, `opsi_text`, `gambar_opsi`) VALUES
(1, 1, 'A', 'kekuasaan', NULL),
(2, 1, 'B', 'bujukan', NULL),
(3, 1, 'C', 'kekayaan', NULL),
(4, 1, 'D', 'keberanian', NULL),
(5, 1, 'E', 'kewibawaan', NULL),
(6, 2, 'A', 'murah', NULL),
(7, 2, 'B', 'kikir', NULL),
(8, 2, 'C', 'boros', NULL),
(9, 2, 'D', 'bernilai', NULL),
(10, 2, 'E', 'kaya', NULL),
(11, 3, 'A', 'angin puyuh', NULL),
(12, 3, 'B', 'halilintar', NULL),
(13, 3, 'C', 'salju', NULL),
(14, 3, 'D', 'gempa bumi', NULL),
(15, 3, 'E', 'kabut', NULL),
(16, 4, 'A', 'cinta', NULL),
(17, 4, 'B', 'benci', NULL),
(18, 4, 'C', 'persahabatan', NULL),
(19, 4, 'D', 'khianat', NULL),
(20, 4, 'E', 'permusuhan', NULL),
(21, 5, 'A', 'kandang', NULL),
(22, 5, 'B', 'ladam', NULL),
(23, 5, 'C', 'pelana', NULL),
(24, 5, 'D', 'kuku', NULL),
(25, 5, 'E', 'surai', NULL),
(26, 6, 'A', 'jarang', NULL),
(27, 6, 'B', 'biasanya', NULL),
(28, 6, 'C', 'selalu', NULL),
(29, 6, 'D', 'tak pernah', NULL),
(30, 6, 'E', 'kadang-kadang', NULL),
(31, 7, 'A', 'ikan', NULL),
(32, 7, 'B', 'daging', NULL),
(33, 7, 'C', 'lemak', NULL),
(34, 7, 'D', 'tahu', NULL),
(35, 7, 'E', 'sayuran', NULL),
(36, 8, 'A', 'lawan', NULL),
(37, 8, 'B', 'wasit', NULL),
(38, 8, 'C', 'penonton', NULL),
(39, 8, 'D', 'sorak', NULL),
(40, 8, 'E', 'kemenangan', NULL),
(41, 9, 'A', 'paradoks', NULL),
(42, 9, 'B', 'tergesa-gesa', NULL),
(43, 9, 'C', 'mempunyai arti lengkap', NULL),
(44, 9, 'D', 'menyesatkan', NULL),
(45, 9, 'E', 'hipotesis', NULL),
(46, 10, 'A', 'kulit', NULL),
(47, 10, 'B', 'sol', NULL),
(48, 10, 'C', 'tali sepatu', NULL),
(49, 10, 'D', 'gesper', NULL),
(50, 10, 'E', 'lidah', NULL),
(51, 11, 'A', 'lampu lalulintas', NULL),
(52, 11, 'B', 'kacamata pelindung', NULL),
(53, 11, 'C', 'kotak PPPK', NULL),
(54, 11, 'D', 'tanda peringatan', NULL),
(55, 11, 'E', 'palang kereta api', NULL),
(56, 12, 'A', '17', NULL),
(57, 12, 'B', '29', NULL),
(58, 12, 'C', '25', NULL),
(59, 12, 'D', '24', NULL),
(60, 12, 'E', '15', NULL),
(61, 13, 'A', 'demokratis', NULL),
(62, 13, 'B', 'radikal', NULL),
(63, 13, 'C', 'liberal', NULL),
(64, 13, 'D', 'konservativ', NULL),
(65, 13, 'E', 'anarkis', NULL),
(66, 14, 'A', 'sering', NULL),
(67, 14, 'B', 'kadang', NULL),
(68, 14, 'C', 'jarang', NULL),
(69, 14, 'D', 'kerap kali', NULL),
(70, 14, 'E', 'selalu', NULL),
(71, 15, 'A', '650', NULL),
(72, 15, 'B', '1000', NULL),
(73, 15, 'C', '800', NULL),
(74, 15, 'D', '6000', NULL),
(75, 15, 'E', '950', NULL),
(76, 16, 'A', 'kekuatan', NULL),
(77, 16, 'B', 'peranan', NULL),
(78, 16, 'C', 'ayunan', NULL),
(79, 16, 'D', 'berat', NULL),
(80, 16, 'E', 'suara', NULL),
(81, 17, 'A', 'selalu', NULL),
(82, 17, 'B', 'biasanya', NULL),
(83, 17, 'C', 'jauh', NULL),
(84, 17, 'D', 'jarang', NULL),
(85, 17, 'E', 'pada dasarnya', NULL),
(86, 18, 'A', 'Jakarta', NULL),
(87, 18, 'B', 'Bandung', NULL),
(88, 18, 'C', 'Cirebon', NULL),
(89, 18, 'D', 'Semarang', NULL),
(90, 18, 'E', 'Surabaya', NULL),
(91, 19, 'A', 'jml nomor menang', NULL),
(92, 19, 'B', 'pajak lotere', NULL),
(93, 19, 'C', 'kemungkinan menang', NULL),
(94, 19, 'D', 'jml pengikut', NULL),
(95, 19, 'E', 'tinggi keuntungan', NULL),
(96, 20, 'A', '150', NULL),
(97, 20, 'B', '130', NULL),
(98, 20, 'C', '110', NULL),
(99, 20, 'D', '105', NULL),
(100, 20, 'E', '115', NULL),
(101, 21, 'A', 'lingkaran', NULL),
(102, 21, 'B', 'panah', NULL),
(103, 21, 'C', 'elips', NULL),
(104, 21, 'D', 'busur', NULL),
(105, 21, 'E', 'lengkungan', NULL),
(106, 22, 'A', 'mengetuk', NULL),
(107, 22, 'B', 'memaki', NULL),
(108, 22, 'C', 'menjahit', NULL),
(109, 22, 'D', 'menggergaji', NULL),
(110, 22, 'E', 'memukul', NULL),
(111, 23, 'A', 'lebar', NULL),
(112, 23, 'B', 'keliling', NULL),
(113, 23, 'C', 'luas', NULL),
(114, 23, 'D', 'isi', NULL),
(115, 23, 'E', 'panjang', NULL),
(116, 24, 'A', 'mengikat', NULL),
(117, 24, 'B', 'menyatukan', NULL),
(118, 24, 'C', 'melepaskan', NULL),
(119, 24, 'D', 'mengaitkan', NULL),
(120, 24, 'E', 'melekatkan', NULL),
(121, 25, 'A', 'arah', NULL),
(122, 25, 'B', 'timur', NULL),
(123, 25, 'C', 'perjalanan', NULL),
(124, 25, 'D', 'tujuan', NULL),
(125, 25, 'E', 'selatan', NULL),
(126, 26, 'A', 'jarak', NULL),
(127, 26, 'B', 'perpisahan', NULL),
(128, 26, 'C', 'tugas', NULL),
(129, 26, 'D', 'batas', NULL),
(130, 26, 'E', 'perceraian', NULL),
(131, 27, 'A', 'saringan', NULL),
(132, 27, 'B', 'kelambu', NULL),
(133, 27, 'C', 'payung', NULL),
(134, 27, 'D', 'tapisan', NULL),
(135, 27, 'E', 'jala', NULL),
(136, 28, 'A', 'putih', NULL),
(137, 28, 'B', 'pucat', NULL),
(138, 28, 'C', 'buram', NULL),
(139, 28, 'D', 'kasar', NULL),
(140, 28, 'E', 'berkilauan', NULL),
(141, 29, 'A', 'otobis', NULL),
(142, 29, 'B', 'pesawat terbang', NULL),
(143, 29, 'C', 'sepeda motor', NULL),
(144, 29, 'D', 'sepeda', NULL),
(145, 29, 'E', 'kapal api', NULL),
(146, 30, 'A', 'biola', NULL),
(147, 30, 'B', 'seruling', NULL),
(148, 30, 'C', 'clarinet', NULL),
(149, 30, 'D', 'terompet', NULL),
(150, 30, 'E', 'saxophone', NULL),
(151, 31, 'A', 'bergelombang', NULL),
(152, 31, 'B', 'kasar', NULL),
(153, 31, 'C', 'berduri', NULL),
(154, 31, 'D', 'licin', NULL),
(155, 31, 'E', 'lurus', NULL),
(156, 32, 'A', 'jam', NULL),
(157, 32, 'B', 'kompas', NULL),
(158, 32, 'C', 'penunjuk jalan', NULL),
(159, 32, 'D', 'bintang pari', NULL),
(160, 32, 'E', 'arah', NULL),
(161, 33, 'A', 'kebijaksanaan', NULL),
(162, 33, 'B', 'pendidikan', NULL),
(163, 33, 'C', 'perencanaan', NULL),
(164, 33, 'D', 'penempatan', NULL),
(165, 33, 'E', 'pengarahan', NULL),
(166, 34, 'A', 'bermotor', NULL),
(167, 34, 'B', 'berjalan', NULL),
(168, 34, 'C', 'berlayar', NULL),
(169, 34, 'D', 'bersepeda', NULL),
(170, 34, 'E', 'berkuda', NULL),
(171, 35, 'A', 'gambar', NULL),
(172, 35, 'B', 'lukisan', NULL),
(173, 35, 'C', 'potret', NULL),
(174, 35, 'D', 'patung', NULL),
(175, 35, 'E', 'ukiran', NULL),
(176, 36, 'A', 'panjang', NULL),
(177, 36, 'B', 'lonjong', NULL),
(178, 36, 'C', 'runcing', NULL),
(179, 36, 'D', 'bulat', NULL),
(180, 36, 'E', 'bersudut', NULL),
(181, 37, 'A', 'kunci', NULL),
(182, 37, 'B', 'palang pintu', NULL),
(183, 37, 'C', 'gerendel', NULL),
(184, 37, 'D', 'gunting', NULL),
(185, 37, 'E', 'obeng', NULL),
(186, 38, 'A', 'jembatan', NULL),
(187, 38, 'B', 'batas', NULL),
(188, 38, 'C', 'perkawinan', NULL),
(189, 38, 'D', 'pagar', NULL),
(190, 38, 'E', 'masyarakat', NULL),
(191, 39, 'A', 'mengetam', NULL),
(192, 39, 'B', 'mamahat', NULL),
(193, 39, 'C', 'mengasah', NULL),
(194, 39, 'D', 'melicinkan', NULL),
(195, 39, 'E', 'menggosok', NULL),
(196, 40, 'A', 'batu', NULL),
(197, 40, 'B', 'baja', NULL),
(198, 40, 'C', 'bulu', NULL),
(199, 40, 'D', 'karet', NULL),
(200, 40, 'E', 'kayu', NULL),
(201, 41, 'A', 'menghafal', NULL),
(202, 41, 'B', 'mengenai', NULL),
(203, 41, 'C', 'melupakan', NULL),
(204, 41, 'D', 'berpikir', NULL),
(205, 41, 'E', 'memimpikan', NULL),
(206, 42, 'A', 'sarang', NULL),
(207, 42, 'B', 'langit', NULL),
(208, 42, 'C', 'pagar', NULL),
(209, 42, 'D', 'pohon', NULL),
(210, 42, 'E', 'sangkar', NULL),
(211, 43, 'A', 'roda', NULL),
(212, 43, 'B', 'poros', NULL),
(213, 43, 'C', 'ban', NULL),
(214, 43, 'D', 'jalan raya', NULL),
(215, 43, 'E', 'kecepatan', NULL),
(216, 44, 'A', 'arloji', NULL),
(217, 44, 'B', 'berlian', NULL),
(218, 44, 'C', 'permata', NULL),
(219, 44, 'D', 'gelang', NULL),
(220, 44, 'E', 'platina', NULL),
(221, 45, 'A', 'bentuk', NULL),
(222, 45, 'B', 'gambar', NULL),
(223, 45, 'C', 'segi empat', NULL),
(224, 45, 'D', 'kubus', NULL),
(225, 45, 'E', 'piramida', NULL),
(226, 46, 'A', 'menawarkan', NULL),
(227, 46, 'B', 'menentukan', NULL),
(228, 46, 'C', 'menilai', NULL),
(229, 46, 'D', 'menimbang', NULL),
(230, 46, 'E', 'merenungkan', NULL),
(231, 47, 'A', 'mencium', NULL),
(232, 47, 'B', 'bernapas', NULL),
(233, 47, 'C', 'mengecap', NULL),
(234, 47, 'D', 'tengik', NULL),
(235, 47, 'E', 'asin', NULL),
(236, 48, 'A', 'pitu air', NULL),
(237, 48, 'B', 'sungai', NULL),
(238, 48, 'C', 'talang', NULL),
(239, 48, 'D', 'hujan', NULL),
(240, 48, 'E', 'ember', NULL),
(241, 49, 'A', 'penyinara', NULL),
(242, 49, 'B', 'mata', NULL),
(243, 49, 'C', 'melihat', NULL),
(244, 49, 'D', 'cahaya', NULL),
(245, 49, 'E', 'pelindung', NULL),
(246, 50, 'A', 'palu godam', NULL),
(247, 50, 'B', 'pedagang besi', NULL),
(248, 50, 'C', 'api', NULL),
(249, 50, 'D', 'tukang emas', NULL),
(250, 50, 'E', 'besi tempa', NULL),
(251, 51, 'A', 'pendengaan', NULL),
(252, 51, 'B', 'mendengar', NULL),
(253, 51, 'C', 'nada', NULL),
(254, 51, 'D', 'kata', NULL),
(255, 51, 'E', 'telinga', NULL),
(256, 52, 'A', 'penghinaan', NULL),
(257, 52, 'B', 'pidato', NULL),
(258, 52, 'C', 'kelakar', NULL),
(259, 52, 'D', 'pesan', NULL),
(260, 52, 'E', 'ayat', NULL),
(261, 53, 'A', 'suka ccita', NULL),
(262, 53, 'B', 'sakit hati', NULL),
(263, 53, 'C', 'suasana hati', NULL),
(264, 53, 'D', 'sedih', NULL),
(265, 53, 'E', 'rindu', NULL),
(266, 54, 'A', 'bahan sandang', NULL),
(267, 54, 'B', 'domba', NULL),
(268, 54, 'C', 'sutra', NULL),
(269, 54, 'D', 'jas', NULL),
(270, 54, 'E', 'tekstil', NULL),
(271, 55, 'A', 'garpu penala', NULL),
(272, 55, 'B', 'sopran', NULL),
(273, 55, 'C', 'nyanyian', NULL),
(274, 55, 'D', 'panjang senar', NULL),
(275, 55, 'E', 'suara', NULL),
(276, 56, 'A', 'biologi', NULL),
(277, 56, 'B', 'keturunan', NULL),
(278, 56, 'C', 'mutasi', NULL),
(279, 56, 'D', 'seleksi', NULL),
(280, 56, 'E', 'ilmu hewan', NULL),
(281, 57, 'A', 'haus', NULL),
(282, 57, 'B', 'khatulistiwa', NULL),
(283, 57, 'C', 'es', NULL),
(284, 57, 'D', 'matahari', NULL),
(285, 57, 'E', 'dingin', NULL),
(286, 58, 'A', 'dipalu', NULL),
(287, 58, 'B', 'digergaji', NULL),
(288, 58, 'C', 'dituang', NULL),
(289, 58, 'D', 'dikikir', NULL),
(290, 58, 'E', 'ditempa', NULL),
(291, 59, 'A', 'perpustakaan', NULL),
(292, 59, 'B', 'penelitian', NULL),
(293, 59, 'C', 'karya', NULL),
(294, 59, 'D', 'studi', NULL),
(295, 59, 'E', 'mikroskop', NULL),
(296, 60, 'A', 'pemadam api', NULL),
(297, 60, 'B', 'obor', NULL),
(298, 60, 'C', 'letupan', NULL),
(299, 60, 'D', 'korek api', NULL),
(300, 60, 'E', 'lautan api', NULL),
(301, 77, 'A', '35', NULL),
(302, 77, 'B', '36', NULL),
(303, 77, 'C', '37', NULL),
(304, 77, 'D', '38', NULL),
(305, 77, 'E', '39', NULL),
(306, 78, 'A', '280', NULL),
(307, 78, 'B', '208', NULL),
(308, 78, 'C', '288', NULL),
(309, 78, 'D', '281', NULL),
(310, 78, 'E', '218', NULL),
(311, 79, 'A', '195', NULL),
(312, 79, 'B', '190', NULL),
(313, 79, 'C', '205', NULL),
(314, 79, 'D', '210', NULL),
(315, 79, 'E', '211', NULL),
(316, 80, 'A', '10', NULL),
(317, 80, 'B', '16', NULL),
(318, 80, 'C', '20', NULL),
(319, 80, 'D', '26', NULL),
(320, 80, 'E', '30', NULL),
(321, 81, 'A', '30', NULL),
(322, 81, 'B', '33', NULL),
(323, 81, 'C', '60', NULL),
(324, 81, 'D', '63', NULL),
(325, 81, 'E', '66', NULL),
(326, 82, 'A', '15', NULL),
(327, 82, 'B', '25', NULL),
(328, 82, 'C', '49', NULL),
(329, 82, 'D', '50', NULL),
(330, 82, 'E', '70', NULL),
(331, 83, 'A', '27', NULL),
(332, 83, 'B', '82', NULL),
(333, 83, 'C', '45', NULL),
(334, 83, 'D', '1', NULL),
(335, 83, 'E', '54', NULL),
(336, 84, 'A', '15', NULL),
(337, 84, 'B', '50', NULL),
(338, 84, 'C', '75', NULL),
(339, 84, 'D', '55', NULL),
(340, 84, 'E', '45', NULL),
(341, 85, 'A', '74', NULL),
(342, 85, 'B', '84', NULL),
(343, 85, 'C', '94', NULL),
(344, 85, 'D', '104', NULL),
(345, 85, 'E', '114', NULL),
(346, 86, 'A', '1', NULL),
(347, 86, 'B', '36', NULL),
(348, 86, 'C', '76', NULL),
(349, 86, 'D', '78', NULL),
(350, 86, 'E', '87', NULL),
(351, 87, 'A', '19', NULL),
(352, 87, 'B', '118', NULL),
(353, 87, 'C', '121', NULL),
(354, 87, 'D', '191', NULL),
(355, 87, 'E', '20', NULL),
(356, 88, 'A', '5', NULL),
(357, 88, 'B', '6', NULL),
(358, 88, 'C', '7', NULL),
(359, 88, 'D', '8', NULL),
(360, 88, 'E', '9', NULL),
(361, 89, 'A', '57', NULL),
(362, 89, 'B', '75', NULL),
(363, 89, 'C', '77', NULL),
(364, 89, 'D', '55', NULL),
(365, 89, 'E', '47', NULL),
(366, 90, 'A', '9', NULL),
(367, 90, 'B', '19', NULL),
(368, 90, 'C', '90', NULL),
(369, 90, 'D', '100', NULL),
(370, 90, 'E', '109', NULL),
(371, 91, 'A', '120', NULL),
(372, 91, 'B', '102', NULL),
(373, 91, 'C', '122', NULL),
(374, 91, 'D', '201', NULL),
(375, 91, 'E', '212', NULL),
(376, 92, 'A', '7', NULL),
(377, 92, 'B', '11', NULL),
(378, 92, 'C', '717', NULL),
(379, 92, 'D', '71', NULL),
(380, 92, 'E', '17', NULL),
(381, 93, 'A', '36', NULL),
(382, 93, 'B', '63', NULL),
(383, 93, 'C', '33', NULL),
(384, 93, 'D', '24', NULL),
(385, 93, 'E', '42', NULL),
(386, 94, 'A', '5', NULL),
(387, 94, 'B', '1', NULL),
(388, 94, 'C', '3', NULL),
(389, 94, 'D', '7', NULL),
(390, 94, 'E', '10', NULL),
(391, 95, 'A', '44', NULL),
(392, 95, 'B', '48', NULL),
(393, 95, 'C', '8', NULL),
(394, 95, 'D', '49', NULL),
(395, 95, 'E', '50', NULL),
(396, 96, 'A', '0', NULL),
(397, 96, 'B', '1', NULL),
(398, 96, 'C', '3', NULL),
(399, 96, 'D', '2', NULL),
(400, 96, 'E', '5', NULL),
(401, 97, 'A', '17', NULL),
(402, 97, 'B', '27', NULL),
(403, 97, 'C', '37', NULL),
(404, 97, 'D', '47', NULL),
(405, 97, 'E', '57', NULL),
(406, 98, 'A', '5', NULL),
(407, 98, 'B', '25', NULL),
(408, 98, 'C', '27', NULL),
(409, 98, 'D', '10', NULL),
(410, 98, 'E', '55', NULL),
(411, 99, 'A', '7', NULL),
(412, 99, 'B', '17', NULL),
(413, 99, 'C', '27', NULL),
(414, 99, 'D', '37', NULL),
(415, 99, 'E', '10', NULL),
(416, 100, 'A', '1', NULL),
(417, 100, 'B', '10', NULL),
(418, 100, 'C', '13', NULL),
(419, 100, 'D', '15', NULL),
(420, 100, 'E', '17', NULL),
(421, 101, 'A', '46', NULL),
(422, 101, 'B', '46', NULL),
(423, 101, 'C', '23', NULL),
(424, 101, 'D', '2', NULL),
(425, 101, 'E', '1', NULL),
(426, 102, 'A', '10', NULL),
(427, 102, 'B', '1', NULL),
(428, 102, 'C', '0', NULL),
(429, 102, 'D', '15', NULL),
(430, 102, 'E', '9', NULL),
(431, 103, 'A', '11', NULL),
(432, 103, 'B', '41', NULL),
(433, 103, 'C', '42', NULL),
(434, 103, 'D', '1', NULL),
(435, 103, 'E', '28', NULL),
(436, 104, 'A', '2', NULL),
(437, 104, 'B', '21', NULL),
(438, 104, 'C', '7', NULL),
(439, 104, 'D', '14', NULL),
(440, 104, 'E', '25', NULL),
(441, 105, 'A', '2', NULL),
(442, 105, 'B', '7', NULL),
(443, 105, 'C', '5', NULL),
(444, 105, 'D', '4', NULL),
(445, 105, 'E', '1', NULL),
(446, 106, 'A', '12', NULL),
(447, 106, 'B', '211', NULL),
(448, 106, 'C', '14', NULL),
(449, 106, 'D', '71', NULL),
(450, 106, 'E', '25', NULL),
(451, 107, 'A', '8', NULL),
(452, 107, 'B', '4', NULL),
(453, 107, 'C', '12', NULL),
(454, 107, 'D', '33', NULL),
(455, 107, 'E', '101', NULL),
(456, 108, 'A', '14', NULL),
(457, 108, 'B', '12', NULL),
(458, 108, 'C', '41', NULL),
(459, 108, 'D', '4', NULL),
(460, 108, 'E', '44', NULL),
(461, 109, 'A', '27.5', NULL),
(462, 109, 'B', '45', NULL),
(463, 109, 'C', '-3', NULL),
(464, 109, 'D', '-1', NULL),
(465, 109, 'E', '0', NULL),
(466, 110, 'A', '6.3', NULL),
(467, 110, 'B', '-3', NULL),
(468, 110, 'C', '-6', NULL),
(469, 110, 'D', '63', NULL),
(470, 110, 'E', '-63', NULL),
(471, 111, 'A', '-1', NULL),
(472, 111, 'B', '13', NULL),
(473, 111, 'C', '21', NULL),
(474, 111, 'D', '-11', NULL),
(475, 111, 'E', '12', NULL),
(476, 112, 'A', '17', NULL),
(477, 112, 'B', '20', NULL),
(478, 112, 'C', '18', NULL),
(479, 112, 'D', '91', NULL),
(480, 112, 'E', '80', NULL),
(481, 113, 'A', '12', NULL),
(482, 113, 'B', '34', NULL),
(483, 113, 'C', '7', NULL),
(484, 113, 'D', '15', NULL),
(485, 113, 'E', '14', NULL),
(486, 114, 'A', '22', NULL),
(487, 114, 'B', '12', NULL),
(488, 114, 'C', '17', NULL),
(489, 114, 'D', '24', NULL),
(490, 114, 'E', '25', NULL),
(491, 115, 'A', '63', NULL),
(492, 115, 'B', '-63', NULL),
(493, 115, 'C', '-3', NULL),
(494, 115, 'D', '3', NULL),
(495, 115, 'E', '36', NULL),
(496, 116, 'A', '17', NULL),
(497, 116, 'B', '77', NULL),
(498, 116, 'C', '80', NULL),
(499, 116, 'D', '45', NULL),
(500, 116, 'E', '10', NULL),
(501, 117, 'A', NULL, '(A)117-128.jpeg'),
(502, 118, 'A', NULL, '(A)117-128.jpeg'),
(503, 119, 'A', NULL, '(A)117-128.jpeg'),
(504, 120, 'A', NULL, '(A)117-128.jpeg'),
(505, 121, 'A', NULL, '(A)117-128.jpeg'),
(506, 122, 'A', NULL, '(A)117-128.jpeg'),
(507, 123, 'A', NULL, '(A)117-128.jpeg'),
(508, 124, 'A', NULL, '(A)117-128.jpeg'),
(509, 125, 'A', NULL, '(A)117-128.jpeg'),
(510, 126, 'A', NULL, '(A)117-128.jpeg'),
(511, 127, 'A', NULL, '(A)117-128.jpeg'),
(512, 128, 'A', NULL, '(A)117-128.jpeg'),
(516, 117, 'B', NULL, '(B)117-128.jpeg'),
(517, 118, 'B', NULL, '(B)117-128.jpeg'),
(518, 119, 'B', NULL, '(B)117-128.jpeg'),
(519, 120, 'B', NULL, '(B)117-128.jpeg'),
(520, 121, 'B', NULL, '(B)117-128.jpeg'),
(521, 122, 'B', NULL, '(B)117-128.jpeg'),
(522, 123, 'B', NULL, '(B)117-128.jpeg'),
(523, 124, 'B', NULL, '(B)117-128.jpeg'),
(524, 125, 'B', NULL, '(B)117-128.jpeg'),
(525, 126, 'B', NULL, '(B)117-128.jpeg'),
(526, 127, 'B', NULL, '(B)117-128.jpeg'),
(527, 128, 'B', NULL, '(B)117-128.jpeg'),
(531, 117, 'C', NULL, '(C)117-128.jpeg'),
(532, 118, 'C', NULL, '(C)117-128.jpeg'),
(533, 119, 'C', NULL, '(C)117-128.jpeg'),
(534, 120, 'C', NULL, '(C)117-128.jpeg'),
(535, 121, 'C', NULL, '(C)117-128.jpeg'),
(536, 122, 'C', NULL, '(C)117-128.jpeg'),
(537, 123, 'C', NULL, '(C)117-128.jpeg'),
(538, 124, 'C', NULL, '(C)117-128.jpeg'),
(539, 125, 'C', NULL, '(C)117-128.jpeg'),
(540, 126, 'C', NULL, '(C)117-128.jpeg'),
(541, 127, 'C', NULL, '(C)117-128.jpeg'),
(542, 128, 'C', NULL, '(C)117-128.jpeg'),
(546, 117, 'D', NULL, '(D)117-128.jpeg'),
(547, 118, 'D', NULL, '(D)117-128.jpeg'),
(548, 119, 'D', NULL, '(D)117-128.jpeg'),
(549, 120, 'D', NULL, '(D)117-128.jpeg'),
(550, 121, 'D', NULL, '(D)117-128.jpeg'),
(551, 122, 'D', NULL, '(D)117-128.jpeg'),
(552, 123, 'D', NULL, '(D)117-128.jpeg'),
(553, 124, 'D', NULL, '(D)117-128.jpeg'),
(554, 125, 'D', NULL, '(D)117-128.jpeg'),
(555, 126, 'D', NULL, '(D)117-128.jpeg'),
(556, 127, 'D', NULL, '(D)117-128.jpeg'),
(557, 128, 'D', NULL, '(D)117-128.jpeg'),
(561, 117, 'E', NULL, '(E)117-128.jpeg'),
(562, 118, 'E', NULL, '(E)117-128.jpeg'),
(563, 119, 'E', NULL, '(E)117-128.jpeg'),
(564, 120, 'E', NULL, '(E)117-128.jpeg'),
(565, 121, 'E', NULL, '(E)117-128.jpeg'),
(566, 122, 'E', NULL, '(E)117-128.jpeg'),
(567, 123, 'E', NULL, '(E)117-128.jpeg'),
(568, 124, 'E', NULL, '(E)117-128.jpeg'),
(569, 125, 'E', NULL, '(E)117-128.jpeg'),
(570, 126, 'E', NULL, '(E)117-128.jpeg'),
(571, 127, 'E', NULL, '(E)117-128.jpeg'),
(572, 128, 'E', NULL, '(E)117-128.jpeg'),
(576, 129, 'A', NULL, '(A)129-136.jpeg'),
(577, 130, 'A', NULL, '(A)129-136.jpeg'),
(578, 131, 'A', NULL, '(A)129-136.jpeg'),
(579, 132, 'A', NULL, '(A)129-136.jpeg'),
(580, 133, 'A', NULL, '(A)129-136.jpeg'),
(581, 134, 'A', NULL, '(A)129-136.jpeg'),
(582, 135, 'A', NULL, '(A)129-136.jpeg'),
(583, 136, 'A', NULL, '(A)129-136.jpeg'),
(591, 129, 'B', NULL, '(B)129-136.jpeg'),
(592, 130, 'B', NULL, '(B)129-136.jpeg'),
(593, 131, 'B', NULL, '(B)129-136.jpeg'),
(594, 132, 'B', NULL, '(B)129-136.jpeg'),
(595, 133, 'B', NULL, '(B)129-136.jpeg'),
(596, 134, 'B', NULL, '(B)129-136.jpeg'),
(597, 135, 'B', NULL, '(B)129-136.jpeg'),
(598, 136, 'B', NULL, '(B)129-136.jpeg'),
(606, 129, 'C', NULL, '(C)129-136.jpeg'),
(607, 130, 'C', NULL, '(C)129-136.jpeg'),
(608, 131, 'C', NULL, '(C)129-136.jpeg'),
(609, 132, 'C', NULL, '(C)129-136.jpeg'),
(610, 133, 'C', NULL, '(C)129-136.jpeg'),
(611, 134, 'C', NULL, '(C)129-136.jpeg'),
(612, 135, 'C', NULL, '(C)129-136.jpeg'),
(613, 136, 'C', NULL, '(C)129-136.jpeg'),
(621, 129, 'D', NULL, '(D)129-136.jpeg'),
(622, 130, 'D', NULL, '(D)129-136.jpeg'),
(623, 131, 'D', NULL, '(D)129-136.jpeg'),
(624, 132, 'D', NULL, '(D)129-136.jpeg'),
(625, 133, 'D', NULL, '(D)129-136.jpeg'),
(626, 134, 'D', NULL, '(D)129-136.jpeg'),
(627, 135, 'D', NULL, '(D)129-136.jpeg'),
(628, 136, 'D', NULL, '(D)129-136.jpeg'),
(636, 129, 'E', NULL, '(E)129-136.jpeg'),
(637, 130, 'E', NULL, '(E)129-136.jpeg'),
(638, 131, 'E', NULL, '(E)129-136.jpeg'),
(639, 132, 'E', NULL, '(E)129-136.jpeg'),
(640, 133, 'E', NULL, '(E)129-136.jpeg'),
(641, 134, 'E', NULL, '(E)129-136.jpeg'),
(642, 135, 'E', NULL, '(E)129-136.jpeg'),
(643, 136, 'E', NULL, '(E)129-136.jpeg'),
(651, 137, 'A', NULL, '(A)137-156.jpeg'),
(652, 138, 'A', NULL, '(A)137-156.jpeg'),
(653, 139, 'A', NULL, '(A)137-156.jpeg'),
(654, 140, 'A', NULL, '(A)137-156.jpeg'),
(655, 141, 'A', NULL, '(A)137-156.jpeg'),
(656, 142, 'A', NULL, '(A)137-156.jpeg'),
(657, 143, 'A', NULL, '(A)137-156.jpeg'),
(658, 144, 'A', NULL, '(A)137-156.jpeg'),
(659, 145, 'A', NULL, '(A)137-156.jpeg'),
(660, 146, 'A', NULL, '(A)137-156.jpeg'),
(661, 147, 'A', NULL, '(A)137-156.jpeg'),
(662, 148, 'A', NULL, '(A)137-156.jpeg'),
(663, 149, 'A', NULL, '(A)137-156.jpeg'),
(664, 150, 'A', NULL, '(A)137-156.jpeg'),
(665, 151, 'A', NULL, '(A)137-156.jpeg'),
(666, 152, 'A', NULL, '(A)137-156.jpeg'),
(667, 153, 'A', NULL, '(A)137-156.jpeg'),
(668, 154, 'A', NULL, '(A)137-156.jpeg'),
(669, 155, 'A', NULL, '(A)137-156.jpeg'),
(670, 156, 'A', NULL, '(A)137-156.jpeg'),
(682, 137, 'B', NULL, '(B)137-156.jpeg'),
(683, 138, 'B', NULL, '(B)137-156.jpeg'),
(684, 139, 'B', NULL, '(B)137-156.jpeg'),
(685, 140, 'B', NULL, '(B)137-156.jpeg'),
(686, 141, 'B', NULL, '(B)137-156.jpeg'),
(687, 142, 'B', NULL, '(B)137-156.jpeg'),
(688, 143, 'B', NULL, '(B)137-156.jpeg'),
(689, 144, 'B', NULL, '(B)137-156.jpeg'),
(690, 145, 'B', NULL, '(B)137-156.jpeg'),
(691, 146, 'B', NULL, '(B)137-156.jpeg'),
(692, 147, 'B', NULL, '(B)137-156.jpeg'),
(693, 148, 'B', NULL, '(B)137-156.jpeg'),
(694, 149, 'B', NULL, '(B)137-156.jpeg'),
(695, 150, 'B', NULL, '(B)137-156.jpeg'),
(696, 151, 'B', NULL, '(B)137-156.jpeg'),
(697, 152, 'B', NULL, '(B)137-156.jpeg'),
(698, 153, 'B', NULL, '(B)137-156.jpeg'),
(699, 154, 'B', NULL, '(B)137-156.jpeg'),
(700, 155, 'B', NULL, '(B)137-156.jpeg'),
(701, 156, 'B', NULL, '(B)137-156.jpeg'),
(713, 137, 'C', NULL, '(C)137-156.jpeg'),
(714, 138, 'C', NULL, '(C)137-156.jpeg'),
(715, 139, 'C', NULL, '(C)137-156.jpeg'),
(716, 140, 'C', NULL, '(C)137-156.jpeg'),
(717, 141, 'C', NULL, '(C)137-156.jpeg'),
(718, 142, 'C', NULL, '(C)137-156.jpeg'),
(719, 143, 'C', NULL, '(C)137-156.jpeg'),
(720, 144, 'C', NULL, '(C)137-156.jpeg'),
(721, 145, 'C', NULL, '(C)137-156.jpeg'),
(722, 146, 'C', NULL, '(C)137-156.jpeg'),
(723, 147, 'C', NULL, '(C)137-156.jpeg'),
(724, 148, 'C', NULL, '(C)137-156.jpeg'),
(725, 149, 'C', NULL, '(C)137-156.jpeg'),
(726, 150, 'C', NULL, '(C)137-156.jpeg'),
(727, 151, 'C', NULL, '(C)137-156.jpeg'),
(728, 152, 'C', NULL, '(C)137-156.jpeg'),
(729, 153, 'C', NULL, '(C)137-156.jpeg'),
(730, 154, 'C', NULL, '(C)137-156.jpeg'),
(731, 155, 'C', NULL, '(C)137-156.jpeg'),
(732, 156, 'C', NULL, '(C)137-156.jpeg'),
(744, 137, 'D', NULL, '(D)137-156.jpeg'),
(745, 138, 'D', NULL, '(D)137-156.jpeg'),
(746, 139, 'D', NULL, '(D)137-156.jpeg'),
(747, 140, 'D', NULL, '(D)137-156.jpeg'),
(748, 141, 'D', NULL, '(D)137-156.jpeg'),
(749, 142, 'D', NULL, '(D)137-156.jpeg'),
(750, 143, 'D', NULL, '(D)137-156.jpeg'),
(751, 144, 'D', NULL, '(D)137-156.jpeg'),
(752, 145, 'D', NULL, '(D)137-156.jpeg'),
(753, 146, 'D', NULL, '(D)137-156.jpeg'),
(754, 147, 'D', NULL, '(D)137-156.jpeg'),
(755, 148, 'D', NULL, '(D)137-156.jpeg'),
(756, 149, 'D', NULL, '(D)137-156.jpeg'),
(757, 150, 'D', NULL, '(D)137-156.jpeg'),
(758, 151, 'D', NULL, '(D)137-156.jpeg'),
(759, 152, 'D', NULL, '(D)137-156.jpeg'),
(760, 153, 'D', NULL, '(D)137-156.jpeg'),
(761, 154, 'D', NULL, '(D)137-156.jpeg'),
(762, 155, 'D', NULL, '(D)137-156.jpeg'),
(763, 156, 'D', NULL, '(D)137-156.jpeg'),
(775, 137, 'E', NULL, '(E)137-156.jpeg'),
(776, 138, 'E', NULL, '(E)137-156.jpeg'),
(777, 139, 'E', NULL, '(E)137-156.jpeg'),
(778, 140, 'E', NULL, '(E)137-156.jpeg'),
(779, 141, 'E', NULL, '(E)137-156.jpeg'),
(780, 142, 'E', NULL, '(E)137-156.jpeg'),
(781, 143, 'E', NULL, '(E)137-156.jpeg'),
(782, 144, 'E', NULL, '(E)137-156.jpeg'),
(783, 145, 'E', NULL, '(E)137-156.jpeg'),
(784, 146, 'E', NULL, '(E)137-156.jpeg'),
(785, 147, 'E', NULL, '(E)137-156.jpeg'),
(786, 148, 'E', NULL, '(E)137-156.jpeg'),
(787, 149, 'E', NULL, '(E)137-156.jpeg'),
(788, 150, 'E', NULL, '(E)137-156.jpeg'),
(789, 151, 'E', NULL, '(E)137-156.jpeg'),
(790, 152, 'E', NULL, '(E)137-156.jpeg'),
(791, 153, 'E', NULL, '(E)137-156.jpeg'),
(792, 154, 'E', NULL, '(E)137-156.jpeg'),
(793, 155, 'E', NULL, '(E)137-156.jpeg'),
(794, 156, 'E', NULL, '(E)137-156.jpeg'),
(806, 157, 'A', 'bunga', NULL),
(807, 158, 'A', 'bunga', NULL),
(808, 159, 'A', 'bunga', NULL),
(809, 160, 'A', 'bunga', NULL),
(810, 161, 'A', 'bunga', NULL),
(811, 162, 'A', 'bunga', NULL),
(812, 163, 'A', 'bunga', NULL),
(813, 164, 'A', 'bunga', NULL),
(814, 165, 'A', 'bunga', NULL),
(815, 166, 'A', 'bunga', NULL),
(816, 167, 'A', 'bunga', NULL),
(817, 168, 'A', 'bunga', NULL),
(818, 169, 'A', 'bunga', NULL),
(819, 170, 'A', 'bunga', NULL),
(820, 171, 'A', 'bunga', NULL),
(821, 172, 'A', 'bunga', NULL),
(822, 173, 'A', 'bunga', NULL),
(823, 174, 'A', 'bunga', NULL),
(824, 175, 'A', 'bunga', NULL),
(825, 176, 'A', 'bunga', NULL),
(837, 157, 'B', 'perkakas', NULL),
(838, 158, 'B', 'perkakas', NULL),
(839, 159, 'B', 'perkakas', NULL),
(840, 160, 'B', 'perkakas', NULL),
(841, 161, 'B', 'perkakas', NULL),
(842, 162, 'B', 'perkakas', NULL),
(843, 163, 'B', 'perkakas', NULL),
(844, 164, 'B', 'perkakas', NULL),
(845, 165, 'B', 'perkakas', NULL),
(846, 166, 'B', 'perkakas', NULL),
(847, 167, 'B', 'perkakas', NULL),
(848, 168, 'B', 'perkakas', NULL),
(849, 169, 'B', 'perkakas', NULL),
(850, 170, 'B', 'perkakas', NULL),
(851, 171, 'B', 'perkakas', NULL),
(852, 172, 'B', 'perkakas', NULL),
(853, 173, 'B', 'perkakas', NULL),
(854, 174, 'B', 'perkakas', NULL),
(855, 175, 'B', 'perkakas', NULL),
(856, 176, 'B', 'perkakas', NULL),
(868, 157, 'C', 'negara', NULL),
(869, 158, 'C', 'negara', NULL),
(870, 159, 'C', 'negara', NULL),
(871, 160, 'C', 'negara', NULL),
(872, 161, 'C', 'negara', NULL),
(873, 162, 'C', 'negara', NULL),
(874, 163, 'C', 'negara', NULL),
(875, 164, 'C', 'negara', NULL),
(876, 165, 'C', 'negara', NULL),
(877, 166, 'C', 'negara', NULL),
(878, 167, 'C', 'negara', NULL),
(879, 168, 'C', 'negara', NULL),
(880, 169, 'C', 'negara', NULL),
(881, 170, 'C', 'negara', NULL),
(882, 171, 'C', 'negara', NULL),
(883, 172, 'C', 'negara', NULL),
(884, 173, 'C', 'negara', NULL),
(885, 174, 'C', 'negara', NULL),
(886, 175, 'C', 'negara', NULL),
(887, 176, 'C', 'negara', NULL),
(899, 157, 'D', 'kesenian', NULL),
(900, 158, 'D', 'kesenian', NULL),
(901, 159, 'D', 'kesenian', NULL),
(902, 160, 'D', 'kesenian', NULL),
(903, 161, 'D', 'kesenian', NULL),
(904, 162, 'D', 'kesenian', NULL),
(905, 163, 'D', 'kesenian', NULL),
(906, 164, 'D', 'kesenian', NULL),
(907, 165, 'D', 'kesenian', NULL),
(908, 166, 'D', 'kesenian', NULL),
(909, 167, 'D', 'kesenian', NULL),
(910, 168, 'D', 'kesenian', NULL),
(911, 169, 'D', 'kesenian', NULL),
(912, 170, 'D', 'kesenian', NULL),
(913, 171, 'D', 'kesenian', NULL),
(914, 172, 'D', 'kesenian', NULL),
(915, 173, 'D', 'kesenian', NULL),
(916, 174, 'D', 'kesenian', NULL),
(917, 175, 'D', 'kesenian', NULL),
(918, 176, 'D', 'kesenian', NULL),
(930, 157, 'E', 'hewan', NULL),
(931, 158, 'E', 'hewan', NULL),
(932, 159, 'E', 'hewan', NULL),
(933, 160, 'E', 'hewan', NULL),
(934, 161, 'E', 'hewan', NULL),
(935, 162, 'E', 'hewan', NULL),
(936, 163, 'E', 'hewan', NULL),
(937, 164, 'E', 'hewan', NULL),
(938, 165, 'E', 'hewan', NULL),
(939, 166, 'E', 'hewan', NULL),
(940, 167, 'E', 'hewan', NULL),
(941, 168, 'E', 'hewan', NULL),
(942, 169, 'E', 'hewan', NULL),
(943, 170, 'E', 'hewan', NULL),
(944, 171, 'E', 'hewan', NULL),
(945, 172, 'E', 'hewan', NULL),
(946, 173, 'E', 'hewan', NULL),
(947, 174, 'E', 'hewan', NULL),
(948, 175, 'E', 'hewan', NULL),
(949, 176, 'E', 'hewan', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_questions`
--

CREATE TABLE `iq_questions` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `nomor_soal` int(11) NOT NULL,
  `pertanyaan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tipe_soal` enum('pilihan','isian','angka','gambar') NOT NULL,
  `jawaban_benar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_questions`
--

INSERT INTO `iq_questions` (`id`, `section_id`, `nomor_soal`, `pertanyaan`, `gambar`, `tipe_soal`, `jawaban_benar`) VALUES
(1, 1, 1, 'Pengaruh seseorang terhadap orang lain seharusnya bergantung pada ………', NULL, 'pilihan', 'E'),
(2, 1, 2, 'Lawannya \"hemat\" ialah ……… ', NULL, 'pilihan', 'C'),
(3, 1, 3, '……… Tidak termasuk cuaca.', NULL, 'pilihan', 'D'),
(4, 1, 4, 'Lawannya “setia” ialah ………', NULL, 'pilihan', 'D'),
(5, 1, 5, 'Seekor kuda selalu mempunyai ………', NULL, 'pilihan', 'D'),
(6, 1, 6, 'Seorang paman ………………… lebih tua dari kemenakannya', NULL, 'pilihan', 'B'),
(7, 1, 7, 'Pada jumlah yang sama, nilai kalori tertinggi terdapat pada ………………… ', NULL, 'pilihan', 'C'),
(8, 1, 8, 'Pada suatu pertandingan selalu terdapat ………………………', NULL, 'pilihan', 'A'),
(9, 1, 9, 'Suatu pernyataan yang belum dipastikan dikatakan sebagai pernyataan yang ………………', NULL, 'pilihan', 'E'),
(10, 1, 10, 'Pada sepatu selalu terdapat …………………', NULL, 'pilihan', 'B'),
(11, 1, 11, 'Suatu …………… tidak termasuk persoalan pencegahan kecelakaan', NULL, 'pilihan', 'C'),
(12, 1, 12, 'Mata uang dari Rp.50,- garis tengahnya adalah ………………………', NULL, 'pilihan', 'D'),
(13, 1, 13, 'Seseorang yang bersikap menyangsikan setiap kemajuan adalah seorang yang ………………………', NULL, 'pilihan', 'D'),
(14, 1, 14, 'Lawannya “tidak pernah” adalah …………………………', NULL, 'pilihan', 'E'),
(15, 1, 15, 'Jarak antara Jakarta – Surabaya ialah kira-kira ……………', NULL, 'pilihan', 'C'),
(16, 1, 16, 'Untuk dapat membuat nada yang rendah dan mendalam, kita memerlukan banyak………………', NULL, 'pilihan', 'A'),
(17, 1, 17, 'Ayah …………………… lebih pengalaman daripada anaknya', NULL, 'pilihan', 'B'),
(18, 1, 18, 'Diantara kota-kota berikut, maka kota ……………… letaknya paling selatan ', NULL, 'pilihan', 'B'),
(19, 1, 19, 'Menghitung kemungkinan menang jika tahu persentase nomor lotere tidak menang', NULL, 'pilihan', 'C'),
(20, 1, 20, 'Seorang anak yang baru berumur 10 tahun tingginya rata-rata ……………………', NULL, 'pilihan', 'B'),
(21, 2, 21, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'B'),
(22, 2, 22, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'B'),
(23, 2, 23, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'D'),
(24, 2, 24, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(25, 2, 25, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(26, 2, 26, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(27, 2, 27, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(28, 2, 28, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'D'),
(29, 2, 29, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'D'),
(30, 2, 30, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'A'),
(31, 2, 31, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'E'),
(32, 2, 32, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'A'),
(33, 2, 33, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'A'),
(34, 2, 34, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'B'),
(35, 2, 35, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(36, 2, 36, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'A'),
(37, 2, 37, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'D'),
(38, 2, 38, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'E'),
(39, 2, 39, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'B'),
(40, 2, 40, 'Carilah kata yang tidak memiliki kesamaan', NULL, 'pilihan', 'C'),
(41, 3, 41, 'menemukan : Menghilangkan = Mengingat : ?', NULL, 'pilihan', 'C'),
(42, 3, 42, 'bunga : jambangan = Burung : ?', NULL, 'pilihan', 'E'),
(43, 3, 43, 'kereta api : rel = otobis : ?', NULL, 'pilihan', 'D'),
(44, 3, 44, 'perak : emas = cincin : ?', NULL, 'pilihan', 'D'),
(45, 3, 45, 'lingkaran : bola = bujur sangkar : ?', NULL, 'pilihan', 'D'),
(46, 3, 46, 'saran : Keputusan = Merundingkan : ?', NULL, 'pilihan', 'B'),
(47, 3, 47, 'lidah : asam = hidung : ?', NULL, 'pilihan', 'D'),
(48, 3, 48, 'darah : pembuluh = air : ?', NULL, 'pilihan', 'B'),
(49, 3, 49, 'saraf : penyalur = pupil : ?', NULL, 'pilihan', 'E'),
(50, 3, 50, 'pengantar surat : pengantar Telegram = pandai besi : ?', NULL, 'pilihan', 'D'),
(51, 3, 51, 'buta : warna = Tuli : ?', NULL, 'pilihan', 'C'),
(52, 3, 52, 'makanan : bumbu = ceramah : ?', NULL, 'pilihan', 'C'),
(53, 3, 53, 'marah : emosi = duka cita : ?', NULL, 'pilihan', 'C'),
(54, 3, 54, 'mantel : jubbah = Wool : ?', NULL, 'pilihan', 'C'),
(55, 3, 55, 'ketinggian puncak : tekanan udara = ketinggian nada : ?', NULL, 'pilihan', 'D'),
(56, 3, 56, 'Negara : revolusi = Hidup : ?', NULL, 'pilihan', 'C'),
(57, 3, 57, 'kekurangan : penemuan = panas : ?', NULL, 'pilihan', 'C'),
(58, 3, 58, 'kayu : diketam = besi : ?', NULL, 'pilihan', 'E'),
(59, 3, 59, 'olahragawan : lembing = cendikiawan : ?', NULL, 'pilihan', 'E'),
(60, 3, 60, 'keledai : kuda pacuan = pembakaran : ?', NULL, 'pilihan', 'E'),
(61, 4, 61, 'Mawar – melati', NULL, 'isian', NULL),
(62, 4, 62, 'Mata – telinga', NULL, 'isian', NULL),
(63, 4, 63, 'Gula – intan', NULL, 'isian', NULL),
(64, 4, 64, 'Hujan – salju', NULL, 'isian', NULL),
(65, 4, 65, 'Pengantar surat – telepon', NULL, 'isian', NULL),
(66, 4, 66, 'Kamera – kacamata', NULL, 'isian', NULL),
(67, 4, 67, 'Lambung – usus', NULL, 'isian', NULL),
(68, 4, 68, 'Banyak – sedikit', NULL, 'isian', NULL),
(69, 4, 69, 'Telur – benih', NULL, 'isian', NULL),
(70, 4, 70, 'Bendera – lencana', NULL, 'isian', NULL),
(71, 4, 71, 'Rumput – gajah', NULL, 'isian', NULL),
(72, 4, 72, 'Ember – kantong', NULL, 'isian', NULL),
(73, 4, 73, 'Awal – akhir', NULL, 'isian', NULL),
(74, 4, 74, 'Kikir – boros', NULL, 'isian', NULL),
(75, 4, 75, 'Penawaran – permintaan', NULL, 'isian', NULL),
(76, 4, 76, 'Atas – bawah', NULL, 'isian', NULL),
(77, 5, 77, 'Jika seorang anak memiliki Rp. 50,- dan memberikan Rp. 15,-. Berapa rupiahkah yang masih tinggal padanya?', NULL, 'pilihan', 'A'),
(78, 5, 78, 'Berapa km-kah yang dapat ditempuh oleh kereta api dalam waktu 7 jam. Jika kecepatannya 40 km/jam ?', NULL, 'pilihan', 'A'),
(79, 5, 79, '15 peti buah-buahan beratnya 250 kg dan setiap peti kosong beratnya 3 kg, berapakah berat buah-buahan itu ?', NULL, 'pilihan', 'C'),
(80, 5, 80, 'Seseorang mempunyai persediaan rumput yang cukup untuk 7 ekor kuda selama 78 hari. Berapa harikah persediaan itu cukup untuk 21 ekor kuda?', NULL, 'pilihan', 'D'),
(81, 5, 81, '3 batang coklat harganya Rp. 5,-. Berapa batangkah yang dapat kita beli dengan Rp. 50,-?', NULL, 'pilihan', 'A'),
(82, 5, 82, 'Seseorang dapat berjalan 1,75 m dalam waktu ¼ detik. Berapa meterkah yang dapat ditempuh dalam waktu 10 detik ?', NULL, 'pilihan', 'E'),
(83, 5, 83, 'Jika sebuah batu terletak 15 m di sebelah selatan dari sebatang pohon dan pohon itu berada 30 m di sebelah selatan dari sebuah rumah. Berapa meterkah jarak antara batu dan rumah itu?', NULL, 'pilihan', 'C'),
(84, 5, 84, 'Jika 4 1/5 bahan sandang harganya Rp. 90,-. Berapa rupiahkah harga 2 ½ m?', NULL, 'pilihan', 'B'),
(85, 5, 85, '7 orang dapat menyelesaikan suatu pekerjaan dalam 6 hari. Berapa orangkah yang diperlukan untuk menyelesaikan pekerjaan itu dalam setengah hari ?', NULL, 'pilihan', 'B'),
(86, 5, 86, 'Karena dipanaskan, kawat yang panjangnya 48 cm akan mengembang menjadi 52 cm. Setelah pemanasan, berapakah panjangnya kawat yang berukuran 72 cm ?', NULL, 'pilihan', 'D'),
(87, 5, 87, 'Suatu pabrik dapat menghasilkan 304 batang pensil dalam waktu 8 jam. Berapa batangkah yang dihasilkan dalam waktu setengah jam ?', NULL, 'pilihan', 'A'),
(88, 5, 88, 'Suatu pabrik dapat menghasilkan 304 batang pensil dalam waktu 8 jam. Berapa batangkah yang dihasilkan dalam waktu setengah jam ?', NULL, 'pilihan', 'B'),
(89, 5, 89, 'Untuk setiap Rp. 3,- yang dimiliki Sidin, Hamid memiliki Rp. 5,- . Jika mereka bersama mempunyai Rp. 120,- berapa rupiahkah yang dimiliki Hamid ?', NULL, 'pilihan', 'B'),
(90, 5, 90, 'Mesin A menenun 60 m kain, sedangkan mesin B menenun 40 m kain. Berapa meterkah yang ditenun mesin A, Jika mesin B menenun 60 m?', NULL, 'pilihan', 'C'),
(91, 5, 91, 'Seseorang memberikan 1/10 dari uangnya untuk perangko dan 4 kali jumlah itu untuk alat tulis. Sisa uangnya masih Rp. 60,-. Berapa rupiahkah uangnya semula?', NULL, 'pilihan', 'A'),
(92, 5, 92, 'Dalam 2 peti terdapat 43 piring. Didalam peti yang satu terdapat 9 buah piring lebih banyak daripada didalam peti yang lain. Berapa buah piring terdapat didalam peti yang lebih kecil?', NULL, 'pilihan', 'E'),
(93, 5, 93, 'Suatu lembaran kain yang panjangnya 60 cm harus dibagi sedemikian rupa sehingga panjangnya satu bagian adalah 2/3 dari bagian yang lain. Berapakah bagian yang terpendek?', NULL, 'pilihan', 'D'),
(94, 5, 94, 'Suatu perusahaan mengekspor ¾ dari hasil produksinya dan menjual 4/5 dari sisa itu di dalam negeri. Berapa % kah hasil produksi yang masih tinggal?\r\n', NULL, 'pilihan', 'A'),
(95, 5, 95, 'Jika suatu botol berisi anggur hanya 7/8 bagian dan harganya ialah Rp. 84,-. Berapakah harga anggur itu jika botol itu hanya terisi ½ penuh?', NULL, 'pilihan', 'B'),
(96, 5, 96, 'Didalam suatu keluarga setiap anak perempuan mempunyai jumlah saudara laki-laki yang sama dengan jumlah saudara perempuan dan setiap anak laki-laki mempunyai dua kali lebih banyaksaudara perempuan daripada saudara laki-laki. Berapa anak laki-laki kah yang terdapat dalam keluarga itu?', NULL, 'pilihan', 'B'),
(97, 6, 97, '6	9	12	15	18	21	24 ?', NULL, 'pilihan', 'B'),
(98, 6, 98, '15	16	18	19	21	22	24 ?', NULL, 'pilihan', 'B'),
(99, 6, 99, '19	18	22	21	25	24	28 ?', NULL, 'pilihan', 'C'),
(100, 6, 100, '15	12	17	13	18	14	19 ?', NULL, 'pilihan', 'D'),
(101, 6, 101, '2	4	8	10	20	22	44 ?', NULL, 'pilihan', 'A'),
(102, 6, 102, '15	13	16	12	17	11	18 ?', NULL, 'pilihan', 'A'),
(103, 6, 103, '25	22	11	33	30	15	45 ?', NULL, 'pilihan', 'C'),
(104, 6, 104, '49	51	54	27	9	11	14 ?', NULL, 'pilihan', 'C'),
(105, 6, 105, '2	3	1	3	4	2	4 ?', NULL, 'pilihan', 'C'),
(106, 6, 106, '19	17	20	16	21	15	22 ?', NULL, 'pilihan', 'C'),
(107, 6, 107, '94	92	46	44	22	20	10 ?', NULL, 'pilihan', 'A'),
(108, 6, 108, '5	8	9	8	11	12 	11 ?', NULL, 'pilihan', 'A'),
(109, 6, 109, '12 	15	 19	 23	 28	 33	 39 ?', NULL, 'pilihan', 'B'),
(110, 6, 110, '7 	5	10	 7 	21	 17	 68 ?', NULL, 'pilihan', 'D'),
(111, 6, 111, '11	 15 	18	 9 	13 	16	 8 ?', NULL, 'pilihan', 'E'),
(112, 6, 112, '3 	8 	15	 24	 35	 48 	63 ?', NULL, 'pilihan', 'E'),
(113, 6, 113, '4	 5 	7 	4 	8 	13	 7 ?', NULL, 'pilihan', 'E'),
(114, 6, 114, '8 	5 	15	 18	 6	 3	 9 ?', NULL, 'pilihan', 'B'),
(115, 6, 115, '15	 6	 18	 10 	30	 23	 69 ?', NULL, 'pilihan', 'A'),
(116, 6, 116, '5	 35 	28	 4 	11	 77	 70 ?', NULL, 'pilihan', 'E'),
(117, 7, 117, NULL, '117.jpeg', 'gambar', 'D'),
(118, 7, 118, NULL, '118.jpeg', 'gambar', 'E'),
(119, 7, 119, NULL, '119.jpeg', 'gambar', 'B'),
(120, 7, 120, NULL, '120.jpeg', 'gambar', 'A'),
(121, 7, 121, NULL, '121.jpeg', 'gambar', 'C'),
(122, 7, 122, NULL, '122.jpeg', 'gambar', 'A'),
(123, 7, 123, NULL, '123.jpeg', 'gambar', 'D'),
(124, 7, 124, NULL, '124.jpeg', 'gambar', 'E'),
(125, 7, 125, NULL, '125.jpeg', 'gambar', 'C'),
(126, 7, 126, NULL, '126.jpeg', 'gambar', 'B'),
(127, 7, 127, NULL, '127.jpeg', 'gambar', 'B'),
(128, 7, 128, NULL, '128.jpeg', 'gambar', 'A'),
(129, 7, 129, NULL, '129.jpeg', 'gambar', 'E'),
(130, 7, 130, NULL, '130.jpeg', 'gambar', 'C'),
(131, 7, 131, NULL, '131.jpeg', 'gambar', 'D'),
(132, 7, 132, NULL, '132.jpeg', 'gambar', 'B'),
(133, 7, 133, NULL, '133.jpeg', 'gambar', 'E'),
(134, 7, 134, NULL, '134.jpeg', 'gambar', 'A'),
(135, 7, 135, NULL, '135.jpeg', 'gambar', 'C'),
(136, 7, 136, NULL, '136.jpeg', 'gambar', 'D'),
(137, 8, 137, NULL, '137.jpeg', 'gambar', 'A'),
(138, 8, 138, NULL, '138.jpeg', 'gambar', 'C'),
(139, 8, 139, NULL, '139.jpeg', 'gambar', 'D'),
(140, 8, 140, NULL, '140.jpeg', 'gambar', 'E'),
(141, 8, 141, NULL, '141.jpeg', 'gambar', 'A'),
(142, 8, 142, NULL, '142.jpeg', 'gambar', 'C'),
(143, 8, 143, NULL, '143.jpeg', 'gambar', 'D'),
(144, 8, 144, NULL, '144.jpeg', 'gambar', 'C'),
(145, 8, 145, NULL, '145.jpeg', 'gambar', 'E'),
(146, 8, 146, NULL, '146.jpeg', 'gambar', 'A'),
(147, 8, 147, NULL, '147.jpeg', 'gambar', 'B'),
(148, 8, 148, NULL, '148.jpeg', 'gambar', 'D'),
(149, 8, 149, NULL, '149.jpeg', 'gambar', 'E'),
(150, 8, 150, NULL, '150.jpeg', 'gambar', 'B'),
(151, 8, 151, NULL, '151.jpeg', 'gambar', 'D'),
(152, 8, 152, NULL, '152.jpeg', 'gambar', 'B'),
(153, 8, 153, NULL, '153.jpeg', 'gambar', 'A'),
(154, 8, 154, NULL, '154.jpeg', 'gambar', 'E'),
(155, 8, 155, NULL, '155.jpeg', 'gambar', 'B'),
(156, 8, 156, NULL, '156.jpeg', 'gambar', 'C'),
(157, 9, 157, 'Kata yang mempunyai huruf permulaan A', NULL, 'pilihan', 'D'),
(158, 9, 158, 'Kata yang mempunyai huruf permulaan B', NULL, 'pilihan', 'E'),
(159, 9, 159, 'Kata yang mempunyai huruf permulaan C', NULL, 'pilihan', 'B'),
(160, 9, 160, 'Kata yang mempunyai huruf permulaan D', NULL, 'pilihan', 'A'),
(161, 9, 161, 'Kata yang mempunyai huruf permulaan E', NULL, 'pilihan', 'C'),
(162, 9, 162, 'Kata yang mempunyai huruf permulaan F', NULL, 'pilihan', 'A'),
(163, 9, 163, 'Kata yang mempunyai huruf permulaan G', NULL, 'pilihan', 'D'),
(164, 9, 164, 'Kata yang mempunyai huruf permulaan H', NULL, 'pilihan', 'E'),
(165, 9, 165, 'Kata yang mempunyai huruf permulaan I', NULL, 'pilihan', 'C'),
(166, 9, 166, 'Kata yang mempunyai huruf permulaan J', NULL, 'pilihan', 'B'),
(167, 9, 167, 'Kata yang mempunyai huruf permulaan K', NULL, 'pilihan', 'B'),
(168, 9, 168, 'Kata yang mempunyai huruf permulaan L', NULL, 'pilihan', 'A'),
(169, 9, 169, 'Kata yang mempunyai huruf permulaan M', NULL, 'pilihan', 'E'),
(170, 9, 170, 'Kata yang mempunyai huruf permulaan N', NULL, 'pilihan', 'C'),
(171, 9, 171, 'Kata yang mempunyai huruf permulaan O', NULL, 'pilihan', 'D'),
(172, 9, 172, 'Kata yang mempunyai huruf permulaan P', NULL, 'pilihan', 'B'),
(173, 9, 173, 'Kata yang mempunyai huruf permulaan R', NULL, 'pilihan', 'E'),
(174, 9, 174, 'Kata yang mempunyai huruf permulaan S', NULL, 'pilihan', 'A'),
(175, 9, 175, 'Kata yang mempunyai huruf permulaan T', NULL, 'pilihan', 'C'),
(176, 9, 176, 'Kata yang mempunyai huruf permulaan U', NULL, 'pilihan', 'D');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_results`
--

CREATE TABLE `iq_results` (
  `id` int(11) NOT NULL,
  `user_id` varchar(25) NOT NULL,
  `skor` int(11) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_results`
--

INSERT INTO `iq_results` (`id`, `user_id`, `skor`, `tanggal`) VALUES
(1, '230211060055', 0, '2026-03-16 02:57:08'),
(2, '230211060035', 30, '2026-03-16 03:00:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_sections`
--

CREATE TABLE `iq_sections` (
  `id` int(11) NOT NULL,
  `nama_bagian` varchar(50) NOT NULL,
  `jumlah_soal` int(11) NOT NULL,
  `waktu_detik` int(11) NOT NULL,
  `waktu_hafalan` int(11) DEFAULT NULL,
  `instruksi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL,
  `gambar_instruksi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_sections`
--

INSERT INTO `iq_sections` (`id`, `nama_bagian`, `jumlah_soal`, `waktu_detik`, `waktu_hafalan`, `instruksi`, `urutan`, `gambar_instruksi`) VALUES
(1, 'Bagian 1', 20, 360, NULL, '\r\nPada setiap kalimat satu kata hilang dan disediakan 5(lima) kata pilihan sebagai jawabannya. Pilihlah kata yang tepat yang dapat menyempurnakan kalimat itu!\r\nContoh:\r\nSeekor kuda mempunyai kesamaan terbanyak dengan seekor…………………\r\nA. kucing B. bajing C. keledai D. lembu E. anjing\r\nJawaban yang benar adalah : c) keledai\r\n', 1, NULL),
(2, 'Bagian 2', 20, 360, NULL, '\r\nDitentukan lima kata. Pada 4 dari 5 kata itu terdapat suatu kesamaan. Carilah satu kata yang tidak memiliki kesamaan dengan keempat kata yang lain.\r\nContoh:\r\nA. meja B. kursi C. burung D. lemari E. tempat tidur\r\nMeja, kursi, lemari, dan tempat tidur adalah perabot rumah, sedangkan “burung” bukanlah perabot rumah yang tidak memiliki kesamaan dengan keempat kata yang lain. Jawaban yang benar adalah : burung. Oleh karena itu pada kertas jawaban, pilih jawaban C. burung\r\n', 2, NULL),
(3, 'Bagian 3', 20, 420, NULL, '\r\nDitentukan tiga kata. Antara kata pertama dan kata kedua terdapat suatu hubungan tertentu. Antara kata ketiga dan salah satu kata di antara kelima kata pilihan, harus pula terdapat hubungan yang sama. Carilah kata itu.\r\nContoh:\r\nHUTAN : POHON = TEMBOK : …\r\nA. batu bata B. rumah C. semen D. putih E. dinding\r\nHubungan antara hutan dan pohon adalah bahwa hutan terdiri atas pohon-pohon, maka hubungan\r\nantara tembok dan salah satu kata pilihan adalah bahwa tembok terdiri atas batu bata. Jawaban yang benar adalah : batu bata . Oleh karena itu pada kertas jawaban, pilih jawaban A. batu bata.\r\n', 3, NULL),
(4, 'Bagian 4', 16, 480, NULL, '\r\nDitentukan dua kata. Carilah satu perkataan yang meliputi pengertian kedua kata tadi. Tulislah perkataan itu pada kotak yang telah disediakan.\r\nDitentukan dua kata. Carilah satu perkataan yang meliputi pengertian kedua kata tadi. Tulislah perkataan itu pada kotak yang telah disediakan.\r\nContoh:\r\nAyam – itik\r\nKata “unggas” dapat meliputi pengertian kedua kata tersebut. Oleh karena itu pada isian yang telah disediakan tulis kata “unggas”.\r\n', 4, NULL),
(5, 'Bagian 5', 20, 600, NULL, '\r\nTest berikutnya adalah soal-soal hitungan.\r\nContoh:\r\nSebatang pensil harganya 25 rupiah. Berapakah harga 3 batang?\r\nJawabannya adalah : 75\r\nCara menjawabnya adalah dengan memilih angka jawaban.\r\nA. 75 B. 96 C. 87 D. 68 E. 79\r\n', 5, NULL),
(6, 'Bagian 6', 20, 600, NULL, '\r\nPada test berikut akan diberikan deret angka. Setiap deret tersusun menurut suatu pola tertentu dan dapat dilanjutkan menurut pola tersebut. Carilah angka berikutnya untuk setiap deret, dan tulis jawaban saudara pada kotak yang telah disediakan.\r\nContoh:\r\n2   4   6    8    10    12   14   ?\r\nPada deret ini angka berikutnya selalu didapat jika angka didepannya ditambah dengan 2.\r\nA. 15 B. 16 C. 27 D. 18 E. 19\r\nJawabannya adalah : 16\r\n', 6, NULL),
(7, 'Bagian 7', 20, 420, NULL, '\r\nPada test berikutnya, setiap soal memperlihatkan sesuatu bentuk tertentu yang terpotong menjadi beberapa bagian.\r\nCarilah diantara bentuk-bentuk yang terdapat dalam pilihan (a, b, c, d, e), suatu bentuk yang dapat dibangun dengan cara menyusun potongan-potongan yang terdapat dalam soal.\r\nJika potongan-potongan pada contoh 1 (kotak pertama) diatas setelah disusun (digabungkan), maka akan menghasilkan bentuk (A)\r\n', 7, 'images/img_section/contoh 7.jpeg'),
(8, 'Bagian 8', 20, 540, NULL, '\r\nTerdapat sebuah kubus dengan tanda yang terlihat pada ketiga sisi nya. Kubus tersebut dapat diputar, dapat digulingkan atau dapat diputar dan digulingkan dalam pikiran saudara. Carilah 1 (satu) dari 5 (lima) pilihan kubus yang memiliki tanda yang sama dengan kubus yang terdapat pada soal.\r\nContoh 1 (Kotak pertama) ini memperlihatkan kubus A dengan kedudukan yang berbeda. Mendapatkannya adalah dengancara menggulingkan lebih dahulu kubus itu ke kiri satu kali kemudian diputar ke kiri satu kali, sehingga sisi kubus yang bertanda dua segi empat hitam terletak di depan seperti kubus A.\r\n', 8, 'images/img_section/contoh 8.jpeg'),
(9, 'Bagian 9', 20, 360, 120, '\nHafalkan kata-kata berikut ini dalam waktu 2 menit. Setelah 2 menit, akan langsung berpindah ke halaman tes.\nContoh:\nKESENIAN :Gamelan, Opera, Arca, Quintet, Ukiran\nHEWAN	 :Musang, Rusa, Beruang, Zebra, Harimau\nKata yang mempunyai huruf permulaan Q adalah suatu……………\nA. bunga B. perkakas C. negara D. kesenian E. hewan\nQuintet adalah termasuk dalam jenis Kesenian, sehingga jawaban yang benar adalah Kesenian. Oleh karena itu pilih jawaban E. kesenian.\n', 9, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_test_sessions`
--

CREATE TABLE `iq_test_sessions` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `question` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `status` enum('running','finished') DEFAULT 'running'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_test_sessions`
--

INSERT INTO `iq_test_sessions` (`id`, `nip`, `section`, `question`, `start_time`, `status`) VALUES
(1, '230211060035', 1, 1, '2026-03-16 01:19:12', 'finished'),
(3, '12345678910', 1, 1, '2026-03-17 12:36:15', 'running'),
(7, '10386446990001', 1, 1, '2026-03-31 08:39:57', 'running');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_user_answers`
--

CREATE TABLE `iq_user_answers` (
  `id` int(11) NOT NULL,
  `user_nip` varchar(30) NOT NULL,
  `question_id` int(11) NOT NULL,
  `jawaban_user` varchar(100) DEFAULT NULL,
  `waktu_jawab` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iq_user_answers`
--

INSERT INTO `iq_user_answers` (`id`, `user_nip`, `question_id`, `jawaban_user`, `waktu_jawab`) VALUES
(1, '230211060035', 1, 'A', '2026-03-16 00:49:44'),
(6, '230211060035', 2, 'A', '2026-03-16 00:51:10'),
(7, '230211060035', 3, 'A', '2026-03-16 00:51:11'),
(8, '230211060035', 4, 'A', '2026-03-16 00:51:11'),
(9, '230211060035', 5, 'A', '2026-03-16 00:51:11'),
(10, '230211060035', 6, 'A', '2026-03-16 00:51:11'),
(11, '230211060035', 7, 'A', '2026-03-16 00:51:11'),
(12, '230211060035', 8, 'A', '2026-03-16 00:51:11'),
(13, '230211060035', 9, 'A', '2026-03-16 00:51:12'),
(14, '230211060035', 10, 'A', '2026-03-16 00:51:13'),
(15, '230211060035', 11, 'A', '2026-03-16 00:51:13'),
(16, '230211060035', 12, 'A', '2026-03-16 00:51:14'),
(17, '230211060035', 13, 'A', '2026-03-16 00:51:14'),
(18, '230211060035', 14, 'A', '2026-03-16 00:51:15'),
(19, '230211060035', 15, 'A', '2026-03-16 00:51:15'),
(20, '230211060035', 16, 'A', '2026-03-16 00:51:15'),
(21, '230211060035', 17, 'A', '2026-03-16 00:51:16'),
(22, '230211060035', 18, 'A', '2026-03-16 00:51:16'),
(23, '230211060035', 19, 'A', '2026-03-16 00:51:17'),
(24, '230211060035', 20, 'A', '2026-03-16 00:51:17'),
(26, '230211060035', 21, 'A', '2026-03-16 00:51:28'),
(27, '230211060035', 22, 'A', '2026-03-16 00:51:28'),
(28, '230211060035', 23, 'A', '2026-03-16 00:51:29'),
(29, '230211060035', 24, 'A', '2026-03-16 00:51:29'),
(30, '230211060035', 25, 'A', '2026-03-16 00:51:29'),
(31, '230211060035', 26, 'A', '2026-03-16 00:51:29'),
(32, '230211060035', 27, 'A', '2026-03-16 00:51:29'),
(33, '230211060035', 28, 'A', '2026-03-16 00:51:30'),
(34, '230211060035', 29, 'A', '2026-03-16 00:51:30'),
(35, '230211060035', 30, 'A', '2026-03-16 00:51:30'),
(36, '230211060035', 31, 'A', '2026-03-16 00:51:30'),
(37, '230211060035', 32, 'A', '2026-03-16 00:51:30'),
(38, '230211060035', 33, 'A', '2026-03-16 00:51:30'),
(39, '230211060035', 34, 'A', '2026-03-16 00:51:31'),
(40, '230211060035', 35, 'A', '2026-03-16 00:51:31'),
(41, '230211060035', 36, 'A', '2026-03-16 00:51:31'),
(42, '230211060035', 37, 'A', '2026-03-16 00:51:31'),
(43, '230211060035', 38, 'A', '2026-03-16 00:51:31'),
(44, '230211060035', 39, 'A', '2026-03-16 00:51:32'),
(45, '230211060035', 40, 'A', '2026-03-16 00:51:32'),
(46, '230211060035', 41, 'A', '2026-03-16 00:51:36'),
(47, '230211060035', 42, 'A', '2026-03-16 00:51:36'),
(48, '230211060035', 43, 'A', '2026-03-16 00:51:36'),
(49, '230211060035', 44, 'A', '2026-03-16 00:51:37'),
(50, '230211060035', 45, 'A', '2026-03-16 00:51:37'),
(51, '230211060035', 46, 'A', '2026-03-16 00:51:37'),
(52, '230211060035', 47, 'A', '2026-03-16 00:51:37'),
(53, '230211060035', 48, 'A', '2026-03-16 00:51:37'),
(54, '230211060035', 49, 'A', '2026-03-16 00:51:38'),
(55, '230211060035', 50, 'A', '2026-03-16 00:51:38'),
(56, '230211060035', 51, 'A', '2026-03-16 00:51:38'),
(57, '230211060035', 52, 'A', '2026-03-16 00:51:38'),
(58, '230211060035', 53, 'A', '2026-03-16 00:51:38'),
(59, '230211060035', 54, 'A', '2026-03-16 00:51:39'),
(60, '230211060035', 55, 'A', '2026-03-16 00:51:39'),
(61, '230211060035', 56, 'A', '2026-03-16 00:51:40'),
(62, '230211060035', 57, 'A', '2026-03-16 00:51:40'),
(63, '230211060035', 58, 'A', '2026-03-16 00:51:41'),
(64, '230211060035', 59, 'A', '2026-03-16 00:51:42'),
(65, '230211060035', 60, 'A', '2026-03-16 00:51:42'),
(66, '230211060035', 61, 'tes', '2026-03-16 00:51:49'),
(67, '230211060035', 62, 'tes', '2026-03-16 00:51:51'),
(68, '230211060035', 63, 'tes', '2026-03-16 00:51:56'),
(69, '230211060035', 64, 'tes', '2026-03-16 00:51:57'),
(70, '230211060035', 65, 'tes', '2026-03-16 00:51:59'),
(71, '230211060035', 66, 'tes', '2026-03-16 00:52:01'),
(72, '230211060035', 67, 'tes', '2026-03-16 00:52:02'),
(73, '230211060035', 68, 'tes', '2026-03-16 00:52:04'),
(74, '230211060035', 69, 'tes', '2026-03-16 00:52:05'),
(75, '230211060035', 70, 'tes', '2026-03-16 00:52:07'),
(76, '230211060035', 71, 'tes', '2026-03-16 00:52:10'),
(77, '230211060035', 72, 'tes', '2026-03-16 00:52:12'),
(78, '230211060035', 73, 'tes', '2026-03-16 00:52:17'),
(79, '230211060035', 74, 'tes', '2026-03-16 00:52:19'),
(80, '230211060035', 75, 'tes', '2026-03-16 00:52:22'),
(81, '230211060035', 76, 'tes', '2026-03-16 00:52:24'),
(82, '230211060035', 77, 'A', '2026-03-16 00:52:29'),
(83, '230211060035', 78, 'A', '2026-03-16 00:52:30'),
(84, '230211060035', 79, 'A', '2026-03-16 00:52:30'),
(85, '230211060035', 80, 'A', '2026-03-16 00:52:30'),
(86, '230211060035', 81, 'A', '2026-03-16 00:52:30'),
(87, '230211060035', 82, 'A', '2026-03-16 00:52:30'),
(88, '230211060035', 83, 'A', '2026-03-16 00:52:31'),
(89, '230211060035', 84, 'B', '2026-03-16 00:52:32'),
(90, '230211060035', 85, 'A', '2026-03-16 00:52:32'),
(91, '230211060035', 86, 'A', '2026-03-16 00:52:33'),
(92, '230211060035', 87, 'A', '2026-03-16 00:52:33'),
(93, '230211060035', 88, 'A', '2026-03-16 00:52:33'),
(94, '230211060035', 89, 'A', '2026-03-16 00:52:34'),
(95, '230211060035', 90, 'A', '2026-03-16 00:52:34'),
(96, '230211060035', 91, 'A', '2026-03-16 00:52:34'),
(97, '230211060035', 92, 'A', '2026-03-16 00:52:35'),
(98, '230211060035', 93, 'A', '2026-03-16 00:52:35'),
(99, '230211060035', 94, 'A', '2026-03-16 00:52:36'),
(100, '230211060035', 95, 'A', '2026-03-16 00:52:36'),
(101, '230211060035', 96, 'A', '2026-03-16 00:52:37'),
(102, '230211060035', 97, 'A', '2026-03-16 00:52:43'),
(103, '230211060035', 98, 'A', '2026-03-16 00:52:43'),
(104, '230211060035', 99, 'A', '2026-03-16 00:52:43'),
(105, '230211060035', 100, 'A', '2026-03-16 00:52:43'),
(106, '230211060035', 101, 'A', '2026-03-16 00:52:43'),
(107, '230211060035', 102, 'A', '2026-03-16 00:52:44'),
(108, '230211060035', 103, 'A', '2026-03-16 00:52:44'),
(109, '230211060035', 104, 'A', '2026-03-16 00:52:44'),
(110, '230211060035', 105, 'A', '2026-03-16 00:52:44'),
(111, '230211060035', 106, 'A', '2026-03-16 00:52:45'),
(112, '230211060035', 107, 'A', '2026-03-16 00:52:45'),
(113, '230211060035', 108, 'A', '2026-03-16 00:52:45'),
(114, '230211060035', 109, 'A', '2026-03-16 00:52:45'),
(115, '230211060035', 110, 'A', '2026-03-16 00:52:45'),
(116, '230211060035', 111, 'A', '2026-03-16 00:52:46'),
(117, '230211060035', 112, 'A', '2026-03-16 00:52:46'),
(118, '230211060035', 113, 'A', '2026-03-16 00:52:46'),
(119, '230211060035', 114, 'A', '2026-03-16 00:52:46'),
(120, '230211060035', 115, 'A', '2026-03-16 00:52:47'),
(121, '230211060035', 116, 'A', '2026-03-16 00:52:47'),
(122, '230211060035', 117, 'A', '2026-03-16 00:52:53'),
(123, '230211060035', 118, 'A', '2026-03-16 00:52:53'),
(124, '230211060035', 119, 'A', '2026-03-16 00:52:53'),
(125, '230211060035', 120, 'A', '2026-03-16 00:52:54'),
(126, '230211060035', 121, 'A', '2026-03-16 00:52:54'),
(127, '230211060035', 122, 'A', '2026-03-16 00:52:54'),
(128, '230211060035', 123, 'A', '2026-03-16 00:52:54'),
(129, '230211060035', 124, 'A', '2026-03-16 00:52:54'),
(130, '230211060035', 125, 'A', '2026-03-16 00:52:55'),
(131, '230211060035', 126, 'A', '2026-03-16 00:52:55'),
(132, '230211060035', 127, 'A', '2026-03-16 00:52:55'),
(133, '230211060035', 128, 'A', '2026-03-16 00:52:55'),
(134, '230211060035', 129, 'A', '2026-03-16 00:52:55'),
(135, '230211060035', 130, 'A', '2026-03-16 00:52:56'),
(136, '230211060035', 131, 'A', '2026-03-16 00:52:56'),
(137, '230211060035', 132, 'A', '2026-03-16 00:52:56'),
(138, '230211060035', 133, 'A', '2026-03-16 00:52:56'),
(139, '230211060035', 134, 'A', '2026-03-16 00:52:56'),
(140, '230211060035', 135, 'A', '2026-03-16 00:52:57'),
(141, '230211060035', 136, 'A', '2026-03-16 00:52:57'),
(142, '230211060035', 137, 'A', '2026-03-16 00:53:03'),
(143, '230211060035', 138, 'A', '2026-03-16 00:53:03'),
(144, '230211060035', 139, 'A', '2026-03-16 00:53:03'),
(145, '230211060035', 140, 'A', '2026-03-16 00:53:03'),
(146, '230211060035', 141, 'A', '2026-03-16 00:53:04'),
(147, '230211060035', 142, 'A', '2026-03-16 00:53:04'),
(148, '230211060035', 143, 'A', '2026-03-16 00:53:04'),
(149, '230211060035', 144, 'A', '2026-03-16 00:53:04'),
(150, '230211060035', 145, 'A', '2026-03-16 00:53:05'),
(151, '230211060035', 146, 'A', '2026-03-16 00:53:05'),
(152, '230211060035', 147, 'A', '2026-03-16 00:53:05'),
(153, '230211060035', 148, 'A', '2026-03-16 00:53:05'),
(154, '230211060035', 149, 'A', '2026-03-16 00:53:05'),
(155, '230211060035', 150, 'A', '2026-03-16 00:53:06'),
(156, '230211060035', 151, 'A', '2026-03-16 00:53:06'),
(157, '230211060035', 152, 'A', '2026-03-16 00:53:06'),
(158, '230211060035', 153, 'A', '2026-03-16 00:53:06'),
(159, '230211060035', 154, 'A', '2026-03-16 00:53:06'),
(160, '230211060035', 155, 'A', '2026-03-16 00:53:07'),
(161, '230211060035', 156, 'A', '2026-03-16 00:53:07'),
(162, '230211060035', 157, 'A', '2026-03-16 01:01:02'),
(163, '230211060035', 158, 'A', '2026-03-16 01:01:02'),
(164, '230211060035', 159, 'A', '2026-03-16 01:01:03'),
(165, '230211060035', 160, 'A', '2026-03-16 01:01:03'),
(166, '230211060035', 161, 'A', '2026-03-16 01:01:03'),
(167, '230211060035', 162, 'A', '2026-03-16 01:01:03'),
(168, '230211060035', 163, 'A', '2026-03-16 01:01:04'),
(169, '230211060035', 164, 'A', '2026-03-16 01:01:04'),
(170, '230211060035', 165, 'A', '2026-03-16 01:01:04'),
(171, '230211060035', 166, 'A', '2026-03-16 01:01:04'),
(172, '230211060035', 167, 'A', '2026-03-16 01:01:04'),
(173, '230211060035', 168, 'A', '2026-03-16 01:01:05'),
(174, '230211060035', 169, 'A', '2026-03-16 01:01:05'),
(175, '230211060035', 170, 'A', '2026-03-16 01:01:05'),
(176, '230211060035', 171, 'A', '2026-03-16 01:01:06'),
(177, '230211060035', 172, 'A', '2026-03-16 01:01:06'),
(178, '230211060035', 173, 'A', '2026-03-16 01:01:06'),
(179, '230211060035', 174, 'A', '2026-03-16 01:01:06'),
(180, '230211060035', 175, 'A', '2026-03-16 01:01:07'),
(181, '230211060035', 176, 'A', '2026-03-16 01:01:07'),
(358, '12345678910', 1, 'E', '2026-03-18 20:46:41'),
(359, '12345678910', 2, 'C', '2026-03-18 20:46:46'),
(360, '12345678910', 3, 'A', '2026-03-18 20:46:57'),
(361, '12345678910', 4, 'D', '2026-03-18 20:47:03'),
(362, '12345678910', 5, 'D', '2026-03-18 20:47:08'),
(363, '12345678910', 6, 'C', '2026-03-18 20:47:20'),
(364, '12345678910', 7, 'C', '2026-03-18 20:47:33'),
(365, '12345678910', 8, 'E', '2026-03-18 20:47:46'),
(366, '12345678910', 9, 'B', '2026-03-18 20:48:01'),
(367, '12345678910', 10, 'B', '2026-03-18 20:48:11'),
(368, '12345678910', 11, 'B', '2026-03-18 20:48:29'),
(369, '12345678910', 12, 'C', '2026-03-18 20:48:43'),
(370, '12345678910', 13, 'A', '2026-03-18 20:49:06'),
(371, '12345678910', 14, 'E', '2026-03-18 20:49:12'),
(372, '12345678910', 15, 'C', '2026-03-18 20:49:39'),
(373, '12345678910', 16, 'E', '2026-03-18 20:49:57'),
(374, '12345678910', 17, 'B', '2026-03-18 20:50:08'),
(375, '12345678910', 18, 'A', '2026-03-18 20:50:21'),
(376, '12345678910', 19, 'A', '2026-03-18 20:50:38'),
(377, '12345678910', 20, 'A', '2026-03-18 20:50:52'),
(378, '12345678910', 21, 'C', '2026-03-18 20:53:12'),
(379, '12345678910', 22, 'B', '2026-03-18 20:53:23'),
(380, '12345678910', 23, 'D', '2026-03-18 20:53:34'),
(381, '12345678910', 24, 'E', '2026-03-18 20:53:55'),
(382, '12345678910', 25, 'C', '2026-03-18 20:54:16'),
(383, '12345678910', 26, 'D', '2026-03-18 20:54:41'),
(384, '12345678910', 27, 'C', '2026-03-18 20:54:55'),
(385, '12345678910', 28, 'D', '2026-03-18 20:55:11'),
(386, '12345678910', 29, 'A', '2026-03-18 20:55:25'),
(387, '12345678910', 30, 'A', '2026-03-18 20:55:47'),
(388, '12345678910', 31, 'E', '2026-03-18 20:56:05'),
(389, '12345678910', 32, 'D', '2026-03-18 20:56:35'),
(390, '12345678910', 33, 'A', '2026-03-18 20:56:52'),
(391, '12345678910', 34, 'E', '2026-03-18 20:57:00'),
(392, '12345678910', 35, 'C', '2026-03-18 20:57:21'),
(393, '12345678910', 36, 'C', '2026-03-18 20:57:36'),
(394, '12345678910', 37, 'B', '2026-03-18 20:57:53'),
(395, '12345678910', 38, 'E', '2026-03-18 20:58:03'),
(396, '12345678910', 39, 'A', '2026-03-18 20:58:13'),
(397, '12345678910', 40, 'D', '2026-03-18 20:58:18'),
(398, '12345678910', 41, 'C', '2026-03-18 21:01:49'),
(399, '12345678910', 42, 'A', '2026-03-18 21:02:18'),
(400, '12345678910', 43, 'A', '2026-03-18 21:02:33'),
(401, '12345678910', 44, 'D', '2026-03-18 21:02:51'),
(402, '12345678910', 45, 'C', '2026-03-18 21:03:45'),
(403, '12345678910', 46, 'B', '2026-03-18 21:03:53'),
(404, '12345678910', 47, 'A', '2026-03-18 21:04:02'),
(405, '12345678910', 48, 'B', '2026-03-18 21:04:14'),
(406, '12345678910', 49, 'C', '2026-03-18 21:04:23'),
(407, '12345678910', 50, 'B', '2026-03-18 21:04:41'),
(408, '12345678910', 51, 'B', '2026-03-18 21:04:51'),
(409, '12345678910', 52, 'D', '2026-03-18 21:05:17'),
(410, '12345678910', 53, 'D', '2026-03-18 21:05:30'),
(411, '12345678910', 54, 'B', '2026-03-18 21:05:36'),
(412, '12345678910', 55, 'E', '2026-03-18 21:06:12'),
(413, '12345678910', 56, 'A', '2026-03-18 21:06:20'),
(414, '12345678910', 57, 'D', '2026-03-18 21:06:30'),
(415, '12345678910', 58, 'E', '2026-03-18 21:06:37'),
(416, '12345678910', 59, 'C', '2026-03-18 21:06:50'),
(417, '12345678910', 60, 'A', '2026-03-18 21:07:18'),
(418, '12345678910', 61, 'bunga', '2026-03-18 21:10:08'),
(419, '12345678910', 62, 'indera', '2026-03-18 21:10:33'),
(420, '12345678910', 63, 'perasa', '2026-03-18 21:10:52'),
(421, '12345678910', 64, 'cuaca', '2026-03-18 21:11:03'),
(422, '12345678910', 65, 'informasi', '2026-03-18 21:11:18'),
(423, '12345678910', 66, 'lensa', '2026-03-18 21:11:26'),
(424, '12345678910', 67, 'organ', '2026-03-18 21:11:35'),
(425, '12345678910', 68, 'ukursn', '2026-03-18 21:11:58'),
(426, '12345678910', 69, 'bibit', '2026-03-18 21:12:04'),
(427, '12345678910', 70, 'simbol', '2026-03-18 21:12:23'),
(428, '12345678910', 71, 'makanan', '2026-03-18 21:12:30'),
(429, '12345678910', 72, 'mengisi', '2026-03-18 21:12:52'),
(430, '12345678910', 73, 'cerita', '2026-03-18 21:13:02'),
(431, '12345678910', 74, 'sifat', '2026-03-18 21:13:13'),
(432, '12345678910', 75, 'negosiasi', '2026-03-18 21:13:30'),
(433, '12345678910', 76, 'arah', '2026-03-18 21:13:42'),
(434, '12345678910', 77, 'A', '2026-03-18 21:14:54'),
(435, '12345678910', 78, 'A', '2026-03-18 21:15:28'),
(436, '12345678910', 79, 'C', '2026-03-18 21:17:55'),
(437, '12345678910', 80, 'D', '2026-03-18 21:19:29'),
(438, '12345678910', 81, 'A', '2026-03-18 21:20:16'),
(439, '12345678910', 82, 'B', '2026-03-18 21:20:44'),
(440, '12345678910', 83, 'A', '2026-03-18 21:21:00'),
(441, '12345678910', 84, 'B', '2026-03-18 21:21:02'),
(442, '12345678910', 85, 'B', '2026-03-18 21:21:03'),
(443, '12345678910', 86, 'B', '2026-03-18 21:21:04'),
(444, '12345678910', 87, 'C', '2026-03-18 21:21:04'),
(445, '12345678910', 88, 'D', '2026-03-18 21:21:06'),
(446, '12345678910', 89, 'D', '2026-03-18 21:21:07'),
(447, '12345678910', 90, 'C', '2026-03-18 21:21:08'),
(448, '12345678910', 91, 'A', '2026-03-18 21:21:09'),
(449, '12345678910', 92, 'B', '2026-03-18 21:21:11'),
(450, '12345678910', 93, 'A', '2026-03-18 21:21:12'),
(451, '12345678910', 94, 'C', '2026-03-18 21:21:13'),
(452, '12345678910', 95, 'A', '2026-03-18 21:21:14'),
(453, '12345678910', 96, 'B', '2026-03-18 21:21:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iq_user_section_progress`
--

CREATE TABLE `iq_user_section_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `status` enum('belum','sedang','selesai') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id` int(11) NOT NULL,
  `kode_tes` enum('KEPRIBADIAN','KEPRIBADIAN2','IQ') NOT NULL,
  `nomor_soal` int(11) NOT NULL,
  `pertanyaan_a` text NOT NULL,
  `pertanyaan_b` text NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id`, `kode_tes`, `nomor_soal`, `pertanyaan_a`, `pertanyaan_b`, `status`, `created_at`) VALUES
(65, 'KEPRIBADIAN', 1, 'Saya tidak akan menegur pelanggar-pelanggar peraturan bila saya merasa pasti bahwa tidak ada satu orangpun yang mengetahui tentang pelanggaran-pelanggaran tersebut.', 'Bila saya mengumumkan suatu keputusan yang kurang menyenangkan, saya akan menjelaskan kepada bawahan saya bahwa keputusan ini dibuat oleh direktur.', 'aktif', '2026-02-12 00:48:30'),
(66, 'KEPRIBADIAN', 2, 'Bila ada seorang karyawan yang hasil kerjanya selalu tidak memuaskan saya, saya akan menunggu suatu kesempatan untuk memindahkannya dan bukan untuk memecatnya.', 'Bila ada bawahan saya yang dikucilkan dari kelompok kerjanya, saya akan mencari jalan agar orang lain dapat berteman dengannya.', 'aktif', '2026-02-12 00:48:30'),
(67, 'KEPRIBADIAN', 3, 'Bila direktur memberikan perintah yang kurang menyenangkan, saya piker adalah cukup bijaksana bila saya menyebutkan namanya dan bukan nama saya.', 'Bila biasanya membuat keputusan-keputusan sendiri dan menyampaikannya kepada bawahan saya.', 'aktif', '2026-02-12 00:48:30'),
(68, 'KEPRIBADIAN', 4, 'Bila saya ditegur oleh atasan saya, saya akan memanggil semua bawahan saya dan mengatakan semua teguran tersebut kepada mereka.', 'Saya selalu memberikan tugas-tugas yang sangat sulit kepada karyawan-karyawan yang paling berpengalaman.', 'aktif', '2026-02-12 00:48:30'),
(69, 'KEPRIBADIAN', 5, 'Saya selalu melakukan diskusi-diskusi untuk mencapai kata sepakat.', 'Saya selalu menganjurkan kepada bawahan saya untuk memberikan usul-usul, tetapi kadang-kadang saya langsung mengambil tindakan tertentu.', 'aktif', '2026-02-12 00:48:30'),
(70, 'KEPRIBADIAN', 6, 'Seringkali saya lebih mementingkan tugas daripada diri saya sendiri.', 'Saya mengijinkan bawahan-bawahan saya untuk ikut serta dalam mengambil keputusan.', 'aktif', '2026-02-12 00:48:30'),
(71, 'KEPRIBADIAN', 7, 'Bila jumlah dan mutu hasil kerja bagian saya tidak memuaskan, saya mengatakan kepada bawahan-bawahan saya bahwa direktur merasa kecewa. Oleh karena itu mereka harus memperbaiki kerja mereka.', 'Saya membuat keputusan-keputusan sendiri dan kemudian saya mencoba untuk “menjual” keputusan-keputusan itu kepada bawahan saya.', 'aktif', '2026-02-12 00:48:30'),
(72, 'KEPRIBADIAN', 8, 'Bila saya mengumumkan suatu keputusan yang kurang menyenangkan, saya akan menjelaskan kepada bawahan saya bahwa keputusan ini dibuat oleh direktur.', 'Saya mengijinkan bawahan-bawahan saya untuk ikut serta di dalam pengambilan keputusan, tetapi sayapun menyediakan sesuatu yang jitu sebagai keputusan terakhir.', 'aktif', '2026-02-12 00:48:30'),
(73, 'KEPRIBADIAN', 9, 'Saya akan memberikan tugas-tugas yang sulit kepada bawahan saya yang belum berpengalaman, tetapi bila mereka memperoleh kesukaran, saya akan mengambil alih tanggung jawab mereka.', 'Bila jumlah dan mutu hasil kerja bagian saya tidak memuaskan, saya mengatakan kepada bawahan-bawahan saya bahwa direktur merasa kecewa. Oleh karena itu mereka harus memperbaiki kerja mereka.', 'aktif', '2026-02-12 00:48:30'),
(74, 'KEPRIBADIAN', 10, 'Saya merasa bahwa dengan bekerja keras untuk bawahan saya, mereka akan menyukai saya.', 'Saya membiarkan orang lain menangani tugas mereka masing-masing, walaupun mereka membuat banyak kesalahan.', 'aktif', '2026-02-12 00:48:30'),
(75, 'KEPRIBADIAN', 11, 'Saya menunjukkan minat saya terhadap kehidupan pribadi bawahan-bawahan saya, karena sayapun mengharapkan mereka berbuat seperti itu kepada saya.', 'Saya merasa bahwa bawahan-bawahan saya tidak perlu mengerti mengapa mereka mengerjakan sesuatu hal, sejauh mereka mengerjakan hal tersebut.', 'aktif', '2026-02-12 00:48:30'),
(76, 'KEPRIBADIAN', 12, 'Saya percaya bahwa bawahan-bawahan yang tidak disiplin tidak akan memperbaiki jumlah atau mutu kerja mereka dalam jangka waktu yang panjang.', 'Bila menghadapi masalah yang sulit, saya berusaha untuk mencapai pemecahan yang dapat diterima oleh sebagian besar orang.', 'aktif', '2026-02-12 00:48:30'),
(77, 'KEPRIBADIAN', 13, 'Bila beberapa bawahan saya merasa tidak bahagia, saya akan mencoba melakukan sesuatu untuk mengatasi hal tersebut.', 'Saya berusaha bekerja sebaik mungkin dan memberikan ide-ide pengembangan pada pimpinan.', 'aktif', '2026-02-12 00:48:30'),
(78, 'KEPRIBADIAN', 14, 'Saya menyetujui kenaikan tunjangan-tunjangan untuk staf dan karyawan.', 'Saya mendukung bawahan saya yang ingin meningkatkan pengetahuan tentang pekerjaan dan instansi, walaupun hal itu sebenarnya belum diperlukan untuk kedudukan mereka sekarang.', 'aktif', '2026-02-12 00:48:30'),
(79, 'KEPRIBADIAN', 15, 'Saya membiarkan orang lain menangani tugas mereka masing-masing, walaupun mereka banyak membuat kesalahan.', 'Saya membuat keputusan-keputusan sendiri, tetapi saya akan mempertimbangkan usul-usul dari bawahan-bawahan saya.', 'aktif', '2026-02-12 00:48:30'),
(80, 'KEPRIBADIAN', 16, 'Bila bawahan saya yang dikucilkan dari kelompok kerjanya, saya akan mencari cara agar orang lain dapat berteman dengannya.', 'Bila seorang karyawan tidak sanggup menyelesaikan tugasnya, saya akan membantu dia untuk menyelesaikan tugas tersebut.', 'aktif', '2026-02-12 00:48:30'),
(81, 'KEPRIBADIAN', 17, 'Saya percaya bahwa penerapan disiplin merupakan contoh untuk karyawan-karyawan lain.', 'Saya merasa lebih mementingkan tugas daripada diri saya sendiri.', 'aktif', '2026-02-12 00:48:30'),
(82, 'KEPRIBADIAN', 18, 'Saya mencela pembicaraan-pembicaraan yang tidak perlu diantara bawahan-bawahan saya, selama mereka bekerja.', 'Saya menyetujui kenaikan tunjangan-tunjangan untuk staf dan karyawan.', 'aktif', '2026-02-12 00:48:30'),
(83, 'KEPRIBADIAN', 19, 'Saya selalu memperhatikan keterlambatan dan kemangkiran bawahan saya.', 'Saya percaya bahwa organisasi KORPRI akan mencoba meruntuhkan kewibawaan pimpinan unit kerja.', 'aktif', '2026-02-12 00:48:30'),
(84, 'KEPRIBADIAN', 20, 'Kadang-kadang saya merasa bahwa apa yang dikeluhkan oleh KORPRI bukanlah masalah yang mendasar.', 'Saya merasa bahwa keluhan-keluhan tidak dapat dicegah dan saya akan berusaha untuk menghilangkan keluhan tersebut.', 'aktif', '2026-02-12 00:48:30'),
(85, 'KEPRIBADIAN', 21, 'Adalah penting bagi saya untuk memperoleh penghargaan atas ide-ide saya yang baik.', 'Saya mengemukakan pendapat-pendapat saya dimuka umum hanya bila saya merasa bahwa orang lain akan setuju dengan saya.', 'aktif', '2026-02-12 00:48:30'),
(86, 'KEPRIBADIAN', 22, 'Saya percaya bahwa organisasi KORPRI akan mencoba meruntuhkan kewibawaan pimpinan unit kerja.', 'Saya percaya bahwa pertemuan-pertemuan yang sering dengan karyawan secara pribadi akan membantu pengembangan diri mereka.', 'aktif', '2026-02-12 00:48:30'),
(87, 'KEPRIBADIAN', 23, 'Saya merasa bahwa bawahan-bawahan saya tidak perlu mengerti mengapa mereka mengerjakan sesuatu hal, sejauh mereka mengerjakan hal tersebut.', 'Saya merasa bahwa jam pencatat waktu datang dan pulangnya para pegawai akan mengurangi keterlambatan.', 'aktif', '2026-02-12 00:48:30'),
(88, 'KEPRIBADIAN', 24, 'Saya biasanya membuat keputusan-keputusan sendiri dan menyampaikannya kepada bawahan saya.', 'Saya merasa bahwa organisasi KORPRI dan pimpinan unit kerja dapat bekerjasama untuk mencapai tujuan-tujuan bersama.', 'aktif', '2026-02-12 00:48:30'),
(89, 'KEPRIBADIAN', 25, 'Saya menyukai penggunaan skala penggajian karyawan.', 'Saya selalu melakukan diskusi-diskusi untuk mencapai kata sepakat.', 'aktif', '2026-02-12 00:48:30'),
(90, 'KEPRIBADIAN', 26, 'Saya tidak akan memberikan tugas yang tidak saya senangi kepada orang lain.', 'Bila beberapa bawahan saya merasa tidak berbahagia, saya akan mencoba melakukan sesuatu untuk mengatasi hal tersebut.', 'aktif', '2026-02-12 00:48:30'),
(91, 'KEPRIBADIAN', 27, 'Bila ada tugas yang mendesak, walaupun semua peralatannya sudah diselesaikan, saya akan membiarkannya saja dan meminta salah seorang bawahan saya untuk mengerjakan tugas tersebut.', 'Adalah penting bagi saya untuk memperoleh penghargaan atas ide-ide saya yang baik.', 'aktif', '2026-02-12 00:48:30'),
(92, 'KEPRIBADIAN', 28, 'Tujuan saya adalah berusaha mengerjakan tugas sebaik mungkin tanpa mengeluh.', 'Saya memberikan tugas kepada bawahan saya tanpa banyak mempertimbangkan pengalaman atau kemampuan saya lebih menuntut pencapaian hasilnya saja.', 'aktif', '2026-02-12 00:48:30'),
(93, 'KEPRIBADIAN', 29, 'Saya memberikan tugas kepada bawahan saya tanpa banyak mempertimbangkan pengalaman atau kemampuan saya lebih menuntut pencapaian hasilnya saja.', 'Saya dengan sabar mendengarkan keluhan-keluhan dan ketidakpuasan-ketidakpuasan bawahan saya, tetapi sering kali saya meralat apa yang mereka katakan.', 'aktif', '2026-02-12 00:48:30'),
(94, 'KEPRIBADIAN', 30, 'Saya merasa bahwa keluhan-keluhan tidak dapat dicegah dan saya berusaha untuk menghilangkan keluhan tersebut.', 'Saya percaya bahwa bawahan-bawahan saya akan merasakan kepuasan kerja tanpa merasa tertekan oleh saya.', 'aktif', '2026-02-12 00:48:30'),
(95, 'KEPRIBADIAN', 31, 'Bila menghadapi masalah yang sulit, saya berusaha untuk mencapai pemecahan yang dapat diterima oleh sebagian besar orang.', 'Saya percaya bahwa pengalaman bekerja lebih bermanfaat daripada pendidikan teoritis.', 'aktif', '2026-02-12 00:48:30'),
(96, 'KEPRIBADIAN', 32, 'Saya selalu memberikan tugas-tugas yang sangat sulit kepada karyawan-karyawan yang paling berpengalaman.', 'Saya percaya bahwa kenaikan jabatan adalah semata-mata berdasarkan kemampuan yang ada.', 'aktif', '2026-02-12 00:48:30'),
(97, 'KEPRIBADIAN', 33, 'Saya merasa bahwa masalah-masalah yang timbul diantara para karyawan biasanya akan dapat diselesaikan di antara mereka sendiri, tanpa campur tangan dari saya.', 'Bila saya ditegur oleh atasan saya, saya akan memanggil semua bawahan saya dan mengatakan semua teguran tersebut kepada mereka.', 'aktif', '2026-02-12 00:48:30'),
(98, 'KEPRIBADIAN', 34, 'Saya tidak peduli dengan apa yang dikerjakan oleh karyawan saya di luar jam kerja kantornya.', 'Saya percaya bahwa bawahan-bawahan yang tidak disiplin tidak akan memperbaiki jumlah atau mutu kerja mereka dalam jangka waktu yang panjang.', 'aktif', '2026-02-12 00:48:30'),
(99, 'KEPRIBADIAN', 35, 'Saya memberikan informasikepada pimpinan unit kerja tidak lebih dari apa yang mereka tanyakan.', 'Kadang-kadang saya merasa bahwa apa yang dikeluhkan oleh KORPRI bukanlah masalah yang mendasar.', 'aktif', '2026-02-12 00:48:30'),
(100, 'KEPRIBADIAN', 36, 'Saya kadang ragu-ragu untuk membuat suatu keputusan yang akan tidak disukai oleh bawahan-bawahan saya.', 'Tujuan saya adalah berusaha mengerjakan tugas sebaik mungkin tanpa mengeluh.', 'aktif', '2026-02-12 00:48:30'),
(101, 'KEPRIBADIAN', 37, 'Saya dengan sabar mendengarkan keluhan-keluhan dan ketidakpuasan-ketidakpuasan bawahan saya, tetapi sering kali saya meralat apa yang mereka katakan.', 'Saya kadang ragu-ragu untuk membuat suatu keputusan yang akan tidak disukai oleh bawahan-bawahan saya.', 'aktif', '2026-02-12 00:48:30'),
(102, 'KEPRIBADIAN', 38, 'Saya mengemukakan pendapat-pendapat saya dimuka umum hanya bila saya merasa bahwa orang lain akan setuju dengan saya.', 'Sebagian besar dari bawahan-bawahan saya dapat menyelesaikan tugas-tugas mereka, bila perlu tanpa kehadiran saya.', 'aktif', '2026-02-12 00:48:30'),
(103, 'KEPRIBADIAN', 39, 'Saya berusaha bekerja sebaik mungkin dan memberikan ide-ide pengembangan pada pimpinan unit kerja.', 'Bila saya memberikan tugas kepada bawahan-bawahan saya, saya akan menentukan batas waktu penyelesaiannya.', 'aktif', '2026-02-12 00:48:30'),
(104, 'KEPRIBADIAN', 40, 'Saya selalu menganjurkan kepada bawahan saya untuk memberikan usul-usul, tetapi kadang-kadang saya langsung mengambil tindakan tertentu.', 'Saya mencoba membuat bawahan-bawahan saya merasa senang apabila mereka berbicara dengan saya.', 'aktif', '2026-02-12 00:48:30'),
(105, 'KEPRIBADIAN', 41, 'Di dalam diskusi-diskusi saya memberikan fakta-fakta sesuai pemahaman bawahan saya, dan membiarkan mereka untuk membuat kesimpulan sendiri.', 'Bila direktur memberikan perintah yang kurang menyenangkan, saya piker adalah cukup bijaksana bila saya menyebutkan namanya dan bukan nama saya.', 'aktif', '2026-02-12 00:48:30'),
(106, 'KEPRIBADIAN', 42, 'Bila ada tugas-tugas mendadak atau tugas yang tidak menyenangkan, sebelumnya saya akan meminta beberapa sukarelawan yang mau mengerjakan tugas tersebut.', 'Saya menunjukkan minat saya terhadap kehidupan pribadi bawahan-bawahan saya, karena sayapun mengharapkan mereka berbuat seperti itu kepada saya.', 'aktif', '2026-02-12 00:48:30'),
(107, 'KEPRIBADIAN', 43, 'Saya selalu memperhatikan kebahagiaan karyawan-karyawan saya saat mekera mengerjakan tugas-tugas mereka.', 'Saya selalu memperhatikan keterlambatan dan kemangkiran bawahan saya.', 'aktif', '2026-02-12 00:48:30'),
(108, 'KEPRIBADIAN', 44, 'Sebagian besar dari bawahan-bawahan saya dapat menyelesaikan tugas-tugas mereka, bila perlu tanpa kehadiran saya.', 'Bila ada tugas yang mendesak, walaupun semua peralatannya sudah diselesaikan, saya akan membiarkannya saja dan meminta salah seorang bawahan saya untuk mengerjakan tugas tersebut.', 'aktif', '2026-02-12 00:48:30'),
(109, 'KEPRIBADIAN', 45, 'Saya percaya bahwa bawahan bawahan saya akan merasakan kepuasan kerja tanpa merasa tertekan oleh saya.', 'Saya memberikan informasi kepada “pimpinan unit kerja” tidak lebih dari apa yang mereka tanyakan.', 'aktif', '2026-02-12 00:48:30'),
(110, 'KEPRIBADIAN', 46, 'Saya percaya bahwa pertemuan-pertemuan yang sering dengan karyawan secara pribadi akan membantu pengembangan diri mereka.', 'Saya selalu memperhatikan kebahagiaan karyawan-karyawan saya saat mereka mengerjakan tugas-tugas mereka.', 'aktif', '2026-02-12 00:48:30'),
(111, 'KEPRIBADIAN', 47, 'Saya mendukung bawahan saya yang ingin meningkatkan pengetahuan tentang pekerjaan dan instansi, walaupun hal itu sebenarnya belum diperlukan untuk kedudukan mereka sekarang.', 'Saya mengawasi benar bawahan-bawahan saya yang kurang mahir dalam pekerjaannya atau bawahan-bawahan saya yang hasil kerjanya kurang memuaskan.', 'aktif', '2026-02-12 00:48:30'),
(112, 'KEPRIBADIAN', 48, 'Saya mengijinkan bawahan-bawahan saya untuk ikut serta dalam mengambil keputusan dan saya selalu mematuhi keputusan yang dibuat berdasarkan suara terbanyak.', 'Saya membuat bawahan-bawahan saya bekerja keras, dan saya berusaha meyakinkan mereka bahwa biasanya mereka akan mendapatkan perlakuan yang adil dari pimpinan unit kerja.', 'aktif', '2026-02-12 00:48:30'),
(113, 'KEPRIBADIAN', 49, 'Saya merasa bahwa semua karyawan pada jabatan yang sama seharusnya memperoleh gaji yang sama.', 'Bila ada seseorang karyawan yang hasil kerjanya selalu tidak memuaskan saya, saya akan menunggu suatu kesempatan untuk memindahkannya dan bukan untuk memecatnya.', 'aktif', '2026-02-12 00:48:30'),
(114, 'KEPRIBADIAN', 50, 'Saya merasa bahwa tujuan-tujuan KORPRI dan tujuan-tujuan instansi saling berbeda.', 'Saya merasa bahwa dengan bekerja keras bagi bawahan saya, mereka akan menyenangi saya.', 'aktif', '2026-02-12 00:48:30'),
(115, 'KEPRIBADIAN', 51, 'Saya mengawasi benar bawahan-bawahan saya yang kurang mahir dalam pekerjaannya atau bawahan-bawahan saya yang hasil kerjanya kurang memuaskan.', 'Saya mencela pembicaraan-pembicaraan yang tidak perlu diantara bawahan-bawahan saya, selama mereka bekerja.', 'aktif', '2026-02-12 00:48:30'),
(116, 'KEPRIBADIAN', 52, 'Bila saya memberikan tugas kepada bawahan-bawahan saya, saya akan menentukan batas waktu penyelesaiannya.', 'Saya tidak akan memberikan tugas yang tidak saya senangi kepada orang lain,', 'aktif', '2026-02-12 00:48:30'),
(117, 'KEPRIBADIAN', 53, 'Saya percaya bahwa pengalaman bekerja lebih bermanfaat daripada pendidikan teoritis.', 'Saya tidak peduli dengan apa yang dikerjakan oleh karyawan saya di luar jam kantornya.', 'aktif', '2026-02-12 00:48:30'),
(118, 'KEPRIBADIAN', 54, 'Saya merasa bahwa jam pencatat waktu datang dan pulangnya para pegawai akan mengurangi keterlambatan.', 'Saya mengijinkan bawahan-bawahan saya untuk ikut serta dalam mengambil keputusan dan saya selalu mematuhi keputusan yang dibuat berdasarkan suara terbanyak.', 'aktif', '2026-02-12 00:48:30'),
(119, 'KEPRIBADIAN', 55, 'Saya membuat keputusan-keputusan sendiri, tetapi saya dapat mempertimbangkan saran-saran yang wajar dari bawahan-bawahan saya.', 'Saya merasa bahwa tujuan-tujuan KORPRI dan tujuan-tujuan Instansi adalah saling berbeda.', 'aktif', '2026-02-12 00:48:30'),
(120, 'KEPRIBADIAN', 56, 'Saya membuat keputusan-keputusan sendiri dan kemudian saya mencoba untuk “menjual” keputusan-keputusan itu kepada bawahan saya.', 'Apabila mungkin, saya akan membentuk kelompok-kelompok kerja yang terdiri dari orang-orang yang sudah menjadi teman-teman baik saya.', 'aktif', '2026-02-12 00:48:30'),
(121, 'KEPRIBADIAN', 57, 'Saya tidak akan ragu-ragu untuk mempekerjakan pegawai-pegawai yang cacat jasmani, bilamana saya merasa pasti bahwa mereka dapat menangani pekerjaannya.', 'Saya tidak akan menegur pelanggar-pelanggar peraturan bila saya merasa pasti bahwa tidak ada satu orangpun yang mengetahui tentang pelanggaran-pelanggaran tersebut.', 'aktif', '2026-02-12 00:48:30'),
(122, 'KEPRIBADIAN', 58, 'Apabila mungkin, saya akan membentuk kelompok-kelompok kerja yang terdiri dari orang-orang yang sudah menjadi teman-teman baik saya.', 'Saya akan memberikan tugas-tugas yang sulit kepada bawahan saya yang belum berpengalaman, tetapi bila mereka memperoleh kesukaran, saya akan mengambil alih tanggung jawab mereka.', 'aktif', '2026-02-12 00:48:30'),
(123, 'KEPRIBADIAN', 59, 'Saya membuat bawahan-bawahan saya bekerja keras, dan saya berusaha meyakinkan mereka bahwa biasanya mereka akan mendapatkan perlakuan yang adil dari pimpinan unit kerja.', 'Saya percaya bahwa penerapan disiplin merupakan contoh untuk karyawan-karyawan lain.', 'aktif', '2026-02-12 00:48:30'),
(124, 'KEPRIBADIAN', 60, 'Saya mencoba membuat bawahan-bawahan saya merasa senang apabila mereka berbicara dengan saya.', 'Saya menyukai penggunaan skala penggajian karyawan.', 'aktif', '2026-02-12 00:48:30'),
(125, 'KEPRIBADIAN', 61, 'Saya percaya bahwa kenaikan jabatan adalah semata-mata berdasarkan kemampuan yang ada.', 'Saya merasa bahwa masalah-masalah yang timbul diantara para karyawan biasanya akan dapat diselesaikan di antara mereka sendiri, tanpa campur tangan dari saya.', 'aktif', '2026-02-12 00:48:30'),
(126, 'KEPRIBADIAN', 62, 'Saya merasa bahwa KORPRI dan pimpinan unit kerja bekerja untuk mencapai tujuan-tujuan yang sama.', 'Di dalam diskusi-diskusi saya memberikan fakta-fakta sesuai pemahaman bawahan saya, dan membiarkan mereka untuk membuat kesimpulan sendiri.', 'aktif', '2026-02-12 00:48:30'),
(127, 'KEPRIBADIAN', 63, 'Bila seorang karyawan tidak sanggup menyelesaikan tugasnya, saya akan membantu dia untuk menyelesaikan tugas tersebut.', 'Saya merasa bahwa semua karyawan pada jabatan yang sama seharusnya memperoleh gaji yang sama.', 'aktif', '2026-02-12 00:48:30'),
(128, 'KEPRIBADIAN', 64, 'Saya mengijinkan bawahan-bawahan saya untuk ikut serta di dalam pengambilan keputusan, tetapi sayapun menyediakan sesuatu yang jitu sebagai keputusan terakhir.', 'Saya tidak akan ragu-ragu untuk mempekerjakan pegawai-pegawai yang cacat jasmani, bilamana saya merasa pasti bahwa mereka dapat menangani pekerjaannya.', 'aktif', '2026-02-12 00:48:30'),
(129, 'KEPRIBADIAN2', 1, 'Saya seorang pekerja keras', 'Saya tidak suka uring-uringan', 'aktif', '2026-02-18 06:33:24'),
(130, 'KEPRIBADIAN2', 2, 'Saya suka menghasilkan pekerjaan yang lebih baik daripada orang lain', 'Saya akan tetap menangani suatu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(131, 'KEPRIBADIAN2', 3, 'Saya suka menunjukkan pada orang lain cara melakukan sesuatu', 'Saya ingin berusaha sebaik mungkin', 'aktif', '2026-02-18 06:33:24'),
(132, 'KEPRIBADIAN2', 4, 'Saya suka melucu', 'Saya senang memberitahu orang lain hal-hal yang harus dikerjakan', 'aktif', '2026-02-18 06:33:24'),
(133, 'KEPRIBADIAN2', 5, 'Saya suka bergabung dengan kelompok', 'Saya senang diperhatikan oleh kelompok', 'aktif', '2026-02-18 06:33:24'),
(134, 'KEPRIBADIAN2', 6, 'Saya suka menjalin hubungan pribadi yang akrab', 'Saya suka berteman dengan kelompok', 'aktif', '2026-02-18 06:33:24'),
(135, 'KEPRIBADIAN2', 7, 'Saya dapat cepat berubah jika merasa perlu', 'Saya berusaha menjalin hubungan pribadi yang akrab', 'aktif', '2026-02-18 06:33:24'),
(136, 'KEPRIBADIAN2', 8, 'Saya suka menyerang kembali jika benar-benar disakiti', 'Saya suka melakukan hal-hal yang baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(137, 'KEPRIBADIAN2', 9, 'Saya ingin agar atasan menyukai saya', 'Saya suka menegur orang lain jika mereka melakukan kesalahan', 'aktif', '2026-02-18 06:33:24'),
(138, 'KEPRIBADIAN2', 10, 'Saya suka mengikuti petunjuk-petunjuk yang diberikan pada saya', 'Saya suka menyenangkan orang-orang yang menjadi atasan saya', 'aktif', '2026-02-18 06:33:24'),
(139, 'KEPRIBADIAN2', 11, 'Saya berusaha keras', 'Saya seorang yang tertib. Saya meletakkan segala sesuatu pada tempatnya', 'aktif', '2026-02-18 06:33:24'),
(140, 'KEPRIBADIAN2', 12, 'Saya membuat orang lain melakukan apa yang saya inginkan', 'Saya tidak mudah marah', 'aktif', '2026-02-18 06:33:24'),
(141, 'KEPRIBADIAN2', 13, 'Saya suka memberi tahu kelompok tentang apa yang harus mereka lakukan', 'Saya selalu menekuni satu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(142, 'KEPRIBADIAN2', 14, 'Saya ingin tampak menarik dan lincah', 'Saya ingin menjadi orang yang sangat berhasil', 'aktif', '2026-02-18 06:33:24'),
(143, 'KEPRIBADIAN2', 15, 'Saya senang jika dapat berbaur dengan kelompok', 'Saya suka membantu orang lain mengambil keputusan', 'aktif', '2026-02-18 06:33:24'),
(144, 'KEPRIBADIAN2', 16, 'Saya cemas jika orang lain tidak menyukai saya', 'Saya senang jika orang-orang memperhatikan saya', 'aktif', '2026-02-18 06:33:24'),
(145, 'KEPRIBADIAN2', 17, 'Saya suka mencoba hal-hal yang baru', 'Saya lebih suka bekerja dengan orang lain daripada bekerja sendirian', 'aktif', '2026-02-18 06:33:24'),
(146, 'KEPRIBADIAN2', 18, 'Kadang-kadang saya menyalahkan orang lain jika terjadi kesalahan', 'Saya merasa terganggu bila saya tidak disukai orang lain', 'aktif', '2026-02-18 06:33:24'),
(147, 'KEPRIBADIAN2', 19, 'Saya ingin menyenangkan hati atasan saya', 'Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(148, 'KEPRIBADIAN2', 20, 'Saya menyukai petunjuk yang terperinci tentang bagaimana melakukan suatu pekerjaan', 'Saya suka memberi tahu orang lain bila mereka menjengkelkan saya', 'aktif', '2026-02-18 06:33:24'),
(149, 'KEPRIBADIAN2', 21, 'Saya selalu berusaha keras', 'Saya suka pergi ke pesta-pesta dan berada di antara orang banyak', 'aktif', '2026-02-18 06:33:24'),
(150, 'KEPRIBADIAN2', 22, 'Saya suka memimpin orang lain', 'Saya memiliki ingatan yang kuat', 'aktif', '2026-02-18 06:33:24'),
(151, 'KEPRIBADIAN2', 23, 'Saya suka melakukan hal-hal yang berbeda dari yang lain', 'Saya suka menyelesaikan pekerjaan yang sudah saya mulai', 'aktif', '2026-02-18 06:33:24'),
(152, 'KEPRIBADIAN2', 24, 'Saya ingin orang-orang mengenal saya', 'Saya ingin menjadi orang yang sangat berhasil', 'aktif', '2026-02-18 06:33:24'),
(153, 'KEPRIBADIAN2', 25, 'Saya suka menjadi bagian dari suatu kelompok', 'Saya suka memimpin orang lain', 'aktif', '2026-02-18 06:33:24'),
(154, 'KEPRIBADIAN2', 26, 'Saya suka menjalin hubungan pribadi yang akrab dengan orang lain', 'Saya ingin agar orang-orang memperhatikan apa yang saya lakukan', 'aktif', '2026-02-18 06:33:24'),
(155, 'KEPRIBADIAN2', 27, 'Saya suka melakukan hal-hal yang baru dan berbeda', 'Saya suka berada di tengah-tengah orang banyak', 'aktif', '2026-02-18 06:33:24'),
(156, 'KEPRIBADIAN2', 28, 'Saya suka menonjolkan diri', 'Saya senang jika orang lain menganggap saya sebagai teman yang akrab', 'aktif', '2026-02-18 06:33:24'),
(157, 'KEPRIBADIAN2', 29, 'Saya ingin atasan saya menyukai saya', 'Saya suka melakukan sesuatu dengan cara saya sendiri', 'aktif', '2026-02-18 06:33:24'),
(158, 'KEPRIBADIAN2', 30, 'Saya suka melakukan pekerjaan yang sudah jelas instruksinya', 'Saya suka membalas dendam bila saya disakiti', 'aktif', '2026-02-18 06:33:24'),
(159, 'KEPRIBADIAN2', 31, 'Saya berusaha sangat keras', 'Saya suka mengerjakan pekerjaan-pekerjaan yang membutuhkan ketelitian', 'aktif', '2026-02-18 06:33:24'),
(160, 'KEPRIBADIAN2', 32, 'Saya senang jika orang-orang menganggap saya seorang pemimpin', 'Saya melakukan segala sesuatu secara tertib dan rapi', 'aktif', '2026-02-18 06:33:24'),
(161, 'KEPRIBADIAN2', 33, 'Saya suka membuat keputusan sendiri', 'Saya seorang yang tenang dan tidak mudah terganggu', 'aktif', '2026-02-18 06:33:24'),
(162, 'KEPRIBADIAN2', 34, 'Saya suka melakukan hal-hal yang lucu', 'Saya berusaha keras untuk menjadi yang terbaik', 'aktif', '2026-02-18 06:33:24'),
(163, 'KEPRIBADIAN2', 35, 'Saya senang melakukan pekerjaan secara bersama-sama dalam kelompok', 'Saya senang memberi tahu orang lain bagaimana mereka harus bekerja', 'aktif', '2026-02-18 06:33:24'),
(164, 'KEPRIBADIAN2', 36, 'Saya ingin orang lain sangat menyukai saya', 'Saya senang diperhatikan orang lain', 'aktif', '2026-02-18 06:33:24'),
(165, 'KEPRIBADIAN2', 37, 'Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda', 'Saya suka berada di tengah-tengah orang banyak', 'aktif', '2026-02-18 06:33:24'),
(166, 'KEPRIBADIAN2', 38, 'Saya senang sekali menjalin hubungan pribadi yang akrab', 'Saya suka mencoba hal-hal baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(167, 'KEPRIBADIAN2', 39, 'Saya menyukai pujian dari atasan saya', 'Saya senang jika orang lain menganggap saya sebagai teman akrab', 'aktif', '2026-02-18 06:33:24'),
(168, 'KEPRIBADIAN2', 40, 'Saya suka mengikuti peraturan dengan sungguh-sungguh', 'Saya suka melakukan sesuatu dengan cara saya sendiri', 'aktif', '2026-02-18 06:33:24'),
(169, 'KEPRIBADIAN2', 41, 'Saya berusaha keras', 'Saya sangat tenang dan tidak pernah merasa kesal', 'aktif', '2026-02-18 06:33:24'),
(170, 'KEPRIBADIAN2', 42, 'Saya senang jika orang-orang memilih saya sebagai pemimpin mereka', 'Saya suka mengerjakan pekerjaan yang membutuhkan ketelitian', 'aktif', '2026-02-18 06:33:24'),
(171, 'KEPRIBADIAN2', 43, 'Saya suka melakukan hal-hal yang berbeda dari orang lain', 'Saya menjaga agar barang-barang saya tetap rapi dan bersih', 'aktif', '2026-02-18 06:33:24'),
(172, 'KEPRIBADIAN2', 44, 'Saya ingin agar orang-orang memperhatikan saya', 'Saya selalu menekuni suatu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(173, 'KEPRIBADIAN2', 45, 'Saya suka bekerja sama dengan orang lain dalam kelompok', 'Saya ingin menjadi orang yang sangat berhasil', 'aktif', '2026-02-18 06:33:24'),
(174, 'KEPRIBADIAN2', 46, 'Saya ingin agar orang-orang sangat menyukai saya', 'Saya senang jika saya dapat membantu orang lain membuat keputusan', 'aktif', '2026-02-18 06:33:24'),
(175, 'KEPRIBADIAN2', 47, 'Saya senang mencoba hal-hal yang baru dan berbeda', 'Saya ingin orang-orang memperhatikan apa yang saya lakukan', 'aktif', '2026-02-18 06:33:24'),
(176, 'KEPRIBADIAN2', 48, 'Saya senang sekali menjalin hubungan pribadi yang akrab', 'Saya suka bekerja dalam suatu kelompok', 'aktif', '2026-02-18 06:33:24'),
(177, 'KEPRIBADIAN2', 49, 'Saya sangat menginginkan pujian dari atasan saya', 'Saya merasa terganggu bila saya tidak disukai orang lain', 'aktif', '2026-02-18 06:33:24'),
(178, 'KEPRIBADIAN2', 50, 'Saya suka mengikuti petunjuk atasan dengan patuh', 'Saya senang mencoba pekerjaan-pekerjaan yang baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(179, 'KEPRIBADIAN2', 51, 'Saya selalu berusaha keras', 'Saya suka memberi tahu orang lain bila mereka menjengkelkan saya', 'aktif', '2026-02-18 06:33:24'),
(180, 'KEPRIBADIAN2', 52, 'Saya suka membuat orang lain melakukan apa yang saya inginkan', 'Saya adalah orang yang tenang dan tidak mudah terganggu', 'aktif', '2026-02-18 06:33:24'),
(181, 'KEPRIBADIAN2', 53, 'Saya suka melakukan hal-hal yang berbeda dari orang lain', 'Saya suka mengerjakan pekerjaan yang membutuhkan ketelitian', 'aktif', '2026-02-18 06:33:24'),
(182, 'KEPRIBADIAN2', 54, 'Saya senang diperhatikan oleh orang lain', 'Saya melakukan segala sesuatu secara tertib dan rapi', 'aktif', '2026-02-18 06:33:24'),
(183, 'KEPRIBADIAN2', 55, 'Saya suka bekerja dengan orang lain dalam suatu kelompok', 'Saya memiliki ingatan yang kuat', 'aktif', '2026-02-18 06:33:24'),
(184, 'KEPRIBADIAN2', 56, 'Saya senang menjalin hubungan pribadi yang akrab dengan orang lain', 'Saya selalu menekuni suatu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(185, 'KEPRIBADIAN2', 57, 'Saya suka mencoba hal-hal baru dan berbeda', 'Saya berusaha keras untuk menjadi yang terbaik', 'aktif', '2026-02-18 06:33:24'),
(186, 'KEPRIBADIAN2', 58, 'Saya suka menonjolkan diri', 'Saya suka membimbing orang lain mengambil keputusan', 'aktif', '2026-02-18 06:33:24'),
(187, 'KEPRIBADIAN2', 59, 'Saya ingin agar atasan menyukai saya', 'Saya ingin agar orang-orang memperhatikan saya', 'aktif', '2026-02-18 06:33:24'),
(188, 'KEPRIBADIAN2', 60, 'Saya suka mengikuti petunjuk-petunjuk dengan patuh', 'Saya suka bekerja sama dengan orang lain dalam kelompok', 'aktif', '2026-02-18 06:33:24'),
(189, 'KEPRIBADIAN2', 61, 'Saya berusaha sangat keras', 'Saya merasa terganggu bila saya tidak disukai orang lain', 'aktif', '2026-02-18 06:33:24'),
(190, 'KEPRIBADIAN2', 62, 'Saya suka memberi tahu orang lain apa yang harus dilakukan', 'Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(191, 'KEPRIBADIAN2', 63, 'Saya suka melakukan hal-hal yang berbeda dari orang lain', 'Saya suka menyatakan ketidaksenangan saya secara langsung', 'aktif', '2026-02-18 06:33:24'),
(192, 'KEPRIBADIAN2', 64, 'Saya senang jika orang-orang memperhatikan saya', 'Saya adalah orang yang tenang dan tidak mudah terganggu', 'aktif', '2026-02-18 06:33:24'),
(193, 'KEPRIBADIAN2', 65, 'Saya suka bekerja dalam suatu kelompok', 'Saya suka mengerjakan pekerjaan-pekerjaan yang teliti', 'aktif', '2026-02-18 06:33:24'),
(194, 'KEPRIBADIAN2', 66, 'Saya senang menjalin hubungan pribadi yang akrab', 'Saya menjaga agar segala sesuatu tetap rapi dan bersih', 'aktif', '2026-02-18 06:33:24'),
(195, 'KEPRIBADIAN2', 67, 'Saya suka mencoba hal-hal yang baru dan berbeda', 'Saya memiliki ingatan yang kuat', 'aktif', '2026-02-18 06:33:24'),
(196, 'KEPRIBADIAN2', 68, 'Saya suka menyatakan ketidaksenangan saya secara langsung', 'Saya selalu menekuni suatu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(197, 'KEPRIBADIAN2', 69, 'Saya ingin agar atasan menyukai saya', 'Saya berusaha keras untuk menjadi yang terbaik', 'aktif', '2026-02-18 06:33:24'),
(198, 'KEPRIBADIAN2', 70, 'Saya suka mengikuti petunjuk-petunjuk dengan patuh', 'Saya suka membantu orang lain mengambil keputusan', 'aktif', '2026-02-18 06:33:24'),
(199, 'KEPRIBADIAN2', 71, 'Saya selalu berusaha keras', 'Saya ingin agar orang-orang memperhatikan saya', 'aktif', '2026-02-18 06:33:24'),
(200, 'KEPRIBADIAN2', 72, 'Saya suka memberi tahu orang lain apa yang harus mereka lakukan', 'Saya suka bekerja sama dalam kelompok', 'aktif', '2026-02-18 06:33:24'),
(201, 'KEPRIBADIAN2', 73, 'Saya suka melakukan hal-hal yang berbeda dari orang lain', 'Saya merasa terganggu bila orang lain tidak menyukai saya', 'aktif', '2026-02-18 06:33:24'),
(202, 'KEPRIBADIAN2', 74, 'Saya senang jika orang-orang memperhatikan saya', 'Saya senang mencoba hal-hal baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(203, 'KEPRIBADIAN2', 75, 'Saya suka bekerja dalam kelompok', 'Saya suka menyatakan ketidaksenangan saya secara langsung', 'aktif', '2026-02-18 06:33:24'),
(204, 'KEPRIBADIAN2', 76, 'Saya senang menjalin hubungan pribadi yang akrab', 'Saya adalah orang yang tenang dan tidak mudah marah', 'aktif', '2026-02-18 06:33:24'),
(205, 'KEPRIBADIAN2', 77, 'Saya suka mencoba hal-hal baru dan berbeda', 'Saya suka melakukan pekerjaan-pekerjaan yang teliti', 'aktif', '2026-02-18 06:33:24'),
(206, 'KEPRIBADIAN2', 78, 'Saya suka menyatakan apa yang saya rasakan secara langsung', 'Saya menjaga agar segala sesuatu tetap rapi dan bersih', 'aktif', '2026-02-18 06:33:24'),
(207, 'KEPRIBADIAN2', 79, 'Saya ingin atasan menyukai saya', 'Saya memiliki ingatan yang kuat', 'aktif', '2026-02-18 06:33:24'),
(208, 'KEPRIBADIAN2', 80, 'Saya suka mengikuti peraturan dengan sungguh-sungguh', 'Saya menekuni satu pekerjaan sampai selesai', 'aktif', '2026-02-18 06:33:24'),
(209, 'KEPRIBADIAN2', 81, 'Saya selalu berusaha keras', 'Saya suka membantu orang lain mengambil keputusan', 'aktif', '2026-02-18 06:33:24'),
(210, 'KEPRIBADIAN2', 82, 'Saya suka memberi tahu orang lain apa yang harus dilakukan', 'Saya ingin agar orang-orang memperhatikan saya', 'aktif', '2026-02-18 06:33:24'),
(211, 'KEPRIBADIAN2', 83, 'Saya suka melakukan hal-hal yang berbeda dari orang lain', 'Saya suka bekerja dalam kelompok', 'aktif', '2026-02-18 06:33:24'),
(212, 'KEPRIBADIAN2', 84, 'Saya senang jika orang-orang memperhatikan saya', 'Saya merasa terganggu bila orang lain tidak menyukai saya', 'aktif', '2026-02-18 06:33:24'),
(213, 'KEPRIBADIAN2', 85, 'Saya suka bekerja dalam kelompok', 'Saya suka mencoba hal-hal yang baru dan berbeda', 'aktif', '2026-02-18 06:33:24'),
(214, 'KEPRIBADIAN2', 86, 'Saya senang menjalin hubungan pribadi yang akrab', 'Saya suka menyatakan ketidaksenangan saya secara langsung', 'aktif', '2026-02-18 06:33:24'),
(215, 'KEPRIBADIAN2', 87, 'Saya suka mencoba hal-hal baru dan berbeda', 'Saya orang yang tenang dan tidak mudah terganggu', 'aktif', '2026-02-18 06:33:24'),
(216, 'KEPRIBADIAN2', 88, 'Saya suka menyatakan ketidaksenangan saya secara langsung', 'Saya suka melakukan pekerjaan yang teliti', 'aktif', '2026-02-18 06:33:24'),
(217, 'KEPRIBADIAN2', 89, 'Saya ingin agar atasan menyukai saya', 'Saya menjaga agar segala sesuatu tetap rapi dan bersih', 'aktif', '2026-02-18 06:33:24'),
(218, 'KEPRIBADIAN2', 90, 'Saya suka mengikuti instruksi dengan patuh', 'Saya memiliki ingatan yang kuat', 'aktif', '2026-02-18 06:33:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','peserta') DEFAULT 'peserta',
  `jabatan` varchar(100) NOT NULL,
  `satuan_kerja` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 0,
  `pangkat_golongan` varchar(100) DEFAULT NULL,
  `status_tes` enum('belum','proses','selesai') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`nip`, `nama`, `role`, `jabatan`, `satuan_kerja`, `password`, `created_at`, `is_active`, `pangkat_golongan`, `status_tes`) VALUES
('10386446990001', 'Tester 1', 'peserta', 'pelaksana', 'BPS Prov. Sulawesi Utara', '$2y$10$6W2iiTQuEK/ZVYLGu3BJteEvl5TqjhTSqU2wGDVD9fxyIqsarBA.m', '2026-03-26 03:57:39', 1, 'Penata Muda / IIIa', 'belum'),
('12345678910', 'Pegawai', 'peserta', 'Kepala Ruangan', 'UPT', '$2y$10$1cqeOGNoDrFRQ689dzyDbe6.I8UVujCARzceD7sYPsCtHz.GUa20i', '2026-02-12 06:11:52', 1, NULL, 'belum'),
('196610261992121001', 'Ir. Royke Oct Rawung, MM', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$fWl.pOz24m0b81Vpb76EiunRpCNUOiHagNk3G8gXM5cZYWNaPyvAq', '2026-03-19 12:56:38', 0, 'Pembina Tk.I / IVb', 'belum'),
('196611291986032001', 'Norma Olga Frida Regar, S.Si., M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$.0ZOBXytA4gkb5E0humWwu7HKhKQEfwDjhzfhzEzkwIDfU/WgBa9e', '2026-03-19 12:56:38', 0, 'Pembina Utama Muda / IVc', 'belum'),
('196703261990031002', 'Marthedy Mormin Tenggehi, S.Si, M.Si.', 'peserta', 'Statistisi Ahli Madya', 'BPS Kota Manado', '$2y$10$7C7Ds0tpxX97XCiE8P/4MebHIpWtvvUztkIRaoh3nVRzWLbxGngGW', '2026-03-19 12:56:41', 0, 'Pembina Tk.I / IVb', 'belum'),
('196704301993031003', 'Raymond Audey Engelbert Kodoati, SE,M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Kab. Minahasa Utara', '$2y$10$MoQvZ1Mo3ENyegeo21uD0OmTvwskmSqo4x7LyN1HKaiN.OsJ8MA7q', '2026-03-19 12:56:40', 0, 'Pembina Tk.I / IVb', 'belum'),
('196808161994011001', 'Ir. James Koesbiantoro', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$GaFj7/ZSXXE2RLTmr/mjFenkLC0JXjqQf36lH.BdxMjEerC8/sC06', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('196808281994012001', 'Sirly Catharina Worotikan, SE, M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$WDp3x9Tf3vG7YpPpOXlQC.YSlcqwHLVlyci0/3T6qeorqJmPn6762', '2026-03-19 12:56:38', 0, 'Pembina Utama Muda / IVc', 'belum'),
('196810081989031004', 'Daniel Salu', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$.pp/7/tXFU6cWb3Biy2Ye.egE6l5Sdhqon9TqSMmOcnTMwPTCk79u', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('196811141989031004', 'Abdurrahim Unonongo, S.E.', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$YCFoHvGQ84CUsB3enUyRDe/x5tP2erd93dyVPURq1SH38rt5/IuI.', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('196812052007011006', 'Lasimran Tarib', 'peserta', 'Pelaksana', 'BPS Kota Bitung', '$2y$10$jQPCgVINagOykrhGLGu.D.yWVODJSqnI66GheP3fjUoSQE9OVhv9m', '2026-03-19 12:56:41', 0, 'Pengatur / IIc', 'belum'),
('196903031992101001', 'Yasir Akuba, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Kotamobagu', '$2y$10$ZCu.bLOsuTV0FVHKp9EnE.ciw6t2B/VU0iBLcidwX1diqAlsLgevC', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('196903162007011005', 'Jahrun Trumpi', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$U0xFJGOtFE83aq/gfCPzSOjKvxrm1RGEq4Hai8eIVp6ktJzIGFoZa', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('196903252025212006', 'Wiesye Dolontelide', 'peserta', 'Pengelola Umum Operasional', 'BPS Kota Manado', '$2y$10$V5cUdZ32KpHZOm4dMQMlp.FLPIMeOWPv9rzD.h3miM.uYNHphrX3m', '2026-03-19 12:56:41', 0, 'III', 'belum'),
('196904122003122003', 'Zukjan Tanijo, S.E.', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$k7AWC4IbP6ZGXX90TRmuFe7i82Eo11s.KF6q8CK7ODD3eO4qCjiGS', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('196907161990032003', 'Santje Magriet Prang, SE.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$knuN9msjIaChXJtHeydV7OnQMO.l0PA1vxH.u/RdjYDbvmKrede7.', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('196910161994032003', 'Siti Lusiana Tungkagi', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$76uF2/hNaq3LK6QPtBBztefYAVrTn1.e4fOE9S07zRepoiqIejTZq', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('196911011992022001', 'Vonny Joice Lalujan, SE', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$rKR1dqt432BflQ8NZzpxLuombxZ3gCD2ZJW2GyTEIKkdSngx5pj/W', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('196912201994011001', 'Jasni Makalunsenge, S.E., M.Si', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kota Kotamobagu', '$2y$10$efTmT5tc5Q8sMTM/UG9l4.PcE.L5357LkxVtbTtZqTI2hHxxckRnO', '2026-03-19 12:56:42', 0, 'Pembina Tk.I / IVb', 'belum'),
('197002042014061001', 'Febry Ashari', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$jXU/lZkiRU/kyYls.uI0au3DY8wSP9Fj6cctI3oHr8/tk2ROQGdYi', '2026-03-19 12:56:41', 0, 'Pengatur / IIc', 'belum'),
('197003231992111001', 'Jusran Zakaria, SE', 'peserta', 'Statistisi Penyelia', 'BPS Kota Manado', '$2y$10$k0fJBfxlC6Vh0onvbJ4wxuYgCmrK6uvVwzOnnscJzqECEukR3uDCq', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197007021992112001', 'Juliana Kalumata, SE', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Minahasa', '$2y$10$4IlgY1zf5AKPhAGJXP.ki.mfmkH0sXerPHAuQiVDden.NopEDY4.6', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197007031989031001', 'Janny Eduard Maliangkay, SE.', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Bolaang Mongondow', '$2y$10$wxQOmZr/Ku700A4zSrUmCe5bf6XUYTAEsBzfnKMIjXPhrRBO3RHt.', '2026-03-19 12:56:39', 0, 'Pembina Tk.I / IVb', 'belum'),
('197008011992102001', 'Augustin Sultje Selfana Pussung, SE.', 'peserta', 'Pelaksana', 'BPS Kota Bitung', '$2y$10$vhmjFWpE7oRSr1CpQ0uDB.gEKE9UqmNPkHS8qA0yvkigL1M84qDra', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197009141992032001', 'Sri Endang T Karim, SE', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Minahasa Selatan', '$2y$10$pZDD3u/62XjrvBD0tEG.2u0J76XsYnVJCPdroekrHBfbTuS9n9FHS', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197012032006041009', 'Muchlis Manoppo', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$ZDBBfrgu7S5Wf972lMUiGOFhuyp0AyyPGiLego8q3EMTD9MltujFi', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('197012112014102002', 'Diane Roosjefien Rondonuwu, S.P.', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$juufq1NUYRlv8jjZ1feUde3Xe.9nzihKfwIg2EPv4OfyCjSqremU6', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('197102051992022001', 'Johanna Maria Farida Tampemawa, S.E.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$9gEmAwJzeUdBjg4gdcA.5eWvxuou0E4klEkUHl.kNezFJiSH4GK/K', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('197103012025211011', 'Bram Henoch', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$6gq903v7Io5tr/sjSkKQOuzC4WfRXWVkUB1uVFqwon2lkVoUHXxCS', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('197103301991021001', 'Alfian Fransiscus Sumayku, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Bitung', '$2y$10$huabxIzTh2KsjSD4Qj1UGuwxi0f7r8iTbE5iho3Umw2wlqnaRt3Yi', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197106242025211004', 'Edwin Zeke', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa Utara', '$2y$10$7OtpcyO9qUWyOHuq2ulf/epTJvxUhdiyRUXYPTZI0NckYfGgZJ64u', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('197108112014101001', 'Alfie Gustaf Paul Sunkudon, S.Pd', 'peserta', 'Statistisi Penyelia', 'BPS Kab. Minahasa Selatan', '$2y$10$vC88c482uxefJNS6DGrAF.2XnYj4/EYudOPHV7MGyHbpwJtzMwN02', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('197108232014061001', 'Jantje Maycel Mamahit', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa', '$2y$10$NEBYiB0xTQ2RjyxOKr0BmuhG8RZ0EgI2flc9hgBiIV89XGbWnRV6y', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197201071994032001', 'Nik\'mah', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$vejBKWlpDa2Yir3rvhEApekZa7Dpy3mUpEcDIFs6cTAdjgRQjQsw6', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197201171992021001', 'Ahmad Handoko Lipoeto, SE', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kota Bitung', '$2y$10$MgeUcBRWOGUhVfQJ3EZCB.hUMPQxCmnONI8wYj8YbU2PwtpX9CELS', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197202132002122004', 'Ortje Manahampi', 'peserta', 'Pelaksana', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$WWWAdaGzV9kpRoVKIDkKfeJebe6irDg8wA0WFZ5.sJz8Ze.AQOseq', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197204021992022002', 'Altje Lontoh', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa Utara', '$2y$10$GuOPmG5qjsOzboEP8.ECq..ULT15.JbMvPzBnUf01/IYdEC2uiVbC', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197205281992021001', 'Rudy Helfrich Pomantow', 'peserta', 'Pelaksana', 'BPS Kota Bitung', '$2y$10$I9X1WIDjqXg4p9tjRLrjD./HMxQ13PFMgG7AnwGrtlWc0zBBJUYQO', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197207222025211012', 'Jimmy Herman Waturandang', 'peserta', 'Pengelola Umum Operasional', 'BPS Kab. Minahasa Utara', '$2y$10$IFEOOL6uGIl74TW.8Hauw.D2Hjh/tQAzexFf8NPwjZN137FU.gUru', '2026-03-19 12:56:40', 0, 'III', 'belum'),
('197209091992092001', 'Stela Engeline Doris Lomboan', 'peserta', 'Pelaksana', 'BPS Prov. Sulawesi Utara', '$2y$10$61.LfuPCrk24zGLdQFVctO8QiSwZ1d.YgMvzLd7b2Gu.II7qyOLG6', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197209212002121002', 'Ulung Rimbah', 'peserta', 'Pelaksana', 'BPS Kota Kotamobagu', '$2y$10$M0OZCtl5TIYrTyVqwrVZEujH9iWDqEcz.7KkY2fiH1FxiSLlPmmtu', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197209261996031002', 'Steven Bonny Reppi, S.E.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$bqQikvhl3ZkWxLfKZgacdOW5C6wEMgIq.mhrmycyq8wTDNRu8rHUO', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('197210072003121003', 'Jusmi Gumansalangi', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$aMS2fA6oSM2XmDtMA6Vd8uNvxHmzM3bvt8hgzz5IcIyOc41JErkgq', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197210092014102001', 'Lusine Karel', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$qJLmijZNFdyh6PMbw/uLWufkWI6vGBdmR2aJnRvk0LpJt5tnFqUzK', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197211151994031006', 'Jermias Oscar Jeffry Sahambangun, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$77r6VzvWpVm2Ni2oxFFA3OzBUkILKQM7xyxHyZPOY692MWlo9SXzu', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('197212232007011003', 'Herison H Gunde', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$OQ9US6o9Fv4ZNK6k7J38Ee.sba2eE91bJg7gCU4H1W1uCd71jHHme', '2026-03-19 12:56:39', 0, 'Pengatur Muda / IIa', 'belum'),
('197307291992102001', 'Linda Telly Oroh, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$GY6lF6GXrVpGHBU0xBY.vuSsgol/fe6Q0wIpkfnlnCaZdV7ofW3PG', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197308222025211011', 'Junaidi', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Kotamobagu', '$2y$10$ySIOvPojteIfr.zUgJO4YOYFBn/ZB7uQY7WAzID8lYBg19TCX1Z9G', '2026-03-19 12:56:42', 0, 'V', 'belum'),
('197309102025211025', 'Sem Ham Lumempow', 'peserta', 'Pengelola Umum Operasional', 'BPS Kab. Minahasa Selatan', '$2y$10$cu4priUgRsLZWEDsu1iH0.q96fIgLwG/fPAjlet4E5EQl0PXqy5n.', '2026-03-19 12:56:39', 0, 'III', 'belum'),
('197403242006042019', 'Irene Ruth Longkutoy, SH', 'peserta', 'Analis SDM Aparatur Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$zXbtLyxqz/tYc1enki5Fz.1XhHF7aSaCwJ.A9QhBQX0.nO0WWRIOq', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197406222001122001', 'Yunita Yetty Winangon, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab.Kepl.Sangihe', '$2y$10$sQcZAPKrTkPoSNj0x5ld9.0hs2.QDJStLmC5vnGYpDUtkLEKRK6xm', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197407271997121001', 'Abdullah Kango, S.Si, M.M', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Minahasa Utara', '$2y$10$zsYte39pjhCsHSdT6MIWZugBhcljJKOtvfbHzBn3IAvHAYj.3NAia', '2026-03-19 12:56:39', 0, 'Pembina / IVa', 'belum'),
('197409112003121004', 'Raymond Nica Seyun Kumolontang, S.Pi', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$VaWvKwG39pYe60qPyvyJ0ulgHcb2HTXczRMfeP1ysxLdxmbcAXwnK', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197409152007012002', 'Betty Andreita Worungan', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa Utara', '$2y$10$CFn6wBri1/I/7F.2qRW9Fuf8y04Ke7Lah2WNiD5PhuW0IujLm6hoO', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('197411072006042001', 'Noviastuty Indriasari, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$TAyAFEbD2VbbdatcjH6Fqen4.Ta9snn8SBaLdmKRuSBA4HRWW0RZe', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('197412311996121001', 'Agus Sudibyo, M.Stat', 'peserta', 'Kepala', 'BPS Prov. Sulawesi Utara', '$2y$10$InLsipmarnnY46F2WYCb9.cPDTXQZP4quigUsoAdUTHSvMiSqozQe', '2026-03-19 12:56:38', 0, 'Pembina Utama Madya / IVd', 'belum'),
('197501131997121001', 'Winarko, SST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kota Tomohon', '$2y$10$aT61s9d4Whc8KVSNZqqwTOKA2GTwWV8BCUa5iktpFX4INw9ECn8me', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('197507022007012002', 'Julia Taarae', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$8OFaAWcaMsuxWqUe8507sev7c5Dt2kveHoVjc0hOcWWTItQCqrddC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('197508312006041002', 'Agus Purwandi, S.Kom', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$qJ79hVTFEWd6fbMYUIRjFO5Xj0/4aSaedGFGLEsGgIlQECsev8Dk6', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('197509062005021001', 'Stefanus George Lumi, SE', 'peserta', 'Kepala Subbagian Umum', 'BPS Kota Bitung', '$2y$10$zH1RxL5N0RYldIqDGiYiPeisgKiG5lW7L9R/jBViyHLAnzwtmZlsq', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197602012014061001', 'Pradipto Ontorael', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$k/vUOD/I9jHCzivq34Clye7.67/p0GxEDJbUW2i39hwkEMUmTpAqG', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197604092006041016', 'Ryko Aprianto Puasa, SE', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab.Kepl.Talaud', '$2y$10$IcGJ0qtvJNH8RQcENwNp0e4QARTEU267uvWIHpKH1ymyN0d58qAGa', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('197607152025211008', 'Jelly Jevry Tapada, A.Md', 'peserta', 'Pengelola Layanan Operasional', 'BPS Kab. Minahasa Selatan', '$2y$10$aSPwFB5JHeyIRoFf/jEOHeJNXSH4EJLrhUGLC6UtA8LgDGgwoxzqe', '2026-03-19 12:56:39', 0, 'VII', 'belum'),
('197607232025211020', 'Ernes Kaumbur', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$y8im5irS3SDUrAwEKXNQs.7sXTPYzkun1saIwGXcmvMhIGFL36M1.', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('197609102001122006', 'Susana Ritha Lanny Rumangkang, SE', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$Qw1vlmP8dLIrdjBRpnHjFeQoOjUc7SNxNln4yQSKeG1PXdxnWymNS', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('197609242007011002', 'Steven Monintja', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow', '$2y$10$6AKo6UgViROIFASHEdwsIetCKFTQIsaw0UGPRyqKPSxUOVLRUa8dm', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197609242025211014', 'Saul Tatauhe', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Bitung', '$2y$10$9Sn8OgVD61HpDcVxCTBjROAt4HvrQEOfhGwCq4NNBTF.QsidVbK.K', '2026-03-19 12:56:41', 0, 'V', 'belum'),
('197610102006041003', 'Daniel Tri Hemawan, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$SyKiuu6OmujMY5vOn9ohdOyyAnd4z/EBgT43GfjEExsWWeoKTpG3y', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('197702192006042003', 'Inneke Meylinda Nender, S.P', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$G0EDvOy7fjFjeOpdZqzWu.EaKjXlNbWuEJkNJ287wLhQCnUPXg5dS', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('197706182001122002', 'Linda Diana Lumingkewas, SE', 'peserta', 'Statistisi Mahir', 'BPS Kota Kotamobagu', '$2y$10$Km1784uY4V31gH/1nrYjQuO0T/fC7jjnTOQnAf8mTL2NZTqa3D41C', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('197707202000121002', 'Rahmadi, S.ST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kota Manado', '$2y$10$sJ7GVFgTfZlUm0BhgrAIGOkKzIJiIBRqtEvK9ZH.u.HV5WBWR4KeC', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197710111999121001', 'Simon Andreas August Remiasa, S.ST, M.A.P', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kota Manado', '$2y$10$z9G0yhekAktHpIztfpq8G.tVPZrTpbS322zxxPy56A2vsduUcDz5O', '2026-03-19 12:56:41', 0, 'Pembina / IVa', 'belum'),
('197712202006041013', 'Demsi Andalangi', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$YAix9f3661JORnBjIHfrEOtfKZazelQKJs.TSiypcG1w1YIXpTa5i', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('197803192025211002', 'Hendra Peddro Riekho Sunkudon', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa Selatan', '$2y$10$p4rwvPaj2YkqAvjeeawyGOjJDVGk6vUunimmHM4onXjqFXpZobB.W', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('197805072006042002', 'Meitha Olivia Rumate', 'peserta', 'Pelaksana', 'BPS Kota Tomohon', '$2y$10$tsf3nraYccomE8.Ndx6.wejxUde/OtLtMV5Dw1lQVv6QHnQ0sqrYm', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('197805202014061003', 'Jemi Fesly Ambat', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$Gd/2w8nCYJnSl.gII1AN4eKNRRtqTbKgoMVJ/6nooBge2vEEFPdp2', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197808212006042005', 'Sendy Oroh', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa', '$2y$10$Xj7knKiiApqK3J9VPKQMOu0JoY7akFmJfTT94.m1P2.KV2a4ncGbS', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('197809252006042019', 'Reyske Meiyty Jacobs', 'peserta', 'Pelaksana', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$oXxSeJEih2hdcbjTkDLeR.LIVUcdl5nFARqGDfanIHydGYk.q78xi', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('197810092025211011', 'Menahem Beggin Janis', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Sangihe', '$2y$10$YATX/C5amP/c6A85rwcJkeBpdHQMVlCOGivtTM6nrGg98s74GYSJq', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('197902091999031002', 'Rikky Marco Williem Jotam Rugian', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$DCOGLmBUefGmRr1nrnVO8ONnIPyx.SRX3zM.ZiBP1fnL.Pz5IXSzK', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('197904062003121004', 'Steven Kalvin Montolalu, S.E.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$g6fM07VNI3sillHdHz8yPeW/v5scWp01REpKPFMlEBV/1wkP/hdnu', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('197905232005021001', 'Syamsul Mahar Ishak', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$tT8Lq.BjNIlr1HHcEFNqZ.1TeauLFeeF2gcbB/dpNIBvUmSsWc7N2', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('197907112014101001', 'Edwin Franke Lantang', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa', '$2y$10$zFlnvreWddyawmDUpADhL.3VC.m0iyRESqaJXpPLEJUMSH6ol8uGq', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('197908102006042005', 'Verra Margaret Goni, S.E.', 'peserta', 'Pranata Keuangan APBN Mahir', 'BPS Kota Tomohon', '$2y$10$du8Z3NV6vAvC7ggEzSWmMOdANJ9c8AOaaIiTG5HPfUiwdemgQGRY.', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('197909192025211040', 'Mustari Mokodompit', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Bolaang Mongondow', '$2y$10$DoCt/QXJNEFLa3fgU8t8beYbUvsRlizdpGM1bvYfzEW6INaYBOPva', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('197910052025211052', 'Audy Fouke Mokoagouw', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa', '$2y$10$JqfcxJOWDlscD32MOIe99.j7ymq.QKdpG5VwZnC3jEiq3XxDxAF5S', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('197912072010032001', 'Deyske Puasa, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$VTpd0B4E6rq2tjPHPKMneeeHG6bxKRmc91pT.xX7mGh27zXBZQYwK', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('197912192014062002', 'Deyvi Stella Rumangkang, S.E.', 'peserta', 'Statistisi Mahir', 'BPS Kota Manado', '$2y$10$lOsmDSM7kkmUd3mPxi51Zu0byhlCnLu5ybWXHZw3KaWYosuRhiyZ2', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('197912222003122009', 'Prih Wismaningsih, SST, M.Stat', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Bitung', '$2y$10$vso45Iu58wbxlwVwuQomPeMqYueb399SfiHdG2InTyghhl4glHtWS', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('197912241999031001', 'Djon Very Manontonau Lahamendu', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$lJyuHL.Tf3e2br2vet3bmufRn9ZPRnL8HfuOCpMst.g/IV9kJnH2W', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198001022002121003', 'Anton Tri Wijayanto, S.ST, M.Si.', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$z.12ggMn90R/CeYm38k0JOwjCCdtlYLdsoI0AO1CZ6fjGVlF9jHPq', '2026-03-19 12:56:38', 0, 'Pembina Tk.I / IVb', 'belum'),
('198002162007012001', 'Fin Mangendai', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$Kx2Pf11AIzcgZ9SEgEFcqe8tVjuacx1VIQp3kpJ4gb.xfRkvNeYUC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198003042007011002', 'Ekstipan Riung Mahda', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$h8zflIZQKzQ7UaoVJ2LZAumcAijfDknrCp8fJlLKBPAhofxTmX4Ym', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198004082025211012', 'Sony Nesare', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$bVPGNMEkC.lYnhp1iYipUelVUzz1HALZa3ejC1K4MvixtgEdStNTq', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('198005112002121003', 'Johanes, S.ST', 'peserta', 'Statistisi Ahli Madya', 'BPS Kab. Minahasa Selatan', '$2y$10$qSZvmL2g1FjHf0OJ6SNZb.ujK4Vc6KWNNH6NN7Hdzh3c4.4tQGpt2', '2026-03-19 12:56:39', 0, 'Pembina / IVa', 'belum'),
('198005252002122003', 'Titien Kristiningsih, SST., SE., M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$VGgVaM5rEuDh.HRvN/i/D.ln52JbUrL0NrBbzfeQ21EaXyT/O6d7G', '2026-03-19 12:56:38', 0, 'Pembina Tk.I / IVb', 'belum'),
('198006212008012018', 'Yulita Liroga', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$EgQoEV/c2P0u4PKOojdDFOmMchelD2kDbNMT8CZ5nkbY81feF90E6', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198011172010032002', 'Vivi Novita Sumampouw, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$rSFixISyCYDKU05V4nTSDe7CH5vq.BM5lzFHeny2LIYcHMjmDzpe2', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198012162011011003', 'Ricky, A.Md', 'peserta', 'Statistisi Mahir', 'BPS Kota Manado', '$2y$10$uxnkRZ4HenHGsiOOX70jFum1uLkhmSct0yhpHPOIe/.mOo0zUiKt.', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198012242025212008', 'Natalia Merry Christie Pesik', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa', '$2y$10$00Yp/jVHCc6zP8DBf/nFu.ft5QNS62cE.JxahqIVa/bTj.wP.vzEu', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('198101262011011005', 'Zaenuri Putro Utomo, S.Si., M.Eng.', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$Roi3enmRHpuGri5e0YyJc.4mw8qR0dU37.wKzd0KlssuVMXGu5lyi', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198102212002121003', 'Febry Leonard Oroh', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa Tenggara', '$2y$10$./7pcpcfu/W0WZLqh47nnunHxPlH7zwemnmyRzdHSj5Eb1iRJF4pu', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198103132011012005', 'Meidy Talingkas, S.Si,M.Ec.Dev', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Bitung', '$2y$10$uxq/Nb8XIic7kZQ/P/4eROV13Xf9TisKVmdzYnqlYEUEEMBHyTWOu', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198103282006021001', 'Viktor Prima Sirait, SST, M.S.E', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$RinqkoF1QZtxfH8EBB1HVuPdc2Tqm./Ue2JTeMR87j8OHV7gvf4BS', '2026-03-19 12:56:38', 0, 'Pembina / IVa', 'belum'),
('198104042011011005', 'Bambang Suwarno, A.Md', 'peserta', 'Statistisi Mahir', 'BPS Kab. Minahasa Utara', '$2y$10$5WpHxaqLr.YIEVSG7PQ3huQF6IRMmJ1ENVYSU6G1lJRh109FEfOi.', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198105122010032001', 'Novi Yanty Mamangkey, SP', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$KzJalAxfx2K7gJK1nOP48OlEN1BKFHmValPYInKuJAhIITychdeOq', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198106042009021007', 'Eko Setyo Budi, S.H, M.H.', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Minahasa', '$2y$10$OpRa/3bYvkxIJtBadVtLv.wb4dKl4br95GBZskK3YObfZSB53tqmu', '2026-03-19 12:56:39', 0, 'Pembina / IVa', 'belum'),
('198107062003122005', 'Irena Listianawati, SST, SE.,M.Si', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Minahasa Selatan', '$2y$10$/POxLMMTw3fV/OhsbAPkUefF0Cq1M8qw/ahOHr7fskvIWeuHeJzEC', '2026-03-19 12:56:39', 0, 'Pembina Tk.I / IVb', 'belum'),
('198107272010032003', 'Suharsi Dewi Yuliandari, SE, M.E. v', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab.Kepl.Sangihe', '$2y$10$rQeteiuATUoVgEUSL6mVJ.Nk8Vx0PvX4TDVg5ST4PDz7zMKY/5jsK', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198108212010031001', 'Kharis Metanoia Hendrik Yeremias Mare, SE', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$SlfSMFZPp7wEaJZpICK46eKWGFYQMDN7YFNrwYVIj2E4u1VHZodVq', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198109142007012001', 'Sutriwati Daulu', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$xIgzdZAa3KXeSeehGYEPs.IUBkHD74NN0BnFerlXFAc848E2NDwHK', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198110042008012013', 'Olivia Inggrit Waturandang', 'peserta', 'Pranata Keuangan APBN Terampil', 'BPS Kota Bitung', '$2y$10$rqsmeZrjHSo1kytJ4hoRFeKbTinBKcB3KwcsuGivtbCpRmzanox0u', '2026-03-19 12:56:41', 0, 'Pengatur Tk.I / IId', 'belum'),
('198110082010032001', 'Olha Makdalena Kaparang, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$iOsUbYDx.VDl14nULgfJCeu2Zlqerlbv17w3rFp7h9z7zqheyuNjW', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198110162007011002', 'Reyf Reymond Kumontoy, S.E.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$O6zmA3r8tQJibFgOqDu58.0zl5TJSm8P5ER2vhLNeuP5Ajg1Uusii', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('198110312007012001', 'Lazia Outenty Bimbangnaung', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$AaJkG2BKv0.fBOK/zoUwlO0y97DJqthdsmt00DQezwzxTV5vg9l1i', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('198111032011011008', 'Rezza Dhamziq, S.P', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$LeKRvygLKyCgkAUiOyWYy.UI/Z7KHdbHnDLuPripYFjEOxjLgwI1y', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('198111192014101001', 'Erwin Arifin Makadapa', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$508aizWpp4oLRAKTqs9mC.YNUffxeu3N.gf9beuKFOhNRydyq6sCm', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('198112182003121002', 'Carlos Erickson, SST., M.Si.', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kota Bitung', '$2y$10$LvrtGFZ4jVgAokQXICF9juThKoSC8J1Fg9JId7sTSYTrNu.j5R53y', '2026-03-19 12:56:41', 0, 'Pembina Tk.I / IVb', 'belum'),
('198112232025212010', 'Mellanny Marry Christ Na', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa', '$2y$10$hcvN04kEptsokhWxmnWnVO1WHgkX0KqP20Qkg2IwnOfzJGay7D2jy', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('198201202008012012', 'Yanti Jane Ivonne Kaeng, S.M.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$zEFoLRYVTrhGtEMqQhzFbeXqhTC3gCHv.iqbE64O6ljmWyCCTsQTC', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('198202082010032003', 'Sastadeferah Risab, S.Si', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$FM8pFE2VM4pinYQU3qOFruMs1KnRH0KG9H9yGXYFhIRqiIxVsa2Ay', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198208222008012014', 'Agnes Marlise Oroh, S.M.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$d6F9IcRSQs0oddtnz8uX8.lt7NyyCOFD/M.nNNsP/iiporwDbZJZO', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('198209182008012012', 'Bregitta Sisilia Lasut, SS', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$kYkzxr6yrDXk0lhbR8JfgeF1shV5k7PkkddRQ/5ei2I4nViURh/I6', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198209212008011011', 'Stanly Mokodompis, S.Si', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kab.Kepl.Sangihe', '$2y$10$2G4DYFdJQZ/fTuPvRf7r4ueMYyz5eGWwXpKZxRzeEehYJNgviLBF2', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198210022010032001', 'Elrini Diane Wuisan, S.E', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$Wg4rkRxQRyqva6w13ywSJeW2Y4Hge5sDfBb3GaCrSG7v5Z.XEJKtO', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198211262011011007', 'Priska Harto Lolowang, SH', 'peserta', 'Analis SDM Aparatur Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$7wLHY7v1yLyMCeD.yopuq.oJgbnosoOgwYmrNaswbgY7iRdTfwvNi', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198211292011011010', 'Ries Afmi Nicky Manorek, S.E', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Bolaang Mongondow', '$2y$10$Em7uf8okzmQNL9BkdAoKW.ca56yezso/73gO2KJtxoBCAxvGJ4Hu2', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198301232010032002', 'Tirsa Poli, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$8UkqnyHqSKxRfpXIpiJAje4TjWUqlRi0hQTeAHXnle2ngHEPaZaJu', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198301242025212029', 'Maltina Dali', 'peserta', 'Pengelola Umum Operasional', 'BPS Kota Kotamobagu', '$2y$10$jCvU7RFoCzz2fYIjxhcuzujOl.8k/H2vQN44wPDKQ2cm9kNts0RbC', '2026-03-19 12:56:42', 0, 'V', 'belum'),
('198301252003122001', 'Indira Anastasia Lolowang, SE, M.M.', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$YSZEoGXwvV1fERLAZyqBlOvYExYXQtR2BeGW5U7AUDx1fBw88NIy.', '2026-03-19 12:56:41', 0, 'Pembina / IVa', 'belum'),
('198301312006041005', 'Kurniawan Winston Walangitan, SE', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$rbsjud9xJy79RKuakGF/wuh2m/6RQN2lW/tcbYc3DmqSZ1.KR8ZI.', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198302232002122001', 'Fabiola Sanger, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$EUC9SunhdexrWz3g0JLUTOGAL0ehsQNtbgUxkN9vaUT3ayjxwlrbu', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198303242025211028', 'Kamaludin Djafar', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$vMnfUzYg/wnodoTctQxGruI0uP3/CA9JsulveBlSqVkS8RjYdkYBG', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('198304112005022001', 'Mia Wahyumiranti, S.M.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kota Manado', '$2y$10$inpDBZWvDn0H41Cr8/F/ZOJipAu3eX74eDp5dATCCSi6z3NCiksKu', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198304242010032003', 'Sany Herlina Lendo, SP', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Kotamobagu', '$2y$10$HzBGNUxocUuhVzFqBxdNH.QjfcjciO7d4u5spZLDW54OYGIwfpO9O', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('198307052003121004', 'Hendri Janis', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Sangihe', '$2y$10$vsIo9rVvTpOb3ZBO5DARoeh0wKYgZr9C02vUR7LoF.zzHq5f50aDe', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198307062011012015', 'Joice Juliana Koyongian, S.M.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$BC1wLmZCHIg/Gq6mWnv5TOiPij7lmjvrHVULElO6GpSCyKupNgH9m', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198307162010032002', 'Ireyne Tamburian, S.Sos', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$ShKZB8yjiyTb2MlhekNHQOZ15EpAsAkLfBxK55OZ/GOZ3214NhkWG', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198308132003121003', 'Augresyo Lucky Johanes Walewangko, S.Kom', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kota Tomohon', '$2y$10$b1CDSlJX8fLOjWNPupiLZu7e5IL2gvfvp5/a9ZX5rbHVXeKwCfYZW', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('198309182005021001', 'Steven Minggus Tandayu, SP', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$qLvv00fGQP3Ufp4UUCoEguZFdXYpclvagxBgbIUIi.D3u8vmeMSdK', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198309182010031001', 'Semuel Sofian Tujuwale, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$OebAhZm5Aqsnjikw45DvK.kqnNlt1KE.EgT5MmUlc1.Mg.2AgRpdu', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198402282008011002', 'Yandi Liwe', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa Tenggara', '$2y$10$QNgc7K0xlKOB/MIMRRMjiuQ/UxOBQBux9mS5BK1GqrvkHCNWzWKKC', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('198403232007012003', 'Inke Margareth Tambeo, SST, M.Ec.Dev', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$AYzj8.gBqqTI3Q3cyYZE6eKOxXOxJhMpE5J19.3dVktYLfbKfD47q', '2026-03-19 12:56:40', 0, 'Pembina Tk.I / IVb', 'belum'),
('198404092024211002', 'Michael Yosafat Dumumpe, A.Md.Kom', 'peserta', 'Pranata SDM Aparatur Terampil', 'BPS Kab.Kepl.Sangihe', '$2y$10$OhBBv/bzApLhMZb3hUPF4.YTjT5nZ13..a083AHiWczEHwOFHrf7W', '2026-03-19 12:56:39', 0, 'VII', 'belum'),
('198405132011012012', 'Meri Yohana Singkara, A.Md', 'peserta', 'Statistisi Mahir', 'BPS Kab.Kepl.Talaud', '$2y$10$38Dgsgw8LUU3CqOvYiOr4uG1crdHijWMhRJCYndTMpt070sivEAfO', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198405302007011003', 'Bhayu Prabowo, SST, M.Ec.Dev', 'peserta', 'Kepala Bagian Umum', 'BPS Prov. Sulawesi Utara', '$2y$10$A4U29mi5qIZ7cLQsNO0pQOCRVMaZd5SKrCc8QkF51rNGsoACqwmnC', '2026-03-19 12:56:38', 0, 'Pembina / IVa', 'belum'),
('198406092010031001', 'Jimmy Ferdinan Mamahani, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$DhPMrbAetCfywqiJFQogi./xyoL9PFk5UyUFbRBVN98o8gJ1Baoe.', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198407142025211047', 'Fredy Sumitro Mokodompit', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$OkoMiqZtaK6AiWIxyJmaneuj/PD.a9Rnwl.nfKKM6wo/HOYUw.zTa', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('198410292006041004', 'Berliand Fadly Igir, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Tomohon', '$2y$10$GsRXi6VgPX8s0ncd83o6Ze.GJoe.cuir8BoScdPF7h9YxTsULxdk6', '2026-03-19 12:56:42', 0, 'Penata / IIIc', 'belum'),
('198411302010032001', 'Anastasya Wowompansing, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Bitung', '$2y$10$5vr.9Vq5BmcMv39YMuHsneDvrYzS6OJC9Uu4yZfCZZO/aC7ZwE29S', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198501292010032002', 'Joice Rorimpandey, A.Md', 'peserta', 'Statistisi Mahir', 'BPS Kab. Minahasa Selatan', '$2y$10$41HnHmrz1lbI..N85nZUVOsIxbhXc1Tz0cUVX2a68SiN6jeFCPq8m', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198502042007012003', 'Purnama Cahya Sari Silalahi, SST, M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Kota Tomohon', '$2y$10$OV6siSX/ARP6nSsfT3X9BewryXID9ofPigooXeQb3VEjPkuP8dCw.', '2026-03-19 12:56:41', 0, 'Pembina / IVa', 'belum'),
('198502132011012017', 'Uyun Racmawati, S.Psi', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$GXYZD4ynWBH6vaUiLS2KMe6Md7c.w5bCmHnQ0R5xcEwLS.bL4yblm', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('198503032014062001', 'Marni Lodia Wuisan', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$clVrkgu8EO3mqqfe8G9j5.Jyc.aN1gBiRnD3vtCC9aZhddsDEsMYK', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('198503292025211034', 'Handri Mewar Karim', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Manado', '$2y$10$yo.GmSqsA/wvxWOYD5PlhOOLU/4xzPBziq9bLJRdcnjex//s6cCZq', '2026-03-19 12:56:41', 0, 'V', 'belum'),
('198504112011011009', 'Radjid Dwi Iskandar, S.M.', 'peserta', 'Arsiparis Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$SCllA/kTBZv8npzPV2QKX.YGKyi6pDEaAhH0o7HuwEBSx3lZfEltG', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198504132025211036', 'Raiin Pitoy', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Tomohon', '$2y$10$WgdkxC.sLFHPU6ZRwunve.askACL6IBnECdAGhKkH3Lz3oiMFvWy2', '2026-03-19 12:56:42', 0, 'V', 'belum'),
('198504202008011003', 'Eko Siswahto, SST, M.SE', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab.Kepl.Sangihe', '$2y$10$/Yky/uMRvRFE2Gb9jvUhKOwBVAaLoEIkQ4fmEndgFKW/TQob9Ih.S', '2026-03-19 12:56:39', 0, 'Pembina / IVa', 'belum'),
('198505032010032001', 'Srimaryati Une, S.Si', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$IBEJ64wI6O1SqlWVcOYyl.gFt5TT5wSH2SzCT/ZW9JDoL43DlxVXW', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198505172010032003', 'Henny Pura, ST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$O3QQFZvhvh0L.87b1dhSR.T5DOJ8ba8iQh81wtS8eOg42UXSuwokC', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198505262008012001', 'Ratna Sulistyowati, SST, SAB, M.Si', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$s6cwX.VaOy5KyoSrXZ4f5eEwhZkuHL43OLLx1PjQybKF4gvhpXmm.', '2026-03-19 12:56:38', 0, 'Pembina / IVa', 'belum'),
('198507012006042001', 'Julianti Hartanto', 'peserta', 'Pelaksana', 'BPS Kota Manado', '$2y$10$Lolt/pVdhaTxWvjb1b2IxOU8HFwgPWyHAMsN2dqRVWqAJ.H8FiWfC', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('198508172010032002', 'Trisylia Augina Merdeka Putri Pangemanan, A.Md', 'peserta', 'Pranata Keuangan APBN Mahir', 'BPS Kab. Minahasa Utara', '$2y$10$Ft0uV6vr/kM5r5LUe8rDoO7Q7mTWKwgynGLsNlx/DAYOnd4r2dpxy', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198508192008012004', 'Retno Nurratri Woroastuti, SST', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa Utara', '$2y$10$xB6aggQazPWGKS.9b1vefe.q7ykF9dTS3HRer7/TwnjYlE3EnNGCm', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198509072025211051', 'Erwinsyah Kamsia', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa Utara', '$2y$10$1FJyzutSh7AaQphztzYJDuh7xdkZmTqClsaz2ijErtuketqfrdp8W', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('198510142009021003', 'Andi Alimuddin, S.ST, MEKK', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$kWQacSOTA1QY0vhTmP/UW.LGBCAFrAVJmom.padzamYKMrX.eT4cC', '2026-03-19 12:56:40', 0, 'Pembina / IVa', 'belum'),
('198512042010032001', 'Deesye Loury Bue, SE, M.Si.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$dsWgGlBooXkCIS2lFP7D1utxOm.W4A1fzGGN3idboXGb9/IHXF7Hm', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198601022006042002', 'Ira Yuliana Posumah, S.Si., M.Si.', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$Ezn4VzU7A7LW4XILMucoBuPUSrwfm2GLEKUKu5bCrJygDjGTTsufO', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198601042025211044', 'Usman Maliki', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$SUKMv/rjWL3yBD90J6mXOOeCftkpSp7KGKJuB092pI5HlWpeJcROu', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('198602072010031004', 'Frenly Wongkar, S.Si', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$/heZcYCblJp0jHUiD8wvuepsA.qhe7oKnFDytOYzhii/IWmwCrb3K', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198602172012121002', 'Adeantiko Riza Febiunca, S.Si, M.M.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$QUGCAHxU.yRX7vmboHYSLuZb3JXP/C/fk88sda/pTVUX.jrh6YTGu', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198603032010031001', 'Qoiril Missbach, ST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$nyY4RD9C3bkHnqPfkEJaWeXYT/gfHC6Osik36xWTEYB9xrDaySCzW', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198603082006042001', 'Frisda Arisanti Tarigan, S.E.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$KEVyyPQ5L9ltCXnyTseQlOo5.3PqshHI6RuDWqQAo0nMTkvAILk.K', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198603122008012003', 'Sih Kawuri Sejati, SST, M.E.K.K', 'peserta', 'Statistisi Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$UdzQ/iVRfUH/aTzmmsufD.7smBheoQeiU.QTfE3LNsOcDsT5jBdEi', '2026-03-19 12:56:38', 0, 'Pembina / IVa', 'belum'),
('198603202009021004', 'Hahotan Sagala, SST, M.Ec.Dev.', 'peserta', 'Pranata Komputer Ahli Madya', 'BPS Prov. Sulawesi Utara', '$2y$10$UZGVZzTX.M9sH31jOXqpU.wHNArnis7Fh2j89imlltSNCbfp0p936', '2026-03-19 12:56:38', 0, 'Pembina / IVa', 'belum'),
('198604132025212012', 'Adtriati Nusa Binambuni', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Talaud', '$2y$10$lgtj4Gq7io5NG8hT.jijD.WJt5/7iEhZLE/1DUumO7JZ.P21DvqWG', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('198604222011012016', 'Sylvia Tambeo, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$B47.TBIf.Nuj.klG78U8heugEPiR6BGgZJUong7WNpwCwiP01Y8IS', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198605262010032001', 'Meity Chintya Sinadia, SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$9jHnxK8LA9lYyk1Pi1eIDexxykneYB/mGTz4S.SRB.y3YziIr5uue', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198606052008012003', 'Ivonne Rivianita Nender, S.M.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$G1x9LqFayZ.15lbPwlE0DeZahnaH08jO/y8Rdp64KcDMHa8MQGCwi', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198606112009022007', 'Junitha Joce Sahureka, SST', 'peserta', 'Statistisi Ahli Madya', 'BPS Kota Bitung', '$2y$10$rZxxgRrSbP.n4EPFHD9DH.bTv7wchfYD/bNjdM1jxHteezeH3xbsK', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198608062010031002', 'Sahirjan Raup, A.Md', 'peserta', 'Pranata Komputer Mahir', 'BPS Kota Bitung', '$2y$10$NR6D5hmDVHoB8hHUx0kgNOUGEF5j78X1rGT9lgJS7NmAyhPZfi0ua', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198608102010031001', 'Richi Cherio Pinontoan, SE, M.A.P', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$EgpsnNBE0/7a2KKgm.BgTO4ut0jOJXmtHQpQxCvNDsPI4zuJvCMtq', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198608192005022001', 'Triny Aleine Talumewo', 'peserta', 'Pelaksana', 'BPS Kab. Minahasa', '$2y$10$vf3.5GJDSNfupHsQvv5FiON0/rxWMXHeDYizdUwgMuslZLpq9fkm2', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198610142006042002', 'Olivia Widyawaty Sahambangung', 'peserta', 'Pelaksana', 'BPS Kab. Bolaang Mongondow', '$2y$10$xZxR0Vs12Rd94.jtbAFtZegjWzHjBOefL1klCC5V3z4yazJLyPPSC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198610192009022008', 'Ika Oktaria Gaib, SST', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kota Tomohon', '$2y$10$XYpUBtGyzBIxYJEHbsO0c.5K6mLcZkZsAnAZKaH7M5kgJlch1Zi4m', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198611132010031001', 'Neivrid Caesy Lumiwu, A.Md', 'peserta', 'Statistisi Mahir', 'BPS Kota Bitung', '$2y$10$7fLLn6rwkGdPjrL85b1kGeobntrBFvRG6Tc279KQFOw5gu0kt1Jz2', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198611242009021001', 'Dwi Raharjo, S.Tr.Stat', 'peserta', 'Kepala Subbagian Umum', 'BPS Kota Tomohon', '$2y$10$iZ2h9aZVMPabiH4.sxw/e.loAuvYA5PrpZWSIJnXL1vUmVSO71FGe', '2026-03-19 12:56:41', 0, 'Penata / IIIc', 'belum'),
('198612042011011006', 'Roberth Yanto Sanggana, S.Kom', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$CKbnYOR.lZdVj2senk8mEeCK.1xs4gCkzGRI0E7z5XE5OFgiPyXJe', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('198612062010032001', 'Natalia Ondang, S.Si', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$5zbUQFhUUdPC/ID68/aMXez7Wr4y.KDB3Zz0zmFMoZWSTdalh6yOW', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198612142025211042', 'Dedy Hartinya', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$5o02z07yi.xx6JNP95KreefWLTXF32Ymb/.UKFoabst6spFeNqGc6', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('198702142009021002', 'Wisnu Triaji, SE', 'peserta', 'APK APBN Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$FKZsWSCiLlbzjL0eOpuT9O6Y99ULuGd5XADxPFWrDZgmVk/kFcwFS', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('198704302009122007', 'Widhita Krisnahapsari, S.ST., M.SE.', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$2CS3VCUQ8LoUHiCDs5fZ6uBseoaDr.SxYLAVpiWKY4iRZcYrbpgom', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198708172012121002', 'Agustinus Dirga Istanto Mantow, SE, M.Ak.', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Tomohon', '$2y$10$zmG3IN4abxfxeq2cHyRMmOIoeEg2rCIsf4NEb45Z3j1ZNnriXMVSy', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('198802082009122002', 'Tiara Dameani, S.ST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$Pb7Swv6N94MZBcSwjLWBaOsReIfZOR.8J5iJ.zgYSs0Qsx74XQn1y', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198803032010121006', 'Jipi Pangemanan, SST, M.Si.', 'peserta', 'Statistisi Ahli Madya', 'BPS Kab. Minahasa', '$2y$10$78tJkyempZ288CfnrHts1uomUc1g9b0YqmLn1MNRRkraHjdcUoUMC', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198804042012121001', 'Johannes Hasibuan, S.Si, M.SE', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$XKS2aU/2WqaKZhRlr82E5OfamC8yWF/Rq5olBYzsM2y7Fzwx8lSCy', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('198805032012112001', 'Sizi Lia Ginoga, SST, M.E.K.K.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kota Kotamobagu', '$2y$10$TrVWtessiIaNxihqPd6iMeQdmsGQr.yvNEdqXDNQ.FmgP81kQnwkO', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('198805082010122007', 'Ugiana Ramdhani SST, M.S.E.', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Kotamobagu', '$2y$10$YRLUdazL2Yx7wHA3oVyBW.xNYlIyNRGALqgi.D8kEHHYyor7SN7Dm', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('198805202013111001', 'Munawir Kobis, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$h62j4GcIOqLllg4HHdF1Y.oQ/pV0SVpgDVo/nGnigTzO8R06l/9o.', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('198807022025212040', 'Julita Sendhy Elisabeth Baheseh', 'peserta', 'Pengelola Layanan Operasional', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$OLPYvtw0BRIoMuS1VApkwOFHBxr9pfpauT4993R/upHGETeAyBsSW', '2026-03-19 12:56:40', 0, 'VII', 'belum'),
('198807092010031001', 'Jolly Jody Pesik, A.Md. Kom', 'peserta', 'Pranata Komputer Mahir', 'BPS Prov. Sulawesi Utara', '$2y$10$FvtZsK.2H3E5oo6aZ8ICCuLT6eHMv.WzuPDdlXM5j6wHLc21MQjKG', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198807182010031001', 'Piky Pomantow, A.Md. Kom', 'peserta', 'Statistisi Mahir', 'BPS Prov. Sulawesi Utara', '$2y$10$09JTcCn5lnLE/zNGOqqo6OkAiAxFLLybYVPW0pZSCWACDY8XnmbJe', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('198808112008011001', 'Jongly Wua', 'peserta', 'Pelaksana', 'BPS Kab.Kepl.Talaud', '$2y$10$VtAXAuvmV0.BjTXQZyQ2AO8mP2QR6dTS98siKQUuFpFNDxAC5sGmS', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('198808172011011011', 'Alfian Harki Damopoli, S.E', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Minahasa Tenggara', '$2y$10$.7/jFzesQKBodJN7PZ9dTuzqSbW5HbpjduvZ1mfvJ1HBqZT9AKFBy', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198809272025211046', 'Samson Talimpuu', 'peserta', 'Pengelola Umum Operasional', 'BPS Kab.Kepl.Talaud', '$2y$10$lpuLJpwS.eDlfUj7aoGB/O3uqP6esPWD6xmA7FUN54nXLeqTEJH0S', '2026-03-19 12:56:39', 0, 'III', 'belum'),
('198810072008012001', 'Olivia Susanti Makaringang, S.E.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$sIUXlq.m3yHmXaSipK5npu3lfXkn8NmpmQaviPprqP0G49wzX8/Sa', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('198902022012121003', 'Furqon Suparzaky, S.Si., M.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Bolaang Mongondow', '$2y$10$IHbTa37Cty4/SSBkqG8xauHdDEddWTsMx2b/bwo0TeYfZ1cmdrtV6', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('198905162012121001', 'Gemayel Gamaliel Sahiu, S.Si', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Tomohon', '$2y$10$18/5IzN2hIabDUhZzgKer.6w9bSlQQ58SdYBAo9vxVcmLf3DHZPS6', '2026-03-19 12:56:42', 0, 'Penata / IIIc', 'belum'),
('198905302025211047', 'Michael Kaseger', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa', '$2y$10$bmvHcltJcQdd7AILMOjLZO0WC8nKdnhJdgsjKUo9jIUUEw40X/bnS', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('198906032025211052', 'Joudy Koraag', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa', '$2y$10$o3ktES4NJguNVvT.pzTsLOkjisZujzjOim6qxRbPL1em5GqEkPghq', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('198906152024211002', 'Yuan Philips Gigir, A.Md.Ak.', 'peserta', 'Arsiparis Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$z5J3fVGB9TDJ2QT6jcvq5.koKfB3CxbfCCuCVl8DAyyTvV8XJkET6', '2026-03-19 12:56:38', 0, 'VII', 'belum'),
('198907302025211028', 'Yunardi Ansiga', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$XfOqkQuNFzTQGC3USfdin.UcvV0lwHTJcKCS0n6bHY1AIG0MCsAGO', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('198910192012112001', 'Erna Kusumawati, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$J2LHRkUcHp7H/0y02pQZTuufNGp6SYJUB5YiQo2PjUerCdIl5FtW2', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('198911102012111001', 'Randy Pratama Lumenta, SST, M.A.P', 'peserta', 'Kepala BPS Kabupaten/Kota', 'BPS Kab. Minahasa Tenggara', '$2y$10$gJzfNF7uxZEt9reI.f696O715E3rdDxzllAzmHCMvfrll8.bFbxKm', '2026-03-19 12:56:40', 0, 'Penata Tk.I / IIId', 'belum'),
('198912232013111001', 'I Dewa Gede Sunanjaya, SST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$gIAQ3d1YSesmnl4Pe4.fC.2g5PfPvVnkbzU9uwZGK1fE/nSsJWh1O', '2026-03-19 12:56:41', 0, 'Penata / IIIc', 'belum'),
('198912292022032007', 'Friska Patricia Raintung, S.E.', 'peserta', 'Pengelola Pengadaan Barang dan Jasa Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$4t6u666UYCrR9GXenZcSW.cghUxROpvjrP/xF1QjZFUKqMOddl8Xy', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199001230205212001', 'Merlin Worek', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$fr652B3anCep6WpEcCsAze6AULLScfdaCJLtaVU9seZy/tkDEVu.2', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('199008012014102001', 'Windha Wijaya, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$wrnL8YJwSPF.gMbOuxxkduKv81VN7MWfw9B3gusukapAPeAafEpQq', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199008212025211041', 'Joko Priono', 'peserta', 'Penata Layanan Operasional', 'BPS Kota Kotamobagu', '$2y$10$Zp8JY/vraWkVZmwiQ2WjNuHeRVq8m9OzxNT3XUc7KzJYQI13zqdgu', '2026-03-19 12:56:42', 0, 'IX', 'belum');
INSERT INTO `users` (`nip`, `nama`, `role`, `jabatan`, `satuan_kerja`, `password`, `created_at`, `is_active`, `pangkat_golongan`, `status_tes`) VALUES
('199009142025211045', 'Napolion Malensang', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Talaud', '$2y$10$UwfNMxXlDzTZAWxuZfzA3OyGLbvaR7qJ7vPfCcHJQZZUISXgHtAQS', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199012052023211024', 'Denis Diego Kaparang, S.A.P', 'peserta', 'Analis SDM Aparatur Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$d1qy35WHwXwd/4ehq9XksuJ.jHvKMQ5psDCsSIZUzh8otOlUiNE/S', '2026-03-19 12:56:38', 0, 'IX', 'belum'),
('199101012013112001', 'Frisca Ully Hapsari Saragih, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$361C4aPJs.5gJx8jh4PBWuVT6N274ak8xks2XY1xYuRBrWo5N2C9.', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('199102102014102001', 'Marnita Simatupang, SST, M.Stat', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$IZaXn5DRLUiYEd77IjSsSewrrN14bALk98RMJFKMlHgb5d9G4LgIi', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199109092019031001', 'Arief Budhiman, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$tT6CYfM7M4HrbfXe07ABHOdm6pxOhv8YTtSCP4Sq/ZTyCjQHnnwcq', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199110152014101001', 'Bayu Setyobudi Hardana, SST', 'peserta', 'APK APBN Ahli Muda', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$z.Ty702orlo7DZ7nezlhQOyiXnK7/Wy5fZQQRME4pf9LCxOLEkMRy', '2026-03-19 12:56:41', 0, 'Penata Tk.I / IIId', 'belum'),
('199111062025211041', 'Saputra Rizky Salu', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Manado', '$2y$10$PCkyTroaF5HmMeHpX9c/H.oKm11AdqHcknbXIFX9IvpdSLpwAsZdu', '2026-03-19 12:56:41', 0, 'V', 'belum'),
('199112202019031001', 'Dading, S.Si', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$fBiCGRKrX3R.eFezOjOGmudFlQb9a.1KvSMc52TwhxtMY5VWeyz9e', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199203222025211044', 'Michael Damopoli', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$ARfaBEb34Em5V6Mp2EOXwOTfRDQO6pPUoDcdgvMBtds.Uz5YdB2Ia', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('199203262016022001', 'Intan Angelia Senduk, SST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$APMexCVfF4.8oYSS3H8wZuRVVdrLUD79PMaruYuZOwOnvbqHVbR9m', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199204212016022001', 'Prima Puspita Indra Murti, SST, M.Si.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$lbrNcYz4YYb4e8cT/Di9peQGuFupbKxW.IvkfoARdukRdKF0Ktgve', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199205102016021001', 'Flendy Yusak Manganguwi, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$GZ5VvZ04feTu3d0PLaKIMuiUMQELTKeMjahyXTZ.p4qjIEeMgw9qO', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('199207152014121001', 'Suryo Refli Ranto, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab.Kepl.Talaud', '$2y$10$89opxB0lL5Qlz04GVL5KmOId8DLPoS5JJCS8.T8EUdSUrA332pMk2', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('199207262025211047', 'Julfrit Gunde', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Sangihe', '$2y$10$tNbp05f1Z2LbUYbXHlK5A.dHCI6Jzg96mXnsmYoanJtkbtcSOYnra', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199209032014122001', 'Halida Zulfa Muthia, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Kotamobagu', '$2y$10$HFMWWEC1GM8BtJaSeF6RKORErI951ZwWty6IyXXITSyy08iNktBOK', '2026-03-19 12:56:42', 0, 'Penata / IIIc', 'belum'),
('199210282025211059', 'Muh. Rasyid Sasikome', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Sangihe', '$2y$10$60uyc8Dq.GVZP4CJSaFPv./ia1Zw2g4iit5Rc0beWrLjxWb79mfMu', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199211072014122001', 'Yola Christhy Larinse, SST', 'peserta', 'Analis SDM Aparatur Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$s4iDFcjnSm7O.KIZYgn43OU7/NM9J99ZpCKfaRN6a.SbXEqRnzd9m', '2026-03-19 12:56:38', 0, 'Penata Tk.I / IIId', 'belum'),
('199211252025062001', 'Marlina Tuti Handayani, A.Md', 'peserta', 'Pelaksana / Pranata SDM Aparatur Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$o0mYfTmTAMWoIb48S7IvfuXpoHeJe/9.SXgvRcHf/8ihLylMSPEsi', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('199212292014122002', 'Enggelin Giacinta Wongkar, SST', 'peserta', 'Pelaksana / Statistisi Ahli Muda', 'BPS Kota Manado', '$2y$10$9TyIcHqLZd5kkCOwEu3u5eCqcs6.AMplHlOo5fGaSf8ww68QDcZEi', '2026-03-19 12:56:41', 0, 'Penata / IIIc', 'belum'),
('199304122014122001', 'Kristin Paskahrani Bakara, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$KrY3NL.38OChLbyMF430COVTEdtBHtP0b2cBO8UuD0apo8ouqm4mS', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199304182016021001', 'Arista Roza Belawan, SST', 'peserta', 'APK APBN Ahli Muda', 'BPS Kota Manado', '$2y$10$S0ECtT8FimYoD/RUZKIbyOYQxSpqEupWckwPpQjEba6OVu5zk90l6', '2026-03-19 12:56:41', 0, 'Penata / IIIc', 'belum'),
('199305032025212063', 'Meltje Feralini Dohali', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$vRwZL4kyBtCOImuarSflte/jTih2CoQkFNlUjHtTGtJIVdV5hrbRC', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('199305252017011001', 'Edwin Nababan, SST', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Minahasa Utara', '$2y$10$LfjEuwhjcTuHiEObcz7VgeUlLUWa.GGzs5NhAs6oNLxMElJXNqD0q', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('199306142016022002', 'Sonia Hajar Marophahita, SST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kab. Minahasa Utara', '$2y$10$2jdstB36mp7Z2D6sFUOk8Oqsme/viSqNvhcqoDWes.C976.2qVLKC', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('199307102016021001', 'Satria June Adwendi, SST, M.Si.', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$Lw1PvJ3TndBwVW//37gZP.7/mxgS7hbCDkQEklwviBLkK811jcxg6', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('199310122016021001', 'Satrio Farli Tangkudung, SST', 'peserta', 'Pranata Komputer Ahli Muda', 'BPS Kab. Minahasa', '$2y$10$/dbG76vxvE4uvFocPE8GIebbYnF8vcORrZl8cFazkUGxjYYkUf2Pm', '2026-03-19 12:56:39', 0, 'Penata Tk.I / IIId', 'belum'),
('199310142024211001', 'Oksamja Stevanlie Janis', 'peserta', 'Pranata Komputer Terampil', 'BPS Kab. Minahasa Utara', '$2y$10$gs7hldWJSGYlcBP9ObURV.bXP4LAI0qyQssDJTSineYDaOKWWE0bu', '2026-03-19 12:56:40', 0, 'VII', 'belum'),
('199310142025211044', 'Ridho A. Sambuaga', 'peserta', 'Operator Layanan Operasional', 'BPS Prov. Sulawesi Utara', '$2y$10$5SbeBUCke6f4IvQep66BEe9yp2ptktsbwTUi/CSpU3bGBBz/wpE8y', '2026-03-19 12:56:38', 0, 'V', 'belum'),
('199311062016022003', 'Euclidesia Finella Loong, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab.Kepl.Sangihe', '$2y$10$usnN0xUf8IFmDfbdk7RNwO.5QSt5ASuB7/OUihnGLFlyhp1uehb0u', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('199311082016022002', 'Isna Muflichatul Fadhilah, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kota Tomohon', '$2y$10$.Dv5utG6hRKVZRh.CnaFau3P/fCpxV/gG9KhGsf64ShrxUKl.mTCi', '2026-03-19 12:56:42', 0, 'Penata Tk.I / IIId', 'belum'),
('199311112017012002', 'Nurul Hayati Unonongo, SST', 'peserta', 'Pranata Keuangan APBN Mahir', 'BPS Prov. Sulawesi Utara', '$2y$10$4gIjumhBpC3XRdTjGECltup9wm3NiH0NWu5r6s/uK4QgCZqiJCk1K', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199401022022031005', 'Golfrid Woren Wel Pumpente, A.Md.Kom', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$aUSJHLSAkqZPgv0dsgCzgu8u/qX3CCp.zQgzQVVdDJF9wdz/bq4t.', '2026-03-19 12:56:41', 0, 'Pengatur / IIc', 'belum'),
('199402282019031001', 'Rizky Aditya Akbar, S.Si.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$h0rwL5jh1yKluVD.yi8...Jn.vZvC5wT9fqkewssh4lf0/Dg9vASu', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199403182025211045', 'Fadlan Mohamad', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Bitung', '$2y$10$v483ksFMcAap/RkzVeSVue4KT5nOZDbEsMDHHHX3KBD7s0DY0NGqK', '2026-03-19 12:56:41', 0, 'V', 'belum'),
('199404142025211056', 'Fikriyanto Patingki', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$yfZdV8Qhr21SwnB/nuYYTeeSWKVO8zR8CXnfFc5f1eLwMutSiJuAu', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('199407272017012001', 'Julita Dorneka Maapanawang, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Talaud', '$2y$10$goAwB/iXK4ZFyvRxKiM86.lD.i/TV8z2KubmmDUnpFhWIa2iCxXR2', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199408062017012001', 'Mariane Esther Rantung, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$jrqjKs2e0v2Kp1f708rr9.zHV12CO5yHrSVwd5s7foTgc3xHMFO32', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199408132019032001', 'Tri Hidayati, S.Si', 'peserta', 'Analis SDM Aparatur Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$SPhUmGm477J8lW/S/pGz5.2LLu8HuMwTOHGY.BmLBCLeJwV25QKjm', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199409252017012001', 'Nurtia Katiandagho, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$SxNonEg90nD6rbbNCWmUReJtWgClRW4/IGOoW9Bp183H/HSMFi8.W', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199410302018022001', 'Salonica Oktaviani, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$Te8umIz3X2GluJCSOFG9NeE2hE1HqJhnXC414wRJlBhdoxoikYX82', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199411302025211044', 'Sugito Mokodompit', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Bolaang Mongondow', '$2y$10$21uDi3m6lLVGFgitJmaWGeFUgaERm98esKfQDjknqhRuSX3ccon2m', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199412012017012001', 'Wulandari Ponto, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$O1jRCbzTqN7n.hJNmwD26u3pGK/DDodlo2Y1cMe6rNFrngotcpzy6', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('199412042017011001', 'Denny Silirambang, SST', 'peserta', 'PK APBN Ahli Muda', 'BPS Kab.Kepl.Sangihe', '$2y$10$FlCRN.30n346.IjnVWoc..08i0dUDiqALeQddzCqVtWXUaCiKTCza', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('199412072018021001', 'Irfan Damar Prasetyo, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$9Wr6B2I3cFWdEvrBkSpUbud.aOZ4OctwgvlnwqBophYuAfFhlfd/6', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199412082018021001', 'Ahmad Samsudin, SST', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$pO7wHqhtZGa7W3gy1EwD1e0/Bhxi7zSnh55CAwmJRs0skKLBPmkqe', '2026-03-19 12:56:38', 0, 'Penata / IIIc', 'belum'),
('199501082022031006', 'La Ode Abdul Hamid, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Minahasa Tenggara', '$2y$10$iGiF/P.K/1Zkfrg1f8gmPeoUcfzumAooMlUOgRSVesUvO.xNoWE8O', '2026-03-19 12:56:40', 0, 'Pengatur Tk.I / IId', 'belum'),
('199501242019031001', 'Nur Alamsyah, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$eqtiBte4k6zBbJMDcL4m3..9T1ijw3XvevWnpY1Lj1OoBKjVU5K0O', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199504202017012001', 'Aprilia Tifani Sumenda, SST', 'peserta', 'APK APBN Ahli Muda', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$D08giwry027NsfeYyeZWM.b2B9IJL9CuyZHGV2DrOHlzaYIz/cUeO', '2026-03-19 12:56:40', 0, 'Penata / IIIc', 'belum'),
('199504292018022001', 'Loveria Candra Puspita, SST', 'peserta', 'Pelaksana/Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$1nh4tIuYkR8aMRUIGSPbMu71Gterr9w1vhlF.ILFMwsrt0GRR2nyy', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199505022019012001', 'Rahadita Nur Haida, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$V9mFSsiNk5/VDR5Dg33.iuSkbc.qG.uZSpU.hchYNBUCrjqclFuGi', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIc', 'belum'),
('199505242018021001', 'Rafif Rikasatya, SST', 'peserta', 'Pelaksana / Statistisi Ahli Muda', 'BPS Kab. Minahasa Selatan', '$2y$10$X7lF//nVtIpd2H4WON4C9uQeFWpSC6QBXNeElbqJupFxbl5jcI3xa', '2026-03-19 12:56:39', 0, 'Penata / IIIc', 'belum'),
('199506012018022001', 'Evi Sanjaya, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$Cw4S/Hx.0yRm5..Foj68geODU33xqa3vpUrTpjmE3dGDVM1PW.sMm', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199506182019122002', 'Firly Lutfiana Prahardini, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$HSlbFIHOdNQnFkd8BJ5JsOWEckk3O0HDX3O2xeOADr2ol7ONkx/u2', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199507102018022001', 'Yulia Sakinah, SST', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Kotamobagu', '$2y$10$ZC/JND5tV8VWf90GRjf1YesU1vd9P7r5aKFkIV/RVCf0OcuhoHRli', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199509152025211046', 'Vicky Eliahu Cohen Laming', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Sangihe', '$2y$10$RyQaW/zpyY/OQhtAJV.4vO5qE8w5lBUvhvETGcsCQsKK74qGcbQgG', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199510092019032002', 'Nurfadhila Fahmi Utami, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$/4lj1nGcM0Kn2ZUuY6ZX4ukOSoiBtSVZHqOY65MHX7qSbFKGSXluO', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199510132019031001', 'Muhammad Iqbal, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$NCz4C37rZzAwU6q6kNx/iOBYU6mCaHCNGlTxmFVto4P.OhZ.5NlcG', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199511052025211038', 'Frensix Katihokang', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa Selatan', '$2y$10$ViaEYneAG5w//Lo9UeMocODKmikrcDyvIJDWPR.nmlJhjy/vZFozG', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199512212019121001', 'Artha Gumelar Suharsa, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$SIV/agSChIEJvMgf6glrWOk8nXf34YSt9myLDVzY.XQM5DSVtXblK', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199601042019011001', 'Janter Simorangkir, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$UJ6ViPPqhwAZULGK4zzyZO5jJxfGKlHcHXiagYTKEZkLU.LtZpVDq', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199603162018021001', 'Renald Saputra Salendah, SST', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$TXFiqgurTJ7He7TWFGWNTupzr6g6nj/DE1D1/4qpB/9NE9l8qOZBK', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199604092019012002', 'Eka Apriliyana, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$hGWNIMoWuXPEv83ecj5J6OofVXAdt0FcX/7IUSatJQ5HUXY/Oj6Ca', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199605122019012002', 'Puput Astutik, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$/0ySBLQz4Crp65UIyW8yz.TC6NrD45A8CEQ7FthYGvJXxaNlRfCWm', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199606072019012002', 'Yunanda Angelia Sinurat, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$FP5SILeJ2kvAUSX9qQb5RO/g09Hw6efDhLh.quNOsSKV/1gdOQZSm', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199606072019121001', 'Anggit Prihatmaja, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$hWib2Yz5qwoafHhEvOMnxuB9Dx7nSX/HN1f0kYgCaOTJfQpM3XZLW', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199606222019011001', 'Dian Teguh Prasetyo, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$J7Iu4rj4wDnV5FjTNRhto.ItjEyifHUvajQSaCGWrLkIRP5o0BgEq', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199607082019011003', 'Abdul Aziz Makhrus, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$2QuDnr9Vp8FkRX4xYE2MBeyUJrFLfwCByAt3lYaH6b/cSBdI0ay16', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIc', 'belum'),
('199607222019012001', 'Anne Indiarti Banjar Nahor, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$YJcPHukHLfuQ1pGL3UYPuuDFfBJAEDBpTJY1XGUXpTeE3e1w/c1da', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199608092019011001', 'Marcopolo Gultom, S.Tr.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$asCZiYlts6vuu39PTc/hCuhQnnoJzVVWb0USoUhyb/bn5SOAgUUyW', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199609222019011002', 'Afwin Fauzy Akhsan, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$NVp50PzMt2rZ5rAgsazuW.c7yTXieeaMG.0NVyO3eC2DwnUIsNNAK', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199610152019012001', 'Weny Fitrah Anwari, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$aj55RZK5Cg366HGzEfhvy.u46k7lkq9ixq8LSIArj52ZTauGJaHS.', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199611272019011002', 'Eldorado Alfu Ilmy, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$/QlODVmkzGo446kweJmY8OkzTPunwk3JLvu3SrWYplbk3sF9Nln66', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199611292019012002', 'Ni Putu Beliana Puspita Sari, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kota Manado', '$2y$10$nwJnz3LMofYI16lKJL/Ce.UniLo.TBh1saFz8qHue8uANlj/6tAy2', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199612052019121001', 'Yohanes Adinata, S.Tr.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab.Kepl.Talaud', '$2y$10$NC2pgBIeraCdu5g5L.JPF.lXO0nSJRu.TKygD0xzFmQpQeIplTuxG', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199701052019122001', 'Putri Sekarsinung, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$wz6QknDbCXQrB9cZsLJsperz9s.TMDSsOi4VHRhACo4.L.d9zn2t2', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199701072025062004', 'Michelle Jessica Suprapto, S.Psi', 'admin', 'Pelaksana / Asesor SDM Aparatur Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$KvGa52avJmJYkz7GzxMyW.XS9ZrGvBZWco.XP32xCkM0wkfn.n3QW', '2026-03-19 12:56:38', 1, 'Penata Muda / IIIa', 'belum'),
('199701292019012001', 'Zulfa Nur Fajri Ramadhani, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Muda', 'BPS Prov. Sulawesi Utara', '$2y$10$ubpa/EA/gYP.FBN23/q/Q.PLcAJvY8HBDFLjnf1iqN4sD5RROzazO', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199703082025211032', 'Roy Marthen Samalukang', 'peserta', 'Operator Layanan Operasional', 'BPS Kota Manado', '$2y$10$DBKe8YYBJg0xrQLGZhb.Fe53O8Hrb0DeKMDohgAGkCS.UZPrlnG6G', '2026-03-19 12:56:41', 0, 'V', 'belum'),
('199704212019012001', 'Nurul Hidayah, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$G/t8NYRvMvjblI4F/QotluJz3rW8m2/QP3o0Hkg3weQK7gkXzCEmO', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199705032019122001', 'Ratna Pratiwi Kusumastuti, S.Tr.Stat', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Kotamobagu', '$2y$10$vEYCURAkJ02ZzjbdzIwrH.S7ZG8qYBIsxmqScgtFxI3t27xZnVQvu', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199706092022032016', 'Regina Pangau, A.Md.T', 'peserta', 'Statistisi Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$VB386nMRmaG3QJsXZzfW7OxaJOyWDGi4SeE.Po5KKQRLiZlCjPaYy', '2026-03-19 12:56:38', 0, 'Pengatur Tk.I / IId', 'belum'),
('199708072019121001', 'Christian Leonardo Pratama Tamboto, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$nElsvvGL8nsvy7YqWvPfUO3774xkER0cLTr3t37sw3PkHJDsex7tS', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199708252019121001', 'Dhuhal Islaminuddin, S.Tr.Stat', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$Vwd4dSTOwkuqN0EIpRjNU.AI/PltLEeOAIbDUcekUvgXJNcW31YoS', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199708252019122001', 'Laily Agustina Bestari, S.Tr.Stat', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$I5gUJpiFQSgBlEhkRu4VmeLXDhxsgQ3HpSkdGbHDFtJUZvtblcdeC', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199709202023021001', 'Agus Hardiyanto, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$V.y0IEwdC3LBNmJLrAq6y.jG9iQy11sByVwu.MbN/eQMW.EMzhWve', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199710062021041001', 'Mohamad Rezka Aji Pambudi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$wHb5DA7hosSwaAbArT3FXuitZ2n2Z0EFWNnHkCHEQaLgupEfDFGJi', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199710092021042002', 'Iska Myranti, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$JmKEdNbqmscprmWyBgCyKOzr7bpewzRnXgGXGWTPj/HN97slRV8o6', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199710192023021001', 'Akhmad Farkhan Hazim, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$TWxIPyxYWL5k6NVa1ZdL8eOuEnF1FSNrbYCnms7bBGYq5YrKb8v6m', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199711032021042001', 'Danty Welmin Yoshida Fatima, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$or1i5UBfca.HJBegA0f9Y.t3lvI7G6ekLAj3Vj3/GKEXFq.sjTnqi', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199801052022011002', 'Dwiwandi Alfa Sekeon, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$tJh7jFoQjJpc5euGyae9f.wqJXdpUC/jKNuYsQDIWk2oDDaSp3j1a', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199801122021041001', 'Rozan Fikri, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$/Z2FzbyhAYgwDN16IlOipuraSlwlDvsw7IhdSsuMjj0ZhKxAL/Jw6', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199801182019122002', 'Nadya Tatum Tsaniya, S.Tr.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Minahasa Tenggara', '$2y$10$bJ1ZQtzsqa5H/wGrI/vehekvCKP98VQh56OjrH96JcK//wJyxGoha', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199801252021041001', 'Gilang Cahya Permadi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Talaud', '$2y$10$QUPV0MNp61e25y0SWWFFWO1NLIc.aNgl2tC4C2MAMi3GRlDfFJzcO', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199802032021042001', 'Defi Astuti Anggraeni, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$7JcnqeaCZhGMWaJT7d7w2.kGDBaAxAk.C49IB5UwUA.nTrUhZQaXK', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199802092021042001', 'Cynthia Dwi Setyarini, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$RJsib0FkiKZnqqgZ.1axOeXXETowruVEoBMPoOms4CWQjLhqE/uKS', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199802182021042001', 'Syahfianti Inung Pratiwi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$X01i9lOIGOmEuL5hNbJeDOCmEGxW73crtpxyxdqkwC9iycObONXJO', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199803132019122001', 'Rosmita Noor Arifah, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$g.jxATjqNqqmYkWj4t59huRE.cVovi/TwiP86UKSTyfvNsyflCDX2', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199803252025062007', 'Nurul Fatmah Khoiriah, A.Md.S.I.Ak.', 'peserta', 'Pelaksana / Pranata Keuangan APBN Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$OTji6vtvj2/rn9oYU.jq6Oo2iQpkRH.cfFWa6PB6EyPHv/n/LMmOe', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('199804142021042001', 'Ayu Susilowati, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$YvFWPuigbmXNf0DKVII/B.VOWN9QU7kdKTONGNlDjQn.FfMYJArIu', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199804172021042001', 'Eliza Tiara Devi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Kotamobagu', '$2y$10$QUsu2mu35IGEcGLmyoCMreqN6wJAJE6ZUoL.q.YN6tRRpmG/MWM7G', '2026-03-19 12:56:42', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199804252021041003', 'Wahyu April Yanto, S.Tr.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab.Kepl.Sangihe', '$2y$10$DWZ3jAPJ.gUDNzLMiXLQv.bAcMUolwiLsot3NHwn6RogielGzhk4O', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199805192021042001', 'Nadia Firdausa S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$z9jwx4J21N8sfx16nxeG4OWmQMPmiA2N9mP3OWSZK5dCUkJhpoLJu', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199805302021042002', 'Kannia Amielsa Shanenda, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$DSre5YJsrBqYej17Ojr5g.M5WYbg.XHZFl8yhzQnSZ0mlXY9iTlwC', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199805312021041001', 'Saeful Latif, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$c1CnTl0.D2KsU8LTCMg.lerI6KlLTmeRYlnULHuCV9dicRZBqTPQa', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199806032021041003', 'Samuel Kurnia Putra, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$ud1rvZqOJBJyKoZOrkF6lutFMR2GxloekNvbZi0kvhwZ1AiTPGeju', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199806052021042001', 'Zahra Aliifa Putri Siswandari, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$JtudVmaP94TMfrFuJc0sQeeNdsFxJl7r/2A0e7gyCFJMhpWGdvnwO', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199806112021042001', 'Aninditya Yuniar, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$zJRLam5L4fPKglmDrHRC.ein8TOFlKsAlUrPkoVMcSXrMnOi7ktny', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199806202021041002', 'Krisnanda Prasetya Adi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Kepl.Sangihe', '$2y$10$15tnpH5IzyWxkPdOInP37uIfOOoQtm0bKPcet1pJOlZ53Iw6H/wiS', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199806202021042003', 'Melati Sukmaningtyas, S.Tr.Stat', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$LDc3BbxvQLwpeuPX539zveyLYUf8r41BDZ/DGv74qO0iqAp5MAr9C', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199806232021041001', 'Adalard Yusuf Kamarastha, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kota Kotamobagu', '$2y$10$v/B/cVPmeRfXEQCTj.cKfeG7A3U9M.ra3UZs/CU1Qw7ORJothRnpW', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('199809082021042001', 'Putri Septianingsih, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$04o0DsjWiLIfpi1y37RzTOkAEEs8aPZzlcJiIlOoBJ8NdtofY8pMS', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199809212022011002', 'Rizky Perdana, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Bitung', '$2y$10$.IRvg.6QyMF0WDZyqSELrOi8xo5TVqtqzYk2YBNTccS2pTRITKh2C', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199810112023102001', 'Ratih Rodliyah, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$IgFhzdRTQ9zmolfFlQLX2uMXb1p6PW8ur/E2/dGl6H79P2HtqYZlC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199810112025211023', 'Andris Firdaus Maasawet', 'peserta', 'Operator Layanan Operasional', 'BPS Kab.Kepl.Talaud', '$2y$10$iK5GtlHDSh4NL7aucznTTuhWlqimUwARgouhv46hkYAVlOm5zf7Wi', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199810122021041002', 'Gilar Tri Panji Saputra, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Talaud', '$2y$10$/THchJYmJelrVTFSg9F8mOXpCaJyBg1qyeL6JeXOG01euvPh5fTsK', '2026-03-19 12:56:39', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199810132021041001', 'Ponimin, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$Z2sEzJ/EyB/jmQm/L5xsOeVdMDHiXIVaMqXU4BLDWGWnvPvYMmrRy', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199810152022012001', 'Delvira Ukhtian Nafiesah, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$MRIBRXk35jbfua6HAjiZJOH/BQyPSehS.cOnD0R1AVT0TYTbkjvUO', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199811022019122002', 'Anisa Sekarwati, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Utara', '$2y$10$v6hrOqaTPrf0KJluU.wSX.rZfJEb0jYabiyOwUgCOMa6.5hb3bVG2', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199811112021041001', 'Insan Riski Dwi Perdana, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$RYC4kKxNrMmePLpzuij1Kuf3GgdKfFYjsZqiV4WedRfI9caIKTOBm', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199811162022011002', 'Maskhur Solikhudin, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$qkS59qtRVhbdjcHG1mgfEOjex.6XEnOPV/.BQ.8D.mE2AEtQ7X.Zi', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('199901052021041001', 'Muhammad Rifqi Mubarak, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$AZgyS4nqoxg0Oc2QNTF9g.JFkeuEGSu5zERVrAZa8tQ2gl8wqQB4.', '2026-03-19 12:56:38', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199902202022011001', 'Frenaldy Wahyudi Wenas, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$iI/8adJWHw35PuEEV4O4be3FXFvARZWAVlsfiPNMos2/ylHO1kN5q', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199902242023022001', 'Monita Purba, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Tenggara', '$2y$10$JMkqLWIxaiErmcmzx23XxumuAGRkxJvRdlUtP7fd59Fxy6KPa2SXu', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199903162023021002', 'Fadila Ahmad, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$PTk7E6GN4lReVCQ8TK0lEel4X/ijjAxfkVPpGs1y718a2E1KyxtOm', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199903192025061003', 'Muh. Miriansyah Putra Watupongoh, SS', 'peserta', 'Pelaksana / Pranata Humas Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$1t.D.Bd4F558A70q/k3cL.eBs7mcTZBvHOK3Bw7lqQOQIqLItUrKS', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199903202022012004', 'Ratriani Retno Wardani, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$MjSdiQu4GzgjxT32nlF19./tp76c7nurdPUBj9knPQkKvi9ym40ye', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199905122021041001', 'Farhan, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$Vum//AuD3b5s6gum0QhypehmrxPoFjuEYwiB3TT4Xuhr4oxMdzk7y', '2026-03-19 12:56:41', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199905162023021001', 'Habel Nitalessy, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$6gZ1N9sd26EPcEWUpDrGdeK2/0TgO23.O0LB.tgYWHsoQ.Im7emoe', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('199906262021042001', 'Akfarianti Nawangsih Nugraheni, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$k1ovI26BMvSC7AMt2xGxF.PahvNlDLZhO1ViJCIR45qY37goigCBu', '2026-03-19 12:56:40', 0, 'Penata Muda Tk. I / IIIb', 'belum'),
('199908062022012006', 'Ester Harefa, S.Tr.Stat.', 'peserta', 'Kepala Subbagian Umum', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$gUeqfYnaVUEGPlR1QJEvjutIoxMfoeKhDtRx9dtkVg4VGeG8zT.3O', '2026-03-19 12:56:40', 0, 'Penata Muda Tk.I / IIIb', 'belum'),
('199908272022012003', 'Vini Cristin, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$NCExM4ZH/MDY3qeimUjTOevmWf9LoNQZ93H01KFHgaRk8LYyg/gVK', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('199909042023021001', 'Adryan Sapta Setyadinata, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$LcJ9fyTc.3iUrYuXJZSJ4OBcmbJ38Yz73QqJA6Glkjw4G7lxWLmXe', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199909062023022001', 'Irene Savira Gabriella Sitanggang, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$uw4auJ5H4g8sdfguDjyanujtD4hdk17JHOkdTPa3bfSNOePz/N6Mi', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199910222025211020', 'Mansur Trumpi', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$JfFynGOzWhCRuByfbN8xXedwjIKALzkxRB1anmGB/jSfn.s5YOnJ6', '2026-03-19 12:56:40', 0, 'V', 'belum'),
('199910262022011001', 'Mohammad Rifky Pontoh, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$JVwR1T8gh1DFjouIkHvNxuOftxiYIEQG9MkOE5fstZmPB0kZkQxgG', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('199911012023021003', 'Yogi Resmana Barus, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$z5EYgBhq221epBpwEYDljO8kFbnuitBC2RWM8C30uNInPmlJUCGDG', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('199911052022011003', 'Novert Cyril Lengkong, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$bDrPdgXl9yo5kTX0h0ZeH.yIiQZWgtDXzxuJA2b.rnST9zeW8R8Oy', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('199911072025211013', 'Daniel Albert Manaf', 'peserta', 'Operator Layanan Operasional', 'BPS Kab. Minahasa Selatan', '$2y$10$Iz.Rqo04G/xwufuudIYXPueRz3vdLvWoKv/YwqQKitfV5tZEKpud2', '2026-03-19 12:56:39', 0, 'V', 'belum'),
('199911272019122002', 'Afifah Syabaniah Sanubari Langkau, S.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$nB1WRtMnpaj1P7yHX3qpveFVv1nD1eTeY8FjdGnxVVDooqMcI9Gwy', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('200001122023102001', 'Azim Naila Fadhlillah, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$bMXGEfNNgX54pmAQC32X7uk4YonXfxf6SKb/I3hs.n1caIBZHaGny', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200002202022011004', 'Yanro Eric Immanuel Kuron, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$7.g7wdNpDRPzkHHUs5WFVOSe25PnOgnRXSn9wF5Z6MYxt4NdqRrCe', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200004102023021002', 'Vincentius Agra Ananta Gunito, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$EERi/Uwnau8UiBSxLc8CcOPMCJLStg1aGXjKhUFT9o00s0YsP744C', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200004172022012008', 'Wardzatul Khoiriyah, A.Md.Kb.N.', 'peserta', 'Pranata Keuangan APBN Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$q5hgAZR.drsBVOrWkCDiUefUT.LtUt2ISw/xtcHSB9WkJfmQ0oRZu', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('200005112023022002', 'Meytry Petronella Purba, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$39WKku.kBSFAMFTxyrSjG.oCbSx68oc3Jc4Q2LCclbfpnP/G9wFmq', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200005312022011003', 'Yohanes Adham Anugerah Widhi, A.Md.Kb.N.', 'peserta', 'Pranata Keuangan APBN Terampil', 'BPS Kota Kotamobagu', '$2y$10$SfYGV2WWdhs.AP.7CH3pB.PpVf7aDNhRrSUcZxVDmFLvv49czkUuq', '2026-03-19 12:56:42', 0, 'Pengatur / IIc', 'belum'),
('200006272023021002', 'Albert Junior Chen, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$b0YJAeQ7B294NoXvaztYsOtTFaEU/mXwsSAAnsJSd.yCtayn/U.eW', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200006292023021002', 'Manca Satria, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Tomohon', '$2y$10$UPyHDS7wy0nj7GTltEMM9.pXA2T1Hq2plT8pWPBsnFj718j6n2GIS', '2026-03-19 12:56:42', 0, 'Penata Muda / IIIa', 'belum'),
('200008292023102002', 'Marya Zahra Aliffio Krisfianda, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Minahasa Tenggara', '$2y$10$SoC0cGfCdtUA0YKDqxK71ey1z/v6uOS09iYEGcjmljWKM8/MlOrqW', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200009082024122001', 'Alfiyah Nurul Kahfi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$JWkSaYhQdo7pF3KPDBnIx.2TI7T1IWdCdueXVLMHykeYfj0E4jGZq', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200009202022012006', 'Pidyatama Putri Situmorang, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$YEH6yMSUlFHQ5PIKG8unauDFeVwtCurKKUAQMavOQ3Y4wEnNKaYQa', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200011072023101001', 'Muhammad Syibli, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Selatan', '$2y$10$Hc7JdUHSEn0qup.FV.CrNuiUS.TCW2QitNgMZk0EC8qmJ0GM230uu', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200011112023021002', 'Bernaldo Napitupulu, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$lnuR9fETw9NLD3DiMCA3fOEYWqcXR2Mr4XkAD8TYbeCvQO6v1j1oC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200011292023101004', 'Dodik Setyawan, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Minahasa Selatan', '$2y$10$o.gNqbT1sYBpzOu5Kc83S.pF6tYD4duyyQwS6f9kbmps8sk8mXynK', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200012102023101002', 'Sofian Ramdani, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa Tenggara', '$2y$10$3i0NytMBMfKHAefQ1Mv6OeLwaJOIPNObaoXLBtkIA7v1z0ZnJ6al6', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200101082023102001', 'Dita Saskia SJ Saida, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kota Kotamobagu', '$2y$10$JwLPMTHeKEuZb2RxJt1hsufNwtbN8mdXHRqT6V8DJNrrOTCwjZAgq', '2026-03-19 12:56:42', 0, 'Pengatur / IIc', 'belum'),
('200102092023022003', 'Paramitha Madelin Albright, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$snnPetB22.7V4WY0TKyMfOxjdxS4dFm03mHHMDptqZvgrbgrvh8UW', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200103072024122002', 'Aulia Pramudya Wardani, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$H8fO/3EIr/r7ZLSIFrG5Qu2/oxhksypaqBRysKnFlLmgArVO6SjuK', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200103092023021006', 'Gaberiel, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kota Bitung', '$2y$10$vBUbVyEfnB9mSh0de1H3ruP7rpoTLWN05442g3xmuCjNLvLaCi.Ee', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200103132024122001', 'Putri Eunike Kale, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$XHZXCFzgxAxn5h0bNDytEuTVzh.gqaQJGJv.ucQojBLdAhFKGd4sS', '2026-03-19 12:56:41', 0, 'Pengatur / IIc', 'belum'),
('200104112022012001', 'Mustika Aridya Arum, A.Md.Kb.N.', 'peserta', 'Pranata Keuangan APBN Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$c3pA8QHA1YWUtnhXmgYaR.zHs29i27cUliSUOGetb6RWlbv72CMIy', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('200104202023102003', 'Sukma Kurnia Wijayanti, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$MYJKIOtg1ifTGPn4OJW3T.4iQYMxy3f2J6ZVoTqVZ4OfV7k57oo9W', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200104212024122002', 'Santi Hayuning Putri, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kota Manado', '$2y$10$L8MZcfMgGAj84KdqPpinQemvpJKhr3gPFtHGUAcF9oaKqOnCehGD6', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200105082023102002', 'Meilisa Indahsari, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$3WYZs8T5VeaL/okTt4WzBeUtucNX4Grq8WTTAxTqBEFFj0i6GI0RK', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200106052023102002', 'Rahmi Maulina Alwi, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$QHu4hPTfiBMAqhrnB2X2HObVdxdudPXtABO7iF8gyLfE312CKTPJC', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200107102023102002', 'Arsdhewani Maria Vianey, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Minahasa', '$2y$10$Rz2jDkMkWmrTp0GBneq9c.nRCH3EVS0PKHhyT385AldyN2SOJplw.', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200107222023101001', 'Ilham Alifian Firmansyah, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Prov. Sulawesi Utara', '$2y$10$fwyIPevF7pXg/lBR6ReUdOL7NlG18ZYLiIbUt3Vvo96v6wNeThDoS', '2026-03-19 12:56:38', 0, 'Penata Muda / IIIa', 'belum'),
('200110102023102001', 'Mega Hardika Octaviana Putri, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$tMkYFf7nSSRbAOuxh9VOPuyBJMylyfYlnhS7Ydg1Y8Hla5EQya5hq', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200110122024121001', 'Okta Gilang Al Jaffarsyah, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$7oNNIaXINrtm9JgOoSMb4.GyQN/3XezqMW.ItmXXZzE2MTTAAdlvS', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200110292023102001', 'Nadyah Rizka Cahyani, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kota Kotamobagu', '$2y$10$AvV7h.EN9DpyOQ5ER7NLF.8dsHPr33xLQJcTBQ/Qlt0JajESky3Eq', '2026-03-19 12:56:42', 0, 'Pengatur / IIc', 'belum'),
('200111092024122001', 'Athira Fatin Naura, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$2dNF.N6DjTFI4TErnd28sew4v3ltQgxd9OJRRmAGv8VYBgoCMQ8Yy', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200112092024122002', 'Shashella Zelicha Ramadhanty, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab.Kepl.Sangihe', '$2y$10$3zSnhfbojSiNn919VXDwT.WdyEZTTACAMIejquacYsridyolAkixK', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200112152024121002', 'Lalu Muhammad Ristiyadi Ramadhana, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab.Kepl.Sangihe', '$2y$10$djme6dRNK2KAf1c5.yKqMe0GYvSagrhHj3/mRwUfQFYUjipanY7DK', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('200112172023102001', 'Siti Mutiah Rahma Utina, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow', '$2y$10$9xJKnpraQmgLDgY6rGH/6.2NuELQtJeZPpUy7gf4JFhpkAGhLmHwm', '2026-03-19 12:56:39', 0, 'Pengatur / IIc', 'belum'),
('200203012023022002', 'Samuelin Caroles Pandelaki, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$g0Dh8wOtqS4ZFBJFr02DQuOxzDYd1p.ebesTKDHky2MNyzRcP.JLC', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('200203082024121003', 'A. Naufal Muzakky, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$IO6lGfaNIAJS2KXxZ12d6Oqz7i7htQmHCLyphdATTg6tj0kmuCZvi', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200203132023102001', 'Mutmainnah Aminarti, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$onlvvFLYmCiSD6D53lfgk.tg1ru9dyNqAVPKfffmKJK8P9lXII5UO', '2026-03-19 12:56:40', 0, 'Pengatur / IIc', 'belum'),
('200203142023102001', 'Indah Khairunnisa, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$qrg9vTKCl9AauLexY04gRO2j7ovq35wS9ThXyT7hDPFH7v.SiU53C', '2026-03-19 12:56:40', 0, 'Pengatur / IIc', 'belum'),
('200203202024122001', 'Nur Retno Fitriyyah, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$bP/.HuPM3zkRDM5zzVLOMOAnji3rcxjqqi6QhCRZhhrivBa.XwTOO', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200204082024122004', 'Veny Nurhayani, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$8hicdVD5WQwkuryTgYoj9eRM/AS0ZCKRWDB1TOnpVPDp7NrJ.b1uq', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200204242024122001', 'Shania Wulan Suluh, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$51pdWh2m7V7YZCcoEKhfre1jT9d0jMKzK/ngeM53ctym9WlTGH9NC', '2026-03-19 12:56:41', 0, 'Pengatur / IIc', 'belum'),
('200204272024122002', 'Amadea Putri Deffare Sembiring, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$Hmd1cLyNqFX25qTziw8ljuDpdRE/fLhAPZkQS03fOBj1JBwyk1Nmi', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200205262024121003', 'Juan Pradana Putra, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab.Kepl.Talaud', '$2y$10$i7TTkn9HCfclbRLSlU5bYuYaMF9KH1RboD/kwbCm5rE7WUwV4fLGe', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200206162024122001', 'Nurul Awalia Hasanah, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$CebCaOIuYifr7ILnk681Xe4JLD/3.jsgCtYc/kfCzE4I/OqvRmtPy', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200207112023101003', 'Muhammad Alifh, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Prov. Sulawesi Utara', '$2y$10$TB5VaSVVs8/K0e56rmq8aeOdFpwYHErntTASeykObzlNFpMWk71H.', '2026-03-19 12:56:38', 0, 'Pengatur / IIc', 'belum'),
('200207282024122002', 'Berliana Sugiarti Putri, S.Tr.Stat.', 'peserta', 'Pranata Komputer Ahli Pertama', 'BPS Kab. Bolaang Mongondow Utara', '$2y$10$PsnwDn.PdRymgVniiIbUoes.Vk5Y0g1TKKnT/pUzQOANayS0JBkbO', '2026-03-19 12:56:40', 0, 'Penata Muda / IIIa', 'belum'),
('200209062023102003', 'Rani Adila, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow', '$2y$10$vRgWTnFb5VHTFzNv8yTCJOdhfebDJ/sxHVgGHoR6U26anJKD6Ol86', '2026-03-19 12:56:39', 0, 'Penata Muda / IIIa', 'belum'),
('200212212023101001', 'Gusti Praba Nata Negara, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$gyTnMQEcjYOmOxdwJpyHDOMjlbMr3oTmKFpxO/UKnkfcCY60AsQIa', '2026-03-19 12:56:40', 0, 'Pengatur / IIc', 'belum'),
('200212252024122001', 'Shaliha Kemala Zahra, S.Tr.Stat.', 'peserta', 'Statistisi Ahli Pertama', 'BPS Kab. Bolaang Mongondow Timur', '$2y$10$/2g0Qi64S6O2QRnSGCev0.pJ6t5V/ZfQhy4hbtimGE8AACnv9KbIy', '2026-03-19 12:56:41', 0, 'Penata Muda / IIIa', 'belum'),
('200301262023101002', 'Andre Widyanto Teguh, A.Md.Stat.', 'peserta', 'Statistisi Terampil', 'BPS Kab. Siau Tagulandang Biaro', '$2y$10$By.IlBTfNCSWh/qGcveMF.snwjjcbbe/D00K7Ai0VxGn56A9gwz0m', '2026-03-19 12:56:40', 0, 'Pengatur / IIc', 'belum'),
('230211060035', 'Kesia', 'peserta', 'magang', 'SDM', '$2y$10$ZEDJhXGWexwX9nBl.thsKuqHjtxMHZfqFXX7qoP1HNFn36o.cxvky', '2026-02-12 05:49:48', 1, NULL, 'belum'),
('230211060055', 'Jeannette', 'admin', 'magang', 'SDM', '$2y$10$xsrZTxGgx7/.kUCqYS.fTezOO/fRGNx.27BwC5sWd31ib.sTyE2EO', '2026-02-12 00:49:26', 1, NULL, 'belum');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `biodata_peserta`
--
ALTER TABLE `biodata_peserta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indeks untuk tabel `hasil_msdt`
--
ALTER TABLE `hasil_msdt`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indeks untuk tabel `hasil_papi`
--
ALTER TABLE `hasil_papi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nip` (`nip`);

--
-- Indeks untuk tabel `iq_example_options`
--
ALTER TABLE `iq_example_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `example_question_id` (`example_question_id`);

--
-- Indeks untuk tabel `iq_example_questions`
--
ALTER TABLE `iq_example_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indeks untuk tabel `iq_fill_answers`
--
ALTER TABLE `iq_fill_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeks untuk tabel `iq_memory_items`
--
ALTER TABLE `iq_memory_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `iq_options`
--
ALTER TABLE `iq_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeks untuk tabel `iq_questions`
--
ALTER TABLE `iq_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indeks untuk tabel `iq_results`
--
ALTER TABLE `iq_results`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `iq_sections`
--
ALTER TABLE `iq_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `iq_test_sessions`
--
ALTER TABLE `iq_test_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nip` (`nip`);

--
-- Indeks untuk tabel `iq_user_answers`
--
ALTER TABLE `iq_user_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_question` (`user_nip`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeks untuk tabel `iq_user_section_progress`
--
ALTER TABLE `iq_user_section_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`nip`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `biodata_peserta`
--
ALTER TABLE `biodata_peserta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `hasil_msdt`
--
ALTER TABLE `hasil_msdt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `hasil_papi`
--
ALTER TABLE `hasil_papi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `iq_example_options`
--
ALTER TABLE `iq_example_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `iq_example_questions`
--
ALTER TABLE `iq_example_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `iq_fill_answers`
--
ALTER TABLE `iq_fill_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT untuk tabel `iq_memory_items`
--
ALTER TABLE `iq_memory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `iq_options`
--
ALTER TABLE `iq_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=950;

--
-- AUTO_INCREMENT untuk tabel `iq_questions`
--
ALTER TABLE `iq_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT untuk tabel `iq_results`
--
ALTER TABLE `iq_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `iq_sections`
--
ALTER TABLE `iq_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `iq_test_sessions`
--
ALTER TABLE `iq_test_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `iq_user_answers`
--
ALTER TABLE `iq_user_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=454;

--
-- AUTO_INCREMENT untuk tabel `iq_user_section_progress`
--
ALTER TABLE `iq_user_section_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `biodata_peserta`
--
ALTER TABLE `biodata_peserta`
  ADD CONSTRAINT `fk_biodata_users_nip` FOREIGN KEY (`nip`) REFERENCES `users` (`nip`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hasil_msdt`
--
ALTER TABLE `hasil_msdt`
  ADD CONSTRAINT `hasil_msdt_ibfk_1` FOREIGN KEY (`nip`) REFERENCES `users` (`nip`);

--
-- Ketidakleluasaan untuk tabel `hasil_papi`
--
ALTER TABLE `hasil_papi`
  ADD CONSTRAINT `hasil_papi_ibfk_1` FOREIGN KEY (`nip`) REFERENCES `users` (`nip`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_example_options`
--
ALTER TABLE `iq_example_options`
  ADD CONSTRAINT `iq_example_options_ibfk_1` FOREIGN KEY (`example_question_id`) REFERENCES `iq_example_questions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_example_questions`
--
ALTER TABLE `iq_example_questions`
  ADD CONSTRAINT `iq_example_questions_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `iq_sections` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_fill_answers`
--
ALTER TABLE `iq_fill_answers`
  ADD CONSTRAINT `iq_fill_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `iq_questions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_options`
--
ALTER TABLE `iq_options`
  ADD CONSTRAINT `iq_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `iq_questions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_questions`
--
ALTER TABLE `iq_questions`
  ADD CONSTRAINT `iq_questions_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `iq_sections` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_test_sessions`
--
ALTER TABLE `iq_test_sessions`
  ADD CONSTRAINT `iq_test_sessions_ibfk_1` FOREIGN KEY (`nip`) REFERENCES `users` (`nip`);

--
-- Ketidakleluasaan untuk tabel `iq_user_answers`
--
ALTER TABLE `iq_user_answers`
  ADD CONSTRAINT `iq_user_answers_ibfk_1` FOREIGN KEY (`user_nip`) REFERENCES `users` (`nip`) ON DELETE CASCADE,
  ADD CONSTRAINT `iq_user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `iq_questions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iq_user_section_progress`
--
ALTER TABLE `iq_user_section_progress`
  ADD CONSTRAINT `iq_user_section_progress_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `iq_sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
