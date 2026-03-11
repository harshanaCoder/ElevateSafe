-- ElevateSafe Maintenance System Database Schema
-- Generated for phpMyAdmin Import

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--
-- Password is 'admin123' (hashed)
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `breakdowns`
--

CREATE TABLE IF NOT EXISTS `breakdowns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_no` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT 'Uncategorized',
  `nature_of_breakdown` varchar(255) NOT NULL,
  `work_description` text NOT NULL,
  `inform_date` date NOT NULL,
  `inform_time` time NOT NULL,
  `attendent_date` date NOT NULL,
  `attended_time` time NOT NULL,
  `team` varchar(100) NOT NULL,
  `submit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_unit` (`unit_no`),
  KEY `idx_inform_date` (`inform_date`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- IMPORTANT: Run this manually if the table already exists:
-- ALTER TABLE `breakdowns` ADD COLUMN `category` varchar(50) DEFAULT 'Uncategorized' AFTER `unit_no`;
-- ALTER TABLE `breakdowns` ADD INDEX `idx_category` (`category`);

COMMIT;
