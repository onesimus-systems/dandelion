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

class UserRepo extends BaseRepo implements Interfaces\UserRepo
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = $this->prefix.'user';
    }

    public function saveUser($uid, $realname, $role, $theme, $first, $disabled, $password = null)
    {
        $fields = [
            'fullname' => $realname,
            'group_id' => $role,
            'initial_login' => $first,
            'theme' => $theme,
            'disabled' => $disabled
        ];

        if ($password !== null) {
            $fields['password'] = $password;
        }

        return $this->database->updateItem($this->table, $uid, $fields);
    }

    public function createUser($username, $password, $fullname, $role, $date, $prompt)
    {
        return $this->database->createItem($this->table, [
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'group_id' => $role,
            'created'  => $date,
            'initial_login' => $prompt
        ]);
    }

    public function deleteUser($uid)
    {
        return $this->database->deleteItem($this->table, $uid);
    }

    public function getUserById($id)
    {
        return $this->getUserByField('id', $id);
    }

    public function getUserByName($username)
    {
        return $this->getUserByField('username', $username);
    }

    private function getUserByField($field, $condition)
    {
        $fields = 'id, fullname, username, password, group_id, created, initial_login, logs_per_page, theme, disabled';
        return $this->database
            ->find($this->table)
            ->whereEqual($field, $condition)
            ->read($fields)[0];
    }
}
