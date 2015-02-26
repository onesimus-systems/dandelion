<?php
/**
 * User management API module
 */
namespace Dandelion\API\Module;

use Dandelion\Permissions;
use \Dandelion\Users;
use \Dandelion\Controllers\ApiController;

class UsersAPI extends BaseModule
{
    /**
     * Reset a user's password
     */
    public function resetPassword()
    {
        $userid = USER_ID;

        // Check permissions
        if (isset($this->up->uid)) {
            if ($this->up->uid == USER_ID || $this->ur->authorized('edituser')) {
                $userid = $this->up->uid;
            } else {
                exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
            }
        }

        // Validate password
        $newPass = $this->up->pw;
        if (!$newPass) {
            exit(ApiController::makeDAPI(5, 'New password is invalid', 'users'));
        }

        // Do action
        $user = new Users($this->repo, $userid);
        return $user->resetPassword($newPass);
    }

    /**
     * Create a new user
     */
    public function create()
    {
        if (!$this->ur->authorized('adduser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $username = $this->up->username;
        $password = $this->up->password;
        $realname = $this->up->fullname;
        $role = $this->up->role;
        $cheesto = $this->up->get('cheesto', true);

        $user = new Users($this->repo);
        return $user->createUser($username, $password, $realname, $role, $cheesto);
    }

    /**
     * Save edits to a user
     */
    public function edit()
    {
        if (!$this->ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $this->up->uid;
        if (!$uid) {
            exit(ApiController::makeDAPI(5, 'No user id given.', 'users'));
        }

        $user = new Users($this->repo, $uid, true);
        $user->userInfo['realname'] = $this->up->get('fullname', $user->userInfo['realname']);
        $user->userInfo['role'] = $this->up->get('role', $user->userInfo['role']);
        $user->userInfo['firsttime'] = $this->up->get('prompt', $user->userInfo['firsttime']);
        $user->userInfo['theme'] = $this->up->get('theme', $user->userInfo['theme']);
        return $user->saveUser();
    }

    /**
     * Delete a user
     */
    public function delete()
    {
        if (USER_ID == $this->up->uid) {
            exit(ApiController::makeDAPI(5, 'You can\'t delete yourself.', 'users'));
        }

        // Check permissions
        if ($this->ur->authorized('deleteuser')) {
            $userid = $this->up->uid;
        } else {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $user = new Users($this->repo);
        $permissions = new Permissions($this->makeRepo('Rights'));
        return $user->deleteUser($userid, $permissions);
    }

    /**
     * Get list of user accounts
     */
    public function getUsersList()
    {
        // Check permissions
        if (!$this->ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }
        $list = new Users($this->repo);
        return $list->getUserList();
    }

    /**
     * Get information for a single user
     */
    public function getUserInfo()
    {
        // Check permissions
        if (!$this->ur->authorized('edituser')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'users'));
        }

        $uid = $this->up->uid;
        if (!$uid) {
            exit(ApiController::makeDAPI(5, 'No user id given.', 'users'));
        }

        $user = new Users($this->repo);
        return $user->getUser($uid);
    }
}
