<?php
/**
 * Rights group management
 */
namespace Dandelion;

use \Dandelion\Repos\Interfaces\GroupsRepo;

class Groups
{
    private $defaultPermissions = [
        'createlog' => false,
        'editlog' => false,
        'viewlog' => false,

        'createcat' => false,
        'editcat' => false,
        'deletecat' => false,

        'createuser' => false,
        'edituser' => false,
        'deleteuser' => false,

        'creategroup' => false,
        'editgroup' => false,
        'deletegroup' => false,

        'viewcheesto' => false,
        'updatecheesto' => false,

        'admin' => false
    ];

    private $permissionNames = [
        'createlog' => 'Create logs',
        'editlog' => 'Edit logs',
        'viewlog' => 'View the log',

        'createcat' => 'Create categories',
        'editcat' => 'Edit categories',
        'deletecat' => 'Delete categories',

        'createuser' => 'Create users',
        'edituser' => 'Edit users',
        'deleteuser' => 'Delete users',

        'creategroup' => 'Create groups',
        'editgroup' => 'Edit groups',
        'deletegroup' => 'Delete groups',

        'viewcheesto' => 'View Cheesto',
        'updatecheesto' => 'Update Cheesto',

        'admin' => 'Is Full Admin'
    ];

    public function __construct(GroupsRepo $repo)
    {
        $this->repo = $repo;
        return;
    }
    /**
     * Get group data from database
     *
     * @param int $groupID - Group ID number, if omitted returns all groups
     * @return array
     */
    public function getGroupList($groupID = null)
    {
        if ($groupID === null) {
            return $this->repo->getGroupList();
        } else {
            if (is_numeric($groupID)) {
                $group = $this->repo->getGroupById($groupID);
            } else {
                $group = $this->repo->getGroupByName($groupID);
            }
            $group['permissions'] = unserialize($group['permissions']);
            $group['permissionNames'] = $this->permissionNames;
            return $group;
        }
    }

    /**
     * Save a new group to database
     *
     * @param string $name - Name of new group
     * @param array $rights - Array containing rights
     * @return int - ID of new group
     */
    public function createGroup($name, $rights)
    {
        $rights = array_merge($this->defaultPermissions, $rights);
        $rights = serialize($rights);
        return $this->repo->createGroup(strtolower($name), $rights);
    }

    /**
     * Remove group from database
     *
     * @param int $id - ID number of group
     * @return bool - Status of query
     */
    public function deleteGroup($gid)
    {
        return $this->repo->deleteGroup($gid);
    }

    /**
     * Save edits to group
     *
     * @param int $gid - Group ID number
     * @param array $rights - Group rights
     * @return bool - Status of query
     */
    public function editGroup($gid, $rights)
    {
        $rights = array_merge($this->defaultPermissions, $rights);
        $rights = serialize($rights);
        return $this->repo->editGroup($gid, $rights);
    }

    /**
     * Get only permissions array from database for group $userrole
     *
     * @param string $userrole - Name of permisions group
     * @return array - Permissions
     */
    public function loadRights($gid)
    {
       return (array) unserialize($this->repo->loadRights($gid));
    }

    /**
     * Determine if any users belong to group id/name $group
     *
     * @param int/string $group - Group ID or name
     * @return array - Containing user IDs of users in group
     */
    public function usersExistInGroup($group)
    {
        return $this->repo->userCountInGroup($group);
    }

    public function usersInGroup($group)
    {
        return $this->repo->usersInGroup($group);
    }
}
