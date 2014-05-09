-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2014 at 05:54 PM
-- Server version: 5.5.37-MariaDB-1~precise-log
-- PHP Version: 5.5.11-3+deb.sury.org~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dandyBase`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dan_presence`
--

INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`) VALUES
(1, 1, 'Admin', 1, '', '', '2014-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `dan_settings`
--

CREATE TABLE IF NOT EXISTS `dan_settings` (
  `settings_id` smallint(9) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dan_settings`
--

INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
(1, 'app_title', 'Dandelion Web Log'),
(2, 'slogan', 'Website tagline'),
(3, 'default_theme', 'Halloween'),
(4, 'cheesto_enabled', '1');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `dan_users`
--

INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`) VALUES
(1, 'admin', '$2y$10$h0eBfRKZyyfOiEGd4F/Fa.luM3VdF3RU2285jgyGp6cGGdaa.21FO', 'Admin', 'admin', '2014-01-01', 0, 25, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
