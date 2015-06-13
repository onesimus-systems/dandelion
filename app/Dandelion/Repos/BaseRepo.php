<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion\Repos;

use \SC\SC;
use \Dandelion\Utils\Configuration;

abstract class BaseRepo
{
    protected $database;
    private static $dbconnection;

    public function __construct()
    {
        if (is_null(self::$dbconnection)) {
            // Create new database connection object and cache
            self::$dbconnection = new SC();
            $dbConfig = Configuration::getConfig()['db'];

            // Connect to database
            self::$dbconnection->connect(
                $dbConfig['type'],
                $dbConfig['hostname'],
                $dbConfig['dbname'],
                $dbConfig['username'],
                $dbConfig['password'],
                [\PDO::ATTR_PERSISTENT => true]);

            // Check for proper connection
            if (!self::$dbconnection) {
                throw new \Exception("Error Connecting to Database", 1);
            }
        }

        // Set object specific prefix and database connection
        $this->prefix = Configuration::getConfig()['db']['tablePrefix'];
        $this->database = self::$dbconnection;
    }
}
