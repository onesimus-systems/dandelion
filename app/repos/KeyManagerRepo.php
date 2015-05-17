<?php
/**
 * Repository for API key manager
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class KeyManagerRepo extends BaseRepo implements Interfaces\KeyManagerRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'apikey';
    }

    public function getKeyForUser($uid)
    {
        return $this->database->find($this->table)->whereEqual('user_id', $uid)->readRecord();
    }

    public function saveKeyForUser($uid, $key)
    {
        return $this->database->createItem($this->table, ['keystring' => $key, 'user_id' => $uid]);
    }

    public function revoke($uid)
    {
        return $this->database->find($this->table)->whereEqual('user_id', $uid)->delete();
    }
}
