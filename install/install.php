<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$CONFIG = array(
		'db_user' => $_POST['dbuname'],
		'db_pass' => $_POST['dbpass'],
		'db_host' => $_POST['dbhost'],
		'db_name' => $_POST['dbname'],
		'db_type' => 'mysql'
	);
	
	try {
        switch($CONFIG['db_type']) {
        	case 'mysql':
	            $db_connect = 'mysql:host='.$CONFIG['db_host'].';dbname='.$CONFIG['db_name'];
	            break;
	            
        	case 'sqlite':
	            $db_connect = 'sqlite:'.dirname(dirname(__FILE__)).'/database.sq3';
	            $CONFIG['db_user'] = null;
	            $CONFIG['db_pass'] = null;
	            break;
	            
        	default:
        		throw new Exception('Error: No database driver loaded');
        		break;
        }
        
        $conn = new PDO($db_connect, $CONFIG['db_user'], $CONFIG['db_pass'], array(
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

		include_once 'mysqlInstall.php'; // Once the user can choose, make this dynamically load the correct creation statements
		
		/** Write config file */
		$handle = fopen('../config/config.php', 'w');
		$newFile = "<?php\n";
		$newFile .= "\$CONFIG=array(\n";
		$newFile .= "'db_type' => '".$CONFIG['db_type']."',\n";
		$newFile .= "'db_name' => '".$CONFIG['db_name']."',\n";
		$newFile .= "'db_host' => '".$CONFIG['db_host']."',\n";
		$newFile .= "'db_user' => '".$CONFIG['db_user']."',\n";
		$newFile .= "'db_pass' => '".$CONFIG['db_pass']."',\n";
		$newFile .= "'installed' => true\n";
		$newFile .= ");";
		
		fwrite($handle, $newFile);
		
		header( 'Location: ../index.php' );
	}
	catch(PDOException $e) {
		echo 'Error setting up database.';
	}
}
else {
    header( 'Location: ../install.php' );
}