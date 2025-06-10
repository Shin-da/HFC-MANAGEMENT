-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 12:46 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbhenrichfoodcorps`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_aggregate_daily_metrics` ()   BEGIN
    INSERT INTO financial_metrics (period_date, total_revenue, net_profit, operating_costs)
    SELECT 
        CURRENT_DATE,
        SUM(ol.quantity * ol.unit_price) as revenue,
        SUM(ol.quantity * (ol.unit_price - p.unit_price)) as profit,
        (SELECT SUM(availablequantity * unit_price) * 0.1 FROM inventory) as costs
    FROM orderlog ol
    JOIN products p ON ol.productcode = p.productcode
    WHERE DATE(ol.orderdate) = CURRENT_DATE
    ON DUPLICATE KEY UPDATE
        total_revenue = VALUES(total_revenue),
        net_profit = VALUES(net_profit),
        operating_costs = VALUES(operating_costs);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_calculate_performance` ()   BEGIN
    INSERT INTO department_performance (department, performance_score, target_achievement, period_date)
    SELECT 
        'Sales',
        (COUNT(*) * 100.0) / MAX(target_orders) as score,
        SUM(ordertotal) / MAX(target_revenue) * 100 as achievement,
        CURRENT_DATE
    FROM customerorder
    CROSS JOIN (
        SELECT 100 as target_orders, 1000000 as target_revenue
    ) as targets
    WHERE orderdate = CURRENT_DATE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_requests`
--

CREATE TABLE `account_requests` (
  `request_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_date` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_requests`
--

INSERT INTO `account_requests` (`request_id`, `firstname`, `lastname`, `email`, `department`, `position`, `reason`, `status`, `request_date`, `processed_date`, `processed_by`, `rejection_reason`, `notes`) VALUES
(1, 'Jeff', 'Mathew Garcia', 'henrichsupervisoor@henrich.com', 'Inventory', 'Supervisor', 'I am the supervisor', 'rejected', '2025-03-29 18:42:08', '2025-04-17 12:57:22', 4, 'only a test', NULL),
(2, 'Shin', 'da', 'shinshin04@henrich.com', 'Logistics', 'packer', 'please', 'pending', '2025-04-17 03:04:11', '2025-04-17 03:43:01', 4, NULL, NULL),
(3, 'test', 'order', 'testinglang@henrich.com', 'Administration', 'testaccount', 'test kung saan papasok', 'rejected', '2025-04-17 05:12:18', '2025-04-17 13:01:51', 4, 'testing batch rejection', NULL),
(4, 'Shin', 'da', 'shinshin04@henrich.com', 'Warehouse', 'Packeeer', 'testing form', 'rejected', '2025-04-17 05:43:09', '2025-04-17 09:58:52', 4, NULL, NULL),
(5, 'Shin', 'da', 'shinshin04@henrich.com', 'Warehouse', 'Packeeer', 'testing form', 'approved', '2025-04-17 09:59:15', '2025-04-17 09:59:23', 4, NULL, NULL),
(6, 'Shin', 'da', 'shinshinu04@henrich.com', 'Administration', 'admin', 'please', 'rejected', '2025-04-17 12:37:42', '2025-04-17 12:37:51', 4, 'No reason provided', NULL),
(7, 'Shin', 'da', 'shinshinu04@henrich.com', 'Administration', 'admin', 'please', 'approved', '2025-04-17 12:39:49', '2025-04-17 12:39:56', 4, NULL, NULL),
(8, 'sa', 'na', 'sanaceo@henrich.com', 'Administration', 'CEO', 'ceo ako', 'approved', '2025-04-17 22:12:07', '2025-04-17 22:12:19', 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `activity_type`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:39:02'),
(2, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:39:18'),
(3, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:57:14'),
(4, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:57:26'),
(5, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:58:10'),
(6, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 03:59:03'),
(7, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 04:32:01'),
(8, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 05:17:56'),
(9, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 06:12:44'),
(10, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 11:19:16'),
(11, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-03-30 11:19:28'),
(12, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-09 06:05:26'),
(13, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-09 06:48:33'),
(14, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-09 09:31:38'),
(15, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-09 09:55:27'),
(16, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-09 10:45:36'),
(17, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-09 10:52:36'),
(18, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-09 10:52:40'),
(19, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-10 06:57:18'),
(20, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 03:00:48'),
(21, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:01:02'),
(22, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:01:09'),
(23, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:01:44'),
(24, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:01:45'),
(25, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:01:47'),
(26, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:02:26'),
(27, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:02:36'),
(28, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 03:02:40'),
(29, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 03:02:46'),
(30, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 03:17:52'),
(31, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 05:49:17'),
(32, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 07:14:58'),
(33, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 09:58:28'),
(34, 10, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:02:06'),
(35, 10, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 10:09:31'),
(36, 10, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 10:09:32'),
(37, 10, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 10:09:48'),
(38, 10, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 10:09:53'),
(39, 10, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 10:09:58'),
(40, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:39:30'),
(41, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:43:33'),
(42, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:45:40'),
(43, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:46:09'),
(44, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:47:29'),
(45, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:48:44'),
(46, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:53:23'),
(47, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:54:29'),
(48, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 10:59:47'),
(49, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:00:45'),
(50, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:01:25'),
(51, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:05:19'),
(52, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:05:47'),
(53, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:06:51'),
(54, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:10:27'),
(55, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:10:29'),
(56, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:10:50'),
(57, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:10:52'),
(58, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:12:24'),
(59, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:12:26'),
(60, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:12:28'),
(61, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:14:27'),
(62, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:14:32'),
(63, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:14:34'),
(64, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:17:26'),
(65, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:17:29'),
(66, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:19:09'),
(67, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:19:11'),
(68, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:22:30'),
(69, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:22:32'),
(70, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:25:14'),
(71, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:30:18'),
(72, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:30:21'),
(73, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:30:24'),
(74, 2, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:34:23'),
(75, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 11:34:40'),
(76, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:35:03'),
(77, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:35:14'),
(78, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:35:53'),
(79, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 11:35:56'),
(80, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 11:50:33'),
(81, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 12:34:29'),
(82, 2, 'logout', 'User logged out', NULL, NULL, '2025-04-17 12:36:31'),
(83, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 12:45:02'),
(84, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 13:19:09'),
(85, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 13:19:21'),
(86, 2, 'logout', 'User logged out', NULL, NULL, '2025-04-17 16:23:40'),
(87, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 16:23:46'),
(88, 4, 'logout', 'User logged out', NULL, NULL, '2025-04-17 16:57:49'),
(89, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 16:57:54'),
(90, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 19:37:04'),
(91, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 22:07:06'),
(92, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 22:09:46'),
(93, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 22:09:51'),
(94, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 22:09:54'),
(95, 4, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 22:10:32'),
(96, 2, 'logout', 'User logged out', NULL, NULL, '2025-04-17 22:11:25'),
(97, 12, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-17 22:12:58'),
(98, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 22:14:16'),
(99, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 22:14:18'),
(100, 2, 'logout', 'User logged out', NULL, NULL, '2025-04-17 22:15:43'),
(101, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-17 22:15:47'),
(102, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-18 07:19:06'),
(103, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-18 07:21:24'),
(104, 3, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-18 10:29:46'),
(105, 2, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-18 10:30:29'),
(106, 2, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-18 10:31:43'),
(107, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-18 10:31:48'),
(108, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-18 10:31:57'),
(109, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-18 11:00:50'),
(110, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-19 09:10:13'),
(111, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-19 10:21:33'),
(112, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-19 17:20:05'),
(113, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-19 17:20:23'),
(114, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-20 08:40:58'),
(115, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-20 08:41:01'),
(116, 4, 'failed_login', 'Failed login attempt', NULL, NULL, '2025-04-20 08:41:03'),
(117, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 08:41:55'),
(118, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 08:54:06'),
(119, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 08:57:15'),
(120, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:01:59'),
(121, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:17:52'),
(122, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:19:08'),
(123, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:19:47'),
(124, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:22:43'),
(125, 2, 'login', 'User logged in successfully', NULL, NULL, '2025-04-20 09:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approvedaccount_history`
--

CREATE TABLE `approvedaccount_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `usermail` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` enum('admin','supervisor','ceo') DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT NULL,
  `last_online` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvedaccount_history`
--

INSERT INTO `approvedaccount_history` (`history_id`, `user_id`, `usermail`, `username`, `role`, `password`, `first_name`, `last_name`, `status`, `created_at`, `updated_at`, `is_online`, `last_online`, `approved_at`) VALUES
(1, 23, 'testorbe@henrich.com', 'testtest', 'supervisor', '$2y$10$izr8nJy3h5wrad9bLPVjt.0785N/g/r/6AZDCbvAqloryS1JLDOTS', 'test', 'tesst', '', '2025-02-02 07:09:03', '2025-02-02 07:09:03', 0, NULL, '2025-02-02 07:09:03'),
(2, 22, 'maiigdlg@henrich.com', 'maiigdl', 'admin', '$2y$10$pOupNCNTE4zwxKV44wjCVefrcig6Rob1uxza6duH3ncZ99brtLBvW', 'myles', 'orbe', '', '2025-02-02 07:41:16', '2025-02-02 07:41:16', 0, '2025-02-02 07:41:16', '2025-02-02 07:41:16'),
(3, 26, 'jeff@henrich.com', 'jeffgarcia', 'admin', '$2y$10$aU0v8ZKeac4iG5f3u0NVqeuEtg6Rh7cLpt3kjGUuVCICwfa.k6b8a', 'jeff', 'garcia', '', '2025-02-02 07:52:20', '2025-02-02 07:52:20', 0, '2025-02-02 07:52:20', '2025-02-02 07:52:20'),
(4, 24, 'maiigdll@henrich.com', 'maiigdlll', 'supervisor', '$2y$10$Iyt2aWMBZCCKy5JA2INbieTd3xzdChISkwWYbxbqsQqFJ2Zkcsl3S', 'myless', 'maiigdl', '', '2025-02-02 07:34:21', '2025-02-02 07:34:21', 0, '2025-02-02 07:34:21', '2025-02-02 07:34:21'),
(5, 28, 'maiigdlgggg@henrich.com', 'maiigdlllg', 'supervisor', '$2y$10$UcEPrIWcj5obMHyaAe54huLB3qv.Inlyi8J/BO7cBhwwurSYuE0sq', 'myles', 'orbe', '', '2025-02-03 03:45:41', '2025-02-03 03:45:41', 0, '2025-02-03 03:45:41', '2025-02-03 03:45:41'),
(1, 23, 'testorbe@henrich.com', 'testtest', 'supervisor', '$2y$10$izr8nJy3h5wrad9bLPVjt.0785N/g/r/6AZDCbvAqloryS1JLDOTS', 'test', 'tesst', '', '2025-02-02 07:09:03', '2025-02-02 07:09:03', 0, NULL, '2025-02-02 07:09:03'),
(2, 22, 'maiigdlg@henrich.com', 'maiigdl', 'admin', '$2y$10$pOupNCNTE4zwxKV44wjCVefrcig6Rob1uxza6duH3ncZ99brtLBvW', 'myles', 'orbe', '', '2025-02-02 07:41:16', '2025-02-02 07:41:16', 0, '2025-02-02 07:41:16', '2025-02-02 07:41:16'),
(3, 26, 'jeff@henrich.com', 'jeffgarcia', 'admin', '$2y$10$aU0v8ZKeac4iG5f3u0NVqeuEtg6Rh7cLpt3kjGUuVCICwfa.k6b8a', 'jeff', 'garcia', '', '2025-02-02 07:52:20', '2025-02-02 07:52:20', 0, '2025-02-02 07:52:20', '2025-02-02 07:52:20'),
(4, 24, 'maiigdll@henrich.com', 'maiigdlll', 'supervisor', '$2y$10$Iyt2aWMBZCCKy5JA2INbieTd3xzdChISkwWYbxbqsQqFJ2Zkcsl3S', 'myless', 'maiigdl', '', '2025-02-02 07:34:21', '2025-02-02 07:34:21', 0, '2025-02-02 07:34:21', '2025-02-02 07:34:21'),
(5, 28, 'maiigdlgggg@henrich.com', 'maiigdlllg', 'supervisor', '$2y$10$UcEPrIWcj5obMHyaAe54huLB3qv.Inlyi8J/BO7cBhwwurSYuE0sq', 'myles', 'orbe', '', '2025-02-03 03:45:41', '2025-02-03 03:45:41', 0, '2025-02-03 03:45:41', '2025-02-03 03:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_location` varchar(255) NOT NULL,
  `branch_manager` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `branch_name`, `branch_location`, `branch_manager`, `contact_number`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Main Branch', 'San Fernando, Pampanga', 'Admin User', '+639123456789', 'admin@henrichfood.com', 'active', '2025-01-28 15:31:32', '2025-01-28 15:31:32'),
(2, 'Main Branch', 'Default Location', NULL, NULL, NULL, 'active', '2025-02-03 00:02:48', '2025-02-03 00:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `branch_inventory`
--

CREATE TABLE `branch_inventory` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `productcode` char(3) NOT NULL,
  `available_quantity` int(11) DEFAULT 0,
  `reorder_point` int(11) DEFAULT 10,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_notifications`
--

CREATE TABLE `chat_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customeraccount`
--

CREATE TABLE `customeraccount` (
  `accountid` int(11) NOT NULL,
  `customername` varchar(50) NOT NULL,
  `customeraddress` varchar(100) NOT NULL,
  `customerphonenumber` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `useremail` varchar(30) NOT NULL,
  `profilepicture` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `accountstatus` varchar(30) NOT NULL,
  `accounttype` varchar(30) NOT NULL,
  `reset_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customeraccount`
--

INSERT INTO `customeraccount` (`accountid`, `customername`, `customeraddress`, `customerphonenumber`, `customerid`, `username`, `password`, `useremail`, `profilepicture`, `status`, `accountstatus`, `accounttype`, `reset_token`) VALUES
(327956, ' ', 'Block 1 Lot 4 Southfairway Street Canada', 2147483647, 559617, 'generaluser', '$2y$10$MHs9Gv9pACNmw6tNi5RgeOuBBcZ6AxxWyDYf0s74o2ySHrpAbzodC', 'generaluser@gmail.com', '1744917796_daf9dbe9-9f62-4e24-9d6c-6c2902ab2a3c.jpg', 'active', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `customerdetails`
--

CREATE TABLE `customerdetails` (
  `accountid` int(11) NOT NULL,
  `customername` varchar(50) NOT NULL,
  `customeraddress` varchar(100) NOT NULL,
  `customerphonenumber` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `useremail` varchar(30) NOT NULL,
  `customerstatus` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customerorder`
--

CREATE TABLE `customerorder` (
  `orderid` varchar(20) NOT NULL,
  `orderdescription` text DEFAULT NULL,
  `orderdate` date DEFAULT NULL,
  `customername` varchar(100) DEFAULT NULL,
  `customeraddress` text DEFAULT NULL,
  `customerphonenumber` varchar(20) DEFAULT NULL,
  `ordertotal` decimal(10,2) DEFAULT NULL,
  `salesperson` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `timeoforder` time DEFAULT NULL,
  `ordertype` varchar(50) DEFAULT NULL,
  `hid` int(11) NOT NULL,
  `datecompleted` date DEFAULT NULL,
  `datemodified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `customerid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customerorder`
--

INSERT INTO `customerorder` (`orderid`, `orderdescription`, `orderdate`, `customername`, `customeraddress`, `customerphonenumber`, `ordertotal`, `salesperson`, `status`, `timeoforder`, `ordertype`, `hid`, `datecompleted`, `datemodified`, `branch_id`, `customerid`) VALUES
('SO-20250419-0001', 'All Day Bacon (0.25 kg) x 1', '2025-04-19', 'Jeff Mathew D. Garcia', 'ADD TEST ', '0999999999', '140.00', 'Mariaa', 'Pending', '17:12:51', 'Walk-in', 106, NULL, '2025-04-19 09:12:51', 1, NULL),
('SO-20250419-0002', '[{\"productId\":\"013\",\"productcode\":\"013\",\"productname\":\"Bologna Loaf\",\"unit_price\":450,\"quantity\":1}]', '2025-04-19', 'Jeff Mathew Datoon Garcia', 'Southfairway', '0887', '450.00', NULL, 'Pending', '17:13:50', 'Online', 107, NULL, '2025-04-19 09:13:50', 1, NULL);

--
-- Triggers `customerorder`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `customerorder` FOR EACH ROW BEGIN
    -- Update sales performance
    INSERT INTO sales_performance (date, total_sales, orders_count, customer_count)
    VALUES (NEW.orderdate, NEW.ordertotal, 1, 1)
    ON DUPLICATE KEY UPDATE
        total_sales = total_sales + NEW.ordertotal,
        orders_count = orders_count + 1,
        customer_count = customer_count + 1;

    -- Update financial metrics
    INSERT INTO financial_metrics (period_date, total_revenue)
    VALUES (DATE(NEW.orderdate), NEW.ordertotal)
    ON DUPLICATE KEY UPDATE
        total_revenue = total_revenue + NEW.ordertotal;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `department_performance`
--

CREATE TABLE `department_performance` (
  `id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `performance_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `target_achievement` decimal(5,2) NOT NULL DEFAULT 0.00,
  `period_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `hire_date` date NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `status` enum('present','absent','late','leave') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_performance`
--

CREATE TABLE `employee_performance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_date` date NOT NULL,
  `performance_score` decimal(5,2) NOT NULL,
  `attendance_rate` decimal(5,2) NOT NULL,
  `productivity_score` decimal(5,2) NOT NULL,
  `evaluated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `executive_reports`
--

CREATE TABLE `executive_reports` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `generated_by` int(11) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_metrics`
--

CREATE TABLE `financial_metrics` (
  `id` int(11) NOT NULL,
  `period_date` date NOT NULL,
  `total_revenue` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_profit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `operating_costs` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_metrics`
--

INSERT INTO `financial_metrics` (`id`, `period_date`, `total_revenue`, `net_profit`, `operating_costs`, `created_at`) VALUES
(1, '2025-01-30', '3360.00', '0.00', '0.00', '2025-01-30 00:15:17'),
(2, '2025-01-30', '250.00', '0.00', '0.00', '2025-01-30 00:29:34'),
(3, '2025-01-30', '750.00', '0.00', '0.00', '2025-01-30 07:51:25'),
(4, '2025-01-30', '720.00', '0.00', '0.00', '2025-01-30 07:52:15'),
(5, '2025-01-31', '1320.00', '0.00', '0.00', '2025-01-31 09:31:25'),
(6, '2025-02-01', '2100.00', '0.00', '0.00', '2025-02-01 14:11:26'),
(7, '2025-02-01', '1560.00', '0.00', '0.00', '2025-02-01 14:19:51'),
(8, '2025-02-01', '7250.00', '0.00', '0.00', '2025-02-01 14:20:50'),
(9, '2025-02-01', '3760.00', '0.00', '0.00', '2025-02-01 14:23:24'),
(10, '2025-02-01', '150.00', '0.00', '0.00', '2025-02-01 14:27:10'),
(11, '2025-02-02', '0.00', '0.00', '0.00', '2025-02-02 13:27:21'),
(12, '2025-02-03', '0.00', '0.00', '0.00', '2025-02-02 18:46:13'),
(13, '2025-02-03', '0.00', '0.00', '0.00', '2025-02-02 19:50:30'),
(14, '2025-02-03', '29700.00', '0.00', '0.00', '2025-02-02 20:17:54'),
(15, '2025-02-03', '120.00', '0.00', '0.00', '2025-02-02 22:13:55'),
(16, '2025-02-03', '1400.00', '0.00', '0.00', '2025-02-02 22:22:02'),
(17, '2025-02-03', '250.00', '0.00', '0.00', '2025-02-02 22:23:45'),
(18, '2025-02-03', '370.00', '0.00', '0.00', '2025-02-02 22:34:49'),
(19, '2025-02-03', '120.00', '0.00', '0.00', '2025-02-02 22:38:20'),
(20, '2025-02-03', '240.00', '0.00', '0.00', '2025-02-02 22:42:08'),
(21, '2025-02-03', '150.00', '0.00', '0.00', '2025-02-02 22:51:05'),
(35, '2025-02-03', '120.00', '0.00', '0.00', '2025-02-03 02:36:24'),
(36, '2025-02-03', '2750.00', '0.00', '0.00', '2025-02-03 03:45:07'),
(37, '2025-02-03', '240.00', '0.00', '0.00', '2025-02-03 04:55:26'),
(38, '2025-03-30', '120.00', '0.00', '0.00', '2025-03-30 04:34:53'),
(44, '2025-04-17', '1320.00', '0.00', '0.00', '2025-04-17 11:51:47'),
(45, '2025-04-17', '1440.00', '0.00', '0.00', '2025-04-17 11:52:28'),
(46, '2025-04-17', '3120.00', '0.00', '0.00', '2025-04-17 14:04:47'),
(51, '2025-04-18', '120.00', '0.00', '0.00', '2025-04-17 16:56:23'),
(52, '2025-04-18', '250.00', '0.00', '0.00', '2025-04-17 17:04:54'),
(53, '2025-04-18', '120.00', '0.00', '0.00', '2025-04-17 17:06:37'),
(54, '2025-04-18', '500.00', '0.00', '0.00', '2025-04-17 18:39:23'),
(55, '2025-04-18', '370.00', '0.00', '0.00', '2025-04-17 19:14:18'),
(56, '2025-04-18', '130.00', '0.00', '0.00', '2025-04-17 19:51:35'),
(57, '2025-04-18', '620.00', '0.00', '0.00', '2025-04-17 20:02:48'),
(58, '2025-04-18', '960.00', '0.00', '0.00', '2025-04-17 20:13:57'),
(59, '2025-04-18', '140.00', '0.00', '0.00', '2025-04-17 20:35:08'),
(60, '2025-04-18', '2040.00', '0.00', '0.00', '2025-04-18 03:56:16'),
(61, '2025-04-19', '140.00', '0.00', '0.00', '2025-04-19 09:12:51'),
(62, '2025-04-19', '450.00', '0.00', '0.00', '2025-04-19 09:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `productcode` char(3) NOT NULL,
  `productname` varchar(100) NOT NULL,
  `productcategory` varchar(50) NOT NULL,
  `availablequantity` int(11) DEFAULT 0,
  `onhandquantity` int(11) DEFAULT 0,
  `dateupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `productprice` decimal(10,2) DEFAULT 0.00,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `branch_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`productcode`, `productname`, `productcategory`, `availablequantity`, `onhandquantity`, `dateupdated`, `productprice`, `unit_price`, `branch_id`) VALUES
('005', 'Chicken Tocino Boneless', 'Tocino', 48, 50, '2025-04-17 19:14:18', '0.00', '0.00', 1),
('006', 'Chicken Tocino Boneless', 'Tocino', 46, 50, '2025-04-17 19:51:35', '0.00', '0.00', 1),
('007', 'Pork Tocino Classic Sweet', 'Tocino', 66, 75, '2025-04-17 20:13:57', '0.00', '0.00', 1),
('001', 'Pork Tocino Tamis-Alat', 'Tocino', 0, 50, '2025-04-17 17:06:37', '0.00', '0.00', 1),
('002', 'Pork Tocino Tamis-Alat', 'Tocino', 21, 25, '2025-04-18 03:56:16', '0.00', '0.00', 1),
('012', 'Chicken Longanisa', 'Longanisa', 50, 50, '2025-04-17 20:33:45', '0.00', '0.00', 1),
('013', 'Bologna Loaf', 'Ham', 49, 50, '2025-04-19 09:13:50', '0.00', '0.00', 1),
('014', 'Sliced Bologna', 'Ham', 125, 125, '2025-04-17 20:33:45', '0.00', '0.00', 1),
('016', 'Pork Longanisa', 'Longanisa', 25, 25, '2025-04-17 20:33:45', '0.00', '0.00', 1),
('053', 'Belly Bacon', 'Bacon', 14, 25, '2025-04-18 03:56:16', '0.00', '0.00', 1),
('055', 'All Day Bacon', 'Bacon', 47, 50, '2025-04-19 09:12:51', '0.00', '0.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kpi_metrics`
--

CREATE TABLE `kpi_metrics` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(50) NOT NULL,
  `metric_value` decimal(10,2) NOT NULL,
  `target_value` decimal(10,2) NOT NULL,
  `period_date` date NOT NULL,
  `status` enum('exceeded','met','below') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type` enum('sick','vacation','personal','other') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mekeni_orders`
--

CREATE TABLE `mekeni_orders` (
  `order_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `products` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mekeni_order_details`
--

CREATE TABLE `mekeni_order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `productcode` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `is_read`, `created_at`) VALUES
(1, 1, 2, 'Hello! This is a test message', 0, '2025-01-22 08:36:28'),
(2, 3, 2, 'Please review the latest inventory report', 0, '2025-01-22 07:36:28'),
(3, 1, 2, 'Urgent: Stock level update needed', 0, '2025-01-22 06:36:28');

-- --------------------------------------------------------

--
-- Table structure for table `message_threads`
--

CREATE TABLE `message_threads` (
  `thread_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `last_message_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_threads`
--

INSERT INTO `message_threads` (`thread_id`, `user1_id`, `user2_id`, `last_message_id`, `updated_at`) VALUES
(1, 1, 2, 3, '2025-01-22 08:36:28'),
(2, 3, 2, 2, '2025-01-22 07:36:28');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `activity_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`, `activity_id`) VALUES
(1, 1, 'New stock movement added - Batch ID: 1', 0, '2025-01-24 05:50:58', NULL),
(3, 4, 'New stock movement added - Batch ID: 1', 0, '2025-01-24 05:50:58', NULL),
(4, 5, 'New stock movement added - Batch ID: 1', 0, '2025-01-24 05:50:58', NULL),
(5, 6, 'New stock movement added - Batch ID: 1', 0, '2025-01-24 05:50:58', NULL),
(8, 1, 'New stock movement added - Batch ID: 2', 0, '2025-01-24 05:57:15', NULL),
(10, 4, 'New stock movement added - Batch ID: 2', 0, '2025-01-24 05:57:15', NULL),
(11, 5, 'New stock movement added - Batch ID: 2', 0, '2025-01-24 05:57:15', NULL),
(12, 6, 'New stock movement added - Batch ID: 2', 0, '2025-01-24 05:57:15', NULL),
(15, 1, 'New stock movement added - Batch ID: 3', 0, '2025-01-24 05:57:37', NULL),
(17, 4, 'New stock movement added - Batch ID: 3', 0, '2025-01-24 05:57:37', NULL),
(18, 5, 'New stock movement added - Batch ID: 3', 0, '2025-01-24 05:57:37', NULL),
(19, 6, 'New stock movement added - Batch ID: 3', 0, '2025-01-24 05:57:37', NULL),
(22, 1, 'New stock movement added - Batch ID: 4', 0, '2025-01-24 05:59:16', NULL),
(24, 4, 'New stock movement added - Batch ID: 4', 0, '2025-01-24 05:59:16', NULL),
(25, 5, 'New stock movement added - Batch ID: 4', 0, '2025-01-24 05:59:16', NULL),
(26, 6, 'New stock movement added - Batch ID: 4', 0, '2025-01-24 05:59:16', NULL),
(29, 1, 'New stock movement added - Batch ID: 5', 0, '2025-01-24 06:00:51', NULL),
(31, 4, 'New stock movement added - Batch ID: 5', 0, '2025-01-24 06:00:51', NULL),
(32, 5, 'New stock movement added - Batch ID: 5', 0, '2025-01-24 06:00:51', NULL),
(33, 6, 'New stock movement added - Batch ID: 5', 0, '2025-01-24 06:00:51', NULL),
(36, 1, 'New stock movement added - Batch ID: 6', 0, '2025-01-24 06:01:30', NULL),
(38, 4, 'New stock movement added - Batch ID: 6', 0, '2025-01-24 06:01:30', NULL),
(39, 5, 'New stock movement added - Batch ID: 6', 0, '2025-01-24 06:01:30', NULL),
(40, 6, 'New stock movement added - Batch ID: 6', 0, '2025-01-24 06:01:30', NULL),
(43, 1, 'New stock movement added - Batch ID: 7', 0, '2025-01-24 06:05:14', NULL),
(45, 4, 'New stock movement added - Batch ID: 7', 0, '2025-01-24 06:05:14', NULL),
(46, 5, 'New stock movement added - Batch ID: 7', 0, '2025-01-24 06:05:14', NULL),
(47, 6, 'New stock movement added - Batch ID: 7', 0, '2025-01-24 06:05:14', NULL),
(50, 1, 'New stock movement added - Batch ID: 8', 0, '2025-01-24 06:05:35', NULL),
(52, 4, 'New stock movement added - Batch ID: 8', 0, '2025-01-24 06:05:35', NULL),
(53, 5, 'New stock movement added - Batch ID: 8', 0, '2025-01-24 06:05:35', NULL),
(54, 6, 'New stock movement added - Batch ID: 8', 0, '2025-01-24 06:05:35', NULL),
(57, 1, 'New stock movement added - Batch ID: 9', 0, '2025-01-24 06:06:32', NULL),
(59, 4, 'New stock movement added - Batch ID: 9', 0, '2025-01-24 06:06:32', NULL),
(60, 5, 'New stock movement added - Batch ID: 9', 0, '2025-01-24 06:06:32', NULL),
(61, 6, 'New stock movement added - Batch ID: 9', 0, '2025-01-24 06:06:32', NULL),
(64, 1, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:23', NULL),
(66, 4, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:23', NULL),
(67, 5, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:23', NULL),
(68, 6, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:23', NULL),
(71, 1, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:59', NULL),
(73, 4, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:59', NULL),
(74, 5, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:59', NULL),
(75, 6, 'New stock movement added - Batch ID: 10', 0, '2025-01-24 06:11:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orderlog`
--

CREATE TABLE `orderlog` (
  `id` int(11) NOT NULL,
  `orderid` varchar(20) DEFAULT NULL,
  `productcode` varchar(50) DEFAULT NULL,
  `productname` varchar(100) DEFAULT NULL,
  `productweight` decimal(10,2) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `orderdate` date DEFAULT NULL,
  `timeoforder` time DEFAULT NULL,
  `branch_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderlog`
--

INSERT INTO `orderlog` (`id`, `orderid`, `productcode`, `productname`, `productweight`, `unit_price`, `quantity`, `orderdate`, `timeoforder`, `branch_id`) VALUES
(63, 'SO-20250419-0001', '055', 'All Day Bacon', '0.25', '140.00', 1, '2025-04-19', '17:12:51', 1),
(64, 'SO-20250419-0002', '013', 'Bologna Loaf', NULL, '450.00', 1, '2025-04-19', '17:13:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `overtime_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `token_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productid` int(11) NOT NULL,
  `productname` varchar(255) NOT NULL,
  `productcode` varchar(100) NOT NULL,
  `productcategory` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `productimage` varchar(255) DEFAULT 'placeholder-image.png',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productid`, `productname`, `productcode`, `productcategory`, `price`, `productimage`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Frozen Chicken Wings', 'FCHW001', 'Frozen Meat', '299.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23'),
(2, 'Frozen Beef Chunks', 'FBEF001', 'Frozen Meat', '499.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23'),
(3, 'Ice Cream Vanilla', 'ICVAN001', 'Ice Cream', '149.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23'),
(4, 'Frozen Mixed Vegetables', 'FVEG001', 'Frozen Vegetables', '99.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23'),
(5, 'Frozen Shrimp', 'FSHR001', 'Seafood', '399.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23'),
(6, 'Fish Fillet', 'FFIL001', 'Seafood', '249.99', 'placeholder-image.png', NULL, '2025-04-17 17:59:23', '2025-04-17 17:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productcode` char(3) NOT NULL,
  `productname` varchar(100) NOT NULL,
  `productweight` decimal(5,2) NOT NULL,
  `productcategory` varchar(50) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `piecesperbox` int(11) NOT NULL DEFAULT 25,
  `productimage` varchar(100) DEFAULT 'placeholder-image.png',
  `productstatus` varchar(30) DEFAULT 'Active',
  `reorderpoint` int(11) NOT NULL DEFAULT 10
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productcode`, `productname`, `productweight`, `productcategory`, `unit_price`, `piecesperbox`, `productimage`, `productstatus`, `reorderpoint`) VALUES
('001', 'Pork Tocino Tamis-Alat', '0.22', 'Tocino', '120.00', 25, 'tocino.png', 'Active', 10),
('002', 'Pork Tocino Tamis-Alat', '0.45', 'Tocino', '250.00', 25, 'Tocino-Together.png', 'Active', 10),
('003', 'Pork Tocino Fatless', '0.45', 'Tocino', '260.00', 25, 'tocino.png', 'Active', 10),
('004', 'Chicken Tocino Bone-in', '0.45', 'Tocino', '220.00', 25, 'tocino.png', 'Active', 10),
('005', 'Chicken Tocino Boneless', '0.45', 'Tocino', '240.00', 25, 'tocino.png', 'Active', 10),
('006', 'Chicken Tocino Boneless', '0.23', 'Tocino', '130.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('007', 'Pork Tocino Classic Sweet', '0.22', 'Tocino', '120.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('008', 'Pork Tocino Classic Sweet', '0.45', 'Tocino', '250.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('009', 'Tocino Sakto', '0.20', 'Tocino', '100.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('010', 'Skinless Longanisa Original', '0.40', 'Longanisa', '150.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('011', 'Native Longanisa', '0.38', 'Longanisa', '145.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('012', 'Chicken Longanisa', '0.50', 'Longanisa', '160.00', 25, 'Product_Picnic-Cheesedog.png', 'Active', 10),
('013', 'Bologna Loaf', '5.00', 'Ham', '450.00', 25, 'placeholder-image.png', 'Active', 10),
('014', 'Sliced Bologna', '0.23', 'Ham', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('015', 'Sliced Bologna', '0.50', 'Ham', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('016', 'Pork Longanisa', '0.40', 'Longanisa', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('017', 'Pork Longanisa', '0.20', 'Longanisa', '80.00', 25, 'placeholder-image.png', 'Active', 10),
('018', 'Chummy Cheesedog Footlong Regular', '1.00', 'Cheesedog', '80.00', 25, 'placeholder-image.png', 'Active', 10),
('019', 'Chummy Cheesedog Footlong Jumbo', '1.00', 'Cheesedog', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('020', 'Chummy Cheesedog Footlong Jumbo', '0.50', 'Cheesedog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('021', 'Chummy Cheesedog Footlong Regular', '1.00', 'Cheesedog', '80.00', 25, 'placeholder-image.png', 'Active', 10),
('022', 'Chummy Cheesedog Jumbo', '0.50', 'Cheesedog', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('023', 'Smoked Clear Longanisa', '0.25', 'Longanisa', '110.00', 25, 'placeholder-image.png', 'Active', 10),
('024', 'Smoked Skinless Longanisa', '0.22', 'Longanisa', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('025', 'Garlic Longanisa', '0.23', 'Longanisa', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('026', 'Garlic Longanisa', '0.50', 'Longanisa', '180.00', 25, 'placeholder-image.png', 'Active', 10),
('027', 'Mizmo Cheesedog Jumbo', '1.00', 'Cheesedog', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('028', 'Mizmo Cheesedog Regular', '0.25', 'Cheesedog', '60.00', 25, 'placeholder-image.png', 'Active', 10),
('029', 'Mizmo Cheesedog Jumbo', '0.50', 'Cheesedog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('030', 'Zippy Cheesedog Regular VP', '0.25', 'Cheesedog', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('031', 'Zippy Cheesedog Jumbo', '0.25', 'Cheesedog', '55.00', 25, 'placeholder-image.png', 'Active', 10),
('032', 'Picnic Red Hotdog Super Jumbo', '2.50', 'Hotdog', '250.00', 25, 'placeholder-image.png', 'Active', 10),
('033', 'Picnic Red Hotdog Regular', '2.50', 'Hotdog', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('034', 'Picnic Red Hotdog Jumbo', '2.50', 'Hotdog', '200.00', 25, 'placeholder-image.png', 'Active', 10),
('035', 'Picnic Cheesedog Regular', '0.50', 'Cheesedog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('036', 'Picnic Cheesedog Jumbo', '2.50', 'Cheesedog', '180.00', 25, 'placeholder-image.png', 'Active', 10),
('037', 'Picnic Cheesedog Regular VP', '0.22', 'Cheesedog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('038', 'Picnic Cheesedog Regular VP', '0.45', 'Cheesedog', '85.00', 25, 'placeholder-image.png', 'Active', 10),
('039', 'Picnic Cheesedog Jumbo VP', '0.45', 'Cheesedog', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('040', 'Suki Choice Hotdog Mini', '0.25', 'Hotdog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('041', 'Suki Choice Hotdog Mini', '1.00', 'Hotdog', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('042', 'Suki Choice Hotdog Jumbo', '1.00', 'Hotdog', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('043', 'Chicken Hotdog Jumbo', '2.50', 'Hotdog', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('044', 'Chicken Hotdog Jumbo', '0.50', 'Hotdog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('045', 'Chicken Hotdog Super Jumbo', '1.00', 'Hotdog', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('046', 'Chicken Hotdog Regular', '0.25', 'Hotdog', '40.00', 25, 'placeholder-image.png', 'Active', 10),
('047', 'Chicken Hotdog Jumbo', '0.25', 'Hotdog', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('048', 'Picnic Cheesedog Jumbo', '0.50', 'Cheesedog', '60.00', 25, 'placeholder-image.png', 'Active', 10),
('049', 'Chicken Franks Jumbo', '0.25', 'Hotdog', '45.00', 25, 'placeholder-image.png', 'Active', 10),
('050', 'Picnic Red Hotdog Super Jumbo', '1.00', 'Hotdog', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('051', 'Picnic Brown Hotdog Jumbo', '1.00', 'Hotdog', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('052', 'Picnic Brown Hotdog Jumbo', '0.50', 'Hotdog', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('053', 'Belly Bacon', '0.25', 'Bacon', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('054', 'Belly Bacon', '0.50', 'Bacon', '250.00', 25, 'placeholder-image.png', 'Active', 10),
('055', 'All Day Bacon', '0.25', 'Bacon', '140.00', 25, 'placeholder-image.png', 'Active', 10),
('056', 'Sweet Ham Sliced', '0.25', 'Ham', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('057', 'Sweet Ham Sliced', '0.50', 'Ham', '200.00', 25, 'placeholder-image.png', 'Active', 10),
('058', 'Burger Patties', '0.65', 'Burger Patties', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('059', 'Chicken Ham Sliced', '0.25', 'Ham', '110.00', 25, 'placeholder-image.png', 'Active', 10),
('060', 'Cooked Ham Sliced', '0.25', 'Ham', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('061', 'Cooked Ham Sliced', '0.50', 'Ham', '200.00', 25, 'placeholder-image.png', 'Active', 10),
('062', 'Cooked Ham Loaf', '0.25', 'Ham', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('063', 'Cooked Ham Loaf', '0.50', 'Ham', '250.00', 25, 'placeholder-image.png', 'Active', 10),
('064', 'Cooked Ham Loaf', '1.00', 'Ham', '450.00', 25, 'placeholder-image.png', 'Active', 10),
('065', 'Luncheon Meat', '0.25', 'Ham', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('066', 'Belly Bacon VP', '0.20', 'Bacon', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('067', 'Picnic Red Hotdog Giga', '1.00', 'Hotdog', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('068', 'Beef Tapa', '0.22', 'Tapa', '200.00', 25, 'placeholder-image.png', 'Active', 10),
('069', 'Beef Tapa', '0.45', 'Tapa', '350.00', 25, 'placeholder-image.png', 'Active', 10),
('070', 'Pork Tapa', '0.22', 'Tapa', '180.00', 25, 'placeholder-image.png', 'Active', 10),
('071', 'Embotido', '0.25', 'Embotido', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('072', 'Embotido', '0.40', 'Embotido', '140.00', 25, 'placeholder-image.png', 'Active', 10),
('073', 'Suki Choice Embotido', '0.25', 'Embotido', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('074', 'Suki Choice Embotido', '1.00', 'Embotido', '300.00', 25, 'placeholder-image.png', 'Active', 10),
('075', 'Bayani Kikiam Balls', '0.20', 'Kikiam', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('076', 'Kikiam', '0.25', 'Kikiam', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('077', 'Squid Balls', '0.25', 'Seafood', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('078', 'Bayani Kikiam', '0.40', 'Kikiam', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('079', 'Orlians', '0.25', 'Kikiam', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('080', 'Bayani Chicken Balls', '0.20', 'Seafood', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('081', 'Bayani Squid Balls', '0.20', 'Seafood', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('082', 'Siopao', '0.50', 'Siopao', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('083', 'Siopao Jumbo', '1.00', 'Siopao', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('084', 'Bayani Embutido Jumbo', '0.50', 'Embotido', '200.00', 25, 'placeholder-image.png', 'Active', 10),
('085', 'Sisig', '0.25', 'Sisig', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('086', 'Sisig', '0.50', 'Sisig', '220.00', 25, 'placeholder-image.png', 'Active', 10),
('087', 'Bayani Sisig', '1.00', 'Sisig', '380.00', 25, 'placeholder-image.png', 'Active', 10),
('088', 'Chicken Nuggets', '0.20', 'Nuggets', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('089', 'Chicken Nuggets', '0.50', 'Nuggets', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('090', 'Corned Beef', '0.50', 'Corned Beef', '170.00', 25, 'placeholder-image.png', 'Active', 10),
('091', 'Corned Beef Jumbo', '1.00', 'Corned Beef', '330.00', 25, 'placeholder-image.png', 'Active', 10),
('092', 'Bayani Chicken Siomai', '0.25', 'Siomai', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('093', 'Bayani Pork Siomai', '0.50', 'Siomai', '150.00', 25, 'placeholder-image.png', 'Active', 10),
('094', 'Bayani Pork Siomai', '0.20', 'Siomai', '80.00', 25, 'placeholder-image.png', 'Active', 10),
('095', 'Chicken Siomai', '0.25', 'Siomai', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('096', 'Siomai', '0.50', 'Siomai', '180.00', 25, 'placeholder-image.png', 'Active', 10),
('097', 'Bayani Lumpia Shanghai', '0.50', 'Shanghai Rolls', '140.00', 25, 'placeholder-image.png', 'Active', 10),
('098', 'Lumpiang Shanghai', '0.20', 'Shanghai Rolls', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('099', 'Lumpiang Shanghai', '0.50', 'Shanghai Rolls', '160.00', 25, 'placeholder-image.png', 'Active', 10),
('100', 'Siomai', '0.50', 'Siomai', '180.00', 25, 'placeholder-image.png', 'Active', 10),
('101', 'Siomai Sakto', '0.25', 'Siomai', '100.00', 25, 'placeholder-image.png', 'Active', 10),
('102', 'Zippy Cheesedog Regular', '0.25', 'Cheesedog', '50.00', 25, 'placeholder-image.png', 'Active', 10),
('103', 'Zippy Cheesedog Jumbo', '1.00', 'Cheesedog', '90.00', 25, 'placeholder-image.png', 'Active', 10),
('104', 'Bayani Lumpia Shanghai', '0.25', 'Shanghai Rolls', '130.00', 25, 'placeholder-image.png', 'Active', 10),
('105', 'Bayani Lumpia Shanghai', '1.00', 'Shanghai Rolls', '350.00', 25, 'placeholder-image.png', 'Active', 10),
('106', 'Lumpiang Shanghai', '0.50', 'Shanghai Rolls', '140.00', 25, 'placeholder-image.png', 'Active', 10),
('107', 'Pork Longanisa', '0.20', 'Longanisa', '80.00', 25, 'placeholder-image.png', 'Active', 10),
('108', 'Pork Longanisa', '0.50', 'Longanisa', '120.00', 25, 'placeholder-image.png', 'Active', 10),
('109', 'Pork Longanisa', '0.25', 'Longanisa', '100.00', 25, 'placeholder-image.png', 'Active', 10);

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `after_product_insert` AFTER INSERT ON `products` FOR EACH ROW BEGIN
    -- Awtomatikong mag-insert ng kaukulang row sa inventory na may 0 stock
    -- Gumamit ng INSERT IGNORE para sakaling may race condition o manu-manong entry na nakalikha na nito
    INSERT IGNORE INTO inventory (productcode, productname, productcategory, availablequantity, onhandquantity) 
    VALUES (NEW.productcode, NEW.productname, NEW.productcategory, 0, 0);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires`, `created_at`) VALUES
(1, 2, 'e28289968a35240701737ce7742d7148ca363f671831c77aae1b21317c4511bb', '2025-05-17 18:39:30', '2025-04-17 10:39:30'),
(2, 2, '6fcfe8b7f7455dd45525d1aba4cd588dfe4aacb3a17b6dd276ebde3100c8dbcc', '2025-05-17 18:43:33', '2025-04-17 10:43:33'),
(3, 2, 'aefdbffa74ff90ffe6b60fcad7e8dd9cd210bb9ef614f14ec0b585c97201b763', '2025-05-17 18:46:09', '2025-04-17 10:46:09'),
(4, 2, '3d761e3f0fdadb65362b7fb83e3dab590a09b72edd69b2ba631581e421601304', '2025-05-17 18:47:29', '2025-04-17 10:47:29'),
(5, 2, '5c9b130229fdae08e893cad96b5e6b62763147d3762649c2eeae45605a4d2eba', '2025-05-17 18:48:44', '2025-04-17 10:48:44'),
(6, 2, 'e67cb8c5b475181ff8a938e4cbd8f0f5e589d3d31498bec7b3aa6079dbf101b3', '2025-05-17 18:53:23', '2025-04-17 10:53:23'),
(8, 4, 'baae01d1dd16e636ee315093ba62d9e540a1c9dd28d51479014b301bf3e6d837', '2025-05-17 18:59:47', '2025-04-17 10:59:47'),
(9, 4, '744f423aa7e54e1a25ae3e291f7d5ce979e3bfe1ca32233809ddcff6fff64975', '2025-05-17 19:00:45', '2025-04-17 11:00:45'),
(10, 4, '69a048c5ca653facd3bf9576e19b280c772aacb86ce0941c8c68e282a21d06e8', '2025-05-17 19:01:25', '2025-04-17 11:01:25'),
(11, 4, 'e703166fabe2c920acbfba2ba9d2c186fe155437b259eefb06daa6cdeaf63b24', '2025-05-17 19:05:18', '2025-04-17 11:05:18'),
(12, 4, '24d6f8820d350516436f810c9588482e69b84609d121e01dd53ea28223454e0f', '2025-05-17 19:05:47', '2025-04-17 11:05:47'),
(13, 4, '6667390e33884feedfa1a95caac8fb50f8d4935a9d684da8ebbd5fbf97bac449', '2025-05-17 19:06:51', '2025-04-17 11:06:51'),
(14, 4, '3831210227ee770faeddc04ecee97f4eca628e3f918d2ce9c41d4479ed71a7a2', '2025-05-17 19:10:27', '2025-04-17 11:10:27'),
(15, 4, 'd54c5e2c2bc68b63a35ff1bd300f59aa3ac7e6f492c4dbe7d35e6a0647b55bd9', '2025-05-17 19:10:50', '2025-04-17 11:10:50'),
(25, 2, 'f3deef0f19abd534401fcf565aa21220e02e34e8e4fad00513d1b2215b24d40e', '2025-05-17 19:34:15', '2025-04-17 11:34:15');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('leave','overtime','schedule_change','account','password','other') NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `description` text NOT NULL,
  `details` text DEFAULT NULL,
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `hours` decimal(5,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `current_schedule` varchar(100) DEFAULT NULL,
  `requested_schedule` varchar(100) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `user_id`, `request_type`, `status`, `description`, `details`, `leave_type`, `start_date`, `end_date`, `hours`, `date`, `current_schedule`, `requested_schedule`, `reason`, `created_at`, `updated_at`) VALUES
(1, 4, 'account', 'pending', 'Request for account information update', 'User needs to update their contact information', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-30 06:00:32', '2025-03-30 06:00:32');

-- --------------------------------------------------------

--
-- Table structure for table `sales_performance`
--

CREATE TABLE `sales_performance` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `orders_count` int(11) NOT NULL DEFAULT 0,
  `average_order_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `customer_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_performance`
--

INSERT INTO `sales_performance` (`id`, `date`, `total_sales`, `orders_count`, `average_order_value`, `customer_count`, `created_at`) VALUES
(1, '2025-01-30', '3360.00', 1, '0.00', 1, '2025-01-30 00:15:17'),
(2, '2025-01-30', '250.00', 1, '0.00', 1, '2025-01-30 00:29:34'),
(3, '2025-01-30', '750.00', 1, '0.00', 1, '2025-01-30 07:51:25'),
(4, '2025-01-30', '720.00', 1, '0.00', 1, '2025-01-30 07:52:15'),
(5, '2025-01-31', '1320.00', 1, '0.00', 1, '2025-01-31 09:31:25'),
(6, '2025-02-01', '2100.00', 1, '0.00', 1, '2025-02-01 14:11:26'),
(7, '2025-02-01', '1560.00', 1, '0.00', 1, '2025-02-01 14:19:51'),
(8, '2025-02-01', '7250.00', 1, '0.00', 1, '2025-02-01 14:20:50'),
(9, '2025-02-01', '3760.00', 1, '0.00', 1, '2025-02-01 14:23:24'),
(10, '2025-02-01', '150.00', 1, '0.00', 1, '2025-02-01 14:27:10'),
(11, '2025-02-02', '0.00', 1, '0.00', 1, '2025-02-02 13:27:21'),
(12, '2025-02-03', '0.00', 1, '0.00', 1, '2025-02-02 18:46:13'),
(13, '2025-02-03', '0.00', 1, '0.00', 1, '2025-02-02 19:50:30'),
(14, '2025-02-03', '29700.00', 1, '0.00', 1, '2025-02-02 20:17:54'),
(15, '2025-02-03', '120.00', 1, '0.00', 1, '2025-02-02 22:13:55'),
(16, '2025-02-03', '1400.00', 1, '0.00', 1, '2025-02-02 22:22:02'),
(17, '2025-02-03', '250.00', 1, '0.00', 1, '2025-02-02 22:23:45'),
(18, '2025-02-03', '370.00', 1, '0.00', 1, '2025-02-02 22:34:49'),
(19, '2025-02-03', '120.00', 1, '0.00', 1, '2025-02-02 22:38:20'),
(20, '2025-02-03', '240.00', 1, '0.00', 1, '2025-02-02 22:42:08'),
(21, '2025-02-03', '150.00', 1, '0.00', 1, '2025-02-02 22:51:05'),
(35, '2025-02-03', '120.00', 1, '0.00', 1, '2025-02-03 02:36:24'),
(36, '2025-02-03', '2750.00', 1, '0.00', 1, '2025-02-03 03:45:07'),
(37, '2025-02-03', '240.00', 1, '0.00', 1, '2025-02-03 04:55:26'),
(38, '2025-03-30', '120.00', 1, '0.00', 1, '2025-03-30 04:34:53'),
(44, '2025-04-17', '1320.00', 1, '0.00', 1, '2025-04-17 11:51:47'),
(45, '2025-04-17', '1440.00', 1, '0.00', 1, '2025-04-17 11:52:28'),
(46, '2025-04-17', '3120.00', 1, '0.00', 1, '2025-04-17 14:04:47'),
(51, '2025-04-18', '120.00', 1, '0.00', 1, '2025-04-17 16:56:23'),
(52, '2025-04-18', '250.00', 1, '0.00', 1, '2025-04-17 17:04:54'),
(53, '2025-04-18', '120.00', 1, '0.00', 1, '2025-04-17 17:06:37'),
(54, '2025-04-18', '500.00', 1, '0.00', 1, '2025-04-17 18:39:23'),
(55, '2025-04-18', '370.00', 1, '0.00', 1, '2025-04-17 19:14:18'),
(56, '2025-04-18', '130.00', 1, '0.00', 1, '2025-04-17 19:51:35'),
(57, '2025-04-18', '620.00', 1, '0.00', 1, '2025-04-17 20:02:48'),
(58, '2025-04-18', '960.00', 1, '0.00', 1, '2025-04-17 20:13:57'),
(59, '2025-04-18', '140.00', 1, '0.00', 1, '2025-04-17 20:35:08'),
(60, '2025-04-18', '2040.00', 1, '0.00', 1, '2025-04-18 03:56:16'),
(61, '2025-04-19', '140.00', 1, '0.00', 1, '2025-04-19 09:12:51'),
(62, '2025-04-19', '450.00', 1, '0.00', 1, '2025-04-19 09:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_requests`
--

CREATE TABLE `schedule_requests` (
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_schedule` varchar(100) NOT NULL,
  `requested_schedule` varchar(100) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stockactivitylog`
--

CREATE TABLE `stockactivitylog` (
  `logid` int(11) NOT NULL,
  `batchid` varchar(50) DEFAULT NULL,
  `dateofarrival` date DEFAULT NULL,
  `dateencoded` datetime DEFAULT current_timestamp(),
  `encoder` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `totalNumberOfBoxes` int(11) DEFAULT 0,
  `overalltotalweight` decimal(10,2) DEFAULT 0.00,
  `activity_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockactivitylog`
--

INSERT INTO `stockactivitylog` (`logid`, `batchid`, `dateofarrival`, `dateencoded`, `encoder`, `description`, `totalNumberOfBoxes`, `overalltotalweight`, `activity_type`) VALUES
(1, 'Array', '2025-04-17', '2025-04-17 00:00:00', 'supervisor', 'Stock received for batch Array', 6, '39.25', 'IN'),
(2, 'Array', '2025-04-17', '2025-04-17 00:00:00', 'supervisor', 'Stock received for batch Array', 1, '11.25', 'IN'),
(3, 'Array', '2025-04-17', '2025-04-17 00:00:00', 'supervisor', 'Stock received for batch Array', 2, '16.75', 'IN'),
(4, 'Array', '2025-04-17', '2025-04-17 00:00:00', 'supervisor', 'Stock received for batch Array', 1, '5.50', 'IN'),
(5, 'Array', '2025-04-18', '2025-04-18 00:00:00', 'supervisor', 'Stock received for batch Array', 10, '313.75', 'IN'),
(6, 'Array', '2025-04-18', '2025-04-18 00:00:00', 'supervisor', 'Stock received for batch Array', 3, '18.75', 'IN');

-- --------------------------------------------------------

--
-- Table structure for table `stockmovement`
--

CREATE TABLE `stockmovement` (
  `ibdid` int(11) NOT NULL,
  `batchid` varchar(50) NOT NULL,
  `productcode` varchar(50) NOT NULL,
  `productname` varchar(100) NOT NULL,
  `numberofbox` int(11) NOT NULL DEFAULT 0,
  `totalpacks` int(11) NOT NULL DEFAULT 0,
  `totalweight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dateencoded` datetime DEFAULT current_timestamp(),
  `encoder` varchar(50) DEFAULT NULL,
  `movement_type` enum('IN','OUT','ADJUSTMENT') DEFAULT 'IN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockmovement`
--

INSERT INTO `stockmovement` (`ibdid`, `batchid`, `productcode`, `productname`, `numberofbox`, `totalpacks`, `totalweight`, `dateencoded`, `encoder`, `movement_type`) VALUES
(1, 'Array', '005', 'Chicken Tocino Boneless', 1, 25, '11.25', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(2, 'Array', '006', 'Chicken Tocino Boneless', 2, 50, '11.50', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(3, 'Array', '007', 'Pork Tocino Classic Sweet', 3, 75, '16.50', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(4, 'Array', '005', 'Chicken Tocino Boneless', 1, 25, '11.25', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(5, 'Array', '001', 'Pork Tocino Tamis-Alat', 1, 25, '5.50', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(6, 'Array', '002', 'Pork Tocino Tamis-Alat', 1, 25, '11.25', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(7, 'Array', '001', 'Pork Tocino Tamis-Alat', 1, 25, '5.50', '2025-04-17 00:00:00', 'supervisor', 'IN'),
(8, 'Array', '012', 'Chicken Longanisa', 2, 50, '25.00', '2025-04-18 00:00:00', 'supervisor', 'IN'),
(9, 'Array', '013', 'Bologna Loaf', 2, 50, '250.00', '2025-04-18 00:00:00', 'supervisor', 'IN'),
(10, 'Array', '014', 'Sliced Bologna', 5, 125, '28.75', '2025-04-18 00:00:00', 'supervisor', 'IN'),
(11, 'Array', '016', 'Pork Longanisa', 1, 25, '10.00', '2025-04-18 00:00:00', 'supervisor', 'IN'),
(12, 'Array', '053', 'Belly Bacon', 1, 25, '6.25', '2025-04-18 00:00:00', 'supervisor', 'IN'),
(13, 'Array', '055', 'All Day Bacon', 2, 50, '12.50', '2025-04-18 00:00:00', 'supervisor', 'IN');

--
-- Triggers `stockmovement`
--
DELIMITER $$
CREATE TRIGGER `after_stockmovement_insert` AFTER INSERT ON `stockmovement` FOR EACH ROW BEGIN
    -- First check if inventory record exists
    DECLARE inv_exists INT;
    DECLARE product_cat VARCHAR(50);
    
    -- Try to get product category from products table
    SELECT productcategory INTO product_cat
    FROM products 
    WHERE productcode = NEW.productcode
    LIMIT 1;
    
    -- If not found, use a default category
    IF product_cat IS NULL THEN
        SET product_cat = 'DEFAULT';
    END IF;
    
    SELECT COUNT(*) INTO inv_exists 
    FROM inventory 
    WHERE productcode = NEW.productcode;
    
    IF inv_exists = 0 THEN
        -- Create inventory record if it doesn't exist
        INSERT INTO inventory 
            (productcode, productname, productcategory, availablequantity, onhandquantity, dateupdated)
        VALUES
            (NEW.productcode, 
            NEW.productname, 
            product_cat, 
            0, 0, 
            CURRENT_TIMESTAMP);
    END IF;
    
    -- Update inventory quantities
    IF NEW.movement_type = 'IN' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity + NEW.totalpacks,
            onhandquantity = onhandquantity + NEW.totalpacks,
            productname = NEW.productname,  -- Update the product name if it's blank
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;
    ELSEIF NEW.movement_type = 'OUT' THEN
        UPDATE inventory 
        SET availablequantity = availablequantity - NEW.totalpacks,
            onhandquantity = onhandquantity - NEW.totalpacks,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = NEW.productcode;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_stockmovement_insert` BEFORE INSERT ON `stockmovement` FOR EACH ROW BEGIN
    DECLARE current_stock INT;
    
    IF NEW.movement_type = 'OUT' THEN
        SELECT availablequantity INTO current_stock
        FROM inventory
        WHERE productcode = NEW.productcode;
        
        IF current_stock < NEW.totalpacks THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient stock available';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_description`, `updated_at`) VALUES
(1, 'site_name', 'HFC Management System', 'The name of the system', '2025-03-30 06:27:35'),
(2, 'site_email', 'admin@hfc.com', 'System email address', '2025-03-30 06:27:35'),
(3, 'max_leave_days', '15', 'Maximum number of leave days per year', '2025-03-30 06:27:35'),
(4, 'max_overtime_hours', '40', 'Maximum overtime hours per month', '2025-03-30 06:27:35'),
(5, 'notification_email', 'notifications@hfc.com', 'Email address for system notifications', '2025-03-30 06:27:35'),
(6, 'maintenance_mode', '0', 'System maintenance mode (0: Off, 1: On)', '2025-03-30 06:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `useremail` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('admin','supervisor','ceo') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_online` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `useremail`, `username`, `role`, `password`, `created_at`, `updated_at`, `first_name`, `last_name`, `department`, `status`, `last_online`, `is_online`) VALUES
(1, 'jeffmathewg@henrich.com', 'Jeff Admin', 'admin', 'jeffadmin', '2025-01-19 19:09:40', '2025-02-03 05:36:46', 'aaaa', 'bbbb', NULL, 'active', '2025-02-03 05:36:46', 1),
(2, 'henrichsupervisor@henrich.com', 'Mariaa', 'supervisor', 'supervisor-san123', '2025-01-19 19:09:40', '2025-03-29 17:12:07', 'ccc', 'ddd', 'Sales', 'active', '2025-03-29 17:12:07', 0),
(3, 'jeff@henrich.com', 'jeff', 'ceo', 'jeffceo', '2025-01-19 19:09:40', '2025-02-03 05:36:40', NULL, NULL, NULL, 'active', '2025-02-03 05:36:40', 0),
(4, 'admin@henrich.com', 'admin', 'admin', 'admin123', '2025-01-19 19:10:04', '2025-01-19 19:44:29', NULL, NULL, NULL, 'active', '2025-01-28 16:49:51', 0),
(5, 'admin@example.com', 'admin', 'admin', 'admin123', '2025-01-19 19:33:53', '2025-01-19 19:33:53', NULL, NULL, NULL, 'active', '2025-01-28 16:49:51', 0),
(6, 'luwibitoy@henrich.com', 'luwi bitoy', 'supervisor', '$2y$10$mJpPOS41o/GKh2WIqLvDHugCs9ascK2F1zMZHxCI38yL7E2AkXCU6', '2025-01-19 20:22:44', '2025-01-19 20:22:44', 'Mike', 'Orbe', 'IT', 'active', '2025-01-28 16:49:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `useremail` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','supervisor','ceo','employee','customer') NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `is_online` tinyint(1) DEFAULT 0,
  `last_online` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `last_failed_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_password_change` datetime DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `useremail`, `password`, `first_name`, `last_name`, `role`, `department`, `status`, `is_online`, `last_online`, `failed_login_attempts`, `last_failed_login`, `created_at`, `updated_at`, `last_password_change`, `password_reset_token`, `password_reset_expires`) VALUES
(2, 'Mariaa', 'henrichceo@henrich.com', '$2y$10$QI5DiMURbgpy6ah3u3qEreinNo4D03/Hh/sVPKQTt9JpjdTRYU2fy', 'Maria', 'aa', 'ceo', 'Sales', 'active', 1, '2025-04-20 17:28:21', 0, NULL, '2025-01-19 11:09:40', '2025-04-20 09:28:21', NULL, NULL, NULL),
(3, 'jeff', 'jeff@henrich.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'ceo', NULL, 'active', 0, '2025-02-03 05:36:40', 6, '2025-04-18 18:29:46', '2025-01-19 11:09:40', '2025-04-18 10:29:46', NULL, NULL, NULL),
(4, 'admin', 'admin@henrich.com', '$2y$10$EF26DBPVvLIv5u63xGmogutSPpt6eJVZgV5RV9yl2vIfwPajonzJG', '', '', 'admin', NULL, 'active', 1, '2025-04-18 06:10:32', 3, '2025-04-20 16:41:03', '2025-01-19 11:10:04', '2025-04-20 08:41:03', NULL, NULL, NULL),
(6, 'luwi bitoy', 'luwibitoy@henrich.com', '$2y$10$mJpPOS41o/GKh2WIqLvDHugCs9ascK2F1zMZHxCI38yL7E2AkXCU6', 'Mike', 'Orbe', 'supervisor', 'IT', 'active', 0, '2025-01-28 16:49:51', 0, NULL, '2025-01-19 12:22:44', '2025-01-19 12:22:44', NULL, NULL, NULL),
(9, 'testinglang83', 'testinglang@henrich.com', '$2y$10$NR.CnBCS0LlZdom3xpnVBuX3MfHTVNoTqE5eSYPJRcpUHMszujv/a', 'test', 'order', 'supervisor', 'Administration', 'active', 0, NULL, 0, NULL, '2025-04-17 05:12:32', '2025-04-17 05:12:32', NULL, NULL, NULL),
(10, 'shinshin0464', 'shinshinu04@gmail.com', '$2y$10$2D/PSF.LyziBeKxUakL3lOJz5AbexoyNX9J3mKxWBaOYkB33wBSVC', 'Shin', 'da', 'supervisor', 'Warehouse', 'active', 1, '2025-04-17 18:02:06', 5, '2025-04-17 18:09:58', '2025-04-17 09:59:23', '2025-04-17 10:21:40', NULL, 'a20f8ea22b562e79d8b65d638e90df7e72570e34a1cc0213fe8df919395bf569', '2025-04-17 19:17:10'),
(11, 'sda', 'shinshinu04@henrich.com', '$2y$10$pH5VgE9cVQ1jb.wx4bny3.M/sl.C4tsQgLVfpSYxZf33NY3ycPXhe', 'Shin', 'da', 'admin', 'Administration', 'active', 0, NULL, 0, NULL, '2025-04-17 12:39:56', '2025-04-17 12:39:56', NULL, NULL, NULL),
(12, 'sna', 'sanaceo@henrich.com', '$2y$10$I8lzj.LcvnCcqTgRHFss7uOmj/zfh6.Lb75RarWV10agM6/ICg9Hm', 'sa', 'na', 'ceo', 'Administration', 'active', 0, NULL, 1, '2025-04-18 06:12:58', '2025-04-17 22:12:19', '2025-04-17 22:13:23', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_history`
--

CREATE TABLE `user_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_branch_performance`
-- (See below for the actual view)
--
CREATE TABLE `vw_branch_performance` (
`branch_id` int(11)
,`branch_name` varchar(100)
,`total_orders` bigint(21)
,`total_revenue` decimal(32,2)
,`avg_order_value` decimal(14,6)
,`unique_customers` bigint(21)
,`branch_status` enum('active','inactive')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_inventory_value`
-- (See below for the actual view)
--
CREATE TABLE `vw_inventory_value` (
`productcategory` varchar(50)
,`inventory_value` decimal(42,2)
,`product_count` bigint(21)
,`total_quantity` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_monthly_performance`
-- (See below for the actual view)
--
CREATE TABLE `vw_monthly_performance` (
`period` varchar(7)
,`total_orders` bigint(21)
,`total_revenue` decimal(42,2)
,`unique_customers` bigint(21)
,`avg_order_value` decimal(24,6)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_product_performance`
-- (See below for the actual view)
--
CREATE TABLE `vw_product_performance` (
`productcode` char(3)
,`productname` varchar(100)
,`category` varchar(50)
,`total_sold` decimal(32,0)
,`total_revenue` decimal(42,2)
,`order_count` bigint(21)
,`current_stock` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_sales_by_period`
-- (See below for the actual view)
--
CREATE TABLE `vw_sales_by_period` (
`period` varchar(7)
,`total_sales` decimal(42,2)
,`order_count` bigint(21)
,`total_quantity` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_branch_performance`
--
DROP TABLE IF EXISTS `vw_branch_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_branch_performance`  AS SELECT `b`.`branch_id` AS `branch_id`, `b`.`branch_name` AS `branch_name`, count(`co`.`orderid`) AS `total_orders`, sum(`co`.`ordertotal`) AS `total_revenue`, avg(`co`.`ordertotal`) AS `avg_order_value`, count(distinct `co`.`customername`) AS `unique_customers`, `b`.`status` AS `branch_status` FROM (`branches` `b` left join `customerorder` `co` on(`b`.`branch_id` = `co`.`branch_id`)) WHERE `co`.`orderdate` >= curdate() - interval 30 day OR `co`.`orderdate` is null GROUP BY `b`.`branch_id``branch_id`  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_inventory_value`
--
DROP TABLE IF EXISTS `vw_inventory_value`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_inventory_value`  AS SELECT `i`.`productcategory` AS `productcategory`, sum(`i`.`availablequantity` * `i`.`unit_price`) AS `inventory_value`, count(distinct `i`.`productcode`) AS `product_count`, sum(`i`.`availablequantity`) AS `total_quantity` FROM `inventory` AS `i` GROUP BY `i`.`productcategory``productcategory`  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_monthly_performance`
--
DROP TABLE IF EXISTS `vw_monthly_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_monthly_performance`  AS SELECT date_format(`co`.`orderdate`,'%Y-%m') AS `period`, count(distinct `co`.`orderid`) AS `total_orders`, sum(`ol`.`quantity` * `ol`.`unit_price`) AS `total_revenue`, count(distinct `co`.`customername`) AS `unique_customers`, avg(`ol`.`quantity` * `ol`.`unit_price`) AS `avg_order_value` FROM (`customerorder` `co` join `orderlog` `ol` on(`co`.`orderid` = `ol`.`orderid`)) GROUP BY date_format(`co`.`orderdate`,'%Y-%m')  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_product_performance`
--
DROP TABLE IF EXISTS `vw_product_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_product_performance`  AS SELECT `p`.`productcode` AS `productcode`, `p`.`productname` AS `productname`, `p`.`productcategory` AS `category`, sum(`ol`.`quantity`) AS `total_sold`, sum(`ol`.`quantity` * `ol`.`unit_price`) AS `total_revenue`, count(distinct `ol`.`orderid`) AS `order_count`, `i`.`availablequantity` AS `current_stock` FROM ((`products` `p` left join `orderlog` `ol` on(`p`.`productcode` = `ol`.`productcode`)) left join `inventory` `i` on(`p`.`productcode` = `i`.`productcode`)) GROUP BY `p`.`productcode`, `p`.`productname`, `p`.`productcategory`, `i`.`availablequantity` ORDER BY sum(`ol`.`quantity`) AS `DESCdesc` ASC  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_sales_by_period`
--
DROP TABLE IF EXISTS `vw_sales_by_period`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_sales_by_period`  AS SELECT date_format(`co`.`orderdate`,'%Y-%m') AS `period`, sum(`ol`.`quantity` * `ol`.`unit_price`) AS `total_sales`, count(distinct `co`.`orderid`) AS `order_count`, sum(`ol`.`quantity`) AS `total_quantity` FROM (`customerorder` `co` join `orderlog` `ol` on(`co`.`orderid` = `ol`.`orderid`)) GROUP BY date_format(`co`.`orderdate`,'%Y-%m') ORDER BY date_format(`co`.`orderdate`,'%Y-%m') AS `DESCdesc` ASC  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_requests`
--
ALTER TABLE `account_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `unique_email_pending` (`email`,`status`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`),
  ADD KEY `idx_branch_status` (`status`),
  ADD KEY `idx_branch_name` (`branch_name`);

--
-- Indexes for table `branch_inventory`
--
ALTER TABLE `branch_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_branch_product` (`branch_id`,`productcode`),
  ADD KEY `fk_branch_inventory_product` (`productcode`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_chat_users` (`sender_id`,`receiver_id`),
  ADD KEY `idx_chat_timestamp` (`created_at`);

--
-- Indexes for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `customeraccount`
--
ALTER TABLE `customeraccount`
  ADD PRIMARY KEY (`accountid`),
  ADD KEY `idx_customerid` (`customerid`);

--
-- Indexes for table `customerdetails`
--
ALTER TABLE `customerdetails`
  ADD PRIMARY KEY (`accountid`),
  ADD KEY `fk_customerdetails_account` (`customerid`);

--
-- Indexes for table `customerorder`
--
ALTER TABLE `customerorder`
  ADD PRIMARY KEY (`orderid`),
  ADD UNIQUE KEY `hid` (`hid`),
  ADD KEY `idx_order_date_total` (`orderdate`,`ordertotal`),
  ADD KEY `fk_customerorder_customer` (`customerid`),
  ADD KEY `fk_customerorder_branch` (`branch_id`),
  ADD KEY `idx_customer_order_date` (`orderdate`);

--
-- Indexes for table `department_performance`
--
ALTER TABLE `department_performance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dept_date` (`department`,`period_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `idx_emp_branch` (`branch_id`),
  ADD KEY `idx_emp_status` (`status`);

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_attendance` (`employee_id`,`date`);

--
-- Indexes for table `employee_performance`
--
ALTER TABLE `employee_performance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluated_by` (`evaluated_by`),
  ADD KEY `idx_emp_eval` (`employee_id`,`evaluation_date`);

--
-- Indexes for table `executive_reports`
--
ALTER TABLE `executive_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `idx_report_type` (`report_type`,`generated_at`);

--
-- Indexes for table `financial_metrics`
--
ALTER TABLE `financial_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_financial_period` (`period_date`,`total_revenue`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD KEY `fk_inventory_product` (`productcode`),
  ADD KEY `idx_inventory_stock` (`availablequantity`);

--
-- Indexes for table `kpi_metrics`
--
ALTER TABLE `kpi_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_metric_date` (`metric_name`,`period_date`),
  ADD KEY `idx_kpi_tracking` (`metric_name`,`period_date`,`status`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `mekeni_orders`
--
ALTER TABLE `mekeni_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `mekeni_order_details`
--
ALTER TABLE `mekeni_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `message_threads`
--
ALTER TABLE `message_threads`
  ADD PRIMARY KEY (`thread_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`),
  ADD KEY `last_message_id` (`last_message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orderlog`
--
ALTER TABLE `orderlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_metrics` (`productcode`,`quantity`,`unit_price`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `fk_orderlog_order` (`orderid`),
  ADD KEY `idx_order_log_date` (`orderdate`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`overtime_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `idx_token` (`token`),
  ADD KEY `idx_expiry` (`expiry`),
  ADD KEY `idx_user_token` (`uid`,`token`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productid`),
  ADD UNIQUE KEY `productcode` (`productcode`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productcode`),
  ADD KEY `idx_product_category` (`productcategory`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires` (`expires`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sales_performance`
--
ALTER TABLE `sales_performance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_sales_metrics` (`date`,`total_sales`,`orders_count`);

--
-- Indexes for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `stockactivitylog`
--
ALTER TABLE `stockactivitylog`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `idx_date` (`dateencoded`),
  ADD KEY `idx_batch` (`batchid`);

--
-- Indexes for table `stockmovement`
--
ALTER TABLE `stockmovement`
  ADD PRIMARY KEY (`ibdid`),
  ADD KEY `idx_batchid` (`batchid`),
  ADD KEY `idx_date` (`dateencoded`),
  ADD KEY `idx_movement_analysis` (`productcode`,`movement_type`,`totalpacks`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_user_online` (`is_online`,`last_online`),
  ADD KEY `idx_user_role` (`role`),
  ADD KEY `idx_user_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `useremail` (`useremail`),
  ADD KEY `idx_email` (`useremail`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_role` (`role`),
  ADD KEY `idx_user_status` (`status`);

--
-- Indexes for table `user_history`
--
ALTER TABLE `user_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `modified_by` (`modified_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_requests`
--
ALTER TABLE `account_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branch_inventory`
--
ALTER TABLE `branch_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customerorder`
--
ALTER TABLE `customerorder`
  MODIFY `hid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `department_performance`
--
ALTER TABLE `department_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_performance`
--
ALTER TABLE `employee_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `executive_reports`
--
ALTER TABLE `executive_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_metrics`
--
ALTER TABLE `financial_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `kpi_metrics`
--
ALTER TABLE `kpi_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mekeni_orders`
--
ALTER TABLE `mekeni_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mekeni_order_details`
--
ALTER TABLE `mekeni_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `message_threads`
--
ALTER TABLE `message_threads`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `orderlog`
--
ALTER TABLE `orderlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `overtime_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_performance`
--
ALTER TABLE `sales_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stockactivitylog`
--
ALTER TABLE `stockactivitylog`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stockmovement`
--
ALTER TABLE `stockmovement`
  MODIFY `ibdid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_history`
--
ALTER TABLE `user_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_requests`
--
ALTER TABLE `account_requests`
  ADD CONSTRAINT `account_requests_ibfk_1` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_inventory`
--
ALTER TABLE `branch_inventory`
  ADD CONSTRAINT `branch_inventory_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `branch_inventory_ibfk_2` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`),
  ADD CONSTRAINT `fk_branch_inventory_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_branch_inventory_product` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  ADD CONSTRAINT `chat_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `chat_notifications_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`message_id`);

--
-- Constraints for table `customerdetails`
--
ALTER TABLE `customerdetails`
  ADD CONSTRAINT `fk_customerdetails_account` FOREIGN KEY (`customerid`) REFERENCES `customeraccount` (`customerid`) ON DELETE CASCADE;

--
-- Constraints for table `customerorder`
--
ALTER TABLE `customerorder`
  ADD CONSTRAINT `customerorder_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  ADD CONSTRAINT `fk_customerorder_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_customerorder_customer` FOREIGN KEY (`customerid`) REFERENCES `customeraccount` (`customerid`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `employee_performance`
--
ALTER TABLE `employee_performance`
  ADD CONSTRAINT `employee_performance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `employee_performance_ibfk_2` FOREIGN KEY (`evaluated_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `executive_reports`
--
ALTER TABLE `executive_reports`
  ADD CONSTRAINT `executive_reports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `mekeni_order_details`
--
ALTER TABLE `mekeni_order_details`
  ADD CONSTRAINT `mekeni_order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `mekeni_orders` (`order_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `message_threads`
--
ALTER TABLE `message_threads`
  ADD CONSTRAINT `message_threads_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `message_threads_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `message_threads_ibfk_3` FOREIGN KEY (`last_message_id`) REFERENCES `messages` (`message_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activity_log` (`log_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `orderlog`
--
ALTER TABLE `orderlog`
  ADD CONSTRAINT `fk_orderlog_order` FOREIGN KEY (`orderid`) REFERENCES `customerorder` (`orderid`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orderlog_product` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`),
  ADD CONSTRAINT `orderlog_ibfk_1` FOREIGN KEY (`orderid`) REFERENCES `customerorder` (`orderid`),
  ADD CONSTRAINT `orderlog_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`);

--
-- Constraints for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  ADD CONSTRAINT `schedule_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `stockmovement`
--
ALTER TABLE `stockmovement`
  ADD CONSTRAINT `fk_stockmovement_product` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`),
  ADD CONSTRAINT `stockmovement_ibfk_1` FOREIGN KEY (`productcode`) REFERENCES `products` (`productcode`);

--
-- Constraints for table `user_history`
--
ALTER TABLE `user_history`
  ADD CONSTRAINT `user_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_history_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
