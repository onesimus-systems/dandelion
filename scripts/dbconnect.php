<?php
/**
  * This file is a global file which is included on every page.
  * This script is used mainly for database connections,
  * swapping between the test and prod databases,
  * include other needed PHP scripts,
  * and set the app cookie to keep users logged in.
***/

// Remove these two lines during deployment
error_reporting(E_ALL);
ini_set('display_errors', True);

session_start();
$cookie_name = 'dandelionrememt'; // Used for login remembering (soon to go away)
define('D_VERSION', '4.0.0');       // Defines current Dandelion version
 
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
    protected $dbConn;                  /**< $dbConn is passed to the dbManage extended class and is used to interact with the database. */
    
    /** Attempts to start a connection with the database and store it in $dbConn */
    function __construct()
    {
        if (file_exists('config/config.php')) {
            include 'config/config.php';
        }
        else {
            include '../config/config.php';
        }

        try {
            $conn = new PDO('mysql:host='.$CONFIG['db_host'].';dbname='.$CONFIG['db_name'], $CONFIG['db_user'], $CONFIG['db_pass'], array(
                PDO::ATTR_PERSISTENT => true
            ));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //comment when deployed
            $this->dbConn = $conn;
        }
        catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage(); //comment when deployed
            echo 'Database Status: <span class="bad">Not Connected</span>';
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
            
        } catch(PDOException $e) {
            echo 'Database error';
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
            
        } catch(PDOException $e) {
            echo 'Database error: ' . $e;
        }
    }
}

/** Global function to check if a user is logged in to Dandelion
  */
function checkLogIn() {
    //Check for auth cookie, if set check against session_token table to see it session is still valid
    global $cookie_name;
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
    $cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : false;
    
    if ($loggedin) { // If a current PHP session is running, log in
        return true;
    }

    /* If a PHP session has expired, but a person is still logged in,
     * Replace the loggedin and realName session variables, and go in
     *
     * This function will soon go away. I have since learned more about
     * PHP sessions and will able to remove this function.
    */
    if ($cookie) {    
        // Connect to DB
        $conn = new dbManage;
        
        list ($user, $token, $mac) = explode(':', $cookie);

        // Grab information from session_token
        $stmt = 'SELECT * FROM session_token WHERE userid = :id';
        $params = array('id' => $user);
        $auth_user = $conn->queryDB($stmt, $params);
        
        // If a result was returned, check if it has expired
        if ($auth_user['expire']['expire']) {
            if ($mac === hash_hmac('sha256', $user . ':' . $token, "usi.edu")
                AND $auth_user[0]['token'] === $token
                AND $auth_user[0]['expire'] >= time()) {
                
                $stmt = 'SELECT * FROM users WHERE userid = :user';
                $param = array('user' => $user);
                
                $sel_user = $conn->queryDB($stmt, $param);
                
                $_SESSION['userInfo'] = $sel_user[0];
                $_SESSION['loggedin'] = true;
                return true;
            }
        }
    }

    // No session and no session token, need to log in
    else {
        return false;
    }
}