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

use \Dandelion\Groups;
use \Dandelion\Exception\ApiException;
use \Dandelion\Controllers\ApiController;
use \Dandelion\Exception\ApiPermissionException;

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
    public function getGroup()
    {
        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        return $permissions->getGroupList($gid);
    }

    /**
     * Save rights for a group
     */
    public function edit()
    {
        if (!$this->ur->authorized('editgroup')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        $rights = json_decode($this->up->rights, true);

        if ($permissions->editGroup($gid, $rights)) {
            return 'User group saved';
        } else {
            throw new ApiException('Error saving user group', 5);
        }
    }

    /**
     * Create new rights group
     */
    public function create()
    {
        if (!$this->ur->authorized('creategroup')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $name = $this->up->name;
        $rights = $this->up->get('rights', []);

        if ($rights) {
            $rights = json_decode($rights, true);
        }

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return 'User group created successfully';
        } else {
            throw new ApiException('Error creating user group', 5);
        }
    }

    /**
     * Delete rights group
     */
    public function delete()
    {
        if (!$this->ur->authorized('deletegroup')) {
            throw new ApiPermissionException();
        }

        $permissions = new Groups($this->repo);
        $gid = $this->up->groupid;
        $users = $permissions->usersExistInGroup($gid);

        if ($users) {
            throw new ApiException('This group is assigned to users. Cannot delete group.', 5);
        } else {
            $permissions->deleteGroup($gid);
            return 'Group deleted successfully.';
        }
    }

    /**
     * Gets the rights for the current user
     */
    public function getUserRights()
    {
        return $this->ur->getRightsForUser();
    }
}
