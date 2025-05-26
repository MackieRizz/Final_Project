-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 26, 2025 at 09:16 PM
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
-- Database: `final_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_passcode`
--

CREATE TABLE `admin_passcode` (
  `id` int(11) NOT NULL,
  `passcode` varchar(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_passcode`
--

INSERT INTO `admin_passcode` (`id`, `passcode`, `created_at`) VALUES
(1, '12345678', '2025-05-26 19:16:16');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_positions`
--

CREATE TABLE `candidate_positions` (
  `id` int(11) NOT NULL,
  `position_id` int(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `program` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students_registration`
--

CREATE TABLE `students_registration` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `student_id` varchar(15) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `qr_code_path` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `section` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_registration`
--

INSERT INTO `students_registration` (`id`, `fullname`, `student_id`, `department`, `program`, `qr_code_path`, `gender`, `section`, `created_at`) VALUES
(1, 'Ma. Althea Bhea Daza', '2022-12345', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-12345.png', 'Female', '3C', '2025-05-25 17:51:09'),
(2, 'Almackie Andrew J. Bangalao', '2022-30424', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-30424.png', 'Male', '3C', '2025-05-26 11:53:26'),
(4, 'Reyna Marie G. Boyboy', '2022-40312', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-40312.png', 'Female', '3C', '2025-05-26 12:22:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_passcode`
--
ALTER TABLE `admin_passcode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_registration`
--
ALTER TABLE `students_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_passcode`
--
ALTER TABLE `admin_passcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students_registration`
--
ALTER TABLE `students_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
