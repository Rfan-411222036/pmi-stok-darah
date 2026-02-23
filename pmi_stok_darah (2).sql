-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 06, 2025 at 09:14 AM
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
(1, 1, 1, '2025-11-15 09:00:00', 'Dr. Bambang', 'Operasi darurat', 'REQ-001/2025', '2025-11-29 08:33:38'),
(2, 3, 2, '2025-11-16 10:30:00', 'Dr. Siti', 'Transfusi pasien', 'REQ-002/2025', '2025-11-29 08:33:38'),
(3, 4, 3, '2025-11-17 14:00:00', 'Dr. Ahmad', 'Stock rutin', 'REQ-003/2025', '2025-11-29 08:33:38');

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
(1, 'erfan@gmail.com', 'admin123', 'Administrator', 'admin', '2025-11-29 08:33:38'),
(2, 'staff@gmail.com', 'staff123', 'Staff PMI', 'staff', '2025-11-29 08:33:38');

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
(1, 1, '2025-12-01 10:00:00', 'expired', 'Darah expired melampaui tanggal', 'Budi Santoso', '2025-11-29 08:33:38'),
(2, 2, '2025-12-02 14:30:00', 'rusak', 'Kemasan rusak saat penyimpanan', 'Siti Nurhaliza', '2025-11-29 08:33:38');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produsen`
--

INSERT INTO `produsen` (`idprodusen`, `nama`, `jenis`, `alamat`, `telepon`, `email`, `created_at`) VALUES
(1, 'Donor Umum', 'perorangan', 'PMI Kota', '08123456789', NULL, '2025-11-29 08:33:38'),
(2, 'PT. Donor Sehat', 'perusahaan', 'Jl. Perusahaan No. 123', '08234567890', NULL, '2025-11-29 08:33:38'),
(3, 'Universitas Sehat', 'instansi', 'Jl. Kampus No. 45', '08345678901', NULL, '2025-11-29 08:33:38'),
(4, 'Karyawan PT. Maju', 'perorangan', 'Jl. Industri No. 67', '08456789012', NULL, '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Table structure for table `return_darah`
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
-- Dumping data for table `return_darah`
--

INSERT INTO `return_darah` (`idreturn`, `iddistribusi`, `idbag`, `idrs`, `tanggal_retur`, `alasan_return`, `kondisi_darah`, `ditangani_oleh`, `keterangan`, `created_at`) VALUES
(1, 1, 1, 1, '2025-11-22 11:00:00', 'Tidak digunakan', 'baik', 'Dr. Bambang', 'Darah kembali dalam kondisi baik', '2025-11-29 08:33:38'),
(2, 2, 3, 2, '2025-11-23 15:30:00', 'Excess stock', 'baik', 'Dr. Siti', 'Surplus stock rumah sakit', '2025-11-29 08:33:38');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rumah_sakit`
--

INSERT INTO `rumah_sakit` (`idrs`, `nama_rs`, `alamat`, `telepon`, `email`, `jenis_rs`, `created_at`) VALUES
(1, 'RS Umum Daerah', 'Jl. Rumah Sakit No. 1', '021-1234567', NULL, 'pemerintah', '2025-11-29 08:33:38'),
(2, 'RS Premier', 'Jl. Premier No. 2', '021-2345678', NULL, 'swasta', '2025-11-29 08:33:38'),
(3, 'RS Siloam', 'Jl. Siloam No. 3', '021-3456789', NULL, 'swasta', '2025-11-29 08:33:38'),
(4, 'RS Mitra Keluarga', 'Jl. Mitra No. 4', '021-4567890', NULL, 'swasta', '2025-11-29 08:33:38'),
(5, 'RS Khusus Jiwa', 'Jl. Jiwa No. 5', '021-5678901', NULL, 'khusus', '2025-11-29 08:33:38');

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
(1, 'KB001/XI/2024', 1, 'Whole', 'A', '+', 450, '2024-11-01', '2024-12-01', 'tersedia', 'Darah berkualitas tinggi', '2025-11-29 08:33:38'),
(2, 'KB002/XI/2024', 2, 'PRC', 'B', '+', 250, '2024-11-02', '2024-12-02', 'tersedia', 'Red Cell Concentrate', '2025-11-29 08:33:38'),
(3, 'KB003/XI/2024', 3, 'Whole', 'O', '+', 450, '2024-11-03', '2024-12-03', 'tersedia', 'Darah O universal', '2025-11-29 08:33:38'),
(4, 'KB004/XI/2024', 1, 'PRC', 'AB', '+', 250, '2024-11-04', '2024-12-04', 'tersedia', 'Red Cell Concentrate AB', '2025-11-29 08:33:38'),
(5, 'KB005/XI/2024', 4, 'Whole', 'A', '-', 450, '2024-11-05', '2024-12-05', 'tersedia', 'Darah A negatif', '2025-11-29 08:33:38');

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
  MODIFY `iddistribusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pemusnahan`
--
ALTER TABLE `pemusnahan`
  MODIFY `idpemusnahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `produsen`
--
ALTER TABLE `produsen`
  MODIFY `idprodusen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `return_darah`
--
ALTER TABLE `return_darah`
  MODIFY `idreturn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  MODIFY `idrs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stok`
--
ALTER TABLE `stok`
  MODIFY `idbag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
