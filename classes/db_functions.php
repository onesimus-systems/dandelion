<?php
/**
  * @brief DB connects to the database and stores the handle in $dbConn.
  *
  * This class can be called when needed to connect to the database
  * in other scripts. Previously a connection was always made,
  * now it is on-demand. <br /><br />This class is NOT called directly. 
  * It's only used to define how a database connection is being made.
  *
  * @author Lee Keitel
  * @date February 3, 2014
***/
class DB
{
    protected $dbConn;
    
    /** Attempts to start a connection with the database and store it in $dbConn */
    function __construct()
    {
        try {
	        switch($_SESSION['config']['db_type']) {
	        	case 'mysql':
		            $db_connect = 'mysql:host='.$_SESSION['config']['db_host'].';dbname='.$_SESSION['config']['db_name'];
		            break;
		            
	        	case 'sqlite':
		            $db_connect = 'sqlite:'.dirname(dirname(__FILE__)).'/database/'.$_SESSION['config']['sqlite_fn'];
		            $_SESSION['config']['db_user'] = null;
		            $_SESSION['config']['db_pass'] = null;
		            break;
		            
	        	default:
	        		throw new Exception('Error: No database driver loaded');
	        		break;
	        }
	        
            $conn = new PDO($db_connect, $_SESSION['config']['db_user'], $_SESSION['config']['db_pass'], array(
                PDO::ATTR_PERSISTENT => true
            ));
	        
            if ($_SESSION['config']['debug']) {
            	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            $this->dbConn = $conn;
        }
        catch(PDOException $e) {
        	if ($_SESSION['config']['debug']) {
            	echo 'ERROR: ' . $e->getMessage();
        	} else {
        		echo 'Error 0x000185: Can\'t connect to database';
        	}
        }
    }
}

/**
  * @brief dbManage is called in Dandelion to handle all database queries.
  *
  * This class is used whenever a database query
  * wants to be executed. queryDB is the main function.
  *
  * @author Lee Keitel
  * @date February 3, 2014
***/
class dbManage extends DB
{
    /** Default constructor starts a connection with the database */
    function __construct()
    {
        parent::__construct();
    }

    /** Normal database queries will use queryDB
      *
      * @param stmt - Query statement as a string
      * @param paramArray - Array of variables that need to be send to PDO
      *
      * @return Array containing the results of the query. Typically rows of data.
      */
    public function queryDB($stmt, $paramArray)
    {
        try {
            $query = $this->dbConn->prepare($stmt);
            if (isset($paramArray)) {
                $query->execute($paramArray);
            }
            else {
                $query->execute();
            }
            
            $command = substr($stmt, 0, 3); /**< This variable holds the first 3 characters of the query statement **/
            
            // If the statement was to update or insert, do not perform a fetchall
            if ($command == "SEL") {
                return $query->fetchall(PDO::FETCH_ASSOC);
            } else {
                return true;
            }
            
        }
        catch(PDOException $e) {
        	if ($_SESSION['config']['debug']) {
            	echo 'ERROR: ' . $e->getMessage();
        	} else {
        		echo 'Error 0x000186: Error processing query';
        	}
        }
    }
    
    /** Queries that require binding of integers use this.<br />
      * TODO: <br />-Create a one-stop function that  binds all values to the statement
      *       <br />-Find a way to pass-along the PARAM_INT argument
      *
      * @param stmt - Query statement as a string
      * @param paramArray - Array of variables that need to be binded to PDO
      *
      * @return Array containing the results of the query. Typically rows of data.
      */
    public function queryDBbind($stmt, $paramArray)
    {
        try {
            $query = $this->dbConn->prepare($stmt);
            foreach ($paramArray as $key => $value) {
                $query->bindValue($key, $value, PDO::PARAM_INT);  // bind the value to the statement
            }
            $query->execute();
            
            return $query->fetchall(PDO::FETCH_ASSOC);
            
        }
        catch(PDOException $e) {
        	if ($_SESSION['config']['debug']) {
            	echo 'ERROR: ' . $e->getMessage();
        	} else {
        		echo 'Error 0x000186: Error processing query';
        	}
        }
    }
    
    /** Selects all rows from $table
     *
     * @param table (string) - Table to get rows from
     *
     * @return Array containing the results of the query.
     */
    public function selectAll($table) {
    	$stmt = 'SELECT * FROM ' . $table;
    	
    	return $this->queryDB($stmt, NULL);
    }
    
    public function lastInsertId() {
    	return $this->dbConn->lastInsertId();
    }
}
