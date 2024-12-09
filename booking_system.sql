-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 09:26 PM
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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `time_slot_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_status` enum('Confirmed','Pending','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `class_id`, `time_slot_id`, `booking_date`, `booking_status`) VALUES
(1, 12, 26, 2, '2024-12-24', 'Pending'),
(2, 12, 26, 9, '2024-12-24', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_num` varchar(10) NOT NULL,
  `class_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_num`, `class_type_id`) VALUES
(14, 'Class 2000', 2),
(15, 'Class 2001', 2),
(16, 'Class 2002', 2),
(17, 'Class 2003', 2),
(18, 'Class 2004', 2),
(19, 'Class 2005', 2),
(20, 'Lab 10', 3),
(21, 'Lab 20', 3),
(22, 'Lab 30', 3),
(23, 'Lab 40', 3),
(24, 'Open Lab 1', 4),
(25, 'Open Lab 2', 4),
(26, 'Benefit La', 1),
(29, 'Lab 50', 3),
(36, 'Class 2006', 2);

--
-- Triggers `classes`
--
DELIMITER $$
CREATE TRIGGER `after_class_delete` AFTER DELETE ON `classes` FOR EACH ROW BEGIN
    UPDATE class_type
    SET class_count = class_count - 1
    WHERE class_type_id = OLD.class_type_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_class_insert` AFTER INSERT ON `classes` FOR EACH ROW BEGIN
    UPDATE class_type
    SET class_count = class_count + 1
    WHERE class_type_id = NEW.class_type_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_class_update` AFTER UPDATE ON `classes` FOR EACH ROW BEGIN
    -- Check if the class type has been updated
    IF OLD.class_type_id != NEW.class_type_id THEN
        -- Decrement the count for the old type
        UPDATE class_type
        SET class_count = class_count - 1
        WHERE class_type_id = OLD.class_type_id;

        -- Increment the count for the new type
        UPDATE class_type
        SET class_count = class_count + 1
        WHERE class_type_id = NEW.class_type_id;
    END IF;
END
$$
DELIMITER ;

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
(2, 'Class Room', '50-60 students', 'one PC, one projector and one white board.', 'class.jpg', 7),
(3, 'Lab Room', '30-40 students', '30-40 PC\'s, one projector and one white board.', 'lab.jpg', 5),
(4, 'Open Lab Area', '130-150 students', '60-70 PC\'s, one projector and two white boards, one microphone and speakers', 'open.jpg', 2),
(29, 'CLASS5', '15', '15', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `time_slot_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`time_slot_id`, `start_time`, `end_time`) VALUES
(1, '08:00:00', '09:00:00'),
(2, '09:00:00', '10:00:00'),
(3, '10:00:00', '11:00:00'),
(4, '11:00:00', '12:00:00'),
(5, '12:00:00', '13:00:00'),
(6, '13:00:00', '14:00:00'),
(7, '14:00:00', '15:00:00'),
(8, '15:00:00', '16:00:00'),
(9, '16:00:00', '17:00:00'),
(10, '17:00:00', '18:00:00');

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
(1, '', 'Maryam Husainn', '11111111', '$2y$10$99R48JdubD.51zBRuDri6uTMnjEqhcB9718/rIpq5Hy8hMwKd251O', '', 'uploads/675708d2d2cda.jpg'),
(2, '202011585@stu.uob.edu.bh', 'Mohammed Alhusaini', '+973 33414205', '$2y$10$u/rHU4xekWINDFKUitkwdueipn4Od.XZH4FKMHWhnPZ5cGBsLwpWq', '', 'Unknown.png'),
(3, '202100111@stu.uob.edu.bh', 'pass:123-Admin', '33333333', '123-Admin', 'admin', 'Unknown.png'),
(4, '202100863@stu.uob.edu.bh', 'ASMA', '32217880', '$2y$10$1eGP1se39abCMzixLxMevu8kRqWXXWymElNAHwnVmfFnS1Xr45Roy', '', 'Unknown.png'),
(5, 'salehmohmd@uob.edu.bh', 'Saleh Mohammed', '0097333695821', '$2y$10$z93bf6mdgNDN9lxGDXA9eORqqIR46OCPuT/TxZ6L6jrg0C37d0PT2', '', 'Unknown.png'),
(6, '202102021@stu.uob.edu.bh', 'haya', '33221155', '$2y$10$Y36125.7YRMGTcEehM4U/O8zQWi.9wqvwr3.U//9UeLkMsvwS/4ma', '', 'Unknown.png'),
(12, '202009537@stu.uob.edu.bh', 'Salman Alhawaj', '33333333', '$2y$10$Ycq0jiXIg2igX2N7o0l2R.LV8F97Iep.8zsjzNthLnvG54M0KAyVa', 'admin', 'image/67572c65bd8c3.jpg'),
(15, '202003214@stu.uob.edu.bh', 'Salman Alhawaj', '33333333', '$2y$10$v3aiM0IeJR7GQ.AYF6FQXOUPy5N5H3j2/XUQ/DTP876UqgB4eOmxa', 'admin', 'Unknown.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `time_slot_id` (`time_slot_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_num` (`class_num`),
  ADD KEY `class_type_id` (`class_type_id`);

--
-- Indexes for table `class_type`
--
ALTER TABLE `class_type`
  ADD PRIMARY KEY (`class_type_id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`time_slot_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `class_type`
--
ALTER TABLE `class_type`
  MODIFY `class_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `time_slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`time_slot_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`class_type_id`) REFERENCES `class_type` (`class_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
