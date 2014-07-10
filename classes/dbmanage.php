<?php
/**
 * Connects to database and handles SQL queries
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Feb 2014
 * 
 */
namespace Dandelion\Database;

class dbManage
{
    protected $dbConn;
    public static $connInfo = array(); /** This is loaded in when the configuration is loaded */

    /**
     * Connect to database and store PDO object in $dbConn variable
     */
    public function __construct() {
        try {
            switch (self::$connInfo['db_type']) {
                case 'mysql':
                    $db_connect = 'mysql:host=' . self::$connInfo['db_host'] . ';dbname=' . self::$connInfo['db_name'];
                    break;
                
                case 'sqliteDISABLED':
                    $db_connect = 'sqlite:' . dirname(dirname(__FILE__)) . '/database/' . self::$connInfo['sqlite_fn'];
                    self::$connInfo['db_user'] = null;
                    self::$connInfo['db_pass'] = null;
                    break;
                
                default:
                    throw new \Exception('Error: No database driver loaded');
                    break;
            }
            
            $conn = new \PDO($db_connect, self::$connInfo['db_user'], self::$connInfo['db_pass'], array(
                \PDO::ATTR_PERSISTENT => true 
            ));
            
            if (self::$connInfo['debug']) {
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            
            $this->dbConn = $conn;
        }
        catch (\PDOException $e) {
            if (DEBUG_ENABLED) {
                echo 'ERROR: ' . $e->getMessage();
            }
            else {
                echo 'Error 0x000185: Can\'t connect to database';
            }
        }
    }

    /**
     * Destroy PDO object when class is destroyed
     */
    public function __destruct() {
        $this->dbConn = null;
    }

    /**
     * Queries the database with provided statement
     *
     * @param string $stmt - Query statement as a string
     * @param array $paramArray - Array of variables that need to be bound to PDO
     * @param int $type - PDO value type (default: PDO::PARAM_STR)
     *       
     * @return array Containing the results of a SELECT query.
     *         True when performing any other query type.
     */
    public function queryDB($stmt, $paramArray = NULL, $type = \PDO::PARAM_STR) {
        try {
            $query = $this->dbConn->prepare($stmt);
            if (isset($paramArray)) {
                foreach ($paramArray as $key => $value) {
                    // To allow keys with and without semicolons ":"
                    // Remove any semicolons if present
                    $key = ltrim($key, ':');
                    $query->bindValue(':' . $key, $value, $type);
                }
            }
            $success = $query->execute();
            
            $command = substr($stmt, 0, 3);
            
            // If the statement was a SELECT, return a fetchAll
            if ($command != 'UPD' && $command != 'INS' && $command != 'DEL') {
                return $query->fetchall(\PDO::FETCH_ASSOC);
            }
            else {
                return $success;
            }
        }
        catch (\PDOException $e) {
            if (DEBUG_ENABLED) {
                echo 'ERROR: ' . $e->getMessage();
            }
            else {
                echo 'Error 0x000186: Error processing query';
            }
        }
    }

    /**
     * Selects all rows from $table
     *
     * @param string table - Table to get rows from
     *       
     * @return array Results of the query.
     */
    public function selectAll($table) {
        $stmt = 'SELECT * FROM ' . DB_PREFIX . $table;
        
        return $this->queryDB($stmt, NULL);
    }

    /**
     * Gets last inserted id number
     *
     * @return int Last inserted ID
     */
    public function lastInsertId() {
        return $this->dbConn->lastInsertId();
    }

    /**
     * Gets row count from last query
     *
     * @return int Row count of last query
     */
    public function rowCount() {
        return $this->dbConn->rowCount();
    }

    /**
     * Number of rows in $table
     *
     * @param string $table - Table name
     * @return int
     */
    public function numOfRows($table) {
        $stmt = 'SELECT COUNT(*) FROM `' . DB_PREFIX . $table . '`';
        
        return $this->queryDB($stmt, null)[0]['COUNT(*)'];
    }
    
    /**
     * The following are low level methods to interact with
     * the PDO object directly. The prefered method of interaction
     * is through the queryDB() method above. But these can be used
     * if desired.
     */
    
    /**
     * Turn off auto-commit mode
     */
    public function beginTransaction() {
        return $this->dbConn->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->dbConn->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollBack() {
        return $this->dbConn->rollBack();
    }
    
    /**
     * Execute an SQL statement
     * 
     * @param string $sql
     */
    public function exec($sql) {
        return $this->dbConn->exec($sql);
    }
    
    /**
     * Query the database and return a PDOStatement object
     * 
     * @param string $sql
     */
    public function query($sql) {
        return $this->dbConn->query($sql);
    }
    
    /**
     * End low level PDO methods.
     */
}
