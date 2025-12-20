-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 11:57 AM
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
-- Database: `bakery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL DEFAULT 'present',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`meta`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `status`, `meta`) VALUES
(1, 12, '2025-10-25', 'absent', '{\"updated_at\":\"2025-10-25 16:11:54\"}'),
(2, 11, '2025-10-25', 'present', '{\"updated_at\":\"2025-10-25 18:10:48\"}'),
(3, 10, '2025-10-25', 'absent', '{\"updated_at\":\"2025-10-25 16:11:56\"}'),
(4, 9, '2025-10-25', 'present', '{\"updated_at\":\"2025-10-25 15:47:11\"}'),
(5, 8, '2025-10-25', 'present', '{\"updated_at\":\"2025-10-25 14:49:16\"}'),
(6, 4, '2025-10-25', 'present', '{\"updated_at\":\"2025-10-25 15:47:13\"}'),
(7, 1, '2025-10-25', 'present', '{\"updated_at\":\"2025-10-25 14:49:17\"}'),
(8, 11, '2025-10-26', 'absent', '{\"created_at\":\"2025-10-26 16:16:09\"}'),
(9, 12, '2025-10-26', 'present', '{\"created_at\":\"2025-10-26 16:16:18\"}'),
(10, 12, '2025-11-02', 'present', '{\"created_at\":\"2025-11-02 12:38:24\"}'),
(11, 12, '2025-11-03', 'present', '{\"created_at\":\"2025-11-03 13:35:19\"}'),
(12, 15, '2025-11-12', 'present', '{\"updated_at\":\"2025-11-12 11:59:58\"}'),
(13, 15, '2025-11-16', 'present', '{\"created_at\":\"2025-11-16 13:50:20\"}');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `customer_type` enum('Regular','Wholesale','VIP') DEFAULT 'Regular',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `customer_type`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Walk-in Customer', NULL, NULL, NULL, 'Regular', 'Default customer for walk-in sales', 1, '2025-11-08 19:33:55', '2025-11-08 19:33:55'),
(2, 'Cash Customer', NULL, NULL, NULL, 'Regular', 'Default cash customer', 1, '2025-11-08 19:33:55', '2025-11-08 19:33:55'),
(3, 'EFOTEC', '0795987898', 'rilkello251@gmail.com', 'Efotec school', 'Wholesale', 'atuye hafi', 2, '2025-11-08 19:38:27', '2025-11-09 13:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `salary_type` enum('monthly','hourly') DEFAULT 'monthly',
  `salary_amount` decimal(10,2) DEFAULT 0.00,
  `bank_name` varchar(150) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('Active','On Leave','Inactive') NOT NULL,
  `position` varchar(100) NOT NULL,
  `rating` decimal(2,1) DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `first_name`, `last_name`, `dob`, `address`, `phone`, `email`, `profile_pic`, `salary_type`, `salary_amount`, `bank_name`, `bank_account`, `created_at`, `status`, `position`, `rating`) VALUES
(1, NULL, 'MUCYO ', 'Josue', NULL, NULL, '+250790531998', 'm@gmail.com', NULL, 'monthly', 100.00, NULL, NULL, '2025-10-11 17:09:52', 'Active', 'admin', 0.0),
(4, NULL, 'MUCYO ', 'Josue', NULL, NULL, '+250790531998', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:41:03', 'Active', 'Baker', 0.0),
(8, NULL, 'Joe', 'Kesh', NULL, NULL, '+250790531998', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:53:22', 'Active', 'Baker', 0.0),
(9, NULL, 'NIYONSHUTI', 'Isaac', NULL, NULL, '+250793749143', 'm.joshua250@outlook.com', NULL, 'monthly', 100000.00, NULL, NULL, '2025-10-14 12:47:12', 'Active', 'Baker', 0.0),
(10, NULL, 'MANZI', 'Robby', NULL, NULL, '+250790531998', 'robby@gmail.com', NULL, 'monthly', 100000.00, NULL, NULL, '2025-10-19 15:29:25', 'Active', 'Baker', 0.0),
(11, NULL, 'IRAKOZE', 'Sabine', NULL, NULL, '+250790531998', 'sabineirak@gmail.com', NULL, 'monthly', 3000.00, NULL, NULL, '2025-10-19 17:58:41', 'Active', 'Baker', 0.0),
(12, NULL, 'KAMIKAZI ', 'Florida', NULL, NULL, '+250790531998', 'kamikaziflorida@gmail.com', NULL, 'monthly', 100000.00, NULL, NULL, '2025-10-19 18:00:21', 'Active', 'Baker', 0.0),
(13, NULL, 'UWINEZA', 'Faustine', NULL, NULL, '+250790531998', 'uwinezafofo@gmail.com', NULL, 'monthly', 10000.00, NULL, NULL, '2025-11-09 17:49:05', 'Active', 'staff', 0.0),
(14, NULL, 'MANZI', 'Robby', NULL, NULL, '+250793749143', 'robby@gmail.com', NULL, 'monthly', 100000.00, NULL, NULL, '2025-11-09 17:54:16', 'Active', 'Manager', 0.0),
(15, NULL, 'habimana', 'jean', NULL, NULL, '0795756582', 'habimana@gmail.com', NULL, 'monthly', 500000.00, NULL, NULL, '2025-11-12 12:59:46', 'Active', 'staff', 0.0);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `type` enum('raw_material','product_sold','product_made','special') NOT NULL,
  `quantity_change` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `meta` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `type`, `quantity_change`, `amount`, `meta`, `created_at`) VALUES
(1014, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 20:48:25'),
(1015, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 20:48:25'),
(1016, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 20:48:25'),
(1017, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 20:48:25'),
(1018, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 20:48:40'),
(1019, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 20:48:40'),
(1020, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 20:48:40'),
(1021, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 20:48:40'),
(1022, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 20:49:51'),
(1023, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 20:49:51'),
(1024, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 20:49:51'),
(1025, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 20:49:51'),
(1026, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 20:49:54'),
(1027, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 20:49:54'),
(1028, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 20:49:54'),
(1029, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 20:49:54'),
(1030, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 20:57:56'),
(1031, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 20:57:56'),
(1032, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 20:57:56'),
(1033, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 20:57:56'),
(1034, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:01:56'),
(1035, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:01:56'),
(1036, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:01:56'),
(1037, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:01:56'),
(1038, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:02:01'),
(1039, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:02:01'),
(1040, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:02:01'),
(1041, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:02:01'),
(1042, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:03:29'),
(1043, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:03:29'),
(1044, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:03:29'),
(1045, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:03:29'),
(1046, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:08:41'),
(1047, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:08:41'),
(1048, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:08:41'),
(1049, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:08:41'),
(1050, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:08:57'),
(1051, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:08:57'),
(1052, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:08:57'),
(1053, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:08:57'),
(1054, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:08:58'),
(1055, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:08:58'),
(1056, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:08:58'),
(1057, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:08:58'),
(1058, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:11:13'),
(1059, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:11:13'),
(1060, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:11:13'),
(1061, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:11:13'),
(1062, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:11:26'),
(1063, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:11:26'),
(1064, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:11:26'),
(1065, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:11:26'),
(1066, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:24:15'),
(1067, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:24:15'),
(1068, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:24:15'),
(1069, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:24:15'),
(1070, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-26 21:25:00'),
(1071, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-26 21:25:00'),
(1072, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-26 21:25:00'),
(1073, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-26 21:25:00'),
(1074, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-27 14:41:43'),
(1075, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-27 14:41:43'),
(1076, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-27 14:41:43'),
(1077, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-27 14:41:43'),
(1078, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-27 15:09:31'),
(1079, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-27 15:09:31'),
(1080, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-27 15:09:31'),
(1081, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-27 15:09:31'),
(1082, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-27 15:10:00'),
(1083, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-27 15:10:00'),
(1084, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-27 15:10:00'),
(1085, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-27 15:10:00'),
(1086, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-27 15:12:14'),
(1087, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-27 15:12:14'),
(1088, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-27 15:12:14');

-- --------------------------------------------------------

--
-- Table structure for table `material_orders`
--

CREATE TABLE `material_orders` (
  `id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `total_value` decimal(12,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_orders`
--

INSERT INTO `material_orders` (`id`, `order_date`, `total_value`, `note`, `created_by`, `created_at`) VALUES
(3, '2025-11-02', 25000.00, '', 1, '2025-11-02 12:49:12'),
(4, '2025-11-02', 23500.00, '', 1, '2025-11-02 13:37:18'),
(5, '2025-11-02', 0.00, '', 1, '2025-11-02 16:12:08'),
(6, '2025-11-02', 100.00, '', 1, '2025-11-02 16:27:05'),
(7, '2025-11-02', 2000.00, '', 1, '2025-11-02 17:02:37'),
(8, '2025-11-03', 9100.00, '', 1, '2025-11-03 12:49:22'),
(9, '2025-11-04', 201750.00, '', 1, '2025-11-04 08:26:06'),
(10, '2025-11-05', 401250.00, 'as of today', 1, '2025-11-05 13:06:16'),
(11, '2025-11-08', 23000.00, '', 1, '2025-11-08 12:40:13'),
(12, '2025-11-08', 41500.00, '', 1, '2025-11-08 17:48:28'),
(13, '2025-11-09', 41000.00, '', 1, '2025-11-09 14:04:19'),
(14, '2025-11-09', 221000.00, '', 1, '2025-11-09 17:39:08'),
(15, '2025-11-09', 0.00, '', 1, '2025-11-09 17:41:48'),
(16, '2025-11-09', 200.00, '', 1, '2025-11-09 17:55:31'),
(17, '2025-11-16', 30000.00, '', 1, '2025-11-16 14:52:39'),
(18, '2025-11-17', 103500.00, '', 1, '2025-11-17 15:54:37'),
(19, '2025-11-18', 100000.00, '', 1, '2025-11-18 13:09:01'),
(20, '2025-11-21', 5000.00, '', 1, '2025-11-21 12:51:04'),
(21, '2025-11-22', 8000.00, '', 1, '2025-11-22 15:57:12'),
(22, '2025-11-22', 58000.00, '', 1, '2025-11-22 17:24:54'),
(23, '2025-11-22', 20000.00, '', 1, '2025-11-22 17:37:55'),
(24, '2025-12-01', 0.00, '', 1, '2025-12-01 13:04:52'),
(25, '2025-12-15', 5000.00, '', 1, '2025-12-15 10:18:13'),
(26, '2025-12-15', 5000.00, '', 1, '2025-12-15 10:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `material_order_items`
--

CREATE TABLE `material_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `unit_price` decimal(10,4) DEFAULT 0.0000,
  `total_value` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_order_items`
--

INSERT INTO `material_order_items` (`id`, `order_id`, `material_id`, `qty`, `unit_price`, `total_value`) VALUES
(3, 3, 4, 12.000, 2000.0000, 24000.00),
(4, 3, 4, 1.000, 1000.0000, 1000.00),
(12, 6, 4, 10.000, 10.0000, 100.00),
(23, 4, 4, 20.000, 200.0000, 4000.00),
(24, 4, 2, 10.000, 1700.0000, 17000.00),
(25, 4, 3, 10.000, 250.0000, 2500.00),
(34, 7, 4, 10.000, 200.0000, 2000.00),
(36, 8, 4, 30.000, 250.0000, 7500.00),
(37, 8, 1, 0.800, 2000.0000, 1600.00),
(38, 9, 4, 7.000, 250.0000, 1750.00),
(39, 9, 1, 100.000, 2000.0000, 200000.00),
(42, 10, 4, 5.000, 250.0000, 1250.00),
(43, 10, 1, 200.000, 2000.0000, 400000.00),
(46, 11, 4, 10.000, 200.0000, 2000.00),
(47, 11, 2, 10.000, 1700.0000, 17000.00),
(48, 11, 1, 10.000, 200.0000, 2000.00),
(49, 11, 3, 10.000, 200.0000, 2000.00),
(50, 12, 4, 10.000, 250.0000, 2500.00),
(51, 12, 2, 10.000, 1700.0000, 17000.00),
(52, 12, 1, 10.000, 2000.0000, 20000.00),
(53, 12, 3, 10.000, 200.0000, 2000.00),
(54, 13, 4, 10.000, 200.0000, 2000.00),
(55, 13, 2, 10.000, 1700.0000, 17000.00),
(56, 13, 1, 10.000, 2000.0000, 20000.00),
(57, 13, 3, 10.000, 200.0000, 2000.00),
(58, 14, 4, 10.000, 200.0000, 2000.00),
(59, 14, 2, 10.000, 1700.0000, 17000.00),
(60, 14, 1, 100.000, 2000.0000, 200000.00),
(61, 14, 3, 10.000, 200.0000, 2000.00),
(67, 16, 4, 1.000, 200.0000, 200.00),
(68, 17, 5, 10.000, 500.0000, 5000.00),
(69, 17, 5, 100.000, 250.0000, 25000.00),
(70, 18, 5, 100.000, 500.0000, 50000.00),
(71, 18, 4, 10.000, 250.0000, 2500.00),
(72, 18, 2, 30.000, 1700.0000, 51000.00),
(73, 19, 5, 100.000, 1000.0000, 100000.00),
(74, 20, 5, 10.000, 500.0000, 5000.00),
(75, 21, 5, 10.000, 500.0000, 5000.00),
(76, 21, 4, 10.000, 300.0000, 3000.00),
(77, 22, 1, 4.000, 2000.0000, 8000.00),
(78, 22, 5, 100.000, 500.0000, 50000.00),
(79, 23, 2, 10.000, 2000.0000, 20000.00),
(81, 25, 5, 10.000, 500.0000, 5000.00),
(82, 26, 5, 10.000, 500.0000, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `data`, `is_read`, `created_by`, `created_at`) VALUES
(1, 'production', 'Produced 1000 units of product ID 1', NULL, 1, NULL, '2025-10-11 17:22:33'),
(2, 'production', 'Produced 100 units of product ID 1', NULL, 1, NULL, '2025-10-11 17:24:12'),
(3, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-12 13:07:44'),
(4, 'production', 'Produced 100 units of product ID 5', NULL, 1, NULL, '2025-10-19 16:22:20'),
(5, 'production', 'Produced 1 units of product ID 1', NULL, 1, NULL, '2025-10-19 16:32:33'),
(6, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-19 16:42:49'),
(7, 'sale', 'Sold 100 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-10-19 16:48:03'),
(8, 'over_usage', 'Attempted sale exceeds available stock for Product ID 2', NULL, 1, NULL, '2025-10-19 17:27:30'),
(9, 'production', 'Produced 1 units of Gateau', NULL, 1, 1, '2025-10-19 19:23:14'),
(10, 'production', 'Produced 10 units of Bread', NULL, 1, 1, '2025-10-19 19:24:01'),
(11, 'production', 'Produced 1 units of Cake', NULL, 1, 1, '2025-10-19 19:24:54'),
(12, 'production', 'Produced 1 units of Cake', NULL, 1, 1, '2025-10-19 19:34:36'),
(13, 'production', 'Produced 1 units of Bread', NULL, 1, 1, '2025-10-19 20:22:45'),
(14, 'production', 'Produced 1 units of Bread', NULL, 1, 1, '2025-10-19 20:23:13'),
(15, 'production', 'Produced 1 units of Bread', NULL, 1, 1, '2025-10-19 20:29:53'),
(16, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-10-19 20:58:11'),
(17, 'over_usage', 'Attempted sale exceeds available stock for Product ID 2', NULL, 1, NULL, '2025-10-19 21:01:08'),
(18, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-10-19 21:01:52'),
(19, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-20 15:31:02'),
(20, 'over_usage', 'Attempted sale exceeds available stock for Product ID 1', NULL, 1, NULL, '2025-10-20 15:31:12'),
(21, 'over_usage', 'Attempted sale exceeds available stock for Product ID 1', NULL, 1, NULL, '2025-10-20 15:31:23'),
(22, 'over_usage', 'Attempted sale exceeds available stock for Product ID 5', NULL, 1, NULL, '2025-10-21 08:53:56'),
(23, 'sale', 'Sold 1 units of Product ID 5 successfully.', NULL, 1, NULL, '2025-10-21 08:54:03'),
(24, 'sale', 'Sold 1 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-22 08:48:34'),
(25, 'over_usage', 'Attempted sale exceeds available stock for Product ID 2', NULL, 1, NULL, '2025-10-24 21:06:19'),
(26, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-25 11:15:59'),
(27, 'over_usage', 'Attempted sale exceeds available stock for Product ID 2', NULL, 1, NULL, '2025-10-25 16:39:41'),
(28, 'sale', 'Sold 88 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-25 17:05:51'),
(29, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-25 18:18:46'),
(30, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-10-26 18:19:44'),
(31, 'sale', 'Sold 10 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-10-26 18:20:17'),
(32, 'sale', 'Sold 10 units of Product ID 6 successfully.', NULL, 1, NULL, '2025-10-26 18:23:02'),
(33, 'sale', 'Sold 18 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-27 17:05:06'),
(34, 'sale', 'Sold 18 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-10-27 17:05:06'),
(35, 'sale', 'Sold 1 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-02 12:57:17'),
(36, 'sale', 'Sold 1 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-02 12:57:17'),
(37, 'sale', 'Sold 19 units of product_id 6 for 0', '{\"product_id\":6,\"qty\":19,\"total\":0}', 1, 1, '2025-11-02 14:37:49'),
(38, 'sale', 'Sold 19 units of product_id 6 for 0', '{\"product_id\":6,\"qty\":19,\"total\":0}', 1, 1, '2025-11-02 14:38:43'),
(39, 'sale', 'Sold 19 units of product_id 6 for 0', '{\"product_id\":6,\"qty\":19,\"total\":0}', 1, 1, '2025-11-02 14:38:55'),
(40, 'sale', 'Sold 2 units of product_id 5 for 10000', '{\"product_id\":5,\"qty\":2,\"total\":10000}', 1, 1, '2025-11-02 14:58:58'),
(41, 'sale', 'Sold 19 units of product_id 6 for 1900', '{\"product_id\":6,\"qty\":19,\"total\":1900}', 1, 1, '2025-11-02 15:56:50'),
(42, 'sale', 'Sold 10 units of product_id 6 for 1000', '{\"product_id\":6,\"qty\":10,\"total\":1000}', 1, 1, '2025-11-02 15:58:28'),
(43, 'sale', 'Sold 11 units of product_id 6 for 1100', '{\"product_id\":6,\"qty\":11,\"total\":1100}', 1, 1, '2025-11-02 16:29:11'),
(44, 'sale', 'Sold 11 units of product_id 6 for 1100', '{\"product_id\":6,\"qty\":11,\"total\":1100}', 1, 1, '2025-11-02 16:47:14'),
(45, 'sale', 'Sold 11 units of product_id 6 for 1100', '{\"product_id\":6,\"qty\":11,\"total\":1100}', 1, 1, '2025-11-02 16:47:28'),
(46, 'sale', 'Sold 12 units of product_id 6 for 1200', '{\"product_id\":6,\"qty\":12,\"total\":1200}', 1, 1, '2025-11-02 16:58:25'),
(47, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-03 12:50:54'),
(48, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-03 12:50:54'),
(49, 'production', 'Produced 100 units of Cake', '{\"product_id\":1,\"quantity\":100,\"product_name\":\"Cake\"}', 1, 1, '2025-11-03 14:48:10'),
(50, 'production', 'Produced 200.00 units of Biscuit', '{\"product_id\":6,\"quantity\":\"200.00\",\"product_name\":\"Biscuit\"}', 1, 1, '2025-11-04 12:30:39'),
(51, 'sale', 'Sold 100 units of Product ID 6 successfully.', NULL, 1, NULL, '2025-11-04 08:28:59'),
(52, 'sale', 'Sold 100 units of Product ID 6 successfully.', NULL, 1, NULL, '2025-11-04 08:28:59'),
(53, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-05 12:45:10'),
(54, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-05 12:45:10'),
(55, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-05 13:06:44'),
(56, 'sale', 'Sold 100 units of Product ID 6 successfully.', NULL, 1, NULL, '2025-11-05 13:07:15'),
(57, 'sale', 'Sold 100 units of Product ID 6 successfully.', NULL, 1, NULL, '2025-11-05 13:07:15'),
(58, 'sale', 'Sold 70 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-07 19:42:35'),
(59, 'sale', 'Sold 70 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-07 19:42:35'),
(60, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-07 20:47:01'),
(61, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-07 20:47:01'),
(62, 'sale', 'Sold 90 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 12:38:29'),
(63, 'sale', 'Sold 90 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 12:38:29'),
(64, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-08 12:41:28'),
(65, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 13:37:27'),
(66, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 13:37:27'),
(67, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:19:49'),
(68, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:19:49'),
(69, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:22:36'),
(70, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:22:36'),
(71, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:23:49'),
(72, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:23:49'),
(73, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:25:56'),
(74, 'sale', 'Sold 1 units of Product ID 2 successfully.', NULL, 1, NULL, '2025-11-08 17:25:56'),
(75, 'production', 'Produced 100 units of Bread', '{\"product_id\":2,\"quantity\":100,\"product_name\":\"Bread\"}', 1, 2, '2025-11-08 17:48:53'),
(76, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 19:39:38'),
(77, 'sale', 'Sold 10 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-08 19:39:38'),
(78, 'sale', 'Sold 56 units of Product ID 5 successfully.', NULL, 1, NULL, '2025-11-08 20:18:54'),
(79, 'sale', 'Sold 56 units of Product ID 5 successfully.', NULL, 1, NULL, '2025-11-08 20:18:54'),
(80, 'sale', 'Sold 2 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-09 13:27:49'),
(81, 'sale', 'Sold 2 units of Product ID 1 successfully.', NULL, 1, NULL, '2025-11-09 13:27:49'),
(82, 'sale', 'Sold 10 units of Product ID 5 successfully.', NULL, 1, NULL, '2025-11-09 13:45:59'),
(83, 'sale', 'Sold 10 units of Product ID 5 successfully.', NULL, 1, NULL, '2025-11-09 13:45:59'),
(84, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-09 14:04:40'),
(85, 'sale', 'Sold 7 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-09 17:26:32'),
(86, 'sale', 'Sold 7 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-09 17:26:32'),
(87, 'sale', 'Sold 10 units of Gateau (GA-001) successfully.', NULL, 1, NULL, '2025-11-09 17:29:20'),
(88, 'sale', 'Sold 10 units of Gateau (GA-001) successfully.', NULL, 1, NULL, '2025-11-09 17:29:20'),
(89, 'sale', 'Sold 10 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-09 17:30:51'),
(90, 'sale', 'Sold 10 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-09 17:30:51'),
(91, 'sale', 'Sold 86 units of Biscuit (BS-001) successfully.', NULL, 1, NULL, '2025-11-09 17:32:20'),
(92, 'plan_update', 'Saved plan for Product ID 2 on 2025-11-09', NULL, 1, NULL, '2025-11-09 17:39:08'),
(93, 'plan_update', 'Saved plan for Cake (CK-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 17:41:48'),
(94, 'employee_added', 'MANZI Robby was added as Manager', NULL, 1, NULL, '2025-11-09 17:54:16'),
(95, 'plan_update', 'Saved plan for Gateau (GA-001) on 09/11/2025', NULL, 1, NULL, '2025-11-09 17:55:31'),
(96, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:10:08'),
(97, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:18:07'),
(98, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:18:54'),
(99, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:19:02'),
(100, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:22:25'),
(101, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:22:39'),
(102, 'plan_update', 'Updated plan for Cake (CK-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:22:45'),
(103, 'plan_update', 'Updated plan for Gateau (GA-001) on 2025-11-09', NULL, 1, NULL, '2025-11-09 18:22:59'),
(104, 'employee_added', 'habimana jean was added as staff', NULL, 1, NULL, '2025-11-12 12:59:46'),
(105, 'plan_update', 'Saved plan for Bread (BR-001) on 16/11/2025', NULL, 1, NULL, '2025-11-16 14:52:39'),
(106, 'production', 'Produced 100 units of Bread', '{\"product_id\":2,\"quantity\":100,\"product_name\":\"Bread\"}', 1, 2, '2025-11-16 14:54:07'),
(107, 'sale', 'Sold 10 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-16 14:57:33'),
(108, 'sale', 'Sold 80 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-16 16:39:14'),
(109, 'plan_update', 'Saved plan for Biscuit (BS-001) on 17/11/2025', NULL, 1, NULL, '2025-11-17 15:54:37'),
(110, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-17 15:56:00'),
(111, 'sale', 'Sold 50 units of Cake (CK-001) successfully.', NULL, 1, NULL, '2025-11-17 16:10:25'),
(112, 'plan_update', 'Saved plan for Biscuit (BS-001) on 18/11/2025', NULL, 1, NULL, '2025-11-18 13:09:01'),
(113, 'plan_update', 'Saved plan for Biscuit (BS-001) on 21/11/2025', NULL, 1, NULL, '2025-11-21 12:51:04'),
(114, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-21 12:51:56'),
(115, 'plan_update', 'Saved plan for Biscuit (BS-001) on 22/11/2025', NULL, 1, NULL, '2025-11-22 15:57:13'),
(116, 'production', 'Produced 100 units of Biscuit', '{\"product_id\":6,\"quantity\":100,\"product_name\":\"Biscuit\"}', 1, 2, '2025-11-22 15:59:01'),
(117, 'plan_update', 'Saved plan for Bread (BR-001) on 22/11/2025', NULL, 1, NULL, '2025-11-22 17:24:54'),
(118, 'production', 'Produced 100 units of Bread', '{\"product_id\":2,\"quantity\":100,\"product_name\":\"Bread\"}', 1, 2, '2025-11-22 17:25:43'),
(119, 'sale', 'Sold 28 units of Cake (CK-001) successfully.', NULL, 1, NULL, '2025-11-22 17:26:08'),
(120, 'plan_update', 'Saved plan for Cake (CK-001) on 22/11/2025', NULL, 1, NULL, '2025-11-22 17:37:55'),
(121, 'sale', 'Sold 10 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-28 13:35:09'),
(122, 'sale', 'Sold 10 units of Bread (BR-001) successfully.', NULL, 1, NULL, '2025-11-28 13:35:09'),
(123, 'sale', 'Supplier #1 sold 10 units of Biscuit (BS-001) successfully.', NULL, 1, NULL, '2025-11-28 13:42:21'),
(124, 'sale', 'Trip allocation: supplier #1 sold 10 units of Bread (BR-001) via trip item #2.', NULL, 1, NULL, '2025-11-28 20:12:09'),
(125, 'sale', 'Trip allocation: supplier #1 sold 10 units of Biscuit (BS-001) via trip item #1.', NULL, 1, NULL, '2025-11-28 20:12:54'),
(126, 'sale', 'Trip allocation: supplier #1 sold 1 units of Gateau (GA-001) via trip item #3.', NULL, 1, NULL, '2025-11-28 20:13:14'),
(127, 'sale', 'Trip allocation: supplier #1 sold 5 units of Gateau (GA-001) via trip item #3.', NULL, 1, NULL, '2025-11-28 20:13:37'),
(128, 'sale', 'Trip allocation: supplier #1 sold 1 units of Gateau (GA-001) via trip item #3.', NULL, 1, NULL, '2025-11-28 20:13:45'),
(129, 'sale', 'Trip allocation: supplier #1 sold 3 units of Gateau (GA-001) via trip item #3.', NULL, 1, NULL, '2025-11-28 20:14:25'),
(130, 'sale', 'Trip allocation: supplier #1 sold 10 units of Biscuit (BS-001) via trip item #5.', NULL, 1, NULL, '2025-11-28 21:03:56'),
(131, 'sale', 'Trip allocation: supplier #2 sold 10 units of Biscuit (BS-001) via trip item #6.', NULL, 1, NULL, '2025-11-28 21:13:58'),
(132, 'sale', 'Trip allocation: supplier #2 sold 3 units of Biscuit (BS-001) via trip item #7.', NULL, 1, NULL, '2025-11-29 07:42:44'),
(133, 'sale', 'Trip allocation: supplier #2 sold 2 units of Biscuit (BS-001) via trip item #7.', NULL, 1, NULL, '2025-11-29 07:43:02'),
(134, 'sale', 'Trip allocation: supplier #3 sold 9 units of Bread (BR-001) via trip item #11.', NULL, 1, NULL, '2025-11-29 08:42:04'),
(135, 'sale', 'Trip allocation: supplier #3 sold 1 units of Bread (BR-001) via trip item #11.', NULL, 1, NULL, '2025-11-29 09:03:05'),
(136, 'sale', 'Trip allocation: supplier #3 sold 5 units of Bread (BR-001) via trip item #13.', NULL, 1, NULL, '2025-11-29 11:25:06'),
(137, 'sale', 'Trip allocation: supplier #3 sold 3 units of Bread (BR-001) via trip item #11.', NULL, 1, NULL, '2025-11-29 14:15:59'),
(138, 'sale', 'Trip allocation: supplier #3 sold 2 units of Bread (BR-001) via trip item #11.', NULL, 1, NULL, '2025-11-29 14:24:32'),
(139, 'sale', 'Trip allocation: supplier #3 sold 1 units of Bread (BR-001) via trip item #11.', NULL, 1, NULL, '2025-11-29 14:45:15'),
(140, 'plan_update', 'Saved plan for Biscuit (BS-001) on 01/12/2025', NULL, 1, NULL, '2025-12-01 13:04:52'),
(141, 'plan_update', 'Updated plan for Biscuit (BS-001) on 2025-12-01', NULL, 1, NULL, '2025-12-01 13:06:47'),
(142, 'sale', 'Trip allocation: supplier #3 sold 1 units of Bread (BR-001) via trip item #13.', NULL, 1, NULL, '2025-12-01 14:44:59'),
(143, 'sale', 'Trip allocation: supplier #1 sold 25 units of Biscuit (BS-001) via trip item #14.', NULL, 1, NULL, '2025-12-15 10:15:23'),
(145, 'plan_update', 'Saved plan for Bread (BR-001) on 15/12/2025', NULL, 0, NULL, '2025-12-15 10:45:28'),
(146, 'sale', 'Trip allocation: supplier #3 sold 10 units of Bread (BR-001) via trip item #15.', NULL, 0, NULL, '2025-12-15 10:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','sent','failed','paid') DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_produced` decimal(10,2) NOT NULL,
  `raw_materials_used` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production`
--

INSERT INTO `production` (`id`, `product_id`, `quantity_produced`, `raw_materials_used`, `created_by`, `created_at`) VALUES
(1, 1, 1000.00, '', NULL, '2025-10-11 15:20:33'),
(2, 1, 1000.00, '', 1, '2025-10-11 15:21:00'),
(3, 1, 1000.00, '', 1, '2025-10-11 15:22:33'),
(4, 1, 100.00, '', 1, '2025-10-11 15:24:12'),
(5, 5, 100.00, '', 1, '2025-10-19 14:22:20'),
(6, 1, 1.00, '', 1, '2025-10-19 14:32:33'),
(7, 5, 1.00, 'Pakimaya: 3 packets', 1, '2025-10-19 17:23:14'),
(8, 2, 10.00, 'Flour: 15kg', 1, '2025-10-19 17:24:01'),
(9, 1, 1.00, 'Flour: 2kg, Sugar: 1kg', 1, '2025-10-19 17:24:54'),
(10, 1, 1.00, 'Pakimaya: 1packet', 1, '2025-10-19 17:34:36'),
(11, 2, 1.00, 'Pakimaya: 1kg', 1, '2025-10-19 18:22:45'),
(12, 2, 1.00, 'Pakimaya: 1kg', 1, '2025-10-19 18:23:13'),
(13, 2, 1.00, 'Pakimaya: 1kg', 1, '2025-10-19 18:29:53'),
(14, 2, 1.00, 'Pakimaya: 1kg', 1, '2025-10-19 19:00:42'),
(15, 2, 10.00, 'Pakimaya: 10kg', 1, '2025-10-19 19:10:15'),
(16, 1, 1.00, 'isukari: 1kg, ifarini: 1kg, Pakimaya: 1packet', 1, '2025-10-20 13:30:40'),
(17, 5, 1.00, 'isukari: 1kg', 1, '2025-10-21 06:53:42'),
(18, 1, 1.00, 'isukari: 1kg, ifarini: 1kg, Pakimaya: 1packet', 1, '2025-10-22 06:21:19'),
(19, 1, 1.00, 'isukari: 1kg, ifarini: 1kg, Pakimaya: 1packet', 1, '2025-10-22 06:46:05'),
(20, 2, 1.00, 'isukari: 0.2kg', 1, '2025-10-24 19:04:42'),
(21, 1, 1.00, 'isukari: 1kg, ifarini: 1kg, Pakimaya: 1packet', 1, '2025-10-25 09:15:13'),
(22, 6, 6.00, 'isukari: 3kg, ifarini: 0.6kg', 1, '2025-10-25 15:17:48'),
(23, 6, 10.00, 'ifarini: 1kg', 1, '2025-10-26 16:22:03'),
(24, 6, 10.00, 'ifarini: 1kg', 1, '2025-10-26 16:22:45'),
(25, 1, 1.00, 'isukari: 1kg, ifarini: 1kg, Pakimaya: 1packet', 1, '2025-10-27 15:03:46'),
(26, 1, 100.00, 'isukari: 100kg, ifarini: 100kg, Pakimaya: 100packet', 1, '2025-11-03 12:48:10'),
(27, 6, 200.00, 'Eggs: 7.000piece, isukari: 100.000kg', 1, '2025-11-04 06:26:48'),
(28, 6, 100.00, 'Eggs: 5.000piece, isukari: 200.000kg', 2, '2025-11-05 11:06:44'),
(29, 6, 100.00, 'Eggs: 10.000piece, ifarini: 10.000kg, isukari: 10.000kg, Pakimaya: 10.000packet', 2, '2025-11-08 10:41:28'),
(30, 2, 100.00, 'Eggs: 10.000piece, ifarini: 10.000kg, isukari: 10.000kg, Pakimaya: 10.000packet', 2, '2025-11-08 15:48:53'),
(31, 6, 100.00, 'Eggs: 10.000piece, ifarini: 10.000kg, isukari: 10.000kg, Pakimaya: 10.000packet', 2, '2025-11-09 12:04:40'),
(32, 2, 100.00, 'amata: 10.000liter, amata: 100.000liter', 2, '2025-11-16 12:54:06'),
(33, 6, 100.00, 'amata: 100.000liter, Eggs: 10.000piece, ifarini: 30.000kg', 2, '2025-11-17 13:56:00'),
(34, 6, 100.00, 'amata: 10.000liter', 2, '2025-11-21 10:51:56'),
(35, 6, 100.00, 'amata: 10.000liter, Eggs: 10.000piece', 2, '2025-11-22 13:59:01'),
(36, 2, 100.00, 'isukari: 4.000kg, amata: 100.000liter', 2, '2025-11-22 15:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `production_batches`
--

CREATE TABLE `production_batches` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_produced` int(11) NOT NULL,
  `produced_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_materials`
--

CREATE TABLE `production_materials` (
  `id` int(11) NOT NULL,
  `production_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `qty_used` decimal(12,3) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `unit_cost` decimal(10,4) DEFAULT NULL,
  `total_cost` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_records`
--

CREATE TABLE `production_records` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_records`
--

INSERT INTO `production_records` (`id`, `product_id`, `quantity`, `created_by`, `created_at`, `note`) VALUES
(1, 6, 100, 1, '2025-10-31 13:47:52', 'nta byasagutse');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(80) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `price`, `unit`, `created_at`, `stock`) VALUES
(1, 'Cake', 'CK-001', 200.00, 'piece', '2025-10-11 17:11:03', 0),
(2, 'Bread', 'BR-001', 100.00, 'piece', '2025-10-12 13:20:24', 80),
(5, 'Gateau', 'GA-001', 5000.00, 'piece', '2025-10-19 15:31:47', 60),
(6, 'Biscuit', 'BS-001', 100.00, 'packet              ', '2025-10-25 16:37:18', 650);

-- --------------------------------------------------------

--
-- Table structure for table `product_daily_stats`
--

CREATE TABLE `product_daily_stats` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stat_date` date NOT NULL,
  `produced` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `revenue` decimal(12,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_daily_stats`
--

INSERT INTO `product_daily_stats` (`id`, `product_id`, `stat_date`, `produced`, `sold`, `revenue`, `created_by`, `created_at`) VALUES
(1, 6, '2025-11-02', 12, 12, 1200.00, 1, '2025-11-02 16:58:25'),
(3, 5, '2025-11-02', 19, 0, 0.00, 1, '2025-11-02 14:59:21'),
(15, 6, '2025-11-03', 150, 0, 0.00, 1, '2025-11-03 12:51:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_material_plans`
--

CREATE TABLE `product_material_plans` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `plan_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_material_plans`
--

INSERT INTO `product_material_plans` (`id`, `product_id`, `order_id`, `plan_date`, `created_at`) VALUES
(1, 6, 4, '2025-11-02', '2025-11-02 13:37:18'),
(2, 2, 5, '2025-11-02', '2025-11-02 16:12:08'),
(3, 5, 6, '2025-11-02', '2025-11-02 16:27:05'),
(4, 1, 7, '2025-11-02', '2025-11-02 17:02:37'),
(5, 6, 8, '2025-11-03', '2025-11-03 12:49:22'),
(6, 6, 9, '2025-11-04', '2025-11-04 08:26:06'),
(7, 6, 10, '2025-11-05', '2025-11-05 13:06:17'),
(8, 6, 11, '2025-11-08', '2025-11-08 12:40:13'),
(9, 2, 12, '2025-11-08', '2025-11-08 17:48:28'),
(10, 6, 13, '2025-11-09', '2025-11-09 14:04:19'),
(11, 2, 14, '2025-11-09', '2025-11-09 17:39:08'),
(12, 1, 15, '2025-11-09', '2025-11-09 17:41:48'),
(13, 5, 16, '2025-11-09', '2025-11-09 17:55:31'),
(14, 2, 17, '2025-11-16', '2025-11-16 14:52:39'),
(15, 6, 18, '2025-11-17', '2025-11-17 15:54:37'),
(16, 6, 19, '2025-11-18', '2025-11-18 13:09:02'),
(17, 6, 20, '2025-11-21', '2025-11-21 12:51:04'),
(18, 6, 21, '2025-11-22', '2025-11-22 15:57:13'),
(19, 2, 22, '2025-11-22', '2025-11-22 17:24:54'),
(20, 1, 23, '2025-11-22', '2025-11-22 17:37:55'),
(21, 6, 24, '2025-12-01', '2025-12-01 13:04:52'),
(22, 6, 25, '2025-12-15', '2025-12-15 10:18:13'),
(23, 2, 26, '2025-12-15', '2025-12-15 10:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `quantity` decimal(12,3) DEFAULT NULL,
  `unit_cost` decimal(10,4) DEFAULT NULL,
  `total_cost` decimal(12,2) DEFAULT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit_cost` decimal(10,4) DEFAULT 0.0000,
  `stock_quantity` decimal(12,3) DEFAULT 0.000,
  `low_threshold` decimal(12,3) DEFAULT 0.000,
  `supplier_id` int(11) DEFAULT NULL,
  `last_purchase_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `name`, `unit`, `category`, `unit_cost`, `stock_quantity`, `low_threshold`, `supplier_id`, `last_purchase_date`, `created_at`) VALUES
(1, 'isukari', 'kg', '', 2000.0000, 0.000, 5.000, NULL, NULL, '2025-10-11 17:10:27'),
(2, 'ifarini', 'kg', 'ingredient', 1700.0000, 20.000, 10.000, NULL, NULL, '2025-10-11 17:12:32'),
(3, 'Pakimaya', 'packet', '', 300.0000, 60.000, 10.000, NULL, NULL, '2025-10-19 16:05:36'),
(4, 'Eggs', 'piece', 'ingredient', 200.0000, 0.000, 10.000, NULL, NULL, '2025-10-25 16:38:12'),
(5, 'amata', 'liter', 'drinks', 500.0000, 4550.000, 123.000, NULL, NULL, '2025-11-12 13:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `yield_quantity` decimal(10,3) DEFAULT 1.000,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `product_id`, `yield_quantity`, `created_at`) VALUES
(18, 1, 1.000, '2025-10-19 21:12:01'),
(24, 5, 1.000, '2025-10-25 17:10:39'),
(25, 2, 1.000, '2025-10-25 17:13:02'),
(26, 6, 1.000, '2025-10-25 17:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` float DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`id`, `recipe_id`, `material_id`, `quantity`, `unit`) VALUES
(25, 18, 1, 1, 'kg'),
(26, 18, 2, 1, 'kg'),
(27, 18, 3, 1, 'packet'),
(37, 25, 1, 11, 'kg'),
(40, 26, 2, 0.1, 'kg'),
(47, 24, 1, 1, 'kg'),
(48, 24, 3, 2, 'piece'),
(49, 24, 1, 7, 'sacks each 25kg');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_items`
--

CREATE TABLE `recipe_items` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `supplied_by` int(11) DEFAULT NULL,
  `supplier_trip_item_id` int(11) DEFAULT NULL,
  `sold_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `qty`, `unit_price`, `total_price`, `customer_type`, `payment_method`, `customer_id`, `supplied_by`, `supplier_trip_item_id`, `sold_by`, `created_by`, `created_at`) VALUES
(1, 1, 100, 100.00, 10000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-12 13:07:44'),
(2, 2, 100, 100.00, 10000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-11 13:07:44'),
(3, 1, 100, 200.00, 20000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-19 16:42:49'),
(4, 2, 100, 100.00, 10000.00, 'Wholesale', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-19 16:48:03'),
(5, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-19 20:58:11'),
(6, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-19 21:01:52'),
(7, 1, 100, 200.00, 20000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-20 15:31:02'),
(8, 5, 1, 5000.00, 5000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-21 08:54:03'),
(9, 1, 1, 200.00, 200.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-22 08:48:34'),
(10, 1, 100, 200.00, 20000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-25 11:15:59'),
(11, 1, 88, 200.00, 17600.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-25 17:05:51'),
(12, 1, 10, 200.00, 2000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-25 18:18:46'),
(13, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-26 18:19:44'),
(14, 2, 10, 100.00, 1000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-26 18:20:17'),
(15, 6, 10, 100.00, 1000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-26 18:23:02'),
(16, 1, 18, 200.00, 3600.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-10-27 17:05:06'),
(17, 1, 1, 200.00, 200.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-02 12:57:17'),
(18, 6, 19, 0.00, 0.00, 'walk-in', 'cash', NULL, NULL, NULL, 2, 2, '2025-11-02 14:37:49'),
(19, 6, 19, 0.00, 0.00, 'walk-in', 'cash', NULL, NULL, NULL, 2, 1, '2025-11-02 14:38:43'),
(20, 6, 19, 0.00, 0.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 14:38:55'),
(21, 5, 2, 5000.00, 10000.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 14:58:58'),
(22, 6, 19, 100.00, 1900.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 15:56:50'),
(23, 6, 10, 100.00, 1000.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 15:58:28'),
(24, 6, 11, 100.00, 1100.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 16:29:11'),
(25, 6, 11, 100.00, 1100.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 16:47:14'),
(26, 6, 11, 100.00, 1100.00, 'walk-in', 'cash', NULL, NULL, NULL, 1, 1, '2025-11-02 16:47:28'),
(27, 1, 100, 200.00, 20000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-03 12:50:54'),
(28, 6, 100, 100.00, 10000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-04 08:28:59'),
(29, 1, 100, 200.00, 20000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-05 12:45:10'),
(30, 6, 100, 100.00, 10000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-05 13:07:15'),
(31, 1, 70, 200.00, 14000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-07 19:42:35'),
(32, 1, 10, 200.00, NULL, 'Regular', 'Cash', NULL, NULL, NULL, 2, 2, '2025-11-07 20:47:01'),
(33, 1, 90, 200.00, 18000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-08 12:38:29'),
(34, 1, 10, 200.00, 2000.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-08 13:37:27'),
(35, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 1, 1, '2025-11-08 17:19:49'),
(36, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 2, 2, '2025-11-08 17:22:36'),
(37, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 2, 2, '2025-11-08 17:23:49'),
(38, 2, 1, 100.00, 100.00, 'Regular', 'Cash', NULL, NULL, NULL, 2, 2, '2025-11-08 17:25:56'),
(39, 1, 10, 200.00, 2000.00, 'Regular', 'MoMo', 3, NULL, NULL, 2, 2, '2025-11-08 19:39:38'),
(40, 5, 56, 5000.00, 280000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-08 20:18:54'),
(41, 1, 2, 200.00, 400.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-09 13:27:49'),
(42, 5, 10, 5000.00, 50000.00, 'Regular', 'Cash', 2, NULL, NULL, 2, 2, '2025-11-09 13:45:59'),
(43, 2, 7, 100.00, 700.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-09 17:26:32'),
(44, 5, 10, 5000.00, 50000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-09 17:29:20'),
(45, 2, 10, 100.00, 1000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-09 17:30:51'),
(46, 6, 86, 100.00, 8600.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-09 17:32:20'),
(47, 2, 10, 100.00, 1000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 2, 2, '2025-11-16 14:57:33'),
(48, 2, 80, 100.00, 8000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 1, 1, '2025-11-16 16:39:13'),
(49, 1, 50, 200.00, 10000.00, 'Wholesale', 'Cash', 3, NULL, NULL, 1, 1, '2025-11-17 16:10:25'),
(50, 1, 28, 200.00, 5600.00, 'Regular', 'Cash', NULL, NULL, NULL, 2, 2, '2025-11-22 17:26:08'),
(51, 2, 10, 100.00, 1000.00, 'Regular', 'Cash', 2, 1, NULL, 1, 1, '2025-11-28 13:35:09'),
(52, 6, 10, 100.00, 1000.00, 'Regular', 'Cash', 2, 1, NULL, 1, 1, '2025-11-28 13:42:21'),
(53, 2, 10, 100.00, 1000.00, 'Regular', 'Cash', 2, 1, 2, 1, 1, '2025-11-28 20:12:09'),
(54, 6, 10, 100.00, 1000.00, 'Wholesale', 'Cash', 3, 1, 1, 1, 1, '2025-11-28 20:12:54'),
(55, 5, 1, 5000.00, 5000.00, 'Regular', 'Cash', NULL, 1, 3, 1, 1, '2025-11-28 20:13:14'),
(56, 5, 5, 5000.00, 25000.00, 'Regular', 'Cash', NULL, 1, 3, 1, 1, '2025-11-28 20:13:37'),
(57, 5, 1, 5000.00, 5000.00, 'Regular', 'Cash', NULL, 1, 3, 1, 1, '2025-11-28 20:13:45'),
(58, 5, 3, 5000.00, 15000.00, 'Regular', 'Cash', NULL, 1, 3, 1, 1, '2025-11-28 20:14:25'),
(59, 6, 10, 100.00, 1000.00, 'Wholesale', 'Cash', 3, 1, 5, 1, 1, '2025-11-28 21:03:56'),
(60, 6, 9, 100.00, 900.00, 'Wholesale', 'Cash', 3, 2, 6, 1, 1, '2025-11-28 21:13:58'),
(61, 6, 3, 100.00, 300.00, 'Wholesale', 'Cash', 3, 2, 7, 1, 1, '2025-11-29 07:42:44'),
(62, 6, 1, 100.00, 100.00, 'Regular', 'Cash', 1, 2, 7, 1, 1, '2025-11-29 07:43:02'),
(63, 6, 10, 100.00, 1000.00, 'Regular', 'Cash', 3, 2, NULL, 1, 1, '2025-11-29 08:23:07'),
(64, 2, 4, 100.00, 400.00, 'Regular', 'Cash', NULL, 3, 11, 1, 1, '2025-11-29 08:42:04'),
(65, 2, 2, 100.00, 200.00, 'Regular', 'Cash', 3, 3, 11, 1, 1, '2025-11-29 09:03:05'),
(66, 2, 4, 100.00, 400.00, 'Regular', 'Cash', NULL, 3, 13, 1, 1, '2025-11-29 11:25:06'),
(67, 6, 10, 100.00, 1000.00, 'Regular', 'Cash', 3, 3, NULL, 1, 1, '2025-11-29 11:28:13'),
(68, 2, 3, 100.00, 300.00, 'Regular', 'Cash', NULL, 3, 11, 1, 1, '2025-11-29 14:15:59'),
(69, 2, 1, 100.00, 100.00, 'Regular', 'Cash', 3, 3, 11, 1, 1, '2025-11-29 14:24:32'),
(70, 2, 4, 100.00, 400.00, 'Regular', 'Cash', 3, 3, 11, 1, 1, '2025-11-29 14:45:15'),
(71, 2, 1, 100.00, 100.00, 'Regular', 'Cash', 3, 3, 13, 1, 1, '2025-12-01 14:44:59'),
(72, 6, 25, 100.00, 2500.00, 'Regular', 'Cash', NULL, 1, 14, 1, 1, '2025-12-15 10:15:23'),
(73, 2, 10, 100.00, 1000.00, 'Regular', 'Cash', 3, 3, 15, 1, 1, '2025-12-15 10:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `schedule_type` enum('shift','production','maintenance','delivery','other') NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `title`, `description`, `schedule_type`, `start_time`, `end_time`, `assigned_to`, `status`, `meta`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Production', 'amandazi', 'production', '2025-10-24 19:43:00', '2025-10-24 19:44:00', 12, 'completed', '{\"notes\":\"amandazzi\\r\\n\",\"priority\":\"medium\"}', 1, '2025-10-24 17:43:25', '2025-10-24 17:44:18'),
(3, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-20 06:00:00', '2025-10-20 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:49:57', '2025-10-24 17:49:57'),
(4, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-21 06:00:00', '2025-10-21 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:50:02', '2025-10-24 17:50:02'),
(5, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-26 06:00:00', '2025-10-26 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:50:23', '2025-10-24 17:50:23'),
(6, 'Shift: Afternoon', 'Afternoon Shift', 'shift', '2025-10-26 14:00:00', '2025-10-26 22:00:00', 4, 'scheduled', '{\"employee_id\":\"4\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:50:34', '2025-10-24 17:50:34'),
(7, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-13 06:00:00', '2025-10-13 14:00:00', 1, 'in_progress', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:51:34', '2025-10-24 18:19:52'),
(8, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-13 06:00:00', '2025-10-13 14:00:00', 1, 'in_progress', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:51:44', '2025-10-25 09:17:45'),
(9, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-13 06:00:00', '2025-10-13 14:00:00', 1, 'in_progress', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:59:31', '2025-10-25 09:17:49'),
(10, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-19 06:00:00', '2025-10-19 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:59:38', '2025-10-24 17:59:38'),
(11, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-18 06:00:00', '2025-10-18 14:00:00', 1, 'in_progress', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 17:59:51', '2025-10-25 09:17:52'),
(12, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-06 06:00:00', '2025-10-06 14:00:00', 1, 'completed', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:00:06', '2025-10-24 18:20:46'),
(13, 'Shift: Morning', 'Morning Shift', 'shift', '2025-10-24 06:00:00', '2025-10-24 14:00:00', 4, 'in_progress', '{\"employee_id\":\"4\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:00:47', '2025-10-24 18:26:50'),
(14, 'Shift: Morning', '', 'shift', '2025-10-06 06:00:00', '2025-10-06 14:00:00', 1, 'completed', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:20:07', '2025-10-25 09:17:34'),
(15, 'Shift: Morning', 'production\r\n', 'shift', '2025-10-07 06:00:00', '2025-10-07 14:00:00', 1, 'in_progress', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:20:22', '2025-10-25 09:17:41'),
(16, 'Shift: Morning', '', 'shift', '2025-10-24 06:00:00', '2025-10-24 14:00:00', 12, 'in_progress', '{\"employee_id\":\"12\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:21:01', '2025-10-24 18:25:22'),
(17, 'Shift: Morning', '', 'shift', '2025-10-25 06:00:00', '2025-10-25 14:00:00', 4, 'completed', '{\"employee_id\":\"4\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:25:47', '2025-10-25 09:18:02'),
(18, 'Shift: Morning', '', 'shift', '2025-10-24 06:00:00', '2025-10-24 14:00:00', 4, 'scheduled', '{\"employee_id\":\"4\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 18:56:59', '2025-10-24 18:56:59'),
(19, 'Shift: Morning', 'production', 'shift', '2025-10-24 06:00:00', '2025-10-24 14:00:00', 11, 'scheduled', '{\"employee_id\":\"11\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:03:00', '2025-10-24 19:03:00'),
(20, 'Shift: Morning', '', 'shift', '2025-10-23 06:00:00', '2025-10-23 14:00:00', 8, 'scheduled', '{\"employee_id\":\"8\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:09', '2025-10-24 19:05:09'),
(21, 'Shift: Morning', '', 'shift', '2025-10-27 06:00:00', '2025-10-27 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:16', '2025-10-24 19:05:16'),
(22, 'Shift: Morning', '', 'shift', '2025-11-02 06:00:00', '2025-11-02 14:00:00', 12, 'scheduled', '{\"employee_id\":\"12\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:23', '2025-10-24 19:05:23'),
(23, 'Shift: Morning', '', 'shift', '2025-11-01 06:00:00', '2025-11-01 14:00:00', 12, 'scheduled', '{\"employee_id\":\"12\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:27', '2025-10-24 19:05:27'),
(24, 'Shift: Afternoon', '', 'shift', '2025-10-30 14:00:00', '2025-10-30 22:00:00', 8, 'scheduled', '{\"employee_id\":\"8\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:38', '2025-10-24 19:05:38'),
(25, 'Shift: Afternoon', '', 'shift', '2025-10-28 14:00:00', '2025-10-28 22:00:00', 11, 'scheduled', '{\"employee_id\":\"11\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-24 19:05:43', '2025-10-24 19:05:43'),
(26, 'Shift: Morning', '', 'shift', '2025-10-21 06:00:00', '2025-10-21 14:00:00', 11, 'scheduled', '{\"employee_id\":\"11\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 09:17:19', '2025-10-25 09:17:19'),
(27, 'Shift: Afternoon', '', 'shift', '2025-10-26 14:00:00', '2025-10-26 22:00:00', 12, 'scheduled', '{\"employee_id\":\"12\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 09:17:25', '2025-10-25 09:17:25'),
(28, 'Shift: Custom', 'amandazi', 'shift', '2025-10-20 18:14:00', '2025-10-20 18:15:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"custom\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 16:14:31', '2025-10-25 16:14:31'),
(29, 'Shift: Custom', 'amandazi', 'shift', '2025-10-25 18:15:00', '2025-10-25 18:16:00', 8, 'completed', '{\"employee_id\":\"8\",\"shift_type\":\"custom\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 16:15:34', '2025-10-25 16:17:45'),
(30, 'Shift: Morning', '', 'shift', '2025-11-03 06:00:00', '2025-11-03 14:00:00', 1, 'scheduled', '{\"employee_id\":\"1\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 17:10:05', '2025-10-25 17:10:05'),
(31, 'Shift: Afternoon', '', 'shift', '2025-11-04 14:00:00', '2025-11-04 22:00:00', 11, 'scheduled', '{\"employee_id\":\"11\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 17:10:05', '2025-10-25 17:10:05'),
(32, 'Shift: Afternoon', '', 'shift', '2025-11-06 14:00:00', '2025-11-06 22:00:00', 8, 'scheduled', '{\"employee_id\":\"8\",\"shift_type\":\"afternoon\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 17:10:05', '2025-10-25 17:10:05'),
(33, 'Shift: Morning', '', 'shift', '2025-11-08 06:00:00', '2025-11-08 14:00:00', 12, 'scheduled', '{\"employee_id\":\"12\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 17:10:05', '2025-10-25 17:10:05'),
(34, 'Shift: Morning', '', 'shift', '2025-11-09 06:00:00', '2025-11-09 14:00:00', 12, 'scheduled', '{\"employee_id\":\"12\",\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-10-25 17:10:05', '2025-10-25 17:10:05'),
(35, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(36, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(37, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(38, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(39, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(40, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(41, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(42, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(43, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(44, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(45, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(46, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(47, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(48, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(49, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(50, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(51, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(52, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(53, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(54, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(55, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(56, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(57, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(58, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(59, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(60, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(61, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(62, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(63, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(64, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(65, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(66, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(67, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(68, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(69, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(70, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(71, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(72, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(73, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(74, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(75, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(76, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(77, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(78, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(79, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(80, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(81, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(82, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(83, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(84, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(85, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(86, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(87, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-22 06:00:00', '2025-11-22 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(88, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(89, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-17 06:00:00', '2025-11-17 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(90, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-18 06:00:00', '2025-11-18 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(91, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-19 06:00:00', '2025-11-19 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(92, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-20 06:00:00', '2025-11-20 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(93, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-21 06:00:00', '2025-11-21 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(94, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-23 06:00:00', '2025-11-23 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:54:33', '2025-11-18 10:54:33'),
(95, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(96, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(97, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(98, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(99, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(100, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(101, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(102, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(103, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(104, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(105, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(106, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(107, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(108, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(109, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(110, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(111, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(112, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(113, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(114, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(115, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(116, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(117, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(118, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(119, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(120, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(121, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(122, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(123, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(124, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(125, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(126, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(127, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(128, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(129, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(130, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(131, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(132, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(133, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(134, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(135, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(136, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(137, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(138, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(139, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(140, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(141, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(142, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(143, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(144, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(145, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(146, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(147, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(148, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(149, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(150, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(151, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-12 06:00:00', '2025-11-12 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(152, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-13 06:00:00', '2025-11-13 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(153, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(154, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:55:54', '2025-11-18 10:55:54'),
(155, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(156, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(157, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-15 06:00:00', '2025-11-15 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(158, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-16 06:00:00', '2025-11-16 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(159, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(160, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-10 06:00:00', '2025-11-10 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(161, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(162, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-14 06:00:00', '2025-11-14 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:02', '2025-11-18 10:56:02'),
(163, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:14', '2025-11-18 10:56:14'),
(164, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-11 06:00:00', '2025-11-11 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-18 10:56:14', '2025-11-18 10:56:14'),
(165, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(166, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(167, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(168, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(169, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(170, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(171, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(172, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(173, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(174, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(175, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(176, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(177, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(178, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(179, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(180, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(181, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(182, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(183, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(184, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(185, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(186, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(187, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(188, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35');
INSERT INTO `schedules` (`id`, `title`, `description`, `schedule_type`, `start_time`, `end_time`, `assigned_to`, `status`, `meta`, `created_by`, `created_at`, `updated_at`) VALUES
(189, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(190, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(191, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(192, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(193, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(194, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(195, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(196, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(197, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(198, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(199, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(200, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(201, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(202, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(203, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(204, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(205, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(206, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(207, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(208, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(209, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(210, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(211, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(212, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(213, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(214, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(215, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(216, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(217, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(218, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-30 06:00:00', '2025-11-30 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(219, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-24 06:00:00', '2025-11-24 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(220, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-25 06:00:00', '2025-11-25 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(221, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-26 06:00:00', '2025-11-26 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(222, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-27 06:00:00', '2025-11-27 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(223, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-28 06:00:00', '2025-11-28 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(224, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-11-29 06:00:00', '2025-11-29 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-11-22 16:50:35', '2025-11-22 16:50:35'),
(225, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(226, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(227, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(228, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-12 06:00:00', '2025-12-12 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(229, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(230, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(231, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(232, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(233, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(234, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(235, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-12 06:00:00', '2025-12-12 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(236, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(237, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(238, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(239, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(240, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(241, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(242, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(243, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(244, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(245, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(246, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(247, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(248, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(249, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(250, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(251, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(252, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-12 06:00:00', '2025-12-12 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(253, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(254, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(255, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(256, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(257, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(258, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(259, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(260, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(261, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(262, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(263, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(264, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-12 06:00:00', '2025-12-12 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(265, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(266, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(267, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(268, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(269, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(270, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(271, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(272, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(273, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(274, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-09 06:00:00', '2025-12-09 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(275, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(276, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(277, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(278, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(279, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-08 06:00:00', '2025-12-08 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(280, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-10 06:00:00', '2025-12-10 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(281, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-11 06:00:00', '2025-12-11 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(282, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-12 06:00:00', '2025-12-12 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(283, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-13 06:00:00', '2025-12-13 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(284, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-14 06:00:00', '2025-12-14 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-11 12:30:06', '2025-12-11 12:30:06'),
(285, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(286, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(287, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(288, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(289, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(290, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 1, 'scheduled', '{\"employee_id\":1,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(291, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(292, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(293, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(294, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(295, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(296, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 4, 'scheduled', '{\"employee_id\":4,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(297, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(298, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(299, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(300, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(301, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(302, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(303, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(304, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(305, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(306, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(307, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(308, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 9, 'scheduled', '{\"employee_id\":9,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(309, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(310, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(311, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(312, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(313, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(314, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(315, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(316, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(317, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(318, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(319, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(320, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(321, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(322, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(323, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(324, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(325, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(326, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(327, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(328, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(329, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(330, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(331, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(332, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(333, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(334, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(335, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:58', '2025-12-15 08:53:58'),
(336, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(337, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(338, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(339, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(340, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-16 06:00:00', '2025-12-16 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(341, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(342, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(343, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(344, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-21 06:00:00', '2025-12-21 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:53:59', '2025-12-15 08:53:59'),
(345, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 8, 'scheduled', '{\"employee_id\":8,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(346, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-15 06:00:00', '2025-12-15 14:00:00', 10, 'scheduled', '{\"employee_id\":10,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(347, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-17 06:00:00', '2025-12-17 14:00:00', 11, 'scheduled', '{\"employee_id\":11,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(348, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-20 06:00:00', '2025-12-20 14:00:00', 12, 'scheduled', '{\"employee_id\":12,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(349, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-18 06:00:00', '2025-12-18 14:00:00', 13, 'scheduled', '{\"employee_id\":13,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(350, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 14, 'scheduled', '{\"employee_id\":14,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04'),
(351, 'Shift: Morning', 'Auto-generated morning shift', 'shift', '2025-12-19 06:00:00', '2025-12-19 14:00:00', 15, 'scheduled', '{\"employee_id\":15,\"shift_type\":\"morning\",\"break_times\":[],\"responsibilities\":[]}', 1, '2025-12-15 08:54:04', '2025-12-15 08:54:04');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'MUCYO Josue', '0793749143', 'josh@gmail.com', 'Muhanga', '2025-11-28 00:00:00'),
(3, 'NIYONSHUTI Isaac', '0789000303', 'isaac@gmail.com', 'Efotec', '2025-11-29 08:36:55');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_trips`
--

CREATE TABLE `supplier_trips` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `trip_date` date NOT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_trips`
--

INSERT INTO `supplier_trips` (`id`, `supplier_id`, `trip_date`, `status`, `note`, `created_by`, `created_at`) VALUES
(1, 1, '2025-11-28', 'open', 'in cyakabiri', 1, '2025-11-28 13:29:43'),
(2, 1, '2025-11-28', 'open', '', 1, '2025-11-28 21:02:42'),
(3, 2, '2025-11-28', 'open', '', 1, '2025-11-28 21:13:36'),
(4, 2, '2025-11-29', 'open', '', 1, '2025-11-29 07:41:16'),
(5, 2, '2025-11-29', 'open', '', 1, '2025-11-29 08:22:48'),
(6, 2, '2025-11-29', 'open', '', 1, '2025-11-29 08:27:07'),
(7, 3, '2025-11-29', 'open', '', 1, '2025-11-29 08:39:39'),
(8, 2, '2025-11-29', 'open', '', 1, '2025-11-29 08:40:49'),
(9, 3, '2025-11-29', 'open', '', 1, '2025-11-29 08:48:40'),
(10, 3, '2025-11-29', 'open', '', 1, '2025-11-29 08:48:40'),
(11, 3, '2025-11-29', 'open', '', 1, '2025-11-29 11:23:48'),
(12, 1, '2025-11-29', 'open', '', 1, '2025-11-29 21:13:53'),
(13, 1, '2025-12-15', 'open', 'route kibuye', 1, '2025-12-15 10:13:56'),
(14, 3, '2025-12-15', 'open', '', 1, '2025-12-15 10:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_trip_items`
--

CREATE TABLE `supplier_trip_items` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty_dispatched` int(11) NOT NULL,
  `qty_sold` int(11) DEFAULT 0,
  `qty_returned` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_trip_items`
--

INSERT INTO `supplier_trip_items` (`id`, `trip_id`, `product_id`, `qty_dispatched`, `qty_sold`, `qty_returned`, `created_at`) VALUES
(1, 1, 6, 100, 0, 90, '2025-11-28 13:29:43'),
(2, 1, 2, 100, 0, 70, '2025-11-28 13:29:43'),
(3, 1, 5, 10, 0, 0, '2025-11-28 13:29:43'),
(4, 2, 2, 80, 0, 30, '2025-11-28 21:02:42'),
(5, 2, 6, 90, 0, 40, '2025-11-28 21:02:42'),
(6, 3, 6, 40, 0, 10, '2025-11-28 21:13:36'),
(7, 4, 6, 10, 0, 5, '2025-11-29 07:41:16'),
(8, 4, 2, 30, 0, 20, '2025-11-29 07:41:16'),
(9, 6, 6, 5, 0, 0, '2025-11-29 08:27:07'),
(10, 6, 2, 1, 0, 0, '2025-11-29 08:27:07'),
(11, 7, 2, 19, 0, 5, '2025-11-29 08:39:39'),
(12, 8, 5, 10, 0, 0, '2025-11-29 08:40:49'),
(13, 11, 2, 6, 0, 1, '2025-11-29 11:23:48'),
(14, 13, 6, 100, 0, 50, '2025-12-15 10:13:56'),
(15, 14, 2, 10, 0, 0, '2025-12-15 10:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT 'Bakery Management System',
  `currency` varchar(10) DEFAULT 'Frw',
  `timezone` varchar(50) DEFAULT 'Africa/Kigali',
  `date_format` varchar(20) DEFAULT 'Y-m-d',
  `low_stock_threshold` int(11) DEFAULT 10,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `business_name`, `currency`, `timezone`, `date_format`, `low_stock_threshold`, `updated_at`) VALUES
(1, 'Blessing Bakery', 'Frw', 'Africa/Kigali', 'd/m/Y', 10, '2025-11-08 12:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `time_off_requests`
--

CREATE TABLE `time_off_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_off_requests`
--

INSERT INTO `time_off_requests` (`id`, `employee_id`, `start_date`, `end_date`, `reason`, `status`, `created_at`) VALUES
(7, 1, '2025-11-03', '2025-11-03', 'Seed request', 'approved', '2025-10-25 19:55:40'),
(8, 9, '2025-10-25', '2025-10-31', 'sickness', 'pending', '2025-10-25 19:56:16'),
(13, 9, '2025-10-25', '2025-10-31', 'seed', 'pending', '2025-10-25 20:03:00'),
(14, 1, '2025-11-03', '2025-11-03', 'Seed request', 'approved', '2025-10-25 20:05:10'),
(15, 8, '2025-10-25', '2025-10-25', '', 'pending', '2025-10-25 20:07:45'),
(16, 1, '2025-11-03', '2025-11-03', 'Seed request', 'pending', '2025-10-25 20:11:14'),
(17, 9, '2025-10-31', '2025-11-01', '122', 'pending', '2025-10-25 20:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Master', 'm.joshua250@outlook.com', '0783038832', '$2y$10$/ITHWO1zJpoZsniuQObokOlVvrJV4Dfcpdi7ElKs30DmELcTjoidq', 'admin', '2025-10-11 16:13:18'),
(2, 'Sabine', 'sabineirak@gmail.com', '+250790531998', '$2y$10$/ITHWO1zJpoZsniuQObokOlVvrJV4Dfcpdi7ElKs30DmELcTjoidq', 'staff', '2025-10-11 16:13:18'),
(4, 'UWINEZA Faustine', 'uwinezafofo@gmail.com', NULL, '$2y$10$bgG.1ZGaKnSszsnOk2IvceAJy34cTiu/tPI6/RuGKDMGufhrFh1FW', 'staff', '2025-11-09 17:49:05'),
(5, 'MANZI Robby', 'robby@gmail.com', NULL, '$2y$10$dWq11amH7DzxixDMYkSgwuLxbhAZmUyChkFRz1FGK1Wd5ZOBlhxnS', 'manager', '2025-11-09 17:54:16'),
(6, 'habimana jean', 'habimana@gmail.com', NULL, '$2y$10$kwkYnmQN7NPKV7rQdcokvejl4dbcxI5OMRKTFiWFUGAQ0L.ym4d46', 'staff', '2025-11-12 12:59:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `sms_notifications` tinyint(1) DEFAULT 0,
  `theme` varchar(20) DEFAULT 'dark',
  `language` varchar(10) DEFAULT 'en',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `email_notifications`, `sms_notifications`, `theme`, `language`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'dark', 'en', '2025-11-08 12:30:33', '2025-11-30 10:49:30'),
(2, 2, 1, 0, 'dark', 'rw', '2025-11-08 12:30:33', '2025-11-17 15:57:06'),
(3, 5, 1, 0, 'dark', 'en', '2025-11-15 12:32:54', '2025-11-15 12:32:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_orders`
--
ALTER TABLE `material_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_order_items`
--
ALTER TABLE `material_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `production_batches`
--
ALTER TABLE `production_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_materials`
--
ALTER TABLE `production_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_records`
--
ALTER TABLE `production_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_daily_stats`
--
ALTER TABLE `product_daily_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_date_unique` (`product_id`,`stat_date`);

--
-- Indexes for table `product_material_plans`
--
ALTER TABLE `product_material_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe_items`
--
ALTER TABLE `recipe_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_schedules_type` (`schedule_type`),
  ADD KEY `idx_schedules_status` (`status`),
  ADD KEY `idx_schedules_start_time` (`start_time`),
  ADD KEY `idx_schedules_assigned_to` (`assigned_to`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_trips`
--
ALTER TABLE `supplier_trips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_trip_items`
--
ALTER TABLE `supplier_trip_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_off_requests`
--
ALTER TABLE `time_off_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1100;

--
-- AUTO_INCREMENT for table `material_orders`
--
ALTER TABLE `material_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `material_order_items`
--
ALTER TABLE `material_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `production_batches`
--
ALTER TABLE `production_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_materials`
--
ALTER TABLE `production_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_records`
--
ALTER TABLE `production_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_daily_stats`
--
ALTER TABLE `product_daily_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_material_plans`
--
ALTER TABLE `product_material_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `recipe_items`
--
ALTER TABLE `recipe_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supplier_trips`
--
ALTER TABLE `supplier_trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `supplier_trip_items`
--
ALTER TABLE `supplier_trip_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `time_off_requests`
--
ALTER TABLE `time_off_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `production_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `production_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `time_off_requests`
--
ALTER TABLE `time_off_requests`
  ADD CONSTRAINT `time_off_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
