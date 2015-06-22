<?php
/**
 * Dandelion - Web based log journal
 *
 * @author Lee Keitel  <keitellf@gmail.com>
 * @copyright 2015 Lee Keitel, Onesimus Systems
 *
 * @license GNU GPL version 3
 */
namespace Dandelion\API\Module;

use Dandelion\Users;
use Dandelion\Groups;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;

class UsersAPI extends BaseModule
{
    /**
     * Reset a user's password
     */
    public function resetPassword()
    {
        $userid = USER_ID;

        // Check permissions
        if ($this->up->uid) {
            if ($this->up->uid == USER_ID || $this->ur->authorized('edituser')) {
                $userid = $this->up->uid;
            } else {
                throw new ApiPermissionException();
            }
        }

        // Validate password
        $newPass = $this->up->pw;
        if (!$newPass) {
            throw new ApiException('New password is invalid', 5);
        }

        // Do action
        $user = new Users($this->repo, $userid);

        if ($user->resetPassword($newPass)) {
            return 'Password changed successfully';
        } else {
            throw new ApiException('Error changing password', 5);
        }
    }

    /**
     * Create a new user
     */
    public function create()
    {
        if (!$this->ur->authorized('createuser')) {
            throw new ApiPermissionException();
        }

        $username = $this->up->username;
        $password = $this->up->password;
        $fullname = $this->up->fullname;
        $role = $this->up->group;
        $cheesto = $this->up->get('cheesto', true);

        $user = new Users($this->repo);

        if ($user->createUser($username, $password, $fullname, $role, $cheesto)) {
            return 'User created successfully';
        } else {
            throw new ApiException('Error creating user', 5);
        }
    }

    /**
     * Save edits to a user
     */
    public function edit()
    {
        if (!$this->ur->authorized('edituser')) {
            throw new ApiPermissionException();
        }

        $uid = $this->up->uid;
        if (!$uid) {
            throw new ApiException('No user id given', 5);
        }

        $user = new Users($this->repo, $uid, true);
        $user->userInfo['fullname'] = $this->up->get('fullname', $user->userInfo['fullname']);
        $user->userInfo['group_id'] = $this->up->get('role', $user->userInfo['group_id']);
        $user->userInfo['initial_login'] = $this->up->get('prompt', $user->userInfo['initial_login']);
        $user->userInfo['theme'] = $this->up->get('theme', $user->userInfo['theme']);

        if ($user->saveUser()) {
            return 'User saved successfully';
        } else {
            throw new ApiException('Error saving user', 5);
        }
    }

    /**
     * Delete a user
     */
    public function delete()
    {
        if (USER_ID == $this->up->uid) {
            throw new ApiException('Can\'t delete yourself', 5);
        }

        // Check permissions
        if ($this->ur->authorized('deleteuser')) {
            $userid = $this->up->uid;
        } else {
            throw new ApiPermissionException();
        }

        $user = new Users($this->repo);
        $permissions = new Groups($this->makeRepo('Groups'));

        if ($user->deleteUser($userid, $permissions)) {
            return 'User deleted successfully';
        } else {
            throw new ApiException('Error deleting user', 5);
        }
    }

    /**
     * Get list of user accounts
     */
    public function getUsers()
    {
        // Check permissions
        if (!$this->ur->authorized('edituser')) {
            throw new ApiPermissionException();
        }
        $list = new Users($this->repo);
        return $list->getUserList();
    }

    /**
     * Get information for a single user
     */
    public function getUser()
    {
        // Check permissions
        if (!$this->ur->authorized('edituser')) {
            throw new ApiPermissionException();
        }

        $uid = $this->up->uid;
        if (!$uid) {
            throw new ApiException('No user id given', 5);
        }

        $user = new Users($this->repo);
        return $user->getUser($uid);
    }
}
