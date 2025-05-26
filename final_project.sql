-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 06:37 PM
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
(3, 1, 2, 'Vice-President', 'Meow', '2nd', 'Bachelor of Meowing', 'uploads/candidate_68349832366c8_enhance-the-beauty-of-this-cat-s-eye-showing-the-amazing-colors-and-textures-in-great-detail-make-it-look-as-realistic-as-possible-almost-like-agraph-free-photo.jpg'),
(4, 2, 2, 'Vice-President', 'Hambot', '2nd', 'Bachelor of Meowing', 'uploads/candidate_6834983238535_6327664803849552034.jpg');

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
(51, 'Ma. Althea Bhea Daza', '2022-30383', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-30383.png', 'Female', '3C', '2025-05-26 16:24:15');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students_registration`
--
ALTER TABLE `students_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
