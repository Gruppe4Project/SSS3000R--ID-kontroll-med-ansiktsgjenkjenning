-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 09, 2025 at 04:52 AM
-- Server version: 10.11.11-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `id_control`
-- --------------------------------------------------------

-- Table structure for table `admin`
CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table `admin`
INSERT INTO `admin` (`id`, `username`, `password`, `creationDate`, `updationDate`) VALUES
(9, 'momo', '9141f2f132dff383dbacde95f39b1b88', '2025-01-04 20:22:52', '2025-01-04 21:26:56'),
(10, 'group4', 'f8fc69e57c83d18a47b3360dae92cbbb', '2025-02-27 16:10:26', '2025-02-27 16:10:26');

-- --------------------------------------------------------

-- Table structure for table `logs`
CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `personNumber` varchar(20) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `time_date` timestamp NULL DEFAULT current_timestamp(),
  `location` varchar(500) DEFAULT NULL,
  `logstatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `personNumber` varchar(11) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `user_image` varchar(255) DEFAULT NULL,
  `user_profil_image` varchar(255) DEFAULT NULL,
  `Qr_code` varchar(255) DEFAULT NULL,
  `politi_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Indexes for table `admin`
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

-- Indexes for table `logs`
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

-- Indexes for table `users`
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personNumber` (`personNumber`);

-- AUTO_INCREMENT for dumped tables
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
