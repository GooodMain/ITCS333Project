-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 01:22 AM
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
-- Table structure for table `class_type`
--

CREATE TABLE `class_type` (
  `class_type_id` int(11) NOT NULL,
  `type_name` varchar(20) NOT NULL,
  `capacity` varchar(255) NOT NULL,
  `equipments` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `class_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_type`
--

INSERT INTO `class_type` (`class_type_id`, `type_name`, `capacity`, `equipments`, `image`, `class_count`) VALUES
(1, 'Benefit Lab', '30-40 students', '30-40 PC\'s and one projector ', 'benefit.jpg', 1),
(2, 'Class Room', '50-60 students', 'one PC, one projector and one white board.', 'class.jpg', 6),
(3, 'Lab Room', '30-40 students', '30-40 PC\'s, one projector and one white board.', 'lab.jpg', 4),
(4, 'Open Lab Area', '130-150 students', '60-70 PC\'s, one projector and two white boards, one microphone and speakers', 'open.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(5) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `phoneNum` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(10) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'Unknown.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `fullName`, `phoneNum`, `password`, `user_type`, `profile_picture`) VALUES
(1, '202011514@stu.uob.edu.bh', 'Maryam Husain', '39565657', '$2y$10$99R48JdubD.51zBRuDri6uTMnjEqhcB9718/rIpq5Hy8hMwKd251O', '', 'Unknown.png'),
(2, '202011585@stu.uob.edu.bh', 'Mohammed Alhusaini', '+973 33414205', '$2y$10$u/rHU4xekWINDFKUitkwdueipn4Od.XZH4FKMHWhnPZ5cGBsLwpWq', '', 'Unknown.png'),
(3, '202100111@stu.uob.edu.bh', 'pass:123-Admin', '33333333', '123-Admin', 'admin', 'Unknown.png'),
(4, '202100863@stu.uob.edu.bh', 'ASMA', '32217880', '$2y$10$1eGP1se39abCMzixLxMevu8kRqWXXWymElNAHwnVmfFnS1Xr45Roy', '', 'Unknown.png'),
(5, 'salehmohmd@uob.edu.bh', 'Saleh Mohammed', '0097333695821', '$2y$10$z93bf6mdgNDN9lxGDXA9eORqqIR46OCPuT/TxZ6L6jrg0C37d0PT2', '', 'Unknown.png'),
(6, '202102021@stu.uob.edu.bh', 'haya', '33221155', '$2y$10$Y36125.7YRMGTcEehM4U/O8zQWi.9wqvwr3.U//9UeLkMsvwS/4ma', '', 'Unknown.png'),
(7, '202103399@stu.uob.edu.bh', 'Hussain Salah', '39561188', '$2y$10$1.yh5ZpF8oUJnyJBYn66je/awjLC5rzTacmCOmV1PAZaCz.9qqj3q', '', 'Unknown.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class_type`
--
ALTER TABLE `class_type`
  ADD PRIMARY KEY (`class_type_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class_type`
--
ALTER TABLE `class_type`
  MODIFY `class_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
