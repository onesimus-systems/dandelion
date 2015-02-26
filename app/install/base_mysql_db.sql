-- Host: localhost
-- Generation Time: Feb 01, 2015 at 05:51 PM
-- Server version: 5.5.41-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dandelion_base`
--

-- --------------------------------------------------------

--
-- Table structure for table `dan_apikeys`
--

CREATE TABLE IF NOT EXISTS `dan_apikeys` (
  `keystring` varchar(255) COLLATE utf8_bin NOT NULL,
  `user` int(11) NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`keystring`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_category`
--

INSERT INTO `dan_category` (`cid`, `description`, `pid`) VALUES
  (1, 'Logs', 0);

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
  `usercreated` int(11) NOT NULL,
  `cat` text NOT NULL,
  `edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logid`),
  UNIQUE KEY `logid` (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_mail`
--

CREATE TABLE IF NOT EXISTS `dan_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isItRead` varchar(1) NOT NULL DEFAULT '0',
  `toUser` smallint(6) NOT NULL,
  `fromUser` smallint(6) NOT NULL,
  `subject` tinytext NOT NULL,
  `body` text NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `dateSent` date NOT NULL,
  `timeSent` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to` (`toUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_presence`
--

INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`) VALUES
  (1, 1, 'Admin', 0, '', '00:00:00', '2015-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `dan_rights`
--

CREATE TABLE IF NOT EXISTS `dan_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) NOT NULL,
  `permissions` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dan_rights`
--

INSERT INTO `dan_rights` (`id`, `role`, `permissions`) VALUES
  (1, 'user', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:6:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:0;s:7:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:8:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:0;}'),
  (2, 'admin', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:6:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:7:"createuser";b:1;s:8:"edituser";b:1;s:10:"deleteuser";b:1;s:8:"creategroup";b:1;s:9:"editgroup";b:1;s:11:"deletegroup";b:1;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:1;}'),
  (3, 'guest', 'a:15:{s:9:"createlog";b:0;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:6:"createcat";b:0;s:7:"editcat";b:0;s:9:"deletecat";b:0;s:7:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:8:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:0;s:5:"admin";b:0;}');

-- --------------------------------------------------------

--
-- Table structure for table `dan_sessions`
--

CREATE TABLE IF NOT EXISTS `dan_sessions` (
  `id` char(32) NOT NULL,
  `data` mediumtext,
  `last_accessed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `firsttime` tinyint(1) NOT NULL DEFAULT '1',
  `showlimit` int(3) NOT NULL DEFAULT '25',
  `theme` tinytext NOT NULL,
  `mailCount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_users`
--

INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`, `mailCount`) VALUES
  (1, 'admin', '$2y$10$prAlpv4vy07ONN53Kyfgse8HZ8npUX993CeEJ5xPktvtUNDuvYgTm', 'Admininistrator', 'admin', '2015-01-01', 1, 25, '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
