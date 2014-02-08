-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 08, 2014 at 10:22 AM
-- Server version: 5.5.35
-- PHP Version: 5.5.8-3+sury.org~precise+2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gardener`
--
CREATE DATABASE `gardener` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gardener`;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(255) NOT NULL,
  `ptree` varchar(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `logid` int(20) NOT NULL AUTO_INCREMENT,
  `datec` date NOT NULL,
  `timec` time NOT NULL,
  `title` varchar(300) NOT NULL,
  `entry` longtext NOT NULL,
  `usercreated` varchar(255) NOT NULL,
  `cat` varchar(3000) NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `presence`
--

CREATE TABLE IF NOT EXISTS `presence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `realname` text NOT NULL,
  `status` tinyint(2) NOT NULL,
  `message` text NOT NULL,
  `return` text NOT NULL,
  `dmodified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `User_ID` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `presence`
--

INSERT INTO `presence` (`id`, `uid`, `realname`, `status`, `message`, `return`, `dmodified`) VALUES
(1, 1, 'Admin', 1, '', '', '2014-02-08 10:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `session_token`
--

CREATE TABLE IF NOT EXISTS `session_token` (
  `session_id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(256) NOT NULL,
  `userid` int(10) NOT NULL,
  `expire` int(255) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `settings_id` int(255) NOT NULL AUTO_INCREMENT,
  `message` varchar(1000) NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `settings_id` int(10) NOT NULL,
  `role` varchar(255) NOT NULL,
  `datecreated` date NOT NULL,
  `firsttime` tinyint(1) NOT NULL DEFAULT '2',
  `showlimit` int(3) NOT NULL DEFAULT '25',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `realname`, `settings_id`, `role`, `datecreated`, `firsttime`, `showlimit`) VALUES
(1, 'admin', '$2y$10$sRDlu.F6gPVM4kS/k7ESHO9PF0Z5pXk0J/SpuMa88E31/Lux1mfMy', 'Admin', 0, 'Admin', '2014-02-08', 2, 25);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
