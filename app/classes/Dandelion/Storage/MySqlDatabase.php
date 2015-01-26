<?php
/**
 * Storage object to interact with a MySQL database.
 */
namespace Dandelion\Storage;

use \Dandelion\Storage\Contracts\DatabaseConn;

class MySqlDatabase implements DatabaseConn
{
    private $currentConn;
    private static $instance;
    public static $dbPrefix;
    public static $connInfo = array(); // Loaded from bootstrap file
    private $command;
    private $rawStatment;
    private $sqlStatement;
    private $blankStatement = array(
            'select' => '',
            'insert' => array(),
            'set'    => array(),
            'from'   => '',
            'where'  => array(),
            'orderby' => array(),
            'limit'  => '',
            'collate' => ''
        );

    // Prevent something from accidentally making multiple instances of the class
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
    public function __destruct()
    {
        $this->currentConn = null;
        return;
    }

    /**
     * Returns the an instance of the MySqlDatabase class. If one is already created, it will return it.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->init();
        }

        return self::$instance;
    }

    // Connect to the database and set the connection variable
    private function init()
    {
        try {
            $db_connect = 'mysql:host=' . self::$connInfo['db_host'] . ';dbname=' . self::$connInfo['db_name'];

            $conn = new \PDO($db_connect, self::$connInfo['db_user'], self::$connInfo['db_pass'], array(
                \PDO::ATTR_PERSISTENT => true
            ));

            if (true) {
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }

            $this->currentConn = $conn;
            $this->sqlStatement = $this->blankStatement;
        } catch (\PDOException $e) {
            if (true) {
                echo 'ERROR: ' . $e->getMessage();
            } else {
                echo 'Error 0x000185: Can\'t connect to database';
            }
        }
        return;
    }

    private function clearStatement()
    {
        $this->sqlStatement = $this->blankStatement;
    }

    // Main database functions
    public function select($cols = '*')
    {
        $this->clearStatement();
        $this->sqlStatement['select'] = $cols;
        $this->command = 'select';
        return $this;
    }

    public function selectAll($table)
    {
        $this->select()->from(DB_PREFIX.$table);
        return $this;
    }

    public function delete($joinCols = '')
    {
        $this->clearStatement();
        $this->sqlStatement['select'] = $joinCols;
        $this->command = 'delete';
        return $this;
    }

    public function update($table)
    {
        $this->clearStatement();
        $this->sqlStatement['from'] = $table;
        $this->command = 'update';
        return $this;
    }

    public function insert()
    {
        $this->clearStatement();
        $this->command = 'insert';
        return $this;
    }

    public function raw($sql)
    {
        $this->rawStatment = $sql;
        $this->command = 'raw';
        return $this;
    }

    // Auxillary and filter functions
    public function into($table, $cols)
    {
        $this->sqlStatement['from'] = $table;
        $this->sqlStatement['insert'] = $cols;
        return $this;
    }

    public function values($vals)
    {
        $this->sqlStatement['set'] = $vals;
        return $this;
    }

    public function set($colVals)
    {
        $this->sqlStatement['set'] = $colVals;
        return $this;
    }

    public function from($table)
    {
        $this->sqlStatement['from'] = $table;
        return $this;
    }

    public function where($conditions)
    {
        $this->sqlStatement['where'] = $conditions;
        return $this;
    }

    public function collate($collation)
    {
        $this->sqlStatement['collate'] = $collation;
        return $this;
    }

    public function orderBy($col, $direction = 'ASC')
    {
        $this->sqlStatement['orderby']['col'] = $col;
        $this->sqlStatement['orderby']['dir'] = $direction;
        return $this;
    }

    public function limit($range)
    {
        $this->sqlStatement['limit'] = $range;
        return $this;
    }

    // Perform query
    public function get($params = [], $type = \PDO::PARAM_STR)
    {
        $stmt = $this->prepareStatement();
        return $this->queryDB($stmt, $params, $type, true);
    }

    public function go($params = [], $type = \PDO::PARAM_STR)
    {
        $stmt = $this->prepareStatement();
        return $this->queryDB($stmt, $params, $type);
    }

    public function getStatement()
    {
        return $this->prepareStatement();
    }

    /**
     * Build the SQL query string from supplied data
     */
    private function prepareStatement()
    {
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
        if (!empty($this->sqlStatement['collate'])) {
            $stmt = $stmt . ' COLLATE ' . $this->sqlStatement['collate'];
        }
        return $stmt;
    }

    /**
     * Queries the database
     *
     * @param string $stmt - Statement to execute
     * @param array $paramArray - Array of variables that need to be bound to the PDO
     * @param int $type - PDO value type (default: PDO::PARAM_STR)
     * @param bool $returnArray - To return an associative array or not
     *
     * @return mixed
     */
    private function queryDB($stmt = '', array $paramArray = [], $type = \PDO::PARAM_STR, $returnArray = false)
    {
        try {
            $query = $this->currentConn->prepare($stmt);
            if (!empty($paramArray)) {
                foreach ($paramArray as $key => $value) {
                    // Normalize parameters
                    $key = ltrim($key, ':');
                    $query->bindValue(':' . $key, $value, $type);
                }
            }
            $success = $query->execute();

            if ($returnArray) {
                return $query->fetchall(\PDO::FETCH_ASSOC);
            } else {
                return $success;
            }
        } catch (\PDOException $e) {
            if (DEBUG_ENABLED) {
                echo 'ERROR: ' . $e->getMessage();
            } else {
                echo 'Error 0x000186: Error processing query';
            }
        }
    }

    /**
     * Gets last inserted id number
     *
     * @return int Last inserted ID
     */
    public function lastInsertId()
    {
        return $this->currentConn->lastInsertId();
    }

    /**
     * Gets row count from last query
     *
     * @return int Row count of last query
     */
    public function rowCount()
    {
        return $this->currentConn->rowCount();
    }

    /**
     * Number of rows in $table
     *
     * @param string $table - Table name
     * @return int
     */
    public function numOfRows($table)
    {
        $this->select('COUNT(*)')->from(DB_PREFIX.$table);
        return $this->get()[0]['COUNT(*)'];
    }
}
