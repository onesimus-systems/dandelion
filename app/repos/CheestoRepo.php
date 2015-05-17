<?php
/**
 * Repository for the Cheesto module
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class CheestoRepo extends BaseRepo implements Interfaces\CheestoRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'cheesto';
    }

    public function getAllStatuses()
    {
        return $this->database->find($this->table)->read();
    }

    public function getUserStatus($uid)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('user_id', $uid)
            ->readRecord();
    }

    public function updateStatus($uid, $status, $message, $return, $date)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('user_id', $uid)
            ->update([
                'message' => $message,
                'status' => $status,
                'returntime' => $return,
                'modified' => $date
            ]);
    }
}
