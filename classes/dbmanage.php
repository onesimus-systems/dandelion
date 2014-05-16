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
    public function __construct()
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
        } catch(PDOException $e) {
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
    public function __construct()
    {
        parent::__construct();
    }

    /** Queries the database with provided statement
      *
      * @param stmt - Query statement as a string
      * @param paramArray - Array of variables that need to be bound to PDO
      * @param type - PDO value type (default: PDO::PARAM_STR)
      *
      * @return Array containing the results of a SELECT query.
      * 		True when performing any other query type.
      */
    public function queryDB($stmt, $paramArray = NULL, $type = PDO::PARAM_STR)
    {
        try {
            $query = $this->dbConn->prepare($stmt);
            if (isset($paramArray)) {
                foreach ($paramArray as $key => $value) {
                    $query->bindValue(':'.$key, $value, $type);
                }
            }
            $query->execute();

            $command = substr($stmt, 0, 3);

            // If the statement was a SELECT, return a fetchAll
            if ($command != 'UPD' && $command != 'INS' && $command != 'DEL') {
                return $query->fetchall(PDO::FETCH_ASSOC);
            } else {
                return true;
            }

        } catch(PDOException $e) {
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
    public function selectAll($table)
    {
        $stmt = 'SELECT * FROM `'.DB_PREFIX.$table.'`';

        return $this->queryDB($stmt, NULL);
    }

    /** Gets last inserted id number
     *
     * @return Last inserted ID
     */
    public function lastInsertId()
    {
        return $this->dbConn->lastInsertId();
    }

    /** Gets row count from last query
     *
     * @return Row count of last query
     */
    public function rowCount()
    {
        return $this->dbConn->rowCount();
    }
}
