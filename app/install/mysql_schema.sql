-- Host: localhost
-- Generation Time: May 17, 2015 at 08:51 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dandy_base`
--

-- --------------------------------------------------------

--
-- Table structure for table `dan_apikey`
--

CREATE TABLE IF NOT EXISTS `dan_apikey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keystring` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keystring` (`keystring`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_category`
--

CREATE TABLE IF NOT EXISTS `dan_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` tinytext NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_category`
--

INSERT INTO `dan_category` (`id`, `description`, `parent`) VALUES
(1, 'Logs', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dan_cheesto`
--

CREATE TABLE IF NOT EXISTS `dan_cheesto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fullname` text NOT NULL,
  `status` tinytext NOT NULL,
  `message` text NOT NULL,
  `returntime` text NOT NULL,
  `modified` datetime NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dan_cheesto`
--

INSERT INTO `dan_cheesto` (`id`, `user_id`, `fullname`, `status`, `message`, `returntime`, `modified`, `disabled`) VALUES
(1, 1, 'Administrator', 'Available', '', '00:00:00', '2015-05-16 21:05:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dan_group`
--

CREATE TABLE IF NOT EXISTS `dan_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `permissions` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dan_group`
--

INSERT INTO `dan_group` (`id`, `name`, `permissions`) VALUES
(1, 'user', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:9:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:10:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:11:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:0;}'),
(2, 'admin', 'a:15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:9:"createcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:10:"createuser";b:1;s:8:"edituser";b:1;s:10:"deleteuser";b:1;s:11:"creategroup";b:1;s:9:"editgroup";b:1;s:11:"deletegroup";b:1;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:1;}'),
(3, 'guest', 'a:15:{s:9:"createlog";b:0;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:9:"createcat";b:0;s:7:"editcat";b:0;s:9:"deletecat";b:0;s:10:"createuser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:11:"creategroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:0;s:5:"admin";b:0;}');

-- --------------------------------------------------------

--
-- Table structure for table `dan_log`
--

CREATE TABLE IF NOT EXISTS `dan_log` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `title` varchar(300) NOT NULL,
  `body` longtext NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` text NOT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `dan_session`
--

CREATE TABLE IF NOT EXISTS `dan_session` (
  `id` char(32) NOT NULL,
  `data` mediumtext,
  `last_accessed` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dan_user`
--

CREATE TABLE IF NOT EXISTS `dan_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` tinytext NOT NULL,
  `password` tinytext NOT NULL,
  `fullname` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `created` date NOT NULL,
  `initial_login` tinyint(1) NOT NULL DEFAULT '1',
  `logs_per_page` int(3) NOT NULL DEFAULT '25',
  `theme` tinytext NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `dan_user`
--

INSERT INTO `dan_user` (`id`, `username`, `password`, `fullname`, `group_id`, `created`, `initial_login`, `logs_per_page`, `theme`, `disabled`) VALUES
(1, 'admin', '$2y$10$zibMP6jZw5PRMGHGdo/JzeXkb3re0WEIulmkgRe4PC76GwT4M8G5u', 'Administrator', 2, '2015-01-01', 1, 25, '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
