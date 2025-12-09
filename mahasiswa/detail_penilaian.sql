-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 26 Nov 2025 pada 10.58
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
-- Database: `disertasi_s3`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penilaian`
--

CREATE TABLE `detail_penilaian` (
  `id_detail` int(11) NOT NULL,
  `id_penilaian` int(11) DEFAULT NULL,
  `aspek_penilaian` varchar(255) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_penilaian`
--

INSERT INTO `detail_penilaian` (`id_detail`, `id_penilaian`, `aspek_penilaian`, `bobot`, `nilai`, `created_at`) VALUES
(31, 10, 'Kualitas Proposal dan Metodologi Penelitian', 30.00, 100.00, '2025-11-26 08:28:10'),
(32, 10, 'Tinjauan Pustaka dan State of the Art', 25.00, 60.00, '2025-11-26 08:28:10'),
(33, 10, 'Originalitas dan Kontribusi Ilmiah', 25.00, 80.00, '2025-11-26 08:28:10'),
(34, 10, 'Presentasi dan Kemampuan Komunikasi Ilmiah', 20.00, 50.00, '2025-11-26 08:28:10'),
(35, 11, 'Kualitas Proposal dan Metodologi Penelitian', 30.00, 90.00, '2025-11-26 09:01:00'),
(36, 11, 'Tinjauan Pustaka dan State of the Art', 25.00, 60.00, '2025-11-26 09:01:00'),
(37, 11, 'Originalitas dan Kontribusi Ilmiah', 25.00, 70.00, '2025-11-26 09:01:00'),
(38, 11, 'Presentasi dan Kemampuan Komunikasi Ilmiah', 20.00, 100.00, '2025-11-26 09:01:00'),
(39, 12, 'Kualitas Proposal dan Metodologi Penelitian', 30.00, 90.00, '2025-11-26 09:09:56'),
(40, 12, 'Tinjauan Pustaka dan State of the Art', 25.00, 80.00, '2025-11-26 09:09:56'),
(41, 12, 'Originalitas dan Kontribusi Ilmiah', 25.00, 60.00, '2025-11-26 09:09:56'),
(42, 12, 'Presentasi dan Kemampuan Komunikasi Ilmiah', 20.00, 70.00, '2025-11-26 09:09:56'),
(43, 13, 'Kualitas Proposal dan Metodologi Penelitian', 30.00, 100.00, '2025-11-26 09:10:56'),
(44, 13, 'Tinjauan Pustaka dan State of the Art', 25.00, 100.00, '2025-11-26 09:10:56'),
(45, 13, 'Originalitas dan Kontribusi Ilmiah', 25.00, 100.00, '2025-11-26 09:10:56'),
(46, 13, 'Presentasi dan Kemampuan Komunikasi Ilmiah', 20.00, 100.00, '2025-11-26 09:10:56'),
(47, 14, 'Kualitas Proposal dan Metodologi Penelitian', 30.00, 50.00, '2025-11-26 09:13:44'),
(48, 14, 'Tinjauan Pustaka dan State of the Art', 25.00, 60.00, '2025-11-26 09:13:44'),
(49, 14, 'Originalitas dan Kontribusi Ilmiah', 25.00, 90.00, '2025-11-26 09:13:44'),
(50, 14, 'Presentasi dan Kemampuan Komunikasi Ilmiah', 20.00, 75.00, '2025-11-26 09:13:44');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_penilaian`
--
ALTER TABLE `detail_penilaian`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_penilaian` (`id_penilaian`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_penilaian`
--
ALTER TABLE `detail_penilaian`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_penilaian`
--
ALTER TABLE `detail_penilaian`
  ADD CONSTRAINT `detail_penilaian_ibfk_1` FOREIGN KEY (`id_penilaian`) REFERENCES `penilaian_ujian` (`id_penilaian`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
