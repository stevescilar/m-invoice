-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 19, 2026 at 02:57 PM
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
-- Database: `m-invoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_items`
--

CREATE TABLE `catalog_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `service_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `default_unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `default_buying_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_of_measure` varchar(255) NOT NULL DEFAULT 'piece',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `catalog_items`
--

INSERT INTO `catalog_items` (`id`, `company_id`, `service_category_id`, `name`, `default_unit_price`, `default_buying_price`, `unit_of_measure`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Dahua Camera', 2900.00, 0.00, 'piece', '2026-02-27 10:42:35', '2026-02-27 10:46:48'),
(2, 4, 2, 'Dahua Camera 2MP (Without Audio)', 2100.00, 1500.00, 'piece', '2026-03-16 06:31:48', '2026-03-19 09:47:59'),
(3, 4, 2, 'Dahua Camera 2MP (With Audio)', 2700.00, 1900.00, 'piece', '2026-03-16 06:33:03', '2026-03-16 06:33:46'),
(4, 4, 2, 'Dahua DVR 4 Channel', 4500.00, 3800.00, 'piece', '2026-03-16 06:36:27', '2026-03-19 09:47:59'),
(5, 4, 2, 'Dahua DVR 8 Channel', 5800.00, 4800.00, 'piece', '2026-03-16 06:37:15', '2026-03-16 06:37:15'),
(6, 4, 2, 'Dahua DVR 16 Channel', 10000.00, 8500.00, 'piece', '2026-03-16 06:38:53', '2026-03-16 07:08:22'),
(7, 4, 2, '500GB Western Digital Hard disk', 4000.00, 2500.00, 'piece', '2026-03-16 06:40:56', '2026-03-19 09:47:59'),
(8, 4, 2, '1TB Western Digital HardDisk', 6500.00, 5000.00, 'piece', '2026-03-16 06:41:34', '2026-03-16 06:41:34'),
(9, 4, 2, '3TB Western Digital Hard Disk', 10500.00, 8500.00, 'piece', '2026-03-16 06:42:23', '2026-03-16 06:42:23'),
(10, 4, 2, 'Coaxial Cable(Power & Data)  100M', 3000.00, 2400.00, 'roll', '2026-03-16 06:45:28', '2026-03-19 09:47:59'),
(11, 4, 2, 'Coaxial Cable(Power & Data)  200M', 6000.00, 4400.00, 'roll', '2026-03-16 06:46:24', '2026-03-16 07:31:42'),
(12, 4, 2, 'Coaxial Cable(Power & Data)  305M', 8500.00, 6900.00, 'roll', '2026-03-16 06:47:20', '2026-03-16 07:06:30'),
(13, 4, 2, 'PSU 5 AMP', 1500.00, 700.00, 'piece', '2026-03-16 06:48:08', '2026-03-16 06:48:08'),
(14, 4, 2, 'PSU 10 AMP', 2000.00, 1000.00, 'piece', '2026-03-16 06:48:31', '2026-03-19 09:47:59'),
(15, 4, 2, 'PSU 20 AMP', 3500.00, 1500.00, 'piece', '2026-03-16 06:49:19', '2026-03-16 06:49:19'),
(16, 4, 2, 'PSU 10 AMP - Closed', 4000.00, 2000.00, 'piece', '2026-03-16 06:50:33', '2026-03-16 06:50:33'),
(17, 4, 2, 'PSU 20 AMP - Closed', 5500.00, 2500.00, 'piece', '2026-03-16 06:51:08', '2026-03-16 07:07:43'),
(18, 4, 2, 'BNC Connectors', 30.00, 20.00, 'piece', '2026-03-16 06:53:17', '2026-03-19 09:47:59'),
(19, 4, 2, 'Power Adapters', 30.00, 20.00, 'piece', '2026-03-16 06:53:33', '2026-03-19 09:47:59'),
(20, 4, 2, 'Adapter Box', 200.00, 100.00, 'piece', '2026-03-16 06:54:09', '2026-03-19 09:47:59'),
(21, 4, 2, 'Twin Socket', 500.00, 300.00, 'piece', '2026-03-16 06:55:00', '2026-03-19 09:47:59'),
(22, 4, 2, 'Power Cable', 300.00, 100.00, 'piece', '2026-03-16 06:55:59', '2026-03-19 09:47:59'),
(23, 4, 2, 'HDMI 3M', 1500.00, 800.00, 'piece', '2026-03-16 06:56:32', '2026-03-16 07:31:42'),
(24, 4, 2, '32 inch TV Screen', 15000.00, 11500.00, 'piece', '2026-03-16 06:57:02', '2026-03-16 06:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT 0,
  `flag_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `company_id`, `name`, `phone`, `email`, `address`, `is_flagged`, `flag_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'KPMG FM', '0705549257', 'benscilar@gmail.com', 'St. Mulumba makongeni', 0, NULL, '2026-02-27 10:31:25', '2026-02-27 10:31:25'),
(2, 2, 'Caleb', '0705549257', 'test@test.com', NULL, 0, NULL, '2026-03-02 03:23:49', '2026-03-02 03:23:49'),
(3, 4, 'John Wamae', '0722251044', NULL, NULL, 0, NULL, '2026-03-12 03:15:30', '2026-03-12 03:15:30'),
(4, 3, 'TechGuy Inc', '0705549257', NULL, NULL, 0, NULL, '2026-03-12 09:44:11', '2026-03-12 09:44:11'),
(5, 4, 'Pastor  Kamau', '07377740498', NULL, NULL, 0, NULL, '2026-03-16 06:09:08', '2026-03-16 06:09:08'),
(6, 4, 'Josaya', '0711304624', NULL, 'Mountain Mall Area, Garden Estate Road', 0, NULL, '2026-03-19 09:36:41', '2026-03-19 09:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `footer_message` text DEFAULT NULL,
  `primary_color` varchar(7) NOT NULL DEFAULT '#16a34a',
  `kra_pin` varchar(255) DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_bypass` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mpesa_paybill` varchar(255) DEFAULT NULL,
  `mpesa_account` varchar(255) DEFAULT NULL,
  `mpesa_till` varchar(255) DEFAULT NULL,
  `mpesa_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `referral_code` varchar(255) DEFAULT NULL,
  `referral_count` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `phone`, `email`, `address`, `signature`, `footer_message`, `primary_color`, `kra_pin`, `owner_id`, `is_bypass`, `created_at`, `updated_at`, `mpesa_paybill`, `mpesa_account`, `mpesa_till`, `mpesa_number`, `bank_name`, `bank_account`, `bank_branch`, `referral_code`, `referral_count`) VALUES
(1, 'Test Company', 'logos/CtGYz75RNugNTraw9M73RTg1uKIDQjzpD9SMe3iL.png', '0705549257', 'benscilar@gmail.com', 'karatina 1090', NULL, 'Thank you for choosing our services', '#16a34a', NULL, 1, 0, '2026-02-27 10:18:41', '2026-02-27 11:27:46', '247247', '517451', '51857388', '0705549257', 'Equity Bank', NULL, NULL, NULL, 0),
(2, 'Muambi CO', NULL, '0705549257', 'benscilar@gmail.com', '90990', NULL, 'Thank you for choosing our services', '#16a34a', NULL, 2, 0, '2026-03-02 03:22:26', '2026-03-02 03:23:11', NULL, NULL, NULL, '0705549257', NULL, NULL, NULL, NULL, 0),
(3, 'Stephen Muambi\'s Business', NULL, '', 'stevemuambi@gmail.com', NULL, NULL, NULL, '#16a34a', NULL, 3, 0, '2026-03-10 03:30:26', '2026-03-10 03:30:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '06JT7AF8', 0),
(4, 'Microsil Systems', 'logos/nPJf8kmkMK1S2ZDc0Gc3lh5mhcI77Siu2xykAAPl.jpg', '0705549257', 'microsilcorp@gmail.com', 'ABC Place , Westlands', 'signatures/7NN039EuE5IYKzXX91KdoFZiaw7wwHvt1MgHSNI5.jpg', 'Thank you for choosing our services :  Website Development |  Digital Marketing | SEO | Web Hosting | IT & Security | CCTV Installation & Sales.', '#16a34a', NULL, 4, 0, '2026-03-12 02:53:17', '2026-03-19 10:42:22', NULL, NULL, '51857388', '0705549257', NULL, NULL, NULL, '08S9WZCQ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `company_id`, `category`, `description`, `amount`, `receipt_path`, `expense_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Transport', 'From Kitsuru to Nairobi', 3500.00, NULL, '2026-03-02', 2, '2026-03-02 05:09:04', '2026-03-02 05:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('draft','sent','paid','overdue','stalled') NOT NULL DEFAULT 'draft',
  `etr_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `vat_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `material_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `labour_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overall_margin` decimal(5,2) NOT NULL DEFAULT 0.00,
  `profit_from_quotation` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurring_frequency` enum('weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `recurring_next_date` date DEFAULT NULL,
  `recurring_ends_at` date DEFAULT NULL,
  `recurring_active` tinyint(1) NOT NULL DEFAULT 1,
  `recurring_parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recurrence_interval` enum('weekly','monthly') DEFAULT NULL,
  `next_recurrence_date` date DEFAULT NULL,
  `mpesa_code` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `company_id`, `client_id`, `invoice_number`, `issue_date`, `due_date`, `status`, `etr_enabled`, `vat_amount`, `material_cost`, `labour_cost`, `discount_amount`, `discount_percentage`, `grand_total`, `total_cost`, `total_profit`, `overall_margin`, `profit_from_quotation`, `notes`, `is_recurring`, `recurring_frequency`, `recurring_next_date`, `recurring_ends_at`, `recurring_active`, `recurring_parent_id`, `recurrence_interval`, `next_recurrence_date`, `mpesa_code`, `paid_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'INV-000001', '2026-02-27', '2026-03-02', 'overdue', 0, 0.00, 3800.00, 6000.00, 0.00, 0.00, 9800.00, 0.00, 0.00, 0.00, 0, 'Please pay before indicated time', 0, NULL, NULL, NULL, 1, NULL, 'monthly', NULL, NULL, NULL, 1, '2026-02-27 11:00:13', '2026-03-16 07:51:30'),
(4, 2, 2, 'INV-000002', '2026-03-02', '2026-03-16', 'draft', 0, 0.00, 3400.00, 0.00, 0.00, 0.00, 3400.00, 0.00, 3400.00, 100.00, 1, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 2, '2026-03-02 06:25:23', '2026-03-02 06:25:23'),
(5, 2, 2, 'INV-000003', '2026-03-02', '2026-03-16', 'paid', 0, 0.00, 9600.00, 0.00, 0.00, 0.00, 9600.00, 0.00, 9600.00, 100.00, 1, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-03-02 09:21:10', 2, '2026-03-02 06:26:01', '2026-03-02 09:21:10'),
(6, 4, 3, 'INV-000004', '2026-03-12', '2026-03-31', 'draft', 0, 0.00, 798500.00, 0.00, 0.00, 0.00, 796500.00, 0.00, 796500.00, 100.00, 0, 'The original price in China was in USD(conversion of 130)\r\nCost per Ream $ 2.26\r\nTransport Cost $100\r\nIN Kenya(Logistics) per CBM is KSH 57000( The total cbm is 6) \r\nCBM per carton is 0.02.', 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 4, '2026-03-12 05:23:03', '2026-03-19 10:26:40'),
(7, 3, 4, 'INV-000005', '2026-03-12', '2026-03-26', 'draft', 0, 0.00, 900.00, 0.00, 0.00, 0.00, 900.00, 200.00, 700.00, 77.78, 1, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 3, '2026-03-12 09:45:07', '2026-03-12 09:45:07'),
(8, 3, 4, 'INV-000006', '2026-03-12', '2026-03-12', 'overdue', 0, 0.00, 800.00, 6000.00, 0.00, 0.00, 6800.00, 0.00, 0.00, 0.00, 0, NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 3, '2026-03-12 10:16:22', '2026-03-16 07:51:50');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_downloads`
--

CREATE TABLE `invoice_downloads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `downloaded_by` bigint(20) UNSIGNED NOT NULL,
  `charged` tinyint(1) NOT NULL DEFAULT 0,
  `amount_charged` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mpesa_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `catalog_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_labour` tinyint(1) NOT NULL DEFAULT 0,
  `item_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `catalog_item_id`, `description`, `quantity`, `unit_price`, `total_price`, `is_labour`, `item_type_id`, `created_at`, `updated_at`) VALUES
(4, 4, NULL, 'Dahua Camera', 1.00, 2800.00, 2800.00, 0, NULL, '2026-03-02 06:25:23', '2026-03-02 06:25:23'),
(5, 4, NULL, '30 Metre Cable', 1.00, 600.00, 600.00, 0, NULL, '2026-03-02 06:25:23', '2026-03-02 06:25:23'),
(6, 5, NULL, 'Dahua Camera (Full Color)', 3.00, 3200.00, 9600.00, 0, NULL, '2026-03-02 06:26:01', '2026-03-02 06:26:01'),
(15, 7, NULL, 'Dahua Camera', 1.00, 900.00, 900.00, 0, NULL, '2026-03-12 09:45:07', '2026-03-12 09:45:07'),
(16, 8, NULL, 'Printing Papers 80 GSM (300 Cartons Each with 5 Reams)', 1.00, 500.00, 500.00, 0, NULL, '2026-03-12 10:16:22', '2026-03-12 10:16:22'),
(17, 8, NULL, '30 Metre Cable', 1.00, 300.00, 300.00, 0, NULL, '2026-03-12 10:16:22', '2026-03-12 10:16:22'),
(18, 8, NULL, 'Installation per camera', 4.00, 1500.00, 6000.00, 1, NULL, '2026-03-12 10:16:22', '2026-03-12 10:16:22'),
(26, 6, NULL, 'Printing Papers 80 GSM (300 Cartons Each with 5 Reams)', 1500.00, 294.00, 441000.00, 0, 16, '2026-03-19 10:26:40', '2026-03-19 10:26:40'),
(27, 6, NULL, 'Transport from Supplier to Warehouse in China(Guangzhou)', 1.00, 13000.00, 13000.00, 0, 18, '2026-03-19 10:26:40', '2026-03-19 10:26:40'),
(28, 6, NULL, 'Cost from China to Kenya Warehouse(Ready for pickup)', 6.00, 57000.00, 342000.00, 0, 18, '2026-03-19 10:26:40', '2026-03-19 10:26:40'),
(29, 6, NULL, 'Sourcing Fee(Mine)', 1.00, 2500.00, 2500.00, 0, 21, '2026-03-19 10:26:40', '2026-03-19 10:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_reminders`
--

CREATE TABLE `invoice_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `reminder_type` enum('before_due','on_due','after_due') NOT NULL,
  `scheduled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_reminders`
--

INSERT INTO `invoice_reminders` (`id`, `invoice_id`, `reminder_type`, `scheduled_at`, `sent_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'before_due', '2026-03-02 09:41:34', '2026-03-02 06:41:34', 'sent', '2026-02-27 11:00:13', '2026-03-02 06:41:34'),
(2, 1, 'on_due', '2026-03-02 09:41:36', '2026-03-02 06:41:36', 'sent', '2026-02-27 11:00:13', '2026-03-02 06:41:36'),
(3, 1, 'after_due', '2026-03-08 21:00:00', NULL, 'pending', '2026-02-27 11:00:13', '2026-02-27 11:00:13'),
(4, 6, 'before_due', '2026-03-27 21:00:00', NULL, 'pending', '2026-03-12 05:23:03', '2026-03-12 05:23:03'),
(5, 6, 'on_due', '2026-03-30 21:00:00', NULL, 'pending', '2026-03-12 05:23:03', '2026-03-12 05:23:03'),
(6, 6, 'after_due', '2026-04-06 21:00:00', NULL, 'pending', '2026-03-12 05:23:03', '2026-03-12 05:23:03'),
(7, 8, 'before_due', '2026-03-08 21:00:00', NULL, 'pending', '2026-03-12 10:16:22', '2026-03-12 10:16:22'),
(8, 8, 'on_due', '2026-03-11 21:00:00', NULL, 'pending', '2026-03-12 10:16:22', '2026-03-12 10:16:22'),
(9, 8, 'after_due', '2026-03-18 21:00:00', NULL, 'pending', '2026-03-12 10:16:22', '2026-03-12 10:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `item_types`
--

CREATE TABLE `item_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#6b7280',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_types`
--

INSERT INTO `item_types` (`id`, `company_id`, `name`, `color`, `is_default`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Material', '#16a34a', 1, 1, 0, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(2, 1, 'Labour', '#2563eb', 0, 1, 1, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(3, 1, 'Transport', '#f97316', 0, 1, 2, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(4, 1, 'Accommodation', '#7c3aed', 0, 1, 3, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(5, 1, 'Fuel', '#dc2626', 0, 1, 4, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(6, 2, 'Material', '#16a34a', 1, 1, 0, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(7, 2, 'Labour', '#2563eb', 0, 1, 1, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(8, 2, 'Transport', '#f97316', 0, 1, 2, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(9, 2, 'Accommodation', '#7c3aed', 0, 1, 3, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(10, 2, 'Fuel', '#dc2626', 0, 1, 4, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(11, 3, 'Material', '#16a34a', 1, 1, 0, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(12, 3, 'Labour', '#2563eb', 0, 1, 1, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(13, 3, 'Transport', '#f97316', 0, 1, 2, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(14, 3, 'Accommodation', '#7c3aed', 0, 1, 3, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(15, 3, 'Fuel', '#dc2626', 0, 1, 4, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(16, 4, 'Material', '#16a34a', 1, 1, 0, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(17, 4, 'Labour', '#2563eb', 0, 1, 1, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(18, 4, 'Transport', '#f97316', 0, 1, 2, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(19, 4, 'Accommodation', '#7c3aed', 0, 1, 3, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(20, 4, 'Fuel', '#dc2626', 0, 1, 4, '2026-03-12 04:07:50', '2026-03-12 04:07:50'),
(21, 4, 'Sourcing Fee', '#6b7280', 0, 1, 5, '2026-03-12 05:04:25', '2026-03-12 05:04:25'),
(22, 4, 'Logistics', '#6b7280', 0, 1, 6, '2026-03-12 05:23:32', '2026-03-12 05:23:32');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_27_111217_create_permission_tables', 1),
(5, '2026_02_27_111326_create_media_table', 1),
(6, '2026_02_27_111343_create_personal_access_tokens_table', 1),
(7, '2026_02_27_112130_create_companies_table', 1),
(8, '2026_02_27_112131_create_subscriptions_table', 1),
(9, '2026_02_27_112131_create_users_update_table', 1),
(10, '2026_02_27_112132_create_subscription_transactions_table', 1),
(11, '2026_02_27_112133_create_clients_table', 1),
(12, '2026_02_27_112133_create_service_categories_table', 1),
(13, '2026_02_27_112133_create_zzz_catalog_items_table', 1),
(14, '2026_02_27_112134_create_invoices_table', 1),
(15, '2026_02_27_112135_create_invoice_items_table', 1),
(16, '2026_02_27_112136_create_quotations_table', 1),
(17, '2026_02_27_112137_create_quotation_items_table', 1),
(18, '2026_02_27_112138_create_expenses_table', 1),
(19, '2026_02_27_112138_create_invoice_reminders_table', 1),
(20, '2026_02_27_112139_create_invoice_downloads_table', 1),
(21, '2026_02_27_112139_create_notifications_table', 1),
(22, '2026_02_27_112140_create_admin_users_table', 1),
(23, '2026_02_27_141251_add_payment_details_to_companies_table', 2),
(24, '2026_02_28_214325_add_buying_price_to_quotation_items_table', 3),
(25, '2026_03_02_062657_add_profit_columns_to_quotations_table', 3),
(26, '2026_03_02_073032_add_buying_price_to_catalog_items_table', 4),
(27, '2026_03_02_073445_add_profit_columns_to_invoices_table', 5),
(28, '2026_03_02_100642_add_bypass_code_to_companies_table', 6),
(29, '2026_03_02_101044_update_subscriptions_for_trial', 7),
(30, '2026_03_02_102324_add_checkout_fields_to_subscription_transactions_table', 8),
(31, '2026_03_03_085057_update_plan_enum_in_subscriptions_table', 9),
(32, '2026_03_03_135028_add_google_fields_to_users_table', 9),
(33, '2026_03_04_074931_create_staff_invitations_table', 9),
(34, '2026_03_04_085447_add_referral_code_to_companies_table', 9),
(35, '2026_03_04_113522_make_password_nullable_in_users_table', 9),
(36, '2026_03_04_122327_add_recurring_fields_to_invoices_table', 9),
(37, '2026_03_12_065714_create_item_types_table', 10),
(38, '2026_03_12_065809_add_item_type_id_to_invoice_items_table', 10),
(39, '2026_03_16_105732_add_expired_to_quotations_status', 11),
(40, '2026_03_16_133827_add_primary_color_to_companies', 12),
(41, '2026_03_19_130733_add_discount_to_invoices_quotations', 13);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `quotation_number` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('draft','sent','approved','rejected','converted','expired') NOT NULL DEFAULT 'draft',
  `material_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `labour_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overall_margin` decimal(5,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `converted_invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `company_id`, `client_id`, `quotation_number`, `issue_date`, `expiry_date`, `status`, `material_cost`, `labour_cost`, `discount_amount`, `discount_percentage`, `grand_total`, `total_cost`, `total_profit`, `overall_margin`, `notes`, `converted_invoice_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'QT-000001', '2026-03-02', NULL, 'converted', 3400.00, 0.00, 0.00, 0.00, 3400.00, 0.00, 3400.00, 100.00, NULL, 4, 2, '2026-03-02 03:27:37', '2026-03-02 06:25:23'),
(2, 2, 2, 'QT-000002', '2026-03-02', NULL, 'converted', 9600.00, 0.00, 0.00, 0.00, 9600.00, 0.00, 9600.00, 100.00, NULL, 5, 2, '2026-03-02 06:02:24', '2026-03-02 06:26:01'),
(3, 2, 2, 'QT-000003', '2026-03-02', '2026-03-13', 'expired', 1500.00, 0.00, 0.00, 0.00, 1500.00, 1100.00, 400.00, 26.67, 'Let us know', NULL, 2, '2026-03-02 06:08:25', '2026-03-16 07:58:07'),
(5, 4, 3, 'QT-000004', '2026-03-12', '2026-04-01', 'draft', 798500.00, 0.00, 0.00, 0.00, 798500.00, 798500.00, 0.00, 0.00, 'The original price in China was in USD(conversion of 130)\r\nCost per Ream $ 2.26\r\nTransport Cost $100\r\nIN Kenya(Logistics) per CBM is KSH 57000( The total cbm is 6) \r\nCBM per carton is 0.02.S', NULL, 4, '2026-03-12 05:40:50', '2026-03-12 05:41:42'),
(6, 3, 4, 'QT-000005', '2026-03-12', '2026-03-13', 'converted', 900.00, 0.00, 0.00, 0.00, 900.00, 200.00, 700.00, 77.78, NULL, 7, 3, '2026-03-12 09:44:37', '2026-03-12 09:45:07'),
(7, 3, 4, 'QT-000006', '2026-03-12', '2026-03-12', 'expired', 19500.00, 0.00, 0.00, 0.00, 19500.00, 19000.00, 500.00, 2.56, NULL, NULL, 3, '2026-03-12 10:18:54', '2026-03-16 07:58:12'),
(9, 4, 5, 'QT-000007', '2026-03-16', '2026-03-30', 'draft', 35860.00, 6000.00, 0.00, 0.00, 41860.00, 26540.00, 15320.00, 36.60, 'There might be an adjustment or additional  charges due to additional items like piping, trunking( We term them as miscellaneous though not added to this quotation)', NULL, 4, '2026-03-16 07:31:42', '2026-03-16 08:21:49'),
(10, 4, 5, 'QT-000008', '2026-03-16', '2026-04-15', 'draft', 54420.00, 12000.00, 0.00, 0.00, 66420.00, 43680.00, 22740.00, 34.24, 'There might be an adjustment or additional  charges due to additional items like piping, trunking( We term them as miscellaneous though not added to this quotation)', NULL, 4, '2026-03-16 08:39:08', '2026-03-16 08:45:52'),
(11, 4, 6, 'QT-000009', '2026-03-19', '2026-03-26', 'draft', 30125.00, 4500.00, 2625.00, 0.00, 32000.00, 18080.00, 13920.00, 43.50, 'Prices Might change due to extra requirements / materials needed after the site visit', NULL, 4, '2026-03-19 09:47:59', '2026-03-19 10:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quotation_id` bigint(20) UNSIGNED NOT NULL,
  `catalog_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buying_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `profit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `margin_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_labour` tinyint(1) NOT NULL DEFAULT 0,
  `item_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `catalog_item_id`, `description`, `quantity`, `unit_price`, `buying_price`, `profit`, `margin_percentage`, `total_price`, `is_labour`, `item_type_id`, `created_at`, `updated_at`) VALUES
(5, 3, NULL, 'Cameras', 1.00, 1500.00, 1100.00, 400.00, 26.67, 1500.00, 0, NULL, '2026-03-02 06:08:25', '2026-03-02 06:08:25'),
(6, 1, NULL, 'Dahua Camera', 1.00, 2800.00, 0.00, 2800.00, 100.00, 2800.00, 0, NULL, '2026-03-02 06:12:33', '2026-03-02 06:12:33'),
(7, 1, NULL, '30 Metre Cable', 1.00, 600.00, 0.00, 600.00, 100.00, 600.00, 0, NULL, '2026-03-02 06:12:33', '2026-03-02 06:12:33'),
(8, 2, NULL, 'Dahua Camera (Full Color)', 3.00, 3200.00, 0.00, 9600.00, 100.00, 9600.00, 0, NULL, '2026-03-02 06:25:54', '2026-03-02 06:25:54'),
(14, 5, NULL, 'Printing Papers 80 GSM (300 Cartons Each with 5 Reams)', 1500.00, 294.00, 294.00, 0.00, 0.00, 441000.00, 0, NULL, '2026-03-12 05:41:42', '2026-03-12 05:41:42'),
(15, 5, NULL, 'Transport from Supplier to Warehouse in China(Guangzhou)', 1.00, 13000.00, 13000.00, 0.00, 0.00, 13000.00, 0, NULL, '2026-03-12 05:41:42', '2026-03-12 05:41:42'),
(16, 5, NULL, 'Cost from China to Kenya Warehouse(Ready for pickup)', 6.00, 57000.00, 57000.00, 0.00, 0.00, 342000.00, 0, NULL, '2026-03-12 05:41:42', '2026-03-12 05:41:42'),
(17, 5, NULL, 'Sourcing Fee(Mine)', 1.00, 2500.00, 2500.00, 0.00, 0.00, 2500.00, 0, NULL, '2026-03-12 05:41:42', '2026-03-12 05:41:42'),
(18, 6, NULL, 'Dahua Camera', 1.00, 900.00, 200.00, 700.00, 77.78, 900.00, 0, NULL, '2026-03-12 09:44:37', '2026-03-12 09:44:37'),
(22, 7, NULL, 'Printing Papers 80 GSM (300 Cartons Each with 5 Reams)', 1.00, 1500.00, 1000.00, 500.00, 33.33, 1500.00, 0, NULL, '2026-03-12 10:21:07', '2026-03-12 10:21:07'),
(23, 7, NULL, 'Transport from Supplier to Warehouse in China(Guangzhou)', 1.00, 13000.00, 13000.00, 0.00, 0.00, 13000.00, 0, NULL, '2026-03-12 10:21:07', '2026-03-12 10:21:07'),
(24, 7, NULL, 'Installation per camera', 1.00, 5000.00, 5000.00, 0.00, 0.00, 5000.00, 0, NULL, '2026-03-12 10:21:07', '2026-03-12 10:21:07'),
(52, 9, 2, 'Dahua Camera 2MP (Without Audio)', 4.00, 2600.00, 1500.00, 4400.00, 42.31, 10400.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(53, 9, 7, '500GB Western Digital Hard disk', 1.00, 5000.00, 2500.00, 2500.00, 50.00, 5000.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(54, 9, 11, 'Coaxial Cable(Power & Data)  200M', 1.00, 6000.00, 4400.00, 1600.00, 26.67, 6000.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(55, 9, 14, 'PSU 10 AMP', 1.00, 2500.00, 1000.00, 1500.00, 60.00, 2500.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(56, 9, 18, 'BNC Connectors', 8.00, 30.00, 20.00, 80.00, 33.33, 240.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(57, 9, 19, 'Power Adapters', 4.00, 30.00, 20.00, 40.00, 33.33, 120.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(58, 9, 20, 'Adapter Box', 4.00, 200.00, 100.00, 400.00, 50.00, 800.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(59, 9, 21, 'Twin Socket', 1.00, 500.00, 300.00, 200.00, 40.00, 500.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(60, 9, 22, 'Power Cable', 1.00, 300.00, 100.00, 200.00, 66.67, 300.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(61, 9, 23, 'HDMI 3M', 1.00, 1500.00, 800.00, 700.00, 46.67, 1500.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(62, 9, NULL, 'Camera Installation (Per Camera)', 4.00, 1500.00, 1000.00, 2000.00, 33.33, 6000.00, 1, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(63, 9, NULL, 'Fuel', 1.00, 3000.00, 3000.00, 0.00, 0.00, 3000.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(64, 9, 4, 'Dahua DVR 4 Channel', 1.00, 5500.00, 3800.00, 1700.00, 30.91, 5500.00, 0, 16, '2026-03-16 08:21:49', '2026-03-16 08:21:49'),
(78, 10, 2, 'Dahua Camera 2MP (Without Audio)', 8.00, 2600.00, 1500.00, 8800.00, 42.31, 20800.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(79, 10, 7, '1TB Western Digital Hard disk', 1.00, 7500.00, 5000.00, 2500.00, 33.33, 7500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(80, 10, 11, 'Coaxial Cable(Power & Data)  305M', 1.00, 8500.00, 6900.00, 1600.00, 18.82, 8500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(81, 10, 14, 'PSU 20 AMP', 1.00, 3500.00, 1500.00, 2000.00, 57.14, 3500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(82, 10, 18, 'BNC Connectors', 16.00, 30.00, 20.00, 160.00, 33.33, 480.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(83, 10, 19, 'Power Adapters', 8.00, 30.00, 20.00, 80.00, 33.33, 240.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(84, 10, 20, 'Adapter Box', 8.00, 200.00, 100.00, 800.00, 50.00, 1600.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(85, 10, 21, 'Twin Socket', 1.00, 500.00, 300.00, 200.00, 40.00, 500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(86, 10, 22, 'Power Cable', 1.00, 300.00, 100.00, 200.00, 66.67, 300.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(87, 10, 23, 'HDMI 3M', 1.00, 1500.00, 800.00, 700.00, 46.67, 1500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(88, 10, NULL, 'Camera Installation (Per Camera)', 8.00, 1500.00, 1000.00, 4000.00, 33.33, 12000.00, 1, 17, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(89, 10, NULL, 'Fuel', 1.00, 3000.00, 3000.00, 0.00, 0.00, 3000.00, 0, 20, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(90, 10, 4, 'Dahua DVR 8 Channel', 1.00, 6500.00, 4800.00, 1700.00, 26.15, 6500.00, 0, 16, '2026-03-16 08:45:52', '2026-03-16 08:45:52'),
(147, 11, 2, 'Dahua Camera 2MP (Without Audio)', 3.00, 3000.00, 1500.00, 4500.00, 50.00, 9000.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(148, 11, 4, 'Dahua DVR 4 Channel', 1.00, 6500.00, 3800.00, 2700.00, 41.54, 6500.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(149, 11, 7, '500GB Western Digital Hard disk', 1.00, 5500.00, 2500.00, 3000.00, 54.55, 5500.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(150, 11, 10, 'Coaxial Cable(Power & Data)  100M', 1.00, 3000.00, 2400.00, 600.00, 20.00, 3000.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(151, 11, 18, 'BNC Connectors', 6.00, 25.00, 20.00, 30.00, 20.00, 150.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(152, 11, 14, 'PSU 10 AMP', 1.00, 4500.00, 1000.00, 3500.00, 77.78, 4500.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(153, 11, 20, 'Adapter Box', 3.00, 200.00, 100.00, 300.00, 50.00, 600.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(154, 11, 19, 'Power Adapters', 3.00, 25.00, 20.00, 15.00, 20.00, 75.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(155, 11, NULL, 'Camera Installation (Per Camera)', 3.00, 1500.00, 1000.00, 1500.00, 33.33, 4500.00, 1, 17, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(156, 11, 22, 'Power Cable', 1.00, 300.00, 100.00, 200.00, 66.67, 300.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07'),
(157, 11, 21, 'Twin Socket', 1.00, 500.00, 300.00, 200.00, 40.00, 500.00, 0, 16, '2026-03-19 10:42:07', '2026-03-19 10:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `company_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'CCTV Sales and Installation', 'Home Security Services', '2026-02-27 10:42:02', '2026-02-27 10:42:02'),
(2, 4, 'CCTV Sales and Installation', 'Home Security Materials', '2026-03-16 06:27:57', '2026-03-16 06:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0Ilx21yi1gbPRxN6ui9awSXMVO50eI8rboznYgwp', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUDZvMmZ6SG02VXNvVnpaazFGVHlwdGlmTVVrQnQ4bTdkb0pNV0ZVaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdXRoL2dvb2dsZSI7czo1OiJyb3V0ZSI7czoxMToiYXV0aC5nb29nbGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjEwOiJnb29nbGVfcmVmIjtOO3M6NToic3RhdGUiO3M6NDA6ImdxNUd4b0lLRlpQZ0NYRFdLekt0QkVSeGV4M3VKVHdMV3lYS0NwN0wiO30=', 1773923604),
('jE1QxzCUMmeF2mMtPo0yQSAtpYLniyt5VKe6LVOh', NULL, '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMk1VVzRoZXFGeDFUcERUNWxZNU5ocU1FQXdxUDFRNTNFaWhBb0U2TCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTY6Imh0dHA6Ly9sb2NhbGhvc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773928626),
('w9T1Vo5hz1shHjjvKNe53gLY5CCw5qYbKG8e6hST', 4, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiU05zcnd4SEZQWDBuUzRLRU9oYzU5N2x2ektaT05HVDNKUkNQU0ttZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9xdW90YXRpb25zLzExL2Rvd25sb2FkIjtzOjU6InJvdXRlIjtzOjE5OiJxdW90YXRpb25zLmRvd25sb2FkIjt9czoxMDoiZ29vZ2xlX3JlZiI7TjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1773928497);

-- --------------------------------------------------------

--
-- Table structure for table `staff_invitations`
--

CREATE TABLE `staff_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `invited_by` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `status` enum('pending','accepted','expired') NOT NULL DEFAULT 'pending',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `plan` enum('trial','free','per_invoice','monthly','yearly') NOT NULL DEFAULT 'trial',
  `status` enum('trial','active','expired','cancelled') NOT NULL DEFAULT 'trial',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `on_trial` tinyint(1) NOT NULL DEFAULT 1,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `company_id`, `plan`, `status`, `trial_ends_at`, `on_trial`, `auto_renew`, `starts_at`, `ends_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'free', 'active', NULL, 1, 0, '2026-02-27 10:18:41', NULL, '2026-02-27 10:18:41', '2026-02-27 10:18:41'),
(2, 2, 'free', 'active', NULL, 1, 0, '2026-03-02 03:22:26', NULL, '2026-03-02 03:22:26', '2026-03-02 03:22:26'),
(3, 3, 'trial', 'trial', '2026-03-13 03:30:26', 1, 1, '2026-03-10 03:30:26', NULL, '2026-03-10 03:30:26', '2026-03-10 03:30:26'),
(4, 4, 'yearly', 'active', '2026-03-15 02:53:17', 0, 1, '2026-03-12 02:53:17', '2036-03-16 06:03:36', '2026-03-12 02:53:17', '2026-03-16 06:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_transactions`
--

CREATE TABLE `subscription_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `plan` enum('monthly','yearly') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `mpesa_code` varchar(255) DEFAULT NULL,
  `checkout_request_id` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `status` enum('success','failed','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role` enum('owner','staff') NOT NULL DEFAULT 'owner',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `company_id`, `role`, `is_active`) VALUES
(1, 'Test User', 'test@test.com', NULL, NULL, NULL, '$2y$12$Vnee8PT9/7LZ9SbvpCdS2.OF.WlE0flLO7NRQ/Hueg4TvS.tj/YDi', NULL, '2026-02-27 10:18:41', '2026-02-27 10:18:41', 1, 'owner', 1),
(2, 'Sloane', 'sloane@gmail.com', NULL, NULL, NULL, '$2y$12$lmRbji8LE7cqXFG/yJmPyeXGArYo4Sr.CnWomQDkN9DTSXFNdF0L2', NULL, '2026-03-02 03:22:26', '2026-03-02 03:22:26', 2, 'owner', 1),
(3, 'Stephen Muambi', 'stevemuambi@gmail.com', '113301661016062146465', 'https://lh3.googleusercontent.com/a/ACg8ocL-7y2914oHyeMZoFTe6rnhD74PruM2ll6B1XHFeqeTF7hOC29O=s96-c', '2026-03-10 03:30:26', NULL, NULL, '2026-03-10 03:30:26', '2026-03-10 03:30:26', 3, 'owner', 1),
(4, 'Microsil Systems', 'microsilcorp@gmail.com', '107690960301917223039', 'https://lh3.googleusercontent.com/a/ACg8ocJTdKmro-NKE3PCX8wl52glywkiPadLnAqy9rSaUL0LRQ7YkuEc=s96-c', '2026-03-12 02:53:17', NULL, NULL, '2026-03-12 02:53:17', '2026-03-12 02:53:17', 4, 'owner', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_email_unique` (`email`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `catalog_items`
--
ALTER TABLE `catalog_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catalog_items_company_id_foreign` (`company_id`),
  ADD KEY `catalog_items_service_category_id_foreign` (`service_category_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_company_id_foreign` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_referral_code_unique` (`referral_code`),
  ADD KEY `companies_owner_id_foreign` (`owner_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_company_id_foreign` (`company_id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_company_id_foreign` (`company_id`),
  ADD KEY `invoices_client_id_foreign` (`client_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`),
  ADD KEY `invoices_recurring_parent_id_foreign` (`recurring_parent_id`);

--
-- Indexes for table `invoice_downloads`
--
ALTER TABLE `invoice_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_downloads_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_downloads_company_id_foreign` (`company_id`),
  ADD KEY `invoice_downloads_downloaded_by_foreign` (`downloaded_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_items_catalog_item_id_foreign` (`catalog_item_id`),
  ADD KEY `invoice_items_item_type_id_foreign` (`item_type_id`);

--
-- Indexes for table `invoice_reminders`
--
ALTER TABLE `invoice_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_reminders_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_types_company_id_foreign` (`company_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_company_id_foreign` (`company_id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_quotation_number_unique` (`quotation_number`),
  ADD KEY `quotations_company_id_foreign` (`company_id`),
  ADD KEY `quotations_client_id_foreign` (`client_id`),
  ADD KEY `quotations_converted_invoice_id_foreign` (`converted_invoice_id`),
  ADD KEY `quotations_created_by_foreign` (`created_by`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_items_quotation_id_foreign` (`quotation_id`),
  ADD KEY `quotation_items_catalog_item_id_foreign` (`catalog_item_id`),
  ADD KEY `quotation_items_item_type_id_foreign` (`item_type_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_categories_company_id_foreign` (`company_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `staff_invitations`
--
ALTER TABLE `staff_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_invitations_token_unique` (`token`),
  ADD KEY `staff_invitations_company_id_foreign` (`company_id`),
  ADD KEY `staff_invitations_invited_by_foreign` (`invited_by`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_company_id_foreign` (`company_id`);

--
-- Indexes for table `subscription_transactions`
--
ALTER TABLE `subscription_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_transactions_company_id_foreign` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_company_id_foreign` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catalog_items`
--
ALTER TABLE `catalog_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice_downloads`
--
ALTER TABLE `invoice_downloads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `invoice_reminders`
--
ALTER TABLE `invoice_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_invitations`
--
ALTER TABLE `staff_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subscription_transactions`
--
ALTER TABLE `subscription_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `catalog_items`
--
ALTER TABLE `catalog_items`
  ADD CONSTRAINT `catalog_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `catalog_items_service_category_id_foreign` FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_recurring_parent_id_foreign` FOREIGN KEY (`recurring_parent_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_downloads`
--
ALTER TABLE `invoice_downloads`
  ADD CONSTRAINT `invoice_downloads_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_downloads_downloaded_by_foreign` FOREIGN KEY (`downloaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_downloads_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_catalog_item_id_foreign` FOREIGN KEY (`catalog_item_id`) REFERENCES `catalog_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_item_type_id_foreign` FOREIGN KEY (`item_type_id`) REFERENCES `item_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_reminders`
--
ALTER TABLE `invoice_reminders`
  ADD CONSTRAINT `invoice_reminders_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_types`
--
ALTER TABLE `item_types`
  ADD CONSTRAINT `item_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotations_converted_invoice_id_foreign` FOREIGN KEY (`converted_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_catalog_item_id_foreign` FOREIGN KEY (`catalog_item_id`) REFERENCES `catalog_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotation_items_item_type_id_foreign` FOREIGN KEY (`item_type_id`) REFERENCES `item_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD CONSTRAINT `service_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_invitations`
--
ALTER TABLE `staff_invitations`
  ADD CONSTRAINT `staff_invitations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_transactions`
--
ALTER TABLE `subscription_transactions`
  ADD CONSTRAINT `subscription_transactions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
