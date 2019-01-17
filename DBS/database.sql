-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 17, 2019 at 12:45 PM
-- Server version: 5.7.24-0ubuntu0.18.04.1
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
('41b308b5-4878-4729-a5ae-694c1751878c', '2f4e8399-16f3-11e9-bdd6-fcf8aee943ea', '3d583474-536a-45f2-9336-dc4d46542d4d', 'ACTIVE', '2019-01-15 16:56:10'),
('95719109-b133-40e1-8155-ad9db3e57af0', '99c020d7-16f2-11e9-bdd6-fcf8aee943ea', '686a2a41-2e00-4856-84c2-25b8a010763f', 'ACTIVE', '2019-01-15 16:55:38'),
('d3a30795-16f2-11e9-bdd6-fcf8aee943ea', '99c020d7-16f2-11e9-bdd6-fcf8aee943ea', '9418ab6b-0b5e-11e9-89cd-0208c7f15232', 'ACTIVE', '2019-01-13 10:49:40');

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
('3d583474-536a-45f2-9336-dc4d46542d4d', 'smishra8480@gmail.com', 'cd84d683cc5612c69efe115c80d0b7dc', 'Mrs.', 'Suman', NULL, 'Mishra', 'ACTIVE', '2019-01-15 16:56:10'),
('686a2a41-2e00-4856-84c2-25b8a010763f', 'msm@drlal.com', 'cd84d683cc5612c69efe115c80d0b7dc', 'Mr.', 'Manoj', NULL, 'Sharma', 'ACTIVE', '2019-01-15 16:55:38'),
('9418ab6b-0b5e-11e9-89cd-0208c7f15232', 'admin@drlal.com', 'cd84d683cc5612c69efe115c80d0b7dc', 'Mr.', 'System', NULL, 'Admin', 'ACTIVE', '2018-12-29 11:40:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bl_categories`
--
ALTER TABLE `bl_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_role_permissions`
--
ALTER TABLE `bl_role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_roles`
--
ALTER TABLE `bl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_user_roles`
--
ALTER TABLE `bl_user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bl_users`
--
ALTER TABLE `bl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_id` (`email_id`);
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
('4d5b7f24-0b5e-11e9-89cd-0208c7f15232', 'drlal', 'Dr. Lal', 'Karkarduma', 'localhost', 'bintch', 'Bin@tech#123', 'ACTIVE');

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
