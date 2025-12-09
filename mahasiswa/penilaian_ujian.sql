-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 26 Nov 2025 pada 10.55
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
-- Struktur dari tabel `penilaian_ujian`
--

CREATE TABLE `penilaian_ujian` (
  `id_penilaian` int(11) NOT NULL,
  `id_registrasi` int(11) DEFAULT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `jenis_nilai` enum('promotor','penguji') DEFAULT NULL,
  `nilai_presentasi` decimal(5,2) DEFAULT NULL,
  `nilai_materi` decimal(5,2) DEFAULT NULL,
  `nilai_diskusi` decimal(5,2) DEFAULT NULL,
  `nilai_total` decimal(5,2) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_penilaian` timestamp NOT NULL DEFAULT current_timestamp(),
  `catatan_approval` text DEFAULT NULL,
  `status_revisi` enum('belum','diajukan','diterima','ditolak') DEFAULT 'belum',
  `file_revisi` varchar(255) DEFAULT NULL,
  `tanggal_revisi` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penilaian_ujian`
--

INSERT INTO `penilaian_ujian` (`id_penilaian`, `id_registrasi`, `id_dosen`, `jenis_nilai`, `nilai_presentasi`, `nilai_materi`, `nilai_diskusi`, `nilai_total`, `catatan`, `tanggal_penilaian`, `catatan_approval`, `status_revisi`, `file_revisi`, `tanggal_revisi`) VALUES
(10, 25, 49, 'promotor', NULL, NULL, NULL, 75.00, 'tolong diperbaiki lagi ya kakakkakakakakkak', '2025-11-26 08:28:10', NULL, 'belum', NULL, NULL),
(11, 25, 50, 'promotor', NULL, NULL, NULL, 79.50, 'tolong diperbaiki lagi ya dekk', '2025-11-26 09:01:00', NULL, 'belum', NULL, NULL),
(12, 25, 51, 'promotor', NULL, NULL, NULL, 76.00, 'semangat ya dek ya', '2025-11-26 09:09:56', NULL, 'belum', NULL, NULL),
(13, 25, 52, 'penguji', NULL, NULL, NULL, 100.00, 'aman aman aja ddekkk', '2025-11-26 09:10:56', NULL, 'belum', NULL, NULL),
(14, 25, 53, 'penguji', NULL, NULL, NULL, 67.50, 'saya kurang puas', '2025-11-26 09:13:44', NULL, 'belum', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `penilaian_ujian`
--
ALTER TABLE `penilaian_ujian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_registrasi` (`id_registrasi`),
  ADD KEY `id_dosen` (`id_dosen`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `penilaian_ujian`
--
ALTER TABLE `penilaian_ujian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `penilaian_ujian`
--
ALTER TABLE `penilaian_ujian`
  ADD CONSTRAINT `penilaian_ujian_ibfk_1` FOREIGN KEY (`id_registrasi`) REFERENCES `registrasi` (`id_registrasi`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_ujian_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
