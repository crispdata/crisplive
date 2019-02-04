-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2016 at 08:14 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mobilemaidz`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(20) NOT NULL,
  `firstName` varchar(200) NOT NULL,
  `lastName` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  `phoneNo` varchar(200) NOT NULL,
  `deviceType` int(20) NOT NULL COMMENT '1 for ios and 2 for android',
  `deviceToken` varchar(200) NOT NULL,
  `loginFrom` int(20) NOT NULL COMMENT '0 for normal,1 for facebook',
  `isVerified` int(20) NOT NULL COMMENT '1 for done,0 for not verified',
  `userType` int(20) NOT NULL COMMENT '1 for client ,2 for maid',
  `createdon` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `isactive` int(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `firstName`, `lastName`, `email`, `password`, `image`, `phoneNo`, `deviceType`, `deviceToken`, `loginFrom`, `isVerified`, `userType`, `createdon`, `modified`, `isactive`) VALUES
(1, 'tajinder', 'singh', 'wtyttajind111er@gmail.com', '202cb962ac59075b964b07152d234b70', 'images/1456124731.png', '12w212113', 1, '123', 1, 1, 1, '2016-02-22 08:05:31', '2016-02-22 08:05:46', 1),
(2, 'tajinder', 'singh', 'wtyttajind111e111r@gmail.com', '202cb962ac59075b964b07152d234b70', 'images/1456124764.png', '12w212111113', 1, '123', 1, 0, 1, '2016-02-22 08:06:04', '2016-02-22 08:06:04', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
