-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2026 at 03:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `safina_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `time_slot` varchar(20) DEFAULT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `manual_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `final_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=pending,1=confirmed,2=cancelled',
  `meta_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_values`)),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `time_slot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `service_id`, `user_id`, `counter_id`, `name`, `phone`, `email`, `address`, `emergency_contact`, `check_in_date`, `check_in_time`, `check_out_date`, `check_out_time`, `date`, `start_time`, `end_time`, `time_slot`, `promo_code`, `base_price`, `discount_amount`, `manual_discount`, `total_price`, `final_price`, `status`, `meta_values`, `created_by`, `updated_by`, `time_slot_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Md Moshiur Rahman', '01547656787', 'shetu887@gmail.com', 'Mirpur, Dhaka', '015685351993', '2026-06-24', NULL, '2026-06-24', NULL, '2026-06-24', '08:00:00', '18:00:00', 'Full Day', 'ABCD1', NULL, 200.00, 0.00, 1000.00, 800.00, 1, '{\"Number of Guests\":{\"label\":\"Number of Guests\",\"value\":\"2\",\"file_path\":null},\"Marital Status\":{\"label\":\"Marital Status\",\"value\":\"1\",\"file_path\":null},\"NID\\/Birth Certificate\":{\"label\":\"NID\\/Birth Certificate\",\"value\":\"\",\"file_path\":\"booking_files\\/zuc692iidrFDDJhkNh2GEidA3ly5gPjfLGZBQUuN.webp\"},\"Marriege Certificate\":{\"label\":\"Marriege Certificate\",\"value\":\"\",\"file_path\":\"booking_files\\/083NvxY7qgv70kkGB5aS4scGHgv1fL1YJfvdd5GY.jpg\"}}', 1, 1, 1, '2026-06-24 04:31:03', '2026-06-24 04:31:50'),
(2, 1, 1, 1, 'shetu shetu', '01547656787', 'shetu887@gmail.com', 'asefsdfsdfcsdfd', '01568535199', '2026-06-26', NULL, '2026-06-26', NULL, '2026-06-25', '08:00:00', '07:30:00', 'Full Day Night', NULL, NULL, 0.00, 0.00, 2000.00, 2000.00, 1, '{\"Number of Guests\":{\"label\":\"Number of Guests\",\"value\":\"2\",\"file_path\":null},\"Marital Status\":{\"label\":\"Marital Status\",\"value\":\"0\",\"file_path\":null},\"NID\\/Birth Certificate\":{\"label\":\"NID\\/Birth Certificate\",\"value\":\"\",\"file_path\":\"booking_files\\/3eIASWupSDiUOouJHY0IoP22i0QzaH6NMyOxD1KL.webp\"}}', 1, 1, 4, '2026-06-24 09:28:31', '2026-06-24 23:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `booking_cash_handovers`
--

CREATE TABLE `booking_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `counter_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_cash_handovers`
--

INSERT INTO `booking_cash_handovers` (`id`, `user_id`, `counter_id`, `amount`, `status`, `receiver_user_id`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `remark`, `business_date`, `requested_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2000.00, 'approved', 1, 1, '2026-06-25 00:04:32', NULL, NULL, NULL, '2026-06-25', '2026-06-24 23:41:21', '2026-06-24 23:41:21', '2026-06-25 00:04:32'),
(3, 1, 1, 800.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24', '2026-06-25 00:04:38', '2026-06-25 00:04:38', '2026-06-25 00:04:38');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `remarks`) VALUES
(1, 'ষ্টেশনারী', NULL),
(2, 'কম্পিউটার ও কম্পিউটার এক্সেসরিজ', NULL),
(3, 'ল্যাপটপ', NULL),
(4, 'প্রিন্টার ও স্ক্যানার', NULL),
(5, 'ইলেকট্রনিক্স', NULL),
(6, 'ক্যামেরা', NULL),
(7, 'বার্ষিক প্রতিবেদন', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_meta_fields`
--

CREATE TABLE `category_meta_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_category_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_type` tinyint(3) UNSIGNED NOT NULL COMMENT '0=text,1=number,2=select,3=date',
  `required` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `options` text DEFAULT NULL COMMENT 'JSON for select options',
  `conditional_field` varchar(100) DEFAULT NULL,
  `conditional_value` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_resource` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `help_text` text DEFAULT NULL,
  `resource_key` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_meta_fields`
--

INSERT INTO `category_meta_fields` (`id`, `service_category_id`, `field_name`, `field_type`, `required`, `options`, `conditional_field`, `conditional_value`, `sort_order`, `is_resource`, `help_text`, `resource_key`, `created_at`, `updated_at`) VALUES
(1, 1, 'Number of Guests', 1, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-24 03:41:09', '2026-06-24 03:41:09'),
(2, 1, 'NID/Birth Certificate', 4, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-24 03:42:55', '2026-06-24 03:42:55'),
(3, 1, 'Marital Status', 2, 1, '[\"Single\",\"Married\"]', NULL, NULL, 0, 0, NULL, NULL, '2026-06-24 03:43:39', '2026-06-24 03:43:39'),
(4, 1, 'Marriege Certificate', 4, 1, NULL, 'Marital Status', '1', 0, 0, NULL, NULL, '2026-06-24 03:44:10', '2026-06-24 03:44:10'),
(5, 2, 'Number of Guests', 1, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-24 04:19:50', '2026-06-24 04:19:50'),
(6, 2, 'NID/Birth Certificate', 4, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-24 04:20:01', '2026-06-24 04:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active,0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counters`
--

INSERT INTO `counters` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Service Counter 1', 1, '2026-06-24 04:21:05', '2026-06-24 04:21:05');

-- --------------------------------------------------------

--
-- Table structure for table `counter_services`
--

CREATE TABLE `counter_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `counter_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counter_services`
--

INSERT INTO `counter_services` (`id`, `counter_id`, `service_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(3, 1, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `counter_user`
--

CREATE TABLE `counter_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `counter_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `counter_user`
--

INSERT INTO `counter_user` (`id`, `counter_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'General Department'),
(2, 'demo');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `name`) VALUES
(1, 'Global Admin'),
(2, 'demo');

-- --------------------------------------------------------

--
-- Table structure for table `discount_rules`
--

CREATE TABLE `discount_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=fixed,1=percentage',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0=inactive,1=active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discount_rules`
--

INSERT INTO `discount_rules` (`id`, `name`, `code`, `category_id`, `discount_type`, `amount`, `service_id`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Wooden Villa Discount', 'ABCD1', 1, 0, 200.00, 1, '2026-06-23', '2026-06-30', 1, '2026-06-24 04:27:42', '2026-06-24 04:27:42');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(150) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gates`
--

CREATE TABLE `gates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gates`
--

INSERT INTO `gates` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ticket Counter 1', 1, '2026-06-22 02:19:02', '2026-06-25 00:35:45');

-- --------------------------------------------------------

--
-- Table structure for table `gate_tickets`
--

CREATE TABLE `gate_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gate_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gate_tickets`
--

INSERT INTO `gate_tickets` (`id`, `gate_id`, `ticket_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, NULL),
(2, 1, 1, NULL, NULL),
(3, 1, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gear_items`
--

CREATE TABLE `gear_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `available_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gear_items`
--

INSERT INTO `gear_items` (`id`, `name`, `total_stock`, `available_stock`, `created_at`, `updated_at`) VALUES
(1, 'T Shirt', 100, 98, '2026-07-02 02:18:12', '2026-07-02 09:58:37'),
(2, 'Tube', 50, 49, '2026-07-02 02:18:44', '2026-07-02 03:48:54'),
(3, 'Cap', 200, 199, '2026-07-02 02:19:07', '2026-07-02 03:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_name` varchar(200) DEFAULT NULL,
  `model` varchar(200) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `measuring_unit` varchar(30) NOT NULL,
  `low_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `attributes` text DEFAULT NULL,
  `combination` text DEFAULT NULL,
  `additional` text DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `item_img` varchar(250) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `brand_name`, `model`, `name`, `category_id`, `measuring_unit`, `low_stock`, `remarks`, `attributes`, `combination`, `additional`, `status`, `item_img`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'পেপার ওয়ান', NULL, 'পেপার এ-৪', 1, 'Rim', 20, NULL, '[{\"name\":\"Weight\",\"values\":\"80|60\"}]', '[]', '[]', 1, '1699849261_paperonea4copierpaper80gsm-paperone-77b85-296183.jpg', 1, 1, '2023-11-13 04:21:01', '2025-10-01 08:53:21'),
(2, 'ডাবল এ', NULL, 'লিগ্যাল পেপার', 1, 'Rim', 5, NULL, '[{\"name\":\"Weight\",\"values\":\"80|60\"}]', '[]', '[]', 1, '1699849418_paperonea4copierpaper80gsm-paperone-77b85-296183.jpg', 1, 1, '2023-11-13 04:23:38', '2024-05-19 15:56:45'),
(3, 'অন্যান্য', NULL, 'ফাইল ট্যাগ পিনযুক্ত সুতা', 1, 'Pieces', 2, NULL, '[]', '[]', '[]', 1, '1699849521_41wiegoytil.jpg', 1, 1, '2023-11-13 04:25:21', '2024-05-05 21:32:05'),
(4, 'অন্যান্য', NULL, 'সুতার গুটি', 1, 'Pieces', 50, NULL, '[]', '[]', '[]', 1, '1699849647_1590227229.jpg', 1, 1, '2023-11-13 04:27:27', '2023-11-13 04:27:27'),
(5, 'অন্যান্য', NULL, 'ফাইল বাঁধার ফিতা', 1, 'Box', 2, NULL, '[]', '[]', '[]', 1, '1699850260_images.jpg', 1, 1, '2023-11-13 04:37:40', '2024-05-05 21:32:45'),
(6, NULL, NULL, 'ভোমর', 1, 'Pieces', 5, NULL, '[]', '[]', '[]', 1, '1700367850_6.jpg', 1, 1, '2023-11-19 04:24:10', '2024-05-19 16:02:28'),
(7, NULL, NULL, 'জেমস ক্লিপ (প্লাস্টিক কভারযুক্ত)', 1, 'Box', 5, NULL, '[]', '[]', '[]', 1, '1700368101_7.jpg', 1, 1, '2023-11-19 04:28:21', '2024-05-19 16:04:06'),
(8, NULL, NULL, 'পেন্সিল', 1, 'Pieces', 10, NULL, '[]', '[]', '[]', 1, '1700368409_8.jpg', 1, 1, '2023-11-19 04:33:29', '2024-05-14 15:43:01'),
(9, NULL, NULL, 'Towel', 1, 'Pieces', 20, NULL, '[{\"name\":\"Color\",\"values\":\"Black|Red|Blue\"},{\"name\":\"Size\",\"values\":\"M|XL|S\"}]', '[]', '[{\"name\":\"test1\",\"value\":\"value1\"},{\"name\":\"test2\",\"value\":\"value2\"},{\"name\":\"test3\",\"value\":\"value3\"}]', 1, NULL, 1, 1, '2025-10-18 01:50:26', '2025-10-18 01:50:26'),
(10, 'dell', NULL, 'Laptop', 3, 'Pieces', 4, NULL, '[]', '[]', '[]', 1, NULL, 1, 1, '2025-10-18 04:02:13', '2025-10-18 04:02:13'),
(11, NULL, NULL, 'Paper a4', 1, 'Rim', 10, NULL, '[{\"name\":\"Size\",\"values\":\"80 GSM|60 GSM|70 GSM\"},{\"name\":\"Color\",\"values\":\"Red|White|Yellow\"}]', '[]', '[{\"name\":\"test1\",\"value\":\"value1\"}]', 1, NULL, 1, 1, '2025-10-26 07:10:46', '2025-10-26 07:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `item_pricings`
--

CREATE TABLE `item_pricings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_type` enum('locker','gear') NOT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_unit_minutes` int(11) NOT NULL DEFAULT 30,
  `extra_unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_pricings`
--

INSERT INTO `item_pricings` (`id`, `item_type`, `item_id`, `duration_minutes`, `base_price`, `extra_unit_minutes`, `extra_unit_price`, `created_at`, `updated_at`) VALUES
(1, 'locker', NULL, 60, 30.00, 30, 20.00, '2026-07-02 02:23:59', '2026-07-02 02:23:59'),
(2, 'gear', 1, 60, 20.00, 30, 10.00, '2026-07-02 02:25:19', '2026-07-02 02:25:19'),
(3, 'gear', 2, 60, 40.00, 30, 20.00, '2026-07-02 02:25:36', '2026-07-02 02:25:36'),
(4, 'gear', 3, 60, 20.00, 30, 10.00, '2026-07-02 02:26:21', '2026-07-02 02:26:21');

-- --------------------------------------------------------

--
-- Table structure for table `locker_gear_cash_handovers`
--

CREATE TABLE `locker_gear_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `locker_gear_counter_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locker_gear_counters`
--

CREATE TABLE `locker_gear_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locker_gear_counters`
--

INSERT INTO `locker_gear_counters` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Locker Counter 1', 'active', '2026-07-02 02:14:25', '2026-07-02 02:14:25');

-- --------------------------------------------------------

--
-- Table structure for table `locker_gear_counter_user`
--

CREATE TABLE `locker_gear_counter_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `locker_gear_counter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locker_gear_counter_user`
--

INSERT INTO `locker_gear_counter_user` (`user_id`, `locker_gear_counter_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locker_gear_tickets`
--

CREATE TABLE `locker_gear_tickets` (
  `ticket_number` varchar(50) NOT NULL,
  `qr_code` varchar(100) NOT NULL,
  `status` enum('checked_in','checked_out') NOT NULL DEFAULT 'checked_in',
  `entry_time` timestamp NULL DEFAULT NULL,
  `exit_time` timestamp NULL DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `extra_collected_counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `extra_collected_at` timestamp NULL DEFAULT NULL,
  `locker_gear_counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locker_gear_tickets`
--

INSERT INTO `locker_gear_tickets` (`ticket_number`, `qr_code`, `status`, `entry_time`, `exit_time`, `total_amount`, `extra_amount`, `extra_collected_by`, `extra_collected_counter_id`, `extra_collected_at`, `locker_gear_counter_id`, `created_by`, `created_at`, `updated_at`) VALUES
('LG20260702119F', 'LGQR20260702082638E111AB', 'checked_in', '2026-07-02 02:26:38', NULL, 90.00, 0.00, NULL, NULL, NULL, 1, 1, '2026-07-02 02:26:38', '2026-07-02 02:26:38'),
('LG202607021D57', 'LGQR20260702094917D11D63', 'checked_in', '2026-07-02 03:49:17', NULL, 50.00, 0.00, NULL, NULL, NULL, 1, 1, '2026-07-02 03:49:17', '2026-07-02 03:49:17'),
('LG20260702CD01', 'LGQR2026070208575425CD0E', 'checked_out', '2026-07-02 02:57:54', '2026-07-02 03:48:54', 90.00, 0.00, NULL, NULL, NULL, 1, 1, '2026-07-02 02:57:54', '2026-07-02 03:48:54'),
('LG20260702FBC6', 'LGQR20260702155837D4FBD3', 'checked_in', '2026-07-02 09:58:37', NULL, 50.00, 0.00, NULL, NULL, NULL, 1, 1, '2026-07-02 09:58:37', '2026-07-02 09:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `locker_gear_ticket_items`
--

CREATE TABLE `locker_gear_ticket_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `item_type` enum('locker','gear') NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locker_gear_ticket_items`
--

INSERT INTO `locker_gear_ticket_items` (`id`, `ticket_number`, `item_type`, `item_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 'LG20260702119F', 'locker', 1, 1, '2026-07-02 02:26:38', '2026-07-02 02:26:38'),
(2, 'LG20260702119F', 'gear', 1, 1, '2026-07-02 02:26:38', '2026-07-02 02:26:38'),
(3, 'LG20260702119F', 'gear', 2, 1, '2026-07-02 02:26:38', '2026-07-02 02:26:38'),
(4, 'LG20260702CD01', 'locker', 2, 1, '2026-07-02 02:57:54', '2026-07-02 02:57:54'),
(5, 'LG20260702CD01', 'gear', 2, 1, '2026-07-02 02:57:54', '2026-07-02 02:57:54'),
(6, 'LG20260702CD01', 'gear', 3, 1, '2026-07-02 02:57:54', '2026-07-02 02:57:54'),
(7, 'LG202607021D57', 'locker', 3, 1, '2026-07-02 03:49:17', '2026-07-02 03:49:17'),
(8, 'LG202607021D57', 'gear', 3, 1, '2026-07-02 03:49:17', '2026-07-02 03:49:17'),
(9, 'LG20260702FBC6', 'locker', 2, 1, '2026-07-02 09:58:37', '2026-07-02 09:58:37'),
(10, 'LG20260702FBC6', 'gear', 1, 1, '2026-07-02 09:58:37', '2026-07-02 09:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `locker_items`
--

CREATE TABLE `locker_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` enum('available','occupied') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locker_items`
--

INSERT INTO `locker_items` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Locker 1', 'occupied', '2026-07-02 02:15:20', '2026-07-02 02:26:38'),
(2, 'Locker 2', 'occupied', '2026-07-02 02:15:27', '2026-07-02 09:58:37'),
(3, 'Locker 3', 'occupied', '2026-07-02 02:15:39', '2026-07-02 03:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(250) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2024_08_25_034926_create_permissions_table', 1),
(4, '2024_08_25_034957_create_roles_table', 1),
(5, '2024_08_25_035113_create_role_permissions_table', 1),
(6, '2024_08_25_035354_create_designations_table', 1),
(7, '2024_08_25_035355_create_departments_table', 1),
(8, '2024_08_25_035433_create_users_table', 1),
(9, '2024_08_25_035434_create_password_resets_table', 1),
(10, '2024_08_25_035434_create_site_settings_table', 1),
(11, '2025_03_08_004947_create_suppliers_table', 1),
(12, '2025_03_09_035354_create_categories_table', 1),
(13, '2025_03_09_035354_create_warehouses_table', 1),
(14, '2025_03_09_114816_create_items_table', 1),
(19, '2025_03_09_114947_create_purchases_table', 2),
(20, '2025_03_09_115014_create_purchase_items_table', 2),
(21, '2025_04_05_124450_create_stock_ins_table', 3),
(25, '2025_04_05_124620_create_purposes_table', 4),
(26, '2025_04_05_124621_create_requisitions_table', 4),
(28, '2025_04_05_124625_create_requisition_items_table', 5),
(29, '2025_04_09_124625_create_mrs_items_table', 6),
(30, '2025_04_05_120612_create_purchase_transactions_table', 7),
(48, '2026_04_06_000001_create_gates_table', 8),
(49, '2026_04_06_000002_create_user_gates_table', 8),
(50, '2026_04_06_000003_create_ticket_categories_table', 8),
(51, '2026_04_06_000004_create_tickets_table', 8),
(52, '2026_04_06_000005_create_gate_tickets', 8),
(53, '2026_04_06_000006_create_ticket_sales_table', 8),
(54, '2026_04_06_000007_create_ticket_cash_handovers_table', 8),
(55, '2026_05_06_000001_create_water_park_counters_table', 8),
(56, '2026_05_06_000002_create_water_park_settings_table', 8),
(57, '2026_05_06_000003_create_water_park_tickets_table', 8),
(58, '2026_05_06_000004_create_water_park_cash_handovers_table', 8),
(60, '2026_06_06_000001_create_vehicles', 9),
(61, '2026_06_06_000002_create_parking_counters_table', 10),
(62, '2026_06_06_000003_create_parking_counter_user_table', 10),
(64, '2026_06_06_000005_add_parking_counter_permissions', 11),
(65, '2026_06_06_000004_create_parking_tickets', 12),
(66, '2026_06_06_000006_create_parking_ticket_payments_table', 13),
(67, '2026_06_06_000007_create_parking_cash_handovers_table', 14),
(68, '2026_06_07_000001_create_packages_table', 15),
(70, '2026_06_07_000002_create_package_items_table', 16),
(71, '2026_06_07_000003_create_package_counters_table', 17),
(72, '2026_06_07_000004_create_package_counter_user_table', 17),
(73, '2026_06_07_000005_create_package_counter_packages_table', 18),
(74, '2026_06_07_000006_create_package_bookings_table', 19),
(75, '2026_06_07_000007_create_package_booking_items_table', 19),
(76, '2026_06_07_000008_create_package_cash_handovers_table', 19),
(86, '2026_06_09_000001_create_service_categories_table', 24),
(87, '2026_06_09_000002_create_services_table', 24),
(88, '2026_06_09_000003_create_category_meta_fields_table', 24),
(89, '2026_06_09_000004_create_time_slots_table', 24),
(90, '2026_06_09_000005_create_counters_table', 24),
(91, '2026_06_09_000006_create_pricing_rules_table', 24),
(92, '2026_06_09_000007_create_discount_rules_table', 24),
(93, '2026_06_09_000008_create_counter_user_table', 24),
(94, '2026_06_09_000009_create_counter_services', 24),
(95, '2026_06_09_0000010_create_bookings_table', 25),
(96, '2026_06_09_0000011_create_booking_cash_handovers_table', 25),
(97, '2026_06_10_000001_create_locker_gear_counters_table', 26),
(98, '2026_06_10_000002_create_locker_items_table', 26),
(99, '2026_06_10_000003_create_gear_items_table', 26),
(100, '2026_06_10_000004_create_item_pricings_table', 26),
(101, '2026_06_10_000005_create_locker_gear_tickets_table', 26),
(102, '2026_06_10_000006_create_locker_gear_cash_handovers_table', 26);

-- --------------------------------------------------------

--
-- Table structure for table `mrs_items`
--

CREATE TABLE `mrs_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `requisition_item_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `combinations` text DEFAULT NULL,
  `admin_comments` text DEFAULT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `measuring_unit` varchar(50) NOT NULL,
  `item_condition` tinyint(4) NOT NULL DEFAULT 0,
  `received_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mrs_items`
--

INSERT INTO `mrs_items` (`id`, `requisition_id`, `requisition_item_id`, `item_id`, `user_id`, `name`, `combinations`, `admin_comments`, `received_by`, `warehouse_id`, `quantity`, `measuring_unit`, `item_condition`, `received_date`, `created_at`, `updated_at`) VALUES
(1, 4, 7, 10, 1, 'Laptop', '\"\"', 'test for return item entry', 1, 1, 1.00, 'Pieces', 0, '2025-10-23', '2025-10-23 09:12:38', '2025-10-23 09:12:38'),
(2, 6, 12, 10, 1, 'Laptop', '\"\"', 'test', 1, 2, 1.00, 'Pieces', 0, '2025-10-26', '2025-10-26 08:19:41', '2025-10-26 08:19:41');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `default_person` int(11) NOT NULL DEFAULT 1,
  `extra_person_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active,0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `base_price`, `default_person`, `extra_person_price`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Family Package', 1200.00, 4, 200.00, 1, '2026-06-22 03:35:18', '2026-06-22 04:41:44'),
(6, 'Student Package', 1500.00, 7, 250.00, 1, '2026-06-22 04:14:36', '2026-06-22 04:14:36');

-- --------------------------------------------------------

--
-- Table structure for table `package_bookings`
--

CREATE TABLE `package_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `package_counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_person` int(11) NOT NULL DEFAULT 1,
  `base_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_person` int(11) NOT NULL DEFAULT 0,
  `extra_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `qr_code` varchar(64) DEFAULT NULL,
  `booking_token` varchar(64) DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `ticket_status` enum('draft','printed') NOT NULL DEFAULT 'draft',
  `ticket_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ticket_data`)),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_bookings`
--

INSERT INTO `package_bookings` (`id`, `package_id`, `package_counter_id`, `date`, `quantity`, `total_person`, `base_amount`, `extra_person`, `extra_amount`, `final_amount`, `qr_code`, `booking_token`, `is_used`, `ticket_status`, `ticket_data`, `used_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 6, 2, '2026-06-22', 1, 8, 1500.00, 1, 250.00, 1750.00, 'PKG6A39239DBE766C68E0A5A', 'PKG6A39239DBE76CF99EA3F1', 0, 'printed', '{\"tickets\":[{\"ticket_id\":1,\"ticket_name\":\"Nagordola\",\"ticket_token\":\"PKT6A39239DBF2716A6CC371\",\"is_used\":false,\"used_at\":null,\"source\":\"package\"},{\"ticket_id\":2,\"ticket_name\":\"Rollar Coster\",\"ticket_token\":\"PKT6A39239DBF47E32654C9B\",\"is_used\":false,\"used_at\":null,\"source\":\"package\"},{\"ticket_id\":3,\"ticket_name\":\"Gate Entry\",\"ticket_token\":\"PKT6A39239DBF6C4A1988B27\",\"is_used\":false,\"used_at\":null,\"source\":\"package\"}]}', NULL, 1, '2026-06-22 05:59:25', '2026-06-22 06:24:05'),
(2, 5, 3, '2026-06-22', 1, 6, 1200.00, 2, 400.00, 1600.00, 'PKG6A392BAC86F7E699438E2', 'PKG6A392BAC86F8503418819', 0, 'draft', '{\"tickets\":[{\"ticket_id\":1,\"ticket_name\":\"Nagordola\",\"ticket_token\":\"PKT6A392BAC87FE0C40DEEFF\",\"is_used\":true,\"used_at\":\"2026-06-22 12:37:28\",\"source\":\"package\"},{\"ticket_id\":3,\"ticket_name\":\"Gate Entry\",\"ticket_token\":\"PKT6A392BAC885FCD6D97A93\",\"is_used\":false,\"used_at\":null,\"source\":\"package\"}]}', NULL, 1, '2026-06-22 06:33:48', '2026-06-22 06:37:28');

-- --------------------------------------------------------

--
-- Table structure for table `package_booking_items`
--

CREATE TABLE `package_booking_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_booking_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `source` varchar(20) NOT NULL DEFAULT 'package' COMMENT 'package=included,extra=added extra',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_booking_items`
--

INSERT INTO `package_booking_items` (`id`, `package_booking_id`, `service_id`, `quantity`, `price`, `source`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 0.00, 'package', '2026-06-22 05:59:25', '2026-06-22 05:59:25'),
(2, 1, 2, 1, 0.00, 'package', '2026-06-22 05:59:25', '2026-06-22 05:59:25'),
(3, 1, 3, 1, 0.00, 'package', '2026-06-22 05:59:25', '2026-06-22 05:59:25'),
(4, 2, 1, 1, 0.00, 'package', '2026-06-22 06:33:48', '2026-06-22 06:33:48'),
(5, 2, 3, 1, 0.00, 'package', '2026-06-22 06:33:48', '2026-06-22 06:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `package_cash_handovers`
--

CREATE TABLE `package_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `counter_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_cash_handovers`
--

INSERT INTO `package_cash_handovers` (`id`, `user_id`, `counter_id`, `amount`, `status`, `receiver_user_id`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `remark`, `business_date`, `requested_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1750.00, 'approved', 1, 1, '2026-06-22 06:34:26', NULL, NULL, NULL, '2026-06-22', '2026-06-22 06:32:26', '2026-06-22 06:32:26', '2026-06-22 06:34:26'),
(2, 1, 3, 1600.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22', '2026-06-22 06:34:20', '2026-06-22 06:34:20', '2026-06-22 06:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `package_counters`
--

CREATE TABLE `package_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_counters`
--

INSERT INTO `package_counters` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Package Counter 1', 1, '2026-06-22 04:48:07', '2026-06-22 04:48:07'),
(3, 'Package Counter 2', 1, '2026-06-22 06:33:24', '2026-06-22 06:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `package_counter_packages`
--

CREATE TABLE `package_counter_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_counter_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_counter_packages`
--

INSERT INTO `package_counter_packages` (`id`, `package_counter_id`, `package_id`, `created_at`, `updated_at`) VALUES
(3, 2, 5, NULL, NULL),
(4, 2, 6, NULL, NULL),
(5, 3, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package_counter_user`
--

CREATE TABLE `package_counter_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `package_counter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_counter_user`
--

INSERT INTO `package_counter_user` (`user_id`, `package_counter_id`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, NULL),
(2, 2, NULL, NULL),
(2, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package_items`
--

CREATE TABLE `package_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_items`
--

INSERT INTO `package_items` (`id`, `package_id`, `service_id`, `created_at`, `updated_at`) VALUES
(4, 6, 1, '2026-06-22 04:14:36', '2026-06-22 04:14:36'),
(5, 6, 2, '2026-06-22 04:14:36', '2026-06-22 04:14:36'),
(6, 6, 3, '2026-06-22 04:14:36', '2026-06-22 04:14:36'),
(7, 5, 1, '2026-06-22 04:41:44', '2026-06-22 04:41:44'),
(8, 5, 3, '2026-06-22 04:41:44', '2026-06-22 04:41:44');

-- --------------------------------------------------------

--
-- Table structure for table `parking_cash_handovers`
--

CREATE TABLE `parking_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parking_counter_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_cash_handovers`
--

INSERT INTO `parking_cash_handovers` (`id`, `user_id`, `parking_counter_id`, `amount`, `status`, `receiver_user_id`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `remark`, `business_date`, `requested_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 350.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-21', '2026-06-21 07:10:58', '2026-06-21 07:10:58', '2026-06-21 07:10:58'),
(2, 1, 2, 400.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22', '2026-06-22 00:05:49', '2026-06-22 00:05:50', '2026-06-22 00:05:50'),
(3, 1, 2, 350.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-29', '2026-06-29 01:36:25', '2026-06-29 01:36:25', '2026-06-29 01:36:25');

-- --------------------------------------------------------

--
-- Table structure for table `parking_counters`
--

CREATE TABLE `parking_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_counters`
--

INSERT INTO `parking_counters` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Parking Counter 1', 'Parking Counter 1 created', 1, '2026-06-21 03:03:02', '2026-06-21 03:03:02'),
(2, 'Parking Counter 2', 'Parking Counter 2 create', 1, '2026-06-22 00:03:50', '2026-06-22 00:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `parking_counter_user`
--

CREATE TABLE `parking_counter_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parking_counter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_counter_user`
--

INSERT INTO `parking_counter_user` (`user_id`, `parking_counter_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parking_tickets`
--

CREATE TABLE `parking_tickets` (
  `ticket_number` varchar(250) NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parking_counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_number` varchar(250) NOT NULL,
  `driver_name` varchar(250) DEFAULT NULL,
  `driver_phone` varchar(250) DEFAULT NULL,
  `entry_time` timestamp NULL DEFAULT NULL,
  `exit_time` timestamp NULL DEFAULT NULL,
  `total_minutes` int(11) DEFAULT NULL,
  `total_hours` decimal(8,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `extra_amount` decimal(10,2) DEFAULT NULL,
  `parking_slot_start_time` time DEFAULT NULL,
  `parking_slot_end_time` time DEFAULT NULL,
  `slot_multiplier` int(11) DEFAULT NULL,
  `status` enum('pending','checked_in','checked_out') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_tickets`
--

INSERT INTO `parking_tickets` (`ticket_number`, `vehicle_id`, `parking_counter_id`, `vehicle_number`, `driver_name`, `driver_phone`, `entry_time`, `exit_time`, `total_minutes`, `total_hours`, `hourly_rate`, `base_price`, `total_amount`, `paid_amount`, `extra_amount`, `parking_slot_start_time`, `parking_slot_end_time`, `slot_multiplier`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
('0451782046514', 2, 1, 'Ch-932435', 'Khairul Basar', '01934243240', '2026-06-21 06:55:14', '2026-06-21 06:55:46', 0, 0.00, 350.00, 350.00, 350.00, 350.00, 0.00, '08:00:00', '18:00:00', 1, 'checked_out', 1, '2026-06-21 06:55:14', '2026-06-21 06:55:46'),
('1281782718569', 2, 2, 'DH-2020202', 'Mr Aslam khan', '24123131231', '2026-06-29 01:36:09', NULL, NULL, NULL, 350.00, 350.00, 350.00, 350.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-29 01:36:09', '2026-06-29 01:36:09'),
('3491782206493', 2, 2, 'gh-435532', 'Mizan', '012534254542', '2026-06-23 03:21:33', '2026-06-24 04:27:50', 1446, 25.00, 350.00, 350.00, 1050.00, 1750.00, 700.00, '08:00:00', '18:00:00', 3, 'checked_out', 1, '2026-06-23 03:21:33', '2026-06-24 04:27:50'),
('3581782108285', 3, 2, 'gh-435532', 'Asif hosen', '0165342545423', '2026-06-22 00:04:45', NULL, NULL, NULL, 400.00, 400.00, 400.00, 400.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-22 00:04:45', '2026-06-22 00:04:45'),
('4911782033048', 1, 1, 'Dh-285536', 'Monirul Islam', '01734243240', '2026-06-21 03:10:48', '2026-06-21 07:09:26', 238, 4.00, 100.00, 100.00, 100.00, 100.00, 0.00, '08:00:00', '18:00:00', 1, 'checked_out', 1, '2026-06-21 03:10:48', '2026-06-21 07:09:26'),
('5671782032967', 1, 1, 'Dh-285536', 'Monirul Islam', '01734243240', '2026-06-21 03:09:27', NULL, NULL, NULL, 100.00, 100.00, 100.00, 100.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-21 03:09:27', '2026-06-21 03:09:27'),
('7861782108114', 2, 1, 'gh-435533', 'Habib munsi', '0165342545423', '2026-06-22 00:01:54', NULL, NULL, NULL, 350.00, 350.00, 350.00, 350.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-22 00:01:54', '2026-06-22 00:01:54'),
('8441782108037', 1, 1, 'gh-435533', 'Manik mia', '012534254542', '2026-06-22 00:00:37', NULL, NULL, NULL, 100.00, 100.00, 100.00, 100.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-22 00:00:37', '2026-06-22 00:00:37'),
('8631782108075', 1, 1, 'Dh-285536', 'Rabbi Gazi', '01734243240', '2026-06-22 00:01:15', NULL, NULL, NULL, 100.00, 100.00, 100.00, 100.00, 0.00, '08:00:00', '18:00:00', NULL, 'checked_in', 1, '2026-06-22 00:01:15', '2026-06-22 00:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `parking_ticket_payments`
--

CREATE TABLE `parking_ticket_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parking_ticket_number` varchar(250) NOT NULL,
  `payment_type` enum('entry','extra') NOT NULL DEFAULT 'entry',
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `parking_counter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_ticket_payments`
--

INSERT INTO `parking_ticket_payments` (`id`, `parking_ticket_number`, `payment_type`, `amount`, `payment_date`, `created_by`, `parking_counter_id`, `created_at`, `updated_at`) VALUES
(1, '0451782046514', 'entry', 350.00, '2026-06-21', 1, 1, '2026-06-21 06:55:15', '2026-06-21 06:55:15'),
(2, '8441782108037', 'entry', 100.00, '2026-06-22', 1, 1, '2026-06-22 00:00:37', '2026-06-22 00:00:37'),
(3, '8631782108075', 'entry', 100.00, '2026-06-22', 1, 1, '2026-06-22 00:01:15', '2026-06-22 00:01:15'),
(4, '7861782108114', 'entry', 350.00, '2026-06-22', 1, 1, '2026-06-22 00:01:54', '2026-06-22 00:01:54'),
(5, '3581782108285', 'entry', 400.00, '2026-06-22', 1, 2, '2026-06-22 00:04:45', '2026-06-22 00:04:45'),
(6, '3491782206493', 'entry', 350.00, '2026-06-23', 1, 2, '2026-06-23 03:21:33', '2026-06-23 03:21:33'),
(7, '3491782206493', 'extra', 700.00, '2026-06-24', 3, 2, '2026-06-24 04:27:50', '2026-06-24 04:27:50'),
(8, '1281782718569', 'entry', 350.00, '2026-06-29', 1, 2, '2026-06-29 01:36:09', '2026-06-29 01:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(200) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `parent_id`) VALUES
(1, 'RegisterController', NULL),
(2, 'showRegistrationForm', 1),
(3, 'register', 1),
(4, 'showUserLists', 1),
(5, 'create', 1),
(6, 'store', 1),
(7, 'showUser', 1),
(8, 'editUser', 1),
(9, 'updateUser', 1),
(10, 'password', 1),
(11, 'changePassword', 1),
(12, 'changeAllUserPassword', 1),
(13, 'profile', 1),
(14, 'updateProfile', 1),
(15, 'destroyUser', 1),
(16, 'RoleController', NULL),
(17, 'index', 16),
(18, 'create', 16),
(19, 'store', 16),
(20, 'show', 16),
(21, 'edit', 16),
(22, 'update', 16),
(23, 'destroy', 16),
(24, 'SiteSettingController', NULL),
(25, 'edit', 24),
(26, 'update', 24),
(27, 'DesignationController', NULL),
(28, 'create', 27),
(29, 'store', 27),
(30, 'destroy', 27),
(31, 'DepartmentController', NULL),
(32, 'create', 31),
(33, 'store', 31),
(34, 'destroy', 31),
(35, 'CategoryController', NULL),
(36, 'create', 35),
(37, 'store', 35),
(38, 'destroy', 35),
(39, 'WarehouseController', NULL),
(40, 'create', 39),
(41, 'store', 39),
(42, 'edit', 39),
(43, 'update', 39),
(44, 'SupplierController', NULL),
(45, 'index', 44),
(46, 'create', 44),
(47, 'store', 44),
(48, 'show', 44),
(49, 'edit', 44),
(50, 'update', 44),
(51, 'destroy', 44),
(52, 'ItemController', NULL),
(53, 'index', 52),
(54, 'create', 52),
(55, 'store', 52),
(56, 'show', 52),
(57, 'edit', 52),
(58, 'update', 52),
(59, 'destroy', 52),
(60, 'PurchaseController', NULL),
(61, 'index', 60),
(62, 'create', 60),
(63, 'store', 60),
(64, 'show', 60),
(65, 'edit', 60),
(66, 'update', 60),
(67, 'destroy', 60),
(68, 'purchase_print', 60),
(69, 'StockInController', NULL),
(70, 'index', 69),
(71, 'create', 69),
(72, 'store', 69),
(73, 'show', 69),
(74, 'edit', 69),
(75, 'update', 69),
(76, 'destroy', 69),
(77, 'stock_summary', 69),
(78, 'low_stock_reminder', 69),
(79, 'stock_in_print', 69),
(80, 'RequisitionController', NULL),
(81, 'index', 80),
(82, 'create', 80),
(83, 'store', 80),
(84, 'show', 80),
(85, 'edit', 80),
(86, 'update', 80),
(87, 'destroy', 80),
(88, 'counter_sign_list', 80),
(89, 'counter_sign_show', 80),
(90, 'counter_sign_update', 80),
(91, 'admin_requisition_list', 80),
(92, 'admin_requisition_show', 80),
(93, 'admin_requisition_update', 80),
(94, 'admin_requisition_summary', 80),
(95, 'item_wise_requisition', 80),
(96, 'IndentController', NULL),
(97, 'indent_list', 96),
(98, 'admin_indent_list', 96),
(99, 'MrsItemController', NULL),
(100, 'index', 99),
(101, 'create', 99),
(102, 'store', 99),
(103, 'show', 99),
(104, 'edit', 99),
(105, 'update', 99),
(106, 'destroy', 99),
(107, 'my_mrs_item_list', 99),
(108, 'my_mrs_item_show', 99),
(109, 'mrs_item_summary', 99),
(110, 'PurposeController', NULL),
(111, 'create', 110),
(112, 'store', 110),
(113, 'edit', 110),
(114, 'update', 110),
(115, 'PurchaseTransactionController', NULL),
(116, 'index', 115),
(117, 'create', 115),
(118, 'store', 115),
(119, 'show', 115),
(120, 'edit', 115),
(121, 'update', 115),
(122, 'destroy', 115),
(123, 'TicketCategoryController', NULL),
(124, 'create', 123),
(125, 'store', 123),
(126, 'destroy', 123),
(127, 'GateController', NULL),
(128, 'index', 127),
(129, 'create', 127),
(130, 'store', 127),
(131, 'show', 127),
(132, 'edit', 127),
(133, 'update', 127),
(134, 'destroy', 127),
(135, 'TicketController', NULL),
(136, 'index', 135),
(137, 'create', 135),
(138, 'store', 135),
(139, 'show', 135),
(140, 'edit', 135),
(141, 'update', 135),
(142, 'destroy', 135),
(143, 'TicketSaleController', NULL),
(144, 'index', 143),
(145, 'create', 143),
(146, 'store', 143),
(147, 'destroy', 143),
(148, 'print', 143),
(149, 'groupPrint', 143),
(150, 'getTicketPrice', 143),
(151, 'report', 143),
(152, 'validationForm', 143),
(153, 'validateTicket', 143),
(154, 'scan', 143),
(155, 'scanValidate', 143),
(156, 'TicketCashHandoverController', NULL),
(157, 'index', 156),
(158, 'store', 156),
(159, 'approval', 156),
(160, 'approve', 156),
(161, 'reject', 156),
(162, 'WaterParkTicketController', NULL),
(163, 'index', 162),
(164, 'create', 162),
(165, 'store', 162),
(166, 'scanCamera', 162),
(167, 'bulkPrint', 162),
(168, 'show', 162),
(169, 'scan', 162),
(170, 'checkIn', 162),
(171, 'checkOut', 162),
(172, 'WaterParkTimeRangeController', NULL),
(173, 'index', 172),
(174, 'WaterParkSettingController', NULL),
(175, 'edit', 174),
(176, 'update', 174),
(177, 'WaterParkCounterController', NULL),
(178, 'index', 177),
(179, 'create', 177),
(180, 'store', 177),
(181, 'show', 177),
(182, 'edit', 177),
(183, 'update', 177),
(184, 'destroy', 177),
(185, 'WaterParkCashHandoverController', NULL),
(186, 'index', 185),
(187, 'store', 185),
(188, 'approval', 185),
(189, 'approve', 185),
(190, 'reject', 185),
(191, 'ParkingTicketController', NULL),
(192, 'index', 191),
(193, 'create', 191),
(194, 'store', 191),
(195, 'scanCamera', 191),
(196, 'view_all_parking_tickets', 191),
(197, 'show', 191),
(198, 'edit', 191),
(199, 'update', 191),
(200, 'destroy', 191),
(201, 'scan', 191),
(202, 'checkIn', 191),
(203, 'checkOut', 191),
(204, 'receipt', 191),
(205, 'entryReceipt', 191),
(206, 'extraPayment', 191),
(207, 'processExtraPayment', 191),
(208, 'ParkingCounterController', NULL),
(209, 'index', 208),
(210, 'create', 208),
(211, 'store', 208),
(212, 'show', 208),
(213, 'edit', 208),
(214, 'update', 208),
(215, 'destroy', 208),
(216, 'ParkingReportController', NULL),
(217, 'index', 216),
(218, 'view_all_parking_reports', 216),
(219, 'ParkingCashHandoverController', NULL),
(220, 'index', 219),
(221, 'store', 219),
(222, 'approval', 219),
(223, 'approve', 219),
(224, 'reject', 219),
(225, 'VehicleController', NULL),
(226, 'index', 225),
(227, 'create', 225),
(228, 'store', 225),
(229, 'edit', 225),
(230, 'update', 225),
(231, 'getRates', 225),
(232, 'view_parking_counters', 208),
(233, 'create_parking_counter', 208),
(234, 'edit_parking_counter', 208),
(235, 'delete_parking_counter', 208),
(236, 'verifyTicket', 143),
(237, 'confirmVerify', 143),
(238, 'PackageBookingController', NULL),
(239, 'scanByToken', 238),
(240, 'validateByToken', 238),
(241, 'index', 238),
(242, 'create', 238),
(243, 'store', 238),
(244, 'show', 238),
(245, 'edit', 238),
(246, 'update', 238),
(247, 'destroy', 238),
(248, 'getPackageDetails', 238),
(249, 'print', 238),
(250, 'previewTickets', 238),
(251, 'printTickets', 238),
(252, 'showScanForm', 238),
(253, 'PackageController', NULL),
(254, 'index', 253),
(255, 'create', 253),
(256, 'store', 253),
(257, 'show', 253),
(258, 'edit', 253),
(259, 'update', 253),
(260, 'destroy', 253),
(261, 'PackageCounterController', NULL),
(262, 'index', 261),
(263, 'create', 261),
(264, 'store', 261),
(265, 'show', 261),
(266, 'edit', 261),
(267, 'update', 261),
(268, 'destroy', 261),
(269, 'PackageReportController', NULL),
(270, 'index', 269),
(271, 'generate', 269),
(272, 'print', 269),
(273, 'PackageCashHandoverController', NULL),
(274, 'index', 273),
(275, 'store', 273),
(276, 'approval', 273),
(277, 'approve', 273),
(278, 'reject', 273),
(279, 'LockerItemController', NULL),
(280, 'index', 279),
(281, 'create', 279),
(282, 'store', 279),
(283, 'show', 279),
(284, 'edit', 279),
(285, 'update', 279),
(286, 'destroy', 279),
(287, 'GearItemController', NULL),
(288, 'index', 287),
(289, 'create', 287),
(290, 'store', 287),
(291, 'show', 287),
(292, 'edit', 287),
(293, 'update', 287),
(294, 'destroy', 287),
(295, 'ItemPricingController', NULL),
(296, 'index', 295),
(297, 'create', 295),
(298, 'store', 295),
(299, 'show', 295),
(300, 'edit', 295),
(301, 'update', 295),
(302, 'destroy', 295),
(303, 'LockerGearCounterController', NULL),
(304, 'index', 303),
(305, 'create', 303),
(306, 'store', 303),
(307, 'show', 303),
(308, 'edit', 303),
(309, 'update', 303),
(310, 'destroy', 303),
(311, 'assignUsers', 303),
(312, 'updateUsers', 303),
(313, 'LockerGearTicketController', NULL),
(314, 'index', 313),
(315, 'create', 313),
(316, 'store', 313),
(317, 'show', 313),
(318, 'scanCamera', 313),
(319, 'scan', 313),
(320, 'checkOut', 313),
(321, 'LockerGearReportController', NULL),
(322, 'index', 321),
(323, 'itemReport', 321),
(324, 'stockReport', 321),
(325, 'userReport', 321),
(326, 'activeRentals', 321),
(327, 'overdueReport', 321),
(328, 'LockerGearCashHandoverController', NULL),
(329, 'index', 328),
(330, 'store', 328),
(331, 'approval', 328),
(332, 'approve', 328),
(333, 'reject', 328),
(334, 'BookingController', NULL),
(335, 'checkRoomAvailability', 334),
(336, 'index', 334),
(337, 'create', 334),
(338, 'store', 334),
(339, 'show', 334),
(340, 'edit', 334),
(341, 'update', 334),
(342, 'destroy', 334),
(343, 'get_fields', 334),
(344, 'validatePromo', 334),
(345, 'checkAvailability', 334),
(346, 'availabilityCalendar', 334),
(347, 'getCalendarData', 334),
(348, 'counterReport', 334),
(349, 'getCounterReportData', 334),
(350, 'ServiceCategoryController', NULL),
(351, 'index', 350),
(352, 'create', 350),
(353, 'store', 350),
(354, 'show', 350),
(355, 'edit', 350),
(356, 'update', 350),
(357, 'destroy', 350),
(358, 'ServiceController', NULL),
(359, 'index', 358),
(360, 'create', 358),
(361, 'store', 358),
(362, 'show', 358),
(363, 'edit', 358),
(364, 'update', 358),
(365, 'destroy', 358),
(366, 'getService', 358),
(367, 'getServicesByCategory', 358),
(368, 'CategoryMetaFieldController', NULL),
(369, 'create', 368),
(370, 'store', 368),
(371, 'edit', 368),
(372, 'update', 368),
(373, 'destroy', 368),
(374, 'updateSortOrder', 368),
(375, 'ServiceMetaFieldController', NULL),
(376, 'create', 375),
(377, 'store', 375),
(378, 'edit', 375),
(379, 'update', 375),
(380, 'destroy', 375),
(381, 'updateSortOrder', 375),
(382, 'AvailabilityController', NULL),
(383, 'index', 382),
(384, 'getBookingDetails', 382),
(385, 'PricingRuleController', NULL),
(386, 'index', 385),
(387, 'create', 385),
(388, 'store', 385),
(389, 'show', 385),
(390, 'edit', 385),
(391, 'update', 385),
(392, 'destroy', 385),
(393, 'DiscountRuleController', NULL),
(394, 'index', 393),
(395, 'create', 393),
(396, 'store', 393),
(397, 'show', 393),
(398, 'edit', 393),
(399, 'update', 393),
(400, 'destroy', 393),
(401, 'TimeSlotController', NULL),
(402, 'index', 401),
(403, 'create', 401),
(404, 'store', 401),
(405, 'show', 401),
(406, 'edit', 401),
(407, 'update', 401),
(408, 'destroy', 401),
(409, 'getSlotsByService', 401),
(410, 'CounterController', NULL),
(411, 'index', 410),
(412, 'create', 410),
(413, 'store', 410),
(414, 'show', 410),
(415, 'edit', 410),
(416, 'update', 410),
(417, 'destroy', 410),
(418, 'CustomerController', NULL),
(419, 'print_customer_details', 418),
(420, 'BookingCashHandoverController', NULL),
(421, 'index', 420),
(422, 'store', 420),
(423, 'approval', 420),
(424, 'approve', 420),
(425, 'reject', 420);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(150) NOT NULL,
  `tokenable_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_rules`
--

CREATE TABLE `pricing_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `rule_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=seasonal,1=weekend,2=holiday',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array for weekend days like ["sat","sun"]' CHECK (json_valid(`days`)),
  `price_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=fixed,1=percentage',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0=inactive,1=active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `supplier_type` int(10) UNSIGNED NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web_site` varchar(250) DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `po_number` varchar(150) DEFAULT NULL,
  `purchase_person` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `fob_point` varchar(150) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  `vat_percent` decimal(12,2) NOT NULL,
  `vat` decimal(12,2) NOT NULL,
  `grand_total` decimal(12,2) NOT NULL,
  `inword` varchar(150) DEFAULT NULL,
  `special_instruction` text DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `contact_name`, `company_name`, `supplier_type`, `address`, `mobile`, `email`, `web_site`, `supplier_id`, `purchase_date`, `po_number`, `purchase_person`, `invoice_no`, `fob_point`, `discount`, `sub_total`, `vat_percent`, `vat`, `grand_total`, `inword`, `special_instruction`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Sahadat2', 'AtomSoft2', 2, 'Mirpur, Dhaka', '01923760310', 'sahadat39@gmail.com', NULL, 2, '2025-10-01', NULL, 1, NULL, NULL, 3000.00, 124050.00, 5.00, 6052.50, 127102.50, 'One Lac Twenty Seven Thousand One Hundred and Three Only', 'test', 3, 1, 1, '2025-10-18 05:56:03', '2025-10-18 09:13:20'),
(3, 'Sahadat', 'AtomSoft', 2, 'Mirpur, Dhaka', '01923760310', 'sahadat39@gmail.com', NULL, 1, '2025-10-01', NULL, 1, NULL, NULL, NULL, 300.00, 0.00, 0.00, 300.00, 'Three Hundred', NULL, 4, 1, 1, '2025-10-18 09:23:12', '2025-10-18 09:23:46'),
(4, 'Sahadat', 'AtomSoft', 2, 'Mirpur, Dhaka', '01923760310', 'sahadat39@gmail.com', NULL, 1, '2025-10-02', NULL, 1, NULL, 'inside park', 1.00, 3144.00, 5.00, 157.15, 3300.15, 'Three Thousand Three Hundred', NULL, 0, 1, 1, '2025-10-23 01:25:34', '2025-10-23 01:25:34'),
(5, 'Test up', 'Test Comp', 1, NULL, '01923760310', NULL, NULL, 3, '2025-10-06', NULL, 2, NULL, NULL, 200.00, 184150.00, 5.00, 9197.50, 193147.50, 'One Lac Ninety Three Thousand One Hundred and Forty Eight Only', 'test', 0, 1, 1, '2025-10-26 07:26:23', '2025-10-26 07:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `combinations` text DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `measuring_unit` varchar(50) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `per_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `item_id`, `name`, `description`, `category_id`, `combinations`, `unit_price`, `measuring_unit`, `quantity`, `per_total`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 'Laptop', 'Category Name: ল্যাপটপ, Brand: dell, Model: null, Additional Info:', 3, '\"\"', 60000.00, 'Pieces', 2.00, 120000.00, '2025-10-18 05:56:03', '2025-10-18 09:13:20'),
(2, 1, 1, 'পেপার এ-৪', 'Category Name: ষ্টেশনারী, Brand: পেপার ওয়ান, Model: null, Additional Info:', 1, '{\"Weight\":\"60\"}', 350.00, 'Rim', 11.00, 3850.00, '2025-10-18 05:56:03', '2025-10-18 09:13:20'),
(3, 1, 6, 'ভোমর', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 1, '\"\"', 50.00, 'Pieces', 4.00, 200.00, '2025-10-18 09:08:46', '2025-10-18 09:13:20'),
(5, 3, 5, 'ফাইল বাঁধার ফিতা', 'Category Name: ষ্টেশনারী, Brand: অন্যান্য, Model: null, Additional Info:', 1, '\"\"', 60.00, 'Box', 5.00, 300.00, '2025-10-18 09:23:12', '2025-10-18 09:23:46'),
(6, 4, 8, 'পেন্সিল', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 1, '\"\"', 10.00, 'Pieces', 120.00, 1200.00, '2025-10-23 01:25:34', '2025-10-23 01:25:34'),
(7, 4, 6, 'ভোমর', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 1, '\"\"', 38.00, 'Pieces', 18.00, 684.00, '2025-10-23 01:25:34', '2025-10-23 01:25:34'),
(8, 4, 7, 'জেমস ক্লিপ (প্লাস্টিক কভারযুক্ত)', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 1, '\"\"', 7.00, 'Box', 180.00, 1260.00, '2025-10-23 01:25:34', '2025-10-23 01:25:34'),
(9, 5, 11, 'Paper a4', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info: test1: value1,', 1, '{\"Size\":\"80 GSM\",\"Color\":\"White\"}', 500.00, 'Rim', 5.00, 2500.00, '2025-10-26 07:26:23', '2025-10-26 07:29:17'),
(10, 5, 10, 'Laptop', 'Category Name: ল্যাপটপ, Brand: dell, Model: null, Additional Info:', 3, '\"\"', 45000.00, 'Pieces', 4.00, 180000.00, '2025-10-26 07:26:23', '2025-10-26 07:29:17'),
(11, 5, 2, 'লিগ্যাল পেপার', 'Category Name: ষ্টেশনারী, Brand: ডাবল এ, Model: null, Additional Info:', 1, '{\"Weight\":\"80\"}', 550.00, 'Rim', 3.00, 1650.00, '2025-10-26 07:26:23', '2025-10-26 07:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_transactions`
--

CREATE TABLE `purchase_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_type` int(10) UNSIGNED NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `money_rceipt_no` varchar(50) DEFAULT NULL,
  `received_by` varchar(50) DEFAULT NULL,
  `given_by` bigint(20) UNSIGNED NOT NULL,
  `attachment_copy` varchar(250) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_transactions`
--

INSERT INTO `purchase_transactions` (`id`, `supplier_id`, `purchase_id`, `payment_date`, `amount`, `payment_type`, `invoice_no`, `money_rceipt_no`, `received_by`, `given_by`, `attachment_copy`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 3, 5, '2025-10-28', 30000.00, 1, NULL, '1234', 'test', 1, NULL, 'test up', 1, 1, '2025-12-28 13:41:03', '2025-12-28 13:47:37'),
(3, 1, 4, '2025-10-28', 3300.00, 2, NULL, '12345', 'test1', 2, '7909_image (1).png', 'test', 1, 1, '2025-12-28 14:14:38', '2025-12-28 14:32:16'),
(4, 3, 5, '2026-01-01', 100.50, 1, NULL, NULL, NULL, 1, NULL, NULL, 1, 1, '2026-01-17 01:24:07', '2026-01-17 01:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `purposes`
--

CREATE TABLE `purposes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purpose_type` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purposes`
--

INSERT INTO `purposes` (`id`, `purpose_type`, `name`, `created_at`, `updated_at`) VALUES
(1, 2, 'Wooden Villa Ground Floor 01', '2025-10-18 10:36:15', '2025-10-18 10:36:15'),
(2, 2, 'Wooden Villa Ground Floor 02', '2025-10-18 10:36:37', '2025-10-18 10:36:37'),
(3, 2, 'Wooden Villa First Floor 01', '2025-10-18 10:36:58', '2025-10-18 10:36:58'),
(4, 2, 'Wooden Villa First Floor 02', '2025-10-18 10:37:06', '2025-10-18 10:37:06'),
(5, 5, 'Lamp Post Number 001 up', '2025-10-18 10:37:30', '2025-10-18 10:43:36'),
(6, 1, 'Employee Personal Use Purpose Only', '2025-10-19 01:36:20', '2025-10-19 01:36:20'),
(7, 9, 'Conference Room with AC', '2025-10-23 09:41:18', '2025-10-23 09:41:18'),
(8, 9, 'Conference Room without AC', '2025-10-23 09:41:34', '2025-10-23 09:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

CREATE TABLE `requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stock_out_date` date DEFAULT NULL,
  `received_by` varchar(50) DEFAULT NULL,
  `given_by` varchar(50) DEFAULT NULL,
  `purpose_type` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `purpose_id` bigint(20) UNSIGNED NOT NULL,
  `requisitioner_comments` text DEFAULT NULL,
  `admin_comments` text DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `received_status` varchar(50) DEFAULT NULL,
  `counter_sign_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `counter_sign_date` date DEFAULT NULL,
  `counter_sign_by` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisitions`
--

INSERT INTO `requisitions` (`id`, `user_id`, `stock_out_date`, `received_by`, `given_by`, `purpose_type`, `purpose_id`, `requisitioner_comments`, `admin_comments`, `supervisor_comments`, `status`, `received_status`, `counter_sign_status`, `counter_sign_date`, `counter_sign_by`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-10-23', 'xyz', 'Admin', 2, 1, 'test', 'test বর্তমানে স্টোরে আপনার চাহিত পণ্যটি নাই, স্টোক হওয়ার পর ব্যবস্থা গ্রহণ করা হবে', 'test', 3, 'received', 1, '2025-10-23', 1, 1, 1, '2025-10-19 10:02:48', '2025-10-23 03:45:44'),
(2, 1, '2025-10-20', NULL, 'Admin', 1, 6, 'self', NULL, 'test by supervisor', 3, NULL, 1, '2025-10-22', 1, 1, 1, '2025-10-20 01:46:04', '2025-10-23 03:49:23'),
(4, 1, '2025-10-23', 'xyz2', 'Admin', 1, 6, NULL, NULL, NULL, 3, NULL, 1, '2025-10-23', 1, 1, 1, '2025-10-23 08:58:46', '2025-10-23 09:09:12'),
(5, 1, '2025-10-23', NULL, 'Admin', 9, 7, 'test', NULL, NULL, 3, NULL, 1, '2025-10-23', 1, 1, 1, '2025-10-23 09:42:41', '2025-10-23 09:43:46'),
(6, 1, '2025-10-26', 'xyz2', 'Admin', 2, 1, NULL, 'done', 'test', 3, 'received', 1, '2025-10-26', 2, 1, 2, '2025-10-26 07:48:16', '2025-10-26 08:09:53'),
(7, 1, NULL, NULL, NULL, 1, 6, NULL, NULL, 'fbbb', 0, NULL, 1, '2025-10-26', 2, 1, 2, '2025-10-26 08:11:41', '2025-10-26 08:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `combinations` text DEFAULT NULL,
  `req_quantity` decimal(12,2) NOT NULL,
  `given_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `measuring_unit` varchar(50) NOT NULL,
  `returnable` tinyint(4) NOT NULL DEFAULT 0,
  `product_type` tinyint(4) NOT NULL DEFAULT 0,
  `stock_out_date` date DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisition_items`
--

INSERT INTO `requisition_items` (`id`, `requisition_id`, `item_id`, `category_id`, `name`, `description`, `warehouse_id`, `combinations`, `req_quantity`, `given_quantity`, `measuring_unit`, `returnable`, `product_type`, `stock_out_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 1, 'Towel', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info: test1: value1, test2: value2, test3: value3,', 0, '{\"Color\":\"Blue\",\"Size\":\"M\"}', 1.00, 1.00, 'Pieces', 0, 0, '2025-10-23', 3, '2025-10-19 10:02:48', '2025-10-23 03:42:51'),
(2, 1, 7, 1, 'জেমস ক্লিপ (প্লাস্টিক কভারযুক্ত)', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Box', 0, 0, '2025-10-23', 3, '2025-10-19 10:02:48', '2025-10-23 03:42:51'),
(3, 2, 8, 1, 'পেন্সিল', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Pieces', 0, 0, '2025-10-20', 3, '2025-10-20 01:46:04', '2025-10-23 03:49:23'),
(4, 1, 8, 1, 'পেন্সিল', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Pieces', 0, 0, '2025-10-23', 3, '2025-10-22 04:42:50', '2025-10-23 03:42:51'),
(7, 4, 10, 3, 'Laptop', 'Category Name: ল্যাপটপ, Brand: dell, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Pieces', 1, 0, '2025-10-23', 3, '2025-10-23 08:58:46', '2025-10-23 09:09:12'),
(8, 4, 1, 1, 'পেপার এ-৪', 'Category Name: ষ্টেশনারী, Brand: পেপার ওয়ান, Model: null, Additional Info:', 0, '{\"Weight\":\"80\"}', 10.00, 1.00, 'Rim', 0, 0, '2025-10-23', 3, '2025-10-23 08:58:46', '2025-10-23 09:09:12'),
(9, 5, 10, 3, 'Laptop', 'Category Name: ল্যাপটপ, Brand: dell, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Pieces', 1, 1, '2025-10-23', 3, '2025-10-23 09:42:41', '2025-10-23 09:43:46'),
(10, 5, 9, 1, 'Towel', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info: test1: value1, test2: value2, test3: value3,', 0, '{\"Color\":\"Red\",\"Size\":\"XL\"}', 1.00, 1.00, 'Pieces', 0, 0, '2025-10-23', 3, '2025-10-23 09:42:41', '2025-10-23 09:43:46'),
(11, 6, 11, 1, 'Paper a4', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info: test1: value1,', 0, '{\"Size\":\"80 GSM\",\"Color\":\"White\"}', 2.00, 1.00, 'Rim', 0, 0, '2025-10-26', 3, '2025-10-26 07:48:16', '2025-10-26 08:06:59'),
(12, 6, 10, 3, 'Laptop', 'Category Name: ল্যাপটপ, Brand: dell, Model: null, Additional Info:', 0, '\"\"', 1.00, 1.00, 'Pieces', 1, 0, '2025-10-26', 3, '2025-10-26 07:48:16', '2025-10-26 08:06:59'),
(13, 7, 9, 1, 'Towel', 'Category Name: ষ্টেশনারী, Brand: null, Model: null, Additional Info: test1: value1, test2: value2, test3: value3,', 0, '{\"Color\":\"Black\",\"Size\":\"M\"}', 1.00, 0.00, 'Pieces', 0, 0, NULL, 0, '2025-10-26 08:11:41', '2025-10-26 08:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `is_deletable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`, `is_deletable`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Super Power', 1, 0, '2025-09-29 07:54:45', NULL),
(2, 'Data Entry Operator', NULL, 1, 1, '2025-10-26 06:34:01', '2025-10-26 06:34:01'),
(3, 'Ticket Seller', NULL, 1, 1, '2026-06-23 02:51:04', '2026-06-23 02:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(1, 80),
(1, 81),
(1, 82),
(1, 83),
(1, 84),
(1, 85),
(1, 86),
(1, 87),
(1, 88),
(1, 89),
(1, 90),
(1, 91),
(1, 92),
(1, 93),
(1, 94),
(1, 95),
(1, 96),
(1, 97),
(1, 98),
(1, 99),
(1, 100),
(1, 101),
(1, 102),
(1, 103),
(1, 104),
(1, 105),
(1, 106),
(1, 107),
(1, 108),
(1, 109),
(1, 110),
(1, 111),
(1, 112),
(1, 113),
(1, 114),
(1, 115),
(1, 116),
(1, 117),
(1, 118),
(1, 119),
(1, 120),
(1, 121),
(1, 122),
(1, 123),
(1, 124),
(1, 125),
(1, 126),
(1, 127),
(1, 128),
(1, 129),
(1, 130),
(1, 131),
(1, 132),
(1, 133),
(1, 134),
(1, 135),
(1, 136),
(1, 137),
(1, 138),
(1, 139),
(1, 140),
(1, 141),
(1, 142),
(1, 143),
(1, 144),
(1, 145),
(1, 146),
(1, 147),
(1, 148),
(1, 149),
(1, 150),
(1, 151),
(1, 152),
(1, 153),
(1, 154),
(1, 155),
(1, 156),
(1, 157),
(1, 158),
(1, 159),
(1, 160),
(1, 161),
(1, 162),
(1, 163),
(1, 164),
(1, 165),
(1, 166),
(1, 167),
(1, 168),
(1, 169),
(1, 170),
(1, 171),
(1, 172),
(1, 173),
(1, 174),
(1, 175),
(1, 176),
(1, 177),
(1, 178),
(1, 179),
(1, 180),
(1, 181),
(1, 182),
(1, 183),
(1, 184),
(1, 185),
(1, 186),
(1, 187),
(1, 188),
(1, 189),
(1, 190),
(1, 191),
(1, 192),
(1, 193),
(1, 194),
(1, 195),
(1, 196),
(1, 197),
(1, 198),
(1, 199),
(1, 200),
(1, 201),
(1, 202),
(1, 203),
(1, 204),
(1, 205),
(1, 206),
(1, 207),
(1, 208),
(1, 209),
(1, 210),
(1, 211),
(1, 212),
(1, 213),
(1, 214),
(1, 215),
(1, 216),
(1, 217),
(1, 218),
(1, 219),
(1, 220),
(1, 221),
(1, 222),
(1, 223),
(1, 224),
(1, 225),
(1, 226),
(1, 227),
(1, 228),
(1, 229),
(1, 230),
(1, 231),
(1, 232),
(1, 233),
(1, 234),
(1, 235),
(1, 236),
(1, 237),
(1, 238),
(1, 239),
(1, 240),
(1, 241),
(1, 242),
(1, 243),
(1, 244),
(1, 245),
(1, 246),
(1, 247),
(1, 248),
(1, 249),
(1, 250),
(1, 251),
(1, 252),
(1, 253),
(1, 254),
(1, 255),
(1, 256),
(1, 257),
(1, 258),
(1, 259),
(1, 260),
(1, 261),
(1, 262),
(1, 263),
(1, 264),
(1, 265),
(1, 266),
(1, 267),
(1, 268),
(1, 269),
(1, 270),
(1, 271),
(1, 272),
(1, 273),
(1, 274),
(1, 275),
(1, 276),
(1, 277),
(1, 278),
(1, 279),
(1, 280),
(1, 281),
(1, 282),
(1, 283),
(1, 284),
(1, 285),
(1, 286),
(1, 287),
(1, 288),
(1, 289),
(1, 290),
(1, 291),
(1, 292),
(1, 293),
(1, 294),
(1, 295),
(1, 296),
(1, 297),
(1, 298),
(1, 299),
(1, 300),
(1, 301),
(1, 302),
(1, 303),
(1, 304),
(1, 305),
(1, 306),
(1, 307),
(1, 308),
(1, 309),
(1, 310),
(1, 311),
(1, 312),
(1, 313),
(1, 314),
(1, 315),
(1, 316),
(1, 317),
(1, 318),
(1, 319),
(1, 320),
(1, 321),
(1, 322),
(1, 323),
(1, 324),
(1, 325),
(1, 326),
(1, 327),
(1, 328),
(1, 329),
(1, 330),
(1, 331),
(1, 332),
(1, 333),
(1, 334),
(1, 335),
(1, 336),
(1, 337),
(1, 338),
(1, 339),
(1, 340),
(1, 341),
(1, 342),
(1, 343),
(1, 344),
(1, 345),
(1, 346),
(1, 347),
(1, 348),
(1, 349),
(1, 350),
(1, 351),
(1, 352),
(1, 353),
(1, 354),
(1, 355),
(1, 356),
(1, 357),
(1, 358),
(1, 359),
(1, 360),
(1, 361),
(1, 362),
(1, 363),
(1, 364),
(1, 365),
(1, 366),
(1, 367),
(1, 368),
(1, 369),
(1, 370),
(1, 371),
(1, 372),
(1, 373),
(1, 374),
(1, 375),
(1, 376),
(1, 377),
(1, 378),
(1, 379),
(1, 380),
(1, 381),
(1, 382),
(1, 383),
(1, 384),
(1, 385),
(1, 386),
(1, 387),
(1, 388),
(1, 389),
(1, 390),
(1, 391),
(1, 392),
(1, 393),
(1, 394),
(1, 395),
(1, 396),
(1, 397),
(1, 398),
(1, 399),
(1, 400),
(1, 401),
(1, 402),
(1, 403),
(1, 404),
(1, 405),
(1, 406),
(1, 407),
(1, 408),
(1, 409),
(1, 410),
(1, 411),
(1, 412),
(1, 413),
(1, 414),
(1, 415),
(1, 416),
(1, 417),
(1, 420),
(1, 421),
(1, 422),
(1, 423),
(1, 424),
(1, 425),
(2, 1),
(2, 10),
(2, 11),
(2, 13),
(2, 14),
(2, 27),
(2, 28),
(2, 29),
(2, 31),
(2, 32),
(2, 33),
(2, 35),
(2, 36),
(2, 37),
(2, 39),
(2, 40),
(2, 41),
(2, 42),
(2, 43),
(2, 44),
(2, 45),
(2, 46),
(2, 47),
(2, 48),
(2, 49),
(2, 52),
(2, 53),
(2, 54),
(2, 55),
(2, 56),
(2, 57),
(2, 58),
(2, 60),
(2, 61),
(2, 62),
(2, 63),
(2, 69),
(2, 70),
(2, 71),
(2, 72),
(2, 73),
(2, 74),
(2, 75),
(2, 80),
(2, 81),
(2, 82),
(2, 83),
(2, 84),
(2, 85),
(2, 86),
(2, 87),
(2, 88),
(2, 89),
(2, 90),
(2, 91),
(2, 99),
(2, 107),
(2, 108),
(2, 110),
(2, 111),
(2, 112),
(2, 113),
(2, 114),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(3, 37),
(3, 38),
(3, 39),
(3, 40),
(3, 41),
(3, 42),
(3, 43),
(3, 44),
(3, 45),
(3, 46),
(3, 47),
(3, 48),
(3, 49),
(3, 50),
(3, 51),
(3, 52),
(3, 53),
(3, 54),
(3, 55),
(3, 56),
(3, 57),
(3, 58),
(3, 59),
(3, 60),
(3, 61),
(3, 62),
(3, 63),
(3, 64),
(3, 65),
(3, 66),
(3, 67),
(3, 68),
(3, 69),
(3, 70),
(3, 71),
(3, 72),
(3, 73),
(3, 74),
(3, 75),
(3, 76),
(3, 77),
(3, 78),
(3, 79),
(3, 80),
(3, 81),
(3, 82),
(3, 83),
(3, 84),
(3, 85),
(3, 86),
(3, 87),
(3, 88),
(3, 89),
(3, 90),
(3, 91),
(3, 92),
(3, 93),
(3, 94),
(3, 95),
(3, 96),
(3, 97),
(3, 98),
(3, 99),
(3, 100),
(3, 101),
(3, 102),
(3, 103),
(3, 104),
(3, 105),
(3, 106),
(3, 107),
(3, 108),
(3, 109),
(3, 110),
(3, 111),
(3, 112),
(3, 113),
(3, 114),
(3, 115),
(3, 116),
(3, 117),
(3, 118),
(3, 119),
(3, 120),
(3, 121),
(3, 122),
(3, 123),
(3, 124),
(3, 125),
(3, 126),
(3, 127),
(3, 128),
(3, 129),
(3, 130),
(3, 131),
(3, 132),
(3, 133),
(3, 134),
(3, 135),
(3, 136),
(3, 137),
(3, 138),
(3, 139),
(3, 140),
(3, 141),
(3, 142),
(3, 143),
(3, 144),
(3, 145),
(3, 146),
(3, 147),
(3, 148),
(3, 149),
(3, 150),
(3, 151),
(3, 152),
(3, 153),
(3, 154),
(3, 155),
(3, 156),
(3, 157),
(3, 158),
(3, 159),
(3, 160),
(3, 161),
(3, 162),
(3, 163),
(3, 164),
(3, 165),
(3, 166),
(3, 167),
(3, 168),
(3, 169),
(3, 170),
(3, 171),
(3, 172),
(3, 173),
(3, 174),
(3, 175),
(3, 176),
(3, 177),
(3, 178),
(3, 179),
(3, 180),
(3, 181),
(3, 182),
(3, 183),
(3, 184),
(3, 185),
(3, 186),
(3, 187),
(3, 188),
(3, 189),
(3, 190),
(3, 191),
(3, 192),
(3, 193),
(3, 194),
(3, 195),
(3, 197),
(3, 198),
(3, 199),
(3, 200),
(3, 201),
(3, 202),
(3, 203),
(3, 204),
(3, 205),
(3, 206),
(3, 207),
(3, 208),
(3, 209),
(3, 210),
(3, 211),
(3, 212),
(3, 213),
(3, 214),
(3, 215),
(3, 216),
(3, 217),
(3, 218),
(3, 219),
(3, 220),
(3, 221),
(3, 222),
(3, 223),
(3, 224),
(3, 225),
(3, 226),
(3, 227),
(3, 228),
(3, 229),
(3, 230),
(3, 231),
(3, 232),
(3, 233),
(3, 234),
(3, 235),
(3, 236),
(3, 237),
(3, 238),
(3, 239),
(3, 240),
(3, 241),
(3, 242),
(3, 243),
(3, 244),
(3, 245),
(3, 246),
(3, 247),
(3, 248),
(3, 249),
(3, 250),
(3, 251),
(3, 252),
(3, 253),
(3, 254),
(3, 255),
(3, 256),
(3, 257),
(3, 258),
(3, 259),
(3, 260),
(3, 261),
(3, 262),
(3, 263),
(3, 264),
(3, 265),
(3, 266),
(3, 267),
(3, 268),
(3, 269),
(3, 270),
(3, 271),
(3, 272),
(3, 273),
(3, 274),
(3, 275),
(3, 276),
(3, 277),
(3, 278),
(3, 279),
(3, 280),
(3, 281),
(3, 282),
(3, 283),
(3, 284),
(3, 285),
(3, 286),
(3, 287),
(3, 288),
(3, 289),
(3, 290),
(3, 291),
(3, 292),
(3, 293),
(3, 294),
(3, 295),
(3, 296),
(3, 297),
(3, 298),
(3, 299),
(3, 300),
(3, 301),
(3, 302),
(3, 303),
(3, 304),
(3, 305),
(3, 306),
(3, 307),
(3, 308),
(3, 309),
(3, 310),
(3, 311),
(3, 312),
(3, 313),
(3, 314),
(3, 315),
(3, 316),
(3, 317),
(3, 318),
(3, 319),
(3, 320),
(3, 321),
(3, 322),
(3, 323),
(3, 324),
(3, 325),
(3, 326),
(3, 327),
(3, 328),
(3, 329),
(3, 330),
(3, 331),
(3, 332),
(3, 333);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `pricing_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=fixed,1=hourly,2=daily',
  `guest_capacity` int(11) DEFAULT NULL,
  `service_details` text DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_category_id`, `name`, `pricing_type`, `guest_capacity`, `service_details`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Wooden Villa 101', 0, 3, 'The roof is made out of 136 larch caissons, a matching floor with this time 136 okoume wood panels, all in a perfect aligned symmetry … an architectural sandwich.\r\nA scaffolding warehouse had to be installed on-site to shelter the construction, the level of precision required to build the roof did not allow any humidity. The larch has been sanded down to acquire a refined finish usually reserved to furniture.\r\n\r\n There are no screws and no apparent nails. The use of shadow joints offers a unique sense of fluidity both inside and outside of the house.\r\n\r\nIf the standard height for sliding glass doors is 2,20m, here they reach beyond 3 meters high. The shadows of the pine forest, projected onto the interior wooden surfaces, accompany the inhabitants with different degrees of intensity all throughout the day.', 1, 1, 1, '2026-06-24 04:07:06', '2026-06-24 04:09:56'),
(2, 2, 'Petunia 1', 0, 40, 'You are most welcome to visit a dream land like Safina Park & Resort. It will be a great pleasure for us while you and your family spend your valuable time in this park for study tour, picnic, family get-together or memorable event. Due to a small country in the sense of area the facilities of recreation is very limited among the city dwellers.To relieve from a monotonous city-life we would like to offer them a natural environment with adventurous amusement. \r\n\r\n Hoping to fulfill the aim we have established a special Dream Park near the Rajshahi City, Bangladesh. The total park area about 90 Bighas is arranged with numerous attractive items. Out of this maximum area is decorated with water related items and beautiful greeneries', 1, 1, 1, '2026-06-24 04:22:55', '2026-06-24 04:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Cottage', 1, 1, 1, '2026-06-24 03:39:03', '2026-06-24 03:39:03'),
(2, 'Picnic Corner', 1, 1, 1, '2026-06-24 04:19:34', '2026-06-24 04:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `logo_alt` varchar(150) DEFAULT NULL,
  `pdf_header_img` varchar(150) DEFAULT NULL,
  `pdf_footer_img` varchar(150) DEFAULT NULL,
  `pdf_no_header_footer` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `name`, `email`, `logo`, `logo_alt`, `pdf_header_img`, `pdf_footer_img`, `pdf_no_header_footer`) VALUES
(1, 'eStore Management', 'safina@gmail.com', 'logo.png', 'Insert logo alter', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stock_ins`
--

CREATE TABLE `stock_ins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `combinations` text DEFAULT NULL,
  `stock_date` date NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `received_by` varchar(50) DEFAULT NULL,
  `given_by` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_ins`
--

INSERT INTO `stock_ins` (`id`, `purchase_id`, `supplier_id`, `purchase_item_id`, `item_id`, `warehouse_id`, `department_id`, `combinations`, `stock_date`, `quantity`, `received_by`, `given_by`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, 10, 1, 0, '\"\"', '2025-10-15', 1.00, '1', 'xyz 2', NULL, 1, 1, '2025-10-18 09:36:38', '2025-10-26 07:37:21'),
(2, 1, 2, 2, 1, 1, 0, '{\"Weight\":\"60\"}', '2025-10-13', 7.00, '1', 'xyz up', 'test', 1, 1, '2025-10-18 09:36:38', '2025-10-18 09:43:06'),
(3, 1, 2, 3, 6, 1, 0, '\"\"', '2025-10-15', 4.00, '1', 'xyz', NULL, 1, 1, '2025-10-18 09:36:38', '2025-10-18 09:36:38'),
(5, 4, 1, 6, 8, 1, 0, '\"\"', '2025-10-05', 70.00, '1', 'xyz up', NULL, 1, 1, '2025-10-23 01:26:39', '2025-10-23 01:26:39'),
(6, 4, 1, 7, 6, 1, 0, '\"\"', '2025-10-05', 18.00, '1', 'xyz up', NULL, 1, 1, '2025-10-23 01:26:39', '2025-10-23 01:26:39'),
(7, 4, 1, 8, 7, 1, 0, '\"\"', '2025-10-05', 80.00, '1', 'xyz up', NULL, 1, 1, '2025-10-23 01:26:39', '2025-10-23 01:26:39'),
(8, 5, 3, 9, 11, 1, 0, '{\"Size\":\"80 GSM\",\"Color\":\"White\"}', '2025-10-26', 2.00, '1', 'xyz up', NULL, 1, 1, '2025-10-26 07:35:37', '2025-10-26 07:35:37'),
(9, 5, 3, 10, 10, 1, 0, '\"\"', '2025-10-26', 1.00, '1', 'xyz up', NULL, 1, 1, '2025-10-26 07:35:37', '2025-10-26 07:35:37'),
(10, 5, 3, 11, 2, 1, 0, '{\"Weight\":\"80\"}', '2025-10-26', 3.00, '1', 'xyz up', NULL, 1, 1, '2025-10-26 07:35:37', '2025-10-26 07:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_name` varchar(150) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `supplier_type` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(150) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web_site` varchar(150) DEFAULT NULL,
  `status` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `contact_name`, `company_name`, `supplier_type`, `address`, `mobile`, `email`, `web_site`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Sahadat', 'AtomSoft', 2, 'Mirpur, Dhaka', '01923760310', 'sahadat39@gmail.com', NULL, 1, 1, 1, '2025-09-30 09:33:16', '2025-09-30 09:33:16'),
(2, 'Sahadat2', 'AtomSoft2', 2, 'Mirpur, Dhaka', '01923760310', 'sahadat39@gmail.com', NULL, 1, 1, 1, '2025-10-18 04:23:23', '2025-10-18 04:23:23'),
(3, 'Test up', 'Test Comp', 1, NULL, '01923760310', NULL, NULL, 1, 1, 1, '2025-10-26 06:54:42', '2025-10-26 06:55:06');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `ticket_number` varchar(20) DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `gate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `name`, `price`, `status`, `ticket_number`, `is_used`, `gate_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Nagordola', 80.00, 1, '202606220818247353', 0, NULL, 1, 1, '2026-06-22 02:18:24', '2026-06-22 02:18:24'),
(2, 'Rollar Coster', 100.00, 1, '202606220818336213', 0, NULL, 1, 1, '2026-06-22 02:18:33', '2026-06-22 02:18:33'),
(3, 'Gate Entry', 150.00, 1, '202606220818472096', 0, NULL, 1, 1, '2026-06-22 02:18:47', '2026-06-22 02:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_cash_handovers`
--

CREATE TABLE `ticket_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `gate_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_cash_handovers`
--

INSERT INTO `ticket_cash_handovers` (`id`, `user_id`, `gate_id`, `amount`, `status`, `receiver_user_id`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `remark`, `business_date`, `requested_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100.00, 'approved', 1, 1, '2026-06-29 00:07:04', NULL, NULL, NULL, '2026-06-29', '2026-06-29 00:06:58', '2026-06-29 00:06:58', '2026-06-29 00:07:04'),
(2, 1, 1, 150.00, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-29', '2026-06-29 01:35:11', '2026-06-29 01:35:11', '2026-06-29 01:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_categories`
--

CREATE TABLE `ticket_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_sales`
--

CREATE TABLE `ticket_sales` (
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `qr_code` varchar(64) DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `sale_group_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_sales`
--

INSERT INTO `ticket_sales` (`ticket_id`, `price`, `total_price`, `discount_amount`, `gate_id`, `date`, `qr_code`, `is_used`, `used_at`, `created_by`, `sale_group_token`, `created_at`, `updated_at`) VALUES
(1, 80.00, 80.00, 0.00, 1, '2026-06-22', '6A38F001B1C218194B899', 0, NULL, 1, '6A38F001B183B9B8164A7', '2026-06-22 02:19:13', '2026-06-22 02:19:13'),
(3, 150.00, 150.00, 0.00, 1, '2026-06-22', '6A38F14C21CB2E835BAEA', 1, '2026-06-22 02:26:56', 1, '6A38F14C218756AE1D025', '2026-06-22 02:24:44', '2026-06-22 02:26:56'),
(1, 80.00, 80.00, 0.00, 1, '2026-06-22', '6A38F1AA0820A2D56F32F', 1, '2026-06-22 02:26:36', 1, '6A38F1AA07E8765CDCD88', '2026-06-22 02:26:18', '2026-06-22 02:26:36'),
(2, 100.00, 100.00, 0.00, 1, '2026-06-29', '6A4200A68EDB2DE25154B', 1, '2026-06-28 23:21:06', 1, '6A4200A68E9A1E5723E39', '2026-06-28 23:20:38', '2026-06-28 23:21:06'),
(3, 150.00, 150.00, 0.00, 1, '2026-06-29', '6A4220273F00207C8F105', 0, NULL, 1, '6A4220273EC16026B77EA', '2026-06-29 01:35:03', '2026-06-29 01:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `service_id`, `name`, `start_time`, `end_time`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Full Day', '08:00:00', '18:00:00', 1000.00, 1, '2026-06-24 04:23:51', '2026-06-24 04:23:51'),
(2, 2, 'Full Day', '08:00:00', '18:00:00', 1000.00, 1, '2026-06-24 04:23:51', '2026-06-24 04:23:51'),
(3, 1, 'Full Night', '19:00:00', '07:30:00', 1200.00, 1, '2026-06-24 04:25:13', '2026-06-24 04:25:13'),
(4, 1, 'Full Day Night', '08:00:00', '07:30:00', 2000.00, 1, '2026-06-24 04:26:15', '2026-06-24 04:26:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `designation_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `supervisor_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `photo` varchar(120) DEFAULT NULL,
  `signature` varchar(120) DEFAULT NULL,
  `status` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `designation_id`, `department_id`, `supervisor_id`, `name`, `mobile_no`, `email`, `email_verified_at`, `password`, `remember_token`, `address`, `photo`, `signature`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2, 'Admin', '01923760310', 'safina@gmail.com', NULL, '$2y$10$3GVtIE7ONdHdryLMny0W/exFeII3ZFynVfxqnnAnmgMbs3ocBRKu6', 'r0vUv9ed0gNC8wNwEWO1yHeBhgOayhCwiIjTMuBit7JEnPfdkls9dnB3VN7A', NULL, '1760770727_3.-sahadat-photo.jpg', '1761131961_map-icon.png', 1, '2025-09-29 07:54:45', '2025-10-26 06:48:36'),
(2, 2, 2, 2, 1, 'Sharif', '3214324353', 'safina1@gmail.com', NULL, '$2y$10$2NuXJ/8y/XgGYEMBuoflGuN/TSVEPpL9kzZMI7jjRLtBmnqwPcO8O', NULL, NULL, '1761482634_map-icon.png', '1761482634_penguins.jpg', 1, '2025-10-26 06:43:17', '2025-10-26 06:43:54'),
(3, 3, 1, 1, 1, 'Asif Islam', '01568635198', 'a@gmail.com', NULL, '$2y$10$68nQeoj4Lzu.YAWTvbB/qu9Od70AiRR893BHYbouZHtWtU8DTPCSO', NULL, 'Dhaka', '1782205485_man-with-beard-avatar-character-isolated-icon-free-vector.jpg', NULL, 1, '2026-06-23 03:04:45', '2026-06-23 03:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_gates`
--

CREATE TABLE `user_gates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `gate_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_gates`
--

INSERT INTO `user_gates` (`id`, `user_id`, `gate_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `name`, `base_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bike', 100.00, 'active', '2026-06-21 02:26:10', '2026-06-21 02:26:10'),
(2, 'Bus', 350.00, 'active', '2026-06-21 02:48:45', '2026-06-21 02:48:45'),
(3, 'Truck', 400.00, 'active', '2026-06-22 00:04:22', '2026-06-22 00:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`) VALUES
(1, 'demo 1 up'),
(2, 'demo 2 up');

-- --------------------------------------------------------

--
-- Table structure for table `water_park_cash_handovers`
--

CREATE TABLE `water_park_cash_handovers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `water_park_counter_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `receiver_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `business_date` date NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_park_counters`
--

CREATE TABLE `water_park_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_park_counter_user`
--

CREATE TABLE `water_park_counter_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `water_park_counter_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_park_settings`
--

CREATE TABLE `water_park_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 120,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_unit_minutes` int(11) NOT NULL DEFAULT 30,
  `extra_unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `water_park_settings`
--

INSERT INTO `water_park_settings` (`id`, `duration_minutes`, `price`, `extra_unit_minutes`, `extra_unit_price`, `created_at`, `updated_at`) VALUES
(1, 120, 350.00, 30, 100.00, '2026-06-20 08:25:54', '2026-06-20 08:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `water_park_tickets`
--

CREATE TABLE `water_park_tickets` (
  `water_park_counter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ticket_number` varchar(250) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 120,
  `extra_unit_minutes` int(11) NOT NULL DEFAULT 30,
  `extra_unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','checked_in','checked_out') NOT NULL DEFAULT 'pending',
  `entry_time` datetime DEFAULT NULL,
  `exit_time` datetime DEFAULT NULL,
  `extra_minutes` int(11) NOT NULL DEFAULT 0,
  `extra_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_time_slot_id_foreign` (`time_slot_id`),
  ADD KEY `bookings_service_id_foreign` (`service_id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_counter_id_foreign` (`counter_id`),
  ADD KEY `bookings_created_by_foreign` (`created_by`),
  ADD KEY `bookings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `booking_cash_handovers`
--
ALTER TABLE `booking_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pending_handover` (`user_id`,`counter_id`,`status`),
  ADD KEY `booking_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `booking_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_counter_status` (`user_id`,`counter_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_counter_status_requested` (`counter_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_meta_fields`
--
ALTER TABLE `category_meta_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_meta_fields_service_category_id_foreign` (`service_category_id`);

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `counter_services`
--
ALTER TABLE `counter_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `counter_services_counter_id_service_id_unique` (`counter_id`,`service_id`),
  ADD KEY `counter_services_counter_id_index` (`counter_id`),
  ADD KEY `counter_services_service_id_index` (`service_id`);

--
-- Indexes for table `counter_user`
--
ALTER TABLE `counter_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `counter_user_counter_id_user_id_unique` (`counter_id`,`user_id`),
  ADD KEY `counter_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_rules`
--
ALTER TABLE `discount_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_rules_code_unique` (`code`),
  ADD KEY `discount_rules_category_id_foreign` (`category_id`),
  ADD KEY `discount_rules_service_id_foreign` (`service_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gates`
--
ALTER TABLE `gates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gate_tickets`
--
ALTER TABLE `gate_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gate_tickets_gate_id_ticket_id_unique` (`gate_id`,`ticket_id`),
  ADD KEY `gate_tickets_gate_id_index` (`gate_id`),
  ADD KEY `gate_tickets_ticket_id_index` (`ticket_id`);

--
-- Indexes for table `gear_items`
--
ALTER TABLE `gear_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_category_id_foreign` (`category_id`),
  ADD KEY `items_created_by_foreign` (`created_by`),
  ADD KEY `items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `item_pricings`
--
ALTER TABLE `item_pricings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locker_gear_cash_handovers`
--
ALTER TABLE `locker_gear_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pending_handover` (`user_id`,`locker_gear_counter_id`,`status`),
  ADD KEY `locker_gear_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `locker_gear_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_counter_status` (`user_id`,`locker_gear_counter_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_counter_status_requested` (`locker_gear_counter_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `locker_gear_counters`
--
ALTER TABLE `locker_gear_counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locker_gear_counter_user`
--
ALTER TABLE `locker_gear_counter_user`
  ADD PRIMARY KEY (`user_id`,`locker_gear_counter_id`),
  ADD KEY `locker_gear_counter_user_locker_gear_counter_id_foreign` (`locker_gear_counter_id`);

--
-- Indexes for table `locker_gear_tickets`
--
ALTER TABLE `locker_gear_tickets`
  ADD PRIMARY KEY (`ticket_number`),
  ADD UNIQUE KEY `locker_gear_tickets_qr_code_unique` (`qr_code`),
  ADD KEY `locker_gear_tickets_extra_collected_by_index` (`extra_collected_by`),
  ADD KEY `locker_gear_tickets_extra_collected_counter_id_index` (`extra_collected_counter_id`),
  ADD KEY `locker_gear_tickets_extra_collected_at_index` (`extra_collected_at`),
  ADD KEY `locker_gear_tickets_locker_gear_counter_id_foreign` (`locker_gear_counter_id`),
  ADD KEY `locker_gear_tickets_created_by_foreign` (`created_by`);

--
-- Indexes for table `locker_gear_ticket_items`
--
ALTER TABLE `locker_gear_ticket_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locker_gear_ticket_items_ticket_number_index` (`ticket_number`);

--
-- Indexes for table `locker_items`
--
ALTER TABLE `locker_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mrs_items`
--
ALTER TABLE `mrs_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mrs_items_requisition_id_foreign` (`requisition_id`),
  ADD KEY `mrs_items_requisition_item_id_foreign` (`requisition_item_id`),
  ADD KEY `mrs_items_item_id_foreign` (`item_id`),
  ADD KEY `mrs_items_user_id_foreign` (`user_id`),
  ADD KEY `mrs_items_received_by_foreign` (`received_by`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_bookings_qr_code_unique` (`qr_code`),
  ADD UNIQUE KEY `package_bookings_booking_token_unique` (`booking_token`),
  ADD KEY `package_bookings_package_counter_id_foreign` (`package_counter_id`),
  ADD KEY `package_bookings_package_id_foreign` (`package_id`),
  ADD KEY `package_bookings_created_by_foreign` (`created_by`);

--
-- Indexes for table `package_booking_items`
--
ALTER TABLE `package_booking_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_booking_items_package_booking_id_foreign` (`package_booking_id`),
  ADD KEY `package_booking_items_service_id_foreign` (`service_id`);

--
-- Indexes for table `package_cash_handovers`
--
ALTER TABLE `package_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pending_handover` (`user_id`,`counter_id`,`status`),
  ADD KEY `package_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `package_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_counter_status` (`user_id`,`counter_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_counter_status_requested` (`counter_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `package_counters`
--
ALTER TABLE `package_counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_counter_packages`
--
ALTER TABLE `package_counter_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_counter_packages_package_counter_id_package_id_unique` (`package_counter_id`,`package_id`),
  ADD KEY `package_counter_packages_package_counter_id_index` (`package_counter_id`),
  ADD KEY `package_counter_packages_package_id_index` (`package_id`);

--
-- Indexes for table `package_counter_user`
--
ALTER TABLE `package_counter_user`
  ADD PRIMARY KEY (`user_id`,`package_counter_id`),
  ADD KEY `package_counter_user_package_counter_id_foreign` (`package_counter_id`);

--
-- Indexes for table `package_items`
--
ALTER TABLE `package_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_items_package_id_foreign` (`package_id`),
  ADD KEY `package_items_service_id_foreign` (`service_id`);

--
-- Indexes for table `parking_cash_handovers`
--
ALTER TABLE `parking_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parking_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `parking_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_counter_status` (`user_id`,`parking_counter_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_counter_status_requested` (`parking_counter_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `parking_counters`
--
ALTER TABLE `parking_counters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parking_counters_status_index` (`status`);

--
-- Indexes for table `parking_counter_user`
--
ALTER TABLE `parking_counter_user`
  ADD PRIMARY KEY (`user_id`,`parking_counter_id`),
  ADD KEY `parking_counter_user_parking_counter_id_foreign` (`parking_counter_id`);

--
-- Indexes for table `parking_tickets`
--
ALTER TABLE `parking_tickets`
  ADD UNIQUE KEY `parking_tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `parking_tickets_created_by_foreign` (`created_by`),
  ADD KEY `parking_tickets_parking_counter_id_index` (`parking_counter_id`),
  ADD KEY `parking_tickets_ticket_number_index` (`ticket_number`),
  ADD KEY `parking_tickets_vehicle_number_index` (`vehicle_number`),
  ADD KEY `parking_tickets_status_index` (`status`),
  ADD KEY `parking_tickets_vehicle_id_index` (`vehicle_id`);

--
-- Indexes for table `parking_ticket_payments`
--
ALTER TABLE `parking_ticket_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parking_ticket_payments_parking_ticket_number_index` (`parking_ticket_number`),
  ADD KEY `parking_ticket_payments_payment_type_index` (`payment_type`),
  ADD KEY `parking_ticket_payments_payment_date_index` (`payment_date`),
  ADD KEY `parking_ticket_payments_created_by_index` (`created_by`),
  ADD KEY `parking_ticket_payments_parking_counter_id_index` (`parking_counter_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`);

--
-- Indexes for table `pricing_rules`
--
ALTER TABLE `pricing_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pricing_rules_service_id_foreign` (`service_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_created_by_foreign` (`created_by`),
  ADD KEY `purchases_updated_by_foreign` (`updated_by`),
  ADD KEY `purchases_purchase_person_foreign` (`purchase_person`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_item_id_foreign` (`item_id`),
  ADD KEY `purchase_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `purchase_transactions`
--
ALTER TABLE `purchase_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_transactions_given_by_foreign` (`given_by`),
  ADD KEY `purchase_transactions_created_by_foreign` (`created_by`),
  ADD KEY `purchase_transactions_updated_by_foreign` (`updated_by`),
  ADD KEY `purchase_transactions_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchase_transactions_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purposes`
--
ALTER TABLE `purposes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purposes_name_unique` (`name`);

--
-- Indexes for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisitions_purpose_id_foreign` (`purpose_id`),
  ADD KEY `requisitions_counter_sign_by_foreign` (`counter_sign_by`),
  ADD KEY `requisitions_created_by_foreign` (`created_by`),
  ADD KEY `requisitions_updated_by_foreign` (`updated_by`),
  ADD KEY `requisitions_user_id_foreign` (`user_id`);

--
-- Indexes for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_items_requisition_id_foreign` (`requisition_id`),
  ADD KEY `requisition_items_item_id_foreign` (`item_id`),
  ADD KEY `requisition_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_service_category_id_foreign` (`service_category_id`),
  ADD KEY `services_created_by_foreign` (`created_by`),
  ADD KEY `services_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_categories_created_by_foreign` (`created_by`),
  ADD KEY `service_categories_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_settings_email_unique` (`email`);

--
-- Indexes for table `stock_ins`
--
ALTER TABLE `stock_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_ins_created_by_foreign` (`created_by`),
  ADD KEY `stock_ins_updated_by_foreign` (`updated_by`),
  ADD KEY `stock_ins_supplier_id_foreign` (`supplier_id`),
  ADD KEY `stock_ins_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `stock_ins_purchase_id_foreign` (`purchase_id`),
  ADD KEY `stock_ins_item_id_foreign` (`item_id`),
  ADD KEY `stock_ins_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suppliers_created_by_foreign` (`created_by`),
  ADD KEY `suppliers_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `tickets_created_by_foreign` (`created_by`),
  ADD KEY `tickets_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ticket_cash_handovers`
--
ALTER TABLE `ticket_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pending_handover` (`user_id`,`gate_id`,`status`),
  ADD KEY `ticket_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `ticket_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_gate_status` (`user_id`,`gate_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_gate_status_requested` (`gate_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_sales`
--
ALTER TABLE `ticket_sales`
  ADD UNIQUE KEY `ticket_sales_qr_code_unique` (`qr_code`),
  ADD KEY `ticket_sales_ticket_id_foreign` (`ticket_id`),
  ADD KEY `ticket_sales_sale_group_token_index` (`sale_group_token`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_slots_service_id_foreign` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_designation_id_foreign` (`designation_id`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- Indexes for table `user_gates`
--
ALTER TABLE `user_gates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_gates_user_id_gate_id_unique` (`user_id`,`gate_id`),
  ADD KEY `user_gates_user_id_index` (`user_id`),
  ADD KEY `user_gates_gate_id_index` (`gate_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_status_index` (`status`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouses_name_unique` (`name`);

--
-- Indexes for table `water_park_cash_handovers`
--
ALTER TABLE `water_park_cash_handovers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pending_handover` (`user_id`,`water_park_counter_id`,`status`),
  ADD KEY `water_park_cash_handovers_approved_by_foreign` (`approved_by`),
  ADD KEY `water_park_cash_handovers_rejected_by_foreign` (`rejected_by`),
  ADD KEY `idx_user_counter_status` (`user_id`,`water_park_counter_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `idx_user_status_requested` (`user_id`,`status`,`requested_at`),
  ADD KEY `idx_counter_status_requested` (`water_park_counter_id`,`status`,`requested_at`),
  ADD KEY `idx_receiver_user_id` (`receiver_user_id`);

--
-- Indexes for table `water_park_counters`
--
ALTER TABLE `water_park_counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `water_park_counter_user`
--
ALTER TABLE `water_park_counter_user`
  ADD PRIMARY KEY (`user_id`,`water_park_counter_id`),
  ADD KEY `water_park_counter_user_water_park_counter_id_foreign` (`water_park_counter_id`);

--
-- Indexes for table `water_park_settings`
--
ALTER TABLE `water_park_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `water_park_tickets`
--
ALTER TABLE `water_park_tickets`
  ADD UNIQUE KEY `water_park_tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `water_park_tickets_created_by_foreign` (`created_by`),
  ADD KEY `water_park_tickets_water_park_counter_id_foreign` (`water_park_counter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_cash_handovers`
--
ALTER TABLE `booking_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category_meta_fields`
--
ALTER TABLE `category_meta_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `counters`
--
ALTER TABLE `counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `counter_services`
--
ALTER TABLE `counter_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `counter_user`
--
ALTER TABLE `counter_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discount_rules`
--
ALTER TABLE `discount_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gates`
--
ALTER TABLE `gates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gate_tickets`
--
ALTER TABLE `gate_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gear_items`
--
ALTER TABLE `gear_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `item_pricings`
--
ALTER TABLE `item_pricings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `locker_gear_cash_handovers`
--
ALTER TABLE `locker_gear_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locker_gear_counters`
--
ALTER TABLE `locker_gear_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `locker_gear_ticket_items`
--
ALTER TABLE `locker_gear_ticket_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locker_items`
--
ALTER TABLE `locker_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `mrs_items`
--
ALTER TABLE `mrs_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `package_bookings`
--
ALTER TABLE `package_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `package_booking_items`
--
ALTER TABLE `package_booking_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `package_cash_handovers`
--
ALTER TABLE `package_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `package_counters`
--
ALTER TABLE `package_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `package_counter_packages`
--
ALTER TABLE `package_counter_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `package_items`
--
ALTER TABLE `package_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parking_cash_handovers`
--
ALTER TABLE `parking_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parking_counters`
--
ALTER TABLE `parking_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parking_ticket_payments`
--
ALTER TABLE `parking_ticket_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing_rules`
--
ALTER TABLE `pricing_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `purchase_transactions`
--
ALTER TABLE `purchase_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purposes`
--
ALTER TABLE `purposes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_ins`
--
ALTER TABLE `stock_ins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_cash_handovers`
--
ALTER TABLE `ticket_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_gates`
--
ALTER TABLE `user_gates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `water_park_cash_handovers`
--
ALTER TABLE `water_park_cash_handovers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `water_park_counters`
--
ALTER TABLE `water_park_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `water_park_settings`
--
ALTER TABLE `water_park_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`),
  ADD CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `bookings_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_cash_handovers`
--
ALTER TABLE `booking_cash_handovers`
  ADD CONSTRAINT `booking_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_cash_handovers_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`),
  ADD CONSTRAINT `booking_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `category_meta_fields`
--
ALTER TABLE `category_meta_fields`
  ADD CONSTRAINT `category_meta_fields_service_category_id_foreign` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `counter_services`
--
ALTER TABLE `counter_services`
  ADD CONSTRAINT `counter_services_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `counter_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `counter_user`
--
ALTER TABLE `counter_user`
  ADD CONSTRAINT `counter_user_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `counter_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_rules`
--
ALTER TABLE `discount_rules`
  ADD CONSTRAINT `discount_rules_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `discount_rules_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gate_tickets`
--
ALTER TABLE `gate_tickets`
  ADD CONSTRAINT `gate_tickets_gate_id_foreign` FOREIGN KEY (`gate_id`) REFERENCES `gates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gate_tickets_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `locker_gear_cash_handovers`
--
ALTER TABLE `locker_gear_cash_handovers`
  ADD CONSTRAINT `locker_gear_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locker_gear_cash_handovers_locker_gear_counter_id_foreign` FOREIGN KEY (`locker_gear_counter_id`) REFERENCES `locker_gear_counters` (`id`),
  ADD CONSTRAINT `locker_gear_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locker_gear_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locker_gear_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `locker_gear_counter_user`
--
ALTER TABLE `locker_gear_counter_user`
  ADD CONSTRAINT `locker_gear_counter_user_locker_gear_counter_id_foreign` FOREIGN KEY (`locker_gear_counter_id`) REFERENCES `locker_gear_counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `locker_gear_counter_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `locker_gear_tickets`
--
ALTER TABLE `locker_gear_tickets`
  ADD CONSTRAINT `locker_gear_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `locker_gear_tickets_extra_collected_by_foreign` FOREIGN KEY (`extra_collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locker_gear_tickets_extra_collected_counter_id_foreign` FOREIGN KEY (`extra_collected_counter_id`) REFERENCES `locker_gear_counters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locker_gear_tickets_locker_gear_counter_id_foreign` FOREIGN KEY (`locker_gear_counter_id`) REFERENCES `locker_gear_counters` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `locker_gear_ticket_items`
--
ALTER TABLE `locker_gear_ticket_items`
  ADD CONSTRAINT `locker_gear_ticket_items_ticket_number_foreign` FOREIGN KEY (`ticket_number`) REFERENCES `locker_gear_tickets` (`ticket_number`) ON DELETE CASCADE;

--
-- Constraints for table `mrs_items`
--
ALTER TABLE `mrs_items`
  ADD CONSTRAINT `mrs_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mrs_items_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mrs_items_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mrs_items_requisition_item_id_foreign` FOREIGN KEY (`requisition_item_id`) REFERENCES `requisition_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mrs_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD CONSTRAINT `package_bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `package_bookings_package_counter_id_foreign` FOREIGN KEY (`package_counter_id`) REFERENCES `package_counters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `package_bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Constraints for table `package_booking_items`
--
ALTER TABLE `package_booking_items`
  ADD CONSTRAINT `package_booking_items_package_booking_id_foreign` FOREIGN KEY (`package_booking_id`) REFERENCES `package_bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_booking_items_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_cash_handovers`
--
ALTER TABLE `package_cash_handovers`
  ADD CONSTRAINT `package_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `package_cash_handovers_counter_id_foreign` FOREIGN KEY (`counter_id`) REFERENCES `package_counters` (`id`),
  ADD CONSTRAINT `package_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `package_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `package_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `package_counter_packages`
--
ALTER TABLE `package_counter_packages`
  ADD CONSTRAINT `package_counter_packages_package_counter_id_foreign` FOREIGN KEY (`package_counter_id`) REFERENCES `package_counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_counter_packages_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_counter_user`
--
ALTER TABLE `package_counter_user`
  ADD CONSTRAINT `package_counter_user_package_counter_id_foreign` FOREIGN KEY (`package_counter_id`) REFERENCES `package_counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_counter_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_items`
--
ALTER TABLE `package_items`
  ADD CONSTRAINT `package_items_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_items_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parking_cash_handovers`
--
ALTER TABLE `parking_cash_handovers`
  ADD CONSTRAINT `parking_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `parking_cash_handovers_parking_counter_id_foreign` FOREIGN KEY (`parking_counter_id`) REFERENCES `parking_counters` (`id`),
  ADD CONSTRAINT `parking_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `parking_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `parking_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parking_counter_user`
--
ALTER TABLE `parking_counter_user`
  ADD CONSTRAINT `parking_counter_user_parking_counter_id_foreign` FOREIGN KEY (`parking_counter_id`) REFERENCES `parking_counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_counter_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parking_tickets`
--
ALTER TABLE `parking_tickets`
  ADD CONSTRAINT `parking_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_tickets_parking_counter_id_foreign` FOREIGN KEY (`parking_counter_id`) REFERENCES `parking_counters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `parking_tickets_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `parking_ticket_payments`
--
ALTER TABLE `parking_ticket_payments`
  ADD CONSTRAINT `parking_ticket_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_ticket_payments_parking_counter_id_foreign` FOREIGN KEY (`parking_counter_id`) REFERENCES `parking_counters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_ticket_payments_parking_ticket_number_foreign` FOREIGN KEY (`parking_ticket_number`) REFERENCES `parking_tickets` (`ticket_number`) ON DELETE CASCADE;

--
-- Constraints for table `pricing_rules`
--
ALTER TABLE `pricing_rules`
  ADD CONSTRAINT `pricing_rules_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_purchase_person_foreign` FOREIGN KEY (`purchase_person`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `purchase_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_transactions`
--
ALTER TABLE `purchase_transactions`
  ADD CONSTRAINT `purchase_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_transactions_given_by_foreign` FOREIGN KEY (`given_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_transactions_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_transactions_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_transactions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD CONSTRAINT `requisitions_counter_sign_by_foreign` FOREIGN KEY (`counter_sign_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requisitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requisitions_purpose_id_foreign` FOREIGN KEY (`purpose_id`) REFERENCES `purposes` (`id`),
  ADD CONSTRAINT `requisitions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requisitions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD CONSTRAINT `requisition_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `requisition_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requisition_items_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `services_service_category_id_foreign` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `services_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD CONSTRAINT `service_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `service_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_ins`
--
ALTER TABLE `stock_ins`
  ADD CONSTRAINT `stock_ins_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_ins_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ins_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ins_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ins_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_ins_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_ins_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `suppliers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_cash_handovers`
--
ALTER TABLE `ticket_cash_handovers`
  ADD CONSTRAINT `ticket_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ticket_cash_handovers_gate_id_foreign` FOREIGN KEY (`gate_id`) REFERENCES `gates` (`id`),
  ADD CONSTRAINT `ticket_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ticket_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ticket_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_sales`
--
ALTER TABLE `ticket_sales`
  ADD CONSTRAINT `ticket_sales_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`);

--
-- Constraints for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD CONSTRAINT `time_slots_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `users_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`),
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_gates`
--
ALTER TABLE `user_gates`
  ADD CONSTRAINT `user_gates_gate_id_foreign` FOREIGN KEY (`gate_id`) REFERENCES `gates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_gates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_park_cash_handovers`
--
ALTER TABLE `water_park_cash_handovers`
  ADD CONSTRAINT `water_park_cash_handovers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `water_park_cash_handovers_receiver_user_id_foreign` FOREIGN KEY (`receiver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `water_park_cash_handovers_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `water_park_cash_handovers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `water_park_cash_handovers_water_park_counter_id_foreign` FOREIGN KEY (`water_park_counter_id`) REFERENCES `water_park_counters` (`id`);

--
-- Constraints for table `water_park_counter_user`
--
ALTER TABLE `water_park_counter_user`
  ADD CONSTRAINT `water_park_counter_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `water_park_counter_user_water_park_counter_id_foreign` FOREIGN KEY (`water_park_counter_id`) REFERENCES `water_park_counters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_park_tickets`
--
ALTER TABLE `water_park_tickets`
  ADD CONSTRAINT `water_park_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `water_park_tickets_water_park_counter_id_foreign` FOREIGN KEY (`water_park_counter_id`) REFERENCES `water_park_counters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
