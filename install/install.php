<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$CONFIG = array(
		'db_user' => $_POST['dbuname'],
		'db_pass' => $_POST['dbpass'],
		'db_host' => $_POST['dbhost'],
		'db_name' => $_POST['dbname']
	);
	
	try {
        if (is_writable('../config')) { // Is it possible to write the config file?
            $conn = new PDO('mysql:host='.$CONFIG['db_host'].';dbname='.$CONFIG['db_name'], $CONFIG['db_user'], $CONFIG['db_pass'], array(
                PDO::ATTR_PERSISTENT => true
            ));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbConn = $conn;
            
            /** Drop any existing tables in the database */
            $stmt = 'SHOW TABLES';
            $exec = $dbConn->prepare($stmt);
            $exec->execute();
            $allTables = $exec->fetchAll();
            
            if ($allTables[0]) {
                $drop = 'DROP TABLES ';
                
                foreach ($allTables as $table) {
                    $drop .= '`'.$table[0].'`,';
                }
                
                $drop = rtrim($drop, ',');
                $drop .= ';';
                
                $exec = $dbConn->prepare($drop);
                $exec->execute();
            }

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
                      `settings_id` int(255) NOT NULL AUTO_INCREMENT,
                      `message` varchar(1000) NOT NULL,
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
                        `settings_id`, `role`, `datecreated`, `firsttime`, `showlimit`)
                        VALUES (1, \'admin\', \'$2y$10$sRDlu.F6gPVM4kS/k7ESHO9PF0Z5pXk0J/SpuMa88E31/Lux1mfMy\',
                        \'Admin\', 0, \'admin\', \'2014-02-08\', 2, 25)';
            $exec = $dbConn->prepare($stmt);
            $exec->execute();
            
            $handle = fopen('../config/config.php', 'w');
            $newFile = "<?php\n";
            $newFile .= "\$CONFIG=array(\n";
            $newFile .= "'db_name' => '".$CONFIG['db_name']."',\n";
            $newFile .= "'db_host' => '".$CONFIG['db_host']."',\n";
            $newFile .= "'db_user' => '".$CONFIG['db_user']."',\n";
            $newFile .= "'db_pass' => '".$CONFIG['db_pass']."',\n";
            $newFile .= "'installed' => true\n";
            $newFile .= ");";
            fwrite($handle, $newFile);
            
            // Change config directory to user:readonly for security
            chmod('../config/config.php', 0400);
            chmod('../config', 0500);
		
            header( 'Location: ../scripts/logout.php' );
        }
        else {
            echo 'Dandelion does not have sufficient write permissions to create configuration.<br />Please make the ./config directory writeable to Dandelion and try again.';
        }
	}
	catch(PDOException $e) {
		echo 'Error setting up database.';
	}
}
else {
    header( 'Location: ../install.php' );
}