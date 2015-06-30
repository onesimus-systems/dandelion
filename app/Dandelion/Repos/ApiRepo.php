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

use Dandelion\Repos\Interfaces;

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
