<?php
/** MySQL/Maria specific table creation statements */
/** Create category table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_category` (
          `cid` int(11) NOT NULL AUTO_INCREMENT,
          `description` varchar(255) NOT NULL,
          `pid` int(11) NOT NULL,
          PRIMARY KEY (`cid`),
          UNIQUE KEY `cid` (`cid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create log table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_log` (
          `logid` int(20) NOT NULL AUTO_INCREMENT,
          `datec` date NOT NULL,
          `timec` time NOT NULL,
          `title` varchar(300) NOT NULL,
          `entry` longtext NOT NULL,
          `usercreated` int(11) NOT NULL,
          `cat` text NOT NULL,
          `edited` tinyint(1) NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`logid`),
          UNIQUE KEY `logid` (`logid`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create presence (Cxeesto) table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_presence` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `uid` int(11) NOT NULL,
          `realname` text NOT NULL,
          `status` tinyint(2) NOT NULL,
          `message` text NOT NULL,
          `returntime` text NOT NULL,
          `dmodified` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create settings table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_settings` (
      `settings_id` smallint(9) NOT NULL AUTO_INCREMENT,
      `name` tinytext NOT NULL,
      `value` mediumtext NOT NULL,
      PRIMARY KEY (`settings_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create users table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_users` (
          `userid` smallint(6) NOT NULL AUTO_INCREMENT,
          `username` varchar(255) NOT NULL,
          `password` varchar(255) NOT NULL,
          `realname` varchar(255) NOT NULL,
          `role` varchar(255) NOT NULL,
          `datecreated` date NOT NULL,
          `firsttime` tinyint(1) NOT NULL DEFAULT \'2\',
          `showlimit` int(3) NOT NULL DEFAULT \'25\',
          `theme` tinytext NOT NULL,
          PRIMARY KEY (`userid`),
          UNIQUE KEY `userid` (`userid`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create admin user */
$stmt = "INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`)
        VALUES (1, 1, 'Admin', 1, '', '', '2014-01-01 00:00:00')";
$exec = $conn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`) VALUES
        (1, 'admin', '\$2y\$10\$iMkjkCcdztMxamIul6sP2ur8IZJpNrJWYSXC6jsvl4vENwf2Vw1du', 'Admin', 'admin', '2014-01-01', 2, 25, '')";
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create Initial Settings */
$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (1, 'app_title', 'Dandelion Web Log'),
        (2, 'slogan', 'Website tagline'),
        (3, 'default_theme', 'Halloween'),
        (4, 'cheesto_enabled', '1')";
$exec = $conn->prepare($stmt);
$exec->execute();
