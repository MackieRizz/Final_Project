-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 06:03 AM
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
(3, 'Michael Johnson', '2022-10003', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10003.png', 'Male', 'A', '2025-05-26 16:05:07'),
(4, 'Emily Davis', '2022-10004', 'Computer Studies Department', 'BSIT', 'qr_codes/2022-10004.png', 'Female', 'C', '2025-05-26 16:05:07'),
(5, 'Daniel Brown', '2022-10005', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10005.png', 'Male', 'B', '2025-05-26 16:05:07'),
(9, 'James Martinez', '2022-10009', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10009.png', 'Male', 'B', '2025-05-26 16:05:07'),
(11, 'Benjamin Lopez', '2022-10011', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10011.png', 'Male', 'A', '2025-05-26 16:05:07'),
(14, 'Charlotte Perez', '2022-10014', 'Computer Studies Department', 'BSIT', 'qr_codes/2022-10014.png', 'Female', 'A', '2025-05-26 16:05:07'),
(15, 'Henry Anderson', '2022-10015', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10015.png', 'Male', 'B', '2025-05-26 16:05:07'),
(17, 'Alexander Taylor', '2022-10017', 'Engineering Department', 'BSECE', 'qr_codes/2022-10017.png', 'Male', 'A', '2025-05-26 16:05:07'),
(18, 'Evelyn Moore', '2022-10018', 'Engineering Department', 'BSEE', 'qr_codes/2022-10018.png', 'Female', 'B', '2025-05-26 16:05:07'),
(19, 'Sebastian Jackson', '2022-10019', 'Engineering Department', 'BSCE', 'qr_codes/2022-10019.png', 'Male', 'C', '2025-05-26 16:05:07'),
(20, 'Harper White', '2022-10020', 'Engineering Department', 'BSME', 'qr_codes/2022-10020.png', 'Female', 'A', '2025-05-26 16:05:07'),
(21, 'Elijah Harris', '2022-10021', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10021.png', 'Male', 'B', '2025-05-26 16:05:07'),
(22, 'Avery Martin', '2022-10022', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10022.png', 'Female', 'C', '2025-05-26 16:05:07'),
(23, 'Logan Thompson', '2022-10023', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10023.png', 'Male', 'A', '2025-05-26 16:05:07'),
(24, 'Ella Martinez', '2022-10024', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10024.png', 'Female', 'B', '2025-05-26 16:05:07'),
(25, 'Jackson Lee', '2022-10025', 'Teacher Education Department', 'BSED English', 'qr_codes/2022-10025.png', 'Male', 'C', '2025-05-26 16:05:07'),
(26, 'Lily Clark', '2022-10026', 'Teacher Education Department', 'BSED Math', 'qr_codes/2022-10026.png', 'Female', 'A', '2025-05-26 16:05:07'),
(27, 'Mateo Lewis', '2022-10027', 'Teacher Education Department', 'BSED Science', 'qr_codes/2022-10027.png', 'Male', 'B', '2025-05-26 16:05:07'),
(28, 'Aria Walker', '2022-10028', 'Teacher Education Department', 'BEED', 'qr_codes/2022-10028.png', 'Female', 'C', '2025-05-26 16:05:07'),
(29, 'Jack Hall', '2022-10029', 'Teacher Education Department', 'BEED', 'qr_codes/2022-10029.png', 'Male', 'A', '2025-05-26 16:05:07'),
(30, 'Sofia Allen', '2022-10030', 'Teacher Education Department', 'BSED Filipino', 'qr_codes/2022-10030.png', 'Female', 'B', '2025-05-26 16:05:07'),
(31, 'Leo Young', '2022-10031', 'Industrial Technology Department', 'BSIT', 'qr_codes/2022-10031.png', 'Male', 'C', '2025-05-26 16:05:07'),
(32, 'Scarlett King', '2022-10032', 'Industrial Technology Department', 'BSIT', 'qr_codes/2022-10032.png', 'Female', 'A', '2025-05-26 16:05:07'),
(33, 'Grayson Wright', '2022-10033', 'Industrial Technology Department', 'BSIT', 'qr_codes/2022-10033.png', 'Male', 'B', '2025-05-26 16:05:07'),
(34, 'Chloe Scott', '2022-10034', 'Industrial Technology Department', 'BSIT', 'qr_codes/2022-10034.png', 'Female', 'C', '2025-05-26 16:05:07'),
(37, 'Owen Baker', '2022-10037', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10037.png', 'Male', 'C', '2025-05-26 16:05:07'),
(38, 'Victoria Nelson', '2022-10038', 'Computer Studies Department', 'BSIT', 'qr_codes/2022-10038.png', 'Female', 'A', '2025-05-26 16:05:07'),
(39, 'Wyatt Carter', '2022-10039', 'Computer Studies Department', 'BSCS', 'qr_codes/2022-10039.png', 'Male', 'B', '2025-05-26 16:05:07'),
(40, 'Hannah Mitchell', '2022-10040', 'Computer Studies Department', 'BSIT', 'qr_codes/2022-10040.png', 'Female', 'C', '2025-05-26 16:05:07'),
(41, 'Dylan Roberts', '2022-10041', 'Engineering Department', 'BSECE', 'qr_codes/2022-10041.png', 'Male', 'A', '2025-05-26 16:05:07'),
(42, 'Zoey Turner', '2022-10042', 'Engineering Department', 'BSCE', 'qr_codes/2022-10042.png', 'Female', 'B', '2025-05-26 16:05:07'),
(43, 'Nathan Phillips', '2022-10043', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10043.png', 'Male', 'C', '2025-05-26 16:05:07'),
(44, 'Penelope Campbell', '2022-10044', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10044.png', 'Female', 'A', '2025-05-26 16:05:07'),
(45, 'Caleb Parker', '2022-10045', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10045.png', 'Male', 'B', '2025-05-26 16:05:07'),
(46, 'Riley Evans', '2022-10046', 'Business and Management Department', 'BSBA', 'qr_codes/2022-10046.png', 'Female', 'C', '2025-05-26 16:05:07'),
(47, 'Nathaniel Edwards', '2022-10047', 'Teacher Education Department', 'BSED Math', 'qr_codes/2022-10047.png', 'Male', 'A', '2025-05-26 16:05:07'),
(50, 'Stella Sanchez', '2022-10050', 'Industrial Technology Department', 'BSIT', 'qr_codes/2022-10050.png', 'Female', 'A', '2025-05-26 16:05:07'),
(52, 'Ma. Althea Bhea Daza', '2022-30383', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-30383.png', 'Female', '3C', '2025-05-28 01:25:55'),
(53, 'Almackie Bangalao', '2022-12345', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-12345.png', 'Male', '3C', '2025-05-28 01:52:01'),
(56, 'Kata Kata', '2022-98754', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-98754.png', 'Female', '3A', '2025-05-28 02:02:36'),
(58, 'Kapoy Na', '2022-35473', 'Engineering Department', 'Bachelor of Science in Electrical Engineering (BSEE)', '../qr_codes/QRCode_2022-35473.png', 'Female', '3A', '2025-05-28 03:57:28');

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
(1, '2022-30383', '2025-05-28 11:46:54', '2025-05-28 05:47:18', 'Voted'),
(3, '2022-35473', '2025-05-28 11:58:17', '2025-05-28 05:58:40', 'Voted');

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
(9, '2022-35473', 2, 3, '2025-05-28 05:58:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students_registration`
--
ALTER TABLE `students_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

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
-- AUTO_INCREMENT for table `students_registration`
--
ALTER TABLE `students_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `student_votes`
--
ALTER TABLE `student_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
