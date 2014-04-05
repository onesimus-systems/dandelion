<?php
/** SQLite specific table creation statements */
/** Create log table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_log`(
			logid INTEGER PRIMARY KEY AUTOINCREMENT,
			datec DATE NOT NULL,
			timec TIME NOT NULL,
			title TEXT NOT NULL,
			entry TEXT NOT NULL,
			usercreated INT NOT NULL,
			cat TEXT NOT NULL,
			edited INT DEFAULT 0
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create users table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_users` (
		  `userid` INTEGER PRIMARY KEY AUTOINCREMENT,
		  `username` TEXT NOT NULL,
		  `password` TEXT NOT NULL,
		  `realname` TEXT NOT NULL,
		  `role` TEXT NOT NULL,
		  `datecreated` DATE NOT NULL,
		  `firsttime` INT DEFAULT 2,
		  `showlimit` INT DEFAULT 25,
		  `theme` TEXT NOT NULL DEFAULT \'default\'
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create Cxeesto table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_presence` (
		  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
		  `uid` INT NOT NULL,
		  `realname` TEXT NOT NULL,
		  `status` INT NOT NULL,
		  `message` TEXT NOT NULL,
		  `return` TEXT NOT NULL,
		  `dmodified` DATETIME NOT NULL
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create category table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_category` (
		  `cid` INTEGER PRIMARY KEY AUTOINCREMENT,
		  `desc` TEXT NOT NULL,
		  `ptree` TEXT NOT NULL
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create session_tokens table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_session_token` (
		  `session_id` INTEGER PRIMARY KEY AUTOINCREMENT,
		  `token` TEXT NOT NULL,
		  `userid` INT NOT NULL,
		  `expire` INT NOT NULL
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create settings table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_settings` (
		  `settings_id` INTEGER PRIMARY KEY AUTOINCREMENT,
		  `name` TEXT NOT NULL,
		  `value` TEXT NOT NULL
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create admin user */
$stmt = 'INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`)
			VALUES (1, \'admin\', \'$2y$10$sRDlu.F6gPVM4kS/k7ESHO9PF0Z5pXk0J/SpuMa88E31/Lux1mfMy\', \'Admin\', \'admin\', \'2014-02-08\', 2, 25, \'default\'
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = 'INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `return`, `dmodified`)
			VALUES (1, 1, \'Admin\', 1, \'\', \'00:00:00\', \'2014-02-08 10:21:34\'
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create initial settings */
$stmt = 'INSERT INTO `dan_settings` (`settings_id`, `name`, `value`)
			VALUES (1, \'slogan\', \'Website Slogan\'
		)';
$exec = $dbConn->prepare($stmt);
$exec->execute();