-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 02:32 PM
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
-- Database: `laravel_reconciliation`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_statement_pdfs`
--

CREATE TABLE `bank_statement_pdfs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','done','failed') DEFAULT 'pending',
  `error_message` varchar(1000) DEFAULT NULL,
  `records_inserted` int(11) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_statement_pdfs`
--

INSERT INTO `bank_statement_pdfs` (`id`, `original_filename`, `stored_path`, `password`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(2, '8186206404745040_23012025_unlocked.pdf', 'bankstatements/u3VKlrlJfpK2REVQWpQ7Dyd8JFrDNaxajp2Bvlbb.pdf', NULL, 'done', NULL, 1, '2026-04-01 05:32:49', '2026-04-01 05:18:00', '2026-04-01 05:32:49'),
(3, '8186206404745040_23012025.pdf', 'bankstatements/d8g9ALZsOG8CAogmxnGFxm4iaYUkWFj7JrNDhPMJ.pdf', '190319911631', 'done', NULL, 1, '2026-04-01 05:44:51', '2026-04-01 05:37:17', '2026-04-01 05:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `bank_transactions`
--

CREATE TABLE `bank_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `statement_date` date DEFAULT NULL,
  `transaction_details` text NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `is_merged` tinyint(1) DEFAULT 0,
  `merged_name` varchar(255) DEFAULT NULL,
  `merged_group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_transactions`
--

INSERT INTO `bank_transactions` (`id`, `pdf_filename`, `transaction_date`, `statement_date`, `transaction_details`, `amount`, `type`, `is_merged`, `merged_name`, `merged_group_id`, `created_at`, `updated_at`) VALUES
(1, '8186206404745040_23012025_unlocked.pdf', '2024-12-30', '2025-01-23', 'PAYMENT RECEIVED 00 0DP 014365 000 905RjZoe6', 4485.00, 'credit', 0, NULL, NULL, '2026-04-01 05:32:49', '2026-04-01 05:32:49'),
(2, '8186206404745040_23012025.pdf', '2024-12-30', '2025-01-23', 'PAYMENT RECEIVED 00 0DP 014365 000 905RjZoe6', 4485.00, 'credit', 0, NULL, NULL, '2026-04-01 05:44:51', '2026-04-01 05:44:51');

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
-- Table structure for table `godaddy_receipts`
--

CREATE TABLE `godaddy_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `domain_name` varchar(255) DEFAULT NULL,
  `icann_fee` decimal(10,2) DEFAULT 0.00,
  `length` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `order_total` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'INR',
  `payment_category` varchar(255) DEFAULT NULL,
  `payment_sub_category` varchar(255) DEFAULT NULL,
  `source_filename` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `godaddy_receipts`
--

INSERT INTO `godaddy_receipts` (`id`, `receipt_number`, `order_date`, `product_name`, `domain_name`, `icann_fee`, `length`, `subtotal`, `tax_amount`, `order_total`, `currency`, `payment_category`, `payment_sub_category`, `source_filename`, `created_at`, `updated_at`) VALUES
(1, '4040453619', '2026-03-25', '.IN (.CO.IN) Domain Transfer', 'paras.co.in', 0.00, '1 Year', 499.00, 0.00, 499.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(2, '4028790309', '2026-03-02', '.COM Domain Renewal', 'blasttechsolution.com', 88.00, '5 Year', 6396.00, 0.00, 6484.00, 'INR', 'InStoreCredit', 'InStoreCredit', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(3, '4027217450', '2026-02-28', '.COM Domain Registration', 'blasttechsolutuons.com', 88.00, '5 Year', 6396.00, 0.00, 6484.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(4, '4008945255', '2026-02-03', '.COM Domain Renewal', 'EVENTSOFHAPPINESS.com', 17.60, '1 Year', 1279.20, 0.00, 1296.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(5, '4008945255', '2026-02-03', '.COM Domain Renewal', 'MADHURASHCARDS.com', 17.60, '1 Year', 1279.20, 0.00, 1296.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(6, '4008945255', '2026-02-03', '.COM Domain Renewal', 'PHOENIXRESORTRAJKOT.com', 17.60, '1 Year', 1279.20, 0.00, 1296.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(7, '3994678825', '2026-02-03', '.COM Domain Transfer', 'parastool.com', 17.60, '1 Year', 999.00, 0.00, 1016.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(8, '3988620438', '2026-01-15', '.COM Domain Transfer', 'jaysonsexports.com', 17.60, '1 Year', 999.00, 0.00, 1016.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(9, '3982300165', '2026-01-09', '.COM Domain Registration', 'jayambetoursrajkot.com', 17.60, '1 Year', 999.00, 0.00, 1016.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(10, '3980343237', '2026-01-01', '.IN Domain Renewal', 'DAZED.in', 0.00, '1 Year', 899.00, 0.00, 899.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(11, '3975824230', '2025-12-29', '.IN Domain Renewal', 'anandiappliances.in', 0.00, '1 Year', 649.00, 0.00, 649.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(12, '3964553737', '2025-12-22', '.COM Domain Renewal', 'THEYAGREED.com', 17.60, '1 Year', 1499.00, 0.00, 1516.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(13, '3964550069', '2025-12-08', '.COM Domain Renewal', 'OWIEDREAMCLUB.com', 17.60, '1 Year', 1499.00, 0.00, 1516.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(14, '3935228131', '2025-11-17', '.COM Domain Renewal', 'WEBCLOUDFX.com', 17.60, '1 Year', 1199.20, 0.00, 1216.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(15, '3935228131', '2025-11-17', '.COM Domain Renewal', 'WEBSMANIAC.COM', 17.60, '1 Year', 1199.20, 0.00, 1216.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(16, '3935228131', '2025-11-17', '.COM Domain Renewal', 'WHYBUGS.COM', 17.60, '1 Year', 1199.20, 0.00, 1216.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(17, '3927962145', '2025-10-30', '.COM Domain Renewal', 'JITHYDRAULIC.com', 17.60, '1 Year', 1199.20, 0.00, 1216.80, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(18, '3927957929', '2025-10-21', '.COM Domain Renewal', 'STARPIPEFOUNDRY.com', 88.00, '5 Year', 5621.25, 0.00, 5709.25, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(19, '3896852566', '2025-10-10', '.COM Domain Renewal', 'pustaksanskriti.com', 17.60, '1 Year', 1499.00, 0.00, 1516.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(20, '3888034521', '2025-09-19', '.COM Domain Renewal', 'galexrigs.com', 17.60, '1 Year', 1499.00, 0.00, 1516.60, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(21, '3862692535', '2025-09-12', '.COM Domain Renewal', 'gsmfoils.com', 17.60, '1 Year', 1124.25, 0.00, 1141.85, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(22, '3862692535', '2025-09-12', '.COM Domain Renewal', 'SANVIPOLYFAB.com', 88.00, '5 Year', 5621.25, 0.00, 5709.25, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(23, '3862692535', '2025-09-12', '.COM Domain Renewal', 'sanvispinning.com', 88.00, '5 Year', 5621.25, 0.00, 5709.25, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(24, '3849258885', '2025-08-22', '.IN Domain Renewal', 'ANGELSOLUTIONS.in', 0.00, '1 Year', 899.00, 0.00, 899.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(25, '3849258885', '2025-08-22', '.COM Domain Renewal', 'thegranddwarika.com', 17.00, '1 Year', 1499.00, 0.00, 1516.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(26, '3842201960', '2025-08-11', '.COM Domain Renewal', 'MYSHADICARDS.com', 17.00, '1 Year', 1279.22, 0.00, 1296.22, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(27, '3842201960', '2025-08-11', '.IN Domain Renewal', 'SPINDS.in', 0.00, '1 Year', 639.18, 0.00, 639.18, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(28, '3816456989', '2025-08-05', '.COM Domain Renewal', 'CVFYME.COM', 17.00, '1 Year', 1499.00, 0.00, 1516.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(29, '3816453727', '2025-07-14', '.COM Domain Renewal', 'amsolindustries.com', 17.00, '1 Year', 1199.20, 0.00, 1216.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(30, '3816453727', '2025-07-14', '.COM Domain Renewal', 'BILLWITHGST.COM', 17.00, '1 Year', 1199.20, 0.00, 1216.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(31, '3816453727', '2025-07-14', '.COM Domain Renewal', 'DARSEDUCATION.com', 17.00, '1 Year', 1199.20, 0.00, 1216.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(32, '3816453727', '2025-07-14', '.COM Domain Renewal', 'EVERMASTERMANDAP.com', 17.00, '1 Year', 1199.20, 0.00, 1216.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(33, '3807430309', '2025-07-10', '.COM Domain Renewal', 'MADHURASH.com', 17.00, '1 Year', 1499.00, 0.00, 1516.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(34, '3785741878', '2025-07-06', '.IN Domain Renewal', 'ADVANCEGROUP.in', 0.00, '1 Year', 749.00, 0.00, 749.00, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(35, '3774479323', '2025-06-17', '.COM Domain Renewal', 'rktradersindia.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(36, '3774477821', '2025-06-07', '.COM Domain Renewal', 'CHARGESEO.COM', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(37, '3774477821', '2025-06-07', '.IN Domain Renewal', 'JSQUAREINTERIORS.in', 0.00, '1 Year', 599.20, 0.00, 599.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(38, '3741653308', '2025-05-30', '.COM Domain Renewal', 'CALCINEBAUXITE.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(39, '3741653308', '2025-05-30', '.COM Domain Renewal', 'FAIRGROWTHIMPEX.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(40, '3741653308', '2025-05-30', '.COM Domain Renewal', 'FELIXCALCINATION.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(41, '3741653308', '2025-05-30', '.COM Domain Renewal', 'FUTUREREFRACTORIES.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(42, '3741653308', '2025-05-30', '.COM Domain Renewal', 'HIGHALUMINA.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(43, '3739873108', '2025-05-09', '.COM Domain Renewal', 'BRANDMOOZE.COM', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(44, '3739873108', '2025-05-09', '.COM Domain Renewal', 'SPOTINJOB.COM', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(45, '3721110019', '2025-05-08', '.COM Domain Registration', 'stttp.com', 15.84, '1 Year', 999.00, 0.00, 1014.84, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(46, '3700714655', '2025-04-19', '.IN (.CO.IN) Domain Renewal', 'ASIACOMPUTER.co.in', 0.00, '1 Year', 535.20, 0.00, 535.20, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(47, '3700714655', '2025-04-19', '.COM Domain Renewal', 'MYSHAADICARDS.com', 15.84, '1 Year', 1199.20, 0.00, 1215.04, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08'),
(48, '3679152184', '2025-04-10', '.COM Domain Renewal', 'BHAKTIIND.com', 15.84, '1 Year', 1499.00, 0.00, 1514.84, 'INR', 'Upi', 'Upi', 'Book1.xlsx', '2026-04-01 06:55:08', '2026-04-01 06:55:08');

-- --------------------------------------------------------

--
-- Table structure for table `hostinger_invoice_records`
--

CREATE TABLE `hostinger_invoice_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `order_number` varchar(255) DEFAULT NULL,
  `billed_to_name` varchar(255) DEFAULT NULL,
  `billed_to_company` varchar(255) DEFAULT NULL,
  `billed_to_gstin` varchar(255) DEFAULT NULL,
  `billed_to_email` varchar(255) DEFAULT NULL,
  `billed_to_country` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `billing_period` varchar(255) DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_excl_gst` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostinger_invoice_records`
--

INSERT INTO `hostinger_invoice_records` (`id`, `pdf_filename`, `invoice_number`, `invoice_date`, `next_billing_date`, `order_number`, `billed_to_name`, `billed_to_company`, `billed_to_gstin`, `billed_to_email`, `billed_to_country`, `description`, `client_name`, `type`, `billing_period`, `unit_price`, `discount`, `total_excl_gst`, `gst_amount`, `line_total`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'H_24173371.pdf', 'HSG-4534787', '2025-05-12', '2026-05-18', 'hb_30420687', 'Ishan Shah', 'WebsManiac Inc.', 'INCIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'WordPress Pro (billed every year) |\n Daily Backup', NULL, 'Hosting', 'May 18, 2025 to May 18, 2026', 299.88, 14.99, 284.89, 0.00, 284.89, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(2, 'H_35513307.pdf', 'HSG-6686846', '2026-01-05', '2027-01-05', 'hb_43540338', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Premium Web Hosting (billed every year)', NULL, 'Hosting', 'Jan 05, 2026 to Jan 05, 2027', 131.88, 90.00, 41.88, 0.00, 41.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(3, 'H_36104577.pdf', 'HSG-6794648', '2026-01-15', '2027-01-15', 'hb_44226228', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Premium Web Hosting (billed every year)', NULL, 'Hosting', 'Jan 15, 2026 to Jan 15, 2027', 131.88, 72.00, 59.88, 0.00, 59.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(4, 'H_36371485.pdf', 'HSG-6843543', '2026-01-20', '2027-01-20', 'hb_44522734', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Jan 20, 2026 to Jan 20, 2027', 203.88, 132.00, 71.88, 0.00, 71.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(5, 'H_37442270.pdf', 'HSG-7062323', '2026-02-07', '2027-02-07', 'hb_45745106', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Premium Web Hosting (billed every year)', NULL, 'Hosting', 'Feb 07, 2026 to Feb 07, 2027', 131.88, 72.00, 59.88, 0.00, 59.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(6, 'H_37881465.pdf', 'HSG-7142935', '2026-02-14', '2027-02-28', 'hb_26721330', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Premium Web Hosting (billed every year)', NULL, 'Hosting', 'Feb 28, 2026 to Feb 28, 2027', 131.88, 0.00, 131.88, 0.00, 131.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(7, 'H_38789157.pdf', 'HSG-7302238', '2026-03-01', '2027-03-15', 'hb_27510849', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Mar 15, 2026 to Mar 15, 2027', 167.88, 0.00, 167.88, 0.00, 167.88, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(8, 'H_40006334.pdf', 'HSG-7521633', '2026-03-21', '2026-04-21', 'hb_48483292', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', 'Premium Web Hosting (billed every month)', NULL, 'Hosting', 'Mar 21, 2026 to Apr 21, 2026', 12.99, 0.00, 12.99, 0.00, 12.99, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(9, 'H_40173655.pdf', 'HSG-7553557', '2026-03-24', '2027-03-24', 'hb_48654642', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 'kenosisindiaofficial@gmail.com', 'India', '.CO.IN Domain (billed every year) flavorit.co.in', 'flavorit', 'Domain', 'Mar 24, 2026 to Mar 24, 2027', 9.99, 4.00, 5.99, 0.00, 5.99, 'USD', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(10, 'H_18986136.pdf', 'HSG-3557134', '2025-01-10', '2026-02-06', 'hb_8063975', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) kitchenkingrajkot.in', 'kitchenkingrajkot', 'Domain', 'Feb 06, 2025 to Feb 06, 2026', 749.00, 0.00, 749.00, 0.00, 749.00, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(11, 'H_22485986.pdf', 'HSG-4214265', '2025-04-02', '2026-04-23', 'hb_28415794', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) theicelandadventures.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'theicelandadventures', 'Domain', 'Apr 23, 2025 to Apr 23, 2026', 1193.93, 0.00, 1193.93, 0.00, 1193.93, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(12, 'H_23135042.pdf', 'HSG-4338482', '2025-04-17', '2026-04-26', 'hb_29213800', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) powersalescorporation.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'powersalescorporation', 'Domain', 'Apr 26, 2025 to Apr 26, 2026', 1193.93, 0.00, 1193.93, 0.00, 1193.93, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(13, 'H_23287775.pdf', 'HSG-4368663', '2025-04-21', '2026-04-21', 'hb_29380295', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) cannolifoods.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'cannolifoods', 'Domain', 'Apr 21, 2025 to Apr 21, 2026', 1193.93, 430.00, 763.93, 0.00, 763.93, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(14, 'H_23287840.pdf', 'HSG-4368696', '2025-04-21', '2026-04-21', 'hb_29380363', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) cannolifoods.in', 'cannolifoods', 'Domain', 'Apr 21, 2025 to Apr 21, 2026', 729.00, 300.00, 429.00, 0.00, 429.00, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(15, 'H_25087198.pdf', 'HSG-4710011', '2025-06-02', '2026-06-02', 'hb_31535886', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain Transfer greenforcefood.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'greenforcefood', 'Domain', 'Jun 02, 2025 to Jun 02, 2025', 893.93, 0.00, 893.93, 0.00, 893.93, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(16, 'H_25832661.pdf', 'HSG-4849514', '2025-06-19', '2026-06-19', 'hb_32461133', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) shakeheaven.in', 'shakeheaven', 'Domain', 'Jun 19, 2025 to Jun 19, 2026', 729.00, 300.00, 429.00, 0.00, 429.00, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(17, 'H_26170570.pdf', 'HSG-4916251', '2025-06-27', '2026-06-27', 'hb_32853587', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Jun 27, 2025 to Jun 27, 2026', 7788.00, 3467.40, 4320.60, 0.00, 4320.60, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(18, 'H_26387020.pdf', 'HSG-4959393', '2025-07-02', '2026-07-02', 'hb_33107439', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Jul 02, 2025 to Jul 02, 2026', 7788.00, 3467.40, 4320.60, 0.00, 4320.60, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(19, 'H_26760189.pdf', 'HSG-5034455', '2025-07-10', '2026-07-10', 'hb_33552434', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) galexequipments.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'galexequipments', 'Domain', 'Jul 10, 2025 to Jul 10, 2026', 1196.44, 430.00, 766.44, 0.00, 766.44, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(20, 'H_27123916.pdf', 'HSG-5107053', '2025-07-18', '2026-07-18', 'hb_33983403', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Jul 18, 2025 to Jul 18, 2026', 7788.00, 3240.00, 4548.00, 0.00, 4548.00, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(21, 'H_29573394.pdf', 'HSG-5602869', '2025-09-10', '2026-10-06', 'hb_18259741', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) jit-industries.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'jit-industries', 'Domain', 'Oct 06, 2025 to Oct 06, 2026', 1193.93, 0.00, 1193.93, 0.00, 1193.93, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(22, 'H_29771455.pdf', 'HSG-5624148', '2025-09-13', '2026-09-13', 'hb_37067994', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 'info@websmaniac.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Sep 13, 2025 to Sep 13, 2026', 7788.00, 3467.40, 4320.60, 0.00, 4320.60, 'INR', '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(23, 'H_30426157.pdf', 'HSG-5762976', '2025-09-28', '2026-10-24', 'hb_20157997', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) illuminits.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'illuminits', 'Domain', 'Oct 24, 2025 to Oct 24, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(24, 'H_31157452.pdf', 'HSG-5899150', '2025-10-13', '2026-11-08', 'hb_6364238', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) ethnicoutfits.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'ethnicoutfits', 'Domain', 'Nov 08, 2025 to Nov 08, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(25, 'H_31474187.pdf', 'HSG-5952463', '2025-10-20', '2026-11-14', 'hb_21269505', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) elegantmindshospital.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'elegantmindshospital', 'Domain', 'Nov 14, 2025 to Nov 14, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(26, 'H_31884556.pdf', 'HSG-6027172', '2025-10-28', '2026-11-23', 'hb_21741193', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) talametalparts.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'talametalparts', 'Domain', 'Nov 23, 2025 to Nov 23, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(27, 'H_32870133.pdf', 'HSG-6203077', '2025-11-16', '2026-12-11', 'hb_7429314', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) jobaboard.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'jobaboard', 'Domain', 'Dec 11, 2025 to Dec 11, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(28, 'H_32907519.pdf', 'HSG-6209468', '2025-11-16', '2026-12-12', 'hb_7452161', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) sprfoundry.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'sprfoundry', 'Domain', 'Dec 12, 2025 to Dec 12, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(29, 'H_32907520.pdf', 'HSG-6209469', '2025-11-16', '2026-12-12', 'hb_7452266', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) sweetpeachwithhannah.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'sweetpeachwithhannah', 'Domain', 'Dec 12, 2025 to Dec 12, 2026', 1196.44, 0.00, 1196.44, 0.00, 1196.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(30, 'H_33071039.pdf', 'HSG-6226659', '2025-11-18', '2026-11-18', 'hb_40759132', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) starthtechmation.com |\n ICANN fee (billed every year) |\n Domain WHOIS Privacy Protection', 'starthtechmation', 'Domain', 'Nov 18, 2025 to Nov 18, 2026', 1316.44, 550.00, 766.44, 0.00, 766.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(31, 'H_33071973.pdf', 'HSG-6226877', '2025-11-18', '2026-11-18', 'hb_40761165', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) 3magnet.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', '3magnet', 'Domain', 'Nov 18, 2025 to Nov 18, 2026', 1316.44, 550.00, 766.44, 0.00, 766.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(32, 'H_33828490.pdf', 'HSG-6382643', '2025-12-04', '2026-12-29', 'hb_18259406', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) jitbearings.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'jitbearings', 'Domain', 'Dec 29, 2025 to Dec 29, 2026', 1316.44, 0.00, 1316.44, 0.00, 1316.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(33, 'H_35806850.pdf', 'HSG-6754579', '2026-01-11', '2027-02-06', 'hb_8063975', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) kitchenkingrajkot.in', 'kitchenkingrajkot', 'Domain', 'Feb 06, 2026 to Feb 06, 2027', 899.00, 0.00, 899.00, 0.00, 899.00, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(34, 'H_36200725.pdf', 'HSG-6825200', '2026-01-18', '2027-02-13', 'hb_9571276', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) eatwow.in', 'eatwow', 'Domain', 'Feb 13, 2026 to Feb 13, 2027', 899.00, 0.00, 899.00, 0.00, 899.00, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(35, 'H_37006290.pdf', 'HSG-6981764', '2026-02-02', '2027-02-14', 'hb_25985797', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', 'Business Web Hosting (billed every year) |\n Daily Backup', NULL, 'Hosting', 'Feb 14, 2026 to Feb 14, 2027', 6948.00, 0.00, 6948.00, 0.00, 6948.00, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(36, 'H_37322321.pdf', 'HSG-7059945', '2026-02-06', '2027-03-04', 'hb_25985797', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) sunenvironmentalservices.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'sunenvironmentalservices', 'Domain', 'Mar 04, 2026 to Mar 04, 2027', 1316.44, 0.00, 1316.44, 0.00, 1316.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(37, 'H_37706663.pdf', 'HSG-7126564', '2026-02-13', '2027-03-10', 'hb_27248896', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) nextstepmbbs.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'nextstepmbbs', 'Domain', 'Mar 10, 2026 to Mar 10, 2027', 1316.44, 0.00, 1316.44, 0.00, 1316.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(38, 'H_40230622.pdf', 'HSG-7581496', '2026-03-26', '2027-04-21', 'hb_29380295', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.COM Domain (billed every year) cannolifoods.com |\n Domain WHOIS Privacy Protection |\n ICANN fee (billed every year)', 'cannolifoods', 'Domain', 'Apr 21, 2026 to Apr 21, 2027', 1316.44, 0.00, 1316.44, 0.00, 1316.44, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(39, 'H_40230623.pdf', 'HSG-7581497', '2026-03-26', '2027-04-21', 'hb_29380363', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 'info@websmaniac.com', 'India', '.IN Domain (billed every year) cannolifoods.in', 'cannolifoods', 'Domain', 'Apr 21, 2026 to Apr 21, 2027', 899.00, 0.00, 899.00, 0.00, 899.00, 'INR', '2026-04-01 01:33:30', '2026-04-01 01:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `hostinger_invoice_summaries`
--

CREATE TABLE `hostinger_invoice_summaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `order_number` varchar(255) DEFAULT NULL,
  `billed_to_name` varchar(255) DEFAULT NULL,
  `billed_to_company` varchar(255) DEFAULT NULL,
  `billed_to_gstin` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `total_records` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostinger_invoice_summaries`
--

INSERT INTO `hostinger_invoice_summaries` (`id`, `pdf_filename`, `invoice_number`, `invoice_date`, `next_billing_date`, `order_number`, `billed_to_name`, `billed_to_company`, `billed_to_gstin`, `subtotal`, `total_discount`, `gst_amount`, `grand_total`, `amount_paid`, `amount_due`, `currency`, `total_records`, `created_at`, `updated_at`) VALUES
(1, 'H_24173371.pdf', 'HSG-4534787', '2025-05-12', '2026-05-18', 'hb_30420687', 'Ishan Shah', 'WebsManiac Inc.', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(2, 'H_35513307.pdf', 'HSG-6686846', '2026-01-05', '2027-01-05', 'hb_43540338', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(3, 'H_36104577.pdf', 'HSG-6794648', '2026-01-15', '2027-01-15', 'hb_44226228', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(4, 'H_36371485.pdf', 'HSG-6843543', '2026-01-20', '2027-01-20', 'hb_44522734', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(5, 'H_37442270.pdf', 'HSG-7062323', '2026-02-07', '2027-02-07', 'hb_45745106', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(6, 'H_37881465.pdf', 'HSG-7142935', '2026-02-14', '2027-02-28', 'hb_26721330', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(7, 'H_38789157.pdf', 'HSG-7302238', '2026-03-01', '2027-03-15', 'hb_27510849', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(8, 'H_40006334.pdf', 'HSG-7521633', '2026-03-21', '2026-04-21', 'hb_48483292', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(9, 'H_40173655.pdf', 'HSG-7553557', '2026-03-24', '2027-03-24', 'hb_48654642', 'Ishan Shah', 'WebsManiac Inc.', 'IN24CIBPS7329P1ZM', 5.99, 0.00, 0.00, 0.00, 0.00, 0.00, 'USD', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(10, 'H_18986136.pdf', 'HSG-3557134', '2025-01-10', '2026-02-06', 'hb_8063975', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(11, 'H_22485986.pdf', 'HSG-4214265', '2025-04-02', '2026-04-23', 'hb_28415794', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(12, 'H_23135042.pdf', 'HSG-4338482', '2025-04-17', '2026-04-26', 'hb_29213800', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(13, 'H_23287775.pdf', 'HSG-4368663', '2025-04-21', '2026-04-21', 'hb_29380295', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(14, 'H_23287840.pdf', 'HSG-4368696', '2025-04-21', '2026-04-21', 'hb_29380363', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 429.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(15, 'H_25087198.pdf', 'HSG-4710011', '2025-06-02', '2026-06-02', 'hb_31535886', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(16, 'H_25832661.pdf', 'HSG-4849514', '2025-06-19', '2026-06-19', 'hb_32461133', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 429.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(17, 'H_26170570.pdf', 'HSG-4916251', '2025-06-27', '2026-06-27', 'hb_32853587', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(18, 'H_26387020.pdf', 'HSG-4959393', '2025-07-02', '2026-07-02', 'hb_33107439', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(19, 'H_26760189.pdf', 'HSG-5034455', '2025-07-10', '2026-07-10', 'hb_33552434', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(20, 'H_27123916.pdf', 'HSG-5107053', '2025-07-18', '2026-07-18', 'hb_33983403', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(21, 'H_29573394.pdf', 'HSG-5602869', '2025-09-10', '2026-10-06', 'hb_18259741', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:29', '2026-04-01 01:33:29'),
(22, 'H_29771455.pdf', 'HSG-5624148', '2025-09-13', '2026-09-13', 'hb_37067994', 'Ishan Shah', 'WEBSMANIAC INC', 'INCIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(23, 'H_30426157.pdf', 'HSG-5762976', '2025-09-28', '2026-10-24', 'hb_20157997', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(24, 'H_31157452.pdf', 'HSG-5899150', '2025-10-13', '2026-11-08', 'hb_6364238', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(25, 'H_31474187.pdf', 'HSG-5952463', '2025-10-20', '2026-11-14', 'hb_21269505', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(26, 'H_31884556.pdf', 'HSG-6027172', '2025-10-28', '2026-11-23', 'hb_21741193', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(27, 'H_32870133.pdf', 'HSG-6203077', '2025-11-16', '2026-12-11', 'hb_7429314', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(28, 'H_32907519.pdf', 'HSG-6209468', '2025-11-16', '2026-12-12', 'hb_7452161', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(29, 'H_32907520.pdf', 'HSG-6209469', '2025-11-16', '2026-12-12', 'hb_7452266', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(30, 'H_33071039.pdf', 'HSG-6226659', '2025-11-18', '2026-11-18', 'hb_40759132', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(31, 'H_33071973.pdf', 'HSG-6226877', '2025-11-18', '2026-11-18', 'hb_40761165', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(32, 'H_33828490.pdf', 'HSG-6382643', '2025-12-04', '2026-12-29', 'hb_18259406', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(33, 'H_35806850.pdf', 'HSG-6754579', '2026-01-11', '2027-02-06', 'hb_8063975', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(34, 'H_36200725.pdf', 'HSG-6825200', '2026-01-18', '2027-02-13', 'hb_9571276', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(35, 'H_37006290.pdf', 'HSG-6981764', '2026-02-02', '2027-02-14', 'hb_25985797', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(36, 'H_37322321.pdf', 'HSG-7059945', '2026-02-06', '2027-03-04', 'hb_25985797', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(37, 'H_37706663.pdf', 'HSG-7126564', '2026-02-13', '2027-03-10', 'hb_27248896', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(38, 'H_40230622.pdf', 'HSG-7581496', '2026-03-26', '2027-04-21', 'hb_29380295', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30'),
(39, 'H_40230623.pdf', 'HSG-7581497', '2026-03-26', '2027-04-21', 'hb_29380363', 'Ishan Shah', 'WEBSMANIAC INC', 'IN24CIBPS7329P1ZM', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'INR', 1, '2026-04-01 01:33:30', '2026-04-01 01:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `hostinger_pending_pdfs`
--

CREATE TABLE `hostinger_pending_pdfs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `status` enum('pending','processing','done','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `records_inserted` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostinger_pending_pdfs`
--

INSERT INTO `hostinger_pending_pdfs` (`id`, `original_filename`, `stored_path`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 'H_24173371.pdf', 'hostinger-invoices/3l4SP3JYbyOXr0czLIsxPrciIO33trxynnbRIdcH.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:19', '2026-04-01 01:33:29'),
(2, 'H_35513307.pdf', 'hostinger-invoices/85zLUsx2gC7dgu9PwPilZh9Ih8VX86ZuNUlk2uL3.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(3, 'H_36104577.pdf', 'hostinger-invoices/cnz4WoqLHqGWi9kERgrvCvhDjY0nHhKc6yeo4MFy.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(4, 'H_36371485.pdf', 'hostinger-invoices/4IapenXqxYorK8aVWnjuiB7MbjbIAJDTlSyyQX5H.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(5, 'H_37442270.pdf', 'hostinger-invoices/IxNsZBKEjh52hKgvkMAsUO2lGbudNK81KvLAQ3JZ.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(6, 'H_37881465.pdf', 'hostinger-invoices/KXSDtAfCEzI5tEoVsDNvdMpx63AS4KGIk1pQg2EJ.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(7, 'H_38789157.pdf', 'hostinger-invoices/dmzccBO6GDZdlSpAoPtQ2kBUImHD2tV6zmXbGj00.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(8, 'H_40006334.pdf', 'hostinger-invoices/yf7pu9E4SXDpGlJ7kKyZpNYwSmXZmVRci2mEQ6ko.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(9, 'H_40173655.pdf', 'hostinger-invoices/67a1NvqyK3Gk8ARh7xTK2QEpfseUPdkx1F8OiiS8.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:23:21', '2026-04-01 01:33:29'),
(10, 'H_18986136.pdf', 'hostinger-invoices/MphoqMCeKYu6N6a1NBqq4TvmlE7wtyWI9KqEeXA4.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:36:08', '2026-04-01 01:33:29'),
(11, 'H_22485986.pdf', 'hostinger-invoices/jbKYpAZSzxfG0mRnmgIUyIuynT3gjzM8mOjTNym8.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(12, 'H_23135042.pdf', 'hostinger-invoices/qw4oJm05jKep8fQkxmbEYoknYz0au68Bmyx2Zc73.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(13, 'H_23287775.pdf', 'hostinger-invoices/6ZoSJDxZtC3sHbE8iIOry6EhJgq5kXv5WWn8edXJ.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(14, 'H_23287840.pdf', 'hostinger-invoices/MpQpJ5Lrubb3SBk86AhO2e3RlUARCNFqW9e1dEp1.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(15, 'H_25087198.pdf', 'hostinger-invoices/Ve0cR1KecptnVWH3brqPNSSfa2mePVqaSWKVbGhF.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(16, 'H_25832661.pdf', 'hostinger-invoices/aZY3i4Wa6Rr7ucRH8CTMvhXPHmKUdoTovPuLKYIy.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(17, 'H_26170570.pdf', 'hostinger-invoices/wunQ0gdEBJZ9XtSaU9DlDruI32aa3t0RS6Hw0cGn.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(18, 'H_26387020.pdf', 'hostinger-invoices/O1fAXcCNKNErJimhmFDrNyhpHX03f8wlcTD9wjNU.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(19, 'H_26760189.pdf', 'hostinger-invoices/qxTktGgWxXHvv7gcatvbG3eahuTEQta8OmBvILmW.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(20, 'H_27123916.pdf', 'hostinger-invoices/vDSzkUut0F8zxsoI4rHYfRyeQcDxJ2rtkKPm4ZCL.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(21, 'H_29573394.pdf', 'hostinger-invoices/9cVVhovhDF7eyjG7f0NtZvyhuQWrOAk7KYMmihgF.pdf', 'done', NULL, 1, '2026-04-01 01:33:29', '2026-03-31 22:42:54', '2026-04-01 01:33:29'),
(22, 'H_29771455.pdf', 'hostinger-invoices/aXDNHIq4Upbz7XatF4pdCW1HxSR29H8FxTIK9NoD.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(23, 'H_30426157.pdf', 'hostinger-invoices/40Zxiymz44QmQbBS48p7VoVA6bpnmHwuekqWFDYv.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(24, 'H_31157452.pdf', 'hostinger-invoices/cgYhUCFXgGDHQA9wg6fh63BVdQHVt8mq9xjYCkGx.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(25, 'H_31474187.pdf', 'hostinger-invoices/mZzj77VSzOjhYUGaXBZG5NFgbqzv92yGmwkrd2NU.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(26, 'H_31884556.pdf', 'hostinger-invoices/tNnI3WNlI4bw87Igioa2Sg6faKAyWtA7Dl4tOVUj.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(27, 'H_32870133.pdf', 'hostinger-invoices/IemxGN8VVUAbSyKom94c75cSRBg98IgmPTSv0EUA.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(28, 'H_32907519.pdf', 'hostinger-invoices/GkdQK8xVQTLLrsQKvzBdaXsVJyWQ8P5iM7vwPk6y.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(29, 'H_32907520.pdf', 'hostinger-invoices/1BAPRWL4e1cFP77SC10zrr655m4mPE0PiEdE0G1P.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(30, 'H_33071039.pdf', 'hostinger-invoices/qjaG3hOTXeiENgLvF9Zxb4BPrKcKmEv9IUGnOmYe.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:42:54', '2026-04-01 01:33:30'),
(31, 'H_33071973.pdf', 'hostinger-invoices/zEXVyfCIER5XZJZi7B2N5EeoXlJwu51gF9BFrx0W.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(32, 'H_33828490.pdf', 'hostinger-invoices/WNsKTmpVDOKHleUbpkxzJWFYO5q0vIsB1sqxFt0p.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(33, 'H_35806850.pdf', 'hostinger-invoices/R6Y0pqQqOxPnM0RBKpSyVxZokH8aICKlS8UKjhT1.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(34, 'H_36200725.pdf', 'hostinger-invoices/0gPNt9GohXkuwqQxgHYXXIX08DQtod2iwdyRrLVh.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(35, 'H_37006290.pdf', 'hostinger-invoices/rBmxh17xsnP9e4h3u4TtRdgoenTmY1VovuWyxOuF.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(36, 'H_37322321.pdf', 'hostinger-invoices/FZacuFERVh1TqBrxZa7qbnvY94bwRyIkrS6XBSEh.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(37, 'H_37706663.pdf', 'hostinger-invoices/4Whf4DrxNJd0QufD6Zk2qJAKHgHHUuH4LJMAiPsj.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(38, 'H_40230622.pdf', 'hostinger-invoices/DA25QcUurgTQgjXoSork743Db3nKySOeGCMkm4wX.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30'),
(39, 'H_40230623.pdf', 'hostinger-invoices/uJnfENPxAb9GT8OQjFayF9bU6ZYKe1p7YctGf7Hs.pdf', 'done', NULL, 1, '2026-04-01 01:33:30', '2026-03-31 22:43:16', '2026-04-01 01:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_records`
--

CREATE TABLE `invoice_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `document_date` date NOT NULL,
  `tax_invoice_id` varchar(255) DEFAULT NULL,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `impressions` int(11) NOT NULL DEFAULT 0,
  `campaign_type` varchar(255) DEFAULT NULL,
  `is_merged` tinyint(1) NOT NULL DEFAULT 0,
  `merged_name` varchar(255) DEFAULT NULL,
  `merged_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_records`
--

INSERT INTO `invoice_records` (`id`, `client_name`, `price`, `document_date`, `tax_invoice_id`, `pdf_filename`, `impressions`, `campaign_type`, `is_merged`, `merged_name`, `merged_group_id`, `created_at`, `updated_at`) VALUES
(220, 'UV Business - Sales Call', 0.40, '2026-03-10', 'ADS312-105614626', '2026-03-10T20-06 Tax invoice #26398889039797519-26239936022359492.pdf', 15, 'UV Business - Sales Call - 5/3/26 to', 0, NULL, NULL, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(343, 'Jogi Art', 172.10, '2025-04-02', 'ADS312-104340551', '2025-04-02T14-39 Tax invoice #9475873755858982-9470645536381803.pdf', 1173, 'Resin Category - Catelogue Ads for Jogi Art + MDF Category - Catelogue Ads for Jogi Art', 0, NULL, NULL, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(344, 'Twilight Lighting Solution', 139.00, '2025-04-02', 'ADS312-104340551', '2025-04-02T14-39 Tax invoice #9475873755858982-9470645536381803.pdf', 1061, 'Lead Ads for Interior Designer', 0, NULL, NULL, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(3376, 'Phoenix - May 2025', 22691.95, '2025-04-02', NULL, NULL, 2923174, 'Merged Record', 1, 'Phoenix - May 2025', 1, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3377, 'Phoenix - May 2025', 34989.47, '2025-05-01', NULL, NULL, 4700767, 'Merged Record', 1, 'Phoenix - May 2025', 2, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3378, 'Phoenix - July 2025', 17946.86, '2025-06-03', NULL, NULL, 1521684, 'Merged Record', 1, 'Phoenix - July 2025', 3, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3379, 'Phoenix - July 2025', 29258.89, '2025-07-02', NULL, NULL, 1542854, 'Merged Record', 1, 'Phoenix - July 2025', 4, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3380, 'Phoenix - August 2025', 5237.32, '2025-08-01', NULL, NULL, 43594, 'Merged Record', 1, 'Phoenix - August 2025', 5, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3381, 'Phoenix - October 2025', 40802.46, '2025-09-16', NULL, NULL, 3637664, 'Merged Record', 1, 'Phoenix - October 2025', 6, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3382, 'Phoenix - October 2025', 17903.53, '2025-10-02', NULL, NULL, 619491, 'Merged Record', 1, 'Phoenix - October 2025', 7, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3383, 'Phoenix - December 2025', 8148.84, '2025-11-03', NULL, NULL, 421581, 'Merged Record', 1, 'Phoenix - December 2025', 8, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3384, 'Phoenix - December 2025', 788.02, '2025-12-09', NULL, NULL, 107792, 'Merged Record', 1, 'Phoenix - December 2025', 9, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3385, 'Phoenix - January 2026', 4291.40, '2026-01-07', NULL, NULL, 2082640, 'Merged Record', 1, 'Phoenix - January 2026', 10, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3386, 'Phoenix - March 2026', 13580.59, '2026-02-04', NULL, NULL, 4264801, 'Merged Record', 1, 'Phoenix - March 2026', 11, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3387, 'Phoenix - March 2026', 7116.68, '2026-03-03', NULL, NULL, 2662044, 'Merged Record', 1, 'Phoenix - March 2026', 12, '2026-03-31 00:16:39', '2026-03-31 00:16:39'),
(3388, 'Cannoli - May 2025', 8923.59, '2025-04-22', NULL, NULL, 101174, 'Merged Record', 1, 'Cannoli - May 2025', 13, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3389, 'Cannoli - May 2025', 10101.51, '2025-05-01', NULL, NULL, 135736, 'Merged Record', 1, 'Cannoli - May 2025', 14, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3390, 'Cannoli - July 2025', 9923.48, '2025-06-13', NULL, NULL, 449492, 'Merged Record', 1, 'Cannoli - July 2025', 15, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3391, 'Cannoli - July 2025', 10126.79, '2025-07-02', NULL, NULL, 756134, 'Merged Record', 1, 'Cannoli - July 2025', 16, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3392, 'Cannoli - August 2025', 5396.17, '2025-08-14', NULL, NULL, 730313, 'Merged Record', 1, 'Cannoli - August 2025', 17, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3393, 'Cannoli - October 2025', 2110.41, '2025-09-24', NULL, NULL, 101053, 'Merged Record', 1, 'Cannoli - October 2025', 18, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3394, 'Cannoli - October 2025', 368.80, '2025-10-02', NULL, NULL, 19389, 'Merged Record', 1, 'Cannoli - October 2025', 19, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3395, 'Cannoli - December 2025', 1351.93, '2025-11-16', NULL, NULL, 140474, 'Merged Record', 1, 'Cannoli - December 2025', 20, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3396, 'Cannoli - December 2025', 2726.23, '2025-12-05', NULL, NULL, 361381, 'Merged Record', 1, 'Cannoli - December 2025', 21, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3397, 'Cannoli - January 2026', 5907.90, '2026-01-15', NULL, NULL, 1202804, 'Merged Record', 1, 'Cannoli - January 2026', 22, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3398, 'Cannoli - March 2026', 3103.02, '2026-02-04', NULL, NULL, 1628827, 'Merged Record', 1, 'Cannoli - March 2026', 23, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3399, 'Cannoli - March 2026', 1016.71, '2026-03-03', NULL, NULL, 40561, 'Merged Record', 1, 'Cannoli - March 2026', 24, '2026-03-31 00:21:56', '2026-03-31 00:21:56'),
(3400, 'Weldor - May 2025', 669.05, '2025-04-26', NULL, NULL, 5454, 'Merged Record', 1, 'Weldor - May 2025', 25, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3401, 'Weldor - May 2025', 11665.81, '2025-05-01', NULL, NULL, 114516, 'Merged Record', 1, 'Weldor - May 2025', 26, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3402, 'Weldor - July 2025', 28580.82, '2025-06-03', NULL, NULL, 312993, 'Merged Record', 1, 'Weldor - July 2025', 27, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3403, 'Weldor - July 2025', 13700.33, '2025-07-02', NULL, NULL, 174336, 'Merged Record', 1, 'Weldor - July 2025', 28, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3404, 'Weldor - August 2025', 9643.95, '2025-08-01', NULL, NULL, 165147, 'Merged Record', 1, 'Weldor - August 2025', 29, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3405, 'Weldor - December 2025', 18954.27, '2025-11-06', NULL, NULL, 395718, 'Merged Record', 1, 'Weldor - December 2025', 30, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3406, 'Weldor - December 2025', 17364.53, '2025-12-05', NULL, NULL, 468760, 'Merged Record', 1, 'Weldor - December 2025', 31, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3407, 'Weldor - January 2026', 31687.24, '2026-01-01', NULL, NULL, 873211, 'Merged Record', 1, 'Weldor - January 2026', 32, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3408, 'Weldor - March 2026', 21532.40, '2026-02-04', NULL, NULL, 509825, 'Merged Record', 1, 'Weldor - March 2026', 33, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3409, 'Weldor - March 2026', 18708.52, '2026-03-03', NULL, NULL, 363089, 'Merged Record', 1, 'Weldor - March 2026', 34, '2026-03-31 00:25:05', '2026-03-31 00:25:05'),
(3410, 'Maristella - March 2026', 4497.59, '2026-02-12', NULL, NULL, 640067, 'Merged Record', 1, 'Maristella - March 2026', 35, '2026-03-31 00:33:54', '2026-03-31 00:33:54'),
(3411, 'Maristella - March 2026', 497.40, '2026-03-13', NULL, NULL, 8616, 'Merged Record', 1, 'Maristella - March 2026', 36, '2026-03-31 00:33:54', '2026-03-31 00:33:54'),
(3412, 'Akansha - March 2026', 5878.47, '2026-03-13', NULL, NULL, 155920, 'Merged Record', 1, 'Akansha - March 2026', 37, '2026-03-31 00:39:33', '2026-03-31 00:39:33'),
(3413, 'Spot In Job - February 2026', 3124.62, '2026-02-04', NULL, NULL, 54981, 'Merged Record', 1, 'Spot In Job - February 2026', 38, '2026-03-31 00:41:44', '2026-03-31 00:41:44'),
(3414, 'Spot In Job - March 2026', 511.64, '2026-03-03', NULL, NULL, 10443, 'Merged Record', 1, 'Spot In Job - March 2026', 39, '2026-03-31 00:41:44', '2026-03-31 00:41:44'),
(3415, 'Just Smile - February 2026', 12263.92, '2026-02-15', NULL, NULL, 3772496, 'Merged Record', 1, 'Just Smile - February 2026', 40, '2026-03-31 00:43:09', '2026-03-31 00:43:09'),
(3416, 'Just Smile - March 2026', 3929.59, '2026-03-03', NULL, NULL, 271659, 'Merged Record', 1, 'Just Smile - March 2026', 41, '2026-03-31 00:43:09', '2026-03-31 00:43:09'),
(3417, 'Move Maker - May 2025', 7984.54, '2025-05-11', NULL, NULL, 89872, 'Merged Record', 1, 'Move Maker - May 2025', 42, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3418, 'Move Maker - June 2025', 4531.92, '2025-06-03', NULL, NULL, 69506, 'Merged Record', 1, 'Move Maker - June 2025', 43, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3419, 'Move Maker - July 2025', 4464.76, '2025-07-02', NULL, NULL, 77939, 'Merged Record', 1, 'Move Maker - July 2025', 44, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3420, 'Move Maker - December 2025', 663.46, '2025-12-24', NULL, NULL, 80653, 'Merged Record', 1, 'Move Maker - December 2025', 45, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3421, 'Move Maker - January 2026', 3210.13, '2026-01-01', NULL, NULL, 420316, 'Merged Record', 1, 'Move Maker - January 2026', 46, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3422, 'Move Maker - February 2026', 2126.27, '2026-02-04', NULL, NULL, 330358, 'Merged Record', 1, 'Move Maker - February 2026', 47, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3423, 'Move Maker - March 2026', 918.80, '2026-03-10', NULL, NULL, 124879, 'Merged Record', 1, 'Move Maker - March 2026', 48, '2026-03-31 00:44:02', '2026-03-31 00:44:02'),
(3424, 'Honey - March 2026', 1121.75, '2026-03-19', NULL, NULL, 113514, 'Merged Record', 1, 'Honey - March 2026', 49, '2026-03-31 00:44:38', '2026-03-31 00:44:38'),
(3425, 'Giriraj - June 2025', 8169.60, '2025-06-03', NULL, NULL, 154709, 'Merged Record', 1, 'Giriraj - June 2025', 50, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3426, 'Giriraj - July 2025', 12675.20, '2025-07-05', NULL, NULL, 178351, 'Merged Record', 1, 'Giriraj - July 2025', 51, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3427, 'Giriraj - August 2025', 25546.32, '2025-08-05', NULL, NULL, 1807121, 'Merged Record', 1, 'Giriraj - August 2025', 52, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3428, 'Giriraj - September 2025', 13066.94, '2025-09-16', NULL, NULL, 844137, 'Merged Record', 1, 'Giriraj - September 2025', 53, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3429, 'Giriraj - October 2025', 11826.71, '2025-10-02', NULL, NULL, 363980, 'Merged Record', 1, 'Giriraj - October 2025', 54, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3430, 'Giriraj - November 2025', 20628.74, '2025-11-03', NULL, NULL, 680980, 'Merged Record', 1, 'Giriraj - November 2025', 55, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3431, 'Giriraj - December 2025', 11050.62, '2025-12-05', NULL, NULL, 437638, 'Merged Record', 1, 'Giriraj - December 2025', 56, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3432, 'Giriraj - January 2026', 496.70, '2026-01-01', NULL, NULL, 18761, 'Merged Record', 1, 'Giriraj - January 2026', 57, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3433, 'Giriraj - March 2026', 3553.90, '2026-03-14', NULL, NULL, 101308, 'Merged Record', 1, 'Giriraj - March 2026', 58, '2026-03-31 00:46:08', '2026-03-31 00:46:08'),
(3434, 'Sunbright Solar - March 2026', 4655.07, '2026-03-10', NULL, NULL, 26204, 'Merged Record', 1, 'Sunbright Solar - March 2026', 59, '2026-03-31 00:47:06', '2026-03-31 00:47:06'),
(3435, 'Asctra - February 2026', 6545.64, '2026-02-18', NULL, NULL, 43397, 'Merged Record', 1, 'Asctra - February 2026', 60, '2026-03-31 00:47:37', '2026-03-31 00:47:37'),
(3436, 'Asctra - March 2026', 3079.51, '2026-03-10', NULL, NULL, 17684, 'Merged Record', 1, 'Asctra - March 2026', 61, '2026-03-31 00:47:37', '2026-03-31 00:47:37'),
(3437, 'Icon - March 2026', 1615.85, '2026-03-14', NULL, NULL, 22400, 'Merged Record', 1, 'Icon - March 2026', 62, '2026-03-31 00:48:09', '2026-03-31 00:48:09'),
(3438, 'Rio Pipes - December 2025', 3077.24, '2025-12-24', NULL, NULL, 41645, 'Merged Record', 1, 'Rio Pipes - December 2025', 63, '2026-03-31 00:48:56', '2026-03-31 00:48:56'),
(3439, 'Rio Pipes - January 2026', 5054.06, '2026-01-01', NULL, NULL, 87869, 'Merged Record', 1, 'Rio Pipes - January 2026', 64, '2026-03-31 00:48:56', '2026-03-31 00:48:56'),
(3440, 'Rio Pipes - February 2026', 8468.63, '2026-02-13', NULL, NULL, 115595, 'Merged Record', 1, 'Rio Pipes - February 2026', 65, '2026-03-31 00:48:56', '2026-03-31 00:48:56'),
(3441, 'Rio Pipes - March 2026', 2040.75, '2026-03-03', NULL, NULL, 29152, 'Merged Record', 1, 'Rio Pipes - March 2026', 66, '2026-03-31 00:48:56', '2026-03-31 00:48:56'),
(3442, 'Shrini - February 2026', 1999.61, '2026-02-06', NULL, NULL, 244145, 'Merged Record', 1, 'Shrini - February 2026', 67, '2026-03-31 00:49:25', '2026-03-31 00:49:25'),
(3443, 'Royal Kitchenware - February 2026', 571.05, '2026-02-06', NULL, NULL, 7980, 'Merged Record', 1, 'Royal Kitchenware - February 2026', 68, '2026-03-31 00:49:54', '2026-03-31 00:49:54'),
(3444, 'All Tech Casting - December 2025', 603.53, '2025-12-24', NULL, NULL, 1154, 'Merged Record', 1, 'All Tech Casting - December 2025', 69, '2026-03-31 00:50:37', '2026-03-31 00:50:37'),
(3445, 'All Tech Casting - January 2026', 119.59, '2026-01-10', NULL, NULL, 218, 'Merged Record', 1, 'All Tech Casting - January 2026', 70, '2026-03-31 00:50:37', '2026-03-31 00:50:37'),
(3446, 'PSDJ Interior - June 2025', 11656.02, '2025-06-14', NULL, NULL, 20500, 'Merged Record', 1, 'PSDJ Interior - June 2025', 71, '2026-03-31 00:51:51', '2026-03-31 00:51:51'),
(3447, 'PSDJ Interior - July 2025', 343.94, '2025-07-02', NULL, NULL, 654, 'Merged Record', 1, 'PSDJ Interior - July 2025', 72, '2026-03-31 00:51:51', '2026-03-31 00:51:51'),
(3448, 'PSDJ Interior - August 2025', 4182.26, '2025-08-21', NULL, NULL, 51419, 'Merged Record', 1, 'PSDJ Interior - August 2025', 73, '2026-03-31 00:51:51', '2026-03-31 00:51:51'),
(3449, 'PSDJ Interior - November 2025', 1539.94, '2025-11-03', NULL, NULL, 11336, 'Merged Record', 1, 'PSDJ Interior - November 2025', 74, '2026-03-31 00:51:51', '2026-03-31 00:51:51'),
(3450, 'PSDJ Interior - December 2025', 219.40, '2025-12-05', NULL, NULL, 1034, 'Merged Record', 1, 'PSDJ Interior - December 2025', 75, '2026-03-31 00:51:51', '2026-03-31 00:51:51'),
(3451, 'Shree Hari Yoga - May 2025', 1916.57, '2025-05-28', NULL, NULL, 2290, 'Merged Record', 1, 'Shree Hari Yoga - May 2025', 76, '2026-03-31 00:52:32', '2026-03-31 00:52:32'),
(3452, 'Shree Hari Yoga - June 2025', 28345.68, '2025-06-03', NULL, NULL, 18200, 'Merged Record', 1, 'Shree Hari Yoga - June 2025', 77, '2026-03-31 00:52:32', '2026-03-31 00:52:32'),
(3453, 'Shree Hari Yoga - August 2025', 21324.86, '2025-08-05', NULL, NULL, 37782, 'Merged Record', 1, 'Shree Hari Yoga - August 2025', 78, '2026-03-31 00:52:32', '2026-03-31 00:52:32'),
(3454, 'Shree Hari Yoga - September 2025', 19266.74, '2025-09-16', NULL, NULL, 19058, 'Merged Record', 1, 'Shree Hari Yoga - September 2025', 79, '2026-03-31 00:52:32', '2026-03-31 00:52:32'),
(3455, 'Shree Hari Yoga - October 2025', 3169.31, '2025-10-02', NULL, NULL, 4412, 'Merged Record', 1, 'Shree Hari Yoga - October 2025', 80, '2026-03-31 00:52:32', '2026-03-31 00:52:32'),
(3456, 'Power Shine - May 2025', 14209.28, '2025-05-05', NULL, NULL, 90982, 'Merged Record', 1, 'Power Shine - May 2025', 81, '2026-03-31 00:53:31', '2026-03-31 00:53:31'),
(3457, 'Power Shine - June 2025', 10878.07, '2025-06-03', NULL, NULL, 126057, 'Merged Record', 1, 'Power Shine - June 2025', 82, '2026-03-31 00:53:32', '2026-03-31 00:53:32'),
(3458, 'Power Shine - July 2025', 561.70, '2025-07-02', NULL, NULL, 6754, 'Merged Record', 1, 'Power Shine - July 2025', 83, '2026-03-31 00:53:32', '2026-03-31 00:53:32'),
(3459, 'Power Shine - September 2025', 2460.22, '2025-09-16', NULL, NULL, 17942, 'Merged Record', 1, 'Power Shine - September 2025', 84, '2026-03-31 00:53:32', '2026-03-31 00:53:32'),
(3460, 'Sharnay Foods - August 2025', 4384.78, '2025-08-21', NULL, NULL, 30801, 'Merged Record', 1, 'Sharnay Foods - August 2025', 85, '2026-03-31 00:54:11', '2026-03-31 00:54:11'),
(3461, 'Kanbery Cables - May 2025', 16293.40, '2025-05-03', NULL, NULL, 103228, 'Merged Record', 1, 'Kanbery Cables - May 2025', 86, '2026-03-31 00:54:42', '2026-03-31 00:54:42'),
(3462, 'Kanbery Cables - July 2025', 12996.46, '2025-07-08', NULL, NULL, 121915, 'Merged Record', 1, 'Kanbery Cables - July 2025', 87, '2026-03-31 00:54:42', '2026-03-31 00:54:42'),
(3463, 'Yoga Change Me - August 2025', 1364.04, '2025-08-28', NULL, NULL, 901, 'Merged Record', 1, 'Yoga Change Me - August 2025', 88, '2026-03-31 00:55:14', '2026-03-31 00:55:14'),
(3464, 'Om Sai Safeguard Services - July 2025', 573.94, '2025-07-24', NULL, NULL, 8960, 'Merged Record', 1, 'Om Sai Safeguard Services - July 2025', 89, '2026-03-31 00:55:43', '2026-03-31 00:55:43'),
(3465, 'Dhanvardhan Investment Firm - July 2025', 2370.18, '2025-07-27', NULL, NULL, 31241, 'Merged Record', 1, 'Dhanvardhan Investment Firm - July 2025', 90, '2026-03-31 00:56:12', '2026-03-31 00:56:12'),
(3466, 'Dhanvardhan Investment Firm - August 2025', 128.19, '2025-08-01', NULL, NULL, 1455, 'Merged Record', 1, 'Dhanvardhan Investment Firm - August 2025', 91, '2026-03-31 00:56:12', '2026-03-31 00:56:12'),
(3467, 'Pine Hardware - July 2025', 9999.38, '2025-07-12', NULL, NULL, 75649, 'Merged Record', 1, 'Pine Hardware - July 2025', 92, '2026-03-31 00:56:41', '2026-03-31 00:56:41'),
(3468, 'Microactive - June 2025', 4580.21, '2025-06-03', NULL, NULL, 38739, 'Merged Record', 1, 'Microactive - June 2025', 93, '2026-03-31 00:57:07', '2026-03-31 00:57:07'),
(3469, 'Microactive - July 2025', 349.52, '2025-07-18', NULL, NULL, 3069, 'Merged Record', 1, 'Microactive - July 2025', 94, '2026-03-31 00:57:07', '2026-03-31 00:57:07'),
(3470, 'BNI Uday - June 2025', 4172.96, '2025-06-03', NULL, NULL, 27350, 'Merged Record', 1, 'BNI Uday - June 2025', 95, '2026-03-31 00:57:35', '2026-03-31 00:57:35'),
(3471, 'BNI Uday - July 2025', 1475.33, '2025-07-18', NULL, NULL, 10311, 'Merged Record', 1, 'BNI Uday - July 2025', 96, '2026-03-31 00:57:35', '2026-03-31 00:57:35'),
(3472, 'S&S Tours Travels - July 2025', 2444.43, '2025-07-05', NULL, NULL, 37137, 'Merged Record', 1, 'S&S Tours Travels - July 2025', 97, '2026-03-31 00:58:03', '2026-03-31 00:58:03'),
(3473, 'Shri Uma Sales - July 2025', 2499.50, '2025-07-02', NULL, NULL, 33592, 'Merged Record', 1, 'Shri Uma Sales - July 2025', 98, '2026-03-31 00:59:13', '2026-03-31 00:59:13'),
(3474, 'Teknovative Solution - June 2025', 2909.87, '2025-06-18', NULL, NULL, 20401, 'Merged Record', 1, 'Teknovative Solution - June 2025', 99, '2026-03-31 00:59:39', '2026-03-31 00:59:39'),
(3475, '9 Square - May 2025', 14246.73, '2025-05-18', NULL, NULL, 66957, 'Merged Record', 1, '9 Square - May 2025', 100, '2026-03-31 01:00:12', '2026-03-31 01:00:12'),
(3476, '9 Square - June 2025', 2327.31, '2025-06-03', NULL, NULL, 7949, 'Merged Record', 1, '9 Square - June 2025', 101, '2026-03-31 01:00:12', '2026-03-31 01:00:12'),
(3477, '9 Square - July 2025', 444.39, '2025-07-18', NULL, NULL, 1371, 'Merged Record', 1, '9 Square - July 2025', 102, '2026-03-31 01:00:12', '2026-03-31 01:00:12'),
(3478, 'Well Way Tea - May 2025', 13902.21, '2025-05-03', NULL, NULL, 77145, 'Merged Record', 1, 'Well Way Tea - May 2025', 103, '2026-03-31 01:00:42', '2026-03-31 01:00:42'),
(3479, 'Well Way Tea - June 2025', 33.64, '2025-06-03', NULL, NULL, 198, 'Merged Record', 1, 'Well Way Tea - June 2025', 104, '2026-03-31 01:00:42', '2026-03-31 01:00:42'),
(3480, 'Blasttech - April 2025', 2418.80, '2025-04-16', NULL, NULL, 25149, 'Merged Record', 1, 'Blasttech - April 2025', 105, '2026-03-31 01:01:07', '2026-03-31 01:01:07'),
(3481, 'Blasttech - May 2025', 5469.85, '2025-05-01', NULL, NULL, 82841, 'Merged Record', 1, 'Blasttech - May 2025', 106, '2026-03-31 01:01:07', '2026-03-31 01:01:07'),
(3482, 'Blasttech - June 2025', 22.81, '2025-06-03', NULL, NULL, 213, 'Merged Record', 1, 'Blasttech - June 2025', 107, '2026-03-31 01:01:07', '2026-03-31 01:01:07'),
(3483, 'Divaami Chocolate - May 2025', 379.64, '2025-05-30', NULL, NULL, 3354, 'Merged Record', 1, 'Divaami Chocolate - May 2025', 108, '2026-03-31 01:01:32', '2026-03-31 01:01:32'),
(3484, 'Divaami Chocolate - June 2025', 1616.55, '2025-06-03', NULL, NULL, 22620, 'Merged Record', 1, 'Divaami Chocolate - June 2025', 109, '2026-03-31 01:01:32', '2026-03-31 01:01:32'),
(3485, 'Next Step MBBS - May 2025', 15185.73, '2025-05-07', NULL, NULL, 146210, 'Merged Record', 1, 'Next Step MBBS - May 2025', 110, '2026-03-31 01:02:00', '2026-03-31 01:02:00'),
(3486, 'YK Creation - April 2025', 1945.45, '2025-04-02', NULL, NULL, 23603, 'Merged Record', 1, 'YK Creation - April 2025', 111, '2026-03-31 01:02:34', '2026-03-31 01:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_subtotals`
--

CREATE TABLE `invoice_subtotals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `tax_invoice_id` varchar(255) DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT 0.00,
  `gst_amount` decimal(12,2) DEFAULT 0.00,
  `grand_total` decimal(12,2) DEFAULT 0.00,
  `total_records` int(11) DEFAULT 0,
  `total_impressions` bigint(20) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_subtotals`
--

INSERT INTO `invoice_subtotals` (`id`, `pdf_filename`, `tax_invoice_id`, `document_date`, `subtotal`, `gst_amount`, `grand_total`, `total_records`, `total_impressions`, `created_at`, `updated_at`) VALUES
(1, '2026-02-04T02-00 Tax invoice #26085672977785799-25960135323672896.pdf', 'ADS312-105452853', '2026-02-04', 4200.00, 756.00, 4956.00, 6, 457684, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(2, '2026-02-06T18-59 Tax invoice #26114216241598139-26080136891672737.pdf', 'ADS312-105465393', '2026-02-06', 4200.00, 756.00, 4956.00, 7, 333077, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(3, '2026-02-08T20-04 Tax invoice #26100992902920469-26117603497926075.pdf', 'ADS312-105474275', '2026-02-08', 4200.00, 756.00, 4956.00, 7, 311539, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(4, '2026-02-11T00-27 Tax invoice #26012705321749224-26032080216478406.pdf', 'ADS312-105484796', '2026-02-11', 4200.00, 756.00, 4956.00, 8, 474616, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(5, '2026-02-12T18-17 Tax invoice #26049393014747126-26049393071413787.pdf', 'ADS312-105493953', '2026-02-12', 4200.00, 756.00, 4956.00, 10, 1150427, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(6, '2026-02-13T15-32 Tax invoice #26103030086050089-25985958267757269.pdf', 'ADS312-105491330', '2026-02-13', 1257.17, 226.29, 1483.46, 10, 340031, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(7, '2026-02-13T23-42 Tax invoice #26062052616814499-25994016693618094.pdf', 'ADS312-105492714', '2026-02-13', 4200.00, 756.00, 4956.00, 12, 1062545, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(8, '2026-02-13T23-46 Tax invoice #25994049600281470-25994049626948134.pdf', 'ADS312-105492720', '2026-02-13', 5.97, 1.07, 7.04, 9, 997, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(9, '2026-02-14T06-16 Tax invoice #26064782213208206-26064782243208203.pdf', 'ADS312-105493943', '2026-02-14', 108.70, 19.57, 128.27, 12, 26272, '2026-03-30 23:37:36', '2026-03-30 23:37:36'),
(10, '2026-02-15T05-48 Tax invoice #26074670645552696-26002252766127819.pdf', 'ADS312-105498172', '2026-02-15', 4200.00, 756.00, 4956.00, 13, 931080, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(11, '2026-02-15T06-16 Tax invoice #26002422079444221-26182220584797699.pdf', 'ADS312-105498335', '2026-02-15', 1.47, 0.26, 1.73, 6, 14, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(12, '2026-02-16T06-16 Tax invoice #26191886600497764-26012024895150606.pdf', 'ADS312-105502582', '2026-02-16', 2941.66, 529.50, 3471.16, 10, 224655, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(13, '2026-02-17T06-16 Tax invoice #26031536379866120-26184668737886218.pdf', 'ADS312-105506593', '2026-02-17', 2940.23, 529.24, 3469.47, 8, 240782, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(14, '2026-02-18T06-12 Tax invoice #26083646951321727-26041181965568228.pdf', 'ADS312-105510548', '2026-02-18', 3800.01, 684.00, 4484.01, 8, 266767, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(15, '2026-02-19T06-12 Tax invoice #26093601633659592-26204294305923661.pdf', 'ADS312-105514341', '2026-02-19', 3084.17, 555.15, 3639.32, 9, 402327, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(16, '2026-02-20T06-12 Tax invoice #26123546873998406-26214116174941474.pdf', 'ADS312-105518463', '2026-02-20', 3569.48, 642.51, 4211.99, 7, 751528, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(17, '2026-02-21T06-15 Tax invoice #26258444153842013-26258444200508675.pdf', 'ADS312-105522576', '2026-02-21', 1303.86, 234.69, 1538.55, 6, 176733, '2026-03-30 23:37:37', '2026-03-30 23:37:37'),
(18, '2026-02-21T13-49 Tax invoice #26063645566655205-26180994858253611.pdf', 'ADS312-105529808', '2026-02-21', 4200.00, 756.00, 4956.00, 8, 864779, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(19, '2026-02-22T14-36 Tax invoice #26146959311657162-26016064351413322.pdf', 'ADS312-105533923', '2026-02-22', 4200.00, 756.00, 4956.00, 8, 779993, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(20, '2026-02-23T14-51 Tax invoice #26093656293654128-26087907857562310.pdf', 'ADS312-105538322', '2026-02-23', 4200.00, 756.00, 4956.00, 8, 742391, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(21, '2026-02-24T17-07 Tax invoice #26257871250565966-26292159970470431.pdf', 'ADS312-105543642', '2026-02-24', 4200.00, 756.00, 4956.00, 7, 809593, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(22, '2026-02-25T20-13 Tax invoice #26303764112643350-26116160161403741.pdf', 'ADS312-105549323', '2026-02-25', 4200.00, 756.00, 4956.00, 8, 707043, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(23, '2026-02-27T12-27 Tax invoice #26239900082363088-26064834546536302.pdf', 'ADS312-105558640', '2026-02-27', 4200.00, 756.00, 4956.00, 8, 318139, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(24, '2026-02-28T23-24 Tax invoice #26190363833983371-26335540666132361.pdf', 'ADS312-105565152', '2026-02-28', 4200.00, 756.00, 4956.00, 8, 239214, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(25, '2026-03-03T00-16 Tax invoice #26167566876263069-26099474579738965.pdf', 'ADS312-105574705', '2026-03-03', 4200.00, 756.00, 4956.00, 7, 127486, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(26, '2026-03-10T20-06 Tax invoice #26398889039797519-26239936022359492.pdf', 'ADS312-105614626', '2026-03-10', 4200.00, 756.00, 4956.00, 11, 52021, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(27, '2026-03-11T20-12 Tax invoice #26244263211926772-26318984511121307.pdf', 'ADS312-105619478', '2026-03-11', 4200.00, 756.00, 4956.00, 9, 63869, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(28, '2026-03-13T08-43 Tax invoice #26377803535239408-26334729812880110.pdf', 'ADS312-105627019', '2026-03-13', 4200.00, 756.00, 4956.00, 10, 95028, '2026-03-30 23:37:38', '2026-03-30 23:37:38'),
(29, '2026-03-13T13-53 Tax invoice #26461265653559861-26205099049176517.pdf', 'ADS312-105620586', '2026-03-13', 1.86, 0.33, 2.19, 5, 60, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(30, '2026-03-14T07-18 Tax invoice #26269652402721186-26452427577776997.pdf', 'ADS312-105624139', '2026-03-14', 3419.57, 615.52, 4035.09, 11, 73583, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(31, '2026-03-14T23-18 Tax invoice #26394279770258451-26441183805568042.pdf', 'ADS312-105627313', '2026-03-14', 4200.00, 756.00, 4956.00, 9, 100698, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(32, '2026-03-15T06-13 Tax invoice #26461696223516799-26278813105138449.pdf', 'ADS312-105628529', '2026-03-15', 228.19, 41.07, 269.26, 9, 6390, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(33, '2026-03-16T06-30 Tax invoice #26488315694188190-26294505873569173.pdf', 'ADS312-105640587', '2026-03-16', 2052.08, 369.37, 2421.45, 8, 56645, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(34, '2026-03-17T09-36 Tax invoice #26375655882120836-26483826937970394.pdf', 'ADS312-105645975', '2026-03-17', 4200.00, 756.00, 4956.00, 9, 203450, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(35, '2026-03-19T07-44 Tax invoice #26504177965935291-26485959317757157.pdf', 'ADS312-105655281', '2026-03-19', 4200.00, 756.00, 4956.00, 10, 496163, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(36, '2026-03-20T16-36 Tax invoice #26342560202097073-26336502499369509.pdf', 'ADS312-105654617', '2026-03-20', 2277.00, 409.86, 2686.86, 7, 341238, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(37, '2026-03-21T06-32 Tax invoice #26349896568030103-26418948937791530 (1).pdf', 'ADS312-105657047', '2026-03-21', 1091.47, 196.46, 1287.93, 6, 117664, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(38, '2026-03-22T06-36 Tax invoice #26520630017623420-26473417532344674.pdf', 'ADS312-105661211', '2026-03-22', 2798.46, 503.72, 3302.18, 7, 282011, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(39, '2026-03-23T06-36 Tax invoice #26550148621338225-26566629313023494.pdf', 'ADS312-105665195', '2026-03-23', 1715.15, 308.73, 2023.88, 6, 248099, '2026-03-30 23:37:39', '2026-03-30 23:37:39'),
(40, '2026-03-24T06-36 Tax invoice #26320894427596978-26388496344170120.pdf', 'ADS312-105669327', '2026-03-24', 1736.10, 312.50, 2048.60, 6, 246557, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(41, '2026-03-25T06-36 Tax invoice #26332321746454246-26464041136615643.pdf', 'ADS312-105673832', '2026-03-25', 1522.53, 274.06, 1796.59, 6, 219608, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(42, '2025-04-02T14-39 Tax invoice #9475873755858982-9470645536381803.pdf', 'ADS312-104340551', '2025-04-02', 4200.00, 756.00, 4956.00, 5, 232152, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(43, '2025-04-16T06-45 Tax invoice #9511078135671871-9629449357168085.pdf', 'ADS312-104386756', '2025-04-16', 1554.25, 279.77, 1834.02, 4, 161369, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(44, '2025-04-22T19-56 Tax invoice #9608591885920500-9799905413455814.pdf', 'ADS312-104407652', '2025-04-22', 4200.00, 756.00, 4956.00, 8, 58893, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(45, '2025-04-23T19-05 Tax invoice #9782689865177364-9766827626763589.pdf', 'ADS312-104405846', '2025-04-23', 1314.57, 236.62, 1551.19, 8, 112201, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(46, '2025-04-24T00-08 Tax invoice #9658201177626231-9658201197626229.pdf', 'ADS312-104406350', '2025-04-24', 746.07, 134.29, 880.36, 8, 64539, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(47, '2025-04-24T06-54 Tax invoice #9741531869293170-9683500871762933.pdf', 'ADS312-104407533', '2025-04-24', 27.16, 4.89, 32.05, 8, 1332, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(48, '2025-04-24T08-09 Tax invoice #9660603924052623-9624810367631986.pdf', 'ADS312-104407662', '2025-04-24', 4.57, 0.82, 5.39, 1, 461, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(49, '2025-04-24T10-17 Tax invoice #9566433756802975-9684535684992785.pdf', 'ADS312-104407974', '2025-04-24', 2.94, 0.53, 3.47, 1, 456, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(50, '2025-04-24T10-17 Tax invoice #9617390171707334-9566433176803033.pdf', 'ADS312-104407973', '2025-04-24', 1443.86, 259.89, 1703.75, 8, 131896, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(51, '2025-04-24T10-17 Tax invoice #9811678848945137-9742469792532711.pdf', 'ADS312-104407975', '2025-04-24', 0.14, 0.03, 0.17, 1, 6, '2026-03-30 23:37:40', '2026-03-30 23:37:40'),
(52, '2025-04-24T17-26 Tax invoice #9663498643763151-9686646224781731.pdf', 'ADS312-104408828', '2025-04-24', 0.04, 0.01, 0.05, 1, 2, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(53, '2025-04-24T17-26 Tax invoice #9744613732318317-9773678602745158.pdf', 'ADS312-104408827', '2025-04-24', 1865.78, 335.84, 2201.62, 8, 178678, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(54, '2025-04-24T17-37 Tax invoice #9773735376072814-9773735392739479.pdf', 'ADS312-104408860', '2025-04-24', 41.44, 7.46, 48.90, 8, 3856, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(55, '2025-04-25T06-07 Tax invoice #9631320043647685-9793212824125068.pdf', 'ADS312-104410658', '2025-04-25', 1384.31, 249.18, 1633.49, 8, 141817, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(56, '2025-04-25T22-39 Tax invoice #9628644957248522-9753545314758492.pdf', 'ADS312-104413004', '2025-04-25', 2958.26, 532.49, 3490.75, 8, 228193, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(57, '2025-04-25T22-39 Tax invoice #9672775349502147-9577596409020043.pdf', 'ADS312-104417991', '2025-04-25', 1241.74, 223.51, 1465.25, 1, 188132, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(58, '2025-04-26T20-52 Tax invoice #9830222633757425-9760701240709566.pdf', 'ADS312-104420807', '2025-04-26', 4200.00, 756.00, 4956.00, 9, 393010, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(59, '2025-04-27T06-07 Tax invoice #9641222712657417-9587495474696803.pdf', 'ADS312-104417288', '2025-04-27', 79.28, 14.27, 93.55, 9, 4351, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(60, '2025-04-27T10-51 Tax invoice #9834306003349088-9834306010015754.pdf', 'ADS312-104418026', '2025-04-27', 4.26, 0.77, 5.03, 1, 646, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(61, '2025-04-28T06-07 Tax invoice #9654111508035205-9800312403415111.pdf', 'ADS312-104420370', '2025-04-28', 1387.19, 249.69, 1636.88, 9, 144582, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(62, '2025-04-28T09-04 Tax invoice #9595799413866409-9841413515971670.pdf', 'ADS312-104420848', '2025-04-28', 3.29, 0.59, 3.88, 2, 454, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(63, '2025-04-28T19-27 Tax invoice #9695040457275636-9695040470608968.pdf', 'ADS312-104422204', '2025-04-28', 4200.00, 756.00, 4956.00, 9, 410138, '2026-03-30 23:37:41', '2026-03-30 23:37:41'),
(64, '2025-04-28T19-31 Tax invoice #9658473234265699-9695063553939993.pdf', 'ADS312-104422225', '2025-04-28', 7.42, 1.34, 8.76, 1, 65, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(65, '2025-04-28T23-22 Tax invoice #9805991996180485-9805992002847151.pdf', 'ADS312-104422583', '2025-04-28', 1611.80, 290.12, 1901.92, 9, 148954, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(66, '2025-04-29T06-07 Tax invoice #9823515631094787-9661903653922657.pdf', 'ADS312-104423624', '2025-04-29', 281.57, 50.68, 332.25, 9, 19153, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(67, '2025-04-29T19-27 Tax invoice #9607372962709054-9812970658815952.pdf', 'ADS312-104430808', '2025-04-29', 3300.23, 594.04, 3894.27, 9, 388073, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(68, '2025-04-29T19-27 Tax invoice #9661207390658949-9666683133444709.pdf', 'ADS312-104425389', '2025-04-29', 899.77, 161.96, 1061.73, 5, 67379, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(69, '2025-05-01T16-34 Tax invoice #9623193504460333-9674538282659189.pdf', 'ADS312-104431919', '2025-05-01', 2748.74, 494.77, 3243.51, 7, 229645, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(70, '2025-05-03T20-38 Tax invoice #9695924870520534-9641860189260331.pdf', 'ADS312-104443411', '2025-05-03', 4200.00, 756.00, 4956.00, 11, 49180, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(71, '2025-05-05T19-07 Tax invoice #9754307231348958-9711171882329166.pdf', 'ADS312-104444709', '2025-05-05', 2552.20, 459.40, 3011.60, 10, 34136, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(72, '2025-05-06T23-34 Tax invoice #9667138936732456-9842849559161400.pdf', 'ADS312-104448470', '2025-05-06', 968.64, 174.36, 1143.00, 11, 11107, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(73, '2025-05-07T06-11 Tax invoice #9767074420072239-9721113568001660.pdf', 'ADS312-104449520', '2025-05-07', 3029.46, 545.30, 3574.76, 14, 59141, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(74, '2025-05-07T22-10 Tax invoice #9727078957405121-9896998743746475.pdf', 'ADS312-104451825', '2025-05-07', 4200.00, 756.00, 4956.00, 14, 151787, '2026-03-30 23:37:42', '2026-03-30 23:37:42'),
(75, '2025-05-07T22-14 Tax invoice #9675415342571482-9793929894053363.pdf', 'ADS312-104451839', '2025-05-07', 2.12, 0.38, 2.50, 1, 33, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(76, '2025-05-08T06-06 Tax invoice #9899792563467093-9729882823791401.pdf', 'ADS312-104453081', '2025-05-08', 594.79, 107.06, 701.85, 14, 15210, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(77, '2025-05-08T21-27 Tax invoice #9859467570832932-9859467577499598.pdf', 'ADS312-104455465', '2025-05-08', 4200.00, 756.00, 4956.00, 14, 157930, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(78, '2025-05-08T21-31 Tax invoice #9738075386305482-9735540216558995.pdf', 'ADS312-104455473', '2025-05-08', 13.45, 2.42, 15.87, 7, 202, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(79, '2025-05-09T06-06 Tax invoice #9738554819590868-9908498455929837.pdf', 'ADS312-104456792', '2025-05-09', 491.63, 88.49, 580.12, 14, 23814, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(80, '2025-05-10T06-06 Tax invoice #9794229600690054-9750114928434861.pdf', 'ADS312-104460731', '2025-05-10', 3486.95, 627.65, 4114.60, 14, 168390, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(81, '2025-05-11T06-06 Tax invoice #9704417543004595-9926077687505247.pdf', NULL, '2025-05-11', 10.03, 1.81, 11.84, 1, 172, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(82, '2025-05-11T06-06 Tax invoice #9758687354244285-9880066085439747.pdf', 'ADS312-104464273', '2025-05-11', 2005.93, 361.07, 2367.00, 12, 139305, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(83, '2025-05-11T20-39 Tax invoice #9761149347331415-9763637757082578.pdf', 'ADS312-104471425', '2025-05-11', 4200.00, 756.00, 4956.00, 13, 167768, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(84, '2025-05-12T22-08 Tax invoice #9776972302415791-9816272565152424.pdf', 'ADS312-104475121', '2025-05-12', 4200.00, 756.00, 4956.00, 14, 195187, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(85, '2025-05-13T19-11 Tax invoice #9946727298773619-9824338167679197.pdf', 'ADS312-104478077', '2025-05-13', 4200.00, 756.00, 4956.00, 14, 138360, '2026-03-30 23:37:43', '2026-03-30 23:37:43'),
(86, '2025-05-14T20-31 Tax invoice #9786801444766205-9942079369238413.pdf', 'ADS312-104481994', '2025-05-14', 4200.00, 756.00, 4956.00, 14, 138742, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(87, '2025-05-15T23-40 Tax invoice #9804660556313632-9952237871555896.pdf', 'ADS312-104486158', '2025-05-15', 4200.00, 756.00, 4956.00, 14, 171122, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(88, '2025-05-16T06-36 Tax invoice #9807265392719815-9923011681145187.pdf', 'ADS312-104487016', '2025-05-16', 133.60, 24.05, 157.65, 12, 2320, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(89, '2025-05-17T07-48 Tax invoice #9857413094371704-10003754689737551.pdf', 'ADS312-104490380', '2025-05-17', 4200.00, 756.00, 4956.00, 13, 156612, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(90, '2025-05-18T05-42 Tax invoice #9865677870211893-9987039274742421.pdf', 'ADS312-104493221', '2025-05-18', 4200.00, 756.00, 4956.00, 14, 152909, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(91, '2025-05-19T06-51 Tax invoice #10020395208073499-9995505107229171.pdf', 'Document', '2025-05-19', 4200.00, 756.00, 4956.00, 13, 180880, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(92, '2025-05-20T06-17 Tax invoice #9956982037748151-10028364803943206.pdf', 'Document', '2025-05-20', 4200.00, 756.00, 4956.00, 13, 169643, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(93, '2025-05-20T19-14 Tax invoice #9887597618019918-9846249542154733.pdf', 'Document', '2025-05-20', 6500.00, 1170.00, 7670.00, 13, 303647, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(94, '2025-05-21T08-12 Tax invoice #9966266716819683-9998702960242720.pdf', 'Document', '2025-05-21', 8933.00, 1607.94, 10540.94, 13, 384980, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(95, '2025-05-21T08-29 Tax invoice #9791733087606373-9843244459121903.pdf', 'ADS312-104498800', '2025-05-21', 11.74, 2.11, 13.85, 12, 365, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(96, '2025-05-21T16-25 Tax invoice #9845726818873667-10001299326649750.pdf', 'ADS312-104499823', '2025-05-21', 9310.29, 1675.85, 10986.14, 13, 423345, '2026-03-30 23:37:44', '2026-03-30 23:37:44'),
(97, '2025-05-21T16-25 Tax invoice #9968822029897485-10001299723316377.pdf', 'ADS312-104499825', '2025-05-21', 1452.17, 261.39, 1713.56, 13, 41707, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(98, '2025-05-22T10-40 Tax invoice #9853469248099428-9853469261432760.pdf', 'ADS312-104502476', '2025-05-22', 7500.00, 1350.00, 8850.00, 14, 283825, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(99, '2025-05-23T07-14 Tax invoice #9924447911001560-9805931949519820.pdf', 'ADS312-104510397', '2025-05-23', 4200.00, 756.00, 4956.00, 15, 161878, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(100, '2025-05-24T04-57 Tax invoice #9871971522915868-9871971532915867.pdf', 'ADS312-104513293', '2025-05-24', 4200.00, 756.00, 4956.00, 14, 177626, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(101, '2025-05-25T00-28 Tax invoice #9870399383073077-10040123806100634.pdf', 'ADS312-104516085', '2025-05-25', 4200.00, 756.00, 4956.00, 14, 181235, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(102, '2025-05-26T09-20 Tax invoice #10049211225191892-9929490713830608.pdf', 'ADS312-104520886', '2025-05-26', 4200.00, 756.00, 4956.00, 14, 200420, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(103, '2025-05-27T03-25 Tax invoice #9951612954951722-10040654149380934.pdf', 'ADS312-104523752', '2025-05-27', 4200.00, 756.00, 4956.00, 16, 152342, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(104, '2025-05-27T17-59 Tax invoice #10083884421724577-9891167247662961.pdf', 'ADS312-104525943', '2025-05-27', 4200.00, 756.00, 4956.00, 12, 106262, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(105, '2025-05-28T11-01 Tax invoice #9896332120479807-9944604915652521.pdf', 'ADS312-104528470', '2025-05-28', 4200.00, 756.00, 4956.00, 12, 121909, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(106, '2025-05-28T22-10 Tax invoice #9905414339571586-10067786653334349.pdf', 'ADS312-104530269', '2025-05-28', 4200.00, 756.00, 4956.00, 14, 117272, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(107, '2025-05-29T15-24 Tax invoice #9851686004944414-10073048316141516.pdf', 'ADS312-104532445', '2025-05-29', 4200.00, 756.00, 4956.00, 13, 116251, '2026-03-30 23:37:45', '2026-03-30 23:37:45'),
(108, '2025-05-30T08-37 Tax invoice #9975388005907550-9916114618501558.pdf', 'ADS312-104535639', '2025-05-30', 4200.00, 756.00, 4956.00, 13, 126884, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(109, '2025-05-31T12-12 Tax invoice #9920247658088253-9984584734987877.pdf', 'ADS312-104539254', '2025-05-31', 4200.00, 756.00, 4956.00, 12, 200355, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(110, '2025-06-03T00-00 Tax invoice #9989178891195123-9989178897861789.pdf', 'ADS312-104548003', '2025-06-03', 4200.00, 756.00, 4956.00, 17, 82750, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(111, '2025-06-04T23-35 Tax invoice #10019800508132966-9953779151401766.pdf', 'ADS312-104554858', '2025-06-04', 4200.00, 756.00, 4956.00, 11, 29352, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(112, '2025-06-05T12-49 Tax invoice #9957961540983527-10080036792109341.pdf', 'ADS312-104551523', '2025-06-05', 1248.99, 224.82, 1473.81, 9, 6068, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(113, '2025-06-05T12-49 Tax invoice #10009600229152989-9905990426180638.pdf', 'ADS312-104551526', '2025-06-05', 5.15, 0.93, 6.08, 6, 38, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(114, '2025-06-05T12-49 Tax invoice #23876405402045908-9964620866984266.pdf', 'ADS312-104551527', '2025-06-05', 0.71, 0.13, 0.84, 2, 4, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(115, '2025-06-05T12-49 Tax invoice #23876405815379200-9957967054316309.pdf', 'ADS312-104551528', '2025-06-05', 1.23, 0.22, 1.45, 1, 16, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(116, '2025-06-05T12-50 Tax invoice #10009604799152532-9905994692846878.pdf', 'ADS312-104551530', '2025-06-05', 1.67, 0.30, 1.97, 1, 7, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(117, '2025-06-06T06-55 Tax invoice #23921265110893274-23921265120893273.pdf', 'ADS312-104554275', '2025-06-06', 1344.54, 242.02, 1586.56, 9, 8245, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(118, '2025-06-06T11-50 Tax invoice #23897480929938354-10017371638375848.pdf', 'ADS312-104554881', '2025-06-06', 4.92, 0.89, 5.81, 3, 21, '2026-03-30 23:37:46', '2026-03-30 23:37:46'),
(119, '2025-06-07T06-55 Tax invoice #10037784826334534-23890455587307556.pdf', 'ADS312-104557591', '2025-06-07', 3425.42, 616.58, 4042.00, 11, 22622, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(120, '2025-06-08T06-55 Tax invoice #23911196361900144-23863528593333594.pdf', 'ADS312-104560866', '2025-06-08', 3374.27, 607.37, 3981.64, 7, 31131, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(121, '2025-06-09T02-44 Tax invoice #23917160784637035-23869469502739503.pdf', 'ADS312-104562954', '2025-06-09', 4200.00, 756.00, 4956.00, 6, 42085, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(122, '2025-06-09T02-48 Tax invoice #23904109449275503-9987009848078700.pdf', 'ADS312-104562959', '2025-06-09', 0.88, 0.16, 1.04, 4, 11, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(123, '2025-06-09T06-55 Tax invoice #23905171189169329-10039051456207866.pdf', 'ADS312-104563935', '2025-06-09', 13.74, 2.47, 16.21, 5, 49, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(124, '2025-06-10T06-55 Tax invoice #9994254597354221-10059861210793562.pdf', 'ADS312-104567284', '2025-06-10', 1972.75, 355.10, 2327.85, 6, 24811, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(125, '2025-06-11T06-55 Tax invoice #9950298985083115-23920589244294190.pdf', 'ADS312-104570509', '2025-06-11', 1089.09, 196.04, 1285.13, 8, 34839, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(126, '2025-06-12T06-07 Tax invoice #23967270662959385-23893739010312552.pdf', 'ADS312-104573500', '2025-06-12', 251.66, 45.30, 296.96, 6, 33523, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(127, '2025-06-13T01-45 Tax invoice #23900470576306062-23973875498965568.pdf', 'ADS312-104581520', '2025-06-13', 4200.00, 756.00, 4956.00, 12, 165683, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(128, '2025-06-14T14-01 Tax invoice #10028500860596261-23912412941778492.pdf', 'ADS312-104586352', '2025-06-14', 4200.00, 756.00, 4956.00, 16, 176470, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(129, '2025-06-15T07-01 Tax invoice #10034042936708720-10040628889383463.pdf', 'ADS312-104583958', '2025-06-15', 3212.60, 578.27, 3790.87, 16, 84813, '2026-03-30 23:37:47', '2026-03-30 23:37:47'),
(130, '2025-06-16T00-21 Tax invoice #23867223216297461-23923812560638530.pdf', 'ADS312-104586239', '2025-06-16', 4200.00, 756.00, 4956.00, 17, 124195, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(131, '2025-06-16T00-25 Tax invoice #10039806909465656-23997133446639773.pdf', 'ADS312-104586247', '2025-06-16', 19.23, 3.46, 22.69, 1, 127, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(132, '2025-06-16T02-15 Tax invoice #23971493412537105-10041181679328183.pdf', 'ADS312-104586371', '2025-06-16', 9.04, 1.63, 10.67, 1, 9, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(133, '2025-06-16T06-08 Tax invoice #23868827072803742-23925532433799876.pdf', 'ADS312-104587113', '2025-06-16', 24.74, 4.45, 29.19, 12, 126, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(134, '2025-06-16T22-30 Tax invoice #23873899878963128-23964737669879347.pdf', 'ADS312-104589388', '2025-06-16', 4200.00, 756.00, 4956.00, 16, 120533, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(135, '2025-06-16T22-34 Tax invoice #24003906092629175-10099186570194354.pdf', 'ADS312-104589399', '2025-06-16', 1.96, 0.35, 2.31, 1, 22, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(136, '2025-06-17T06-08 Tax invoice #10050093688436982-23864116809941430.pdf', 'ADS312-104590568', '2025-06-17', 458.11, 82.46, 540.57, 16, 10142, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(137, '2025-06-17T10-00 Tax invoice #10050558875057126-23981901518162961.pdf', 'ADS312-104591261', '2025-06-17', 1261.75, 227.12, 1488.87, 16, 25128, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(138, '2025-06-18T06-08 Tax invoice #10057374021042278-23872296885790089.pdf', 'ADS312-104593971', '2025-06-18', 3587.95, 645.83, 4233.78, 16, 109529, '2026-03-30 23:37:48', '2026-03-30 23:37:48'),
(139, '2025-06-18T21-37 Tax invoice #23980749278278186-23980749298278184.pdf', 'ADS312-104596125', '2025-06-18', 4200.00, 756.00, 4956.00, 19, 102870, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(140, '2025-06-18T21-41 Tax invoice #24019821817704269-23890014704018312.pdf', 'ADS312-104596133', '2025-06-18', 21.81, 3.93, 25.74, 12, 266, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(141, '2025-06-19T06-11 Tax invoice #23892890447064071-10072338626212489.pdf', 'ADS312-104597433', '2025-06-19', 1254.73, 225.85, 1480.58, 19, 29471, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(142, '2025-06-19T18-33 Tax invoice #10076593372453681-23884426381243806.pdf', 'ADS312-104599153', '2025-06-19', 4200.00, 756.00, 4956.00, 19, 91131, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(143, '2025-06-19T18-37 Tax invoice #24026804923672625-23884452167907894.pdf', 'ADS312-104599170', '2025-06-19', 7.41, 1.33, 8.74, 3, 167, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(144, '2025-06-19T20-56 Tax invoice #23898215463198236-10077519199027765.pdf', 'ADS312-104599522', '2025-06-19', 652.07, 117.37, 769.44, 19, 18782, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(145, '2025-06-19T20-56 Tax invoice #23898215743198208-10071896922923325.pdf', 'ADS312-104599523', '2025-06-19', 3.25, 0.59, 3.84, 2, 40, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(146, '2025-06-19T20-57 Tax invoice #10071902129589471-23988852400801207.pdf', 'ADS312-104599525', '2025-06-19', 6.09, 1.10, 7.19, 10, 302, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(147, '2025-06-20T06-11 Tax invoice #24005188649167581-23901309022888880.pdf', 'ADS312-104600756', '2025-06-20', 1377.88, 248.02, 1625.90, 19, 32470, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(148, '2025-06-20T15-55 Tax invoice #23891445347208576-23995009213518859.pdf', 'ADS312-104602173', '2025-06-20', 4200.00, 756.00, 4956.00, 19, 76372, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(149, '2025-06-20T15-59 Tax invoice #10078019725644378-23891468170539627.pdf', 'ADS312-104602186', '2025-06-20', 8.48, 1.53, 10.01, 1, 133, '2026-03-30 23:37:49', '2026-03-30 23:37:49'),
(150, '2025-06-21T01-10 Tax invoice #10081394145306936-24037599075926543.pdf', 'ADS312-104603301', '2025-06-21', 4200.00, 756.00, 4956.00, 20, 71628, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(151, '2025-06-21T01-14 Tax invoice #23964917246528061-10080223555423991.pdf', 'ADS312-104603307', '2025-06-21', 12.08, 2.17, 14.25, 3, 368, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(152, '2025-06-21T06-11 Tax invoice #23966407533045699-23909682028718246.pdf', 'ADS312-104604217', '2025-06-21', 34.75, 6.26, 41.01, 15, 90, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(153, '2025-06-21T14-49 Tax invoice #24002763312743449-24002763322743448.pdf', 'ADS312-104605402', '2025-06-21', 4200.00, 756.00, 4956.00, 16, 57212, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(154, '2025-06-21T14-53 Tax invoice #23912640641755718-10091370797642605.pdf', 'ADS312-104605404', '2025-06-21', 102.98, 18.54, 121.52, 1, 2203, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(155, '2025-06-22T06-11 Tax invoice #24020762137610232-10089330874513259.pdf', 'ADS312-104607460', '2025-06-22', 2975.36, 535.56, 3510.92, 16, 74592, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(156, '2025-06-22T11-49 Tax invoice #23905765992443178-10091059607673719.pdf', 'ADS312-104608322', '2025-06-22', 658.82, 118.59, 777.41, 10, 3100, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(157, '2025-06-22T11-53 Tax invoice #23919251734427942-10039075326205480.pdf', 'ADS312-104608329', '2025-06-22', 1405.02, 252.90, 1657.92, 13, 38072, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(158, '2025-06-23T06-11 Tax invoice #10097008730412140-23981449741541478.pdf', 'ADS312-104610753', '2025-06-23', 3137.60, 564.77, 3702.37, 12, 88042, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(159, '2025-06-23T19-21 Tax invoice #23929712910048491-23865314643154986.pdf', 'ADS312-104612701', '2025-06-23', 4200.00, 756.00, 4956.00, 17, 87140, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(160, '2025-06-23T19-25 Tax invoice #23865339036485880-23865339046485879.pdf', 'ADS312-104612726', '2025-06-23', 7.86, 1.41, 9.27, 6, 123, '2026-03-30 23:37:50', '2026-03-30 23:37:50'),
(161, '2025-06-24T06-11 Tax invoice #24063180013368449-24063180016701782.pdf', 'ADS312-104614307', '2025-06-24', 2165.30, 389.75, 2555.05, 16, 41420, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(162, '2025-06-24T14-14 Tax invoice #23870808062605640-24026853547001092.pdf', 'ADS312-104615499', '2025-06-24', 4200.00, 756.00, 4956.00, 16, 63595, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(163, '2025-06-24T14-18 Tax invoice #23872252942461156-23936626579357124.pdf', 'ADS312-104615506', '2025-06-24', 14.37, 2.59, 16.96, 9, 213, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(164, '2025-06-24T23-23 Tax invoice #23996176186735500-24030266206659826.pdf', 'ADS312-104616758', '2025-06-24', 1819.00, 327.42, 2146.42, 16, 53538, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(165, '2025-06-24T23-27 Tax invoice #23926332370386540-23939972892355826.pdf', 'ADS312-104616766', '2025-06-24', 1506.63, 271.19, 1777.82, 10, 12570, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(166, '2025-06-25T06-11 Tax invoice #23928381203514990-24032520619767718.pdf', 'ADS312-104617887', '2025-06-25', 287.35, 51.72, 339.07, 14, 3913, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(167, '2025-06-25T16-07 Tax invoice #23886506941035757-24035917162761397.pdf', 'ADS312-104619236', '2025-06-25', 4200.00, 756.00, 4956.00, 14, 59625, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(168, '2025-06-25T16-11 Tax invoice #24074621125557671-24035939382759175.pdf', 'ADS312-104619244', '2025-06-25', 0.95, 0.17, 1.12, 5, 54, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(169, '2025-06-26T05-32 Tax invoice #23884624784557301-23936577799361997.pdf', 'ADS312-104621301', '2025-06-26', 4200.00, 756.00, 4956.00, 15, 60326, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(170, '2025-06-26T05-36 Tax invoice #23884644854555294-24040632258956554.pdf', 'ADS312-104621303', '2025-06-26', 0.82, 0.15, 0.97, 2, 3, '2026-03-30 23:37:51', '2026-03-30 23:37:51'),
(171, '2025-06-26T06-06 Tax invoice #23936766992676411-24079201078433009.pdf', 'ADS312-104621362', '2025-06-26', 0.17, 0.03, 0.20, 4, 8, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(172, '2025-06-26T14-24 Tax invoice #10070294233083589-10070294243083588.pdf', 'ADS312-104622637', '2025-06-26', 2839.41, 511.09, 3350.50, 15, 48477, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(173, '2025-06-27T01-14 Tax invoice #24085494964470287-23891553823864397.pdf', 'ADS312-104624126', '2025-06-27', 4200.00, 756.00, 4956.00, 16, 78582, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(174, '2025-06-27T01-18 Tax invoice #23892571430429307-24047792794907167.pdf', 'ADS312-104624130', '2025-06-27', 20.15, 3.63, 23.78, 3, 305, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(175, '2025-06-27T06-40 Tax invoice #24087102117642905-10075664659213213.pdf', 'ADS312-104625152', '2025-06-27', 20.26, 3.65, 23.91, 14, 124, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(176, '2025-06-27T16-54 Tax invoice #24053440367675743-24066762626343516.pdf', 'ADS312-104626594', '2025-06-27', 4200.00, 756.00, 4956.00, 16, 68505, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(177, '2025-06-27T16-58 Tax invoice #24018982421121543-24018982441121541.pdf', 'ADS312-104626606', '2025-06-27', 23.58, 4.24, 27.82, 9, 338, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(178, '2025-06-28T06-41 Tax invoice #24023565610663224-10083292925117053.pdf', 'ADS312-104628947', '2025-06-28', 2644.60, 476.03, 3120.63, 12, 62969, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(179, '2025-06-28T17-06 Tax invoice #23956885887331188-23971254129227702.pdf', 'ADS312-104630392', '2025-06-28', 4200.00, 756.00, 4956.00, 12, 84839, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(180, '2025-06-28T17-10 Tax invoice #23956908793995564-24098012173218566.pdf', 'ADS312-104630402', '2025-06-28', 34.22, 6.16, 40.38, 11, 476, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(181, '2025-06-29T06-41 Tax invoice #10090862234360122-23916122224740895.pdf', 'ADS312-104632529', '2025-06-29', 3142.40, 565.63, 3708.03, 12, 69849, '2026-03-30 23:37:52', '2026-03-30 23:37:52'),
(182, '2025-06-29T17-01 Tax invoice #23912204498465996-24034933799526405.pdf', 'ADS312-104639255', '2025-06-29', 1822.23, 328.00, 2150.23, 11, 60728, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(183, '2025-06-29T17-01 Tax invoice #23914940894859027-23914940898192360.pdf', 'ADS312-104633809', '2025-06-29', 2377.77, 428.00, 2805.77, 11, 21078, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(184, '2025-06-30T09-22 Tax invoice #24075987498754363-23924109690608815.pdf', 'ADS312-104641702', '2025-06-30', 4200.00, 756.00, 4956.00, 11, 92153, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(185, '2025-07-02T13-50 Tax invoice #23878677295152049-24057151763971275.pdf', 'ADS312-104648974', '2025-07-02', 4200.00, 756.00, 4956.00, 12, 88938, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(186, '2025-07-05T17-46 Tax invoice #24027073030312478-23966656316354152.pdf', 'ADS312-104659825', '2025-07-05', 4200.00, 756.00, 4956.00, 4, 45698, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(187, '2025-07-08T09-33 Tax invoice #24048307711522343-24048307744855673.pdf', 'ADS312-104668895', '2025-07-08', 4200.00, 756.00, 4956.00, 10, 78647, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(188, '2025-07-09T15-32 Tax invoice #23934565679563210-24162907160062395.pdf', 'ADS312-104672914', '2025-07-09', 4200.00, 756.00, 4956.00, 10, 110948, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(189, '2025-07-10T19-06 Tax invoice #24193262873693495-24193262880360161.pdf', 'ADS312-104677370', '2025-07-10', 4200.00, 756.00, 4956.00, 12, 113649, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(190, '2025-07-11T21-19 Tax invoice #24008922085460903-24169781772708268.pdf', 'ADS312-104680929', '2025-07-11', 4200.00, 756.00, 4956.00, 12, 134426, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(191, '2025-07-12T22-25 Tax invoice #24189680220718422-24021257387560710.pdf', 'ADS312-104684427', '2025-07-12', 4200.00, 756.00, 4956.00, 12, 141506, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(192, '2025-07-13T23-41 Tax invoice #24197742683245509-24218763744476741.pdf', 'ADS312-104688186', '2025-07-13', 4200.00, 756.00, 4956.00, 12, 138063, '2026-03-30 23:37:53', '2026-03-30 23:37:53'),
(193, '2025-07-15T08-52 Tax invoice #24088092480877194-24103782822641498.pdf', 'ADS312-104693131', '2025-07-15', 4200.00, 756.00, 4956.00, 11, 142262, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(194, '2025-07-16T06-02 Tax invoice #24166307859722331-24095678720118570.pdf', 'ADS312-104696177', '2025-07-16', 2809.52, 505.71, 3315.23, 12, 106094, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(195, '2025-07-17T22-45 Tax invoice #24062367883449660-24180354844984299.pdf', 'ADS312-104702283', '2025-07-17', 4200.00, 756.00, 4956.00, 12, 203005, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(196, '2025-07-18T03-36 Tax invoice #23911196361900144-24003514926001618.pdf', '312-104560866', '2025-07-18', 3374.27, 607.37, 3981.64, 7, 31131, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(197, '2025-07-19T06-42 Tax invoice #24191078560578594-24136421002711013.pdf', NULL, '2025-07-19', 16.66, 3.00, 19.66, 2, 142, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(198, '2025-07-19T15-23 Tax invoice #24075848785434903-24265043276515454.pdf', 'ADS312-104707383', '2025-07-19', 4200.00, 756.00, 4956.00, 12, 113396, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(199, '2025-07-20T08-38 Tax invoice #24083804397972676-24083804401306009.pdf', 'ADS312-104710063', '2025-07-20', 4200.00, 756.00, 4956.00, 12, 127850, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(200, '2025-07-20T22-36 Tax invoice #24203854109301039-24085985371087911.pdf', 'ADS312-104712166', '2025-07-20', 4200.00, 756.00, 4956.00, 12, 133866, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(201, '2025-07-21T19-13 Tax invoice #24094731706879945-24260783643608079.pdf', 'ADS312-104715077', '2025-07-21', 4200.00, 756.00, 4956.00, 14, 130428, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(202, '2025-07-22T12-32 Tax invoice #24100630296290086-24098338523185929.pdf', 'ADS312-104717271', '2025-07-22', 4200.00, 756.00, 4956.00, 14, 119551, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(203, '2025-07-23T05-14 Tax invoice #24221964570823326-24167489329604180.pdf', 'ADS312-104719668', '2025-07-23', 4200.00, 756.00, 4956.00, 13, 130422, '2026-03-30 23:37:54', '2026-03-30 23:37:54'),
(204, '2025-07-23T21-43 Tax invoice #24266921799660931-24049558668063910.pdf', 'ADS312-104722091', '2025-07-23', 4200.00, 756.00, 4956.00, 13, 136773, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(205, '2025-07-24T16-03 Tax invoice #24055509970802113-24116103771409404.pdf', 'ADS312-104724321', '2025-07-24', 4200.00, 756.00, 4956.00, 13, 142304, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(206, '2025-07-25T12-25 Tax invoice #24186153517737761-24118925184460592.pdf', 'ADS312-104727268', '2025-07-25', 4200.00, 756.00, 4956.00, 12, 161722, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(207, '2025-07-26T08-15 Tax invoice #24247128978306885-24068754979477612.pdf', 'ADS312-104729981', '2025-07-26', 4200.00, 756.00, 4956.00, 10, 162198, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(208, '2025-07-26T12-31 Tax invoice #24126890306997413-24299499379736505.pdf', 'ADS312-104725914', '2025-07-26', 772.40, 139.03, 911.43, 10, 35122, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(209, '2025-07-27T00-37 Tax invoice #24182533898099718-24303799705973139.pdf', 'ADS312-104727283', '2025-07-27', 2.59, 0.47, 3.06, 2, 63, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(210, '2025-07-27T06-30 Tax invoice #24132814616404982-24136756186010829.pdf', 'ADS312-104728263', '2025-07-27', 2502.34, 450.42, 2952.76, 11, 101349, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(211, '2025-07-27T20-28 Tax invoice #24188909564128818-24259088153777634.pdf', 'ADS312-104730005', '2025-07-27', 1.14, 0.21, 1.35, 3, 36, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(212, '2025-07-28T02-48 Tax invoice #24312419178444525-24145793278440454.pdf', 'ADS312-104730572', '2025-07-28', 4200.00, 756.00, 4956.00, 8, 109239, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(213, '2025-07-28T02-52 Tax invoice #24082934061393037-24145811011772014.pdf', 'ADS312-104730584', '2025-07-28', 4.03, 0.73, 4.76, 4, 79, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(214, '2025-07-28T06-30 Tax invoice #24146882398331542-24262360640117052.pdf', 'ADS312-104731401', '2025-07-28', 7.08, 1.27, 8.35, 7, 91, '2026-03-30 23:37:55', '2026-03-30 23:37:55'),
(215, '2025-07-29T06-30 Tax invoice #24322270867459356-24200832789603162.pdf', 'ADS312-104734765', '2025-07-29', 2805.54, 505.00, 3310.54, 8, 76393, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(216, '2025-07-30T06-30 Tax invoice #24330860309933745-24161846053501842.pdf', 'ADS312-104738019', '2025-07-30', 1922.35, 346.02, 2268.37, 5, 22778, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(217, '2025-07-31T06-30 Tax invoice #24217588637927577-24327053736981070.pdf', 'ADS312-104741323', '2025-07-31', 1866.82, 336.03, 2202.85, 4, 22098, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(218, '2025-08-01T06-30 Tax invoice #24180565871629861-24347515628268213.pdf', 'ADS312-104745165', '2025-08-01', 1375.54, 247.60, 1623.14, 3, 14976, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(219, '2025-08-02T06-30 Tax invoice #24187087164311064-24344159268603850.pdf', 'ADS312-104748528', '2025-08-02', 1190.34, 214.26, 1404.60, 2, 15570, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(220, '2025-08-03T06-30 Tax invoice #24364632463223196-24191892550497188.pdf', 'ADS312-104751780', '2025-08-03', 298.98, 53.82, 352.80, 2, 2625, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(221, '2025-08-05T22-34 Tax invoice #24215250348161408-24215250361494740.pdf', 'ADS312-104766186', '2025-08-05', 4200.00, 756.00, 4956.00, 4, 52363, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(222, '2025-08-08T08-45 Tax invoice #24237724202580689-24237724215914021.pdf', 'ADS312-104774373', '2025-08-08', 4200.00, 756.00, 4956.00, 10, 54532, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(223, '2025-08-10T10-48 Tax invoice #24307321682287605-24198946066458502.pdf', 'ADS312-104780745', '2025-08-10', 4200.00, 756.00, 4956.00, 11, 152150, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(224, '2025-08-12T13-28 Tax invoice #24217641171255658-24467525492933897.pdf', 'ADS312-104787837', '2025-08-12', 4200.00, 756.00, 4956.00, 14, 194303, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(225, '2025-08-14T11-36 Tax invoice #24452285591124550-24234157889603986.pdf', 'ADS312-104795349', '2025-08-14', 4200.00, 756.00, 4956.00, 14, 177600, '2026-03-30 23:37:56', '2026-03-30 23:37:56'),
(226, '2025-08-16T06-45 Tax invoice #24310918291927950-24312866105066503.pdf', 'ADS312-104802010', '2025-08-16', 2943.03, 529.75, 3472.78, 14, 150793, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(227, '2025-08-18T09-55 Tax invoice #24445701015116346-24445701018449679.pdf', 'ADS312-104808896', '2025-08-18', 4200.00, 756.00, 4956.00, 13, 247877, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(228, '2025-08-20T08-47 Tax invoice #24285667704453004-24410443608642083.pdf', 'ADS312-104815433', '2025-08-20', 4200.00, 756.00, 4956.00, 15, 275229, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(229, '2025-08-21T23-38 Tax invoice #24551440257875753-24478243925195388.pdf', 'ADS312-104820919', '2025-08-21', 4200.00, 756.00, 4956.00, 18, 247161, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(230, '2025-08-22T23-32 Tax invoice #24540591675627273-24372901602396286.pdf', 'ADS312-104824221', '2025-08-22', 4200.00, 756.00, 4956.00, 18, 130180, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(231, '2025-08-23T22-13 Tax invoice #24568905856129193-24378455015174273.pdf', 'ADS312-104827346', '2025-08-23', 4200.00, 756.00, 4956.00, 19, 162699, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(232, '2025-08-24T23-36 Tax invoice #24545442768475498-24388431920843253.pdf', 'ADS312-104830984', '2025-08-24', 4200.00, 756.00, 4956.00, 19, 158764, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(233, '2025-08-26T00-02 Tax invoice #24396209813398793-24398461743173605.pdf', 'ADS312-104835044', '2025-08-26', 4200.00, 756.00, 4956.00, 19, 166109, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(234, '2025-08-27T01-28 Tax invoice #24345481188471655-24563722946647480.pdf', 'ADS312-104839338', '2025-08-27', 4200.00, 756.00, 4956.00, 18, 120583, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(235, '2025-08-28T02-44 Tax invoice #24417886964564416-24355129760840131.pdf', 'ADS312-104843792', '2025-08-28', 4200.00, 756.00, 4956.00, 19, 132257, '2026-03-30 23:37:57', '2026-03-30 23:37:57'),
(236, '2025-08-29T09-51 Tax invoice #24427715010248277-24617151607971284.pdf', 'ADS312-104848874', '2025-08-29', 4200.00, 756.00, 4956.00, 19, 140499, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(237, '2025-08-30T17-14 Tax invoice #24438022582550849-24438261545860290.pdf', 'ADS312-104853956', '2025-08-30', 4200.00, 756.00, 4956.00, 19, 132383, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(238, '2025-08-31T14-36 Tax invoice #24561016396918140-24445223118497462.pdf', 'ADS312-104857256', '2025-08-31', 4200.00, 756.00, 4956.00, 22, 139880, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(239, '2025-09-16T06-58 Tax invoice #24579967475023029-24643847731968335.pdf', 'ADS312-104915211', '2025-09-16', 2032.19, 365.79, 2397.98, 22, 287858, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(240, '2025-09-16T13-45 Tax invoice #24629343836752053-24772208259132284.pdf', 'ADS312-104910918', '2025-09-16', 22.36, 4.02, 26.38, 1, 244, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(241, '2025-09-16T14-50 Tax invoice #24751682551184850-24582448454774931.pdf', 'ADS312-104911029', '2025-09-16', 4200.00, 756.00, 4956.00, 22, 480995, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(242, '2025-09-16T14-54 Tax invoice #24584083304611447-24646319671721141.pdf', 'ADS312-104911037', '2025-09-16', 10.73, 1.93, 12.66, 1, 57, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(243, '2025-09-17T01-10 Tax invoice #24650142308005544-24633537662999337.pdf', 'ADS312-104912325', '2025-09-17', 4200.00, 756.00, 4956.00, 22, 594801, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(244, '2025-09-17T01-14 Tax invoice #24776496928703417-24587910564228721.pdf', 'ADS312-104912333', '2025-09-17', 1.08, 0.19, 1.27, 1, 10, '2026-03-30 23:37:58', '2026-03-30 23:37:58'),
(245, '2025-09-17T01-24 Tax invoice #24633614022991701-24524911150528657.pdf', 'ADS312-104912346', '2025-09-17', 40.41, 7.27, 47.68, 2, 25, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(246, '2025-09-17T15-47 Tax invoice #24590984543921322-24638198429199927.pdf', 'ADS312-104919941', '2025-09-17', 4200.00, 756.00, 4956.00, 21, 519847, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(247, '2025-09-18T02-37 Tax invoice #24595319806821129-24785600527793057.pdf', 'ADS312-104921955', '2025-09-18', 4200.00, 756.00, 4956.00, 21, 531915, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(248, '2025-09-18T18-07 Tax invoice #24600840496269060-24664897643196677.pdf', 'ADS312-104924211', '2025-09-18', 4200.00, 756.00, 4956.00, 21, 582387, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(249, '2025-09-19T11-08 Tax invoice #24671238722562569-24545845375101901.pdf', 'ADS312-104926571', '2025-09-19', 4200.00, 756.00, 4956.00, 21, 276636, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(250, '2025-09-20T01-39 Tax invoice #24551454917874280-24676891855330589.pdf', 'ADS312-104928852', '2025-09-20', 4200.00, 756.00, 4956.00, 23, 142269, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(251, '2025-09-20T20-00 Tax invoice #24809857025367407-24621047234248387.pdf', 'ADS312-104931401', '2025-09-20', 4200.00, 756.00, 4956.00, 24, 87490, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(252, '2025-09-21T16-22 Tax invoice #24628267113526399-24690646840621757.pdf', 'ADS312-104933971', '2025-09-21', 4200.00, 756.00, 4956.00, 24, 108818, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(253, '2025-09-22T11-42 Tax invoice #24790685407284565-24634978922855218.pdf', 'ADS312-104936979', '2025-09-22', 4200.00, 756.00, 4956.00, 21, 113265, '2026-03-30 23:37:59', '2026-03-30 23:37:59'),
(254, '2025-09-23T09-09 Tax invoice #24810465868639851-24641277922225317.pdf', 'ADS312-104940177', '2025-09-23', 4200.00, 756.00, 4956.00, 21, 112302, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(255, '2025-09-24T02-05 Tax invoice #24650744731278632-24694768940209542.pdf', 'ADS312-104942669', '2025-09-24', 4200.00, 756.00, 4956.00, 22, 129952, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(256, '2025-09-24T22-40 Tax invoice #24657192430633867-24655716010781508.pdf', 'ADS312-104945714', '2025-09-24', 4200.00, 756.00, 4956.00, 22, 136995, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(257, '2025-09-25T19-35 Tax invoice #24666497843036654-24664898933196550.pdf', 'ADS312-104948859', '2025-09-25', 4200.00, 756.00, 4956.00, 22, 134606, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(258, '2025-09-26T21-04 Tax invoice #24863753093311133-24676571842029254.pdf', 'ADS312-104952557', '2025-09-26', 4200.00, 756.00, 4956.00, 23, 91783, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(259, '2025-09-27T19-46 Tax invoice #24851700314516406-24839921762360929.pdf', 'ADS312-104955652', '2025-09-27', 4200.00, 756.00, 4956.00, 21, 105281, '2026-03-30 23:38:00', '2026-03-30 23:38:00');
INSERT INTO `invoice_subtotals` (`id`, `pdf_filename`, `tax_invoice_id`, `document_date`, `subtotal`, `gst_amount`, `grand_total`, `total_records`, `total_impressions`, `created_at`, `updated_at`) VALUES
(260, '2025-09-28T19-05 Tax invoice #24692601400426303-24805418772477900.pdf', 'ADS312-104959019', '2025-09-28', 4200.00, 756.00, 4956.00, 16, 81965, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(261, '2025-09-30T21-01 Tax invoice #24714168014936303-24774329758920131.pdf', 'ADS312-104966979', '2025-09-30', 4200.00, 756.00, 4956.00, 18, 100353, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(262, '2025-10-02T13-48 Tax invoice #24886734834346288-24775217945497974.pdf', 'ADS312-104972705', '2025-10-02', 4200.00, 756.00, 4956.00, 13, 84800, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(263, '2025-10-05T23-27 Tax invoice #24705307365822367-24705307375822366.pdf', 'ADS312-104984817', '2025-10-05', 4200.00, 756.00, 4956.00, 11, 60020, '2026-03-30 23:38:00', '2026-03-30 23:38:00'),
(264, '2025-10-08T15-14 Tax invoice #24983757057977402-24906065372413239.pdf', 'ADS312-104994388', '2025-10-08', 4200.00, 756.00, 4956.00, 9, 80793, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(265, '2025-10-12T03-03 Tax invoice #25023730543980053-24878576445162123.pdf', 'ADS312-105008378', '2025-10-12', 4200.00, 756.00, 4956.00, 11, 214383, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(266, '2025-10-16T06-54 Tax invoice #25044481268571642-24875545158798591.pdf', 'ADS312-105024368', '2025-10-16', 3867.15, 696.09, 4563.24, 8, 73816, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(267, '2025-10-19T06-13 Tax invoice #24955861677433599-25021679107518531.pdf', NULL, '2025-10-19', 1.20, 0.22, 1.42, 1, 49, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(268, '2025-10-19T21-57 Tax invoice #24962252563461177-24922597194093383.pdf', 'ADS312-105038141', '2025-10-19', 4200.00, 756.00, 4956.00, 7, 84099, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(269, '2025-10-23T20-08 Tax invoice #25004025205950579-24895963013423467.pdf', 'ADS312-105052474', '2025-10-23', 4200.00, 756.00, 4956.00, 6, 206501, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(270, '2025-10-28T13-01 Tax invoice #25007899525563154-25120237094329398.pdf', 'ADS312-105068488', '2025-10-28', 4200.00, 756.00, 4956.00, 5, 202811, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(271, '2025-11-03T14-31 Tax invoice #25066374186382354-25065294459823659.pdf', 'ADS312-105090731', '2025-11-03', 4200.00, 756.00, 4956.00, 4, 144441, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(272, '2025-11-06T14-02 Tax invoice #25251656011187500-25285376757815429.pdf', 'ADS312-105102102', '2025-11-06', 4200.00, 756.00, 4956.00, 10, 88845, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(273, '2025-11-07T19-13 Tax invoice #25106720432347728-25263567269996374.pdf', 'ADS312-105107066', '2025-11-07', 4200.00, 756.00, 4956.00, 13, 57569, '2026-03-30 23:38:01', '2026-03-30 23:38:01'),
(274, '2025-11-09T06-53 Tax invoice #25279390821747352-25122485997437838.pdf', 'ADS312-105106987', '2025-11-09', 1012.42, 182.24, 1194.66, 14, 17902, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(275, '2025-11-09T07-03 Tax invoice #25236388159380957-25186719644347805.pdf', 'ADS312-105107045', '2025-11-09', 373.59, 67.25, 440.84, 14, 5380, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(276, '2025-11-09T07-26 Tax invoice #25292471363772630-25122703997416038.pdf', 'ADS312-105107081', '2025-11-09', 3.91, 0.70, 4.61, 3, 100, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(277, '2025-11-10T07-00 Tax invoice #25178669191819512-25302089029477530.pdf', 'ADS312-105110618', '2025-11-10', 2161.73, 389.11, 2550.84, 11, 52880, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(278, '2025-11-11T06-27 Tax invoice #25255230377496735-25141341872218917.pdf', 'ADS312-105114071', '2025-11-11', 2248.27, 404.69, 2652.96, 11, 60672, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(279, '2025-11-12T06-27 Tax invoice #25216060958080340-25308672488819185.pdf', 'ADS312-105117877', '2025-11-12', 2167.53, 390.16, 2557.69, 11, 64046, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(280, '2025-11-13T06-27 Tax invoice #25099099703109796-25351749001178204.pdf', 'ADS312-105121710', '2025-11-13', 1803.05, 324.55, 2127.60, 10, 47046, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(281, '2025-11-14T06-27 Tax invoice #25170420102644427-25177148388638261.pdf', 'ADS312-105125570', '2025-11-14', 1559.53, 280.72, 1840.25, 8, 35019, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(282, '2025-11-15T06-27 Tax invoice #25349820538037712-25117511777935255.pdf', 'ADS312-105129614', '2025-11-15', 1533.61, 276.05, 1809.66, 8, 34442, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(283, '2025-11-16T06-13 Tax invoice #25302628676090238-25188428030843634.pdf', 'ADS312-105133688', '2025-11-16', 1757.09, 316.28, 2073.37, 9, 44128, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(284, '2025-11-17T06-13 Tax invoice #25387779027575201-25354597864226647.pdf', 'ADS312-105137334', '2025-11-17', 1910.17, 343.83, 2254.00, 10, 53774, '2026-03-30 23:38:02', '2026-03-30 23:38:02'),
(285, '2025-11-18T06-13 Tax invoice #25207692332250538-25397123319974105.pdf', 'ADS312-105141064', '2025-11-18', 418.24, 75.28, 493.52, 10, 11064, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(286, '2025-11-19T18-01 Tax invoice #25334172349602537-25157970267222739.pdf', 'ADS312-105152470', '2025-11-19', 4200.00, 756.00, 4956.00, 10, 121705, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(287, '2025-11-22T17-22 Tax invoice #25249344268085344-25255051620847937.pdf', 'ADS312-105163269', '2025-11-22', 4200.00, 756.00, 4956.00, 14, 205775, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(288, '2025-11-23T06-02 Tax invoice #25190916663928099-25299790799707350.pdf', 'ADS312-105159946', '2025-11-23', 911.97, 164.15, 1076.12, 12, 52710, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(289, '2025-11-24T05-35 Tax invoice #25326480040371764-25326480073705094.pdf', 'ADS312-105163315', '2025-11-24', 0.16, 0.03, 0.19, 1, 86, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(290, '2025-11-24T06-40 Tax invoice #25419148824438217-25375836518769453.pdf', 'ADS312-105163552', '2025-11-24', 1337.83, 240.81, 1578.64, 13, 75024, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(291, '2025-11-25T06-51 Tax invoice #25440862205600211-25316696521350111.pdf', 'ADS312-105167358', '2025-11-25', 2214.42, 398.60, 2613.02, 13, 124034, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(292, '2025-11-26T06-51 Tax invoice #25216201828066249-25343564435329991.pdf', 'ADS312-105171012', '2025-11-26', 2192.56, 394.66, 2587.22, 12, 124387, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(293, '2025-11-27T06-40 Tax invoice #25351903924496042-25333504793002617.pdf', 'ADS312-105174677', '2025-11-27', 1817.64, 327.18, 2144.82, 11, 107152, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(294, '2025-11-29T23-50 Tax invoice #25499330449753391-25374039585615809.pdf', 'ADS312-105190935', '2025-11-29', 4200.00, 756.00, 4956.00, 11, 121908, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(295, '2025-12-05T10-13 Tax invoice #25543682631984839-25510802841939481.pdf', 'ADS312-105211377', '2025-12-05', 4200.00, 756.00, 4956.00, 13, 162618, '2026-03-30 23:38:03', '2026-03-30 23:38:03'),
(296, '2025-12-09T18-17 Tax invoice #25438074745878954-25438074789212283.pdf', 'ADS312-105228017', '2025-12-09', 4200.00, 756.00, 4956.00, 12, 280576, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(297, '2025-12-13T14-29 Tax invoice #25583677634652001-25363278580025239.pdf', 'ADS312-105242745', '2025-12-13', 4200.00, 756.00, 4956.00, 15, 261786, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(298, '2025-12-16T06-18 Tax invoice #25449411521411950-25639300155756419.pdf', 'ADS312-105253361', '2025-12-16', 2818.45, 507.32, 3325.77, 8, 130629, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(299, '2025-12-17T06-18 Tax invoice #25464607873225643-25572235569129546.pdf', 'ADS312-105251529', '2025-12-17', 1327.34, 238.92, 1566.26, 7, 40732, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(300, '2025-12-18T06-18 Tax invoice #25404222502597513-25467668376252931.pdf', 'ADS312-105255264', '2025-12-18', 1210.89, 217.96, 1428.85, 6, 38551, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(301, '2025-12-19T06-08 Tax invoice #25522626267423801-25666360066383761.pdf', 'ADS312-105258996', '2025-12-19', 1210.46, 217.88, 1428.34, 8, 47297, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(302, '2025-12-20T06-08 Tax invoice #25675559615463806-25483094601376974.pdf', 'ADS312-105262780', '2025-12-20', 1512.37, 272.23, 1784.60, 9, 69472, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(303, '2025-12-21T06-08 Tax invoice #25500622356290861-25684360117917089.pdf', 'ADS312-105266697', '2025-12-21', 1560.02, 280.80, 1840.82, 9, 54508, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(304, '2025-12-22T06-08 Tax invoice #25439441809075582-25692783857074715.pdf', 'ADS312-105270339', '2025-12-22', 1424.95, 256.49, 1681.44, 8, 46745, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(305, '2025-12-23T06-08 Tax invoice #25447883398231423-25576758235343942.pdf', 'ADS312-105274208', '2025-12-23', 228.55, 41.14, 269.69, 3, 6540, '2026-03-30 23:38:04', '2026-03-30 23:38:04'),
(306, '2025-12-24T11-50 Tax invoice #25457859577233805-25586807491005683.pdf', 'ADS312-105283863', '2025-12-24', 4200.00, 756.00, 4956.00, 12, 120347, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(307, '2025-12-27T17-00 Tax invoice #25552345627785200-25546361698383598.pdf', 'ADS312-105294725', '2025-12-27', 4200.00, 756.00, 4956.00, 11, 106443, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(308, '2025-12-30T08-05 Tax invoice #25723712420648521-25566353569717744.pdf', 'ADS312-105303809', '2025-12-30', 4200.00, 756.00, 4956.00, 5, 133813, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(309, '2026-01-01T20-10 Tax invoice #25593205017032594-25744577911895305.pdf', 'ADS312-105311704', '2026-01-01', 4200.00, 756.00, 4956.00, 4, 142550, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(310, '2026-01-04T19-33 Tax invoice #25724798797206555-25724798837206551.pdf', 'ADS312-105321454', '2026-01-04', 4200.00, 756.00, 4956.00, 4, 169874, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(311, '2026-01-07T19-13 Tax invoice #25794845266868569-25643362252016870.pdf', 'ADS312-105333829', '2026-01-07', 4200.00, 756.00, 4956.00, 4, 147338, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(312, '2026-01-10T12-04 Tax invoice #25726823700337394-25658168713869561.pdf', 'ADS312-105344862', '2026-01-10', 4200.00, 756.00, 4956.00, 5, 306468, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(313, '2026-01-13T13-25 Tax invoice #25844087295277699-25683182351368197.pdf', 'ADS312-105358480', '2026-01-13', 4200.00, 756.00, 4956.00, 6, 541082, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(314, '2026-01-15T22-02 Tax invoice #25881879231498504-25714271991592562.pdf', 'ADS312-105369293', '2026-01-15', 4200.00, 756.00, 4956.00, 5, 646982, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(315, '2026-01-16T06-07 Tax invoice #25903341536018945-25708124475540651.pdf', 'ADS312-105370943', '2026-01-16', 217.87, 39.22, 257.09, 5, 29920, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(316, '2026-01-17T18-20 Tax invoice #25721934667492965-25731298553223239.pdf', 'ADS312-105370966', '2026-01-17', 1611.83, 290.13, 1901.96, 5, 229196, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(317, '2026-01-18T06-09 Tax invoice #25667348722951555-25903573595995734.pdf', 'ADS312-105372793', '2026-01-18', 1608.60, 289.55, 1898.15, 5, 168724, '2026-03-30 23:38:05', '2026-03-30 23:38:05'),
(318, '2026-01-19T06-50 Tax invoice #25912783918408035-25735510746135357.pdf', 'ADS312-105377358', '2026-01-19', 1583.95, 285.11, 1869.06, 4, 163395, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(319, '2026-01-20T07-06 Tax invoice #25922204370799323-25861405526879214.pdf', 'ADS312-105381693', '2026-01-20', 1567.96, 282.23, 1850.19, 4, 162655, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(320, '2026-01-21T06-50 Tax invoice #25754339167585848-25757898753896557.pdf', 'ADS312-105385929', '2026-01-21', 1586.09, 285.50, 1871.59, 5, 201337, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(321, '2026-01-22T07-06 Tax invoice #25960986463587785-25926908536995574.pdf', 'ADS312-105390424', '2026-01-22', 516.14, 92.91, 609.05, 5, 10174, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(322, '2026-01-23T22-53 Tax invoice #25943435828676178-25723126687373758.pdf', 'ADS312-105403667', '2026-01-23', 4200.00, 756.00, 4956.00, 6, 445461, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(323, '2026-01-25T06-10 Tax invoice #25846958288323929-25956729757346785.pdf', 'ADS312-105402856', '2026-01-25', 1940.37, 349.27, 2289.64, 6, 220211, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(324, '2026-01-25T11-05 Tax invoice #25867023699650726-25958522003834227.pdf', 'ADS312-105403728', '2026-01-25', 4.23, 0.76, 4.99, 5, 406, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(325, '2026-01-26T06-10 Tax invoice #25808454022174363-25982617601424666.pdf', 'ADS312-105406843', '2026-01-26', 1809.06, 325.63, 2134.69, 6, 249691, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(326, '2026-01-27T06-10 Tax invoice #25814728564880241-25818472887839143.pdf', 'ADS312-105411221', '2026-01-27', 1922.01, 345.96, 2267.97, 6, 262994, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(327, '2026-01-28T06-42 Tax invoice #25895375150148914-25834655702887523.pdf', 'ADS312-105416040', '2026-01-28', 1461.96, 263.15, 1725.11, 6, 125247, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(328, '2026-01-29T06-42 Tax invoice #25844397375246689-25905212365831859.pdf', 'ADS312-105420570', '2026-01-29', 1336.95, 240.65, 1577.60, 4, 114080, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(329, '2026-01-31T23-11 Tax invoice #26023618267324600-25865324239820674.pdf', 'ADS312-105439040', '2026-01-31', 4200.00, 756.00, 4956.00, 4, 348034, '2026-03-30 23:38:06', '2026-03-30 23:38:06'),
(330, '2026-03-30T12-06 Tax invoice #26614143368272084-26614143404938747.pdf', 'Document', '2026-03-30', 4202.63, 756.47, 4959.10, 5, 609586, '2026-03-30 23:38:07', '2026-03-30 23:38:07'),
(331, '2026-03-27T16-56 Tax invoice #26416819974671094-26416820018004423.pdf', 'ADS312-105692420', '2026-03-27', 4200.00, 756.00, 4956.00, 6, 607332, '2026-03-30 23:38:07', '2026-03-30 23:38:07'),
(332, '2026-02-15T05-52 Tax invoice #26165409849812107-26002276302792132.pdf', 'ADS312-105498183', '2026-02-15', 0.62, 0.11, 0.73, 2, 246, '2026-03-30 23:38:07', '2026-03-30 23:38:07');

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

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(151, 'default', '{\"uuid\":\"9583700c-f668-48b9-9abd-392d81fb172e\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:11;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(152, 'default', '{\"uuid\":\"786c0678-bac9-4493-b05d-bbc1313198a8\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:12;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(153, 'default', '{\"uuid\":\"a42e4ae0-0f4c-4427-b3d9-51d274a0186d\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:13;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(154, 'default', '{\"uuid\":\"324c0617-cf01-47cf-94db-4302c7a4b2a5\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:14;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(155, 'default', '{\"uuid\":\"9e06a903-2d61-445d-97d6-4fd7ca1bf806\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:15;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(156, 'default', '{\"uuid\":\"f2d08538-e904-4ca3-8d6d-b17f95f9d5ae\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:16;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(157, 'default', '{\"uuid\":\"47d917b3-8b69-43eb-be8f-f487ebafac12\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:17;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(158, 'default', '{\"uuid\":\"cf141f77-05e5-4b0c-9bf0-eeb0338d3b4d\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:18;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(159, 'default', '{\"uuid\":\"4dc925c5-72e7-4646-a9f8-a866c82eb0ba\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:19;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(160, 'default', '{\"uuid\":\"a9cd9028-6c69-4d1c-bb4c-28b9a7634d6c\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:20;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(161, 'default', '{\"uuid\":\"9e800668-7f88-460b-85cc-c2799ae0b02f\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:21;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(162, 'default', '{\"uuid\":\"b2ed48fc-e572-4f88-81d0-7dac0d591804\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:22;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(163, 'default', '{\"uuid\":\"0aa6a486-c655-4704-9d09-df4dd6539b9e\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:23;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(164, 'default', '{\"uuid\":\"a17a74f5-f370-401b-a06f-e47d553f0692\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:24;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(165, 'default', '{\"uuid\":\"616d2db3-cdde-40b7-8144-b5954aa5f986\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:25;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(166, 'default', '{\"uuid\":\"69fde886-7df0-4dc5-9e5c-358346bf6309\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:26;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(167, 'default', '{\"uuid\":\"71d0293a-14b8-46bf-965f-ef8ad6de2576\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:27;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(168, 'default', '{\"uuid\":\"76cec7fa-8631-4ab3-ac60-ca6b33739f80\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:28;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(169, 'default', '{\"uuid\":\"893ea6b5-3016-4aa3-a40d-36d512df83ba\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:29;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(170, 'default', '{\"uuid\":\"205c4c00-d777-4cdd-a883-dc0cfd7263df\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:30;}\",\"batchId\":null},\"createdAt\":1775016774,\"delay\":null}', 0, NULL, 1775016774, 1775016774),
(171, 'default', '{\"uuid\":\"6ab379bb-e54d-4421-9502-ffc7da1b0c01\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:31;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(172, 'default', '{\"uuid\":\"ee6c2a01-a16c-4c34-8852-4515327f7304\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:32;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(173, 'default', '{\"uuid\":\"85e0e017-46c1-4f33-a17e-3dc02628f473\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:33;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(174, 'default', '{\"uuid\":\"16c537ad-a5ef-4cc7-8322-2525e66c4195\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:34;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(175, 'default', '{\"uuid\":\"d85ea9ba-157d-4713-8ee2-935ca3b89908\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:35;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(176, 'default', '{\"uuid\":\"0b9e09d5-8cf9-42cb-a8e0-549faa5030f8\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:36;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(177, 'default', '{\"uuid\":\"8a7b7ed3-a855-40a1-9ae9-2a9118315e12\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:37;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(178, 'default', '{\"uuid\":\"9b2d0505-73c0-4d2d-b025-bd0bb22dc19b\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:38;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(179, 'default', '{\"uuid\":\"c1a02d30-f0f2-4ac4-be7e-025ef5948592\",\"displayName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\",\"command\":\"O:35:\\\"App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\\":1:{s:46:\\\"\\u0000App\\\\Jobs\\\\ProcessHostingerInvoicePdf\\u0000pendingId\\\";i:39;}\",\"batchId\":null},\"createdAt\":1775016796,\"delay\":null}', 0, NULL, 1775016796, 1775016796),
(180, 'default', '{\"uuid\":\"38602f26-2996-44ec-82d5-940e655fa7e5\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775020648,\"delay\":null}', 0, NULL, 1775020648, 1775020648),
(181, 'default', '{\"uuid\":\"fbcdd851-5e5d-4caf-8277-ae9f35cc7d61\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775020696,\"delay\":null}', 0, NULL, 1775020696, 1775020696),
(182, 'default', '{\"uuid\":\"7b5849f4-8eeb-4843-8e4c-5bf603bbd5a9\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:2;}\",\"batchId\":null},\"createdAt\":1775021276,\"delay\":null}', 0, NULL, 1775021276, 1775021276),
(183, 'default', '{\"uuid\":\"2873b5c1-52d8-4913-b009-4230350fff55\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775027441,\"delay\":null}', 0, NULL, 1775027441, 1775027441),
(184, 'default', '{\"uuid\":\"19872173-c3d3-4076-a506-8ec1fe7b32c6\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775038047,\"delay\":null}', 0, NULL, 1775038047, 1775038047),
(185, 'default', '{\"uuid\":\"f7df1775-008d-4fd0-816b-0747b1a793ae\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:2;}\",\"batchId\":null},\"createdAt\":1775039040,\"delay\":null}', 0, NULL, 1775039040, 1775039040),
(186, 'default', '{\"uuid\":\"ea54962e-fb17-4cb0-9e8e-4fa8b3599b79\",\"displayName\":\"App\\\\Jobs\\\\ProcessStatementImage\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":2,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"5\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessStatementImage\",\"command\":\"O:30:\\\"App\\\\Jobs\\\\ProcessStatementImage\\\":1:{s:39:\\\"\\u0000App\\\\Jobs\\\\ProcessStatementImage\\u0000imageId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775039701,\"delay\":null}', 0, NULL, 1775039701, 1775039701),
(187, 'default', '{\"uuid\":\"c984154e-0f56-4564-9240-48c7fbb349ea\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775040121,\"delay\":null}', 0, NULL, 1775040121, 1775040121),
(188, 'default', '{\"uuid\":\"f0612a86-7625-4eb7-8aeb-1a93022e81d3\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:2;}\",\"batchId\":null},\"createdAt\":1775040480,\"delay\":null}', 0, NULL, 1775040480, 1775040480),
(189, 'default', '{\"uuid\":\"2264f8ae-2ba6-4fcf-88f1-da283d278e71\",\"displayName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessBankStatementPdf\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessBankStatementPdf\\\":1:{s:43:\\\"\\u0000App\\\\Jobs\\\\ProcessBankStatementPdf\\u0000pendingId\\\";i:3;}\",\"batchId\":null},\"createdAt\":1775041637,\"delay\":null}', 0, NULL, 1775041637, 1775041637),
(190, 'default', '{\"uuid\":\"25de08ce-c8e4-4d75-8f63-ba5d9caa1067\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775044034,\"delay\":null}', 0, NULL, 1775044034, 1775044034),
(191, 'default', '{\"uuid\":\"5658a244-206b-4358-ad2b-99a5595dad6b\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:2;}\",\"batchId\":null},\"createdAt\":1775044120,\"delay\":null}', 0, NULL, 1775044120, 1775044120),
(192, 'default', '{\"uuid\":\"43368170-a104-46ce-ade6-6da71262289a\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:3;}\",\"batchId\":null},\"createdAt\":1775044294,\"delay\":null}', 0, NULL, 1775044294, 1775044294),
(193, 'default', '{\"uuid\":\"6e364684-2e59-4678-972a-d7cd97b74617\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:4;}\",\"batchId\":null},\"createdAt\":1775044372,\"delay\":null}', 0, NULL, 1775044372, 1775044372),
(194, 'default', '{\"uuid\":\"b0deb11e-385a-4369-a9bc-0e7a3b16f8b3\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775044451,\"delay\":null}', 0, NULL, 1775044451, 1775044451),
(195, 'default', '{\"uuid\":\"6556c350-7dbd-4daa-8425-9be2d83ffe17\",\"displayName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10\",\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessGodaddyFile\",\"command\":\"O:27:\\\"App\\\\Jobs\\\\ProcessGodaddyFile\\\":1:{s:38:\\\"\\u0000App\\\\Jobs\\\\ProcessGodaddyFile\\u0000pendingId\\\";i:1;}\",\"batchId\":null},\"createdAt\":1775046304,\"delay\":null}', 0, NULL, 1775046304, 1775046304);

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
(7, '2024_01_01_000001_create_invoice_records_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `outsource_receipts`
--

CREATE TABLE `outsource_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `subscription` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `interval` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outsource_receipts`
--

INSERT INTO `outsource_receipts` (`id`, `client_name`, `invoice_number`, `invoice_date`, `subscription`, `description`, `interval`, `subtotal`, `gst_amount`, `grand_total`, `pdf_filename`, `created_at`, `updated_at`) VALUES
(1, 'Kitoko Foods', '4945564632', '2024-03-01', 'Google Workspace', 'Usage', '1 Mar 2024 - 31 Mar 2024', 370.62, 66.71, 437.33, '4945564632.pdf', '2026-03-31 05:29:41', '2026-03-31 05:29:41'),
(2, 'Kitoko Foods', '4983510577', '2024-05-01', 'Google Workspace', 'Usage', '1 May 2024 - 31 May 2024', 420.00, 75.60, 495.60, '4983510577.pdf', '2026-03-31 05:30:54', '2026-03-31 05:30:54'),
(3, 'Kitoko Foods', '4962754097', '2024-04-01', 'Google Workspace', 'Usage', '1 Apr 2024 - 30 Apr 2024', 420.00, 75.60, 495.60, '4962754097.pdf', '2026-03-31 05:30:54', '2026-03-31 05:30:54'),
(4, 'Kitoko Foods', '4945564632', '2024-03-01', 'Google Workspace', 'Usage', '1 Mar 2024 - 31 Mar 2024', 370.62, 66.71, 437.33, '4945564632.pdf', '2026-03-31 05:30:54', '2026-03-31 05:30:54'),
(5, 'Kitoko Foods', '4919720207', '2024-02-01', 'Google Workspace', 'Usage', '1 Feb 2024 - 29 Feb 2024', 249.90, 44.98, 294.88, '4919720207.pdf', '2026-03-31 05:30:54', '2026-03-31 05:30:54'),
(6, 'Kitoko Foods', '4901335423', '2024-01-01', 'Google Workspace', 'Usage', '1 Jan 2024 - 31 Jan 2024', 249.90, 44.98, 294.88, '4901335423.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(7, 'Kitoko Foods', '4882703329', '2023-12-01', 'Google Workspace', 'Usage', '1 Dec 2023 - 31 Dec 2023', 249.90, 44.98, 294.88, '4882703329.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(8, 'Kitoko Foods', '4857260805', '2023-11-01', 'Google Workspace', 'Usage', '1 Nov 2023 - 30 Nov 2023', 249.90, 44.98, 294.88, '4857260805.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(9, 'Kitoko Foods', '4838506528', '2023-10-01', 'Google Workspace', 'Usage', '1 Oct 2023 - 31 Oct 2023', 249.90, 44.98, 294.88, '4838506528.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(10, 'Kitoko Foods', '4818243220', NULL, 'Google Workspace', 'Usage', '1 Sept 2023 - 30 Sept 2023', 249.90, 44.98, 294.88, '4818243220.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(11, 'Kitoko Foods', '4798509453', '2023-08-01', 'Google Workspace', 'Usage', '1 Aug 2023 - 31 Aug 2023', 249.90, 44.98, 294.88, '4798509453.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(12, 'Kitoko Foods', '4773224953', '2023-07-01', 'Google Workspace', 'Usage', '1 Jul 2023 - 31 Jul 2023', 249.90, 44.98, 294.88, '4773224953.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(13, 'Kitoko Foods', '4755949829', '2023-06-01', 'Google Workspace', 'Usage', '1 Jun 2023 - 30 Jun 2023', 249.90, 44.98, 294.88, '4755949829.pdf', '2026-03-31 05:30:55', '2026-03-31 05:30:55'),
(14, 'Kitoko Foods', '4732220507', '2023-05-01', 'Google Workspace', 'Usage', '1 May 2023 - 31 May 2023', 249.90, 44.98, 294.88, '4732220507.pdf', '2026-03-31 05:30:56', '2026-03-31 05:30:56'),
(15, 'Kitoko Foods', '4710276711', '2023-04-01', 'Google Workspace', 'Usage', '1 Apr 2023 - 30 Apr 2023', 249.90, 44.98, 294.88, '4710276711.pdf', '2026-03-31 05:30:56', '2026-03-31 05:30:56'),
(16, 'Kitoko Foods', '4693254477', '2023-03-10', 'Google Workspace', 'Usage', '10 Mar 2023 - 31 Mar 2023', 177.34, 31.92, 209.26, '4693254477.pdf', '2026-03-31 05:30:56', '2026-03-31 05:30:56'),
(17, 'Sangani Hospital', '5478266544', '2026-01-01', 'Google Workspace', 'Usage', '1 Jan 2026 - 31 Jan 2026', 2275.00, 409.50, 2684.50, 'Outsource- Recipts-2025-2026_11_.pdf', '2026-03-31 05:37:09', '2026-03-31 05:37:09'),
(18, 'Sangani Hospital', '5499311159', '2026-02-01', 'Google Workspace', 'Usage', '1 Feb 2026 - 28 Feb 2026', 2275.00, 409.50, 2684.50, '5499311159.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(19, 'Sangani Hospital', '5368632655', NULL, 'Google Workspace', 'Usage', '1 Sept 2025 - 30 Sept 2025', 2816.67, 507.00, 3323.67, '5368632655.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(20, 'Sangani Hospital', '5349353560', '2025-08-01', 'Google Workspace', 'Usage', '1 Aug 2025 - 31 Aug 2025', 2925.00, 526.50, 3451.50, '5349353560.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(21, 'Sangani Hospital', '5321643876', '2025-07-01', 'Google Workspace', 'Usage', '1 Jul 2025 - 31 Jul 2025', 2811.19, 506.01, 3317.20, '5321643876.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(22, 'Sangani Hospital', '5295908536', '2025-06-01', 'Google Workspace', 'Usage', '1 Jun 2025 - 30 Jun 2025', 2484.00, 447.12, 2931.12, '5295908536.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(23, 'Sangani Hospital', '5266326261', '2025-05-01', 'Google Workspace', 'Usage', '1 May 2025 - 31 May 2025', 2484.00, 447.12, 2931.12, '5266326261.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(24, 'Sangani Hospital', '5238004214', '2025-04-01', 'Google Workspace', 'Usage', '1 Apr 2025 - 30 Apr 2025', 2484.00, 447.12, 2931.12, '5238004214.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(25, 'Sangani Hospital', '5214677634', '2025-03-01', 'Google Workspace', 'Usage', '1 Mar 2025 - 31 Mar 2025', 2484.00, 447.12, 2931.12, '5214677634.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(26, 'Sangani Hospital', '5193038389', '2025-02-01', 'Google Workspace', 'Usage', '1 Feb 2025 - 28 Feb 2025', 2484.00, 447.12, 2931.12, '5193038389.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(27, 'Sangani Hospital', '5166216785', '2025-01-01', 'Google Workspace', 'Usage', '1 Jan 2025 - 31 Jan 2025', 2484.00, 447.12, 2931.12, '5166216785.pdf', '2026-03-31 05:37:10', '2026-03-31 05:37:10'),
(28, 'Sangani Hospital', '5147432689', '2024-12-01', 'Google Workspace', 'Usage', '1 Dec 2024 - 31 Dec 2024', 2484.00, 447.12, 2931.12, '5147432689.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(29, 'Sangani Hospital', '5123546479', '2024-11-01', 'Google Workspace', 'Usage', '1 Nov 2024 - 30 Nov 2024', 2484.00, 447.12, 2931.12, '5123546479.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(30, 'Sangani Hospital', '5093487954', '2024-10-01', 'Google Workspace', 'Usage', '1 Oct 2024 - 31 Oct 2024', 2484.00, 447.12, 2931.12, '5093487954.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(31, 'Sangani Hospital', '5071042835', NULL, 'Google Workspace', 'Usage', '1 Sept 2024 - 30 Sept 2024', 2484.00, 447.12, 2931.12, '5071042835.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(32, 'Sangani Hospital', '5051573821', '2024-08-01', 'Google Workspace', 'Usage', '1 Aug 2024 - 31 Aug 2024', 1442.32, 259.62, 1701.94, '5051573821.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(33, 'Sangani Hospital', '5027512205', '2024-07-01', 'Google Workspace', 'Usage', '1 Jul 2024 - 31 Jul 2024', 2243.62, 403.85, 2647.47, '5027512205.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(34, 'Sangani Hospital', '5009483088', '2024-06-01', 'Google Workspace', 'Usage', '1 Jun 2024 - 30 Jun 2024', 2484.00, 447.12, 2931.12, '5009483088.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(35, 'Sangani Hospital', '4985540897', '2024-05-01', 'Google Workspace', 'Usage', '1 May 2024 - 31 May 2024', 2484.00, 447.12, 2931.12, '4985540897.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(36, 'Sangani Hospital', '4962641939', '2024-04-01', 'Google Workspace', 'Usage', '1 Apr 2024 - 30 Apr 2024', 2484.00, 447.12, 2931.12, '4962641939.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(37, 'Sangani Hospital', '4943306167', '2024-03-01', 'Google Workspace', 'Usage', '1 Mar 2024 - 31 Mar 2024', 2484.00, 447.12, 2931.12, '4943306167.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(38, 'Sangani Hospital', '4919857971', '2024-02-01', 'Google Workspace', 'Usage', '1 Feb 2024 - 29 Feb 2024', 2156.28, 388.13, 2544.41, '4919857971.pdf', '2026-03-31 05:37:11', '2026-03-31 05:37:11'),
(39, 'Sangani Hospital', '4902777118', '2024-01-01', 'Google Workspace', 'Usage', '1 Jan 2024 - 31 Jan 2024', 1890.00, 340.20, 2230.20, '4902777118.pdf', '2026-03-31 05:37:12', '2026-03-31 05:37:12'),
(40, 'Sangani Hospital', '4880883645', '2023-12-01', 'Google Workspace', 'Usage', '1 Dec 2023 - 31 Dec 2023', 1890.00, 340.20, 2230.20, '4880883645.pdf', '2026-03-31 05:37:12', '2026-03-31 05:37:12'),
(41, 'Sangani Hospital', '4861678182', '2023-11-01', 'Google Workspace', 'Usage', '1 Nov 2023 - 30 Nov 2023', 1827.00, 328.86, 2155.86, '4861678182.pdf', '2026-03-31 05:37:12', '2026-03-31 05:37:12'),
(42, 'Sangani Hospital', '4841440076', '2023-10-01', 'Google Workspace', 'Usage', '1 Oct 2023 - 31 Oct 2023', 1890.00, 340.20, 2230.20, '4841440076.pdf', '2026-03-31 05:37:12', '2026-03-31 05:37:12'),
(43, 'Sangani Hospital', '4815927355', NULL, 'Google Workspace', 'Usage', '1 Sept 2023 - 30 Sept 2023', 1701.00, 306.18, 2007.18, '4815927355.pdf', '2026-03-31 05:37:12', '2026-03-31 05:37:12'),
(44, 'Vervax', '5369010351', NULL, 'Google Workspace', 'Usage', '1 Sept 2025 - 30 Sept 2025', 1050.00, 189.00, 1239.00, '5369010351.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(45, 'Vervax', '5348784527', '2025-08-01', 'Google Workspace', 'Usage', '1 Aug 2025 - 31 Aug 2025', 1050.00, 189.00, 1239.00, '5348784527.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(46, 'Vervax', '5318076743', '2025-07-01', 'Google Workspace', 'Usage', '1 Jul 2025 - 31 Jul 2025', 1050.00, 189.00, 1239.00, '5318076743.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(47, 'Vervax', '5293342903', '2025-06-01', 'Google Workspace', 'Usage', '1 Jun 2025 - 30 Jun 2025', 1050.00, 189.00, 1239.00, '5293342903.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(48, 'Vervax', '5270040992', '2025-05-01', 'Google Workspace', 'Usage', '1 May 2025 - 31 May 2025', 1050.00, 189.00, 1239.00, '5270040992.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(49, 'Vervax', '5237884022', '2025-04-01', 'Google Workspace', 'Usage', '1 Apr 2025 - 30 Apr 2025', 1050.00, 189.00, 1239.00, '5237884022.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(50, 'Vervax', '5215477122', '2025-03-01', 'Google Workspace', 'Usage', '1 Mar 2025 - 31 Mar 2025', 1050.00, 189.00, 1239.00, '5215477122.pdf', '2026-03-31 05:44:08', '2026-03-31 05:44:08'),
(51, 'Vervax', '5192961139', '2025-02-01', 'Google Workspace', 'Usage', '1 Feb 2025 - 28 Feb 2025', 1050.00, 189.00, 1239.00, '5192961139.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(52, 'Vervax', '5165030589', '2025-01-01', 'Google Workspace', 'Usage', '1 Jan 2025 - 31 Jan 2025', 1050.00, 189.00, 1239.00, '5165030589.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(53, 'Vervax', '5140751871', '2024-12-01', 'Google Workspace', 'Usage', '1 Dec 2024 - 31 Dec 2024', 1050.00, 189.00, 1239.00, '5140751871.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(54, 'Vervax', '5123493392', '2024-11-01', 'Google Workspace', 'Usage', '1 Nov 2024 - 30 Nov 2024', 1050.00, 189.00, 1239.00, '5123493392.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(55, 'Vervax', '5099105900', '2024-10-01', 'Google Workspace', 'Usage', '1 Oct 2024 - 31 Oct 2024', 1050.00, 189.00, 1239.00, '5099105900.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(56, 'Vervax', '5072354243', NULL, 'Google Workspace', 'Usage', '1 Sept 2024 - 30 Sept 2024', 1050.00, 189.00, 1239.00, '5072354243.pdf', '2026-03-31 05:44:09', '2026-03-31 05:44:09'),
(57, 'Vervax', '5052792442', '2024-08-01', 'Google Workspace', 'Usage', '1 Aug 2024 - 31 Aug 2024', 1050.00, 189.00, 1239.00, '5052792442.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(58, 'Vervax', '5027550776', '2024-07-01', 'Google Workspace', 'Usage', '1 Jul 2024 - 31 Jul 2024', 1050.00, 189.00, 1239.00, '5027550776.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(59, 'Vervax', '5005490520', '2024-06-01', 'Google Workspace', 'Usage', '1 Jun 2024 - 30 Jun 2024', 1050.00, 189.00, 1239.00, '5005490520.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(60, 'Vervax', '4990891270', '2024-05-01', 'Google Workspace', 'Usage', '1 May 2024 - 31 May 2024', 1050.00, 189.00, 1239.00, '4990891270.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(61, 'Vervax', '4962860392', '2024-04-01', 'Google Workspace', 'Usage', '1 Apr 2024 - 30 Apr 2024', 1050.00, 189.00, 1239.00, '4962860392.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(62, 'Vervax', '4946493057', '2024-03-01', 'Google Workspace', 'Usage', '1 Mar 2024 - 31 Mar 2024', 1050.00, 189.00, 1239.00, '4946493057.pdf', '2026-03-31 05:44:10', '2026-03-31 05:44:10'),
(63, 'Vervax', '4924684425', '2024-02-01', 'Google Workspace', 'Usage', '1 Feb 2024 - 29 Feb 2024', 1050.00, 189.00, 1239.00, '4924684425.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(64, 'Vervax', '4900351637', '2024-01-01', 'Google Workspace', 'Usage', '1 Jan 2024 - 31 Jan 2024', 1050.00, 189.00, 1239.00, '4900351637.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(65, 'Vervax', '4882177034', '2023-12-01', 'Google Workspace', 'Usage', '1 Dec 2023 - 31 Dec 2023', 652.18, 117.39, 769.57, '4882177034.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(66, 'Vervax', '4862940036', '2023-11-01', 'Google Workspace', 'Usage', '1 Nov 2023 - 30 Nov 2023', 624.75, 112.46, 737.21, '4862940036.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(67, 'Vervax', '4840337105', '2023-10-01', 'Google Workspace', 'Usage', '1 Oct 2023 - 31 Oct 2023', 624.75, 112.46, 737.21, '4840337105.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(68, 'Vervax', '4815461537', NULL, 'Google Workspace', 'Usage', '1 Sept 2023 - 30 Sept 2023', 624.75, 112.46, 737.21, '4815461537.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(69, 'Vervax', '4798868076', '2023-08-01', 'Google Workspace', 'Usage', '1 Aug 2023 - 31 Aug 2023', 624.75, 112.46, 737.21, '4798868076.pdf', '2026-03-31 05:44:11', '2026-03-31 05:44:11'),
(70, 'Vervax', '4776182967', '2023-07-01', 'Google Workspace', 'Usage', '1 Jul 2023 - 31 Jul 2023', 624.75, 112.46, 737.21, '4776182967.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(71, 'Vervax', '4754349938', '2023-06-01', 'Google Workspace', 'Usage', '1 Jun 2023 - 30 Jun 2023', 624.75, 112.46, 737.21, '4754349938.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(72, 'Vervax', '4731367898', '2023-05-01', 'Google Workspace', 'Usage', '1 May 2023 - 31 May 2023', 620.72, 111.73, 732.45, '4731367898.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(73, 'Vervax', '4713258802', '2023-04-01', 'Google Workspace', 'Usage', '1 Apr 2023 - 30 Apr 2023', 499.80, 89.96, 589.76, '4713258802.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(74, 'Vervax', '4695016610', '2023-03-01', 'Google Workspace', 'Usage', '1 Mar 2023 - 31 Mar 2023', 499.80, 89.96, 589.76, '4695016610.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(75, 'Vervax', '4674453972', '2023-02-01', 'Google Workspace', 'Usage', '1 Feb 2023 - 28 Feb 2023', 499.80, 89.96, 589.76, '4674453972.pdf', '2026-03-31 05:44:12', '2026-03-31 05:44:12'),
(76, 'Vervax', '4654105377', '2023-01-01', 'Google Workspace', 'Usage', '1 Jan 2023 - 31 Jan 2023', 455.46, 81.98, 537.44, '4654105377.pdf', '2026-03-31 05:44:13', '2026-03-31 05:44:13'),
(77, 'Vervax', '4634708491', '2022-12-30', 'Google Workspace', 'Usage', '30 Dec 2022 - 31 Dec 2022', 24.19, 4.35, 28.54, '4634708491.pdf', '2026-03-31 05:44:13', '2026-03-31 05:44:13');

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
-- Table structure for table `pending_godaddy_files`
--

CREATE TABLE `pending_godaddy_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT 'xlsx',
  `status` enum('pending','processing','done','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `records_inserted` int(11) DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_godaddy_files`
--

INSERT INTO `pending_godaddy_files` (`id`, `original_filename`, `stored_path`, `file_type`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 'Book1.xlsx', 'godaddy_files/g8wJsZ1WH3ZAMkNNEgquNYPl7g19MEdE2W0cILtv.xlsx', 'xlsx', 'done', NULL, 48, '2026-04-01 06:55:08', '2026-04-01 06:55:04', '2026-04-01 06:55:08');

-- --------------------------------------------------------

--
-- Table structure for table `pending_invoice_pdfs`
--

CREATE TABLE `pending_invoice_pdfs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `status` enum('pending','processing','done','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `records_inserted` int(11) DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_invoice_pdfs`
--

INSERT INTO `pending_invoice_pdfs` (`id`, `original_filename`, `stored_path`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, '2026-02-04T02-00 Tax invoice #26085672977785799-25960135323672896.pdf', 'invoices/DktbW6k4po4uiZauv7F5DZvefCNL0Q0NWoXt65BX.pdf', 'done', NULL, 6, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(2, '2026-02-06T18-59 Tax invoice #26114216241598139-26080136891672737.pdf', 'invoices/1mJbUz4nVmSEwpHJDpMkupmuN1Vm2g4Dh1vhDIBM.pdf', 'done', NULL, 7, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(3, '2026-02-08T20-04 Tax invoice #26100992902920469-26117603497926075.pdf', 'invoices/WqRKzXThdVlyA1lCl3ZYduz2oy7tCGbWEv05hXBi.pdf', 'done', NULL, 7, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(4, '2026-02-11T00-27 Tax invoice #26012705321749224-26032080216478406.pdf', 'invoices/CwNcJeCPCc7le9bZkzfvTfwnF3DVT1sUy4dgL8W3.pdf', 'done', NULL, 8, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(5, '2026-02-12T18-17 Tax invoice #26049393014747126-26049393071413787.pdf', 'invoices/UkPjAlW4wuIK7GLpWOoPtf8VbeQKcOEWjFNmjEhN.pdf', 'done', NULL, 10, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(7, '2026-02-13T15-32 Tax invoice #26103030086050089-25985958267757269.pdf', 'invoices/ct88mnQAWnbFDR0yL4JhWBpKqTp09lLpstaRqGSF.pdf', 'done', NULL, 10, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(8, '2026-02-13T23-42 Tax invoice #26062052616814499-25994016693618094.pdf', 'invoices/xs0Qm3RjXk1BH0ZJbB7kG16APWuuKqnziPSqa26R.pdf', 'done', NULL, 12, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(9, '2026-02-13T23-46 Tax invoice #25994049600281470-25994049626948134.pdf', 'invoices/D0bmP2YVORHRmhbkUzHkGkw9MD0opUVHGQ5Xj9pj.pdf', 'done', NULL, 9, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(10, '2026-02-14T06-16 Tax invoice #26064782213208206-26064782243208203.pdf', 'invoices/qiJeTEa8MJItRR9fzH7l1twfOtBcbmdCX3OUw2M5.pdf', 'done', NULL, 12, '2026-03-30 23:37:36', '2026-03-30 06:30:51', '2026-03-30 23:37:36'),
(12, '2026-02-15T05-48 Tax invoice #26074670645552696-26002252766127819.pdf', 'invoices/k8S7Va6ZMm20KijwuxmJHME9FyWO3yOsbaCxWfRj.pdf', 'done', NULL, 13, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(14, '2026-02-15T06-16 Tax invoice #26002422079444221-26182220584797699.pdf', 'invoices/2BB5x0JF5imGbozMAPbSfbdYY3Gh7v2Rbe5GQMeS.pdf', 'done', NULL, 6, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(15, '2026-02-16T06-16 Tax invoice #26191886600497764-26012024895150606.pdf', 'invoices/pAiuUsFwdoPYz0D3V7nhLuLyGB8ZYmo19v28imbV.pdf', 'done', NULL, 10, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(16, '2026-02-17T06-16 Tax invoice #26031536379866120-26184668737886218.pdf', 'invoices/H8D0QZX4SEAEuJNBPOLxG8syKmiDrob3yx3BMqBd.pdf', 'done', NULL, 8, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(17, '2026-02-18T06-12 Tax invoice #26083646951321727-26041181965568228.pdf', 'invoices/OhC1evPk3Xi6KBKVQTqjimzgiQ05wJ0vXLg7lHyE.pdf', 'done', NULL, 8, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(18, '2026-02-19T06-12 Tax invoice #26093601633659592-26204294305923661.pdf', 'invoices/gyyHuaTPxnC1lzxCOKzBdfW26mVHEwCVWPn59CfR.pdf', 'done', NULL, 9, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(19, '2026-02-20T06-12 Tax invoice #26123546873998406-26214116174941474.pdf', 'invoices/M2FoccKIP0jXVYKZbuvV87WMnvrmmp4oY5y3kNo8.pdf', 'done', NULL, 7, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(20, '2026-02-21T06-15 Tax invoice #26258444153842013-26258444200508675.pdf', 'invoices/ZjpRt8ZrXcUYNvOg6kmEN0E40ECrFFYsrgwzFUWr.pdf', 'done', NULL, 6, '2026-03-30 23:37:37', '2026-03-30 06:30:51', '2026-03-30 23:37:37'),
(21, '2026-02-21T13-49 Tax invoice #26063645566655205-26180994858253611.pdf', 'invoices/drjn0Hi3TA984gf62asm2WsjkRSv7LRcP26yX45i.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(22, '2026-02-22T14-36 Tax invoice #26146959311657162-26016064351413322.pdf', 'invoices/zRVYxWJgVEq4DMeHxqrNoWzbMlJakrqkx5Wk7FAU.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(23, '2026-02-23T14-51 Tax invoice #26093656293654128-26087907857562310.pdf', 'invoices/JYF8V3N9MfrmIrtXelPAbO6WUwWecTYSh9MbeOEo.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(24, '2026-02-24T17-07 Tax invoice #26257871250565966-26292159970470431.pdf', 'invoices/2QlrzhfnRFKGKrPVFprnDJoF0zQHWFY9zNa6fFyp.pdf', 'done', NULL, 7, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(25, '2026-02-25T20-13 Tax invoice #26303764112643350-26116160161403741.pdf', 'invoices/MXMUXXIDWe1iNmDhQiBGYy8xJVX1oAmpkr8JGZnO.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(26, '2026-02-27T12-27 Tax invoice #26239900082363088-26064834546536302.pdf', 'invoices/dAOwMK8wkOQFVj5pMoBTvXHTALj71LdAqVxq2Smb.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:11', '2026-03-30 23:37:38'),
(27, '2026-02-28T23-24 Tax invoice #26190363833983371-26335540666132361.pdf', 'invoices/F5Xj2ZCkNXZWDXlw2gEy8ScKIAanXgMrEM11l0Zj.pdf', 'done', NULL, 8, '2026-03-30 23:37:38', '2026-03-30 06:31:12', '2026-03-30 23:37:38'),
(28, '2026-03-03T00-16 Tax invoice #26167566876263069-26099474579738965.pdf', 'invoices/158AbWVvRm82CV85DjjnZhtF3MARDsI7YVdmXTQi.pdf', 'done', NULL, 7, '2026-03-30 23:37:38', '2026-03-30 06:31:12', '2026-03-30 23:37:38'),
(33, '2026-03-10T20-06 Tax invoice #26398889039797519-26239936022359492.pdf', 'invoices/WropO0xc7oHGHZYBtZpkB6TWyyZRLi0SY8Eiwptf.pdf', 'done', NULL, 11, '2026-03-30 23:37:38', '2026-03-30 06:31:12', '2026-03-30 23:37:38'),
(34, '2026-03-11T20-12 Tax invoice #26244263211926772-26318984511121307.pdf', 'invoices/eQtDm4WNXeic7lRHmeEhohq8nMeDdWGg4qLh3t3y.pdf', 'done', NULL, 9, '2026-03-30 23:37:38', '2026-03-30 06:31:12', '2026-03-30 23:37:38'),
(35, '2026-03-13T08-43 Tax invoice #26377803535239408-26334729812880110.pdf', 'invoices/PLoGDzB0rTHLwDbq3r4Hb23yhPYY9x1Kgc1wk6H4.pdf', 'done', NULL, 10, '2026-03-30 23:37:38', '2026-03-30 06:31:12', '2026-03-30 23:37:38'),
(38, '2026-03-13T13-53 Tax invoice #26461265653559861-26205099049176517.pdf', 'invoices/cHtXwoJvquguwE1tAykXzPrbD1MFD0jWRBrsV3LK.pdf', 'done', NULL, 5, '2026-03-30 23:37:39', '2026-03-30 06:31:12', '2026-03-30 23:37:39'),
(39, '2026-03-14T07-18 Tax invoice #26269652402721186-26452427577776997.pdf', 'invoices/5FcMtI4oINDCYc0dVUZ2NbVOh60vcVVzv3lWvYAc.pdf', 'done', NULL, 11, '2026-03-30 23:37:39', '2026-03-30 06:31:12', '2026-03-30 23:37:39'),
(41, '2026-03-14T23-18 Tax invoice #26394279770258451-26441183805568042.pdf', 'invoices/mTBAwGgLWPOH9bYCjrdXp9kK2H3JUiMfAP7BIGtf.pdf', 'done', NULL, 9, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(43, '2026-03-15T06-13 Tax invoice #26461696223516799-26278813105138449.pdf', 'invoices/dDGX0b2GrdbUK4F5MiCDP4BZgZ3gPAwcYxCrE276.pdf', 'done', NULL, 9, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(44, '2026-03-16T06-30 Tax invoice #26488315694188190-26294505873569173.pdf', 'invoices/nTvef139QnTli4VgilYR7BzZnmRdWzrLwImoqaMJ.pdf', 'done', NULL, 8, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(45, '2026-03-17T09-36 Tax invoice #26375655882120836-26483826937970394.pdf', 'invoices/15G0mGL38mStZThtu2xeQHPb8bVp7m92PeWaIHb2.pdf', 'done', NULL, 9, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(46, '2026-03-19T07-44 Tax invoice #26504177965935291-26485959317757157.pdf', 'invoices/cBfrrlLPW4yRHDHbe1con82in5qezysCPrXS809S.pdf', 'done', NULL, 10, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(47, '2026-03-20T16-36 Tax invoice #26342560202097073-26336502499369509.pdf', 'invoices/llQSE2gd4UdTGCpNIHklZ6uxGiN1RRxC3iqzI2IH.pdf', 'done', NULL, 7, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(50, '2026-03-21T06-32 Tax invoice #26349896568030103-26418948937791530 (1).pdf', 'invoices/R3TIQeeb0DypNw8iekjx6bpwHc8xchsC19EAOthK.pdf', 'done', NULL, 6, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(51, '2026-03-22T06-36 Tax invoice #26520630017623420-26473417532344674.pdf', 'invoices/dEakiZjdwnhrYNybisZm54Ztqg01LtgvRtufdOKA.pdf', 'done', NULL, 7, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(52, '2026-03-23T06-36 Tax invoice #26550148621338225-26566629313023494.pdf', 'invoices/NDpe2mS3rd97bpkCi2F5i3pHbmdscrF1eJGaeTsU.pdf', 'done', NULL, 6, '2026-03-30 23:37:39', '2026-03-30 06:31:27', '2026-03-30 23:37:39'),
(53, '2026-03-24T06-36 Tax invoice #26320894427596978-26388496344170120.pdf', 'invoices/oqFlLtNYrROkGsEBXGdO6VnTxJwYcKuT1Y8r8zuR.pdf', 'done', NULL, 6, '2026-03-30 23:37:40', '2026-03-30 06:31:27', '2026-03-30 23:37:40'),
(54, '2026-03-25T06-36 Tax invoice #26332321746454246-26464041136615643.pdf', 'invoices/UrxxhwpuFmw0L9ebQUFZBbnfI8gQ1A0PKbTrBIbZ.pdf', 'done', NULL, 6, '2026-03-30 23:37:40', '2026-03-30 06:31:27', '2026-03-30 23:37:40'),
(55, '2025-04-02T14-39 Tax invoice #9475873755858982-9470645536381803.pdf', 'invoices/GLMmJJqvr16y5XNnut1HTzRiKZapDSEFUW916Zpm.pdf', 'done', NULL, 5, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(56, '2025-04-16T06-45 Tax invoice #9511078135671871-9629449357168085.pdf', 'invoices/RxyDt1fiG8mUxeqQxduF4qPw2TqxCOs8kIuiFn23.pdf', 'done', NULL, 4, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(57, '2025-04-22T19-56 Tax invoice #9608591885920500-9799905413455814.pdf', 'invoices/sj9cDpCyZVhJW2ID0DkZuLhtGq5pHuvajJhrbG7e.pdf', 'done', NULL, 8, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(58, '2025-04-23T19-05 Tax invoice #9782689865177364-9766827626763589.pdf', 'invoices/qesRosK6VbtzLM6X8fhNbQ3VDFTcnpElqvRiHtsv.pdf', 'done', NULL, 8, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(59, '2025-04-24T00-08 Tax invoice #9658201177626231-9658201197626229.pdf', 'invoices/Ll9thN7Yh96feNfbulsDaAC9wrauMOnnRshKWJRK.pdf', 'done', NULL, 8, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(60, '2025-04-24T06-54 Tax invoice #9741531869293170-9683500871762933.pdf', 'invoices/G947LkkMvWiJFC9xMsW1yRnuHnzS8FldDo8wXxwx.pdf', 'done', NULL, 8, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(61, '2025-04-24T08-09 Tax invoice #9660603924052623-9624810367631986.pdf', 'invoices/h4XkQEeP9xFZOKCgYuaLFDt9ftHMb7QmpPePrExc.pdf', 'done', NULL, 1, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(62, '2025-04-24T10-17 Tax invoice #9566433756802975-9684535684992785.pdf', 'invoices/GVW9LQmqF26aFGkzGQ1Tool7ZlVf6cHZcWrM4TO9.pdf', 'done', NULL, 1, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(63, '2025-04-24T10-17 Tax invoice #9617390171707334-9566433176803033.pdf', 'invoices/5uGaIdm1ZcioWFNF0VCckjEDIrZrDUdX0ELno4eu.pdf', 'done', NULL, 8, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(64, '2025-04-24T10-17 Tax invoice #9811678848945137-9742469792532711.pdf', 'invoices/SIZTFb6CbVw8mZLRMYi0AQ3dLlwsuoiwpAprfUcS.pdf', 'done', NULL, 1, '2026-03-30 23:37:40', '2026-03-30 06:32:41', '2026-03-30 23:37:40'),
(65, '2025-04-24T17-26 Tax invoice #9663498643763151-9686646224781731.pdf', 'invoices/VWEFGuBljUfKnBBqQEbIlbbkv5uye5h8wSasLH8c.pdf', 'done', NULL, 1, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(66, '2025-04-24T17-26 Tax invoice #9744613732318317-9773678602745158.pdf', 'invoices/5ObFCMIaJ3K8yMf8i0DvIGughGZF95VrxuaKYvp8.pdf', 'done', NULL, 8, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(67, '2025-04-24T17-37 Tax invoice #9773735376072814-9773735392739479.pdf', 'invoices/nkKJrXIQjlGaWfujHWl5omdnX74yVEl74FKQefW0.pdf', 'done', NULL, 8, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(68, '2025-04-25T06-07 Tax invoice #9631320043647685-9793212824125068.pdf', 'invoices/G79TmjGJhBptwQWXksCHDdRrU1ywqOdvYAETYANl.pdf', 'done', NULL, 8, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(69, '2025-04-25T22-39 Tax invoice #9628644957248522-9753545314758492.pdf', 'invoices/i9PXu750Ht8lzYlfdbcI5MePKmVCAhCxbjc7d0Ew.pdf', 'done', NULL, 8, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(70, '2025-04-25T22-39 Tax invoice #9672775349502147-9577596409020043.pdf', 'invoices/1999cJDADmTG3fd9SRRzR7asz3UUwlkrLnrourr9.pdf', 'done', NULL, 1, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(71, '2025-04-26T20-52 Tax invoice #9830222633757425-9760701240709566.pdf', 'invoices/ZeQvxksCS3bYPPgw0NuT4OUrG2qCGw78moPovH6P.pdf', 'done', NULL, 9, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(72, '2025-04-27T06-07 Tax invoice #9641222712657417-9587495474696803.pdf', 'invoices/XGCMB18HKAuESSndAZhaql8EQDnHfEeozLXLaspP.pdf', 'done', NULL, 9, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(73, '2025-04-27T10-51 Tax invoice #9834306003349088-9834306010015754.pdf', 'invoices/R9gUgGUmUhwoaSOaxmSYgArE6isPBFSUl0Q0JJop.pdf', 'done', NULL, 1, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(74, '2025-04-28T06-07 Tax invoice #9654111508035205-9800312403415111.pdf', 'invoices/lPBK8LMpaye0hxx8yc0XgtJIVqacV9lr5YtShjeC.pdf', 'done', NULL, 9, '2026-03-30 23:37:41', '2026-03-30 06:32:41', '2026-03-30 23:37:41'),
(75, '2025-04-28T09-04 Tax invoice #9595799413866409-9841413515971670.pdf', 'invoices/03beXyxgvCDElI2eAB9Dh91Pt3rx5ftPpQFt18Ir.pdf', 'done', NULL, 2, '2026-03-30 23:37:41', '2026-03-30 06:33:43', '2026-03-30 23:37:41'),
(76, '2025-04-28T19-27 Tax invoice #9695040457275636-9695040470608968.pdf', 'invoices/NCPTq67DX3RizFs1cxjK3kFtmM3f8YnXjhR0G4ts.pdf', 'done', NULL, 9, '2026-03-30 23:37:41', '2026-03-30 06:33:43', '2026-03-30 23:37:41'),
(77, '2025-04-28T19-31 Tax invoice #9658473234265699-9695063553939993.pdf', 'invoices/E0C4KLhRq81GV5U2FjWaj3w6IbTSsX9KNJOL8Ua9.pdf', 'done', NULL, 1, '2026-03-30 23:37:42', '2026-03-30 06:33:43', '2026-03-30 23:37:42'),
(78, '2025-04-28T23-22 Tax invoice #9805991996180485-9805992002847151.pdf', 'invoices/TrtgNCbw8oxxNJQI0YmHDRqBkfZEC1qdRRSHnv8W.pdf', 'done', NULL, 9, '2026-03-30 23:37:42', '2026-03-30 06:33:43', '2026-03-30 23:37:42'),
(79, '2025-04-29T06-07 Tax invoice #9823515631094787-9661903653922657.pdf', 'invoices/CN1NWRJXJhiNlmY7KY60sJKvULHKeU8RjlsfAejo.pdf', 'done', NULL, 9, '2026-03-30 23:37:42', '2026-03-30 06:33:43', '2026-03-30 23:37:42'),
(80, '2025-04-29T19-27 Tax invoice #9607372962709054-9812970658815952.pdf', 'invoices/mMEOG78NulS8srXtjQqGrnmphxCFg962iiiE11CK.pdf', 'done', NULL, 9, '2026-03-30 23:37:42', '2026-03-30 06:33:43', '2026-03-30 23:37:42'),
(81, '2025-04-29T19-27 Tax invoice #9661207390658949-9666683133444709.pdf', 'invoices/kd0N6YsyhT7aDnAJJShHSPeq540xQDfOPwWLEkth.pdf', 'done', NULL, 5, '2026-03-30 23:37:42', '2026-03-30 06:33:43', '2026-03-30 23:37:42'),
(82, '2025-05-01T16-34 Tax invoice #9623193504460333-9674538282659189.pdf', 'invoices/8p5gzB2TVhfq3HBUxo9fpmBa9zNPIxQ4jma1LTO5.pdf', 'done', NULL, 7, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(83, '2025-05-03T20-38 Tax invoice #9695924870520534-9641860189260331.pdf', 'invoices/DqiIFZiGGifiYv4hYc4IPeON7rxzvrok9x9PvSFs.pdf', 'done', NULL, 11, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(84, '2025-05-05T19-07 Tax invoice #9754307231348958-9711171882329166.pdf', 'invoices/ELxUWgMdnDGeWMH5uKftAPUm7QhidacYqcCfQDuV.pdf', 'done', NULL, 10, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(85, '2025-05-06T23-34 Tax invoice #9667138936732456-9842849559161400.pdf', 'invoices/zYChnawOaWe2p58VuIvYP54wmhA1dBG4oqNOnIY9.pdf', 'done', NULL, 11, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(86, '2025-05-07T06-11 Tax invoice #9767074420072239-9721113568001660.pdf', 'invoices/GAndtGB06kB5HJ4qI6NVIGAJrsrXnPcssXOkDk2D.pdf', 'done', NULL, 14, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(87, '2025-05-07T22-10 Tax invoice #9727078957405121-9896998743746475.pdf', 'invoices/tGpFmyJmxiwKJBxwx2G4zMxO60VN21KJqc6I7JPQ.pdf', 'done', NULL, 14, '2026-03-30 23:37:42', '2026-03-30 06:34:32', '2026-03-30 23:37:42'),
(88, '2025-05-07T22-14 Tax invoice #9675415342571482-9793929894053363.pdf', 'invoices/2uTUx4Oh6C90DNKYFUFmfrkaVpr7ecgofmcsD8C4.pdf', 'done', NULL, 1, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(89, '2025-05-08T06-06 Tax invoice #9899792563467093-9729882823791401.pdf', 'invoices/YnilS6cHKPBXdstIpcJGJd9iWFNDqsNaG3clFOk3.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(90, '2025-05-08T21-27 Tax invoice #9859467570832932-9859467577499598.pdf', 'invoices/6nuFphuCL0zYPxmbvls8VpyAAnJjMn0LQ5H6EiWB.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(91, '2025-05-08T21-31 Tax invoice #9738075386305482-9735540216558995.pdf', 'invoices/rfxNa9XXUw64YplpCTR3hWfqSxgbUXzdhsjUhsQs.pdf', 'done', NULL, 7, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(92, '2025-05-09T06-06 Tax invoice #9738554819590868-9908498455929837.pdf', 'invoices/jnGDgL2yp4yqKEKFhwpXC5iQKpbK7OezNAVXSsST.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(93, '2025-05-10T06-06 Tax invoice #9794229600690054-9750114928434861.pdf', 'invoices/uxxCn2sXImUJJMld4QCv9kSGNvjDLx5Qy86WKqeJ.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(94, '2025-05-11T06-06 Tax invoice #9704417543004595-9926077687505247.pdf', 'invoices/uBr2KTVeTwaKEZDfeaumDjEkonWMXdyaMTq9BM4A.pdf', 'done', NULL, 1, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(95, '2025-05-11T06-06 Tax invoice #9758687354244285-9880066085439747.pdf', 'invoices/0sChnZVaREIcskUWGFTyoRrrxvefIi0vkByakjtv.pdf', 'done', NULL, 12, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(96, '2025-05-11T20-39 Tax invoice #9761149347331415-9763637757082578.pdf', 'invoices/3P8f9huPs3OcmBuIAFhKPzR3SuPNVSqNm9cOpU4d.pdf', 'done', NULL, 13, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(97, '2025-05-12T22-08 Tax invoice #9776972302415791-9816272565152424.pdf', 'invoices/nUg0WbaqzW3nUEzj7ANtHGB3CfzAtPw2aUnU21zm.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(98, '2025-05-13T19-11 Tax invoice #9946727298773619-9824338167679197.pdf', 'invoices/EqNd5e13xtNDBeYReRSQZtaE2YgJIULbnv7ItUoK.pdf', 'done', NULL, 14, '2026-03-30 23:37:43', '2026-03-30 06:34:32', '2026-03-30 23:37:43'),
(99, '2025-05-14T20-31 Tax invoice #9786801444766205-9942079369238413.pdf', 'invoices/8Uchmm24kvKC8uC1ZNtAbBekS6COMxe673XXmW7U.pdf', 'done', NULL, 14, '2026-03-30 23:37:44', '2026-03-30 06:34:32', '2026-03-30 23:37:44'),
(100, '2025-05-15T23-40 Tax invoice #9804660556313632-9952237871555896.pdf', 'invoices/3799LzXEa4QgOE2eZnJ00Mr4o7axysOVOMUL3jCw.pdf', 'done', NULL, 14, '2026-03-30 23:37:44', '2026-03-30 06:34:32', '2026-03-30 23:37:44'),
(101, '2025-05-16T06-36 Tax invoice #9807265392719815-9923011681145187.pdf', 'invoices/xLzEub7zhNDECHRZ6azMVQBL8G7KY4otdJc5SfDb.pdf', 'done', NULL, 12, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(102, '2025-05-17T07-48 Tax invoice #9857413094371704-10003754689737551.pdf', 'invoices/zR7C5pMFCa3MTttk0av1GBGAcamwkW8nAPTKGxOy.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(103, '2025-05-18T05-42 Tax invoice #9865677870211893-9987039274742421.pdf', 'invoices/LAW34wfasGdFlVgbRmG9hQnBmhieYbKCTKZDSD5r.pdf', 'done', NULL, 14, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(104, '2025-05-19T06-51 Tax invoice #10020395208073499-9995505107229171.pdf', 'invoices/YF1rihTucFz71QpwDASROtcFTJLyBCJivVbgyqIQ.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(105, '2025-05-20T06-17 Tax invoice #9956982037748151-10028364803943206.pdf', 'invoices/5cLMsJtFhWiXc4olc6RGcqf4CNDRJxxLVEbX1AnM.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(106, '2025-05-20T19-14 Tax invoice #9887597618019918-9846249542154733.pdf', 'invoices/nj4zeIpA73UPCMbXRT8mz98kz4jajTBPRuNbQgzk.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(107, '2025-05-21T08-12 Tax invoice #9966266716819683-9998702960242720.pdf', 'invoices/koPU4yg3RWuRqKqyBwUMDtBjA2bzEk7byZBPKBGX.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(108, '2025-05-21T08-29 Tax invoice #9791733087606373-9843244459121903.pdf', 'invoices/xcY2270s9dkKdajjBLZkOt1wjsGfCHFddN7pg3fp.pdf', 'done', NULL, 12, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(111, '2025-05-21T16-25 Tax invoice #9845726818873667-10001299326649750.pdf', 'invoices/5XUQ3ojHcvLVF811HoNjiPfYoHu6hIdOou4LjnD0.pdf', 'done', NULL, 13, '2026-03-30 23:37:44', '2026-03-30 06:35:17', '2026-03-30 23:37:44'),
(112, '2025-05-21T16-25 Tax invoice #9968822029897485-10001299723316377.pdf', 'invoices/2Op5jyziJGKgqCt0wCu0h3eBdtLJa55vNdYLyfyp.pdf', 'done', NULL, 13, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(115, '2025-05-22T10-40 Tax invoice #9853469248099428-9853469261432760.pdf', 'invoices/Nfw1ZiTx44Riuvj3ekpQfUYOjRh0koFPiLI16D12.pdf', 'done', NULL, 14, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(116, '2025-05-23T07-14 Tax invoice #9924447911001560-9805931949519820.pdf', 'invoices/zb7Qk0htWzjpxS9YeSLwug0TfCoh2sRIE1zvuE7h.pdf', 'done', NULL, 15, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(117, '2025-05-24T04-57 Tax invoice #9871971522915868-9871971532915867.pdf', 'invoices/F4YvAkdqirzaO8SODjDW4KM8n97e0v81716bPAlS.pdf', 'done', NULL, 14, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(118, '2025-05-25T00-28 Tax invoice #9870399383073077-10040123806100634.pdf', 'invoices/dSkVoQIx09ON2yMA7w5UcVe7Tfrl4hJWfVjTNAAa.pdf', 'done', NULL, 14, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(119, '2025-05-26T09-20 Tax invoice #10049211225191892-9929490713830608.pdf', 'invoices/tyk5yAtgz2FlY766guCTjwmTKutJEzA32TewNDwr.pdf', 'done', NULL, 14, '2026-03-30 23:37:45', '2026-03-30 06:35:17', '2026-03-30 23:37:45'),
(120, '2025-05-27T03-25 Tax invoice #9951612954951722-10040654149380934.pdf', 'invoices/9EFcLZZMn3FU7SXK6OlOBWRmiOxTuKvHNrEhXiyg.pdf', 'done', NULL, 16, '2026-03-30 23:37:45', '2026-03-30 06:35:48', '2026-03-30 23:37:45'),
(121, '2025-05-27T17-59 Tax invoice #10083884421724577-9891167247662961.pdf', 'invoices/ILbR8SWY8XrwNncL1uS6yEvmhMAeo4Xn2ObuhdCd.pdf', 'done', NULL, 12, '2026-03-30 23:37:45', '2026-03-30 06:35:48', '2026-03-30 23:37:45'),
(122, '2025-05-28T11-01 Tax invoice #9896332120479807-9944604915652521.pdf', 'invoices/aGBM5B0ue7vYQZRdeApvGR2PSgWFEqLz2AbHgH8D.pdf', 'done', NULL, 12, '2026-03-30 23:37:45', '2026-03-30 06:35:48', '2026-03-30 23:37:45'),
(123, '2025-05-28T22-10 Tax invoice #9905414339571586-10067786653334349.pdf', 'invoices/SS1cle34IAqeQuKR38PneMJblpQDJVtkWGVd1Nyb.pdf', 'done', NULL, 14, '2026-03-30 23:37:45', '2026-03-30 06:35:48', '2026-03-30 23:37:45'),
(124, '2025-05-29T15-24 Tax invoice #9851686004944414-10073048316141516.pdf', 'invoices/xOQDsb8ylrWRifedic9XvWfsXJHPNecaRXncx4WF.pdf', 'done', NULL, 13, '2026-03-30 23:37:45', '2026-03-30 06:35:49', '2026-03-30 23:37:45'),
(125, '2025-05-30T08-37 Tax invoice #9975388005907550-9916114618501558.pdf', 'invoices/cUGYnzYY1y5u96OB8LvSrajKLYZEKwvF15LikyBo.pdf', 'done', NULL, 13, '2026-03-30 23:37:46', '2026-03-30 06:35:49', '2026-03-30 23:37:46'),
(126, '2025-05-31T12-12 Tax invoice #9920247658088253-9984584734987877.pdf', 'invoices/t6tgxFEDw3QoWpuaY0jhRnoPOc3mRlXs5DPD3ytM.pdf', 'done', NULL, 12, '2026-03-30 23:37:46', '2026-03-30 06:35:49', '2026-03-30 23:37:46'),
(127, '2025-06-03T00-00 Tax invoice #9989178891195123-9989178897861789.pdf', 'invoices/izRHNvLemdUOOVLhMeInUtpLiY4BmWLlVP6NksjJ.pdf', 'done', NULL, 17, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(128, '2025-06-04T23-35 Tax invoice #10019800508132966-9953779151401766.pdf', 'invoices/WwcdhvVUulWs4QtYthY6zenaHyiqvSMyKzYWNd7N.pdf', 'done', NULL, 11, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(129, '2025-06-05T12-49 Tax invoice #9957961540983527-10080036792109341.pdf', 'invoices/PQG80WnC1cM5AGBDQedOUNRDm4wnjFAvxOUEQ5UC.pdf', 'done', NULL, 9, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(130, '2025-06-05T12-49 Tax invoice #10009600229152989-9905990426180638.pdf', 'invoices/ywkP4nGV7yDduuFc87bG4NbnJARXXLzRNSUzWNKs.pdf', 'done', NULL, 6, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(131, '2025-06-05T12-49 Tax invoice #23876405402045908-9964620866984266.pdf', 'invoices/hXVPmVVvmDfkxGve6ZN6lK5u9bDO7sQHuugm6HAo.pdf', 'done', NULL, 2, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(132, '2025-06-05T12-49 Tax invoice #23876405815379200-9957967054316309.pdf', 'invoices/25PRSuHScPjKcTynKhgfl1ohNKFRkLhAEcojcDaC.pdf', 'done', NULL, 1, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(133, '2025-06-05T12-50 Tax invoice #10009604799152532-9905994692846878.pdf', 'invoices/j0m5xGiFLPqCeDAhL3NVmAC2Az2KO1chS4PMDOMv.pdf', 'done', NULL, 1, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(134, '2025-06-06T06-55 Tax invoice #23921265110893274-23921265120893273.pdf', 'invoices/zW1vTm3tS4vb93tGuZwU0z1qVQAy1imztAAMEzxs.pdf', 'done', NULL, 9, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(135, '2025-06-06T11-50 Tax invoice #23897480929938354-10017371638375848.pdf', 'invoices/zA8ewUcfQoUKIwjbkL8IrsPqWmG5bzK0DJ0PvSqs.pdf', 'done', NULL, 3, '2026-03-30 23:37:46', '2026-03-30 06:36:48', '2026-03-30 23:37:46'),
(136, '2025-06-07T06-55 Tax invoice #10037784826334534-23890455587307556.pdf', 'invoices/E10NyETPLzUyx7ueWuampirbxeW8ikjPn9QB21s2.pdf', 'done', NULL, 11, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(137, '2025-06-08T06-55 Tax invoice #23911196361900144-23863528593333594.pdf', 'invoices/eNwukH0e3aU6cCZlDONIExqRcKi5a5zPxMO2c1TD.pdf', 'done', NULL, 7, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(138, '2025-06-09T02-44 Tax invoice #23917160784637035-23869469502739503.pdf', 'invoices/4VtAKteWt7E3WawsDwYNXJaS7kUi8yfbW5VXB2nw.pdf', 'done', NULL, 6, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(139, '2025-06-09T02-48 Tax invoice #23904109449275503-9987009848078700.pdf', 'invoices/GgVNIhK37HHiDnNgwx0BK5t212WNfgjV887dATtu.pdf', 'done', NULL, 4, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(140, '2025-06-09T06-55 Tax invoice #23905171189169329-10039051456207866.pdf', 'invoices/qh5eSLKigwnVg7doMsURy7LUEQeesOaaktlybE57.pdf', 'done', NULL, 5, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(141, '2025-06-10T06-55 Tax invoice #9994254597354221-10059861210793562.pdf', 'invoices/s7AeEMEmJWEgP0avNvlG8BrscjjgssxNGg2d23us.pdf', 'done', NULL, 6, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(142, '2025-06-11T06-55 Tax invoice #9950298985083115-23920589244294190.pdf', 'invoices/VOWHrYPo5YT8Q6HFIJmFF8iZC1UpVwDr7W97Vj7g.pdf', 'done', NULL, 8, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(143, '2025-06-12T06-07 Tax invoice #23967270662959385-23893739010312552.pdf', 'invoices/Mr1XcJsMpvLSG9CpUUWOoI4nEFP1a6ypq9hQcYCi.pdf', 'done', NULL, 6, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(144, '2025-06-13T01-45 Tax invoice #23900470576306062-23973875498965568.pdf', 'invoices/eV4fiQL6khEso3HaeUxiQYl3fOBtPawYSo4nYnJq.pdf', 'done', NULL, 12, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(145, '2025-06-14T14-01 Tax invoice #10028500860596261-23912412941778492.pdf', 'invoices/e1yFU5b5SIkIOBbA5ch1Vkqn5rdTV0uZyJc21m6C.pdf', 'done', NULL, 16, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(146, '2025-06-15T07-01 Tax invoice #10034042936708720-10040628889383463.pdf', 'invoices/z7GXiQEh7VedpqrssIQN5Px8HjrI2JF1G3HgGyBz.pdf', 'done', NULL, 16, '2026-03-30 23:37:47', '2026-03-30 06:36:48', '2026-03-30 23:37:47'),
(147, '2025-06-16T00-21 Tax invoice #23867223216297461-23923812560638530.pdf', 'invoices/2vwgZjk5qo5z3rHi3WATDW8hSKsu7UGoaUm1TQCX.pdf', 'done', NULL, 17, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(148, '2025-06-16T00-25 Tax invoice #10039806909465656-23997133446639773.pdf', 'invoices/ART11ixJCmeLkAMaZLqQeAdC2ByZ2ELT16j3FvXA.pdf', 'done', NULL, 1, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(149, '2025-06-16T02-15 Tax invoice #23971493412537105-10041181679328183.pdf', 'invoices/eUGNKcNpbymi9AZdJLJfgrIKmEA2wVLJ15qfXNkW.pdf', 'done', NULL, 1, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(150, '2025-06-16T06-08 Tax invoice #23868827072803742-23925532433799876.pdf', 'invoices/DM3X1Z58FiwwUUPbW4NU9a4ak2fwbmZkzJXid6H0.pdf', 'done', NULL, 12, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(151, '2025-06-16T22-30 Tax invoice #23873899878963128-23964737669879347.pdf', 'invoices/jPDjRDtDRjFGNdL0QyjfpODx0MfltHlqFGc9Aj5X.pdf', 'done', NULL, 16, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(152, '2025-06-16T22-34 Tax invoice #24003906092629175-10099186570194354.pdf', 'invoices/R9SUbkYQPrKQmytNLyXr1bsy5vQYKKwpJiFH82Fg.pdf', 'done', NULL, 1, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(153, '2025-06-17T06-08 Tax invoice #10050093688436982-23864116809941430.pdf', 'invoices/lwDSBV9vVnuyxJClCElL7jiu9DhTTmJIda3QDSpy.pdf', 'done', NULL, 16, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(154, '2025-06-17T10-00 Tax invoice #10050558875057126-23981901518162961.pdf', 'invoices/XPnIwPZqtDsl1gAkZaSX7kJsUlp7PlT3KTZzoV1Y.pdf', 'done', NULL, 16, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(155, '2025-06-18T06-08 Tax invoice #10057374021042278-23872296885790089.pdf', 'invoices/3u1u16p6eLmtdDExJNB13fFu0aM6WYJMkIZ0thv7.pdf', 'done', NULL, 16, '2026-03-30 23:37:48', '2026-03-30 06:40:42', '2026-03-30 23:37:48'),
(156, '2025-06-18T21-37 Tax invoice #23980749278278186-23980749298278184.pdf', 'invoices/7n1HbhKabppIwmtUsrPK8M0MLri51ulzlHirXVG1.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(157, '2025-06-18T21-41 Tax invoice #24019821817704269-23890014704018312.pdf', 'invoices/s2sFKXPBFdeFMQ8u6liSmsig69F31eq0z3hm7RPs.pdf', 'done', NULL, 12, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(158, '2025-06-19T06-11 Tax invoice #23892890447064071-10072338626212489.pdf', 'invoices/VLG5GVpVVnawSDeYzkrap63NXBMxsCKFNnp0AuLv.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(159, '2025-06-19T18-33 Tax invoice #10076593372453681-23884426381243806.pdf', 'invoices/koJl7PpT8S8CNKZXfS1FxLyw1kMc9WuC3MvjERYv.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(160, '2025-06-19T18-37 Tax invoice #24026804923672625-23884452167907894.pdf', 'invoices/ZOU6M2Eql4hIzL2d3vM4lUXMOOQsJDJb4AD1xXiq.pdf', 'done', NULL, 3, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(161, '2025-06-19T20-56 Tax invoice #23898215463198236-10077519199027765.pdf', 'invoices/lLBN0m39lvcARGBLN1mF6DJKbe74foXEjWEG111m.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(162, '2025-06-19T20-56 Tax invoice #23898215743198208-10071896922923325.pdf', 'invoices/YYNaiJcGnq0ORrpTUuxmSr2Mxbvisc9LOu6fQ4xe.pdf', 'done', NULL, 2, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(163, '2025-06-19T20-57 Tax invoice #10071902129589471-23988852400801207.pdf', 'invoices/Svpl3JQKFg1frgTQOl363BS9L1E7xUMtY3ktf285.pdf', 'done', NULL, 10, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(164, '2025-06-20T06-11 Tax invoice #24005188649167581-23901309022888880.pdf', 'invoices/2ZxtGGhpVZac6wel2GBTWaav5TmrDhSEhCd20aAl.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(165, '2025-06-20T15-55 Tax invoice #23891445347208576-23995009213518859.pdf', 'invoices/ey6HwqV03B1boju5VTx2fQMBLaamLpjlQG2utlTW.pdf', 'done', NULL, 19, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(166, '2025-06-20T15-59 Tax invoice #10078019725644378-23891468170539627.pdf', 'invoices/5DzeL3qfm2AdwmBvRv1xWXamEMJBu1ughFtnFbyh.pdf', 'done', NULL, 1, '2026-03-30 23:37:49', '2026-03-30 06:40:42', '2026-03-30 23:37:49'),
(167, '2025-06-21T01-10 Tax invoice #10081394145306936-24037599075926543.pdf', 'invoices/Da55VQbN1IceGu3agoWAzDlzhNsLLlmyyeTuOYva.pdf', 'done', NULL, 20, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(168, '2025-06-21T01-14 Tax invoice #23964917246528061-10080223555423991.pdf', 'invoices/bD92r9b9QplDJeYdCqBLTQhu152G7uAu6qeEJJks.pdf', 'done', NULL, 3, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(169, '2025-06-21T06-11 Tax invoice #23966407533045699-23909682028718246.pdf', 'invoices/9JxPSjgAl1jvMHUA8aLwHJ3cR91WKMhWc8HRcpNs.pdf', 'done', NULL, 15, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(170, '2025-06-21T14-49 Tax invoice #24002763312743449-24002763322743448.pdf', 'invoices/bPuTGc627ng0O9YI1KOFrwmC4zcDpnBWniyPtrzb.pdf', 'done', NULL, 16, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(171, '2025-06-21T14-53 Tax invoice #23912640641755718-10091370797642605.pdf', 'invoices/irm0x1499nTrLyvpSaTrMjl6litKQlu6pdWkNeNa.pdf', 'done', NULL, 1, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(172, '2025-06-22T06-11 Tax invoice #24020762137610232-10089330874513259.pdf', 'invoices/eDrkh8sfr3qiMlNfrys7HLPrQFU7fbBduiG8oFg9.pdf', 'done', NULL, 16, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(173, '2025-06-22T11-49 Tax invoice #23905765992443178-10091059607673719.pdf', 'invoices/ScgJZXDtkj6tevQhkwAdfmGLfwsvpua3UuH633FO.pdf', 'done', NULL, 10, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(174, '2025-06-22T11-53 Tax invoice #23919251734427942-10039075326205480.pdf', 'invoices/wwqP3waSwWZuXm31B4rBiQXBXKTWv1JgayGRHbr1.pdf', 'done', NULL, 13, '2026-03-30 23:37:50', '2026-03-30 06:40:55', '2026-03-30 23:37:50'),
(175, '2025-06-23T06-11 Tax invoice #10097008730412140-23981449741541478.pdf', 'invoices/DTS9cdUEUxxFw8r9Z7UhUIBJpsfKu4DDkXlBTQkU.pdf', 'done', NULL, 12, '2026-03-30 23:37:50', '2026-03-30 06:41:49', '2026-03-30 23:37:50'),
(176, '2025-06-23T19-21 Tax invoice #23929712910048491-23865314643154986.pdf', 'invoices/UNrhD0U0CQj3XUh4bmLAhuO1G1E7BhmjuHxbMMno.pdf', 'done', NULL, 17, '2026-03-30 23:37:50', '2026-03-30 06:41:49', '2026-03-30 23:37:50'),
(177, '2025-06-23T19-25 Tax invoice #23865339036485880-23865339046485879.pdf', 'invoices/KVWKQkINAE9QfE4qWoSendcMtaBBcmfZrqPvNPvT.pdf', 'done', NULL, 6, '2026-03-30 23:37:50', '2026-03-30 06:41:49', '2026-03-30 23:37:50'),
(178, '2025-06-24T06-11 Tax invoice #24063180013368449-24063180016701782.pdf', 'invoices/qlk7yWDRvEOTeE3hE5af2ysAVsZuRvgLFk9rPNTV.pdf', 'done', NULL, 16, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(179, '2025-06-24T14-14 Tax invoice #23870808062605640-24026853547001092.pdf', 'invoices/c6W7UYFDDJsOZGnxadLrClqnQtEvInYeOyQrVPNU.pdf', 'done', NULL, 16, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(180, '2025-06-24T14-18 Tax invoice #23872252942461156-23936626579357124.pdf', 'invoices/zVBuS1eE8JCFz2hjMWMEmLYT2IZZ8KXoxNXupCfE.pdf', 'done', NULL, 9, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(181, '2025-06-24T23-23 Tax invoice #23996176186735500-24030266206659826.pdf', 'invoices/fS5oik4l9Rj8PmqsHPLz8w04uAVa1lutCgL8nFPR.pdf', 'done', NULL, 16, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(182, '2025-06-24T23-27 Tax invoice #23926332370386540-23939972892355826.pdf', 'invoices/YnCGYzefrdldnt8GVQ8bPzJa4C2k7gGgAAMGnNlI.pdf', 'done', NULL, 10, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(183, '2025-06-25T06-11 Tax invoice #23928381203514990-24032520619767718.pdf', 'invoices/294wZ2gYcS89wcKVmNNZXsOU4g5QBxkOCSM1pk5D.pdf', 'done', NULL, 14, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(184, '2025-06-25T16-07 Tax invoice #23886506941035757-24035917162761397.pdf', 'invoices/iItBQQqzSPWgF9c9hH5CYoXvWsPZPSxKAiYWQP13.pdf', 'done', NULL, 14, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(185, '2025-06-25T16-11 Tax invoice #24074621125557671-24035939382759175.pdf', 'invoices/RZVpuePPzmMOzZDBRfsUm8mYZ8qdQi8ZsYnSz3s7.pdf', 'done', NULL, 5, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(186, '2025-06-26T05-32 Tax invoice #23884624784557301-23936577799361997.pdf', 'invoices/QSbYiqwj7hEjeUMQZXtSbcIuOkJL5pSdf97nuORX.pdf', 'done', NULL, 15, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(187, '2025-06-26T05-36 Tax invoice #23884644854555294-24040632258956554.pdf', 'invoices/Bwvj1ZB39oeE23DXBGpWGVwmezx9ZthJCYWssmx1.pdf', 'done', NULL, 2, '2026-03-30 23:37:51', '2026-03-30 06:41:49', '2026-03-30 23:37:51'),
(188, '2025-06-26T06-06 Tax invoice #23936766992676411-24079201078433009.pdf', 'invoices/bi2fXM28qqMR6SPizQuv3cgYZksoI7soygtQ0zLo.pdf', 'done', NULL, 4, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(189, '2025-06-26T14-24 Tax invoice #10070294233083589-10070294243083588.pdf', 'invoices/VqjF0OAyt8jQnId3wqGqto8HGtBpYM2ul7OqrQBr.pdf', 'done', NULL, 15, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(190, '2025-06-27T01-14 Tax invoice #24085494964470287-23891553823864397.pdf', 'invoices/qCu2M5zYB47Y9zKE2a9t6aY9wrpYlRSIfV4sJfLu.pdf', 'done', NULL, 16, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(191, '2025-06-27T01-18 Tax invoice #23892571430429307-24047792794907167.pdf', 'invoices/h7zN7eXDHtPSYzqxCFdGe9fpcPfkLMRPRRPSCe7U.pdf', 'done', NULL, 3, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(192, '2025-06-27T06-40 Tax invoice #24087102117642905-10075664659213213.pdf', 'invoices/Pgp33RImkBjBe8lfei0OUmTRk7dm5bIphj1oWfqe.pdf', 'done', NULL, 14, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(193, '2025-06-27T16-54 Tax invoice #24053440367675743-24066762626343516.pdf', 'invoices/58s5cWWnCH27NZv58n6ytt1xklicXslXz5RmOAmi.pdf', 'done', NULL, 16, '2026-03-30 23:37:52', '2026-03-30 06:41:49', '2026-03-30 23:37:52'),
(194, '2025-06-27T16-58 Tax invoice #24018982421121543-24018982441121541.pdf', 'invoices/PBdby4QxnXPuqGMQhHbrmDIEMFABIk7lqEjhuuUT.pdf', 'done', NULL, 9, '2026-03-30 23:37:52', '2026-03-30 06:42:05', '2026-03-30 23:37:52'),
(195, '2025-06-28T06-41 Tax invoice #24023565610663224-10083292925117053.pdf', 'invoices/QMhBdj3yObP5mTAuwbdl6hqdq5MlLJxE87BDolX1.pdf', 'done', NULL, 12, '2026-03-30 23:37:52', '2026-03-30 06:42:05', '2026-03-30 23:37:52'),
(196, '2025-06-28T17-06 Tax invoice #23956885887331188-23971254129227702.pdf', 'invoices/HtD0dO5FjNsAdQ7ZIhrtv1841DUBMqYhcXyc3IDz.pdf', 'done', NULL, 12, '2026-03-30 23:37:52', '2026-03-30 06:42:05', '2026-03-30 23:37:52'),
(197, '2025-06-28T17-10 Tax invoice #23956908793995564-24098012173218566.pdf', 'invoices/BP5vMeEvIpm6XrzaxpJDu7ML3g7HSVlRm1ujGyv7.pdf', 'done', NULL, 11, '2026-03-30 23:37:52', '2026-03-30 06:42:05', '2026-03-30 23:37:52'),
(198, '2025-06-29T06-41 Tax invoice #10090862234360122-23916122224740895.pdf', 'invoices/UzEpkaTqg9gSXJKv4r8J7HCQSEZh2m2JEuzJ1dmP.pdf', 'done', NULL, 12, '2026-03-30 23:37:52', '2026-03-30 06:42:05', '2026-03-30 23:37:52'),
(199, '2025-06-29T17-01 Tax invoice #23912204498465996-24034933799526405.pdf', 'invoices/jeCglRxoOEvmR9dW44I0ulxiFUYmROS4BFQ9PYp9.pdf', 'done', NULL, 11, '2026-03-30 23:37:53', '2026-03-30 06:42:05', '2026-03-30 23:37:53'),
(200, '2025-06-29T17-01 Tax invoice #23914940894859027-23914940898192360.pdf', 'invoices/pkQlRLVKzbi1RIljPsRytghHUC55Z3VKNOOt5BhR.pdf', 'done', NULL, 11, '2026-03-30 23:37:53', '2026-03-30 06:42:05', '2026-03-30 23:37:53'),
(201, '2025-06-30T09-22 Tax invoice #24075987498754363-23924109690608815.pdf', 'invoices/njATuZaDXDeRx2Af6mVyaDurYkUYKoEszpN4db66.pdf', 'done', NULL, 11, '2026-03-30 23:37:53', '2026-03-30 06:42:05', '2026-03-30 23:37:53'),
(202, '2025-07-02T13-50 Tax invoice #23878677295152049-24057151763971275.pdf', 'invoices/6sYk1vNQua1eh5M4ptKy9qmcdW4vJXeUbtnSGYHS.pdf', 'done', NULL, 12, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(203, '2025-07-05T17-46 Tax invoice #24027073030312478-23966656316354152.pdf', 'invoices/17Efx2bwCT2sLxZyaXPL62IZOkCsVe0JzzvbAKJk.pdf', 'done', NULL, 4, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(204, '2025-07-08T09-33 Tax invoice #24048307711522343-24048307744855673.pdf', 'invoices/RxYMCJKXhC9Jcw4RF3eAsBE24r7IwXvwQctqO9Db.pdf', 'done', NULL, 10, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(205, '2025-07-09T15-32 Tax invoice #23934565679563210-24162907160062395.pdf', 'invoices/5KJYBSwScfhCloFHkDM9rgUjXRhIgl0YoiKyovnI.pdf', 'done', NULL, 10, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(206, '2025-07-10T19-06 Tax invoice #24193262873693495-24193262880360161.pdf', 'invoices/e9Fu3844taQlZX0AXGUxCV0XlgY18RscVFZOTvNG.pdf', 'done', NULL, 12, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(207, '2025-07-11T21-19 Tax invoice #24008922085460903-24169781772708268.pdf', 'invoices/GbEbVfG2XzoRyWpAfVOyDJ24duL9dEC7c566K08V.pdf', 'done', NULL, 12, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(208, '2025-07-12T22-25 Tax invoice #24189680220718422-24021257387560710.pdf', 'invoices/DBAKyk6Knlz7xQCipiXIMEG785s52mxSvyMcPFof.pdf', 'done', NULL, 12, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(209, '2025-07-13T23-41 Tax invoice #24197742683245509-24218763744476741.pdf', 'invoices/sTgwD4jPrKr514Z74eBpdTNo5PQrNkr3tUjHp1xQ.pdf', 'done', NULL, 12, '2026-03-30 23:37:53', '2026-03-30 06:42:37', '2026-03-30 23:37:53'),
(210, '2025-07-15T08-52 Tax invoice #24088092480877194-24103782822641498.pdf', 'invoices/zXWVqSe8MANwBiUe4sWAkP7aTXjPSuHcBHtPy0LG.pdf', 'done', NULL, 11, '2026-03-30 23:37:54', '2026-03-30 06:42:37', '2026-03-30 23:37:54'),
(211, '2025-07-16T06-02 Tax invoice #24166307859722331-24095678720118570.pdf', 'invoices/XTqUvXGOSHvcMYgU6gOcBPQyEhlZBDerm6j5Fxjy.pdf', 'done', NULL, 12, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(212, '2025-07-17T22-45 Tax invoice #24062367883449660-24180354844984299.pdf', 'invoices/Jy9EGuBXIfpOk8Gt0T1NKjATvOp7KceY49xn7W2D.pdf', 'done', NULL, 12, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(213, '2025-07-18T03-36 Tax invoice #23911196361900144-24003514926001618.pdf', 'invoices/k7msABZaOGbUuQ7V1TywqVQihW56i075YfbsKE8w.pdf', 'done', NULL, 7, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(214, '2025-07-19T06-42 Tax invoice #24191078560578594-24136421002711013.pdf', 'invoices/XZtINIc5PPjJygIUiNBuPjTShZdfOktm6htcv2H9.pdf', 'done', NULL, 2, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(215, '2025-07-19T15-23 Tax invoice #24075848785434903-24265043276515454.pdf', 'invoices/HfeJqqN7jkAunu8pmyO9Zhd2R0iN82SyjeL7a0nI.pdf', 'done', NULL, 12, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(216, '2025-07-20T08-38 Tax invoice #24083804397972676-24083804401306009.pdf', 'invoices/ryB7MXUwVC9dbNduA5SbQRDQwTHt5MhmI65GxLRZ.pdf', 'done', NULL, 12, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(217, '2025-07-20T22-36 Tax invoice #24203854109301039-24085985371087911.pdf', 'invoices/7rfREBP4ZDmhaCwP43YL3P5YgTcAQ0ZtZ7SNqh3L.pdf', 'done', NULL, 12, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(218, '2025-07-21T19-13 Tax invoice #24094731706879945-24260783643608079.pdf', 'invoices/Tjy8UcjOTx5aRPiTI2C8li21IRYG6BhOXFx9cmlX.pdf', 'done', NULL, 14, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(219, '2025-07-22T12-32 Tax invoice #24100630296290086-24098338523185929.pdf', 'invoices/852rniG5ztP2mRPwqVwMPWFKXCn6N5fkROjZjQSt.pdf', 'done', NULL, 14, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(220, '2025-07-23T05-14 Tax invoice #24221964570823326-24167489329604180.pdf', 'invoices/5BLNcmZ0biJw3sS29qBDLQYhmXFsyuT9x1MAqDnT.pdf', 'done', NULL, 13, '2026-03-30 23:37:54', '2026-03-30 06:43:19', '2026-03-30 23:37:54'),
(221, '2025-07-23T21-43 Tax invoice #24266921799660931-24049558668063910.pdf', 'invoices/8M9oXXaj3I6U9Bqzbi7pkpM31nF3TY4pi2UGg4TB.pdf', 'done', NULL, 13, '2026-03-30 23:37:55', '2026-03-30 06:43:19', '2026-03-30 23:37:55'),
(222, '2025-07-24T16-03 Tax invoice #24055509970802113-24116103771409404.pdf', 'invoices/xotsOoMiKGrt192ehgXvYlsKbPIYVzHBGc6vRXsy.pdf', 'done', NULL, 13, '2026-03-30 23:37:55', '2026-03-30 06:43:19', '2026-03-30 23:37:55'),
(223, '2025-07-25T12-25 Tax invoice #24186153517737761-24118925184460592.pdf', 'invoices/8nSQjCJUFqMhhcSJxgryCN0O1JsVypIr1QUj5LOJ.pdf', 'done', NULL, 12, '2026-03-30 23:37:55', '2026-03-30 06:43:19', '2026-03-30 23:37:55'),
(224, '2025-07-26T08-15 Tax invoice #24247128978306885-24068754979477612.pdf', 'invoices/z9lUyqsrfVKJgfzSmBGyNzO0YlYAzE5QdkrNKJwt.pdf', 'done', NULL, 10, '2026-03-30 23:37:55', '2026-03-30 06:43:19', '2026-03-30 23:37:55'),
(225, '2025-07-26T12-31 Tax invoice #24126890306997413-24299499379736505.pdf', 'invoices/gGAiRYyEjilTuiUfyY8OmpnMkCsCPksoMWmQsBLc.pdf', 'done', NULL, 10, '2026-03-30 23:37:55', '2026-03-30 06:43:19', '2026-03-30 23:37:55'),
(226, '2025-07-27T00-37 Tax invoice #24182533898099718-24303799705973139.pdf', 'invoices/zXgj0Oqp9TEVnaFRGtQDLogY8MKSYxF5V794jJgm.pdf', 'done', NULL, 2, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(227, '2025-07-27T06-30 Tax invoice #24132814616404982-24136756186010829.pdf', 'invoices/ZImLnIHJgn49dBJlqiY4KVd1Gh2RS3kZuz6CTN2o.pdf', 'done', NULL, 11, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(228, '2025-07-27T20-28 Tax invoice #24188909564128818-24259088153777634.pdf', 'invoices/1VjCAFHrdL3OqjnVxoenLfLfBMBuFfU9hZrHsAdQ.pdf', 'done', NULL, 3, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(229, '2025-07-28T02-48 Tax invoice #24312419178444525-24145793278440454.pdf', 'invoices/OZYJ920QvpX4Rz8gcGSNVWLiQR89WRXZj9VbiIQe.pdf', 'done', NULL, 8, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(230, '2025-07-28T02-52 Tax invoice #24082934061393037-24145811011772014.pdf', 'invoices/jWvVAXNPu0WDVP1nLPVnmUxo8lxxV3bfkWuRE9E0.pdf', 'done', NULL, 4, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(231, '2025-07-28T06-30 Tax invoice #24146882398331542-24262360640117052.pdf', 'invoices/szNSHDcgNi8DFKSB2jN6tMksX5FxgRUJNfSFbN4K.pdf', 'done', NULL, 7, '2026-03-30 23:37:55', '2026-03-30 06:45:08', '2026-03-30 23:37:55'),
(232, '2025-07-29T06-30 Tax invoice #24322270867459356-24200832789603162.pdf', 'invoices/wxURSEkoeihxgSZnWnTNeFbInHxk52m6CQcydAkw.pdf', 'done', NULL, 8, '2026-03-30 23:37:56', '2026-03-30 06:45:08', '2026-03-30 23:37:56'),
(233, '2025-07-30T06-30 Tax invoice #24330860309933745-24161846053501842.pdf', 'invoices/C5bp8xIq6eifmj0nlMis5zrhGmfOZDLdSyX76fP1.pdf', 'done', NULL, 5, '2026-03-30 23:37:56', '2026-03-30 06:45:08', '2026-03-30 23:37:56'),
(234, '2025-07-31T06-30 Tax invoice #24217588637927577-24327053736981070.pdf', 'invoices/R4vdspzUBKD3Th5kVfAlq5o9pQ0BRYg51wQXvdWL.pdf', 'done', NULL, 4, '2026-03-30 23:37:56', '2026-03-30 06:45:08', '2026-03-30 23:37:56'),
(235, '2025-08-01T06-30 Tax invoice #24180565871629861-24347515628268213.pdf', 'invoices/47H8aQYbPz6RW3bzD3tPskEuyWRXjrvXhSJaJbpH.pdf', 'done', NULL, 3, '2026-03-30 23:37:56', '2026-03-30 06:45:36', '2026-03-30 23:37:56'),
(236, '2025-08-02T06-30 Tax invoice #24187087164311064-24344159268603850.pdf', 'invoices/CFqP45pWJx0IotBvAMbWKXOaSAVlBpKHorruzHpu.pdf', 'done', NULL, 2, '2026-03-30 23:37:56', '2026-03-30 06:45:36', '2026-03-30 23:37:56'),
(237, '2025-08-03T06-30 Tax invoice #24364632463223196-24191892550497188.pdf', 'invoices/6Q9IQsTLYWr2G0ojgkmBno7Mui3ctTFNumAoKVV3.pdf', 'done', NULL, 2, '2026-03-30 23:37:56', '2026-03-30 06:45:36', '2026-03-30 23:37:56'),
(238, '2025-08-05T22-34 Tax invoice #24215250348161408-24215250361494740.pdf', 'invoices/jldCkaEayZD0SJnuy88Gukw5Y31Sb0NeCwDBNKFn.pdf', 'done', NULL, 4, '2026-03-30 23:37:56', '2026-03-30 06:45:36', '2026-03-30 23:37:56'),
(239, '2025-08-08T08-45 Tax invoice #24237724202580689-24237724215914021.pdf', 'invoices/QK8obyQ3Exjr3TPICIJll7Xwu1NrZl0hvfNzkjMk.pdf', 'done', NULL, 10, '2026-03-30 23:37:56', '2026-03-30 06:45:37', '2026-03-30 23:37:56'),
(240, '2025-08-10T10-48 Tax invoice #24307321682287605-24198946066458502.pdf', 'invoices/XSxC3reRza2Isw5TjG0tfmDD3iLwx3v2XRGybAr2.pdf', 'done', NULL, 11, '2026-03-30 23:37:56', '2026-03-30 06:45:37', '2026-03-30 23:37:56'),
(241, '2025-08-12T13-28 Tax invoice #24217641171255658-24467525492933897.pdf', 'invoices/fXBVZEjtkarovN10sP7gijPR2rnAtxPoHL7vwst8.pdf', 'done', NULL, 14, '2026-03-30 23:37:56', '2026-03-30 06:45:37', '2026-03-30 23:37:56'),
(242, '2025-08-14T11-36 Tax invoice #24452285591124550-24234157889603986.pdf', 'invoices/PlzsGlyWf9bRnfJSVwLlraFKrcCKRzdO1SDGTFrm.pdf', 'done', NULL, 14, '2026-03-30 23:37:56', '2026-03-30 06:45:37', '2026-03-30 23:37:56');
INSERT INTO `pending_invoice_pdfs` (`id`, `original_filename`, `stored_path`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(243, '2025-08-16T06-45 Tax invoice #24310918291927950-24312866105066503.pdf', 'invoices/L8X8VgXgP2FPkvNEhNRvVCvwVexrBeKHRME9Ryp1.pdf', 'done', NULL, 14, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(244, '2025-08-18T09-55 Tax invoice #24445701015116346-24445701018449679.pdf', 'invoices/8RgUAaqLQl8mqSUh1m3cFRX4JYwgHwRNZzZ2AIuQ.pdf', 'done', NULL, 13, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(245, '2025-08-20T08-47 Tax invoice #24285667704453004-24410443608642083.pdf', 'invoices/f6hHlegHQeyRRtRWCXnp4ATpf8pz13wyWRYg9wJx.pdf', 'done', NULL, 15, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(246, '2025-08-21T23-38 Tax invoice #24551440257875753-24478243925195388.pdf', 'invoices/pzfSIPUOo31V83gPZoNfFwtzzrXIB9MJpcewjedi.pdf', 'done', NULL, 18, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(247, '2025-08-22T23-32 Tax invoice #24540591675627273-24372901602396286.pdf', 'invoices/ZaiBiuhcBj6pUjS6AGYXIn5qH4w0Xe2RtjlSBAcX.pdf', 'done', NULL, 18, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(248, '2025-08-23T22-13 Tax invoice #24568905856129193-24378455015174273.pdf', 'invoices/gUZbQ3xK5ctWd23MC7ZYd2KcjrFFxayfVIT7Ip8i.pdf', 'done', NULL, 19, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(249, '2025-08-24T23-36 Tax invoice #24545442768475498-24388431920843253.pdf', 'invoices/LsybBwGrJ2P9WKgdKXT1J0iMvqO0F7nlY3eGMSR2.pdf', 'done', NULL, 19, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(250, '2025-08-26T00-02 Tax invoice #24396209813398793-24398461743173605.pdf', 'invoices/yfwibTyGrdua7xEOD9vtY2ftonEonepqrHua99bf.pdf', 'done', NULL, 19, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(251, '2025-08-27T01-28 Tax invoice #24345481188471655-24563722946647480.pdf', 'invoices/PRlrNnohXFhOCw7W5bIOtDH0EJat1ga5qjaGCQEz.pdf', 'done', NULL, 18, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(252, '2025-08-28T02-44 Tax invoice #24417886964564416-24355129760840131.pdf', 'invoices/TsiRJ22ACxFi8FQxxUeznMfbef8wtmflIH7rDcPv.pdf', 'done', NULL, 19, '2026-03-30 23:37:57', '2026-03-30 06:46:00', '2026-03-30 23:37:57'),
(253, '2025-08-29T09-51 Tax invoice #24427715010248277-24617151607971284.pdf', 'invoices/KaLAgLNKIwPbtKlHN1ytdhBN1wZJ030Cb2VlfmB2.pdf', 'done', NULL, 19, '2026-03-30 23:37:58', '2026-03-30 06:46:00', '2026-03-30 23:37:58'),
(254, '2025-08-30T17-14 Tax invoice #24438022582550849-24438261545860290.pdf', 'invoices/wsRcC6vovlSXN2HXrJgO6MHeIkiQu9ocvwMqi1Bz.pdf', 'done', NULL, 19, '2026-03-30 23:37:58', '2026-03-30 06:46:00', '2026-03-30 23:37:58'),
(255, '2025-08-31T14-36 Tax invoice #24561016396918140-24445223118497462.pdf', 'invoices/FlNRokuvHvp9Is3oFal99PdFfjpf77Z95LHaO3hk.pdf', 'done', NULL, 22, '2026-03-30 23:37:58', '2026-03-30 06:46:00', '2026-03-30 23:37:58'),
(256, '2025-09-16T06-58 Tax invoice #24579967475023029-24643847731968335.pdf', 'invoices/PnzpSvrXA17AyFtR7ZEJeiIzh9PGrs6AcXy8G9Bz.pdf', 'done', NULL, 22, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(257, '2025-09-16T13-45 Tax invoice #24629343836752053-24772208259132284.pdf', 'invoices/aClkcbIB6RpQfYB9zqChjkfUbwmPfD3hxZ3jQAs7.pdf', 'done', NULL, 1, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(258, '2025-09-16T14-50 Tax invoice #24751682551184850-24582448454774931.pdf', 'invoices/VnaF23ICkebNs3ZQA65X91thi9DBDeOeeybi4S21.pdf', 'done', NULL, 22, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(259, '2025-09-16T14-54 Tax invoice #24584083304611447-24646319671721141.pdf', 'invoices/wMKybvjraM6lSud4FVfOiL7I3A93vdFqHbwv6wQr.pdf', 'done', NULL, 1, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(260, '2025-09-17T01-10 Tax invoice #24650142308005544-24633537662999337.pdf', 'invoices/V7Am1zAPZFLgZ0onfHVTG2ZUiTuORtcmzsY3rG5d.pdf', 'done', NULL, 22, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(261, '2025-09-17T01-14 Tax invoice #24776496928703417-24587910564228721.pdf', 'invoices/Gm5oiP5AifAYbk4RWnpMYC7p45ygrWQoVijryzUW.pdf', 'done', NULL, 1, '2026-03-30 23:37:58', '2026-03-30 06:46:47', '2026-03-30 23:37:58'),
(262, '2025-09-17T01-24 Tax invoice #24633614022991701-24524911150528657.pdf', 'invoices/s6IHpjrcRKfWFQDyJXugBJRm9bSIpgh54x4r7DXo.pdf', 'done', NULL, 2, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(263, '2025-09-17T15-47 Tax invoice #24590984543921322-24638198429199927.pdf', 'invoices/XBMg9XGNUXQe8zkjEji32E6LAIKxZTmfTUM0JmtM.pdf', 'done', NULL, 21, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(264, '2025-09-18T02-37 Tax invoice #24595319806821129-24785600527793057.pdf', 'invoices/u05Vza2IQsANUR2KOGIYtuYqh0cklVigbiQfm6uu.pdf', 'done', NULL, 21, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(265, '2025-09-18T18-07 Tax invoice #24600840496269060-24664897643196677.pdf', 'invoices/OPi7iDIg57Vcj3gFy6bHFjceb86zjOwertlJT6ut.pdf', 'done', NULL, 21, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(266, '2025-09-19T11-08 Tax invoice #24671238722562569-24545845375101901.pdf', 'invoices/93YAWK6YRCfelIQREWiD9FXkwLiosxB3etMnkvqw.pdf', 'done', NULL, 21, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(267, '2025-09-20T01-39 Tax invoice #24551454917874280-24676891855330589.pdf', 'invoices/GgMXljO6Ea87KzNsF6pDR5v3AdH66QudC6kMAL5n.pdf', 'done', NULL, 23, '2026-03-30 23:37:59', '2026-03-30 06:46:47', '2026-03-30 23:37:59'),
(268, '2025-09-20T20-00 Tax invoice #24809857025367407-24621047234248387.pdf', 'invoices/O4LTgOm3ACgTepB35cqkkJFzNtbzMzPlJrdLwXon.pdf', 'done', NULL, 24, '2026-03-30 23:37:59', '2026-03-30 06:46:48', '2026-03-30 23:37:59'),
(269, '2025-09-21T16-22 Tax invoice #24628267113526399-24690646840621757.pdf', 'invoices/OgSV1lzy7T55Nqyj1Qwd9VMQQ8tpXtZn4AuOZZOa.pdf', 'done', NULL, 24, '2026-03-30 23:37:59', '2026-03-30 06:46:48', '2026-03-30 23:37:59'),
(270, '2025-09-22T11-42 Tax invoice #24790685407284565-24634978922855218.pdf', 'invoices/g3R4G3Vl8ye8z65jQ0csQHUo74Na3mTmBGHPw8Ok.pdf', 'done', NULL, 21, '2026-03-30 23:37:59', '2026-03-30 06:46:48', '2026-03-30 23:37:59'),
(271, '2025-09-23T09-09 Tax invoice #24810465868639851-24641277922225317.pdf', 'invoices/UuxATGmhWc317bK38zNq7yQkK95fQ3yuesltbIcc.pdf', 'done', NULL, 21, '2026-03-30 23:38:00', '2026-03-30 06:46:48', '2026-03-30 23:38:00'),
(272, '2025-09-24T02-05 Tax invoice #24650744731278632-24694768940209542.pdf', 'invoices/hspNTV3LH6FOCa729FuHxy1KlgpnMuvcKHBqdQvt.pdf', 'done', NULL, 22, '2026-03-30 23:38:00', '2026-03-30 06:46:48', '2026-03-30 23:38:00'),
(273, '2025-09-24T22-40 Tax invoice #24657192430633867-24655716010781508.pdf', 'invoices/8ex2CORHD1Z35EWCeIVzEld6Q8RXV40DCUvpQxRx.pdf', 'done', NULL, 22, '2026-03-30 23:38:00', '2026-03-30 06:46:48', '2026-03-30 23:38:00'),
(274, '2025-09-25T19-35 Tax invoice #24666497843036654-24664898933196550.pdf', 'invoices/kvv7cK9n2bVDGSYlLUOkb1iTLHnth7GUSDTQYRGm.pdf', 'done', NULL, 22, '2026-03-30 23:38:00', '2026-03-30 06:46:48', '2026-03-30 23:38:00'),
(275, '2025-09-26T21-04 Tax invoice #24863753093311133-24676571842029254.pdf', 'invoices/NoUD8UjBCYj1CgIzdxUHMfCN7LNJvox9RSIpAGcX.pdf', 'done', NULL, 23, '2026-03-30 23:38:00', '2026-03-30 06:46:58', '2026-03-30 23:38:00'),
(276, '2025-09-27T19-46 Tax invoice #24851700314516406-24839921762360929.pdf', 'invoices/rENVmDvws3QtUiFsBqvhOyIAVHmI2V4Ntw9aXBa8.pdf', 'done', NULL, 21, '2026-03-30 23:38:00', '2026-03-30 06:46:58', '2026-03-30 23:38:00'),
(277, '2025-09-28T19-05 Tax invoice #24692601400426303-24805418772477900.pdf', 'invoices/EstI939mnZE2XqbSOg12kjpZVfXIk819oiROpB9S.pdf', 'done', NULL, 16, '2026-03-30 23:38:00', '2026-03-30 06:46:58', '2026-03-30 23:38:00'),
(278, '2025-09-30T21-01 Tax invoice #24714168014936303-24774329758920131.pdf', 'invoices/4mgRk75FRYENfe09pV9hJvPYGPdY6EG2NlOFFGrR.pdf', 'done', NULL, 18, '2026-03-30 23:38:00', '2026-03-30 06:46:58', '2026-03-30 23:38:00'),
(279, '2025-10-02T13-48 Tax invoice #24886734834346288-24775217945497974.pdf', 'invoices/3QmYOLS0CznZlhQUPLKoY7ldWA68NClPRVN1Ckyq.pdf', 'done', NULL, 13, '2026-03-30 23:38:00', '2026-03-30 06:47:19', '2026-03-30 23:38:00'),
(280, '2025-10-05T23-27 Tax invoice #24705307365822367-24705307375822366.pdf', 'invoices/ee2KToPYKO4plvYcnosrT3U1BrxJlbtpYvIln9tw.pdf', 'done', NULL, 11, '2026-03-30 23:38:00', '2026-03-30 06:47:19', '2026-03-30 23:38:00'),
(281, '2025-10-08T15-14 Tax invoice #24983757057977402-24906065372413239.pdf', 'invoices/duXE32w7h50MbeB4QSYmgf7jmn29IYEiWBzenELa.pdf', 'done', NULL, 9, '2026-03-30 23:38:01', '2026-03-30 06:47:19', '2026-03-30 23:38:01'),
(282, '2025-10-12T03-03 Tax invoice #25023730543980053-24878576445162123.pdf', 'invoices/fmt1zdX7jxo7rsuRvyuK6r8kZnJuJIxxB5MnoE9t.pdf', 'done', NULL, 11, '2026-03-30 23:38:01', '2026-03-30 06:47:19', '2026-03-30 23:38:01'),
(283, '2025-10-16T06-54 Tax invoice #25044481268571642-24875545158798591.pdf', 'invoices/LxsQuSU37phhcbgNwYuEEdvk6YTiamvCeqn9QweD.pdf', 'done', NULL, 8, '2026-03-30 23:38:01', '2026-03-30 06:47:42', '2026-03-30 23:38:01'),
(284, '2025-10-19T06-13 Tax invoice #24955861677433599-25021679107518531.pdf', 'invoices/Cvt7EedIWNLRN1FK1UcEqM0GnVWHGuILoBTqQ8Zm.pdf', 'done', NULL, 1, '2026-03-30 23:38:01', '2026-03-30 06:47:42', '2026-03-30 23:38:01'),
(285, '2025-10-19T21-57 Tax invoice #24962252563461177-24922597194093383.pdf', 'invoices/DyfVU6cbq58StqR76kPcK8AACxg2t77irdHtLM8y.pdf', 'done', NULL, 7, '2026-03-30 23:38:01', '2026-03-30 06:47:42', '2026-03-30 23:38:01'),
(286, '2025-10-23T20-08 Tax invoice #25004025205950579-24895963013423467.pdf', 'invoices/uKFqA6ROnXizbEvdhcmf594MkZ7tpIwBXQDgUb7R.pdf', 'done', NULL, 6, '2026-03-30 23:38:01', '2026-03-30 06:47:42', '2026-03-30 23:38:01'),
(287, '2025-10-28T13-01 Tax invoice #25007899525563154-25120237094329398.pdf', 'invoices/ZnLCzG18LnVEMOKXz4dRf2kC4khZDEAWuwOpTv7q.pdf', 'done', NULL, 5, '2026-03-30 23:38:01', '2026-03-30 06:47:43', '2026-03-30 23:38:01'),
(288, '2025-11-03T14-31 Tax invoice #25066374186382354-25065294459823659.pdf', 'invoices/cBTi9Hviue4pBceiKMudoPaKBDZiDOul2X3Dqu3g.pdf', 'done', NULL, 4, '2026-03-30 23:38:01', '2026-03-30 06:48:28', '2026-03-30 23:38:01'),
(289, '2025-11-06T14-02 Tax invoice #25251656011187500-25285376757815429.pdf', 'invoices/570w3bFTfJPKdoal2XJdjjsGAJOHjT8m8z9EiApD.pdf', 'done', NULL, 10, '2026-03-30 23:38:01', '2026-03-30 06:48:28', '2026-03-30 23:38:01'),
(290, '2025-11-07T19-13 Tax invoice #25106720432347728-25263567269996374.pdf', 'invoices/MraOJ2xN3FPQaOVPtvDJ0BuATCNQwhoDuR23kx16.pdf', 'done', NULL, 13, '2026-03-30 23:38:01', '2026-03-30 06:48:28', '2026-03-30 23:38:01'),
(291, '2025-11-09T06-53 Tax invoice #25279390821747352-25122485997437838.pdf', 'invoices/cMyfB418ut0SJfsiUJXtn77XB6i4eew2lIPlAlSR.pdf', 'done', NULL, 14, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(292, '2025-11-09T07-03 Tax invoice #25236388159380957-25186719644347805.pdf', 'invoices/TN45UxmgcDmXMvgqTCh1YG5TP0u8j2uNI0Tv7ZqM.pdf', 'done', NULL, 14, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(293, '2025-11-09T07-26 Tax invoice #25292471363772630-25122703997416038.pdf', 'invoices/3uZMP0jKjgQRWzAQE0hZ17NGHsWj7H1XJmIs4VqA.pdf', 'done', NULL, 3, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(294, '2025-11-10T07-00 Tax invoice #25178669191819512-25302089029477530.pdf', 'invoices/MFUeMnCNTvdPSepkKAcLSbbnbOiCh06TXKJ9AIWG.pdf', 'done', NULL, 11, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(295, '2025-11-11T06-27 Tax invoice #25255230377496735-25141341872218917.pdf', 'invoices/9GZr9M5LXZPNBXA6kbClJqyepTT22laEjZaM17vC.pdf', 'done', NULL, 11, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(296, '2025-11-12T06-27 Tax invoice #25216060958080340-25308672488819185.pdf', 'invoices/xVmnbJVAFVlxL4gOuQfrV8VkKYiCHstvmYCsGczn.pdf', 'done', NULL, 11, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(297, '2025-11-13T06-27 Tax invoice #25099099703109796-25351749001178204.pdf', 'invoices/vetf4PBCKDgxTAGHdB2Ql7WnQ36bv7VLhwYhyGsB.pdf', 'done', NULL, 10, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(298, '2025-11-14T06-27 Tax invoice #25170420102644427-25177148388638261.pdf', 'invoices/ptePCd7x8NGO6YhnAt1xkx1qf9UJaFce3REFbuvV.pdf', 'done', NULL, 8, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(299, '2025-11-15T06-27 Tax invoice #25349820538037712-25117511777935255.pdf', 'invoices/IJ1unG7ozOEbVayAEO7PrWoD9G75931JzO40t4Ne.pdf', 'done', NULL, 8, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(300, '2025-11-16T06-13 Tax invoice #25302628676090238-25188428030843634.pdf', 'invoices/uDg9SQQ42UDlnFtVVBpw167KT5PyMU67ihsrwm5D.pdf', 'done', NULL, 9, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(301, '2025-11-17T06-13 Tax invoice #25387779027575201-25354597864226647.pdf', 'invoices/qmiW6UBo0j3XdB387nWALUjKj1XwBlyJlFdYzlXh.pdf', 'done', NULL, 10, '2026-03-30 23:38:02', '2026-03-30 06:48:28', '2026-03-30 23:38:02'),
(302, '2025-11-18T06-13 Tax invoice #25207692332250538-25397123319974105.pdf', 'invoices/2nAfc2PGIQM2ifhMsD8dp3bjBhycnxyEW7znRVVY.pdf', 'done', NULL, 10, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(303, '2025-11-19T18-01 Tax invoice #25334172349602537-25157970267222739.pdf', 'invoices/JUvIKQ2UrhK1yOR0gL915rzaAJ7AZ93lZhpRxSmf.pdf', 'done', NULL, 10, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(304, '2025-11-22T17-22 Tax invoice #25249344268085344-25255051620847937.pdf', 'invoices/saeotVNPxxFNrxRm9yta35ZKwLzjMxuwcMLFizVj.pdf', 'done', NULL, 14, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(305, '2025-11-23T06-02 Tax invoice #25190916663928099-25299790799707350.pdf', 'invoices/foLdJgnjnMnqIVgMIxdBTkooNZp2ccDLtMAtQWAG.pdf', 'done', NULL, 12, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(306, '2025-11-24T05-35 Tax invoice #25326480040371764-25326480073705094.pdf', 'invoices/zEzivK2oCc7SZ8nnD79eAzZ0BmuEcLvakusV470Y.pdf', 'done', NULL, 1, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(307, '2025-11-24T06-40 Tax invoice #25419148824438217-25375836518769453.pdf', 'invoices/LVjlglzqQxx2jKkRA4N1OVerm5KG1CchyDzor8Tw.pdf', 'done', NULL, 13, '2026-03-30 23:38:03', '2026-03-30 06:48:28', '2026-03-30 23:38:03'),
(308, '2025-11-25T06-51 Tax invoice #25440862205600211-25316696521350111.pdf', 'invoices/qjpTuLF4UXYkvdf6ahNPD6OfJ0Nj6PyJwbQwJ75Q.pdf', 'done', NULL, 13, '2026-03-30 23:38:03', '2026-03-30 06:48:56', '2026-03-30 23:38:03'),
(309, '2025-11-26T06-51 Tax invoice #25216201828066249-25343564435329991.pdf', 'invoices/ZLQRPfXxtONvGVh9vboGYDONBudn4ms4KShzCFLb.pdf', 'done', NULL, 12, '2026-03-30 23:38:03', '2026-03-30 06:48:56', '2026-03-30 23:38:03'),
(310, '2025-11-27T06-40 Tax invoice #25351903924496042-25333504793002617.pdf', 'invoices/yVKqPRTsxVPbX3OAIfcRZg0gjMy0nqh12cwrBhlF.pdf', 'done', NULL, 11, '2026-03-30 23:38:03', '2026-03-30 06:48:56', '2026-03-30 23:38:03'),
(311, '2025-11-29T23-50 Tax invoice #25499330449753391-25374039585615809.pdf', 'invoices/cVi64DfZGOUa5JcPYhcFq9RwbC2pgG70LJAy8Tlz.pdf', 'done', NULL, 11, '2026-03-30 23:38:03', '2026-03-30 06:48:56', '2026-03-30 23:38:03'),
(312, '2025-12-05T10-13 Tax invoice #25543682631984839-25510802841939481.pdf', 'invoices/UrxcGxZc7hweE1kHbY0UFOU34WdZ2sMGMOJKyBbB.pdf', 'done', NULL, 13, '2026-03-30 23:38:03', '2026-03-30 06:49:35', '2026-03-30 23:38:03'),
(313, '2025-12-09T18-17 Tax invoice #25438074745878954-25438074789212283.pdf', 'invoices/0tSQjotFmg4vmTGagnMPv6vEQJyWyzmV1vi7l9xo.pdf', 'done', NULL, 12, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(314, '2025-12-13T14-29 Tax invoice #25583677634652001-25363278580025239.pdf', 'invoices/q5dWCJd4PstL0hKDHDikb6SSOqGDYx5BE0Vf4MkU.pdf', 'done', NULL, 15, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(315, '2025-12-16T06-18 Tax invoice #25449411521411950-25639300155756419.pdf', 'invoices/QsEEB1cQMvOrpvnvDKetPkgYulMGGE0vge0gTKTN.pdf', 'done', NULL, 8, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(316, '2025-12-17T06-18 Tax invoice #25464607873225643-25572235569129546.pdf', 'invoices/YRIl3zydmjrHuJRShv64aj0tArGybHFzCHhaJhHA.pdf', 'done', NULL, 7, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(317, '2025-12-18T06-18 Tax invoice #25404222502597513-25467668376252931.pdf', 'invoices/Nqo828lu39nvuyBkeH1PDTEObmJU6Ubr07aqspbH.pdf', 'done', NULL, 6, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(318, '2025-12-19T06-08 Tax invoice #25522626267423801-25666360066383761.pdf', 'invoices/8n3188z7eT4Wuc0PAYEpQLE4fjP1OBZ6eq2BiiMZ.pdf', 'done', NULL, 8, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(319, '2025-12-20T06-08 Tax invoice #25675559615463806-25483094601376974.pdf', 'invoices/5ZR8R0PhUX0yFruCJ06gZzNnpc5yYnCyLOdGEHFz.pdf', 'done', NULL, 9, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(320, '2025-12-21T06-08 Tax invoice #25500622356290861-25684360117917089.pdf', 'invoices/5OyrUFgsYfBeMXJiMKU5T35CWQ9zqEtqL9aoSQes.pdf', 'done', NULL, 9, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(321, '2025-12-22T06-08 Tax invoice #25439441809075582-25692783857074715.pdf', 'invoices/dTzSGR8eF812BDhshMkERUI20NO8qNT0r8fg2gIQ.pdf', 'done', NULL, 8, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(322, '2025-12-23T06-08 Tax invoice #25447883398231423-25576758235343942.pdf', 'invoices/d6lRW4KTK8Y8hYaD33aR9oDsOpvxYKcMJ0fprnUO.pdf', 'done', NULL, 3, '2026-03-30 23:38:04', '2026-03-30 06:49:35', '2026-03-30 23:38:04'),
(323, '2025-12-24T11-50 Tax invoice #25457859577233805-25586807491005683.pdf', 'invoices/HhEGmKo0R6jb02e5IhngTyRsXzE1IS8BH1G59nH3.pdf', 'done', NULL, 12, '2026-03-30 23:38:05', '2026-03-30 06:49:35', '2026-03-30 23:38:05'),
(324, '2025-12-27T17-00 Tax invoice #25552345627785200-25546361698383598.pdf', 'invoices/Jno3S5k0qUc3Q8N2qEnFXQ0qbdWHN6swaWvI9r4p.pdf', 'done', NULL, 11, '2026-03-30 23:38:05', '2026-03-30 06:49:35', '2026-03-30 23:38:05'),
(325, '2025-12-30T08-05 Tax invoice #25723712420648521-25566353569717744.pdf', 'invoices/bESLea0fDiIhD6A0Ag8A6GauYEfAkJ7SKiruIkkv.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:49:35', '2026-03-30 23:38:05'),
(326, '2026-01-01T20-10 Tax invoice #25593205017032594-25744577911895305.pdf', 'invoices/Pkv9PGyvyZ2CaOrAASsfKPinbfLdAvUB6t6XDPBH.pdf', 'done', NULL, 4, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(327, '2026-01-04T19-33 Tax invoice #25724798797206555-25724798837206551.pdf', 'invoices/4st7jkWDiGIb1A7KU5qIE5RpQkHnbpSpvrVJR4hk.pdf', 'done', NULL, 4, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(328, '2026-01-07T19-13 Tax invoice #25794845266868569-25643362252016870.pdf', 'invoices/Pah5wpIXioIoh1fnBx1uFUu55ePbbkJEk7uk9FdE.pdf', 'done', NULL, 4, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(329, '2026-01-10T12-04 Tax invoice #25726823700337394-25658168713869561.pdf', 'invoices/vYgUIXlC44JKhYFFs8A0ByF6E7vznBxJluKX0o51.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(330, '2026-01-13T13-25 Tax invoice #25844087295277699-25683182351368197.pdf', 'invoices/2y0Kmyb7vp93Uy9iZYcuDITC5tckTOXdIW0ydkWm.pdf', 'done', NULL, 6, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(331, '2026-01-15T22-02 Tax invoice #25881879231498504-25714271991592562.pdf', 'invoices/rAaqmcdzXoupyju8vDb1FE1zwqv3VCffou4yugsc.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(332, '2026-01-16T06-07 Tax invoice #25903341536018945-25708124475540651.pdf', 'invoices/5Sckb9Vowa0GGQiLQuUTUzjglXnRbd3MHB2Fx8mG.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(333, '2026-01-17T18-20 Tax invoice #25721934667492965-25731298553223239.pdf', 'invoices/BE54SMdZON85Lhgkg5GKt5hRyAvPM0r5uHjmn3lI.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(334, '2026-01-18T06-09 Tax invoice #25667348722951555-25903573595995734.pdf', 'invoices/AFOppaMiZeGWBE2aGmzIcZxKWde2Qyh9HihcHkTJ.pdf', 'done', NULL, 5, '2026-03-30 23:38:05', '2026-03-30 06:50:12', '2026-03-30 23:38:05'),
(335, '2026-01-19T06-50 Tax invoice #25912783918408035-25735510746135357.pdf', 'invoices/UXynR6u0bXC9R4yeJw8gcdM2SZCsRfWSxn5cN4aC.pdf', 'done', NULL, 4, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(336, '2026-01-20T07-06 Tax invoice #25922204370799323-25861405526879214.pdf', 'invoices/khnJ1B51B6kruLx2YOSzo3GI5gZKpeIowKa0wwiz.pdf', 'done', NULL, 4, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(337, '2026-01-21T06-50 Tax invoice #25754339167585848-25757898753896557.pdf', 'invoices/KK45PZN6e7i2OsZT8qd8UwBcGfYwyWPj3mahLbB6.pdf', 'done', NULL, 5, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(338, '2026-01-22T07-06 Tax invoice #25960986463587785-25926908536995574.pdf', 'invoices/PlJYHWIq9xdIhgqfZo9g72aTIgzAXA8lph2up23q.pdf', 'done', NULL, 5, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(339, '2026-01-23T22-53 Tax invoice #25943435828676178-25723126687373758.pdf', 'invoices/9zjPkYQH3ISdzJ0iBhodxHKc8PusAhAZB5XFSlk3.pdf', 'done', NULL, 6, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(340, '2026-01-25T06-10 Tax invoice #25846958288323929-25956729757346785.pdf', 'invoices/I3VkIQFFB9j2lqocGKVFJt3C34Wo7ZtqLnl2mRIG.pdf', 'done', NULL, 6, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(341, '2026-01-25T11-05 Tax invoice #25867023699650726-25958522003834227.pdf', 'invoices/2yIWaPo6PwusCV4Iv5o2NcAon7zd7RAwQwPGLONJ.pdf', 'done', NULL, 5, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(342, '2026-01-26T06-10 Tax invoice #25808454022174363-25982617601424666.pdf', 'invoices/tA7XHleDvZPe3CJqWG2ybGzoSAyxtTEf0FQtYQob.pdf', 'done', NULL, 6, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(343, '2026-01-27T06-10 Tax invoice #25814728564880241-25818472887839143.pdf', 'invoices/GWLRF7WiWNApWmvoqgDZRdJ96TQdzdLgTQa2fJgp.pdf', 'done', NULL, 6, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(344, '2026-01-28T06-42 Tax invoice #25895375150148914-25834655702887523.pdf', 'invoices/WDIR5CEQMfcfmA1Rnna49RdEhfdXscgubatM47vc.pdf', 'done', NULL, 6, '2026-03-30 23:38:06', '2026-03-30 06:50:12', '2026-03-30 23:38:06'),
(345, '2026-01-29T06-42 Tax invoice #25844397375246689-25905212365831859.pdf', 'invoices/fEleOLVZAWJnMTz6DdHBDnadI0PxUE14yLIr3fge.pdf', 'done', NULL, 4, '2026-03-30 23:38:06', '2026-03-30 06:50:22', '2026-03-30 23:38:06'),
(346, '2026-01-31T23-11 Tax invoice #26023618267324600-25865324239820674.pdf', 'invoices/JNN4jBjxwsvcje7oITyZh9XMXTwRB0YPplDtCPL2.pdf', 'done', NULL, 4, '2026-03-30 23:38:06', '2026-03-30 06:50:22', '2026-03-30 23:38:06'),
(347, '2026-03-30T12-06 Tax invoice #26614143368272084-26614143404938747.pdf', 'invoices/HMMEWGSV0CTShimKmTBJKM7WtfXULTfNbZWN8UCc.pdf', 'done', NULL, 5, '2026-03-30 23:38:07', '2026-03-30 07:11:50', '2026-03-30 23:38:07'),
(348, '2026-03-27T16-56 Tax invoice #26416819974671094-26416820018004423.pdf', 'invoices/dh1QHTLO9WhYHDTeFyvpGV2UjGGLfcSxoRCsJzy1.pdf', 'done', NULL, 6, '2026-03-30 23:38:07', '2026-03-30 07:11:50', '2026-03-30 23:38:07'),
(350, '2026-02-15T05-52 Tax invoice #26165409849812107-26002276302792132.pdf', 'invoices/7B6eAdU45tTDlqga6VvI5hXbP84ZleNkgRFbQRju.pdf', 'done', NULL, 2, '2026-03-30 23:38:07', '2026-03-30 23:19:17', '2026-03-30 23:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `pending_outsource_pdfs`
--

CREATE TABLE `pending_outsource_pdfs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `status` enum('pending','processing','done','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `records_inserted` int(11) NOT NULL DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pending_outsource_pdfs`
--

INSERT INTO `pending_outsource_pdfs` (`id`, `client_name`, `original_filename`, `stored_path`, `status`, `error_message`, `records_inserted`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 'Kitoko Foods', '4983510577.pdf', 'outsource_receipts/R08aon5fa6TAu7JXYsrF1Cho5s7b9XmyPDUG3nHS.pdf', 'done', NULL, 1, '2026-03-31 05:30:54', '2026-03-31 05:30:49', '2026-03-31 05:30:54'),
(2, 'Kitoko Foods', '4962754097.pdf', 'outsource_receipts/uuWTeC5HesSa9PeszVbITYpJImIT8TXKWmCRpbON.pdf', 'done', NULL, 1, '2026-03-31 05:30:54', '2026-03-31 05:30:49', '2026-03-31 05:30:54'),
(3, 'Kitoko Foods', '4945564632.pdf', 'outsource_receipts/7TUkVXCWZMhOXJMpvKKlZ7BeGtWEZ1IMyRPYilGD.pdf', 'done', NULL, 1, '2026-03-31 05:30:54', '2026-03-31 05:30:49', '2026-03-31 05:30:54'),
(4, 'Kitoko Foods', '4919720207.pdf', 'outsource_receipts/AYIJ87UmKT7cIdN01ArcQt8GgFkbm3XP5DDF5j6r.pdf', 'done', NULL, 1, '2026-03-31 05:30:54', '2026-03-31 05:30:49', '2026-03-31 05:30:54'),
(5, 'Kitoko Foods', '4901335423.pdf', 'outsource_receipts/PtuzWzyvM9vExTPnyxwVtAmIU6ZAMg2TOzRNnB0J.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(6, 'Kitoko Foods', '4882703329.pdf', 'outsource_receipts/S5MTHDsZy8dBO8LLXMKK1UlCwC2wYCG8wJNPXYmq.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(7, 'Kitoko Foods', '4857260805.pdf', 'outsource_receipts/Ef0Xkhh86xQ9Kvuya3Cl3ARFJnSJwDYIm9knhHzj.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(8, 'Kitoko Foods', '4838506528.pdf', 'outsource_receipts/SEF5AttDJkS1UvruRQ4dRZIvvKjj1HF53q3wRi6K.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(9, 'Kitoko Foods', '4818243220.pdf', 'outsource_receipts/JlzqiaUjRN7RYJL2fUT9t9fBbkrG87r4eUTfoz9u.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(10, 'Kitoko Foods', '4798509453.pdf', 'outsource_receipts/8tCJ9yR4crJnxmpVJq2sMt8PLWl9ZVIRJgKjrbgj.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(11, 'Kitoko Foods', '4773224953.pdf', 'outsource_receipts/OPUsiRmomAnXlvu2gGf1pihthEnAG3wCM6zD7qKT.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(12, 'Kitoko Foods', '4755949829.pdf', 'outsource_receipts/CP20X1LCwTc0ARRp0iPohgvuWVHLhQka3KbS7Mqx.pdf', 'done', NULL, 1, '2026-03-31 05:30:55', '2026-03-31 05:30:49', '2026-03-31 05:30:55'),
(13, 'Kitoko Foods', '4732220507.pdf', 'outsource_receipts/SjgVGEMtk8Sk0et9n5Tir22OWjF7C1mFfxWREDrJ.pdf', 'done', NULL, 1, '2026-03-31 05:30:56', '2026-03-31 05:30:49', '2026-03-31 05:30:56'),
(14, 'Kitoko Foods', '4710276711.pdf', 'outsource_receipts/WR6wZ9AVwh4XU1U31csUQnwQsunaTkgJ8I1AV1eN.pdf', 'done', NULL, 1, '2026-03-31 05:30:56', '2026-03-31 05:30:49', '2026-03-31 05:30:56'),
(15, 'Kitoko Foods', '4693254477.pdf', 'outsource_receipts/dyTsKAfGq6yxuJ1qKM4C82m1QP1JaQzLxyaXb1eF.pdf', 'done', NULL, 1, '2026-03-31 05:30:56', '2026-03-31 05:30:49', '2026-03-31 05:30:56'),
(16, 'Sangani Hospital', 'Outsource- Recipts-2025-2026_11_.pdf', 'outsource_receipts/jcHSBKSj292QXqI0dO7toZKd901WceJGEypBgm1u.pdf', 'done', NULL, 1, '2026-03-31 05:37:09', '2026-03-31 05:36:35', '2026-03-31 05:37:09'),
(17, 'Sangani Hospital', '5499311159.pdf', 'outsource_receipts/j6Y3fryTD0RW9q8znAW5XMSJQs9uNZSaVnU8o9rF.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(18, 'Sangani Hospital', '5368632655.pdf', 'outsource_receipts/mXB5INHed5axqA9M0imddpmzmaGmwajXb36T3uO2.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(19, 'Sangani Hospital', '5349353560.pdf', 'outsource_receipts/Ci7tqXZAJ4C9peAsd0pofjxmHUnQkyXGq6sYXYIe.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(20, 'Sangani Hospital', '5321643876.pdf', 'outsource_receipts/cg5Yx4oEZmgpnd6I0i5vYB3Ytj4akss3ywOfakOW.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(21, 'Sangani Hospital', '5295908536.pdf', 'outsource_receipts/F20mxZOVhA6IlHBYBHF2N0OzBbHpJ5gPvKcM0IEh.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(22, 'Sangani Hospital', '5266326261.pdf', 'outsource_receipts/u1mohecsIlozpWCJOIhHkcMxbiRpJoJL1GueZzQM.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(23, 'Sangani Hospital', '5238004214.pdf', 'outsource_receipts/sr6NPq80dsHpIB9CRpib4arbmYMecpVD4v4srucZ.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(24, 'Sangani Hospital', '5214677634.pdf', 'outsource_receipts/xryth6Ev5TU1Y4ETSew1DdyJWao790EzDQaw7JTS.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(25, 'Sangani Hospital', '5193038389.pdf', 'outsource_receipts/I4MzHaKRrI7F2n9jFXpn35my6hij7pimzCsig04N.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(26, 'Sangani Hospital', '5166216785.pdf', 'outsource_receipts/GuQiI5pJbnYnAMQMLsRW2CIybY8eFW7Fkn8BckRV.pdf', 'done', NULL, 1, '2026-03-31 05:37:10', '2026-03-31 05:36:35', '2026-03-31 05:37:10'),
(27, 'Sangani Hospital', '5147432689.pdf', 'outsource_receipts/w9lLZjp0WEIfx2s4a0wEFp1MlV4ZcVA1msTgL9P3.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(28, 'Sangani Hospital', '5123546479.pdf', 'outsource_receipts/EJmcV3UmO1Hr571KafGFXUeCko4looW0j01V2Nwm.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(29, 'Sangani Hospital', '5093487954.pdf', 'outsource_receipts/F6FUKiiIAVOWhBJGMsGrd9tInd9kGEoQ7fcmzj7B.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(30, 'Sangani Hospital', '5071042835.pdf', 'outsource_receipts/wD4fAj465INiwz3qB6HVHANoc9A3HL3z2o1yO5HN.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(31, 'Sangani Hospital', '5051573821.pdf', 'outsource_receipts/y45ImAWidg2gYvTKkFYUbwNKs2uWP45gsoZ6T8C2.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(32, 'Sangani Hospital', '5027512205.pdf', 'outsource_receipts/1RgxhsH5SB5DtQLPlsF6hghLzpalGT1M9ZQ2c9bn.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(33, 'Sangani Hospital', '5009483088.pdf', 'outsource_receipts/OVZEIT73Y7DYa57rvGvdbLuUbv922vdTGMRU2KC3.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(34, 'Sangani Hospital', '4985540897.pdf', 'outsource_receipts/ysh9cwV0VYv8blTOYqgPYQFuWr3cG2VfuDnWAAgj.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(35, 'Sangani Hospital', '4962641939.pdf', 'outsource_receipts/qjfEeZL7N1GwOmpJQk1r2VvpSQ7nxKdAHV7PmIDm.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:35', '2026-03-31 05:37:11'),
(36, 'Sangani Hospital', '4943306167.pdf', 'outsource_receipts/R4HVPONCQiIZuZWcJz5I5JCWT0i2BGKU6bASXsSv.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:58', '2026-03-31 05:37:11'),
(37, 'Sangani Hospital', '4919857971.pdf', 'outsource_receipts/o0ruMxQhipWHZcGHyGI6TTEV1VYrGptGFmQdp7Iq.pdf', 'done', NULL, 1, '2026-03-31 05:37:11', '2026-03-31 05:36:58', '2026-03-31 05:37:11'),
(38, 'Sangani Hospital', '4902777118.pdf', 'outsource_receipts/3iBbAZVu58BYNwW24L6xvwegeGfE6ynTayweM5Qj.pdf', 'done', NULL, 1, '2026-03-31 05:37:12', '2026-03-31 05:36:58', '2026-03-31 05:37:12'),
(39, 'Sangani Hospital', '4880883645.pdf', 'outsource_receipts/9IKIZWc6rf10d5XoiM4IrTuKIc673OGq0CAStpY4.pdf', 'done', NULL, 1, '2026-03-31 05:37:12', '2026-03-31 05:36:58', '2026-03-31 05:37:12'),
(40, 'Sangani Hospital', '4861678182.pdf', 'outsource_receipts/HGoPUNsItKC4FUeSo7F247Ee9m2zfrpiXX43BMIs.pdf', 'done', NULL, 1, '2026-03-31 05:37:12', '2026-03-31 05:36:58', '2026-03-31 05:37:12'),
(41, 'Sangani Hospital', '4841440076.pdf', 'outsource_receipts/vmlzLQLFkA9GiBcmPtDUOpl4alDxGYxsebsjNjsV.pdf', 'done', NULL, 1, '2026-03-31 05:37:12', '2026-03-31 05:36:58', '2026-03-31 05:37:12'),
(42, 'Sangani Hospital', '4815927355.pdf', 'outsource_receipts/IBiMkQpZTMRcWOzRYKihzv60CUOL8qOZ7sdlT4MQ.pdf', 'done', NULL, 1, '2026-03-31 05:37:12', '2026-03-31 05:36:58', '2026-03-31 05:37:12'),
(43, 'Vervax', '5369010351.pdf', 'outsource_receipts/z57vxRwyjM60eWlfht2gxKa6yN3Zm7TlVQszJBFx.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:03', '2026-03-31 05:44:08'),
(44, 'Vervax', '5348784527.pdf', 'outsource_receipts/qZ0HFfe33iqTWVyiqF6KJTzD0mvmp4fJV7iPJOIW.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(45, 'Vervax', '5318076743.pdf', 'outsource_receipts/wveaTTWAYe0wPawKGQMryxaJD0OliNT8bh1e20xw.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(46, 'Vervax', '5293342903.pdf', 'outsource_receipts/ePd0QE0Nm46l9RyjoTkZVc3DEqzQrHIuoCZLKFn2.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(47, 'Vervax', '5270040992.pdf', 'outsource_receipts/DYtV94hTQ6mClRH6BPj8FY5I5SJHLiXiW9p8A9Iv.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(48, 'Vervax', '5237884022.pdf', 'outsource_receipts/APIzZNHQzLO7lKqg1vyaYTktI6DmfqqGOZjmt15W.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(49, 'Vervax', '5215477122.pdf', 'outsource_receipts/jft2eWSavBYTpFOI6G2iWOvcAyQrPjdd2Ewf0rz9.pdf', 'done', NULL, 1, '2026-03-31 05:44:08', '2026-03-31 05:43:04', '2026-03-31 05:44:08'),
(50, 'Vervax', '5192961139.pdf', 'outsource_receipts/8fPFtRxHLOI0dRP71mdyyMcgRSokJSYNyEaDrqKH.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(51, 'Vervax', '5165030589.pdf', 'outsource_receipts/04KM3lQjkqTaxfEttH3TBHkqoBOFb5MbxDdMwoz7.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(52, 'Vervax', '5140751871.pdf', 'outsource_receipts/RYQjVyxOVlikcky8UWb1daUaRi3EjPJmu5MEyh0V.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(53, 'Vervax', '5123493392.pdf', 'outsource_receipts/LUgkeKxF75kE3Z43zLEtHoga0WUXPg9zrcFCMCfO.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(54, 'Vervax', '5099105900.pdf', 'outsource_receipts/wyFO2fVnyVqah8y0h8HppNahsxFbEqBolNUslldq.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(55, 'Vervax', '5072354243.pdf', 'outsource_receipts/pXFoCvbkx5exMV6HSIk5c5DVfm7WopzHpz0HAGNC.pdf', 'done', NULL, 1, '2026-03-31 05:44:09', '2026-03-31 05:43:04', '2026-03-31 05:44:09'),
(56, 'Vervax', '5052792442.pdf', 'outsource_receipts/fZCd320D2ubKrwQO0l3UNGbeUv5gdSZenvlWBVP1.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(57, 'Vervax', '5027550776.pdf', 'outsource_receipts/VYwQJnhJdSe8YOCkHEbHNxHujg87WGFgzR4B0d9n.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(58, 'Vervax', '5005490520.pdf', 'outsource_receipts/f5MDIaJElqmfTFDnhas8dcgJ5MCAdqqUFS1hDWS8.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(59, 'Vervax', '4990891270.pdf', 'outsource_receipts/hwY8v6In3HIyRrHf58cqfWuz55O28Zi7a0VbMAVJ.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(60, 'Vervax', '4962860392.pdf', 'outsource_receipts/DJdwxctNjwIViAiy0naz3Q3l13UJNWWgkNo2J4nC.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(61, 'Vervax', '4946493057.pdf', 'outsource_receipts/7UHOSNbdWMa7L235ua1CdqdlFpSLnWg9WgZsmgbQ.pdf', 'done', NULL, 1, '2026-03-31 05:44:10', '2026-03-31 05:43:04', '2026-03-31 05:44:10'),
(62, 'Vervax', '4924684425.pdf', 'outsource_receipts/n05xRdjn2NcmjYbWbggFdxV6gwJAeABHjVODIZT9.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:04', '2026-03-31 05:44:11'),
(63, 'Vervax', '4900351637.pdf', 'outsource_receipts/A42JsekeKxjbHHQTw4OgrGTdh7AbWpgtePaep2Ln.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(64, 'Vervax', '4882177034.pdf', 'outsource_receipts/Lg0K4FIWqqmq1z5TvaY26QSeBN93unfiNU4TZ9b5.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(65, 'Vervax', '4862940036.pdf', 'outsource_receipts/NObGVZL2PmvYwcnoiSdAJjhK5DeUkaX8bg6CTa59.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(66, 'Vervax', '4840337105.pdf', 'outsource_receipts/O7N7GQtvuYwU3GfW9TnHZeQxkoxzLKr7lFpDJWXT.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(67, 'Vervax', '4815461537.pdf', 'outsource_receipts/o7NrlLfB01iAZznkfCQjQpeGY96xS5MlhvYn9Vpb.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(68, 'Vervax', '4798868076.pdf', 'outsource_receipts/SIYiDeRrOm956p1WalJ5HvQDvfKEfK3rLnYxHzIM.pdf', 'done', NULL, 1, '2026-03-31 05:44:11', '2026-03-31 05:43:43', '2026-03-31 05:44:11'),
(69, 'Vervax', '4776182967.pdf', 'outsource_receipts/NOsF2wa8uNOY7kxY5KLcUpjiVn2hFBuQnS3WLC6b.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(70, 'Vervax', '4754349938.pdf', 'outsource_receipts/UaVfAwfNLEoWd3bYL3yCqYDPJkQK3rnJtLkkdpoe.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(71, 'Vervax', '4731367898.pdf', 'outsource_receipts/lpXikUaqLZkRYwO3XRFp6Z32NUzW2FHm63fyM2A9.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(72, 'Vervax', '4713258802.pdf', 'outsource_receipts/0WOkJn03uuz2Iv4si96O8REG58y1p7cPNVsRZpKU.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(73, 'Vervax', '4695016610.pdf', 'outsource_receipts/CIt0LsxiapJBXZw5BpVlp4lLtCM8JZX43YeUpDxK.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(74, 'Vervax', '4674453972.pdf', 'outsource_receipts/VifDfdu2IoQJ5zVyksV3QIIQ8DgGNa5MBzz5TMhg.pdf', 'done', NULL, 1, '2026-03-31 05:44:12', '2026-03-31 05:43:43', '2026-03-31 05:44:12'),
(75, 'Vervax', '4654105377.pdf', 'outsource_receipts/Km9pmv5gQYGkc4uUKzEDCoVBnkkK00HIRrwmNY57.pdf', 'done', NULL, 1, '2026-03-31 05:44:13', '2026-03-31 05:43:43', '2026-03-31 05:44:13'),
(76, 'Vervax', '4634708491.pdf', 'outsource_receipts/4riSzJtxAVaK0IuD6hig56U5p6CaPkTBBitVbIeP.pdf', 'done', NULL, 1, '2026-03-31 05:44:13', '2026-03-31 05:43:43', '2026-03-31 05:44:13');

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
('bY5Re0QgXHJnMRojtNj4Ui5TX8PMTY5GKkl1LISo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1loMjlheDZVRmFkTGhuRTZxbXh3MmF1UUNhR1MwQ2JwZ0lJVnVNRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9nb2RhZGR5L2V4cG9ydCI7czo1OiJyb3V0ZSI7czoxNDoiZ29kYWRkeS5leHBvcnQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775046444);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_statement_pdfs`
--
ALTER TABLE `bank_statement_pdfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `godaddy_receipts`
--
ALTER TABLE `godaddy_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostinger_invoice_records`
--
ALTER TABLE `hostinger_invoice_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_invoice_date` (`invoice_date`),
  ADD KEY `idx_pdf_filename` (`pdf_filename`);

--
-- Indexes for table `hostinger_invoice_summaries`
--
ALTER TABLE `hostinger_invoice_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pdf_filename` (`pdf_filename`),
  ADD KEY `idx_invoice_date` (`invoice_date`);

--
-- Indexes for table `hostinger_pending_pdfs`
--
ALTER TABLE `hostinger_pending_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `invoice_records`
--
ALTER TABLE `invoice_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_subtotals`
--
ALTER TABLE `invoice_subtotals`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outsource_receipts`
--
ALTER TABLE `outsource_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pending_godaddy_files`
--
ALTER TABLE `pending_godaddy_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_invoice_pdfs`
--
ALTER TABLE `pending_invoice_pdfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_outsource_pdfs`
--
ALTER TABLE `pending_outsource_pdfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_statement_pdfs`
--
ALTER TABLE `bank_statement_pdfs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `godaddy_receipts`
--
ALTER TABLE `godaddy_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `hostinger_invoice_records`
--
ALTER TABLE `hostinger_invoice_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `hostinger_invoice_summaries`
--
ALTER TABLE `hostinger_invoice_summaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `hostinger_pending_pdfs`
--
ALTER TABLE `hostinger_pending_pdfs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `invoice_records`
--
ALTER TABLE `invoice_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3487;

--
-- AUTO_INCREMENT for table `invoice_subtotals`
--
ALTER TABLE `invoice_subtotals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `outsource_receipts`
--
ALTER TABLE `outsource_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `pending_godaddy_files`
--
ALTER TABLE `pending_godaddy_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pending_invoice_pdfs`
--
ALTER TABLE `pending_invoice_pdfs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;

--
-- AUTO_INCREMENT for table `pending_outsource_pdfs`
--
ALTER TABLE `pending_outsource_pdfs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
