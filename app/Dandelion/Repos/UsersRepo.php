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

class UsersRepo extends BaseRepo implements Interfaces\UsersRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'user';
    }

    public function deleteUser($uid)
    {
        return $this->database->deleteItem($this->table, $uid);
    }

    public function getUserRole($uid, $invert = false)
    {
        $group = $this->database->find($this->table);

        if ($invert) {
            return $group->whereNotEqual('id', $uid)->read();
        }

        return $group->whereEqual('id', $uid)->readField('group_id');
    }

    private function fixUserFieldTypes(&$record)
    {
        $record['id'] = (int) $record['id'];
        $record['group_id'] = (int) $record['group_id'];
        $record['initial_login'] = (int) $record['initial_login'];
        $record['disabled'] = (bool) $record['disabled'];
    }

    public function getUsers($uid = null, $disabled = 0)
    {
        $fields = 'id, fullname, username, group_id, created, initial_login, theme, disabled';
        $statement = $this->database
                        ->find($this->table);

        if ($uid) {
            $statement->whereEqual('id', $uid);
        }

        $records = $statement->read($fields);

        foreach ($records as &$record) {
            $this->fixUserFieldTypes($record);
        }

        return $records;
    }
}
