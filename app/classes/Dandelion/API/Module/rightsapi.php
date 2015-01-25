<?php
/**
 * Rights management API module
 */
namespace Dandelion\API\Module;

use \Dandelion\Permissions;
use \Dandelion\Controllers\ApiController;

class rightsAPI extends BaseModule
{
    public function __construct($db, $ur, $params)
    {
        parent::__construct($db, $ur, $params);
    }

    /**
     * Gets the list of rights groups
     */
    public function getList()
    {
        $permissions = new Permissions($this->db);
        $allGroups = $permissions->getGroupList();
        foreach ($allGroups as $key => $value) {
            $allGroups[$key]['permissions'] = unserialize($allGroups[$key]['permissions']);
        }
        return json_encode($allGroups);
    }

    /**
     * Gets the rights for a specific group
     */
    public function getGroup()
    {
        $permissions = new Permissions($this->db);
        $gid = $this->up->groupid;
        return json_encode(unserialize($permissions->getGroupList($gid)[0]['permissions']));
    }

    /**
     * Save rights for a group
     */
    public function save()
    {
        if (!$this->ur->authorized('editgroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new Permissions($this->db);
        $gid = $this->up->groupid;
        $rights = json_decode($this->up->rights, true);

        if ($permissions->editGroup($gid, $rights)) {
            return json_encode('User group saved');
        } else {
            exit(ApiController::makeDAPI(5, 'Error saving user group', 'rights'));
        }
    }

    /**
     * Create new rights group
     */
    public function create()
    {
        if (!$this->ur->authorized('addgroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new Permissions($this->db);
        $name = $this->up->name;
        $rights = json_decode($this->up->rights, true);

        if (is_numeric($permissions->createGroup($name, $rights))) {
            return json_encode('User group created successfully');
        } else {
            exit(ApiController::makeDAPI(5, 'Error creating user group', 'rights'));
        }
    }

    /**
     * Delete rights group
     */
    public function delete()
    {
        if (!$this->ur->authorized('deletegroup')) {
            exit(ApiController::makeDAPI(4, 'This account doesn\'t have the proper permissions.', 'rights'));
        }

        $permissions = new Permissions($this->db);
        $gid = $this->up->groupid;
        $users = $permissions->usersInGroup($gid);

        if (isset($users[0])) {
            return json_encode('This group is assigned to users.<br>Can not delete this group.');
        }
        else {
            $permissions->deleteGroup($gid);
            return json_encode('Group deleted successfully.');
        }
    }

    /**
     * Gets the rights for the current user
     */
    public function getUsersRights()
    {
        return json_encode($this->ur->getRightsForUser());
    }
}
