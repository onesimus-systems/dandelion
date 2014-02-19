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
        if (is_writable('../config')) { // Is it possible to write the config file?
            switch($CONFIG['db_type']) {
                case 'mysql':
                    $db_connect = 'mysql:host='.$CONFIG['db_host'].';dbname='.$CONFIG['db_name'];
                    $db_user = $CONFIG['db_user'];
                    $db_pass = $CONFIG['db_pass'];
                    $stmt = 'SHOW TABLES';
                    break;
                    
                case 'sqlite':
                    $db_connect = 'sqlite:'.dirname(dirname(__FILE__)).'/database.sq3';
                    $db_user = null;
                    $db_pass = null;
                    $stmt = '.tables';
                    break;
                    
                default:
                    throw new Exception('Error: No database driver loaded');
                    break;
            }
            
            $conn = new PDO($db_connect, $db_user, $db_pass);
            
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbConn = $conn;
            
            /** Drop any existing tables in the database */
            $exec = $dbConn->prepare($stmt);
            $exec->execute();
            
            $allTables = $exec->fetchAll();
            
            // Change this to execute DROP TABLE for each table
            // SQLite doesn't support DROP TABLE(S)
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

            include_once $CONFIG['db_type'].'Install.php'; // Load the database specific creation commands
            
            $conn = null;
            
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