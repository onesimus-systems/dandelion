<?php
namespace Dandelion\Storage\Contracts;

/**
 * Represents a connection to some database
 * The required functions reflect the typicall CRUD and allow
 * queries to be written in quasi-SQL form for clarity
 */
interface DatabaseConn
{
    public function insert();
    public function update($table);
    public function delete($joinCols = '');
    public function select($cols);
    public function into($table, $cols);
    public function values($vals);
    public function set($colVals);
    public function from($table);
    public function where($conditions);
    public function selectAll($table);
    public function raw($sql);
    public function get(array $params = [], $type = \PDO::PARAM_STR);
    public function go(array $params = [], $type = \PDO::PARAM_STR);
}
