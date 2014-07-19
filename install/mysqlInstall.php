<?php
/** MySQL/Maria specific table creation statements */
/** Create API key table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_apikeys` (
          `keystring` varchar(255) NOT NULL,
          `user` int(11) NOT NULL,
          `expires` int(11) NOT NULL,
          PRIMARY KEY (`keystring`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$exec = $conn->prepare($stmt);
$exec->execute();

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
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create mail table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_mail` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `isItRead` varchar(1) NOT NULL DEFAULT \'0\',
          `toUser` smallint(6) NOT NULL,
          `fromUser` smallint(6) NOT NULL,
          `subject` tinytext NOT NULL,
          `body` text NOT NULL,
          `deleted` tinyint(4) NOT NULL DEFAULT \'0\',
          `dateSent` date NOT NULL,
          `timeSent` time NOT NULL,
          PRIMARY KEY (`id`),
          KEY `to` (`toUser`)
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
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create rights table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_rights` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `role` varchar(255) NOT NULL,
          `permissions` text NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `role` (`role`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create sessions table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_sessions` (
          `id` char(32) NOT NULL,
          `data` mediumtext,
          `last_accessed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create settings table */
$stmt ='CREATE TABLE IF NOT EXISTS `dan_settings` (
          `settings_id` smallint(9) NOT NULL AUTO_INCREMENT,
          `name` tinytext NOT NULL,
          `value` mediumtext NOT NULL,
          PRIMARY KEY (`settings_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6';
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
          `mailCount` int(11) NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`userid`),
          UNIQUE KEY `userid` (`userid`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2';
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create admin user */
$stmt = "INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`)
        VALUES (1, 1, 'Admin', 1, '', '', '2014-01-01 00:00:00');";
$exec = $conn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`, `mailCount`)
        VALUES (1, 'admin', '\$2y\$10\$iMkjkCcdztMxamIul6sP2ur8IZJpNrJWYSXC6jsvl4vENwf2Vw1du', 'Admin', 'admin', '2014-01-01', 2, 25, '', 0);":
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create Initial Settings */
$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (1, 'app_title', 'Dandelion Web Log'),
        (2, 'slogan', 'Website Slogan'),
        (3, 'default_theme', 'Halloween'),
        (4, 'cheesto_enabled', '1'),
        (5, 'public_api', '0');";
$exec = $conn->prepare($stmt);
$exec->execute();

/** Create Initial rights groups */
$stmt = <<< 'SQL'
        INSERT INTO `dan_rights` (`id`, `role`, `permissions`) VALUES
        (1, 'user', 'O:8:"stdClass":15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:6:"addcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:0;s:7:"adduser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:8:"addgroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:0;}'),
        (2, 'admin', 'O:8:"stdClass":15:{s:9:"createlog";b:1;s:7:"editlog";b:1;s:7:"viewlog";b:1;s:6:"addcat";b:1;s:7:"editcat";b:1;s:9:"deletecat";b:1;s:7:"adduser";b:1;s:8:"edituser";b:1;s:10:"deleteuser";b:1;s:8:"addgroup";b:1;s:9:"editgroup";b:1;s:11:"deletegroup";b:1;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:1;s:5:"admin";b:1;}'),
        (3, 'guest', 'O:8:"stdClass":15:{s:9:"createlog";b:0;s:7:"editlog";b:0;s:7:"viewlog";b:1;s:6:"addcat";b:0;s:7:"editcat";b:0;s:9:"deletecat";b:0;s:7:"adduser";b:0;s:8:"edituser";b:0;s:10:"deleteuser";b:0;s:8:"addgroup";b:0;s:9:"editgroup";b:0;s:11:"deletegroup";b:0;s:11:"viewcheesto";b:1;s:13:"updatecheesto";b:0;s:5:"admin";b:0;}');
SQL;
$exec = $conn->prepare($stmt);
$exec->execute();