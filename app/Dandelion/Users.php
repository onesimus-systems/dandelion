<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion;

use Dandelion\Repos\Interfaces\UsersRepo;

class Users
{
    protected $repo;

    // User data
    public $userInfo = array();
    public $userCheesto = array();
    public $userApi = array();

    public function __construct(UsersRepo $repo, $uid = -1, $load = false)
    {
        $this->repo = $repo;

        if ($uid >= 0) {
            $this->userInfo['id'] = $uid;
        } else {
            $this->userInfo['id'] = null;
        }

        if ($load === true && $uid >= 0) {
            $this->loadUser();
        }
    }

    public function loadUser()
    {
        $this->userInfo = $this->getUser($this->userInfo['id']);
        return true;
    }

    public function saveUser()
    {
        if (!$this->userInfo['fullname']
            || !$this->userInfo['group_id']
            || (!$this->userInfo['initial_login'] && $this->userInfo['initial_login'] != 0)
            || !$this->userInfo['id']
        ) {
            return false;
        }

        if (!isset($this->userInfo['disabled'])) {
            $this->userInfo['disabled'] = 0;
        }

        // Update main user row
        $userSaved = $this->repo->saveUser(
            $this->userInfo['id'],
            $this->userInfo['fullname'],
            $this->userInfo['group_id'],
            $this->userInfo['theme'],
            $this->userInfo['initial_login'],
            $this->userInfo['disabled']
        );

        return is_numeric($userSaved);
    }

    public function createUser($username, $password, $fullname, $gid, $cheesto = true)
    {
        $date = new \DateTime();

        // Error checking
        if (!$username || !$password || !$fullname || !$gid) {
            return 'Something is empty';
        }
        if ($this->isUser($username)) {
            return 'Username already in use';
        }

        $password = $this->doHash($password);

        // Create row in users table
        $userCreated = $this->repo->createUser($username, $password, $fullname, $gid, $date->format('Y-m-d'));

        $userCheestoCreated = true;
        if ($cheesto) {
            // Create row in presence table
            $userCheestoCreated = $this->repo->createUserCheesto($userCreated, $fullname, $date->format('Y-m-d H:i:s'));
        }

        return (is_numeric($userCreated) && is_numeric($userCheestoCreated));
    }

    private function isUser($username)
    {
        return $this->repo->isUser($username);
    }

    public function resetPassword($pass = '')
    {
        $uid = $this->userInfo['id'];

        if (!$uid || !$pass) {
            return 'Something is empty';
        }

        $pass = $this->doHash($pass);

        // Should return 1 row
        return $this->repo->resetPassword($uid, $pass);
    }

    public function deleteUser($uid, Groups $permissions)
    {
        if (!$uid) {
            if ($this->userInfo['id']) {
                $uid = $this->userInfo['id'];
            } else {
                return 'No user id provided';
            }
        }

        $delete = false;
        $userGroup = $this->repo->getUserRole($uid);
        $isAdmin = $permissions->loadRights($userGroup);

        if (!$isAdmin['admin']) {
            // If the account being deleted isn't an admin, then there's nothing to worry about
            $delete = true;
        } else {
            // If the account IS an admin, check all other users to make sure
            // there's at least one other user with the admin rights flag
            $otherUsers = $this->repo->getUserRole($uid, true);

            foreach ($otherUsers as $areTheyAdmin) {
                $isAdmin = $permissions->loadRights($areTheyAdmin['id']);

                if ($isAdmin['admin']) {
                    // If one is found, stop for loop and allow the delete
                    $delete = true;
                    break;
                }
            }
        }

        if ($delete) {
            // Should return 1 row
            return $this->repo->deleteUser($uid);
        } else {
            return 'At least one admin account must be left to delete another admin account';
        }
    }

    public function enable()
    {
        $this->userInfo['disabled'] = 0;
    }

    public function disable()
    {
        $this->userInfo['disabled'] = 1;
    }

    public function getUserList()
    {
        return $this->repo->getUsers();
    }

    public function getUser($uid)
    {
        return $this->repo->getUsers($uid)[0];
    }

    protected function doHash($s)
    {
        return password_hash($s, PASSWORD_BCRYPT);
    }
}
