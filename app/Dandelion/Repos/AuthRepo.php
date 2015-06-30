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

class AuthRepo extends BaseRepo implements Interfaces\AuthRepo
{
    public function isUser($username)
    {
        return $this->database->find($this->prefix.'user')
            ->whereEqual('username', $username)
            ->readRecord();
    }
}
