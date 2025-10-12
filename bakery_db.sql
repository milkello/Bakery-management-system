-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 09:02 PM
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
  `status` enum('present','absent') NOT NULL DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'm@gmail.com', NULL, 'monthly', 100.00, NULL, NULL, '2025-10-11 17:09:52', 'Active', 'admin', 0.0),
(2, NULL, 'hvj', 'gcb', NULL, NULL, '123', 'm@gmail.com', NULL, 'monthly', 78.00, NULL, NULL, '2025-10-12 13:20:00', 'Active', '', 0.0),
(3, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 100.00, NULL, NULL, '2025-10-12 18:31:02', 'Active', '', 0.0),
(4, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:41:03', 'Active', '', 0.0),
(5, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:42:48', 'Active', '', 0.0),
(6, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:43:41', 'Active', '', 0.0),
(7, NULL, 'MUCYO ', 'Josue', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:45:16', 'Active', '', 0.0),
(8, NULL, 'Joe', 'Kesh', NULL, NULL, '123', 'joekesh001@gmail.com', NULL, 'monthly', 1000.00, NULL, NULL, '2025-10-12 18:53:22', 'Active', '', 0.0);

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
(1, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 16:57:40'),
(2, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:08:42'),
(3, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:10:54'),
(4, 1, 'Product sold', 'product_sold', 46, NULL, 'Bread', '2025-10-12 17:10:54'),
(5, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:10:54'),
(6, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:11:19'),
(7, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:14:30'),
(8, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:14:30'),
(9, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:14:30'),
(10, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:14:30'),
(11, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:18:16'),
(12, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:18:16'),
(13, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:18:16'),
(14, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:18:16'),
(15, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:18:51'),
(16, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:18:51'),
(17, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:18:51'),
(18, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:18:51'),
(19, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:19:19'),
(20, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:19:19'),
(21, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:19:19'),
(22, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:19:19'),
(23, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:21:14'),
(24, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:21:14'),
(25, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:21:14'),
(26, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:21:14'),
(27, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:21:43'),
(28, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:21:43'),
(29, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:21:43'),
(30, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:21:43'),
(31, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:41:08'),
(32, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:41:08'),
(33, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:41:08'),
(34, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:41:08'),
(35, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 17:41:11'),
(36, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 17:41:11'),
(37, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 17:41:11'),
(38, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 17:41:11'),
(39, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 18:02:10'),
(40, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 18:02:10'),
(41, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 18:02:10'),
(42, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 18:02:10'),
(43, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 18:02:10'),
(44, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 18:02:10'),
(45, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 18:02:10'),
(46, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 18:02:10'),
(47, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 18:47:05'),
(48, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 18:47:05'),
(49, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 18:47:05'),
(50, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 18:47:05'),
(51, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 18:52:03'),
(52, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 18:52:03'),
(53, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 18:52:03'),
(54, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 18:52:03'),
(55, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 18:52:08'),
(56, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 18:52:08'),
(57, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 18:52:08'),
(58, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 18:52:08'),
(59, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 19:18:24'),
(60, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 19:18:24'),
(61, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 19:18:24'),
(62, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 19:18:24'),
(63, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 19:18:27'),
(64, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 19:18:27'),
(65, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 19:18:27'),
(66, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 19:18:27'),
(67, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 19:23:41'),
(68, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 19:23:41'),
(69, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 19:23:41'),
(70, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 19:23:41'),
(71, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 19:40:02'),
(72, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 19:40:02'),
(73, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 19:40:02'),
(74, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 19:40:02'),
(75, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 19:53:23'),
(76, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 19:53:23'),
(77, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 19:53:23'),
(78, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 19:53:23'),
(79, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 20:16:06'),
(80, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 20:16:06'),
(81, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 20:16:06'),
(82, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 20:16:06'),
(83, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 20:20:13'),
(84, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 20:20:13'),
(85, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 20:20:13'),
(86, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 20:20:13'),
(87, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 20:20:35'),
(88, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 20:20:35'),
(89, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 20:20:35'),
(90, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 20:20:35'),
(91, 1, 'Raw material added', 'raw_material', 50, NULL, 'Flour', '2025-10-12 20:48:20'),
(92, 1, 'Product sold', 'product_sold', NULL, 45.99, 'Bread', '2025-10-12 20:48:20'),
(93, 1, 'Product made', 'product_made', 20, NULL, 'Chocolate Cake', '2025-10-12 20:48:20'),
(94, 1, 'Special discount applied', 'special', NULL, NULL, 'Black Friday Sale', '2025-10-12 20:48:20');

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
(1, 'production', 'Produced 1000 units of product ID 1', NULL, 0, NULL, '2025-10-11 17:22:33'),
(2, 'production', 'Produced 100 units of product ID 1', NULL, 0, NULL, '2025-10-11 17:24:12'),
(3, 'sale', 'Sold 100 units of Product ID 1 successfully.', NULL, 0, NULL, '2025-10-12 13:07:44');

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
(4, 1, 100.00, '', 1, '2025-10-11 15:24:12');

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
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
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

INSERT INTO `products` (`id`, `product_code`, `name`, `sku`, `price`, `unit`, `created_at`, `stock`) VALUES
(1, NULL, 'Cake', 'CK-001', 200.00, 'piece', '2025-10-11 17:11:03', 780),
(2, NULL, 'Bread', 'BR-001', 100.00, 'piece', '2025-10-12 13:20:24', 100);

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
(1, 'isukari', 'kg', '', 2000.0000, 10.000, 5.000, NULL, NULL, '2025-10-11 17:10:27'),
(2, 'ifarini', 'kg', '', 1000.0000, 9.000, 10.000, NULL, NULL, '2025-10-11 17:12:32');

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
(1, 1, 1.000, '2025-10-11 17:12:13'),
(2, 1, 1.000, '2025-10-11 17:12:55');

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
(1, 1, 1, 10, 'kg'),
(2, 2, 1, 10, 'kg'),
(3, 2, 2, 10, 'kg');

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
  `sold_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `qty`, `unit_price`, `total_price`, `customer_type`, `payment_method`, `sold_by`, `created_by`, `created_at`) VALUES
(1, 1, 100, 100.00, 1000.00, 'Regular', 'Cash', 1, 1, '2025-10-12 13:07:44'),
(2, 2, 100, 100.00, 100.00, 'Regular', 'Cash', 1, 1, '2025-10-11 13:07:44');

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', NULL, 'admin123', 'admin', '2025-10-11 16:13:18');

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
-- Indexes for table `products`
--
ALTER TABLE `products`
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recipe_items`
--
ALTER TABLE `recipe_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `production_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `production_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
