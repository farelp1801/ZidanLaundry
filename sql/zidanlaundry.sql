-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2024 at 05:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zidanlaundry`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `kode_admin` char(3) NOT NULL,
  `admin` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`kode_admin`, `admin`) VALUES
('C01', 'mel'),
('C02', 'yul'),
('C03', 'zul'),
('C04', 'diwi'),
('C05', 'siddiq');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `kode_invoice` char(4) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `id_pelanggan` char(4) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `id_layanan` char(4) DEFAULT NULL,
  `kode_jenis_pembayaran` char(3) DEFAULT NULL,
  `kode_admin` char(3) DEFAULT NULL,
  `id_pengerjaan` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`kode_invoice`, `tanggal_masuk`, `tanggal_keluar`, `id_pelanggan`, `jumlah`, `id_layanan`, `kode_jenis_pembayaran`, `kode_admin`, `id_pengerjaan`, `id`) VALUES
('A001', '2024-06-03', '2024-06-04', 'S001', 1, 'L001', 'T01', 'C02', 1, 39),
('A001', '2024-06-03', '2024-06-04', 'S001', 1, 'L005', 'T01', 'C02', 1, 40),
('A002', '2024-06-04', '2024-06-05', 'S001', 1, 'L001', 'T01', 'C02', 1, 41),
('A002', '2024-06-04', '2024-06-05', 'S001', 1, 'L005', 'T01', 'C02', 1, 42),
('A004', '2024-07-03', '2024-07-03', 'S001', 1, 'L001', 'T01', 'C02', 1, 45),
('A004', '2024-07-03', '2024-07-03', 'S001', 1, 'L005', 'T01', 'C02', 1, 46),
('A005', '2024-07-03', '2024-07-03', 'S003', 2, 'L001', 'T01', 'C02', 1, 47),
('A005', '2024-07-03', '2024-07-03', 'S003', 1, 'L005', 'T01', 'C02', 1, 48),
('A006', '2024-07-03', '2024-07-03', 'S001', 3, 'L001', 'T01', 'C02', 1, 49),
('A006', '2024-07-03', '2024-07-03', 'S001', 4, 'L005', 'T01', 'C02', 1, 50),
('A007', '2024-07-03', NULL, 'S005', 1, 'L003', 'T02', 'C02', 2, 51);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_pembayaran`
--

CREATE TABLE `jenis_pembayaran` (
  `kode_jenis_pembayaran` char(3) NOT NULL,
  `jenis_pembayaran` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_pembayaran`
--

INSERT INTO `jenis_pembayaran` (`kode_jenis_pembayaran`, `jenis_pembayaran`) VALUES
('T01', 'tunai'),
('T02', 'transfer bank');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` char(4) NOT NULL,
  `layanan` varchar(50) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `layanan`, `harga`) VALUES
('L001', 'cuci saja / kg', 4000.00),
('L002', 'cuci+setrika / kg', 5000.00),
('L003', 'express(1hari siap) / kg', 7000.00),
('L004', 'selimut / buah', 10000.00),
('L005', 'bed cover / buah', 15000.00),
('L006', 'boneka / buah', 10000.00),
('L007', 'gorden / meter', 6000.00),
('L008', 'bantal/guling / buah', 9000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` char(4) NOT NULL,
  `nama_pelanggan` varchar(50) DEFAULT NULL,
  `kontak_pelanggan` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `kontak_pelanggan`) VALUES
('S001', 'Zidan', '081355225500'),
('S002', 'Dion', '085145778908'),
('S003', 'Siddiq', '082345546787'),
('S004', 'Ilham', '0831676761565'),
('S005', 'Agnesya', '085124767757'),
('S006', 'nurul', '085151515123');

-- --------------------------------------------------------

--
-- Table structure for table `pengerjaan`
--

CREATE TABLE `pengerjaan` (
  `id_pengerjaan` int(11) NOT NULL,
  `status_pengerjaan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengerjaan`
--

INSERT INTO `pengerjaan` (`id_pengerjaan`, `status_pengerjaan`) VALUES
(1, 'selesai'),
(2, 'belum selesai');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`kode_admin`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_layanan` (`id_layanan`),
  ADD KEY `kode_jenis_pembayaran` (`kode_jenis_pembayaran`),
  ADD KEY `kode_admin` (`kode_admin`),
  ADD KEY `id_pengerjaan` (`id_pengerjaan`);

--
-- Indexes for table `jenis_pembayaran`
--
ALTER TABLE `jenis_pembayaran`
  ADD PRIMARY KEY (`kode_jenis_pembayaran`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pengerjaan`
--
ALTER TABLE `pengerjaan`
  ADD PRIMARY KEY (`id_pengerjaan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `pengerjaan`
--
ALTER TABLE `pengerjaan`
  MODIFY `id_pengerjaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`),
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`kode_jenis_pembayaran`) REFERENCES `jenis_pembayaran` (`kode_jenis_pembayaran`),
  ADD CONSTRAINT `invoice_ibfk_4` FOREIGN KEY (`kode_admin`) REFERENCES `admin` (`kode_admin`),
  ADD CONSTRAINT `invoice_ibfk_5` FOREIGN KEY (`id_pengerjaan`) REFERENCES `pengerjaan` (`id_pengerjaan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
