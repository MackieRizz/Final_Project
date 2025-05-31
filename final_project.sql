-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2025 at 09:16 AM
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
(1, 'almackieandrew.bangalao@evsu.edu.ph', 'SARJAGA', '$2y$10$5Rq2ddj3IdR5y5sBXGjntusyez6rPrLb1fou3NO/6O6L5Rep0POwO', 'uploads/profile_pics/profile_6837646f755bf.jpg', NULL, 'verified'),
(4, 'maaltheabhea.daza@evsu.edu.ph', 'Altiya', '$2y$10$svD9mZUEdSt27AWdLEQ4UOHpRZDaG1xluzHnORCQY/zLmLggQ9UI.', 'uploads/profile_pics/profile_6838a7debc568.jpg', NULL, 'verified'),
(5, 'stephanieangel.nudalo@evsu.edu.ph', 'stefhany', '$2y$10$1w4ljUNeGKBfeVhKcOCsQuB5Trk3p5YLXvIeYD/56Z3GEBgNmvJxu', 'uploads/profile_pics/profile_68394ccf23e63.jpg', NULL, 'verified');

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
(1, 1, 1, 'President', 'Marc Fritz Aseo', '1st', 'Bachelor of Handsomeness', 'uploads/candidate_683a98977e97a_6312061879152919097.jpg'),
(2, 2, 1, 'President', 'Edward Bertulfo', '2nd', 'Bachelor of Hagbogness', 'uploads/candidate_683957e868b0a_IMG_0177.JPG'),
(3, 1, 2, 'Vice-President', 'Joseph Jaymel Morpos', '2nd', 'Bachelor of EducTour', 'uploads/candidate_6839588d315c5_IMG_0175.JPG'),
(4, 2, 2, 'Vice-President', 'Jeffry Ocay', '2nd', 'Bachelor of Gardenness', 'uploads/candidate_6839588d32572_IMG_0194.JPG'),
(5, 1, 3, 'Secretary', 'Fiona', '3rd', 'Bachelor of Kupit', 'uploads/candidate_68395900b74b3_IMG_0218.JPG'),
(6, 2, 3, 'Secretary', 'Tae', '3rd', 'Bachelor of Makapungot', 'uploads/candidate_68395900b874c_IMG_0195.JPG'),
(7, 1, 4, 'Treasurer', 'Ms. Registrar', '4th', 'Bachelor of Money', 'uploads/candidate_683959601cea3_6190663219782205145.jpg'),
(8, 2, 4, 'Treasurer', 'Ms. Cashier Office', '4th', 'Bachelor of Bank', 'uploads/candidate_683959601e42b_6289303272653178459.jpg'),
(9, 1, 5, 'Auditor', 'Jollibee', '1st', 'Bachelor of Mix&Match', 'uploads/candidate_683959f574903_1741087355605.jpg'),
(10, 2, 5, 'Auditor', 'McDonalds', '1st', 'Bachelor of ChickenJoy', 'uploads/candidate_683959f575da3_6215005827400581132.jpg');

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
(27, '2025-05-31 15:09:00', '2025-05-31 22:10:00', '2025-05-31 07:14:18', '2025-05-31 07:14:18');

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
(66, 'Tirzo Charles Apuya', '2022-31913', 'tirzocharles.apuya@evsu.edu.ph', 'Computer Studies Department', 'Bachelor of Science in Information Technology (BSIT)', '../qr_codes/QRCode_2022-31913.png', 'Male', '3B', '2025-05-30 06:39:56', '264734', 'verified'),
(67, 'Reyna Marie G. Boyboy', '2022-32000', 'reynamarie.boyboy@evsu.edu.ph', 'Business and Management Department', 'Bachelor of Science in Hospitality Management (BSHM)', '../qr_codes/QRCode_2022-32000.png', 'Female', '3A', '2025-05-30 06:43:09', '318642', 'verified'),
(68, 'Almackie Bangalao', '2022-30424', 'almackieandrew.bangalao@evsu.edu.ph', 'Engineering Department', 'Bachelor of Science in Civil Engineering (BSCE)', '../qr_codes/QRCode_2022-30424.png', 'Male', '3B', '2025-05-30 06:45:57', '342432', 'verified'),
(69, 'Stephanie Angel Nudalo', '2022-31997', 'stephanieangel.nudalo@evsu.edu.ph', 'Industrial Technology Department', 'Bachelor of Industrial Technology (BIT) with major in Culinary Arts (CA)', '../qr_codes/QRCode_2022-31997.png', 'Female', '3A', '2025-05-30 06:50:42', '318269', 'verified'),
(70, 'Ruby Tinunga', '2022-30222', 'ruby.tinunga@evsu.edu.ph', 'Teacher Education Department', 'Bachelor of Technical-Vocational Teacher Education (BTVTEd)', '../qr_codes/QRCode_2022-30222.png', 'Female', '3C', '2025-05-30 06:53:13', '485101', 'verified'),
(71, 'Jenica Tayab', '2022-31408', 'jenica.tayab@evsu.edu.ph', 'Teacher Education Department', 'Bachelor of Secondary Education (BSEd) major in Science', '../qr_codes/QRCode_2022-31408.png', 'Female', '3C', '2025-05-30 06:55:34', '365737', 'verified');

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
(9, '2022-32000', '2025-05-31 13:53:41', NULL, 'Didn\'t vote yet');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scanner_settings`
--
ALTER TABLE `scanner_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `students_registration`
--
ALTER TABLE `students_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `student_votes`
--
ALTER TABLE `student_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
