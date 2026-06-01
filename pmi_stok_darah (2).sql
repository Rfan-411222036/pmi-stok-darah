-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Bulan Mei 2026 pada 10.34
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
-- Database: `pmi_stok_darah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `distribusi`
--

CREATE TABLE `distribusi` (
  `iddistribusi` int(11) NOT NULL,
  `idbag` int(11) NOT NULL,
  `idrs` int(11) NOT NULL,
  `tanggal_distribusi` datetime NOT NULL,
  `penerima` varchar(100) NOT NULL,
  `keperluan` text DEFAULT NULL,
  `no_permintaan` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `distribusi`
--

INSERT INTO `distribusi` (`iddistribusi`, `idbag`, `idrs`, `tanggal_distribusi`, `penerima`, `keperluan`, `no_permintaan`, `created_at`) VALUES
(1, 1, 1, '2025-11-15 09:00:00', 'Dr. Bambang', 'Operasi darurat', 'REQ-001/2025', '2025-11-29 08:33:38'),
(2, 3, 2, '2025-11-16 10:30:00', 'Dr. Siti', 'Transfusi pasien', 'REQ-002/2025', '2025-11-29 08:33:38'),
(3, 4, 3, '2025-11-17 14:00:00', 'Dr. Ahmad', 'Stock rutin', 'REQ-003/2025', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login`
--

CREATE TABLE `login` (
  `iduser` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','staff','bdrs','rs','pimpinan') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login`
--

INSERT INTO `login` (`iduser`, `email`, `password`, `nama`, `role`, `created_at`) VALUES
(1, 'erfan@gmail.com', 'admin123', 'Administrator', 'admin', '2025-11-29 08:33:38'),
(2, 'bdrssiloam@gmail.com', 'siloam123', 'BDRS Siloam Hospital', 'bdrs', '2025-11-29 08:33:38'),
(3, 'rsjatisampurna@gmail.com', 'jatisampurna123', 'Admin RS Jati sampurna', 'rs', '2026-05-09 09:14:00'),
(4, 'bdrseka@gmail.com', 'ekahospital', 'BDRS Eka Hospital', 'bdrs', '2026-05-09 09:16:39'),
(5, 'bunda@gmail.com', 'bunda123', 'RS Bunda Bekasi', 'rs', '2026-05-09 09:17:37'),
(6, 'dinkes@gmail.com', 'dinkes123', 'Dinkes Kota Bekasi', 'pimpinan', '2026-05-09 09:18:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemusnahan`
--

CREATE TABLE `pemusnahan` (
  `idpemusnahan` int(11) NOT NULL,
  `idbag` int(11) NOT NULL,
  `tanggal_pemusnahan` datetime NOT NULL,
  `alasan` enum('expired','rusak','lainnya') DEFAULT 'expired',
  `keterangan` text DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemusnahan`
--

INSERT INTO `pemusnahan` (`idpemusnahan`, `idbag`, `tanggal_pemusnahan`, `alasan`, `keterangan`, `petugas`, `created_at`) VALUES
(1, 1, '2025-12-01 10:00:00', 'expired', 'Darah expired melampaui tanggal', 'Budi Santoso', '2025-11-29 08:33:38'),
(2, 2, '2025-12-02 14:30:00', 'rusak', 'Kemasan rusak saat penyimpanan', 'Siti Nurhaliza', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produsen`
--

CREATE TABLE `produsen` (
  `idprodusen` int(11) NOT NULL,
  `iduser` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('umum','pemerintah','swasta') DEFAULT 'umum',
  `jenis_darah` varchar(10) DEFAULT NULL,
  `no_kantong` varchar(20) DEFAULT NULL,
  `status` enum('masih layak','expired') DEFAULT 'masih layak',
  `alamat` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produsen`
--

INSERT INTO `produsen` (`idprodusen`, `iduser`, `nama`, `jenis`, `jenis_darah`, `no_kantong`, `status`, `alamat`, `is_active`, `created_at`) VALUES
(1, NULL, 'Donor Umum', 'umum', 'A+', 'KB001/XI/2024', 'masih layak', 'PMI Kota', 1, '2025-11-29 08:33:38'),
(2, 2, 'BDRS Siloam Hospital', 'pemerintah', 'O+', 'KB003/XI/2024', 'masih layak', 'Jl. Siloam No. 3', 1, '2025-11-29 08:33:38'),
(3, NULL, 'Universitas Sehat', 'pemerintah', 'O+', 'KB002/XI/2024', 'masih layak', 'Jl. Kampus No. 45', 1, '2025-11-29 08:33:38'),
(4, 4, 'BDRS Eka Hospital', 'pemerintah', 'A+', 'KB005/XI/2024', 'expired', 'Jl. Eka No. 10', 1, '2025-11-29 08:33:38'),
(5, NULL, 'PT. Donor Sehat', 'swasta', 'B+', NULL, 'masih layak', 'Jl. Perusahaan No. 123', 1, '2025-11-29 08:33:38'),
(6, NULL, 'Karyawan PT. Maju', 'umum', 'AB+', NULL, 'masih layak', 'Jl. Industri No. 67', 1, '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `return_darah`
--

CREATE TABLE `return_darah` (
  `idreturn` int(11) NOT NULL,
  `iddistribusi` int(11) NOT NULL,
  `idbag` int(11) NOT NULL,
  `idrs` int(11) NOT NULL,
  `tanggal_retur` datetime NOT NULL,
  `alasan_return` varchar(100) DEFAULT NULL,
  `kondisi_darah` enum('baik','rusak','expired') DEFAULT 'baik',
  `ditangani_oleh` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `return_darah`
--

INSERT INTO `return_darah` (`idreturn`, `iddistribusi`, `idbag`, `idrs`, `tanggal_retur`, `alasan_return`, `kondisi_darah`, `ditangani_oleh`, `keterangan`, `created_at`) VALUES
(1, 1, 1, 1, '2025-11-22 11:00:00', 'Tidak digunakan', 'baik', 'Dr. Bambang', 'Darah kembali dalam kondisi baik', '2025-11-29 08:33:38'),
(2, 2, 3, 2, '2025-11-23 15:30:00', 'Excess stock', 'baik', 'Dr. Siti', 'Surplus stock rumah sakit', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rumah_sakit`
--

CREATE TABLE `rumah_sakit` (
  `idrs` int(11) NOT NULL,
  `nama_rs` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `jenis_rs` enum('umum','khusus','swasta','pemerintah') DEFAULT 'umum',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rumah_sakit`
--

INSERT INTO `rumah_sakit` (`idrs`, `nama_rs`, `alamat`, `telepon`, `email`, `jenis_rs`, `is_active`, `created_at`) VALUES
(1, 'RS Umum Daerah', 'Jl. Rumah Sakit No. 1', '021-1234567', NULL, 'pemerintah', 1, '2025-11-29 08:33:38'),
(2, 'RS Premier', 'Jl. Premier No. 2', '021-2345678', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(3, 'RS Siloam', 'Jl. Siloam No. 3', '021-3456789', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(4, 'RS Mitra Keluarga', 'Jl. Mitra No. 4', '021-4567890', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(5, 'RS Khusus Jiwa', 'Jl. Jiwa No. 5', '021-5678901', NULL, 'khusus', 1, '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok`
--

CREATE TABLE `stok` (
  `idbag` int(11) NOT NULL,
  `no_kantong` varchar(20) NOT NULL,
  `idprodusen` int(11) NOT NULL,
  `jenisdarah` varchar(10) NOT NULL,
  `goldar` varchar(4) NOT NULL,
  `rhesus` enum('+','-') DEFAULT '+',
  `volume` int(11) NOT NULL COMMENT 'dalam ml',
  `tanggal_produksi` date NOT NULL,
  `tanggal_expired` date NOT NULL,
  `status` enum('tersedia','terdistribusi','expired','musnah') DEFAULT 'tersedia',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stok`
--

INSERT INTO `stok` (`idbag`, `no_kantong`, `idprodusen`, `jenisdarah`, `goldar`, `rhesus`, `volume`, `tanggal_produksi`, `tanggal_expired`, `status`, `keterangan`, `created_at`) VALUES
(1, 'KB001/XI/2024', 1, 'Whole', 'A', '+', 450, '2024-11-01', '2024-12-08', 'tersedia', 'Darah berkualitas tinggi', '2025-11-29 08:33:38'),
(2, 'KB002/XI/2024', 2, 'PRC', 'B', '+', 250, '2024-11-02', '2025-12-07', 'tersedia', 'Red Cell Concentrate', '2025-11-29 08:33:38'),
(3, 'KB003/XI/2024', 3, 'Whole', 'O', '+', 450, '2025-11-04', '2025-12-11', 'tersedia', 'Darah O universal', '2025-11-29 08:33:38'),
(4, 'KB004/XI/2024', 1, 'PRC', 'AB', '+', 250, '2024-11-04', '2024-12-04', 'tersedia', 'Red Cell Concentrate AB', '2025-11-29 08:33:38'),
(5, 'KB005/XI/2024', 4, 'Whole', 'A', '-', 450, '2024-11-05', '2024-12-05', 'tersedia', 'Darah A negatif', '2025-11-29 08:33:38');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `distribusi`
--
ALTER TABLE `distribusi`
  ADD PRIMARY KEY (`iddistribusi`),
  ADD KEY `idbag` (`idbag`),
  ADD KEY `idrs` (`idrs`);

--
-- Indeks untuk tabel `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`iduser`);

--
-- Indeks untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD PRIMARY KEY (`idpemusnahan`),
  ADD KEY `idbag` (`idbag`);

--
-- Indeks untuk tabel `produsen`
--
ALTER TABLE `produsen`
  ADD PRIMARY KEY (`idprodusen`);

--
-- Indeks untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  ADD PRIMARY KEY (`idreturn`),
  ADD KEY `iddistribusi` (`iddistribusi`),
  ADD KEY `idbag` (`idbag`),
  ADD KEY `idrs` (`idrs`);

--
-- Indeks untuk tabel `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  ADD PRIMARY KEY (`idrs`);

--
-- Indeks untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`idbag`),
  ADD UNIQUE KEY `no_kantong` (`no_kantong`),
  ADD KEY `idprodusen` (`idprodusen`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `distribusi`
--
ALTER TABLE `distribusi`
  MODIFY `iddistribusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `login`
--
ALTER TABLE `login`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  MODIFY `idpemusnahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `produsen`
--
ALTER TABLE `produsen`
  MODIFY `idprodusen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  MODIFY `idreturn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  MODIFY `idrs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `stok`
--
ALTER TABLE `stok`
  MODIFY `idbag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `distribusi`
--
ALTER TABLE `distribusi`
  ADD CONSTRAINT `distribusi_ibfk_1` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`),
  ADD CONSTRAINT `distribusi_ibfk_2` FOREIGN KEY (`idrs`) REFERENCES `rumah_sakit` (`idrs`);

--
-- Ketidakleluasaan untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD CONSTRAINT `pemusnahan_ibfk_1` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`);

--
-- Ketidakleluasaan untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  ADD CONSTRAINT `return_darah_ibfk_1` FOREIGN KEY (`iddistribusi`) REFERENCES `distribusi` (`iddistribusi`),
  ADD CONSTRAINT `return_darah_ibfk_2` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`),
  ADD CONSTRAINT `return_darah_ibfk_3` FOREIGN KEY (`idrs`) REFERENCES `rumah_sakit` (`idrs`);

--
-- Ketidakleluasaan untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`idprodusen`) REFERENCES `produsen` (`idprodusen`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
