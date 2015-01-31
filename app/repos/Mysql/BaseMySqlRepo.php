<?php
/**
 * Base repo for MySql repositories
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Storage\MySqlDatabase;

class BaseMySqlRepo
{
    protected $database;

    public function __construct()
    {
        $this->database = MySqlDatabase::getInstance();
        $this->prefix = $this->database->getTablePrefix();
    }
}
