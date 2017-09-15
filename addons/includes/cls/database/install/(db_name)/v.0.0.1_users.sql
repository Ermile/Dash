-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 15, 2017 at 01:53 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.1.7-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `az_reza11`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `displayname` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `meta` mediumtext CHARACTER SET utf8mb4,
  `status` enum('active','awaiting','deactive','removed','filter','unreachable') DEFAULT 'awaiting',
  `parent` int(10) UNSIGNED DEFAULT NULL,
  `permission` varchar(1000) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `group` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `fileid` int(20) UNSIGNED DEFAULT NULL,
  `chatid` int(20) UNSIGNED DEFAULT NULL,
  `pin` smallint(4) UNSIGNED DEFAULT NULL,
  `ref` int(10) UNSIGNED DEFAULT NULL,
  `creator` int(10) UNSIGNED DEFAULT NULL,
  `twostep` bit(1) DEFAULT NULL,
  `googlemail` varchar(100) DEFAULT NULL,
  `facebookmail` varchar(100) DEFAULT NULL,
  `twittermail` varchar(100) DEFAULT NULL,
  `dontwillsetmobile` varchar(50) DEFAULT NULL,
  `fileurl` varchar(2000) DEFAULT NULL,
  `notification` text,
  `setup` bit(1) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `father` varchar(100) DEFAULT NULL,
  `birthday` varchar(50) DEFAULT NULL,
  `shcode` varchar(100) DEFAULT NULL,
  `nationalcode` varchar(100) DEFAULT NULL,
  `shfrom` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `brithplace` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `passportcode` varchar(100) DEFAULT NULL,
  `marital` enum('single','married') DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `childcount` smallint(2) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `insurancetype` varchar(100) DEFAULT NULL,
  `insurancecode` varchar(100) DEFAULT NULL,
  `dependantscount` smallint(4) DEFAULT NULL,
  `postion` varchar(100) DEFAULT NULL,
  `unit_id` smallint(5) DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  `job` varchar(100) DEFAULT NULL,
  `cardnumber` varchar(100) DEFAULT NULL,
  `shaba` varchar(100) DEFAULT NULL,
  `personnelcode` varchar(100) DEFAULT NULL,
  `passportexpire` varchar(100) DEFAULT NULL,
  `paymentaccountnumber` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;