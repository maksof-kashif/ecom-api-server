-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 14, 2018 at 02:10 PM
-- Server version: 10.1.33-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `slim_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_packages`
--

CREATE TABLE `tbl_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `expiry` int(11) DEFAULT NULL,
  `monthly_payout` float DEFAULT NULL,
  `user_bonus_percent` float DEFAULT NULL,
  `reffer_bonus_percent` float DEFAULT NULL,
  `admin_bonus_percent` float DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `line1` varchar(30) DEFAULT NULL,
  `line2` varchar(30) DEFAULT NULL,
  `line4` varchar(30) DEFAULT NULL,
  `line3` varchar(30) DEFAULT NULL,
  `line5` varchar(30) DEFAULT NULL,
  `line6` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payments`
--

CREATE TABLE `tbl_payments` (
  `id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `method` varchar(30) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pending_payments`
--

CREATE TABLE `tbl_pending_payments` (
  `payment_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pending_purchase`
--

CREATE TABLE `tbl_pending_purchase` (
  `user_id` int(11) DEFAULT NULL,
  `pending_payment_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_purchase`
--

CREATE TABLE `tbl_purchase` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(30) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reffer_balance_log`
--

CREATE TABLE `tbl_reffer_balance_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reffer_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `bonus` float DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sessions`
--

CREATE TABLE `tbl_sessions` (
  `user_id` int(11) NOT NULL,
  `token` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction_logs`
--

CREATE TABLE `tbl_transaction_logs` (
  `user_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `message` text,
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `private_key` varchar(50) NOT NULL,
  `date` datetime DEFAULT NULL,
  `reffer_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `email`, `username`, `password`, `mobile`, `private_key`, `date`, `reffer_id`, `is_deleted`, `is_verified`) VALUES
(44, 'syedasharkhalid@gmail.com', 'ashar', 'ashar', '03442534317', '100200300400500', '2018-05-14 16:40:52', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_balance`
--

CREATE TABLE `tbl_user_balance` (
  `user_id` int(11) NOT NULL,
  `current_balance` double DEFAULT NULL,
  `reffer_balance` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_packages`
--

CREATE TABLE `tbl_user_packages` (
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `subscibe_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_verification`
--

CREATE TABLE `tbl_verification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bluff_code` varchar(32) DEFAULT NULL,
  `token` text,
  `type` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_withdrawls`
--

CREATE TABLE `tbl_withdrawls` (
  `withdrawl_id` varchar(12) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `wallet_address` text,
  `status` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_packages`
--
ALTER TABLE `tbl_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_payments`
--
ALTER TABLE `tbl_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_pending_payments`
--
ALTER TABLE `tbl_pending_payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `tbl_purchase`
--
ALTER TABLE `tbl_purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reffer_balance_log`
--
ALTER TABLE `tbl_reffer_balance_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tbl_transaction_logs`
--
ALTER TABLE `tbl_transaction_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_balance`
--
ALTER TABLE `tbl_user_balance`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tbl_user_packages`
--
ALTER TABLE `tbl_user_packages`
  ADD PRIMARY KEY (`user_id`,`package_id`);

--
-- Indexes for table `tbl_verification`
--
ALTER TABLE `tbl_verification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_packages`
--
ALTER TABLE `tbl_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_payments`
--
ALTER TABLE `tbl_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_pending_payments`
--
ALTER TABLE `tbl_pending_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_reffer_balance_log`
--
ALTER TABLE `tbl_reffer_balance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transaction_logs`
--
ALTER TABLE `tbl_transaction_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_verification`
--
ALTER TABLE `tbl_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
