-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 10:15 AM
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
-- Database: `saranaprasarana`
--

-- --------------------------------------------------------

--
-- Table structure for table `apar`
--

CREATE TABLE `apar` (
  `id` int(11) NOT NULL,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apar`
--

INSERT INTO `apar` (`id`, `no_kode`, `nama_sarana`, `lokasi`) VALUES
(30, 'KNR-1', 'APAR 9KG', 'GEDUNG UTAMA LANTAI 1'),
(31, 'LAB-2', 'APAR 9 KG', 'KANTOR LAB LT 2'),
(32, 'LAB-1', 'APAR 9 KG', 'KANTOR LAB LT 1'),
(33, 'POS-1', 'APAR 9 KG', 'Pos'),
(34, 'KNR-1', 'APAR 9 KG', 'KANTOR LANTAI 1'),
(35, 'G2-1', 'APAR 9 KG', 'G 2 TPS B3 DEPAN'),
(36, 'G2-3', 'APAR 9 KG', 'G 2 TPS DOMESTIK'),
(37, 'G2-2', 'APAR 9 KG', 'G 2 TPS B3 CLASERAN'),
(38, 'B4-3', 'APAR 9 KG', 'Workshop pstsd. 1'),
(39, 'B4-2', 'APAR 9 KG', 'Workshop pstsd. 2'),
(40, 'B4-1', 'APAR 9 KG', 'Workshop pstsd. 3'),
(41, 'KNR-2', 'APAR 9 KG', 'Kantor baru Lt. 1 Kantin'),
(42, 'KNR-3', 'APAR 9 KG', 'Kantor baru Lt. 2 Kantor Staf'),
(43, 'KNR-4', 'APAR 9 KG', 'Kantor baru Lt. 2 Aula'),
(44, 'B2-5', 'APAR 9 KG', 'Compressor & Panel SDP CF'),
(45, 'GA4-1', 'APAR 9 KG', 'Posko Darurat'),
(46, 'STP-1', 'APAR 9 KG', 'STP'),
(47, 'KTN-1', 'APAR 9 KG', 'Kantin Baru'),
(48, 'A1-1', 'APAR 9 KG', 'A 1 BB CF 1'),
(49, 'A1-2', 'APAR 9 KG', 'A 1 BB CF 2'),
(50, 'A2-2', 'APAR 9 KG', 'A 2 BJ Carbofuran 2'),
(51, 'A2-1', 'APAR 9 KG', 'A 2 BJ Carbofuran 1'),
(52, 'A3-1', 'APAR 9 KG', 'A 3 BJ. MP & IF 1'),
(53, 'A3-2', 'APAR 9 KG', 'A 3 BJ. MP & IF 2'),
(54, 'B1-3', 'APAR 9 KG', 'B 1 Gudang Karton & BB CF 3'),
(55, 'B1-2', 'APAR 9 KG', 'B 1 Gudang Karton & BB CF 2'),
(56, 'B1-1', 'APAR 9 KG', 'B 1 Gudang Karton & BB CF 1'),
(57, 'B2-2', 'APAR 9 KG', 'B 2 Prod CF 2'),
(58, 'B2-1', 'APAR 9 KG', 'B 2 Prod CF 1'),
(59, 'B2-3', 'APAR 9 KG', 'B 2 Prod CF 3'),
(60, 'B2-4', 'APAR 9 KG', 'B 2 Prod CF 4'),
(61, 'B1-5', 'APAR 9 KG', 'B 2 Prod IF 2'),
(62, 'B1-4', 'APAR 9 KG', 'B 2 Prod IF 1'),
(63, 'B5-1', 'APAR 9 KG', 'B 4 Prod IF 1'),
(64, 'B5-2', 'APAR 9 KG', 'B 4 Prod IF 2'),
(65, 'B5-3', 'APAR 9 KG', 'B 4 Prod IF 3'),
(66, 'E1-1', 'APAR 9 KG', 'E 1 Prod Filling. 1'),
(67, 'E1-2', 'APAR 9 KG', 'E 1 Prod Filling. 2'),
(68, 'E3-2', 'APAR 9 KG', 'E 3 Reaktor 2'),
(69, 'E2-2', 'APAR 9 KG', 'E 2 Reaktor 2'),
(70, 'E2-3', 'APAR 9 KG', 'E 2 Genset'),
(71, 'E2-1', 'APAR 9 KG', 'E 2 Reaktor 1'),
(72, 'E3-1', 'APAR 9 KG', 'E 3 Reaktor 1'),
(73, 'E4-1', 'APAR 9 KG', 'E 4 Metil. 1'),
(74, 'E4-2', 'APAR 9 KG', 'E 4 Metil. 2'),
(75, 'E5-1', 'APAR 6 KG', 'E 5 Starkum/MP 1'),
(76, 'B6-1', 'APAR 9 KG', 'B 6 MP BARU 1'),
(77, 'B6-2', 'APAR 9 KG', 'B 6 MP BARU 2'),
(78, 'E5-3', 'APAR 9 KG', 'E 5 Starkum/MP 3'),
(79, 'E5-2', 'APAR 9 KG', 'E 5 Starkum/MP 2'),
(80, 'D2-2', 'APAR 9 KG', 'D 2 Prpd Jetmil 2'),
(81, 'D2-1', 'APAR 9 KG', 'D 2 Prpd Jetmil 1'),
(82, 'F1-1', 'APAR 9 KG', 'F 1 BB & WIP Pq 1'),
(83, 'F1-2', 'APAR 6 KG', 'F 1 BB & WIP Pq 2'),
(84, 'F2-1', 'APAR 9 KG', 'F 2 BB & WIP Glyps 1'),
(85, 'F2-2', 'APAR 9 KG', 'F 2 BB & WIP Glyps 2'),
(86, 'F3-3', 'APAR 9 KG', 'F 3 BB MP, MTL, AUX (3)'),
(87, 'F3-2', 'APAR 9 KG', 'F 3 BB MP, MTL, AUX (2)'),
(88, 'F3-1', 'APAR 9 KG', 'F 3 BB MP, MTL, AUX (1)'),
(89, 'F4-1', 'APAR 9 KG', 'F 4 Gudang BJ Pq (1)'),
(90, 'F4-2', 'APAR 9 KG', 'F 4 Gudang BJ Pq (2)'),
(91, 'F5-1', 'APAR 9 KG', 'F 5 Gudang BJ Glyps (1)'),
(92, 'F5-2', 'APAR 9 KG', 'F 5 Gudang BJ Glyps (2)'),
(93, 'C2-2', 'APAR 9 KG', 'C 1 Produksi Mulsa 2'),
(94, 'C2-1', 'APAR 9 KG', 'C 1 Produksi Mulsa 1'),
(95, 'C1-2', 'APAR 9 KG', 'C 1 Gudang Gudang BB Mulsa 2'),
(96, 'C1-1', 'APAR 9 KG', 'C 1 Gudang Gudang BB Mulsa 1'),
(97, 'C1-3', 'APAR 9 KG', 'C 1 Gudang Gudang BB Mulsa 3'),
(98, 'D1-1', 'APAR 9 KG', 'D 1 Prod Botol'),
(99, 'D1-2', 'APAR 9 KG', 'D 1 Pintu Panel Utility'),
(100, 'D1-3', 'APAR 9 KG', 'D 1 R. Trapo'),
(101, 'D3-1', 'APAR 9 KG', 'D 3 Prod botol. 1'),
(102, 'D3-2', 'APAR 9 KG', 'D 3 Prod botol. 2'),
(103, 'D2-3', 'APAR 9 KG', 'D 3 Mixer PLASTIK 3'),
(104, 'D4-1', 'APAR 9 KG', 'D 4 GUDANG BJ TUTUP BTL.'),
(105, 'D5-1', 'APAR 9 KG', 'D 5 BB Botol & Mulsa 1'),
(106, 'D5-2', 'APAR 9 KG', 'D 5 BB Botol & Mulsa 2'),
(107, 'G1-1', 'APAR 9 KG', 'G 1 Barang Jadi Mulsa 1'),
(108, 'G1-2', 'APAR 9 KG', 'G 1 Barang Jadi Mulsa 2'),
(109, 'J1-2', 'APAR 9 KG', 'J Gudang BJ All pestisida 2'),
(110, 'J1-1', 'APAR 9 KG', 'J Gudang BJ All pestisida 1'),
(111, 'J1-3', 'APAR 9 KG', 'J Gudang BJ All pestisida 3'),
(112, 'H1-1', 'APAR 9 KG', 'H1 (WELDING)'),
(113, 'H2-1', 'APAR 9 KG', 'H2 Prod Assembling 1'),
(114, 'H2-2', 'APAR 9 KG', 'H2 Prod Assembling 2'),
(115, 'H2-3', 'APAR 9 KG', 'H2 Prod Assembling 3'),
(116, 'H3-1', 'APAR 9 KG', 'H3 Gudang sprayer BJ'),
(117, 'I1-1', 'APAR 9 KG', 'I 1 Gudang BB Mp, methyl'),
(118, 'I2-1', 'APAR 9 KG', 'I 2 Gudang Karton Box'),
(119, 'I3-1', 'APAR 9 KG', 'I 3 BB Sprayer'),
(120, 'I4-2', 'APAR 9 KG', 'I 4 BB Sprayer 2'),
(121, 'I4-1', 'APAR 9 KG', 'I 4 BB Sprayer 1'),
(122, 'KNR-1', 'APAR 9 KG', 'GEDUNG UTAMA LANTAI 1'),
(123, 'KNR-2', 'APAR 6 KG', 'GEDUNG UTAMA LANTAI 2'),
(124, 'KNR-3', 'APAR 3 KG', 'RUANG SERVER'),
(125, 'KNR-4', 'APAR 9 KG', 'GUDANG UTAMA'),
(126, 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `checklist_apar`
--

CREATE TABLE `checklist_apar` (
  `id` int(11) NOT NULL,
  `apar_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `label_pengisian` enum('Ok','Nok') DEFAULT NULL,
  `tekanan_pressure` enum('Ok','Nok') DEFAULT NULL,
  `safety_pin` enum('Ok','Nok') DEFAULT NULL,
  `handle` enum('Ok','Nok') DEFAULT NULL,
  `selang_nozzle` enum('Ok','Nok') DEFAULT NULL,
  `dry_chemical` enum('Ok','Nok') DEFAULT NULL,
  `tablulan` enum('Ok','Nok') DEFAULT NULL,
  `bambu_petunjuk` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `hhh` enum('Ok','Nok') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checklist_apar`
--

INSERT INTO `checklist_apar` (`id`, `apar_id`, `tahun`, `bulan`, `tanggal_cek`, `label_pengisian`, `tekanan_pressure`, `safety_pin`, `handle`, `selang_nozzle`, `dry_chemical`, `tablulan`, `bambu_petunjuk`, `paraf`, `catatan`, `foto`, `users_id`, `created_at`, `hhh`) VALUES
(15, 48, 2026, 5, '2026-05-22', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'bagas', 'sadas', 'APAR_48_2026_5_1779431521.png', 11, '2026-05-22 13:32:03', NULL),
(16, 48, 2026, 6, '2026-05-22', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'bagas', 'sadas', 'APAR_48_2026_6_1779433042.PNG', 10, '2026-05-22 13:57:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `checklist_gedung`
--

CREATE TABLE `checklist_gedung` (
  `id` int(11) NOT NULL,
  `gedung_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `dinding` enum('Ok','Nok') DEFAULT NULL,
  `atap_talang` enum('Ok','Nok') DEFAULT NULL,
  `lantai` enum('Ok','Nok') DEFAULT NULL,
  `wastafel` enum('Ok','Nok') DEFAULT NULL,
  `pintu_kaca` enum('Ok','Nok') DEFAULT NULL,
  `toilet` enum('Ok','Nok') DEFAULT NULL,
  `lain_lain` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuu` enum('Ok','Nok') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_grease_trap`
--

CREATE TABLE `checklist_grease_trap` (
  `id` int(11) NOT NULL,
  `grease_trap_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `minggu` tinyint(1) NOT NULL DEFAULT 1,
  `tanggal_cek` date DEFAULT NULL,
  `kondisi_fisik` enum('Ok','Nok') DEFAULT NULL,
  `kebersihan_internal` enum('Ok','Nok') DEFAULT NULL,
  `pemisahan_lemak` enum('Ok','Nok') DEFAULT NULL,
  `saluran_in_out` enum('Ok','Nok') DEFAULT NULL,
  `bau_kontaminasi` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_hydrant`
--

CREATE TABLE `checklist_hydrant` (
  `id` int(11) NOT NULL,
  `hydrant_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `hose_coupling_conect` enum('Ok','Nok') DEFAULT NULL,
  `baut_valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `fire_hose` enum('Ok','Nok') DEFAULT NULL,
  `slang_hydrant` enum('Ok','Nok') DEFAULT NULL,
  `nozzle` enum('Ok','Nok') DEFAULT NULL,
  `box_hydrant` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_toilet`
--

CREATE TABLE `checklist_toilet` (
  `id` int(11) NOT NULL,
  `toilet_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `tissue_toilet` enum('Ok','Nok') DEFAULT NULL,
  `lantai_bersih` enum('Ok','Nok') DEFAULT NULL,
  `closet_bersih` enum('Ok','Nok') DEFAULT NULL,
  `dinding_bersih` enum('Ok','Nok') DEFAULT NULL,
  `kran_shower` enum('Ok','Nok') DEFAULT NULL,
  `sarang_laba` enum('Ok','Nok') DEFAULT NULL,
  `tersedia_pewangi` enum('Ok','Nok') DEFAULT NULL,
  `lap_sabun` enum('Ok','Nok') DEFAULT NULL,
  `tempat_sampah` enum('Ok','Nok') DEFAULT NULL,
  `matikan_lampu` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `test` enum('Ok','Nok') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checklist_toilet`
--

INSERT INTO `checklist_toilet` (`id`, `toilet_id`, `tahun`, `bulan`, `tanggal_cek`, `tissue_toilet`, `lantai_bersih`, `closet_bersih`, `dinding_bersih`, `kran_shower`, `sarang_laba`, `tersedia_pewangi`, `lap_sabun`, `tempat_sampah`, `matikan_lampu`, `paraf`, `catatan`, `foto`, `users_id`, `created_at`, `test`) VALUES
(36, 1, 2026, 5, '2026-05-02', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Pengecekan pagi, kondisi normal', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(37, 1, 2026, 5, '2026-05-03', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Dinding ada noda, sudah dibersihkan', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(38, 1, 2026, 5, '2026-05-04', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Kondisi baik setelah pembersihan', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(39, 1, 2026, 5, '2026-05-05', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Toilet rapi dan bersih', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(40, 1, 2026, 5, '2026-05-06', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Cek rutin pagi, semua aman', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(41, 1, 2026, 5, '2026-05-07', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Tissue habis pagi, sudah diisi ulang', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(42, 1, 2026, 5, '2026-05-08', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Semua item pengecekan normal', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(43, 1, 2026, 5, '2026-05-09', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Lantai sudah dipel, wangi', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(44, 1, 2026, 5, '2026-05-10', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Ada sarang laba-laba di sudut atas, sudah dibersihkan', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(45, 1, 2026, 5, '2026-05-11', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Kondisi toilet sangat bersih', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(46, 1, 2026, 5, '2026-05-12', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Pengecekan siang, semua baik', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(47, 1, 2026, 5, '2026-05-13', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Sabun dan tissue tersedia cukup', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(48, 1, 2026, 5, '2026-05-14', 'Ok', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Kran bocor sedikit, sudah dilaporkan ke maintenance', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(49, 1, 2026, 5, '2026-05-15', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Kran sudah diperbaiki, kondisi normal', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(50, 1, 2026, 5, '2026-05-16', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Cek rutin, semua dalam kondisi baik', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(51, 1, 2026, 5, '2026-05-17', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Ok', 'Ok', 'Budi', 'Pewangi habis, sudah diganti baru', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(52, 1, 2026, 5, '2026-05-18', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Toilet bersih dan wangi', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(53, 1, 2026, 5, '2026-05-19', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Pengecekan pagi, tidak ada masalah', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(54, 1, 2026, 5, '2026-05-20', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Semua perlengkapan tersedia lengkap', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(55, 1, 2026, 5, '2026-05-21', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Closet bersih, lantai kering', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(56, 1, 2026, 5, '2026-05-22', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Ok', 'Andi', 'Sabun habis, sudah diisi ulang', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(57, 1, 2026, 5, '2026-05-23', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Kondisi baik, tempat sampah sudah dikosongkan', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(58, 1, 2026, 5, '2026-05-24', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Cek sore hari, toilet dalam keadaan rapi', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(59, 1, 2026, 5, '2026-05-25', 'Ok', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Lantai agak kotor setelah hujan, sudah dipel', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(60, 1, 2026, 5, '2026-05-26', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Pengecekan normal, semua baik', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(61, 1, 2026, 5, '2026-05-27', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Toilet bersih, pewangi masih cukup', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(62, 1, 2026, 5, '2026-05-28', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Nok', 'Ok', 'Andi', 'Tempat sampah penuh, sudah dikosongkan', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(63, 1, 2026, 5, '2026-05-29', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Semua item OK, kondisi prima', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(64, 1, 2026, 5, '2026-05-30', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Sari', 'Cek akhir bulan, kondisi terjaga baik', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(65, 1, 2026, 5, '2026-05-31', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Andi', 'Pengecekan terakhir bulan Mei, semua baik', 'uploads/sample_toilet.png', 1, '2026-05-21 10:29:49', NULL),
(101, 13, 2026, 5, '2026-05-19', 'Nok', 'Nok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Ok', 'Budi', 'Kondisi semua baik', 'TOILET_13_20260519_1779419185.png', 8, '2026-05-22 10:06:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `id` int(11) NOT NULL,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gedung`
--

INSERT INTO `gedung` (`id`, `no_kode`, `nama_sarana`, `lokasi`) VALUES
(1, 'GDG-01', 'Gedung', 'KANTOR'),
(2, 'GDG-02', 'Gedung', 'LABORATORIUM'),
(3, 'GDG-03', 'Gedung', 'POS 1 SECURITY'),
(4, 'GDG-04', 'Gedung', 'POS 2 SECURITY'),
(5, 'GDG-05', 'Gedung', 'MUSHOLAH & TOILET BAWAH'),
(6, 'GDG-06', 'Gedung', 'MUSHOLAH & TOILET ATAS'),
(7, 'GDG-07', 'Gedung', 'POS TIMBANGAN'),
(8, 'GDG-08', 'Gedung', 'MESS DALAM PABRIK'),
(9, 'GDG-09', 'Gedung', 'MESS UTARA'),
(10, 'GDG-10', 'Gedung', 'MESS PORTAL'),
(11, 'GDG-11', 'Gedung', 'MESS LAES'),
(12, 'GDG-12', 'Gedung', 'MESS TAMAN CIKANDE'),
(13, 'GDG-13', 'Gedung', 'TOILET ASSEMBLING'),
(14, 'GDG-14', 'Gedung', 'KANTIN ATAS'),
(15, 'GDG-15', 'Gedung', 'A 1 GDG TIMBANG BB. CF&IF'),
(16, 'GDG-16', 'Gedung', 'A 2 BJ. CARBOFURAN'),
(17, 'GDG-17', 'Gedung', 'A 3 BJ. MP & IF'),
(18, 'GDG-18', 'Gedung', 'B 1 GUDANG KARTON'),
(19, 'GDG-19', 'Gedung', 'B 2 PROD. CF'),
(20, 'GDG-20', 'Gedung', 'B 3 GUDANG PASIR'),
(21, 'GDG-21', 'Gedung', 'B 4 WORKSHOP PESTISIDA'),
(22, 'GDG-22', 'Gedung', 'B 5 PROD. IF'),
(23, 'GDG-23', 'Gedung', 'B 6 MP BARU'),
(24, 'GDG-24', 'Gedung', 'C 1 GUDANG MULSA'),
(25, 'GDG-25', 'Gedung', 'C 2 PROD. MULSA'),
(26, 'GDG-26', 'Gedung', 'D 1 PROD. BOTOL'),
(27, 'GDG-27', 'Gedung', 'D PROD. BOTOL'),
(28, 'GDG-28', 'Gedung', 'D 2 PROD. MP JETMILL'),
(29, 'GDG-29', 'Gedung', 'D 3 WELDING'),
(30, 'GDG-30', 'Gedung', 'D 4 WIP TUTUP BOTOL'),
(31, 'GDG-31', 'Gedung', 'D 5 BB BOTOL & MULSA'),
(32, 'GDG-32', 'Gedung', 'E 1 PROD FILLING. 1'),
(33, 'GDG-33', 'Gedung', 'E 2 AUX'),
(34, 'GDG-34', 'Gedung', 'E 3 GLYPHOSATE'),
(35, 'GDG-35', 'Gedung', 'E 4 PROD. METHYL'),
(36, 'GDG-36', 'Gedung', 'E 5 PROD. STARKUM'),
(37, 'GDG-37', 'Gedung', 'F 1 BB & WIP PARAQUAT'),
(38, 'GDG-38', 'Gedung', 'F 2 BB & WIP GLYPHOSATE'),
(39, 'GDG-39', 'Gedung', 'F 3 BB MP, MTL, AUX'),
(40, 'GDG-40', 'Gedung', 'F 4 PROD. PARAQUAT'),
(41, 'GDG-41', 'Gedung', 'F 5 GUDANG BJ GLYPHOSATE'),
(42, 'GDG-42', 'Gedung', 'G 1 GUDANG BJ MULSA'),
(43, 'GDG-43', 'Gedung', 'G 2 TPS'),
(44, 'GDG-44', 'Gedung', 'G 3 GUDANG BJ ALL PESTISIDA'),
(45, 'GDG-45', 'Gedung', 'H 1 GUDANG SPRAYER'),
(46, 'GDG-46', 'Gedung', 'H 2 PROD. ASSEMBLING'),
(47, 'GDG-47', 'Gedung', 'H 3 BJ SPRAYER'),
(48, 'GDG-48', 'Gedung', 'I 1 GUDANG BB MP & METHYL'),
(49, 'GDG-49', 'Gedung', 'I 2 GUDANG KARTON BOX'),
(50, 'GDG-50', 'Gedung', 'I 3 BB & BJ SPRAYER'),
(51, 'GDG-51', 'Gedung', 'I 4 BB SPRAYER'),
(55, 'GDG-52', 'Gedung', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `grease_trap`
--

CREATE TABLE `grease_trap` (
  `id` int(11) NOT NULL,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grease_trap`
--

INSERT INTO `grease_trap` (`id`, `no_kode`, `nama_sarana`, `lokasi`) VALUES
(1, 'GT-01', 'Grease Trap', 'Kantin Atas'),
(10, 'GT-02', 'Grease Trap', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `hydrant`
--

CREATE TABLE `hydrant` (
  `id` int(11) NOT NULL,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hydrant`
--

INSERT INTO `hydrant` (`id`, `no_kode`, `nama_sarana`, `lokasi`) VALUES
(1, 'HYD-01', 'Hydrant', 'Area Depan Gedung C1 (Mulsa)'),
(2, 'HYD-02', 'Hydrant', 'Area Depan Gedung E1 (Filling)'),
(3, 'HYD-03', 'Hydrant', 'Area Depan Gedung E2 (Glyphosate)'),
(4, 'HYD-04', 'Hydrant', 'Area Samping Gedung E5 (Starkum)'),
(5, 'HYD-05', 'Hydrant', 'Area Taman Methyl'),
(6, 'HYD-06', 'Hydrant', 'Area Depan Gedung D3 (Welding)'),
(7, 'HYD-07', 'Hydrant', 'Area Depan Gedung D5 (Gudang Botol)'),
(8, 'HYD-08', 'Hydrant', 'Area WTP'),
(9, 'HYD-09', 'Hydrant', 'Area Mushola Atas'),
(10, 'HYD-10', 'Hydrant', 'Area Depan Gedung D1 (Prod. Botol)'),
(11, 'HYD-11', 'Hydrant', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `master_item`
--

CREATE TABLE `master_item` (
  `id` int(11) NOT NULL,
  `modul` varchar(50) NOT NULL,
  `kolom` varchar(100) NOT NULL,
  `label` varchar(200) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_item`
--

INSERT INTO `master_item` (`id`, `modul`, `kolom`, `label`, `is_active`) VALUES
(1, 'apar', 'label_pengisian', 'Label pengisian ulang', 1),
(2, 'apar', 'tekanan_pressure', 'Tekanan (pressure) Amper', 1),
(3, 'apar', 'safety_pin', 'Safety pin', 1),
(4, 'apar', 'handle', 'Handle', 1),
(5, 'apar', 'selang_nozzle', 'Selang (Nozzle)', 1),
(6, 'apar', 'dry_chemical', 'Dry Chemical', 1),
(7, 'apar', 'tablulan', 'Tablulan', 1),
(8, 'apar', 'bambu_petunjuk', 'Bambu & petunjuk penggunaan', 1),
(9, 'hydrant', 'valve_handle', 'Valve Handle', 1),
(10, 'hydrant', 'hose_coupling_conect', 'Hose Coupling Conect', 1),
(11, 'hydrant', 'baut_valve_handle', 'Baut valve handle', 1),
(12, 'hydrant', 'fire_hose', 'Fire hose', 1),
(13, 'hydrant', 'slang_hydrant', 'Slang hydrant', 1),
(14, 'hydrant', 'nozzle', 'Nozzle', 1),
(15, 'hydrant', 'box_hydrant', 'Box Hydrant', 1),
(16, 'gedung', 'dinding', 'Dinding', 1),
(17, 'gedung', 'atap_talang', 'Atap/Talang', 1),
(18, 'gedung', 'lantai', 'Lantai', 1),
(19, 'gedung', 'wastafel', 'Wastafel', 1),
(20, 'gedung', 'pintu_kaca', 'Pintu/Kaca', 1),
(21, 'gedung', 'toilet', 'Toilet', 1),
(22, 'gedung', 'lain_lain', 'Lain-lain', 1),
(23, 'grease_trap', 'kondisi_fisik', 'Kondisi fisik grease trap', 1),
(24, 'grease_trap', 'kebersihan_internal', 'Kebersihan internal', 1),
(25, 'grease_trap', 'pemisahan_lemak', 'Fungsi pemisahan lemak', 1),
(26, 'grease_trap', 'saluran_in_out', 'Saluran masuk dan keluar', 1),
(27, 'grease_trap', 'bau_kontaminasi', 'Bau atau kontaminasi', 1),
(39, 'toilet', 'tissue_toilet', 'Tissue toilet selalu tersedia', 1),
(40, 'toilet', 'lantai_bersih', 'Lantai bersih (tidak ada sampah)', 1),
(41, 'toilet', 'closet_bersih', 'Closet bersih & tidak mampet', 1),
(42, 'toilet', 'dinding_bersih', 'Dinding Toilet bersih', 1),
(43, 'toilet', 'kran_shower', 'Kran/shower berfungsi dengan baik', 1),
(44, 'toilet', 'sarang_laba', 'Tidak ada sarang laba-laba', 1),
(45, 'toilet', 'tersedia_pewangi', 'Tersedia pewangi', 1),
(46, 'toilet', 'lap_sabun', 'Ada lap tangan dan sabun di washtafel', 1),
(47, 'toilet', 'tempat_sampah', 'Ada tempat sampah di dalam toilet', 1),
(48, 'toilet', 'matikan_lampu', 'Matikan lampu toilet saat tidak digunakan', 1),
(49, 'apar', 'hhh', 'hhh', 0),
(50, 'gedung', 'uuu', 'uuu', 0),
(51, 'toilet', 'test', 'test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `toilet_unit`
--

CREATE TABLE `toilet_unit` (
  `id` int(11) NOT NULL,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT 'Toilet',
  `lokasi` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toilet_unit`
--

INSERT INTO `toilet_unit` (`id`, `no_kode`, `nama_sarana`, `lokasi`) VALUES
(1, 'TLT-13', 'Toilet', 'test'),
(13, 'TLT-1', 'Toilet', 'Toilet kantor utama lantai 1 (Laki-Laki)'),
(14, 'TLT-2', 'Toilet', 'Toilet kantor utama lantai 2 (Perempuan)'),
(15, 'TLT-3', 'Toilet', 'Toilet kantor Lab Lantai 1'),
(16, 'TLT-4', 'Toilet', 'Toilet kantor Lab Lantai 2'),
(17, 'TLT-5', 'Toilet', 'Toilet Pos Satpam 1'),
(18, 'TLT-6', 'Toilet', 'Toilet Pos Satpam 2'),
(19, 'TLT-7', 'Toilet', 'Toilet Kantor MTC lantai 1'),
(20, 'TLT-8', 'Toilet', 'Toilet Kantor MTC lantai 2'),
(21, 'TLT-9', 'Toilet', 'Toilet Timbangan'),
(22, 'TLT-10', 'Toilet', 'Toilet Musholah Atas'),
(23, 'TLT-11', 'Toilet', 'Toilet Musholah Bawah'),
(24, 'TLT-12', 'Toilet', 'Toilet Assembling'),
(25, 'TLT-14', 'Toilet', 'Toilet kantor utama lantai 1 (Perempuan)'),
(26, 'TLT-15', 'Toilet', 'Toilet kantor utama lantai 2 ( Laki-Laki )');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif',
  `role` enum('Admin','User','superadmin','Monitoring') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass`, `nama`, `email`, `status`, `role`, `created_at`) VALUES
(2, 'user', '$2y$10$kosvdiXHU9mhSc2GkNtPTu3p17G63TmtONTgz.n20v4d7TOTGhDq6', 'User Biasa', 'user@example.com', 'Aktif', 'User', '2026-05-04 10:28:55'),
(8, 'itcba', '$2y$10$OnB6kYcyY5sLPokQKwyWJO578IzGCIU2ZrqSJV/bh1H/ywR7mbFwi', NULL, NULL, 'Aktif', 'Admin', '2026-05-21 11:09:46'),
(9, 'superadmin', '$2y$10$fVIpgBoNpk55E1D3Z2X93OhuWyGiPrnh2KbjvEkwcUMCZyuHglHcC', NULL, NULL, 'Aktif', 'superadmin', '2026-05-21 11:11:05'),
(10, 'admin', '$2y$10$YQxzOom9riCuEUZfSYb0GuHmz50u7fzFgHFgNsvlIB1vz3To1Z3Hu', NULL, NULL, 'Aktif', 'Admin', '2026-05-22 10:05:35'),
(15, 'monitoring', '$2y$10$8HTK03TlyXUXOloU3Dz1XuEvgkYPlttk0LwbEZ8EtdSQtFmwchk.S', NULL, NULL, 'Aktif', 'Monitoring', '2026-05-22 14:08:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apar`
--
ALTER TABLE `apar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist_apar`
--
ALTER TABLE `checklist_apar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist_gedung`
--
ALTER TABLE `checklist_gedung`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist_grease_trap`
--
ALTER TABLE `checklist_grease_trap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist_hydrant`
--
ALTER TABLE `checklist_hydrant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checklist_toilet`
--
ALTER TABLE `checklist_toilet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_toilet_date` (`toilet_id`,`tanggal_cek`);

--
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grease_trap`
--
ALTER TABLE `grease_trap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hydrant`
--
ALTER TABLE `hydrant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_item`
--
ALTER TABLE `master_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toilet_unit`
--
ALTER TABLE `toilet_unit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apar`
--
ALTER TABLE `apar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `checklist_apar`
--
ALTER TABLE `checklist_apar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `checklist_gedung`
--
ALTER TABLE `checklist_gedung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `checklist_grease_trap`
--
ALTER TABLE `checklist_grease_trap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `checklist_hydrant`
--
ALTER TABLE `checklist_hydrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `checklist_toilet`
--
ALTER TABLE `checklist_toilet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `grease_trap`
--
ALTER TABLE `grease_trap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hydrant`
--
ALTER TABLE `hydrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `master_item`
--
ALTER TABLE `master_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `toilet_unit`
--
ALTER TABLE `toilet_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
