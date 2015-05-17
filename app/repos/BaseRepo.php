<?php
/**
 * Base repo
 */
namespace Dandelion\Repos;

use \SC\SC;
use \Dandelion\Utils\Configuration;

abstract class BaseRepo
{
    protected $database;

    public function __construct()
    {
        $this->database = SC::connect();
        $this->prefix = Configuration::getConfig()['db']['tablePrefix'];
    }
}
