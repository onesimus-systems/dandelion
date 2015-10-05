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
use Dandelion\Factories\UserFactory;

class UsersAPI extends BaseModule
{
    /**
     * Reset a user's password
     * POST
     */
    public function resetPassword()
    {
        // Check permissions
        $userid = $this->request->postParam('uid', $this->requestUser->get('id'));
        if ($userid != $this->requestUser->get('id') && !$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        // Validate password
        $newPass = $this->request->postParam('pw');
        if (!$newPass) {
            throw new ApiException('New password is invalid', 5);
        }

        // Do action
        $uf = new UserFactory();
        $user = $uf->get($userid);
        $user->setPassword($newPass);

        if ($user->save()) {
            return 'Password changed successfully';
        } else {
            throw new ApiException('Error changing password', 5);
        }
    }

    /**
     * Create a new user
     * POST
     */
    public function create()
    {
        if (!$this->authorized($this->requestUser, 'create_user')) {
            throw new ApiPermissionException();
        }

        $uf = new UserFactory();
        $user = $uf->create();
        $user->set('username', $this->request->postParam('username'));
        $user->set('fullname', $this->request->postParam('fullname'));
        $user->set('group_id', $this->request->postParam('group'));
        $user->setPassword($this->request->postParam('password'));

        if ($user->save()) {
            return 'User created successfully';
        } else {
            throw new ApiException('Error creating user', 5);
        }
    }

    /**
     * Save edits to a user
     * POST
     */
    public function edit()
    {
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        $uid = $this->request->postParam('uid');
        if (!$uid) {
            throw new ApiException('No user id given', 5);
        }

        $uf = new UserFactory();
        $user = $uf->get($uid);
        $user->set('fullname', $this->request->postParam('fullname', $user->get('fullname')));
        $user->set('group_id', $this->request->postParam('role', $user->get('group_id')));
        $user->set('initial_login', $this->request->postParam('prompt', $user->get('initial_login')));
        $user->set('theme', $this->request->postParam('theme', $user->get('theme')));

        if ($user->save()) {
            return 'User saved successfully';
        } else {
            throw new ApiException('Error saving user', 5);
        }
    }

    /**
     * Disable user
     * POST
     */
    public function disable()
    {
        return $this->enableDisable(true);
    }

    /**
     * Enable user
     * POST
     */
    public function enable()
    {
        return $this->enableDisable(false);
    }

    private function enableDisable($disable)
    {
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        $uid = $this->request->postParam('uid');
        if (!$uid) {
            throw new ApiException('No user id given', 5);
        }

        $uf = new UserFactory();
        $user = $uf->get($uid);
        if ($disable) {
            $user->disable();
        } else {
            $user->enable();
        }

        if ($user->save()) {
            return $disable ? 'User disabled' : 'User enabled';
        } else {
            $msg = $disable ? 'Error disabling user' : 'Error enabling user';
            throw new ApiException($msg, 5);
        }
    }

    /**
     * Delete a user
     * POST
     */
    public function delete()
    {
        $userid = $this->request->postParam('uid');

        if ($this->requestUser->get('id') == $userid) {
            throw new ApiException('Can\'t delete yourself', 5);
        }

        // Check permissions
        if (!$this->authorized($this->requestUser, 'delete_user')) {
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
     * GET
     */
    public function getUsers()
    {
        // Check permissions
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }
        $list = new Users($this->repo);
        return $list->getUserList();
    }

    /**
     * Get information for a single user
     * GET
     */
    public function getUser()
    {
        // Check permissions
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        $uid = $this->request->getParam('uid');
        if (!$uid) {
            throw new ApiException('No user id given', 5);
        }

        $user = new Users($this->repo);
        return $user->getUser($uid);
    }
}
