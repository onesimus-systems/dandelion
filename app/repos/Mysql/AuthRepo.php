<?php
/**
 * MySQL repository for the authentication module
 */
namespace Dandelion\Repos\Mysql;

use \Dandelion\Repos\Interfaces;

class AuthRepo extends BaseMySqlRepo implements Interfaces\AuthRepo
{
    public function isUser($username)
    {
        $this->database->select()
            ->from($this->prefix . 'users')
            ->where('username = :user');

        return $this->database->getFirst(['user' => $username]);
    }
}
