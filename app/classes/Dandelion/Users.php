<?php
/**
 * User management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\UsersRepo;

class Users
{
    // User data
    public $userInfo = array();
    public $userCheesto = array();
    public $userApi = array();

    public function __construct(UsersRepo $repo, $uid = -1, $load = false)
    {
        $this->repo = $repo;

        if ($uid >= 0) {
            $this->userInfo['userid'] = $uid;
        } else {
            $this->userInfo['userid'] = null;
        }

        if ($load === true && $uid >= 0) {
            $this->loadUser();
        } elseif ($load === true && $uid < 0) {
            trigger_error('To load a user you must provide a user ID.');
        }
        return true;
    }

    public function loadUser()
    {
        $allUserInfo = $this->repo->getFullUser($this->userInfo['userid']);

        foreach ($allUserInfo as $key => $value) {
            $infoType = substr($key, 0, 2);
            $key = substr_replace($key, '', 0, 2);

            switch ($infoType) {
                case 'u_':
                    if ($key == 'permissions') {
                        $value = (array)unserialize($value);
                    }
                    $this->userInfo[$key] = $value;
                    break;
                case 'p_':
                    $this->userCheesto[$key] = $value;
                    break;
                case 'a_':
                    $this->userApi[$key] = $value;
                    break;
            }
        }

        return true;
    }

    public function saveUser()
    {
        if (empty($this->userInfo['realname'])
            || empty($this->userInfo['role'])
            || (empty($this->userInfo['firsttime']) && $this->userInfo['firsttime'] != 0)
            || empty($this->userInfo['userid'])
        ) {
            return 'Something is empty';
        }

        $this->userInfo['role'] = strtolower($this->userInfo['role']);
        // Update main user row
        $userSaved = $this->repo->saveUser(
            $this->userInfo['userid'],
            $this->userInfo['realname'],
            $this->userInfo['role'],
            $this->userInfo['theme'],
            $this->userInfo['firsttime']
        );

        // Update Cheesto information
        $userCheestoSaved = $this->repo->saveUserCheesto(
            $this->userInfo['realname'],
            $this->userInfo['userid']
        );

        if ($userSaved && $userCheestoSaved) {
            return true;
        } else {
            return 'There was an error saving user';
        }
    }

    public function createUser($username, $password, $realname, $role, $cheesto = true)
    {
        $date = new \DateTime();

        // Error checking
        if (empty($username) || empty($password) || empty($realname) || empty($role)) {
            return 'Something is empty';
        }
        if ($this->isUser($username)) {
            return 'Username already in use';
        }

        $role = strtolower($role);
        $password = password_hash($password, PASSWORD_BCRYPT);

        // Create row in users table
        $userCreated = $this->repo->createUser($username, $password, $realname, $role, $date->format('Y-m-d'));

        $userCheestoCreated = true;
        if ($cheesto) {
            $lastID = $this->repo->lastCreatedUserId();

            // Create row in presence table
            $userCheestoCreated = $this->repo->createUserCheesto($lastID, $realname, $date->format('Y-m-d H:i:s'));
        }

        if ($userCreated && $userCheestoCreated) {
            return true;
        } else {
            return 'Error saving user';
        }
    }

    private function isUser($username)
    {
        return $this->repo->isUser($username);
    }

    public function resetPassword($pass = '')
    {
        $uid = $this->userInfo['userid'];

        if (!$uid || !$pass) {
            return 'Something is empty';
        }

        $pass = password_hash($pass, PASSWORD_BCRYPT);

        if ($this->repo->resetPassword($uid, $pass)) {
            return true;
        } else {
            return 'Error changing password.';
        }
    }

    public function deleteUser($uid, Permissions $permissions)
    {
        if (empty($uid)) {
            if (!empty($this->userInfo['userid'])) {
                $uid = $this->userInfo['userid'];
            } else {
                return 'No user id provided';
            }
        }

        $delete = false;
        $userRole = $this->repo->getUserRole($uid);
        $isAdmin = (array)$permissions->loadRights($userRole);

        if (!$isAdmin['admin']) {
            // If the account being deleted isn't an admin, then there's nothing to worry about
            $delete = true;
        } else {
            // If the account IS an admin, check all other users to make sure
            // there's at least one other user with the admin rights flag
            $otherUsers = $this->repo->getUserRole($uid, true);

            foreach ($otherUsers as $areTheyAdmin) {
                $isAdmin = (array)$permissions->loadRights($areTheyAdmin['role']);

                if ($isAdmin['admin']) {
                    // If one is found, stop for loop and allow the delete
                    $delete = true;
                    break;
                }
            }
        }

        if ($delete) {
            if ($this->repo->deleteUser($uid)) {
                return true;
            } else {
                return 'Error deleting user';
            }
        } else {
            return 'At least one admin account must be left to delete another admin account';
        }
    }

    public function getUserList()
    {
        return $this->repo->getUserList();
    }

    public function getUser($uid)
    {
        return $this->repo->getUser($uid);
    }
}
