<?php
/**
 * Repository for the authentication module
 */
namespace Dandelion\Repos;

use \Dandelion\Repos\Interfaces;

class AuthRepo extends BaseRepo implements Interfaces\AuthRepo
{
    public function isUser($username)
    {
        return $this->database->find($this->prefix.'user')
            ->whereEqual('username', $username)
            ->readRecord();
    }
}
