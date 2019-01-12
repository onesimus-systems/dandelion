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
use Dandelion\API\ApiCommander;

class UsersAPI extends BaseModule
{
    /**
     * Reset a user's password
     * POST
     */
    public function resetPassword($params)
    {
        // Check permissions
        $userid = $params->uid ?: $this->requestUser->get('id');
        if ($userid != $this->requestUser->get('id') && !$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        // Validate password
        $newPass = $params->pw;
        if (!$newPass) {
            throw new ApiException('New password is invalid', ApiCommander::API_GENERAL_ERROR);
        }

        // Do action
        $uf = new UserFactory();
        $user = $uf->get($userid);
        $user->setPassword($newPass);
        if ($params->force_reset) {
            $user->set('initial_login', 1);
        } else {
            $user->set('initial_login', 0);
        }

        if ($user->save()) {
            return 'Password changed successfully';
        } else {
            throw new ApiException('Error changing password', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Create a new user
     * POST
     */
    public function create($params)
    {
        if (!$this->authorized($this->requestUser, 'create_user')) {
            throw new ApiPermissionException();
        }

        $groups = new Groups($this->makeRepo('Groups'));
        $group = $groups->getGroupList($params->role);
        if (is_null($group['permissions'])) {
            throw new ApiException('Group doesn\'t exist', ApiCommander::API_GENERAL_ERROR);
        }

        $uf = new UserFactory();
        $user = $uf->create();
        $user->set('username', $params->username);
        $user->set('fullname', $params->fullname);
        $user->set('group_id', $params->role);
        $user->set('api_override', $params->api_override);
        $user->setPassword($params->password);
        $user->setMakeCheesto($params->cheesto);
        if ($params->force_reset) {
            $user->set('initial_login', 1);
        } else {
            $user->set('initial_login', 0);
        }

        if ($user->save()) {
            return 'User created successfully';
        } else {
            throw new ApiException('Error creating user', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Save edits to a user
     * POST
     */
    public function edit($params)
    {
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        $uid = $params->uid;
        if (!$uid) {
            throw new ApiException('No user id given', ApiCommander::API_GENERAL_ERROR);
        }

        $uf = new UserFactory();
        $user = $uf->get($uid);

        $fn = $params->fullname ?? $user->get('fullname');
        $gi = $params->role ?? $user->get('group_id');
        $il = $params->prompt ?? $user->get('initial_login');
        $t = $params->theme ?? $user->get('theme');
        $ao = $params->api_override ?? $user->get('api_override');

        $user->set('fullname', $fn);
        $user->set('group_id', $gi);
        $user->set('initial_login', $il);
        $user->set('theme', $t);
        $user->set('api_override', $ao);

        if ($user->save()) {
            return 'User saved successfully';
        } else {
            throw new ApiException('Error saving user', ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Disable user
     * POST
     */
    public function disable($params)
    {
        return $this->enableDisable($params->uid, true);
    }

    /**
     * Enable user
     * POST
     */
    public function enable($params)
    {
        return $this->enableDisable($params->uid, false);
    }

    private function enableDisable($uid, $disable)
    {
        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
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
            throw new ApiException($msg, ApiCommander::API_GENERAL_ERROR);
        }
    }

    /**
     * Delete a user
     * POST
     */
    public function delete($params)
    {
        $userid = $params->uid;

        if ($this->requestUser->get('id') == $userid) {
            throw new ApiException('Can\'t delete yourself', ApiCommander::API_GENERAL_ERROR);
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
            throw new ApiException('Error deleting user', ApiCommander::API_GENERAL_ERROR);
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
    public function getUser($params)
    {
        // Check permissions
        $userid = $params->uid ?: $this->requestUser->get('id');
        if ($userid == $this->requestUser->get('id')) {
            return [$this->requestUser->getApiData()];
        }

        if (!$this->authorized($this->requestUser, 'edit_user')) {
            throw new ApiPermissionException();
        }

        $user = new Users($this->repo);
        return $user->getUser($userid);
    }
}
