-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 03:28 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mirtrading`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(155) NOT NULL,
  `name` varchar(40) NOT NULL,
  `mobile` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `country` varchar(155) DEFAULT NULL,
  `ComName` varchar(155) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`user_id`, `email`, `password`, `name`, `mobile`, `address`, `pic`, `country`, `ComName`, `role_id`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 'imranertaza12@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Syed Imran Ertaza', 1924329315, 'Noapara, Abhaynagar, Jessore', 'profile_1664976903_39bd3bf1ddc2da4682f0.jpg', NULL, NULL, 1, '1', '2023-08-06 10:35:27', 1, 1, '2023-08-06 10:35:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_user`
--

CREATE TABLE `affiliate_user` (
  `affiliate_user_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` bigint(20) NOT NULL,
  `address` text NOT NULL,
  `commission` float NOT NULL COMMENT 'Commission (%)',
  `balance` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `affiliate_user`
--

INSERT INTO `affiliate_user` (`affiliate_user_id`, `sch_id`, `name`, `phone`, `address`, `commission`, `balance`) VALUES
(1, 3, 'murad hossion', 132454648, '121 King St, Melbourne VIC 3000, United Kingdom\r\nNowapara', 10, 100);

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `bank_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) NOT NULL,
  `account_no` bigint(20) NOT NULL,
  `balance` double UNSIGNED NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`bank_id`, `sch_id`, `name`, `account_no`, `balance`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(18, 2, 'IBBL', 1323213123213213, 39852, '1', '2026-02-15 06:53:24', 1, 1, '2026-02-28 15:48:19', NULL, NULL),
(19, 2, 'IFIC', 45345435345435, 570, '1', '2026-02-15 06:53:46', 1, 1, '2026-02-24 14:47:43', NULL, NULL),
(20, 3, 'IFIC Bank', 23424234, 500, '1', '2026-03-31 05:34:36', 2, NULL, '2026-03-31 05:34:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bank_deposit`
--

CREATE TABLE `bank_deposit` (
  `dep_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `amount` double UNSIGNED NOT NULL,
  `commont` text DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bank_deposit`
--

INSERT INTO `bank_deposit` (`dep_id`, `sch_id`, `bank_id`, `amount`, `commont`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(16, 2, 19, 40, '', '2026-02-17 07:28:52', 1, NULL, '2026-02-22 11:59:43', NULL, NULL),
(17, 2, 18, 50000, 'fghfghfhfhg', '2026-02-24 02:45:58', 1, NULL, '2026-02-24 14:45:58', NULL, NULL),
(18, 3, 20, 500, '', '2026-03-31 05:34:49', 2, NULL, '2026-03-31 17:34:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bank_withdraw`
--

CREATE TABLE `bank_withdraw` (
  `wthd_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `amount` double UNSIGNED NOT NULL,
  `commont` text DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) NOT NULL,
  `image` varchar(155) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `sch_id`, `name`, `image`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(7, 2, 'Louis Vuitton', 'brand_1771340390_5b0ba3c93458c199c60b.png', '2026-02-15 07:44:41', 1, 1, '2026-02-17 20:59:50', NULL, NULL),
(8, 3, 'New Brand', 'brand_1774694654_6f75da079ec4221f208a.jpg', '2026-03-28 04:44:13', 2, NULL, '2026-03-28 16:44:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chaque`
--

CREATE TABLE `chaque` (
  `chaque_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `chaque_number` int(11) NOT NULL,
  `to_name` varchar(155) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `from_name` varchar(155) DEFAULT NULL,
  `from` int(11) DEFAULT NULL,
  `from_loan_provider` int(11) DEFAULT NULL,
  `amount` float UNSIGNED NOT NULL,
  `issue_date` date NOT NULL,
  `account_number` int(11) DEFAULT NULL,
  `status` enum('Pending','Bounce','Approved') NOT NULL DEFAULT 'Pending',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `chaque`
--

INSERT INTO `chaque` (`chaque_id`, `sch_id`, `chaque_number`, `to_name`, `to`, `from_name`, `from`, `from_loan_provider`, `amount`, `issue_date`, `account_number`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 321, NULL, 1, NULL, 30, NULL, 10, '0000-00-00', NULL, 'Approved', '2026-02-17 07:36:06', NULL, 1, '2026-02-22 12:03:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `commission`
--

CREATE TABLE `commission` (
  `commission_id` int(11) NOT NULL,
  `affiliate_user_id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `commission` float NOT NULL COMMENT 'commission (%)',
  `commission_amount` float NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `commission`
--

INSERT INTO `commission` (`commission_id`, `affiliate_user_id`, `sales_id`, `commission`, `commission_amount`, `date`) VALUES
(2, 1, 29, 10, 300, '2026-03-31'),
(3, 1, 30, 10, 150, '2026-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `commission_pay`
--

CREATE TABLE `commission_pay` (
  `commission_pay_id` int(11) NOT NULL,
  `affiliate_user_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `status` enum('Paid','Unpaid','Cancel') NOT NULL DEFAULT 'Paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_name` varchar(55) NOT NULL,
  `father_name` varchar(55) DEFAULT NULL,
  `mother_name` varchar(55) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `age` int(10) UNSIGNED DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `pic` varchar(55) DEFAULT NULL,
  `nid` varchar(155) DEFAULT NULL,
  `cus_type_id` int(11) NOT NULL,
  `affiliate_user_id` int(11) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `balance` double NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `sch_id`, `customer_name`, `father_name`, `mother_name`, `address`, `present_address`, `age`, `mobile`, `pic`, `nid`, `cus_type_id`, `affiliate_user_id`, `status`, `balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(30, 2, 'Rahim', NULL, NULL, NULL, NULL, NULL, 1666778899, NULL, NULL, 18, NULL, '1', 310.5, '2026-02-15 06:48:02', 1, 1, '2026-03-08 16:24:57', NULL, NULL),
(31, 2, 'Hannan', NULL, NULL, NULL, NULL, NULL, 16667788999, NULL, NULL, 19, NULL, '1', 1351.57, '2026-02-15 06:48:16', 1, 1, '2026-02-28 15:47:08', NULL, NULL),
(33, 3, 'Sumon', NULL, NULL, NULL, NULL, NULL, 1923149437, NULL, NULL, 21, 1, '1', 4500, '2026-03-08 04:20:01', 2, 2, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_type`
--

CREATE TABLE `customer_type` (
  `cus_type_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `type_name` varchar(155) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer_type`
--

INSERT INTO `customer_type` (`cus_type_id`, `sch_id`, `type_name`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 1, 'regular', '2023-09-27 12:06:05', 1, NULL, '2023-09-27 12:06:05', NULL, NULL),
(18, 2, 'Regular', '2026-02-15 06:47:35', 1, NULL, '2026-02-15 18:47:35', NULL, NULL),
(19, 2, 'VIP', '2026-02-15 06:47:38', 1, NULL, '2026-02-15 18:47:38', NULL, NULL),
(20, 2, 'CIP', '2026-02-17 07:14:40', 1, NULL, '2026-02-17 19:14:40', NULL, NULL),
(21, 3, 'Reguler', '2026-03-08 04:19:51', 2, NULL, '2026-03-08 16:19:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) NOT NULL,
  `salary` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `balance` float UNSIGNED DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `sch_id`, `name`, `salary`, `age`, `balance`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(9, 2, 'Khan', 444444, 32, NULL, '1', '2026-02-15 07:02:17', 1, NULL, '2026-02-15 19:02:17', NULL, NULL),
(10, 2, 'Jabbar', 12345, 33, NULL, '1', '2026-02-15 07:02:41', 1, NULL, '2026-02-15 19:02:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_product`
--

CREATE TABLE `exchange_product` (
  `exchange_pro_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exchange_product`
--

INSERT INTO `exchange_product` (`exchange_pro_id`, `sch_id`, `customer_id`, `customer_name`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(3, 3, 33, NULL, '2026-03-28 12:02:27', NULL, NULL, '2026-03-28 12:02:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_stock_relation`
--

CREATE TABLE `exchange_stock_relation` (
  `exchange_stock_relation_id` int(11) NOT NULL,
  `exchange_pro_id` int(11) NOT NULL,
  `stock_transfer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `exchange_stock_relation`
--

INSERT INTO `exchange_stock_relation` (`exchange_stock_relation_id`, `exchange_pro_id`, `stock_transfer_id`) VALUES
(1, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `gen_settings`
--

CREATE TABLE `gen_settings` (
  `settings_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `label` varchar(155) NOT NULL,
  `value` varchar(155) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gen_settings`
--

INSERT INTO `gen_settings` (`settings_id`, `sch_id`, `label`, `value`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(11, 2, 'barcode_img_size', '100', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(12, 2, 'barcode_type', 'C128A', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(13, 2, 'business_type', 'Ownership business', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(14, 2, 'currency', 'BDT', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(15, 2, 'currency_before_symbol', '৳', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(16, 2, 'currency_after_symbol', '/-', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(17, 2, 'running_year', '2018-2019', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(18, 2, 'disable_frontend', '0', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(19, 2, 'phone_code', '880', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(20, 2, 'country', 'Bangladesh', '2023-10-02 19:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(21, 3, 'barcode_img_size', '100', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(22, 3, 'barcode_type', 'C128A', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(23, 3, 'business_type', 'Ownership business', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(24, 3, 'currency', 'BDT', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(25, 3, 'currency_before_symbol', '৳', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(26, 3, 'currency_after_symbol', '/-', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(27, 3, 'running_year', '2018-2019', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(28, 3, 'disable_frontend', '0', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(29, 3, 'phone_code', '880', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(30, 3, 'country', 'Bangladesh', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(31, 3, 'edit_permission_expire', '10', '2025-10-22 18:43:01', NULL, NULL, '2025-10-22 18:43:01', NULL, NULL),
(32, 2, 'edit_permission_expire', '10', '2025-10-22 18:43:13', NULL, NULL, '2025-10-22 18:43:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gen_settings_super`
--

CREATE TABLE `gen_settings_super` (
  `settings_id_sup` int(10) UNSIGNED NOT NULL,
  `label` varchar(155) NOT NULL,
  `value` varchar(155) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gen_settings_super`
--

INSERT INTO `gen_settings_super` (`settings_id_sup`, `label`, `value`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 'loading_message', 'Please wait until it is processing...', '2023-09-02 10:22:44', 0, NULL, '2023-09-02 10:22:44', NULL, NULL),
(2, 'site_title', 'Shohoz Hishab | Accounting management system', '2023-09-02 10:22:44', 0, NULL, '2023-09-02 10:22:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `entire_sale_discount` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `final_amount` double UNSIGNED NOT NULL,
  `profit` double NOT NULL COMMENT 'Profit on individual product',
  `nagad_paid` double UNSIGNED DEFAULT NULL,
  `bank_paid` double UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `chaque_paid` double UNSIGNED DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `due` double UNSIGNED DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext DEFAULT NULL,
  `payment_method` longtext DEFAULT NULL,
  `payment_details` longtext DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` longtext DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `sch_id`, `customer_id`, `pymnt_type_id`, `customer_name`, `amount`, `entire_sale_discount`, `vat`, `final_amount`, `profit`, `nagad_paid`, `bank_paid`, `bank_id`, `chaque_paid`, `chaque_id`, `due`, `creation_timestamp`, `payment_timestamp`, `payment_method`, `payment_details`, `status`, `timestamp`, `year`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(24, 2, 30, NULL, NULL, 550, 0, 0, 550, 200, 0, 0, 0, 10, 1, 540, NULL, NULL, NULL, NULL, '1', '2026-02-17 13:36:06', NULL, '2026-02-17 07:36:06', 1, 1, '2026-02-17 19:36:06', NULL, NULL),
(25, 2, 31, NULL, NULL, 1078, 3, 2, 1066.57, 366.57, 555, 0, 0, 0, NULL, 511.57, NULL, NULL, NULL, NULL, '1', '2026-02-22 05:40:51', NULL, '2026-02-22 11:40:51', 1, 1, '2026-02-22 11:40:51', NULL, NULL),
(26, 2, 30, NULL, NULL, 666, 2, 2, 665.73, 221.73, 665.73, 0, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '1', '2026-03-02 09:31:16', NULL, '2026-03-02 03:31:16', 1, 1, '2026-03-02 15:31:16', NULL, NULL),
(27, 3, 33, NULL, NULL, 1500, 0, 0, 1500, 100, 1500, 0, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '1', '2026-03-08 10:20:39', NULL, '2026-03-08 04:20:39', 2, 2, '2026-03-08 16:20:39', NULL, NULL),
(33, 3, 33, NULL, NULL, 3000, 0, 0, 3000, 200, 0, 0, 0, 0, NULL, 3000, NULL, NULL, NULL, NULL, '1', '2026-03-31 06:11:44', NULL, '2026-03-31 12:11:44', 2, 2, '2026-03-31 12:11:44', NULL, NULL),
(34, 3, 33, NULL, NULL, 1500, 0, 0, 1500, 100, 0, 0, 0, 0, NULL, 1500, NULL, NULL, NULL, NULL, '1', '2026-03-31 06:15:07', NULL, '2026-03-31 12:15:07', 2, 2, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_item`
--

CREATE TABLE `invoice_item` (
  `inv_item` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` float UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` float UNSIGNED NOT NULL,
  `discount` int(11) DEFAULT NULL,
  `final_price` float UNSIGNED NOT NULL,
  `profit` float NOT NULL COMMENT 'Profit on individual product',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `invoice_item`
--

INSERT INTO `invoice_item` (`inv_item`, `invoice_id`, `sch_id`, `prod_id`, `title`, `price`, `quantity`, `total_price`, `discount`, `final_price`, `profit`, `date`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(27, 24, 2, 21, NULL, 550, 1, 550, 0, 550, 200, '2026-02-17 19:36:06', '2026-02-17 07:36:06', 1, NULL, '2026-02-17 19:36:06', NULL, NULL),
(28, 25, 2, 21, NULL, 550, 2, 1100, 2, 1078, 378, '2026-02-22 11:40:51', '2026-02-22 11:40:51', 1, NULL, '2026-02-22 11:40:51', NULL, NULL),
(29, 26, 2, 23, NULL, 666, 1, 666, 0, 666, 222, '2026-03-02 15:31:16', '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(30, 27, 3, 24, NULL, 1500, 1, 1500, 0, 1500, 100, '2026-03-08 16:20:39', '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(36, 33, 3, 24, NULL, 1500, 2, 3000, 0, 3000, 200, '2026-03-31 12:11:44', '2026-03-31 12:11:44', 2, NULL, '2026-03-31 12:11:44', NULL, NULL),
(37, 34, 3, 24, NULL, 1500, 1, 1500, 0, 1500, 100, '2026-03-31 12:15:07', '2026-03-31 12:15:07', 2, NULL, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lc`
--

CREATE TABLE `lc` (
  `lc_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `lc_name` varchar(155) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL COMMENT 'Rest of the balance of the LC',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `ledg_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `service_invoice_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger`
--

INSERT INTO `ledger` (`ledg_id`, `sch_id`, `customer_id`, `invoice_id`, `trans_id`, `rtn_sale_id`, `chaque_id`, `service_invoice_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(94, 2, 30, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 0, -999.5, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(95, 2, 31, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 0, -50, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(96, 2, 30, 24, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 550, -449.5, '2026-02-17 07:36:06', 1, NULL, '2026-02-17 19:36:06', NULL, NULL),
(97, 2, 31, NULL, 86, NULL, NULL, NULL, 'ADVANCE', 'Dr.', 110, -60, '2026-02-17 07:50:58', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(98, 2, 31, 25, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 1066.57, 1006.57, '2026-02-22 11:40:51', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(99, 2, 31, 25, NULL, NULL, NULL, NULL, 'Sales Cash Pay', 'Cr.', 555, 571.57, '2026-02-22 11:40:51', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(100, 2, 30, NULL, NULL, NULL, 1, NULL, 'Chaque Cash Approved', 'Cr.', 10, -459.5, '2026-02-22 12:03:06', NULL, NULL, '2026-02-22 12:03:06', NULL, NULL),
(101, 2, 31, NULL, 91, NULL, NULL, NULL, 'due pay', 'Dr.', 900, 1551.57, '2026-02-24 12:13:41', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(102, 2, 30, NULL, 95, NULL, NULL, NULL, 'paid', 'Dr.', 770, -1129.5, '2026-02-25 12:02:18', 1, NULL, '2026-02-28 15:48:18', NULL, NULL),
(103, 2, 30, 26, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 665.73, 976.23, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(104, 2, 30, 26, NULL, NULL, NULL, NULL, 'Sales Cash Pay', 'Cr.', 665.73, 310.5, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(105, 3, 33, 27, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 1500, 1500, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(106, 3, 33, 27, NULL, NULL, NULL, NULL, 'Sales Cash Pay', 'Cr.', 1500, 0, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(107, 2, 30, NULL, NULL, 1, NULL, NULL, 'Return Sale Product', 'Cr.', 550, -239.5, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(108, 2, 30, NULL, NULL, 1, NULL, NULL, 'Return Sale Product cash Pay', 'Dr.', 550, 310.5, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(109, 3, 33, 33, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 3000, 3000, '2026-03-31 12:11:44', 2, NULL, '2026-03-31 12:11:44', NULL, NULL),
(110, 3, 33, 34, NULL, NULL, NULL, NULL, 'Sales Cash Due', 'Dr.', 1500, 4500, '2026-03-31 12:15:07', 2, NULL, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_bank`
--

CREATE TABLE `ledger_bank` (
  `ledgBank_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `service_invoice_id` int(11) DEFAULT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `commission_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float UNSIGNED NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_bank`
--

INSERT INTO `ledger_bank` (`ledgBank_id`, `sch_id`, `bank_id`, `service_invoice_id`, `money_receipt_id`, `purchase_id`, `trans_id`, `rtn_purchase_id`, `rtn_sale_id`, `chaque_id`, `invoice_id`, `commission_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(43, 2, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 10, 10, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(44, 2, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 30, 30, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(45, 2, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Dr.', 40, 70, '2026-02-17 07:28:52', NULL, NULL, '2026-02-22 11:59:43', NULL, NULL),
(46, 2, 18, NULL, NULL, NULL, 94, NULL, NULL, NULL, NULL, NULL, 'Withdraw', 'Cr.', 500, 500, '2026-02-24 02:44:57', 1, NULL, '2026-02-24 14:47:43', NULL, NULL),
(47, 2, 19, NULL, NULL, NULL, 94, NULL, NULL, NULL, NULL, NULL, 'Withdraw', 'Dr.', 500, 570, '2026-02-24 02:44:58', 1, NULL, '2026-02-24 14:47:43', NULL, NULL),
(48, 2, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Dr.', 50000, 49510, '2026-02-24 02:45:58', NULL, NULL, '2026-02-24 14:47:43', NULL, NULL),
(49, 2, 18, NULL, NULL, NULL, 95, NULL, NULL, NULL, NULL, NULL, 'paid', 'Cr.', 770, 50180, '2026-02-25 12:02:18', 1, NULL, '2026-02-28 15:48:18', NULL, NULL),
(50, 2, 18, NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, NULL, 'Purchase Bank Pay', 'Cr.', 8888, 41292, '2026-02-25 12:35:29', 1, NULL, '2026-02-28 15:48:18', NULL, NULL),
(51, 3, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Dr.', 500, 500, '2026-03-31 05:34:49', NULL, NULL, '2026-03-31 17:34:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_capital`
--

CREATE TABLE `ledger_capital` (
  `capital_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_capital`
--

INSERT INTO `ledger_capital` (`capital_id`, `sch_id`, `trans_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(25, 2, NULL, 'Previous balance', 'Dr.', -100222, -100222, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_discount`
--

CREATE TABLE `ledger_discount` (
  `discount_ledg_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_discount`
--

INSERT INTO `ledger_discount` (`discount_ledg_id`, `sch_id`, `invoice_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(10, 2, NULL, 'Previous discount', 'Dr.', 27.5, 27.5, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(11, 2, 25, 'Sale discount', 'Dr.', 33.43, 60.93, '2026-02-22 11:40:51', NULL, NULL, '2026-02-22 11:40:51', NULL, NULL),
(12, 2, 26, 'Sale discount', 'Dr.', 0.27, 61.2, '2026-03-02 03:31:16', NULL, NULL, '2026-03-02 15:31:16', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_employee`
--

CREATE TABLE `ledger_employee` (
  `ledg_emp_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float UNSIGNED NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_expense`
--

CREATE TABLE `ledger_expense` (
  `ledg_exp_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `memo_number` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_expense`
--

INSERT INTO `ledger_expense` (`ledg_exp_id`, `sch_id`, `memo_number`, `trans_id`, `chaque_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(8, 2, 55, 88, NULL, 'decoration', 'Dr.', 40, 40, '2026-02-17 08:55:53', 1, NULL, '2026-02-17 20:58:43', NULL, NULL),
(9, 2, 55, 89, NULL, 'Hospitality', 'Dr.', 10, 50, '2026-02-17 08:57:10', 1, NULL, '2026-02-17 20:58:43', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_expense_commission`
--

CREATE TABLE `ledger_expense_commission` (
  `ledg_exp_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `commission_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_expense_commission`
--

INSERT INTO `ledger_expense_commission` (`ledg_exp_id`, `sch_id`, `commission_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(3, 3, 1, 'Commission Pay', 'Dr.', 200, 200, '2026-03-31 06:59:40', 2, NULL, '2026-03-31 18:59:40', NULL, NULL),
(4, 3, 1, 'Commission Pay', 'Dr.', 50, 250, '2026-03-31 07:01:04', 2, NULL, '2026-03-31 19:01:04', NULL, NULL),
(5, 3, 1, 'Commission Pay', 'Dr.', 100, 350, '2026-03-31 07:07:01', 2, NULL, '2026-03-31 19:07:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_loan`
--

CREATE TABLE `ledger_loan` (
  `ledg_loan_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `loan_pro_id` int(11) NOT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_loan`
--

INSERT INTO `ledger_loan` (`ledg_loan_id`, `sch_id`, `loan_pro_id`, `money_receipt_id`, `trans_id`, `chaque_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(17, 2, 13, NULL, NULL, NULL, 'Previous balance', 'Dr.', 0, 0, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(18, 2, 13, NULL, 93, NULL, 'borrow', 'Cr.', 12000, -12000, '2026-02-24 12:40:09', 1, NULL, '2026-02-24 12:42:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_nagodan`
--

CREATE TABLE `ledger_nagodan` (
  `ledg_nagodan_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `service_invoice_id` int(11) DEFAULT NULL,
  `loan_pro_id` int(11) DEFAULT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `commission_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float UNSIGNED NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_nagodan`
--

INSERT INTO `ledger_nagodan` (`ledg_nagodan_id`, `sch_id`, `customer_id`, `bank_id`, `service_invoice_id`, `loan_pro_id`, `money_receipt_id`, `purchase_id`, `trans_id`, `rtn_purchase_id`, `rtn_sale_id`, `chaque_id`, `invoice_id`, `commission_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(97, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 1500, 24000, '2025-11-04 18:39:32', NULL, NULL, '2025-11-04 18:39:32', NULL, NULL),
(124, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 400, 50100, '2025-12-25 12:16:17', NULL, NULL, '2025-12-25 12:16:17', NULL, NULL),
(128, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 11000, 41000, '2025-12-25 12:20:43', NULL, NULL, '2025-12-25 12:20:43', NULL, NULL),
(129, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 10000, 39000, '2025-12-25 12:20:58', NULL, NULL, '2025-12-25 12:20:58', NULL, NULL),
(130, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 11000, 41000, '2025-12-25 12:21:31', NULL, NULL, '2025-12-25 12:21:31', NULL, NULL),
(131, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 10000, 39000, '2025-12-25 12:21:52', NULL, NULL, '2025-12-25 12:21:52', NULL, NULL),
(132, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 11000, 41000, '2025-12-25 12:22:28', NULL, NULL, '2025-12-25 12:22:28', NULL, NULL),
(133, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 10000, 39000, '2025-12-25 12:22:51', NULL, NULL, '2025-12-25 12:22:51', NULL, NULL),
(134, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 11000, 41000, '2025-12-25 12:23:58', NULL, NULL, '2025-12-25 12:23:58', NULL, NULL),
(135, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 10000, 39000, '2025-12-25 12:31:06', NULL, NULL, '2025-12-25 12:31:06', NULL, NULL),
(136, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 12000, 42000, '2025-12-25 12:33:11', NULL, NULL, '2025-12-25 12:33:11', NULL, NULL),
(137, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 10000, 38000, '2025-12-25 12:33:23', NULL, NULL, '2025-12-25 12:33:23', NULL, NULL),
(141, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 11000, 41000, '2025-12-25 12:36:00', NULL, NULL, '2025-12-25 12:36:00', NULL, NULL),
(152, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 98322, 98322, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(153, 2, NULL, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Cr.', 50, 98272, '2026-02-17 07:28:52', NULL, NULL, '2026-02-17 19:28:52', NULL, NULL),
(154, 2, NULL, NULL, NULL, NULL, NULL, NULL, 86, NULL, NULL, NULL, NULL, NULL, 'ADVANCE', 'Cr.', 110, 98272, '2026-02-17 07:50:58', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(155, 2, NULL, NULL, NULL, NULL, NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, 'Furniture sale', 'Dr.', 900, 99052, '2026-02-17 08:53:59', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(156, 2, NULL, NULL, NULL, NULL, NULL, NULL, 88, NULL, NULL, NULL, NULL, NULL, 'decoration', 'Cr.', 40, 99212, '2026-02-17 08:55:53', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(157, 2, NULL, NULL, NULL, NULL, NULL, NULL, 89, NULL, NULL, NULL, NULL, NULL, 'Hospitality', 'Cr.', 10, 99202, '2026-02-17 08:57:10', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(158, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, NULL, 'Sales Cash Pay', 'Dr.', 555, 99657, '2026-02-22 11:40:51', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(159, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'Cr.', 40, 98262, '2026-02-22 11:59:43', NULL, NULL, '2026-02-22 11:59:43', NULL, NULL),
(160, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 'Chaque Cash Approved', 'Dr.', 10, 99687, '2026-02-22 12:03:06', NULL, NULL, '2026-02-25 11:50:59', NULL, NULL),
(161, 2, NULL, NULL, NULL, NULL, NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, 'vat pay', 'Cr.', 20, 99807, '2026-02-22 12:05:31', 1, NULL, '2026-02-25 12:42:20', NULL, NULL),
(162, 2, NULL, NULL, NULL, NULL, NULL, NULL, 91, NULL, NULL, NULL, NULL, NULL, 'due pay', 'Cr.', 900, 98907, '2026-02-24 12:13:41', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(163, 2, NULL, NULL, NULL, NULL, NULL, NULL, 92, NULL, NULL, NULL, NULL, NULL, 'due receive', 'Dr.', 100, 98867, '2026-02-24 12:14:31', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(164, 2, NULL, NULL, NULL, NULL, NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, 'borrow', 'Dr.', 12000, 110867, '2026-02-24 12:40:09', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(165, 2, NULL, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Cr.', 50000, 61207, '2026-02-24 02:45:58', NULL, NULL, '2026-02-28 15:47:08', NULL, NULL),
(166, 2, NULL, NULL, NULL, NULL, NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, 'Chair sale', 'Dr.', 700, 61587, '2026-02-26 10:44:32', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(167, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 26, NULL, 'Sales Cash Pay', 'Dr.', 665.73, 62252.7, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(168, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 27, NULL, 'Sales Cash Pay', 'Dr.', 1500, 1500, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(169, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 'Return Sale Cash Pay', 'Cr.', 550, 61702.7, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(170, 3, NULL, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bank Cash Deposit', 'Cr.', 500, 1000, '2026-03-31 05:34:49', NULL, NULL, '2026-03-31 17:34:49', NULL, NULL),
(171, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Commission Pay', 'Cr.', 200, 800, '2026-03-31 06:59:40', 2, NULL, '2026-03-31 18:59:40', NULL, NULL),
(172, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Commission Pay', 'Cr.', 50, 750, '2026-03-31 07:01:04', 2, NULL, '2026-03-31 19:01:04', NULL, NULL),
(173, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Commission Pay', 'Cr.', 100, 650, '2026-03-31 07:07:01', 2, NULL, '2026-03-31 19:07:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_other_sales`
--

CREATE TABLE `ledger_other_sales` (
  `ledg_oth_sales_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_other_sales`
--

INSERT INTO `ledger_other_sales` (`ledg_oth_sales_id`, `sch_id`, `trans_id`, `chaque_id`, `particulars`, `trangaction_type`, `amount`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(8, 2, 87, NULL, 'Furniture sale', 'Dr.', 900, '2026-02-17 08:53:59', 1, NULL, '2026-02-24 14:57:13', NULL, NULL),
(9, 2, 96, NULL, 'Chair sale', 'Dr.', 700, '2026-02-26 10:44:32', 1, NULL, '2026-02-26 10:46:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_profit`
--

CREATE TABLE `ledger_profit` (
  `profit_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_profit`
--

INSERT INTO `ledger_profit` (`profit_id`, `sch_id`, `invoice_id`, `rtn_sale_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(24, 2, 24, NULL, 'Sales profit get', 'Cr.', 200, -200, '2026-02-17 07:36:06', 1, NULL, '2026-02-17 19:36:06', NULL, NULL),
(25, 2, 25, NULL, 'Sales profit get', 'Cr.', 378, -1545.66, '2026-02-22 11:40:51', 1, NULL, '2026-02-22 11:40:51', NULL, NULL),
(26, 2, 26, NULL, 'Sales profit get', 'Cr.', 222, -2354.34, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(27, 3, 27, NULL, 'Sales profit get', 'Cr.', 100, -100, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(28, 2, NULL, 1, 'Return Profit', 'Dr.', 200, -2154.34, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(29, 3, 33, NULL, 'Sales profit get', 'Cr.', 200, -300, '2026-03-31 12:11:44', 2, NULL, '2026-03-31 12:11:44', NULL, NULL),
(30, 3, 34, NULL, 'Sales profit get', 'Cr.', 100, -400, '2026-03-31 12:15:07', 2, NULL, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_purchase`
--

CREATE TABLE `ledger_purchase` (
  `ledgPurch_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_purchase`
--

INSERT INTO `ledger_purchase` (`ledgPurch_id`, `sch_id`, `purchase_id`, `rtn_purchase_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(19, 2, NULL, NULL, 'Previous balance', 'Dr.', 3500, 3500, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(20, 2, 29, NULL, 'New purchase amount', 'Dr.', 2664, 6164, '2026-02-24 04:27:06', 1, NULL, '2026-02-24 16:27:06', NULL, NULL),
(21, 2, 30, NULL, 'New purchase amount', 'Dr.', 9768, 15932, '2026-02-25 12:35:29', 1, NULL, '2026-02-25 12:35:29', NULL, NULL),
(22, 3, 31, NULL, 'New purchase amount', 'Dr.', 140000, 140000, '2026-03-08 04:19:33', 2, NULL, '2026-03-08 16:19:33', NULL, NULL),
(24, 3, 32, NULL, 'New purchase amount', 'Dr.', 28000, 168000, '2026-03-31 12:20:11', 2, NULL, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_sales`
--

CREATE TABLE `ledger_sales` (
  `ledgSale_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_sales`
--

INSERT INTO `ledger_sales` (`ledgSale_id`, `sch_id`, `invoice_id`, `rtn_sale_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(26, 2, NULL, NULL, 'Previous balance', 'Dr.', -522.5, -522.5, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(27, 2, 24, NULL, 'New Sale amount', 'Cr.', 550, -1072.5, '2026-02-17 07:36:06', 1, NULL, '2026-02-17 19:36:06', NULL, NULL),
(28, 2, 25, NULL, 'New Sale amount', 'Cr.', 1046.57, -2119.07, '2026-02-22 11:40:51', 1, NULL, '2026-02-22 11:40:51', NULL, NULL),
(29, 2, 26, NULL, 'New Sale amount', 'Cr.', 652.73, -2771.8, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(30, 3, 27, NULL, 'New Sale amount', 'Cr.', 1500, -1500, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(31, 2, NULL, 1, 'return sale', 'Dr.', 550, -2221.8, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(33, 3, 33, NULL, 'New Sale amount', 'Cr.', 3000, -4500, '2026-03-31 12:11:44', 2, NULL, '2026-03-31 12:11:44', NULL, NULL),
(34, 3, 34, NULL, 'New Sale amount', 'Cr.', 1500, -6000, '2026-03-31 12:15:07', 2, NULL, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_service_charge`
--

CREATE TABLE `ledger_service_charge` (
  `ledger_service_charge_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `service_invoice_id` int(11) DEFAULT NULL,
  `particulars` text NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ledger_service_charge`
--

INSERT INTO `ledger_service_charge` (`ledger_service_charge_id`, `sch_id`, `service_invoice_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 1, 'Service new create', 'Cr.', 250, -250, '2026-02-15 07:37:41', 1, NULL, '2026-02-15 19:37:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_stock`
--

CREATE TABLE `ledger_stock` (
  `stock_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_stock`
--

INSERT INTO `ledger_stock` (`stock_id`, `sch_id`, `invoice_id`, `rtn_sale_id`, `purchase_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(46, 2, NULL, NULL, NULL, 'Previous balance', 'Dr.', 3150, 3150, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(47, 2, 24, NULL, NULL, 'Sale amount', 'Cr.', 350, 2800, '2026-02-17 07:36:06', 1, NULL, '2026-02-17 19:36:06', NULL, NULL),
(48, 2, 25, NULL, NULL, 'Sale amount', 'Cr.', 700, 2100, '2026-02-22 11:40:51', 1, NULL, '2026-02-22 11:40:51', NULL, NULL),
(49, 2, NULL, NULL, 29, 'New purchase amount', 'Dr.', 2664, 4764, '2026-02-24 04:27:06', 1, NULL, '2026-02-24 16:27:06', NULL, NULL),
(50, 2, NULL, NULL, 30, 'New purchase amount', 'Dr.', 9768, 14532, '2026-02-25 12:35:29', 1, NULL, '2026-02-25 12:35:29', NULL, NULL),
(51, 2, 26, NULL, NULL, 'Sale amount', 'Cr.', 444, 14088, '2026-03-02 03:31:16', 1, NULL, '2026-03-02 15:31:16', NULL, NULL),
(52, 3, NULL, NULL, 31, 'New purchase amount', 'Dr.', 140000, 140000, '2026-03-08 04:19:33', 2, NULL, '2026-03-08 16:19:33', NULL, NULL),
(53, 3, 27, NULL, NULL, 'Sale amount', 'Cr.', 1400, 138600, '2026-03-08 04:20:39', 2, NULL, '2026-03-08 16:20:39', NULL, NULL),
(54, 2, NULL, 1, NULL, 'Return sale', 'Dr.', 350, 14438, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL),
(55, 3, 33, NULL, NULL, 'Sale amount', 'Cr.', 2800, 135800, '2026-03-31 12:11:44', 2, NULL, '2026-03-31 12:11:44', NULL, NULL),
(56, 3, 34, NULL, NULL, 'Sale amount', 'Cr.', 1400, 134400, '2026-03-31 12:15:07', 2, NULL, '2026-03-31 12:15:07', NULL, NULL),
(58, 3, NULL, NULL, 32, 'New purchase amount', 'Dr.', 28000, 162400, '2026-03-31 12:20:11', 2, NULL, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_suppliers`
--

CREATE TABLE `ledger_suppliers` (
  `ledg_sup_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_suppliers`
--

INSERT INTO `ledger_suppliers` (`ledg_sup_id`, `sch_id`, `supplier_id`, `money_receipt_id`, `purchase_id`, `trans_id`, `rtn_purchase_id`, `chaque_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(40, 2, 21, NULL, NULL, NULL, NULL, NULL, 'Previous balance', 'Dr.', 10, 10, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(41, 2, 22, NULL, NULL, 92, NULL, NULL, 'due receive', 'Cr.', 100, -100, '2026-02-24 12:14:31', 1, NULL, '2026-02-24 12:38:48', NULL, NULL),
(42, 2, 21, NULL, 29, NULL, NULL, NULL, 'Purchase Cash Due', 'Cr.', 2664, -2654, '2026-02-24 04:27:06', 1, NULL, '2026-02-24 16:27:06', NULL, NULL),
(43, 2, 22, NULL, 30, NULL, NULL, NULL, 'Purchase Cash Due', 'Cr.', 9768, -9868, '2026-02-25 12:35:29', 1, NULL, '2026-02-25 12:35:29', NULL, NULL),
(44, 2, 22, NULL, 30, NULL, NULL, NULL, 'Purchase Bank Pay', 'Dr.', 8888, -980, '2026-02-25 12:35:29', 1, NULL, '2026-02-25 12:35:29', NULL, NULL),
(45, 3, 23, NULL, 31, NULL, NULL, NULL, 'Purchase Cash Due', 'Cr.', 140000, -140000, '2026-03-08 04:19:33', 2, NULL, '2026-03-08 16:19:33', NULL, NULL),
(47, 3, 23, NULL, 32, NULL, NULL, NULL, 'Purchase Cash Due', 'Cr.', 28000, -168000, '2026-03-31 12:20:11', 2, NULL, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ledger_vat`
--

CREATE TABLE `ledger_vat` (
  `ledg_vat_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `vat_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `particulars` text COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Cr.',
  `amount` float NOT NULL,
  `rest_balance` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ledger_vat`
--

INSERT INTO `ledger_vat` (`ledg_vat_id`, `sch_id`, `vat_id`, `invoice_id`, `trans_id`, `particulars`, `trangaction_type`, `amount`, `rest_balance`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(14, 2, 1, NULL, NULL, 'Previous balance', 'Dr.', 0, 0, '2026-02-15 08:59:20', 1, NULL, '2026-02-15 20:59:20', NULL, NULL),
(15, 2, 1, 25, NULL, 'Sale Vat Earn ', 'Cr.', 20.91, -20.91, '2026-02-22 11:40:51', NULL, NULL, '2026-02-22 11:40:51', NULL, NULL),
(16, 2, 1, NULL, 90, 'vat pay', 'Dr.', 20, -0.91, '2026-02-22 12:05:31', 1, NULL, '2026-02-25 12:42:20', NULL, NULL),
(17, 2, 1, 26, NULL, 'Sale Vat Earn ', 'Cr.', 13.05, -13.96, '2026-03-02 03:31:16', NULL, NULL, '2026-03-02 15:31:16', NULL, NULL),
(18, 2, 1, 24, NULL, 'Return Sale Vat return', 'Dr.', 0, -13.96, '2026-03-08 16:24:57', 2, NULL, '2026-03-08 16:24:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE `license` (
  `lic_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `lic_key` text COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `license`
--

INSERT INTO `license` (`lic_id`, `sch_id`, `lic_key`, `start_date`, `end_date`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(2, 2, '651ac4dd948c7', '2023-10-02', '2026-10-06', '1', '2023-10-02 19:25:49', NULL, NULL, '2023-10-02 19:25:49', NULL, NULL),
(3, 3, '651b8c5336dc2', '2023-10-03', '2030-12-31', '1', '2023-10-03 09:36:51', NULL, NULL, '2023-10-03 09:36:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_provider`
--

CREATE TABLE `loan_provider` (
  `loan_pro_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `balance` double NOT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `loan_provider`
--

INSERT INTO `loan_provider` (`loan_pro_id`, `sch_id`, `name`, `phone`, `address`, `balance`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(13, 2, 'Abdur Rahman', 1929649440, 'Dhaka', -12000, '1', '2026-02-15 08:54:33', 1, 1, '2026-02-28 15:26:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(3, '2023-08-06-034752', 'App\\Database\\Migrations\\Admin', 'default', 'App', 1691296280, 1),
(4, '2023-08-06-050108', 'App\\Database\\Migrations\\Bank', 'default', 'App', 1691591167, 2),
(5, '2023-08-29-130121', 'App\\Database\\Migrations\\CreateBankDepositTable', 'default', 'App', 1693314325, 3),
(6, '2023-08-29-130632', 'App\\Database\\Migrations\\CreateBankWithdrawTable', 'default', 'App', 1693314497, 4),
(13, '2023-08-30-051417', 'App\\Database\\Migrations\\CreateBrandTable', 'default', 'App', 1693375265, 5),
(14, '2023-08-30-052222', 'App\\Database\\Migrations\\CreateChaqueTable', 'default', 'App', 1693375265, 5),
(16, '2023-08-30-061545', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1693376996, 6),
(17, '2023-08-30-113923', 'App\\Database\\Migrations\\CreateCustomerTypeTable', 'default', 'App', 1693396448, 7),
(19, '2023-08-31-035553', 'App\\Database\\Migrations\\CreateEmployeeTable', 'default', 'App', 1693454627, 8),
(20, '2023-09-02-040549', 'App\\Database\\Migrations\\CreateGeneralSettingsTable', 'default', 'App', 1693627691, 9),
(21, '2023-09-02-041941', 'App\\Database\\Migrations\\CreateGeneralSettingsSuper', 'default', 'App', 1693628522, 10),
(23, '2023-09-02-052043', 'App\\Database\\Migrations\\CreateInvoiceTable', 'default', 'App', 1693633627, 11),
(25, '2023-09-02-055851', 'App\\Database\\Migrations\\CreateInvoiceItemTable', 'default', 'App', 1693636404, 12),
(27, '2023-09-02-064307', 'App\\Database\\Migrations\\CreateLcTable', 'default', 'App', 1693637263, 13),
(28, '2023-09-02-064843', 'App\\Database\\Migrations\\CreateLedgerTable', 'default', 'App', 1693637827, 14),
(29, '2023-09-02-114902', 'App\\Database\\Migrations\\CreateLedgerBankTable', 'default', 'App', 1693655916, 15),
(30, '2023-09-02-120046', 'App\\Database\\Migrations\\CreateLedgerCapitalTable', 'default', 'App', 1693656198, 16),
(31, '2023-09-02-120438', 'App\\Database\\Migrations\\CreateLedgerDiscountTable', 'default', 'App', 1693656612, 17),
(32, '2023-09-02-121229', 'App\\Database\\Migrations\\CreateLedgerEmployeeTable', 'default', 'App', 1693656947, 18),
(33, '2023-09-02-121818', 'App\\Database\\Migrations\\CreateLedgerExpenseTable', 'default', 'App', 1693657241, 19),
(34, '2023-09-02-122309', 'App\\Database\\Migrations\\CreateLedgerLoanTable', 'default', 'App', 1693657539, 20),
(36, '2023-09-02-131316', 'App\\Database\\Migrations\\CreateLedgerCashTable', 'default', 'App', 1693661001, 21),
(37, '2023-09-02-132543', 'App\\Database\\Migrations\\CreateLedgerOtherSalesTable', 'default', 'App', 1693661572, 22),
(38, '2023-09-02-133416', 'App\\Database\\Migrations\\CreateLedgerProfitTable', 'default', 'App', 1693661976, 23),
(39, '2023-09-02-134140', 'App\\Database\\Migrations\\CreateLedgerPurchaseTable', 'default', 'App', 1693662392, 24),
(40, '2023-09-02-134827', 'App\\Database\\Migrations\\CreateLedgerSalesTable', 'default', 'App', 1693662675, 25),
(41, '2023-09-02-135213', 'App\\Database\\Migrations\\CreateLedgerStockTable', 'default', 'App', 1693662893, 26),
(42, '2023-09-02-135626', 'App\\Database\\Migrations\\CreateLedgerSuppliersTable', 'default', 'App', 1693663221, 27),
(43, '2023-09-02-140208', 'App\\Database\\Migrations\\CreateLedgerVatTable', 'default', 'App', 1693663488, 28),
(44, '2023-09-05-043422', 'App\\Database\\Migrations\\CreateLicenseTable', 'default', 'App', 1693888703, 29),
(46, '2023-09-05-044711', 'App\\Database\\Migrations\\CreateAccountHeadTable', 'default', 'App', 1693890038, 30),
(47, '2023-09-05-050915', 'App\\Database\\Migrations\\CreateMoneyReceiptTable', 'default', 'App', 1693890807, 31),
(48, '2023-09-06-034629', 'App\\Database\\Migrations\\CreatePackageTable', 'default', 'App', 1693972265, 32),
(49, '2023-09-09-035823', 'App\\Database\\Migrations\\CreatePaymentTypeTable', 'default', 'App', 1694232050, 33),
(50, '2023-09-09-040712', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1694233613, 34),
(51, '2023-09-09-130211', 'App\\Database\\Migrations\\CreateProductCategoryTable', 'default', 'App', 1694264990, 35),
(52, '2023-09-09-133650', 'App\\Database\\Migrations\\CreatePurchaseTable', 'default', 'App', 1694266921, 36),
(53, '2023-09-09-134518', 'App\\Database\\Migrations\\CreatePurchaseItemTable', 'default', 'App', 1694267376, 37),
(54, '2023-09-16-033102', 'App\\Database\\Migrations\\CreateReturnPurchaseTable', 'default', 'App', 1694835937, 38),
(55, '2023-09-16-034720', 'App\\Database\\Migrations\\CreateReturnPurchaseItemTable', 'default', 'App', 1694836411, 39),
(57, '2023-09-16-041635', 'App\\Database\\Migrations\\CreateReturnSaleTable', 'default', 'App', 1694838173, 40),
(58, '2023-09-16-042450', 'App\\Database\\Migrations\\CreateReturnSaleItemTable', 'default', 'App', 1694838555, 41),
(59, '2023-09-16-103104', 'App\\Database\\Migrations\\CreateRoleTable', 'default', 'App', 1694860525, 42),
(60, '2023-09-16-103740', 'App\\Database\\Migrations\\CreateSalesTable', 'default', 'App', 1694860842, 43),
(61, '2023-09-16-104244', 'App\\Database\\Migrations\\CreateShopsTable', 'default', 'App', 1694861576, 44),
(62, '2023-09-16-115122', 'App\\Database\\Migrations\\CreateStoresTable', 'default', 'App', 1694865560, 45),
(63, '2023-10-01-042738', 'App\\Database\\Migrations\\CreateSuppliersTable', 'default', 'App', 1696134719, 46),
(64, '2023-10-01-043628', 'App\\Database\\Migrations\\CreateTransactionTable', 'default', 'App', 1696135481, 47),
(65, '2023-10-01-045006', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1696136118, 48),
(66, '2023-10-01-045745', 'App\\Database\\Migrations\\CreateVatRegisterTable', 'default', 'App', 1696136443, 49),
(67, '2023-10-01-050151', 'App\\Database\\Migrations\\CreateWarrantyManageTable', 'default', 'App', 1696136714, 50);

-- --------------------------------------------------------

--
-- Table structure for table `money_receipt`
--

CREATE TABLE `money_receipt` (
  `money_receipt_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `package_id` int(10) UNSIGNED NOT NULL,
  `package_name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `package_all_permission` text COLLATE utf8_unicode_ci NOT NULL,
  `package_admin_permission` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_type`
--

CREATE TABLE `payment_type` (
  `pymnt_type_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `type_name` varchar(115) COLLATE utf8_unicode_ci NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prod_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `unit` int(11) NOT NULL,
  `purchase_price` float NOT NULL,
  `selling_price` float NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Last purchase price will be added here.',
  `supplier_id` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `serial_number` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `picture` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `warranty` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prod_cat_id` int(11) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prod_id`, `sch_id`, `name`, `unit`, `purchase_price`, `selling_price`, `purchase_date`, `supplier_id`, `size`, `serial_number`, `brand_id`, `picture`, `warranty`, `barcode`, `prod_cat_id`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(21, 2, 'Neverfull', 1, 350, 550, '2026-02-15 19:22:12', 21, NULL, NULL, NULL, 'product_1771913322_e517e8a1c0951ece00d7.jpg', NULL, NULL, 19, '2026-02-15 07:22:12', 1, 1, '2026-03-08 16:24:57', NULL, NULL),
(22, 2, 'Keepall', 1, 444, 555, '2026-02-24 16:27:06', 21, NULL, NULL, NULL, NULL, NULL, NULL, 19, '2026-02-24 04:27:06', 1, NULL, '2026-02-24 16:27:06', NULL, NULL),
(23, 2, 'Alma', 1, 444, 666, '2026-02-25 12:35:29', 22, NULL, NULL, NULL, NULL, NULL, NULL, 19, '2026-02-25 12:35:29', 1, 1, '2026-03-02 15:31:16', NULL, NULL),
(24, 3, 'twinmos 4gb ', 1, 1400, 1500, '2026-03-08 16:19:33', 23, NULL, NULL, NULL, 'product_1773657772_0fbd9bd6590d56dc59b9.jpg', NULL, NULL, 21, '2026-03-08 04:19:33', 2, 2, '2026-03-16 16:42:52', NULL, NULL),
(26, 3, 'test', 1, 1400, 1500, '2026-03-31 12:20:11', 23, NULL, NULL, NULL, NULL, NULL, NULL, 21, '2026-03-31 12:20:11', 2, NULL, '2026-03-31 12:20:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `prod_cat_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `parent_pro_cat` int(11) NOT NULL,
  `product_category` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`prod_cat_id`, `sch_id`, `parent_pro_cat`, `product_category`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(18, 2, 0, 'Bags', '1', '2026-02-15 07:17:29', 1, NULL, '2026-02-15 19:17:29', NULL, NULL),
(19, 2, 18, 'Women', '1', '2026-02-15 07:17:41', 1, 1, '2026-02-24 15:38:41', NULL, NULL),
(20, 3, 0, 'Ram', '1', '2026-03-08 04:18:46', 2, NULL, '2026-03-08 16:18:46', NULL, NULL),
(21, 3, 20, 'DDR4', '1', '2026-03-08 04:18:59', 2, NULL, '2026-03-08 16:18:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_stock_relation`
--

CREATE TABLE `product_stock_relation` (
  `product_stock_relation_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_stock_relation`
--

INSERT INTO `product_stock_relation` (`product_stock_relation_id`, `store_id`, `product_id`, `quantity`) VALUES
(1, 2, 22, 20),
(2, 2, 23, 20),
(3, 2, 21, 20),
(4, 3, 24, 15),
(6, 4, 24, 1),
(7, 5, 24, 1),
(9, 3, 26, 20);

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `nagad_paid` double DEFAULT NULL,
  `bank_paid` double DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `due` double DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`purchase_id`, `sch_id`, `supplier_id`, `amount`, `nagad_paid`, `bank_paid`, `bank_id`, `due`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(29, 2, 21, 2664, 0, 0, 0, 2664, '2026-02-24 04:12:16', 1, 1, '2026-02-24 16:27:06', NULL, NULL),
(30, 2, 22, 9768, 0, 8888, 18, 880, '2026-02-25 12:25:51', 1, 1, '2026-02-25 12:35:29', NULL, NULL),
(31, 3, 23, 140000, 0, 0, 0, 140000, '2026-03-08 04:19:08', 2, 2, '2026-03-08 16:19:33', NULL, NULL),
(32, 3, 23, 28000, 0, 0, 0, 14000, '2026-03-31 12:15:33', 2, 2, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item`
--

CREATE TABLE `purchase_item` (
  `purchase_item_id` int(10) UNSIGNED NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `purchase_price` float NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` float NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `purchase_item`
--

INSERT INTO `purchase_item` (`purchase_item_id`, `purchase_id`, `prod_id`, `purchase_price`, `quantity`, `total_price`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(19, 29, 22, 444, 6, 2664, '2026-02-24 04:27:06', 1, NULL, '2026-02-24 16:27:06', NULL, NULL),
(20, 30, 23, 444, 22, 9768, '2026-02-25 12:35:29', 1, NULL, '2026-02-25 12:35:29', NULL, NULL),
(21, 31, 24, 1400, 100, 140000, '2026-03-08 04:19:33', 2, NULL, '2026-03-08 16:19:33', NULL, NULL),
(22, 32, 26, 1400, 20, 28000, '2026-03-31 12:20:11', 2, NULL, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `return_purchase`
--

CREATE TABLE `return_purchase` (
  `rtn_purchase_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float UNSIGNED NOT NULL,
  `rtn_profit` float NOT NULL COMMENT 'Profit on the sale',
  `nagad_paid` float UNSIGNED DEFAULT NULL,
  `bank_paid` float UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_method` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_details` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_purchase_item`
--

CREATE TABLE `return_purchase_item` (
  `rtn_purchase_item_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `rtn_purchase_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` float UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` float UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_sale`
--

CREATE TABLE `return_sale` (
  `rtn_sale_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float NOT NULL,
  `rtn_profit` float NOT NULL COMMENT 'Profit on the sale',
  `nagad_paid` float UNSIGNED DEFAULT NULL,
  `bank_paid` float UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_method` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_details` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `return_sale`
--

INSERT INTO `return_sale` (`rtn_sale_id`, `sch_id`, `customer_id`, `pymnt_type_id`, `customer_name`, `amount`, `rtn_profit`, `nagad_paid`, `bank_paid`, `bank_id`, `creation_timestamp`, `payment_timestamp`, `payment_method`, `payment_details`, `status`, `timestamp`, `year`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 30, NULL, NULL, 550, 200, 550, 0, 0, NULL, NULL, NULL, NULL, '1', '2026-03-08 10:24:57', NULL, '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `return_sale_item`
--

CREATE TABLE `return_sale_item` (
  `rtn_sale_item_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `rtn_sale_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` float UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` float UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `return_sale_item`
--

INSERT INTO `return_sale_item` (`rtn_sale_item_id`, `sch_id`, `rtn_sale_id`, `prod_id`, `title`, `price`, `quantity`, `total_price`, `date`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 1, 21, NULL, 550, 1, 550, '2026-03-08 16:24:57', '2026-03-08 04:24:57', 1, NULL, '2026-03-08 16:24:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `is_default` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `sch_id`, `role`, `permission`, `is_default`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(2, 2, 'Admin', '{\"Dashboard\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Pages\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Customers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_item\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Product_category\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank_deposit\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Settings\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank_withdraw\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Warranty_manage\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Suppliers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\",\"discount\":\"1\",\"warranty\":\"1\",\"vat_option\":\"1\"},\"Role\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Money_receipt\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Loan_provider\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_suppliers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_nagodan\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_loan\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"User\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Stores\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_lc\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Invoice\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\",\"discount\":\"1\",\"warranty\":\"1\"},\"Expense_category\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Balance_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Lc_installment\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Lc\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Customer_type\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Products\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Transaction\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Chaque\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Stock_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Daily_book\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Brand\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Employee\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_employee\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Return_sale\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Return_purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Vat_register\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_vat\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Acquisition_due\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Owe_amount\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Trial_balance\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Capital\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_capital\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_profit\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_stock\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_expense\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"HeadToheadTransfer\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_other_sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_discount\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Previous_data_show\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service_type\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service_invoice\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_service_charge\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"ExchangeProduct\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"StockTransfer\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"AffiliateUser\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"}}', '1', '2023-10-02 07:24:29', 1, NULL, '2026-03-29 16:39:52', NULL, NULL),
(3, 3, 'Admin', '{\"Dashboard\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Pages\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Customers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_item\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Product_category\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank_deposit\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Settings\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank_withdraw\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Warranty_manage\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Suppliers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\",\"discount\":\"1\",\"warranty\":\"1\",\"vat_option\":\"1\"},\"Role\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Money_receipt\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Loan_provider\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_suppliers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_nagodan\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_loan\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"User\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Stores\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_lc\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Invoice\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\",\"discount\":\"1\",\"warranty\":\"1\"},\"Expense_category\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Balance_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Lc_installment\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Lc\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Customer_type\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Products\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Transaction\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Chaque\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Stock_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Daily_book\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Brand\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Employee\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_employee\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Return_sale\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Return_purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Vat_register\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_vat\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Acquisition_due\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Owe_amount\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Trial_balance\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Capital\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_capital\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_profit\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_stock\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_expense\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"HeadToheadTransfer\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_other_sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_discount\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Previous_data_show\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service_type\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Service_invoice\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_service_charge\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"ExchangeProduct\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"StockTransfer\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"AffiliateUser\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"}}', '1', '2023-10-03 09:36:23', 1, NULL, '2026-03-29 16:39:56', NULL, NULL),
(4, 2, 'Manager', '{\"Dashboard\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Customers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_item\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Product_category\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Bank_deposit\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Suppliers\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\",\"discount\":\"1\",\"warranty\":\"1\",\"vat_option\":\"1\"},\"Stores\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_bank\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Products\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Sales_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Purchase_report\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"},\"Ledger_employee\":{\"mod_access\":\"1\",\"create\":\"1\",\"read\":\"1\",\"update\":\"1\",\"delete\":\"1\"}}', '0', '2024-07-24 12:20:41', 1, 1, '2026-02-15 20:57:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `sch_id`, `invoice_id`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(24, 2, 24, '2026-02-17 07:36:06', NULL, NULL, '2026-02-17 19:36:06', NULL, NULL),
(25, 2, 25, '2026-02-22 11:40:51', NULL, NULL, '2026-02-22 11:40:51', NULL, NULL),
(26, 2, 26, '2026-03-02 03:31:16', NULL, NULL, '2026-03-02 15:31:16', NULL, NULL),
(27, 3, 27, '2026-03-08 04:20:39', NULL, NULL, '2026-03-08 16:20:39', NULL, NULL),
(29, 3, 33, '2026-03-31 12:11:44', NULL, NULL, '2026-03-31 12:11:44', NULL, NULL),
(30, 3, 34, '2026-03-31 12:15:07', NULL, NULL, '2026-03-31 12:15:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `service_invoice_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `service_type` enum('onetime','daily','weekly','monthly','yearly') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'onetime',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `sch_id`, `service_invoice_id`, `title`, `service_type`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 1, 'Shohozhisab', 'yearly', '2026-02-15 07:37:41', NULL, NULL, '2026-02-15 19:37:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_invoice`
--

CREATE TABLE `service_invoice` (
  `service_invoice_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) DEFAULT NULL,
  `amount` float UNSIGNED NOT NULL,
  `entire_sale_discount` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `final_amount` float UNSIGNED NOT NULL,
  `nagad_paid` float UNSIGNED DEFAULT NULL,
  `bank_paid` float UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `chaque_paid` float UNSIGNED DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `due` float UNSIGNED DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext DEFAULT NULL,
  `payment_method` longtext DEFAULT NULL,
  `payment_details` longtext DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` longtext DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_invoice`
--

INSERT INTO `service_invoice` (`service_invoice_id`, `sch_id`, `customer_id`, `pymnt_type_id`, `customer_name`, `amount`, `entire_sale_discount`, `vat`, `final_amount`, `nagad_paid`, `bank_paid`, `bank_id`, `chaque_paid`, `chaque_id`, `due`, `creation_timestamp`, `payment_timestamp`, `payment_method`, `payment_details`, `status`, `timestamp`, `year`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 31, NULL, NULL, 250, 0, NULL, 250, 250, 0, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, '1', '2026-02-15 13:37:41', NULL, '2026-02-15 07:37:41', 1, NULL, '2026-02-15 19:37:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_invoice_item`
--

CREATE TABLE `service_invoice_item` (
  `inv_item` int(10) UNSIGNED NOT NULL,
  `service_invoice_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `title` varchar(155) DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` float UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `total_price` float UNSIGNED NOT NULL,
  `discount` int(11) DEFAULT NULL,
  `final_price` float UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_invoice_item`
--

INSERT INTO `service_invoice_item` (`inv_item`, `service_invoice_id`, `sch_id`, `title`, `price`, `quantity`, `total_price`, `discount`, `final_price`, `date`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 1, 2, 'Business Software', 250, 1, 250, NULL, 250, '2026-02-15 19:37:41', '2026-02-15 07:37:41', 1, NULL, '2026-02-15 19:37:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `sch_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cash` double UNSIGNED NOT NULL COMMENT 'This is the nagad cash of the shop owner.',
  `capital` double DEFAULT NULL,
  `service_charge` double DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `stockAmount` double DEFAULT NULL,
  `expense` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `purchase_balance` double NOT NULL,
  `sale_balance` double NOT NULL,
  `ledger_expense_commission` double DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `opening_status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`sch_id`, `name`, `email`, `cash`, `capital`, `service_charge`, `profit`, `stockAmount`, `expense`, `discount`, `purchase_balance`, `sale_balance`, `ledger_expense_commission`, `address`, `mobile`, `comment`, `logo`, `image`, `package_id`, `status`, `opening_status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(2, 'Khan', 'khan@gmail.com', 61702.73, -100222.5, -250, -2154.34, 14438, 50, 61.2, 15932, -2221.8, NULL, '', 1999777777, 'Khan Shop', 'profile_1721801689_b1443d19f6699312a4fa.jpg', 'pro_1721801690_50ae01b451978bdcebd0.jpg', NULL, '1', '1', '2023-10-02 19:24:29', NULL, 1, '2026-03-08 16:24:57', NULL, NULL),
(3, 'murad', 'murad@gmail.com', 650, 0, 0, -400, 162400, 0, 0, 420000, -6000, 350, '', 1923121212, '', 'profile_1696304427_5e26be77f2c7b5e0f028.jpg', 'pro_1696304427_e7797bb9579863f6a726.png', NULL, '1', '1', '2023-10-03 09:36:23', NULL, 2, '2026-03-31 19:07:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer`
--

CREATE TABLE `stock_transfer` (
  `stock_transfer_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `from_stock_id` int(11) NOT NULL,
  `to_stock_id` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `stock_transfer`
--

INSERT INTO `stock_transfer` (`stock_transfer_id`, `sch_id`, `from_stock_id`, `to_stock_id`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(3, 3, 3, 4, '2026-03-28 12:02:27', NULL, NULL, '2026-03-28 12:02:27', NULL, NULL),
(4, 3, 3, 5, '2026-03-28 18:16:01', NULL, NULL, '2026-03-28 18:16:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_item`
--

CREATE TABLE `stock_transfer_item` (
  `stock_transfer_item_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `stock_transfer_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `stock_transfer_item`
--

INSERT INTO `stock_transfer_item` (`stock_transfer_item_id`, `sch_id`, `stock_transfer_id`, `prod_id`, `quantity`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 3, 3, 24, 1, '2026-03-28 12:02:27', 2, NULL, '2026-03-28 12:02:27', NULL, NULL),
(2, 3, 4, 24, 1, '2026-03-28 18:16:01', 2, NULL, '2026-03-28 18:17:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `store_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(115) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `is_default` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`store_id`, `sch_id`, `name`, `description`, `is_default`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(2, 2, 'Default', 'Default Store', '1', '2023-10-02 07:24:29', NULL, NULL, '2023-10-02 19:24:29', NULL, NULL),
(3, 3, 'Default', 'Default Store', '1', '2023-10-03 09:36:23', NULL, NULL, '2023-10-03 09:36:23', NULL, NULL),
(4, 3, 'Exchange', 'Exchange', '0', '2026-03-28 10:45:39', NULL, NULL, '2026-03-28 10:45:39', NULL, NULL),
(5, 3, 'Company', 'Company', '0', '2026-03-28 10:45:59', NULL, NULL, '2026-03-28 10:45:59', NULL, NULL),
(6, 3, 'Transfer', 'Transfer', '0', '2026-03-28 10:46:28', NULL, NULL, '2026-03-28 10:46:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `balance` double NOT NULL,
  `address` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `sch_id`, `name`, `balance`, `address`, `phone`, `status`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(21, 2, 'YALICE', -2654, 'Dhaka\r\nDhaka', 1714070770, '1', '2026-02-15 07:07:05', 1, 1, '2026-02-24 16:27:06', NULL, NULL),
(22, 2, 'Sk', -980, 'Dhaka\r\nDhaka', 12345678, '1', '2026-02-17 07:16:06', 1, 1, '2026-02-25 12:35:29', NULL, NULL),
(23, 3, 'test', -168000, NULL, 1923171718, '1', '2026-02-25 11:42:11', 2, 2, '2026-03-31 12:37:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `trans_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `trangaction_type` enum('Dr.','Cr.') COLLATE utf8_unicode_ci NOT NULL,
  `amount` float UNSIGNED NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `loan_pro_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_to_id` int(11) DEFAULT NULL COMMENT 'this is only used on fund transfer from bank to bank	',
  `lc_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `vat_id` int(11) DEFAULT NULL,
  `memo_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`trans_id`, `sch_id`, `title`, `description`, `trangaction_type`, `amount`, `customer_id`, `loan_pro_id`, `bank_id`, `bank_to_id`, `lc_id`, `supplier_id`, `employee_id`, `vat_id`, `memo_number`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(86, 2, 'ADVANCE', NULL, 'Cr.', 110, 31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17 07:50:58', 1, NULL, '2026-02-25 11:50:59', NULL, NULL),
(87, 2, 'Furniture sale', NULL, 'Dr.', 900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17 08:53:59', 1, NULL, '2026-02-24 14:57:13', NULL, NULL),
(88, 2, 'decoration', NULL, 'Cr.', 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '55', '2026-02-17 08:55:53', 1, NULL, '2026-02-17 20:58:43', NULL, NULL),
(89, 2, 'Hospitality', NULL, 'Cr.', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '55', '2026-02-17 08:57:10', 1, NULL, '2026-02-17 20:57:10', NULL, NULL),
(90, 2, 'vat pay', NULL, 'Dr.', 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-02-22 12:05:31', 1, NULL, '2026-02-25 12:42:20', NULL, NULL),
(91, 2, 'due pay', NULL, 'Cr.', 900, 31, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-24 12:13:41', 1, NULL, '2026-02-28 15:47:08', NULL, NULL),
(92, 2, 'due receive', NULL, 'Dr.', 100, NULL, NULL, NULL, NULL, NULL, 22, NULL, NULL, NULL, '2026-02-24 12:14:31', 1, NULL, '2026-02-24 12:38:48', NULL, NULL),
(93, 2, 'borrow', NULL, 'Dr.', 12000, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-24 12:40:09', 1, NULL, '2026-02-24 12:42:07', NULL, NULL),
(94, 2, 'Withdraw', NULL, 'Cr.', 500, NULL, NULL, 18, 19, NULL, NULL, NULL, NULL, NULL, '2026-02-24 02:44:57', 1, NULL, '2026-02-24 14:47:43', NULL, NULL),
(95, 2, 'paid', NULL, 'Cr.', 770, 30, NULL, 18, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-25 12:02:18', 1, NULL, '2026-02-28 15:48:18', NULL, NULL),
(96, 2, 'Chair sale', NULL, 'Dr.', 700, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-26 10:44:32', 1, NULL, '2026-02-26 10:46:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_edit_log`
--

CREATE TABLE `transaction_edit_log` (
  `transaction_log_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `colum_name` varchar(255) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `old_amount` decimal(10,2) DEFAULT NULL,
  `new_amount` decimal(10,2) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction_edit_log`
--

INSERT INTO `transaction_edit_log` (`transaction_log_id`, `sch_id`, `table_name`, `colum_name`, `id`, `trans_id`, `user_id`, `invoice_id`, `purchase_id`, `old_amount`, `new_amount`, `createdDtm`) VALUES
(7, 3, 'suppliers', NULL, 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(8, 3, 'ledger_suppliers', NULL, 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(9, 3, 'ledger_suppliers', 'purchase_balance', 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(10, 3, 'ledger_purchase', '', 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(11, 3, 'shops', 'stockAmount', 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(12, 3, 'ledger_stock', NULL, 0, 0, 2, 0, 32, '14000.00', '14000.00', '2026-03-31 12:36:48'),
(13, 3, 'suppliers', NULL, 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02'),
(14, 3, 'ledger_suppliers', NULL, 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02'),
(15, 3, 'ledger_suppliers', 'purchase_balance', 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02'),
(16, 3, 'ledger_purchase', '', 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02'),
(17, 3, 'shops', 'stockAmount', 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02'),
(18, 3, 'ledger_stock', NULL, 0, 0, 2, 0, 32, '14000.00', '28000.00', '2026-03-31 12:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE `transaction_log` (
  `transaction_log_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `colum_name` varchar(255) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `dep_id` int(11) DEFAULT NULL,
  `wthd_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction_log`
--

INSERT INTO `transaction_log` (`transaction_log_id`, `sch_id`, `table_name`, `colum_name`, `id`, `trans_id`, `dep_id`, `wthd_id`, `invoice_id`, `purchase_id`, `amount`, `createdDtm`) VALUES
(1, 3, 'products', 'quantity', 24, NULL, NULL, NULL, 34, NULL, '1.00', '2026-03-31 12:15:07'),
(2, 3, 'shops', 'sale_balance', 3, NULL, NULL, NULL, 34, NULL, '1500.00', '2026-03-31 12:15:07'),
(3, 3, 'ledger_sales', 'sale_balance', 34, NULL, NULL, NULL, 34, NULL, '1500.00', '2026-03-31 12:15:07'),
(4, 3, 'invoice_id', 'profit', 34, NULL, NULL, NULL, 34, NULL, NULL, '2026-03-31 12:15:07'),
(5, 3, 'shops', 'profit', 3, NULL, NULL, NULL, 34, NULL, '100.00', '2026-03-31 12:15:07'),
(6, 3, 'ledger_profit', NULL, 30, NULL, NULL, NULL, 34, NULL, '100.00', '2026-03-31 12:15:07'),
(7, 3, 'shops', 'stockAmount', 3, NULL, NULL, NULL, 34, NULL, '1400.00', '2026-03-31 12:15:07'),
(8, 3, 'ledger_stock', NULL, 56, NULL, NULL, NULL, 34, NULL, '1400.00', '2026-03-31 12:15:07'),
(9, 3, 'customers', NULL, 33, NULL, NULL, NULL, 34, NULL, '1500.00', '2026-03-31 12:15:07'),
(10, 3, 'ledger', NULL, 110, NULL, NULL, NULL, 34, NULL, '1500.00', '2026-03-31 12:15:07'),
(18, 3, 'suppliers', NULL, 23, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(19, 3, 'ledger_suppliers', NULL, 47, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(20, 3, 'shops', 'purchase_balance', 3, NULL, NULL, NULL, NULL, 32, '154000.00', '2026-03-31 12:20:11'),
(21, 3, 'ledger_purchase', NULL, 24, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(22, 3, 'shops', 'stockAmount', 3, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(23, 3, 'ledger_stock', NULL, 58, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(24, 3, 'products', NULL, 26, NULL, NULL, NULL, NULL, 32, '1400.00', '2026-03-31 12:20:11'),
(25, 3, 'purchase_item', NULL, 22, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(26, 3, 'purchase', NULL, 32, NULL, NULL, NULL, NULL, 32, '14000.00', '2026-03-31 12:20:11'),
(27, 3, 'bank_deposit', NULL, 18, NULL, 18, NULL, NULL, NULL, '500.00', '2026-03-31 17:34:49'),
(28, 3, 'shops', NULL, 3, NULL, 18, NULL, NULL, NULL, '500.00', '2026-03-31 17:34:49'),
(29, 3, 'ledger_nagodan', NULL, 170, NULL, 18, NULL, NULL, NULL, '500.00', '2026-03-31 17:34:49'),
(30, 3, 'bank', NULL, 20, NULL, 18, NULL, NULL, NULL, '500.00', '2026-03-31 17:34:49'),
(31, 3, 'ledger_bank', NULL, 51, NULL, 18, NULL, NULL, NULL, '500.00', '2026-03-31 17:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `email` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `pic` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL,
  `is_default` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `sch_id`, `email`, `password`, `name`, `mobile`, `address`, `pic`, `role_id`, `status`, `is_default`, `permission`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 'khan@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Khan', NULL, NULL, '', 2, '1', '1', '', '2023-10-02 07:24:29', 1, NULL, '2023-10-02 19:25:49', NULL, NULL),
(2, 3, 'murad@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 'murad', NULL, NULL, '', 3, '1', '1', '', '2023-10-03 09:36:23', 1, NULL, '2023-10-03 09:36:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vat_register`
--

CREATE TABLE `vat_register` (
  `vat_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `vat_register_no` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `balance` float NOT NULL,
  `is_default` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `vat_register`
--

INSERT INTO `vat_register` (`vat_id`, `sch_id`, `name`, `vat_register_no`, `balance`, `is_default`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 2, 'Default Vat Name', 'BIN-0000-01', -13.96, '1', '2023-10-02 07:24:29', 1, 1, '2026-03-02 15:31:16', NULL, NULL),
(2, 3, 'Default Vat Name', 'BIN-0000-01', 0, '1', '2023-10-03 09:36:23', 1, 2, '2025-12-25 12:18:33', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warranty_manage`
--

CREATE TABLE `warranty_manage` (
  `warranty_id` int(10) UNSIGNED NOT NULL,
  `sch_id` int(11) NOT NULL,
  `product_name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `receive_date` date NOT NULL,
  `delivery_date` date NOT NULL,
  `customer_address` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `customer_name` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp(),
  `createdBy` int(11) DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `affiliate_user`
--
ALTER TABLE `affiliate_user`
  ADD PRIMARY KEY (`affiliate_user_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `name` (`name`),
  ADD KEY `account_no` (`account_no`);

--
-- Indexes for table `bank_deposit`
--
ALTER TABLE `bank_deposit`
  ADD PRIMARY KEY (`dep_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `bank_withdraw`
--
ALTER TABLE `bank_withdraw`
  ADD PRIMARY KEY (`wthd_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `chaque`
--
ALTER TABLE `chaque`
  ADD PRIMARY KEY (`chaque_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `commission`
--
ALTER TABLE `commission`
  ADD PRIMARY KEY (`commission_id`);

--
-- Indexes for table `commission_pay`
--
ALTER TABLE `commission_pay`
  ADD PRIMARY KEY (`commission_pay_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `mobile` (`mobile`),
  ADD KEY `cus_type_id` (`cus_type_id`);

--
-- Indexes for table `customer_type`
--
ALTER TABLE `customer_type`
  ADD PRIMARY KEY (`cus_type_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `exchange_product`
--
ALTER TABLE `exchange_product`
  ADD PRIMARY KEY (`exchange_pro_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `exchange_stock_relation`
--
ALTER TABLE `exchange_stock_relation`
  ADD PRIMARY KEY (`exchange_stock_relation_id`);

--
-- Indexes for table `gen_settings`
--
ALTER TABLE `gen_settings`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `gen_settings_super`
--
ALTER TABLE `gen_settings_super`
  ADD PRIMARY KEY (`settings_id_sup`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD PRIMARY KEY (`inv_item`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `lc`
--
ALTER TABLE `lc`
  ADD PRIMARY KEY (`lc_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`ledg_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_bank`
--
ALTER TABLE `ledger_bank`
  ADD PRIMARY KEY (`ledgBank_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_capital`
--
ALTER TABLE `ledger_capital`
  ADD PRIMARY KEY (`capital_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_discount`
--
ALTER TABLE `ledger_discount`
  ADD PRIMARY KEY (`discount_ledg_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `ledger_employee`
--
ALTER TABLE `ledger_employee`
  ADD PRIMARY KEY (`ledg_emp_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_expense`
--
ALTER TABLE `ledger_expense`
  ADD PRIMARY KEY (`ledg_exp_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `memo_number` (`memo_number`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_expense_commission`
--
ALTER TABLE `ledger_expense_commission`
  ADD PRIMARY KEY (`ledg_exp_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `commission_id` (`commission_id`);

--
-- Indexes for table `ledger_loan`
--
ALTER TABLE `ledger_loan`
  ADD PRIMARY KEY (`ledg_loan_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_nagodan`
--
ALTER TABLE `ledger_nagodan`
  ADD PRIMARY KEY (`ledg_nagodan_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_other_sales`
--
ALTER TABLE `ledger_other_sales`
  ADD PRIMARY KEY (`ledg_oth_sales_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_profit`
--
ALTER TABLE `ledger_profit`
  ADD PRIMARY KEY (`profit_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `ledger_purchase`
--
ALTER TABLE `ledger_purchase`
  ADD PRIMARY KEY (`ledgPurch_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`);

--
-- Indexes for table `ledger_sales`
--
ALTER TABLE `ledger_sales`
  ADD PRIMARY KEY (`ledgSale_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_service_charge`
--
ALTER TABLE `ledger_service_charge`
  ADD PRIMARY KEY (`ledger_service_charge_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`service_invoice_id`);

--
-- Indexes for table `ledger_stock`
--
ALTER TABLE `ledger_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `ledger_suppliers`
--
ALTER TABLE `ledger_suppliers`
  ADD PRIMARY KEY (`ledg_sup_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`);

--
-- Indexes for table `ledger_vat`
--
ALTER TABLE `ledger_vat`
  ADD PRIMARY KEY (`ledg_vat_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `vat_id` (`vat_id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `license`
--
ALTER TABLE `license`
  ADD PRIMARY KEY (`lic_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `loan_provider`
--
ALTER TABLE `loan_provider`
  ADD PRIMARY KEY (`loan_pro_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `phone` (`phone`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `money_receipt`
--
ALTER TABLE `money_receipt`
  ADD PRIMARY KEY (`money_receipt_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `payment_type`
--
ALTER TABLE `payment_type`
  ADD PRIMARY KEY (`pymnt_type_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `prod_cat_id` (`prod_cat_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`prod_cat_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `product_stock_relation`
--
ALTER TABLE `product_stock_relation`
  ADD PRIMARY KEY (`product_stock_relation_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD PRIMARY KEY (`purchase_item_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `return_purchase`
--
ALTER TABLE `return_purchase`
  ADD PRIMARY KEY (`rtn_purchase_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `return_purchase_item`
--
ALTER TABLE `return_purchase_item`
  ADD PRIMARY KEY (`rtn_purchase_item_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `return_sale`
--
ALTER TABLE `return_sale`
  ADD PRIMARY KEY (`rtn_sale_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `return_sale_item`
--
ALTER TABLE `return_sale_item`
  ADD PRIMARY KEY (`rtn_sale_item_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `service_invoice_id` (`service_invoice_id`);

--
-- Indexes for table `service_invoice`
--
ALTER TABLE `service_invoice`
  ADD PRIMARY KEY (`service_invoice_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `service_invoice_item`
--
ALTER TABLE `service_invoice_item`
  ADD PRIMARY KEY (`inv_item`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `invoice_id` (`service_invoice_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`sch_id`),
  ADD KEY `email` (`email`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `stock_transfer`
--
ALTER TABLE `stock_transfer`
  ADD PRIMARY KEY (`stock_transfer_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `stock_transfer_item`
--
ALTER TABLE `stock_transfer_item`
  ADD PRIMARY KEY (`stock_transfer_item_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `rtn_sale_id` (`stock_transfer_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`store_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `phone` (`phone`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `lc_id` (`lc_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `vat_id` (`vat_id`);

--
-- Indexes for table `transaction_edit_log`
--
ALTER TABLE `transaction_edit_log`
  ADD PRIMARY KEY (`transaction_log_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD PRIMARY KEY (`transaction_log_id`),
  ADD KEY `id` (`id`),
  ADD KEY `table_name` (`table_name`),
  ADD KEY `table_name_2` (`table_name`,`id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `vat_register`
--
ALTER TABLE `vat_register`
  ADD PRIMARY KEY (`vat_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `warranty_manage`
--
ALTER TABLE `warranty_manage`
  ADD PRIMARY KEY (`warranty_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `affiliate_user`
--
ALTER TABLE `affiliate_user`
  MODIFY `affiliate_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `bank_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bank_deposit`
--
ALTER TABLE `bank_deposit`
  MODIFY `dep_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `bank_withdraw`
--
ALTER TABLE `bank_withdraw`
  MODIFY `wthd_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chaque`
--
ALTER TABLE `chaque`
  MODIFY `chaque_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commission`
--
ALTER TABLE `commission`
  MODIFY `commission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `commission_pay`
--
ALTER TABLE `commission_pay`
  MODIFY `commission_pay_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `customer_type`
--
ALTER TABLE `customer_type`
  MODIFY `cus_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `exchange_product`
--
ALTER TABLE `exchange_product`
  MODIFY `exchange_pro_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exchange_stock_relation`
--
ALTER TABLE `exchange_stock_relation`
  MODIFY `exchange_stock_relation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gen_settings`
--
ALTER TABLE `gen_settings`
  MODIFY `settings_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `gen_settings_super`
--
ALTER TABLE `gen_settings_super`
  MODIFY `settings_id_sup` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `invoice_item`
--
ALTER TABLE `invoice_item`
  MODIFY `inv_item` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `lc`
--
ALTER TABLE `lc`
  MODIFY `lc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `ledg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `ledger_bank`
--
ALTER TABLE `ledger_bank`
  MODIFY `ledgBank_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `ledger_capital`
--
ALTER TABLE `ledger_capital`
  MODIFY `capital_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `ledger_discount`
--
ALTER TABLE `ledger_discount`
  MODIFY `discount_ledg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ledger_employee`
--
ALTER TABLE `ledger_employee`
  MODIFY `ledg_emp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ledger_expense`
--
ALTER TABLE `ledger_expense`
  MODIFY `ledg_exp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ledger_expense_commission`
--
ALTER TABLE `ledger_expense_commission`
  MODIFY `ledg_exp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ledger_loan`
--
ALTER TABLE `ledger_loan`
  MODIFY `ledg_loan_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ledger_nagodan`
--
ALTER TABLE `ledger_nagodan`
  MODIFY `ledg_nagodan_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `ledger_other_sales`
--
ALTER TABLE `ledger_other_sales`
  MODIFY `ledg_oth_sales_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ledger_profit`
--
ALTER TABLE `ledger_profit`
  MODIFY `profit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ledger_purchase`
--
ALTER TABLE `ledger_purchase`
  MODIFY `ledgPurch_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ledger_sales`
--
ALTER TABLE `ledger_sales`
  MODIFY `ledgSale_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `ledger_service_charge`
--
ALTER TABLE `ledger_service_charge`
  MODIFY `ledger_service_charge_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ledger_stock`
--
ALTER TABLE `ledger_stock`
  MODIFY `stock_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `ledger_suppliers`
--
ALTER TABLE `ledger_suppliers`
  MODIFY `ledg_sup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `ledger_vat`
--
ALTER TABLE `ledger_vat`
  MODIFY `ledg_vat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `license`
--
ALTER TABLE `license`
  MODIFY `lic_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_provider`
--
ALTER TABLE `loan_provider`
  MODIFY `loan_pro_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `money_receipt`
--
ALTER TABLE `money_receipt`
  MODIFY `money_receipt_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `package_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_type`
--
ALTER TABLE `payment_type`
  MODIFY `pymnt_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `prod_cat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_stock_relation`
--
ALTER TABLE `product_stock_relation`
  MODIFY `product_stock_relation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `purchase_item`
--
ALTER TABLE `purchase_item`
  MODIFY `purchase_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `return_purchase`
--
ALTER TABLE `return_purchase`
  MODIFY `rtn_purchase_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_purchase_item`
--
ALTER TABLE `return_purchase_item`
  MODIFY `rtn_purchase_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_sale`
--
ALTER TABLE `return_sale`
  MODIFY `rtn_sale_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `return_sale_item`
--
ALTER TABLE `return_sale_item`
  MODIFY `rtn_sale_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_invoice`
--
ALTER TABLE `service_invoice`
  MODIFY `service_invoice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_invoice_item`
--
ALTER TABLE `service_invoice_item`
  MODIFY `inv_item` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `sch_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock_transfer`
--
ALTER TABLE `stock_transfer`
  MODIFY `stock_transfer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_transfer_item`
--
ALTER TABLE `stock_transfer_item`
  MODIFY `stock_transfer_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `store_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `trans_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `transaction_edit_log`
--
ALTER TABLE `transaction_edit_log`
  MODIFY `transaction_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transaction_log`
--
ALTER TABLE `transaction_log`
  MODIFY `transaction_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vat_register`
--
ALTER TABLE `vat_register`
  MODIFY `vat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `warranty_manage`
--
ALTER TABLE `warranty_manage`
  MODIFY `warranty_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
