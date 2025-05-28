-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 28, 2025 at 10:44 PM
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
-- Database: `Final_Project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `status` enum('unverified','verified') DEFAULT 'unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `username`, `password`, `profile_pic`, `otp`, `status`) VALUES
(1, 'almackieandrew.bangalao@evsu.edu.ph', 'SARJAGA', '$2y$10$5Rq2ddj3IdR5y5sBXGjntusyez6rPrLb1fou3NO/6O6L5Rep0POwO', 'uploads/profile_pics/profile_6837646f755bf.jpg', NULL, 'verified');

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
(1, '12345678', '2025-05-26 11:16:16');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_positions`
--

CREATE TABLE `candidate_positions` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `program` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_positions`
--

INSERT INTO `candidate_positions` (`id`, `candidate_id`, `position_id`, `position`, `name`, `year`, `program`, `image`) VALUES
(1, 1, 1, 'President', 'Sung Jin Woo', '4th', 'Bachelor of Solo Leveling', 'uploads/candidate_683497ec77aaa_sung-jinwoo-8k-7680x4320-14673.jpg'),
(2, 2, 1, 'President', 'Tirzo Woo', '4th', 'Bachelor of Solo Leveling', 'uploads/candidate_683497ec7ac3d_6328115552077334118.jpg'),
(4, 2, 2, 'Vice-President', 'Meow', '3rd', 'Bachelor of Meowing', 'uploads/candidate_6834a3c23f45d_enhance-the-beauty-of-this-cat-s-eye-showing-the-amazing-colors-and-textures-in-great-detail-make-it-look-as-realistic-as-possible-almost-like-agraph-free-photo.jpg'),
(5, 3, 2, 'Vice-President', 'Please', '2nd', 'Bachelor of Meowing', 'uploads/candidate_683683333500b_6328115552077334118.jpg'),
(6, 1, 3, 'Secretary', 'Kapoy', '2nd', 'Bachelor of Solo Leveling', 'uploads/candidate_683686a3d8504_6147625809230282886.jpg'),
(7, 3, 1, 'President', 'COJ', '4th', 'Bachelor of Solo Leveling', 'uploads/candidate_6836871849454_7bd74bfe-e57f-43d6-b9d7-28c1d156fb04_removalai_preview.png'),
(8, 2, 3, 'Secretary', 'Hambot', '1st', 'Bachelor of Meowing', 'uploads/candidate_683689721f49d_DIGITAL LITERACY POSTER.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `scanner_settings`
--

CREATE TABLE `scanner_settings` (
  `id` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scanner_settings`
--

INSERT INTO `scanner_settings` (`id`, `start_datetime`, `end_datetime`, `created_at`, `updated_at`) VALUES
(8, '2025-05-30 04:33:00', '2025-05-30 23:59:00', '2025-05-28 20:35:20', '2025-05-28 20:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `students_registration`
--

CREATE TABLE `students_registration` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `student_id` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `qr_code_path` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `section` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp` varchar(10) DEFAULT NULL,
  `status` enum('pending','verified') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_registration`
--

INSERT INTO `students_registration` (`id`, `fullname`, `student_id`, `email`, `department`, `program`, `qr_code_path`, `gender`, `section`, `created_at`, `otp`, `status`) VALUES
(59, 'Stephanie Angel  A. Nudalo', '2022-31997', 'stephanieangel.nudalo@evsu.edu.ph', 'Engineering Department', 'Bachelor of Science in Civil Engineering (BSCE)', '../qr_codes/QRCode_2022-31997.png', 'Female', '3B', '2025-05-28 17:39:37', NULL, 'pending'),
(61, 'Almackie Andrew Bangalao', '2022-30424', 'almackieandrew.bangalao@evsu.edu.ph', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-30424.png', 'Male', '3C', '2025-05-28 19:00:48', '856147', 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `student_votes`
--

CREATE TABLE `student_votes` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `scan_time` datetime NOT NULL,
  `vote_time` datetime DEFAULT NULL,
  `status` enum('Didn''t vote yet','Voted') NOT NULL DEFAULT 'Didn''t vote yet'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_votes`
--

INSERT INTO `student_votes` (`id`, `student_id`, `scan_time`, `vote_time`, `status`) VALUES
(4, '2022-31997', '2025-05-29 01:40:16', '2025-05-28 19:40:28', 'Voted');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `student_id` varchar(15) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `vote_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `student_id`, `candidate_id`, `position_id`, `vote_time`) VALUES
(1, '2022-30383', 3, 1, '2025-05-28 05:47:18'),
(2, '2022-30383', 3, 2, '2025-05-28 05:47:18'),
(3, '2022-30383', 1, 3, '2025-05-28 05:47:18'),
(4, '2022-37643', 3, 1, '2025-05-28 05:55:27'),
(5, '2022-37643', 2, 2, '2025-05-28 05:55:27'),
(6, '2022-37643', 1, 3, '2025-05-28 05:55:27'),
(7, '2022-35473', 1, 1, '2025-05-28 05:58:40'),
(8, '2022-35473', 2, 2, '2025-05-28 05:58:40'),
(9, '2022-35473', 2, 3, '2025-05-28 05:58:40'),
(10, '2022-31997', 2, 1, '2025-05-28 19:40:28'),
(11, '2022-31997', 2, 2, '2025-05-28 19:40:28'),
(12, '2022-31997', 1, 3, '2025-05-28 19:40:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scanner_settings`
--
ALTER TABLE `scanner_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_registration`
--
ALTER TABLE `students_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_votes`
--
ALTER TABLE `student_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scanner_settings`
--
ALTER TABLE `scanner_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students_registration`
--
ALTER TABLE `students_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `student_votes`
--
ALTER TABLE `student_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_votes`
--
ALTER TABLE `student_votes`
  ADD CONSTRAINT `student_votes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_registration` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
