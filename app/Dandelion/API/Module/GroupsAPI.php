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

use Dandelion\Groups;
use Dandelion\Exception\ApiException;
use Dandelion\Controllers\ApiController;
use Dandelion\Exception\ApiPermissionException;
use Dandelion\API\ApiCommander;

class GroupsAPI extends BaseModule
{
    /**
     * Gets the list of rights groups
     */
    public function getList()
    {
        $permissions = new Groups($this->repo);
        return $permissions->getGroupList();
    }

    /**
     * Gets the rights for a specific group
     */
    public function getGroup($params)
    {
        $permissions = new Groups($this->repo);
        return $permissions->getGroupList($params->groupid);
    }

    /**
     * Save rights for a group
     */
    public function edit($params)
    {
        if (!$this->authorized($this->requestUser, 'edit_group')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $gid = $params->groupid;
        $rights = json_decode($params->rights, true);

        if ($permissions->editGroup($gid, $rights)) {
            return 'User group saved';
        }
        throw new ApiException('Error saving user group', ApiCommander::API_GENERAL_ERROR);
    }

    /**
     * Create new rights group
     */
    public function create($params)
    {
        if (!$this->authorized($this->requestUser, 'create_group')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $name = $params->name;
        $rights = $params->rights ? json_decode($params->rights, true) : [];

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return 'User group created successfully';
        }
        throw new ApiException('Error creating user group', ApiCommander::API_GENERAL_ERROR);
    }

    /**
     * Delete rights group
     */
    public function delete($params)
    {
        if (!$this->authorized($this->requestUser, 'delete_group')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $gid = $params->groupid;
        $users = $permissions->usersExistInGroup($gid);

        if ($users) {
            throw new ApiException('This group is assigned to users. Cannot delete group.', ApiCommander::API_GENERAL_ERROR);
        }

        $permissions->deleteGroup($gid);
        return 'Group deleted successfully.';
    }

    /**
     * Gets the rights for the current user
     */
    public function getUserRights()
    {
        return $this->requestUser->getKeycard()->readAll();
    }
}
