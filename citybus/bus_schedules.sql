-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 15, 2025 at 04:42 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bus_tracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus_schedules`
--

CREATE TABLE `bus_schedules` (
  `schedule_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `stop_name` varchar(255) NOT NULL,
  `departure_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_schedules`
--

INSERT INTO `bus_schedules` (`schedule_id`, `route_id`, `stop_name`, `departure_time`) VALUES
(1, 25, 'Janmahel (City Bus Stand)', '05:50:00'),
(2, 25, 'Panchvati', '06:35:00'),
(3, 25, 'Janmahel (City Bus Stand)', '07:20:00'),
(4, 25, 'Panchvati', '08:15:00'),
(5, 25, 'Janmahel (City Bus Stand)', '09:05:00'),
(6, 25, 'Panchvati', '09:55:00'),
(7, 25, 'Janmahel (City Bus Stand)', '10:45:00'),
(8, 25, 'Panchvati', '11:35:00'),
(9, 25, 'Janmahel (City Bus Stand)', '12:25:00'),
(10, 25, 'Panchvati', '13:15:00'),
(11, 25, 'Janmahel (City Bus Stand)', '14:05:00'),
(12, 25, 'Panchvati', '14:55:00'),
(13, 25, 'Janmahel (City Bus Stand)', '15:45:00'),
(14, 25, 'Panchvati', '16:35:00'),
(15, 25, 'Janmahel (City Bus Stand)', '17:25:00'),
(16, 25, 'Panchvati', '18:15:00'),
(17, 25, 'Janmahel (City Bus Stand)', '19:05:00'),
(18, 25, 'Panchvati', '19:55:00'),
(19, 25, 'Janmahel (City Bus Stand)', '20:45:00'),
(20, 25, 'Panchvati', '21:35:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus_schedules`
--
ALTER TABLE `bus_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `route_id` (`route_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bus_schedules`
--
ALTER TABLE `bus_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus_schedules`
--
ALTER TABLE `bus_schedules`
  ADD CONSTRAINT `bus_schedules_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `bus_routes` (`route_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
