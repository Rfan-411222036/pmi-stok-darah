-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jun 2026 pada 12.20
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
  `id_distribusi` int(11) NOT NULL,
  `id_bag` int(11) NOT NULL,
  `id_rs` int(11) NOT NULL,
  `tanggal_distribusi` datetime NOT NULL,
  `penerima` varchar(100) NOT NULL,
  `keperluan` text DEFAULT NULL,
  `no_permintaan` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `distribusi`
--

INSERT INTO `distribusi` (`id_distribusi`, `id_bag`, `id_rs`, `tanggal_distribusi`, `penerima`, `keperluan`, `no_permintaan`, `created_at`) VALUES
(1, 1, 1, '2025-11-15 09:00:00', 'Dr. Bambang', 'Operasi darurat', 'REQ-001/2025', '2025-11-29 08:33:38'),
(2, 3, 2, '2025-11-16 10:30:00', 'Dr. Siti', 'Transfusi pasien', 'REQ-002/2025', '2025-11-29 08:33:38'),
(3, 4, 3, '2025-11-17 14:00:00', 'Dr. Ahmad', 'Stock rutin', 'REQ-003/2025', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login`
--

CREATE TABLE `login` (
  `id_user` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','staff','bdrs','rs','pimpinan') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login`
--

INSERT INTO `login` (`id_user`, `email`, `password`, `nama`, `role`, `created_at`) VALUES
(1, 'erfan@gmail.com', 'admin123', 'Administrator', 'admin', '2025-11-29 08:33:38'),
(2, 'tiara@bdrs.com', 'tiara123', 'BDRS Tiara', 'bdrs', '2025-11-29 08:33:38'),
(3, 'rsjatisampurna@gmail.com', 'jatisampurna123', 'Admin RS Jati sampurna', 'rs', '2026-05-09 09:14:00'),
(4, 'eka@bdrs.com', 'eka123', 'BDRS Eka Hospital', 'bdrs', '2026-05-09 09:16:39'),
(5, 'bunda@gmail.com', 'bunda123', 'RS Bunda Bekasi', 'rs', '2026-05-09 09:17:37'),
(6, 'dinkes@gmail.com', 'dinkes123', 'Dinkes Kota Bekasi', 'pimpinan', '2026-05-09 09:18:32'),
(7, 'masmitra@bdrs.com', 'masmitra123', 'BDRS Mas Mitra', 'bdrs', '2026-06-07 09:00:44'),
(8, 'mitrakelpratama@bdrs.com', 'mitra123', 'BDRS Mitra Kel Pratama', 'bdrs', '2026-06-07 09:00:44'),
(11, 'rsumumdaerah@rs.com', '12345', 'RS Umum Daerah', 'rs', '2026-06-07 09:39:52'),
(12, 'rspremier@rs.com', '12345', 'RS Premier', 'rs', '2026-06-07 09:39:52'),
(13, 'rssiloam@rs.com', '12345', 'RS Siloam', 'rs', '2026-06-07 09:39:52'),
(14, 'rsmitrakeluarga@rs.com', '12345', 'RS Mitra Keluarga', 'rs', '2026-06-07 09:39:52'),
(15, 'rskhususjiwa@rs.com', '12345', 'RS Khusus Jiwa', 'rs', '2026-06-07 09:39:52'),
(25, 'rsjatisampurna@rs.com', 'e10adc3949ba59abbe56e057f20f883e', 'RS. Jati Sampurna', '', '2026-06-07 10:18:54'),
(26, 'rshelsajatirahayu@rs.com', 'e10adc3949ba59abbe56e057f20f883e', 'RS. Helsa Jati Rahayu', '', '2026-06-07 10:18:54'),
(27, 'rssetohasbadi@rs.com', 'e10adc3949ba59abbe56e057f20f883e', 'RS. Seto Hasbadi', '', '2026-06-07 10:18:54'),
(28, 'rscitraharapan@rs.com', 'e10adc3949ba59abbe56e057f20f883e', 'RS. Citra Harapan', '', '2026-06-07 10:18:54'),
(29, 'rskartikahusada@rs.com', 'e10adc3949ba59abbe56e057f20f883e', 'RS. Kartika Husada', '', '2026-06-07 10:18:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemusnahan`
--

CREATE TABLE `pemusnahan` (
  `id_pemusnahan` int(11) NOT NULL,
  `id_bag` int(11) NOT NULL,
  `tanggal_pemusnahan` datetime NOT NULL,
  `alasan` enum('expired','rusak','lainnya') DEFAULT 'expired',
  `keterangan` text DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemusnahan`
--

INSERT INTO `pemusnahan` (`id_pemusnahan`, `id_bag`, `tanggal_pemusnahan`, `alasan`, `keterangan`, `petugas`, `created_at`) VALUES
(1, 1, '2025-12-01 10:00:00', 'expired', 'Darah expired melampaui tanggal', 'Budi Santoso', '2025-11-29 08:33:38'),
(2, 2, '2025-12-02 14:30:00', 'rusak', 'Kemasan rusak saat penyimpanan', 'Siti Nurhaliza', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permintaan`
--

CREATE TABLE `permintaan` (
  `id_permintaan` int(11) UNSIGNED NOT NULL,
  `id_rs` int(11) NOT NULL,
  `id_produsen` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `gol_dar` varchar(10) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `nama_penerima` varchar(255) DEFAULT NULL,
  `id_bag` int(11) DEFAULT NULL,
  `approval_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `permintaan`
--

INSERT INTO `permintaan` (`id_permintaan`, `id_rs`, `id_produsen`, `jumlah`, `gol_dar`, `jenis`, `keterangan`, `status`, `created_by`, `approved_by`, `approved_at`, `created_at`, `nama_penerima`, `id_bag`, `approval_note`) VALUES
(1, 1, 1, 1, 'A', 'PRC', 'Butuh segera ada pasien lansia', 'approved', 1, 2, '2026-06-07 09:01:21', '2026-06-07 08:55:37', NULL, NULL, NULL),
(2, 3, 3, 1, 'A', 'PRC', 'ANEMIA', 'approved', 5, 8, '2026-06-07 09:46:15', '2026-06-07 09:34:39', 'Suprianto Rizky', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produsen`
--

CREATE TABLE `produsen` (
  `id_produsen` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
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

INSERT INTO `produsen` (`id_produsen`, `id_user`, `nama`, `jenis`, `jenis_darah`, `no_kantong`, `status`, `alamat`, `is_active`, `created_at`) VALUES
(1, 2, 'BDRS Tiara', 'pemerintah', 'A+', 'BDRS001', 'masih layak', 'Bekasi', 1, '2025-11-29 08:33:38'),
(2, 7, 'BDRS Mas Mitra', 'pemerintah', 'O+', 'BDRS002', 'masih layak', 'Bekasi', 1, '2025-11-29 08:33:38'),
(3, 8, 'BDRS Mitra Kel Pratama', 'pemerintah', 'B+', 'BDRS003', 'masih layak', 'Bekasi', 1, '2025-11-29 08:33:38'),
(4, 4, 'BDRS Eka Hospital', 'pemerintah', 'AB+', 'BDRS004', 'masih layak', 'Bekasi', 1, '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `return_darah`
--

CREATE TABLE `return_darah` (
  `id_return` int(11) NOT NULL,
  `id_distribusi` int(11) NOT NULL,
  `id_bag` int(11) NOT NULL,
  `id_rs` int(11) NOT NULL,
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

INSERT INTO `return_darah` (`id_return`, `id_distribusi`, `id_bag`, `id_rs`, `tanggal_retur`, `alasan_return`, `kondisi_darah`, `ditangani_oleh`, `keterangan`, `created_at`) VALUES
(1, 1, 1, 1, '2025-11-22 11:00:00', 'Tidak digunakan', 'baik', 'Dr. Bambang', 'Darah kembali dalam kondisi baik', '2025-11-29 08:33:38'),
(2, 2, 3, 2, '2025-11-23 15:30:00', 'Excess stock', 'baik', 'Dr. Siti', 'Surplus stock rumah sakit', '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rumah_sakit`
--

CREATE TABLE `rumah_sakit` (
  `id_rs` int(11) NOT NULL,
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

INSERT INTO `rumah_sakit` (`id_rs`, `nama_rs`, `alamat`, `telepon`, `email`, `jenis_rs`, `is_active`, `created_at`) VALUES
(1, 'RS. Jati Sampurna', 'Jl. Rumah Sakit No. 1', '021-1234567', NULL, 'pemerintah', 1, '2025-11-29 08:33:38'),
(2, 'RS. Helsa Jati Rahayu', 'Jl. Premier No. 2', '021-2345678', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(3, 'RS. Seto Hasbadi', 'Jl. Siloam No. 3', '021-3456789', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(4, 'RS. Citra Harapan', 'Jl. Mitra No. 4', '021-4567890', NULL, 'swasta', 1, '2025-11-29 08:33:38'),
(5, 'RS. Kartika Husada', 'Jl. Jiwa No. 5', '021-5678901', NULL, 'khusus', 1, '2025-11-29 08:33:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok`
--

CREATE TABLE `stok` (
  `id_bag` int(11) NOT NULL,
  `no_kantong` varchar(20) NOT NULL,
  `id_produsen` int(11) NOT NULL,
  `jenis_darah` varchar(10) NOT NULL,
  `gol_dar` varchar(4) NOT NULL,
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

INSERT INTO `stok` (`id_bag`, `no_kantong`, `id_produsen`, `jenis_darah`, `gol_dar`, `rhesus`, `volume`, `tanggal_produksi`, `tanggal_expired`, `status`, `keterangan`, `created_at`) VALUES
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
  ADD PRIMARY KEY (`id_distribusi`),
  ADD KEY `id_bag` (`id_bag`),
  ADD KEY `id_rs` (`id_rs`);

--
-- Indeks untuk tabel `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD PRIMARY KEY (`id_pemusnahan`),
  ADD KEY `id_bag` (`id_bag`);

--
-- Indeks untuk tabel `permintaan`
--
ALTER TABLE `permintaan`
  ADD PRIMARY KEY (`id_permintaan`);

--
-- Indeks untuk tabel `produsen`
--
ALTER TABLE `produsen`
  ADD PRIMARY KEY (`id_produsen`);

--
-- Indeks untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  ADD PRIMARY KEY (`id_return`),
  ADD KEY `id_distribusi` (`id_distribusi`),
  ADD KEY `id_bag` (`id_bag`),
  ADD KEY `id_rs` (`id_rs`);

--
-- Indeks untuk tabel `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  ADD PRIMARY KEY (`id_rs`);

--
-- Indeks untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`id_bag`),
  ADD UNIQUE KEY `no_kantong` (`no_kantong`),
  ADD KEY `id_produsen` (`id_produsen`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `distribusi`
--
ALTER TABLE `distribusi`
  MODIFY `id_distribusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `login`
--
ALTER TABLE `login`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  MODIFY `id_pemusnahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `permintaan`
--
ALTER TABLE `permintaan`
  MODIFY `id_permintaan` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `produsen`
--
ALTER TABLE `produsen`
  MODIFY `id_produsen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  MODIFY `id_return` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `rumah_sakit`
--
ALTER TABLE `rumah_sakit`
  MODIFY `id_rs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `stok`
--
ALTER TABLE `stok`
  MODIFY `id_bag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `distribusi`
--
ALTER TABLE `distribusi`
  ADD CONSTRAINT `distribusi_ibfk_1` FOREIGN KEY (`id_bag`) REFERENCES `stok` (`id_bag`),
  ADD CONSTRAINT `distribusi_ibfk_2` FOREIGN KEY (`id_rs`) REFERENCES `rumah_sakit` (`id_rs`);

--
-- Ketidakleluasaan untuk tabel `pemusnahan`
--
ALTER TABLE `pemusnahan`
  ADD CONSTRAINT `pemusnahan_ibfk_1` FOREIGN KEY (`id_bag`) REFERENCES `stok` (`id_bag`);

--
-- Ketidakleluasaan untuk tabel `return_darah`
--
ALTER TABLE `return_darah`
  ADD CONSTRAINT `return_darah_ibfk_1` FOREIGN KEY (`id_distribusi`) REFERENCES `distribusi` (`id_distribusi`),
  ADD CONSTRAINT `return_darah_ibfk_2` FOREIGN KEY (`id_bag`) REFERENCES `stok` (`id_bag`),
  ADD CONSTRAINT `return_darah_ibfk_3` FOREIGN KEY (`id_rs`) REFERENCES `rumah_sakit` (`id_rs`);

--
-- Ketidakleluasaan untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD CONSTRAINT `stok_ibfk_1` FOREIGN KEY (`id_produsen`) REFERENCES `produsen` (`id_produsen`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
