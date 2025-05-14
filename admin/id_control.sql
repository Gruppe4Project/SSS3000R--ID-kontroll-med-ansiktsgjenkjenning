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

--
-- Database: `id_control`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `creationDate`, `updationDate`) VALUES
(9, 'momo', '9141f2f132dff383dbacde95f39b1b88', '2025-01-04 20:22:52', '2025-01-04 21:26:56'),
(10, 'group4', 'f8fc69e57c83d18a47b3360dae92cbbb', '2025-02-27 16:10:26', '2025-02-27 16:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `personNumber` varchar(20) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `time_date` timestamp NULL DEFAULT current_timestamp(),
  `location` varchar(500) DEFAULT NULL,
  `logstatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `personNumber`, `username`, `time_date`, `location`, `logstatus`) VALUES
(1, '20067612345', 'Mohammed Morjane', '2025-04-08 11:46:21', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Permission Granted - All Allowed'),
(2, 'Unknown', 'Personen er ikke registrert!', '2025-04-08 14:52:08', 'Malmskriverveien 18, 1337 Sandvika, Norge', 'Permission Denied - Not Registered'),
(3, 'Unknown', 'Personen er ikke registrert!', '2025-04-08 14:53:13', 'Malmskriverveien 18, 1337 Sandvika, Norge', 'Permission Denied - Not Registered'),
(4, 'Unknown', 'Person er ikke registrert!', '2025-04-08 14:54:48', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Person er ikke registret'),
(5, 'Unknown', 'Personen er ikke registrert!', '2025-04-08 15:03:46', 'Malmskriverveien 18, 1337 Sandvika, Norge', 'Permission Denied - Not Registered'),
(6, '20067612345', 'Mohammed Morjane', '2025-04-08 15:04:15', 'Malmskriverveien 18, 1337 Sandvika, Norge', 'Permission Granted - All Allowed'),
(7, 'Unknown', 'Personen er ikke registrert!', '2025-04-08 15:04:18', 'Malmskriverveien 18, 1337 Sandvika, Norge', 'Permission Denied - Not Registered'),
(8, '20067612345', 'Mohammed Morjane', '2025-04-08 16:04:59', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Permission Granted - All Allowed'),
(9, '20067612345', 'Mohammed Morjane', '2025-04-08 18:52:39', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Permission Granted - All Allowed'),
(10, 'Unknown', 'Person er ikke registrert!', '2025-04-08 19:25:00', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Person er ikke registret'),
(11, 'Unknown', 'Person er ikke registrert!', '2025-04-08 19:27:48', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Person er ikke registret'),
(12, 'Unknown', 'Person er ikke registrert!', '2025-04-08 19:32:37', 'Bredalsveien 14, 3511 Hønefoss, Norge', 'Person er ikke registret');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `personNumber`, `fullname`, `date_of_birth`, `adresse`, `email`, `created_at`, `user_image`, `user_profil_image`, `Qr_code`, `politi_status`) VALUES
(1, '20067612345', 'Mohammed Morjane', '1976-06-20', 'østeråsen 81c, østerås 1234', 'mohammed.morjane@hotmail.com', '2025-04-08 19:31:25', 'dataset/Mohammed Morjane/Mohammed Morjane_20250408_213125.jpg', 'profile_1_1744112654.jpg', 'dataset/QR_codes/QR_20067612345.png', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personNumber` (`personNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
