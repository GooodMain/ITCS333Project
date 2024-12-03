-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 04:50 PM
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
-- Database: `booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(4) NOT NULL,
  `c_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `c_type`) VALUES
(1, 'class'),
(2, 'class'),
(3, 'lab'),
(4, 'lab'),
(5, 'open lab');

-- --------------------------------------------------------

--
-- Table structure for table `class_type`
--

CREATE TABLE `class_type` (
  `type` varchar(100) NOT NULL,
  `capacity` varchar(255) NOT NULL,
  `equipments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_type`
--

INSERT INTO `class_type` (`type`, `capacity`, `equipments`) VALUES
('benefit lab', '30-40 students', '30-40 PC\'s and one projector '),
('class', '50-60 students', 'one PC, one projector and one white board.'),
('lab', '30-40 students', '30-40 PC\'s, one projector and one white board.'),
('open lab', '130-150 students', '60-70 PC\'s, one projector and two white boards, one microphone and speakers');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `class_id` int(11) NOT NULL,
  `cl_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(100) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `phoneNum` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`email`, `fullName`, `phoneNum`, `password`, `user_type`) VALUES
('202011514@stu.uob.edu.bh', 'Maryam Husain', '39565657', '$2y$10$99R48JdubD.51zBRuDri6uTMnjEqhcB9718/rIpq5Hy8hMwKd251O', ''),
('202011585@stu.uob.edu.bh', 'Mohammed Alhusaini', '+973 33414205', '$2y$10$u/rHU4xekWINDFKUitkwdueipn4Od.XZH4FKMHWhnPZ5cGBsLwpWq', ''),
('202100111@stu.uob.edu.bh', 'pass:123-Admin', '33333333', '123-Admin', 'admin'),
('202100863@stu.uob.edu.bh', 'ASMA', '32217880', '$2y$10$1eGP1se39abCMzixLxMevu8kRqWXXWymElNAHwnVmfFnS1Xr45Roy', ''),
('202103399@stu.uob.edu.bh', 'Hussain Salah Mohammed', '39561188', '$2y$10$WXQBVKxRyD2XIhCgMKJIge2Mqbprf.1Ysegx.HCaOb0rMzlrAZg0u', ''),
('salehmohmd@uob.edu.bh', 'Saleh Mohammed', '0097333695821', '$2y$10$z93bf6mdgNDN9lxGDXA9eORqqIR46OCPuT/TxZ6L6jrg0C37d0PT2', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class_type`
--
ALTER TABLE `class_type`
  ADD UNIQUE KEY `idx_type` (`type`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_room_class_type` (`cl_type`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD CONSTRAINT `fk_room_class_type` FOREIGN KEY (`cl_type`) REFERENCES `class_type` (`type`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
