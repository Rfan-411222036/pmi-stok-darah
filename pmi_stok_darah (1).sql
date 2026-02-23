-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 29, 2025 at 11:58 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `distribusi`
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
-- Dumping data for table `distribusi`
--

INSERT INTO `distribusi` (`iddistribusi`, `idbag`, `idrs`, `tanggal_distribusi`, `penerima`, `keperluan`, `no_permintaan`, `created_at`) VALUES
(1, 6, 6, '2025-11-29 09:29:00', 'rizky kapal laut', 'operasi', '123', '2025-11-29 09:30:11'),
(2, 7, 2, '2025-11-29 09:32:00', 'Pasien Thalasemi', 'Transfusi HB Rendah', 'RM20122025', '2025-11-29 09:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `iduser` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`iduser`, `email`, `password`, `nama`, `role`, `created_at`) VALUES
(3, 'rikisetiyopambudi@gmail.com', 'rikisetiyopambudi@gmail.com', 'Riki Setiyo Pambudi', 'admin', '2025-11-29 09:08:31'),
(4, 'ervan@gmail.com', 'ervan@gmail.com', 'ervan fatoni', 'staff', '2025-11-29 09:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `pemusnahan`
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
-- Dumping data for table `pemusnahan`
--

INSERT INTO `pemusnahan` (`idpemusnahan`, `idbag`, `tanggal_pemusnahan`, `alasan`, `keterangan`, `petugas`, `created_at`) VALUES
(1, 1, '2025-11-29 09:29:00', 'expired', 'Ervan', 'Ervan', '2025-11-29 09:29:15'),
(2, 3, '2025-11-29 09:33:00', 'expired', 'dimusnhkan', 'Ervan', '2025-11-29 09:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `produsen`
--

CREATE TABLE `produsen` (
  `idprodusen` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis` enum('perorangan','perusahaan','instansi') DEFAULT 'perorangan',
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produsen`
--

INSERT INTO `produsen` (`idprodusen`, `nama`, `jenis`, `alamat`, `telepon`, `email`, `is_active`, `created_at`) VALUES
(1, 'Donor Umum', 'perorangan', 'PMI Kota', '08123456789', NULL, 1, '2025-11-29 08:33:38'),
(2, 'PT. Donor Sehat', 'perusahaan', 'Jl. Perusahaan No. 123', '08234567890', NULL, 1, '2025-11-29 08:33:38'),
(3, 'Universitas Sehat', 'instansi', 'Jl. Kampus No. 45', '08345678901', NULL, 1, '2025-11-29 08:33:38'),
(4, 'Karyawan PT. Maju', 'perorangan', 'Jl. Industri No. 67', '08456789012', NULL, 1, '2025-11-29 08:33:38'),
(5, 'Rizky kapal laut', 'perorangan', '', '08367463746', 'kevinsanjaya@gmail.com', 0, '2025-11-29 09:19:39'),
(6, 'mr.Riki', 'perorangan', 'bekasi', '0371', 'riki@gmail.com', 1, '2025-11-29 09:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `return_darah`
--

CREATE TABLE `return_darah` (
  `idreturn` int(11) NOT NULL,
  `iddistribusi` int(11) NOT NULL,
  `idbag` int(11) NOT NULL,
  `idrs` int(11) NOT NULL,
  `tanggal_return` datetime NOT NULL,
  `alasan_return` text NOT NULL,
  `kondisi_darah` enum('baik','rusak') DEFAULT 'baik',
  `ditangani_oleh` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_darah`
--

INSERT INTO `return_darah` (`idreturn`, `iddistribusi`, `idbag`, `idrs`, `tanggal_return`, `alasan_return`, `kondisi_darah`, `ditangani_oleh`, `keterangan`, `created_at`) VALUES
(1, 2, 7, 2, '2025-11-29 10:01:00', 'Pasien tidak membutuhkan', 'baik', 'ervan', 'ervan', '2025-11-29 10:02:15'),
(2, 1, 6, 6, '2025-11-29 10:09:00', 'Tidak digunakan', 'baik', 'ervan', '', '2025-11-29 10:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `rumah_sakit`
--

CREATE TABLE `rumah_sakit` (
  `idrs` int(11) NOT NULL,
  `nama_rs` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `jenis_rs` enum('umum','khusus','swasta','pemerintah') DEFAULT 'umum',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rumah_sakit`
--

INSERT INTO `rumah_sakit` (`idrs`, `nama_rs`, `alamat`, `telepon`, `email`, `jenis_rs`, `is_active`, `created_at`) VALUES
(1, 'RS Umum Daerah Kota Bekasi', 'Jl. Rumah Sakit No. 1', '021-1234567', '', 'pemerintah', 1, '2025-11-29 08:33:38'),
(2, 'RS Premier', 'Jl. Premier No. 2', '021-2345678', 'erfan@gmail.com', 'swasta', 0, '2025-11-29 08:33:38'),
(6, 'RS Hermina Bekasi', 'kapallaut@gmail.co', '08367463746', 'kapallaut@gmail.co', 'umum', 1, '2025-11-29 09:14:10'),
(7, 'RS. Mitra Keluarga', 'bekasi timur', '03275', 'mitra@gmail.com', 'umum', 1, '2025-11-29 09:36:54');

-- --------------------------------------------------------

--
-- Table structure for table `stok`
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
-- Dumping data for table `stok`
--

INSERT INTO `stok` (`idbag`, `no_kantong`, `idprodusen`, `jenisdarah`, `goldar`, `rhesus`, `volume`, `tanggal_produksi`, `tanggal_expired`, `status`, `keterangan`, `created_at`) VALUES
(1, 'KB001/XI/2024', 1, 'Whole', 'A', '+', 450, '2024-11-01', '2024-12-01', 'musnah', NULL, '2025-11-29 08:33:38'),
(2, 'KB002/XI/2024', 2, 'PRC', 'B', '+', 250, '2024-11-02', '2024-12-02', 'tersedia', NULL, '2025-11-29 08:33:38'),
(3, 'KB003/XI/2024', 3, 'Whole', 'O', '+', 450, '2024-11-03', '2024-12-03', 'musnah', NULL, '2025-11-29 08:33:38'),
(4, 'KB004/XI/2024', 1, 'PRC', 'AB', '+', 5000, '2024-11-04', '2024-12-04', 'tersedia', '', '2025-11-29 08:33:38'),
(5, 'KB005/XI/2024', 4, 'Whole', 'A', '-', 450, '2024-11-05', '2024-12-05', 'tersedia', NULL, '2025-11-29 08:33:38'),
(6, '1k', 5, 'PRC', 'AB', '+', 450, '2025-11-29', '2026-01-03', 'tersedia', '', '2025-11-29 09:23:54'),
(7, '40JK00076', 6, 'Whole', 'O', '+', 350, '2025-11-29', '2026-01-03', 'tersedia', 'it', '2025-11-29 09:32:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD PRIMARY KEY (`iddistribusi`),
  ADD KEY `idbag` (`idbag`),
  ADD KEY `idrs` (`idrs`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`iduser`);

--
-- Indexes for table `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD PRIMARY KEY (`idpemusnahan`),
  ADD KEY `idbag` (`idbag`);

--
-- Indexes for table `produsen`
--
ALTER TABLE `produsen`
  ADD PRIMARY KEY (`idprodusen`);

--
-- Indexes for table `return_darah`
--
ALTER TABLE `return_darah`
  ADD PRIMARY KEY (`idreturn`),
  ADD KEY `iddistribusi` (`iddistribusi`),
  ADD KEY `idbag` (`idbag`),
  ADD KEY `idrs` (`idrs`);

--
-- Indexes for table `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  ADD PRIMARY KEY (`idrs`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`idbag`),
  ADD UNIQUE KEY `no_kantong` (`no_kantong`),
  ADD KEY `idprodusen` (`idprodusen`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distribusi`
--
ALTER TABLE `distribusi`
  MODIFY `iddistribusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pemusnahan`
--
ALTER TABLE `pemusnahan`
  MODIFY `idpemusnahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `produsen`
--
ALTER TABLE `produsen`
  MODIFY `idprodusen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `return_darah`
--
ALTER TABLE `return_darah`
  MODIFY `idreturn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  MODIFY `idrs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stok`
--
ALTER TABLE `stok`
  MODIFY `idbag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD CONSTRAINT `distribusi_ibfk_1` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`),
  ADD CONSTRAINT `distribusi_ibfk_2` FOREIGN KEY (`idrs`) REFERENCES `rumah_sakit` (`idrs`);

--
-- Constraints for table `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD CONSTRAINT `pemusnahan_ibfk_1` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`);

--
-- Constraints for table `return_darah`
--
ALTER TABLE `return_darah`
  ADD CONSTRAINT `return_darah_ibfk_1` FOREIGN KEY (`iddistribusi`) REFERENCES `distribusi` (`iddistribusi`),
  ADD CONSTRAINT `return_darah_ibfk_2` FOREIGN KEY (`idbag`) REFERENCES `stok` (`idbag`),
  ADD CONSTRAINT `return_darah_ibfk_3` FOREIGN KEY (`idrs`) REFERENCES `rumah_sakit` (`idrs`);

--
-- Constraints for table `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`idprodusen`) REFERENCES `produsen` (`idprodusen`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
