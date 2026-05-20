-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 12:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_gaji`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Hadir',
  `total_hari` decimal(3,1) NOT NULL DEFAULT 1.0,
  `jam_lembur` int(11) NOT NULL DEFAULT 0,
  `dapat_uang_makan` tinyint(1) NOT NULL DEFAULT 1,
  `nominal_basic` decimal(15,2) DEFAULT NULL,
  `nominal_lembur` decimal(15,2) DEFAULT NULL,
  `nominal_makan` decimal(15,2) DEFAULT NULL,
  `nominal_kasbon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `jam_masuk`, `jam_keluar`, `status`, `total_hari`, `jam_lembur`, `dapat_uang_makan`, `nominal_basic`, `nominal_lembur`, `nominal_makan`, `nominal_kasbon`, `created_at`, `updated_at`) VALUES
(9, 4, '2026-05-24', '09:00:00', '10:27:00', 'Hadir', 1.5, 0, 1, 270000.00, 0.00, 20000.00, 0.00, '2026-05-18 20:28:01', '2026-05-18 20:29:40'),
(10, 5, '2026-05-24', '09:00:00', '10:27:00', 'Hadir', 1.5, 0, 1, 270000.00, 0.00, 20000.00, 500000.00, '2026-05-18 20:28:01', '2026-05-19 03:08:32'),
(11, 6, '2026-05-24', '09:00:00', '10:27:00', 'Hadir', 1.5, 10, 1, 285000.00, 237500.00, 20000.00, 20000.00, '2026-05-18 20:28:01', '2026-05-19 03:07:46'),
(12, 4, '2026-05-25', '09:00:00', '17:28:00', 'Hadir', 1.0, 0, 1, 180000.00, 0.00, 20000.00, 0.00, '2026-05-18 20:28:19', '2026-05-18 20:29:40'),
(13, 5, '2026-05-25', '09:00:00', '17:28:00', 'Hadir', 1.0, 0, 1, 180000.00, 0.00, 20000.00, 0.00, '2026-05-18 20:28:19', '2026-05-18 20:29:31'),
(14, 6, '2026-05-25', '09:00:00', '17:28:00', 'Hadir', 1.0, 0, 1, 190000.00, 0.00, 20000.00, 100000.00, '2026-05-18 20:28:19', '2026-05-19 03:07:32'),
(15, 4, '2026-05-26', '09:00:00', '20:28:00', 'Hadir', 1.0, 3, 1, 180000.00, 67500.00, 20000.00, 0.00, '2026-05-18 20:28:31', '2026-05-18 20:29:40'),
(16, 5, '2026-05-26', '09:00:00', '20:28:00', 'Hadir', 1.0, 3, 1, 180000.00, 67500.00, 20000.00, 0.00, '2026-05-18 20:28:31', '2026-05-18 20:29:31'),
(17, 6, '2026-05-26', '09:00:00', '20:28:00', 'Hadir', 1.0, 3, 1, 190000.00, 71250.00, 20000.00, 0.00, '2026-05-18 20:28:31', '2026-05-18 20:29:14'),
(18, 4, '2026-05-27', '09:00:00', '19:28:00', 'Hadir', 1.0, 2, 1, 180000.00, 45000.00, 20000.00, 0.00, '2026-05-18 20:28:47', '2026-05-18 20:29:40'),
(19, 5, '2026-05-27', '09:00:00', '19:28:00', 'Hadir', 1.0, 2, 1, 180000.00, 45000.00, 20000.00, 0.00, '2026-05-18 20:28:47', '2026-05-18 20:29:31'),
(20, 6, '2026-05-27', '09:00:00', '19:28:00', 'Hadir', 1.0, 2, 1, 190000.00, 47500.00, 20000.00, 0.00, '2026-05-18 20:28:47', '2026-05-18 20:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `absensis`
--

CREATE TABLE `absensis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Hadir',
  `total_hari` decimal(3,1) NOT NULL DEFAULT 1.0,
  `jam_lembur` int(11) NOT NULL DEFAULT 0,
  `dapat_uang_makan` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nominal_basic` decimal(15,2) DEFAULT NULL,
  `nominal_lembur` decimal(15,2) DEFAULT NULL,
  `nominal_makan` decimal(15,2) DEFAULT NULL,
  `nominal_kasbon` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_lapangan`
--

CREATE TABLE `laporan_lapangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `minggu_ke` tinyint(3) UNSIGNED DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `deskripsi_pekerjaan` text DEFAULT NULL,
  `kendala` text DEFAULT NULL,
  `solusi` text DEFAULT NULL,
  `foto_paths` text DEFAULT NULL,
  `foto_deskripsis` text DEFAULT NULL,
  `status` enum('Draft','Terkirim','Disetujui','Ditolak') NOT NULL DEFAULT 'Terkirim',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_lapangan`
--

INSERT INTO `laporan_lapangan` (`id`, `user_id`, `tanggal`, `minggu_ke`, `lokasi`, `deskripsi_pekerjaan`, `kendala`, `solusi`, `foto_paths`, `foto_deskripsis`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-05-19', NULL, 'Lantai 2', '-', NULL, NULL, '[\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/mVGIXGzmZoSGTfhMBslqPlUTYXx44o5cYb8Ri6X7.jpg\"]', '[\"pemasangan ac di outdoor\"]', 'Disetujui', NULL, '2026-05-18 21:36:40', '2026-05-18 21:37:24'),
(2, 1, '2026-05-19', NULL, '2', '-', NULL, NULL, '[\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/X6kGBy1IIOLBA2jOHtwkscKTxDFoaRZ0ATy7YhDd.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/Kzep5goLWSJ6AQJh2cebFNDUofd35kY2SulmRC5q.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/yHq7OMBsZPoS0YOTarJWQHEy6JG2vpLBuj1wvF5u.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/CMktUw01GmgPN4SDH4Z9xCYXO7A0vbWA3c7rSxUl.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/0U7i7SERYmqkyUrZ9hBWuHxythAHyebm6k0pNlRB.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/SbS7Z02JflqaGFYhcx1WbQXz46o1xZcd6IWWSUBr.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/lJJ4utSHpgN7MaH3NshLitXr6J5bEU1sN8lsFx4l.jpg\"]', '[\"pabrikasi ducting\",\"masang ac\",\"pabrikasi ducting\",\"loading pipa\",\"pemasangan pipa refrigerant\",\"pemasangan unit ac\",\"pemasangan pipa springkler\"]', 'Disetujui', NULL, '2026-05-18 21:53:58', '2026-05-18 21:54:14'),
(3, 5, '2026-05-20', NULL, 'lantai 2', '-', NULL, NULL, '[\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/9tVene9gZsRnOhAsQ0ggM6dG4MFqyVf28CHRgYJ7.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/C1K9vEjMb4VBL1A2MjKVpOKOsBzcfbLxnsYjHVum.jpg\"]', '[\"pemasangapipa refrigerant\",\"pemasangan pipa refrigerant\"]', 'Disetujui', NULL, '2026-05-19 02:40:25', '2026-05-19 02:42:44'),
(4, 4, '2026-05-20', NULL, 'workshop', '-', NULL, NULL, '[\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/HOBM6oCQEExGY4QREruGmh5pGtKzUsk1GvZFvNXl.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/TUQNO1GwttSfKNZMvAYgtwT9TcOPyW0dtcJihdvn.jpg\"]', '[\"pabrikasi support\",\"pabrikasi support\"]', 'Disetujui', NULL, '2026-05-19 02:41:20', '2026-05-19 02:43:51'),
(5, 6, '2026-05-20', NULL, 'lantai 3', '-', NULL, NULL, '[\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/tFwawhsSWwTiRM2do6BIpPR6FK3DaqDYXQHrLoTi.jpg\",\"http:\\/\\/127.0.0.1:8000\\/storage\\/laporan_lapangan\\/6qSKKMJolHTnMP2fB207BMRQCdIoWLOkVvT5H7LC.jpg\"]', '[\"pemasangan ducting\",\"pemasangan ducting\"]', 'Disetujui', NULL, '2026-05-19 02:42:12', '2026-05-19 02:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_lapangans`
--

CREATE TABLE `laporan_lapangans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `minggu_ke` tinyint(3) UNSIGNED DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `deskripsi_pekerjaan` text NOT NULL,
  `kendala` text DEFAULT NULL,
  `solusi` text DEFAULT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Terkirim','Disetujui') NOT NULL DEFAULT 'Terkirim',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_25_000254_create_absensi_table', 1),
(5, '2026_04_25_000254_create_absensis_table', 1),
(6, '2026_04_25_000254_create_penggajians_table', 1),
(7, '2026_04_25_000255_create_penggajian_table', 1),
(8, '2026_05_01_095856_add_nominal_to_absensis', 1),
(9, '2026_05_01_103843_create_laporan_lapangan_table', 1),
(10, '2026_05_01_103843_create_laporan_lapangans_table', 1),
(11, '2026_05_19_042619_add_profile_fields_to_users_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `penggajian`
--

CREATE TABLE `penggajian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `total_kehadiran_hari` decimal(5,1) NOT NULL DEFAULT 0.0,
  `total_jam_lembur` int(11) NOT NULL DEFAULT 0,
  `total_gaji_pokok` int(11) NOT NULL DEFAULT 0,
  `total_uang_lembur` int(11) NOT NULL DEFAULT 0,
  `total_uang_makan` int(11) NOT NULL DEFAULT 0,
  `kasbon` int(11) NOT NULL DEFAULT 0,
  `total_gaji_bersih` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penggajian`
--

INSERT INTO `penggajian` (`id`, `user_id`, `periode_mulai`, `periode_akhir`, `total_kehadiran_hari`, `total_jam_lembur`, `total_gaji_pokok`, `total_uang_lembur`, `total_uang_makan`, `kasbon`, `total_gaji_bersih`, `created_at`, `updated_at`) VALUES
(3, 6, '2026-05-01', '2026-05-31', 4.5, 15, 855000, 356250, 80000, 120000, 1171250, '2026-05-18 20:29:05', '2026-05-19 03:07:46'),
(4, 5, '2026-05-01', '2026-05-31', 4.5, 5, 810000, 112500, 80000, 500000, 502500, '2026-05-18 20:29:29', '2026-05-19 03:08:32'),
(5, 4, '2026-05-01', '2026-05-31', 4.5, 5, 810000, 112500, 80000, 0, 1002500, '2026-05-18 20:29:39', '2026-05-18 20:29:40');

-- --------------------------------------------------------

--
-- Table structure for table `penggajians`
--

CREATE TABLE `penggajians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `total_kehadiran_hari` decimal(5,1) NOT NULL DEFAULT 0.0,
  `total_jam_lembur` int(11) NOT NULL DEFAULT 0,
  `total_gaji_pokok` int(11) NOT NULL DEFAULT 0,
  `total_uang_lembur` int(11) NOT NULL DEFAULT 0,
  `total_uang_makan` int(11) NOT NULL DEFAULT 0,
  `kasbon` int(11) NOT NULL DEFAULT 0,
  `total_gaji_bersih` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('CYpLVIOpxsd5yU5om2iAMI6GEWjBHIvbiELMl2T7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'eyJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sIl90b2tlbiI6Ik93UnB6dzAySEsyczhEOFZhaFNPbFNtQW10RDVmZkhDWVJ5ODNLOTEiLCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9nYWppXC9zbGlwP2RhdGVfZnJvbT0yMDI2LTA1LTAxJmRhdGVfdG89MjAyNi0wNS0zMSZ1c2VyX2lkPTUiLCJyb3V0ZSI6ImFkbWluLmdhamkuc2xpcCJ9LCJsb2dnZWRfaW4iOnRydWUsInVzZXJfaWQiOjEsInVzZXJfcm9sZSI6ImFkbWluIiwidXNlcl9uYW1lIjoiQWRtaW5pc3RyYXRvciIsInVzZXJfZm90byI6ImZvdG8tcHJvZmlsXC9JZW9qbVI1Tm1YUUhBbkZXZjA5R3VEQkVZRVVtZ3RCd0FYQWUxdDY4LndlYnAifQ==', 1779185312);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'karyawan',
  `nik` varchar(255) DEFAULT NULL,
  `divisi` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `gaji_pokok_harian` int(11) NOT NULL DEFAULT 0,
  `uang_makan_harian` int(11) NOT NULL DEFAULT 0,
  `uang_lembur_per_jam` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `nik`, `divisi`, `jabatan`, `phone`, `alamat`, `foto`, `gaji_pokok_harian`, `uang_makan_harian`, `uang_lembur_per_jam`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', '$2y$12$eHm6kxvgOuj4RkvT5ChfWeLdMfBm288Co3hhWzv.X/0cXGBkLJEXK', 'admin', NULL, NULL, NULL, NULL, NULL, 'foto-profil/IeojmR5NmXQHAnFWf09GuDBEYEUmgtBwAXAe1t68.webp', 0, 0, 0, NULL, '2026-05-18 20:08:32', '2026-05-18 22:14:46'),
(3, 'Rinduwan', 'ridwan', '$2y$12$tetzpJyaySFlKmzKd5VyROjj0XgeWkvwiilxQRSb0HjpHV6mq/Kay', 'supervisi', '001', NULL, 'Supervisi', NULL, NULL, NULL, 0, 0, 0, NULL, '2026-05-18 20:11:16', '2026-05-18 20:11:16'),
(4, 'doni', 'doni', '$2y$12$N3e2XqT0ALMIP5XoI.KzrO8AWsH7j.9QkTz1MgddA/PFDVpxfhWj6', 'karyawan', '002', 'ac', 'skill', NULL, NULL, NULL, 180000, 20000, 22500, NULL, '2026-05-18 20:24:08', '2026-05-18 20:24:08'),
(5, 'sohib', 'sohib', '$2y$12$nPCubZpvHUoK63aG85MDVedNmBi/y70IvGyT98snu3BP4qUN835ta', 'karyawan', '003', 'ac', 'skill', NULL, NULL, NULL, 180000, 20000, 22500, NULL, '2026-05-18 20:24:59', '2026-05-18 20:24:59'),
(6, 'aceng', 'aceng', '$2y$12$ArD44W9u1M.AFcB6Gtf8te3aSf6zRg/Oto6YWB3Z0FfBHx6TxmGdS', 'karyawan', '004', 'ac', 'skill', NULL, NULL, NULL, 190000, 20000, 23750, NULL, '2026-05-18 20:26:10', '2026-05-18 20:26:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensi_user_id_foreign` (`user_id`);

--
-- Indexes for table `absensis`
--
ALTER TABLE `absensis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensis_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_lapangan`
--
ALTER TABLE `laporan_lapangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_lapangan_user_id_foreign` (`user_id`);

--
-- Indexes for table `laporan_lapangans`
--
ALTER TABLE `laporan_lapangans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_lapangans_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penggajian_user_id_foreign` (`user_id`);

--
-- Indexes for table `penggajians`
--
ALTER TABLE `penggajians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penggajians_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_nik_unique` (`nik`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `absensis`
--
ALTER TABLE `absensis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_lapangan`
--
ALTER TABLE `laporan_lapangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_lapangans`
--
ALTER TABLE `laporan_lapangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `penggajian`
--
ALTER TABLE `penggajian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penggajians`
--
ALTER TABLE `penggajians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `absensis`
--
ALTER TABLE `absensis`
  ADD CONSTRAINT `absensis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_lapangan`
--
ALTER TABLE `laporan_lapangan`
  ADD CONSTRAINT `laporan_lapangan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_lapangans`
--
ALTER TABLE `laporan_lapangans`
  ADD CONSTRAINT `laporan_lapangans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD CONSTRAINT `penggajian_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penggajians`
--
ALTER TABLE `penggajians`
  ADD CONSTRAINT `penggajians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
