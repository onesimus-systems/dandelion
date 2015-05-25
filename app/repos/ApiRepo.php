<?php
/**
 * Repository for the API
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class ApiRepo extends BaseRepo implements Interfaces\ApiRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'apikey';
    }

    public function getKey($keystring)
    {
        return $this->database->find($this->table)
            ->whereEqual('keystring', $keystring)
            ->readRecord();
    }
}
