<?php
/**
 * Storage object to interact with a MySQL database.
 *
 * This file is part of the Dandelion log application.
 *
 * @author Lee Keitel
 * @date   December 2014
 * @license GPLv3
 */

namespace Dandelion\Storage;

class mySqlDatabase implements \Dandelion\databaseConn {
    private $currentConn;
    private static $instance;
    public static $connInfo = array(); // Loaded from bootstrap file
    private $command;
    private $rawStatment;
    private $sqlStatement = array(
            'select' => '',
            'insert' => array(),
            'set'    => array(),
            'from'   => '',
            'where'  => array(),
            'orderby' => array(),
            'limit'  => ''
        );

    /**
     * Returns the an instance of the MySqlDatabase class. If one is already created, it will return it.
     */
    public static function getInstance() {
        if ($instance === NULL) {
            $instance = new self();
        }

        $instance->init();
        return $instance;
    }

    public function __destruct() {
        $this->currentConn = null;
        return;
    }

    // Prevent something from accidentally making multiple instances of the class
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    // Connect to the database and set the connection variable
    private function init() {
        try {
            $db_connect = 'mysql:host=' . self::$connInfo['db_host'] . ';dbname=' . self::$connInfo['db_name'];

            $conn = new \PDO($db_connect, self::$connInfo['db_user'], self::$connInfo['db_pass'], array(
                \PDO::ATTR_PERSISTENT => true
            ));

            if (DEBUG_ENABLED) {
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }

            $this->currentConn = $conn;
        }
        catch (\PDOException $e) {
            if (DEBUG_ENABLED) {
                echo 'ERROR: ' . $e->getMessage();
            }
            else {
                echo 'Error 0x000185: Can\'t connect to database';
            }
        }
        return;
    }

    private function clearStatement() {
        $this->sqlStatement = array(
            'select' => '',
            'insert' => array(),
            'set'    => array(),
            'from'   => '',
            'where'  => array(),
            'orderby' => array(),
            'limit'  => ''
        );
    }

    // Main database functions
    public function select($cols = '*') {
        $this->clearStatement();
        $this->sqlStatement['select'] = $cols;
        $this->command = 'select';
        return $this;
    }

    public function delete($joinCols = '') {
        $this->clearStatement();
        $this->sqlStatement['select'] = $joinCols;
        $this->command = 'delete';
        return $this;
    }

    public function update($table) {
        $this->clearStatement();
        $this->sqlStatement['from'] = $table;
        $this->command = 'update';
        return $this;
    }

    public function insert() {
        $this->clearStatement();
        $this->command = 'insert';
        return $this;
    }

    public function into($table, $cols) {
        $this->sqlStatement['from'] = $table;
        $this->sqlStatement['insert'] = $cols;
        return $this;
    }

    public function values($vals) {
        $this->sqlStatement['set'] = $vals;
        return $this;
    }

    public function set($colVals) {
        $this->sqlStatement['set'] = $colVals;
        return $this;
    }

    public function from($table) {
        $this->sqlStatement['from'] = $table;
        return $this;
    }

    public function where($conditions) {
        $this->sqlStatement['where'] = $conditions;
        return $this;
    }

    public function orderBy($col, $direction = 'ASC') {
        $this->sqlStatement['orderby']['col'] = $col;
        $this->sqlStatement['orderby']['dir'] = $direction;
        return $this;
    }

    public function limit($range) {
        $this->sqlStatement['limit'] = $range;
        return $this;
    }

    public function get($params = null, $type = \PDO::PARAM_STR) {
        return $this->queryDB($params, $type);
    }

    public function go($params = null, $type = \PDO::PARAM_STR) {
        return $this->queryDB($params, $type);
    }

    public function selectAll($table) {
        $this->select('*')->from(DB_PREFIX.$table);
        return $this;
    }

    public function showFormalStatement() {
        return $this->prepareStatement();
    }

    public function raw($sql) {
        $this->rawStatment = $sql;
        $this->command = 'raw';
        return $this;
    }

    /**
     * Build the SQL query string from supplied data
     */
    private function prepareStatement() {
        $stmt = '';
        switch ($this->command) {
            case 'raw':
                return $this->rawStatment;
                break;
            case 'select':
                $stmt = 'SELECT ' . $this->sqlStatement['select'] . ' FROM ' . $this->sqlStatement['from'];
                break;
            case 'delete':
                $stmt = 'DELETE ' . $this->sqlStatement['select'] . ' FROM ' . $this->sqlStatement['from'];
                break;
            case 'update':
                $stmt = 'UPDATE ' . $this->sqlStatement['from'] . ' SET ' . (is_array($this->sqlStatement['set']) ? implode(', ', $this->sqlStatement['set']) : $this->sqlStatement['set']);
                break;
            case 'insert':
                $stmt = 'INSERT INTO ' . $this->sqlStatement['from'] . '(' . implode(', ', $this->sqlStatement['insert']) . ') VALUES (' . implode(', ', $this->sqlStatement['set']) . ')';
                break;

        }
        if (!empty($this->sqlStatement['where'])) {
            if (is_array($this->sqlStatement['where'])) {
                $stmt = $stmt . ' WHERE ' . implode(', ', $this->sqlStatement['where']);
            } else {
                $stmt = $stmt . ' WHERE ' . $this->sqlStatement['where'];
            }
        }
        if (!empty($this->sqlStatement['orderby'])) {
            $stmt = $stmt . ' ORDER BY ' . $this->sqlStatement['orderby']['col'] . ' ' . $this->sqlStatement['orderby']['dir'];
        }
        if (!empty($this->sqlStatement['limit'])) {
            $stmt = $stmt . ' LIMIT ' . $this->sqlStatement['limit'];
        }
        return $stmt;
    }

    /**
     * Queries the database
     *
     * @param array $paramArray - Array of variables that need to be bound to the PDO
     * @param int $type - PDO value type (default: PDO::PARAM_STR)
     *
     * @return array Containing the results of a SELECT query.
     *         True when performing any other query type.
     */
    private function queryDB($paramArray = NULL, $type = \PDO::PARAM_STR) {
        $stmt = $this->prepareStatement();
        try {
            $query = $this->currentConn->prepare($stmt);
            if (isset($paramArray)) {
                foreach ($paramArray as $key => $value) {
                    // To allow keys with and without semicolons ":"
                    // Remove any semicolons if present
                    $key = ltrim($key, ':');
                    $query->bindValue(':' . $key, $value, $type);
                }
            }
            //var_dump($this->parms($stmt,$paramArray));
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
     * Create a prepared statement for troubleshooting
     */
    private function parms($string,$data) {
        $indexed=$data==array_values($data);
        foreach($data as $k=>$v) {
            if(is_string($v)) $v="'$v'";
            if($indexed) $string=preg_replace('/\?/',$v,$string,1);
            else $string=str_replace(":$k",$v,$string);
        }
        return $string;
    }
    /**
     * Gets last inserted id number
     *
     * @return int Last inserted ID
     */
    public function lastInsertId() {
        return $this->currentConn->lastInsertId();
    }

    /**
     * Gets row count from last query
     *
     * @return int Row count of last query
     */
    public function rowCount() {
        return $this->currentConn->rowCount();
    }

    /**
     * Number of rows in $table
     *
     * @param string $table - Table name
     * @return int
     */
    public function numOfRows($table) {
        $this->select('COUNT(*)')->from(DB_PREFIX.$table);
        return $this->get()[0]['COUNT(*)'];
    }
}
