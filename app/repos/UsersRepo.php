<?php
/**
 * Repository for administration module
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class UsersRepo extends BaseRepo implements Interfaces\UsersRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'user';
    }

    public function saveUser($uid, $realname, $role, $theme, $first)
    {
        return $this->database->updateItem($this->table, $uid, [
            'fullname' => $realname,
            'group_id' => $role,
            'initial_login' => $first,
            'theme' => $theme
        ]);
    }

    public function saveUserCheesto($uid, $fullname)
    {
        return $this->database
            ->find($this->prefix.'cheesto')
            ->whereEqual('user_id', $uid)
            ->update(['fullname' => $fullname]);
    }

    public function createUser($username, $password, $fullname, $role, $date)
    {
        return $this->database->createItem($this->table, [
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'group_id' => $role,
            'created'  => $date
        ]);
    }

    public function createUserCheesto($uid, $fullname, $date)
    {
        return $this->database->createItem($this->prefix.'cheesto', [
            'fullname' => $fullname,
            'status' => 'Available',
            'message' => '',
            'returntime' => '00:00:00',
            'modified' => $date,
            'user_id' => $uid
        ]);
    }

    public function isUser($username)
    {
        return $this->database
            ->find($this->table)
            ->whereEqual('username', $username)
            ->readRecord();
    }

    public function resetPassword($uid, $pass)
    {
        return $this->database->updateItem($this->table, $uid, ['password' => $pass, 'initial_login' => 0]);
    }

    // TODO: Add a disabled field so a user's info isn't really deleted
    public function deleteUser($uid)
    {
        // May return 0 or 1 row affected
        $this->database
            ->find($this->prefix.'cheesto')
            ->whereEqual('user_id', $uid)
            ->delete();

        // May return 0 or 1 row affected
        $this->database
            ->find($this->prefix.'apikey')
            ->whereEqual('user_id', $uid)
            ->delete();

        // Should return 1 row affected
        return $this->database->deleteItem($this->table, $uid);
    }

    public function getUserRole($uid, $invert = false)
    {
        $group = $this->database->find($this->table);

        if ($invert) {
            return $group->whereNotEqual('id', $uid)->read();
        } else {
            return $group->whereEqual('id', $uid)->readField('group_id');
        }
    }

    public function getUsers($uid = null)
    {
        $fields = 'id, fullname, username, group_id, created, initial_login, theme';
        if ($uid) {
            return $this->database
                    ->find($this->table)
                    ->whereEqual('id', $uid)
                    ->read($fields);
        } else {
            return $this->database
                    ->find($this->table)
                    ->read($fields);
        }
    }
}
