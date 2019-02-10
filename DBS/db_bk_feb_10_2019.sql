-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2019 at 07:03 AM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `binlab_client_drlal`
--
CREATE DATABASE IF NOT EXISTS `binlab_client_drlal` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `binlab_client_drlal`;

-- --------------------------------------------------------

--
-- Table structure for table `bl_categories`
--

CREATE TABLE `bl_categories` (
  `id` varchar(36) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `parent_id` varchar(36) DEFAULT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_categories`
--

INSERT INTO `bl_categories` (`id`, `name`, `description`, `parent_id`, `status`, `updated`) VALUES
('8115ab1a-1c02-11e9-be47-fcf8aee943ea', 'Hematology', NULL, '0', 'ACTIVE', '2019-01-19 21:24:29'),
('9cdcb860-1c02-11e9-be47-fcf8aee943ea', 'Oncology', NULL, '0', 'ACTIVE', '2019-01-19 21:25:15'),
('b80116fc-1c02-11e9-be47-fcf8aee943ea', 'Hematology Basic', NULL, '8115ab1a-1c02-11e9-be47-fcf8aee943ea', 'ACTIVE', '2019-01-19 21:26:01'),
('cc36a2df-f7fb-4699-b201-bdf30e2b363d', 'test my test', 'Test categories', '9cdcb860-1c02-11e9-be47-fcf8aee943ea', 'DELETED', '2019-01-20 11:45:31'),
('cd8e69e1-1c02-11e9-be47-fcf8aee943ea', 'Hematology Advance', NULL, '8115ab1a-1c02-11e9-be47-fcf8aee943ea', 'ACTIVE', '2019-01-19 21:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `bl_collectors`
--

CREATE TABLE `bl_collectors` (
  `id` varchar(36) NOT NULL,
  `title` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `mobile` bigint(20) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_collectors`
--

INSERT INTO `bl_collectors` (`id`, `title`, `first_name`, `last_name`, `email_id`, `mobile`, `status`, `updated`) VALUES
('cc5ddc16-eb83-4d9f-bf25-25a068f9d581', 'Mr.', 'Pawan', 'Hawa', 'pawan.hawa@drlal.com', 9876543210, 'ACTIVE', '2019-01-25 00:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `bl_doctors`
--

CREATE TABLE `bl_doctors` (
  `id` varchar(36) NOT NULL,
  `title` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `clinic` varchar(100) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_doctors`
--

INSERT INTO `bl_doctors` (`id`, `title`, `first_name`, `last_name`, `email_id`, `address`, `city`, `mobile`, `clinic`, `status`, `updated`) VALUES
('24a04821-5a1c-4069-b3b6-8bdb5a9ad628', '', '', '', '', '', '', '', '', '', '2019-01-18 01:26:44'),
('58ca9f62-61e8-45cf-baae-ee3a92925add', 'Dr.', 'Prabhakar', 'Banerjee', 'bpr@bnr.com', 'Kolkata', 'Kolkatta', '9387373636`', 'New India Clinic', 'ACTIVE', '2019-01-18 01:50:02'),
('86b27b51-e395-4a8f-a7b2-05665a0ca0f1', '', '', '', '', '', '', '', '', '', '2019-01-18 01:25:36'),
('ce9fb65e-f793-498c-a221-7ffcc78564c9', 'Dr.', 'Rohit', 'Bajaj', 'rbajaj@gml.com', '514, 8Th Cross Road, Neeladri Nagar', 'Bangalore', '987654321', 'Bajaj Clinic', 'ACTIVE', '2019-01-18 19:00:29'),
('e3aceabe-db12-46d8-aece-3d630d69f00e', '', '', '', '', '', '', '', '', '', '2019-01-18 01:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `bl_items`
--

CREATE TABLE `bl_items` (
  `id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit` char(10) NOT NULL,
  `minval` float NOT NULL,
  `maxval` float NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_items`
--

INSERT INTO `bl_items` (`id`, `name`, `unit`, `minval`, `maxval`, `product_id`, `description`, `status`, `updated`) VALUES
('09e8b38f-850d-466c-a6c3-e692d7d97f6c', 'New Item 1', 'mg/L', 0, 10, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'New Desc   for  test', 'ACTIVE', '2019-01-27 20:34:03'),
('5435e6f9-35a5-4bbc-b809-a30a630a6694', 'New item 6', 'mg/L', 0, 5, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:37:30'),
('54e53145-b88c-4029-b587-0d04c9a1b4d7', 'New item 10', 'mg/L', 9, 12, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:39:35'),
('5722b908-df25-4acb-9167-62f0abe2bae7', 'New item 2', 'mg/L', 0, 200, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:34:43'),
('740d3e30-66a8-482a-b1c9-4d1899924aeb', 'New item 3', 'mg/L', 0, 10, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:35:22'),
('8ce90bef-73f9-4f60-8825-f127d4af673c', 'New item 8', 'mg/L', 50, 100, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:38:58'),
('9b564b61-21c3-43ce-b1e7-e909121952b3', 'New item 4', 'mg/L', 1, 5, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:36:14'),
('a6fc9f11-1da0-11e9-a61c-fcf8aee943ea', 'RBC Count', 'g/ml', 50, 100, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'Red Blood Cells  count', 'ACTIVE', '2019-01-21 22:49:04'),
('c6fceb2f-c4f8-4f48-a335-11fe27d4b277', 'New  item', 'mg/L', 50, 500, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'New Desc   for  test', 'ACTIVE', '2019-01-24 23:26:02'),
('e178e7d2-8a8f-4aba-b368-55c4b86b10ea', 'New item 7', 'mg/L', 0, 6, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:38:16'),
('e7531800-5382-4d01-894c-7bea2d0f6bb4', 'New item 5', 'mg/L', 0, 3, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 'ACTIVE', '2019-01-27 20:36:55');

-- --------------------------------------------------------

--
-- Table structure for table `bl_orders`
--

CREATE TABLE `bl_orders` (
  `id` varchar(36) NOT NULL,
  `patient_id` varchar(36) NOT NULL,
  `doctor_id` varchar(36) DEFAULT NULL,
  `collector_id` varchar(36) DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `observation` text,
  `doctor_name` varchar(100) DEFAULT NULL,
  `sign_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `doctor_esign` blob,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_orders`
--

INSERT INTO `bl_orders` (`id`, `patient_id`, `doctor_id`, `collector_id`, `order_date`, `barcode`, `observation`, `doctor_name`, `sign_date`, `doctor_esign`, `status`, `updated`) VALUES
('013a6b96-abe4-4640-b5e1-e5f4ce77c6a5', '12795d10-107d-4e75-a9db-da212b606a2f', '58ca9f62-61e8-45cf-baae-ee3a92925add', 'cc5ddc16-eb83-4d9f-bf25-25a068f9d581', '2019-01-23 00:00:00', '121122345', NULL, NULL, '2019-02-09 13:58:21', NULL, 'ACTIVE', '2019-01-26 23:44:30'),
('631c1980-cf85-497f-9332-a570f88ddb58', '229d2f33-814c-4ff3-aef6-f98e1d932fa3', 'XXX', 'XXX', '2019-01-24 00:00:00', '34567890', NULL, NULL, '2019-02-09 13:58:21', NULL, 'ACTIVE', '2019-01-27 19:17:46'),
('f850e1c0-eb7e-450a-ab98-96364aed40ad', '229d2f33-814c-4ff3-aef6-f98e1d932fa3', '58ca9f62-61e8-45cf-baae-ee3a92925add', 'XXX', '2019-01-25 00:00:00', '22456789', NULL, NULL, '2019-02-09 13:58:21', NULL, 'PROCESSED', '2019-01-27 19:15:49');

-- --------------------------------------------------------

--
-- Table structure for table `bl_order_process`
--

CREATE TABLE `bl_order_process` (
  `id` varchar(36) NOT NULL,
  `order_id` varchar(36) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_order_process`
--

INSERT INTO `bl_order_process` (`id`, `order_id`, `status`, `updated`) VALUES
('c7d0cd08-e79e-4ea5-8abf-2e52f0bb0b46', 'f850e1c0-eb7e-450a-ab98-96364aed40ad', 'PROCESSED', '2019-02-02 22:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `bl_order_process_items`
--

CREATE TABLE `bl_order_process_items` (
  `id` varchar(36) NOT NULL,
  `order_process_product_id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit` char(10) NOT NULL,
  `minval` float NOT NULL,
  `maxval` float NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `currentval` float NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_order_process_items`
--

INSERT INTO `bl_order_process_items` (`id`, `order_process_product_id`, `name`, `unit`, `minval`, `maxval`, `product_id`, `description`, `currentval`, `status`, `updated`) VALUES
('1a661f2f-afe7-46fe-9ed1-0b24149e333a', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 2', 'mg/L', 0, 200, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 1, 'ACTIVE', '2019-02-02 22:21:15'),
('286a7c56-bbe4-46df-97b7-01ec8d90f7c7', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 5', 'mg/L', 0, 3, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 2, 'ACTIVE', '2019-02-02 22:21:15'),
('54a8f9e5-139e-411c-b37d-59701241dcd1', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 8', 'mg/L', 50, 100, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 33, 'ACTIVE', '2019-02-02 22:21:15'),
('658e6465-868d-4b45-a9e2-8fdd0d4d2fb6', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 6', 'mg/L', 0, 5, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 44, 'ACTIVE', '2019-02-02 22:21:15'),
('7834b7b1-2335-4519-abd6-ca5c59353275', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 3', 'mg/L', 0, 10, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 5, 'ACTIVE', '2019-02-02 22:21:15'),
('c0e6c2b1-2f6a-4d50-9e09-d90bdedf6d73', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New  item', 'mg/L', 50, 500, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'New Desc   for  test', 66, 'ACTIVE', '2019-02-02 22:21:15'),
('c4981136-5903-4d04-8593-bd07b0fb4019', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 10', 'mg/L', 9, 12, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 7, 'ACTIVE', '2019-02-02 22:21:15'),
('c662fe25-5553-41c9-bcf4-bfbb3fc61e46', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New Item 1', 'mg/L', 0, 10, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'New Desc   for  test', 88, 'ACTIVE', '2019-02-02 22:21:15'),
('d270989f-1424-4ef0-b17a-b4df62018035', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 7', 'mg/L', 0, 6, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 9, 'ACTIVE', '2019-02-02 22:21:15'),
('d4e3665e-5ac8-4184-aaa0-121d5659ff73', '1752a336-a6b7-46d3-9863-f15eedda766e', 'RBC Count', 'g/ml', 50, 100, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'Red Blood Cells  count', 99, 'ACTIVE', '2019-02-02 22:21:15'),
('d60095eb-495c-4dcc-954b-97b2e2cc6221', '1752a336-a6b7-46d3-9863-f15eedda766e', 'New item 4', 'mg/L', 1, 5, '08876ae9-088b-4cd9-ad55-50bf1b410b2d', '', 199, 'ACTIVE', '2019-02-02 22:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `bl_order_process_products`
--

CREATE TABLE `bl_order_process_products` (
  `id` varchar(36) NOT NULL,
  `order_process_id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_order_process_products`
--

INSERT INTO `bl_order_process_products` (`id`, `order_process_id`, `product_id`, `status`, `updated`) VALUES
('1752a336-a6b7-46d3-9863-f15eedda766e', 'c7d0cd08-e79e-4ea5-8abf-2e52f0bb0b46', '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'ACTIVE', '2019-02-02 22:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `bl_order_products`
--

CREATE TABLE `bl_order_products` (
  `id` varchar(36) NOT NULL,
  `order_id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_order_products`
--

INSERT INTO `bl_order_products` (`id`, `order_id`, `product_id`, `status`, `updated`) VALUES
('4f368f7a-dda2-4026-875e-11c360c323dc', '013a6b96-abe4-4640-b5e1-e5f4ce77c6a5', '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'ACTIVE', '2019-01-27 19:13:35'),
('722ec84f-8418-41bb-8cf9-ba3e7dc78c2a', '631c1980-cf85-497f-9332-a570f88ddb58', '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'ACTIVE', '2019-01-27 19:17:46'),
('b2b062a1-70fd-4826-becc-d53f95c6b476', 'f850e1c0-eb7e-450a-ab98-96364aed40ad', '08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'ACTIVE', '2019-01-27 19:15:49');

-- --------------------------------------------------------

--
-- Table structure for table `bl_patients`
--

CREATE TABLE `bl_patients` (
  `id` varchar(36) NOT NULL,
  `title` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `email_id` varchar(200) DEFAULT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_patients`
--

INSERT INTO `bl_patients` (`id`, `title`, `first_name`, `last_name`, `address`, `city`, `mobile`, `email_id`, `status`, `updated`) VALUES
('12795d10-107d-4e75-a9db-da212b606a2f', 'Mr.', 'rahul', 'Rai', '', '', '', 'rrai@xy.co', 'ACTIVE', '2019-01-19 19:52:55'),
('229d2f33-814c-4ff3-aef6-f98e1d932fa3', 'Miss', 'Preeti', 'Kumari', '', '', '', 'pkumari@gmail.com', 'ACTIVE', '2019-01-19 19:54:23'),
('29706222-8a6a-432a-bd31-913df802ed9b', 'Mrs.', 'Indirawati', 'Mishra', '', '', '9876564544', 'imis@gml.com', 'ACTIVE', '2019-01-18 19:48:06'),
('8af5e158-4444-4e33-9120-d0ebe109475e', 'Prof.', 'prabhu', 'Rajpoot', '', '', '', 'prajpoot@cyct.net', 'ACTIVE', '2019-01-19 19:55:51'),
('a1675311-4fdb-44f6-8035-1152356453b1', 'Mrs.', 'SIta', 'Dogra', '', '', '', 'sita.dogra@gmail.com', 'ACTIVE', '2019-01-19 19:55:06'),
('d99ba016-789d-428a-8d6a-a326fbde74bc', 'Mr.', 'Karan', 'Rana', '', '', '', 'krana@gmail.com', 'ACTIVE', '2019-01-19 19:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `bl_products`
--

CREATE TABLE `bl_products` (
  `id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category_id` varchar(36) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_products`
--

INSERT INTO `bl_products` (`id`, `name`, `description`, `category_id`, `status`, `updated`) VALUES
('08876ae9-088b-4cd9-ad55-50bf1b410b2d', 'Test One Product', 'Test products', 'cd8e69e1-1c02-11e9-be47-fcf8aee943ea', 'ACTIVE', '2019-01-20 12:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `bl_roles`
--

CREATE TABLE `bl_roles` (
  `id` varchar(36) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_roles`
--

INSERT INTO `bl_roles` (`id`, `name`, `description`, `status`, `updated`) VALUES
('2f4e8399-16f3-11e9-bdd6-fcf8aee943ea', 'DOCTOR', 'Doctor of the lab', 'ACTIVE', '2019-01-13 10:52:13'),
('5231fb54-16f3-11e9-bdd6-fcf8aee943ea', 'LABOFFICE', 'Lab office user', 'ACTIVE', '2019-01-13 10:53:12'),
('99c020d7-16f2-11e9-bdd6-fcf8aee943ea', 'SYSADMIN', 'System  Administrator', 'ACTIVE', '2019-01-13 10:48:02'),
('9cb7e295-16f3-11e9-bdd6-fcf8aee943ea', 'FRONTDESK', 'Front Office role', 'ACTIVE', '2019-01-13 10:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `bl_role_permissions`
--

CREATE TABLE `bl_role_permissions` (
  `id` varchar(36) NOT NULL,
  `role_id` varchar(36) NOT NULL,
  `object` varchar(20) NOT NULL,
  `can_view` int(11) NOT NULL DEFAULT '0',
  `can_edit` int(11) NOT NULL DEFAULT '0',
  `can_delete` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_role_permissions`
--

INSERT INTO `bl_role_permissions` (`id`, `role_id`, `object`, `can_view`, `can_edit`, `can_delete`) VALUES
('f2d02a05-16f2-11e9-bdd6-fcf8aee943ea', '99c020d7-16f2-11e9-bdd6-fcf8aee943ea', 'Login', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bl_users`
--

CREATE TABLE `bl_users` (
  `id` varchar(36) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `title` char(5) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_users`
--

INSERT INTO `bl_users` (`id`, `email_id`, `password`, `title`, `first_name`, `middle_name`, `last_name`, `status`, `updated`) VALUES
('9418ab6b-0b5e-11e9-89cd-0208c7f15232', 'admin@drlal.com', 'cd84d683cc5612c69efe115c80d0b7dc', 'Mr.', 'System', NULL, 'Admin', 'ACTIVE', '2018-12-29 11:40:45'),
('dcb420b4-9e7e-4735-b585-56c9dc5cdf75', 'smishra8480@gmail.com', 'cd84d683cc5612c69efe115c80d0b7dc', 'Miss', 'Suman', NULL, 'Mishra', 'ACTIVE', '2019-01-17 19:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `bl_user_roles`
--

CREATE TABLE `bl_user_roles` (
  `id` varchar(36) NOT NULL,
  `role_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bl_user_roles`
--

INSERT INTO `bl_user_roles` (`id`, `role_id`, `user_id`, `status`, `updated`) VALUES
('a35cb6ca-bdef-4ae1-8051-c52238e8c84d', '2f4e8399-16f3-11e9-bdd6-fcf8aee943ea', 'dcb420b4-9e7e-4735-b585-56c9dc5cdf75', 'ACTIVE', '2019-01-17 19:41:03'),
('d3a30795-16f2-11e9-bdd6-fcf8aee943ea', '99c020d7-16f2-11e9-bdd6-fcf8aee943ea', '9418ab6b-0b5e-11e9-89cd-0208c7f15232', 'DELETED', '2019-01-13 10:49:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bl_categories`
--
ALTER TABLE `bl_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_collectors`
--
ALTER TABLE `bl_collectors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_doctors`
--
ALTER TABLE `bl_doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_items`
--
ALTER TABLE `bl_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_orders`
--
ALTER TABLE `bl_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_order_process`
--
ALTER TABLE `bl_order_process`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_order_process_items`
--
ALTER TABLE `bl_order_process_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_order_process_products`
--
ALTER TABLE `bl_order_process_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_order_products`
--
ALTER TABLE `bl_order_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_patients`
--
ALTER TABLE `bl_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_products`
--
ALTER TABLE `bl_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_roles`
--
ALTER TABLE `bl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_role_permissions`
--
ALTER TABLE `bl_role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_users`
--
ALTER TABLE `bl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`email_id`);

--
-- Indexes for table `bl_user_roles`
--
ALTER TABLE `bl_user_roles`
  ADD PRIMARY KEY (`id`);
--
-- Database: `binlab_master_db`
--
CREATE DATABASE IF NOT EXISTS `binlab_master_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `binlab_master_db`;

-- --------------------------------------------------------

--
-- Table structure for table `lab_master`
--

CREATE TABLE `lab_master` (
  `id` varchar(36) NOT NULL,
  `alias` char(20) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `server_ip` varchar(15) NOT NULL,
  `user` varchar(20) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `status` char(20) NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_master`
--

INSERT INTO `lab_master` (`id`, `alias`, `name`, `address`, `server_ip`, `user`, `pwd`, `status`) VALUES
('4d5b7f24-0b5e-11e9-89cd-0208c7f15232', 'drlal', 'Dr. Lal', 'Karkarduma', 'localhost', 'root', '', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(36) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `title` char(5) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `is_admin` int(11) NOT NULL DEFAULT '0',
  `status` char(10) NOT NULL DEFAULT 'ACTIVE',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email_id`, `password`, `title`, `first_name`, `last_name`, `is_admin`, `status`, `created`) VALUES
('4fe7b339-0b43-11e9-89cd-0208c7f15232', 'admin@bintechsol.com', '2212b80aca4523a00e29b76e7bd5e7fe', 'Mr.', 'System', 'Admin', 1, 'ACTIVE', '2018-12-29 08:25:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lab_master`
--
ALTER TABLE `lab_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alias` (`alias`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`email_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
