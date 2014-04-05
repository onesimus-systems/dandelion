-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 05, 2014 at 09:13 AM
-- Server version: 5.5.36-MariaDB-1~precise-log
-- PHP Version: 5.5.10-1+deb.sury.org~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gardenerBase`
--

-- --------------------------------------------------------

--
-- Table structure for table `dan_category`
--

CREATE TABLE IF NOT EXISTS `dan_category` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_log`
--

CREATE TABLE IF NOT EXISTS `dan_log` (
  `logid` int(20) NOT NULL AUTO_INCREMENT,
  `datec` date NOT NULL,
  `timec` time NOT NULL,
  `title` varchar(300) NOT NULL,
  `entry` longtext NOT NULL,
  `usercreated` varchar(255) NOT NULL,
  `cat` text NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logid`),
  UNIQUE KEY `logid` (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_presence`
--

CREATE TABLE IF NOT EXISTS `dan_presence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `realname` text NOT NULL,
  `status` tinyint(2) NOT NULL,
  `message` text NOT NULL,
  `returntime` text NOT NULL,
  `dmodified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_presence`
--

INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`) VALUES
(1, 1, 'Admin', 1, '', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `dan_session_token`
--

CREATE TABLE IF NOT EXISTS `dan_session_token` (
  `session_id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(256) NOT NULL,
  `userid` int(10) NOT NULL,
  `expire` int(255) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_settings`
--

CREATE TABLE IF NOT EXISTS `dan_settings` (
  `settings_id` smallint(9) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `dan_settings`
--

INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
(1, 'slogan', ''),
(2, 'app_title', 'Dandelion Web Log');

-- --------------------------------------------------------

--
-- Table structure for table `dan_users`
--

CREATE TABLE IF NOT EXISTS `dan_users` (
  `userid` smallint(6) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `datecreated` date NOT NULL,
  `firsttime` tinyint(1) NOT NULL DEFAULT '2',
  `showlimit` int(3) NOT NULL DEFAULT '25',
  `theme` tinytext NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_users`
--

INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`) VALUES
(1, 'admin', '$2y$10$sRDlu.F6gPVM4kS/k7ESHO9PF0Z5pXk0J/SpuMa88E31/Lux1mfMy', 'Admin', 'admin', '2014-02-08', 2, 25, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
