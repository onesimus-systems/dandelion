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

    public function saveUser($uid, $realname, $role, $theme, $first, $disabled)
    {
        return $this->database->updateItem($this->table, $uid, [
            'fullname' => $realname,
            'group_id' => $role,
            'initial_login' => $first,
            'theme' => $theme,
            'disabled' => $disabled
        ]);
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

    public function createUserCheesto($uid, $date)
    {
        return $this->database->createItem($this->prefix.'cheesto', [
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
            ->whereEqual('disabled', 0)
            ->readRecord();
    }

    public function resetPassword($uid, $pass)
    {
        return $this->database
            ->updateItem($this->table, $uid, ['password' => $pass, 'initial_login' => 0]);
    }

    public function deleteUser($uid)
    {
        return $this->database->deleteItem($this->table, $uid);
    }

    public function disableUser($uid, $disabled = 1)
    {
        return $this->database
            ->updateItem($this->table, $uid, ['disabled' => $disabled]);
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

    public function getUsers($uid = null, $disabled = 0)
    {
        $fields = 'id, fullname, username, group_id, created, initial_login, theme, disabled';
        if ($uid) {
            return $this->database
                    ->find($this->table)
                    ->whereEqual('id', $uid)
                    //->whereEqual('disabled', $disabled)
                    ->read($fields);
        } else {
            return $this->database
                    ->find($this->table)
                    //->whereEqual('disabled', $disabled)
                    ->read($fields);
        }
    }
}
