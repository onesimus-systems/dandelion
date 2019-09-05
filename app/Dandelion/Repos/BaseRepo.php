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

use PDO;
use SC\SC;
use Exception;
use Dandelion\Utils\Configuration as Config;

abstract class BaseRepo
{
    protected $database;
    private static $dbconnection;

    public function __construct()
    {
        if (is_null(self::$dbconnection)) {
            // Create new database connection object and cache
            $pdoParams = [];
            self::$dbconnection = new SC();
            $dbConfig = Config::get('db');

            if ($dbConfig['type'] !== 'sqlite') {
                $pdoParams = [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ];
            }

            // Connect to database
            $connectOK = self::$dbconnection->connect(
                $dbConfig['type'],
                $dbConfig['hostname'],
                $dbConfig['dbname'],
                $dbConfig['username'],
                $dbConfig['password'],
                $pdoParams);

            // Check for proper connection
            if (!$connectOK || !self::$dbconnection->pdo()) {
                throw new Exception("Error Connecting to Database", 1);
            }
        }

        // Set object specific prefix and database connection
        $this->prefix = Config::get('db')['tablePrefix'];
        $this->database = self::$dbconnection;
    }

    public function getPDO()
    {
        return $this->database->pdo();
    }
}
