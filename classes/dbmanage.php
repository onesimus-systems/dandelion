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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * The full GPLv3 license is available in LICENSE.md in the root.
 *
 * @author Lee Keitel
 * @date Feb 2014
 ***/
namespace Dandelion\Database;

class dbManage
{
    protected $dbConn;

    /**
     * Connect to database and store PDO object in $dbConn variable
     */
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
                    throw new \Exception('Error: No database driver loaded');
                    break;
            }

            $conn = new \PDO($db_connect, $_SESSION['config']['db_user'], $_SESSION['config']['db_pass'], array(
                \PDO::ATTR_PERSISTENT => true
            ));

            if ($_SESSION['config']['debug']) {
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }

            $this->dbConn = $conn;
        } catch(\PDOException $e) {
            if ($_SESSION['config']['debug']) {
                echo 'ERROR: ' . $e->getMessage();
            } else {
                echo 'Error 0x000185: Can\'t connect to database';
            }
        }
    }

    /**
     * Queries the database with provided statement
     *
     * @param string $stmt - Query statement as a string
     * @param array $paramArray - Array of variables that need to be bound to PDO
     * @param int $type - PDO value type (default: PDO::PARAM_STR)
     *
     * @return array Containing the results of a SELECT query.
     *        True when performing any other query type.
     */
    public function queryDB($stmt, $paramArray = NULL, $type = \PDO::PARAM_STR)
    {
        try {
            $query = $this->dbConn->prepare($stmt);
            if (isset($paramArray)) {
                foreach ($paramArray as $key => $value) {
                    // To allow keys with and without semicolons ":"
                    // Remove any semicolons if present
                    $key = trim($key, ':');
                    $query->bindValue(':'.$key, $value, $type);
                }
            }
            $query->execute();

            $command = substr($stmt, 0, 3);

            // If the statement was a SELECT, return a fetchAll
            if ($command != 'UPD' && $command != 'INS' && $command != 'DEL') {
                return $query->fetchall(\PDO::FETCH_ASSOC);
            } else {
                return true;
            }

        } catch(\PDOException $e) {
            if ($_SESSION['config']['debug']) {
                echo 'ERROR: ' . $e->getMessage();
            } else {
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
    public function selectAll($table)
    {
        $stmt = 'SELECT * FROM `'.DB_PREFIX.$table.'`';

        return $this->queryDB($stmt, NULL);
    }

    /**
     * Gets last inserted id number
     *
     * @return int Last inserted ID
     */
    public function lastInsertId()
    {
        return $this->dbConn->lastInsertId();
    }

    /**
     * Gets row count from last query
     *
     * @return int Row count of last query
     */
    public function rowCount()
    {
        return $this->dbConn->rowCount();
    }

    /**
     * Number of rows in $table
     *
     * @param string $table - Table name
     * @return int
     */
    public function numOfRows($table)
    {
    	$stmt = 'SELECT COUNT(*) FROM `'.DB_PREFIX.$table.'`';
    	
    	return $this->queryDB($stmt, null)[0]['COUNT(*)'];
    }
}
