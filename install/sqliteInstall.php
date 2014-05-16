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
          `theme` TEXT NOT NULL
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
          `returntime` TEXT NOT NULL,
          `dmodified` DATETIME NOT NULL
        )';
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create category table */
$stmt = 'CREATE TABLE IF NOT EXISTS `dan_category` (
          `cid` INTEGER PRIMARY KEY AUTOINCREMENT,
          `desc` TEXT NOT NULL,
          `pid` INTEGER NOT NULL
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
$stmt = "INSERT INTO `dan_presence` (`id`, `uid`, `realname`, `status`, `message`, `returntime`, `dmodified`)
        VALUES (1, 1, 'Admin', 1, '', '', '2014-01-01 00:00:00')";
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_users` (`userid`, `username`, `password`, `realname`, `role`, `datecreated`, `firsttime`, `showlimit`, `theme`) VALUES
        (1, 'admin', '\$2y\$10\$iMkjkCcdztMxamIul6sP2ur8IZJpNrJWYSXC6jsvl4vENwf2Vw1du', 'Admin', 'admin', '2014-01-01', 2, 25, '')";
$exec = $dbConn->prepare($stmt);
$exec->execute();

/** Create Initial Settings */
$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (1, 'app_title', 'Dandelion Web Log')";
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (2, 'slogan', 'Website tagline')";
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (3, 'default_theme', 'Halloween')";
$exec = $dbConn->prepare($stmt);
$exec->execute();

$stmt = "INSERT INTO `dan_settings` (`settings_id`, `name`, `value`) VALUES
        (4, 'cheesto_enabled', '1')";
$exec = $dbConn->prepare($stmt);
$exec->execute();
