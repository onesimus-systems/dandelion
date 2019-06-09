<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\Factories;

use Dandelion\Repos;
use Dandelion\User;
use Dandelion\Auth\Keycard;

class UserFactory
{
    public function __construct()
    {
    }

    public function create()
    {
        return $this->get(0);
    }

    public function get($userid)
    {
        $userRepo = new Repos\UserRepo();
        $cheestoRepo = new Repos\CheestoRepo();
        return new User($userRepo, $cheestoRepo, $userid);
    }

    public function getWithKeycard($userid)
    {
        $user = $this->get($userid);

        $aclf = new ACLFactory();
        $user->giveKeycard($aclf->createKeycardForUser($user));

        return $user;
    }

    public function getByUsername($username)
    {
        $userRepo = new Repos\UserRepo();
        $cheestoRepo = new Repos\CheestoRepo();
        return new User($userRepo, $cheestoRepo, 0, $username);
    }

    public function getWithKeycardUsername($username)
    {
        $user = $this->getByUsername($username);

        $aclf = new ACLFactory();
        $user->giveKeycard($aclf->createKeycardForUser($user));

        return $user;
    }
}
