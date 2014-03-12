<?php
/** MySQL/Maria specific table creation statements */
/** Create category table */
$stmt = 'CREATE TABLE IF NOT EXISTS `category` (
		  `cid` int(11) NOT NULL AUTO_INCREMENT,
		  `desc` varchar(255) NOT NULL,
		  `ptree` varchar(11) NOT NULL,
		  PRIMARY KEY (`cid`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create log table */
$stmt = 'CREATE TABLE IF NOT EXISTS `log` (
		  `logid` int(20) NOT NULL AUTO_INCREMENT,
		  `datec` date NOT NULL,
		  `timec` time NOT NULL,
		  `title` varchar(300) NOT NULL,
		  `entry` longtext NOT NULL,
		  `usercreated` varchar(255) NOT NULL,
		  `cat` varchar(3000) NOT NULL,
		  `edited` tinyint(1) NOT NULL DEFAULT \'0\',
		  PRIMARY KEY (`logid`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create presence (Cxeesto) table */
$stmt = 'CREATE TABLE IF NOT EXISTS `presence` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `uid` int(11) NOT NULL,
		  `realname` text NOT NULL,
		  `status` tinyint(2) NOT NULL,
		  `message` text NOT NULL,
		  `return` text NOT NULL,
		  `dmodified` datetime NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `User_ID` (`uid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create session_token table */
$stmt = 'CREATE TABLE IF NOT EXISTS `session_token` (
		  `session_id` int(255) NOT NULL AUTO_INCREMENT,
		  `token` varchar(256) NOT NULL,
		  `userid` int(10) NOT NULL,
		  `expire` int(255) NOT NULL,
		  PRIMARY KEY (`session_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create settings table */
$stmt = 'CREATE TABLE IF NOT EXISTS `settings` (
		  `settings_id` mediumint(9) NOT NULL AUTO_INCREMENT,
		  `name` tinytext NOT NULL,
		  `value` mediumtext NOT NULL,
		  PRIMARY KEY (`settings_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create users table */
$stmt = 'CREATE TABLE IF NOT EXISTS `users` (
		  `userid` int(255) NOT NULL AUTO_INCREMENT,
		  `username` varchar(255) NOT NULL,
		  `password` varchar(255) NOT NULL,
		  `realname` varchar(255) NOT NULL,
		  `settings_id` int(10) NOT NULL,
		  `role` varchar(255) NOT NULL,
		  `datecreated` date NOT NULL,
		  `firsttime` tinyint(1) NOT NULL DEFAULT \'2\',
		  `showlimit` int(3) NOT NULL DEFAULT \'25\',
  		  `theme` tinytext NOT NULL,
		  PRIMARY KEY (`userid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create admin user */
$stmt = 'INSERT INTO `presence` (`id`, `uid`, `realname`, `status`, `message`, `return`, `dmodified`)
		VALUES (1, 1, \'Admin\', 1, \'\', \'00:00:00\', \'2014-02-08 10:21:34\')';
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = 'INSERT INTO `users` (`userid`, `username`, `password`, `realname`,
			`settings_id`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`)
			VALUES (1, \'admin\', \'$2y$10$sRDlu.F6gPVM4kS/k7ESHO9PF0Z5pXk0J/SpuMa88E31/Lux1mfMy\',
			\'Admin\', 0, \'admin\', \'2014-02-08\', 2, 25, \'default\')';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create Initial Settings */
$stmt = 'INSERT INTO `settings` (`settings_id`, `name`, `value`)
			VALUES (1, \'slogan\', \'Website Slogan\')';
$exec = $dbConn->prepare($stmt);
$exec->execute();