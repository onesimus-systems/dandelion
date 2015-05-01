<?php
/**
 * Base repo for MySql repositories
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Storage\MysqlDatabase;

abstract class BaseMySqlRepo
{
    protected $database;

    public function __construct()
    {
        $this->database = MysqlDatabase::getInstance();
        $this->prefix = $this->database->getTablePrefix();
    }
}
